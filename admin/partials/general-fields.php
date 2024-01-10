<?php
echo "<script>var return_url ='" . esc_url_raw($this->url) . "';</script>";
$TVC_Admin_Helper = new TVC_Admin_Helper();
$this->customApiObj = new CustomApi();
$class = "";
$message_p = "";
$validate_pixels = array();
$google_detail = $TVC_Admin_Helper->get_ee_options_data();
$plan_id = 46;
$googleDetail = "";
if (isset($google_detail['setting'])) {
  $googleDetail = $google_detail['setting'];
  if (isset($googleDetail->plan_id) && !in_array($googleDetail->plan_id, array("46"))) {
    $plan_id = $googleDetail->plan_id;
  }
}

$data = unserialize(get_option('ee_options'));
$conv_selected_events = unserialize(get_option('conv_selected_events'));
$this->current_customer_id = $TVC_Admin_Helper->get_currentCustomerId();
$subscription_id = $TVC_Admin_Helper->get_subscriptionId();

$TVC_Admin_Helper->add_spinner_html();
$is_show_tracking_method_options =  true; //$TVC_Admin_Helper->is_show_tracking_method_options($subscription_id);
?>


<!-- Main container -->
<div class="container-old conv-container conv-setting-container pt-4">

  <!-- Main row -->
  <div class="row justify-content-center" style="--bs-gutter-x: 0rem;">
    <!-- Main col8 center -->
    <div class="convfixedcontainermid col-md-8 col-xs-12 m-0 p-0">

      <div class="pt-4 pb-4 conv-heading-box">
        <div class="d-flex d-flex justify-content-between">
          <h3>
            <?php esc_html_e("IMPLEMENTATION METHOD", "enhanced-e-commerce-for-woocommerce-store"); ?>
          </h3>
        </div>
        <span>
          <?php esc_html_e("Connect your Google Tag Manager account to start configuring Google Analytics and/or pixel tracking.", "enhanced-e-commerce-for-woocommerce-store"); ?>
        </span>
      </div>

      <!-- GTM Card -->
      <?php
      $tracking_method = (isset($data['tracking_method']) && $data['tracking_method'] != "") ? $data['tracking_method'] : "";
      $want_to_use_your_gtm = (isset($data['want_to_use_your_gtm']) && $data['want_to_use_your_gtm'] != "") ? $data['want_to_use_your_gtm'] : "0";
      $use_your_gtm_id = "";
      if (isset($tracking_method) && $tracking_method == "gtm") {
        $use_your_gtm_id =  ($data['tracking_method'] == 'gtm' && $want_to_use_your_gtm == 1) ? "Your own GTM container - " . $data['use_your_gtm_id'] : (($data['tracking_method'] == 'gtm') ? "Conversios container - GTM-K7X94DG" : esc_attr("Your own GTM container - " . $data['use_your_gtm_id']));
      }
      $is_sst_gtm_connected = "";
      if ((isset($data['sst_web_container']) && $data['sst_web_container'] != "") && (isset($data['sst_transport_url']) && $data['sst_transport_url'] != "")) {
        $is_sst_gtm_connected = "yes";
      }
      ?>

      <?php if (isset($tracking_method) && $tracking_method == 'gtag') { ?>
        <div class="alert d-flex align-items-cente p-0" role="alert">
          <div class="text-light conv-error-bg rounded-start d-flex">
            <span class="p-2 material-symbols-outlined align-self-center">info</span>
          </div>

          <div class="p-2 w-100 rounded-end border border-start-0 shadow-sm conv-notification-alert lh-lg bg-white">
            <h6 class="fs-6 lh-1 text-dark fw-bold border-bottom w-100 py-2">
              <?php esc_html_e("Attention!", "enhanced-e-commerce-for-woocommerce-store"); ?>
            </h6>

            <span class="fs-6 lh-1 text-dark">
              <?php esc_html_e("As you might be knowing, GA3 is seeing sunset from 1st July 2023, we are also removing gtag.js based implementation for the old app users soon. Hence, we recommend you to change your implementation method to Google Tag Manager from below to avoid data descrepancy in the future.", "enhanced-e-commerce-for-woocommerce-store"); ?>
          </div>
        </div>

      <?php } ?>

     



      <!-- GTM Server Side Start -->
      <div class="convo_sst convcard d-flex flex-row  mt-3">
        <div class="convcard-left conv-pixel-logo">
          <div class="convcard-logo text-center">
            <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_sstgtm_logo.svg'); ?>" />
          </div>
        </div>
        <div class="convcard-center ">
          <div class="convcard-title">
            <h3>
              <?php esc_html_e("Server Side Tagging Via GTM", "enhanced-e-commerce-for-woocommerce-store"); ?>
            </h3>
            <p>
              <?php esc_html_e("To Know The Benefits and How To User Server Side Tagging", "enhanced-e-commerce-for-woocommerce-store"); ?>
              <span style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#convSsttoProModal"><?php esc_html_e("Click Here", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
            </p>
            <p class="white-text mt-1">
              <?php
              $sst_web_container = (isset($data['sst_web_container']) && $data['sst_web_container'] != "") ? $data['sst_web_container'] : "";
              $sst_transport_url = (isset($data['sst_transport_url']) && $data['sst_transport_url'] != "") ? $data['sst_transport_url'] : "";
              if (isset($tracking_method) && $tracking_method == "gtm" && $is_sst_gtm_connected == "yes") {
                echo "Your Server GTM Container - " . $sst_web_container;
              }
              ?>
            </p>
          </div>
        </div>

        <div class="convcard-right ms-auto">
          <a href="<?php echo esc_url_raw('admin.php?page=conversios-google-analytics&subpage="gtmsstsettings"'); ?>" class="h-100 rounded-end d-flex justify-content-center convcard-right-arrow link-dark">
            <span class="material-symbols-outlined align-self-center">chevron_right</span>
          </a>
        </div>
      </div>
      <!-- GTM Server Side End -->



      <!-- Blue upgrade to pro -->
      <div class="convcard conv-green-grad-bg rounded-3 d-flex flex-row p-3 mt-4 shadow-sm">
        <div class="convcard-blue-left align-self-center p-2 bd-highlight">
          <h3 class="text-light mb-3">
            <?php esc_html_e("Upgrade your Plan to get pro benefits", "enhanced-e-commerce-for-woocommerce-store"); ?>
          </h3>
          <span class="text-light">
            <ul class="conv-green-banner-list ps-4">
              <li><?php esc_html_e("Take control, boost speed. Automate your Google Tag Manager.", "enhanced-e-commerce-for-woocommerce-store"); ?></li>
              <li><?php esc_html_e("Maximize campaigns with Google Ads Conversion integration.", "enhanced-e-commerce-for-woocommerce-store"); ?></li>
              <li><?php esc_html_e("Quick and Easy install of Facebook Conversions API to drive sales via Facebook Ads.", "enhanced-e-commerce-for-woocommerce-store"); ?></li>
              <li><?php esc_html_e("Sync unlimited product feeds with Content API and more.", "enhanced-e-commerce-for-woocommerce-store"); ?></li>
              <li><?php esc_html_e("Make data-driven decisions. Scale your ecommerce business with our reporting dashboard.", "enhanced-e-commerce-for-woocommerce-store"); ?></li>
              <li><?php esc_html_e("Free website audit, dedicated success manager, priority slack support.", "enhanced-e-commerce-for-woocommerce-store"); ?></li>
            </ul>
          </span>
          <span class="d-flex">
            <a style="padding:8px 24px 8px 24px;" class="btn conv-yellow-bg mt-4 btn-lg" href="<?php echo $TVC_Admin_Helper->get_conv_pro_link_adv("banner", "pixel_list", "", "linkonly") ?>" target="_blank">Upgrade Now</a>
          </span>
        </div>
        <div class="convcard-blue-right align-self-center p-2 bd-highlight mx-auto">
          <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/freetopaid_banner_img.png'); ?>" />
        </div>
      </div>
      <!-- Blue upgrade to pro End -->


      <div class="pt-4 conv-heading-box">
        <h3>
          <?php esc_html_e("INTEGRATIONS", "enhanced-e-commerce-for-woocommerce-store"); ?>
        </h3>
        <span>
          <?php esc_html_e("Once you’ve finished setting up your Google Tag Manager (GTM), go ahead with pixels & other integrations.", "enhanced-e-commerce-for-woocommerce-store"); ?>
        </span>
      </div>

      <!-- All pixel list -->
      <?php
      $conv_gtm_not_connected = ($is_sst_gtm_connected != 'yes') ? "conv-gtm-not-connected" : "conv-gtm-connected";
      $pixel_not_connected = array(
        "ga_id" => (isset($data['ga_id']) && $data['ga_id'] != '') ? '' : 'conv-pixel-not-connected',
        "gm_id" => (isset($data['gm_id']) && $data['gm_id'] != '') ? '' : 'conv-pixel-not-connected',
        "google_ads_id" => (isset($data['google_ads_id']) && $data['google_ads_id'] != '') ? '' : 'conv-pixel-not-connected',
        "fb_pixel_id" => (isset($data['fb_pixel_id']) && $data['fb_pixel_id'] != '') ? '' : 'conv-pixel-not-connected',
        "microsoft_ads_pixel_id" => (isset($data['microsoft_ads_pixel_id']) && $data['microsoft_ads_pixel_id'] != '') ? '' : 'conv-pixel-not-connected',
        "twitter_ads_pixel_id" => (isset($data['twitter_ads_pixel_id']) && $data['twitter_ads_pixel_id'] != '') ? '' : 'conv-pixel-not-connected',
        "pinterest_ads_pixel_id" => (isset($data['pinterest_ads_pixel_id']) && $data['pinterest_ads_pixel_id'] != '') ? '' : 'conv-pixel-not-connected',
        "snapchat_ads_pixel_id" => (isset($data['snapchat_ads_pixel_id']) && $data['snapchat_ads_pixel_id'] != '') ? '' : 'conv-pixel-not-connected',
        "tiKtok_ads_pixel_id" => (isset($data['tiKtok_ads_pixel_id']) && $data['tiKtok_ads_pixel_id'] != '') ? '' : 'conv-pixel-not-connected',
      );


      $pixel_video_link = array(
        "ga_id" => "https://www.conversios.io/docs/ecommerce-events-that-will-be-automated-using-conversios/?utm_source=galisting_inapp&utm_medium=resource_center_list&utm_campaign=resource_center",
        "gm_id" => "https://www.conversios.io/docs/ecommerce-events-that-will-be-automated-using-conversios/?utm_source=galisting_inapp&utm_medium=resource_center_list&utm_campaign=resource_center",
        "google_ads_id" => "https://youtu.be/Vr7vEeMIf7c",
        "fb_pixel_id" => "https://youtu.be/8nIyvQjeEkY",
        "microsoft_ads_pixel_id" => "https://youtu.be/BeP1Tp0I92o",
        "twitter_ads_pixel_id" => "",
        "pinterest_ads_pixel_id" => "https://youtu.be/Z0rcP1ItJDk",
        "snapchat_ads_pixel_id" => "https://youtu.be/uLQqAMQhFUo",
        "tiKtok_ads_pixel_id" => "https://www.conversios.io/docs/how-to-set-up-tiktok-pixel-using-conversios-plugin/?utm_source=Tiktoklisting_inapp&utm_medium=resource_center_list&utm_campaign=resource_center",
      );
      ?>

      <div id="conv_pixel_list_box" class="shadow-sm">

        <!-- Google analytics  -->
        <div class="convcard conv-pixel-list-item d-flex flex-row p-2 mt-4 rounded-top <?php echo $conv_gtm_not_connected; ?>">

          <div class="p-2 pe-3 conv-pixel-logo border-end d-flex">
            <img class="align-self-center" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_ganalytics_logo.png'); ?>" />
          </div>

          <div class="p-1 ps-3 align-self-center">
            <span class="fw-bold m-0">
              <?php esc_html_e("Google Analytics 4", "enhanced-e-commerce-for-woocommerce-store"); ?>
              <a target="_blank" class="conv-link-blue conv-watch-video ps-2 fw-normal invisible" href="<?php echo esc_url($pixel_video_link['gm_id']); ?>">
                <span class="material-symbols-outlined align-text-bottom">play_circle_outline</span>
                <?php esc_html_e("Watch here", "enhanced-e-commerce-for-woocommerce-store"); ?>
              </a>
            </span>
            <?php if ((empty($pixel_not_connected['ga_id']) || empty($pixel_not_connected['gm_id'])) && $conv_gtm_not_connected == "conv-gtm-connected") { ?>
              <div class="d-flex pt-2">

                <?php if (isset($data['gm_id']) && $data['gm_id'] != '') { ?>
                  <span class=""> <?php echo (isset($data['gm_id']) && $data['gm_id'] != '') ? esc_attr("GA4: " . $data['gm_id']) : ''; ?> </span>
                <?php } ?>
              </div>
            <?php } ?>
          </div>

          <div class="ms-auto d-flex">
            <?php if ((empty($pixel_not_connected['ga_id']) || empty($pixel_not_connected['gm_id'])) && $conv_gtm_not_connected == "conv-gtm-connected") { ?>
              <span class="badge rounded-pill conv-badge conv-badge-green m-0 me-3 align-self-center">Connected</span>
            <?php } ?>
            <a href="<?php echo esc_url_raw('admin.php?page=conversios-google-analytics&subpage="gasettings"'); ?>" class="rounded-end convcard-right-arrow align-self-center link-dark">
              <span class="material-symbols-outlined p-2">chevron_right</span>
            </a>
          </div>

        </div>

        <!-- Google Ads -->
        <div class="convcard conv-pixel-list-item d-flex flex-row p-2 mt-0 border-top <?php echo $conv_gtm_not_connected; ?>">
          <div class="p-2 pe-3 conv-pixel-logo border-end d-flex">
            <img class="align-self-center" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_gads_logo.png'); ?>" />
          </div>

          <div class="p-1 ps-3 align-self-center">
            <span class="fw-bold m-0">
              <?php esc_html_e("Google Ads Conversion Tracking", "enhanced-e-commerce-for-woocommerce-store"); ?>
              <a target="_blank" class="conv-link-blue conv-watch-video ps-2 fw-normal invisible" href="<?php echo esc_url($pixel_video_link['google_ads_id']); ?>">
                <span class="material-symbols-outlined align-text-bottom">play_circle_outline</span>
                <?php esc_html_e("Watch here", "enhanced-e-commerce-for-woocommerce-store"); ?>
              </a>
            </span>
            <?php if (empty($pixel_not_connected['google_ads_id']) && $conv_gtm_not_connected == "conv-gtm-connected") { ?>
              <div class="d-flex pt-2">
                <span class="pe-2 m-0"> <?php echo (isset($data['google_ads_id']) && $data['google_ads_id'] != '') ? esc_attr($data['google_ads_id']) : ''; ?> </span>
              </div>
            <?php } ?>
          </div>

          <div class="ms-auto d-flex">
            <?php if (empty($pixel_not_connected['google_ads_id']) && $conv_gtm_not_connected == "conv-gtm-connected") { ?>
              <span class="badge rounded-pill conv-badge conv-badge-green m-0 me-3 align-self-center">Connected</span>
            <?php } ?>
            <a href="<?php echo esc_url_raw('admin.php?page=conversios-google-analytics&subpage="gadssettings"'); ?>" class="rounded-end convcard-right-arrow align-self-center link-dark">
              <span class="material-symbols-outlined p-2">chevron_right</span>
            </a>
          </div>
        </div>

        <!-- FB Pixel -->
        <div class="convcard conv-pixel-list-item d-flex flex-row p-2 mt-0 border-top <?php echo $conv_gtm_not_connected; ?>">
          <div class="p-2 pe-3 conv-pixel-logo border-end d-flex">
            <img class="align-self-center" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_meta_logo.png'); ?>" />
          </div>

          <div class="p-1 ps-3 align-self-center">
            <span class="fw-bold m-0">
              <?php esc_html_e("Facebook Pixel & Facebook Conversions API (Meta)", "enhanced-e-commerce-for-woocommerce-store"); ?>
              <a target="_blank" class="conv-link-blue conv-watch-video ps-2 fw-normal invisible" href="<?php echo esc_url($pixel_video_link['fb_pixel_id']); ?>">
                <span class="material-symbols-outlined align-text-bottom">play_circle_outline</span>
                <?php esc_html_e("Watch here", "enhanced-e-commerce-for-woocommerce-store"); ?>
              </a>
            </span>
            <?php if (empty($pixel_not_connected['fb_pixel_id']) && $conv_gtm_not_connected == "conv-gtm-connected") { ?>
              <div class="d-flex pt-2">
                <span class="pe-2 m-0"> <?php echo (isset($data['fb_pixel_id']) && $data['fb_pixel_id'] != '') ? esc_attr($data['fb_pixel_id']) : ''; ?> </span>
              </div>
            <?php } ?>
          </div>

          <div class="ms-auto d-flex">
            <?php if (empty($pixel_not_connected['fb_pixel_id']) && $conv_gtm_not_connected == "conv-gtm-connected") { ?>
              <span class="badge rounded-pill conv-badge conv-badge-green m-0 me-3 align-self-center">Connected</span>
            <?php } ?>
            <a href="<?php echo esc_url_raw('admin.php?page=conversios-google-analytics&subpage="fbsettings"'); ?>" class="rounded-end convcard-right-arrow align-self-center link-dark">
              <span class="material-symbols-outlined p-2">chevron_right</span>
            </a>
          </div>
        </div>



        <!-- Snapchat Pixel -->
        <div class="convcard conv-pixel-list-item d-flex flex-row p-2 mt-0 border-top <?php echo $conv_gtm_not_connected; ?>">
          <div class="p-2 pe-3 conv-pixel-logo border-end d-flex">
            <img class="align-self-center" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_snap_logo.png'); ?>" />
          </div>

          <div class="p-1 ps-3 align-self-center">
            <span class="fw-bold m-0">
              <?php esc_html_e("Snapchat Pixel & Conversion API", "enhanced-e-commerce-for-woocommerce-store"); ?>
              <a target="_blank" class="conv-link-blue conv-watch-video ps-2 fw-normal invisible" href="<?php echo esc_url($pixel_video_link['snapchat_ads_pixel_id']); ?>">
                <span class="material-symbols-outlined align-text-bottom">play_circle_outline</span>
                <?php esc_html_e("Watch here", "enhanced-e-commerce-for-woocommerce-store"); ?>
              </a>
            </span>
            <?php if (empty($pixel_not_connected['snapchat_ads_pixel_id']) && $conv_gtm_not_connected == "conv-gtm-connected") { ?>
              <div class="d-flex pt-2">
                <span class="pe-2 m-0"> <?php echo (isset($data['snapchat_ads_pixel_id']) && $data['snapchat_ads_pixel_id'] != '') ? esc_attr($data['snapchat_ads_pixel_id']) : ''; ?> </span>
              </div>
            <?php } ?>
          </div>

          <div class="ms-auto d-flex">
            <?php if (empty($pixel_not_connected['snapchat_ads_pixel_id']) && $conv_gtm_not_connected == "conv-gtm-connected") { ?>
              <span class="badge rounded-pill conv-badge conv-badge-green m-0 me-3 align-self-center">Connected</span>
            <?php } ?>
            <a href="<?php echo esc_url_raw('admin.php?page=conversios-google-analytics&subpage="snapchatsettings"'); ?>" class="rounded-end convcard-right-arrow align-self-center link-dark">
              <span class="material-symbols-outlined p-2">chevron_right</span>
            </a>
          </div>
        </div>

        <!-- Tiktok -->
        <div class="convcard conv-pixel-list-item d-flex flex-row p-2 mt-0 border-top <?php echo $conv_gtm_not_connected; ?>">
          <div class="p-2 pe-3 conv-pixel-logo border-end d-flex">
            <img class="align-self-center" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_tiktok_logo.png'); ?>" />
          </div>

          <div class="p-1 ps-3 align-self-center">
            <span class="fw-bold m-0">
              <?php esc_html_e("TikTok Pixel & Events API", "enhanced-e-commerce-for-woocommerce-store"); ?>
              <a target="_blank" class="conv-link-blue conv-watch-video ps-2 fw-normal invisible" href="<?php echo esc_url($pixel_video_link['tiKtok_ads_pixel_id']); ?>">
                <span class="material-symbols-outlined align-text-bottom">play_circle_outline</span>
                <?php esc_html_e("Watch here", "enhanced-e-commerce-for-woocommerce-store"); ?>
              </a>
            </span>
            <?php if (empty($pixel_not_connected['tiKtok_ads_pixel_id']) && $conv_gtm_not_connected == "conv-gtm-connected") { ?>
              <div class="d-flex pt-2">
                <span class="pe-2 m-0"> <?php echo (isset($data['tiKtok_ads_pixel_id']) && $data['tiKtok_ads_pixel_id'] != '') ? esc_attr($data['tiKtok_ads_pixel_id']) : ''; ?> </span>
              </div>
            <?php } ?>
          </div>

          <div class="ms-auto d-flex">
            <?php if (empty($pixel_not_connected['tiKtok_ads_pixel_id']) && $conv_gtm_not_connected == "conv-gtm-connected") { ?>
              <span class="badge rounded-pill conv-badge conv-badge-green m-0 me-3 align-self-center">Connected</span>
            <?php } ?>
            <a href="<?php echo esc_url_raw('admin.php?page=conversios-google-analytics&subpage="tiktoksettings"'); ?>" class="rounded-end convcard-right-arrow align-self-center link-dark">
              <span class="material-symbols-outlined p-2">chevron_right</span>
            </a>
          </div>
        </div>
      </div>
      <!-- All pixel list end -->

      <?php if (is_plugin_active_for_network('woocommerce/woocommerce.php') || in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) { ?>
        <div class="pt-4 conv-heading-box">
          <h3>
            <?php esc_html_e("ADVANCE OPTIONS", "enhanced-e-commerce-for-woocommerce-store"); ?>
          </h3>
          <span>
            <?php esc_html_e("This feature is for the woocommerce store which has changed standard woocommerce hooks or implemented custom woocommerce hooks.", "enhanced-e-commerce-for-woocommerce-store"); ?>
          </span>
        </div>

        <!-- Advanced option -->
        <div class="convcard conv-pixel-list-item rounded d-flex flex-row p-2 mt-1 <?php echo $conv_gtm_not_connected; ?>">
          <div class="p-2 pe-3 conv-pixel-logo border-end d-flex">
            <img class="align-self-center" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_event_track_custom.png'); ?>" />
          </div>

          <div class="p-1 ps-3 align-self-center">
            <span class="fw-bold">
              <?php esc_html_e("Event Tracking - Custom Integration", "enhanced-e-commerce-for-woocommerce-store"); ?>
              <a target="_blank" class="conv-link-blue conv-watch-video ps-2 fw-normal invisible" href="<?php echo esc_url_raw("https://" . TVC_AUTH_CONNECT_URL . "/help-center/event-tracking-custom-integration.pdf"); ?>">
                <span class="material-symbols-outlined align-text-bottom">article</span>
                <?php esc_html_e("Read Here", "enhanced-e-commerce-for-woocommerce-store"); ?>
              </a>
            </span>
          </div>

          <div class="ms-auto d-flex">
            <a href="<?php echo esc_url_raw('admin.php?page=conversios-google-analytics&subpage="customintgrationssettings"'); ?>" class="rounded-end convcard-right-arrow align-self-center link-dark">
              <span class="material-symbols-outlined p-2">chevron_right</span>
            </a>
          </div>

        </div>
        <!-- Advance option End -->
      <?php } ?>


    </div>
    <!-- Main col8 center -->
  </div>
  <!-- Main row -->
