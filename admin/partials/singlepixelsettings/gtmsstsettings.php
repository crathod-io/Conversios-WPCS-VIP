<?php
// $gcp_regions = array(
//   "asia-east1-a" => "asia-east1-a",
//   "asia-east1-b" => "asia-east1-b",
//   "asia-east1-c" => "asia-east1-c",
//   "asia-east2-a" => "asia-east2-a",
//   "asia-east2-b" => "asia-east2-b",
//   "asia-east2-c" => "asia-east2-c",
//   "asia-northeast1-a" => "asia-northeast1-a",
//   "asia-northeast1-b" => "asia-northeast1-b",
//   "asia-northeast1-c" => "asia-northeast1-c",
//   "asia-northeast2-a" => "asia-northeast2-a",
//   "asia-northeast2-b" => "asia-northeast2-b",
//   "asia-northeast2-c" => "asia-northeast2-c",
//   "asia-northeast3-a" => "asia-northeast3-a",
//   "asia-northeast3-b" => "asia-northeast3-b",
//   "asia-northeast3-c" => "asia-northeast3-c",
//   "asia-south1-a" => "asia-south1-a",
//   "asia-south1-b" => "asia-south1-b",
//   "asia-south1-c" => "asia-south1-c",
//   "asia-south2-a" => "asia-south2-a",
//   "asia-south2-b" => "asia-south2-b",
//   "asia-south2-c" => "asia-south2-c",
//   "asia-southeast1-a" => "asia-southeast1-a",
//   "asia-southeast1-b" => "asia-southeast1-b",
//   "asia-southeast1-c" => "asia-southeast1-c",
//   "asia-southeast2-a" => "asia-southeast2-a",
//   "asia-southeast2-b" => "asia-southeast2-b",
//   "asia-southeast2-c" => "asia-southeast2-c",
//   "australia-southeast1-a" => "australia-southeast1-a",
//   "australia-southeast1-b" => "australia-southeast1-b",
//   "australia-southeast1-c" => "australia-southeast1-c",
//   "australia-southeast2-a" => "australia-southeast2-a",
//   "australia-southeast2-b" => "australia-southeast2-b",
//   "australia-southeast2-c" => "australia-southeast2-c",
//   "europe-north1-a" => "europe-north1-a",
//   "europe-north1-b" => "europe-north1-b",
//   "europe-central2-a" => "europe-central2-a",
//   "europe-central2-b" => "europe-central2-b",
//   "europe-central2-c" => "europe-central2-c",
//   "europe-north1-c" => "europe-north1-c",
//   "europe-southwest1-a" => "europe-southwest1-a",
//   "europe-southwest1-b" => "europe-southwest1-b",
//   "europe-southwest1-c" => "europe-southwest1-c",
//   "europe-west1-b" => "europe-west1-b",
//   "europe-west1-c" => "europe-west1-c",
//   "europe-west1-d" => "europe-west1-d",
//   "europe-west12-a" => "europe-west12-a",
//   "europe-west12-b" => "europe-west12-b",
//   "europe-west12-c" => "europe-west12-c",
//   "europe-west2-a" => "europe-west2-a",
//   "europe-west2-b" => "europe-west2-b",
//   "europe-west2-c" => "europe-west2-c",
//   "europe-west3-a" => "europe-west3-a",
//   "europe-west3-b" => "europe-west3-b",
//   "europe-west3-c" => "europe-west3-c",
//   "europe-west4-a" => "europe-west4-a",
//   "europe-west4-b" => "europe-west4-b",
//   "europe-west4-c" => "europe-west4-c",
//   "europe-west6-a" => "europe-west6-a",
//   "europe-west6-b" => "europe-west6-b",
//   "europe-west6-c" => "europe-west6-c",
//   "europe-west8-a" => "europe-west8-a",
//   "europe-west8-b" => "europe-west8-b",
//   "europe-west8-c" => "europe-west8-c",
//   "europe-west9-a" => "europe-west9-a",
//   "europe-west9-b" => "europe-west9-b",
//   "europe-west9-c" => "europe-west9-c",
//   "me-central1-a" => "me-central1-a",
//   "me-central1-b" => "me-central1-b",
//   "me-central1-c" => "me-central1-c",
//   "me-west1-a" => "me-west1-a",
//   "me-west1-b" => "me-west1-b",
//   "me-west1-c" => "me-west1-c",
//   "northamerica-northeast1-a" => "northamerica-northeast1-a",
//   "northamerica-northeast1-b" => "northamerica-northeast1-b",
//   "northamerica-northeast1-c" => "northamerica-northeast1-c",
//   "northamerica-northeast2-a" => "northamerica-northeast2-a",
//   "northamerica-northeast2-b" => "northamerica-northeast2-b",
//   "northamerica-northeast2-c" => "northamerica-northeast2-c",
//   "southamerica-east1-a" => "southamerica-east1-a",
//   "southamerica-east1-b" => "southamerica-east1-b",
//   "southamerica-east1-c" => "southamerica-east1-c",
//   "southamerica-west1-a" => "southamerica-west1-a",
//   "southamerica-west1-b" => "southamerica-west1-b",
//   "southamerica-west1-c" => "southamerica-west1-c",
//   "us-central1-a" => "us-central1-a",
//   "us-central1-b" => "us-central1-b",
//   "us-central1-c" => "us-central1-c",
//   "us-central1-f" => "us-central1-f",
//   "us-east1-b" => "us-east1-b",
//   "us-east1-c" => "us-east1-c",
//   "us-east1-d" => "us-east1-d",
//   "us-east4-a" => "us-east4-a",
//   "us-east4-b" => "us-east4-b",
//   "us-east4-c" => "us-east4-c",
//   "us-east5-a" => "us-east5-a",
//   "us-east5-b" => "us-east5-b",
//   "us-east5-c" => "us-east5-c",
//   "us-south1-a" => "us-south1-a",
//   "us-south1-b" => "us-south1-b",
//   "us-south1-c" => "us-south1-c",
//   "us-west1-a" => "us-west1-a",
//   "us-west1-b" => "us-west1-b",
//   "us-west1-c" => "us-west1-c",
//   "us-west2-a" => "us-west2-a",
//   "us-west2-b" => "us-west2-b",
//   "us-west2-c" => "us-west2-c",
//   "us-west3-a" => "us-west3-a",
//   "us-west3-b" => "us-west3-b",
//   "us-west3-c" => "us-west3-c",
//   "us-west4-a" => "us-west4-a",
//   "us-west4-b" => "us-west4-b",
//   "us-west4-c" => "us-west4-c",
// );

// $gcp_regions = array(
//   "asia-east1" => "asia-east1 (Taiwan)",
//   "asia-northeast1" => "asia-northeast1 (Tokyo)",
//   "asia-northeast2" => "asia-northeast2 (Osaka)",
//   "europe-north1" => "europe-north1 (Finland) ",
//   "europe-southwest1" => "europe-southwest1 (Madrid)",
//   "europe-west1" => "europe-west1 (Belgium)",
//   "europe-west4" => "europe-west4 (Netherlands)",
//   "europe-west8" => "europe-west8 (Milan)",
//   "europe-west9" => "europe-west9 (Paris)",
//   "me-west1" => "me-west1 (Tel Aviv)",
//   "us-central1" => "us-central1 (Iowa)",
//   "us-east1" => "us-east1 (South Carolina)",
//   "us-east4" => "us-east4 (Northern Virginia)",
//   "us-east5" => "us-east5 (Columbus)",
//   "us-south1" => "us-south1 (Dallas)",
//   "us-west1" => "us-west1 (Oregon)",
// );
$gcp_regions = array(
  "asia-east1" => "asia-east1",
  "asia-northeast1" => "asia-northeast1",
  "asia-northeast2" => "asia-northeast2",
  "europe-north1" => "europe-north1",
  "europe-southwest1" => "europe-southwest1",
  "europe-west1" => "europe-west1",
  "europe-west4" => "europe-west4",
  "europe-west8" => "europe-west8",
  "europe-west9" => "europe-west9",
  "me-west1" => "me-west1",
  "us-central1" => "us-central1",
  "us-east1" => "us-east1",
  "us-east4" => "us-east4",
  "us-east5" => "us-east5",
  "us-south1" => "us-south1",
  "us-west1" => "us-west1",
);
$tvs_admin = new TVC_Admin_Helper();
$tvs_admin_data = $tvs_admin->get_ee_options_data();
$store_id = $tvs_admin_data['setting']->store_id;

// echo "<pre>";
// print_r($store_id);
// echo "</pre>";
// exit();

$sst_web_container = (isset($ee_options['sst_web_container']) && $ee_options['sst_web_container'] != "") ? $ee_options['sst_web_container'] : "";
$sst_server_container = (isset($ee_options['sst_server_container']) && $ee_options['sst_server_container'] != "") ? $ee_options['sst_server_container'] : "";
$sst_server_container_config = (isset($ee_options['sst_server_container_config']) && $ee_options['sst_server_container_config'] != "") ? $ee_options['sst_server_container_config'] : "";
$sst_transport_url = (isset($ee_options['sst_transport_url']) && $ee_options['sst_transport_url'] != "") ? $ee_options['sst_transport_url'] : "";
$sst_region = (isset($ee_options['sst_region']) && $ee_options['sst_region'] != "") ? $ee_options['sst_region'] : "";
$is_own_server = isset($ee_options['sst_server_details']['is_sst_own_server']) ? $ee_options['sst_server_details']['is_sst_own_server'] : '';

$sst_server_ip = (isset($ee_options['sst_server_ip']) && $ee_options['sst_server_ip'] != "") ? $ee_options['sst_server_ip'] : "";

$sst_cloud_run_name =  isset($ee_options['sst_server_details']['sst_cloud_run_name']) ? $ee_options['sst_server_details']['sst_cloud_run_name'] : '';

$doc_link_url = "https://www.conversios.io/docs/how-to-create-a-web-server-side-container-and-find-the-container-config-3/?utm_source=app&utm_medium=gtmsetup&utm_campaign=doc";




// check if user is authenticated or not
if ((isset($_GET['g_mail']) && sanitize_text_field($_GET['g_mail'])) && (isset($_GET['subscription_id']) && sanitize_text_field($_GET['subscription_id']))) {
  update_option('ee_customer_gtm_gmail', sanitize_email($_GET['g_mail']));
}

$g_gtm_email = get_option('ee_customer_gtm_gmail');

