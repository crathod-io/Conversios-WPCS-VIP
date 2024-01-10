<?php
/***************** create object for required class ************************/
$tvcProductSyncHelper = new TVCProductSyncHelper();
$tvc_admin_helper = new TVC_Admin_Helper();
$category_wrapper_obj = new Tatvic_Category_Wrapper();

$serializeAttribute = array_unique(array_map("serialize", $tvcProductSyncHelper->wooCommerceAttributes()));
$wooCommerceAttributes = array_map("unserialize", $serializeAttribute);
$ee_mapped_attrs = unserialize(get_option('ee_prod_mapped_attrs'));
$gmcAttributes = $tvc_admin_helper->get_gmcAttributes();

$site_url = "admin.php?page=conversios-google-shopping-feed&tab=product_mapping&product_tab=";

$ee_options = $tvc_admin_helper->get_ee_options_settings();
$tiktok_business_account = '';
if (isset($ee_options['tiktok_setting']['tiktok_business_id']) === TRUE && $ee_options['tiktok_setting']['tiktok_business_id'] !== '') {
    $tiktok_business_account = $ee_options['tiktok_setting']['tiktok_business_id'];
}

$google_merchant_center_id = '';
if (isset($ee_options['google_merchant_id']) === TRUE && $ee_options['google_merchant_id'] !== '') {
    $google_merchant_center_id = $ee_options['google_merchant_id'];
}

// Redirecting if no channel is mapped.
if ($google_merchant_center_id === '' && $tiktok_business_account === '') {
    wp_safe_redirect("admin.php?page=conversios-google-shopping-feed&tab=gaa_config_page");
    exit;
}