</div>
<!-- Main container End -->




<!-- Modal -->
<div class="modal fade upgradetosstmodal" id="convSsttoProModal" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog ">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="modal-content">

      <h2>Unlock The benefits of <br> <span>Server Side Tagging Via GTM</span> </h2>
      <div class="row">
        <div class="col-lg-6 col-md-12 col-12">
          <ul class="listing">
            <span>Benefits</span>
            <li>Adopt To First Party Cookies</li>
            <li>Improve Data Accuracy & Reduced Ad Blocker Impact</li>
            <li>Faster Page Speed</li>
            <li>Enhanced Data Privacy & Security</li>
          </ul>
        </div>
        <div class="col-lg-6 col-md-12 col-12">
          <ul class="listing">
            <span>Features</span>
            <li>Server Side Tagging Via GTM</li>
            <li>Powerful Google Cloud Servers</li>
            <li>Custom Loader & Custom Domain Mapping</li>
            <li>Server Side Tagging For Google Analytics 4 (GA4), Google Ads & Facebook CAPI</li>
            <li>Free Setup & Audit By Dedicated Customer Success Manager</li>
          </ul>
        </div>
        <div class="col-12">
          <div class="discount-btn">
            <a target="_blank" href="<?php echo esc_url_raw('https://www.conversios.io/server-side-tagging-gtm/?utm_source=pixelandanalytics&utm_medium=in_app&utm_campaign=sstpopup'); ?>" class="btn btn-dark common-btn">Get Early Bird Discount</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Upgrade to PRO modal End -->


