<?php
class ViewCampaignReport {
  public $response;
  protected $TVC_Admin_Helper;
  protected $TVC_Admin_DB_Helper;
  protected $site_url;
  protected $google_ads_id;
  protected $currentCustomerId;
  protected $subscription_id;
  protected $ga_currency;
  protected $ga_currency_symbols;
  protected $plan_id;
  protected $subscription_data;
  protected $ee_options;
  protected $ga_swatch;
  protected $is_refresh_token_expire;
  protected $aiArr;
  protected $aiMainArr;
  protected $is_ai_unlocked;
  protected $promptLimit;
  protected $promptUsed;
  protected $last_fetched_prompt_date;
  protected $todayAiDate01;

  public function __construct() {
    $this->TVC_Admin_Helper = new TVC_Admin_Helper();
    $this->TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();    
    $this->currentCustomerId = $this->TVC_Admin_Helper->get_currentCustomerId();
    $this->subscription_id = $this->TVC_Admin_Helper->get_subscriptionId(); 

    $this->site_url = "admin.php?page=conversios&tab=conversios-campaign-performance-report";

    $this->ga_swatch="ga4";
    $this->google_ads_id = "";
    $this->is_refresh_token_expire = $this->TVC_Admin_Helper->is_refresh_token_expire();
    $this->subscription_data = $this->TVC_Admin_Helper->get_user_subscription_data();
    $this->todayAiDate01 = new DateTime(date('Y-m-d H:i:s'));
    $this->ee_options = unserialize(get_option('ee_options'));
    $this->ga_currency = isset($this->ee_options['ecom_reports_ga_currency']) ? sanitize_text_field($this->ee_options['ecom_reports_ga_currency']) : '';
    if($this->ga_currency != ""){
      $this->ga_currency_symbols = $this->TVC_Admin_Helper->get_currency_symbols($this->ga_currency);
    }else{
      $this->ga_currency_symbols = '';
    }
    $this->is_ai_unlocked = isset($this->ee_options['is_ai_unlocked']) ? sanitize_text_field($this->ee_options['is_ai_unlocked']) : '0';
    $this->promptLimit = isset($this->ee_options['promptLimit']) ? sanitize_text_field($this->ee_options['promptLimit']) : '20';
    $this->promptUsed = isset($this->ee_options['promptUsed']) ? sanitize_text_field($this->ee_options['promptUsed']) : '0';
    $this->last_fetched_prompt_date = isset($this->ee_options['last_fetched_prompt_date']) ? sanitize_text_field($this->ee_options['last_fetched_prompt_date']) : '';
    
    if($this->is_ai_unlocked == "0"){
      $this->create_prompts_table(); //check if table exists if not create one
    }else{
      //fetch data from ai reports data table
      $this->aiMainArr = $this->TVC_Admin_DB_Helper->tvc_get_results('ee_ai_reportdata');
      if(!empty($this->aiMainArr)){
          $this->aiArr = array();
          foreach($this->aiMainArr as $allElements){
            $key = $allElements->prompt_key;
            $value = $allElements->ai_response;
            $last_prompt_date = $allElements->last_prompt_date;
            $this->aiArr[$key]['value'] = $value;
            $this->aiArr[$key]['last_prompt_date'] = $last_prompt_date;
        }
      }
   } 
  //print_r($this->aiArr);
    if(isset($this->subscription_data->google_ads_id) && $this->subscription_data->google_ads_id != ""){
        $this->google_ads_id = $this->subscription_data->google_ads_id;
        //$this->google_ads_id="7901932449";
      }
    if(isset($this->subscription_data->plan_id) && !in_array($this->subscription_data->plan_id, array("1"))){
        $this->plan_id = $this->subscription_data->plan_id;
        if($this->plan_id != 1 && $this->google_ads_id != ''){ //load only if paid and google ads acc setup
          $this->load_html();
          $this->load_js(); 
        }else{ 
           wp_redirect("admin.php?page=conversios");
           exit;
        } 
      }
  }
  public function create_prompts_table(){
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

public function load_html(){  ?>
<div class="con-tab-content">
    <div class="tab-card">
        <h3><?php echo esc_html_e("Campaign Performance Report","enhanced-e-commerce-for-woocommerce-store"); ?></h3>
        <div class="dashtp-right">
            <div id="reportrange_campaign" class="dshtpdaterange">
                <div class="dateclndicn">
                    <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/claendar-icon.png'); ?>"
                        alt="" />
                </div>
                <span class="daterangearea report_range_val"></span>
                <div class="careticn"><img
                        src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/caret-down.png'); ?>" alt="" />
                </div>
            </div>
        </div>
        <div class="loader-section" id="loader-section"><img
                src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL.'/admin/images/fevicon.gif');?>" alt="loader"></div>
        <table id="view_allcampaign_reports" class="table table-striped table-hover table-responsive table-section"
            style="width:100%">

            <thead class="table-primary">
                <tr>
                    <th>Campaign Name</th>
                    <th>Type</th>
                    <th>Daily Budget (<span
                            class="ga_currency_symbols"><?php echo esc_attr($this->ga_currency_symbols); ?></span>)</th>
                    <th>Status</th>
                    <th>Clicks</th>
                    <th>Cost (<span
                            class="ga_currency_symbols"><?php echo esc_attr($this->ga_currency_symbols); ?></span>)</th>
                    <th>Conversions</th>
                    <th>Sales</th>
                </tr>
            </thead>
            <tbody id="campaignTablebody">
            </tbody>
        </table>
        <?php if($this->is_ai_unlocked == "0" ) { ?>
        <!-- smart insights -->
        <div class="dash-ga4 initial_ai_sections">
            <div class="card smart-insightscard">
                <div class="card-body">
                    <div class="card-content">
                        <div class="smart-insightsbox">
                            <div class="smart-powered">
                                <a ><span> <img
                                            src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/reporting-images/ai.png'); ?>"
                                            alt="" class="img-fluid" /></span><?php echo esc_html_e("Powered Smart Insights", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
                            </div>
                            <div class="genrate-insights">
                                <a class="btn btn-dark common-btn unlock_ai_insights"><?php echo esc_html_e("Generate Insights", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php 
        $ai_cls = 'style="display: none;"';
        }else{
           $ai_cls = 'style="display: block;"';
       } ?>
        <!-- prompt box -->
        <input type="hidden" id="conv_ai_count" value="<?php echo $this->promptUsed; ?>">
        <input type="hidden" id="conv_ai_limit" value="<?php echo $this->promptLimit; ?>">
        <div class="dash-ga4 advanced_ai_sections" <?php echo $ai_cls; ?>>
            <div class="card smartprompt-card">
                <div class="card-body">
                    <div class="card-content">
                        <div class="smart-promptbox">
                            <div class="smart-powered">
                                <a ><span> <img
                                            src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/reporting-images/ai.png'); ?>"
                                            alt="" class="img-fluid" /></span><?php echo esc_html_e("Powered Smart Insights", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
                            </div>
                            <?php /*<div class="genrate-insights">
                                <a class="btn btn-dark common-btn" data-bs-toggle="modal"
                                    data-bs-target="#suggestprompt"> <span><img
                                            src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/reporting-images/plus.png'); ?>"
                                            alt="" class="img-fluid" /></span> <?php echo esc_html_e("Suggest Prompt", "enhanced-e-commerce-for-woocommerce-store"); ?></a>
                            </div>*/ ?>
                        </div>
                        <?php 
                            $CampaignAiResult01 = isset($this->aiArr['CampaignPerformImprove']['value'])?$this->aiArr['CampaignPerformImprove']['value']:"";
                            $cstatus_cls01="";
                            $cstatus_cls_val01="";
                            if($CampaignAiResult01 != ""){
                                $cstatus_cls01="active";
                                $cstatus_cls_val01="show active";
                            }
                            ?>
                        <div class="prompttab-box">
                            <span><?php echo esc_html_e("Select Prompt", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
                            <ul class="nav nav-pills" id="pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                <?php 
                                    $CampaignAiDate01 = isset($this->aiArr['CampaignPerformImprove']['last_prompt_date'])?$this->aiArr['CampaignPerformImprove']['last_prompt_date']:"";
                                    if($CampaignAiDate01 != ""){
                                        $CampaignAiDate01 = new DateTime($CampaignAiDate01);
                                        $interval = $this->todayAiDate01->diff($CampaignAiDate01);
                                        $daysDifferencec01 = $interval->days;
                                        $btn_cls_promptc01 = '';
                                    }else{
                                        $daysDifferencec01 ='-1';
                                        $btn_cls_promptc01 = 'ai_prompts';
                                    } ?>
                                    <button class="nav-link <?php echo $cstatus_cls01; ?> <?php echo $btn_cls_promptc01; ?>" id="campaign-CampaignPerformImprove" data-bs-toggle="pill"
                                        data-bs-target="#campaign-prompt-tab1" type="button" role="tab" data-key="CampaignPerformImprove" data-type="campaign" data-destination="campaign_prompt_tab1_ul" data-ele_type="button"
                                        aria-controls="pills-home" aria-selected="true"><?php echo esc_html_e("To improve campaign performance", "enhanced-e-commerce-for-woocommerce-store"); ?></button>
                                        <?php if( $daysDifferencec01 >= '1' ) { ?>
                                        <div id="campaign01-refresh-btn" class="refresh-btn ai_prompts" data-key="CampaignPerformImprove" data-type="campaign" data-destination="campaign_prompt_tab1_ul" data-ele_type="div">
                                            <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/reporting-images/refresh_ai.png'); ?>"
                                                alt="refresh" class="img-fluid" />
                                                <div class="tool-tip">
                                                    <p><?php echo esc_html_e("Refresh to generate new insights", "enhanced-e-commerce-for-woocommerce-store"); ?></p>
                                                </div>
                                        </div>
                                        <?php } ?>
                                </li>
                            </ul>
                            <div class="tab-content" id="campaign-pills-tabContent">
                                <div class="tab-pane fade <?php echo $cstatus_cls_val01; ?>" id="campaign-prompt-tab1" role="tabpanel"
                                    aria-labelledby="pills-home-tab" tabindex="0">
                                    <ul id="campaign_prompt_tab1_ul" class="listing">
                                        <?php if($CampaignAiResult01 != ""){ ?>
                                        <li><?php echo wp_kses_post($CampaignAiResult01); ?></li>
                                        <?php }else{ ?>
                                            <?php esc_html_e("No data available, Click Refresh to generate new insights.", "enhanced-e-commerce-for-woocommerce-store"); ?>
                                        <?php  } ?>
                                    </ul>
                                </div>
                            </div>
                            <div id="campaign-robotyping-box" class="robotyping-box" style="display: none;">
                                <div class="ai-robot">
                                    <video autoplay loop muted height="150" width="150">
                                        <source src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/reporting-images/airobot.mp4'); ?>" type="video/mp4">
                                    </video>
                                </div>
                                <div class="ai-typing">
                                    <h2><span class="conv_loader_type"></span></h2>
                                </div>
                            </div>
                            <div class="response-box">
                                <div class="response-validity">
                                    <p><span><?php echo esc_html_e("Prompt Limit : ", "enhanced-e-commerce-for-woocommerce-store"); ?></span><span
                                            class="prompt_used_count"><?php echo esc_attr($this->promptUsed); ?></span><span>/<?php echo esc_attr($this->promptLimit); ?></span>
                                    </p>
                                    <p class="response-note"><span><?php echo esc_html_e("*Insights generated based on your last 45 days of google ads data."); ?></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php }
public function load_js(){ ?>
<script>
var start = moment().subtract(45, 'days');
var end = moment();
jQuery(document).ready(function() {
    jQuery('#view_allcampaign_reports').DataTable();

    function cb_campaign(start, end) {
        var start_date = start.format('DD/MM/YYYY') || 0,
            end_date = end.format('DD/MM/YYYY') || 0;
        jQuery('span.daterangearea').html(start_date + ' - ' + end_date);

        var post_data = {
            "action": "get_google_ads_campaign_performance",
            "subscription_id": <?php echo $this->subscription_id; ?>,
            "customer_id": <?php echo $this->currentCustomerId; ?>,
            "google_ads_id": <?php echo $this->google_ads_id; ?>,
            "start_date": jQuery.trim(start_date.replace(/\//g, "-")),
            "end_date": jQuery.trim(end_date.replace(/\//g, "-")),
            "plan_id": "3",
            "conversios_nonce": "<?php echo wp_create_nonce('conversios_nonce'); ?>",
            "limit": 10000
        }
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: post_data,
            beforeSend: function() {
                jQuery('#loader-section').show();
            },
            success: function(response) {
                //console.log("campaign response",response);
                if (response.error == false) {
                    var tableData = "";
                    //apply filters then append response to tableData
                    response.data.forEach(value => {
                        tableData += '<tr>';
                        tableData += '<td>' + value.compaignName + '</td>';
                        tableData += '<td>' + value.compaignType + '</td>';
                        tableData += '<td>' + Number(value.dailyBudget).toFixed(2) +
                            '</td>';
                        tableData += '<td>' + value.status + '</td>';
                        tableData += '<td>' + value.clicks + '</td>';
                        tableData += '<td>' + Number(value.cost).toFixed(2) + '</td>';
                        tableData += '<td>' + Number(value.conversions).toFixed(2) +
                            '</td>';
                        tableData += '<td>' + Number(value.sales).toFixed(2) + '</td>';
                        tableData += '</tr>';
                    });
                    //console.log(tableData);
                    jQuery('#view_allcampaign_reports').DataTable().clear().draw();
                    jQuery('#view_allcampaign_reports').DataTable().destroy();
                    jQuery('#view_allcampaign_reports').find('tbody').append(tableData);
                    jQuery('#view_allcampaign_reports').DataTable().draw();
                } else {
                    tvc_helper.tvc_alert("error", "",
                        "There is some problem in fetching data from your Google Ads account.");
                }
                jQuery('#loader-section').hide();
            }
        });
    }
    jQuery('#reportrange_campaign').daterangepicker({
        startDate: start,
        endDate: end,
        maxDate: new Date(),
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month')
                .endOf('month')
            ]
        }
    }, cb_campaign);
    cb_campaign(start, end);
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
                if(index == 0){
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
        if (conv_prompt_key == "" || destination == "" || conv_type== "" || ele_type == "") {
            return false;
        }
        if(ele_type == "button"){
            jQuery("#" + ref_btn_id).off("click");
        }
        let promptUsed = jQuery("#conv_ai_count").val();
        let promptLimit = jQuery("#conv_ai_limit").val();
        if(parseInt(promptLimit) <= parseInt(promptUsed)) { 
            jQuery('#' + destination).html('Prompt Limit reached.');
            if(ele_type != "button"){
                jQuery('#' + conv_type + '-' + conv_prompt_key).click();
                console.log("id is",ref_btn_id);
                if(ref_btn_id != ""){
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
                    jQuery(".prompt_used_count").text(promptUsed);
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
                    if(ele_type != "button"){
                        jQuery('#' + conv_type + '-' + conv_prompt_key).click();
                        console.log("id is",ref_btn_id);
                        if(ref_btn_id != ""){
                            jQuery("#" + ref_btn_id).hide();
                        }
                    }
                }else{
                    if(response?.error == true && response?.errors?.[0] == "Prompt limit reached."){
                        jQuery('#' + destination).html(response?.errors[0]);
                        if(ele_type != "button"){
                            jQuery('#' + conv_type + '-' + conv_prompt_key).click();
                        }
                    }else{
                        jQuery('#' + destination).html("Not enough analytics data please try again later.");
                        if(ele_type != "button"){
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
}