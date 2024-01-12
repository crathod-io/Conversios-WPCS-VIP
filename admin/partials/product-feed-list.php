<?php

$tvc_admin_helper = new TVC_Admin_Helper();
$tvc_admin_helper->need_auto_update_db();
$tvc_admin_helper->get_feed_status();
$feed_data = $tvc_admin_helper->ee_get_results('ee_product_feed');
$subscriptionId = $tvc_admin_helper->get_subscriptionId();
$ee_options = $tvc_admin_helper->get_ee_options_settings();
$site_url = "admin.php?page=conversios-google-shopping-feed&tab=";
$conv_data = $tvc_admin_helper->get_store_data();
$plan_id = $tvc_admin_helper->get_plan_id();
$conv_additional_data = $tvc_admin_helper->get_ee_additional_data();
$total_products = (
    new WP_Query(
        array(
            'post_type' => 'product',
            'post_status' => 'publish',
        )
    )
)->found_posts;

$google_merchant_center_id = '';
if (isset($ee_options['google_merchant_id']) === TRUE && $ee_options['google_merchant_id'] !== '') {
    $google_merchant_center_id = $ee_options['google_merchant_id'];
}

$tiktok_business_account = '';
if (isset($ee_options['tiktok_setting']['tiktok_business_id']) === TRUE && $ee_options['tiktok_setting']['tiktok_business_id'] !== '') {
    $tiktok_business_account = $ee_options['tiktok_setting']['tiktok_business_id'];
}

if ($google_merchant_center_id === '' && $tiktok_business_account === '') {
    wp_safe_redirect("admin.php?page=conversios-google-shopping-feed&tab=gaa_config_page");
    exit;
}

$getCountris = @file_get_contents(ENHANCAD_PLUGIN_DIR . "includes/setup/json/countries.json");
$contData = json_decode($getCountris);
?>
<style>

