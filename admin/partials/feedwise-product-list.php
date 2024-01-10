<?php
$tvc_admin_helper = new TVC_Admin_Helper();
$tvc_admin_db_helper = new TVC_Admin_DB_Helper();
$tvcProductSyncHelper = new TVCProductSyncHelper();
$category_wrapper_obj = new Tatvic_Category_Wrapper();
$tvc_admin_helper->need_auto_update_db();
$category = $tvc_admin_helper->get_tvc_product_cat_list_with_name();
$merchantId = $tvc_admin_helper->get_merchantId();
$accountId = $tvc_admin_helper->get_main_merchantId();
$currentCustomerId = $tvc_admin_helper->get_currentCustomerId();
$subscriptionId = $tvc_admin_helper->get_subscriptionId();
$tvc_admin_helper->get_feed_status();
//$google_detail = $tvc_admin_helper->get_ee_options_data();
$plan_id = $tvc_admin_helper->get_plan_id();
$conv_data = $tvc_admin_helper->get_store_data();
$ee_options = $tvc_admin_helper->get_ee_options_settings();
$site_url = "admin.php?page=conversios-google-shopping-feed&tab=feed_list";

$gmcAttributes = $tvc_admin_helper->get_gmcAttributes();
$wooCommerceAttributes = array_map("unserialize", array_unique(array_map("serialize", $tvcProductSyncHelper->wooCommerceAttributes())));
$ee_mapped_attrs = unserialize(get_option('ee_prod_mapped_attrs'));
$total_products = (new WP_Query(['post_type' => 'product', 'post_status' => 'publish']))->found_posts;
$tiktok_business_account = '';
if (isset($ee_options['tiktok_setting']['tiktok_business_id']) === TRUE && $ee_options['tiktok_setting']['tiktok_business_id'] !== '') {
    $tiktok_business_account = $ee_options['tiktok_setting']['tiktok_business_id'];
}

$google_merchant_center_id = '';
if (isset($ee_options['google_merchant_id']) === TRUE && $ee_options['google_merchant_id'] !== '') {
    $google_merchant_center_id = $ee_options['google_merchant_id'];
}

if ($google_merchant_center_id === '' && $tiktok_business_account === '') {
    wp_safe_redirect("admin.php?page=conversios-google-shopping-feed&tab=gaa_config_page");
    exit;
}
$getCountris = file_get_contents(ENHANCAD_PLUGIN_DIR . "includes/setup/json/countries.json");
$contData = json_decode($getCountris);

