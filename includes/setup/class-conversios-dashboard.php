<?php

/**
 * @since      4.1.4
 * Description: Conversios Onboarding page, It's call while active the plugin
 */
if (class_exists('Conversios_Dashboard') === FALSE) {
    class Conversios_Dashboard
    {

        protected $screen;
        protected $TVC_Admin_Helper;
        protected $TVC_Admin_DB_Helper;
        protected $CustomApi;
        protected $PMax_Helper;
        protected $subscription_id;
        protected $ga_traking_type;
        protected $currency_code;
        protected $currency_symbol;
        protected $ga_currency;
        protected $ga_currency_symbols;
        protected $ga4_measurement_id;
        protected $ga4_analytic_account_id;
        protected $ga4_property_id;
        protected $subscription_data;
        protected $plan_id = 1;
        protected $is_need_to_update_api_data_wp_db = false;
        protected $report_data;
        protected $notice;
        protected $google_ads_id;
        protected $connect_url;
        protected $g_mail;
        protected $is_refresh_token_expire;

        protected $resource_center_data = array();
        protected $ee_options;
        protected $ee_prod_mapped_cats;
        protected $ee_prod_mapped_attrs;
        protected $ee_customer_gmail;
        protected $feed_data;
        protected $feed_count;

        protected $is_ai_unlocked;
        protected $promptLimit;
        protected $promptUsed;
        protected $last_fetched_prompt_date;
        protected $aiArr;
        protected $aiMainArr;
        protected $measurement_id;
        protected $todayAiDate01;

        public function __construct()
        {
            $this->TVC_Admin_Helper = new TVC_Admin_Helper();
            $this->TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
            $this->CustomApi = new CustomApi();
            $this->PMax_Helper = new Conversios_PMax_Helper();
            $this->connect_url = $this->TVC_Admin_Helper->get_custom_connect_url(admin_url() . 'admin.php?page=conversios');
            $this->subscription_id = $this->TVC_Admin_Helper->get_subscriptionId();
            // update API data to DB while expired token

            $this->ee_options = $this->TVC_Admin_Helper->get_ee_options_settings();
            $this->ee_prod_mapped_cats = get_option("ee_prod_mapped_cats");
            $this->ee_prod_mapped_attrs = get_option("ee_prod_mapped_attrs");
            $this->ee_customer_gmail = get_option("ee_customer_gmail");
            $this->feed_data = $this->TVC_Admin_Helper->ee_get_result_limit('ee_product_feed', 2);
            $this->feed_count = !empty($this->feed_data) ? count($this->feed_data) : 0;

            $this->is_refresh_token_expire = false; //$this->TVC_Admin_Helper->is_refresh_token_expire();
            $this->is_ai_unlocked = isset($this->ee_options['is_ai_unlocked']) ? sanitize_text_field($this->ee_options['is_ai_unlocked']) : '0';
            $this->promptLimit = isset($this->ee_options['promptLimit']) ? sanitize_text_field($this->ee_options['promptLimit']) : '20';
            $this->promptUsed = isset($this->ee_options['promptUsed']) ? sanitize_text_field($this->ee_options['promptUsed']) : '0';
            $this->last_fetched_prompt_date = isset($this->ee_options['last_fetched_prompt_date']) ? sanitize_text_field($this->ee_options['last_fetched_prompt_date']) : '';
            $this->measurement_id = isset($this->ee_options['gm_id']) ? sanitize_text_field($this->ee_options['gm_id']) : '';
            $this->todayAiDate01 = new DateTime(date('Y-m-d H:i:s'));
            if ($this->is_ai_unlocked == "0") {
                $this->create_prompts_table(); //check if table exists if not create one
            } else {
                //fetch data from ai reports data table
                $this->aiMainArr = $this->TVC_Admin_DB_Helper->tvc_get_results('ee_ai_reportdata');
                if (!empty($this->aiMainArr)) {
                    $this->aiArr = array();
                    foreach ($this->aiMainArr as $allElements) {
                        $key = $allElements->prompt_key;
                        $value = $allElements->ai_response;
                        $last_prompt_date = $allElements->last_prompt_date;
                        $this->aiArr[$key]['value'] = $value;
                        $this->aiArr[$key]['last_prompt_date'] = $last_prompt_date;
                    }
                }
            }
            $this->subscription_data = $this->TVC_Admin_Helper->get_user_subscription_data();
            if (isset($this->subscription_data->plan_id) && !in_array($this->subscription_data->plan_id, array("1"))) {
                $this->plan_id = $this->subscription_data->plan_id;
            }
            if (isset($this->subscription_data->google_ads_id) && $this->subscription_data->google_ads_id != "") {
                $this->google_ads_id = $this->subscription_data->google_ads_id;

                $currency_code_rs = $this->PMax_Helper->get_campaign_currency_code($this->google_ads_id);
                if (isset($currency_code_rs->data->currencyCode)) {
                    $this->currency_code = $currency_code_rs->data->currencyCode;
                    $this->currency_symbol = $this->TVC_Admin_Helper->get_currency_symbols($this->currency_code);
                }
            }

            if ($this->subscription_id != "") {
                $this->g_mail = sanitize_email(get_option('ee_customer_gmail'));
                $this->ga_traking_type = $this->subscription_data->tracking_option; // UA,GA4,BOTH

                if ($this->is_refresh_token_expire == false) {
                    $this->ga4_measurement_id = $this->subscription_data->measurement_id; //GA4 ID
                    $this->ga4_analytic_account_id = $this->subscription_data->ga4_analytic_account_id; //GA4 ID
                    $this->set_analytics_get_ga4_property_id();
                }
            } else {
                wp_redirect("admin.php?page=conversios-google-analytics");
                exit;
            }

            // resource center data
            $rcd_postdata = array("app_id" => CONV_APP_ID, "platform_id" => 1, "plan_id" => "1", "screen_name" => "dashboard");
            $resource_center_res = $this->CustomApi->get_resource_center_data($rcd_postdata);
            if (!empty($resource_center_res->data)) {
                $this->resource_center_data = $resource_center_res->data;
            }

            $this->includes();
            $this->screen = get_current_screen();
            $this->load_html();
        }

        public function create_prompts_table()
        {
            global $wpdb;
            $tablename = esc_sql($wpdb->prefix . "ee_ai_reportdata");
            $sql_create = "CREATE TABLE `$tablename` (  `id` int(11) NOT NULL AUTO_INCREMENT,
                                                          `subscription_id` int(11) NOT NULL,
                                                          `prompt_key` varchar(50) NOT NULL,
                                                          `ai_response` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
                                                          `report_cat` varchar(50) NOT NULL,
                                                          `last_prompt_date` datetime DEFAULT NULL,
                                                          `created_date` datetime NOT NULL,
                                                          `updated_date` datetime DEFAULT NULL,
                                                          `is_delete` int(11) Null,
                                                          PRIMARY KEY (`id`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;";
            if (maybe_create_table($tablename, $sql_create)) {
            }
        }

        public function includes()
        {
            if (!class_exists('CustomApi.php')) {
                require_once(ENHANCAD_PLUGIN_DIR . 'includes/setup/CustomApi.php');
            }
        }

        /* Need to For GA4 API call */
        public function set_analytics_get_ga4_property_id()
        {
            if (isset($this->subscription_data->ga4_property_id) && $this->subscription_data->ga4_property_id != "") {
                $this->ga4_property_id = $this->subscription_data->ga4_property_id;
            } else {
                $data = array(
                    "subscription_id" => sanitize_text_field($this->subscription_id),
                    "ga4_analytic_account_id" => sanitize_text_field($this->ga4_analytic_account_id)
                );
                if ($this->ga4_analytic_account_id != null) {
                    $api_rs = $this->CustomApi->analytics_get_ga4_property_id($data);
                }
                if (isset($api_rs->error) && $api_rs->error == '') {
                    if (isset($api_rs->ga4_property_id) && $api_rs->ga4_property_id != "") {
                        $this->ga4_property_id = $api_rs->ga4_property_id;
                        $this->ga_currency_symbols = $this->TVC_Admin_Helper->get_currency_symbols($this->ga_currency);
                        //$this->is_need_to_update_api_data_wp_db = true;
                    }
                }
            }
        }


        public function load_html()
        {
            do_action('conversios_start_html_' . sanitize_text_field($_GET['page']));
            $this->current_html();
            $this->current_js_ga4analytics_report();
            if ($this->plan_id != 1) {
                $this->current_js_campaign_report();
                $this->current_js_ai_section();
            }
            $this->current_js_licence_active();
            do_action('conversios_end_html_' . sanitize_text_field($_GET['page']));
        }

        public function current_js_ai_section()
        { ?>
            <script>
                jQuery(function() {
                    /*ai scripts*/
                    /* ai powered insights scripts */
                    jQuery(".unlock_ai_insights").on("click", function() {
                        //hide all initial ai sections from the page and show prompt section for all reports.
                        jQuery(".initial_ai_sections").hide();
                        jQuery(".advanced_ai_sections").show();
                        //set flag for advanced sections
                        var selected_vals = {};
                        selected_vals['is_ai_unlocked'] = "1";
                        selected_vals['promptLimit'] = "100";
                        selected_vals['promptUsed'] = "0";
                        jQuery.ajax({
                            type: "POST",
                            dataType: "json",
                            url: tvc_ajax_url,
                            data: {
                                action: "conv_save_pixel_data",
                                pix_sav_nonce: "<?php echo wp_create_nonce('pix_sav_nonce_val'); ?>",
                                conv_options_data: selected_vals,
                                conv_options_type: ["eeoptions"]
                            },
                            beforeSend: function() {},
                            success: function(response) {
                                //console.log('saved');
                            }
                        });

                    });

                    function displayTypingEffectnormal(text, element) {
                        const delay = 100; // Adjust the typing speed (milliseconds per character)
                        let index = 0;

                        function type() {
                            if (index < text.length) {
                                element.append(text.charAt(index));
                                index++;
                                setTimeout(type, delay);
                            }
                        }
                        type();
                    }

                    function displayTypingEffect(text, element) {
                        const delay = 20; // Adjust the typing speed (milliseconds per character)
                        let index = 0;

                        function type() {
                            if (index < text.length) {
                                if (index == 0) {
                                    text = "<li>" + text;
                                }
                                element.innerHTML = text.substr(0, index + 1);
                                element.empty().append(element.innerHTML); // Clear the div and append the new element
                                index++;
                                setTimeout(type, delay);
                            }
                        }
                        type();
                    }

                    /* get prompt response from middleware */
                    jQuery(".ai_prompts").on("click", function() {
                        let destination = this.dataset.destination;
                        let conv_prompt_key = this?.dataset?.key;
                        let conv_type = this?.dataset.type;
                        let ele_type = this?.dataset.ele_type;
                        let ref_btn_id = this?.id;
                        //console.log("api calling");
                        if (conv_prompt_key == "" || destination == "" || conv_type == "" || ele_type == "") {
                            return false;
                        }
                        if (ele_type == "button") {
                            jQuery("#" + ref_btn_id).off("click");
                        }
                        let promptUsed = jQuery("#conv_ai_count").val();
                        let promptLimit = jQuery("#conv_ai_limit").val();
                        if (parseInt(promptLimit) <= parseInt(promptUsed)) {
                            jQuery('#' + destination).html('Prompt Limit reached.');
                            if (ele_type != "button") {
                                jQuery('#' + conv_type + '-' + conv_prompt_key).click();
                                console.log("id is", ref_btn_id);
                                if (ref_btn_id != "") {
                                    jQuery("#" + ref_btn_id).hide();
                                }
                            }
                            return false;
                        }
                        var data = {
                            "action": "generate_ai_response",
                            "subscription_id": '<?php echo esc_attr($this->subscription_id); ?>',
                            "key": conv_prompt_key,
                            "domain": '<?php echo esc_attr(get_site_url()); ?>',
                            "conversios_nonce": '<?php echo wp_create_nonce('conversios_nonce'); ?>'
                        };
                        jQuery("#" + conv_type + "-pills-tabContent").hide();
                        jQuery("#" + conv_type + "-robotyping-box").show();

                        const loader_span = jQuery('.conv_loader_type');
                        displayTypingEffectnormal("Generating Insights based on your analytics data...", loader_span);
                        //ai_flag is setv
                        jQuery.ajax({
                            type: "POST",
                            dataType: "json",
                            url: tvc_ajax_url,
                            data: data,
                            success: function(response) {
                                jQuery("#" + conv_type + "-pills-tabContent").show();
                                jQuery("#" + conv_type + "-robotyping-box").hide();
                                jQuery(".conv_loader_type").text("");
                                if (response?.error == false && response?.data != "") {
                                    //convert to lis then append
                                    promptUsed = Number(promptUsed) + 1;
                                    //jQuery(".prompt_used_count").text(promptUsed);
                                    jQuery("#conv_ai_count").val(promptUsed);
                                    //save new prompt used in db
                                    var selected_vals = {};
                                    selected_vals['promptUsed'] = promptUsed;
                                    jQuery.ajax({
                                        type: "POST",
                                        dataType: "json",
                                        url: tvc_ajax_url,
                                        data: {
                                            action: "conv_save_pixel_data",
                                            pix_sav_nonce: "<?php echo wp_create_nonce('pix_sav_nonce_val'); ?>",
                                            conv_options_data: selected_vals,
                                            conv_options_type: ["eeoptions"]
                                        },
                                        beforeSend: function() {},
                                        success: function(response) {
                                            //console.log('new prompt used saved');
                                        }
                                    });
                                    let newData = response?.data;
                                    const responseDiv = jQuery('#' + destination);
                                    displayTypingEffect(newData, responseDiv);
                                    //jQuery('#' + destination).html('<li>'+ newData +'</li>');
                                    if (ele_type != "button") {
                                        jQuery('#' + conv_type + '-' + conv_prompt_key).click();
                                        console.log("id is", ref_btn_id);
                                        if (ref_btn_id != "") {
                                            jQuery("#" + ref_btn_id).hide();
                                        }
                                    }
                                } else {
                                    if (response?.error == true && response?.errors?.[0] == "Prompt limit reached.") {
                                        jQuery('#' + destination).html(response?.errors[0]);
                                        if (ele_type != "button") {
                                            jQuery('#' + conv_type + '-' + conv_prompt_key).click();
                                        }
                                    } else {
                                        jQuery('#' + destination).html("Not enough analytics data please try again later.");
                                        if (ele_type != "button") {
                                            jQuery('#' + conv_type + '-' + conv_prompt_key).click();
                                        }
                                    }
                                }
                            }
                        });
                    });
                });
            </script>
        <?php }


        public function current_js_licence_active()
        { ?>
            <script>
                jQuery(function() {
                    jQuery("#acvivelicbtn").click(function() {
                        var post_data_lic = {
                            action: "tvc_call_active_licence",
                            licence_key: jQuery("#licencekeyinput").val(),
                            conv_licence_nonce: '<?php echo wp_create_nonce("conv_lic_nonce"); ?>',
                        }
                        jQuery.ajax({
                            type: "POST",
                            dataType: "json",
                            url: tvc_ajax_url,
                            data: post_data_lic,
                            beforeSend: function() {
                                jQuery("#acvivelicbtn").find(".spinner-border").removeClass("d-none");
                            },
                            success: function(response) {
                                jQuery("#licencemsg").removeClass();
                                if (response.error === false) {
                                    jQuery("#licencemsg").addClass('text-success').text(response.message);
                                    setTimeout(function() {
                                        location.reload();
                                    }, 2000);
                                } else {
                                    jQuery("#licencemsg").addClass('text-danger').text(response.message);
                                }
                                jQuery('#acvivelicbtn').find(".spinner-border").addClass("d-none");
                            }
                        });
                    });
                });
            </script>
        <?php }


        public function current_js_campaign_report()
        { ?>
            <script>
                jQuery(function() {
                    //jQuery('#view_allcampaign_reports').DataTable();
                    var google_ads_id = "<?php echo $this->ee_options['google_ads_id'] ?>";
                    if (google_ads_id != "") {
                        var post_data_camp = {
                            action: "get_google_ads_campaign_performance_dashboard",
                            subscription_id: '<?php echo $this->subscription_id; ?>',
                            google_ads_id: '<?php echo $this->google_ads_id; ?>',
                            start_date: '<?php echo date("d-m-Y", strtotime("-1 month")); ?>',
                            end_date: '<?php echo date("d-m-Y", strtotime("now")); ?>',
                            plan_id: "3",
                            conversios_nonce: '<?php echo wp_create_nonce("conversios_nonce"); ?>',
                            limit: 3
                        }
                        jQuery.ajax({
                            type: "POST",
                            dataType: "json",
                            url: tvc_ajax_url,
                            data: post_data_camp,
                            success: function(response) {
                                jQuery("#campperformcard .spinner-border").addClass('d-none');
                                if (response.error == false) {
                                    var tableData = "";
                                    response.data.forEach(value => {

                                        var st_class = "blue";
                                        if (value.status === "PAUSED") {
                                            st_class = "yellow";
                                        }
                                        if (value.status === "PAUSED") {
                                            st_class = "yellow";
                                        }

                                        tableData += '<tr>';
                                        tableData += '<td class="th-data">' + value.compaignName + '</td>';
                                        tableData += '<td>' + Number(value.dailyBudget).toFixed(2) + '</td>';
                                        tableData += '<td class="' + st_class + '">' + value.status + '</td>';
                                        tableData += '<td>' + value.clicks + '</td>';
                                        tableData += '<td>' + Number(value.cost).toFixed(2) + '</td>';
                                        tableData += '<td>' + Number(value.conversions).toFixed(2) + '</td>';
                                        tableData += '<td>' + Number(value.sales).toFixed(2) + '</td>';
                                        tableData += '</tr>';
                                    });
                                    jQuery('#campper_report_tbl').find('tbody').append(tableData);
                                } else {
                                    jQuery("#dash-campreportgenerated").addClass("d-none");
                                    jQuery("#dash-campreportnotgenerated").removeClass("d-none");
                                    console.log("error", "", "There is some problem in fetching data from your Google Ads account.");
                                }

                            }
                        });
                    } else {
                        jQuery("#dash-campreportgenerated").addClass("d-none");
                        jQuery("#dash-campreportnotgenerated").removeClass("d-none");
                    }

                });
            </script>
        <?php }


        public function current_js_ga4analytics_report()
        { ?>
            <script>
                function compareDates(date1, date2) {
                    const jsDate1 = new Date(
                        date1.split("-")[2],
                        date1.split("-")[1] - 1,
                        date1.split("-")[0]
                    );
                    const jsDate2 = new Date(
                        date2.split("-")[2],
                        date2.split("-")[1] - 1,
                        date2.split("-")[0]
                    );
                    if (jsDate1 > jsDate2) {
                        return 1;
                    } else if (jsDate1 < jsDate2) {
                        return -1;
                    } else {
                        return 0;
                    }
                }
                var post_data = {
                    action: 'get_google_analytics_reports_dashboard',
                    subscription_id: '<?php echo esc_attr($this->subscription_id); ?>',
                    plan_id: 1,
                    ga_swatch: "ga4",
                    ga_traking_type: "GA4",
                    view_id: '',
                    property_id: '<?php echo esc_attr($this->ga4_property_id); ?>',
                    ga4_analytic_account_id: '<?php echo esc_attr($this->ga4_analytic_account_id); ?>',
                    ga_currency: '',
                    plugin_url: '<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL); ?>',
                    start_date: '<?php echo date('d-m-Y', strtotime("-1 month")); ?>',
                    end_date: '<?php echo date('d-m-Y', strtotime("now")) ?>',
                    g_mail: '<?php echo esc_attr($this->ee_customer_gmail); ?>',
                    google_ads_id: '<?php echo esc_attr($this->google_ads_id); ?>',
                    conversios_nonce: '<?php echo wp_create_nonce('conversios_nonce'); ?>',
                    domain: '<?php echo esc_attr(get_site_url()); ?>',
                    measurement_id: '<?php echo esc_attr($this->measurement_id); ?>'
                };

                jQuery.ajax({
                    type: "POST",
                    dataType: "json",
                    url: tvc_ajax_url,
                    data: post_data,
                    success: function(response) {
                        jQuery("#ga4reportcard .spinner-border").addClass('d-none');
                        if (response.error == false) {
                            jQuery("#dash-reportgenerated").removeClass("d-none");
                            if (Object.keys(response.data).length > 0) {
                                var data = JSON.parse(response.data);
                                var dashboard_data_point = data.dashboard_data_point;
                                Object.keys(dashboard_data_point).forEach(function(key, index) {
                                    let rawval = dashboard_data_point[key];
                                    let parsedval = parseFloat(rawval).toFixed(2);
                                    let divid = key.replace("compare_", "");

                                    if (key.includes("compare_")) {
                                        if (rawval < 0) {
                                            jQuery("#" + divid + " .market .reddown").removeClass("d-none");
                                        } else {
                                            jQuery("#" + divid + " .market .greenup").removeClass("d-none");
                                        }
                                        jQuery("#" + divid + " .market span").html(rawval + "%");
                                    } else {
                                        if (key == "averagePurchaseRevenue" || key == "totalRevenue") {
                                            let currsymb = tvc_helper.get_currency_symbols(data.currencyCode);
                                            jQuery("#" + divid + " .price").html(currsymb + parsedval);
                                        } else {
                                            jQuery("#" + divid + " .price").html(rawval);
                                        }

                                    }
                                });
                            }
                        } else {
                            console.log("error", "Error", "Analytics report data not fetched");
                            jQuery("#dash-reportnotgenerated").removeClass("d-none");
                        }
                    }
                });
               /* save all reports data */
                let promptUsed_chk = '<?php echo $this->promptUsed; ?>';
                let promptLimit_chk = '<?php echo $this->promptLimit; ?>';
                if (promptLimit_chk > promptUsed_chk) {
                    var last_report_date = '<?php echo $this->last_fetched_prompt_date; ?>';
                    //console.log("last fetched date", last_report_date);
                    let currDate = moment().format("DD-MM-YYYY");
                    //console.log("curr date", currDate);
                    if (last_report_date == "") {
                        tvc_helper.save_all_reports(post_data, '<?php echo wp_create_nonce('pix_sav_nonce_val'); ?>');
                    } else {
                        const dateRes = compareDates(currDate, last_report_date);
                        if (dateRes === 1) {
                            //console.log("date 1 > date 2 here");
                            tvc_helper.save_all_reports(post_data, '<?php echo wp_create_nonce('pix_sav_nonce_val'); ?>');
                        }
                    }
                }

            </script>
        <?php
        }


        public function dashboard_licencebox_html()
        { ?>
            <div class="dash-area">
                <div class="dashwhole-box">
                    <div class="card py-4">
                        <div class="card-body py-1">
                            <div class="purchase-box">
                                <h4>
                                    <?php esc_html_e("Already purchased license Key?", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </h4>
                                <div class="form-box">
                                    <input type="email" class="form-control icontrol" id="licencekeyinput" placeholder="Enter your key">
                                    <p id="licencemsg" style="position: absolute;"></p>
                                </div>
                                <div class="upgrade-btn">
                                    <button type="button" id="acvivelicbtn" class="btn btn-dark common-btn">
                                        <div class="spinner-border spinner-border-sm text-success d-none me-2" role="status" style="position: absolute; left: 17px;">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <?php esc_html_e("Upgrade to Pro", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }

        public function dashboard_pixelbox_html()
        {
            $pixel_box_arr = array(
                "gtmsettings" => array(
                    "logo" => "/admin/images/logos/conv_gtm_logo.png",
                    "title" => "Google Tag Manager",
                    "active_class" => isset($this->ee_options['tracking_method']) && $this->ee_options['tracking_method'] == 'gtm' ? 'convo-active' : 'gtmnotconnected',
                ),
                "gasettings" => array(
                    "logo" => "/admin/images/logos/conv_ganalytics_logo.png",
                    "title" => "Google Analytics",
                    "active_class" => (isset($this->ee_options['ga_id']) && $this->ee_options['ga_id'] != '') || (isset($this->ee_options['gm_id']) && $this->ee_options['gm_id'] != '') ? 'convo-active' : '',
                ),
                "gadssettings" => array(
                    "logo" => "/admin/images/logos/conv_gads_logo.png",
                    "title" => "Google Ads",
                    "active_class" => isset($this->ee_options['google_ads_id']) && $this->ee_options['google_ads_id'] != '' ? 'convo-active' : '',
                ),
                "fbsettings" => array(
                    "logo" => "/admin/images/logos/conv_meta_logo.png",
                    "title" => "Meta Pixel",
                    "active_class" => isset($this->ee_options['fb_pixel_id']) && $this->ee_options['fb_pixel_id'] != '' ? 'convo-active' : '',
                ),
                "bingsettings" => array(
                    "logo" => "/admin/images/logos/conv_bing_logo.png",
                    "title" => "Bing Pixel",
                    "active_class" => isset($this->ee_options['microsoft_ads_pixel_id']) && $this->ee_options['microsoft_ads_pixel_id'] != '' ? 'convo-active' : '',
                ),
                "twittersettings" => array(
                    "logo" => "/admin/images/logos/conv_twitter_logo.png",
                    "title" => "Twitter Pixel",
                    "active_class" => isset($this->ee_options['twitter_ads_pixel_id']) && $this->ee_options['twitter_ads_pixel_id'] != '' ? 'convo-active' : '',
                ),
                "pintrestsettings" => array(
                    "logo" => "/admin/images/logos/conv_pint_logo.png",
                    "title" => "Pinterest Pixel",
                    "active_class" => isset($this->ee_options['pinterest_ads_pixel_id']) && $this->ee_options['pinterest_ads_pixel_id'] != '' ? 'convo-active' : '',
                ),
                "snapchatsettings" => array(
                    "logo" => "/admin/images/logos/conv_snap_logo.png",
                    "title" => "Snapchat Pixel",
                    "active_class" => isset($this->ee_options['snapchat_ads_pixel_id']) && $this->ee_options['snapchat_ads_pixel_id'] != '' ? 'convo-active' : '',
                ),
                "tiktoksettings" => array(
                    "logo" => "/admin/images/logos/conv_tiktok_logo.png",
                    "title" => "TikTok Pixel",
                    "active_class" => isset($this->ee_options['tiKtok_ads_pixel_id']) && $this->ee_options['tiKtok_ads_pixel_id'] != '' ? 'convo-active' : '',
                )
            );
            $activeClassArr = array_filter(array_column($pixel_box_arr, 'active_class'));
            // $pixeltotalcount = count($activeClassArr) > 1 ? 2 : 1;
            $pixeltotalcount = count($activeClassArr) >= 2 ? 2 : count($activeClassArr);
            $pixelprogressbarclass = $pixeltotalcount * 50;

        ?>
            <div class="pixel-setup card">
                <div class="card-body">
                    <div class="feed-box">
                        <h3> <?php esc_html_e("Pixels & Analytics Setup", "enhanced-e-commerce-for-woocommerce-store"); ?></h3>
                        <div class="progress">
                            <div class="progress-bar w-<?php echo $pixelprogressbarclass ?>" role="progressbar" aria-label="Basic example" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span class="fs-6 text-secondary px-2"> <?php echo $pixeltotalcount ?>/2</span>
                    </div>
                    <div class="pixels-wholebox d-block">
                        <div class="pixel-setup-box pixels-wholebox d-block pt-3  <?php echo $pixel_box_arr['gtmsettings']['active_class'] ?>">
                            <ul class="progress-steps">
                                <li class="<?php echo $pixel_box_arr['gtmsettings']['active_class'] ?>">
                                    <div class="step-box">
                                        <h3 class="px-2 d-flex justify-content-between">
                                            <span><?php esc_html_e("Connect Google Tag Manager", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                            </span>
                                            <?php if ($pixel_box_arr['gtmsettings']['active_class'] == 'convo-active') { ?>
                                                <span class="conv-text-green"><?php esc_html_e("Connected", "enhanced-e-commerce-for-woocommerce-store"); ?> </span>
                                            <?php } ?>

                                        </h3>
                                        <p class="p-2"><?php esc_html_e("To begin, first your need to integrate Google Tag Manager, This will allow you to subsequently integrate Google Analytics, Google Ads and Other Pixels.", "enhanced-e-commerce-for-woocommerce-store"); ?> </p>
                                        <?php foreach ($pixel_box_arr as $key => $value) { ?>
                                            <?php if ($key == 'gtmsettings') { ?>
                                                <div class="pixels-part col-md-12 px-2 <?php echo $key; ?>">
                                                    <a href="<?php echo esc_url_raw('admin.php?page=conversios-google-analytics&subpage=' . $key); ?>" class="pixels-box w-100 p-2 <?php echo $value['active_class']; ?>">
                                                        <div class="pixels-image">
                                                            <img class="align-self-center" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . $value['logo']); ?>" />
                                                        </div>
                                                        <div class="pixels-text w-100">
                                                            <h5 class="d-flex lh-base">
                                                                <?php esc_html_e($value['title'], "enhanced-e-commerce-for-woocommerce-store"); ?>
                                                                <div class="d-flex justify-content-end w-100">
                                                                    <span class="px-1 hover-text">
                                                                        <?php if ($value['active_class'] != "convo-active") { ?>
                                                                            Connect
                                                                        <?php } ?>
                                                                    </span>
                                                                    <span>
                                                                        <?php if ($value['active_class'] == "convo-active") { ?>
                                                                            <img class="align-self-center" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/icon/check_circle_black.png"); ?>" />
                                                                        <?php } ?>
                                                                    </span>
                                                                </div>
                                                            </h5>
                                                        </div>
                                                    </a>
                                                </div>
                                            <?php } ?>

                                        <?php } ?>
                                    </div>
                                </li>
                                <li class="<?php echo count($activeClassArr) > 1 ? $pixel_box_arr['gtmsettings']['active_class'] : '' ?>">
                                    <div class="step-box step-wholebox">
                                        <h3 class="px-2 d-flex justify-content-between w-100">
                                            <span>
                                                <?php esc_html_e("Other Integrations", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                            </span>
                                            <?php if (count($activeClassArr) > 1) { ?>
                                                <span class="conv-text-green"><?php esc_html_e("Connected", "enhanced-e-commerce-for-woocommerce-store"); ?> </span>
                                            <?php } ?>

                                        </h3>
                                        <p class="p-2"><?php esc_html_e("Once you successfully integrate Google Tag Manager,  you can continue here to integrate the other channels, which will enables you to track website traffic, analyze user behavior and to evaluate advertising Performance.", "enhanced-e-commerce-for-woocommerce-store"); ?> </p>

                                        <?php foreach ($pixel_box_arr as $key => $value) { ?>
                                            <?php if ($key != 'gtmsettings') { ?>
                                                <div class="pixels-part col-md-4 px-2 <?php echo $key; ?>">
                                                    <a href="<?php echo esc_url_raw('admin.php?page=conversios-google-analytics&subpage=' . $key); ?>" class="pixels-box pixel-box-<?php echo $key; ?> w-100 p-2 <?php echo $value['active_class']; ?>">
                                                        <div class="pixels-image">
                                                            <img class="align-self-center" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . $value['logo']); ?>" />
                                                        </div>
                                                        <div class="pixels-text w-100">
                                                            <h5 class="d-flex lh-base">
                                                                <?php esc_html_e($value['title'], "enhanced-e-commerce-for-woocommerce-store"); ?>
                                                                <div class="d-flex justify-content-end w-100">
                                                                    <span class="px-1 hover-text">
                                                                        <?php if ($value['active_class'] != "convo-active") { ?>
                                                                            Connect
                                                                        <?php } ?>
                                                                    </span>
                                                                    <span>
                                                                        <?php if ($value['active_class'] == "convo-active") { ?>
                                                                            <img class="align-self-center" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/icon/check_circle_black.png"); ?>" />
                                                                        <?php } else { ?>
                                                                            <img class="align-self-center" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/icon/add_circle_black.png"); ?>" />

                                                                        <?php } ?>
                                                                    </span>
                                                                </div>
                                                            </h5>
                                                        </div>
                                                    </a>
                                                </div>
                                            <?php } ?>

                                        <?php } ?>
                                    </div>

                                </li>
                            </ul>
                        </div>
                        <div class="integrate-btn d-flex justify-content-start">
                            <a href="<?php echo esc_url_raw('admin.php?page=conversios-google-analytics'); ?>" class="btn btn-dark common-btn">
                                <?php esc_html_e("Go To Pixels Settings", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }
        //product feed card 
        public function dashboard_feedbox_html()
        {
            $channel_box_arr = array(
                "gmcsettings" => array(
                    "logo" => "/admin/images/logos/conv_gmc_logo.png",
                    "title" => "Google Merchant Center",
                    "active_class" => isset($this->ee_options['google_merchant_id']) && $this->ee_options['google_merchant_id'] != '' ? 'convo-active' : 'convo-inactive',
                    "redirect" => "admin.php?page=conversios-google-shopping-feed&subpage=gmcsettings",
                ),
                "tiktokBusinessSettings" => array(
                    "logo" => "/admin/images/logos/conv_tiktok_logo.png",
                    "title" => "TikTok Catalog",
                    "active_class" => isset($this->ee_options['tiktok_setting']['tiktok_business_id']) && $this->ee_options['tiktok_setting']['tiktok_business_id'] != '' ? 'convo-active' : 'convo-inactive',
                    "redirect" => "admin.php?page=conversios-google-shopping-feed&subpage=tiktokBusinessSettings",
                ),
                "metacataloguesettings" => array(
                    "logo" => "/admin/images/logos/conv_fb_catalog_logo.png",
                    "title" => "Facebook Catalog \n <span class='badge rounded-pill  fs-12'>Coming Soon</span>",
                    "active_class" => isset($this->ee_options['metacataloguesettings']) && $this->ee_options['metacataloguesettings'] != '' ? 'convo-active' : '',
                    "redirect" => "#",
                ),
            );
            $is_channel_conected = array_search('convo-active', array_column($channel_box_arr, 'active_class'));
            $progressbarclass = 0;
            $totalcount = 0;
            $attrCount = 0;
            if (is_numeric($is_channel_conected)) {
                $progressbarclass = $progressbarclass + 25;
                $totalcount = $totalcount + 1;
            }
            if (!empty($this->ee_prod_mapped_cats)) {
                $progressbarclass = $progressbarclass + 25;
                $attrCount = 1;
            }
            if (!empty($this->ee_prod_mapped_attrs)) {
                $progressbarclass = $progressbarclass + 25;
                $attrCount = 1;
            }
            if ($this->feed_count > 0) {
                $progressbarclass = $progressbarclass + 25;
                $totalcount = $totalcount + 1;
            }
            $totalcount = $totalcount + $attrCount;
        ?>

            <div class="product-feed card">
                <div class="card-body">
                    <div class="feed-box">
                        <h3><?php esc_html_e("Product Feed Manager Setup", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </h3>
                        <div class="progress">
                            <div class="progress-bar w-<?php echo $progressbarclass ?>" role="progressbar" aria-label="Basic example" aria-valuenow="0" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span class="fs-6 text-secondary px-2"> <?php echo $totalcount ?>/3</span>
                    </div>

                    <div class="progress-wholebox">
                        <ul class="progress-steps">
                            <li class="<?php echo (is_numeric($is_channel_conected)) ? "" : "disable"; ?>">
                                <div class="step-box">
                                    <span class="px-2">
                                        <?php esc_html_e("Configuration", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </span>
                                    <p class="p-2">
                                        <?php esc_html_e("Effortlessly customize your product feed channels with our intuitive configuration settings. These feed channels will help you in listing your products for running campaigns and showcasing your products to your potential customer.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </p>
                                    <div class="channel-box">
                                        <?php foreach ($channel_box_arr as $key => $value) { ?>
                                            <div class="channel_part px-2">
                                                <a href="<?php echo $value['redirect']; ?>" class="channel <?php echo $value['active_class']; ?> channel-box-<?php echo $key; ?>">
                                                    <div class="channel-image me-2">
                                                        <img class="align-self-center" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . $value['logo']); ?>" />
                                                    </div>
                                                    <h5 style="display:contents">
                                                        <?php echo $value['title'] ?>
                                                        <div class="d-flex justify-content-end w-100">
                                                            <span class="px-1 hover-text">
                                                                <?php if ($value['active_class'] != "convo-active" && $value['active_class'] != "") { ?>
                                                                    Connect
                                                                <?php } ?>
                                                            </span>
                                                            <span>
                                                                <?php if ($value['active_class'] == "convo-active") { ?>
                                                                    <img class="align-self-center" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/icon/check_circle_black.png"); ?>" />
                                                                <?php } else if ($value['active_class'] == "convo-inactive") { ?>
                                                                    <img class="align-self-center" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/icon/add_circle_black.png"); ?>" />

                                                                <?php } ?>
                                                            </span>
                                                        </div>
                                                    </h5>
                                                </a>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </li>
                            <li class="<?php echo (is_numeric($is_channel_conected) && !empty($this->ee_prod_mapped_cats) && !empty($this->ee_prod_mapped_attrs)) ? "" : "disable"; ?>">
                                <div class="step-box">
                                    <span class="px-2">
                                        <?php esc_html_e("Product Mapping", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </span>
                                    <p class="p-2">
                                        <?php esc_html_e("Simplify product feed management with seamless attribute and category mapping capabilities. Set your mapping once and store it for further use. You can come and change your set attributes at any point in time.", "enhanced-e-commerce-for-woocommerce-store") ?>
                                    </p>
                                    <div class="channel-box">
                                        <?php
                                        $is_feed_cats_connected = !empty($this->ee_prod_mapped_cats) && is_numeric($is_channel_conected) ? "yes" : "no";
                                        ?>
                                        <div class="channel_part px-2">
                                            <a href="admin.php?page=conversios-google-shopping-feed&tab=product_mapping&product_tab=category" class="channel cat-box-<?php echo $is_feed_cats_connected ?> <?php echo $is_feed_cats_connected == "yes" ? "convo-active" : "convo-inactive" ?>">
                                                <div class="channel-image me-2">
                                                    <?php if ($is_feed_cats_connected == "yes") { ?>
                                                        <img class="align-self-center" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/Category_Mapping.png'); ?>" />
                                                    <?php } else { ?>
                                                        <img class="align-self-center" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/Category_Mapping.png'); ?>" />
                                                    <?php } ?>
                                                </div>
                                                <h5 style="display:contents">
                                                    <?php esc_html_e("Category Mapping", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                                    <div class="d-flex justify-content-end w-100">
                                                        <span class="px-1 hover-text">
                                                            <?php if ($is_feed_cats_connected == "no") { ?>
                                                                Connect
                                                            <?php } ?>
                                                        </span>
                                                        <span>
                                                            <?php if ($is_feed_cats_connected == "yes") { ?>
                                                                <img class="align-self-center" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/icon/check_circle_black.png"); ?>" />
                                                            <?php } else if ($is_feed_cats_connected == "no") { ?>
                                                                <img class="align-self-center" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/icon/add_circle_black.png"); ?>" />

                                                            <?php } ?>
                                                        </span>
                                                    </div>
                                                </h5>
                                            </a>
                                        </div>

                                        <?php
                                        $is_feed_attr_connected = !empty($this->ee_prod_mapped_attrs) && is_numeric($is_channel_conected) ? "yes" : "no";
                                        ?>
                                        <div class="channel_part px-2">
                                            <a href="admin.php?page=conversios-google-shopping-feed&tab=product_mapping" class="channel attr-box-<?php echo $is_feed_attr_connected ?> <?php echo $is_feed_attr_connected == "yes" ? "convo-active" : "convo-inactive" ?>">
                                                <div class="channel-image me-2">
                                                    <?php if ($is_feed_attr_connected == "yes") { ?>
                                                        <img class="align-self-center" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/Attribute _mapping.png'); ?>" />
                                                    <?php } else { ?>
                                                        <img class="align-self-center" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/Attribute _mapping.png'); ?>" />
                                                    <?php } ?>
                                                </div>
                                                <h5 style="display:contents">
                                                    <?php esc_html_e("Attribute Mapping", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                                    <div class="d-flex justify-content-end w-100">
                                                        <span class="px-1 hover-text">
                                                            <?php if ($is_feed_attr_connected == "no") { ?>
                                                                Connect
                                                            <?php } ?>
                                                        </span>
                                                        <span>
                                                            <?php if ($is_feed_attr_connected == "yes") { ?>
                                                                <img class="align-self-center" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/icon/check_circle_black.png"); ?>" />
                                                            <?php } else if ($is_feed_attr_connected == "no") { ?>
                                                                <img class="align-self-center" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/icon/add_circle_black.png"); ?>" />

                                                            <?php } ?>
                                                        </span>
                                                    </div>
                                                </h5>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="<?php echo ($this->feed_count > 0) ? "" : "disable"; ?>">
                                <div class="step-box">
                                    <h4>
                                        <?php esc_html_e("Manage Feed", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </h4>
                                    <p>
                                        <?php esc_html_e("Streamline your product feed management with efficient and user-friendly feed management tools. Create, edit, or remove feeds as per your planning, and set auto schedules to keep your product data up to date.", "enhanced-e-commerce-for-woocommerce-store") ?>
                                    </p>
                                    <?php if ($this->feed_count > 0) { ?>
                                        <div class="border border-bottom-0 rounded-top ">
                                            <label class="p-2"><?php esc_html_e("Recent Feed List", "enhanced-e-commerce-for-woocommerce-store"); ?></label>
                                        </div>
                                        <div class="table-responsive ">
                                            <table class="table tablediv border">

                                                <thead class="table-light">
                                                    <tr>
                                                        <th class="fw-normal">FEED NAME</th>
                                                        <th class="fw-normal">CHANNELS</th>
                                                        <th class="fw-normal">LAST SYNC</th>
                                                        <th class="fw-normal text-center">STATUS</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $feed_wise_url = "admin.php?page=conversios-google-shopping-feed&tab=";
                                                    foreach ($this->feed_data as $value) {
                                                        $channel_id = explode(',', $value->channel_ids);
                                                    ?>
                                                        <tr>
                                                            <td>
                                                                <?php if ($value->is_delete === '1') { ?>
                                                                    <span style="cursor: no-drop;">
                                                                        <?php echo esc_attr($value->feed_name); ?>
                                                                    </span>
                                                                <?php } else { ?>
                                                                    <span>
                                                                        <a title="Go to feed wise product list" href="<?php echo esc_html(esc_url_raw($feed_wise_url . 'product_list&id=' . $value->id)); ?>"><?php echo esc_html($value->feed_name); ?></a>
                                                                    </span>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <?php foreach ($channel_id as $val) {
                                                                    if ($val === '1') { ?>
                                                                        <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/google_channel_logo.png'); ?>" />
                                                                    <?php } else if ($val === '2') { ?>
                                                                        <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/fb_channel_logo.png'); ?>" />
                                                                    <?php } else if ($val === '3') { ?>
                                                                        <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/tiktok_channel_logo.png'); ?>" />
                                                                <?php }
                                                                } ?>
                                                            </td>

                                                            <td class="align-middle" data-sort='" <?php echo esc_html(strtotime($value->last_sync_date)) ?> "'>
                                                                <span>
                                                                    <?php echo $value->last_sync_date && $value->last_sync_date !== '0000-00-00 00:00:00' ? esc_html(date_format(date_create($value->last_sync_date), "d M Y")) : 'NA'; ?>
                                                                </span>
                                                                <p class="fs-10 mb-0">
                                                                    <?php echo $value->last_sync_date && $value->last_sync_date !== '0000-00-00 00:00:00' ? esc_html(date_format(date_create($value->last_sync_date), "H:i a")) : ''; ?>
                                                                </p>
                                                            </td>
                                                            <td class="align-middle">
                                                                <?php if ($value->is_delete === '1') { ?>
                                                                    <span class="badgebox fs-10 deleted">
                                                                        Deleted
                                                                    </span>
                                                                    <?php } else {
                                                                    $draft = 0;
                                                                    $inprogress = 0;
                                                                    $synced = 0;
                                                                    $failed = 0;
                                                                    switch ($value->status) {
                                                                        case 'Draft':
                                                                            $draft++;
                                                                            break;

                                                                        case 'In Progress':
                                                                            $inprogress++;
                                                                            break;

                                                                        case 'Synced':
                                                                            $synced++;
                                                                            break;

                                                                        case 'Failed':
                                                                            $failed++;
                                                                            break;
                                                                    }

                                                                    switch ($value->tiktok_status) {
                                                                        case 'Draft':
                                                                            $draft++;
                                                                            break;

                                                                        case 'In Progress':
                                                                            $inprogress++;
                                                                            break;

                                                                        case 'Synced':
                                                                            $synced++;
                                                                            break;

                                                                        case 'Failed':
                                                                            $failed++;
                                                                            break;
                                                                    }
                                                                    if ($draft !== 0) { ?>
                                                                        <div class="badgebox draft" data-bs-toggle="popover" data-bs-placement="left" data-bs-content="Left popover" data-bs-trigger="hover focus">
                                                                            <?php echo esc_html('Draft'); ?>
                                                                            <div class="count-badge" style="margin-top:-4px;color:#DCA310">
                                                                                <?php echo esc_html($draft) ?>
                                                                            </div>
                                                                        </div>
                                                                        <input type="hidden" class="draftGmcImg" value="<?php echo $value->status == 'Draft' ? "<img class='draft-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/google_channel_logo.png") . "' />" : '' ?>">
                                                                        <input type="hidden" class="draftTiktokImg" value="<?php echo $value->tiktok_status == 'Draft' ? "<img class='draft-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/tiktok_channel_logo.png") . "' />" : '' ?>">
                                                                    <?php }
                                                                    if ($inprogress !== 0) { ?>
                                                                        <div class="badgebox inprogress" data-bs-toggle="popover" data-bs-placement="left" data-bs-content="Left popover" data-bs-trigger="hover focus">
                                                                            <?php echo esc_html('In Progress'); ?>
                                                                            <div class="count-badge" style="margin-top:-4px;color:#209EE1">
                                                                                <?php echo esc_html($inprogress) ?>
                                                                            </div>
                                                                        </div>
                                                                        <input type="hidden" class="inprogressGmcImg" value="<?php echo $value->status == 'In Progress' ? "<img class='inprogress-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/google_channel_logo.png") . "' />" : '' ?>">
                                                                        <input type="hidden" class="inprogressTiktokImg" value="<?php echo $value->tiktok_status == 'In Progress' ? "<img class='inprogress-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/tiktok_channel_logo.png") . "' />" : '' ?>">
                                                                    <?php }
                                                                    if ($synced !== 0) { ?>
                                                                        <div class="badgebox synced" data-bs-toggle="popover" data-bs-placement="left" data-bs-content="Left popover" data-bs-trigger="hover focus">
                                                                            <?php echo esc_html('Synced'); ?>
                                                                            <div class="count-badge" style="margin-top:-4px;color:#09bd83">
                                                                                <?php echo esc_html($synced) ?>
                                                                            </div>
                                                                        </div>
                                                                        <input type="hidden" class="syncedGmcImg" value="<?php echo $value->status == 'Synced' ? "<img class='synced-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/google_channel_logo.png") . "' />" : '' ?>">
                                                                        <input type="hidden" class="syncedTiktokImg" value="<?php echo $value->tiktok_status == 'Synced' ? "<img class='synced-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/tiktok_channel_logo.png") . "' />" : '' ?>">
                                                                    <?php }
                                                                    if ($failed !== 0) { ?>
                                                                        <div class="badgebox failed" data-bs-toggle="popover" data-bs-placement="left" data-bs-content="Left popover" data-bs-trigger="hover focus">
                                                                            <?php echo esc_html('Failed'); ?>
                                                                            <div class="count-badge" style="margin-top:-4px;color:#f43e56">
                                                                                <?php echo esc_html($failed) ?>
                                                                            </div>
                                                                        </div>
                                                                        <input type="hidden" class="failedGmcImg" value="<?php echo $value->status == 'Failed' ? "<img class='failed-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/google_channel_logo.png") . "' />" : '' ?>">
                                                                        <input type="hidden" class="failedTiktokImg" value="<?php echo $value->tiktok_status == 'Failed' ? "<img class='failed-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/tiktok_channel_logo.png") . "' />" : '' ?>">
                                                                <?php }
                                                                } ?>
                                                            </td>
                                                        </tr>
                                                    <?php }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php } ?>
                                </div>
                            </li>
                        </ul>
                        <div class="start-btn d-flex justify-content-start">
                            <a href="<?php echo esc_url_raw('admin.php?page=conversios-google-shopping-feed&tab=feed_list'); ?>" class="btn btn-dark common-btn">
                                <?php esc_html_e("Create Feed", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </a>
                        </div>
                    </div>
                </div>
                <script>
                    /*********************Card Popover Start***********************************************************************/
                    jQuery(document).on('mouseover', '.synced', function() {
                        var syncedGmcImg = jQuery(this).next('.syncedGmcImg').val();
                        var syncedTiktokImg = jQuery(this).next('.syncedGmcImg').next('.syncedTiktokImg').val();
                        var content = '<div class="popover-box border-synced">' + syncedGmcImg + '  ' + syncedTiktokImg + '</div>';
                        jQuery(this).popover({
                            html: true,
                            template: content,
                        });
                        jQuery(this).popover('show');
                    })

                    jQuery(document).on('mouseover', '.failed', function() {
                        var failedGmcImg = jQuery(this).next('.failedGmcImg').val();
                        var failedTiktokImg = jQuery(this).next('.failedGmcImg').next('.failedTiktokImg').val();
                        var content = "<div class='popover-box border-failed'>" + failedGmcImg + "  " + failedTiktokImg + "</div>";
                        jQuery(this).popover({
                            html: true,
                            template: content,
                        });
                        jQuery(this).popover('show');
                    })

                    jQuery(document).on('mouseover', '.draft', function() {
                        var draftGmcImg = jQuery(this).next('.draftGmcImg').val();
                        var draftTiktokImg = jQuery(this).next('.draftGmcImg').next('.draftTiktokImg').val();
                        var content = '<div class="popover-box border-draft">' + draftGmcImg + '  ' + draftTiktokImg + '</div>';
                        jQuery(this).popover({
                            html: true,
                            template: content,
                        });
                        jQuery(this).popover('show');
                    })
                    jQuery(document).on('mouseover', '.inprogress', function() {
                        var inprogressGmcImg = jQuery(this).next('.inprogressGmcImg').val();
                        var inprogressTiktokImg = jQuery(this).next('.inprogressGmcImg').next('.inprogressTiktokImg').val();
                        var content = '<div class="popover-box border-inprogress">' + inprogressGmcImg + '  ' + inprogressTiktokImg + '</div>';
                        jQuery(this).popover({
                            html: true,
                            template: content,
                        });
                        jQuery(this).popover('show');
                    })
                    /*********************Card Popover  End**************************************************************************/
                </script>
            </div>
        <?php
        }

        public function dashboard_campaignfreebox_html()
        {
        ?>
            <div class="campaign-manage campaign-manage-bg card">
                <div class="card-body">
                    <div class="campaign-wholebox text-center my-5 mb-3 pt-2">

                        <div class="d-flex fs-2 fw-bold mb-2 justify-content-center">
                            <div class="text-dark">
                                <?php esc_html_e("Campaign", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </div>
                            <div class="conv-yellow-text ms-2">
                                <?php esc_html_e("Performance", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </div>
                        </div>

                        <div class="fs-5">
                            <?php esc_html_e("To Scale Your Business Faster", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </div>
                        <span class="d-flex justify-content-center">
                            <a style="padding:8px 24px 8px 24px;" class="btn conv-yellow-bg mt-4 btn-lg" href="<?php echo esc_url($this->TVC_Admin_Helper->get_conv_pro_link_adv("campaignperformance", "dashboard", "", "linkonly")); ?>" target="_blank">
                                <?php esc_html_e("Upgrade Now", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </a>
                        </span>
                        <div class="d-flex justify-content-center mt-5">
                            <img class="align-self-center" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/conv-yellow-star-multi.png'); ?>" />
                            <p class="fs-5 ps-2">
                                <?php esc_html_e("15 Days Money Back Guarantee 100% free! No Questions Asked", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }

        public function dashboard_campaignprobox_html()
        {
        ?>
            <div id="campperformcard" class="campaign-manage commoncard-box card">
                <div class="card-body">
                    <div class="title-text">
                        <h3><?php esc_html_e("Campaign Management", "enhanced-e-commerce-for-woocommerce-store"); ?></h3>
                        <p>
                            <?php esc_html_e("By running Performance Max campaigns through our platform, you can access advanced targeting options, customized reporting, and seamless integration with other marketing tools. Our platform also provides expert support and guidance, helping you to achieve your advertising goals more efficiently and effectively.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </p>
                    </div>


                    <div id="dash-campreportgenerated">
                        <div class="table-box">
                            <h4 class="ps-2">
                                <?php esc_html_e("Campaign Performance Report", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                <div class="spinner-border text-primary spinner-border-sm" role="status"></div>
                            </h4>
                            <div class="table-responsive">
                                <table id="campper_report_tbl" class="custom-table table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Campaign Name</th>
                                            <th scope="col">Daily Budget (<?php echo $this->currency_symbol; ?>)</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Clicks</th>
                                            <th scope="col">Cost (<?php echo $this->currency_symbol; ?>)</th>
                                            <th scope="col">Conversions</th>
                                            <th scope="col">Sales</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end pe-2">
                            <a href="<?php echo esc_url_raw('admin.php?page=conversios-analytics-reports&tab=campaign-performance-report'); ?>" class="conv-link-blue fw-bold">
                                <?php esc_html_e("View Complete Report", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </a>
                        </div>
                        <div class="d-flex justify-content-start pe-2">
                            <div class="create-campaign">
                                <a href="<?php echo esc_url_raw('admin.php?page=conversios-pmax'); ?>" class="btn btn-dark common-btn">
                                    <?php esc_html_e("Create New Campaign", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div id="dash-campreportnotgenerated" class="report-notgenratedbox d-none">
                        <div class="card-content">
                            <div class="card-image">
                                <img class="align-self-center" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/report_img.svg'); ?>" />
                            </div>
                            <div class="card-content">
                                <h3><?php esc_html_e("Campaign Report Not Generated", "enhanced-e-commerce-for-woocommerce-store"); ?></h3>
                                <p><?php esc_html_e("Please create campaign to view the reports", "enhanced-e-commerce-for-woocommerce-store"); ?></p>
                            </div>
                            <div class="d-flex justify-content-start pe-2">
                                <div class="create-campaign">
                                    <a href="<?php echo esc_url_raw('admin.php?page=conversios-pmax'); ?>" class="btn btn-dark common-btn">
                                        <?php esc_html_e("Create New Campaign", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        <?php
        }

        public function dashboard_ga4reportbox_html()
        { ?>
            <div id="ga4reportcard" class="commoncard-box card">
                <div class="card-body">

                    <div class="title-title d-flex justify-content-between">
                        <h3>
                            <?php esc_html_e("Reports & Insights", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            <div class="spinner-border text-primary spinner-border-sm" role="status"></div>
                        </h3>
                        <h3 class="text-secondary fw-normal">
                            <?php esc_html_e("(Last 30 Days Google Analytics 4 Reports)", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </h3>
                    </div>

                    <div id="dash-reportnotgenerated" class="report-notgenratedbox d-none">
                        <div class="card-content">
                            <div class="card-image">
                                <img class="align-self-center" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/report_img.svg'); ?>" />
                            </div>
                            <div class="card-content">
                                <h3><?php esc_html_e("Report Not Generated", "enhanced-e-commerce-for-woocommerce-store"); ?></h3>
                                <p><?php esc_html_e("Please connect your Google Analytics to view the reports", "enhanced-e-commerce-for-woocommerce-store"); ?></p>
                            </div>
                        </div>
                    </div>

                    <div id="dash-reportgenerated" class="genrated-box d-none mb-2">
                        <ul>
                            <?php
                            $ga4ReportArr = array(
                                "totalRevenue" => array(
                                    "title" => "Revenue",
                                    "divid" => "totalRevenue",
                                ),
                                "transactions" => array(
                                    "title" => "Total Transactions",
                                    "divid" => "transactions",
                                ),
                                "averagePurchaseRevenue" => array(
                                    "title" => "Avg. Order Value",
                                    "divid" => "averagePurchaseRevenue",
                                ),
                                "addToCarts" => array(
                                    "title" => "Added to Cart",
                                    "divid" => "addToCarts",
                                ),
                                "sessions" => array(
                                    "title" => "Sessions",
                                    "divid" => "sessions",
                                ),
                                "totalUsers" => array(
                                    "title" => "Total Users",
                                    "divid" => "totalUsers",
                                ),
                                "newUsers" => array(
                                    "title" => "New Users",
                                    "divid" => "newUsers",
                                ),
                                "itemViews" => array(
                                    "title" => "Product Views",
                                    "divid" => "itemViews",
                                ),
                            );
                            foreach ($ga4ReportArr as $key => $value) {
                            ?>
                                <li id="<?php echo $value['divid']; ?>">
                                    <div class="revenue-box card">
                                        <div class="card-body">
                                            <h3>
                                                <?php esc_html_e($value['title'], "enhanced-e-commerce-for-woocommerce-store"); ?>
                                            </h3>
                                            <p>
                                                <?php esc_html_e("From Previous Period", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                            </p>
                                            <div class="market-box">
                                                <div class="price">-</div>
                                                <div class="market">
                                                    <img class="align-self-center greenup d-none" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/green-up.png'); ?>" />
                                                    <img class="align-self-center reddown d-none" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/red-down.png'); ?>" />
                                                    <span>-</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            <?php
                            }
                            ?>
                        </ul>
                        <div class="d-flex justify-content-end pe-2">
                            <a href="<?php echo esc_url('admin.php?page=conversios-analytics-reports'); ?>" class="conv-link-blue fw-bold">
                                <?php esc_html_e("View Complete Report", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        <?php
        }
        public function dashboard_aiinsightsbox_html()
        { ?>
            <?php if ($this->is_ai_unlocked == "0") { ?>
                <!-- smart insights -->
                <div class="dash-ga4 initial_ai_sections">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-content">
                                <div class="smart-powered">
                                    <a><span> <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/reporting-images/ai.png'); ?>" alt="" class="img-fluid" /></span><?php esc_html_e("Powered Smart Insights", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
                                </div>
                                <h2><?php esc_html_e("Hurray!", "enhanced-e-commerce-for-woocommerce-store"); ?></h2>
                                <h3><?php esc_html_e("New AI powered Smart Insights feature is live now", "enhanced-e-commerce-for-woocommerce-store"); ?></h3>
                                <div class="genrate-insights">
                                    <a class="btn btn-dark common-btn unlock_ai_insights"><?php esc_html_e("Generate Insights", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
                $ai_cls = 'style="display: none;"';
            } else {
                $ai_cls = 'style="display: block;"';
            } ?>
            <!-- Ai powered smart insights -->
            <input type="hidden" id="conv_ai_count" value="<?php echo $this->promptUsed; ?>">
            <input type="hidden" id="conv_ai_limit" value="<?php echo $this->promptLimit; ?>">
            <div class="commoncard-box card ai-poweredcard advanced_ai_sections" <?php echo $ai_cls; ?>>
                <div class="card-body">
                    <div class="ai-header">
                        <a><span><img class="align-self-center" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/ai.png'); ?>" /></span><?php esc_html_e("Powered Smart Insights", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
                        <p><?php esc_html_e("Here you can see the top 3 insights, on the basis of the information you provided regarding your Ecommerce business and goals.", "enhanced-e-commerce-for-woocommerce-store"); ?></p>
                    </div>
                    <?php
                    $DashAiResult01 = isset($this->aiArr['SourceSales25']['value']) ? $this->aiArr['SourceSales25']['value'] : "";
                    $DashAiResult02 = isset($this->aiArr['ProductConv15']['value']) ? $this->aiArr['ProductConv15']['value'] : "";
                    $DashAiResult03 = isset($this->aiArr['CampaignPerformImprove']['value']) ? $this->aiArr['CampaignPerformImprove']['value'] : "";
                    $dstatus_cls01 = "";
                    $dstatus_cls_val01 = "";
                    $dstatus_cls02 = "";
                    $dstatus_cls_val02 = "";
                    $dstatus_cls03 = "";
                    $dstatus_cls_val03 = "";
                    if ($DashAiResult01 != "") {
                        $dstatus_cls01 = "active";
                        $dstatus_cls_val01 = "show active";
                    } else if ($DashAiResult02 != "") {
                        $dstatus_cls02 = "active";
                        $dstatus_cls_val02 = "show active";
                    } else if ($DashAiResult03 != "") {
                        $dstatus_cls03 = "active";
                        $dstatus_cls_val03 = "show active";
                    }
                    ?>
                    <div class="prompttab-box">
                        <span><?php esc_html_e("Widely used prompts in your business category.", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                        <ul class="nav nav-pills" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <?php
                                $DashAiDate01 = isset($this->aiArr['SourceSales25']['last_prompt_date']) ? $this->aiArr['SourceSales25']['last_prompt_date'] : "";
                                if ($DashAiDate01 != "") {
                                    $DashAiDate01 = new DateTime($DashAiDate01);
                                    $interval = $this->todayAiDate01->diff($DashAiDate01);
                                    $daysDifferenced01 = $interval->days;
                                    $btn_cls_promptd01 = '';
                                } else {
                                    $daysDifferenced01 = '-1';
                                    $btn_cls_promptd01 = 'ai_prompts';
                                } ?>
                                <button class="nav-link <?php echo $dstatus_cls01; ?> <?php echo $btn_cls_promptd01; ?>" id="dash-SourceSales25" data-bs-toggle="pill" data-bs-target="#dash-prompt-tab1" type="button" role="tab" aria-controls="pills-home" data-key="SourceSales25" data-destination="dash_prompt_tab1_ul" data-type="dash" data-ele_type="button" aria-selected="true"><?php echo esc_html_e("To increase sales by 25%", "enhanced-e-commerce-for-woocommerce-store"); ?></button>
                                <?php if ($daysDifferenced01 >= '1') { ?>
                                    <div id="dash01-refresh-btn" class="refresh-btn ai_prompts" data-key="SourceSales25" data-destination="dash_prompt_tab1_ul" data-type="dash" data-ele_type="div">
                                        <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/reporting-images/refresh_ai_white.png'); ?>" alt="refresh" class="img-fluid" />
                                        <div class="tool-tip">
                                            <p><?php echo esc_html_e("Refresh to generate new insights", "enhanced-e-commerce-for-woocommerce-store"); ?></p>
                                        </div>
                                    </div>
                                <?php } ?>
                            </li>
                            <li class="nav-item" role="presentation">
                                <?php
                                $DashAiDate02 = isset($this->aiArr['ProductConv15']['last_prompt_date']) ? $this->aiArr['ProductConv15']['last_prompt_date'] : "";
                                if ($DashAiDate02 != "") {
                                    $DashAiDate02 = new DateTime($DashAiDate02);
                                    $interval = $this->todayAiDate01->diff($DashAiDate02);
                                    $daysDifferenced02 = $interval->days;
                                    $btn_cls_promptd02 = '';
                                } else {
                                    $daysDifferenced02 = '-1';
                                    $btn_cls_promptd02 = 'ai_prompts';
                                } ?>
                                <button class="nav-link <?php echo $dstatus_cls02; ?> <?php echo $btn_cls_promptd02; ?>" id="dash-ProductConv15" data-bs-toggle="pill" data-bs-target="#dash-prompt-tab2" type="button" role="tab" aria-controls="pills-profile" data-key="ProductConv15" data-destination="dash_prompt_tab2_ul" data-type="dash" data-ele_type="button" aria-selected="false"><?php esc_html_e("To increase conversions by 15%", "enhanced-e-commerce-for-woocommerce-store"); ?></button>
                                <?php if ($daysDifferenced02 >= '1') { ?>
                                    <div id="dash02-refresh-btn" class="refresh-btn ai_prompts" data-key="ProductConv15" data-destination="dash_prompt_tab2_ul" data-type="dash" data-ele_type="div">
                                        <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/reporting-images/refresh_ai_white.png'); ?>" alt="refresh" class="img-fluid" />
                                        <div class="tool-tip">
                                            <p><?php echo esc_html_e("Refresh to generate new insights", "enhanced-e-commerce-for-woocommerce-store"); ?></p>
                                        </div>
                                    </div>
                                <?php } ?>
                            </li>
                            <li class="nav-item" role="presentation">
                                <?php
                                $DashAiDate03 = isset($this->aiArr['CampaignPerformImprove']['last_prompt_date']) ? $this->aiArr['CampaignPerformImprove']['last_prompt_date'] : "";
                                if ($DashAiDate03 != "") {
                                    $DashAiDate03 = new DateTime($DashAiDate03);
                                    $interval = $this->todayAiDate01->diff($DashAiDate03);
                                    $daysDifferenced03 = $interval->days;
                                    $btn_cls_promptd03 = '';
                                } else {
                                    $daysDifferenced03 = '-1';
                                    $btn_cls_promptd03 = 'ai_prompts';
                                } ?>
                                <button class="nav-link <?php echo $dstatus_cls03; ?> <?php echo $btn_cls_promptd03; ?>" id="dash-CampaignPerformImprove" data-bs-toggle="pill" data-bs-target="#dash-prompt-tab3" type="button" role="tab" aria-controls="pills-profile" data-key="CampaignPerformImprove" data-destination="dash_prompt_tab3_ul" data-type="dash" data-ele_type="button" aria-selected="false"><?php echo esc_html_e("To improve campaign performance", "enhanced-e-commerce-for-woocommerce-store"); ?></button>
                                <?php if ($daysDifferenced03 >= '1') { ?>
                                    <div id="dash03-refresh-btn" class="refresh-btn ai_prompts" data-key="CampaignPerformImprove" data-destination="dash_prompt_tab3_ul" data-type="dash" data-ele_type="div">
                                        <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/reporting-images/refresh_ai_white.png'); ?>" alt="refresh" class="img-fluid" />
                                        <div class="tool-tip">
                                            <p><?php echo esc_html_e("Refresh to generate new insights", "enhanced-e-commerce-for-woocommerce-store"); ?></p>
                                        </div>
                                    </div>
                                <?php } ?>
                            </li>
                        </ul>
                        <div class="tab-content" id="dash-pills-tabContent">
                            <div class="tab-pane fade <?php echo $dstatus_cls_val01; ?>" id="dash-prompt-tab1" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
                                <ul id="dash_prompt_tab1_ul" class="listing">
                                    <?php if ($DashAiResult01 != "") { ?>
                                        <li><?php echo wp_kses_post($DashAiResult01); ?></li>
                                    <?php } else { ?>
                                        <?php echo esc_html_e("No data available, Click Refresh to generate new insights.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    <?php  } ?>
                                </ul>
                            </div>
                            <div class="tab-pane fade <?php echo $dstatus_cls_val02; ?>" id="dash-prompt-tab2" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
                                <ul id="dash_prompt_tab2_ul" class="listing">
                                    <?php if ($DashAiResult02 != "") { ?>
                                        <li><?php echo wp_kses_post($DashAiResult02); ?></li>
                                    <?php } else { ?>
                                        <?php echo esc_html_e("No data available, Click Refresh to generate new insights.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    <?php  } ?>
                                </ul>
                            </div>
                            <div class="tab-pane fade <?php echo $dstatus_cls_val03; ?>" id="dash-prompt-tab3" role="tabpanel" aria-labelledby="pills-contact-tab" tabindex="0">
                                <ul id="dash_prompt_tab3_ul" class="listing">
                                    <?php if ($DashAiResult03 != "") { ?>
                                        <li><?php echo wp_kses_post($DashAiResult03); ?></li>
                                    <?php } else { ?>
                                        <?php echo esc_html_e("No data available, Click Refresh to generate new insights.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                    <?php  } ?>
                                </ul>
                            </div>
                        </div>
                        <div id="dash-robotyping-box" class="robotyping-box" style="display: none;">
                            <div class="ai-robot">
                                <video autoplay loop muted height="150" width="150">
                                    <source src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/reporting-images/airobot.mp4'); ?>" type="video/mp4">
                                </video>
                            </div>
                            <div class="ai-typing">
                                <h2><span class="conv_loader_type"></span></h2>
                            </div>
                        </div>
                        <div class="view-report">
                            <p class="response-note"><span><?php echo esc_html_e("*Insights generated based on your last 45 days of google analytics & ads data."); ?></span>
                            </p>
                            <a href="<?php echo esc_url_raw('admin.php?page=conversios-analytics-reports'); ?>" class="link-dark conv-link-blue fw-bold"><?php esc_html_e("Go To Insights & Reporting", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
                        </div>

                    </div>
                </div>
            </div>

            <?php
        }
        public function dashboard_videocardbox_html()
        {
            $gettingstarr = new stdClass;
            $res_data = $this->resource_center_data;
            foreach ($res_data as $value) {
                if ($value->screen_name == "dashboard" && $value->sub_type == "gettingstartedvideo") {
                    $gettingstarr = $value;
                    break;
                }
            }
            if (!empty((array) $gettingstarr)) {
            ?>
                <div class="videocard card">
                    <div class="videoimage">
                        <img class="align-self-center" src="<?php echo esc_url_raw($gettingstarr->thumbnail_url); ?>" />
                    </div>
                    <div class="card-body">
                        <div class="title-dropdown">
                            <div class="title-text">
                                <h3>
                                    <?php esc_html_e($gettingstarr->title, "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </h3>
                            </div>
                        </div>
                        <div class="card-content">
                            <p>
                                <?php esc_html_e($gettingstarr->description, "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </p>
                            <div class="watch-videobtn">
                                <a target="_blank" href="<?php echo esc_url($gettingstarr->link) ?>" class="btn btn-dark common-btn">
                                    <?php esc_html_e("Watch Video", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            <?php
            }
        }
        public function dashboard_aicardbox_html()
        { ?>
            <!-- Unlock AI-powered insights for your Ecommerce goals! -->
            <div class=" card aipowered-card">
                <div class="card-image">
                    <img class="align-self-center animate__animated animate__pulse animate__slower animate__infinite animate__delay-2s" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/ai-powered.png'); ?>" />

                </div>
                <div class="card-body">
                    <div class="title-text">
                        <h2><?php esc_html_e("Unlock AI-powered insights for your Ecommerce goals!", "enhanced-e-commerce-for-woocommerce-store"); ?></h2>
                        <p><?php esc_html_e("Make informed decisions based on Product, Order, Source/Medium, and Campaign Performance reports.", "enhanced-e-commerce-for-woocommerce-store"); ?></p>
                    </div>
                    <div class="get-started">
                        <a href="<?php echo esc_url_raw('admin.php?page=conversios-analytics-reports'); ?>" class="btn btn-dark common-btn"><?php esc_html_e("Get Started", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
                    </div>
                </div>
            </div>
        <?php }

        public function dashboard_recentpostbox_html()
        { ?>
            <div class="videocard recent-post card">
                <div class="card-body">
                    <div class="title-dropdown">
                        <div class="title-text">
                            <h3>
                                <?php esc_html_e("Recent Post", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </h3>
                        </div>
                    </div>
                    <div class="card-content">
                        <?php
                        $res_data = $this->resource_center_data;
                        foreach ($res_data as $key => $value) {
                            if ($value->screen_name != "dashboard" && $value->sub_type != "recentposts") {
                                continue;
                            }
                        ?>
                            <a href="<?php echo esc_url($value->link); ?>" target="_blank">
                                <span><?php esc_html_e($value->title, "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php
        }

        public function dashboard_getpremium_html()
        {
        ?>
            <div class="commoncard-box get-premium card">
                <div class="card-body">
                    <div class="title-title">
                        <h3>
                            <?php esc_html_e("Get Premium", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </h3>
                        <p>
                            <?php esc_html_e("Checkout our Premium Plans to unlock all the features to scale your business", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </p>
                    </div>
                    <div class="card-content">
                        <div class="card-image">
                            <img class="align-self-center" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/premium.png'); ?>" />
                        </div>
                        <div class=" premium-btn">
                            <a target="_blank" href="<?php echo $this->TVC_Admin_Helper->get_conv_pro_link_adv("sidebar", "dashboard", "", "linkonly", ""); ?>" class="btn btn-dark common-btn">
                                <?php esc_html_e("Checkout Now", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }

        public function dashboard_gethelp_html()
        { ?>
            <div class="commoncard-box get-premium need-help card">
                <div class="card-body">
                    <div class="title-title">
                        <h3>
                            <?php esc_html_e("Need More Help", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </h3>
                        <p>
                            <?php esc_html_e("Book your Demo and our Support team will help you in setting up your account.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </p>
                    </div>
                    <div class="card-content">
                        <div class="card-image">
                            <img class="align-self-center" src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/needhelp.png'); ?>" />
                        </div>
                        <div class="premium-btn book-demo">
                            <a target="_blank" href="<?php echo esc_url_raw("https://calendly.com/dhyey-1"); ?>" class="btn btn-dark common-btn">
                                <?php esc_html_e("Book Demo", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }

        public function dashbord_getsst_html()
        { ?>

            <div class="commoncard-box sst-card card">
                <div class="card-body">
                    <h2>
                        <?php esc_html_e("Rejoice:", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        <span><?php esc_html_e("Server Side Tagging", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                        <?php esc_html_e("Has Arrived!", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </h2>
                    <p>
                        <?php esc_html_e("Gain Early access to Our Server-Side Tagging solution & Unlock the Benefits of first party Data", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </p>
                    <div class="discount-btn">
                        <a target="_blank" href="<?php echo esc_url_raw('https://www.conversios.io/server-side-tagging-gtm/?utm_source=dashboard&utm_medium=in_app&utm_campaign=sstbanner'); ?>" class="btn btn-dark common-btn">
                            <?php esc_html_e("Get Early Bird Discount", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </a>
                    </div>
                </div>
            </div>

        <?php
        }

        public function current_html()
        {
        ?>
            <section style="max-width: 1200px; margin:auto;">
                <div class="dash-convo">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <div class="dash-area">
                                    <div class="dashwhole-box">
                                        <div class="head-title d-flex justify-content-between">
                                            <h2>
                                                <?php esc_html_e("Dashboard", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                            </h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="welcome-wholebox">
                            <div class="row">
                                <div class="col-xl-8 col-lg-12 col-md-12 col-12 ">
                                    <!-- licence key html call-->
                                    <?php
                                    if ($this->plan_id == 1) {
                                        $this->dashboard_licencebox_html();
                                    }
                                    ?>
                                    <?php $this->dashboard_pixelbox_html(); ?>
                                    <?php
                                    if (is_plugin_active_for_network('woocommerce/woocommerce.php') || in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
                                        $this->dashboard_feedbox_html();
                                    }
                                    ?>
                                    <?php
                                    if (is_plugin_active_for_network('woocommerce/woocommerce.php') || in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
                                        if ($this->plan_id == 1) {
                                            $this->dashboard_campaignfreebox_html();
                                        } else {
                                            $this->dashboard_campaignprobox_html();
                                        }
                                    }
                                    ?>
                                    <?php
                                    if (is_plugin_active_for_network('woocommerce/woocommerce.php') || in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
                                        $this->dashboard_ga4reportbox_html();

                                        if ($this->plan_id != 1) {
                                            $this->dashboard_aiinsightsbox_html();
                                        }
                                    }
                                    ?>
                                </div>
                                <div class="col-xl-4 col-lg-12 col-md-12 col-12">
                                    <?php $this->dashboard_videocardbox_html(); ?>
                                    <?php
                                    if (is_plugin_active_for_network('woocommerce/woocommerce.php') || in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
                                        $this->dashboard_aicardbox_html();
                                    }
                                    ?>
                                    <?php $this->dashbord_getsst_html(); ?>
                                    <?php $this->dashboard_recentpostbox_html(); ?>

                                    <?php
                                    if ($this->plan_id == 1) {
                                        $this->dashboard_getpremium_html();
                                    }
                                    $this->dashboard_gethelp_html();
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- End -->
<?php
        }
    }
}
new Conversios_Dashboard();
