<?php
if (!defined('ABSPATH')) {
  exit;
}
if (!class_exists('TVC_Admin_Auto_Product_sync_Helper')) {
  class TVC_Admin_Auto_Product_sync_Helper
  {
    protected $TVC_Admin_Helper;
    protected $TVC_Admin_DB_Helper;
    protected $time_space;
    private $apiDomain;
    protected $batch_size;
    protected $customApiObj;
    public function __construct()
    {
      $this->TVC_Admin_Helper = new TVC_Admin_Helper();
      $this->TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
      $this->apiDomain = TVC_API_CALL_URL;
      $this->includes();
      add_action('admin_init', array($this, 'add_table_in_db'));
      $this->customApiObj = new CustomApi();
      $this->time_space = $this->TVC_Admin_Helper->get_auto_sync_time_space();
      $this->timestamp = $this->TVC_Admin_Helper->get_first_auto_sync_timestamp();
      $this->batch_size = $this->TVC_Admin_Helper->get_auto_sync_batch_size();
      //add_action('admin_init',array($this,'add_woo_req'));
      add_action('admin_init', array($this, 'add_schedule_event'));
      add_action('ee_auto_product_sync_check', array($this, 'call_auto_sync_product'), 10, 1);
      add_filter('cron_schedules', array($this, 'tvc_add_cron_interval_for_product_sync'));
     // add_action('set_recurring_auto_sync_product_feed_wise_default', array($this, 'call_auto_sync_product_feed_wise_default'));

    }

    public function includes()
    {
      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      if (!class_exists('CustomApi')) {
        require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/CustomApi.php');
      }
    }

    public function add_woo_req()
    {
      // include_once WC_ABSPATH . 'packages/action-scheduler/action-scheduler.php';
    }

    public function add_table_in_db()
    {
      //add_filter( 'cron_schedules', array($this,'tvc_add_cron_interval') ); 
      global $wpdb;
      /* cteate table for save sync product settings */
      $tablename = esc_sql($wpdb->prefix . "ee_product_sync_data");
      $query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($tablename));
      if ($wpdb->get_var($query) === $tablename) {
        $queryDataType = $wpdb->prepare("SHOW COLUMNS FROM `$tablename` WHERE FIELD = %s", "update_date");
        $result = $wpdb->get_row($queryDataType);
        if ($result->Type == 'date') {
          $wpdb->query("ALTER TABLE $tablename Modify `update_date`  DATETIME NULL");
        }

        $sync_query = $wpdb->prepare("SHOW COLUMNS FROM " . $tablename . " LIKE %s", $wpdb->esc_like('feedId'));
        $sync_result = $wpdb->get_var($sync_query);
        if ($sync_result == '') {
          $wpdb->query("ALTER TABLE $tablename ADD `feedId` int(11) NULL  AFTER `status`");
        }
      } else {
        $sql_create = "CREATE TABLE `$tablename` ( `id` BIGINT(20) NOT NULL AUTO_INCREMENT , `w_product_id` BIGINT(20) NOT NULL , `w_cat_id` INT(10) NOT NULL , `g_cat_id` INT(10) NOT NULL , `g_attribute_mapping` LONGTEXT NOT NULL , `update_date` DATE NOT NULL , `status` INT(1) NOT NULL DEFAULT '1', `feedId` int(11) NULL, PRIMARY KEY (`id`) );";
        if (maybe_create_table($tablename, $sql_create)) {
        }
      }
      /* cteate table for save auto sync product call */
      $tablename = esc_sql($wpdb->prefix . "ee_product_sync_call");
      $query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($tablename));
      if ($wpdb->get_var($query) === $tablename) {
      } else {
        $sql_create = "CREATE TABLE `$tablename` ( `id` BIGINT(20) NOT NULL AUTO_INCREMENT, `sync_product_ids` LONGTEXT NULL, `w_total_product` INT(10) NOT NULL , `total_sync_product` INT(10) NOT NULL ,last_sync  DATETIME NOT NULL, create_sync DATETIME NOT NULL, next_sync DATETIME NOT NULL, `last_sync_product_id` BIGINT(20) NOT NULL, `action_scheduler_id` INT(10) NOT NULL, `status` INT(1) NOT NULL COMMENT '0 failed, 1 completed', PRIMARY KEY (`id`) );";
        if (!maybe_create_table($tablename, $sql_create)) {
        }
      }

      /* cteate table for save GMC sync product list */
      $tablename = $wpdb->prefix . "ee_products_sync_list";
      $query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($tablename));
      if ($wpdb->get_var($query) === $tablename) {
        $query = $wpdb->prepare("SHOW COLUMNS FROM " . $tablename . " LIKE %s", $wpdb->esc_like('feedId'));
        $result = $wpdb->get_var($query);
        if ($result == '') {
          $wpdb->query("ALTER TABLE $tablename ADD `feedId` int(11) NULL  AFTER `issues`");
        }
      } else {
        $sql_create = "CREATE TABLE `$tablename` ( `id` BIGINT(20) NOT NULL AUTO_INCREMENT , `gmc_id` VARCHAR(200) NOT NULL , `name` VARCHAR(200) NOT NULL , `product_id` VARCHAR(100) NOT NULL , `google_status` VARCHAR(50) NOT NULL , `image_link` VARCHAR(200) NOT NULL, `issues` LONGTEXT NOT NULL, `feedId` int(11) NULL, PRIMARY KEY (`id`) );";
        if (maybe_create_table($tablename, $sql_create)) {
          $this->TVC_Admin_Helper->import_gmc_products_sync_in_db();

          $product_status = $this->TVC_Admin_DB_Helper->tvc_get_counts_groupby('ee_products_sync_list', 'google_status');
          $syncProductStat = array("approved" => 0, "disapproved" => 0, "pending" => 0);
          if (!empty($product_status)) {
            foreach ($product_status as $key => $value) {
              if (isset($value['google_status'])) {
                $syncProductStat[$value['google_status']] = esc_attr((isset($value['count']) && $value['count'] > 0) ? $value['count'] : 0);
              }
            }
          }
          $syncProductStat["total"] = $this->TVC_Admin_DB_Helper->tvc_row_count('ee_products_sync_list');
          $google_detail = $this->TVC_Admin_Helper->get_ee_options_data();
          $google_detail['prod_sync_status'] = (object) $syncProductStat;
          $this->TVC_Admin_Helper->set_ee_options_data($google_detail);
        }
      }
      /********Create product feed table in DB ******************/
      $tablename = $wpdb->prefix . "ee_product_feed";
      $query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($tablename));
      if ($wpdb->get_var($query) === $tablename) {
        $query = $wpdb->prepare("SHOW COLUMNS FROM " . $tablename . " LIKE %s", $wpdb->esc_like('target_country'));
        $result = $wpdb->get_var($query);
        if ($result == '') {
          $wpdb->query("ALTER TABLE $tablename ADD `target_country` varchar(50) DEFAULT NULL  AFTER `is_default`");
        }

        $checkTiktokCat = $wpdb->prepare("SHOW COLUMNS FROM " . $tablename . " LIKE %s", $wpdb->esc_like('tiktok_catalog_id'));
        $resultTiktokCat = $wpdb->get_var($checkTiktokCat);
        if ($resultTiktokCat == '') {
          $wpdb->query("ALTER TABLE $tablename ADD `tiktok_catalog_id` varchar(100) DEFAULT NULL  AFTER `target_country`");
        }

        $querytiktok = $wpdb->prepare("SHOW COLUMNS FROM " . $tablename . " LIKE %s", $wpdb->esc_like('tiktok_status'));
        $resulttiktok = $wpdb->get_var($querytiktok);
        if ($resulttiktok == '') {
          $wpdb->query("ALTER TABLE $tablename ADD `tiktok_status` varchar(200) NULL  AFTER `tiktok_catalog_id`");
        }
      } else {
        $sql_create = "CREATE TABLE `$tablename` (  `id` int(11) NOT NULL AUTO_INCREMENT,
                                                      `feed_name` varchar(200) NOT NULL,
                                                      `channel_ids` varchar(200) NOT NULL COMMENT '1 GMC, 2 FB, 3 tiktok',
                                                      `auto_sync_interval` varchar(200) NOT NULL,
                                                      `auto_schedule` int(11) NOT NULL COMMENT '0 Inactive, 1 Active',
                                                      `categories` LONGTEXT DEFAULT NULL,
                                                      `attributes` LONGTEXT DEFAULT NULL,
                                                      `filters` LONGTEXT DEFAULT NULL,
                                                      `include_product` LONGTEXT DEFAULT NULL,
                                                      `exclude_product` LONGTEXT DEFAULT NULL,
                                                      `created_date` datetime NOT NULL,
                                                      `updated_date` datetime DEFAULT NULL,
                                                      `last_sync_date` datetime DEFAULT NULL,
                                                      `next_schedule_date` datetime NULL,
                                                      `total_product` int(11) Null,
                                                      `status` varchar(200) NOT NULL,
                                                      `is_mapping_update` int(11) Null,
                                                      `is_process_start` int(11) Null,
                                                      `is_auto_sync_start` int(11) Null,
                                                      `product_sync_batch_size` varchar(50) DEFAULT NULL,
                                                      `product_id_prefix` varchar(100) DEFAULT NULL,
                                                      `product_sync_alert` LONGTEXT DEFAULT NULL,
                                                      `is_delete` int(11) Null,
                                                      `is_default` int(11) NOT NULL DEFAULT '0',
                                                      `target_country` varchar(50) DEFAULT NULL,
                                                      `tiktok_catalog_id` varchar(100) DEFAULT NULL,
                                                      PRIMARY KEY (`id`) );";
        if (maybe_create_table($tablename, $sql_create)) {
        }
      }
      /*************Check Default feed exists *****************************/
      $tablenamesync = $wpdb->prefix . "ee_product_sync_data";
      $count = "SELECT count(*) as count from `$tablenamesync` where `feedId` is NULL ";
      $result = $wpdb->get_row($count);
      if ($result->count > 0) {
        /***
         * 
         * 
         * Add here default feed Important
         * 
         * 
         */
        $last_sync = $this->TVC_Admin_DB_Helper->tvc_get_last_row('ee_product_sync_call', array("last_sync", "create_sync", "next_sync", "status"));

        $conv_additional_data = $this->TVC_Admin_Helper->get_ee_additional_data();
        $cat = unserialize(get_option("ee_prod_mapped_cats"));
        $attr = unserialize(get_option("ee_prod_mapped_attrs"));
        $auto_sync_interval = isset($conv_additional_data['product_sync_duration']) ? $conv_additional_data['product_sync_duration'] == 'Day' ? $conv_additional_data['pro_snyc_time_limit'] : '1' : '25';

        $profile_data = array(
          'feed_name' => esc_sql('Default Feed'),
          'channel_ids' => esc_sql('1'),
          'auto_sync_interval' => esc_sql($auto_sync_interval),
          'auto_schedule' => esc_sql('1'),
          'categories' => json_encode($cat),
          'attributes' => json_encode($attr),
          'created_date' => esc_sql(date('Y-m-d H:i:s', current_time('timestamp'))),
          'last_sync_date' => esc_sql($last_sync['last_sync']),
          'next_schedule_date' => esc_sql($last_sync['next_sync']),
          'total_product' => esc_sql($result->count),
          'status' => esc_sql('Synced'),
          'is_mapping_update' => esc_sql($conv_additional_data['is_mapping_update']),
          'is_default' => esc_sql('1'),
        );
        $this->TVC_Admin_DB_Helper->tvc_add_row("ee_product_feed", $profile_data, array("%s", "%s", "%s", "%d", "%s", "%s", "%s", "%s", "%s", "%d", "%s", "%d"));
        $wpdb->query("UPDATE $tablenamesync SET feedId = 1 ");
        $tablename = $wpdb->prefix . "ee_products_sync_list";
        $wpdb->query("UPDATE $tablename SET feedId = 1 ");
        /***
         * 
         * set recurring auto sync here for default feed
         */
        //as_schedule_single_action($last_sync['next_sync'], 'set_recurring_auto_sync_product_feed_wise', array("feedId" => 1));
      }

      // Add TikTok Catalog table
      $tablename = $wpdb->prefix . "ee_tiktok_catalog";
      $query = $wpdb->prepare('SHOW TABLES LIKE %s', $wpdb->esc_like($tablename));
      if ($wpdb->get_var($query) === $tablename) {
      } else {
        $sql_create = "CREATE TABLE `$tablename` (  `id` int(11) NOT NULL AUTO_INCREMENT,
                                                      `country` varchar(200) NOT NULL,
                                                      `catalog_id` varchar(200) NOT NULL,
                                                      `catalog_name` varchar(200) NOT NULL,                                                      
                                                      `created_date` datetime NOT NULL,                                                     
                                                      PRIMARY KEY (`id`) );";
        if (maybe_create_table($tablename, $sql_create)) {
        }
      }

    }
    public function get_product_category($product_id)
    {
      $output = [];
      $terms_ids = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'ids'));
      // Loop though terms ids (product categories)
      if (!empty($terms_ids)) {
        foreach ($terms_ids as $term_id) {
          $term_names = [];
          // Loop through product category ancestors
          foreach (get_ancestors($term_id, 'product_cat') as $ancestor_id) {
            $term_names[] = get_term($ancestor_id, 'product_cat')->name;
            if (isset($output[$ancestor_id]) && $output[$ancestor_id] != "") {
              unset($output[$ancestor_id]);
            }
          }
          $term_names[] = get_term($term_id, 'product_cat')->name;
          // Add the formatted ancestors with the product category to main array
          $output[$term_id] = implode(' > ', $term_names);
        }
      }
      $output = array_values($output);
      return $output;
    }
    /*
     * update last product sync data in DB table "ee_product_sync_data"
     */
    public function update_last_sync_in_db()
    {
      $ee_prod_mapped_cats = unserialize(get_option('ee_prod_mapped_cats'));
      $ee_prod_mapped_attrs = unserialize(get_option('ee_prod_mapped_attrs'));
      if ($ee_prod_mapped_cats != "" && $ee_prod_mapped_attrs != "" && !empty($ee_prod_mapped_cats)) {
        global $wpdb;
        $ee_product_sync_data = $wpdb->prefix . "ee_product_sync_data";
        foreach ($ee_prod_mapped_cats as $mc_key => $mappedCat) {
          $mc_key = intval($mc_key);
          //delete old product data of the category 
          $wpdb->query($wpdb->prepare("DELETE FROM $ee_product_sync_data where `w_cat_id` = '%d'", esc_sql($mc_key)));
          $args = array(
            'post_type' => 'product',
            'numberposts' => -1,
            'post_status' => 'publish',
            'tax_query' => array(
              array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $mc_key,
                'operator' => 'IN',
                'include_children' => false
              )
            )
          );
          $all_products = get_posts($args);
          $where = '`w_cat_id` = ' . esc_sql($mc_key);
          $p_c_ids = $this->TVC_Admin_DB_Helper->tvc_get_results_in_array('ee_product_sync_data', $where, array('w_product_id', 'w_cat_id'), true);
          if (!empty($all_products)) {
            foreach ($all_products as $postkey => $postvalue) {
              $t_data = array(
                'w_product_id' => esc_sql($postvalue->ID),
                'w_cat_id' => esc_sql($mc_key),
                'g_cat_id' => esc_sql(intval($mappedCat['id'])),
                'g_attribute_mapping' => json_encode($ee_prod_mapped_attrs),
                'update_date' => esc_sql(date('Y-m-d'))
              );
              //$table, $where, $field_name = "*"
              $p_c_id = $postvalue->ID . "_" . $mc_key;
              if (!in_array($p_c_id, $p_c_ids)) {
                $this->TVC_Admin_DB_Helper->tvc_add_row('ee_product_sync_data', $t_data, array("%d", "%d", "%d", "%s", "%s"));
              } else {
                $this->TVC_Admin_DB_Helper->tvc_update_row('ee_product_sync_data', $t_data, array('w_product_id' => esc_sql($postvalue->ID), 'w_cat_id' => esc_sql($mc_key)));
              }
            }
            wp_reset_postdata();
          }
        }
      }
    }

    /*
     * update batch wise product sync data in DB table "ee_product_sync_data"
     */
    public function update_last_sync_in_db_batch_wise($products, $feedId)
    {
      try {
        $ee_prod_mapped_attrs = unserialize(get_option('ee_prod_mapped_attrs')); 
        $TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
        $where ='`id` = '.esc_sql($feedId);
        $filed = array('attributes');
        $result = $TVC_Admin_DB_Helper->tvc_get_results_in_array("ee_product_feed", $where, $filed);
        if( $ee_prod_mapped_attrs != "" ){
          global $wpdb; 
          $product_ids = implode(',', array_column($products, 'w_product_id'));         
          $where ='`feedId` in ('.$feedId.') AND `w_product_id` in ('.$product_ids.')';
          $pids = $TVC_Admin_DB_Helper->tvc_get_results_in_array('ee_product_sync_data', $where, array('w_product_id'), true);               
          foreach($products as $key => $product) {
            $t_data = array(
              'w_product_id'=>esc_sql($product->w_product_id),
              'w_cat_id'=>esc_sql($product->w_cat_id),
              'g_cat_id'=>esc_sql($product->g_cat_id),
              'g_attribute_mapping'=> isset($result[0]['attributes'])? $result[0]['attributes'] : $ee_prod_mapped_attrs,
              'update_date'=>esc_sql(date('Y-m-d H:i:s', current_time('timestamp'))),
              'status'=> 1,
              'feedId'=> $feedId
            );
            if(!in_array($product->w_product_id, $pids)){
              $TVC_Admin_DB_Helper->tvc_add_row('ee_product_sync_data', $t_data, array("%d", "%d", "%d", "%s", "%s", "%d") );
            }else{
              $TVC_Admin_DB_Helper->tvc_update_row('ee_product_sync_data', $t_data, array('w_product_id'=> esc_sql($product->w_product_id), 'feedId'=> esc_sql($feedId) ));
            }
          }    
          wp_reset_postdata();
        }
      } catch (Exception $e) {
        $this->TVC_Admin_Helper->plugin_log($e->getMessage(), 'product_sync');
      }
    }

    /*
     * Update batch wise product sync data in DB table "ee_prouct_pre_sync_data"
     */
    public function update_product_status_pre_sync_data_ee($products, $feedId){
      try {
        $conv_prod_mapped_attrs = unserialize(get_option('ee_prod_mapped_attrs'));  
        if( $conv_prod_mapped_attrs != "" ){
          foreach($products as $product) {
            $t_data = array(
              'update_date'=>esc_sql(date( 'Y-m-d H:i:s', current_time( 'timestamp') )),
              'status'=>esc_sql(1)
            );
            $this->TVC_Admin_DB_Helper->tvc_update_row('ee_prouct_pre_sync_data', $t_data, array('w_product_id'=> esc_sql($product->w_product_id), 'feedId'=> esc_sql($feedId) ));
          }
          wp_reset_postdata();
        }
      } catch (Exception $e) {
        $this->TVC_Admin_Helper->plugin_log($e->getMessage(), 'product_sync');
      }    
    }
    public function update_product_status_pre_sync_data($last_sync_product_id = '')
    {
      try {
        $ee_prod_mapped_attrs = unserialize(get_option('ee_prod_mapped_attrs'));
        if ($ee_prod_mapped_attrs != "" && $last_sync_product_id > 0) {
          global $wpdb;
          $tablename = esc_sql($wpdb->prefix . "ee_prouct_pre_sync_data");
          $wpdb->query("UPDATE $tablename SET update_date = '" . esc_sql(date('Y-m-d H:i:s', current_time('timestamp'))) . "', status = " . esc_sql(1) . " WHERE id <= " . $last_sync_product_id);
          
          wp_reset_postdata();
        }
      } catch (Exception $e) {
        $this->TVC_Admin_Helper->plugin_log($e->getMessage(), 'product_sync');
      }
    }

    public function tvc_get_map_product_attribute($products, $tvc_currency, $merchantId)
    {
      try {
        if (!empty($products)) {
          global $wpdb;
          $tve_table_prefix = $wpdb->prefix;
          $plan_id = $this->TVC_Admin_Helper->get_plan_id();
          $items = [];
          $validProducts = [];
          $skipProducts = [];
          $product_ids = [];
          $deletedIds = [];
          $batchId = time();
          foreach ($products as $postkey => $postvalue) {
            if (get_post_status($postvalue->w_product_id) === 'publish') {
              $product_ids[] = $postvalue->w_product_id;
              $postmeta = [];
              $postmeta = $this->TVC_Admin_Helper->tvc_get_post_meta($postvalue->w_product_id);
              $prd = wc_get_product($postvalue->w_product_id);

              $postObj = (object) array_merge((array) get_post($postvalue->w_product_id), (array) $postmeta);
              $permalink = esc_url_raw(get_permalink($postvalue->w_product_id));
              $product = array(
                //'offer_id'=>sanitize_text_field($postvalue->w_product_id),
                'channel' => 'online',
                'link' => esc_url_raw(get_permalink($postvalue->w_product_id)),
                'google_product_category' => sanitize_text_field($postvalue->g_cat_id)
              );
              $temp_product = array();
              $fixed_att_select_list = array("gender", "age_group", "shipping", "tax", "content_language", "target_country", "condition");
              $formArray = json_decode($postvalue->g_attribute_mapping, true);
              foreach ($fixed_att_select_list as $fixed_key) {
                if (isset($formArray[$fixed_key]) && $formArray[$fixed_key] != "") {
                  if ($fixed_key == "shipping" && $formArray[$fixed_key] != "") {
                    $temp_product[$fixed_key]['price']['value'] = sanitize_text_field($formArray[$fixed_key]);
                    $temp_product[$fixed_key]['price']['currency'] = sanitize_text_field($tvc_currency);
                    $temp_product[$fixed_key]['country'] = sanitize_text_field($formArray['target_country']);
                  } else if ($fixed_key == "tax" && $formArray[$fixed_key] != "") {
                    $temp_product['taxes']['rate'] = sanitize_text_field($formArray[$fixed_key]);
                    $temp_product['taxes']['country'] = sanitize_text_field($formArray['target_country']);
                  } else if ($formArray[$fixed_key] != "") {
                    $temp_product[$fixed_key] = sanitize_text_field($formArray[$fixed_key]);
                  }
                }
                unset($formArray[$fixed_key]);
              }

              $product = array_merge($temp_product, $product);
              // for variable 
              if (!empty($prd) && $prd->get_type() == "variable") {
                $p_variations = $prd->get_children();
                if (!empty($p_variations)) {
                  foreach ($p_variations as $v_key => $variation_id) {
                    $variation = wc_get_product($variation_id);
                    if (empty($variation)) {
                      continue;
                    }
                    if ($variation->get_stock_status() != 'instock') {
                      continue;
                    }
                    $variation_description = wc_format_content($variation->get_description());
                    unset($product['customAttributes']);
                    $postmeta_var = (object) $this->TVC_Admin_Helper->tvc_get_post_meta($variation_id);
                    $formArray_val = $formArray['title'];
                    $product['title'] = (isset($postObj->$formArray_val)) ? sanitize_text_field($postObj->$formArray_val) : get_the_title($postvalue->w_product_id);
                    $tvc_temp_desc_key = $formArray['description'];
                    if ($tvc_temp_desc_key == 'post_excerpt' || $tvc_temp_desc_key == 'post_content' || $tvc_temp_desc_key == '') {
                      $product['description'] = ($variation_description != "") ? sanitize_text_field($variation_description) : sanitize_text_field($postObj->$tvc_temp_desc_key);
                    } else {
                      $product['description'] = sanitize_text_field($postObj->$tvc_temp_desc_key);
                    }
                    $product['item_group_id'] = esc_attr($postvalue->w_product_id);
                    $productTypes = $this->get_product_category($postvalue->w_product_id);
                    if (!empty($productTypes)) {
                      $product['productTypes'] = $productTypes;
                    }
                    $image_id = $variation->get_image_id();
                    $variation_permalink = esc_url_raw(get_permalink($variation_id));
                    $product['link'] = $variation_permalink != '' ? $variation_permalink : $permalink;
                    $product['image_link'] = esc_url_raw(wp_get_attachment_image_url($image_id, 'full'));
                    $variation_attributes = $variation->get_variation_attributes();
                    if (isset($variation_attributes) && !empty($variation_attributes)) {
                      foreach ($variation_attributes as $va_key => $va_value) {
                        $va_key = str_replace("_", " ", $va_key);
                        if (strpos($va_key, 'color') !== false) {
                          $product['color'] = $va_value;
                        } else if (strpos($va_key, 'size') !== false) {
                          $product['sizes'] = $va_value;
                        } else {
                          $va_key = str_replace("attribute", "", $va_key);
                          $product['customAttributes'][] = array("name" => $va_key, "value" => $va_value);
                        }
                      }
                    }

                    foreach ($formArray as $key => $value) {
                      if ($key == 'id') {
                        $product[$key] = isset($postmeta_var->$value) ? $postmeta_var->$value : $variation_id;
                        $product['offer_id'] = isset($postmeta_var->$value) ? $postmeta_var->$value : $variation_id;
                      } elseif ($key == 'gtin' && (isset($postmeta_var->$value) || isset($postObj->$value))) {
                        $product[$key] = isset($postmeta_var->$value) ? $postmeta_var->$value : $postObj->$value;
                      } elseif ($key == 'mpn' && (isset($postmeta_var->$value) || isset($postObj->$value))) {
                        $product[$key] = isset($postmeta_var->$value) ? $postmeta_var->$value : $postObj->$value;
                      } elseif ($key == 'price') {
                        if (isset($postmeta_var->$value) && $postmeta_var->$value > 0) {
                          $product[$key]['value'] = $postmeta_var->$value;
                        } else if (isset($postmeta_var->_regular_price) && $postmeta_var->_regular_price && $postmeta_var->_regular_price > 0) {
                          $product[$key]['value'] = $postmeta_var->_regular_price;
                        } else if (isset($postmeta_var->_price) && $postmeta_var->_price && $postmeta_var->_price > 0) {
                          $product[$key]['value'] = $postmeta_var->_price;
                        } else if (isset($postmeta_var->_sale_price) && $postmeta_var->_sale_price && $postmeta_var->_sale_price > 0) {
                          $product[$key]['value'] = $postmeta_var->_sale_price;
                        } else {
                          unset($product[$key]);
                        }
                        if (isset($product[$key]['value']) && $product[$key]['value'] > 0) {
                          $product[$key]['currency'] = sanitize_text_field($tvc_currency);
                        } else {
                          $skipProducts[$postmeta_var->ID] = $postmeta_var;
                        }
                      } else if ($key == 'sale_price') {
                        if (isset($postmeta_var->$value) && $postmeta_var->$value > 0) {
                          $product[$key]['value'] = $postmeta_var->$value;
                        } else if (isset($postmeta_var->_sale_price) && $postmeta_var->_sale_price && $postmeta_var->_sale_price > 0) {
                          $product[$key]['value'] = $postmeta_var->_sale_price;
                        } else {
                          unset($product[$key]);
                        }
                        if (isset($product[$key]['value']) && $product[$key]['value'] > 0) {
                          $product[$key]['currency'] = sanitize_text_field($tvc_currency);
                        }
                      } else if ($key == 'availability') {
                        $tvc_find = array("instock", "outofstock", "onbackorder");
                        $tvc_replace = array("in stock", "out of stock", "preorder");
                        if (isset($postmeta_var->$value) && $postmeta_var->$value != "") {
                          $stock_status = $postmeta_var->$value;
                          $stock_status = str_replace($tvc_find, $tvc_replace, $stock_status);
                          $product[$key] = sanitize_text_field($stock_status);
                        } else {
                          $stock_status = $postmeta_var->_stock_status;
                          $stock_status = str_replace($tvc_find, $tvc_replace, $stock_status);
                          $product[$key] = sanitize_text_field($stock_status);
                        }
                      } else if (in_array($key, array("brand"))) {
                        //list of cutom option added
                        $product_brand = "";
                        $is_custom_attr_brand = false;
                        $woo_attr_list = json_decode(json_encode($this->TVC_Admin_Helper->getTableData($tve_table_prefix . 'woocommerce_attribute_taxonomies', ['attribute_name'])), true);
                        if (!empty($woo_attr_list)) {
                          foreach ($woo_attr_list as $key_attr => $value_attr) {
                            if (isset($value_attr['field']) && $value_attr['field'] == $value) {
                              $is_custom_attr_brand = true;
                              $product_brand = $this->TVC_Admin_Helper->get_custom_taxonomy_name($postvalue->w_product_id, "pa_" . $value);
                            }
                          }
                        }
                        if ($is_custom_attr_brand == false && $product_brand == "") {
                          $product_brand = $this->TVC_Admin_Helper->add_additional_option_val_in_map_product_attribute($key, $postvalue->w_product_id);
                        }
                        if($product_brand === ""){
                          $domain = parse_url(get_site_url());
                          $product_brand = $domain['host'];
                        }
                        if ($product_brand != "") {
                          $product[$key] = sanitize_text_field($product_brand);
                        }
                      } else if($key == 'product_weight'){
                        if(isset($postmeta_var->$value) && $postmeta_var->$value != ""){
                          $product[$key]['value'] = sanitize_text_field($postmeta_var->$value);
                          $product[$key]['unit'] = get_option('woocommerce_weight_unit');
                        }
                        
                      } else if($key == 'shipping_weight'){
                        if(isset($postObj->$value) && $postObj->$value != ""){
                          $product[$key]['value'] = sanitize_text_field($postObj->$value);
                          $product[$key]['unit'] = get_option('woocommerce_weight_unit');
                        }
                        
                      } else if (isset($postmeta_var->$value) && $postmeta_var->$value != "") {
                        $product[$key] = sanitize_text_field($postmeta_var->$value);
                      }
                    }
                    $item = [
                      'merchant_id' => sanitize_text_field($merchantId),
                      'batch_id' => sanitize_text_field(++$batchId),
                      'method' => 'insert',
                      'product' => $product
                    ];
                    $items[] = $item;
                    $validProducts[] = $postvalue;
                  }
                } else {
                  //Delete the variant product which does not have children
                  $deletedIds[] = $postvalue->w_product_id;
                }
              } else if (!empty($prd)) { // for simple product 
                if ($prd->get_stock_status() != 'instock') {
                  continue;
                }
                $image_id = $prd->get_image_id();
                $product['image_link'] = esc_url_raw(wp_get_attachment_image_url($image_id, 'full'));
                $productTypes = $this->get_product_category($postvalue->w_product_id);
                if (!empty($productTypes)) {
                  $product['productTypes'] = $productTypes;
                }
                foreach ($formArray as $key => $value) {
                  if ($key == 'id') {
                    $product[$key] = isset($postObj->$value) ? $postObj->$value : $postvalue->w_product_id;
                    $product['offer_id'] = isset($postObj->$value) ? $postObj->$value : $postvalue->w_product_id;
                  } elseif ($key == 'price') {
                    if (isset($postObj->$value) && $postObj->$value > 0) {
                      $product[$key]['value'] = $postObj->$value;
                    } else if (isset($postObj->_regular_price) && $postObj->_regular_price && $postObj->_regular_price > 0) {
                      $product[$key]['value'] = $postObj->_regular_price;
                    } else if (isset($postObj->_price) && $postObj->_price && $postObj->_price > 0) {
                      $product[$key]['value'] = $postObj->_price;
                    } else if (isset($postObj->_sale_price) && $postObj->_sale_price && $postObj->_sale_price > 0) {
                      $product[$key]['value'] = $postObj->_sale_price;
                    }
                    if (isset($product[$key]['value']) && $product[$key]['value'] > 0) {
                      $product[$key]['currency'] = sanitize_text_field($tvc_currency);
                    } else {
                      $skipProducts[$postObj->ID] = $postObj;
                    }
                  } else if ($key == 'sale_price') {
                    if (isset($postObj->$value) && $postObj->$value > 0) {
                      $product[$key]['value'] = $postObj->$value;
                    } else if (isset($postObj->_sale_price) && $postObj->_sale_price && $postObj->_sale_price > 0) {
                      $product[$key]['value'] = $postObj->_sale_price;
                    }
                    if (isset($product[$key]['value']) && $product[$key]['value'] > 0) {
                      $product[$key]['currency'] = sanitize_text_field($tvc_currency);
                    }
                  } else if ($key == 'availability') {
                    $tvc_find = array("instock", "outofstock", "onbackorder");
                    $tvc_replace = array("in stock", "out of stock", "preorder");
                    if (isset($postObj->$value) && $postObj->$value != "") {
                      $stock_status = $postObj->$value;
                      $stock_status = str_replace($tvc_find, $tvc_replace, $stock_status);
                      $product[$key] = sanitize_text_field($stock_status);
                    } else {
                      $stock_status = $postObj->_stock_status;
                      $stock_status = str_replace($tvc_find, $tvc_replace, $stock_status);
                      $product[$key] = sanitize_text_field($stock_status);
                    }
                  } else if (in_array($key, array("brand"))) {
                    //list of cutom option added
                    $product_brand = "";
                    $is_custom_attr_brand = false;
                    $woo_attr_list = json_decode(json_encode($this->TVC_Admin_Helper->getTableData($tve_table_prefix . 'woocommerce_attribute_taxonomies', ['attribute_name'])), true);
                    if (!empty($woo_attr_list)) {
                      foreach ($woo_attr_list as $key_attr => $value_attr) {
                        if (isset($value_attr['field']) && $value_attr['field'] == $value) {
                          $is_custom_attr_brand = true;
                          $product_brand = $this->TVC_Admin_Helper->get_custom_taxonomy_name($postvalue->w_product_id, "pa_" . $value);
                        }
                      }
                    }
                    if ($is_custom_attr_brand == false && $product_brand == "") {
                      $product_brand = $this->TVC_Admin_Helper->add_additional_option_val_in_map_product_attribute($key, $postvalue->w_product_id);
                    }
                    if($product_brand === ""){
                      $domain = parse_url(get_site_url());
                      $product_brand = $domain['host'];
                    }
                    if ($product_brand != "") {
                      $product[$key] = sanitize_text_field($product_brand);
                    }
                  } else if($key == 'product_weight'){
                    if(isset($postObj->$value) && $postObj->$value != ""){
                      $product[$key]['value'] = sanitize_text_field($postObj->$value);
                      $product[$key]['unit'] = get_option('woocommerce_weight_unit');
                    }
                    
                  } else if($key == 'shipping_weight'){
                    if(isset($postObj->$value) && $postObj->$value != ""){
                      $product[$key]['value'] = sanitize_text_field($postObj->$value);
                      $product[$key]['unit'] = get_option('woocommerce_weight_unit');
                    }
                    
                  } else if (isset($postObj->$value) && $postObj->$value != "") {
                    $product[$key] = $postObj->$value;
                  }
                }
                $item = [
                  'merchant_id' => sanitize_text_field($merchantId),
                  'batch_id' => sanitize_text_field(++$batchId),
                  'method' => 'insert',
                  'product' => $product
                ];
                $items[] = $item;
                $validProducts[] = $postvalue;
              }
            }
          }
          return array('items' => $items, 'valid_products' => $validProducts, 'deleted_products' => $deletedIds, 'skipProducts' => $skipProducts, 'product_ids' => $product_ids);
        }
      } catch (Exception $e) {
        $this->TVC_Admin_Helper->plugin_log($e->getMessage(), 'product_sync');
      }
    }
    public function call_auto_sync_product($last_sync_product_id = array())
    {
      $ee_additional_data = $this->TVC_Admin_Helper->get_ee_additional_data();
      global $wpdb;
      $feedTable = $wpdb->prefix.'ee_product_feed';
      $feedData = $wpdb->get_results($wpdb->prepare("select * from `$feedTable` where `Id` = %d", 1), OBJECT);
      if(end($feedData)->is_default == 1 && end($feedData)->auto_schedule == 1){
        if (!empty($ee_additional_data) && isset($ee_additional_data['is_auto_sync_start']) && $ee_additional_data['is_auto_sync_start'] == true) {
          $product_count = $this->TVC_Admin_DB_Helper->tvc_row_count('ee_product_sync_data');       
          //$count = 0;
          $pre_last_sync_product_id = sanitize_text_field($last_sync_product_id);
          if ($product_count > 0) {
            $tvc_currency = sanitize_text_field($this->TVC_Admin_Helper->get_woo_currency());
            $merchantId = sanitize_text_field($this->TVC_Admin_Helper->get_merchantId());
            $accountId = sanitize_text_field($this->TVC_Admin_Helper->get_main_merchantId());
            $subscriptionId = sanitize_text_field($this->TVC_Admin_Helper->get_subscriptionId());
            $last_sync_product_id = sanitize_text_field(($last_sync_product_id > 0) ? $last_sync_product_id : 0);
            
            $tablename = $wpdb->prefix . 'ee_product_sync_data';
            $last_sync_product_id = esc_sql(intval($last_sync_product_id));
            $product_batch_size = esc_sql(intval($this->batch_size));
            $products = $wpdb->get_results($wpdb->prepare("select * from `$tablename` where `feedId` = '1' and `id` > %d LIMIT %d", $last_sync_product_id, $product_batch_size), OBJECT);
            
            $entries = array();
            if (!empty($products)) {
              $p_map_attribute = $this->tvc_get_map_product_attribute($products, $tvc_currency, $merchantId);
              //Delete the variant product which does not have children
              if (!empty($p_map_attribute) && isset($p_map_attribute['deleted_products']) && !empty($p_map_attribute['deleted_products'])) {
                $dids = esc_sql(implode(', ', $p_map_attribute['deleted_products']));
                $wpdb->query("DELETE FROM $tablename where `w_product_id` in ($dids)");
              }
              if (!empty($p_map_attribute) && isset($p_map_attribute['items']) && !empty($p_map_attribute['items'])) {
                // call product sync API
                $data = [
                  'merchant_id' => sanitize_text_field($accountId),
                  'account_id' => sanitize_text_field($merchantId),
                  'subscription_id' => sanitize_text_field($subscriptionId),
                  'entries' => $p_map_attribute['items']
                ];              
                //$this->TVC_Admin_Helper->plugin_log("Auto - before product sync API Call for " . count($p_map_attribute['items']) . " products", 'product_sync');
                $response = $this->customApiObj->products_sync($data);
                $sync_status = 0;
                if ($response->error == false) {
                  $sync_status = 1;
                }
                // End call product sync API
                //$this->TVC_Admin_Helper->plugin_log("Auto - after product sync API Call", 'product_sync');
                $sync_product_ids = (isset($p_map_attribute['product_ids'])) ? $p_map_attribute['product_ids'] : "";
                $last_sync_product_id = end($products)->id;
                $total_sync_product = 0;
                $action_scheduler_id = "";
                $last_sync = date('Y-m-d H:i:s', current_time('timestamp'));
                $next_sync = date('Y-m-d H:i:s', current_time('timestamp') + $this->time_space);
                if ($pre_last_sync_product_id == 0) {
                  $last_sync_row = $this->TVC_Admin_DB_Helper->tvc_get_last_row('ee_product_sync_call');
                  $total_sync_product = $sync_product_ids != '' ? count($sync_product_ids) : 0;
                  if (!empty($last_sync_row)) {
                    $action_scheduler_id = $last_sync_row['id'] + 1;
                    $last_sync = $last_sync_row['create_sync'];
                    $next_sync = date('Y-m-d H:i:s', current_time('timestamp') + $this->time_space);
                  } else {
                    $action_scheduler_id = 1;
                  }
                } else {
                  $last_sync_row = $this->TVC_Admin_DB_Helper->tvc_get_last_row('ee_product_sync_call');
                  if (!empty($last_sync_row)) {
                    $count_Sync_product_ids = $sync_product_ids != '' ? count($sync_product_ids) : 0;
                    $total_sync_product = $count_Sync_product_ids + $last_sync_row['total_sync_product'];
                    $action_scheduler_id = $last_sync_row['action_scheduler_id'];
                    $next_sync = $last_sync_row['next_sync'];
                    $last_sync = $last_sync_row['last_sync'];
                  }
                }
                $t_data = array(
                  'sync_product_ids' => json_encode($sync_product_ids),
                  'w_total_product' => esc_sql($product_count),
                  'total_sync_product' => esc_sql($total_sync_product),
                  'last_sync' => esc_sql($last_sync),
                  'create_sync' => date('Y-m-d H:i:s', current_time('timestamp')),
                  'next_sync' => esc_sql($next_sync),
                  'last_sync_product_id' => esc_sql($last_sync_product_id),
                  'action_scheduler_id' => esc_sql($action_scheduler_id),
                  'status' => esc_sql($sync_status)
                );
                $this->TVC_Admin_DB_Helper->tvc_add_row('ee_product_sync_call', $t_data, array("%s", "%d", "%d", "%s", "%s", "%s", "%d", "%d", "%d"));
                $feed_data = array(
                  "product_sync_alert" => NULL,
                  "is_process_start" => false,
                  "is_auto_sync_start" => true,
                  "last_sync_date" => esc_sql($last_sync),
                  "next_schedule_date" => esc_sql($next_sync),
                );
                $this->TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => '1'));
                $this->TVC_Admin_Helper->plugin_log("Update data in product feed table", 'product_sync');
                as_enqueue_async_action('ee_auto_product_sync_check', array('last_sync_product_id' => intval($last_sync_product_id)));
                $this->TVC_Admin_Helper->plugin_log("Auto - Done and update ee_auto_product_sync_check", 'product_sync');
              }
            }
          }
        }
      }      
    }
    public function add_schedule_event()
    {
      $row_count = $this->TVC_Admin_DB_Helper->tvc_row_count('ee_product_sync_data');
      if ($row_count > 0) {
        if (function_exists('as_next_scheduled_action') && false === as_next_scheduled_action('ee_auto_product_sync_check')) {
          //strtotime( 'midnight tonight' )
          as_schedule_recurring_action(esc_attr($this->timestamp), esc_attr($this->time_space), 'ee_auto_product_sync_check', array("last_sync_product_id" => 0), "product_sync");
        }
      }
    }

    /*protected function maybe_remove_cronjobs() {
    if ( function_exists( 'as_next_scheduled_action' ) && as_next_scheduled_action( 'ee_auto_product_sync_check' ) ) {
    as_unschedule_all_actions( 'ee_auto_product_sync_check' );
    }
    if ( function_exists( 'as_next_scheduled_action' ) && as_next_scheduled_action( 'ee_auto_product_sync_recheck' ) ) {
    as_unschedule_all_actions( 'ee_auto_product_sync_recheck' );
    }
    }*/

    public function generateAccessToken($access_token, $refresh_token)
    {
      $url = "https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=" . $access_token;
      $request = wp_remote_get(esc_url_raw($url), array('timeout' => 10000));      
      $result = json_decode(wp_remote_retrieve_body($request));
      if (isset($result->error) && $result->error) {
        $credentials = json_decode(file_get_contents(ENHANCAD_PLUGIN_DIR . 'includes/setup/json/client-secrets.json'), true);
        $url = 'https://www.googleapis.com/oauth2/v4/token';
        $header = array("Content-Type" => "application/json");
        $clientId = $credentials['web']['client_id'];
        $clientSecret = $credentials['web']['client_secret'];

        $data = [
          "grant_type" => 'refresh_token',
          "client_id" => sanitize_text_field($clientId),
          'client_secret' => sanitize_text_field($clientSecret),
          'refresh_token' => sanitize_text_field($refresh_token),
        ];
        $args = array(
          'headers' => $header,
          'method' => 'POST',
          'body' => wp_json_encode($data)
        );
        $request = wp_remote_post(esc_url_raw($url), $args);
        // Retrieve information
        //$response_code = wp_remote_retrieve_response_code($request);
        //$response_message = wp_remote_retrieve_response_message($request);
        $response = json_decode(wp_remote_retrieve_body($request));
        if (isset($response->access_token)) {
          $TVC_Admin_Helper = new TVC_Admin_Helper();
          $google_detail = $TVC_Admin_Helper->get_ee_options_data();
          $google_detail["setting"]->access_token = base64_encode(sanitize_text_field($response->access_token));
          $TVC_Admin_Helper->set_ee_options_data($google_detail);
          return $response->access_token;
        } else {
          //return $access_token;
        }
      } else {
        return $access_token;
      }
    }
    public function get_tvc_access_token()
    {
      if (!empty($this->access_token)) {
        return $this->access_token;
      } else {
        $google_detail = $this->TVC_Admin_Helper->get_ee_options_data();
        $this->access_token = sanitize_text_field(base64_decode($google_detail['setting']->access_token));
        return $this->access_token;
      }
    }

    public function get_tvc_refresh_token()
    {
      if (!empty($this->refresh_token)) {
        return $this->refresh_token;
      } else {
        $google_detail = $this->TVC_Admin_Helper->get_ee_options_data();
        $this->refresh_token = sanitize_text_field(base64_decode($google_detail['setting']->refresh_token));
        return $this->refresh_token;
      }
    }
    /*      
    function tvc_add_cron_interval( $schedules ) { 
    $schedules['five_seconds'] = array(
    'interval' => 5,
    'display'  => esc_html__( 'Every Five Seconds' ) );
    return $schedules;
    }
    */
    public function tvc_add_cron_interval_for_product_sync($schedules)
    {
      $schedules['product_sync_interval'] = array(
        'interval' => 180,
        'display' => esc_html__('Every Five Seconds')
      );
      return $schedules;
    }

   
    public function call_auto_sync_product_feed_wise_default($feedId)
    {
      $conv_additional_data = $this->TVC_Admin_Helper->get_ee_additional_data();
      $this->TVC_Admin_Helper->plugin_log("call_auto_sync_product_feed_wise_default", 'product_sync');
      try {
        $google_detail = $this->TVC_Admin_Helper->get_ee_options_data();
        if (isset($google_detail['setting'])) {
          if ($google_detail['setting']) {
            $googleDetail = $google_detail['setting'];
          }
        }
        global $wpdb;
        $startTime = new DateTime();
        $tablename = esc_sql($wpdb->prefix . "ee_product_sync_data");
        $product_count = $wpdb->get_var("SELECT COUNT(*) as a FROM $tablename where `feedId` = $feedId AND `status` = 0");
        $where = '`id` = ' . esc_sql($feedId);
        $filed = array(
          'attributes',
          'auto_schedule',
          'next_schedule_date',
          'auto_sync_interval',
          'channel_ids',
          'is_default'
        );
        $result = $this->TVC_Admin_DB_Helper->tvc_get_results_in_array("ee_product_feed", $where, $filed);
        if ($result[0]['is_default'] == 1) {
          if ($product_count > 0) {
            $product_batch_size = (isset($conv_additional_data['product_sync_batch_size']) && $conv_additional_data['product_sync_batch_size']) ? $conv_additional_data['product_sync_batch_size'] : 100;
            $conv_currency = sanitize_text_field($this->TVC_Admin_Helper->get_woo_currency());
            $merchantId = sanitize_text_field($this->merchantId);
            $accountId = sanitize_text_field($this->accountId);
            $subscriptionId = sanitize_text_field($this->subscriptionId);
            $product_batch_size = esc_sql(intval($product_batch_size));
            $products = $wpdb->get_results($wpdb->prepare("select * from  `$tablename` where `feedId` = %d AND `status` = 0 LIMIT %d", [$feedId, $product_batch_size]), OBJECT);

            if (!empty($products)) {
              $p_map_attribute = $this->tvc_get_map_product_attribute($products, $conv_currency, $merchantId);
              //Delete the variant product which does not have children
              if (!empty($p_map_attribute) && isset($p_map_attribute['deleted_products']) && !empty($p_map_attribute['deleted_products'])) {
                $dids = esc_sql(implode(', ', $p_map_attribute['deleted_products']));
                $wpdb->query("DELETE FROM $tablename where `w_product_id` in ($dids)");
              }
              $CONV_Admin_Auto_Product_sync_Helper = new TVC_Admin_Auto_Product_sync_Helper();
              $CONV_Admin_Auto_Product_sync_Helper->update_last_sync_in_db_batch_wise($p_map_attribute['valid_products'], $feedId);
              if (!empty($p_map_attribute) && isset($p_map_attribute['items']) && !empty($p_map_attribute['items'])) {
                // call product sync API
                $data = [
                  'merchant_id' => isset($accountId) === TRUE ? sanitize_text_field($accountId) : '',
                  'account_id' => isset($merchantId) === TRUE ?  sanitize_text_field($merchantId) : '',
                  'subscription_id' => sanitize_text_field($subscriptionId),
                  'store_feed_id' => sanitize_text_field($feedId),
                  'is_on_gmc' => strpos($result[0]['channel_ids'], '1') !== false ? true : false,
                  'is_on_tiktok' => strpos($result[0]['channel_ids'], '1') !== false ? true : false,
                  'is_on_facebook' => false,
                  'business_id' => '',
                  'catalog_id' => '',
                  'tiktok_catalog_id' => '',
                  'tiktok_business_id' => '',
                  'entries' => $p_map_attribute['items']
                ];
                $this->TVC_Admin_Helper->plugin_log("Auto before product sync API Call for " . isset($p_map_attribute['items']) ? count($p_map_attribute['items']) : 0 . " products", 'product_sync');
                /**************************** API Call to GMC ****************************************************************************/
                $response = $this->customApiObj->feed_wise_products_sync($data);
                if ($response->error == false) {
                  $product_count = $wpdb->get_var("SELECT COUNT(*) as a FROM $tablename where `feedId` = $feedId AND `status` = 0");
                  if ($product_count > 0) {
                    as_schedule_single_action(time() + 5, 'set_recurring_auto_sync_product_feed_wise', array("feedId" => $feedId));
                    $this->TVC_Admin_Helper->plugin_log("Recall auto recurring product sync process done", 'product_sync');
                  } else {
                    $last_sync_date = date('Y-m-d H:i:s', current_time('timestamp'));
                    $next_schedule_date = NULL;
                    if ($result[0]['auto_schedule'] == 1) {
                      $next_schedule_date = date('Y-m-d H:i:s', strtotime('+' . $result[0]['auto_sync_interval'] . 'day', current_time('timestamp')));
                      // add scheduled cron job            
                      as_schedule_single_action($next_schedule_date, 'set_recurring_auto_sync_product_feed_wise', array("feedId" => $feedId));
                    }
                    $feed_data = array(
                      "product_sync_alert" => NULL,
                      "is_process_start" => false,
                      "is_auto_sync_start" => true,
                      "last_sync_date" => esc_sql($last_sync_date),
                      "next_schedule_date" => $next_schedule_date,
                    );
                    $this->TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => $feedId));
                    $syn_data = array(
                      "status" => 0,
                    );
                    $this->TVC_Admin_DB_Helper->tvc_update_row("ee_product_sync_data", $syn_data, array("id" => $feedId));
                    $this->TVC_Admin_Helper->plugin_log("Call auto recurring product sync process done", 'product_sync');
                  }
                } else {
                  if (isset($response->message) && $response->message != "") {
                    $this->TVC_Admin_Helper->plugin_log($response->message, 'product_sync');
                    $conv_additional_data['product_sync_alert'] = $response->message;
                    $this->TVC_Admin_Helper->set_ee_additional_data($conv_additional_data);
                    $feed_data = array(
                      "product_sync_alert" => $response->message,
                    );
                    $this->TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => $feedId));
                  }
                  return true;
                }

              }
            }
          } else {
            $last_sync_date = date('Y-m-d H:i:s', current_time('timestamp'));
            $next_schedule_date = NULL;
            if ($result[0]['auto_schedule'] == 1) {
              $next_schedule_date = date('Y-m-d H:i:s', strtotime('+' . $result[0]['auto_sync_interval'] . 'day', current_time('timestamp')));
              // add scheduled cron job            
              as_schedule_single_action($next_schedule_date, 'set_recurring_auto_sync_product_feed_wise', array("feedId" => $feedId));
            }
          }
        }
      } catch (Exception $e) {
        $feed_data = array(
          "product_sync_alert" => $e->getMessage(),
        );
        $this->TVC_Admin_DB_Helper->tvc_update_row("ee_product_feed", $feed_data, array("id" => $feedId));
        $conv_additional_data['product_sync_alert'] = $e->getMessage();
        $this->TVC_Admin_Helper->set_ee_additional_data($conv_additional_data);
        $this->TVC_Admin_Helper->plugin_log($e->getMessage(), 'product_sync');
      }
    }
  } // end Class
}
new TVC_Admin_Auto_Product_sync_Helper();
?>