// perform validation on the user email
$g_gtm_email =  ($g_gtm_email != '') ? $g_gtm_email : "";
$stepCls = $g_gtm_email != "" ? "" : "stepper-conv-bg-grey";
$disableTextCls = $g_gtm_email != "" ? "" : "conv-link-disabled";
$select2Disabled = $g_gtm_email != "" ? "" : "disabled";
$saveBtnDisabled = $g_gtm_email != "" ? "" : "conv-btn-save-disabled";

$gtm_web_account_id = isset($ee_options['sst_gtm_web_settings']['gtm_account_id']) ? $ee_options['sst_gtm_web_settings']['gtm_account_id'] : "";
$gtm_web_container_id = isset($ee_options['sst_gtm_web_settings']['gtm_container_id']) ? $ee_options['sst_gtm_web_settings']['gtm_container_id'] : "";
$gtm_web_container_publicId = isset($ee_options['sst_gtm_web_settings']['gtm_public_id']) ? $ee_options['sst_gtm_web_settings']['gtm_public_id'] : "";
$gtm_web_account_container_name = isset($ee_options['sst_gtm_web_settings']['gtm_account_container_name']) ? $ee_options['sst_gtm_web_settings']['gtm_account_container_name'] : "";
$is_gtm_automatic_process = isset($ee_options['sst_gtm_web_settings']['is_gtm_automatic_process']) ? $ee_options['sst_gtm_web_settings']['is_gtm_automatic_process'] : false;


$gtm_server_account_id = isset($ee_options['sst_gtm_server_settings']['gtm_account_id']) ? $ee_options['sst_gtm_server_settings']['gtm_account_id'] : "";
$gtm_server_container_id = isset($ee_options['sst_gtm_server_settings']['gtm_container_id']) ? $ee_options['sst_gtm_server_settings']['gtm_container_id'] : "";
$gtm_server_container_publicId = isset($ee_options['sst_gtm_server_settings']['gtm_public_id']) ? $ee_options['sst_gtm_server_settings']['gtm_public_id'] : "";
$gtm_server_account_container_name = isset($ee_options['sst_gtm_server_settings']['gtm_account_container_name']) ? $ee_options['sst_gtm_server_settings']['gtm_account_container_name'] : "";
// $is_gtm_automatic_process = isset($ee_options['sst_gtm_server_settings']['is_gtm_automatic_process']) ? $ee_options['sst_gtm_server_settings']['is_gtm_automatic_process'] : false;

$tracking_method = (isset($ee_options['tracking_method']) && $ee_options['tracking_method'] != "") ? $ee_options['tracking_method'] : "";

$use_your_gtm_id = isset($ee_options['use_your_gtm_id']) ? $ee_options['use_your_gtm_id'] : "";


?>


