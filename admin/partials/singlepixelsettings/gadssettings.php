<?php
$is_sel_disable = 'disabled';
$cust_g_email =  (isset($tvc_data['g_mail']) && esc_attr($subscriptionId)) ? esc_attr($tvc_data['g_mail']) : "";
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

    <?php
    $connect_url = $TVC_Admin_Helper->get_custom_connect_url_subpage(admin_url() . 'admin.php?page=conversios-google-analytics', "gadssettings");
    require_once("googlesignin.php");
    ?>

    <form id="gadssetings_form" class="convpixsetting-inner-box mt-4">
        <div>
            <!-- Google Ads  -->
            <?php
            $google_ads_id = (isset($googleDetail->google_ads_id) && $googleDetail->google_ads_id != "") ? $googleDetail->google_ads_id : "";
            ?>
            <div id="analytics_box_ads" class="py-1">
                <div class="row pt-2">
                    <div class="col-7">
                        <h5 class="fw-normal mb-1">
                            <?php esc_html_e("Select Google Ads Account:", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </h5>
                        <select id="google_ads_id" name="google_ads_id" class="form-select form-select-lg mb-3 selecttwo google_ads_id" style="width: 100%" <?php echo esc_attr($is_sel_disable); ?>>
                            <?php if (!empty($google_ads_id)) { ?>
                                <option value="<?php echo esc_attr($google_ads_id); ?>" selected><?php echo esc_attr($google_ads_id); ?></option>
                            <?php } ?>
                            <option value="">Select Account</option>
                        </select>
                    </div>

                    <div class="col-2 d-flex align-items-end">
                        <button type="button" class="btn btn-sm d-flex conv-enable-selection conv-link-blue align-items-center">
                            <span class="material-symbols-outlined md-18">edit</span>
                            <span class="px-1">Edit</span>
                        </button>
                    </div>

                    <div class="col-12 flex-row pt-3">
                        <h5 class="fw-normal mb-1">
                            <?php esc_html_e("OR", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </h5>
                        <div class="col-12 py-2">
                            <button id="conv_create_gads_new_btn" type="button" class="btn conv-blue-bg text-white" data-bs-toggle="modal" data-bs-target="#conv_create_gads_new">
                                <?php esc_html_e("Create New", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
            <!-- Google Ads End-->

            <!-- Checkboxes -->
            <div id="checkboxes_box" class="pt-4">

                <div class="d-flex pt-2 align-items-center">
                    <input class="form-check-input" type="checkbox" value="1" id="remarketing_tags" name="remarketing_tags" <?php echo (esc_attr($googleDetail->remarketing_tags) == 1) ? 'checked="checked"' : ''; ?>>
                    <label class="form-check-label ps-2" for="remarketing_tags">
                        <?php esc_html_e("Enable remarketing tags", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </label>
                </div>

                <div class="d-flex pt-2 align-items-center">
                    <input class="form-check-input" type="checkbox" value="1" id="dynamic_remarketing_tags" name="dynamic_remarketing_tags" <?php echo (esc_attr($googleDetail->dynamic_remarketing_tags) == 1) ? 'checked="checked"' : ''; ?>>
                    <label class="form-check-label ps-2" for="dynamic_remarketing_tags">
                        <?php esc_html_e("Enable dynamic remarketing tags", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </label>
                </div>

                <div class="d-flex pt-2 align-items-center">
                    <input class="form-check-input" type="checkbox" value="1" id="link_google_analytics_with_google_ads" name="link_google_analytics_with_google_ads" <?php echo (esc_attr($googleDetail->link_google_analytics_with_google_ads) == 1) ? 'checked="checked"' : ''; ?>>
                    <label class="form-check-label ps-2" for="link_google_analytics_with_google_ads">
                        <?php esc_html_e("Link Google analytics with Google ads", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </label>
                </div>

                <div class="d-flex pt-2 align-items-center">
                    <input class="form-check-input" type="checkbox" value="1" id="google_ads_conversion_tracking" name="google_ads_conversion_tracking" <?php echo (esc_attr($googleDetail->google_ads_conversion_tracking) == 1) ? 'checked="checked"' : ''; ?>>
                    <label class="form-check-label ps-2" for="google_ads_conversion_tracking">
                        <?php esc_html_e("Enable Google ads conversion tracking", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </label>
                </div>

                <div class="d-flex pt-2 align-items-center">
                    <?php $ga_EC = get_option("ga_EC"); ?>
                    <input class="form-check-input" type="checkbox" value="1" id="ga_EC" name="ga_EC" <?php echo (esc_attr($ga_EC) == 1) && (esc_attr($googleDetail->google_ads_conversion_tracking) == 1) ? 'checked' : ''; ?> <?php echo ($googleDetail->google_ads_conversion_tracking != "1" ? "disabled" : "") ?>>
                    <label class="form-check-label ps-2" for="ga_EC">
                        <?php esc_html_e("Enable Google ads enhanced conversion tracking", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </label>
                </div>

            </div>
            <!-- Checkboxes end -->

            <div class="mt-4">

                <?php $ee_conversio_send_to = get_option('ee_conversio_send_to'); ?>
                <div id="analytics_box_adstwo" class="py-1 <?php echo (esc_attr($googleDetail->google_ads_conversion_tracking) == 0) ? 'd-none' : ''; ?>">
                    <div class="row pt-2">
                        <div class="col-10">
                            <h5 class="fw-normal mb-1">
                                <?php esc_html_e("Conversion label and ID", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </h5>
                            <select id="ee_conversio_send_to" name="ee_conversio_send_to" class="form-select form-select-lg mb-3 selecttwo google_ads_id" style="width: 100%" <?php echo esc_attr($is_sel_disable); ?>>
                                <?php if (!empty($ee_conversio_send_to)) { ?>
                                    <option value="<?php echo esc_attr($ee_conversio_send_to); ?>" selected><?php echo esc_attr($ee_conversio_send_to); ?></option>
                                <?php } ?>
                                <option value="">
                                    <?php esc_html_e("Conversion label", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </option>
                            </select>
                        </div>

                        <div class="col-2 d-flex align-items-end">
                            <button type="button" class="btn btn-sm d-flex conv-enable-selection_cli conv-link-blue align-items-center">
                                <span class="material-symbols-outlined md-18">edit</span>
                                <span class="px-1">Edit</span>
                            </button>
                        </div>
                        <div id="conversion_idlabel_box" class="col-12 mt-3 d-none">
                            <div class="alert alert-danger" role="alert">
                                No conversion labels are retrived, kindly refresh once else check if conversion label is available in your google ads account.
                                <br>Or<br>
                                Enter it manually in below input box.
                                <small>
                                    <?php esc_html_e("How to find Google Ads Conversion Id and Label?", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    <a target="_blank" href="https://conversios.io/help-center/How-to-Find-Conversion-Id-&-Label.pdf">
                                        <?php esc_html_e("click here", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </a>
                                </small>
                            </div>
                            <h5 class="fw-normal mb-1">
                                <?php esc_html_e("Enter Google Ads Conversion Id and Label:", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </h5>
                            <input type="text" id="ee_conversio_send_to_static" class="form-control" name="ee_conversio_send_to_static" value="<?php echo $ee_conversio_send_to; ?>">

                        </div>


                    </div>
                </div>

            </div>

        </div>
    </form>

</div>


<!-- Create New Ads Account Modal -->
<div class="modal fade" id="conv_create_gads_new" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">
                    <span id="before_gadsacccreated_title" class="before-ads-acc-creation"><?php esc_html_e("Enable Google Ads Account", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                    <span id="after_gadsacccreated_title" class="d-none after-ads-acc-creation"><?php esc_html_e("Account Created", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-start">
                <span id="before_gadsacccreated_text" class="mb-1 lh-lg fs-6 before-ads-acc-creation">
                    <?php esc_html_e("You’ll receive an invite from Google on your email. Accept the invitation to enable your Google Ads Account.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </span>

                <div class="onbrdpp-body alert alert-primary text-start d-none after-ads-acc-creation" id="new_google_ads_section">
                    <p>
                        <?php esc_html_e("Your Google Ads Account has been created", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        <strong>
                            (<b><span id="new_google_ads_id"></span></b>).
                        </strong>
                    </p>
                    <h6>
                        <?php esc_html_e("Steps to claim your Google Ads Account:", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </h6>
                    <ol>
                        <li>
                            <?php esc_html_e("Accept invitation mail from Google Ads sent to your email address", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            <em><?php echo (isset($tvc_data['g_mail'])) ? esc_attr($tvc_data['g_mail']) : ""; ?></em>
                            <span id="invitationLink">
                                <br>
                                <em>OR</em>
                                Open
                                <a href="" target="_blank" id="ads_invitationLink">Invitation Link</a>
                            </span>
                        </li>
                        <li><?php esc_html_e("Log into your Google Ads account and set up your billing preferences", "enhanced-e-commerce-for-woocommerce-store"); ?></li>
                    </ol>
                </div>

            </div>
            <div class="modal-footer">
                <button id="ads-continue" class="btn conv-blue-bg m-auto text-white before-ads-acc-creation">
                    <span id="gadsinviteloader" class="spinner-grow spinner-grow-sm d-none" role="status" aria-hidden="true"></span>
                    <?php esc_html_e("Send Invite", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </button>

                <button id="ads-continue-close" class="btn btn-secondary m-auto text-white d-none after-ads-acc-creation" data-bs-dismiss="modal">
                    <?php esc_html_e("Ok, close", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </button>
            </div>
        </div>
    </div>
</div>





<script>
    // get list google ads dropdown options
    function list_google_ads_account(tvc_data, new_ads_id) {
        var selectedValue = jQuery("#google_ads_id").val();
        var conversios_onboarding_nonce = "<?php echo wp_create_nonce('conversios_onboarding_nonce'); ?>";
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: {
                action: "list_googl_ads_account",
                tvc_data: tvc_data,
                conversios_onboarding_nonce: conversios_onboarding_nonce
            },
            success: function(response) {
                var btn_cam = 'ads_list';
                if (response.error === false) {
                    var error_msg = 'null';
                    if (response.data.length == 0) {
                        add_message("warning", "There are no Google ads accounts associated with email.");
                    } else {
                        if (response.data.length > 0) {
                            var AccOptions = '';
                            var selected = '';
                            if (new_ads_id != "" && new_ads_id != undefined) {
                                AccOptions = AccOptions + '<option value="' + new_ads_id + '" selected>' + new_ads_id + '</option>';
                            }
                            response?.data.forEach(function(item) {
                                AccOptions = AccOptions + '<option value="' + item + '">' + item + '</option>';
                            });
                            jQuery('#google_ads_id').append(AccOptions);
                            jQuery('#google_ads_id').prop("disabled", false);
                            jQuery(".conv-enable-selection").addClass('d-none');
                        }
                    }
                } else {
                    var error_msg = response.errors;
                }
                jQuery('#ads-account').prop('disabled', false);
            }
        });
    }

    //Get conversion list
    function get_conversion_list() {
        conv_change_loadingbar("show");
        jQuery("#conversion_idlabel_box").addClass("d-none");
        var data = {
            action: "conv_get_conversion_list_gads",
            gads_id: jQuery("#google_ads_id").val(),
            TVCNonce: "<?php echo wp_create_nonce('con_get_conversion_list-nonce'); ?>"
        };
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: data,
            success: function(response) {
                if (response == 0) {
                    jQuery('#ee_conversio_send_to').html("<option value=''>No Conversation Label and ID Found</option>");
                    jQuery('#ee_conversio_send_to').trigger("change");
                    jQuery("#conversion_idlabel_box").removeClass("d-none");
                    conv_change_loadingbar("hide");
                } else {
                    var AccOptions = '';
                    var selected = '';
                    response?.forEach(function(item) {
                        AccOptions = AccOptions + '<option value="' + item + '">' + item + '</option>';
                    });
                    jQuery('#ee_conversio_send_to').html(AccOptions);
                    jQuery('#ee_conversio_send_to').prop("disabled", false);
                    jQuery(".conv-enable-selection_cli").addClass('d-none');
                    conv_change_loadingbar("hide");
                }

            }

        });
    }



    // Create new gads acc function
    function create_google_ads_account(tvc_data) {
        var conversios_onboarding_nonce = "<?php echo wp_create_nonce('conversios_onboarding_nonce'); ?>";
        var error_msg = 'null';
        var btn_cam = 'create_new';
        var ename = 'conversios_onboarding';
        var event_label = 'ads';
        //user_tracking_data(btn_cam, error_msg,ename,event_label);   
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: {
                action: "create_google_ads_account",
                tvc_data: tvc_data,
                conversios_onboarding_nonce: conversios_onboarding_nonce
            },
            beforeSend: function() {
                jQuery("#gadsinviteloader").removeClass('d-none');
                jQuery("#ads-continue").addClass('disabled');
            },
            success: function(response) {
                console.log(response);
                if (response) {
                    error_msg = 'null';
                    var btn_cam = 'complate_new';
                    var ename = 'conversios_onboarding';
                    var event_label = 'ads';

                    //add_message("success", response.data.message);
                    jQuery("#new_google_ads_id").text(response.data.adwords_id);
                    if (response.data.invitationLink != "") {
                        jQuery("#ads_invitationLink").attr("href", response.data.invitationLink);
                    } else {
                        jQuery("#invitationLink").html("");
                    }
                    jQuery(".before-ads-acc-creation").addClass("d-none");
                    jQuery(".after-ads-acc-creation").removeClass("d-none");
                    //localStorage.setItem("new_google_ads_id", response.data.adwords_id);
                    var tvc_data = "<?php echo esc_js(wp_json_encode($tvc_data)); ?>";
                    list_google_ads_account(tvc_data, response.data.adwords_id);



                } else {
                    var error_msg = response.errors;
                    add_message("error", response.data.message);
                }
                //user_tracking_data(btn_cam, error_msg,ename,event_label);   
            }
        });
    }


    //Onload functions
    jQuery(function() {
        var tvc_data = "<?php echo esc_js(wp_json_encode($tvc_data)); ?>";
        var tvc_ajax_url = '<?php echo esc_url_raw(admin_url('admin-ajax.php')); ?>';
        let subscription_id = "<?php echo esc_attr($subscriptionId); ?>";
        let plan_id = "<?php echo esc_attr($plan_id); ?>";
        let app_id = "<?php echo esc_attr(CONV_APP_ID); ?>";
        let bagdeVal = "yes";
        let convBadgeVal = "<?php echo esc_attr($convBadgeVal); ?>";

        jQuery(".selecttwo").select2({
            minimumResultsForSearch: -1,
            placeholder: function() {
                jQuery(this).data('placeholder');
            }
        });

        jQuery(".conv-enable-selection").click(function() {
            conv_change_loadingbar("show");
            jQuery(".conv-enable-selection").addClass('disabled');
            list_google_ads_account(tvc_data);
            conv_change_loadingbar("hide");

        });

        jQuery(".conv-enable-selection_cli").click(function() {
            jQuery(".conv-enable-selection_cli").addClass('disabled');
            get_conversion_list(tvc_data);
        });


        jQuery(document).on("change", "form#gadssetings_form", function() {
            <?php if ($cust_g_email != "") { ?>
                
                var ee_conversio_send_to_static = jQuery("#ee_conversio_send_to_static").val();

                if (!jQuery("#conversion_idlabel_box").hasClass('d-none') && ee_conversio_send_to_static == "") {
                    jQuery("#ee_conversio_send_to_static").addClass("conv-border-danger");
                    jQuery(".conv-btn-connect").addClass("conv-btn-connect-disabled");
                    jQuery(".conv-btn-connect").removeClass("conv-btn-connect-enabled-google");
                    jQuery(".conv-btn-connect").text('Save');
                } else {
                    jQuery("#ee_conversio_send_to_static").removeClass("conv-border-danger");
                    jQuery(".conv-btn-connect").removeClass("conv-btn-connect-disabled");
                    jQuery(".conv-btn-connect").addClass("conv-btn-connect-enabled-google");
                    jQuery(".conv-btn-connect").text('Save');
                }

            <?php } else { ?>
                jQuery(".tvc_google_signinbtn").trigger("click");
            <?php } ?>
        });


        <?php if ($cust_g_email == "") { ?>
            jQuery("#conv_create_gads_new_btn").addClass("disabled");
            jQuery(".conv-enable-selection, .conv-enable-selection_cli").addClass("d-none");
        <?php } ?>


        <?php if (isset($_GET['subscription_id']) && sanitize_text_field($_GET['subscription_id'])) { ?>
            list_google_ads_account(tvc_data);
            jQuery(".conv-enable-selection").addClass("d-none");
        <?php } ?>




        jQuery("#google_ads_conversion_tracking").click(function() {
            if (jQuery("#google_ads_conversion_tracking").is(":checked")) {
                jQuery('#ga_EC').removeAttr('disabled');
                jQuery("#ga_EC").prop("checked", true);
                jQuery("#ga_EC").attr('checked', true);
                jQuery("#analytics_box_adstwo").removeClass("d-none");
            } else {
                jQuery('#ga_EC').attr('disabled', true);
                jQuery("#ga_EC").prop("checked", false);
                jQuery("#ga_EC").attr('checked', false);
                jQuery("#analytics_box_adstwo").addClass("d-none");
            }
        });

        //Set gads label id in static box on dropdown change
        jQuery(document).on("change", "#ee_conversio_send_to", function() {
            jQuery("#ee_conversio_send_to_static").val(jQuery("#ee_conversio_send_to").val());
        });

        jQuery(document).on("input", "#ee_conversio_send_to_static", function() {
            if (jQuery("#ee_conversio_send_to_static").val() != "") {
                var inpval = jQuery("#ee_conversio_send_to_static").val();
                var regex = /^AW-+[0-9{5,}]+[\/]+[a-zA-Z0-9{5,}]/;
                console.log(regex.test(inpval));
                if (regex.test(inpval) === false) {
                    jQuery("#ee_conversio_send_to_static").addClass("conv-border-danger");
                }
            } else {
                jQuery("#ee_conversio_send_to_static").removeClass("conv-border-danger");
            }
        });


        jQuery(document).on("change", "#google_ads_id", function() {
            if(jQuery("#google_ads_conversion_tracking").is(":checked"))
            {
                get_conversion_list();
            }
        })


        jQuery(document).on("click", ".conv-btn-connect-enabled-google", function() {
            conv_change_loadingbar("show");
            var google_ads_id = jQuery("#google_ads_id").val();
            var remarketing_tags = jQuery("#remarketing_tags").val();
            var dynamic_remarketing_tags = jQuery("#dynamic_remarketing_tags").val();
            var link_google_analytics_with_google_ads = jQuery("#link_google_analytics_with_google_ads").val();
            var google_ads_conversion_tracking = jQuery("#google_ads_conversion_tracking").val();
            var ga_EC = jQuery("#ga_EC").val();
            var ee_conversio_send_to = jQuery("#ee_conversio_send_to").val();

            var selectedoptions = {};

            selectedoptions['google_ads_id'] = jQuery("#google_ads_id").val();
            selectedoptions['ee_conversio_send_to'] = jQuery("#ee_conversio_send_to").val();
            selectedoptions['ee_conversio_send_to_static'] = jQuery("#ee_conversio_send_to_static").val();
            selectedoptions["subscription_id"] = "<?php echo $tvc_data['subscription_id'] ?>";

            jQuery('#checkboxes_box input[type="checkbox"]').each(function() {
                if (jQuery(this).is(':checked')) {
                    selectedoptions[jQuery(this).attr("name")] = jQuery(this).val();
                } else {
                    selectedoptions[jQuery(this).attr("name")] = "0";
                }
            });

            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: tvc_ajax_url,
                data: {
                    action: "conv_save_googleads_data",
                    pix_sav_nonce: "<?php echo wp_create_nonce('pix_sav_nonce_val'); ?>",
                    conv_options_data: selectedoptions,
                    conv_tvc_data: tvc_data,
                },
                beforeSend: function() {
                    jQuery(".conv-btn-connect-enabled-google").text("Saving...");
                    jQuery('.conv-btn-connect-enabled-google').addClass('disabled');
                },
                success: function(response) {
                    var user_modal_txt = "Congratulations, you have successfully connected your <br> Google Ads Account ID: " + google_ads_id + ".";

                    if (response == "0" || response == "1") {
                        jQuery(".conv-btn-connect-enabled-google").text("Connect");
                        jQuery("#conv_save_success_txt").html(user_modal_txt);
                        jQuery("#conv_save_success_modal").modal("show");
                    }
                    conv_change_loadingbar("hide");
                }
            });

        });

        // Create new gads acc
        jQuery("#ads-continue").on('click', function(e) {
            e.preventDefault();
            create_google_ads_account(tvc_data);
            jQuery('.ggladspp').removeClass('showpopup');
        });

    });
</script>