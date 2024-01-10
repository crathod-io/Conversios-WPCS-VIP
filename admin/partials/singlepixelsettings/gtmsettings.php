<?php
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

$gtm_account_id = isset($ee_options['gtm_settings']['gtm_account_id']) ? $ee_options['gtm_settings']['gtm_account_id'] : "";
$gtm_container_id = isset($ee_options['gtm_settings']['gtm_container_id']) ? $ee_options['gtm_settings']['gtm_container_id'] : "";
$gtm_container_publicId = isset($ee_options['gtm_settings']['gtm_public_id']) ? $ee_options['gtm_settings']['gtm_public_id'] : "";
$gtm_account_container_name = isset($ee_options['gtm_settings']['gtm_account_container_name']) ? $ee_options['gtm_settings']['gtm_account_container_name'] : "";
$is_gtm_automatic_process = isset($ee_options['gtm_settings']['is_gtm_automatic_process']) ? $ee_options['gtm_settings']['is_gtm_automatic_process'] : false;

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

  <form id="gtmsettings_form">

    <div class="convpixsetting-inner-box mt-4">
      <h5 class="fw-normal mb-1">
        <?php esc_html_e("Select the Tag Manager Container ID:", "enhanced-e-commerce-for-woocommerce-store"); ?>
      </h5>
      <?php
      $tracking_method = (isset($ee_options['tracking_method']) && $ee_options['tracking_method'] != "") ? $ee_options['tracking_method'] : "";
      $want_to_use_your_gtm = "";
      if ($tracking_method == "gtm") {
        $want_to_use_your_gtm = (isset($ee_options['want_to_use_your_gtm']) && $ee_options['want_to_use_your_gtm'] != "") ? $ee_options['want_to_use_your_gtm'] : "0";
      }
      if ((isset($_GET['g_mail']) && sanitize_text_field($_GET['g_mail'])) && (isset($_GET['subscription_id']) && sanitize_text_field($_GET['subscription_id']))) {
        $want_to_use_your_gtm = "1";
      }
      $use_your_gtm_id = isset($ee_options['use_your_gtm_id']) ? $ee_options['use_your_gtm_id'] : "";
      ?>
      <div>
        <div class="py-1">
          <input type="radio" <?php echo esc_attr(($want_to_use_your_gtm == "0") ? 'checked="checked"' : ''); ?> name="want_to_use_your_gtm" id="want_to_use_your_gtm_default" value="0">
          <label class="form-check-label ps-2" for="want_to_use_your_gtm_default">
            <?php esc_html_e("Default (Conversios Container - GTM-K7X94DG)", "enhanced-e-commerce-for-woocommerce-store"); ?>
          </label>
        </div>

        <div class="py-1">
          <input type="radio" <?php echo esc_attr(($want_to_use_your_gtm == "1") ? 'checked="checked"' : ''); ?> name="want_to_use_your_gtm" id="want_to_use_your_gtm_own" value="1">
          <label class="form-check-label ps-2" for="want_to_use_your_gtm_own">
            <?php esc_html_e("Use your own GTM container", "enhanced-e-commerce-for-woocommerce-store"); ?>
          </label>
        </div>

      </div>


      <div class="container-section pb-3 <?php echo esc_attr(($want_to_use_your_gtm == "0") || $want_to_use_your_gtm == "" ? 'd-none' : ''); ?>">
        <div class="card border-0 p-0 shadow-none" style="max-width: 100% !important;">
          <div class="container-setting">
            <nav>
              <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <!-- <button class="button-five"></button> -->
                <button class="nav-link active conv-nav-tab" id="nav-automatic-tab" data-bs-toggle="tab" data-bs-target="#nav-automatic" type="button" role="tab" aria-controls="nav-automatic" aria-selected="true"><span>Automatic</span></button>
                <button class="nav-link conv-nav-tab" id="nav-manual-tab" data-bs-toggle="tab" data-bs-target="#nav-manual" type="button" role="tab" aria-controls="nav-manual" aria-selected="false"><span>Manual</span></button>

              </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
              <div class="tab-pane fade show active" id="nav-automatic" role="tabpanel" aria-labelledby="nav-automatic-tab">

                <?php
                $connect_url = $TVC_Admin_Helper->get_custom_connect_url_subpage(admin_url() . 'admin.php?page=conversios-google-analytics', "gtmsettings");
                require_once("googlesigninforgtm.php");
                ?>


                <div class="row" style="margin-top: -10px;">
                  <div class="col-md-1 stepper-parent-div">
                    <div class="stepper step-two <?php echo $stepCls ?>">2</div>
                  </div>
                  <div class="col-md-11">
                    <div class="gtm_div">
                      <div class="row">
                        <h5 class="fw-normal mb-1">
                          <?php esc_html_e("GTM Account container:", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </h5>
                      </div>
                      <div class="row pt-1">
                        <div class="col-md-7">
                          <div class="gtm-account-div">

                            <select class="form-select mb-3 selecttwo w-100" id="gtm_account_container_list" name="gtm_account_container_list" disabled="true" style="width: 100% !important;">
                              <option value="">Select Container</option>
                            </select>
                            <input type="hidden" name="hidden_gtm_account_id" id="hidden_gtm_account_id" val="<?php echo esc_attr($gtm_account_id); ?>">
                            <input type="hidden" name="hidden_gtm_container_id" id="hidden_gtm_container_id" val="<?php echo esc_attr($gtm_container_id); ?>">
                            <input type="hidden" name="hidden_gtm_container_publicId" id="hidden_gtm_container_publicId" val="<?php echo esc_attr($gtm_container_publicId); ?>">
                          </div>
                        </div>

                        <div class="col-md-5 d-flex align-items-center p-0">
                          <div class="row">
                            <div class="col-3 d-flex edit-container-div">
                              <button type="button" class="shadow-none btn btn-sm d-flex conv-enable-selection conv-link-blue align-items-center" id="editContainerDropDown" <?php echo $select2Disabled; ?>>
                                <span class="material-symbols-outlined md-18">edit</span>
                                <span class="px-1">Edit</span>
                              </button>
                            </div>
                            <div class="col-md-9 d-flex align-items-center create-container-link">
                              <span><strong><span class="fw-bold-400"> Or </span><a class="fw-bold-500 conv-link-blue <?php echo $disableTextCls ?>" id="create_container_link" href="#"> Create New Container</a></strong></span>
                            </div>
                          </div>

                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-md-1"></div>
                  <div class="col-md-11 pt-2">
                    <label class="conv-gtm-guide"><?php esc_html_e('Pre build tags, triggers, variable and template will be created in the selected container.', "enhanced-e-commerce-for-woocommerce-store"); ?>
                      <a href="#" id="import_container_btn" data-bs-toggle="modal" data-bs-target="#importContainerModal" class="<?php echo $disableTextCls ?>">See Details </a>
                    </label>
                  </div>
                </div>
                <!-- <div class="impot-container-div"><button type="button" class="btn btn-success" id="import_container_btn" data-bs-toggle="modal" data-bs-target="#importContainerModal">import</button></div> -->
              </div>
              <div class="tab-pane fade" id="nav-manual" role="tabpanel" aria-labelledby="nav-manual-tab">
                <div id="use_your_gtm_id_box" class="use_your_gtm_id pt-3 <?php echo esc_attr(($want_to_use_your_gtm == "0") || $want_to_use_your_gtm == "" ? 'd-none' : ''); ?>">
                  <div class="row">
                    <div class="col-md-4">
                      <h5 class="fw-normal mb-1" for="user_your_gtm_id">GTM Container ID</h5>
                    </div>
                    <div class="col-md-12 pt-1">
                      <input type="text" class="form-control-lg display-6" name="use_your_gtm_id" id="use_your_gtm_id" value="<?php echo esc_attr($use_your_gtm_id); ?>" placeholder="Enter GTM Container ID">
                    </div>
                  </div>
                  <p class="pt-2 conv-gtm-guide"><?php esc_html_e('Since you have selected “Use Your Own GTM Container” manually, follow this guide to set up container in your GTM Account. ', "enhanced-e-commerce-for-woocommerce-store"); ?><strong><a class="conv-link-blue fw-bold-400" href="<?php echo esc_url_raw("https://" . TVC_AUTH_CONNECT_URL . "/help-center/configure_our_plugin_with_your_gtm.pdf"); ?>" target="_blank"><?php esc_html_e(' Click Here', "enhanced-e-commerce-for-woocommerce-store"); ?></a></strong></p>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>


      <div class="event-setting-div py-3 border-top">
        <div class="row">
          <div class="col-md-3">
            <a class="shadow-none collapsed px-2 d-flex align-items-center" id="eventCollapseLink" data-bs-toggle="collapse" href=".collapseEventSetting" role="button" aria-expanded="false" aria-controls="collapseEventSetting">
              Event Settings
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


          <div class="py-3 net_revenue_setting_box">
            <div class="d-flex">
              <h5 class="fw-normal mb-1">
                <?php esc_html_e("Revenue Tracking", "enhanced-e-commerce-for-woocommerce-store"); ?>
              </h5>
              <span class="material-symbols-outlined text-secondary md-18 ps-2 align-self-center" data-bs-toggle="tooltip" data-bs-placement="top" title="Select metrics from below that will be calculated for revenue tracking on the purchase event. For Example, if you select Product subtotal and Shipping then order revenue = product subtotal + shipping.">
                info
              </span>
            </div>

            <div class="form-check form-check-inline">
              <input class="form-check-input conv_revnue_checkinput" type="checkbox" id="conv_revnue_subtotal" value="subtotal" <?php echo isset($ee_options['net_revenue_setting']) ? 'checked onclick="return false"' : ''; ?>>
              <label class="form-check-label" for="conv_revnue_subtotal">
                <?php esc_html_e("Product subtotal (Sum of Product prices)", "enhanced-e-commerce-for-woocommerce-store"); ?>
              </label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input conv_revnue_checkinput" type="checkbox" id="conv_revnue_shipping" value="shipping" <?php echo isset($ee_options['net_revenue_setting']) && in_array('shipping', $ee_options['net_revenue_setting']) ? "checked" : "" ?>>
              <label class="form-check-label" for="conv_revnue_shipping">
                <?php esc_html_e("Include Shipping", "enhanced-e-commerce-for-woocommerce-store"); ?>
              </label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input conv_revnue_checkinput" type="checkbox" id="conv_revnue_tax" value="tax" <?php echo isset($ee_options['net_revenue_setting']) && in_array('tax', $ee_options['net_revenue_setting']) ? "checked" : "" ?>>
              <label class="form-check-label" for="conv_revnue_tax">
                <?php esc_html_e("Include Tax", "enhanced-e-commerce-for-woocommerce-store"); ?>
              </label>
            </div>
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
      </div>
      <div class="modal-footer">
        <button type="button" class="btn conv-cancel-btn" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn conv-blue-btn conv-text-white" id="create_gtm_container_btn">Create</button>
      </div>
    </div>
  </div>
</div>
<!-- create container modal -->

<!-- import container modal -->
<div class="modal fade" id="importContainerModal" tabindex="-1" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog importContainerDetail modal-dialog-centered modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="importContainerModalLabel">Import Container</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <!-- <div class="blg-caption preview-confirm-input"> Preview and confirm your import </div> -->
          <div class="col-xl-3 col-lg-6 col-md-12 col-12">
            <div class="card p-0">
              <h5 class="card-header tags-header conv-blue-bg conv-text-white m-0 conv-d-flex conv-justify-space-between fw-bold-600">Tags<span class="material-symbols-outlined conv-font-22">
                  label
                </span></h5>
              <div class="card-body">
                <div class="row">
                  <div class="gtm-container-import-entity-body">
                    <div class="row conv-text-center">
                      <div class="col-md-3 p-0">
                        <div class="blg-subhead tag-count"> </div>
                      </div>
                      <div class="col-md-5 p-0">
                        <div class="blg-subhead ">0 </div>
                      </div>
                      <div class="col-md-4 p-0">
                        <div class="blg-subhead"> 0</div>
                      </div>
                    </div>
                    <div class="row conv-text-center">
                      <div class="col-md-3 p-0">
                        <div class="blg-body blg-spacer1 fw-bold-500"> New </div>
                      </div>
                      <div class="col-md-5 p-0">
                        <div class="blg-body blg-spacer1 fw-bold-500"> Modified </div>
                      </div>
                      <div class="col-md-4 p-0">
                        <div class="blg-body blg-spacer1 fw-bold-500"> Deleted </div>
                      </div>
                    </div>
                    <!-- <div class="gtm-container-import-number-summary">
                    <div class="blg-subhead tag-count"> </div>
                    <div class="blg-body blg-spacer1"> New </div>
                  </div>
                  <div class="gtm-container-import-number-summary">
                    <div class="blg-subhead"> 0 </div>
                    <div class="blg-body blg-spacer1"> Modified </div>
                  </div>
                  <div class="gtm-container-import-number-summary">
                  </div>
                  <div class="blg-subhead"> 0 </div>
                  <div class="blg-body blg-spacer1"> Deleted </div> -->
                  </div>
                </div>
                <div class="row pt-3">
                  <div class="collapse collapseExample">
                    <div class="gtm-container-import-detail" aria-hidden="false">
                      <div class="gtm-container-import-detail-separator conv-b-grey"></div> <!---->
                      <div class="blg-body-med fw-bold-600"> New tags </div>
                      <div class="gtm-container-import-detail-separator conv-b-grey"></div> <!---->
                      <div class="tag-list pt-2">

                      </div>


                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-6 col-md-12 col-12">
            <div class="card p-0">
              <h5 class="card-header triggers-header conv-green-bg conv-text-white m-0 conv-d-flex conv-justify-space-between fw-bold-600">Triggers<span class="material-symbols-outlined conv-font-22">
                  power_settings_new
                </span></h5>
              <div class="card-body">
                <div class="row">
                  <div class="gtm-container-import-entity-body">
                    <div class="row conv-text-center">
                      <div class="col-md-3 p-0">
                        <div class="blg-subhead trigger-count"> </div>
                      </div>
                      <div class="col-md-5 p-0">
                        <div class="blg-subhead">0 </div>
                      </div>
                      <div class="col-md-4 p-0">
                        <div class="blg-subhead"> 0</div>
                      </div>
                    </div>
                    <div class="row conv-text-center">
                      <div class="col-md-3 p-0">
                        <div class="blg-body blg-spacer1 fw-bold-500"> New </div>
                      </div>
                      <div class="col-md-5 p-0">
                        <div class="blg-body blg-spacer1 fw-bold-500"> Modified </div>
                      </div>
                      <div class="col-md-4 p-0">
                        <div class="blg-body blg-spacer1 fw-bold-500"> Deleted </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row pt-3">
                  <div class="collapse collapseExample">
                    <div class="gtm-container-import-detail" aria-hidden="false">
                      <div class="gtm-container-import-detail-separator conv-b-grey"></div> <!---->
                      <div class="blg-body-med fw-bold-600"> New triggers </div>
                      <div class="gtm-container-import-detail-separator conv-b-grey"></div>
                      <div class="trigger-list pt-2">

                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-6 col-md-12 col-12">
            <div class="card p-0">
              <h5 class="card-header variables-header conv-orange-bg conv-text-white m-0 conv-d-flex conv-justify-space-between fw-bold-600">Variables<img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/icon/variables.png'); ?>" /></h5>
              <div class="card-body">
                <div class="row">
                  <div class="gtm-container-import-entity-body">
                    <div class="row conv-text-center">
                      <div class="col-md-3 p-0">
                        <div class="blg-subhead variable-count"> 48 </div>
                      </div>
                      <div class="col-md-5 p-0">
                        <div class="blg-subhead">0 </div>
                      </div>
                      <div class="col-md-4 p-0">
                        <div class="blg-subhead"> 0</div>
                      </div>
                    </div>
                    <div class="row conv-text-center">
                      <div class="col-md-3 p-0">
                        <div class="blg-body blg-spacer1 fw-bold-500"> New </div>
                      </div>
                      <div class="col-md-5 p-0">
                        <div class="blg-body blg-spacer1 fw-bold-500"> Modified </div>
                      </div>
                      <div class="col-md-4 p-0">
                        <div class="blg-body blg-spacer1 fw-bold-500"> Deleted </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row pt-3">
                  <div class="collapse collapseExample">
                    <div class="gtm-container-import-detail" aria-hidden="false">
                      <div class="gtm-container-import-detail-separator conv-b-grey"></div> <!---->
                      <div class="blg-body-med fw-bold-600"> New Variables </div>
                      <div class="gtm-container-import-detail-separator conv-b-grey"></div>
                      <div class="variable-list pt-2">

                      </div>

                    </div>
                  </div>
                </div>


              </div>
            </div>
          </div>
          <div class="col-xl-3 col-lg-6 col-md-12 col-12">
            <div class="card p-0">
              <h5 class="card-header templates-header conv-purple-bg conv-text-white m-0 conv-d-flex conv-justify-space-between fw-bold-600">Templates<span class="material-symbols-outlined conv-font-20">empty_dashboard</span></h5>
              <div class="card-body">
                <div class="row">
                  <div class="gtm-container-import-entity-body">
                    <div class="row conv-text-center">
                      <div class="col-md-3 p-0">
                        <div class="blg-subhead template-count"> 7 </div>
                      </div>
                      <div class="col-md-5 p-0">
                        <div class="blg-subhead"> 0 </div>
                      </div>
                      <div class="col-md-4 p-0">
                        <div class="blg-subhead"> 0 </div>
                      </div>
                    </div>
                    <div class="row conv-text-center">
                      <div class="col-md-3 p-0">
                        <div class="blg-body blg-spacer1 fw-bold-500"> New </div>
                      </div>
                      <div class="col-md-5 p-0">
                        <div class="blg-body blg-spacer1 fw-bold-500"> Modified </div>
                      </div>
                      <div class="col-md-4 p-0">
                        <div class="blg-body blg-spacer1 fw-bold-500"> Deleted </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="row pt-3">
                  <div class="collapse collapseExample">
                    <div class="gtm-container-import-detail" aria-hidden="false">
                      <div class="gtm-container-import-detail-separator conv-b-grey"></div>
                      <div class="blg-body-med fw-bold-600"> New Templates </div>
                      <div class="gtm-container-import-detail-separator conv-b-grey"></div>
                      <div class="customTemplate-list pt-2">

                      </div>
                    </div>
                  </div>
                </div>


              </div>
            </div>
          </div>

        </div>
      </div>
      <div class="modal-footer conv-justify-space-between">
        <a class="fw-bold-600" data-bs-toggle="collapse" id="details-collapse" href=".collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">View Details</a>
        <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button> -->
        <button type="button" class="btn conv-text-white conv-blue-btn" data-bs-dismiss="modal" id="create_container_back_btn">
          <span>
            Back
          </span>
        </button>
      </div>
    </div>
  </div>
</div>
<!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
<script>
  jQuery(function() {
    // set static width to container dropdown to avoid lenght issue when there is no account.
    // jQuery('#gtm_account_container_list').siblings('.select2:first').attr('style', 'width: 312px');
    $('#gtm_account_container_list').select2();
    let plan_id = "<?php echo $plan_id; ?>";
    let gtm_account_id = "<?php echo $gtm_account_id; ?>";
    let gtm_container_id = "<?php echo $gtm_container_id; ?>";
    let gtm_container_public_id = "<?php echo $gtm_container_publicId; ?>";
    let gtm_account_container_name = "<?php echo $gtm_account_container_name; ?>";
    let subscription_id = "<?php echo $tvc_data['subscription_id']; ?>"; //subscription_id  
    let selectedOption = gtm_account_id + '_' + gtm_container_id + '_' + gtm_container_public_id;

    let is_gtm_automatic_process = "<?php echo $is_gtm_automatic_process; ?>"
    let gtm_gmail = "<?php echo $g_gtm_email; ?>";
    if (is_gtm_automatic_process == true || is_gtm_automatic_process == 'true') {
      $('#nav-automatic-tab').click()
    } else {
      if ($('#use_your_gtm_id').val() != '') {
        <?php if (isset($_GET['subscription_id']) && sanitize_text_field($_GET['subscription_id'])) { ?>
          $('#nav-automatic-tab').click()
        <?php } else { ?>
          $('#nav-manual-tab').click()
        <?php } ?>
      }
    }

    // Conversios JS
    jQuery('input[type=radio][name=want_to_use_your_gtm]').change(function() {
      if (this.value == '0') {
        jQuery("#use_your_gtm_id_box").hide();
        jQuery("#use_your_gtm_id_box").addClass('d-none');
        jQuery('.container-section').hide().addClass('d-none');
      } else if (this.value == '1') {
        jQuery("#use_your_gtm_id_box").show();
        jQuery("#use_your_gtm_id_box").removeClass('d-none');
        jQuery('.container-section').show().removeClass('d-none');
      }
    });

    jQuery(document).on('change', 'form#gtmsettings_form', function() {
      var want_to_use_your_gtm = jQuery('input[type=radio][name=want_to_use_your_gtm]:checked').val();
      let want_to_use_your_gtm_default = $("#want_to_use_your_gtm_default").prop("checked");
      var use_your_gtm_id = jQuery('#use_your_gtm_id').val();
      let isAutomatic = ($('#nav-automatic-tab').hasClass('active') && want_to_use_your_gtm && !want_to_use_your_gtm_default) ? true : false
      if (!isAutomatic && want_to_use_your_gtm == 1 && use_your_gtm_id == "") {
        disableSaveBtn()
      } else {
        enableSaveBtn()
      }
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
      // if (jQuery('#use_your_gtm_id').val() == "") {
      //   jQuery('#use_your_gtm_id').addClass("conv-border-danger");
      // }
      // jQuery('#use_your_gtm_id').addClass("conv-border-danger");
      jQuery(".conv-btn-connect").addClass("conv-btn-connect-disabled");
      jQuery(".conv-btn-connect").removeClass("conv-btn-connect-enabled-google");
      jQuery(".conv-btn-connect").text('Save');
    }
    // Enable save button
    function enableSaveBtn() {
      // jQuery('#use_your_gtm_id').removeClass("conv-border-danger");
      jQuery(".conv-btn-connect").removeClass("conv-btn-connect-disabled");
      jQuery(".conv-btn-connect").addClass("conv-btn-connect-enabled-google");
      jQuery(".conv-btn-connect").text('Save');
    }
    jQuery('#use_your_gtm_id').on('keyup keypress', function(e) {
      if ($('#use_your_gtm_id').val() != '') {
        jQuery('#use_your_gtm_id').removeClass("conv-border-danger");
      } else {
        jQuery('#use_your_gtm_id').addClass("conv-border-danger")
      }
    })
    jQuery(document).on("click", ".conv-btn-connect-enabled-google", function() {
      conv_change_loadingbar("show");
      jQuery(this).addClass('disabled');
      var want_to_use_your_gtm = jQuery('input[type=radio][name=want_to_use_your_gtm]:checked').val();
      var use_your_gtm_id = jQuery('#use_your_gtm_id').val();
      var conv_disabled_users_arr = jQuery("#conv_disabled_users").val();
      var conv_disabled_users = conv_disabled_users_arr.length ? conv_disabled_users_arr : [""];
      var conv_selected_events_arr = {
        ga: jQuery("#ga_selected_events").val()
      };

      var conv_selected_events = conv_selected_events_arr;

      var tracking_method = jQuery('#tracking_method').val();
      let gtmIds
      let account_id
      let container_id
      let container_publicId
      let gtm_account_container_name


      gtmIds = $('#gtm_account_container_list').val().split('_');
      gtm_account_container_name = $("#gtm_account_container_list option:selected").text();

      if (gtmIds.length > 2) {
        account_id = gtmIds[0] // account id
        container_id = gtmIds[1] // container id
        container_publicId = gtmIds[2] // container public id

        $('#hidden_gtm_account_id').val(account_id);
        $('#hidden_gtm_container_id').val(container_id);
        $('#hidden_gtm_container_publicId').val(container_publicId);

      }

      let want_to_use_your_gtm_default = $("#want_to_use_your_gtm_default").prop("checked");
      let isAutomatic = ($('#nav-automatic-tab').hasClass('active') && want_to_use_your_gtm && !want_to_use_your_gtm_default) ? true : false;
      use_your_gtm_id = (isAutomatic == true || isAutomatic == 'true') ? container_publicId : use_your_gtm_id;

      var net_revenue_setting = [];
      jQuery(".conv_revnue_checkinput").each(function() {
        if (jQuery(this).is(":checked")) {
          net_revenue_setting.push(jQuery(this).val());
        }
      });

      jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: tvc_ajax_url,
        data: {
          action: "conv_save_pixel_data",
          pix_sav_nonce: "<?php echo wp_create_nonce('pix_sav_nonce_val'); ?>",
          conv_options_data: {
            want_to_use_your_gtm: want_to_use_your_gtm,
            use_your_gtm_id: use_your_gtm_id,
            conv_disabled_users: conv_disabled_users,
            conv_selected_events: conv_selected_events,
            tracking_method: tracking_method,
            subscription_id: "<?php echo $tvc_data['subscription_id']; ?>",
            net_revenue_setting: net_revenue_setting,
            gtm_settings: {
              gtm_account_id: account_id,
              gtm_container_id: container_id,
              gtm_public_id: container_publicId,
              is_gtm_automatic_process: isAutomatic,
              gtm_account_container_name: gtm_account_container_name
            }
          },
          conv_options_type: ["eeoptions", "eeapidata", "middleware", "eeselectedevents"],
        },
        beforeSend: function() {
          jQuery(".conv-btn-connect-enabled-google").text("Saving...");
        },
        success: function(response) {
          /* run gtm automation */
          if (isAutomatic) {
            runGtmAutomation()
          }
          /* run gtm automation end */
          var user_modal_txt = "Conversios Container - GTM-K7X94DG";
          if (want_to_use_your_gtm == "1") {
            user_modal_txt = "Your own GTM Container - " + use_your_gtm_id;
          }
          if (response == "0" || response == "1") {
            jQuery(".conv-btn-connect-enabled-google").text("Connect");
            jQuery("#conv_save_success_txt").html('Congratulations, you have successfully connected your <br> Google Tag Manager account with <br> ' + user_modal_txt);
            jQuery("#conv_save_success_modal").modal("show");
            conv_change_loadingbar("hide");
          }

        }
      });
    });


    <?php if (isset($_GET['subscription_id']) && sanitize_text_field($_GET['subscription_id'])) { ?>
      // subscription_id = $_GET['subscription_id'];
      getGtmAccountWithContainer();
    <?php } ?>

    // get List of Account and associated container 
    if (gtm_gmail != "") { // check if user is authenticated
      if (gtm_account_id != '' && gtm_container_id != '' && gtm_container_public_id != '') {
        getGtmAccountWithContainerDB(selectedOption, gtm_account_container_name);
      }
    }



    function getGtmAccountWithContainer(isDisabled = false) {
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

            let accountContainerEle = $('#gtm_account_container_list');
            accountContainerEle.select2();
            // remove previous options  from container dropdown
            accountContainerEle
              .find('option')
              .remove()
              .end()
              .append('<option value="">Select Container</option>')
              .val('')

            let data = response.data;

            if ($('#nav-automatic-tab').hasClass('active')) {
              if (data.length == 0) {
                getAlertMessage('info', 'Error', message = 'There is no GTM account associated with email address which you authenticated. Please create GTM Account or Sign in with different account.', icon = 'info', buttonText = 'Try again', buttonColor = '#FCCB1E', iconImageSrc = '<img src="<?= esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_error_logo.png'); ?>"/ >')
                // Swal.fire({
                //   icon: 'info',
                //   title: 'Error',
                //   confirmButtonText: 'Go Back',
                //   confirmButtonColor: '#FCCB1E',
                //   text: "There is no GTM account associated with email address which you authenticated.",
                // })
              }
            }

            jQuery.each(data, function(key, value) {
              // accountData = $data
              jQuery.each(value, function(aKey, aValue) {
                jQuery.each(aValue['container'], function(cKey, cValue) {
                  let text = aValue['name'];
                  let accountId = aValue['accountId'];
                  let containerId = cValue['containerId'];
                  let containerPublicId = cValue['publicId'];
                  let id = accountId + '_' + containerId + '_' + containerPublicId
                  text += '-' + containerPublicId
                  let option = $("<option/>", {
                    value: id,
                    text: text
                  });
                  accountContainerEle.append(option);
                })
              })
            })

            $('#gtm_account_container_list').val('').trigger('change');

            // check if selected container exists in list of containers 
            $("#gtm_account_container_list > option").each(function() {
              if (selectedOption == this.value) {
                if (selectedOption.split('_').length > 2 && selectedOption.split('_')[0] != "" && selectedOption.split('_')[1] != "" && selectedOption.split('_')[2] != "") {
                  $('#gtm_account_container_list').val(selectedOption).trigger('change');
                }
              }
            });

            $('#gtm_account_container_list').attr('disabled', isDisabled);
            if (!isDisabled) {
              $('#editContainerDropDown').parent().addClass('d-none');
              $('.create-container-link').addClass('col-md-12');
            }
          } else {
            var errors = JSON.parse(response.errors)

            if (errors['subscription_id'] != undefined && errors['subscription_id'].length > 0) {
              // Swal.fire({
              //   icon: 'error',
              //   // title: 'Oops...',
              //   text: errors['subscription_id'][0],
              // })
              getAlertMessage('error', 'Error', message = errors['subscription_id'][0], icon = 'error', buttonText = 'Try again', buttonColor = '#FCCB1E', iconImageSrc = '<img src="<?= esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_error_logo.png'); ?>"/ >')
            }
            // Swal.fire({
            //   icon: 'error',
            //   title: 'Oops...',
            //   text: 'Something went wrong',
            // })
          }
        }
      });
    }

    function getGtmAccountWithContainerDB(selectedOption = '', selectedContainerName = '') {
      let accountContainerEle = $('#gtm_account_container_list');
      accountContainerEle.select2();
      accountContainerEle
        .find('option')
        .remove()
        .end()
        .append('<option value="">Select Container</option>')
        .val('')

      let data = {
        id: selectedOption,
        text: selectedContainerName
      };
      let newOption = new Option(data.text, data.id, true, true);
      accountContainerEle.append(newOption).trigger('change');
      disableSaveBtn()

    }

    /*
     *create new container in selected gtm account 
     */
    jQuery(document).on('click', "#create_gtm_container_btn", function(e) {
      e.preventDefault();
      if ($('#gtm_account_list').val() != '') {
        let gtm_create_container_nonce = "<?php echo wp_create_nonce('gtm_create_container_nonce'); ?>";
        let account_id = parseInt($('#gtm_account_list').val());


        let gtm_data = {
          subscription_id: subscription_id, //subscription_id  
          gtm_create_container_nonce: gtm_create_container_nonce,
          account_id: account_id, // account_id 
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

              let data = {
                id: account_id + '_' + response.data.containerId + '_' + response.data.publicId,
                text: response.data.name + '-' + response.data.publicId
              };
              let newOption = new Option(data.text, data.id, true, true);
              $('#gtm_account_container_list').append(newOption).trigger('change');
              // getGtmAccountWithContainer()
              // jQuery("#conv_container_success_modal").modal("show");
              selectedOption = data.id
              enableSaveBtn();
              $('.conv-btn-connect-enabled-google').click()
            } else {
              getAlertMessage('error', 'Error', message = 'Conversios Container already exist in your account.', icon = 'error', buttonText = 'Try again', buttonColor = '#FCCB1E', iconImageSrc = '<img src="<?= esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/conv_error_logo.png'); ?>"/ >')
            }
          }
        });

      }

    })

    // run gtm automatiuon api to import container data into gtm account/container
    function runGtmAutomation() {

      let gtm_run_gtm_automation_nonce = "<?php echo wp_create_nonce('gtm_run_gtm_automation_nonce'); ?>";

      let account_id = $('#hidden_gtm_account_id').val();
      let subscription_id = "<?php echo $tvc_data['subscription_id']; ?>";
      let container_id = $('#hidden_gtm_container_id').val()
      let workspace_id = 2 // 2 is the default workspace id for the container

      let gtm_data = {
        account_id: account_id, // account_id 
        container_id: container_id,
        subscription_id: subscription_id, //subscription_id  
        workspace_id: workspace_id,
        action: "conv_run_gtm_automation",
        gtm_run_gtm_automation_nonce: gtm_run_gtm_automation_nonce
      }

      jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: tvc_ajax_url,
        data: gtm_data,
        beforeSend: function() {

        },
        success: function(response) {
          console.log('runAtomation Success', response)

          if (response.status == 200) {

          } else {

          }
        }
      });
    }

    $('#create_container_link').on('click', function(e) {
      jQuery('#gtm_account_list').select2({
        dropdownParent: $("#createContainerModal")
      });
      $('#createContainerModal').modal('show');
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


            jQuery.each(data, function(key, value) {
              jQuery.each(value, function(aKey, aValue) {
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

    getGlobalContainerJson()

    function getGlobalContainerJson() {

      let gtm_global_container_json_nonce = "<?php echo wp_create_nonce('gtm_global_container_json_nonce'); ?>";
      let gtm_data = {
        action: "conv_get_global_container_json",
        gtm_global_container_json_nonce: gtm_global_container_json_nonce
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

            createDetailViewPageData(response.data.containerVersion)

          } else {

          }
        }
      });

    }

    function createDetailViewPageData(containerData) {
      let tagHtml = ''
      jQuery.each(containerData.tag, function(key, value) {

        tagHtml += `<div ng-repeat="entityName in ctrl.newEntities"> ${value.name} </div>`
      });
      $('.tag-list').append(tagHtml);
      $('.tag-count').text(containerData.tag.length)
      let triggerHtml = ''
      jQuery.each(containerData.trigger, function(key, value) {

        triggerHtml += `<div ng-repeat="entityName in ctrl.newEntities"> ${value.name} </div>`
      });
      $('.trigger-list').append(triggerHtml);
      $('.trigger-count').text(containerData.trigger.length)
      let variableHtml = ''
      jQuery.each(containerData.variable, function(key, value) {

        variableHtml += `<div ng-repeat="entityName in ctrl.newEntities"> ${value.name} </div>`
      });
      $('.variable-list').append(variableHtml);
      $('.variable-count').text(containerData.variable.length)
      let customTemplateHtml = ''
      jQuery.each(containerData.customTemplate, function(key, value) {

        customTemplateHtml += `<div ng-repeat="entityName in ctrl.newEntities"> ${value.name} </div>`
      });
      $('.customTemplate-list').append(customTemplateHtml);
      $('.template-count').text(containerData.customTemplate.length)
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
      $('form#gtmsettings_form').change();
    })

    $('#editContainerDropDown').on('click', function(e) {
      e.preventDefault();
      $('#editContainerDropDown').parent().addClass('d-none');
      $('.create-container-link').addClass('col-md-12');

      getGtmAccountWithContainer(false)
      // $('#gtm_account_container_list').attr('disabled', false);
    })

    $('#gtm_account_list').on('change', function() {
      $('.conv-error').addClass('d-none')
    })
    $('#want_to_use_your_gtm_default').on('change', function() {
      setTimeout(() => {
        enableSaveBtn();
      }, 300)
    });

    function getAlertMessage(type = 'Success', title = 'Success', message = '', icon = 'success', buttonText = 'Ok, Done', buttonColor = '#1085F1', iconImageTag = '') {

      Swal.fire({
        type: type,
        icon: icon,
        title: title,
        confirmButtonText: buttonText,
        confirmButtonColor: buttonColor,
        text: message,
      })
      let swalContainer = Swal.getContainer();
      $(swalContainer).find('.swal2-icon-show').removeClass('swal2-' + icon).removeClass('swal2-icon')
      $('.swal2-icon-show').html(iconImageTag)

    }

  });
</script>