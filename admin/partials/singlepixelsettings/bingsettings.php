<?php
$is_sel_disable = 'disabled';
?>
<div class="convcard p-4 mt-0 rounded-3 shadow-sm">
    <form id="pixelsetings_form" class="convpixsetting-inner-box">
        <div>
            <!-- MS Bing Pixel -->
            <?php $microsoft_ads_pixel_id = isset($ee_options['microsoft_ads_pixel_id']) ? $ee_options['microsoft_ads_pixel_id'] : ""; ?>
            <div id="msbing_box" class="py-1">
                <div class="row pt-2">
                    <div class="col-7">
                        <label class="d-flex fw-normal mb-1 text-dark">
                            <?php esc_html_e("Microsoft Ads (Bing) Pixel", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            <span class="material-symbols-outlined text-secondary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="The Microsoft Ads pixel ID looks like. 343003931">
                                info
                            </span>
                        </label>
                        <input type="text" name="microsoft_ads_pixel_id" id="microsoft_ads_pixel_id" class="form-control valtoshow_inpopup_this" value="<?php echo esc_attr($microsoft_ads_pixel_id); ?>" placeholder="e.g. 343003931">
                    </div>
                </div>
            </div>
            <!-- MS Bing Pixel End-->
        </div>
    </form>
    <input type="hidden" id="valtoshow_inpopup" value="Microsoft Ads (Bing) Pixel:" />

</div>