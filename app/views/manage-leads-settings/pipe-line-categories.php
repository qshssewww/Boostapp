<div class="d-none flex-column justify-content-between bg-white h-100  bsapp-settings-panel  bsapp-page-tab animated slideInStart"
     data-page-id="js-tabs-pipe-categories" data-depth="1">
    <div class="d-flex justify-content-between bsapp-fs-20 bsapp-lh-24 mb-2">
        <a class="text-black d-flex text-decoration-none font-weight-bold" href="javascript:;"
           onclick="LeadsSettings.goTo(this, event);" data-next="js-tabs-home"><i class="fal fa-angle-left mie-7 bsapp-fs-24"></i><?php echo lang('back_new_add_credit') ?></a>
        <a href="javascript:;" onclick="LeadsSettings.closeSettings(this)" class="text-black  text-decoration-none">
            <i class="fal fa-times"></i>
        </a>
    </div>
    <div class="" style="height:calc( 100% - 60px);">
        <div class="d-flex flex-column mt-20">
            <h6 class="bsapp-fs-14 bsapp-lh-17 font-weight-bold"><i class="fal fa-filter"></i> <?php echo lang('lead_salce_proccess') ?></h6>
        </div>
        <div class="d-flex px-8 mt-16 mb-10">
            <span class="bsapp-fs-14 bsapp-lh-17" style="width:176px;"><?php echo lang('store_name') ?></span>
            <span class="bsapp-fs-14 bsapp-lh-17">ID</span>
        </div>
        <div class="scrollable" style="overflow-y: auto; width: calc(100% + 3em); padding-inline-end: 3em; height: 82%; ">
            <ul class="lead-scale-process-list list-unstyled p-0 bsapp-fs-14">

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


                <li class="js-fields js-part-view justify-content-between mb-12 align-items-center js-sortable-item item-example d-none" data-id="" data-type="pipe-line">
                    <div class="h-40p text-gray-700  d-flex justify-content-between align-items-center border px-8" style="width:100%;border-color:#c6c6c6;border-radius: 6px;">
                        <div class="js-text-div js-item-name text-overflow-hidden" style="width:155px;"></div>
                        <div class="bsapp-fs-14 bsapp-lh-17 js-item-id" style="width:70px;"></div>
                        <div class="">
                            <div class="custom-control custom-switch js-item-status">
                                <input type="checkbox" class="custom-control-input" id="js-switch-id-" onchange="LeadsSettings.changeStatus(this, event)">
                                <label class="custom-control-label" for="js-switch-id-" role="button"></label>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <a href="javascript:;" class="bsapp-fs-20 text-gray-500 dropdown-toggle" data-toggle="dropdown">
                                <i class="far fa-ellipsis-v bsapp-fs-20"></i>
                            </a>
                            <div class="dropdown-menu  text-start">
                                <a class="dropdown-item text-gray-700  px-8"
                                   onclick="LeadsSettings.goToPipeLinePage(this, event, false);" href="javascript:;"
                                        data-next="js-tabs-one-pipe-category-page" href="javascript:;">
                                    <i class="fal fa-edit fa-fw mx-5"></i>
                                    <span> <?php echo lang('edit') ?> </span>
                                </a>
                                <a class="dropdown-item  px-8 text-gray-700 js-item-text-status"
                                   onclick="LeadsSettings.changeStatus(this, event);" href="javascript:;">
                                </a>
                            </div>
                        </div>
                    </div>
                </li>

            </ul>
        </div>
    </div>
    <div class="">
        <a class="btn btn-lg bg-light text-gray-700 rounded-lg font-weight-bolder shadow-none border-0 w-100 bsapp-fs-16" href="javascript:;" onclick="LeadsSettings.goToPipeLinePage(this, event, true);"
           data-next="js-tabs-one-pipe-category-page">+ <?php echo lang('create_new') ?>
        </a>
    </div>
</div>