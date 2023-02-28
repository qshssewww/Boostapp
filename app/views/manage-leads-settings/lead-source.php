<div class="d-none flex-column  bsapp-settings-panel  bsapp-page-tab" data-page-id="js-tabs-lead-source" data-depth="1">
    <div class="d-flex justify-content-between bsapp-fs-20 bsapp-lh-24 mb-2 pb-17">
        <a class="text-black d-flex text-decoration-none font-weight-bold" href="javascript:;"
           onclick="LeadsSettings.goTo(this, event);" data-next="js-tabs-home">
                <i class="fal fa-angle-left mie-7 bsapp-fs-24"></i><?php echo lang('back_new_add_credit') ?>
        </a>
        <a href="javascript:;" onclick="LeadsSettings.closeSettings(this)" class="text-black  text-decoration-none">
            <i class="fal fa-times"></i>
        </a>
    </div>
    <div class="d-flex  bsapp-fs-14 bsapp-lh-17  align-items-center font-weight-bold">
        <div class="mie-7"><i class="fas fa-sparkles text-gray-500"></i></div>
        <span><?php echo lang('settings_lead_source') ?></span>
    </div>
    <div class="bsapp-overflow-y-auto bsapp-scroll" style="height:calc( 100% - 60px);">
        <div class="d-flex px-8 mt-16 mb-10">
            <span class="bsapp-fs-14 bsapp-lh-17" style="width:176px;"><?php echo lang('store_name') ?></span>
            <span class="bsapp-fs-14 bsapp-lh-17">ID</span>
        </div>

        <div class="scrollable" style="overflow-y: auto; width: calc(100% + 3em); padding-inline-end: 3em; ">
            <ul class="lead-sources-list list-unstyled p-0 bsapp-fs-14">

                <li class="item-loading item-placeholder mb-12 animated fadeInUp">
                    <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-12 px-10 bsapp-fs-14">
                        <div class="spinner-border spinner-border-sm text-success" role="status">
                            <span class="sr-only"><?php echo lang('loading') ?></span>
                        </div>
                    </div>
                </li>
                <li class="item-loading item-placeholder mb-12 animated fadeInUp delay-1">
                    <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-12 px-10 bsapp-fs-14">
                        <div class="spinner-border spinner-border-sm text-success" role="status">
                            <span class="sr-only"><?php echo lang('loading') ?></span>
                        </div>
                    </div>
                </li>
                <li class="item-loading item-placeholder mb-12 animated fadeInUp delay-2">
                    <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-12 px-10 bsapp-fs-14">
                        <div class="spinner-border spinner-border-sm text-success" role="status">
                            <span class="sr-only"><?php echo lang('loading') ?></span>
                        </div>
                    </div>
                </li>


                <li class="js-fields item-example d-none" data-id="" data-type="lead-source">
                    <div class="bsapp-editable-field js-editable-item">
                        <div class="js-part-view w-100 h-40p text-gray-700 mb-12 d-flex justify-content-between align-items-center border px-8"
                             style="border-color:#c6c6c6;border-radius: 6px;">
                            <div style="width:155px;" class="js-text-div js-item-name text-overflow-hidden"></div>
                            <div class="bsapp-fs-14 bsapp-lh-17 js-item-id" style="width:70px;"></div>
                            <div class="">
                                <div class="custom-control custom-switch status-lead-source js-item-status">
                                    <input type="checkbox" class="custom-control-input" id="js-switch-id"
                                           onchange="LeadsSettings.changeStatus(this, event)">
                                    <label class="custom-control-label" for="js-switch-id" role="button"></label>
                                </div>
                            </div>
                            <div class="d-flex align-items-center ">
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
                        <div class="js-part-edit w-100 h-40p text-gray-700 mb-12 d-none justify-content-between align-items-center border px-8 border border-success"
                             style="border-radius: 6px;">
                            <input class="border-0 shadow-0 form-control flex-fill bsapp-edit-input js-input-div" maxlength="70" onchange="LeadsSettings.addClassChanged(this)"
                                   placeholder="<?php echo lang('enter_name') ?>"/>
                            <div class="bsapp-fs-22 d-flex" style="width:70px;">
                                <a class="mie-20 text-gray-700" onclick="LeadsSettings.cancelEdit(this, event);">
                                    <i class="fal fa-minus-circle"></i>
                                </a>
                                <a class="text-success" onclick="LeadsSettings.saveLeadSource(this, event);">
                                    <i class="fal fa-check-circle"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </li>


            </ul>
        </div>
        <div class="">
            <a class="text-gray-700 font-weight-bold text-decoration-none" href="javascript:;" data-copy-container=".js-fields"
               data-copy-item="js-design-editable-item" onclick="LeadsSettings.addLeadSource(this, event);">
                <?php echo lang('add_new_source_settings') ?>
                </a>
        </div>
    </div>
</div>