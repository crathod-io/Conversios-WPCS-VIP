<?php

/**
 * @since      4.0.2
 * Description: Conversios Onboarding page, It's call while active the plugin
 */
if (!class_exists('Conversios_Footer')) {
	class Conversios_Footer
	{
		protected $TVC_Admin_Helper;
		protected $customApiObj;
		protected $ee_options;
		public function __construct()
		{
			add_action('add_conversios_footer', array($this, 'before_end_footer'));
			add_action('add_conversios_footer', array($this, 'before_end_footer_add_script'));
			$this->TVC_Admin_Helper = new TVC_Admin_Helper();
			$this->customApiObj = new CustomApi();
			$this->ee_options = unserialize(get_option("ee_options"));
		}
		public function before_end_footer()
		{ 
			$googledetails_arr = $this->customApiObj->getGoogleAnalyticDetail($this->ee_options['subscription_id']);
			$googledetails = (array)$googledetails_arr->data;

			if ($googledetails['plan_id'] != 1) {
				$licenceInfoArr = array(
					"Plan Type:" => isset($googledetails['plan_name']) && $googledetails['plan_name'] != "" ? $googledetails['plan_name'] : "Not Available",
					"Plan Price:" => isset($googledetails['price']) && $googledetails['price'] != "" ? "$" . $googledetails['price'] : "Not Available",
					"Active License Key:" => isset($googledetails['licence_key']) && $googledetails['licence_key'] != "" ? $googledetails['licence_key'] : "Not Available",
					"Subscription ID:" => isset($googledetails['id']) && $googledetails['id'] != "" ? $googledetails['id'] : "Not Available",
					"Last Bill Date:" => isset($googledetails['subscription_update_date']) && $googledetails['subscription_update_date'] != "" ? $googledetails['subscription_update_date'] : "Not Available",
					"Next Bill Date:" => isset($googledetails['subscription_expiry_date']) && $googledetails['subscription_expiry_date'] != "" ? $googledetails['subscription_expiry_date'] : "Not Available",
				);
			} else {
				$licenceInfoArr = array(
					"Plan Type:" => "Not Available",
					"Plan Price:" => "Not Available",
					"Active License Key:" => "Not Available",
					"Subscription ID:" => "Not Available",
					"Last Bill Date:" => "Not Available",
					"Next Bill Date:" => "Not Available",
				);
			}
		?>
			<div class="tvc_footer_links">
				<input type="hidden" id="getPlanId" value="<?php echo $googledetails['plan_id'] ?>">
				<input type="hidden" id="getPlanName" value="<?php echo $googledetails['plan_name'] ?>">
			</div>
			<div class="modal fade" id="convLicenceInfoMod" tabindex="-1" aria-labelledby="convLicenceInfoModLabel" aria-hidden="true">
				<div class="modal-dialog modal-lg modal-dialog-centered" style="width: 700px;">
					<div class="modal-content">
						<div class="modal-header badge-dark-blue-bg text-white">
							<h5 class="modal-title text-white" id="convLicenceInfoModLabel">
								<?php esc_html_e("My Subscription", "enhanced-e-commerce-for-woocommerce-store"); ?>
							</h5>
							<button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
						</div>
						<div class="modal-body">
							<div class="container-fluid">
								<div class="row">
									<?php foreach ($licenceInfoArr as $key => $value) { ?>
										<div class="<?php echo $key == "Connected with:" ? "col-md-12" : "col-md-6"; ?> py-2 px-0">
											<div class="fw-bold">
												<?php esc_html_e($key, "enhanced-e-commerce-for-woocommerce-store"); ?>
											</div>
											<div class="">
												<?php esc_html_e($value, "enhanced-e-commerce-for-woocommerce-store"); ?>
											</div>
										</div>
									<?php  } ?>
								</div>
							</div>
						</div>

						<div class="modal-footer justify-content-center">
							<?php if ($googledetails['plan_id'] == 1) { ?>
								<div class="fs-6">
									<span><?php esc_html_e("You are currently using our free plugin, no license needed! Happy Analyzing.", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
									<span><?php esc_html_e("To unlock more features of Google Products ", "enhanced-e-commerce-for-woocommerce-store"); ?></span>
									<?php echo $this->TVC_Admin_Helper->get_conv_pro_link_adv("planpopup", "globalheader", "conv-link-blue", "anchor", "Upgrade to Pro Version"); ?>
								</div>

							<?php } else { ?>
								<div class="py-3">
									<a target="_blank" class="conv-link-blue fw-bold" href="https://www.conversios.io/wordpress/all-in-one-google-analytics-pixels-and-product-feed-manager-for-woocommerce-pricing/">
										<?php esc_html_e("Upgrade Plan", "enhanced-e-commerce-for-woocommerce-store"); ?>
									</a>
								</div>
							<?php } ?>

						</div>

					</div>
				</div>
			</div>
		<?php
		}

		public function before_end_footer_add_script()
		{
			$TVC_Admin_Helper = new TVC_Admin_Helper();
			$subscriptionId =  sanitize_text_field($TVC_Admin_Helper->get_subscriptionId());
		?>
			<script type="text/javascript">
				jQuery(document).ready(function() {
					var type = "warning";
					var plan_name = "Free Plan";
					var plan_id = jQuery("#getPlanId").val();
					if(plan_id != 1) {
						var type = "success"
						var plan_name =jQuery("#getPlanName").val();
					}
					jQuery("#pluginPlanName").html(plan_name).addClass("btn-"+type);
					
					var screen_name = '<?php echo $_GET['page']; ?>';
					var error_msg = 'null';
					jQuery('.navinfotopnav ul li a').click(function() {
						var slug = jQuery(this).find('span').text();
						var menu = jQuery(this).attr('href');
						str_menu = slug.replace(/\s+/g, '_').toLowerCase();
						user_tracking_data('click', error_msg, screen_name, 'topmenu_' + str_menu);
					});

				});

				function user_tracking_data(event_name, error_msg, screen_name, event_label) {
					// alert();
					jQuery.ajax({
						type: "POST",
						dataType: "json",
						url: tvc_ajax_url,
						data: {
							action: "update_user_tracking_data",
							event_name: event_name,
							error_msg: error_msg,
							screen_name: screen_name,
							event_label: event_label,
							TVCNonce: "<?php echo wp_create_nonce('update_user_tracking_data-nonce'); ?>"
						},
						success: function(response) {
							console.log('user tracking');
						}
					});
				}
			</script>
			<script>
				window.fwSettings = {
					'widget_id': 81000001743
				};
				! function() {
					if ("function" != typeof window.FreshworksWidget) {
						var n = function() {
							n.q.push(arguments)
						};
						n.q = [], window.FreshworksWidget = n
					}
				}()
			</script>
			<script type='text/javascript' src='https://ind-widget.freshworks.com/widgets/81000001743.js' async defer></script>
<?php
		}
	}
}
new Conversios_Footer();
