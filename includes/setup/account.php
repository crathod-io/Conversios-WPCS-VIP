<?php
class TVC_Account
{
  protected $TVC_Admin_Helper = "";
  protected $url = "";
  protected $subscriptionId = "";
  protected $google_detail;
  protected $customApiObj;
  protected $ee_options;
  public function __construct()
  {
    $this->TVC_Admin_Helper = new TVC_Admin_Helper();
    $this->customApiObj = new CustomApi();
    $this->subscriptionId = $this->TVC_Admin_Helper->get_subscriptionId();
    $this->google_detail = $this->TVC_Admin_Helper->get_ee_options_data();
    $this->TVC_Admin_Helper->add_spinner_html();
    $this->ee_options = unserialize(get_option('ee_options'));
    $this->create_form();
    $this->aioconv_settings_modal();
    $this->current_js_aioconvsettings();
  }

  public function create_form()
  {
    $message = "";
    $class = "";
    $googleDetail = [];
    $plan_id = 1;
    $plan_name =  esc_html__("Free Plan", "enhanced-e-commerce-for-woocommerce-store");
    $plan_price = esc_html__("Free", "enhanced-e-commerce-for-woocommerce-store");
    $api_licence_key = "";
    $paypal_subscr_id = "";
    $product_sync_max_limit = "100";
    $activation_date = "";
    $next_payment_date = "";
    //$subscription_type = "";
    if (isset($this->google_detail['setting'])) {
      if ($this->google_detail['setting']) {
        $googleDetail = $this->google_detail['setting'];
        if (isset($googleDetail->plan_id) && !in_array($googleDetail->plan_id, array("1"))) {
          $plan_id = $googleDetail->plan_id;
        }
        if (isset($googleDetail->licence_key) && !in_array($googleDetail->plan_id, array("1"))) {
          $api_licence_key = $googleDetail->licence_key;
        }
        if (isset($googleDetail->plan_name) && !in_array($googleDetail->plan_id, array("1"))) {
          $plan_name = $googleDetail->plan_name;
        }
        if (isset($googleDetail->price) && !in_array($googleDetail->plan_id, array("1"))) {
          $plan_price = $googleDetail->price . " USD";
        }
        if (isset($googleDetail->paypal_subscr_id) && !in_array($googleDetail->plan_id, array("1"))) {
          $paypal_subscr_id = $googleDetail->paypal_subscr_id;
        }
        if (isset($googleDetail->max_limit)) {
          $product_sync_max_limit = $googleDetail->max_limit;
          if (in_array($plan_id, array("7", "8"))) {
            $product_sync_max_limit = esc_html__("Unlimited", "enhanced-e-commerce-for-woocommerce-store");
          }
        }
        if (isset($googleDetail->subscription_activation_date) && !in_array($googleDetail->plan_id, array("1"))) {
          $activation_date = $googleDetail->subscription_activation_date;
        }
        if (isset($googleDetail->subscription_expiry_date) && !in_array($googleDetail->plan_id, array("1"))) {
          $next_payment_date = $googleDetail->subscription_expiry_date;
        }
      }
    }
?>
    <div class="con-tab-content">
      <?php if ($message) {
        printf('<div class="%1$s"><div class="alert">%2$s</div></div>', esc_attr($class), esc_html($message));
      } ?>
      <div class="tab-pane show active" id="tvc-account-page">
        <div class="tab-card">
          <div class="row">
            <div class="col-md-10 col-lg-10 border-right">

              <div class="licence tvc-licence">
                <div class="tvc_licence_key_wapper <?php if ($plan_id != 1) { ?>tvc-hide<?php } ?>">
                  <?php if ($plan_id == 1) { ?>
                    <p><?php esc_html_e("You are using our free plugin, no licence needed ! Happy analyzing..!! :)", "enhanced-e-commerce-for-woocommerce-store"); ?></p>
                    <p class="font-weight-bold"><?php esc_html_e("To unlock more features of google products, consider our", "enhanced-e-commerce-for-woocommerce-store"); ?> <a href="<?php echo esc_url_raw($this->TVC_Admin_Helper->get_pro_plan_site() . '?utm_source=EE+Plugin+User+Interface&utm_medium=Account+Summary+pro+version&utm_campaign=Upsell+at+Conversios'); ?>" target="_blank"><?php esc_html_e("pro version.", "enhanced-e-commerce-for-woocommerce-store"); ?></a></p>
                  <?php } ?>
                  <form method="post" name="google-analytic" id="tvc-licence-active">
                    <div class="input-group">
                      <input type="text" id="licence_key" name="licence_key" class="form-control" placeholder="<?php esc_html_e("Already purchased? Enter licence key", "enhanced-e-commerce-for-woocommerce-store"); ?>" required="">
                      <div class="input-group-append">
                        <button type="submit" class="btn btn-primary" name="verify-licence-key"><img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/icon/right-arrow.svg'); ?>" alt="active licence key"></button>
                      </div>
                    </div>
                  </form>
                </div>
                <div class="google-account-analytics tvc_licence_key_change_wapper <?php if ($plan_id == 1) { ?>tvc-hide<?php } ?>">
                  <div class="acc-num">
                    <label class="ga-title tvc_licence_key_title"><?php esc_html_e("Licence key:", "enhanced-e-commerce-for-woocommerce-store"); ?></label>
                    <p class="ga-text tvc_licence_key"><?php echo esc_attr($api_licence_key); ?></p>
                    <p class="ga-text text-right tvc_licence_key_change"><img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/icon/refresh.svg'); ?>" alt="active licence key"></p>
                  </div>
                </div>
              </div>

              <div class="tvc-table">
                <strong><?php esc_html_e("Account Summary", "enhanced-e-commerce-for-woocommerce-store"); ?></strong>
                <table>
                  <tbody>
                    <tr>
                      <th><?php esc_html_e("Plan name", "enhanced-e-commerce-for-woocommerce-store"); ?></th>
                      <td><?php echo esc_attr($plan_name); ?></td>
                    </tr>
                    <tr>
                      <th><?php esc_html_e("Plan price", "enhanced-e-commerce-for-woocommerce-store"); ?></th>
                      <td><?php echo esc_attr($plan_price); ?></td>
                    </tr>
                    <tr>
                      <th><?php esc_html_e("Product sync limit", "enhanced-e-commerce-for-woocommerce-store"); ?></th>
                      <td><?php echo esc_attr($product_sync_max_limit); ?></td>
                    </tr>
                    <?php if ($plan_id != 1) { ?>
                      <tr>
                        <th><?php esc_html_e("Active licence key", "enhanced-e-commerce-for-woocommerce-store"); ?></th>
                        <td><?php echo esc_attr($api_licence_key); ?></td>
                      </tr>
                      <tr>
                        <th><?php esc_html_e("PayPal subscription id", "enhanced-e-commerce-for-woocommerce-store"); ?></th>
                        <td><?php echo esc_attr($paypal_subscr_id); ?></td>
                      </tr>
                      <tr>
                        <th><?php esc_html_e("Last bill date", "enhanced-e-commerce-for-woocommerce-store"); ?></th>
                        <td><?php echo esc_attr($activation_date); ?></td>
                      </tr>
                      <tr>
                        <th><?php esc_html_e("Expected bill date", "enhanced-e-commerce-for-woocommerce-store"); ?></th>
                        <td><?php echo esc_attr($next_payment_date); ?></td>
                      </tr>
                    <?php } ?>

                    <tr>
                      <th>
                        <?php esc_html_e("Refresh subscription details", "enhanced-e-commerce-for-woocommerce-store"); ?></th>
                      <td>
                        <button id="convrefreshsubscription" type="button" class="btn conv-btn-connect-enabled text-white d-flex m-auto justify-content-center">
                          <span class="material-symbols-outlined pe-2">
                            refresh
                          </span>
                          <?php esc_html_e("Refresh Subscription Details", "enhanced-e-commerce-for-woocommerce-store"); ?></th>
                        </button>
                      </td>
                    </tr>

                    <?php if (current_user_can('administrator')) { ?>
                      <tr>
                        <th>
                          <?php esc_html_e("User Access Management", "enhanced-e-commerce-for-woocommerce-store"); ?></th>
                        <td>
                          <button data-bs-toggle="modal" data-bs-target="#pixelsettingsmodal" class="btn conv-btn-connect-enabled text-white d-flex m-auto justify-content-center">
                            <i class="material-symbols-outlined pe-2">
                              settings
                            </i>
                            <?php esc_html_e("Settings", "enhanced-e-commerce-for-woocommerce-store"); ?></th>
                          </button>

                        </td>
                      </tr>
                    <?php } ?>

                  </tbody>
                </table>
              </div>
            </div>
            <div class="col-md-6 col-lg-4"></div>
          </div>
        </div>
      </div>
    </div>
    <?php echo get_connect_google_popup_html_to_active_licence(); ?>
    <script>
      jQuery(document).ready(function() {
        jQuery(document).on('click', '#tvc_google_connect_active_licence_close', function(event) {
          jQuery('#tvc_google_connect_active_licence').modal('hide');
        });
        jQuery(document).on('click', '.tvc_licence_key_change', function(event) {
          jQuery(".tvc_licence_key_change_wapper").slideUp(500);
          jQuery(".tvc_licence_key_wapper").slideDown(700);
        });
        jQuery(document).on('submit', 'form#tvc-licence-active', function(event) {
          event.preventDefault();
          let licence_key = jQuery("#licence_key").val();
          var form_data = jQuery("#tvc-licence-active").serialize();
          if (licence_key != "") {
            var data = {
              action: "tvc_call_active_licence",
              licence_key: licence_key,
              conv_licence_nonce: "<?php echo wp_create_nonce('conv_lic_nonce'); ?>"
            };
            jQuery.ajax({
              type: "POST",
              dataType: "json",
              url: tvc_ajax_url,
              data: data,
              beforeSend: function() {
                tvc_helper.loaderSection(true);
              },
              success: function(response) {
                if (response.error === false) {
                  tvc_helper.tvc_alert("success", "", response.message);
                  setTimeout(function() {
                    location.reload();
                  }, 2000);
                } else {
                  if (response.is_connect == false) {
                    jQuery('#tvc_google_connect_active_licence').modal('show');
                  } else {
                    tvc_helper.tvc_alert("error", "", response.message);
                  }
                }
                tvc_helper.loaderSection(false);
              }
            });
          } else {
            tvc_helper.tvc_alert("error", "Licence key is required.");
          }
        });


        jQuery("#convrefreshsubscription").click(function() {
          var data = {
            action: "conv_call_subscription_refresh",
            conv_licence_nonce: "<?php echo wp_create_nonce('conv_lic_nonce'); ?>"
          };
          jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: data,
            beforeSend: function() {
              tvc_helper.loaderSection(true);
            },
            success: function(response) {
              setTimeout(function() {
                location.reload();
              }, 2000);
            }
          });
        });


      });
    </script>
  <?php
  }

  public function aioconv_settings_modal()
  { ?>
    <!-- Pixel Settings Modal Start -->
    <div class="modal fade pixelsettingsmodal" id="pixelsettingsmodal" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title" id="exampleModalLabel">
              <?php esc_html_e("User Access Management", "enhanced-e-commerce-for-woocommerce-store"); ?>
            </h3>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form>
              <div class="mb-3">
                <label for="recipient-name" class="col-form-label">
                  <b><?php esc_html_e("Plugin Access Permission:", "enhanced-e-commerce-for-woocommerce-store"); ?></b>
                  <small>
                    <?php esc_html_e("(Select user roles to give access of the Conversios plugin)", "enhanced-e-commerce-for-woocommerce-store"); ?>
                  </small>
                </label>

                <select class="form-select mb-3 selecttwomodal w-100" id="conv_permitted_users" name="conv_permitted_users[]" multiple="multiple" data-placeholder="Select role">
                  <?php foreach ($this->TVC_Admin_Helper->conv_get_user_roles() as $slug => $name) {
                    if ($slug == 'administrator') {
                      continue;
                    }
                    $is_selected = "";
                    if ((!empty($this->ee_options['conv_permitted_users']) && in_array($slug, $this->ee_options['conv_permitted_users'])) || $slug == 'administrator') {
                      $is_selected =  "selected";
                    }
                  ?>
                    <option value="<?php echo esc_attr($slug); ?>" <?php echo $is_selected; ?>><?php echo esc_attr($name); ?></option>
                  <?php } ?>
                </select>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button id="saveaioconvsettings" type="button" class="btn conv-btn-connect-enabled text-white">Save changes</button>
          </div>
        </div>
      </div>
    </div>
    <!-- Pixel Settings Modal End -->
  <?php
  }

  public function current_js_aioconvsettings()
  { ?>
    <script>
      jQuery(function() {
        jQuery(".selecttwomodal").select2({
          minimumResultsForSearch: -1,
          dropdownParent: jQuery('#pixelsettingsmodal'),
          placeholder: function() {
            jQuery(this).data('placeholder');
          }
        });

        // jQuery("#conv_permitted_users").change(function() {
        //   console.log(jQuery(this).val().length);
        //   if (jQuery(this).val().length > 0) {
        //     jQuery('#saveaioconvsettings').removeClass('disabled');
        //   } else {
        //     jQuery('#saveaioconvsettings').addClass('disabled');
        //   }
        // });

        jQuery('#saveaioconvsettings').click(function() {
          var selected_vals = {};
          selected_vals["conv_permitted_users"] = jQuery('#conv_permitted_users').val();
          selected_vals["conv_permitted_users"].push('administrator');
          console.log(selected_vals["conv_permitted_users"]);
          jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: {
              action: "conv_save_pixel_data",
              pix_sav_nonce: "<?php echo wp_create_nonce('pix_sav_nonce_val'); ?>",
              conv_options_data: selected_vals,
              conv_options_type: ["eeoptions", "permituserrole"],
            },
            beforeSend: function() {
              jQuery("#saveaioconvsettings").text("Saving...");
            },
            success: function(response) {
              jQuery("#saveaioconvsettings").text("Save Changes");
              jQuery(".modal").modal('hide');
            }
          });
        });

      });
    </script>
<?php }
}
?>