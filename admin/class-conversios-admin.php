<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       tatvic.com
 * @since      1.0.0
 *
 * @package    Enhanced_Ecommerce_Google_Analytics
 * @subpackage Enhanced_Ecommerce_Google_Analytics/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Enhanced_Ecommerce_Google_Analytics
 * @subpackage Enhanced_Ecommerce_Google_Analytics/admin
 * @author     Tatvic
 */
if (!class_exists('Conversios_Admin')) {
  class Conversios_Admin extends TVC_Admin_Helper
  {
    protected $google_detail;
    protected $url;
    protected $version;
    protected $plan_id;
    public function __construct()
    {
      $this->version = PLUGIN_TVC_VERSION;
      $this->includes();
      $this->url = $this->get_onboarding_page_url(); // use in setting page
      $this->google_detail = $this->get_ee_options_data();
      add_action('admin_menu', array($this, 'add_admin_pages'));
      add_action('admin_init', array($this, 'init'));
      add_action("admin_print_styles", [$this, 'dequeue_css']);
      $this->plan_id = $this->get_plan_id();
      if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))) && $this->plan_id != 1) {
        add_action('woocommerce_order_fully_refunded', array($this, 'action_woocommerce_order_refunded'), 10, 2);
        add_action('woocommerce_order_partially_refunded', array($this, 'woocommerce_partial_order_refunded'), 10, 2);
      }
    }
    public function includes()
    {
      if (!class_exists('Conversios_Header')) {
        require_once(ENHANCAD_PLUGIN_DIR . 'admin/partials/class-conversios-header.php');
      }
      if (!class_exists('Conversios_Footer')) {
        require_once(ENHANCAD_PLUGIN_DIR . 'admin/partials/class-conversios-footer.php');
      }
    }

    public function init()
    {
      add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
      add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    function dequeue_css()
    {
      $screen = get_current_screen();
      global $wp_scripts, $wp_styles;
      if (str_contains($screen->id, 'conversios-pro_page_') || $screen->id === 'toplevel_page_conversios') {
        $not_allowed_css = array('porto_admin', 'flashy');
        foreach ($wp_styles->queue as $key => $value) {
          if (in_array($value, $not_allowed_css)) {
            wp_deregister_style($value);
            wp_dequeue_style($value);
          }
        }
      }
    }

    /**
     * Woo Full order refund.
     *
     * @since    1.0.0
     */
    public function action_woocommerce_order_refunded($order_id, $refund_id)
    {
      $data = unserialize(get_option('ee_options'));
      if (
        empty($data['ga_eeT']) ||
        get_post_meta($order_id, "tvc_tracked_refund", true) == 1 || $this->plan_id == 1
      ) {
        return;
      }
      $refund = wc_get_order($refund_id);
      $value = $refund->get_amount();
      $query = urlencode('/refundorders/');
      $currency = $this->get_woo_currency();
      $client_id = mt_rand(1000000000, 9999999999) . "." . time();
      $ga_id = $data['ga_id'];
      $total_refunds = 0;
      if ($ga_id) {
        $url = "https://www.google-analytics.com/collect?v=1&t=event&ni=1&cu=" . $currency . "&ec=Enhanced-Ecommerce&ea=click&el=full_refund&tid=" . $ga_id . "&cid=" . $client_id . "&ti=" . $order_id . "&pa=refund&tr=" . $value . "&dp=" . $query;
        $request = wp_remote_get(esc_url_raw($url), array('timeout' => 1000));
      }
      $gm_id = sanitize_text_field($data['gm_id']);
      $api_secret = sanitize_text_field($data['ga4_api_secret']);
      if ($gm_id && $api_secret) {
        $postData = array(
          "client_id" => $client_id,
          "non_personalized_ads" => true,
          "events" => [
            array(
              "name" => "refund",
              "params" => array(
                "currency" => $currency,
                "transaction_id" => $order_id,
                "value" => $value
              )
            )
          ]
        );
        $args = array(
          'method' => 'POST',
          'body' => wp_json_encode($postData)
        );
        $url = "https://www.google-analytics.com/mp/collect?measurement_id=" . $gm_id . "&api_secret=" . $api_secret;
        $request = wp_remote_post(esc_url_raw($url), $args);
      }
      update_post_meta($order_id, "tvc_tracked_refund", 1);
    }

    /**
     * Woo Partial order refund.
     *
     * @since    1.0.0
     */
    public function woocommerce_partial_order_refunded($order_id, $refund_id)
    {
      $data = unserialize(get_option('ee_options'));
      if (empty($data['ga_eeT']) || $this->plan_id == 1) {
        return;
      }
      $refund = wc_get_order($refund_id);
      $value = $refund->get_amount();
      $refunded_items = array();
      $currency = $this->get_woo_currency();
      $client_id = mt_rand(1000000000, 9999999999) . "." . time();
      $query_params = array();
      $i = 1;
      //GA3
      $ga_id = $data['ga_id'];
      if ($ga_id) {
        foreach ($refund->get_items('line_item') as $item_id => $item) {
          $query_params["pr{$i}id"] = $item['product_id'];
          $query_params["pr{$i}qt"] = abs($item['qty']);
          $query_params["pr{$i}pr"] = abs($item['total']);
          $i++;
        }
        $param_url = http_build_query($query_params, '', '&');
        $url = "https://www.google-analytics.com/collect?v=1&t=event&ni=1&cu=" . $currency . "&ec=Enhanced-Ecommerce&ea=Refund&el=partial_refunded&tid=" . sanitize_text_field($ga_id) . "&cid=" . $client_id . "&tr=" . $value . "&ti=" . $order_id . "&pa=refund&" . $param_url;
        $request = wp_remote_get(esc_url_raw($url), array('timeout' => 1000));
      }
      //GA4
      $gm_id = sanitize_text_field($data['gm_id']);
      $api_secret = sanitize_text_field($data['ga4_api_secret']);
      if ($gm_id && $api_secret) {
        $items = array();
        foreach ($refund->get_items('line_item') as $item_id => $item) {
          $items[] = array("item_id" => $item['product_id'], "item_name" => $item['name'], "quantity" => abs($item['qty']), "price" => abs($item['total']), "currency" => $currency);
        }
        $postData = array(
          "client_id" => $client_id,
          "non_personalized_ads" => true,
          "events" => [
            array(
              "name" => "refund",
              "params" => array(
                "items" => $items,
                "currency" => $currency,
                "transaction_id" => $order_id,
                "value" => $value
              )
            )
          ]
        );
        $args = array(
          'method' => 'POST',
          'body' => wp_json_encode($postData)
        );
        $url = "https://www.google-analytics.com/mp/collect?measurement_id=" . $gm_id . "&api_secret=" . $api_secret;
        $request = wp_remote_post(esc_url_raw($url), $args);
      }
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    4.1.4
     */
    public function enqueue_styles()
    {
      $screen = get_current_screen();
      if ($screen->id == 'toplevel_page_conversios' || (isset($_GET['page']) && strpos(sanitize_text_field(filter_input(INPUT_GET, 'page')), 'conversios') !== false)) {
        //developres hook to custom css
        do_action('add_conversios_css_' . sanitize_text_field(wp_unslash(filter_input(INPUT_GET, 'page'))));
        //conversios page css
        if (sanitize_text_field(wp_unslash(filter_input(INPUT_GET, 'page'))) == "conversios-analytics-reports" || sanitize_text_field(wp_unslash(filter_input(INPUT_GET, 'page'))) == "conversios") {
          wp_register_style('conversios-slick-css', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/css/slick.css'));
          wp_enqueue_style('conversios-slick-css');
          wp_register_style('conversios-daterangepicker-css', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/css/daterangepicker.css'));
          wp_enqueue_style('conversios-daterangepicker-css');
        } else if (sanitize_text_field(wp_unslash(filter_input(INPUT_GET, 'page'))) == "conversios-pmax") {
          //wp_register_style('tvc-bootstrap-datepicker-css', esc_url_raw(ENHANCAD_PLUGIN_URL. '/includes/setup/plugins/datepicker/bootstrap-datepicker.min.css'));
          //wp_enqueue_style('tvc-bootstrap-datepicker-css');
          wp_register_style('jquery-ui', esc_url_raw(ENHANCAD_PLUGIN_URL . '/includes/setup/plugins/datepicker/jquery-ui.css'));
          wp_enqueue_style('jquery-ui');
        }
        //all conversios page css 
        if ($screen->id != "conversios-pro_page_conversios-google-analytics" && $screen->id != "conversios-pro_page_conversios-google-shopping-feed") {
          wp_enqueue_style('conversios-style-css', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/css/style.css'), array(), esc_attr($this->version), 'all');
        }

        wp_enqueue_style('conversios-responsive-css', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/css/responsive.css'), array(), esc_attr($this->version), 'all');
      }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    4.1.4
     */
    public function enqueue_scripts()
    {
      $screen = get_current_screen();
      if ($screen->id == 'toplevel_page_conversios' || (isset($_GET['page']) && strpos(sanitize_text_field(wp_unslash(filter_input(INPUT_GET, 'page'))), 'conversios') !== false)) {
        if (sanitize_text_field(wp_unslash(filter_input(INPUT_GET, 'page'))) == "conversios-analytics-reports" || sanitize_text_field(wp_unslash(filter_input(INPUT_GET, 'page'))) == "conversios") {
          wp_enqueue_script('conversios-chart-js', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/js/chart.js'));
          wp_enqueue_script('conversios-chart-datalabels-js', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/js/chartjs-plugin-datalabels.js'));
          wp_enqueue_script('conversios-basictable-js', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/js/jquery.basictable.min.js'));
          wp_enqueue_script('conversios-moment-js', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/js/moment.min.js'));
          wp_enqueue_script('conversios-daterangepicker-js', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/js/daterangepicker.js'));

          wp_enqueue_script('conversios-custom-js', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/js/tvc-ee-custom.js'), array('jquery'), esc_attr($this->version), false);
        } else if (sanitize_text_field(wp_unslash(filter_input(INPUT_GET, 'page'))) == "conversios-pmax") {
          //wp_enqueue_script( 'conversios-chart-js', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/js/chart.js') );
          wp_enqueue_script('conversios-pmax-js', esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/js/pmax-custom.js'), array('jquery'), esc_attr($this->version), false);
          wp_register_script('tvc-bootstrap-datepicker-js', esc_url_raw(ENHANCAD_PLUGIN_URL . '/includes/setup/plugins/datepicker/bootstrap-datepicker.min.js'));
          wp_enqueue_script('tvc-bootstrap-datepicker-js');
          wp_enqueue_script('jquery-ui-datepicker');
        }
      }
    }

    /**
     * Display Admin Page.
     *
     * @since    4.1.4
     */
    public function add_admin_pages()
    {
      global $wp_roles;
      if (is_user_logged_in()) {
        if (current_user_can('administrator') && !current_user_can('manage_aioconversios')) {
          $wp_roles->add_cap('administrator', 'manage_aioconversios');
          header("Refresh:0");
        }
      }

      $google_detail = $this->google_detail;
      $plan_id = 1;
      if (isset($google_detail['setting'])) {
        $googleDetail = $google_detail['setting'];
        if (isset($googleDetail->plan_id) && !in_array($googleDetail->plan_id, array("1"))) {
          $plan_id = $googleDetail->plan_id;
        }
      }
      $icon = ENHANCAD_PLUGIN_URL . "/admin/images/offer.png";
      $freevspro = ENHANCAD_PLUGIN_URL . "/admin/images/freevspro.png";
      add_menu_page(
        '',
        esc_html__('Conversios SST', 'enhanced-e-commerce-for-woocommerce-store') . '',
        'manage_aioconversios',
        CONV_MENU_SLUG,
        array($this, 'showPage'),
        esc_url_raw(plugin_dir_url(__FILE__) . 'images/tatvic_logo.png'),
        26
      );
      if (!function_exists('is_plugin_active_for_network')) {
        require_once(ABSPATH . '/wp-admin/includes/woocommerce.php');
      }

      add_submenu_page(
        CONV_MENU_SLUG,
        '',
        '',
        'manage_aioconversios',
        CONV_MENU_SLUG,
        0
      );

      add_submenu_page(
        CONV_MENU_SLUG,
        esc_html__('Pixels & Analytics', 'enhanced-e-commerce-for-woocommerce-store'),
        esc_html__('Pixels & Analytics', 'enhanced-e-commerce-for-woocommerce-store'),
        'manage_aioconversios',
        'conversios-google-analytics',
        array($this, 'showPage'),
        1
      );

      add_submenu_page(
        CONV_MENU_SLUG,
        esc_html__('Account Settings', 'enhanced-e-commerce-for-woocommerce-store'),
        esc_html__('Account Settings', 'enhanced-e-commerce-for-woocommerce-store'),
        'manage_aioconversios',
        'conversios-account',
        array($this, 'showPage'),
        6
      );

      if ($plan_id == 1) {
        add_submenu_page(
          CONV_MENU_SLUG,
          esc_html__('Free Vs Pro', 'enhanced-e-commerce-for-woocommerce-store'),
          esc_html__('Free Vs Pro', 'enhanced-e-commerce-for-woocommerce-store') . '<img style="position: absolute; height: 30px;bottom: 5px; right: 10px;" src="' . esc_url_raw($freevspro) . '">',
          'manage_aioconversios',
          'conversios-pricings',
          array($this, 'showPage'),
          5
        );
      }

      if (is_plugin_active_for_network('woocommerce/woocommerce.php') || in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

        add_submenu_page(
          CONV_MENU_SLUG,
          esc_html__('Reports & Insights', 'enhanced-e-commerce-for-woocommerce-store'),
          esc_html__('Reports & Insights', 'enhanced-e-commerce-for-woocommerce-store'),
          'manage_aioconversios',
          'conversios-analytics-reports',
          array($this, 'showPage'),
          2
        );
        if (CONV_APP_ID != 13) {
          add_submenu_page(
            CONV_MENU_SLUG,
            esc_html__('Product Feed', 'enhanced-e-commerce-for-woocommerce-store'),
            '<span class="product_feed_menu"> Product Feed </span>',
            'manage_aioconversios',
            'conversios-google-shopping-feed',
            array($this, 'showPage'),
            3
          );
          add_submenu_page(
            CONV_MENU_SLUG,
            esc_html__('Performance Max', 'enhanced-e-commerce-for-woocommerce-store'),
            esc_html__('Performance Max', 'enhanced-e-commerce-for-woocommerce-store'),
            'manage_aioconversios',
            'conversios-pmax',
            array($this, 'showPage'),
            4
          );
        }
      }
    }

    /**
     * Display page.
     *
     * @since    4.1.4
     */
    public function showPage()
    {
      do_action('add_conversios_header');
      if (!empty(sanitize_text_field(wp_unslash(filter_input(INPUT_GET, 'page'))))) {
        $get_action = str_replace("-", "_", sanitize_text_field(wp_unslash(filter_input(INPUT_GET, 'page'))));
      } else {
        $get_action = "conversios_google_analytics";
      }
      
      if (method_exists($this, $get_action)) {
        $this->$get_action();
      }
      echo $this->get_tvc_popup_message();
      do_action('add_conversios_footer');
    }

    public function conversios()
    {
      $is_inner_page = isset($_GET['tab']) ? sanitize_text_field(wp_unslash(filter_input(INPUT_GET, 'tab'))) : "";
      $is_inner_page = str_replace("-", "_", sanitize_text_field($is_inner_page));
      if ($is_inner_page != "") {
        $this->$is_inner_page();
      } else {
        //require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/class-conversios-dashboard.php');
        $this->conversios_google_analytics();
      }
    }

    public function conversios_pricings()
    {
      require_once(ENHANCAD_PLUGIN_DIR . 'admin/partials/pricings.php');
      new TVC_Pricings();
    }
    public function conversios_account()
    {
      require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/help-html.php');
      require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/account.php');
      new TVC_Account();
    }
    public function conversios_google_analytics()
    {
      $sub_page = (isset($_GET['subpage'])) ? sanitize_text_field(wp_unslash(filter_input(INPUT_GET, 'subpage'))) : "";
      if (!empty($sub_page)) {
        require_once('partials/single-pixel-settings.php');
      } else {
        require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/help-html.php');
        require_once('partials/general-fields.php');
      }
    }

    public function conversios_analytics_reports()
    {
      $is_inner_page = isset($_GET['tab']) ? sanitize_text_field(wp_unslash(filter_input(INPUT_GET, 'tab'))) : "";
      $is_inner_page = str_replace("-", "_", sanitize_text_field($is_inner_page));
      if ($is_inner_page != "") {
        $this->$is_inner_page();
      } else {
        require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/class-conversios-analytics-reports.php');
      }
    }

    public function conversios_google_ads()
    {
      require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/help-html.php');
      require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/google-ads.php');
      new GoogleAds();
    }
    public function conversios_pmax()
    {
      $action_tab = (isset($_GET['tab'])) ? sanitize_text_field(wp_unslash(filter_input(INPUT_GET, 'tab'))) : "";
      if ($action_tab != "") {
        $this->$action_tab();
      } else {
        require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/pmax.php');
        new TVC_PMax();
      }
    }
    public function pmax_add()
    {
      require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/pmax-add.php');
      new TVC_PMaxAdd();
    }
    public function pmax_edit()
    {
      require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/pmax-edit.php');
      new TVC_PMaxEdit();
    }
    public function conversios_google_shopping_feed()
    {
      //include(ENHANCAD_PLUGIN_DIR . 'includes/setup/help-html.php');
      //include(ENHANCAD_PLUGIN_DIR . 'includes/setup/google-shopping-feed.php');
      $action_tab = (isset($_GET['tab'])) ? sanitize_text_field(wp_unslash(filter_input(INPUT_GET, 'tab'))) : "";
      $sub_page = (isset($_GET['subpage'])) ? sanitize_text_field(wp_unslash(filter_input(INPUT_GET, 'subpage'))) : "";
      if (!empty($sub_page)) {
        require_once('partials/single-pixel-settings.php');
      } else if ($action_tab != "") {
        $this->$action_tab();
      } else {
        //new GoogleShoppingFeed();
        require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/help-html.php');
        require_once('partials/general-fields-product-feed.php');
      }
    }
    public function gaa_config_page()
    {
      require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/help-html.php');
      require_once('partials/general-fields-product-feed.php');
    }
    public function feed_list()
    {
      require_once ENHANCAD_PLUGIN_DIR . 'includes/setup/help-html.php';
      require_once ENHANCAD_PLUGIN_DIR . 'admin/class-tvc-admin-helper.php';
      require_once ENHANCAD_PLUGIN_DIR . 'includes/setup/CustomApi.php';
      require_once 'partials/product-feed-list.php';
    }
    public function product_list()
    {
      require_once ENHANCAD_PLUGIN_DIR . 'includes/setup/help-html.php';
      require_once ENHANCAD_PLUGIN_DIR . 'admin/class-tvc-admin-helper.php';
      require_once ENHANCAD_PLUGIN_DIR . 'includes/setup/CustomApi.php';
      require_once ENHANCAD_PLUGIN_DIR . 'admin/class-tvc-admin-db-helper.php';
      require_once ENHANCAD_PLUGIN_DIR . 'includes/setup/tatvic-category-wrapper.php';
      require_once ENHANCAD_PLUGIN_DIR . 'includes/setup/class-tvc-product-sync-helper.php';
      require_once 'partials/feedwise-product-list.php';
    }
    public function product_mapping()
    {
      require_once ENHANCAD_PLUGIN_DIR . 'includes/setup/help-html.php';
      require_once ENHANCAD_PLUGIN_DIR . 'includes/setup/class-tvc-product-sync-helper.php';
      require_once ENHANCAD_PLUGIN_DIR . '/admin/class-tvc-admin-helper.php';
      require_once ENHANCAD_PLUGIN_DIR . 'includes/setup/tatvic-category-wrapper.php';
      require_once 'partials/product-mapping.php';
    }
    public function sync_product_page()
    {
      //require_once( 'partials/general-fields-product-feed.php');
      include(ENHANCAD_PLUGIN_DIR . 'includes/setup/google-shopping-feed-sync-product.php');
      new SyncProductConfiguration();
    }
    public function shopping_campaigns_page()
    {
      include(ENHANCAD_PLUGIN_DIR . 'includes/setup/google-shopping-feed-shopping-campaigns.php');
      new CampaignsConfiguration();
    }
    public function add_campaign_page()
    {
      include(ENHANCAD_PLUGIN_DIR . 'includes/setup/add-campaign.php');
      new AddCampaign();
    }
    public function campaign_performance_report()
    {
      include(ENHANCAD_PLUGIN_DIR . 'includes/setup/view-campaign-performance-report.php');
      new ViewCampaignReport();
    }
    public function source_performance_report()
    {
      include(ENHANCAD_PLUGIN_DIR . 'includes/setup/view-source-performance-report.php');
      new ViewSourceReport();
    }
    public function product_performance_report()
    {
      include(ENHANCAD_PLUGIN_DIR . 'includes/setup/view-product-performance-report.php');
      new ViewProductReport();
    }
    public function order_performance_report()
    {
      include(ENHANCAD_PLUGIN_DIR . 'includes/setup/view-order-performance-report.php');
      new ViewOrderReport();
    }
  }
}
if (is_admin()) {
  new Conversios_Admin();
}