$path = ENHANCAD_PLUGIN_DIR . 'includes/setup/json/category.json';
$str = file_get_contents($path);
?>
<div class="modal fade" id="conv_bad_req_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">

            </div>
            <div class="modal-body text-center p-0">
                <img style="width:184px;" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/error_logo.png'); ?>">
                <h3 class="fw-normal pt-3">
                    <?php esc_html_e("Bad Request, Feed Id missing", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </h3>
                <span id="conv_save_req_error_txt" class="mb-1 lh-lg"></span>
            </div>
            <div class="modal-footer border-0 pb-4 mb-1">
                <a href="<?php echo esc_url_raw($site_url); ?>" type="button" class="btn conv-yellow-bg m-auto text-white"><?php esc_html_e("Go back", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
            </div>
        </div>
    </div>
</div>
<?php

if (!isset($_GET['id']) || filter_input(INPUT_GET, 'id') == '') {
    print_r("<script type='text/javascript'>$(document).ready(function(){ $('#conv_bad_req_modal').modal('show'); }); </script>");
    ?>
    <?php
    return print_r('Cannot access this page, "Feed Id" is missing !!');
}

$where = '`id` = ' . esc_sql(filter_input(INPUT_GET, 'id'));
$filed = [
    'id',
    'feed_name',
    'channel_ids',
    'auto_sync_interval',
    'auto_schedule',
    'categories',
    'attributes',
    'filters',
    'include_product',
    'exclude_product',
    'total_product',
    'product_id_prefix',
    'status',
    'created_date',
    'is_mapping_update',
    'target_country',
    'tiktok_catalog_id',
    'tiktok_status',
];
$result = $tvc_admin_db_helper->tvc_get_results_in_array("ee_product_feed", $where, $filed);
if ($result === FALSE) {
    print_r("<script type='text/javascript'> $(document).ready(function(){ $('#conv_bad_req_modal').modal('show'); }); </script>");
    ?>
    <?php
    return print_r('"Feed Id not found", Bad Request..!!!!');
}

$attr_id = $result[0]['attributes'] ? $result[0]['attributes'] : "1";
$if_exclude_product = $result[0]['exclude_product'] === TRUE ? $result[0]['exclude_product'] : "1";
$if_include_product = $result[0]['include_product'] === TRUE ? $result[0]['include_product'] : "1";
$p_ids = json_decode($result[0]['attributes']);
$p_id = isset($p_ids->id) ? $p_ids->id : '';

$filters = isset($result[0]['filters']) && $result[0]['filters'] !== '' ? json_decode($result[0]['filters']) : '';
$attr = '';
$condition = '';
$value = '';
$filters = isset($result[0]['filters']) && $result[0]['filters'] !== '' ? json_decode($result[0]['filters']) : '';
$attr = '';
$condition = '';
$value = '';
$count = 0;
$html = '';
$filterAttributes = [
    'product_cat' => 'Category',
    'ID' => 'Product Id',
    'post_title' => 'Product Title',
    '_sku' => 'SKU',
    '_regular_price' => 'Regular Price',
    '_sale_price' => 'Sale Price',
    'post_content' => 'Product Description',
    'post_excerpt' => 'Product Short Description',
    '_stock_status' => 'Stock Status',
];
if ($filters !== '') {
    $count = 0;
    foreach ($filters as $val) {
        $attr .= $attr === '' ? $val->attr : ',' . $val->attr;
        $condition .= $condition === '' ? $val->condition : ',' . $val->condition;
        $value .= $value === '' ? $val->value : ',' . $val->value;
        $term = '';
        $eachVallue = $val->value;
        if ($val->attr === 'product_cat') {
            $term = get_term_by('id', $val->value, 'product_cat');
            $eachVallue = $term->name;
        }
        if ($result[0]['is_mapping_update'] == '1') {
            $html .= '<div class="btn-group border rounded mt-1 me-1 disabled"><button class="btn btn-light btn-sm text-secondary fs-7 ps-1 pe-1 pt-0 pb-0" type="button">' . $filterAttributes[$val->attr] . '  <b>' . $val->condition . '</b> ' . $eachVallue . ' </button>
                        <button type="button" class="btn btn-sm btn-light onhover-close pt-0 pb-0" data-bs-toggle=""
                            aria-expanded="false" style="cursor: no-drop;">
                            <span class="material-symbols-outlined fs-6 pt-1 onhover-close">
                                close
                            </span>
                        </button>
                    </div>';
        } else {
            $html .= '<div class="btn-group border rounded mt-1 me-1 removecardThis" ><button value="' . $count++ . '" class="btn btn-light btn-sm text-secondary fs-7 ps-1 pe-1 pt-0 pb-0" type="button">' . $filterAttributes[$val->attr] . '  <b>' . $val->condition . '</b> ' . $eachVallue . ' </button>
                        <button type="button" class="btn btn-sm btn-light onhover-close pt-0 pb-0" data-bs-toggle=""
                            aria-expanded="false">
                            <span class="material-symbols-outlined fs-6 pt-1 onhover-close removecard">
                                close
                            </span>
                        </button>
                    </div>';
        }
    } //end foreach

} //end if

$woo_product = wp_count_posts('product')->publish;
$livechannel = 0;
$channel_id = explode(',', $result[0]['channel_ids']);
?>
<div class="container-fluid conv-light-grey-bg pt-4 ps-4">
    <div class="row ps-4 pe-4">
        <div class="m-0 p-0 col-6">
            <div class="conv-heading-box">
                <span class="float-start">
                    <a href="<?php echo esc_url_raw($site_url); ?>" class="text-dark">
                        <label class="fs-20">
                            <?php esc_html_e("Feed Management >", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </label>
                    </a>
                    <label class="fs-14 fw-400">
                        <?php echo esc_attr($result[0]['feed_name']); ?>
                    </label>
                </span>
                <div class="conv-link-blue">
                    <span style="cursor: pointer" class="material-symbols-outlined ms-2 mt-1 fs-20" onclick="editFeed(<?php echo filter_input(INPUT_GET, 'id'); ?>)">edit</span>
                    <label class="mb-2 fs-14 text" onclick="editFeed(<?php echo filter_input(INPUT_GET, 'id'); ?>)"><?php esc_html_e("Edit Feed", "enhanced-e-commerce-for-woocommerce-store"); ?></label>
                </div>
            </div>
        </div>        
    </div>
    <div class="row ps-4 pe-4">
        <div class="col-8 m-0 p-0">
            <div class="conv-heading-box">
                <span>
                    <label class="fs-14 fw-500">
                        <?php esc_html_e("Created : ", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </label>
                    <label class="col-form-label fs-14 fw-400 pt-2 text-secondary">
                        <?php echo esc_html(date_format(date_create($result[0]['created_date']), "d-m-Y")); ?>
                        <?php echo esc_html(date_format(date_create($result[0]['created_date']), "H:i a")); ?>
                    </label>
                    <span class="ms-1 me-1">|</span>
                    <label class="fs-14 fw-500">
                        <?php esc_html_e("Auto Sync : ", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </label>
                    <label class="col-form-label fs-14 fw-400 pt-2 text-secondary">
                        <?php echo esc_html($result[0]['auto_schedule'] == '1' ? 'Yes , Every ' . $result[0]['auto_sync_interval'] . ' Days' : 'No'); ?>
                    </label>
                    <span class="ms-1 me-1">|</span>
                    <label class="fs-14 fw-500">
                        <?php esc_html_e("Channel Status : ", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </label>
                    <label class="col-form-label fs-14 fw-400 pt-2">
                        
                        <?php foreach ($channel_id as $val) {
                            if ($val === '1') { ?>
                                        <img class="<?php echo strtolower(str_replace(' ', '', $result[0]['status'])) ?>-status"src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/google_channel_logo.png'); ?>" />
                                <?php } else if ($val === '2') { ?>
                                            <img class="<?php echo strtolower(str_replace(' ', '', $result[0]['status'])) ?>-status" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/fb_channel_logo.png'); ?>" />
                                <?php } else if ($val === '3') { ?>
                                                <img class="<?php echo strtolower(str_replace(' ', '', $result[0]['tiktok_status'])) ?>-status" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/tiktok_channel_logo.png'); ?>" />
                            <?php }
                        } ?>
                    </label>                    
                </span>
            </div>
        </div>
        <div class="col-4 m-0 p-0">
            <?php 
            $filteredProductSyn = 'filteredProductSyn';
            if($result[0]['status'] === 'In Progress' && $filteredProductSyn == 'filteredProductSyn'){
                $filteredProductSyn = '';
            }
            if($result[0]['tiktok_status'] === 'In Progress' && $filteredProductSyn == 'filteredProductSyn'){
                $filteredProductSyn = '';
            }
            if($result[0]['status'] === 'Draft' && $filteredProductSyn == 'filteredProductSyn' && $result[0]['is_mapping_update'] == 1){
                $filteredProductSyn = '';
            }
            if($result[0]['tiktok_status'] === 'Draft' && $filteredProductSyn == 'filteredProductSyn' && $result[0]['is_mapping_update'] == 1){
                $filteredProductSyn = '';
            }
            ?>
            <button class="btn btn-soft-primary float-end fs-14 fw-500 <?php echo $filteredProductSyn ?> " name="filteredProductSyn" id="filteredProductSyn" value="syncAll" <?php echo ($filteredProductSyn == '') ? 'style="cursor: no-drop;"' : '' ?>>
                <?php
                esc_html_e("Sync 0 Products", "enhanced-e-commerce-for-woocommerce-store"); ?>
            </button>
        </div>
    </div>
</div>
<div class="container-fluid conv-light-grey-bg p-4 pb-2">
    <div id="loadingbar_blue" class="progress-materializecss d-none ps-2 pe-2">
        <div class="indeterminate"></div>
    </div>
    <div class="card" style="max-width:100%; border-top-left-radius:8px;border-top-right-radius:8px;">
        <div class="card-body row ">
            <div class="col-9 pt-0">
                <span class="ps-2 pe-1 pt-1 pb-1 rounded border <?php echo $result[0]['is_mapping_update'] == '1' ? '' : 'addFilter'; ?>" style="<?php echo $result[0]['is_mapping_update'] == '1' ? 'cursor: no-drop' : 'cursor: pointer'; ?>">
                    <span class="material-symbols-outlined fs-6 text-secondary">
                        filter_list
                    </span> <label class="fs-14 fw-500 mb-2 pt-1 text-secondary" style="<?php echo $result[0]['is_mapping_update'] == '1' ? 'cursor: no-drop' : ''; ?>">Filter</label>
                </span>
                <span class="ms-2 ps-2 pe-1 pt-1 pb-1 rounded border">
                    <label class="fs-14 fw-500  text-dark pb-1 defaultPointer">
                        <?php echo esc_html_e("Total Products : ", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        <label class="fs-14 fw-500  text-secondary">
                            <?php echo esc_html_e(number_format($total_products), "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </label>
                    </label>
                </span>
                <span class="ms-2 ps-2 pe-1 pt-1 pb-1 rounded border">
                    <label class="fs-14 fw-500 text-info pb-1 defaultPointer">
                        <?php echo esc_html_e("Synced : ", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        <label class="fs-14 fw-500 text-secondary">
                            <?php echo esc_html_e(number_format($result[0]['status'] === 'Synced' || $result[0]['tiktok_status'] === 'Synced' ? $result[0]['total_product'] : 0), "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </label>
                    </label>
                </span>                
                <span class="ms-2 ps-2 pe-2 pt-1 pb-1 rounded border">
                    <label class="fs-14 fw-500 text-warning pb-1 defaultPointer">
                        <?php echo esc_html_e("Pending : ", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        <label class="fs-14 fw-500 text-secondary allPendingCount">
                            <?php echo esc_html_e(number_format(0), "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </label>
                    </label>
                </span>                
            </div>
            <div class="col-3 pb-1">
                <span class="pb-2">
                    <input class="form-control me-2 " type="search" placeholder="Search" aria-label="Search" id="searchName" name="searchName" aria-controls="product_list_table">
                </span>
            </div>
            <div class="col-12 row pe-0">
                <div class="col-8" id="addFiltersCard">
                    <?php echo $html; ?>

                </div>
                <div class="col-4 filter_count ">

                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive shadow-sm" style="border-bottom-left-radius:8px;border-bottom-right-radius:8px;">
        <table class="table" id="product_list_table" style="width:100%">
            <thead>
                <tr>
                    <th scope="col" class="padding-start-1 text-start" style="width:3%">
                        <div class="form-check form-check-custom">
                            <input class="form-check-input checkbox fs-17" type="checkbox" name="syncAll" id="syncAll" checked value="syncAll">
                        </div>
                    </th>
                    <th scope="col" class="text-dark text-start">
                        <?php esc_html_e("PRODUCT INFORMATION", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-dark text-start">
                        <?php esc_html_e("CATEGORY", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-dark text-start">
                        <?php esc_html_e("AVAILABILITY", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-dark text-center">
                        <?php esc_html_e("QUANTITY", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-dark text-center">
                        <?php esc_html_e("CHANNEL STATUS", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-dark text-center" style="width:5%">
                        <?php esc_html_e("ACTION", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                </tr>
            </thead>
            <tbody id="table-body">
            </tbody>
        </table>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="filterModal" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content ">
            <div class="modal-header bg-white p-2">
                <h5 class="modal-title fs-6 p-2 col-8">
                    <?php esc_html_e("Apply Filters for Product", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </h5>
                <span class="col-4 addButton">
                    <label class="text-primary float-end p-1">
                        <?php esc_html_e("Add Filter", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </label>
                    <span class="material-symbols-outlined text-primary float-end">
                        add_circle
                    </span>
                </span>
            </div>
            <div class="modal-body ps-2 pt-2" id="">
                <form id="filterForm">
                    <div class="filterRow mb-3 row">
                        <div class="col-11 row">
                            <div class="col-4 productDiv">
                                <select class="select2 product" name="product[]">
                                    <option value="0"><?php esc_html_e("Select Attribute", "enhanced-e-commerce-for-woocommerce-store"); ?></option>
                                    <option value="product_cat"><?php esc_html_e("Category", "enhanced-e-commerce-for-woocommerce-store"); ?></option>
                                    <option value="ID"><?php esc_html_e("Product Id", "enhanced-e-commerce-for-woocommerce-store"); ?></option>
                                    <option value="post_title"><?php esc_html_e("Product Title", "enhanced-e-commerce-for-woocommerce-store"); ?></option>
                                    <option value="_sku"><?php esc_html_e("SKU", "enhanced-e-commerce-for-woocommerce-store"); ?></option>
                                    <option value="_regular_price"><?php esc_html_e("Regular Price", "enhanced-e-commerce-for-woocommerce-store"); ?></option>
                                    <option value="_sale_price"><?php esc_html_e("Sale Price", "enhanced-e-commerce-for-woocommerce-store"); ?></option>
                                    <option value="post_content"><?php esc_html_e("Product Description", "enhanced-e-commerce-for-woocommerce-store"); ?></option>
                                    <option value="post_excerpt"><?php esc_html_e("Product Short Description", "enhanced-e-commerce-for-woocommerce-store"); ?></option>
                                    <option value="_stock_status"><?php esc_html_e("Stock Status", "enhanced-e-commerce-for-woocommerce-store"); ?></option>
                                </select>
                            </div>
                            <div class="col-4 conditionDiv">
                                <select class="select2 condition" name="condition[]">
                                    <option value="0"><?php esc_html_e("Select Conditions", "enhanced-e-commerce-for-woocommerce-store"); ?></option>
                                </select>
                            </div>
                            <div class="col-4 textValue">
                                <input type="text" class="form-control from-control-overload value" placeholder="Add value" name="value[]">
                            </div>
                        </div>
                        <div class="col-1">
                        </div>
                    </div>
                    <div id="allFilters">
                    </div>
            </div>
            <div class="modal-footer p-2">
                <button type="button" class="btn btn-light btn-sm ps-4 pe-4 border-primary text-primary" id="filterReset">
                    <?php esc_html_e("Clear", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </button>
                <button type="button" class="btn btn-soft-primary btn-sm ps-4 pe-4" id="filterSubmit">
                    <?php esc_html_e("Apply", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </button>
            </div>
            </form>
        </div>
    </div>
    <input type="hidden" id="feed_id" value="<?php echo esc_html(sanitize_text_field(filter_input(INPUT_GET, 'id'))); ?>">
    <input type="hidden" id="strProData" value="<?php echo esc_html(sanitize_text_field($attr)); ?>">
    <input type="hidden" id="strConditionData" value="<?php echo esc_html(sanitize_text_field($condition)); ?>">
    <input type="hidden" id="strValueData" value="<?php echo esc_html(sanitize_text_field($value)); ?>">
    <input type="hidden" id="excludeProductFromSync" value="<?php echo esc_html(sanitize_text_field($result[0]['exclude_product'])); ?>">
    <input type="hidden" id="includeProductFromSync" value="<?php echo esc_html(sanitize_text_field($result[0]['include_product'])); ?>">
    <input type="hidden" id="includeExtraProductForFeed" value="">
    <input type="hidden" id="selectAllunchecked" name="selectAllunchecked" value="">
    <input type="hidden" id="is_auto_sync" name="is_auto_sync" value="1">

</div>
<!-- Modal -->
<div class="modal fade" id="convCreateFeedModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content ">
            <form id="feedForm" onfocus="this.className='focused'">
                <div id="loadingbar_blue_modal" class="progress-materializecss d-none ps-2 pe-2" style="width:98%">
                    <div class="indeterminate"></div>
                </div>
                <div class="modal-header bg-light p-2 ps-4 fw-500">
                    <h5 class="modal-title fs-16" id="feedType">
                        <?php esc_html_e("Create New Feed", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="$('#feedForm')[0].reset()"></button>
                </div>
                <div class="modal-body ps-4 pt-0">
                    <div class="mb-4">
                        <label for="feed_name" class="col-form-label text-dark fs-14 fw-500">
                            <?php esc_html_e("Feed Name", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </label>
                        <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip" data-bs-placement="right" title="Add a name to your feed for your reference, for example, 'April end-of-season sales' or 'Black Friday sales for the USA'.">
                            info
                        </span>
                        <input type="text" class="form-control fs-14" name="feedName" id="feedName" placeholder="e.g. New Summer Collection">
                    </div>
                    <div class="mb-2 row">
                        <div class="col-5">
                            <label for="auto_sync" class="col-form-label text-dark fs-14 fw-500">
                                <?php esc_html_e("Auto Sync", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                            <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip" data-bs-placement="right" title="Turn on this feature to schedule an automated product feed to keep your products up to date with the changes made in the products. You can come and change this any time.">
                                info
                            </span>
                        </div>
                        <div class="form-check form-switch col-7 mt-0 fs-5">
                            <input class="form-check-input" type="checkbox" name="autoSync" id="autoSync" checked>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-5">
                            <label for="auto_sync_interval" class="col-form-label text-dark fs-14 fw-500">
                                <?php esc_html_e("Auto Sync Interval", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                            <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip" data-bs-placement="right" title="Set the number of days to schedule the next auto-sync for the products in this feed. You can come and change this any time.">
                                info
                            </span>
                        </div>
                        <div class="col-7">
                            <input type="text" class="form-control-sm fs-14" readonly="readonly" name="autoSyncIntvl" id="autoSyncIntvl" size="3" min="1" onkeypress="return ( event.charCode === 8 || event.charCode === 0 || event.charCode === 13 || event.charCode === 96) ? null : event.charCode >= 48 && event.charCode <= 57" oninput="removeZero();" value="<?php echo (isset($conv_additional_data['pro_snyc_time_limit']) && $conv_additional_data['pro_snyc_time_limit'] > 0 && $plan_id !== 1) ? esc_html(sanitize_text_field($conv_additional_data['pro_snyc_time_limit'])) : "25"; ?>">
                            <label for="" class="col-form-label fs-14">
                                <?php esc_html_e("Days", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                            <span>
                                <?php echo ($plan_id === 46) ? '<a target="_blank" href="https://www.conversios.io/wordpress/product-feed-manager-for-woocommerce-pricing/?utm_source=app_wooPFM&utm_medium=BUSINESS&utm_campaign=Pricing"><b> Upgrade To Pro</b></a>' : ''; ?>
                            </span>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-5">
                            <label for="target_country_feed" class="col-form-label text-dark fs-14 fw-500" name="">
                                <?php esc_html_e("Target Country", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                            <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip"
                                data-bs-placement="right"
                                title="Specify the target country for your product feed. Select the country where you intend to promote and sell your products.">
                                info
                            </span>
                        </div>
                        <div class="col-7">
                            <select class="select2 form-select form-select-sm mb-3" aria-label="form-select-sm example" style="width: 100%" name="target_country_feed" id="target_country_feed">
                            <option value="">Select Country</option>
                            <?php
                            $selecetdCountry = $conv_data['user_country'];
                            foreach ($contData as $key => $value) {
                                ?>
                                        <option value="<?php echo esc_attr($value->code) ?>" <?php echo $selecetdCountry == $value->code ? 'selected = "selecetd"' : '' ?>><?php echo esc_attr($value->name) ?></option>"
                                        <?php
                            }

                            ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="auto_sync_interval" class="col-form-label text-dark fs-14 fw-500">
                            <?php esc_html_e("Select Channel", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </label>
                        <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip" data-bs-placement="right" title="Below is the list of channels that you have linked for product feed. Please note you will not be able to make any changes in the selected channels once product feed process is done.">
                            info
                        </span>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-check-custom">
                            <input class="form-check-input check-height fs-14 errorChannel" type="checkbox"
                                value="<?php echo esc_html_e($google_merchant_center_id); ?>" id="gmc_id" name="gmc_id"
                                <?php echo $google_merchant_center_id !== '' ? "checked" : 'disabled' ?>>
                            <label for="" class="col-form-label fs-14 pt-0 text-dark fw-500">
                                <?php esc_html_e("Google Merchant Center Account :", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                            <label class="col-form-label fs-14 pt-0 fw-400">
                                <?php echo esc_html_e($google_merchant_center_id); ?>
                            </label>
                        </div>
                        <div class="form-check form-check-custom">
                            <input class="form-check-input check-height fs-14 errorChannel" type="checkbox"
                                value="" id="tiktok_id"
                                name="tiktok_id" <?php echo $tiktok_business_account !== '' ? "checked" : 'disabled' ?>>
                            <label for="" class="col-form-label fs-14 pt-0 text-dark fw-500">
                                <?php esc_html_e("TikTok Catalog Id :", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                            <label class="col-form-label fs-14 pt-0 fw-400 tiktok_catalog_id">
                               
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer p-2">
                    <input type="hidden" id="channel_ids" name="channel_ids" value="<?php echo sanitize_text_field($result[0]['channel_ids']); ?>">
                    <input type="hidden" id="autoSyncInterval" name="autoSyncInterval" value="<?php echo sanitize_text_field($result[0]['auto_sync_interval']); ?>">
                    <input type="hidden" id="edit" name="edit">
                    <input type="hidden" value="<?php echo esc_attr($conv_data['user_domain']); ?>" class="fromfiled" name="url" id="url" placeholder="Enter Website">
                    <input type="hidden" id="is_mapping_update" name="is_mapping_update" value="">
                    <input type="hidden" id="last_sync_date" name="last_sync_date" value="">
                    <input type="hidden" id="tiktok_catalog_id" name="tiktok_catalog_id" value="<?php echo sanitize_text_field($result[0]['tiktok_catalog_id']); ?>">
                    <input type="hidden" value="<?php echo esc_attr($conv_data['user_domain']); ?>" class="fromfiled" name="url" id="url">
                    <input type="hidden" id="subscriptionMerchantCenId" name="subscriptionMerchantCenId" value="<?php echo esc_attr($google_merchant_center_id); ?>">
                    <button type="button" class="btn btn-light btn-sm border" data-bs-dismiss="modal" onclick="$('#feedForm')[0].reset()">
                        <?php esc_html_e("Cancel", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </button>
                    <button type="button" class="btn btn-soft-primary btn-sm" id="submitFeed">
                        <?php esc_html_e("Update", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="categoryModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content ">
            <div class="modal-header bg-light p-2 ps-4 ">
                <div id="loadingbar_blue_modal" class="progress-materializecss d-none ps-2 pe-2" style="width:98%">
                    <div class="indeterminate"></div>
                </div>
                <label class="modal-title fs-14 fw-400" id="">
                    <?php esc_html_e("Map your product category and attributes", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </label>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="wrapper p-20">
                <form id="productSync" action="">
                    <p>
                        <?php esc_html_e("Map the categories and other attributed of your WooCommerce products with Conversios categories and attributes. At Conversios we automatically maps your product categories and other attributes to the categories and attributes of the selected channels respectively.", "enhanced-e-commerce-for-woocommerce-store") ?>
                    </p>
                    <span class="Outofstock fs-12 asterisk">** </span><span class="catCount fs-12 fw-500"></span>
                    <div class="tab">
                        <div class="col-12 row conv-light-grey-bg m-0 p-0" style="height:48px;border-radius:4px;">
                            <div class="col-6 pt-2">
                                <span class="ps-2 fw-normal">
                                    <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/woocommerce_logo.png'); ?>" />
                                    <?php esc_html_e("WooCommrece Product Category", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                            </div>
                            <div class="col-6 pt-2 ps-0">
                                <span class="ps-1 fw-normal">
                                    <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conversios_logo.png'); ?>" />
                                    <?php esc_html_e("Conversios Product Category", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                            </div>
                        </div>
                        <div class="col-12 row bg-white m-0 p-0">
                            <div class="col-12 row categoryDiv" style="overflow-y: scroll; max-height:500px;">
                                <?php echo $category_wrapper_obj->category_table_content(0, 0, 'mapping'); ?>
                            </div>
                        </div>

                    </div>

                    <div class="tab productAttribute">
                        <div class="col-12 row conv-light-grey-bg m-0 p-0" style="height:48px;border-radius:4px;">
                            <div class="col-6 pt-2">
                                <span class="ps-2 fw-normal">
                                    <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conversios_logo.png'); ?>" />
                                    <?php esc_html_e("Conversios Product Attribute", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                            </div>
                            <div class="col-6 pt-2 ps-0">
                                <span class="ps-1 fw-normal">
                                    <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/woocommerce_logo.png'); ?>" />
                                    <?php esc_html_e("WooCommerce Product Attribute", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                            </div>
                        </div>
                        <div class="col-12 row bg-white m-0 p-0 mb-3">
                            <div class="col-12 row  attributeDiv" style="overflow-y: scroll; max-height:500px;">
                                <!-- <form id="attribute_mapping" class="row"> -->
                                <?php foreach ($gmcAttributes as $key => $attribute) {
                                    $sel_val = ""; ?>
                                        <div class="col-6 mt-2">
                                            <span class="ps-3 font-weight-400 text-color fs-12">
                                                <?php echo esc_attr($attribute["field"]) . " " . (isset($attribute["required"]) && esc_attr($attribute["required"]) === '1' ? '<span class="text-color fs-6"> *</span>' : ""); ?>
                                                <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip" data-bs-placement="right" title="<?php echo (isset($attribute['desc']) ? esc_attr($attribute['desc']) : ''); ?>">
                                                    info
                                                </span>
                                            </span>
                                            <div class="float-end">
                                                <?php
                                                if ($attribute["field"] == 'id') { ?>
                                                        <input type="text" class="form-control" name="product_id_prefix" id="product_id_prefix" placeholder="Add Prefix" value="">
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="col-6 mt-2">
                                            <?php
                                            $ee_select_option = $tvc_admin_helper->add_additional_option_in_tvc_select($wooCommerceAttributes, $attribute["field"]);
                                            $require = (isset($attribute['required']) && $attribute['required']) ? true : false;
                                            $sel_val_def = (isset($attribute['wAttribute'])) ? $attribute['wAttribute'] : "";
                                            if ($attribute["field"] === 'link') {
                                                "product link";
                                            } else if ($attribute["field"] === 'shipping') {
                                                $sel_val = (isset($ee_mapped_attrs[$attribute["field"]])) ? $ee_mapped_attrs[$attribute["field"]] : $sel_val_def;
                                                $tvc_admin_helper->tvc_text($attribute["field"], 'number', '', esc_html__('Add shipping flat rate', 'product-feed-manager-for-woocommerce'), $sel_val, $require);
                                            } else if ($attribute["field"] === 'tax') {
                                                $sel_val = (isset($ee_mapped_attrs[$attribute["field"]])) ? esc_attr($ee_mapped_attrs[$attribute["field"]]) : esc_attr($sel_val_def);
                                                $tvc_admin_helper->tvc_text($attribute["field"], 'number', '', 'Add TAX flat (%)', $sel_val, $require);
                                            } else if ($attribute["field"] === 'content_language') {
                                                $tvc_admin_helper->tvc_language_select($attribute["field"], 'content_language', esc_html__('Please Select Attribute', 'product-feed-manager-for-woocommerce'), 'en', $require);
                                            } else if ($attribute["field"] === 'target_country') {
                                                $tvc_admin_helper->tvc_countries_select($attribute["field"], 'target_country', esc_html__('Please Select Attribute', 'product-feed-manager-for-woocommerce'), $require);
                                            } else {
                                                if (isset($attribute['fixed_options']) && $attribute['fixed_options'] !== "") {
                                                    $ee_select_option_t = explode(",", $attribute['fixed_options']);
                                                    $ee_select_option = [];
                                                    foreach ($ee_select_option_t as $o_val) {
                                                        $ee_select_option[]['field'] = esc_attr($o_val);
                                                    }
                                                    $sel_val = $sel_val_def;
                                                    $tvc_admin_helper->tvc_select($attribute["field"], $attribute["field"], esc_html__('Please Select Attribute', 'product-feed-manager-for-woocommerce'), $sel_val, $require, $ee_select_option);
                                                } else {
                                                    $sel_val = (isset($ee_mapped_attrs[$attribute["field"]])) ? $ee_mapped_attrs[$attribute["field"]] : $sel_val_def;
                                                    $tvc_admin_helper->tvc_select($attribute["field"], $attribute["field"], esc_html__('Please Select Attribute', 'product-feed-manager-for-woocommerce'), $sel_val, $require, $ee_select_option);
                                                }
                                            }
                                            ?>
                                        </div>

                                <?php }
                                ?>
                                <!-- </form> -->
                            </div>
                        </div>
                        <div class="col-12 p-2">
                            <div style="float:right;">
                                <label>Product Batch Size <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip" data-bs-placement="right" title="" data-bs-original-title="If you are facing an issue with the product feed process with the current batch change the size of the batch according to your count of products.">
                                        info
                                    </span></label>
                                <select id="product_batch_size" style="border-radius:15px;">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option selected="selected" value="500">500</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 row p-2">
                            <p>
                                <?php esc_html_e("We are using WooCommerce’s action schedulers to make the product sync process go smoothly. Please confirm with your hosting provider to ensure CRON is activated/running and confirm that CRON is enabled on your server.", "enhanced-e-commerce-for-woocommerce-store") ?>
                            </p>
                        </div>
                    </div>
                    <div style="overflow:auto;" class="pt-2">
                        <div style="float:left;">
                            <button class="btn btn-soft-primary me-1 ms-1" type="button" id="prevBtn" onclick="nextPrev(-1)" style="width:130px; height:38px;">Previous</button>
                            <button class="btn btn-soft-primary me-1 ms-1" type="button" id="nextBtn" onclick="nextPrev(1)" style="width:130px; height:38px;">Next</button>
                        </div>
                    </div>

                    <!-- Circles which indicates the steps of the form: -->
                    <div style="text-align:center;margin-top:10px;">
                        <span class="step"></span>
                        <span class="step"></span>
                    </div>
                    <input type="hidden" name="selectedCategory" id="selectedCategory">
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Error Save Modal -->
<div class="modal fade" id="conv_save_error_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">

            </div>
            <div class="modal-body text-center p-0">
                <img style="width:184px;" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/error_logo.png'); ?>">
                <h3 class="fw-normal pt-3 errorText">Error</h3>
                <span id="conv_save_error_txt" class="mb-1 lh-lg"></span>
            </div>
            <div class="modal-footer border-0 pb-4 mb-1">
                <button class="btn conv-yellow-bg m-auto text-white" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Error Save Modal End -->
<!-- Success Save Modal -->
<div class="modal fade" id="conv_save_success_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">

            </div>
            <div class="modal-body text-center p-0">
                <img style="width:184px;" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/update_success_logo.png'); ?>">
                <h3 class="fw-normal pt-3">Updated Successfully</h3>
                <span id="conv_save_success_txt" class="mb-1 lh-lg"></span>
            </div>
            <div class="modal-footer border-0 pb-4 mb-1">
                <button class="btn conv-blue-bg m-auto text-white" data-bs-dismiss="modal">Ok, Done</button>
            </div>
        </div>
    </div>
</div>
<!-- Success Save Modal End -->

<script>
    var cat_json = <?php echo $str ?>;
    var currentTab = 0; // Current tab is set to be the first tab (0)
    var totalProduct = 0;
    //showTab(currentTab); // Display the current tab
    jQuery(document).ready(function() {        
        let p_id = "<?php echo $p_id ?>"        
        jQuery('.select2').select2({
            dropdownParent: $("#filterModal")
        });

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        /*************************** DataTable init Start *********************************************************************************************/
        var table = jQuery('#product_list_table').DataTable({
            "ordering": false,
            scrollX: false,
            scrolly: true,
            processing: true,
            serverSide: true,
            searching: false,
            columnDefs: [{
                    className: "align-middle text-start",
                    targets: 0
                },
                {
                    className: "align-middle text-start ps-1 pb-1",
                    targets: 1
                },
                {
                    className: "align-middle text-start",
                    targets: 2
                },
                {
                    className: "align-middle text-start",
                    targets: 3
                },
                {
                    className: "align-middle",
                    targets: 4
                },
                {
                    className: "align-middle",
                    targets: 5
                },
                {
                    className: "align-middle",
                    targets: 6
                }
            ],
            initComplete: function() {
                $('#searchName').on('input', function() {
                    $('#product_list_table').DataTable().search($(this).val()).draw();
                });
            },
            "language": {
                processing: false,
            },
            ajax: {
                url: tvc_ajax_url,
                type: 'POST',
                data: function(d) {
                    conv_change_loadingbar('show');
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                    return $.extend({}, d, {
                        action: "ee_get_product_details_for_table",
                        productData: jQuery("#strProData").val(),
                        conditionData: jQuery("#strConditionData").val(),
                        valueData: jQuery("#strValueData").val(),
                        searchName: jQuery("#searchName").val(),
                        feed_id: jQuery('#feed_id').val(),
                        p_id: p_id,
                        prefix: "<?php echo $result[0]['product_id_prefix'] ?>",
                        product_details_nonce: "<?php echo esc_html(wp_create_nonce('conv_product_details-nonce')); ?>"
                    });
                },
                dataType: 'JSON',
                error: function(err, status) {

                },
            },
            "drawCallback": function(settings) {
                if (jQuery('#selectAllunchecked').val() == 1) {
                    $(".checkbox").prop('checked', false);
                } else {
                    $(".checkbox").prop('checked', true);
                }

                var total = addCommas(settings.json.recordsTotal);
                totalProduct = settings.json.recordsTotal;
                getrealcheckedcount();

                if (jQuery("#strProData").val() == '') {
                    jQuery('.filter_count').empty();
                } else {
                    jQuery('.filter_count').empty();
                    jQuery('.filter_count').append('<label class="fs-7 text-dark float-end mt-3">Filter Applied :<lable class="fs-7  text-secondary"> Found ' + total + ' Products</lable></label>');
                }
            },
            columns: [{
                    data: 'checkbox'
                },
                {
                    data: 'product'
                },
                {
                    data: 'category'
                },
                {
                    data: 'availability'
                },
                {
                    data: 'quantity'
                },
                {
                    data: 'channelstatus'
                },
                {
                    data: 'action'
                }
            ],

        }).on('draw', function() {
            conv_change_loadingbar('hide');
            let exclude = [];
            let include = [];
            var availableId = Array();
            var product_list = '';
            var prefix = "<?php echo $result[0]['product_id_prefix'] ?>";

            if (jQuery('#excludeProductFromSync').val() != '') {
                exclude = jQuery('#excludeProductFromSync').val().split(',');
                jQuery('#syncAll').prop('checked', false)
                $.each(exclude, function(key, value) {
                    jQuery('#sync_' + value).prop('checked', false);
                    jQuery('#attr_' + value).prop('checked', false);
                });
            }

            if (jQuery('#includeProductFromSync').val() != '') {
                include = jQuery('#includeProductFromSync').val().split(',');
                product_list = jQuery('#includeProductFromSync').val();
                jQuery('#syncAll').prop('checked', false);
                jQuery('input[name="syncProduct"]').prop('checked', false);
                jQuery('input[name="attrProduct"]').prop('checked', false);
                jQuery('#selectAllunchecked').val(1);

                $.each(include, function(key, value) {
                    jQuery('#sync_' + value).prop('checked', true);
                    jQuery('#attr_' + value).prop('checked', true);
                });
            }

            /****************Check Channel Status before fetching product status start **********************************/
            var status = "<?php echo $result[0]['status'] ?>";
            var tiktok_status = "<?php echo $result[0]['tiktok_status'] ?>";
            var is_mapping_update = "<?php echo $result[0]['is_mapping_update']; ?>"
            if(status == 'Draft' && tiktok_status == 'Draft' && is_mapping_update != '1'){
                jQuery('.action_').remove();
                $("input:checkbox[name=attrProduct]").each(function() {
                    jQuery('.channelStatus_' + $(this).val()).empty();
                    jQuery('.channelStatus_' + $(this).val()).html('Not yet sync');
                    jQuery('#channel_action_' + $(this).val()).empty();
                    jQuery('#channel_action_' + $(this).val()).append(
                        '<span class="material-symbols-outlined filteredProductSyn pointer" id="filteredProductSyn_' + $(this).val() + '" value="' + $(this).val() + '">cached</span><input type="hidden" class="filteredProductSyn_' + $(this).val() + '" value="' + $(this).val() + '">'
                    )
                });

            } else if (status == 'Draft' && tiktok_status == 'Draft' && is_mapping_update == '1') {
                jQuery('.action_').remove();
                $("input:checkbox[name=attrProduct]").each(function() {
                    jQuery('#channel_action_' + $(this).val()).empty();
                    jQuery('#channel_action_' + $(this).val()).append(
                        '<span class="material-symbols-outlined  no-drop" value="' + $(this).val() + '" title="Product Sync In Progress">cached</span>'
                    );

                    if ($(this).is(':checked')) {
                        jQuery('.pending_count_' + $(this).val()).text(1);
                    }

                    var channel_ids = <?php echo json_encode($channel_id) ?>;
                    if(channel_ids.indexOf('1') != -1){
                        jQuery('.pending_issue_text_' + $(this).val()).append("<h2 class='card-title fs-6'><img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/google_channel_logo.png') ?>' /> Google Merchant Center</h2><span class='text-dark fs-7'><ul class='b'><li>Product Sync In Progress</li></ul></span>");
                    }

                    if(channel_ids.indexOf('3') != -1){
                        jQuery('.pending_issue_text_' + $(this).val()).append("<h2 class='card-title fs-6'><img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/tiktok_channel_logo.png') ?>' /> TikTok Catalog Center</h2><span class='text-dark fs-7'><ul class='b'><li>Product Sync In Progress</li></ul></span>");
                    }
                });

                jQuery('.allPendingCount').text(addCommas(<?php echo $result[0]['total_product'] !== "" ? $result[0]['total_product'] : 0 ?>));

            } else if (status == 'In Progress' && tiktok_status == 'In Progress' && is_mapping_update == '1') {
                jQuery('.action_').remove();
                $("input:checkbox[name=attrProduct]").each(function() {
                    jQuery('#channel_action_' + $(this).val()).empty();
                    jQuery('#channel_action_' + $(this).val()).append(
                        '<span class="material-symbols-outlined  no-drop" value="' + $(this).val() + '" title="Product Sync In Progress">cached</span>'
                    );

                    if ($(this).is(':checked')) {
                        jQuery('.pending_count_' + $(this).val()).text(1);
                    }

                    var channel_ids = <?php echo json_encode($channel_id) ?>;
                    if(channel_ids.indexOf('1') != -1){
                        jQuery('.pending_issue_text_' + $(this).val()).append("<h2 class='card-title fs-6'><img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/google_channel_logo.png') ?>' /> Google Merchant Center</h2><span class='text-dark fs-7'><ul class='b'><li>Product Sync In Progress</li></ul></span>");
                    }

                    if(channel_ids.indexOf('3') != -1){
                        jQuery('.pending_issue_text_' + $(this).val()).append("<h2 class='card-title fs-6'><img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/tiktok_channel_logo.png') ?>' /> TikTok Catalog Center</h2><span class='text-dark fs-7'><ul class='b'><li>Product Sync In Progress</li></ul></span>");
                    }
                });

                jQuery('.allPendingCount').text(addCommas(<?php echo $result[0]['total_product'] !== "" ? $result[0]['total_product'] : 0 ?>));
            } else {
                /*********Fetch real time status from API***********/
                var all_check_list = Array(); // all checked box value
                $("input:checkbox[name=attrProduct]:checked").each(function() {
                    all_check_list.push($(this).val());
                });
                var uncheck = Array(); // get all unchecked check box
                $("input:checkbox[name=attrProduct]:not(:checked)").each(function() {
                    uncheck.push($(this).val());
                    /*******Append filter sync button for unchecked checkbox *********/
                    jQuery('.channelStatus_' + $(this).val()).empty();
                    jQuery('.channelStatus_' + $(this).val()).html('Not yet sync');
                    jQuery('#action_' + $(this).val().replace(prefix, '')).remove();
                    jQuery('#channel_action_' + $(this).val()).empty();
                    jQuery('#channel_action_' + $(this).val()).append('<span class="material-symbols-outlined filteredProductSyn" id="filteredProductSyn_' + $(this).val() + '" value="' + $(this).val().replace(prefix, '') + '">cached</span><input type="hidden" class="filteredProductSyn_' + $(this).val() + '" value="' + $(this).val().replace(prefix, '') + '">');
                });
                /*******************************Fetch Product status if mapping updated and feed status is Synced for all channel *******/
                var product_data = {
                    action: "ee_get_product_status",
                    product_list: all_check_list.join(','),
                    feed_id: jQuery('#feed_id').val(),
                    maxResults: jQuery('select[name=product_list_table_length]').val(),
                    conv_licence_nonce: "<?php echo esc_html(wp_create_nonce('conv_licence-nonce')); ?>"
                };
                jQuery.ajax({
                    type: "POST",
                    dataType: "json",
                    url: tvc_ajax_url,
                    data: product_data,
                    success: function(response) {
                        /*********Remove All other spinner***********/
                        jQuery('.status_').remove();
                        jQuery('.issue_').remove();
                        jQuery('.action_').remove();
                        if (response != "Product does not exists" && response != "Product not synced") {                            
                            var AllApproved = 0;
                            var AllDisapproved = 0;
                            var AllPending = 0;
                            $.each(response, function(key, value) {
                                var countApproved = 0;
                                var countDisapproved = 0;
                                if(value.productId == '' || value.productId == null) {                                    
                                    return true;
                                }

                                var prodID = value.productId.split(':');
                                all_check_list = $.grep(all_check_list, function(values) {
                                    return values != prodID[3];
                                });

                                if (value.googleStatus){
                                    if (value.googleStatus && value.googleStatus !== 'disapproved') {
                                        countApproved++;
                                        AllApproved++;
                                        jQuery('.channel_logo_' + prodID[3]).append("<img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/google_channel_logo.png') ?>' />");
                                    } else {
                                        countDisapproved++;
                                        AllDisapproved++;
                                        var uniqueGoogleIssues = value.googleIssues.filter(function(itm, i, a) {
                                            return i == a.indexOf(itm);
                                        });
                                        var gIssue = '<ul class="b">';
                                        $.each(uniqueGoogleIssues, function(key, issue) {
                                            gIssue += '<li>' + issue + '</li>';
                                        });
                                        gIssue += '</ul>'
                                        jQuery('.rejected_issue_text_' + prodID[3]).append("<h2 class='card-title fs-6'><img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/google_channel_logo.png') ?>' /> Google Merchant Center</h2><span class='text-dark fs-7'>" + gIssue + "</span>");
                                    }
                                }

                                if(value.tiktokStatus && value.tiktokStatus !== 'disapproved') {
                                    countApproved++;
                                    AllApproved++;
                                    jQuery('.channel_logo_' + prodID[3]).append("<img src='<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/tiktok_channel_logo.png') ?>' />");
                                }

                                jQuery('.approved_count_' + prodID[3]).text(countApproved);
                                jQuery('.rejected_count_' + prodID[3]).text(countDisapproved)
                                jQuery('#channel_action_' + prodID[3]).empty();
                                jQuery('#channel_action_' + prodID[3]).append('<span class="material-symbols-outlined pointer" id="action_' + prodID[3] + '" value="Delete" onclick="deleteProduct(' + jQuery('#feed_id').val() + ', \'' + prodID[3] + '\')">delete</span>');
                                jQuery('#channel_action_' + prodID[3]).next('.action_').remove();
                            });

                            jQuery('.allApprovedCount').text(addCommas(AllApproved));
                            jQuery('.allRejectedCount').text(addCommas(AllDisapproved));
                            $.each(all_check_list, function(remain_key, remain_value) {
                                jQuery('.channelStatus_' + remain_value).empty();
                                jQuery('.channelStatus_' + remain_value).html('Not yet sync');
                                jQuery('#channel_action_' + remain_value).empty();
                                jQuery('#channel_action_' + remain_value).append('<span class="material-symbols-outlined filteredProductSyn pointer" id="filteredProductSyn_' + remain_value + '" value="' + remain_value.replace(prefix, '') + '">cached</span><input type="hidden" class="filteredProductSyn_' + remain_value + '" value="' + remain_value.replace(prefix, '') + '">');
                            });

                            $.each(uncheck, function(ch_key, ch_value) {
                                jQuery('.channelStatus_' + ch_value).empty();
                                jQuery('.channelStatus_' + ch_value).html('Not yet sync');
                                jQuery('#channel_action_' + ch_value).empty();
                                jQuery('#channel_action_' + ch_value).append('<span class="material-symbols-outlined filteredProductSyn pointer" id="filteredProductSyn_' + ch_value + '" value="' + ch_value.replace(prefix, '') + '">cached</span><input type="hidden" class="filteredProductSyn_' + ch_value + '" value="' + ch_value.replace(prefix, '') + '">');
                            });

                        } else {
                            $.each(all_check_list, function(key, value) {
                                jQuery('.channelStatus_' + value).empty();
                                jQuery('.channelStatus_' + value).html('Not yet sync');
                                jQuery('#channel_action_' + value).empty();
                                jQuery('#channel_action_' + value).append('<span class="material-symbols-outlined filteredProductSyn pointer" id="filteredProductSyn_' + value + '" value="' + value + '">cached</span><input type="hidden" class="filteredProductSyn_' + value + '" value="' + value + '">');
                            });

                            $.each(uncheck, function(ch_key, ch_value) {
                                jQuery('.channelStatus_' + ch_value).empty();
                                jQuery('.channelStatus_' + ch_value).html('Not yet sync');
                                jQuery('#channel_action_' + ch_value).empty();
                                jQuery('#channel_action_' + ch_value).append('<span class="material-symbols-outlined filteredProductSyn pointer" id="filteredProductSyn_' + ch_value + '" value="' + ch_value.replace(prefix, '') + '">cached</span><input type="hidden" class="filteredProductSyn_' + ch_value + '" value="' + ch_value.replace(prefix, '') + '">');
                            });
                        }
                    }
                });
            }

            return true;
        });
        /*************************** DataTable init End *********************************************************************************************/
        /**************** Reset Modal filter Start ***************************************/
        jQuery(document).on('click', '#filterReset', function(event) {
            $('#allFilters').empty();
            $("#filterForm")[0].reset();
            $(".product").select2('val', '0');
            jQuery('.select2').select2({
                dropdownParent: $("#allFilters")
            });
        });
        /**************** Reset Modal filter End ******************************************/
        /*********************Add Filter Show Start***********************************************************************/
        jQuery('.addFilter').on('click', function(events) {
            let attr = jQuery('#strProData').val();
            let condition = jQuery('#strConditionData').val();
            let value = jQuery('#strValueData').val();
            var a = 0;
            if (attr != '' && condition != '' && value != '') {
                let attrArry = attr.split(",");
                let conditionArry = condition.split(",");
                let valueArry = value.split(",");
                $('#allFilters').empty()
                jQuery.each(attrArry, function(i, value) {
                    if (a == 0) {
                        a = 1;
                        jQuery('select[name="product[]"]').val(value).trigger('change');
                        jQuery('select[name="condition[]"]').val(conditionArry[i]).trigger('change');
                        if (value === 'product_cat' || value === '_stock_status') {
                            jQuery('select[name="value[]"]').val(valueArry[i]).trigger('change');
                        } else {
                            jQuery('input[name="value[]"]').val(valueArry[i]);
                        }

                    } else {
                        var conditionDropDown = getConditionDropDown(value, conditionArry[i]);
                        if (value === 'product_cat') {
                            var category = <?php echo json_encode($category); ?>;
                            let option = '<option value="0">Select Category</option>';
                            $.each(category, function(key, values) {
                                option += '<option value="' + key + '" ' + ((key == valueArry[i]) ? "selected" : "") + '>' + values + '</option>';
                            });
                            var html = '<select class="select2" name="value[]">' +
                                option +
                                '</select>';
                        } else if (value === '_stock_status') {
                            let option = '<option value="0">Select Stock Status</option>'+
                                        '<option value="instock" ' + ((key == valueArry[i]) ? "selected" : "") + '>In Stock</option>'+
                                        '<option value="outofstock" ' + ((key == valueArry[i]) ? "selected" : "") + '>Out Of Stock</option>';                            
                            var html = '<select class="select2" name="value[]">'
                                + option +
                                '</select>';
                        } else {
                            var html = '<input type="text" class="form-control from-control-overload value" placeholder="Add value" name="value[]" value="' + valueArry[i] + '">';
                        }
                        var newRow = '<div class="filterRow mb-3 row">' +
                            '<div class="col-11 row">' +
                            '<div class="col-4 productDiv">' +
                            '<select class="select2 product" name="product[]">' +
                            '<option value="0">Select Attribute</option>' +
                            '<option value="product_cat" ' + ((value == "product_cat") ? "selected" : "") + '>Category</option>' +
                            '<option value="ID" ' + ((value == "ID") ? "selected" : "") + '>Product Id</option>' +
                            '<option value="post_title" ' + ((value == "post_title") ? "selected" : "") + '>Product Title</option>' +
                            '<option value="_sku" ' + ((value == "_sku") ? "selected" : "") + '>SKU</option>' +
                            '<option value="_regular_price" ' + ((value == "_regular_price") ? "selected" : "") + '>Regular Price</option>' +
                            '<option value="_sale_price" ' + ((value == "_sale_price") ? "selected" : "") + '>Sale Price</option>' +
                            '<option value="post_content" ' + ((value == "post_content") ? "selected" : "") + '>Product Description</option>' +
                            '<option value="post_excerpt" ' + ((value == "post_excerpt") ? "selected" : "") + '>Product Short Description</option>' +
                            '<option value="_stock_status" ' + ((value == "_stock_status") ? "selected" : "") + '>Stock Status</option>'+
                            '</select>' +
                            '</div>' +
                            '<div class="col-4 conditionDiv">' +
                            conditionDropDown +
                            '</div>' +
                            '<div class="col-4 textValue">' +
                            html +
                            '</div>' +
                            '</div>' +
                            '<div class="col-1 pt-2">' +
                            '<span class="material-symbols-outlined deleteButton text-primary" style="cursor: pointer;" title="Remove Filter">remove</span>' +
                            '</div>' +
                            '</div>';
                        $('#allFilters').append(newRow);
                    }
                });
            }
            jQuery('#filterModal').modal('show');
            jQuery('.select2').select2({
                dropdownParent: $("#filterModal")
            });
        });
        /*********************Add Filter Show End************************************************************************/
        /**************** Add more filter Start**************************************************************************/
        jQuery(document).on("click", ".addButton", function(event) {
            var newRow = '<div class="filterRow mb-3 row">' +
                '<div class="col-11 row">' +
                '<div class="col-4 productDiv">' +
                '<select class="select2 product" name="product[]">' +
                '<option value="0">Select Attribute</option>' +
                '<option value="product_cat">Category</option>' +
                '<option value="ID">Product Id</option>' +
                '<option value="post_title">Product Title</option>' +
                '<option value="_sku">SKU</option>' +
                '<option value="_regular_price">Regular Price</option>' +
                '<option value="_sale_price">Sale Price</option>' +
                '<option value="post_content">Product Description</option>' +
                '<option value="post_excerpt">Product Short Description</option>' +
                '<option value="_stock_status">Stock Status</option>' +
                '</select>' +
                '</div>' +
                '<div class="col-4 conditionDiv">' +
                '<select class="select2 condition" name="condition[]">' +
                '<option value="0">Select Conditions</option>' +
                '</select>' +
                '</div>' +
                '<div class="col-4 textValue">' +
                '<input type="text" class="form-control from-control-overload value" placeholder="Add value" name="value[]" >' +
                '</div>' +
                '</div>' +
                '<div class="col-1 pt-2">' +
                '<span class="material-symbols-outlined deleteButton text-primary" style="cursor: pointer;" title="Remove Filter">remove</span>' +
                '</div>' +
                '</div>';

            $('#allFilters').append(newRow);
            jQuery('.select2').select2({
                dropdownParent: $("#filterModal")
            });
        });
        /**************** Add more filter End******************************************************************************/
        /**************** get dependent dropdown product change start******************************************************/
        jQuery(document).on('change', '.product', function(event) {
            var changeValue = $(this).val();
            $(this).parent().parent().children('div').eq(1).empty();
            var conditionDropDown = getConditionDropDown(changeValue);
            $(this).parent().parent().children('div').eq(1).append(conditionDropDown);
            if (changeValue === 'product_cat') {
                var category = <?php echo json_encode($category); ?>;
                let option = '<option value="0">Select Category</option>';
                $.each(category, function(key, value) {
                    option += '<option value="' + key + '">' + value + '</option>';
                });
                $(this).parent().parent().children('.textValue').empty();
                var html = '<select class="select2" name="value[]">' +
                    option +
                    '</select>';
                $(this).parent().parent().children('.textValue').append(html);
            } else if (changeValue === '_stock_status'){
                $(this).parent().parent().children('.textValue').empty();
                var html = '<select class="select2" name="value[]">'+
                    '<option value="0">Select Stock Status</option>'+
                    '<option value="instock">In Stock</option>'+
                    '<option value="outofstock">Out Of Stock</option>'+
                    '</select>';
                $(this).parent().parent().children('.textValue').append(html);
            } else {
                $(this).parent().parent().children('.textValue').empty();
                var html = '<input type="text" class="form-control from-control-overload value" placeholder="Add value" name="value[]" >';
                $(this).parent().parent().children('.textValue').append(html);
            }
            jQuery('.select2').select2({
                dropdownParent: $("#allFilters")
            });
        });
        /**************** get dependent dropdown product change end**********************************************************/
        /**************** Delete Add more filed column start*****************************************************************/
        $("body").on("click", ".deleteButton", function() {
            $(this).parents(".filterRow").remove();
        });
        /**************** Delete Add more filed column  end******************************************************************/
        /****************Feed Name error dismissed start************************/
        jQuery(document).on('input', '#feedName', function (e) {
            e.preventDefault();
            jQuery('#feedName').css('margin-left', '0px');
            jQuery('#feedName').css('margin-right', '0px');
            jQuery('#feedName').removeClass('errorInput');
        });
        jQuery(document).on('click', '#gmc_id', function (e) {
            $('.errorChannel').css('color', '');
        });
        jQuery(document).on('change', '#target_country_feed', function (e) {
            var tiktok_business_account = "<?php echo $tiktok_business_account ?>";
            let target_country = jQuery('#target_country_feed').find(":selected").val();
            jQuery('#tiktok_id').empty();
            jQuery('.tiktok_catalog_id').empty();
            jQuery('#tiktok_catalog_id').val('');
            if (target_country !== "" && tiktok_business_account !== "" && jQuery('input#tiktok_id').is(':checked')) {
                getCatalogId(target_country);
            }
            $('.select2-selection').css('border', '1px solid #c6c6c6');
        });
        /****************Feed Name error dismissed end**************************/
        /****************Submit Feed call start*********************************/
        jQuery(document).on('click', '#submitFeed', function(e) {
            e.preventDefault();
            let feedName = jQuery('#feedName').val();

            if (feedName === '') {
                jQuery('#feedName').css('margin-left', '0px');
                jQuery('#feedName').css('margin-right', '0px');
                jQuery('#feedName').addClass('errorInput');
                var l = 4;
                for (var i = 0; i <= 2; i++) {
                    $('#feedName').animate({
                        'margin-left': '+=' + (l = -l) + 'px',
                        'margin-right': '-=' + l + 'px'
                    }, 50);
                }
                return false;
            }

            let target_country = jQuery('#target_country_feed').find(":selected").val();
            if(target_country === ""){
                jQuery('.select2-selection').css('border', '1px solid #ef1717');                
                return false;
            } 

            if (!$('#gmc_id').is(":checked") && !$('#tiktok_id').is(":checked")) {
                $('.errorChannel').css('border', '1px solid red');
                return false;
            }
            save_feed_data();
        });

        /****************Submit Feed call end***********************************/
        /**************** Get filtered data Start ******************************************/
        jQuery(document).on('click', '#filterSubmit', function(event) {
            let product = $("select[name='product[]'] option:selected").map(function() {
                return $(this).val();
            }).get();
            let producttext = $("select[name='product[]'] option:selected").map(function() {
                return $(this).text();
            }).get();
            let condition = $("select[name='condition[]'] option:selected").map(function() {
                return $(this).val();
            }).get();
            let value = $("input[name='value[]']").map(function() {
                return $(this).val();
            }).get();
            let seltext = $("select[name='value[]'] option:selected").map(function() {
                return $(this).text();
            }).get();
            let selVal = $("select[name='value[]'] option:selected").map(function() {
                return $(this).val() ? $(this).val() : '';
            }).get();
            let flag = 0;
            let valFlag = 0;
            let prodData = Array();
            let conditionData = Array();
            let valueData = Array();
            $('#addFiltersCard').empty();
            jQuery.each(product, function(i, val) {
                if (val != "0" && condition[i] != "0" && (value[valFlag] != "" || selVal[flag] != "")) {
                    if (val === 'product_cat' || val === '_stock_status') {
                        prodData[i] = val;
                        conditionData[i] = condition[i];
                        valueData[i] = selVal[flag];
                        var newCard = '<div class="btn-group border rounded mt-1 me-1 removecardThis" >' +
                            '<button class="btn btn-light btn-sm text-secondary fs-7 ps-1 pe-1 pt-0 pb-0" type="button" value="' + i + '">' + producttext[i] + ' <b>' + condition[i] + '</b> ' + seltext[flag++] + '</button>' +
                            '<button type="button" class="btn btn-sm btn-light onhover-close pt-0 pb-0" data-bs-toggle="" aria-expanded="false" style="cursor: pointer;">' +
                            '<span class="material-symbols-outlined fs-6 pt-1 onhover-close removecard">close</span></button></div>';
                    } else {

                        prodData[i] = val;
                        conditionData[i] = condition[i];
                        valueData[i] = value[valFlag];
                        var newCard = '<div class="btn-group border rounded mt-1 me-1 removecardThis">' +
                            '<button class="btn btn-light btn-sm text-secondary fs-7 ps-1 pe-1 pt-0 pb-0" type="button" value="' + i + '">' + producttext[i] + ' <b>' + condition[i] + '</b> ' + value[valFlag++] + '</button>' +
                            '<button type="button" class="btn btn-sm btn-light onhover-close pt-0 pb-0" data-bs-toggle="" aria-expanded="false" style="cursor: pointer;">' +
                            '<span class="material-symbols-outlined fs-6 pt-1 onhover-close removecard">close</span></button></div>';
                    }
                    $('#addFiltersCard').append(newCard);
                    //count++;
                }
            });
            $('#strProData').val('');
            $('#strConditionData').val('');
            $('#strValueData').val('');
            let strProData = prodData.join(',');
            let strConditionData = conditionData.join(',');
            let strValueData = valueData.join(',');
            $('#strProData').val($('#strProData').val() ? $('#strProData').val() + "," + strProData : strProData);
            $('#strConditionData').val($('#strConditionData').val() ? $('#strConditionData').val() + "," + strConditionData : strConditionData);
            $('#strValueData').val($('#strValueData').val() ? $('#strValueData').val() + "," + strValueData : strValueData);
            $('#excludeProductFromSync').val('');
            $('#includeProductFromSync').val('');
            $('#selectAllunchecked').val('');
            $('#includeExtraProductForFeed').val('');
            $('#allFilters').empty();
            $("#filterForm")[0].reset();
            $(".product").select2('val', '0');
            $('#filterDelete').addClass('disabled');
            jQuery('#filterModal').modal('hide');
            table.draw();
        });
        /************************************* Get filtered data End **********************************************************************************/
        /***************************** Remove Cards Startm ******************************************************************************************/
        jQuery(document).on('click', '.removecard', function(event) {
            var ele = $(this).parent();
            var strProData = $('#strProData').val().split(',');
            var strConditionData = $('#strConditionData').val().split(',');
            var strValueData = $('#strValueData').val().split(',');
            var val = ele.prev().val();
            $(ele.parent()).remove();

            strProData.splice(val, 1);
            strConditionData.splice(val, 1);
            strValueData.splice(val, 1);

            $(".removecard").each(function(index, value) {
                $(this).parent().prev().val(index);
            });


            strProData = strProData.join();
            strConditionData = strConditionData.join();
            strValueData = strValueData.join();

            $('#strProData').val(strProData);
            $('#strConditionData').val(strConditionData);
            $('#strValueData').val(strValueData);
            $('#excludeProductFromSync').val('');
            $('#includeProductFromSync').val('');
            $('#selectAllunchecked').val('');
            $('#includeExtraProductForFeed').val('');
            table.draw();
        });
        /****************************** Remove Cards End *********************************************************************************************/
        /****************************** Select All CheckBox Start ***********************************************************************************/
        jQuery(document).on('click', '#syncAll', function(e) {
            $(".checkbox").prop('checked', $(this).prop('checked'));
            if ($(this).prop("checked")) {
                jQuery('#excludeProductFromSync').val('')
                jQuery('#selectAllunchecked').val('');
                jQuery('#includeProductFromSync').val('');
            } else {
                jQuery('#selectAllunchecked').val(1);
            }
        });

        jQuery(document).on('change', '.checkbox', function(e) {
            let exclude = [];
            let include = [];
            if (jQuery('#excludeProductFromSync').val() != '') {
                exclude = jQuery('#excludeProductFromSync').val().split(',');
            }
            if (jQuery('#includeProductFromSync').val() != '') {
                include = jQuery('#includeProductFromSync').val().split(',');
            }

            if (jQuery('#selectAllunchecked').val() == 1) {
                if ($(this).prop("checked")) {
                    include.push($(this).val());
                    let uniqueInclude = include.filter((item, i, ar) => ar.indexOf(item) === i);
                    let val = uniqueInclude.join(',');
                    jQuery('#includeProductFromSync').val(val);
                } else {
                    const newArr = include.filter(e => e !== $(this).val());
                    jQuery('#includeProductFromSync').val(newArr.join(','));
                }
            } else {
                if (!$(this).prop("checked")) {
                    $("#syncAll").prop("checked", false);
                    exclude.push($(this).val());
                    let unique = exclude.filter((item, i, ar) => ar.indexOf(item) === i);
                    let val = unique.join(',');
                    jQuery('#excludeProductFromSync').val(val);
                } else {
                    const newArr = exclude.filter(e => e !== $(this).val());
                    jQuery('#excludeProductFromSync').val(newArr.join(','));
                }
            }
            getrealcheckedcount();
        });
        /****************************** Select All CheckBox End ************************************************************************************/
        /****************************** Product Wise Category Start ************************************************************************************/
        jQuery(document).on('click', '.filteredProductSyn', function(e) {
            var thisobjVal = $(this).val();
            var e_target = e.target.id                   
            let productArray = Array();
            let exclude = Array();
            let include = Array();
            let attr_ids = <?php echo $attr_id ?>;
            let prefix = "<?php echo $result[0]['product_id_prefix'] ?>";
            if (prefix != '') {
                jQuery('#product_id_prefix').val(prefix);
            }
            if (attr_ids != 1) {
                $.each(attr_ids, function(attr_key, attr_value) {
                    jQuery('select[name^="' + attr_key + '"] option[value="' + attr_value + '"]').attr("selected", "selected");
                });
            }
            var prodId = '';                 
            if (thisobjVal != 'syncAll') {
                var thisVal = jQuery('.' + e_target).val();
                productArray.push($('.syncProduct_' + thisVal).val());
                if (attr_ids != 1) {
                    exclude = jQuery('#excludeProductFromSync').val().split(',');
                    include = jQuery('#includeProductFromSync').val();
                    if (exclude != '') {
                        prodId = thisVal;
                        var arrProd = [];
                        exclude = jQuery.grep(exclude, function(values) {
                            return values != prodId;
                        });
                        var excludeProd = exclude.join(',');
                        jQuery('#excludeProductFromSync').val(excludeProd);
                        jQuery('#includeExtraProductForFeed').val(prodId);
                    } else if (include != '') {
                        prodId = thisVal;
                        jQuery('#includeProductFromSync').val(include + ',' + prodId);
                        jQuery('#includeExtraProductForFeed').val(prodId);
                    }

                } else {
                    $(".checkbox").prop('checked', false);
                    jQuery('.syncProduct_' + thisVal).prop('checked', true);
                    jQuery('#includeProductFromSync').val(thisVal);
                    jQuery('#excludeProductFromSync').val('');
                }

            } else if (jQuery('#excludeProductFromSync').val() != '' && jQuery('#selectAllunchecked').val() == '') {
                exclude = jQuery('#excludeProductFromSync').val().split(',');
                jQuery('#includeProductFromSync').val('');
            } else if (jQuery('#includeProductFromSync').val() != '' && jQuery('#selectAllunchecked').val() != '') {
                include = jQuery('#includeProductFromSync').val().split(',');
                jQuery('#excludeProductFromSync').val('');
            }
            
            if (!$.isEmptyObject(productArray) || !$.isEmptyObject(exclude) || (thisobjVal == 'syncAll' && $('.syncProduct').is(':checked')) || !$.isEmptyObject(include)) {
                // jQuery('#target_country').css('-webkit-appearance', 'none');
                var data = {
                    action: "ee_syncProductCategory",
                    productData: jQuery("#strProData").val(),
                    conditionData: jQuery("#strConditionData").val(),
                    valueData: jQuery("#strValueData").val(),
                    searchName: jQuery("#searchName").val(),
                    productArray: productArray,
                    exclude: exclude,
                    include: include,
                    inculdeExtraProduct: prodId
                };
                jQuery.ajax({
                    url: tvc_ajax_url,
                    type: "POST",
                    data: data,
                    dataType: "json",
                    beforeSend: function() {
                        conv_change_loadingbar('show');
                    },
                    error: function(err, status) {
                        conv_change_loadingbar('hide');
                    },
                    success: function(response) {
                        conv_change_loadingbar('hide');
                        selected_cat_id = response;
                        $('#selectedCategory').val('');
                        $('.catTermId').hide();
                        let totCatCount = 0;
                        $('.catTermId').each(function(index) {
                            totCatCount++;
                        })
                        let selectedCategory = [];
                        let catCount = 0;
                        $.each(response, function(key, value) {
                            var last_element = value;
                            $('#selectedCategory').val() == '' ? $('#selectedCategory').val($.trim(last_element)) : $('#selectedCategory').val($('#selectedCategory').val() + ',' + $.trim(last_element))
                            $('.termId_' + $.trim(last_element)).show();
                        });
                        catCount = $('.catTermId').filter(function(index) {
                            return $(this).css('display') !== 'none';
                        }).length;
                        $('.catCount').html(catCount + ' Out of ' + totCatCount + ' categories found. Only mapped categories will be synced in Google Merchant Center.');
                        jQuery('.productAttribute').css('display', 'none')

                        var target_country = "<?php echo $result[0]['target_country'] ?>";
                        if(target_country !== ""){
                            jQuery('#target_country').val("<?php echo $result[0]['target_country'] ?>");
                        }                            
                        currentTab = 0; // Current tab is set to be the first tab (0)
                        showTab(currentTab); // Display the current tab
                        $("#categoryModal").modal("show");
                        $('.catCount').show();
                        $('.asterisk').show();
                        setTimeout(function() {
                            jQuery('.select2').select2({
                                dropdownParent: $("#categoryModal")
                            });
                        }, 100);
                    }
                });
            } else {
                conv_change_loadingbar('hide');
                jQuery(".errorText").html('Error: No Product Selected');
                jQuery("#conv_save_error_txt").html("We're sorry, but it seems you haven't selected any products. <br/>In order to proceed, please select at least one product from the available options.");
                jQuery("#conv_save_error_modal").modal("show");
            }
            
        });
        /****************************** Product Wise Category End ************************************************************************************/
        /****************************** Mapping value is Numeric Start ************************************************************************************/
        jQuery(document).on('keydown', 'input[name="shipping"]', function(event){
            if (event.shiftKey == true) {
                event.preventDefault();
            }
            if ((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105) || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 || event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190) {

            } else {
                event.preventDefault();
            }

            if($(this).val().indexOf('.') !== -1 && event.keyCode == 190)
                event.preventDefault();
        })
        jQuery(document).on('keydown', 'input[name="tax"]', function(){
            if (event.shiftKey == true) {
            event.preventDefault();
            }
            if ((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105) || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 || event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190) {

            } else {
                event.preventDefault();
            }

            if($(this).val().indexOf('.') !== -1 && event.keyCode == 190)
                event.preventDefault();
        })
        /****************************** Mapping value is Numeric End ************************************************************************************/
        jQuery(document).on('change', '#tiktok_id', function () {
            jQuery('.tiktok_catalog_id').empty();
            jQuery('#tiktok_catalog_id').val('');
            jQuery('#tiktok_id').val('');
            if ($('#tiktok_id').is(":checked")) {
                getCatalogId(jQuery('#target_country_feed').find(":selected").val())
            }
        });
    });

    /*********************Card Popover Start***********************************************************************/
    jQuery(document).on('mouseover', '.approvedChannel', function() {
        if ($(this).find('span').html() > 0) {
            var content = $(this).next().html();
            jQuery(this).popover({
                html: true,
                template: content,
            });
            jQuery(this).popover('show');
        }
    })

    jQuery(document).on('mouseover', '.pendingIssues', function() {
        if ($(this).find('span').html() > 0) {
            var content = $(this).next().html();
            jQuery(this).popover({
                html: true,
                template: content,
            });
            jQuery(this).popover('show');
        }
    })

    jQuery(document).on('mouseover', '.rejectIssues', function() {
        if ($(this).find('span').html() > 0) {
            var content = $(this).next().html();
            jQuery(this).popover({
                html: true,
                template: content,
            });
            jQuery(this).popover('show');
        }
    })
    /*********************Card Popover  End**************************************************************************/
    /*************************************Get Condition Dropdown Start******************************************************************/
    function getConditionDropDown(val = '', condition = '') {
        let conditionOption = '<select class="select2 condition" name="condition[]"><option value="0">Select Condition</option>';
        if (val != '0') {
            if (val != '' || condition != '') {
                switch (val) {
                    case 'product_cat':
                    case 'ID':
                        conditionOption += '<option value="=" ' + ((condition == "=") ? "selected" : "") + ' > = </option>' +
                            '<option value="!=" ' + ((condition == "!=") ? "selected" : "") + ' > != </option>';
                        break;
                    case '_stock_status':
                        conditionOption += '<option value="=" ' + ((condition == "=") ? "selected" : "") + ' > = </option>' +
                            '<option value="!=" ' + ((condition == "!=") ? "selected" : "") + ' > != </option>';
                        break;
                    case 'post_title':
                    case '_sku':
                    case 'post_content':
                    case 'post_excerpt':
                        conditionOption += '<option value="Contains" ' + ((condition === "Contains") ? "selected" : "") + ' > Contains </option>' +
                            '<option value="Start With" ' + ((condition === "Start With") ? "selected" : "") + ' > Start With </option>' +
                            '<option value="End With" ' + ((condition === "End With") ? "selected" : "") + ' > End With </option>';
                        break;
                    case '_regular_price':
                    case '_sale_price':
                        conditionOption += '<option value="=" ' + ((condition === "=") ? "selected" : "") + ' > = </option>' +
                            '<option value="!=" ' + ((condition === "!=") ? "selected" : "") + ' > != </option>' +
                            '<option value="<" ' + ((condition === "<") ? "selected" : "") + ' > < </option>' +
                            '<option value=">" ' + ((condition === ">") ? "selected" : "") + ' > > </option>' +
                            '<option value=">=" ' + ((condition === ">=") ? "selected" : "") + ' > >= </option>' +
                            '<option value="<=" ' + ((condition === "<=") ? "selected" : "") + ' > <= </option>';
                        break;
                }
            }
        }
        conditionOption += '</select>';
        return conditionOption;
    }
    /*************************************Get Condition Dropdown End**********************************************************************/
    /*************************************Save Feed Data Start*************************************************************************/
    function save_feed_data(google_merchant_center_id, catalog_id) {
        var conv_onboarding_nonce = "<?php echo esc_html(wp_create_nonce('conv_onboarding_nonce')); ?>"
        let edit = jQuery('#edit').val()
        var planid = "<?php echo esc_attr($plan_id); ?>";
        var data = {
            action: "save_feed_data",
            feedName: jQuery('#feedName').val(),
            google_merchant_center: jQuery('input#gmc_id').is(':checked') ? '1' : '',
            autoSync: jQuery('input#autoSync').is(':checked') ? '1' : '0',
            autoSyncIntvl: (planid == '1') ? '25' : jQuery('#autoSyncIntvl').val(),
            edit: edit,
            last_sync_date: jQuery('#last_sync_date').val(),
            is_mapping_update: jQuery('#is_mapping_update').val(),
            target_country: jQuery('#target_country_feed').find(":selected").val(),
            customer_subscription_id: "<?php echo $subscriptionId ?>",
            tiktok_business_account : "<?php echo $tiktok_business_account ?>",
            tiktok_id: jQuery('input#tiktok_id').is(':checked') ? '3' : '',
            tiktok_catalog_id: jQuery('input#tiktok_id').is(':checked') ? jQuery('input#tiktok_id').val() : '',
            conv_onboarding_nonce: conv_onboarding_nonce
        }
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: data,
            beforeSend: function() {
                conv_change_loadingbar_modal('show');
            },
            success: function(response) {
                conv_change_loadingbar_modal('hide');                
                if (response.id) {
                    jQuery('#convCreateFeedModal').modal('hide');
                    jQuery("#conv_save_success_txt").html("Great job! Your product feed is ready! The next step is to select the products you want to sync and expand your reach across multiple channels.");
                    jQuery("#conv_save_success_modal").modal("show");
                    setTimeout(function() {
                        if (edit != '') {
                            location.reload(true);
                        } else {
                            window.location.replace("<?php echo esc_url_raw($site_url . 'product_list&id='); ?>" + response.id);
                        }

                    }, 100);
                } else if (response.errorType === 'tiktok') { 
                    jQuery('.tiktok_catalog_id').empty();
                    jQuery('.tiktok_catalog_id').html(response.message);
                    jQuery('.tiktok_catalog_id').addClass('text-danger');

                } else {
                    jQuery('#convCreateFeedModal').modal('hide');
                    jQuery(".errorText").html('Error: Data Saving Failed');
                    jQuery("#conv_save_error_txt").html(response.message);
                    jQuery("#conv_save_error_modal").modal("show");
                }

            }
        });

    }
    /*************************************Save Feed Data End***************************************************************************/
    /*************************************Edit Feed Data Start*************************************************************************/
    function editFeed($id) {
        jQuery('#gmc_id').attr('disabled', false);
        jQuery('#target_country_feed').attr('disabled', false); 
        var conv_onboarding_nonce = "<?php echo esc_html(wp_create_nonce('conv_onboarding_nonce')); ?>"
        var data = {
            action: "get_feed_data_by_id",
            id: $id,
            conv_onboarding_nonce: conv_onboarding_nonce
        }
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: data,
            beforeSend: function() {
                conv_change_loadingbar('show');
            },
            success: function(response) {
                jQuery('#feedName').val(response[0].feed_name);
                jQuery('#last_sync_date').val(response[0].last_sync_date);
                jQuery('#is_mapping_update').val(response[0].is_mapping_update);
                jQuery('#autoSyncIntvl').val(response[0].auto_sync_interval);
                if(response[0].target_country){
                    jQuery('#target_country_feed').val(response[0].target_country);
                }

                if (response[0].auto_schedule == 1) {
                    jQuery('input#autoSync').prop('checked', true);
                    jQuery('#autoSyncIntvl').attr('disabled', false);
                } else {
                    jQuery('input#autoSync').prop('checked', false);
                    jQuery('#autoSyncIntvl').attr('disabled', true);
                }
                jQuery('#gmc_id').prop("checked", false);
                jQuery('#gmc_id').attr('disabled', false);
                jQuery('#tiktok_id').prop("checked", false);
                jQuery('#tiktok_id').attr('disabled', false);
                jQuery('.tiktok_catalog_id').empty();
                jQuery('#tiktok_catalog_id').val('');
                //jQuery('#fb_id').prop("checked", false);
                channel_id = response[0].channel_ids.split(",");
                $.each(channel_id, function (index, val) {
                    if (val === '1') {
                        jQuery('#gmc_id').prop("checked", true);
                    }
                    if (val === '3') {
                        jQuery('#tiktok_id').prop("checked", true);
                        jQuery('#tiktok_id').val(response[0].tiktok_catalog_id);
                        jQuery('.tiktok_catalog_id').html(response[0].tiktok_catalog_id);
                        jQuery('#tiktok_catalog_id').val(response[0].tiktok_catalog_id)
                    }
                });
                if (response[0].is_mapping_update == '1') {
                    jQuery('#gmc_id').attr('disabled', true);
                    jQuery('#tiktok_id').attr('disabled', true);
                    jQuery('#target_country').attr('disabled', true);
                }
                jQuery('#edit').val(response[0].id);
                jQuery('#centered').html();
                jQuery('#submitFeed').text('Update Feed');
                jQuery('#feedType').text('Edit Feed - ' + response[0].feed_name);
                conv_change_loadingbar('hide');
                jQuery('#convCreateFeedModal').modal('show');
                jQuery('#target_country_feed').select2({ dropdownParent: $("#convCreateFeedModal") });
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            }
        });
    }
    /*************************************Edit Feed Data End****************************************************************************/
    /*************************************Proces Loader Start*************************************************************************/
    function conv_change_loadingbar(state = 'show') {
        if (state == 'show') {
            jQuery("#loadingbar_blue").removeClass('d-none');
            $("#wpbody").css("pointer-events", "none");
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            jQuery("#loadingbar_blue").addClass('d-none');
            jQuery("#wpbody").css("pointer-events", "auto");
        }
    }

    function conv_change_loadingbar_modal(state = 'show') {
        if (state == 'show') {
            jQuery("#loadingbar_blue_modal").removeClass('d-none');
            $("#wpbody").css("pointer-events", "none");
        } else {
            jQuery("#loadingbar_blue_modal").addClass('d-none');
            jQuery("#wpbody").css("pointer-events", "auto");
        }
    }
    /*************************************Process Loader End*************************************************************************/
    /*************************************Restrict Zero start*************************************************************************/
    function removeZero() {
        var val = $("#autoSyncIntvl").val();
        if (val == 0) {
            $("#autoSyncIntvl").val('')
        }
    }
    /*************************************Restrict Zero  End*************************************************************************/
    /*************************************Number Format start*************************************************************************/
    function addCommas(nStr) {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }
    /*************************************Number Format End*************************************************************************/
    /*************************************Product Mapping steps Start*************************************************************************/
    function showTab(n) {
        // This function will display the specified tab of the form ...
        var x = document.getElementsByClassName("tab");
        x[n].style.display = "block";
        // ... and fix the Previous/Next buttons:
        if (n == 0) {
            document.getElementById("prevBtn").style.display = "none";
        } else {
            document.getElementById("prevBtn").style.display = "inline";
        }
        if (n == (x.length - 1)) {
            document.getElementById("nextBtn").innerHTML = "Sync Products";
        } else {
            document.getElementById("nextBtn").innerHTML = "Next";
        }
        // ... and run a function that displays the correct step indicator:
        fixStepIndicator(n)
    }

    function nextPrev(n) {
        if (n == 1) {
            $('.catCount').hide();
            $('.asterisk').hide();
        } else {
            $('.catCount').show();
            $('.asterisk').show();
        }
        // This function will figure out which tab to display
        var x = document.getElementsByClassName("tab");
        // Exit the function if any field in the current tab is invalid:
        if (n == 1 && !validateForm()) return false;
        // Hide the current tab:
        x[currentTab].style.display = "none";
        // Increase or decrease the current tab by 1:
        currentTab = currentTab + n;
        // if you have reached the end of the form... :
        if (currentTab >= x.length) {
            //...the form gets submitted:
            //document.getElementById("regForm").submit();
            submitProductSyncUp();
            $("#categoryModal").modal("hide");
            return false;
        }
        // Otherwise, display the correct tab:
        showTab(currentTab);
    }

    function validateForm() {
        // This function deals with validation of the form fields
        var x, y, i, valid = true;
        x = document.getElementsByClassName("tab");
        y = x[currentTab].getElementsByTagName("input");
        // A loop that checks every input field in the current tab:
        for (i = 0; i < y.length; i++) {
            // If a field is empty...
            if (y[i].value == "") {
                // add an "invalid" class to the field:
                y[i].className += " invalid";
                // and set the current valid status to false:
                valid = false;
            }
        }
        // If the valid status is true, mark the step as finished and valid:
        if (valid) {
            document.getElementsByClassName("step")[currentTab].className += " finish";
        }
        return valid; // return the valid status
    }

    function fixStepIndicator(n) {
        // This function removes the "active" class of all steps...
        var i, x = document.getElementsByClassName("step");
        for (i = 0; i < x.length; i++) {
            x[i].className = x[i].className.replace(" active", "");
        }
        //... and adds the "active" class to the current step:
        x[n].className += " active";
    }
    /*************************************Product Mapping steps End***************************************************************************/
    /***********************Append Conversios product category on edit start ******************************************************************/
    function selectSubCategory(thisObj) {
        selectId = thisObj.id;
        wooCategoryId = jQuery(thisObj).attr("catid");
        var selvalue = $('#' + selectId).find(":selected").val();
        var seltext = $('#' + selectId).find(":selected").text();
        jQuery("#category-" + wooCategoryId).val(selvalue);
        jQuery("#category-name-" + wooCategoryId).val(seltext);
        setTimeout(function() {
            jQuery('.select2').select2({
                dropdownParent: $("#categoryModal")
            });
        }, 100);
    }
    /***********************Append Conversios product category on edit end ******************************************************************/
    /**************************stepper change in mapping screen start ******************************************/
    jQuery(document).on("click", ".change_prodct_feed_cat", function() {
        jQuery(this).hide();
        var feed_select_cat_id = jQuery(this).attr("data-id");
        var woo_cat_id = jQuery(this).attr("data-cat-id");
        jQuery("#category-" + woo_cat_id).val("0");
        jQuery("#category-name-" + woo_cat_id).val("");
        jQuery("#label-" + feed_select_cat_id).hide();
        jQuery("#" + feed_select_cat_id).css('width', '100%');
        jQuery("#" + feed_select_cat_id).addClass('select2');
        jQuery("#" + feed_select_cat_id).slideDown();
        jQuery('.select2').select2({
            dropdownParent: $("#categoryModal")
        });
    });
    /**************************stepper change in mapping screen end ******************************************/
    /***********************get all checked count start **********************************************************/
    function getrealcheckedcount() {
        var excludeProductFromSync = jQuery('#excludeProductFromSync').val();
        var includeProductFromSync = jQuery('#includeProductFromSync').val();
        if (excludeProductFromSync !== '' && excludeProductFromSync !== 'syncAll') {
            var count = excludeProductFromSync.split(',').length;
            jQuery('#filteredProductSyn').text('Sync ' + addCommas(totalProduct - count) + ' Products');
            return true;
        }
        if (includeProductFromSync !== '') {
            var count = includeProductFromSync.split(',').length;
            jQuery('#filteredProductSyn').text('Sync ' + addCommas(count) + ' Products');
            return true;
        }
        if (jQuery("#syncAll").prop("checked")) {
            jQuery('#filteredProductSyn').text('Sync ' + addCommas(totalProduct) + ' Products');
            return true;
        }
        jQuery('#filteredProductSyn').text('Sync 0 Products');
    }
    /***********************get all checked count end *******************************************************************/
    /*************************************Submit Product Sync Start***************************************************************************/
    function submitProductSyncUp(sync_progressive_data = null) {
        var data = {
            action: 'ee_feed_wise_product_sync_batch_wise',
            merchant_id: '<?php echo esc_attr($merchantId); ?>',
            account_id: '<?php echo esc_attr($accountId); ?>',
            customer_id: '<?php echo esc_attr($currentCustomerId); ?>',
            subscription_id: '<?php echo esc_attr($subscriptionId); ?>',
            conv_data: jQuery("#productSync").find("input[value!=''], select:not(:empty), input[type='number']").serialize(),
            product_batch_size: jQuery("#product_batch_size").val(),
            product_id_prefix: jQuery("#product_id_prefix").val(),
            conv_nonce: "<?php echo esc_html(wp_create_nonce('conv_ajax_product_sync_bantch_wise-nonce')); ?>",
            // Adding filters value to feed table
            feedId: <?php echo esc_attr(filter_input(INPUT_GET, 'id')) ?>,
            productData: jQuery("#strProData").val(),
            conditionData: jQuery("#strConditionData").val(),
            valueData: jQuery("#strValueData").val(),
            searchName: jQuery("#searchName").val(),
            include: jQuery('#includeProductFromSync').val(),
            exclude: jQuery('#excludeProductFromSync').val(),
            autoSyncInterval: jQuery('#autoSyncInterval').val(),
            channel_ids: jQuery('#channel_ids').val(),
            tiktok_catalog_id: jQuery('#tiktok_catalog_id').val(),    
            inculdeExtraProduct: jQuery('#includeExtraProductForFeed').val()
        }
        
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: data,
            beforeSend: function() {
                conv_change_loadingbar('show');
            },
            error: function(err, status) {
                conv_change_loadingbar('hide');
            },
            success: function(response) {
                conv_change_loadingbar('hide');
                if (response.status == "success") {
                    var message = "Congratulations your products are being synced in your product feed channels. It takes up to 30 minutes for the product data to get reflected in the respective channel's dashboards once the product feed process is completed. You will be able to see the status of the products in the feeds.";
                    jQuery("#conv_save_success_txt").html(message);
                    jQuery("#conv_save_success_modal").modal("show");
                    setTimeout(function() {
                        window.location.replace("<?php echo esc_url_raw($site_url); ?>");
                    }, 1000);

                } else {
                    conv_change_loadingbar('hide');
                    jQuery(".errorText").html('Error: Data Saving Failed');
                    jQuery("#conv_save_error_txt").html(response.message);
                    jQuery("#conv_save_error_modal").modal("show");
                    setTimeout(function() {
                        location.reload(true);
                    }, 1000);
                }
            }
        });
    }
    /*************************************Submit Product Sync End***************************************************************************/
    /*************************************DELETE Feed Data Start**********************************************************************/
    function deleteProduct($id, $product_id = null) {
        $message = 'Product in this feed will be deleted from the channels selected in the feed. Are you sure you want to delete it? ';
        if (confirm($message)) {
            var conv_onboarding_nonce = "<?php echo wp_create_nonce('conv_onboarding_nonce'); ?>"
            var data = {
                action: "ee_delete_feed_gmc",
                feed_id: $id,
                product_ids: $product_id,
                conv_onboarding_nonce: conv_onboarding_nonce
            }
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: tvc_ajax_url,
                data: data,
                beforeSend: function() {
                    conv_change_loadingbar('show');
                },
                error: function(err, status) {
                    conv_change_loadingbar('hide');
                },
                success: function(response) {
                    conv_change_loadingbar('hide');
                    if (response.error == false) {
                        jQuery("#conv_save_success_txt").html('Deleted Successfully!!');
                        jQuery("#conv_save_success_modal").modal("show");
                    } else {
                        jQuery(".errorText").html('Error: Failed');
                        jQuery("#conv_save_error_txt").html(response.message);
                        jQuery("#conv_save_error_modal").modal("show");
                    }
                    location.reload(true);

                }
            });
        }
    }
    /*************************************Delete Feed Data End*************************************************************************/

    /*************************************Select2 in Modal - scroll issue fix start ***************************************************/
    $(document).on('select2:close', '.select2', function(e) {
        var evt = "scroll.select2"
        $(e.target).parents().off(evt)
        $(window).off(evt)
    })
    /*************************************Select2 in Modal - scroll issue fix End *****************************************************/
    /***********************On click Conversios product category select append all data start ********************************************/
    $(document).on('click', '.select2-selection.select2-selection--single', function (e) {
        var iscatMapped = $(this).parent().parent().prev().attr('iscategory')
        var selectId = $(this).parent().parent().prev().attr('id')
        var toAppend = '';
        if (iscatMapped == 'false') {
            $(this).parent().parent().prev().attr('iscategory', 'true')
            $.each(cat_json, function (i, o) {
                toAppend += '<option value="' + o.id + '">' + o.name + '</option>';
            });
            $('#' + selectId).append(toAppend);
            $('#' + selectId).select2({
                dropdownParent: $("#categoryModal")
            });
            $('#' + selectId).select2('open');
        }
    });
    /***********************On click Conversios product category select append all data end********************************************/
    /*************************************Get saved catalog id by country code start **************************************************/
    function getCatalogId($countryCode) {
        var conv_country_nonce = "<?php echo esc_html(wp_create_nonce('conv_country_nonce')); ?>";
        var data = {
            action: "ee_getCatalogId",
            countryCode: $countryCode,
            conv_country_nonce: conv_country_nonce
        }
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: data,
            beforeSend: function () {
                conv_change_loadingbar_modal('show');
            },
            error: function (err, status) {
                conv_change_loadingbar_modal('hide');
            },
            success: function (response) {
                jQuery('.tiktok_catalog_id').empty();
                jQuery('#tiktok_catalog_id').val('');
                jQuery('#tiktok_id').empty();                
                jQuery('.tiktok_catalog_id').removeClass('text-danger');
                if (response.error == false) {
                    if(response.data.catalog_id !== '') {
                        jQuery('#tiktok_id').val(response.data.catalog_id);
                        jQuery('.tiktok_catalog_id').text(response.data.catalog_id);
                        jQuery('#tiktok_catalog_id').val(response.data.catalog_id)
                    }else{
                        jQuery('#tiktok_id').val('Create New');
                        jQuery('.tiktok_catalog_id').text('You do not have a catalog associated with the selected target country. Do not worry we will create a new catalog for you.');
                    }
                }
                conv_change_loadingbar_modal('hide');
            }
        });

    }
    /*************************************Get saved catalog id by country code End ****************************************************/
</script>