<!-- Channel Limit Modal -->
<div class="modal fade upgradetosstmodal" id="conv_limitchannelmodal" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog ">
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    <div class="modal-content">

      <h2>Unlock The benefits of <br> <span>Server Side Tagging Via GTM</span> </h2>
      <div class="row">
        <div class="col-lg-6 col-md-12 col-12">
          <ul class="listing">
            <span>Benefits</span>
            <li>Adopt To First Party Cookies</li>
            <li>Improve Data Accuracy & Reduced Ad Blocker Impact</li>
            <li>Faster Page Speed</li>
            <li>Enhanced Data Privacy & Security</li>
          </ul>
        </div>
        <div class="col-lg-6 col-md-12 col-12">
          <ul class="listing">
            <span>Features</span>
            <li>Server Side Tagging Via GTM</li>
            <li>Powerful Google Cloud Servers</li>
            <li>Custom Loader & Custom Domain Mapping</li>
            <li>Server Side Tagging For Google Analytics 4 (GA4), Google Ads & Facebook CAPI</li>
            <li>Free Setup & Audit By Dedicated Customer Success Manager</li>
          </ul>
        </div>
        <div class="col-12">
          <div class="discount-btn">
            <a target="_blank" href="<?php echo esc_url_raw('https://www.conversios.io/server-side-tagging-gtm/?utm_source=pixelandanalytics&utm_medium=in_app&utm_campaign=sstpopup'); ?>" class="btn btn-dark common-btn">Get Early Bird Discount</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Channel Limit Modal END -->

<script>
  jQuery(function() {
    var connectedcount = jQuery("#conv_pixel_list_box .conv-gtm-connected.conv-pixel-list-item .conv-badge.conv-badge-green").length;
    if (connectedcount >= 2) {
      jQuery("#conv_pixel_list_box .conv-gtm-connected.conv-pixel-list-item").each(function(eell) {
        var innerbadge = jQuery(this).find('.conv-badge.conv-badge-green').length;
        if (innerbadge == 0) {
          jQuery(this).find(".convcard-right-arrow").removeAttr('href').addClass('conv_limitchannelmodal_btn');
        }
      });
    }

    jQuery(".conv_limitchannelmodal_btn").click(function() {
      jQuery("#conv_limitchannelmodal").modal('show');
    });
  });
</script>