</style>
<div class="container-fluid conv-light-grey-bg pt-4 ps-4">
    <div class="row ps-4 pe-4">
        <div class="convfixedcontainermid m-0 p-0">
            <div class="conv-heading-box">
                <h5 class="fs-20">
                    <?php esc_html_e("Feed Management", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </h5>
                <span class="fw-400 fs-14 text-secondary">
                    <?php esc_html_e("You have total " . number_format($total_products) . " products in your WooCommerce store", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </span>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid conv-light-grey-bg p-4 pb-2">
    <div id="loadingbar_blue" class="progress-materializecss d-none ps-2 pe-2">
        <div class="indeterminate"></div>
    </div>
    <nav class="navbar navbar-light bg-white shadow-sm" style="border-top-left-radius:8px;border-top-right-radius:8px;">
        <div class="container-fluid">
            <span class="mb-0 h1">
                <input type="search" class="form-control border from-control-width empty" placeholder="Search..."
                    aria-label="Search" name="search_feed" id="search_feed" aria-controls="feed_list_table">
            </span>
            <button class="btn btn-soft-primary" name="create_new_feed" id="create_new_feed">
                <?php esc_html_e("Create New Feed", "enhanced-e-commerce-for-woocommerce-store"); ?>
            </button>
        </div>
    </nav>
    <div class="table-responsive shadow-sm" style="border-bottom-left-radius:8px;border-bottom-right-radius:8px;">
        <table class="table" id="feed_list_table" style="width:100%">
            <thead>
                <tr>
                    <th scope="col" class="text-dark text-start" style="width:25%">
                        <?php esc_html_e("FEED NAME", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-dark text-center" style="width:10%">
                        <?php esc_html_e("TARGET COUNTRY", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-dark text-start" style="width:15%">
                        <?php esc_html_e("CHANNELS", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-dark text-end" style="width:10%">
                        <?php esc_html_e("TOTAL PRODUCT", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-dark text-center" style="width:12%">
                        <?php esc_html_e("AUTO SYNC", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-dark text-center" style="width:10%">
                        <?php esc_html_e("CREATED", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-dark text-center" style="width:10%">
                        <?php esc_html_e("LAST SYNC", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-dark text-center" style="width:10%">
                        <?php esc_html_e("NEXT SYNC", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-dark text-center" style="width:5%">
                        <?php esc_html_e("STATUS", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                    <th scope="col" class="text-dark text-center" style="width:3%">
                        <?php esc_html_e("MORE", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </th>
                </tr>
            </thead>
            <tbody id="table-body" class="table-body">
                <?php if (empty($feed_data) === FALSE) {
                    foreach ($feed_data as $value) {
                        $channel_id = explode(',', $value->channel_ids);
                        ?>
                        <tr class="height">
                            <td class="align-middle text-start">
                                <?php if ($value->is_delete === '1') { ?>
                                    <span style="cursor: no-drop;">
                                        <?php echo esc_attr($value->feed_name); ?>
                                    </span>
                                <?php } else { ?>
                                    <span>
                                        <a title="Go to feed wise product list"
                                            href="<?php echo esc_url_raw($site_url . 'product_list&id=' . $value->id); ?>"><?php echo $value->feed_name; ?></a>
                                    </span>
                                <?php } ?>

                            </td>
                            <td class="align-middle text-center">
                                <?php
                                foreach ($contData as $key => $country) {
                                    if ($value->target_country === $country->code) { ?>
                                        <?php echo esc_html($country->name); ?>
                                    <?php }
                                }
                                ?>
                            </td>
                            <td class="align-middle text-start">
                                <?php foreach ($channel_id as $val) {
                                    if ($val === '1') { ?>
                                        <img
                                            src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/google_channel_logo.png'); ?>" />
                                    <?php } elseif ($val === '2') { ?>
                                        <img
                                            src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/fb_channel_logo.png'); ?>" />
                                    <?php } elseif ($val === '3') { ?>
                                        <img
                                            src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/tiktok_channel_logo.png'); ?>" />
                                    <?php }
                                } ?>
                            </td>
                            <td class="align-middle text-center">
                                <?php echo number_format($value->total_product ? esc_html($value->total_product) : 0); ?>
                            </td>
                            <td class="align-middle">
                                <span class="dot <?php echo $value->auto_schedule === '1' ? 'dot-green' : 'dot-red'; ?>"></span>
                                <span>
                                    <?php echo $value->auto_schedule === '1' ? 'Yes' : 'No'; ?>
                                </span>
                                <p class="fs-10 mb-0">
                                    <?php echo $value->auto_sync_interval !== 0 && $value->auto_schedule === '1' ? 'Every ' . esc_html($value->auto_sync_interval) . ' Days' : ' '; ?>
                                </p>
                            </td>
                            <td class="align-middle" data-sort='" <?php echo esc_html(strtotime($value->created_date)) ?> "'>
                                <span>
                                    <?php echo esc_html(date_format(date_create($value->created_date), "d M Y")); ?>
                                </span>
                                <p class="fs-10 mb-0">
                                    <?php echo esc_html(date_format(date_create($value->created_date), "H:i a")); ?>
                                </p>
                            </td>
                            <td class="align-middle" data-sort='" <?php echo esc_html(strtotime($value->last_sync_date)) ?> "'>
                                <span>
                                    <?php echo $value->last_sync_date && $value->last_sync_date != '0000-00-00 00:00:00' ? esc_html(date_format(date_create($value->last_sync_date), "d M Y")) : 'NA'; ?>
                                </span>
                                <p class="fs-10 mb-0">
                                    <?php echo $value->last_sync_date && $value->last_sync_date != '0000-00-00 00:00:00' ? esc_html(date_format(date_create($value->last_sync_date), "H:i a")) : ''; ?>
                                </p>
                            </td>
                            <td class="align-middle"
                                data-sort='" <?php echo esc_html(strtotime($value->next_schedule_date)) ?> "'>
                                <span>
                                    <?php echo $value->next_schedule_date && $value->next_schedule_date != '0000-00-00 00:00:00' ? esc_html(date_format(date_create($value->next_schedule_date), "d M Y")) : 'NA'; ?>
                                </span>
                                <p class="fs-10 mb-0">
                                    <?php echo $value->next_schedule_date && $value->next_schedule_date != '0000-00-00 00:00:00' ? esc_html(date_format(date_create($value->next_schedule_date), "H:i a")) : ''; ?>
                                </p>
                            </td>
                            <td class="align-middle">
                                <?php if ($value->is_delete === '1') { ?>
                                    <span class="badgebox rounded-pill  fs-10 deleted">
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
                                        <div class="badgebox draft" data-bs-toggle="popover" data-bs-placement="left"
                                            data-bs-content="Left popover" data-bs-trigger="hover focus">
                                            <?php echo esc_html('Draft'); ?>
                                            <div class="count-badge" style="margin-top:-4px;color:#DCA310">
                                                <?php echo esc_html($draft) ?>
                                            </div>
                                        </div>
                                        <input type="hidden" class="draftGmcImg"
                                            value="<?php echo $value->status == 'Draft' ? "<img class='draft-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/google_channel_logo.png") . "' />" : '' ?>">
                                        <input type="hidden" class="draftTiktokImg"
                                            value="<?php echo $value->tiktok_status == 'Draft' ? "<img class='draft-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/tiktok_channel_logo.png") . "' />" : '' ?>">
                                    <?php }
                                    if ($inprogress !== 0) { ?>
                                        <div class="badgebox inprogress" data-bs-toggle="popover" data-bs-placement="left"
                                            data-bs-content="Left popover" data-bs-trigger="hover focus">
                                            <?php echo esc_html('In Progress'); ?>
                                            <div class="count-badge" style="margin-top:-4px;color:#209EE1">
                                                <?php echo esc_html($inprogress) ?>
                                            </div>
                                        </div>
                                        <input type="hidden" class="inprogressGmcImg"
                                            value="<?php echo $value->status == 'In Progress' ? "<img class='inprogress-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/google_channel_logo.png") . "' />" : '' ?>">
                                        <input type="hidden" class="inprogressTiktokImg"
                                            value="<?php echo $value->tiktok_status == 'In Progress' ? "<img class='inprogress-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/tiktok_channel_logo.png") . "' />" : '' ?>">
                                    <?php }
                                    if ($synced !== 0) { ?>
                                        <div class="badgebox synced" data-bs-toggle="popover" data-bs-placement="left"
                                            data-bs-content="Left popover" data-bs-trigger="hover focus">
                                            <?php echo esc_html('Synced'); ?>
                                            <div class="count-badge" style="margin-top:-4px;color:#09bd83">
                                                <?php echo esc_html($synced) ?>
                                            </div>
                                        </div>
                                        <input type="hidden" class="syncedGmcImg"
                                            value="<?php echo $value->status == 'Synced' ? "<img class='synced-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/google_channel_logo.png") . "' />" : '' ?>">
                                        <input type="hidden" class="syncedTiktokImg"
                                            value="<?php echo $value->tiktok_status == 'Synced' ? "<img class='synced-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/tiktok_channel_logo.png") . "' />" : '' ?>">
                                    <?php }
                                    if ($failed !== 0) { ?>
                                        <div class="badgebox failed" data-bs-toggle="popover" data-bs-placement="left"
                                            data-bs-content="Left popover" data-bs-trigger="hover focus">
                                            <?php echo esc_html('Failed'); ?>
                                            <div class="count-badge" style="margin-top:-4px;color:#f43e56">
                                                <?php echo esc_html($failed) ?>
                                            </div>
                                        </div>
                                        <input type="hidden" class="failedGmcImg"
                                            value="<?php echo $value->status == 'Failed' ? "<img class='failed-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/google_channel_logo.png") . "' />" : '' ?>">
                                        <input type="hidden" class="failedTiktokImg"
                                            value="<?php echo $value->tiktok_status == 'Failed' ? "<img class='failed-status' src='" . esc_url_raw(ENHANCAD_PLUGIN_URL . "/admin/images/logos/tiktok_channel_logo.png") . "' />" : '' ?>">
                                    <?php }
                                } //end if ?>
                            </td>
                            <td class="align-middle">
                                <div class="dropdown position-static">
                                    <?php if ($value->is_delete === '1') { ?>
                                        <button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                            style="cursor: no-drop;">
                                            <span class="material-symbols-outlined">
                                                more_horiz
                                            </span>
                                        </button>
                                    <?php } else { ?>
                                        <button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <span class="material-symbols-outlined">
                                                more_horiz
                                            </span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-dark bg-white">
                                            <li class="mb-0 pointer"><a class="dropdown-item text-secondary border-bottom fs-12"
                                                    onclick="editFeed(<?php echo esc_html($value->id); ?>)">Edit</a>
                                            </li>
                                            <li class="mb-0 pointer"><a class="dropdown-item text-secondary border-bottom fs-12 "
                                                    onclick="duplicateFeed(<?php echo esc_html($value->id); ?>)">Duplicate</a>
                                            </li>
                                            <li class="mb-0 pointer"><a class="dropdown-item text-secondary fs-12"
                                                    onclick="deleteFeed(<?php echo esc_html($value->id); ?>)">Delete</a></li>
                                        </ul>
                                    <?php } //end if
                                            ?>
                                </div>
                            </td>
                        </tr>
                    <?php } //end foreach
                } //end if
                ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="convCreateFeedModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content ">
            <form id="feedForm" onfocus="this.className='focused'">
                <div id="loadingbar_blue_modal" class="progress-materializecss d-none ps-2 pe-2" style="width:98%">
                    <div class="indeterminate"></div>
                </div>
                <div class="modal-header bg-light p-2 ps-4">
                    <h5 class="modal-title fs-16 fw-500" id="feedType">
                        <?php esc_html_e("Create New Feed", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        onclick="$('#feedForm')[0].reset()"></button>
                </div>
                <div class="modal-body ps-4 pt-0">
                    <div class="mb-4">
                        <label for="feed_name" class="col-form-label text-dark fs-14 fw-500">
                            <?php esc_html_e("Feed Name", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </label>
                        <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip" data-bs-placement="right"
                            title="Add a name to your feed for your reference, for example, 'April end-of-season sales' or 'Black Friday sales for the USA'.">
                            info
                        </span>
                        <input type="text" class="form-control fs-14" name="feedName" id="feedName"
                            placeholder="e.g. New Summer Collection">
                    </div>
                    <div class="mb-2 row">
                        <div class="col-5">
                            <label for="auto_sync" class="col-form-label text-dark fs-14 fw-500">
                                <?php esc_html_e("Auto Sync", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                            <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip"
                                data-bs-placement="right"
                                title="Turn on this feature to schedule an automated product feed to keep your products up to date with the changes made in the products. You can come and change this any time.">
                                info
                            </span>
                        </div>
                        <div class="form-check form-switch col-7 mt-0 fs-5">
                            <input class="form-check-input" type="checkbox" name="autoSync" id="autoSync" checked>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-5">
                            <label for="auto_sync_interval" class="col-form-label text-dark fs-14 fw-500">
                                <?php esc_html_e("Auto Sync Interval", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                            <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip"
                                data-bs-placement="right"
                                title="Set the number of days to schedule the next auto-sync for the products in this feed. You can come and change this any time.">
                                info
                            </span>
                        </div>
                        <div class="col-7">
                            <input type="text" class="form-control-sm fs-14 " <?php echo ($plan_id === 1) ? 'readonly="readonly"' : ''; ?> name="autoSyncIntvl" id="autoSyncIntvl" size="3" min="1"
                                onkeypress="return ( event.charCode === 8 || event.charCode === 0 || event.charCode === 13 || event.charCode === 96) ? null : event.charCode >= 48 && event.charCode <= 57"
                                oninput="removeZero();"
                                value="<?php echo (isset($conv_additional_data['pro_snyc_time_limit']) && $conv_additional_data['pro_snyc_time_limit'] > 0 && $plan_id !== 1) ? esc_html(sanitize_text_field($conv_additional_data['pro_snyc_time_limit'])) : "25"; ?>">
                            <label for="" class="col-form-label fs-14 fw-400">
                                <?php esc_html_e("Days", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                            <span>
                                <?php echo ($plan_id === 1) ? '<a target="_blank" href="https://www.conversios.io/wordpress/product-feed-manager-for-woocommerce-pricing/?utm_source=app_wooPFM&utm_medium=BUSINESS&utm_campaign=Pricing"><b> Upgrade To Pro</b></a>' : ''; ?>
                            </span>
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-5">
                            <label for="target_country" class="col-form-label text-dark fs-14 fw-500" name="">
                                <?php esc_html_e("Target Country", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                            <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip"
                                data-bs-placement="right"
                                title="Specify the target country for your product feed. Select the country where you intend to promote and sell your products.">
                                info
                            </span>
                        </div>
                        <div class="col-7">
                            <select class="select2 form-select form-select-sm mb-3" aria-label="form-select-sm example"
                                style="width: 100%" name="target_country" id="target_country">
                                <option value="">Select Country</option>
                                <?php
                                $selecetdCountry = $conv_data['user_country'];
                                foreach ($contData as $key => $value) {
                                    ?>
                                    <option value="<?php echo esc_attr($value->code) ?>" <?php echo $selecetdCountry === $value->code ? 'selected = "selecetd"' : '' ?>><?php echo esc_html($value->name) ?></option>"
                                    <?php
                                }

                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="auto_sync_interval" class="col-form-label text-dark fs-14 fw-500">
                            <?php esc_html_e("Select Channel", "enhanced-e-commerce-for-woocommerce-store"); ?>
                        </label>
                        <span class="material-symbols-outlined fs-6" data-bs-toggle="tooltip" data-bs-placement="right"
                            title="Below is the list of channels that you have linked for product feed. Please note you will not be able to make any changes in the selected channels once product feed process is done.">
                            info
                        </span>
                    </div>
                    <div class="mb-3">
                        <div class="form-check form-check-custom">
                            <input class="form-check-input check-height fs-14 errorChannel" type="checkbox"
                                value="<?php echo esc_html_e($google_merchant_center_id); ?>" id="gmc_id" name="gmc_id"
                                <?php echo $google_merchant_center_id !== '' ? "checked" : 'disabled' ?>>
                            <label for="" class="col-form-label fs-14 pt-0 text-dark fw-500">
                                <?php esc_html_e("Google Merchant Center Account :", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                            <label class="col-form-label fs-14 pt-0 fw-400">
                                <?php esc_html_e($google_merchant_center_id); ?>
                            </label>
                        </div>
                        <div class="form-check form-check-custom">
                            <input class="form-check-input check-height fs-14 errorChannel" type="checkbox" value=""
                                id="tiktok_id" name="tiktok_id" <?php echo $tiktok_business_account !== '' ? "checked" : 'disabled' ?>>
                            <label for="" class="col-form-label fs-14 pt-0 text-dark fw-500">
                                <?php esc_html_e("TikTok Catalog Id :", "enhanced-e-commerce-for-woocommerce-store"); ?>
                            </label>
                            <label class="col-form-label fs-14 pt-0 fw-400 tiktok_catalog_id">

                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-2">
                    <input type="hidden" id="edit" name="edit">
                    <input type="hidden" value="<?php echo esc_attr($conv_data['user_domain']); ?>" class="fromfiled"
                        name="url" id="url" placeholder="Enter Website">
                    <input type="hidden" id="is_mapping_update" name="is_mapping_update" value="">
                    <input type="hidden" id="last_sync_date" name="last_sync_date" value="">
                    <button type="button" class="btn btn-light btn-sm border" data-bs-dismiss="modal"
                        onclick="$('#feedForm')[0].reset()">
                        <?php esc_html_e("Cancel", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </button>
                    <button type="button" class="btn btn-soft-primary btn-sm" id="submitFeed">
                        <?php esc_html_e("Create and Next", "enhanced-e-commerce-for-woocommerce-store"); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Error Save Modal -->
<div class="modal fade" id="conv_save_error_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">

            </div>
            <div class="modal-body text-center p-0">
                <img style="width:184px;"
                    src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/error_logo.png'); ?>">
                <h3 class="fw-normal pt-3">Error</h3>
                <span id="conv_save_error_txt" class="mb-1 lh-lg"></span>
            </div>
            <div class="modal-footer border-0 pb-4 mb-1">
                <button class="btn conv-yellow-bg m-auto text-white" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Error Save Modal End -->
<!-- Success Save Modal -->
<div class="modal fade" id="conv_save_success_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">

            </div>
            <div class="modal-body text-center p-0">
                <img style="width:184px;"
                    src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/logos/update_success_logo.png'); ?>">
                <h3 class="fw-normal pt-3">
                    <?php esc_html_e("Updated Successfully", "enhanced-e-commerce-for-woocommerce-store"); ?>
                </h3>
                <span id="conv_save_success_txt" class="mb-1 lh-lg"></span>
            </div>
            <div class="modal-footer border-0 pb-4 mb-1">
                <button class="btn conv-blue-bg m-auto text-white" data-bs-dismiss="modal">Ok, Done</button>
            </div>
        </div>
    </div>
</div>
<!-- Success Save Modal End -->
<script>

    jQuery(document).ready(function () {
        let plan_id = "<?php echo esc_attr($plan_id); ?>";
        /***************************Tooltip initializing here *************************************************/
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
        /*********************Card Popover Start***********************************************************************/
        jQuery(document).on('mouseover', '.synced', function () {
            var syncedGmcImg = jQuery(this).next('.syncedGmcImg').val();
            var syncedTiktokImg = jQuery(this).next('.syncedGmcImg').next('.syncedTiktokImg').val();
            var content = '<div class="popover-box border-synced">' + syncedGmcImg + '  ' + syncedTiktokImg + '</div>';
            jQuery(this).popover({
                html: true,
                template: content,
            });
            jQuery(this).popover('show');
        })

        jQuery(document).on('mouseover', '.failed', function () {
            var failedGmcImg = jQuery(this).next('.failedGmcImg').val();
            var failedTiktokImg = jQuery(this).next('.failedGmcImg').next('.failedTiktokImg').val();
            var content = "<div class='popover-box border-failed'>" + failedGmcImg + "  " + failedTiktokImg + "</div>";
            jQuery(this).popover({
                html: true,
                template: content,
            });
            jQuery(this).popover('show');
        })

        jQuery(document).on('mouseover', '.draft', function () {
            var draftGmcImg = jQuery(this).next('.draftGmcImg').val();
            var draftTiktokImg = jQuery(this).next('.draftGmcImg').next('.draftTiktokImg').val();
            var content = '<div class="popover-box border-draft">' + draftGmcImg + '  ' + draftTiktokImg + '</div>';
            jQuery(this).popover({
                html: true,
                template: content,
            });
            jQuery(this).popover('show');
        })
        jQuery(document).on('mouseover', '.inprogress', function () {
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
        /*********************Custom DataTable for Search functionality Start*********************************************/
        jQuery('#feed_list_table').DataTable({
            order: [[5, 'desc']],
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12't>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            rowReorder: true,
            columnDefs: [
                { orderable: true, targets: 0 },
                { orderable: true, targets: 1 },
                { orderable: true, targets: 2 },
                { orderable: true, targets: 4 },
                { orderable: true, targets: 5 },
                { orderable: true, targets: 6 },
                { orderable: true, targets: 7 },
                { orderable: false, targets: '_all' },
            ],

            initComplete: function () {
                $('#search_feed').on('input', function () {
                    $('#feed_list_table').DataTable().search($(this).val()).draw();
                });
            }
        });
        $('.dataTables_filter').addClass('d-none');
        /*********************Custom DataTable for Search functionality End***********************************************/
        /****************Create Feed call start********************************/
        jQuery('#create_new_feed').on('click', function (events) {
            jQuery('#target_country').attr('disabled', false);
            jQuery('#autoSyncIntvl').attr('disabled', false);
            jQuery("#feedForm")[0].reset();
            jQuery('#feedType').text('Create New Feed');
            jQuery('#submitFeed').text('Create and Next');
            jQuery('#edit').val('');
            jQuery('#tiktok_id').val('');
            jQuery('.tiktok_catalog_id').empty();
            jQuery('.tiktok_catalog_id').removeClass('text-danger');
            jQuery('#convCreateFeedModal').modal('show');
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
            jQuery('.select2').select2({ dropdownParent: $("#convCreateFeedModal") });
            var tiktok_business_account = "<?php echo $tiktok_business_account ?>";
            if (tiktok_business_account !== '' && $('#tiktok_id').is(":checked")) {
                getCatalogId(jQuery('#target_country').find(":selected").val());
            }

        });
        /****************Create Feed call end***********************************/
        /****************Feed Name error dismissed start************************/
        jQuery(document).on('input', '#feedName', function (e) {
            e.preventDefault();
            jQuery('#feedName').css('margin-left', '0px');
            jQuery('#feedName').css('margin-right', '0px');
            jQuery('#feedName').removeClass('errorInput');
        });
        /****************Feed Name error dismissed end**************************/
        /****************Submit Feed call start*********************************/
        jQuery(document).on('click', '#submitFeed', function (e) {
            e.preventDefault();
            let feedName = jQuery('#feedName').val();
            if (feedName === '') {
                jQuery('#feedName').css('margin-left', '0px');
                jQuery('#feedName').css('margin-right', '0px');
                jQuery('#feedName').addClass('errorInput');
                var l = 4;
                for (var i = 0; i <= 2; i++) {
                    $('#feedName').animate({
                        'margin-left': '+=' + (l = -l) + 'px',
                        'margin-right': '-=' + l + 'px'
                    }, 50);
                }
                return false;
            }
            
            let autoSyncIntvl = jQuery('#autoSyncIntvl').val();
            if (autoSyncIntvl === '') {
                jQuery('#autoSyncIntvl').css('margin-left', '0px');
                jQuery('#autoSyncIntvl').css('margin-right', '0px');
                jQuery('#autoSyncIntvl').addClass('errorInput');
                var l = 4;
                for (var i = 0; i <= 2; i++) {
                    $('#autoSyncIntvl').animate({
                        'margin-left': '+=' + (l = -l) + 'px',
                        'margin-right': '-=' + l + 'px'
                    }, 50);
                }
                return false;

            }

            let target_country = jQuery('#target_country').find(":selected").val();
            if (target_country === "") {
                jQuery('.select2-selection').css('border', '1px solid #ef1717');
                return false;
            }

            if (!$('#gmc_id').is(":checked") && !$('#tiktok_id').is(":checked")) {
                $('.errorChannel').css('border', '1px solid red');
                return false;
            }
            save_feed_data();
        });

        /****************Submit Feed call end***********************************/
        /********************Modal POP up validation on click remove**********************************/
        jQuery(document).on('click', '#gmc_id', function (e) {
            $('.errorChannel').css('border', '');
        });
        jQuery(document).on('click', '#tiktok_id', function (e) {
            $('.errorChannel').css('border', '');
        });
        /********************Modal POP up validation on click remove end **********************************/
        /****************Get tiktok catalog id on target country change ***************************************/
        jQuery(document).on('change', '#target_country', function (e) {
            var tiktok_business_account = "<?php echo $tiktok_business_account ?>";
            $('.select2-selection').css('border', '1px solid #c6c6c6');
            let target_country = jQuery('#target_country').find(":selected").val();
            jQuery('#tiktok_id').empty();
            jQuery('.tiktok_catalog_id').empty()
            if (target_country !== "" && tiktok_business_account !== "" && jQuery('input#tiktok_id').is(':checked')) {
                getCatalogId(target_country);
            }
        });
        /****************Get tiktok catalog id on target country change end ***************************************/
        /************************************* Auto Sync Toggle Button Start*************************************************************************/
        jQuery(document).on('change', '#autoSync', function () {
            var autoSync = jQuery('input#autoSync').is(':checked');
            if (autoSync) {
                jQuery('#autoSyncIntvl').attr('disabled', false);
            } else {
                jQuery('#autoSyncIntvl').attr('disabled', true);
                jQuery('#autoSyncIntvl').val(25);
                jQuery('#autoSyncIntvl').removeClass('errorInput');
            }
        });
        /************************************* Auto Sync Toggle Button End*************************************************************************/
        /****************Get tiktok catalog id on check box change ***************************************/
        jQuery(document).on('change', '#tiktok_id', function () {
            jQuery('.tiktok_catalog_id').empty();
            jQuery('#tiktok_id').val('');
            if ($('#tiktok_id').is(":checked")) {
                getCatalogId(jQuery('#target_country').find(":selected").val())
            }
        });
        /****************Get tiktok catalog id on check box change end ***************************************/
    });
    /*************************************Process Loader Start*************************************************************************/
    function conv_change_loadingbar(state = 'show') {
        if (state === 'show') {
            jQuery("#loadingbar_blue").removeClass('d-none');
            $("#wpbody").css("pointer-events", "none");
        } else {
            jQuery("#loadingbar_blue").addClass('d-none');
            jQuery("#wpbody").css("pointer-events", "auto");
        }
    }
    function conv_change_loadingbar_modal(state = 'show') {
        if (state === 'show') {
            jQuery("#loadingbar_blue_modal").removeClass('d-none');
            $("#wpbody").css("pointer-events", "none");
            jQuery('#submitFeed').attr('disabled', true);
        } else {
            jQuery("#loadingbar_blue_modal").addClass('d-none');
            jQuery("#wpbody").css("pointer-events", "auto");
            jQuery('#submitFeed').attr('disabled', false);
        }
    }
    /*************************************Process Loader End*************************************************************************/
    /*************************************Restrict Zero start*************************************************************************/
    function removeZero() {
        var val = $("#autoSyncIntvl").val();
        if (val === '0') {
            $("#autoSyncIntvl").val('')
        }
    }
    /*************************************Restrict Zero  End*************************************************************************/
    /*************************************Save Feed Data Start*************************************************************************/
    function save_feed_data() {
        var conv_onboarding_nonce = "<?php echo esc_html(wp_create_nonce('conv_onboarding_nonce')); ?>"
        let edit = jQuery('#edit').val()
        var planid = "<?php echo esc_attr($plan_id); ?>";
        var data = {
            action: "save_feed_data",
            feedName: jQuery('#feedName').val(),
            google_merchant_center: jQuery('input#gmc_id').is(':checked') ? '1' : '',
            tiktok_id: jQuery('input#tiktok_id').is(':checked') ? '3' : '',
            tiktok_catalog_id: jQuery('input#tiktok_id').is(':checked') ? jQuery('input#tiktok_id').val() : '',
            autoSync: jQuery('input#autoSync').is(':checked') ? '1' : '0',
            autoSyncIntvl: (planid === 1) ? '25' : jQuery('#autoSyncIntvl').val(),
            edit: edit,
            last_sync_date: jQuery('#last_sync_date').val(),
            is_mapping_update: jQuery('#is_mapping_update').val(),
            target_country: jQuery('#target_country').find(":selected").val(),
            customer_subscription_id: "<?php echo $subscriptionId ?>",
            tiktok_business_account: "<?php echo $tiktok_business_account ?>",
            conv_onboarding_nonce: conv_onboarding_nonce
        }
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: data,
            beforeSend: function () {
                conv_change_loadingbar_modal('show');
            },
            error: function (err, status) {
                conv_change_loadingbar_modal('hide');
                jQuery('#convCreateFeedModal').modal('hide');
                jQuery("#conv_save_error_txt").html('Error occured.');
                jQuery("#conv_save_error_modal").modal("show");
            },
            success: function (response) {
                if (response.id) {
                    jQuery('#convCreateFeedModal').modal('hide');
                    jQuery("#conv_save_success_txt").html("Great job! Your product feed is ready! The next step is to select the products you want to sync and expand your reach across multiple channels.");
                    jQuery("#conv_save_success_modal").modal("show");
                    setTimeout(function () {
                        if (edit !== '') {
                            location.reload(true);
                        } else {
                            window.location.replace("<?php echo esc_url_raw($site_url . 'product_list&id='); ?>" + response.id);
                        }
                    }, 100);
                } else if (response.errorType === 'tiktok') {
                    jQuery('.tiktok_catalog_id').empty();
                    jQuery('.tiktok_catalog_id').html(response.message);
                    jQuery('.tiktok_catalog_id').addClass('text-danger');

                } else {
                    jQuery('#convCreateFeedModal').modal('hide');
                    jQuery("#conv_save_error_txt").html(response.message);
                    jQuery("#conv_save_error_modal").modal("show");
                }
                conv_change_loadingbar_modal('hide');
            }
        });

    }
    /*************************************Save Feed Data End***************************************************************************/
    /*************************************Edit Feed Data Start*************************************************************************/
    function editFeed($id) {
        var conv_onboarding_nonce = "<?php echo esc_html(wp_create_nonce('conv_onboarding_nonce')); ?>"
        jQuery('#target_country').attr('disabled', false);
        jQuery('#gmc_id').attr('disabled', false);
        var data = {
            action: "get_feed_data_by_id",
            id: $id,
            conv_onboarding_nonce: conv_onboarding_nonce
        }
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: data,
            beforeSend: function () {
                conv_change_loadingbar('show');
            },
            error: function (err, status) {
                conv_change_loadingbar('hide');
                jQuery("#conv_save_error_txt").html('Error occured.');
                jQuery("#conv_save_error_modal").modal("show");
            },
            success: function (response) {
                jQuery('#feedName').val(response[0].feed_name);
                jQuery('#last_sync_date').val(response[0].last_sync_date);
                jQuery('#is_mapping_update').val(response[0].is_mapping_update);
                jQuery('#autoSyncIntvl').val(response[0].auto_sync_interval);
                if (response[0].target_country) {
                    jQuery('#target_country').val(response[0].target_country);
                }

                if (response[0].auto_schedule === '1') {
                    jQuery('input#autoSync').prop('checked', true);
                    jQuery('#autoSyncIntvl').attr('disabled', false);
                } else {
                    jQuery('input#autoSync').prop('checked', false);
                    jQuery('#autoSyncIntvl').attr('disabled', true);
                }
                jQuery('#gmc_id').prop("checked", false);
                jQuery('#gmc_id').attr('disabled', false);
                jQuery('#tiktok_id').prop("checked", false);
                jQuery('#tiktok_id').attr('disabled', false);
                jQuery('.tiktok_catalog_id').empty();
                channel_id = response[0].channel_ids.split(",");
                $.each(channel_id, function (index, val) {
                    if (val === '1') {
                        jQuery('#gmc_id').prop("checked", true);
                    }
                    if (val === '3') {
                        jQuery('#tiktok_id').prop("checked", true);
                        jQuery('#tiktok_id').val(response[0].tiktok_catalog_id);
                        jQuery('.tiktok_catalog_id').html(response[0].tiktok_catalog_id)
                    }
                });
                if (response[0].is_mapping_update == '1') {
                    jQuery('#gmc_id').attr('disabled', true);
                    jQuery('#tiktok_id').attr('disabled', true);
                    jQuery('#target_country').attr('disabled', true);
                }
                jQuery('#edit').val(response[0].id);
                jQuery('#centered').html();
                jQuery('#submitFeed').text('Update Feed');
                jQuery('#feedType').text('Edit Feed - ' + response[0].feed_name);
                conv_change_loadingbar('hide');
                jQuery('#target_country').select2({ dropdownParent: $("#convCreateFeedModal") });;
                jQuery('#convCreateFeedModal').modal('show');
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })
            }
        });
    }
    /*************************************Edit Feed Data End****************************************************************************/
    /*************************************Duplicate Feed Data Start*********************************************************************/
    function duplicateFeed($id) {
        var planid = "<?php echo esc_attr($plan_id); ?>";
        var conv_onboarding_nonce = "<?php echo esc_html(wp_create_nonce('conv_onboarding_nonce')); ?>"
        var data = {
            action: "ee_duplicate_feed_data_by_id",
            id: $id,
            conv_onboarding_nonce: conv_onboarding_nonce
        }
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: data,
            beforeSend: function () {
                conv_change_loadingbar('show');
            },
            error: function (err, status) {
                conv_change_loadingbar('hide');
                jQuery("#conv_save_error_txt").html('Error occured.');
                jQuery("#conv_save_error_modal").modal("show");
            },
            success: function (response) {
                conv_change_loadingbar('hide');
                if (response.error === false) {
                    jQuery("#conv_save_success_txt").html(response.message);
                    jQuery("#conv_save_success_modal").modal("show");
                    setTimeout(function () {
                        location.reload(true);
                    }, 2000);
                } else {
                    jQuery("#conv_save_error_txt").html(response.message);
                    jQuery("#conv_save_error_modal").modal("show");
                }
            }
        });
    }
    /*************************************Duplicate Feed Data End*********************************************************************/
    /*************************************DELETE Feed Data Satrt**********************************************************************/
    function deleteFeed($id) {
        if (confirm('Products in this feed will be deleted from the channels selected in the feed. Are you sure you want to delete it? By this action, you will not be able to retrieve this feed again.')) {
            var conv_onboarding_nonce = "<?php echo esc_html(wp_create_nonce('conv_onboarding_nonce')); ?>"
            var data = {
                action: "ee_delete_feed_data_by_id",
                id: $id,
                conv_onboarding_nonce: conv_onboarding_nonce
            }
            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: tvc_ajax_url,
                data: data,
                beforeSend: function () {
                    conv_change_loadingbar('show');
                },
                error: function (err, status) {
                    conv_change_loadingbar('hide');
                    jQuery("#conv_save_error_txt").html('Error in Deleting Feed.');
                    jQuery("#conv_save_error_modal").modal("show");
                },
                success: function (response) {
                    conv_change_loadingbar('hide');
                    jQuery("#conv_save_success_txt").html(response.message);
                    jQuery("#conv_save_success_modal").modal("show");
                    setTimeout(function () {
                        location.reload(true);
                    }, 1000);
                }
            });
        }
    }
    /*************************************Delete Feed Data End*************************************************************************/
    /*************************************Get saved catalog id by country code start **************************************************/
    function getCatalogId($countryCode) {
        var conv_country_nonce = "<?php echo esc_html(wp_create_nonce('conv_country_nonce')); ?>";
        var data = {
            action: "ee_getCatalogId",
            countryCode: $countryCode,
            conv_country_nonce: conv_country_nonce
        }
        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: tvc_ajax_url,
            data: data,
            beforeSend: function () {
                conv_change_loadingbar_modal('show');
            },
            error: function (err, status) {
                //conv_change_loadingbar_modal('hide');
            },
            success: function (response) {
                jQuery('.tiktok_catalog_id').empty()
                jQuery('#tiktok_id').empty();
                jQuery('.tiktok_catalog_id').removeClass('text-danger');

                if (response.error == false) {
                    if (response.data.catalog_id !== '') {
                        jQuery('#tiktok_id').val(response.data.catalog_id);
                        jQuery('.tiktok_catalog_id').text(response.data.catalog_id)
                    } else {
                        jQuery('#tiktok_id').val('Create New');
                        jQuery('.tiktok_catalog_id').text('You do not have a catalog associated with the selected target country. Do not worry we will create a new catalog for you.');
                    }
                }
                conv_change_loadingbar_modal('hide');
            }
        });
    }
    /*************************************Get saved catalog id by country code End ****************************************************/
</script>