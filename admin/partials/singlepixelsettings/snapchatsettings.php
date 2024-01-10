<?php
$is_sel_disable = 'disabled';
?>
<div class="convcard p-4 mt-0 rounded-3 shadow-sm">
    <form id="pixelsetings_form" class="convpixsetting-inner-box">
        <div>
            <!-- Snapchat Pixel -->
            <?php $snapchat_ads_pixel_id = isset($ee_options['snapchat_ads_pixel_id']) ? $ee_options['snapchat_ads_pixel_id'] : ""; ?>
            <div id="snapchat_box" class="py-1">
                <div class="row pt-2">
                    <div class="col-7">
                        <label class="d-flex fw-normal mb-1 text-dark">
                            <?php esc_html_e("Snapchat Pixel ID", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            <span class="material-symbols-outlined text-secondary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="The Snapchat Ads pixel ID looks like. 12e1ec0a-90aa-4267-b1a0-182c455711e9">
                                info
                            </span>
                        </label>
                        <input type="text" name="snapchat_ads_pixel_id" id="snapchat_ads_pixel_id" class="form-control valtoshow_inpopup_this" value="<?php echo esc_attr($snapchat_ads_pixel_id); ?>" placeholder="e.g. 12e1ec0a-90aa-4267-b1a0-182c455711e9">
                    </div>
                </div>
            </div>
            <!-- Snapchat Pixel End-->


            <!-- Snapchat CAPI Pixel -->
            <?php
            $isbox_disabled = ""; //($plan_id == 42 || $plan_id == 41 || $plan_id == 1) ? "" : "boxdisabled disabled";
            $snapchat_access_token = (isset($ee_options["snapchat_access_token"]) && $ee_options["snapchat_access_token"] != "") ? $ee_options["snapchat_access_token"] : "";
            ?>
            <div id="tiktok_capi_box" class="py-1">
                <div class="row pt-2">
                    <div class="col-12">
                        <label class="d-flex fw-normal mb-1 text-dark">
                            <?php esc_html_e("Snapchat Conversion API Token", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            <!-- <span class="material-symbols-outlined text-secondary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="The TiKTok Ads pixel ID looks like. CBET743C77U5BM7P178N">
                                info
                            </span> -->
                            <span class="conv-link-blue ms-2 fw-bold-500 upgradetopro_badge" data-bs-toggle="modal" data-bs-target="#convSsttoProModal">
                                <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/upgrade_badge.png'); ?>" />
                                <?php esc_html_e("Unlock Serverside Tagging", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </span>
                        </label>
                        <textarea type="text" name="snapchat_access_token" id="snapchat_access_token" class="form-control <?php echo $isbox_disabled; ?>" style="height: 150px"><?php echo esc_attr($snapchat_access_token); ?></textarea>
                    </div>
                </div>
            </div>
            <!-- Snapchat CAPI Pixel End-->
        </div>
    </form>
    <input type="hidden" id="valtoshow_inpopup" value="Snapchat Pixel ID:" />

</div>