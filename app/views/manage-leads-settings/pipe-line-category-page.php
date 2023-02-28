<div class="d-none flex-column justify-content-between h-100  bsapp-settings-panel  bsapp-page-tab js-pipe-line-settings-page"
     data-page-id="js-tabs-one-pipe-category-page" data-depth="2" data-pipe-id="">
<!--    <div class="js-disabled">-->
    <div class="bsapp-overlay-loader js-loader d-none">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only"><?php echo lang('loading') ?></span>
        </div>
    </div>
        <div class="d-flex justify-content-between bsapp-fs-20 bsapp-lh-24 mb-2 pb-17">
            <a class="text-black d-flex text-decoration-none font-weight-bold js-go-to-pipe-line-categories" href="javascript:;"
               onclick="LeadsSettings.closePageAndSaveTitle(this, event, true);" data-next="js-tabs-pipe-categories"><i
                        class="fal fa-angle-left mie-7 bsapp-fs-24"></i><?php echo lang('back_new_add_credit') ?></a>
            <a href="javascript:;" onclick="LeadsSettings.closePageAndSaveTitle(this, event, false)"
               class="text-black  text-decoration-none">
                <i class="fal fa-times"></i>
            </a>
        </div>
        <div class="bsapp-overflow-y-auto bsapp-scroll h-100">
            <div class="d-flex  bsapp-fs-14 bsapp-lh-17  align-items-center justify-content-between font-weight-bold">
                <i class="fal fa-filter"></i>
                <div style="width:calc(100% - 25px);" class="js-title-pipe-line-page"><?php echo lang('lead_salce_proccess') ?></div>
            </div>
            <div class="d-flex  mt-16 mb-14 align-items-center justify-content-between">
                <i class="fal fa-bookmark"></i>
                <input class="bg-light form-control shadow-none border border-light bsapp-br-3 js-name-pipe-line"
                       value=""
                       placeholder="" style="width:calc(100% - 25px);"
                       maxlength="70"
                       onchange="LeadsSettings.addClassChanged(this)">
            </div>
            <div class="bsapp-fs-13 bsapp-lh-15 text-gray"><?php echo lang('pipeline_status_description') ?></div>
            <div class="d-flex justify-content-between  mt-14 mb-10">
                <div style="width:55px;">#</div>
                <div class="d-flex" style="width:calc(100% - 25px);">
                    <span class="bsapp-fs-14 bsapp-lh-17" style="width:160px;"><?php echo lang('store_name') ?></span>
                    <span class="bsapp-fs-14 bsapp-lh-17">ID</span>
                </div>
            </div>

            <div class="scrollable" style="overflow-y: auto; width: calc(100% + 3em); padding-inline-end: 3em; ">
                <ul id="js_sortable_container" class="lead-status-list list-unstyled p-0 bsapp-fs-14">

                    <li class="item-loading item-placeholder mb-10 animated fadeInUp">
                        <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
                            <div class="spinner-border spinner-border-sm text-success" role="status">
                                <span class="sr-only"><?php echo lang('loading') ?></span>
                            </div>
                        </div>
                    </li>
                    <li class="item-loading item-placeholder mb-10 animated fadeInUp delay-1">
                        <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
                            <div class="spinner-border spinner-border-sm text-success" role="status">
                                <span class="sr-only"><?php echo lang('loading') ?></span>
                            </div>
                        </div>
                    </li>
                    <li class="item-loading item-placeholder mb-10 animated fadeInUp delay-2">
                        <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
                            <div class="spinner-border spinner-border-sm text-success" role="status">
                                <span class="sr-only"><?php echo lang('loading') ?></span>
                            </div>
                        </div>
                    </li>

                    <li class="js-fields js-sortable-item item-example d-none" data-id="" data-type="lead-status">
                        <div class=" d-flex justify-content-between mb-12 align-items-center bsapp-editable-field js-editable-item">
                            <div class="js-item-key"></div>
                            <div class=" js-part-view h-40p text-gray-700  d-flex justify-content-between align-items-center border px-8"
                                 style="width:calc(100% - 25px);border-color:#c6c6c6;border-radius: 6px;">
                                <div class="js-grip-handle" style="color:#c6c6c6;" role="button"><i class="fas fa-grip-vertical"></i>
                                </div>
                                <div class="js-text-div js-item-name text-overflow-hidden" style="width:150px;"></div>
                                <div class="bsapp-fs-14 bsapp-lh-17 js-item-id" style="width:70px;"></div>
                                <div class="">
                                    <div class="custom-control custom-switch js-item-status">
                                        <input type="checkbox" class="custom-control-input" id="toggle-id-"
                                               onchange="LeadsSettings.changeStatus(this, event)">
                                        <label class="custom-control-label" for="toggle-id-" role="button"></label>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <a href="javascript:;" class="bsapp-fs-20 text-gray-500 dropdown-toggle"
                                       data-toggle="dropdown">
                                        <i class="far fa-ellipsis-v bsapp-fs-20"></i>
                                    </a>
                                    <div class="dropdown-menu  text-start">
                                        <a class="dropdown-item text-gray-700  px-8"
                                           onclick="LeadsSettings.showEdit(this, event);" href="javascript:;">
                                            <i class="fal fa-edit fa-fw mx-5"></i>
                                            <span> <?php echo lang('edit') ?> </span>
                                        </a>
                                        <a class="dropdown-item  px-8 text-gray-700 js-item-text-status"
                                           onclick="LeadsSettings.changeStatus(this, event);" href="javascript:;">
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="js-part-edit h-40p text-gray-700 d-none justify-content-between align-items-center border px-8 border border-success "
                                 style="width:calc(100% - 25px);border-radius: 6px;">
                                <input class="border-0 shadow-0 form-control flex-fill bsapp-edit-input js-input-div" maxlength="70" onchange="LeadsSettings.addClassChanged(this)"
                                       placeholder="<?php echo lang('enter_name') ?>"/>
                                <div class="bsapp-fs-22 d-flex" style="width:70px;">
                                    <a class="mie-20 text-gray-700" onclick="LeadsSettings.cancelStepEdit(this, event);"><i
                                                class="fal fa-minus-circle"></i></a>
                                    <a class="text-success" onclick="LeadsSettings.saveLeadStatus(this, event);"><i
                                                class="fal fa-check-circle"></i></a>
                                </div>
                            </div>
                            <div class="js-part-loading item-loading item-placeholder d-none"
                                 style="width:calc(100% - 25px);border-radius: 6px;">
                                <div class=" form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-12 px-8 bsapp-fs-14">
                                    <div class="spinner-border spinner-border-sm text-success" role="status">
                                        <span class="sr-only"><?php echo lang('loading') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>

                </ul>
            </div>
            <div class="mt-4 pb-20 mb-20 border-bottom border-light">
                <a class="text-gray-700 font-weight-bold text-decoration-none" href="javascript:;"
                   onclick="LeadsSettings.addLeadStatus(this, event);" data-copy-container="#js_sortable_container"
                   data-copy-item="js-design-editable-draggable-item"><?php echo lang('add_stage_leads') ?></a>
            </div>
            <div class="mb-20 font-weight-bold"><?php echo lang('fix_stage_leads') ?></div>
            <ul class="static-lead-status-list list-unstyled p-0 bsapp-fs-14">
                <li class="justify-content-between mb-12 align-items-center item-example d-none" data-id="">
                    <div>-</div>
                    <div class="h-40p text-gray-700  d-flex align-items-center border px-8"
                         style="width:calc(100% - 25px);border-color:#c6c6c6;border-radius: 6px;">
                        <div style="width:20px;"></div>
                        <div style="width:160px;" class="js-item-name"></div>
                        <div class="bsapp-fs-14 bsapp-lh-17 js-item-id" style="width:70px;"></div>
                    </div>
                </li>
            </ul>
        </div>
        <div id="js-button-add-pipe-line-category" class="d-none" data-next="js-tabs-pipe-categories">
            <a class="btn btn-lg btn-primary text-white rounded-lg font-weight-bolder shadow-none border-0 w-100 bsapp-fs-16"
               href="javascript:;" onclick="LeadsSettings.addPipeLine(this, event);"
               data-next="js-tabs-one-pipe-category-page"><?php echo lang('save') ?>
            </a>
        </div>
<!--    </div>-->
</div>