<div class="convcard p-4 mt-0 rounded-3 shadow-sm">

  <?php if (isset($pixel_settings_arr[$subpage]['topnoti']) && $pixel_settings_arr[$subpage]['topnoti'] != "") { ?>
    <div class="alert d-flex align-items-cente p-0" role="alert">
      <span class="p-2 material-symbols-outlined text-light conv-success-bg rounded-start">info</span>
      <div class="p-2 w-100 rounded-end border border-start-0 shadow-sm conv-notification-alert lh-lg">
        <?php esc_html_e($pixel_settings_arr[$subpage]['topnoti'], "enhanced-e-commerce-for-woocommerce-store"); ?>
      </div>
    </div>
  <?php } ?>

  <form id="gtmsstsettings_form">

    <div class="convpixsetting-inner-box mt-4">
      <h5 class="fw-normal mb-1">
        <?php esc_html_e("Select the Tag Manager Container ID:", "enhanced-e-commerce-for-woocommerce-store"); ?>
      </h5>

      <div class="container-section pb-3">
        <div class="card border-0 p-0 shadow-none" style="max-width: 100% !important;">
          <div class="container-setting">
            <nav>
              <div class="nav nav-tabs" id="nav-tab" role="tablist">

                <button class="nav-link active conv-nav-tab" id="nav-automatic-tab" data-bs-toggle="tab" data-bs-target="#nav-automatic" type="button" role="tab" aria-controls="nav-automatic" aria-selected="true"><span>Automatic</span></button>
                <button class="nav-link conv-nav-tab" id="nav-manual-tab" data-bs-toggle="tab" data-bs-target="#nav-manual" type="button" role="tab" aria-controls="nav-manual" aria-selected="false"><span>Manual</span></button>

              </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
              <div class="tab-pane fade show active nav-automatic" id="nav-automatic" role="tabpanel" aria-labelledby="nav-automatic-tab">
                <ul class="progress-steps pt-3">
                  <li>
                    <div class="step-box">
                      <?php
                      $connect_url = $TVC_Admin_Helper->get_custom_connect_url_subpage(admin_url() . 'admin.php?page=conversios-google-analytics', "gtmsstsettings");
                      require_once("googlesigninforsstgtm.php");
                      ?>
                    </div>
                  </li>
                  <li class="stepper-deactivate">
                    <div class="step-box">
                      <div class="row web-container-row" style="cursor: pointer">
                        <div class="col-md-12">
                          <div class="row pb-2" data-bs-toggle="collapse" data-bs-target="#collapseWebContainer" aria-expanded="false" aria-controls="collapseWebContainer">

                            <div class="col-md-8">
                              <h5 class="fw-normal mb-1 web-container-id">Web Container ID:</h5>
                            </div>
                            <div class="col-md-3 d-flex justify-content-end">
                              <span class="web-container-setup-status badge rounded-pill conv-badge conv-badge-green m-0 me-3 align-self-center" style="display: none;">Connected</span>
                            </div>

                            <div class="col-md-1">
                              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22" fill="none" class="web-container-arrow">
                                <g clip-path="url(#clip0_280_276)">
                                  <path d="M15.2075 7.87418L11 12.0725L6.7925 7.87418L5.5 9.16668L11 14.6667L16.5 9.16668L15.2075 7.87418Z" fill="#5F6368" />
                                </g>
                                <defs>
                                  <clipPath id="clip0_280_276">
                                    <rect width="22" height="22" fill="white" />
                                  </clipPath>
                                </defs>
                              </svg>
                            </div>

                          </div>

                        </div>
                      </div>
                      <div class="collapse" id="collapseWebContainer">
                        <div class="row">
                          <div class="col-md-9">
                            <div class="gtm-account-div">
                              <select class="form-select mb-3 selecttwo w-100" id="gtm_account_container_list" name="gtm_account_container_list" disabled="true" style="width: 100% !important;">
                                <option value=""><?php esc_html_e('Select Web Container', "enhanced-e-commerce-for-woocommerce-store"); ?></option>
                              </select>
                              <input type="hidden" name="hidden_gtm_account_id" id="hidden_gtm_account_id" val="<?php echo esc_attr($gtm_web_account_id); ?>">
                              <input type="hidden" name="hidden_gtm_container_id" id="hidden_gtm_container_id" val="<?php echo esc_attr($gtm_web_container_id); ?>">
                              <input type="hidden" name="hidden_gtm_container_publicId" id="hidden_gtm_container_publicId" val="<?php echo esc_attr($gtm_web_container_publicId); ?>">
                            </div>
                          </div>
                          <div class="col-md-3 d-flex align-items-center p-0">
                            <div class="row">
                              <!-- <div class="col-3 d-flex edit-container-div">
                                  <button type="button" class="shadow-none btn btn-sm d-flex conv-enable-selection conv-link-blue align-items-center" id="editContainerDropDown" <?php echo $select2Disabled; ?>>
                                    <span class="material-symbols-outlined md-18">edit</span>
                                    <span class="px-1">Edit</span>
                                  </button>
                                </div> -->
                              <div class="col-md-12 d-flex align-items-center create-container-link">
                                <span><strong><span class="fw-bold-400"> Or </span><a class="fw-bold-500 conv-link-blue <?php echo $disableTextCls ?>" id="create_container_link" href="#"> Create New</a></strong></span>
                              </div>
                            </div>

                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-12">
                            <label class="conv-gtm-guide"><?php esc_html_e('Pre build tags, triggers, variable and template will be created in the selected container.', "enhanced-e-commerce-for-woocommerce-store"); ?>
                              <a href="#" id="import_container_btn" data-bs-toggle="modal" data-bs-target="#importContainerModal" class="<?php echo $disableTextCls ?>">See Details </a>
                            </label>
                          </div>
                        </div>
                        <div class="row d-flex justify-content-end">
                          <div class="col-md-3">
                            <button class="ms-auto d-flex justify-content-center btn btn-primary conv-blue-btn <?php echo $saveBtnDisabled ?>" style="width:100%" id="create_container_data">Save</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </li>
                  <li class="stepper-deactivate">
                    <div class="step-box">
                      <div class="row" style="cursor: pointer">
                        <div class="col-md-12">
                          <div class="row pb-2" data-bs-toggle="collapse" data-bs-target="#collapseServerContainer" aria-expanded="false" aria-controls="collapseServerContainer">
                            <div class="col-md-8">
                              <h5 class="fw-normal mb-1 server-container-id">
                                <?php esc_html_e('Server Container ID:', "enhanced-e-commerce-for-woocommerce-store"); ?>
                              </h5>
                            </div>
                            <div class="col-md-3 d-flex justify-content-end">
                              <span class="server-container-setup-status badge rounded-pill conv-badge conv-badge-green m-0 me-3 align-self-center" style="display: none;">Connected</span>
                            </div>
                            <div class="col-md-1">
                              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22" fill="none" class="server-container-arrow">
                                <g clip-path="url(#clip0_280_276)">
                                  <path d="M15.2075 7.87418L11 12.0725L6.7925 7.87418L5.5 9.16668L11 14.6667L16.5 9.16668L15.2075 7.87418Z" fill="#5F6368" />
                                </g>
                                <defs>
                                  <clipPath id="clip0_280_276">
                                    <rect width="22" height="22" fill="white" />
                                  </clipPath>
                                </defs>
                              </svg>
                            </div>
                          </div>

                        </div>
                      </div>
                      <div class="collapse" id="collapseServerContainer">
                        <div class="row">
                          <div class="col-md-9">
                            <div class="server-gtm-account-div">

                              <select class="form-select mb-3 selecttwo w-100" id="server_gtm_account_container_list" name="server_gtm_account_container_list" disabled="true" style="width: 100% !important;">
                                <option value=""><?php esc_html_e('Select Server Container', "enhanced-e-commerce-for-woocommerce-store"); ?></option>
                              </select>
                              <input type="hidden" name="hidden_server_gtm_account_id" id="hidden_server_gtm_account_id" val="<?php echo esc_attr($gtm_server_account_id); ?>">
                              <input type="hidden" name="hidden_server_gtm_container_id" id="hidden_server_gtm_container_id" val="<?php echo esc_attr($gtm_server_container_id); ?>">
                              <input type="hidden" name="hidden_server_gtm_container_publicId" id="hidden_server_gtm_container_publicId" val="<?php echo esc_attr($gtm_server_container_publicId); ?>">
                            </div>
                          </div>
                          <div class="col-md-3 d-flex align-items-center p-0">
                            <div class="row">
                              <!-- <div class="col-3 d-flex edit-container-div">
                                  <button type="button" class="shadow-none btn btn-sm d-flex conv-enable-selection conv-link-blue align-items-center" id="editServerContainerDropDown" <?php echo $select2Disabled; ?>>
                                    <span class="material-symbols-outlined md-18">edit</span>
                                    <span class="px-1">Edit</span>
                                  </button>
                                </div> -->
                              <div class="col-md-12 d-flex align-items-center create-server-container-link">
                                <span><strong><span class="fw-bold-400"> Or </span><a class="fw-bold-500 conv-link-blue <?php echo $disableTextCls ?>" id="create_server_container_link" href="#"> Create New</a></strong></span>
                              </div>
                            </div>

                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-12">
                            <label class="conv-gtm-guide"><?php esc_html_e('Pre build tags, triggers, variable and template will be created in the selected container.', "enhanced-e-commerce-for-woocommerce-store"); ?>
                              <a href="#" id="import_container_btn" data-bs-toggle="modal" data-bs-target="#serverContainerDetailModal" class="<?php echo $disableTextCls ?>">See Details </a>
                            </label>
                          </div>


                        </div>
                        <div class="row d-flex justify-content-end">
                          <div class="col-md-3">
                            <button class="ms-auto d-flex justify-content-center btn btn-primary conv-blue-btn <?php echo $saveBtnDisabled ?>" style="width:100%" id="create_server_container_data">Save</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </li>
                  <li class="stepper-deactivate reduceHeight">
                    <div class="step-box">
                      <div class="row" style="cursor: pointer">
                        <div class="col-md-12">
                          <div class="row pb-2" data-bs-toggle="collapse" data-bs-target="#collapseServerSetup" aria-expanded="true" aria-controls="collapseServerSetup">
                            <div class="col-md-8">
                              <h5 class="fw-normal mb-1 server-cloud-run-url">Server Set-up</h5>
                            </div>
                            <div class="col-md-3 d-flex justify-content-end">
                              <span class="server-cloud-run-setup-status badge rounded-pill conv-badge conv-badge-green m-0 me-3 align-self-center" style="display: none;">Connected</span>
                            </div>
                            <div class="col-md-1">
                              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22" fill="none" class="server-setup-arrow">
                                <g clip-path="url(#clip0_280_276)">
                                  <path d="M15.2075 7.87418L11 12.0725L6.7925 7.87418L5.5 9.16668L11 14.6667L16.5 9.16668L15.2075 7.87418Z" fill="#5F6368"></path>
                                </g>
                                <defs>
                                  <clipPath id="clip0_280_276">
                                    <rect width="22" height="22" fill="white"></rect>
                                  </clipPath>
                                </defs>
                              </svg>
                            </div>

                          </div>

                        </div>
                      </div>

                      <div class="server-set-up-main-div collapse conv-pointer-none-opacity" id="collapseServerSetup">

                        <h5 class="fw-normal mb-1">
                          <?php esc_html_e('Select Server', "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </h5>
                        <div class="row pt-1">
                          <div class="col-md-12">
                            <input type="radio" name="use_you_own_server" id="conversios_server" <?php echo esc_attr(($is_own_server === false || $is_own_server === 'false') ? 'checked="checked"' : ''); ?>>
                            <label class="form-check-label ps-2" for="conversios_server">
                              <?php esc_html_e("Default (Conversios Server)", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                          </div>
                        </div>

                        <div class="row pt-1">
                          <div class="col-md-12">
                            <input type="radio" name="use_you_own_server" id="own_server" <?php echo esc_attr(($is_own_server === true || $is_own_server === 'true') ? 'checked="checked"' : ''); ?>>
                            <label class="form-check-label ps-2" for="own_server">
                              <?php esc_html_e("Use your own server", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                          </div>
                        </div>

                        <div class="default-server-div">
                          <!-- SST Server Region -->
                          <div class="row pt-3">
                            <div class="col-12">
                              <select class="form-select form-select-lg mb-3 selecttwosearch w-100" name="sst_region" id="sst_region" style="width: 100%">
                                <option value=""><?php esc_html_e("Select Region", "enhanced-e-commerce-for-woocommerce-store"); ?></option>
                                <?php
                                foreach ($gcp_regions as $gcp_region) {
                                  $selected = "";
                                  if ($sst_region == $gcp_region) {
                                    $selected = "selected";
                                  }
                                ?>
                                  <option value="<?php echo esc_html_e($gcp_region); ?>" <?php echo $selected; ?>>
                                    <?php echo esc_html_e($gcp_region); ?>
                                  </option>
                                <?php
                                }
                                ?>
                              </select>
                            </div>
                          </div>

                          <!-- SST Server Config -->
                          <div class="row pt-3">
                            <div class="col-md-12">
                              <input type="text" class="form-control-lg w-100" name="sst_server_container_config" id="sst_server_container_config" value="<?php echo esc_attr($sst_server_container_config); ?>" placeholder="Add Container Config here">
                              <p class="fw-bold">
                                <a class="conv-link-blue" href="<?php echo esc_url_raw($doc_link_url); ?>" target="_blank">
                                  <?php esc_html_e('How To Find The Container Config?', "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </a>
                              </p>
                            </div>
                          </div>
                          <div class="row server-detail-div" style="display: none;">
                            <h5 class="m-0"><?php esc_html_e('Server Details:', "enhanced-e-commerce-for-woocommerce-store"); ?></h5>
                            <div class="col-md-12">
                              <div class="row p-3 pb-1">
                                <div class="col-md-12 lh-lg conv-info-box">
                                  URL: <span class="transport-url"> <?php echo $sst_transport_url ?></span>

                                </div>
                              </div>
                              <div class="row p-3 pt-1 d-none">
                                <!-- <div class="col-md-12 lh-lg conv-info-box">
                                  IP: <span class="sst-server-ip"><?php echo $sst_server_ip ?></span>
                                </div> -->
                                <input type="hidden" name="sst_server_ip" id="sst_server_ip" value="<?php echo esc_attr($sst_server_ip); ?>">
                              </div>
                              <input type="hidden" name="sst_cloud_run_name" id="sst_cloud_run_name" value="<?php echo esc_attr($sst_cloud_run_name); ?>">
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <button class="justify-content-center conv-blue-btn conv-btn-save-disabled btn btn-primary" id="create_cloud_run" style="width:100%">Click to import server details</button>
                            </div>
                          </div>

                        </div>

                        <div class="own-server-div" style="display: none;">
                          <!-- SST Server Transport URL -->
                          <div class="">
                            <h5 class="fw-normal mb-1">
                              <?php esc_html_e("Server URL:", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </h5>
                            <input type="text" class="form-control-lg w-100" name="sst_transport_url" id="sst_transport_url" value="<?php echo esc_attr($sst_transport_url); ?>">
                            <p class="fw-bold">
                              <a class="conv-link-blue" href="<?php echo esc_url_raw("https://" . TVC_AUTH_CONNECT_URL . "/help-center/configure_our_plugin_with_your_gtm.pdf"); ?>" target="_blank">
                                <?php esc_html_e('How To Find GTM Server URL?', "enhanced-e-commerce-for-woocommerce-store"); ?>
                              </a>
                            </p>
                          </div>
                        </div>

                        <div class="row d-flex justify-content-end  ">
                          <div class="col-md-3">
                            <button class="ms-auto d-flex justify-content-center btn btn-primary conv-blue-btn conv-btn-save-disabled" style="width:100%" id="save_cloud_run">Save</button>
                          </div>
                        </div>

                      </div>
                    </div>



                  </li>


                </ul>

              </div>
              <div class="tab-pane fade" id="nav-manual" role="tabpanel" aria-labelledby="nav-manual-tab">
                <!-- SST Web Container -->
                <div class="mt-4">
                  <h5 class="fw-normal mb-1">
                    <?php esc_html_e("Web Container ID", "enhanced-e-commerce-for-woocommerce-store"); ?>
                  </h5>
                  <input type="text" class="form-control-lg w-100" name="sst_web_container" id="sst_web_container" value="<?php echo esc_attr($sst_web_container); ?>" placeholder="Enter web container ID">
                  <p class="fw-bold">
                    <a class="conv-link-blue" href="<?php echo esc_url_raw("https://" . TVC_AUTH_CONNECT_URL . "/help-center/configure_our_plugin_with_your_gtm.pdf"); ?>" target="_blank">
                      <?php esc_html_e('How To Setup a Web Container?', "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </a>
                  </p>
                </div>

                <!-- SST Server Container -->
                <div class="mt-4">
                  <h5 class="fw-normal mb-1">
                    <?php esc_html_e("Server Container ID", "enhanced-e-commerce-for-woocommerce-store"); ?>
                  </h5>
                  <input type="text" class="form-control-lg w-100" name="sst_server_container" id="sst_server_container" value="<?php echo esc_attr($sst_server_container); ?>" placeholder="Enter server container ID">
                  <p class="fw-bold">
                    <a class="conv-link-blue" href="<?php echo esc_url_raw($doc_link_url); ?>" target="_blank">
                      <?php esc_html_e('How To Create A Server Container?', "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </a>
                  </p>
                </div>

                <!-- SST Server Transport URL -->
                <div class="mt-4">
                  <h5 class="fw-normal mb-1">
                    <?php esc_html_e("Server URL", "enhanced-e-commerce-for-woocommerce-store"); ?>
                  </h5>
                  <input type="text" class="form-control-lg w-100" name="sst_transport_url" id="manual_sst_transport_url" value="<?php echo esc_attr($sst_transport_url); ?>" placeholder="Enter server url">
                  <p class="fw-bold">
                    <a class="conv-link-blue" href="<?php echo esc_url_raw("https://" . TVC_AUTH_CONNECT_URL . "/help-center/configure_our_plugin_with_your_gtm.pdf"); ?>" target="_blank">
                      <?php esc_html_e('How To Find GTM Server URL?', "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </a>
                  </p>
                </div>

              </div>

            </div>
          </div>
        </div>
      </div>


      <div class="event-setting-div py-3 border-top">
        <div class="row">
          <div class="col-md-12">
            <a class="conv-link-blue shadow-none collapsed px-2 d-flex align-items-center" id="eventCollapseLink" data-bs-toggle="collapse" href=".collapseEventSetting" role="button" aria-expanded="false" aria-controls="collapseEventSetting">
              <?php esc_html_e("Advanced Settings", "enhanced-e-commerce-for-woocommerce-store"); ?>
              <div class="conv-down-arrow conv-arrow m-1">
              </div>
            </a>

          </div>
        </div>

        <div class="row collapse collapseEventSetting">

          <div class="py-3">
            <h5 class="fw-normal mb-1">
              <?php esc_html_e("Select User Roles to Disable Tracking:", "enhanced-e-commerce-for-woocommerce-store"); ?>
            </h5>
            <select class="form-select mb-3 selecttwo w-100" id="conv_disabled_users" name="conv_disabled_users[]" multiple="multiple" data-placeholder="Select role">
              <?php foreach ($TVC_Admin_Helper->conv_get_user_roles() as $slug => $name) {
                $is_selected = "";
                if (!empty($ee_options['conv_disabled_users'])) {
                  $is_selected = in_array($slug, $ee_options['conv_disabled_users']) ? "selected" : "";
                }
              ?>
                <option value="<?php echo esc_attr($slug); ?>" <?php echo $is_selected; ?>><?php echo esc_attr($name); ?></option>
              <?php } ?>
            </select>
          </div>


          <div class="py-3">
            <h5 class="fw-normal mb-1">
              <?php esc_html_e("Select Events to Track:", "enhanced-e-commerce-for-woocommerce-store"); ?>
            </h5>
            <select class="form-select mb-3 selecttwo w-100" id="ga_selected_events" name="conv_selected_events[ga][]" multiple="multiple" required data-placeholder="Select event">
              <?php
              $conv_selected_events = unserialize(get_option('conv_selected_events'));
              $conv_all_pixel_event = $TVC_Admin_Helper->conv_all_pixel_event();
              foreach ($conv_all_pixel_event as $slug => $name) {
                $is_selected = empty($conv_selected_events) ? "selected" : "";
                if (!empty($conv_selected_events['ga'])) {
                  $is_selected =  in_array($slug, $conv_selected_events['ga']) ? "selected" : "";
                }
              ?>
                <option value="<?php echo esc_attr($slug); ?>" <?php echo $is_selected; ?>><?php echo esc_attr($name); ?></option>
              <?php } ?>
            </select>
          </div>

        </div>



      </div>

      <input type="hidden" name="tracking_method" id="tracking_method" value="gtm">
    </div>
  </form>
</div>
<!-- Success Save Modal -->
<div class="modal fade" id="conv_container_success_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">

      </div>
      <div class="modal-body text-center p-0">
        <img style="width:184px;" src="<?= esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/update_success_logo.png'); ?>">
        <h3 class="fw-normal pt-3">Container Created Successfully</h3>
        <span id="conv_container_success_txt" class="mb-1 lh-lg"></span>
      </div>
      <div class="modal-footer border-0 pb-4 mb-1">
        <button class="btn conv-blue-bg m-auto text-white" data-bs-dismiss="modal">Ok, Done</button>
      </div>
    </div>
  </div>
</div>
<!-- Success Save Modal End -->

<!-- create container modal -->
<div class="modal fade" id="createContainerModal" tabindex="-1" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createContainerModalLabel">Create GTM Container</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
            <div class="account-list-div">
              <h5 class="fw-normal mb-1">
                <?php esc_html_e("Select Account To Create Container :", "enhanced-e-commerce-for-woocommerce-store"); ?>
              </h5>
              <select class="form-select mb-3 selecttwo w-100" id="gtm_account_list" name="gtm_account_list">
                <option value="">Select GTM Account</option>
              </select>
            </div>
          </div>

        </div>
        <div class="row pt-3">
          <div class="col-md-8">
            <h5 class="fw-normal mb-1">Container Name</h5>
            <input type="text" name="container_input_name" id="container_input_name" style="width: 100%;">
          </div>
        </div>

        <div class="row pt-1 d-none conv-error">
          <div class="col-md-12">
            <span>Conversios container already exist with above account.</span>
          </div>
        </div>
        <div class="row pt-2">
          <div class="col-md-12">
            <p>Create Conversios container in selected account with pre build tag, trigger, variable and template.</p>
          </div>
        </div>
        <div class="row hidden-container-link-div">

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn conv-cancel-btn" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn conv-blue-btn conv-text-white" id="create_gtm_container_btn">Create</button>
      </div>
    </div>
  </div>
</div>
<!-- create container modal -->
<?php
require_once("sstWebContainerDetailsHtml.php");
require_once("sstServerContainerDetailsHtml.php");

?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  jQuery(function() {
    jQuery(".selecttwosearch").select2({
      containerCssClass: 'w-100',
      width: 'resolve'
    });

    $('#gtm_account_container_list').select2();

    let plan_id = "<?php echo $plan_id; ?>";
    let gtm_account_id = "<?php echo $gtm_web_account_id; ?>";
    let gtm_container_id = "<?php echo $gtm_web_container_id; ?>";
    let gtm_container_public_id = "<?php echo $gtm_web_container_publicId; ?>";
    let gtm_account_container_name = "<?php echo $gtm_web_account_container_name; ?>";
    let subscription_id = "<?php echo $tvc_data['subscription_id']; ?>"; //subscription_id  
    let selectedOption = gtm_account_id + '_' + gtm_container_id + '_' + gtm_container_public_id;

    let gtm_server_account_id = "<?php echo $gtm_server_account_id; ?>";
    let gtm_server_container_id = "<?php echo $gtm_server_container_id; ?>";
    let gtm_server_container_public_id = "<?php echo $gtm_server_container_publicId; ?>";
    let gtm_server_account_container_name = "<?php echo $gtm_server_account_container_name; ?>";
    let selectedGtmServerOption = gtm_server_account_id + '_' + gtm_server_container_id + '_' + gtm_server_container_public_id;

    let is_gtm_automatic_process = "<?php echo $is_gtm_automatic_process; ?>"
    let gtm_gmail = "<?php echo $g_gtm_email; ?>";

    let sst_server_ip = "<?php echo $sst_server_ip; ?>";
    let sst_region = "<?php echo $sst_region; ?>";
    let store_id = "<?php echo $store_id; ?>";
    console.log('gtm_gmail', gtm_gmail)
    console.log('is_gtm_automatic_process', is_gtm_automatic_process)

    if ((is_gtm_automatic_process === true || is_gtm_automatic_process === 'true')) {
      $('#nav-automatic-tab').click()
      console.log('manual click');
    } else {
      <?php if (isset($_GET['subscription_id']) && sanitize_text_field($_GET['subscription_id']) || $is_gtm_automatic_process === false) { ?>
        $('#nav-automatic-tab').click()
        console.log('auto click');
      <?php } else { ?>
        console.log('manual click');
        $('#nav-manual-tab').click()
      <?php } ?>

    }

    jQuery(document).on('change', 'form#gtmsstsettings_form', function() {

      let isAutomatic = ($('#nav-automatic-tab').hasClass('active')) ? true : false
      // if (!isAutomatic) {
      //   disableSaveBtn()
      // } else {
      //   enableSaveBtn()
      // }
      if (isAutomatic) {
        let gtmIds = $('#gtm_account_container_list').val();
        if (gtmIds != null && gtmIds.length > 2) {
          enableSaveBtn()
        } else {
          disableSaveBtn()
        }
      }
    });

    // Disable Save Button
    function disableSaveBtn() {

      jQuery(".conv-btn-connect").addClass("conv-btn-connect-disabled");
      jQuery(".conv-btn-connect").removeClass("conv-btn-connect-enabled-google");
      jQuery(".conv-btn-connect").text('Save');
    }
    // Enable save button
    function enableSaveBtn() {

      jQuery(".conv-btn-connect").removeClass("conv-btn-connect-disabled");
      jQuery(".conv-btn-connect").addClass("conv-btn-connect-enabled-google");
      jQuery(".conv-btn-connect").text('Save');
    }

    jQuery(document).on("click", ".conv-btn-connect-enabled-google", function(e) {
      e.preventDefault();
      conv_change_loadingbar("show");
      saveData()
    });

    function saveData(type = '') {
      jQuery(this).addClass('disabled');
      var conv_disabled_users_arr = jQuery("#conv_disabled_users").val();
      var conv_disabled_users = conv_disabled_users_arr.length ? conv_disabled_users_arr : [""];
      var conv_selected_events_arr = {
        ga: jQuery("#ga_selected_events").val()
      };

      var conv_selected_events = conv_selected_events_arr;

      var tracking_method = jQuery('#tracking_method').val();
      let web_gtm_ids
      let web_account_id
      let web_container_id
      let web_container_publicId
      let web_gtm_account_container_name

      let server_gtm_ids
      let server_account_id
      let server_container_id
      let server_container_publicId
      let server_gtm_account_container_name

      // if (plan_id != 1) {
      web_gtm_ids = $('#gtm_account_container_list').val().split('_');
      web_gtm_account_container_name = $("#gtm_account_container_list option:selected").text();

      if (web_gtm_ids.length > 2) {
        web_account_id = web_gtm_ids[0] // account id
        web_container_id = web_gtm_ids[1] // container id
        web_container_publicId = web_gtm_ids[2] // container public id

        $('#hidden_gtm_account_id').val(web_account_id);
        $('#hidden_gtm_container_id').val(web_container_id);
        $('#hidden_gtm_container_publicId').val(web_container_publicId);

      }

      server_gtm_ids = $('#server_gtm_account_container_list').val().split('_');
      server_gtm_account_container_name = $("#server_gtm_account_container_list option:selected").text();

      if (server_gtm_ids.length > 2) {
        server_account_id = server_gtm_ids[0] // account id
        server_container_id = server_gtm_ids[1] // container id
        server_container_publicId = server_gtm_ids[2] // container public id

        $('#hidden_server_gtm_account_id').val(server_account_id);
        $('#hidden_server_gtm_container_id').val(server_container_id);
        $('#hidden_server_gtm_container_publicId').val(server_container_publicId);

      }
      // }

      let isAutomatic = ($('#nav-automatic-tab').hasClass('active')) ? true : false;

      // container data
      let sst_web_container = isAutomatic ? web_container_publicId : $('#sst_web_container').val()
      let sst_server_container = isAutomatic ? server_container_publicId : $('#sst_server_container').val()

      // server setup 

      let sst_region = ''
      let sst_server_container_config = ''
      let sst_server_ip = '';

      let sst_transport_url = (isAutomatic === true || isAutomatic === 'true') ? $("#sst_transport_url").val() : $('#manual_sst_transport_url').val();
      console.log('sst_transport_url', sst_transport_url)

      let is_sst_own_server = isAutomatic ? $('#own_server').is(":checked") : false;

      // if (!is_sst_own_server && isAutomatic) {

      sst_region = $('#sst_region').val()
      sst_server_container_config = $('#sst_server_container_config').val()
      sst_server_ip = $('#sst_server_ip').val();
      // }
      console.log('is_sst_own_server', is_sst_own_server)
      console.log('isAutomatic', isAutomatic)
      console.log('sst_server_ip', sst_server_ip)
      let sst_cloud_run_name = $('#sst_cloud_run_name').val()

      jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: tvc_ajax_url,
        data: {
          action: "conv_save_pixel_data",
          pix_sav_nonce: "<?php echo wp_create_nonce('pix_sav_nonce_val'); ?>",
          conv_options_data: {
            // use_your_gtm_id: use_your_gtm_id,
            conv_disabled_users: conv_disabled_users,
            conv_selected_events: conv_selected_events,
            tracking_method: tracking_method,
            subscription_id: "<?php echo $tvc_data['subscription_id']; ?>",
            sst_gtm_web_settings: {
              gtm_account_id: web_account_id,
              gtm_container_id: web_container_id,
              gtm_public_id: web_container_publicId,
              is_gtm_automatic_process: isAutomatic,
              gtm_account_container_name: web_gtm_account_container_name
            },
            sst_gtm_server_settings: {
              gtm_account_id: server_account_id,
              gtm_container_id: server_container_id,
              gtm_public_id: server_container_publicId,
              is_gtm_automatic_process: isAutomatic,
              gtm_account_container_name: server_gtm_account_container_name
            },
            sst_web_container: sst_web_container,
            sst_server_container: sst_server_container,
            sst_server_container_config: sst_server_container_config,
            sst_transport_url: sst_transport_url,
            sst_region: sst_region,
            // sst_server_ip: sst_server_ip,
            sst_cloud_run_name: sst_cloud_run_name,
            sst_server_details: {
              sst_transport_url: sst_transport_url,
              sst_region: sst_region,
              // sst_server_ip: sst_server_ip,
              sst_server_container_config: sst_server_container_config,
              is_sst_own_server: is_sst_own_server,
              sst_cloud_run_name: sst_cloud_run_name
            }
          },
          conv_options_type: ["eeoptions", "eeapidata", "middleware", "eeselectedevents"],
        },
        beforeSend: function() {
          // jQuery(".conv-btn-connect-enabled-google").text("Saving...");
          // conv_change_loadingbar("show");
          let textMsg = '<br><br> <h5 class="text-danger"> Please do not press refresh, as it may stop the integration.</h5>';

          getAlertMessage('info', 'Processing', 'Almost there! Usually it will take 1 to 5 min time to get this integration completed ' + textMsg, icon = 'info', '', '', iconImageSrc = '<img width="300" height="300" src="<?= esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/Loading_icon.gif'); ?>"/ >', false)
        },
        success: function(response) {

          if (type == 'web_container') {
            let gtmIds = $('#gtm_account_container_list').val().split('_');
            // let gtm_account_container_name = $("#gtm_account_container_list option:selected").text();

            if (gtmIds.length > 2) {
              let account_id = gtmIds[0] // account id
              let container_id = gtmIds[1] // container id
              let container_publicId = gtmIds[2] // container public id

              $('#hidden_gtm_account_id').val(account_id);
              $('#hidden_gtm_container_id').val(container_id);
              $('#hidden_gtm_container_publicId').val(container_publicId);

              let gtm_run_gtm_automation_nonce = "<?php echo wp_create_nonce('gtm_run_gtm_automation_nonce'); ?>";

              let subscription_id = "<?php echo $tvc_data['subscription_id']; ?>";
              let workspace_id = 2 // 2 is the default workspace id for the container
              let isServerContainer = false;
              let isSSTContainer = true;

              let gtm_data = {
                account_id: account_id, // account_id 
                container_id: container_id,
                subscription_id: subscription_id, //subscription_id  
                workspace_id: workspace_id,
                isSSTContainer: isSSTContainer,
                isServerContainer: isServerContainer,
                action: "conv_run_gtm_automation",
                gtm_run_gtm_automation_nonce: gtm_run_gtm_automation_nonce
              }
              $('#gtm_account_container_list').attr('disabled', true);
              $("#collapseWebContainer").collapse('toggle');
              $("ul li:nth-child(2)").removeClass("stepper-deactivate");
              runGtmAutomation(gtm_data);
              disableNextStep('create_container_data', 'conv-btn-save-disabled')
              $('#create_container_link').css({
                "pointer-events": "none",
                "opacity": "0.5"
              })
              $('.web-container-setup-status').show();

              $('.web-container-id').html('Web Container ID: ' + container_publicId)

              // enable server container set up 
              $('#server_gtm_account_container_list').attr('disabled', false);
              removeDisableNextStep('collapseServerContainer', "conv-pointer-none-opacity")
              $('#create_server_container_link').css({
                "pointer-events": "auto",
                "opacity": "1"
              })

              if (!$('#collapseServerContainer').hasClass('show')) {
                $('#collapseServerContainer').collapse('toggle')
              }
            }
          } else if (type == 'server_container') {

            let gtmIds = $('#server_gtm_account_container_list').val().split('_');
            // let gtm_account_container_name = $("#server_gtm_account_container_list option:selected").text();

            if (gtmIds.length > 2) {
              let account_id = gtmIds[0] // account id
              let container_id = gtmIds[1] // container id
              let container_publicId = gtmIds[2] // container public id

              $('#hidden_server_gtm_account_id').val(account_id);
              $('#hidden_server_gtm_container_id').val(container_id);
              $('#hidden_server_gtm_container_publicId').val(container_publicId);

              let gtm_run_gtm_automation_nonce = "<?php echo wp_create_nonce('gtm_run_gtm_automation_nonce'); ?>";
              let subscription_id = "<?php echo $tvc_data['subscription_id']; ?>";
              let workspace_id = 2 // 2 is the default workspace id for the container
              let isServerContainer = true;
              let isSSTContainer = true;
              let webContainerPublicId = $('#gtm_account_container_list').val().split('_')[2]
              let gtm_data = {
                account_id: account_id, // account_id 
                container_id: container_id,
                subscription_id: subscription_id, //subscription_id  
                workspace_id: workspace_id,
                isSSTContainer: isSSTContainer,
                isServerContainer: isServerContainer,
                webContainerPublicId: webContainerPublicId, // web container public id to create client in server container
                action: "conv_run_gtm_automation",
                gtm_run_gtm_automation_nonce: gtm_run_gtm_automation_nonce
              }
              runGtmAutomation(gtm_data);
              $("ul li:nth-child(3)").removeClass("stepper-deactivate");

              // disable current step
              disableNextStep('create_server_container_data', 'conv-btn-save-disabled')
              $('#create_server_container_link').css({
                "pointer-events": "none",
                "opacity": "0.5"
              })
              $('#server_gtm_account_container_list').attr('disabled', true);
              $('.server-container-setup-status').show();
              $('.server-container-id').html('Server Container ID: ' + container_publicId)

              // enable next step
              if ($('#collapseServerContainer').hasClass('show')) {
                $('#collapseServerContainer').collapse('toggle')
                $('#collapseServerSetup').collapse('toggle')
              }
              $('.server-set-up-main-div').removeClass('conv-pointer-none-opacity')
              jQuery('#conversios_server').click()
            }
          } else if (type == 'cloud_run') {
            $("ul li:nth-child(4)").removeClass("stepper-deactivate");
            $('.server-set-up-main-div').addClass('conv-pointer-none-opacity')
            $('.server-cloud-run-setup-status').show();
            $('.server-cloud-run-url').html('Server Set-up: ' + $("#sst_transport_url").val())
            swal.close()
            if ($('#collapseServerSetup').hasClass('show')) {
              $('#collapseServerSetup').collapse('toggle')
            }
            $('.tvc_google_signinbtn').hide();
            jQuery("#conv_save_success_modal").modal("show");
          } else {
            swal.close()
            conv_change_loadingbar("hide");
            jQuery("#conv_save_success_modal").modal("show");
          }
          /* run gtm automation */
          // if (isAutomatic) {
          //   runGtmAutomation()
          // }
          /* run gtm automation end */
          // var user_modal_txt = "Conversios Container - GTM-K7X94DG";
          // // if (want_to_use_your_gtm == "1") {
          // user_modal_txt = "Your own Web Side Web Container - " + sst_web_container;
          // let user_server_modal_txt = "Your own Server Side Web Container - " + sst_server_container;
          // // }
          // if (response == "0" || response == "1") {
          //   jQuery(".conv-btn-connect-enabled-google").text("Connect");
          //   if (sst_server_container != '' && sst_server_container != undefined) {
          //     jQuery("#conv_save_success_txt").html('Congratulations, you have successfully connected your <br> Google Tag Manager account with <br> ' + user_modal_txt + '<br> ' + user_server_modal_txt);
          //   } else {
          //     jQuery("#conv_save_success_txt").html('Congratulations, you have successfully connected your <br> Google Tag Manager account with <br> ' + user_modal_txt);
          //   }

          //   jQuery("#conv_save_success_modal").modal("show");
          //   conv_change_loadingbar("hide");
          // }

        }
      });
    }

    var is_own_server = "<?php echo $is_own_server; ?>";
    var sst_transport_url = "<?php echo $sst_transport_url; ?>";

    <?php if (isset($_GET['subscription_id']) && sanitize_text_field($_GET['subscription_id'])) { ?>
      // getGtmAccountWithContainer(true, false);
      // $('#nav-automatic-tab').click()
      getSstWebContainer(true, false);

      $("ul li:nth-child(1)").removeClass("stepper-deactivate");
    <?php } else { ?>
      if (gtm_gmail != "") { // check if user is authenticated
        if (gtm_account_id != '' && gtm_container_id != '' && gtm_container_public_id != '') {
          getGtmAccountWithContainerDB(selectedOption, gtm_account_container_name, 'web');
          if (gtm_server_account_id == '' && gtm_server_container_id == '' && gtm_server_container_public_id == '') {
            getSstWebContainer(false, true);
          }
        } else {
          disableNextStep('collapseServerContainer', 'conv-pointer-none-opacity');
          getSstWebContainer(true, false);
          // $('#collapseServerContainer').addClass('conv-pointer-none-opacity')
        }

        if (gtm_server_account_id != '' && gtm_server_container_id != '' && gtm_server_container_public_id != '') {
          getGtmAccountWithContainerDB(selectedGtmServerOption, gtm_server_account_container_name, 'server');
        }

        $("ul li:nth-child(1)").removeClass("stepper-deactivate");
      } else {
        $("ul li:nth-child(1)").addClass("stepper-deactivate");
      }
    <?php } ?>



    function disableServerConfiguration() {
      console.log('is_own_server', is_own_server)
      console.log(sst_region, sst_server_ip, sst_transport_url)
      if ((is_own_server === true || is_own_server === 'true') && sst_transport_url != '') {
        console.log('iffffff')
        $('.default-server-div').hide();
        $('.own-server-div').show()
        $("ul li:nth-child(4)").removeClass("stepper-deactivate");
        if ($("#collapseServerSetup").hasClass('show')) {
          $("#collapseServerSetup").collapse('toggle');
        }
        $('.server-set-up-main-div').addClass('conv-pointer-none-opacity')
        $('.tvc_google_signinbtn').hide();
        $('.server-cloud-run-setup-status').show();
        $('.server-cloud-run-url').html('Server Set-up:  ' + sst_transport_url)

      } else if ((is_own_server === false || is_own_server === 'false')) {

        if (sst_region != '' && sst_transport_url != '') {

          $('.server-set-up-main-div').addClass('conv-pointer-none-opacity')
          $('.server-detail-div').show()
          $('#create_cloud_run').hide()
          $("ul li:nth-child(4)").removeClass("stepper-deactivate");
          if ($("#collapseServerSetup").hasClass('show')) {
            $("#collapseServerSetup").collapse('toggle');
          }
          $('.tvc_google_signinbtn').hide();
          $('.server-cloud-run-setup-status').show();
          $('.server-cloud-run-url').html('Server Set-up:  ' + sst_transport_url)
        } else if (sst_region == '' && sst_server_ip == '' && sst_transport_url != '' && (is_gtm_automatic_process === true || is_gtm_automatic_process === 'true')) {

          $('.server-set-up-main-div').addClass('conv-pointer-none-opacity')
          jQuery('.default-server-div').hide();
          jQuery('.own-server-div').show();
          $("ul li:nth-child(4)").removeClass("stepper-deactivate");
          $('.server-cloud-run-url').html('Server Set-up:  ' + sst_transport_url)
          disableNextStep('save_cloud_run', 'conv-btn-save-disabled')
          $('#own_server').click();
        } else {
          console.log('elss')
        }
      }

    }


    function getGtmAccountWithContainerDB(selectedOption = '', selectedContainerName = '', usage_context = '') {

      if (usage_context == 'web') {
        let accountContainerEle = $('#gtm_account_container_list');
        accountContainerEle.select2();
        accountContainerEle
          .find('option')
          .remove()
          .end()
          .append('<option value="">Select Web Container</option>')
          .val('')

        let data = {
          id: selectedOption,
          text: selectedContainerName
        };
        let newOption = new Option(data.text, data.id, true, true);
        accountContainerEle.append(newOption).trigger('change');
        if (selectedOption.split('_')[0] != '') {
          $('#create_container_data').addClass("conv-btn-save-disabled")
          $('#create_container_link').css({
            "pointer-events": "none",
            "opacity": "0.5"
          })
          $("ul li:nth-child(2)").removeClass("stepper-deactivate");
          $('.web-container-setup-status').show();
          $('.web-container-id').html('Web Container ID:' + selectedOption.split('_')[2])
        }

      } else {
        if ($('#gtm_account_container_list').val() != '') {
          let accountServerContainerEle = $('#server_gtm_account_container_list');
          accountServerContainerEle.select2();
          accountServerContainerEle
            .find('option')
            .remove()
            .end()
            .append('<option value="">Select Server Container</option>')
            .val('')

          let data = {
            id: selectedOption,
            text: selectedContainerName
          };
          let newOption = new Option(data.text, data.id, true, true);
          accountServerContainerEle.append(newOption).trigger('change');
          if (selectedOption.split('_')[0] != '') {
            $('#create_server_container_data').addClass("conv-btn-save-disabled")

            $('#create_server_container_link').css({
              "pointer-events": "none",
              "opacity": "0.5"
            })

            $(".progress-steps li:nth-child(3)").removeClass("stepper-deactivate");
            if ($("#collapseServerSetup").hasClass("show")) {
              $("#collapseServerSetup").collapse('toggle');
            }
            $('.server-set-up-main-div').removeClass('conv-pointer-none-opacity')
            $('.server-container-setup-status').show();
            $('.server-container-id').html('Server Container ID: ' + selectedOption.split('_')[2])
          }
        } else {
          $('#create_server_container_link').css({
            "pointer-events": "none",
            "opacity": "0.5"
          })
        }
        disableServerConfiguration();
      }

      // if ($('#nav-automatic-tab').hasClass('active')) {
      disableSaveBtn()
      // } else {
      //   enableSaveBtn();
      // }
    }

    function getSstWebContainer(isDisabled = false, isServerContainer = false) {
      var get_gtm_account_with_container_nonce = "<?php echo wp_create_nonce('get_gtm_account_with_container_nonce'); ?>";
      let gtm_data = {
        subscription_id: subscription_id, //subscription_id
        action: "conv_get_gtm_account_with_container", // list of gtm accounts
        get_gtm_account_with_container_nonce: get_gtm_account_with_container_nonce,
      }

      jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: tvc_ajax_url,
        data: gtm_data,
        beforeSend: function() {
          conv_change_loadingbar("show");
        },
        success: function(response) {

          conv_change_loadingbar("hide");
          if (response.status == 200) {

            let data = response.data;

            if ($('#nav-automatic-tab').hasClass('active')) {
              if (data.length == 0) {
                getAlertMessage('info', 'Error', message = 'There is no GTM account associated with email address which you authenticated. Please create GTM Account or Sign in with different account.', icon = 'info', buttonText = 'Try again', buttonColor = '#FCCB1E', iconImageSrc = '<img src="<?= esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_error_logo.png'); ?>"/ >')
              }
            }

            var accountContainerEle = $('#gtm_account_container_list');
            accountContainerEle.select2();
            // remove previous options  from container dropdown
            accountContainerEle
              .find('option')
              .remove()
              .end()
              .append('<option value="">Select Web Container</option>')
              .val('')

            $.each(data, function(key, value) {
              $.each(value, function(aKey, aValue) {
                $.each(aValue['container'], function(cKey, cValue) {
                  if (cValue['usageContext'][0] != 'server') {

                    let text = aValue['name'];
                    let accountId = aValue['accountId'];
                    let containerId = cValue['containerId'];
                    let containerPublicId = cValue['publicId'];
                    let id = accountId + '_' + containerId + '_' + containerPublicId
                    text += '-' + cValue['name']
                    text += '-' + containerPublicId
                    let option = $("<option/>", {
                      value: id,
                      text: text
                    });
                    accountContainerEle.append(option);
                  }
                })
              })
            })

            disableNextStep('create_server_container_data', 'conv-btn-save-disabled')
            disableNextStep('create_container_data', 'conv-btn-save-disabled')
            $('#gtm_account_container_list').val('').trigger('change');
            $('#create_server_container_link').css({
              "pointer-events": "none",
              "opacity": "0.5"
            })
            // check if selected container exists in list of containers 
            let isWebContainerExists = false;
            $("#gtm_account_container_list > option").each(function() {
              if (selectedOption == this.value) {
                if (selectedOption.split('_').length > 2 && selectedOption.split('_')[0] != "" && selectedOption.split('_')[1] != "" && selectedOption.split('_')[2] != "") {
                  $('#gtm_account_container_list').val(selectedOption).trigger('change');
                  $('#create_container_data').addClass("conv-btn-save-disabled")
                  $('#gtm_account_container_list').attr('disabled', true);
                  $("ul li:nth-child(2)").removeClass("stepper-deactivate");
                  $('#create_container_link').css({
                    "pointer-events": "none",
                    "opacity": "0.5"
                  })
                  isWebContainerExists = true;
                  // $("#collapseServerContainer").collapse('toggle');
                  $('.web-container-setup-status').show();
                  $('.web-container-id').html('Web Container ID: ' + selectedOption.split('_')[2])
                }
              }

            });

            if ($('#gtm_account_container_list').val() != '') {
              $('#gtm_account_container_list').attr('disabled', isDisabled);
              // removeDisableNextStep('create_container_data', 'conv-btn-save-disabled')
            } else {
              $('#gtm_account_container_list').attr('disabled', false);
              $("#collapseWebContainer").collapse('toggle');
            }

            if (isWebContainerExists) {
              getSstServerContainer(data, false, true, isWebContainerExists)
            } else {
              getSstServerContainer(data, true, true, isWebContainerExists)
            }




            if (!isDisabled) {
              // $('#editContainerDropDown').parent().addClass('d-none');
              $('.create-container-link').addClass('col-md-12');
            }
            // }

          } else {
            var errors = JSON.parse(response.errors)

            if (errors['subscription_id'] != undefined && errors['subscription_id'].length > 0) {
              getAlertMessage('error', 'Error', message = errors['subscription_id'][0], icon = 'error', buttonText = 'Try again', buttonColor = '#FCCB1E', iconImageSrc = '<img src="<?= esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_error_logo.png'); ?>"/ >')
            }
          }
        }
      });
    }

    function getSstServerContainer(data = [], isDisabled = false, isServerContainer = true, isWebContainerExists = false) {

      if (data.length == 0) {
        var get_gtm_account_with_container_nonce = "<?php echo wp_create_nonce('get_gtm_account_with_container_nonce'); ?>";
        let gtm_data = {
          subscription_id: subscription_id, //subscription_id
          action: "conv_get_gtm_account_with_container", // list of gtm accounts
          get_gtm_account_with_container_nonce: get_gtm_account_with_container_nonce,
        }

        jQuery.ajax({
          type: "POST",
          dataType: "json",
          url: tvc_ajax_url,
          data: gtm_data,
          beforeSend: function() {
            conv_change_loadingbar("show");
          },
          success: function(response) {

            conv_change_loadingbar("hide");

            if (response.status == 200) {

              let data = response.data;
              displayServerContainer(data, false, true);

            } else {
              var errors = JSON.parse(response.errors)
              console.log('errors', errors)
              // if (errors['subscription_id'] != undefined && errors['subscription_id'].length > 0) {
              //   getAlertMessage('error', 'Error', message = errors['subscription_id'][0], icon = 'error', buttonText = 'Try again', buttonColor = '#FCCB1E', iconImageSrc = '<img src="<?= esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_error_logo.png'); ?>"/ >')
              // }
            }
          }
        });
      } else {
        displayServerContainer(data, isDisabled, isServerContainer, isWebContainerExists);
      }

    }

    function displayServerContainer(data = [], isDisabled = false, isServerContainer = true, isWebContainerExists = false) {
      if ($('#nav-automatic-tab').hasClass('active')) {
        if (data.length == 0) {
          // getAlertMessage('info', 'Error', message = 'There is no GTM account associated with email address which you authenticated. Please create GTM Account or Sign in with different account.', icon = 'info', buttonText = 'Try again', buttonColor = '#FCCB1E', iconImageSrc = '<img src="<?= esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_error_logo.png'); ?>"/ >')
        }
      }

      var serverAccountContainerEle = $('#server_gtm_account_container_list');
      serverAccountContainerEle.select2();
      serverAccountContainerEle
        .find('option')
        .remove()
        .end()
        .append('<option value="">Select Server Container</option>')
        .val('')

      $.each(data, function(key, value) {
        $.each(value, function(aKey, aValue) {
          $.each(aValue['container'], function(cKey, cValue) {
            if (cValue['usageContext'][0] == 'server') {
              let text = aValue['name'];
              let accountId = aValue['accountId'];
              let containerId = cValue['containerId'];
              let containerPublicId = cValue['publicId'];
              let id = accountId + '_' + containerId + '_' + containerPublicId
              text += '-' + cValue['name']
              text += '-' + containerPublicId
              let option = $("<option/>", {
                value: id,
                text: text
              });
              serverAccountContainerEle.append(option);
            }
          })
        })
      })

      $('#server_gtm_account_container_list').val('').trigger('change');


      // check if selected container exists in list of containers 
      $("#server_gtm_account_container_list > option").each(function() {
        if (selectedGtmServerOption == this.value) {
          if (selectedGtmServerOption.split('_').length > 2 && selectedGtmServerOption.split('_')[0] != "" && selectedGtmServerOption.split('_')[1] != "" && selectedGtmServerOption.split('_')[2] != "") {
            $('#server_gtm_account_container_list').val(selectedGtmServerOption).trigger('change');

            $('#create_server_container_data').addClass("conv-btn-save-disabled")
            $('#create_server_container_link').css({
              "pointer-events": "none",
              "opacity": "0.5"
            })
            $("ul li:nth-child(3)").removeClass("stepper-deactivate");
            if (sst_transport_url == '') {
              $("#collapseServerSetup").collapse('toggle');
            }
            $('.server-set-up-main-div').removeClass('conv-pointer-none-opacity')
            $('.server-container-setup-status').show();
            $('.server-container-id').html('Server Container ID: ' + selectedGtmServerOption.split('_')[2])
          }
        }
      });

      if ($('#server_gtm_account_container_list').val() != '') {
        $('#server_gtm_account_container_list').attr('disabled', true);
        $('#gtm_account_container_list').attr('disabled', true);
      } else {
        $('#server_gtm_account_container_list').attr('disabled', isDisabled);

        if (isWebContainerExists == true) {
          $('#gtm_account_container_list').attr('disabled', true);
          $("#collapseServerContainer").collapse('toggle');
        }
        $('.server-set-up-main-div').addClass('conv-pointer-none-opacity')
      }
      disableServerConfiguration();
    }
    /*
     *create new container in selected gtm account 
     */
    jQuery(document).on('click', "#create_gtm_container_btn", function(e) {
      e.preventDefault();
      if ($('#gtm_account_list').val() != '') {
        let gtm_create_container_nonce = "<?php echo wp_create_nonce('gtm_create_container_nonce'); ?>";
        let account_id = parseInt($('#gtm_account_list').val());

        let usage_context = ($('#container_link_click').val() == 'create_server_container_link') ? 'server' : 'web';
        let container_input_name = $('#container_input_name').val();

        let gtm_data = {
          subscription_id: subscription_id, //subscription_id  
          gtm_create_container_nonce: gtm_create_container_nonce,
          account_id: account_id, // account_id 
          usage_context: usage_context,
          name: container_input_name,
          action: "conv_create_gtm_container",
        }
        $('.conv-error').addClass('d-none') // remove error class

        // create container with selected account
        jQuery.ajax({
          type: "POST",
          dataType: "json",
          url: tvc_ajax_url,
          data: gtm_data,
          beforeSend: function() {
            conv_change_loadingbar("show");
          },
          success: function(response) {
            conv_change_loadingbar("hide");
            $('#createContainerModal').modal('hide');

            if (response.status == 200) {
              let accountName = $('#gtm_account_list option:selected').text()
              let data = {
                id: account_id + '_' + response.data.containerId + '_' + response.data.publicId,
                text: accountName + '-' + response.data.name + '-' + response.data.publicId
              };
              let newOption = new Option(data.text, data.id, true, true);
              if (response.data.usageContext[0] == 'web') {
                $('#gtm_account_container_list').append(newOption).trigger('change');
                selectedOption = data.id
                $('#create_container_data').click()
              } else {
                $('#server_gtm_account_container_list').append(newOption).trigger('change');
                selectedGtmServerOption = data.id
                $('#create_server_container_data').click()
              }

            } else {
              getAlertMessage('error', 'Error', message = 'Conversios Container already exist in your account.', icon = 'error', buttonText = 'Try again', buttonColor = '#FCCB1E', iconImageSrc = '<img src="<?= esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_error_logo.png'); ?>"/ >')
            }
          }
        });
      }

    })

    // run gtm automatiuon api to import container data into gtm account/container
    function runGtmAutomation(gtm_data = []) {
      // if (gtm_data.length == 0) {
      //   let gtm_run_gtm_automation_nonce = "<?php echo wp_create_nonce('gtm_run_gtm_automation_nonce'); ?>";

      //   let account_id = $('#hidden_gtm_account_id').val();
      //   let subscription_id = "<?php echo $tvc_data['subscription_id']; ?>";
      //   let container_id = $('#hidden_gtm_container_id').val()
      //   let workspace_id = 2 // 2 is the default workspace id for the container
      //   let isServerContainer = 'dependent on the container'
      //   let gtm_data = {
      //     account_id: account_id, // account_id 
      //     container_id: container_id,
      //     subscription_id: subscription_id, //subscription_id  
      //     workspace_id: workspace_id,
      //     isSSTContainer: true,
      //     action: "conv_run_gtm_automation",
      //     gtm_run_gtm_automation_nonce: gtm_run_gtm_automation_nonce
      //   }
      // }

      jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: tvc_ajax_url,
        data: gtm_data,
        beforeSend: function() {

        },
        success: function(response) {
          // conv_change_loadingbar("hide");
          swal.close()
          if (response.status == 200) {

          } else {

          }
        },
        error: function(error) {
          swal.close()
        }
      });
    }

    $('#create_container_link,#create_server_container_link').on('click', function(e) {
      let container_link_click = $(this).attr('id');
      jQuery('#gtm_account_list').select2({
        dropdownParent: $("#createContainerModal")
      });
      $('#createContainerModal').modal('show');
      $('#container_input_name').val('')
      let container_link_click_html = '<input type="hidden" name="container_link_click" value="' + container_link_click + '" id="container_link_click"></input>';

      $('.hidden-container-link-div').html(container_link_click_html)
      // if ($(this).attr('id') == 'create_server_container_link') {
      //   $('.server-input').show();
      // } else {
      //   $('.server-input').hide();
      // }
      let gtm_account_nonce = "<?php echo wp_create_nonce('gtm_account_nonce'); ?>";
      let gtm_data = {
        subscription_id: subscription_id, //subscription_id  
        gtm_account_nonce: gtm_account_nonce,
        action: "conv_get_gtm_account_list",
      }


      jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: tvc_ajax_url,
        data: gtm_data,
        beforeSend: function() {
          conv_change_loadingbar("show");

        },
        success: function(response) {
          conv_change_loadingbar("hide");
          if (response.status == 200) {
            let data = response.data;
            let accountEle = $('#gtm_account_list');
            // remove previous options  from container dropdown
            accountEle
              .find('option')
              .remove()
              .end()
              .append('<option value="">Select GTM Account</option>')
              .val('')


            $.each(data, function(key, value) {
              $.each(value, function(aKey, aValue) {
                let option = $("<option/>", {
                  value: aValue['accountId'],
                  text: aValue['name']
                });
                accountEle.append(option);
              })
            })
          } else {
            // account might be already created
            getAlertMessage('error', 'Error', message = 'There is an issue in fetching GTM Account.', icon = 'error', buttonText = 'Try again', buttonColor = '#FCCB1E', iconImageSrc = '<img src="<?= esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_error_logo.png'); ?>"/ >')

          }
        }
      });

      jQuery('#gtm_account_list').siblings('.select2:first').attr('style', 'width: 260px');
      $('.conv-error').addClass('d-none')
    })
    // localStorage.clear();
    if (JSON.parse(localStorage.getItem("webContainerDetails"))) {
      console.log('webContainerDetails')
      createDetailViewPageData(JSON.parse(localStorage.getItem("webContainerDetails")).data.containerVersion, 'web')
    } else {
      console.log('webContainerDetails Api')
      getGlobalSSTContainerJson(false)
    }

    if (JSON.parse(localStorage.getItem("serverContainerDetails"))) {
      console.log('serverContainerDetails')
      createDetailViewPageData(JSON.parse(localStorage.getItem("serverContainerDetails")).data.containerVersion, 'server')
    } else {
      console.log('serverContainerDetails Api')
      getGlobalSSTContainerJson(true)
    }



    function getGlobalSSTContainerJson(isServerJson = false) {

      let gtm_global_container_json_nonce = "<?php echo wp_create_nonce('gtm_global_container_json_nonce'); ?>";
      var is_sst_server_json = (isServerJson != '') ? isServerJson : '';
      console.log('is_sst_server_json', isServerJson)
      let gtm_data = {
        action: "conv_get_global_container_json",
        gtm_global_container_json_nonce: gtm_global_container_json_nonce,
        is_sst_server_json: isServerJson
      }

      jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: tvc_ajax_url,
        data: gtm_data,
        beforeSend: function() {

        },
        success: function(response) {
          if (response.status == 200) {
            if (response.data.containerVersion['client'] == undefined) {
              localStorage.setItem("webContainerDetails", JSON.stringify(response));
              createDetailViewPageData(response.data.containerVersion, 'web')
            } else if (is_sst_server_json === true || is_sst_server_json === 'true') {
              localStorage.setItem("serverContainerDetails", JSON.stringify(response));
              createDetailViewPageData(response.data.containerVersion, 'server')
            }

          }
        }
      });

    }

    function createDetailViewPageData(containerData, jsonType = 'web') {
      console.log('jsonType', jsonType)
      let tagHtml = ''
      $.each(containerData.tag, function(key, value) {

        tagHtml += `<div ng-repeat="entityName in ctrl.newEntities"> ${value.name} </div>`
      });
      $('.' + jsonType + '-tag-list').append(tagHtml);
      $('.' + jsonType + '-tag-count').text(containerData.tag.length)
      let triggerHtml = ''
      $.each(containerData.trigger, function(key, value) {

        triggerHtml += `<div ng-repeat="entityName in ctrl.newEntities"> ${value.name} </div>`
      });
      $('.' + jsonType + '-trigger-list').append(triggerHtml);
      $('.' + jsonType + '-trigger-count').text(containerData.trigger.length)
      let variableHtml = ''
      $.each(containerData.variable, function(key, value) {

        variableHtml += `<div ng-repeat="entityName in ctrl.newEntities"> ${value.name} </div>`
      });
      $('.' + jsonType + '-variable-list').append(variableHtml);
      $('.' + jsonType + '-variable-count').text(containerData.variable.length)
      let customTemplateHtml = ''
      $.each(containerData.customTemplate, function(key, value) {

        customTemplateHtml += `<div ng-repeat="entityName in ctrl.newEntities"> ${value.name} </div>`
      });
      $('.' + jsonType + '-customTemplate-list').append(customTemplateHtml);
      $('.' + jsonType + '-template-count').text(containerData.customTemplate.length)

      let clientHtml = ''
      if (containerData['client'] != undefined) {
        $.each(containerData.tag, function(key, value) {

          clientHtml += `<div ng-repeat="entityName in ctrl.newEntities"> ${value.name} </div>`
        });
        $('.' + jsonType + '-client-list').append(clientHtml);
        $('.' + jsonType + '-client-count').text(containerData.client.length)
      }

    }

    // GTM Account change event handler
    $('#gtm_account_container_list').on('change', function(e) {
      if ($(this).val() != '') {
        $('#import_container_btn').removeClass('conv-link-disabled');
        $('.step-two').removeClass('stepper-conv-bg-grey')
      } else {
        $('#import_container_btn').addClass('conv-link-disabled');
        $('.step-two').addClass('stepper-conv-bg-grey')
      }
    })

    // change text of the container details modal collapse
    $('.collapseExample').on('hide.bs.collapse', function() {
      $('#details-collapse').text('View Details')
    })
    $('.collapseExample').on('show.bs.collapse', function() {
      $('#details-collapse').text('Close Details')
    })

    // Arrow up and down rotation
    $('.collapseEventSetting').on('show.bs.collapse', function() {
      $('.conv-arrow').removeClass('conv-down-arrow').addClass('conv-up-arrow')
    })
    $('.collapseEventSetting').on('hide.bs.collapse', function() {
      $('.conv-arrow').removeClass('conv-up-arrow').addClass('conv-down-arrow')
    })

    $('.conv-nav-tab').on('click', function() {
      $('form#gtmsstsettings_form').change();
    })

    $('#gtm_account_list').on('change', function() {
      $('.conv-error').addClass('d-none')
    })
    // $('#want_to_use_your_gtm_default').on('change', function() {
    //   setTimeout(() => {
    //     enableSaveBtn();
    //   }, 300)
    // });

    jQuery(document).on("click", "#create_server_container_data", function(e) {
      e.preventDefault();
      // if (plan_id != 1) {
      saveData('server_container');
      // }

    });
    jQuery(document).on("click", "#create_container_data", function(e) {
      e.preventDefault();

      // if (plan_id != 1) {
      saveData('web_container')
      // }

    });



    $('#sst_web_container,#sst_server_container,#sst_server_container_config,input[name=sst_transport_url],#sst_region').on('change', function() {
      if ($(this).val() != '') {
        enableSaveBtn();
        if ($('#sst_server_container_config').val() != '' && $('#sst_server_container_config').val() != '') {
          removeDisableNextStep('create_cloud_run', 'conv-btn-save-disabled')
          // $('#create_cloud_run').removeClass('conv-btn-save-disabled')
          // removeDisableNextStep('save_cloud_run', 'conv-btn-save-disabled')

        }
      } else {
        disableSaveBtn();
        disableNextStep('create_cloud_run', 'conv-btn-save-disabled');
        // disableNextStep('save_cloud_run', 'conv-btn-save-disabled')
        // $('#create_cloud_run').addClass('conv-btn-save-disabled')
      }
    })
    $('#server_gtm_account_container_list').on('change', function() {
      if ($(this).val() != '') {
        removeDisableNextStep('create_server_container_data', 'conv-btn-save-disabled');
      } else {
        disableNextStep('create_server_container_data', 'conv-btn-save-disabled')
      }
    });
    $('#gtm_account_container_list').on('change', function() {

      if ($(this).val() != '') {
        removeDisableNextStep('create_container_data', 'conv-btn-save-disabled');
      } else {
        disableNextStep('create_container_data', 'conv-btn-save-disabled')
      }
    });
    $('.collapse').on('show.bs.collapse', function() {

      $(this).prev().find('svg').css({
        "transform": "rotate(180deg)"
      })
    });

    $('.collapse').on('hide.bs.collapse', function() {

      $(this).prev().find('svg').css({
        "transform": "rotate(0deg)"
      })
    });
    jQuery('#conversios_server').on('click', function() {
      if ($(this).is(":checked")) {
        jQuery('.default-server-div').show();
        jQuery('.own-server-div').hide()
        disableNextStep('save_cloud_run', 'conv-btn-save-disabled')
      }

    });
    jQuery('#own_server').on('click', function() {
      if ($(this).is(":checked")) {
        jQuery('.default-server-div').hide();
        jQuery('.own-server-div').show();
        if ($('#own_sst_transport_url').val() != '') {
          removeDisableNextStep('save_cloud_run', 'conv-btn-save-disabled')
        } else {
          disableNextStep('save_cloud_run', 'conv-btn-save-disabled')
        }
      }
    })
    $('#own_sst_transport_url').on('change', function() {

      if ($(this).val() != '' && $('#own_server').is(":checked")) {
        removeDisableNextStep('save_cloud_run', 'conv-btn-save-disabled')
      } else {
        disableNextStep('save_cloud_run', 'conv-btn-save-disabled');
      }
    })

    function disableNextStep(el, cl) {
      $('#' + el).addClass(cl);
    }

    function removeDisableNextStep(el, cl) {
      $('#' + el).removeClass(cl);
    }

    $('#create_cloud_run').on('click', function(e) {
      e.preventDefault();

      let create_cloud_nonce = "<?php echo wp_create_nonce('create_cloud_nonce'); ?>";
      let sst_region = $('#sst_region').val()
      let sst_config = $('#sst_server_container_config').val()
      let sstAccount = $('#server_gtm_account_container_list').val().split('_');
      let sst_server_account_id = sstAccount[0];
      let sst_server_container_id = sstAccount[1];
      let sst_container_name = $("#server_gtm_account_container_list option:selected").text().split('-')[1];
      let cloud_run_data = {
        subscription_id: subscription_id, //subscription_id  
        create_cloud_nonce: create_cloud_nonce,
        action: "conv_create_cloud_run",
        sst_region: sst_region,
        sst_config: sst_config,
        store_id: store_id,
        sst_server_account_id: sst_server_account_id,
        sst_server_container_id: sst_server_container_id,
        sst_server_container_name: sst_container_name
      }

      jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: tvc_ajax_url,
        data: cloud_run_data,
        beforeSend: function() {

          let textMsg = '<br><br> <h5 class="text-danger"> Please do not press refresh, as it may stop the integration.</h5>';

          getAlertMessage('info', 'Processing', 'Almost there! Usually it will take 1 to 5 min time to get this integration completed ' + textMsg, icon = 'info', '', '', iconImageSrc = '<img width="300" height="300" src="<?= esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/Loading_icon.gif'); ?>"/ >', false);

        },
        success: function(response) {
          swal.close()
          console.log('response', response);
          $('#sst_transport_url').val(response['tagging_server_url']);
          // $('#sst_server_ip').val(response['ip']);
          $('.transport-url').text(response['tagging_server_url'])
          // $('.sst-server-ip').text(response['ip'])
          $('#sst_cloud_run_name').val(response['cloud_run_name'])
          $('.server-detail-div').show();
          $('#create_cloud_run').hide()
          removeDisableNextStep('save_cloud_run', 'conv-btn-save-disabled')

        },
        error: function(error) {
          swal.close()
        }
      });

    });

    $('#save_cloud_run').on('click', function(e) {
      e.preventDefault();
      disableNextStep('save_cloud_run', 'conv-btn-save-disabled')
      saveData('cloud_run');

    })

    function getAlertMessage(type = 'Success', title = 'Success', message = '', icon = 'success', buttonText = 'Ok, Done', buttonColor = '#1085F1', iconImageTag = '', showBtn = true) {
      console.log('iconImageTag', iconImageTag)
      Swal.fire({
        type: type,
        icon: icon,
        title: title,
        showCancelButton: showBtn,
        showConfirmButton: showBtn,
        confirmButtonText: buttonText,
        confirmButtonColor: buttonColor,
        html: message,
      })
      let swalContainer = Swal.getContainer();
      $(swalContainer).find('.swal2-icon-show').removeClass('swal2-' + icon).removeClass('swal2-icon')
      $('.swal2-icon-show').html(iconImageTag)

    }

    $('#collapseServerSetup').on('show.bs.collapse hide.bs.collapse', function(e) {
      if (e.type == 'show') {
        $('.server-set-up-main-div').closest('li').removeClass('reduceHeight')
      } else {
        $('.server-set-up-main-div').closest('li').addClass('reduceHeight')
      }
    })

  });
</script>