// Get Conversios Product Category.
$path = ENHANCAD_PLUGIN_DIR . 'includes/setup/json/category.json';
$str = file_get_contents($path);
$category_json = $str ? json_decode($str, true) : [];
?>
<div class="container-fluid conv-light-grey-bg pt-4 ps-4">
    <div class="row ps-4 pe-4">
        <div class="col-8 m-0 p-0">
            <div class="conv-heading-box">
                <h5 class="fs-20 fw-400">
                    <?php esc_html_e("Attribute Mapping", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </h5>
                <span class="fw-light fs-14 fw-400 text-color">
                    <?php esc_html_e("At Conversios, we provide an automatic mapping feature that enables you to align the categories and attributes of your WooCommerce products with Conversios categories and attributes. This mapping ensures that your product categories and attributes seamlessly correspond to the categories and attributes of the selected channels.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </span>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid conv-light-grey-bg p-4 font-style">
    <div id="loadingbar_blue" class="progress-materializecss d-none ps-2 pe-2">
        <div class="indeterminate"></div>
    </div>
    <div class="ps-2 pe-2 pb-0 pt-2 mt-0 bg-white border-top border-end border-start"
        style="max-width:100%; border-top-left-radius:8px;border-top-right-radius:8px; ">
        <ul class="nav nav-pills " id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button
                    class="text-color fs-14 nav-link-color nav-link <?php echo !isset($_GET['product_tab']) || (isset($_GET['product_tab']) === TRUE && sanitize_text_field(wp_unslash(filter_input(INPUT_GET, 'product_tab'))) === 'attribute') ? 'active' : ''; ?>"
                    id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button"
                    role="tab" aria-controls="pills-profile" aria-selected="false">
                    <?php esc_html_e("Attribute Mapping", "enhanced-e-commerce-for-woocommerce-store") ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button
                    class="text-color fs-14 nav-link-color nav-link <?php echo isset($_GET['product_tab']) === TRUE && sanitize_text_field(wp_unslash(filter_input(INPUT_GET, 'product_tab'))) === 'category' ? 'active' : ''; ?>"
                    id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab"
                    aria-controls="pills-home" aria-selected="true">
                    <?php esc_html_e("Category Mapping", "enhanced-e-commerce-for-woocommerce-store") ?>
                </button>
            </li>
        </ul>
    </div>
    <div class="card  pt-0 mt-0 ps-0 pe-0"
        style="max-width:100%;border-bottom-left-radius:8px;border-bottom-right-radius:8px;">
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade <?php echo isset($_GET['product_tab']) === TRUE && sanitize_text_field(wp_unslash(filter_input(INPUT_GET, 'product_tab'))) === 'category' ? 'show active' : ''; ?>"
                id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                <div class="col-12 row conv-light-grey-bg m-0 p-0" style="height:48px;">
                    <div class="col-4 pt-2">
                        <span class="ps-2 fw-normal">
                            <img
                                src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/woocommerce_logo.png'); ?>" />
                            <?php esc_html_e("WooCommrece Product Category", "enhanced-e-commerce-for-woocommerce-store") ?></span>
                    </div>
                    <div class="col-4 pt-2 ps-0">
                        <span class="ps-1 fw-normal">
                            <img
                                src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conversios_logo.png'); ?>" />
                            <?php esc_html_e("Conversios Product Category", "enhanced-e-commerce-for-woocommerce-store") ?></span>
                    </div>
                </div>
                <div class="col-12 row bg-white m-0 p-0">
                    <div class="col-12 row bg-white m-0 p-0 mb-3">
                        <div class="col-8 row categoryDiv" style="overflow-y: scroll; max-height:500px;">
                            <form id="category_mapping" class="row">
                                <?php echo $category_wrapper_obj->category_table_content(0, 0, 'mapping'); ?>
                                <div class="col-12">
                                    <button type="button" id="cat_mapping_save"
                                        class="btn btn-soft-primary float-end mt-2 ps-4 pe-4">Save</button>
                                </div>
                                <input type="hidden" name="selectedCategory" id="selectedCategory">
                            </form>
                        </div>                        
                        <div class="col-4">
                            <fieldset class="border p-3 mt-2" style="border-radius:8px;">
                                <legend class="float-none w-auto px-3 fs-7">
                                    <?php esc_html_e("Benefits Of Category Mapping", "enhanced-e-commerce-for-woocommerce-store") ?>
                                </legend>
                                <div class="control-group fw-400 text-color">
                                    <p>
                                        <?php esc_html_e("Benefits of product attributes and category mapping for a product feed in Google Merchant Center:", "enhanced-e-commerce-for-woocommerce-store") ?>
                                        <br />
                                        <?php esc_html_e("1. Accurate and Structured Data: Mapping attributes and categories ensures organized and consistent product data, leading to better categorization and relevance.", "enhanced-e-commerce-for-woocommerce-store") ?>
                                        <br />
                                        <?php esc_html_e("2. Improved Visibility: Mapping helps your products appear in relevant search results, increasing visibility and driving targeted traffic.", "enhanced-e-commerce-for-woocommerce-store") ?>
                                        <br />
                                        <?php esc_html_e("3. Enhanced User Experience: Clear and detailed product information improves the overall shopping experience for users.", "enhanced-e-commerce-for-woocommerce-store") ?>
                                        <br />
                                        <?php esc_html_e("4. Better Targeting and Ad Performance: Accurate mapping enables precise targeting, resulting in improved ad performance and higher conversion rates.", "enhanced-e-commerce-for-woocommerce-store") ?>
                                        <br />
                                        <?php esc_html_e("5. Simplified Updates and Maintenance: Mapping facilitates easier updates and maintenance of your product feed.", "enhanced-e-commerce-for-woocommerce-store") ?>
                                        <br />
                                        <?php esc_html_e("5. Reduced Errors and Disapprovals: Proper mapping minimizes the risk of errors or disapprovals in product listings, ensuring compliance with Google's guidelines.", "enhanced-e-commerce-for-woocommerce-store") ?>
                                    </p>
                                </div>
                            </fieldset>
                            <!-- <fieldset class="border p-3 mt-2" style="border-radius:8px;">
                                <legend class="float-none w-auto px-3 fs-7"><?php // esc_html_e("Helpful Videos", "enhanced-e-commerce-for-woocommerce-store") ?></legend>
                                <div class="col-12 row p-0 ms-0">
                                    <div class="col-6" style="height: 100px">
                                        <div class="col-12 bg-warning rounded-3 p-2 h-100 align-middle">
                                            <div class="text-white text-center"><?php // esc_html_e("Benefits of", "enhanced-e-commerce-for-woocommerce-store") ?> </div>
                                            <div class="text-dark text-center"><?php // esc_html_e("Category Mapping", "enhanced-e-commerce-for-woocommerce-store") ?></div>
                                        </div>
                                    </div>
                                    <div class="col-6" style="height: 100px">
                                        <div class="col-12 bg-dark rounded-3 p-2 h-100 align-middle">
                                            <div class="text-white text-center"><?php // esc_html_e("Benefits of", "enhanced-e-commerce-for-woocommerce-store") ?> </div>
                                            <div class="text-warning text-center"><?php // esc_html_e("Attribute Mapping", "enhanced-e-commerce-for-woocommerce-store") ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 row ms-0">
                                    <div class="col-6 text-color font-weight-400">
                                    <?php // esc_html_e("Benefits of Category mapping", "enhanced-e-commerce-for-woocommerce-store") ?>
                                    </div>
                                    <div class="col-6 text-color font-weight-400">
                                    <?php // esc_html_e("Benefits of Attribute mapping", "enhanced-e-commerce-for-woocommerce-store") ?>
                                    </div>
                                </div>
                            </fieldset> -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade <?php echo !isset($_GET['product_tab']) || (isset($_GET['product_tab']) === TRUE && sanitize_text_field(wp_unslash(filter_input(INPUT_GET, 'product_tab'))) === 'attribute') ? 'show active' : ''; ?> "
                id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                <div class="col-12 row conv-light-grey-bg m-0 p-0" style="height:48px;">
                    <div class="col-4 pt-2">
                        <span class="ps-2">
                            <img
                                src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conversios_logo.png'); ?>" />
                            <?php esc_html_e("Conversios Product Attribute", "enhanced-e-commerce-for-woocommerce-store") ?></span>
                    </div>
                    <div class="col-4 pt-2 ps-0">
                        <span class="ps-0">
                            <img
                                src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/woocommerce_logo.png'); ?>" />
                            <?php esc_html_e("WooCommerce Product Attribute", "enhanced-e-commerce-for-woocommerce-store") ?></span>
                    </div>
                </div>
                <div class="col-12 row bg-white m-0 p-0 mb-3">
                    <div class="col-8  attributeDiv" style="overflow-y: scroll; max-height:550px;">
                        <form id="attribute_mapping" class="row">
                            <?php foreach ($gmcAttributes as $key => $attribute) {
                                $sel_val = ""; ?>
                                <div class="col-6 mt-2">
                                    <span class="ps-3 fw-400 text-color fs-12">
                                        <?php echo esc_attr($attribute["field"]) . " " . (isset($attribute["required"]) === TRUE && esc_attr($attribute["required"]) === '1' ? '<span class="text-color fs-6"> *</span>' : ""); ?>
                                        <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip"
                                            data-bs-placement="right"
                                            title="<?php echo (isset($attribute['desc']) === TRUE ? esc_attr($attribute['desc']) : ''); ?>">
                                            info
                                        </span>
                                    </span>
                                </div>
                                <div class="col-6 mt-2">
                                    <?php
                                    $ee_select_option = $tvc_admin_helper->add_additional_option_in_tvc_select($wooCommerceAttributes, $attribute["field"]);
                                    $require = (isset($attribute['required']) === TRUE && $attribute['required'] !== "") ? TRUE : FALSE;
                                    $sel_val_def = (isset($attribute['wAttribute'])) === TRUE ? $attribute['wAttribute'] : "";
                                    if ($attribute["field"] === 'link') {
                                        "product link";
                                    } else if ($attribute["field"] === 'shipping') {
                                        $sel_val = (isset($ee_mapped_attrs[$attribute["field"]]) === TRUE) ? $ee_mapped_attrs[$attribute["field"]] : $sel_val_def;
                                        $tvc_admin_helper->tvc_text($attribute["field"], 'number', '', esc_html__('Add shipping flat rate', 'product-feed-manager-for-woocommerce'), $sel_val, $require);
                                    } else if ($attribute["field"] === 'tax') {
                                        $sel_val = (isset($ee_mapped_attrs[$attribute["field"]]) === TRUE) ? esc_attr($ee_mapped_attrs[$attribute["field"]]) : esc_attr($sel_val_def);
                                        $tvc_admin_helper->tvc_text($attribute["field"], 'number', '', 'Add TAX flat (%)', $sel_val, $require);
                                    } else if ($attribute["field"] === 'content_language') {
                                        $tvc_admin_helper->tvc_language_select($attribute["field"], 'content_language', esc_html__('Please Select Attribute', 'product-feed-manager-for-woocommerce'), 'en', $require);
                                    } else if ($attribute["field"] === 'target_country') {
                                        $tvc_admin_helper->tvc_countries_select($attribute["field"], 'target_country', esc_html__('Please Select Attribute', 'product-feed-manager-for-woocommerce'), $require);
                                    } else {
                                        if (isset($attribute['fixed_options']) === TRUE && $attribute['fixed_options'] !== "") {
                                            $ee_select_option_t = explode(",", $attribute['fixed_options']);
                                            $ee_select_option = [];
                                            foreach ($ee_select_option_t as $o_val) {
                                                $ee_select_option[]['field'] = esc_attr($o_val);
                                            }

                                            $sel_val = $sel_val_def;
                                            $tvc_admin_helper->tvc_select($attribute["field"], $attribute["field"], esc_html__('Please Select Attribute', 'product-feed-manager-for-woocommerce'), $sel_val, $require, $ee_select_option);
                                        } else {
                                            $sel_val = (isset($ee_mapped_attrs[$attribute["field"]]) === TRUE) ? $ee_mapped_attrs[$attribute["field"]] : $sel_val_def;
                                            $tvc_admin_helper->tvc_select($attribute["field"], $attribute["field"], esc_html__('Please Select Attribute', 'product-feed-manager-for-woocommerce'), $sel_val, $require, $ee_select_option);
                                        }
                                    }// end attribute if.
                                    ?>
                                </div>

                            <?php }// end gmcAttributes foreach.
                            ?>
                            <div class="col-12">
                                <button type="button" id="attr_mapping_save"
                                    class="btn btn-soft-primary float-end mt-2 ps-4 pe-4">Save</button>
                            </div>
                        </form>
                    </div>

                    <div class="col-4 ">
                        <fieldset class="border p-3 mt-2" style="border-radius:8px;">
                            <legend class="float-none w-auto px-3 fs-7">
                                <?php esc_html_e("Benefits Of Category Mapping", "enhanced-e-commerce-for-woocommerce-store") ?>
                            </legend>
                            <div class="control-group fw-400 text-color">
                                <p>
                                    <?php esc_html_e("Benefits of product attributes and category mapping for a product feed in Google Merchant Center:", "enhanced-e-commerce-for-woocommerce-store") ?>
                                    <br />
                                    <?php esc_html_e("1. Accurate and Structured Data: Mapping attributes and categories ensures organized and consistent product data, leading to better categorization and relevance.", "enhanced-e-commerce-for-woocommerce-store") ?>
                                    <br />
                                    <?php esc_html_e("2. Improved Visibility: Mapping helps your products appear in relevant search results, increasing visibility and driving targeted traffic.", "enhanced-e-commerce-for-woocommerce-store") ?>
                                    <br />
                                    <?php esc_html_e("3. Enhanced User Experience: Clear and detailed product information improves the overall shopping experience for users.", "enhanced-e-commerce-for-woocommerce-store") ?>
                                    <br />
                                    <?php esc_html_e("4. Better Targeting and Ad Performance: Accurate mapping enables precise targeting, resulting in improved ad performance and higher conversion rates.", "enhanced-e-commerce-for-woocommerce-store") ?>
                                    <br />
                                    <?php esc_html_e("5. Simplified Updates and Maintenance: Mapping facilitates easier updates and maintenance of your product feed.", "enhanced-e-commerce-for-woocommerce-store") ?>
                                    <br />
                                    <?php esc_html_e("5. Reduced Errors and Disapprovals: Proper mapping minimizes the risk of errors or disapprovals in product listings, ensuring compliance with Google's guidelines.", "enhanced-e-commerce-for-woocommerce-store") ?>
                                </p>
                            </div>
                        </fieldset>
                        <!-- <fieldset class="border p-3 mt-2" style="border-radius:8px;">
                            <legend class="float-none w-auto px-3 fs-7"><?php // esc_html_e("Helpful Videos", "enhanced-e-commerce-for-woocommerce-store") ?></legend>
                            <div class="col-12 row p-0 ms-0">
                                <div class="col-6" style="height: 100px">
                                    <div class="col-12 bg-warning rounded-3 p-2 h-100 align-middle">
                                        <div class="text-white text-center"><?php // esc_html_e("Benefits of", "enhanced-e-commerce-for-woocommerce-store") ?> </div>
                                        <div class="text-dark text-center"><?php // esc_html_e("Category Mapping", "enhanced-e-commerce-for-woocommerce-store") ?></div>
                                    </div>
                                </div>

                                <div class="col-6" style="height: 100px">
                                    <div class="col-12 bg-dark rounded-3 p-2 h-100 align-middle">
                                        <div class="text-white text-center"><?php // esc_html_e("Benefits of", "enhanced-e-commerce-for-woocommerce-store") ?> </div>
                                        <div class="text-warning text-center"><?php // esc_html_e("Attribute Mapping", "enhanced-e-commerce-for-woocommerce-store") ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 row ms-0">
                                <div class="col-6 text-color font-weight-400">
                                <?php // esc_html_e("Benefits of Category mapping", "enhanced-e-commerce-for-woocommerce-store") ?>
                                </div>
                                <div class="col-6 text-color font-weight-400">
                                <?php // esc_html_e("Benefits of Attribute mapping", "enhanced-e-commerce-for-woocommerce-store") ?>
                                </div>
                            </div>
                        </fieldset> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Success Save Modal -->
<div class="modal fade font-style" id="conv_save_success_modal" data-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">

            </div>
            <div class="modal-body text-center p-0">
                <img style="width:184px;"
                    src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/update_success_logo.png'); ?>">
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

<!-- Error Save Modal -->
<div class="modal fade font-style" id="conv_save_error_modal" data-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">

            </div>
            <div class="modal-body text-center p-0">
                <img style="width:184px;"
                    src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/error_logo.png'); ?>">
                <h3 class="fw-normal pt-3">Error</h3>
                <span id="conv_save_error_txt" class="mb-1 lh-lg"></span>
            </div>
            <div class="modal-footer border-0 pb-4 mb-1">
                <button class="btn conv-yellow-bg m-auto text-white" data-bs-dismiss="modal">Try Again</button>
            </div>
        </div>
    </div>
</div>
<!-- Error Save Modal End -->

<!-- CTA popup Start -->
<div class="modal fade" id="conv_save_success_modal_cta" data-bs-backdrop="static" data-bs-keyboard="false"
    tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header connection-header border-0 pb-0">
                <div class="connection-box">
                    <div class="items">
                        <img style="width:35px;"
                            src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/popup_woocommerce _logo.png'); ?>">
                        <span>Woo Commerce</span>
                    </div>
                    <div class="items">
                        <span class="material-symbols-outlined text-primary">
                            arrow_forward
                        </span>
                    </div>
                    <div class="items">
                        <img style="width:35px;"
                            src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/popup_mapping_logo.png'); ?>">
                        <span>Conversios Product Attributes</span>
                    </div>
                    <div class="items">
                        <span class="material-symbols-outlined text-primary">
                            arrow_forward
                        </span>
                    </div>
                    <div class="items">
                        <img style="width:35px;"
                            src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/popup_gmc_logo.png'); ?>">
                        <span>Google Merchant Center</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4">
                <div class="connected-content">
                    <h4>Successfully Connected</h4>                    
                    <p class="my-3">
                        <?php esc_html_e("Congratulations on successfully mapping your product categories and attributes! By
                        ensuring accurate classification and detailed product information, you've enhanced the
                        discoverability and relevance of your products, providing a better shopping experience for
                        customers.", "enhanced-e-commerce-for-woocommerce-store") ?>
                    </p>
                </div>
                <div>
                    <div class="attributemapping-box">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-12">
                                <div class="attribute-box mb-3">
                                    <div class="attribute-icon">
                                        <img style="width:35px;"
                                            src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/Manage_feed.png'); ?>">
                                    </div>
                                    <div class="attribute-content para">
                                        <h3>
                                            <?php esc_html_e("Manage Feeds", "enhanced-e-commerce-for-woocommerce-store") ?>
                                        </h3>
                                        <p>
                                            <?php esc_html_e("A feed management tool offers benefits such as centralized product updates,
                                            optimized product listings, and improved data quality, ultimately enhancing
                                            the efficiency and effectiveness of your product feed management process.", "enhanced-e-commerce-for-woocommerce-store") ?>

                                        </p>
                                        <div class="attribute-btn">
                                            <a href="<?php echo esc_url_raw('admin.php?page=conversios-google-shopping-feed&tab=feed_list'); ?>"
                                                class="btn btn-dark common-btn">Go To Feed Management</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- CTA popup End -->

<script>
    var cat_json = <?php echo $str ?>;
    jQuery(document).ready(function () {
        /****************************Select2 initialization start ***********************************************************************/
        jQuery('.select2').select2();
        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        });
        /****************************Select2 initialization end ***********************************************************************/
        /****************************Tooltip initialization start ***********************************************************************/
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
        /****************************Tooltip initialization end ***********************************************************************/
        /********************************Show tab on click start*************************************************************************/
        jQuery(document).on("click", ".change_prodct_feed_cat", function () {
            jQuery(this).hide();
            var feed_select_cat_id = jQuery(this).attr("data-id");
            var woo_cat_id = jQuery(this).attr("data-cat-id");
            jQuery("#category-" + woo_cat_id).val("0");
            jQuery("#category-name-" + woo_cat_id).val("");
            jQuery("#label-" + feed_select_cat_id).hide();
            jQuery("#" + feed_select_cat_id).css('width', '100%');
            jQuery("#" + feed_select_cat_id).addClass('select2');
            jQuery("#" + feed_select_cat_id).slideDown();
            jQuery('.select2').select2();
        });
        /********************************Show tab on click end*************************************************************************/
        /********************Ajax call Save attribute mapping in database start**************************************************************************/
        jQuery(document).on("click", "#attr_mapping_save", function () {
            let ee_data = jQuery("#attribute_mapping").find("input[value!=''], select:not(:empty), input[type='number']").serialize();
            var data = {
                action: "save_attribute_mapping",
                ee_data: ee_data,
                auto_product_sync_setting: "<?php echo esc_html_e(wp_create_nonce('auto_product_sync_setting-nonce')); ?>"
            };
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: tvc_ajax_url,
                data: data,
                beforeSend: function () {
                    conv_change_loadingbar("show");
                },
                success: function (response) {
                    conv_change_loadingbar("hide")
                    if (response.error === false) {
                        jQuery("#conv_save_success_modal_cta").modal("show");
                    } else {
                        jQuery("#conv_save_error_txt").html(response.message);
                        jQuery("#conv_save_error_modal").modal("show");
                    }

                }
            });
        });
        /********************Ajax call Save attribute mapping in database end**************************************************************************/
        /********************Ajax call Save category mapping in database start**************************************************************************/
        jQuery(document).on("click", "#cat_mapping_save", function () {
            let ee_data = jQuery("#category_mapping").find("input[value!=''], select:not(:empty), input[type='number']").serialize();
            var data = {
                action: "save_category_mapping",
                ee_data: ee_data,
                auto_product_sync_setting: "<?php echo esc_html_e(wp_create_nonce('auto_product_sync_setting-nonce')); ?>"
            };
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: tvc_ajax_url,
                data: data,
                beforeSend: function () {
                    conv_change_loadingbar("show");
                },
                success: function (response) {
                    conv_change_loadingbar("hide")
                    if (response.error === false) {
                        jQuery("#conv_save_success_modal_cta").modal("show");
                    } else {
                        jQuery("#conv_save_error_txt").html(response.message);
                        jQuery("#conv_save_error_modal").modal("show");
                    }
                }
            });
        });
        /********************Ajax call Save category mapping in database end**************************************************************************/
        /*****************************Add URL according to selected tab start **************************************************************/
        jQuery(document).on("click", "#pills-profile-tab", function (e) {
            if (history.pushState) {
                var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?page=conversios-google-shopping-feed&tab=product_mapping&product_tab=attribute';
                window.history.pushState({ path: newurl }, '', newurl);
            }
        })
        jQuery(document).on("click", "#pills-home-tab", function (e) {
            if (history.pushState) {
                var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?page=conversios-google-shopping-feed&tab=product_mapping&product_tab=category';
                window.history.pushState({ path: newurl }, '', newurl);
            }
        })
        /*****************************URL according to selected tab end **************************************************************/     
        /****************************** Numeric value validation Start ************************************************************************************/
        jQuery(document).on('keydown', 'input[name="shipping"]', function (event) {
            if (event.shiftKey == true) {
                event.preventDefault();
            }
            if ((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105) || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 || event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190) {

            } else {
                event.preventDefault();
            }

            if ($(this).val().indexOf('.') !== -1 && event.keyCode == 190)
                event.preventDefault();
        })
        jQuery(document).on('keydown', 'input[name="tax"]', function () {
            if (event.shiftKey == true) {
                event.preventDefault();
            }
            if ((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105) || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 || event.keyCode == 39 || event.keyCode == 46 || event.keyCode == 190) {

            } else {
                event.preventDefault();
            }

            if ($(this).val().indexOf('.') !== -1 && event.keyCode == 190)
                event.preventDefault();
        })
        /****************************** Numeric value validation End ************************************************************************************/
    })

    /***********************Append Conversios product category on edit start ******************************************************************/
    function selectSubCategory(thisObj) {
        selectId = thisObj.id;
        wooCategoryId = jQuery(thisObj).attr("catid");
        var selvalue = $('#' + selectId).find(":selected").val();
        var seltext = $('#' + selectId).find(":selected").text();
        jQuery("#category-" + wooCategoryId).val(selvalue);
        jQuery("#category-name-" + wooCategoryId).val(seltext);
        setTimeout(function () {
            jQuery(".select2").select2();
        }, 100);
    }
    /***********************Append Conversios product category on edit end ******************************************************************/
    /***********************loading bar start *************************************************************/
    function conv_change_loadingbar(state = 'show') {
        if (state === 'show') {
            jQuery("#loadingbar_blue").removeClass('d-none');
            $("#wpbody").css("pointer-events", "none");
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            jQuery("#loadingbar_blue").addClass('d-none');
            jQuery("#wpbody").css("pointer-events", "auto");
        }
    }
    /***********************loading bar end *************************************************************/
    /***********************On click Conversios product category select append all data start ******************************************************************/
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
            $('#' + selectId).select2();
            $('#' + selectId).select2('open');
        }
    })
    /***********************On click Conversios product category select append all data end ******************************************************************/
</script>