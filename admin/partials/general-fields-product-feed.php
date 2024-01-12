<?php
echo "<script>var return_url ='" . esc_url_raw($this->url) . "';</script>";
$tvc_admin_helper = new TVC_Admin_Helper();
$class = "";
$message_p = "";
$validate_pixels = array();
$google_detail = $tvc_admin_helper->get_ee_options_data();
$plan_id = 1;
$googleDetail = "";
if (isset($google_detail['setting'])) {
    $googleDetail = $google_detail['setting'];
    if (isset($googleDetail->plan_id) && !in_array($googleDetail->plan_id, array("1"))) {
        $plan_id = $googleDetail->plan_id;
    }
}

$data = unserialize(get_option('ee_options'));
if(isset($_GET['tab']) === FALSE 
    && ((isset($data['google_merchant_id']) && $data['google_merchant_id'] !== '') 
    || (isset($data['tiktok_setting']['tiktok_business_id']) && $data['tiktok_setting']['tiktok_business_id'] != ''))) {
        wp_safe_redirect("admin.php?page=conversios-google-shopping-feed&tab=feed_list");
        exit;
}

$channel_not_connected = array(
    "gmc_id" => (isset($data['google_merchant_id']) && $data['google_merchant_id'] != '') ? '' : 'conv-pixel-not-connected',
    "tiktok_bussiness_id" => (isset($data['tiktok_setting']['tiktok_business_id']) && $data['tiktok_setting']['tiktok_business_id'] != '') ? '' : 'tik-tok-not-connected',
);

?>
<style>
    body {
        max-height: 100%;
        background: #f0f0f1;
    }

    #tvc_popup_box {
        width: 500px;
        overflow: hidden;
        background: #eee;
        box-shadow: 0 0 10px black;
        border-radius: 10px;
        position: absolute;
        top: 30%;
        left: 40%;
        display: none;
    }
