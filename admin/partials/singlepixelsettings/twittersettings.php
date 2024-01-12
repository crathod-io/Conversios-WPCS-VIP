<?php
$is_sel_disable = 'disabled';
?>
<div class="convcard p-4 mt-0 rounded-3 shadow-sm">
    <form id="pixelsetings_form" class="convpixsetting-inner-box">
        <div>
            <!-- Twitter Pixel -->
            <?php $twitter_ads_pixel_id = isset($ee_options['twitter_ads_pixel_id']) ? $ee_options['twitter_ads_pixel_id'] : ""; ?>
            <div id="twitter_box" class="py-1">
                <div class="row pt-2">
                    <div class="col-7">
                        <label class="d-flex fw-normal mb-1 text-dark">
                            <?php esc_html_e("Twitter Pixel ID", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            <span class="material-symbols-outlined text-secondary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="The Twitter Ads pixel ID looks like. ocihb">
                                info
                            </span>
                        </label>
                        <input type="text" name="twitter_ads_pixel_id" id="twitter_ads_pixel_id" class="form-control valtoshow_inpopup_this" value="<?php echo esc_attr($twitter_ads_pixel_id); ?>" placeholder="e.g. ocihb">
                    </div>
                </div>
            </div>
            <!-- Twitter Pixel End-->
        </div>
    </form>
    <input type="hidden" id="valtoshow_inpopup" value="Twitter Pixel ID:" />

</div>