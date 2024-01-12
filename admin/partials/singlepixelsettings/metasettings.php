<?php
$is_sel_disable = 'disabled';
$google_merchant_center_id = (isset($googleDetail->google_merchant_center_id) && $googleDetail->google_merchant_center_id != "") ? $googleDetail->google_merchant_center_id : "";
?>
<div class="convcard p-4 mt-0 rounded-3 shadow-sm mt-3">

    <div class="alert d-flex align-items-cente p-0" role="alert">
        <span class="p-2 material-symbols-outlined text-light conv-success-bg rounded-start">info</span>
        <div class="p-2 w-100 rounded-end border border-start-0 shadow-sm conv-notification-alert lh-lg">
            <?php esc_html_e("Meta Business Account", "enhanced-e-commerce-for-woocommerce-store"); ?>
        </div>
    </div>
    <div class="alert d-flex align-items-cente p-0">
        <div class="convpixsetting-inner-box">
            <h4 class="fw-normal mb-1">
                <?php esc_html_e("Meta Business Account", "enhanced-e-commerce-for-woocommerce-store"); ?>
            </h4>
            <span>
                <?php echo (isset($tvc_data['g_mail']) && esc_attr($subscriptionId)) ? esc_attr($tvc_data['g_mail']) : ""; ?>
                <span class="conv-link-blue ps-2 tvc_google_signinbtn">
                    <a href="#">
                        <?php esc_html_e("Change", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </a>
                </span>
            </span>
        </div>
    </div>

    <form id="gmcsetings_form" class="convpixsetting-inner-box mt-4">
        <div id="analytics_box_UA" class="py-1 row">
            <div class="col-5">
                <label class="text-dark">
                    <?php esc_html_e("Facebook Business ID", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </label>
                <div class="pt-2 conv-metasettings">
                    <div class="col-12">
                        <select id="fb_business_id" name="fb_business_id" acctype="FB"
                            class="form-select form-select-lg mb-3 selecttwo" style="width: 100%" <?php echo esc_attr($is_sel_disable); ?>>
                            <?php if (!empty($google_merchant_center_id)) { ?>
                                <!--<option value="<?php // echo esc_attr($google_merchant_center_id); ?>" selected><? php // echo esc_attr($google_merchant_center_id); ?></option>-->
                            <?php } ?>
                            <option value="">Select FB Business ID</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-5">
                <label class="text-dark">
                    <?php esc_html_e("Facebook Catalog ID", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </label>
                <div class="pt-2 conv-metasettings">
                    <div class="col-12">
                        <select id="fb_catalog_id" name="fb_catalog_id" acctype="FB"
                            class="form-select form-select-lg mb-3 selecttwo" style="width: 100%" <?php echo esc_attr($is_sel_disable); ?>>
                            <?php if (!empty($google_merchant_center_id)) { ?>
                                <!--<option value="<?php //echo esc_attr($google_merchant_center_id); ?>" selected><?php //echo esc_attr($google_merchant_center_id); ?></option>-->
                            <?php } ?>
                            <option value="">Select FB Catalog ID</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="col-2">
            <div class="conv-enable-selection text-primary conv-enable-selection pt-4-5">
                    <span class="material-symbols-outlined">edit</span><label class="mb-2 fs-6 text">Edit</label>
                </div>
            </div>
        </div>
    </form>

</div>
<div class="convcard p-4 mt-0 rounded-top shadow-sm mt-3">
    <div class="d-flex mt-0">
        <span class="material-symbols-outlined text-dark">
            campaign
        </span>
        <h6 class="m-0 fw-normal ms-2">
            <?php esc_html_e("Benefits of using your own GTM", "enhanced-e-commerce-for-woocommerce-store"); ?>
        </h6>
    </div>
    <div class="d-flex mt-3">
        <label class="fs-5">
            <?php esc_html_e("Simplify your tag management with features like workspaces, tag templates, and so much more.", "enhanced-e-commerce-for-woocommerce-store"); ?>
        </label>
    </div>
    <div class="d-flex mt-3">
        <ul class="list-unstyled unorder-list">
            <li>Show Dynamic Ads</li>
            <li>Tag your products on Instagram</li>
            <li>Add products to your Facebook Shop</li>
            <li>Dynamic remarketing</li>
            <li>Create Collection Ads</li>
        </ul>
    </div>
</div>
<div class="convcard p-2 rounded-bottom shadow-sm border col-md-12">
    <div class="row">
        <div class="text-end col-md-6 p-0">
            <h6 class="fw-normal text-primary">
                <?php esc_html_e("Upgrade to Pro", "enhanced-e-commerce-for-woocommerce-store"); ?>
            </h6>
        </div>
        <div class="col-md-2 text-primary float-start">
            <span class="material-symbols-outlined">
                arrow_forward
            </span>
        </div>
    </div>
</div>
<script>
    /**
     * Get Google Merchant Center List
     */
    function list_google_merchant_account(tvc_data, selelement) {
        var selectedValue = '<?php echo $google_merchant_center_id ?>';
        var conversios_onboarding_nonce = "<?php echo wp_create_nonce('conversios_onboarding_nonce'); ?>";
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: { action: "list_google_merchant_account", tvc_data: tvc_data, conversios_onboarding_nonce: conversios_onboarding_nonce },
            success: function (response) {
                var btn_cam = 'gmc_list';
                if (response.error === false) {
                    var error_msg = 'null';
                    jQuery('#google_merchant_center_id').empty();
                    jQuery('#google_merchant_center_id').append(jQuery('<option>', { value: "", text: "Select Google Merchant Center" }));
                    if (response.data.length > 0) {
                        jQuery.each(response.data, function (key, value) {
                            if (selectedValue == value.account_id) {
                                jQuery('#google_merchant_center_id').append(jQuery('<option>', { value: value.account_id, "data-merchant_id": value.merchant_id, text: value.account_id, selected: "selected" }));
                            } else {
                                if (selectedValue == "" && key == 0) {
                                    jQuery('#google_merchant_center_id').append(jQuery('<option>', { value: value.account_id, "data-merchant_id": value.merchant_id, text: value.account_id, selected: "selected" }));
                                } else {
                                    jQuery('#google_merchant_center_id').append(jQuery('<option>', { value: value.account_id, "data-merchant_id": value.merchant_id, text: value.account_id, }));
                                }
                            }
                        });
                        jQuery('#tvc-gmc-acc-edit').hide();
                    } else {
                        add_message("error", "There are no Google merchant center accounts associated with email.");
                    }

                } else {
                    var error_msg = response.errors;
                    add_message("error", "There are no Google merchant center accounts associated with email.");
                }
                setTimeout(function () {
                }, 2000);
                jQuery('#google_merchant_center_id').prop('disabled', false);
            }
        });
    }


    //Onload functions
    jQuery(function () {
        var tvc_data = "<?php echo esc_js(wp_json_encode($tvc_data)); ?>";
        var tvc_ajax_url = '<?php echo esc_url_raw(admin_url('admin-ajax.php')); ?>';
        let subscription_id = "<?php echo esc_attr($subscriptionId); ?>";
        let plan_id = "<?php echo esc_attr($plan_id); ?>";
        let app_id = "<?php echo esc_attr($app_id); ?>";
        let bagdeVal = "yes";
        let convBadgeVal = "<?php echo esc_attr($convBadgeVal); ?>";
        let google_merchant_center_id = "<?php echo esc_attr($google_merchant_center_id); ?>";

        if (google_merchant_center_id != '') {
            jQuery(".conv-btn-connect").removeClass("conv-btn-connect-disabled");
            jQuery(".conv-btn-connect").addClass("conv-btn-connect-enabled");
            jQuery(".conv-btn-connect").text('Disconnect');
        }

        jQuery(".selecttwo").select2({
            placeholder: function () {
                jQuery(this).data('placeholder');
            }
        });

        jQuery(".conv-enable-selection").click(function () {
            jQuery(".conv-enable-selection").addClass('hidden');
            var selele = jQuery(".conv-enable-selection").closest(".conv-metasettings").find("select.fb_business_id");
            var currele = jQuery(this).closest(".conv-metasettings").find("select.fb_business_id");
            list_google_merchant_account(tvc_data, selele);
        });


        jQuery(document).on("change", "form#gmcsetings_form", function () {
            jQuery(".conv-btn-connect").removeClass("conv-btn-connect-disabled");
            jQuery(".conv-btn-connect").addClass("btn-primary");
            jQuery(".conv-btn-connect").text('Connect');
        });

        // Save data
        jQuery(document).on("click", ".conv-btn-connect-enabled", function () {
            var tracking_option = jQuery('input[type=radio][name=tracking_option]:checked').val();
            var box_id = "#analytics_box_" + tracking_option;
            var has_error = 0;
            var selected_vals = new Array();
            selected_vals["ua_analytic_account_id"] = "";
            selected_vals["property_id"] = "";
            selected_vals["ga4_analytic_account_id"] = "";
            selected_vals["measurement_id"] = "";

            //= {ua_analytic_account_id: "", property_id: "", ga4_analytic_account_id: "", measurement_id: ""};
            jQuery(box_id).find("select").each(function () {
                if (!jQuery(this).val() || jQuery(this).val() == "" || jQuery(this).val() == "undefined") {
                    has_error = 1;
                    return;
                } else {
                    selected_vals[jQuery(this).attr('name')] = jQuery(this).val();
                }
            });
            selected_vals["tracking_option"] = tracking_option;
            if (has_error == 1) {
                alert("Please select.");
            } else {
                jQuery.ajax({
                    type: "POST",
                    dataType: "json",
                    url: tvc_ajax_url,
                    data: {
                        action: "conv_save_pixel_data",
                        pix_sav_nonce: "<?php echo wp_create_nonce('pix_sav_nonce_val'); ?>",
                        conv_options_data: JSON.stringify(selected_vals),
                    },
                    beforeSend: function () {
                        jQuery(".conv-btn-connect-enabled").text("Saving...");
                    },
                    success: function (response) {
                        var user_modal_txt = "Conversios Container - GTM-K7X94DG";
                        if (want_to_use_your_gtm == "1") {
                            user_modal_txt = "Your own GTM Container - " + use_your_gtm_id;
                        }
                        if (response == "0" || response == "1") {
                            jQuery(".conv-btn-connect-enabled").text("Connect");
                            jQuery("#conv_save_success_txt").html('Congratulations, you have successfully connected your <br> Google Tag Manager account with <br> ' + user_modal_txt);
                            jQuery("#conv_save_success_modal").modal("show");
                        }

                    }
                });
            }

        });

    });
</script>

<?php
// echo '<pre>--tvc_data---';
// print_r($tvc_data);
// echo '</pre>';


// echo '<pre>--Additional data--';
// print_r($googleDetail);
// echo '</pre>';


// echo '<pre>--ee options--';
// print_r($ee_options);
// echo '</pre>';

// echo '<pre>--Google Details--';
// print_r($googleDetail);
// echo '</pre>';
?>