</style>
<!-- Main container -->
<div class="container-old conv-container conv-setting-container pt-4">

    <!-- Main row -->
    <div class="row justify-content-center" style="--bs-gutter-x: 0rem;">
        <!-- Main col8 center -->
        <div class="convfixedcontainermid col-md-8 col-xs-12 m-0 p-0">

            <div class="pt-4 pb-4 conv-heading-box">
                <h3>CHANNEL CONFIGURATION</h3>
                <span>You can configure your Ads channels for your product feeds</span>
            </div>
            <!-- Google Merchant card Start -->
            <div class="convcard d-flex flex-row p-2 mt-0 rounded-3 shadow-sm">
                <div class="convcard-left conv-pixel-logo">
                    <div class="convcard-logo text-center p-2 pe-3 border-end">
                        <img
                            src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_gmc_logo.png'); ?>" />
                    </div>
                </div>
                <div class="convcard-center p-2 ps-3 col-10">
                    <div class="convcard-title">
                        <h3>Google Merchant Center
                            <span
                                class="badge rounded-pill conv-badge <?php echo !empty($channel_not_connected['gmc_id']) ? "conv-badge-yellow" : "conv-badge-green"; ?>">
                                <?php echo !empty($channel_not_connected['gmc_id']) ? "Not Connected" : "Connected"; ?>
                            </span>
                        </h3>
                        <?php if (isset($data['google_merchant_id']) && $data['google_merchant_id'] != '') { ?>
                            <span>
                                Google Merchant Center Account -
                                <?php echo $data['google_merchant_id'] ?>
                            </span>
                        <?php } ?>

                        <hr>
                        <div class="d-flex">
                            <span>
                                How to connect your Google Merchant Center Account
                                <a class="conv-link-blue conv-watch-video"
                                    href="https://www.youtube.com/watch?v=Ku8iW02Os-w"
                                    target="_blank">
                                    Watch here
                                    <span class="material-symbols-outlined align-middle">play_circle_outline</span>
                                </a>
                            </span>
                        </div>

                        <div class="d-flex mt-3">
                            <span>
                                Benefits of integrating Google Merchant Center Account
                                <a class="conv-link-blue conv-watch-video"
                                    href="https://www.conversios.io/docs/benefits-of-product-sync-to-google-merchant-center/?utm_source=gmc_inapp&utm_medium=resource_center_list&utm_campaign=resource_center"
                                    target="_blank">
                                    Click here
                                    <span class="material-symbols-outlined align-middle">open_in_new_outline</span>
                                </a>
                            </span>
                        </div>

                    </div>
                </div>
                <div class="convcard-right ms-auto">
                    <a href="<?php echo esc_url_raw('admin.php?page=conversios-google-shopping-feed&subpage="gmcsettings"'); ?>"
                        class="h-100 rounded-end d-flex justify-content-center convcard-right-arrow link-dark">
                        <span class="material-symbols-outlined align-self-center">chevron_right</span>
                    </a>
                </div>
            </div>
            
            <!-- TikTok Business Account Start -->            
            <div class="convcard d-flex flex-row p-2 mt-4 rounded-3 shadow-sm">
                <div class="convcard-left conv-pixel-logo">
                    <div class="convcard-logo text-center p-2 pe-3 border-end">
                        <img
                            src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_tiktok_logo.png'); ?>" />
                    </div>
                </div>
                <div class="convcard-center p-2 ps-3 col-10">
                    <div class="convcard-title">
                        <h3>TikTok Business Account
                            <span
                                class="badge rounded-pill conv-badge <?php echo !empty($channel_not_connected['tiktok_bussiness_id']) ? "conv-badge-yellow" : "conv-badge-green"; ?>">
                                <?php echo !empty($channel_not_connected['tiktok_bussiness_id']) ? "Not Connected" : "Connected"; ?>
                            </span>
                        </h3>
                        <?php if (isset($data['tiktok_setting']['tiktok_business_id'] ) && $data['tiktok_setting']['tiktok_business_id']  != '') { ?>
                            <span>
                                TikTok Business Account -
                                <?php echo $data['tiktok_setting']['tiktok_business_id'] ?>
                            </span>
                        <?php } ?>

                        <hr>
                        <!-- <div class="d-flex">
                            <span>
                                How to connect your Tiktok business account
                                <a class="conv-link-blue conv-watch-video" href="#">
                                    Watch here
                                    <span class="material-symbols-outlined align-middle">play_circle_outline</span>
                                </a>
                            </span>
                        </div> -->

                        <div class="d-flex mt-3">
                            <span>
                            <?php esc_html_e("Benefits and how to integrate Tiktok Business Account","enhanced-e-commerce-for-woocommerce-store") ?>
                                <a class="conv-link-blue conv-watch-video" href="https://www.conversios.io/docs/how-to-create-product-feed-to-your-tik-tok-catalog/" target="_blank">
                                    Click here
                                    <span class="material-symbols-outlined align-middle">open_in_new_outline</span>
                                </a>
                            </span>
                        </div>

                    </div>
                </div>
                <div class="convcard-right ms-auto">
                    <a href="<?php echo esc_url_raw('admin.php?page=conversios-google-shopping-feed&subpage="tiktokBusinessSettings"'); ?>"
                        class="h-100 rounded-end d-flex justify-content-center convcard-right-arrow link-dark">
                        <span class="material-symbols-outlined align-self-center">chevron_right</span>
                    </a>
                </div>
            </div>
            <!-- TikTok Business Account End -->
            <?php if ($plan_id == 1) { ?>
                <!-- Blue upgrade to pro -->
                <div class="convcard conv-green-grad-bg rounded-3 d-flex flex-row p-3 mt-4 shadow-sm">
                    <div class="convcard-blue-left align-self-center p-2 bd-highlight">
                        <h3 class="text-light mb-3">
                            <?php esc_html_e("Upgrade your Plan to get pro benefits", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </h3>
                        <span class="text-light">
                            <ul class="conv-green-banner-list ps-4">
                                <li>
                                    <?php esc_html_e("Take control, boost speed. Automate your Google Tag Manager.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </li>
                                <li>
                                    <?php esc_html_e("Maximize campaigns with Google Ads Conversion integration.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </li>
                                <li>
                                    <?php esc_html_e("Quick and Easy install of Facebook Conversions API to drive sales via Facebook Ads.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </li>
                                <li>
                                    <?php esc_html_e("Sync unlimited product feeds with Content API and more.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </li>
                                <li>
                                    <?php esc_html_e("Make data-driven decisions. Scale your ecommerce business with our reporting dashboard.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </li>
                                <li>
                                    <?php esc_html_e("Free website audit, dedicated success manager, priority slack support.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </li>
                            </ul>
                        </span>
                        <span class="d-flex">
                            <a style="padding:8px 24px 8px 24px;" class="btn conv-yellow-bg mt-4 btn-lg"
                                href="<?php echo $tvc_admin_helper->get_conv_pro_link_adv("banner", "channel_config", "", "linkonly") ?>"
                                target="_blank">Upgrade Now</a>
                        </span>
                    </div>
                    <div class="convcard-blue-right align-self-center p-2 bd-highlight mx-auto">
                        <img
                            src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/freetopaid_banner_img.png'); ?>" />
                    </div>
                </div>
                <!-- Blue upgrade to pro End -->
            <?php } ?>

        </div>
        <!-- Main col8 center -->
    </div>
    <!-- Main row -->
</div>
<!-- Main container End -->