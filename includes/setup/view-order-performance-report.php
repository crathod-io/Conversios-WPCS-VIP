<?php
class ViewOrderReport {
  public $response;
  protected $TVC_Admin_Helper;
  protected $TVC_Admin_DB_Helper;
  protected $site_url;
  protected $subscription_id;
  protected $ga_currency;
  protected $ga_currency_symbols;
  protected $plan_id;
  protected $subscription_data;
  protected $ee_options;
//   protected $aiArr;
//   protected $aiMainArr;
//   protected $is_ai_unlocked;
//   protected $promptLimit;
//   protected $promptUsed;
//   protected $last_fetched_prompt_date;

  public function __construct() {

    $this->TVC_Admin_Helper = new TVC_Admin_Helper();
    $this->TVC_Admin_DB_Helper = new TVC_Admin_DB_Helper();
    $this->subscription_id = $this->TVC_Admin_Helper->get_subscriptionId(); 
    $this->site_url = "admin.php?page=conversios&tab=conversios-order-performance-report";
    $this->subscription_data = $this->TVC_Admin_Helper->get_user_subscription_data();
    $this->ee_options = unserialize(get_option('ee_options'));
    $this->ga_currency = isset($this->ee_options['ecom_reports_ga_currency']) ? sanitize_text_field($this->ee_options['ecom_reports_ga_currency']) : '';
    if($this->ga_currency != ""){
      $this->ga_currency_symbols = $this->TVC_Admin_Helper->get_currency_symbols($this->ga_currency);
    }else{
      $this->ga_currency_symbols = '';
    }
    // $this->is_ai_unlocked = isset($this->ee_options['is_ai_unlocked']) ? sanitize_text_field($this->ee_options['is_ai_unlocked']) : '0';
    // $this->promptLimit = isset($this->ee_options['promptLimit']) ? sanitize_text_field($this->ee_options['promptLimit']) : '20';
    // $this->promptUsed = isset($this->ee_options['promptUsed']) ? sanitize_text_field($this->ee_options['promptUsed']) : '0';
    // $this->last_fetched_prompt_date = isset($this->ee_options['last_fetched_prompt_date']) ? sanitize_text_field($this->ee_options['last_fetched_prompt_date']) : '';
    
//     if($this->is_ai_unlocked == "0"){
//       $this->create_prompts_table(); //check if table exists if not create one
//     }else{
//       //fetch data from ai reports data table
//       $this->aiMainArr = $this->TVC_Admin_DB_Helper->tvc_get_results('ee_ai_reportdata');
//       if(!empty($this->aiMainArr)){
//           $this->aiArr = array();
//           foreach($this->aiMainArr as $allElements){
//               $key = $allElements->prompt_key;
//               $value = $allElements->ai_response;
//               $this->aiArr[$key] = $value;
//           }
//       }
//    }
  }
//   public function create_prompts_table(){
//     global $wpdb;
//     $tablename = esc_sql($wpdb->prefix . "ee_ai_reportdata");
//     $sql_create = "CREATE TABLE `$tablename` (  `id` int(11) NOT NULL AUTO_INCREMENT,
//                                                   `prompt_key` varchar(50) NOT NULL,
//                                                   `ai_response` json DEFAULT NULL,
//                                                   `report_cat` varchar(50) NOT NULL,
//                                                   `created_date` datetime NOT NULL,
//                                                   `updated_date` datetime DEFAULT NULL,
//                                                   `is_delete` int(11) Null,
//                                                   PRIMARY KEY (`id`) );";
//     if (maybe_create_table($tablename, $sql_create)) {
//     }
// } 
  public function load_html() {  ?>

<div class="con-tab-content">
    <div class="tab-card">
        <h3><?php echo esc_html_e("Order Performance Report","enhanced-e-commerce-for-woocommerce-store"); ?></h3>
        <div class="dashtp-right">
            <div id="reportrange_product" class="dshtpdaterange">
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
        <table id="view_allproduct_reports" class="table table-striped table-hover table-responsive table-section"
            style="width:100%">
            <thead class="table-primary">
                <tr>
                    <th>Transaction Id</th>
                    <th>Source / Medium</th>
                    <th>Purchase Revenue (<span
                            class="ga_currency_symbols"><?php echo esc_attr($this->ga_currency_symbols); ?></span>)</th>
                    <th>Tax Amount (<span
                            class="ga_currency_symbols"><?php echo esc_attr($this->ga_currency_symbols); ?></span>)</th>
                    <th>Refund Amount (<span
                            class="ga_currency_symbols"><?php echo esc_attr($this->ga_currency_symbols); ?></span>)</th>
                    <th>Shipping Amount (<span
                            class="ga_currency_symbols"><?php echo esc_attr($this->ga_currency_symbols); ?></span>)</th>
                </tr>
            </thead>
            <tbody id="productTablebody">
            </tbody>
        </table>
    </div>
</div>
<?php }
  public function load_js(){ ?>
<script>
var start = moment().subtract(45, 'days');
var FinalStart = moment().subtract(45, 'days');
var end = moment();
var Finalend = moment();
var start_date = start.format('DD/MM/YYYY') || 0,
    end_date = end.format('DD/MM/YYYY') || 0;
jQuery('span.daterangearea').html(start_date + ' - ' + end_date);

jQuery(document).ready(function() {
    function cb_campaign(start, end) {
        var start_date = start.format('DD/MM/YYYY') || 0,
            end_date = end.format('DD/MM/YYYY') || 0;
        jQuery('span.daterangearea').html(start_date + ' - ' + end_date);
    }
    jQuery('#reportrange_product').daterangepicker({
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
    var table = jQuery('#view_allproduct_reports').DataTable({
        "ordering": false,
        "scrollY": "auto",
        processing: true,
        serverSide: true,
        searching: true,

        ajax: {
            url: tvc_ajax_url,
            type: 'POST',
            data: function(d) {
                var dates = jQuery('span.daterangearea').html();
                //console.log("here",dates);
                var splitDate = dates.split('-');
                return jQuery.extend({}, d, {
                    action: "get_google_analytics_order_performance",
                    subscription_id: "<?php echo $this->subscription_id; ?>",
                    domain: "<?php echo get_site_url(); ?>",
                    start_date: jQuery.trim(splitDate[0]).replace(/\//g, "-"),
                    end_date: jQuery.trim(splitDate[1]).replace(/\//g, "-"),
                    // end_date :jQuery.trim(end_date.replace(/\//g,"-")),
                    conversios_nonce: "<?php echo wp_create_nonce('conversios_nonce'); ?>"
                });
            },
            dataType: 'JSON',
            error: function(err, status) {
                console.log(err, status);
            },
        },
        columns: [{
                data: 'transactionId'
            },
            {
                data: 'sessionSourceMedium'
            },
            {
                data: 'purchaseRevenue'
            },
            {
                data: 'taxAmount'
            },
            {
                data: 'refundAmount'
            },
            {
                data: 'shippingAmount'
            },
        ]

    });

    //event listners for datepicker , sorting, search, show page
    jQuery("input[name='view_allproduct_reports_length']").change(function() {
        table.clear();
        table.draw();
    });

    jQuery("span.daterangearea").on('DOMSubtreeModified', function() {
        table.clear();
        table.draw();
    });
    /*ai scripts*/ 
        /* ai powered insights scripts */
    //     jQuery(".unlock_ai_insights").on("click", function() {
    //     //hide all initial ai sections from the page and show prompt section for all reports.
    //     jQuery(".initial_ai_sections").hide();
    //     jQuery(".advanced_ai_sections").show();
    //     //set flag for advanced sections
    //     var selected_vals = {};
    //     selected_vals['is_ai_unlocked'] = "1";
    //     selected_vals['promptLimit'] = "20";
    //     selected_vals['promptUsed'] = "0";
    //     jQuery.ajax({
    //         type: "POST",
    //         dataType: "json",
    //         url: tvc_ajax_url,
    //         data: {
    //             action: "conv_save_pixel_data",
    //             pix_sav_nonce: "<php echo wp_create_nonce('pix_sav_nonce_val'); ?>",
    //             conv_options_data: selected_vals,
    //             conv_options_type: ["eeoptions"]
    //         },
    //         beforeSend: function() {},
    //         success: function(response) {
    //             //console.log('saved');
    //         }
    //     });

    // });
    /* get prompt response from middleware */
    // jQuery(".ai_prompts").on("click", function() {
    //     //check if  data required or not
    //     let last_fetched_prompt_date = '<php echo $this->last_fetched_prompt_date; ?>';
    //     console.log("last fetched date", last_fetched_prompt_date);
    //     let currDate = moment().format("DD-MM-YYYY");
    //     console.log("curr date", currDate);
    //     if (last_fetched_prompt_date >= currDate) {
    //         console.log("same date");
    //         return false;
    //     }
    //     let destination = this.dataset.destination;
    //     let conv_prompt_key = this?.dataset?.key;
    //     if (conv_prompt_key == "" && destination == "") {
    //         return false;
    //     }
    //     var data = {
    //         "action": "generate_ai_response",
    //         "subscription_id": '<php echo esc_attr($this->subscription_id); ?>',
    //         "key": conv_prompt_key,
    //         "domain": '<php echo esc_attr(get_site_url()); ?>',
    //         "conversios_nonce": '<php echo wp_create_nonce('conversios_nonce'); ?>'
    //     };
    //     //ai_flag is set
    //     jQuery.ajax({
    //         type: "POST",
    //         dataType: "json",
    //         url: tvc_ajax_url,
    //         data: data,
    //         success: function(response) {
    //             if (response?.error == false && response?.data != "") {
    //                 jQuery('#' + destination).html('<li>' + response?.data + '</li>');

    //                 let promptUsed = '<php echo esc_attr($this->promptUsed); ?>';
    //                 promptUsed = Number(promptUsed) + 1;
    //                 //save new prompt used in db
    //                 var selected_vals = {};
    //                 selected_vals['promptUsed'] = promptUsed;
    //                 jQuery.ajax({
    //                     type: "POST",
    //                     dataType: "json",
    //                     url: tvc_ajax_url,
    //                     data: {
    //                         action: "conv_save_pixel_data",
    //                         pix_sav_nonce: "<php echo wp_create_nonce('pix_sav_nonce_val'); ?>",
    //                         conv_options_data: selected_vals,
    //                         conv_options_type: ["eeoptions"]
    //                     },
    //                     beforeSend: function() {},
    //                     success: function(response) {
    //                         console.log('new prompt used saved');
    //                     }
    //                 });
    //             }
    //         }
    //     });
    // });
});
</script>
<?php }
}