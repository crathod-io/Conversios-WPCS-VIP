<!-- sst web container detail view modal -->
<div class="modal fade" id="importContainerModal" tabindex="-1" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog importContainerDetail modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importContainerModalLabel">Import Container</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- <div class="blg-caption preview-confirm-input"> Preview and confirm your import </div> -->
                    <div class="col-xl-3 col-lg-6 col-md-12 col-12">
                        <div class="card p-0">
                            <h5 class="card-header tags-header conv-blue-bg conv-text-white m-0 conv-d-flex conv-justify-space-between fw-bold-600">Tags<span class="material-symbols-outlined conv-font-22">
                                    label
                                </span></h5>
                            <div class="card-body">
                                <div class="row">
                                    <div class="gtm-container-import-entity-body">
                                        <div class="row conv-text-center">
                                            <div class="col-md-3 p-0">
                                                <div class="blg-subhead web-tag-count"> </div>
                                            </div>
                                            <div class="col-md-5 p-0">
                                                <div class="blg-subhead ">0 </div>
                                            </div>
                                            <div class="col-md-4 p-0">
                                                <div class="blg-subhead"> 0</div>
                                            </div>
                                        </div>
                                        <div class="row conv-text-center">
                                            <div class="col-md-3 p-0">
                                                <div class="blg-body blg-spacer1 fw-bold-500"> New </div>
                                            </div>
                                            <div class="col-md-5 p-0">
                                                <div class="blg-body blg-spacer1 fw-bold-500"> Modified </div>
                                            </div>
                                            <div class="col-md-4 p-0">
                                                <div class="blg-body blg-spacer1 fw-bold-500"> Deleted </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row pt-3">
                                    <div class="collapse collapseExample">
                                        <div class="gtm-container-import-detail" aria-hidden="false">
                                            <div class="gtm-container-import-detail-separator conv-b-grey"></div> <!---->
                                            <div class="blg-body-med fw-bold-600"> New tags </div>
                                            <div class="gtm-container-import-detail-separator conv-b-grey"></div> <!---->
                                            <div class="web-tag-list pt-2">

                                            </div>


                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-12 col-12">
                        <div class="card p-0">
                            <h5 class="card-header triggers-header conv-green-bg conv-text-white m-0 conv-d-flex conv-justify-space-between fw-bold-600">Triggers<span class="material-symbols-outlined conv-font-22">
                                    power_settings_new
                                </span></h5>
                            <div class="card-body">
                                <div class="row">
                                    <div class="gtm-container-import-entity-body">
                                        <div class="row conv-text-center">
                                            <div class="col-md-3 p-0">
                                                <div class="blg-subhead web-trigger-count"> </div>
                                            </div>
                                            <div class="col-md-5 p-0">
                                                <div class="blg-subhead">0 </div>
                                            </div>
                                            <div class="col-md-4 p-0">
                                                <div class="blg-subhead"> 0</div>
                                            </div>
                                        </div>
                                        <div class="row conv-text-center">
                                            <div class="col-md-3 p-0">
                                                <div class="blg-body blg-spacer1 fw-bold-500"> New </div>
                                            </div>
                                            <div class="col-md-5 p-0">
                                                <div class="blg-body blg-spacer1 fw-bold-500"> Modified </div>
                                            </div>
                                            <div class="col-md-4 p-0">
                                                <div class="blg-body blg-spacer1 fw-bold-500"> Deleted </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row pt-3">
                                    <div class="collapse collapseExample">
                                        <div class="gtm-container-import-detail" aria-hidden="false">
                                            <div class="gtm-container-import-detail-separator conv-b-grey"></div> <!---->
                                            <div class="blg-body-med fw-bold-600"> New triggers </div>
                                            <div class="gtm-container-import-detail-separator conv-b-grey"></div>
                                            <div class="web-trigger-list pt-2">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-12 col-12">
                        <div class="card p-0">
                            <h5 class="card-header variables-header conv-orange-bg conv-text-white m-0 conv-d-flex conv-justify-space-between fw-bold-600">Variables<img src="<?php echo esc_url_raw(ENHANCAD_PLUGIN_URL . '/admin/images/icon/variables.png'); ?>" /></h5>
                            <div class="card-body">
                                <div class="row">
                                    <div class="gtm-container-import-entity-body">
                                        <div class="row conv-text-center">
                                            <div class="col-md-3 p-0">
                                                <div class="blg-subhead web-variable-count"> 48 </div>
                                            </div>
                                            <div class="col-md-5 p-0">
                                                <div class="blg-subhead">0 </div>
                                            </div>
                                            <div class="col-md-4 p-0">
                                                <div class="blg-subhead"> 0</div>
                                            </div>
                                        </div>
                                        <div class="row conv-text-center">
                                            <div class="col-md-3 p-0">
                                                <div class="blg-body blg-spacer1 fw-bold-500"> New </div>
                                            </div>
                                            <div class="col-md-5 p-0">
                                                <div class="blg-body blg-spacer1 fw-bold-500"> Modified </div>
                                            </div>
                                            <div class="col-md-4 p-0">
                                                <div class="blg-body blg-spacer1 fw-bold-500"> Deleted </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row pt-3">
                                    <div class="collapse collapseExample">
                                        <div class="gtm-container-import-detail" aria-hidden="false">
                                            <div class="gtm-container-import-detail-separator conv-b-grey"></div> <!---->
                                            <div class="blg-body-med fw-bold-600"> New Variables </div>
                                            <div class="gtm-container-import-detail-separator conv-b-grey"></div>
                                            <div class="web-variable-list pt-2">

                                            </div>

                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-6 col-md-12 col-12">
                        <div class="card p-0">
                            <h5 class="card-header templates-header conv-purple-bg conv-text-white m-0 conv-d-flex conv-justify-space-between fw-bold-600">Templates<span class="material-symbols-outlined conv-font-20">empty_dashboard</span></h5>
                            <div class="card-body">
                                <div class="row">
                                    <div class="gtm-container-import-entity-body">
                                        <div class="row conv-text-center">
                                            <div class="col-md-3 p-0">
                                                <div class="blg-subhead web-template-count"> 7 </div>
                                            </div>
                                            <div class="col-md-5 p-0">
                                                <div class="blg-subhead"> 0 </div>
                                            </div>
                                            <div class="col-md-4 p-0">
                                                <div class="blg-subhead"> 0 </div>
                                            </div>
                                        </div>
                                        <div class="row conv-text-center">
                                            <div class="col-md-3 p-0">
                                                <div class="blg-body blg-spacer1 fw-bold-500"> New </div>
                                            </div>
                                            <div class="col-md-5 p-0">
                                                <div class="blg-body blg-spacer1 fw-bold-500"> Modified </div>
                                            </div>
                                            <div class="col-md-4 p-0">
                                                <div class="blg-body blg-spacer1 fw-bold-500"> Deleted </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row pt-3">
                                    <div class="collapse collapseExample">
                                        <div class="gtm-container-import-detail" aria-hidden="false">
                                            <div class="gtm-container-import-detail-separator conv-b-grey"></div>
                                            <div class="blg-body-med fw-bold-600"> New Templates </div>
                                            <div class="gtm-container-import-detail-separator conv-b-grey"></div>
                                            <div class="web-customTemplate-list pt-2">

                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer conv-justify-space-between">
                <a class="fw-bold-600" data-bs-toggle="collapse" id="details-collapse" href=".collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">View Details</a>
                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button> -->
                <button type="button" class="btn conv-text-white conv-blue-btn" data-bs-dismiss="modal" id="create_container_back_btn">
                    <span>
                        Back
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>