<?php
$is_sel_disable = 'disabled';
?>
<div class="convcard p-4 mt-0 rounded-3 shadow-sm">
    <form id="pixelsetings_form" class="convpixsetting-inner-box">
        <div>
            <!-- Tiktok Pixel -->
            <?php $tiKtok_ads_pixel_id = isset($ee_options['tiKtok_ads_pixel_id']) ? $ee_options['tiKtok_ads_pixel_id'] : ""; ?>
            <div id="tiktok_box" class="py-1">
                <div class="row pt-2">
                    <div class="col-7">
                        <label class="d-flex fw-normal mb-1 text-dark">
                            <?php esc_html_e("TikTok Pixel ID", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            <span class="material-symbols-outlined text-secondary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="The TiKTok Ads pixel ID looks like. CBET743C77U5BM7P178N">
                                info
                            </span>
                        </label>
                        <input type="text" name="tiKtok_ads_pixel_id" id="tiKtok_ads_pixel_id" class="form-control valtoshow_inpopup_this" value="<?php echo esc_attr($tiKtok_ads_pixel_id); ?>" placeholder="eg.CBET743C77U5BM7P178N">
                    </div>
                </div>
            </div>
            <!-- Tiktok Pixel End-->

            <!-- Tiktok Pixel -->
            <?php
            $isbox_disabled = ""; //($plan_id == 42 ||  $plan_id == 41 || $plan_id == 1) ? "" : "boxdisabled disabled";
            $tiktok_access_token = (isset($ee_options["tiktok_access_token"]) && $ee_options["tiktok_access_token"] != "") ? $ee_options["tiktok_access_token"] : "";
            ?>
            <div id="tiktok_capi_box" class="py-1">
                <div class="row pt-2">
                    <div class="col-12">
                        <label class="d-flex fw-normal mb-1 text-dark">
                            <?php esc_html_e("TikTok Events API Key", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            <!-- <span class="material-symbols-outlined text-secondary md-18 ps-2" data-bs-toggle="tooltip" data-bs-placement="top" title="The TiKTok Ads pixel ID looks like. CBET743C77U5BM7P178N">
                                info
                            </span> -->

                            <span class="conv-link-blue ms-2 fw-bold-500 upgradetopro_badge" data-bs-toggle="modal" data-bs-target="#convSsttoProModal">
                                <img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/upgrade_badge.png'); ?>" />
                                <?php esc_html_e("Unlock Serverside Tagging", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </span>

                        </label>
                        <textarea type="text" name="tiktok_access_token" id="tiktok_access_token" class="form-control <?php echo $isbox_disabled; ?>" style="height: 150px"><?php echo esc_attr($tiktok_access_token); ?></textarea>
                    </div>
                </div>
            </div>
            <!-- Tiktok Pixel End-->

        </div>
    </form>
    <input type="hidden" id="valtoshow_inpopup" value="TikTok Pixel ID:" />

</div>