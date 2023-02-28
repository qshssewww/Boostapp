<div class="d-none flex-column justify-content-between h-100  bsapp-settings-panel  bsapp-page-tab js-facebook-details-page"
     data-page-id="js-tabs-facebook-one-page" data-depth="3">
    <div class="d-flex justify-content-between bsapp-fs-20 bsapp-lh-24 mb-2 pb-17">
        <a class="text-black d-flex text-decoration-none font-weight-bold" href="javascript:;"
           onclick="LeadsSettings.goTo(this, event);" data-next="js-tabs-facebook"><i
                    class="fal fa-angle-left mie-7 bsapp-fs-24"></i><?php echo lang('back_new_add_credit') ?></a>
        <a href="javascript:;" onclick="LeadsSettings.closeSettings(this)" class="text-black  text-decoration-none">
            <i class="fal fa-times"></i>
        </a>
    </div>
    <div class="mb-16">
        <div class="d-flex  bsapp-fs-14 bsapp-lh-17  align-items-center font-weight-bold">
            <div class="mie-7"><i class="fab fa-facebook-square text-gray-500"></i></div>
            <span><?php echo lang('settings_facebook') ?></span>
        </div>
    </div>
    <div class="scrollable bsapp-overflow-y-auto bsapp-scroll h-100" style="overflow-y: auto; width: calc(100% + 3em); padding-inline-end: 3em; ">
    <form>
        <input class="js-item-page-id" type="hidden" name="pageId" value="">
        <div class="" style="height:100%;">
            <div class="d-flex justify-content-between align-items-center mb-20">
                <span class="bsapp-fs-18 bsapp-lh-22 js-item-title-page"></span>
                <div class="">
                    <div class="custom-control custom-switch js-item-status-page">
                        <input type="checkbox" class="custom-control-input" id="js-facebook-checks">
                        <label class="custom-control-label" for="js-facebook-checks"></label>
                    </div>
                </div>
            </div>
            <div class="pb-20 border-light border-bottom js-default-routing-fb">
                <h6><?php echo lang('facebook_rout_leads') ?></h6>
                <div class="mb-15 js-item-branch">
                    <?php echo lang('branch') ?>
                    <select class=" js-select2" name="branch" data-branch>
                    </select>
                </div>
                <div class="d-flex">
                    <div class="mie-16 flex-fill js-item-name-pipe-line">
                        <div><?php echo lang('facebook_pipeline') ?></div>
                        <select class="js-select2" data-categories onchange="FBFunction.changePipeLineFB(this)">
                        </select>
                    </div>
                    <div class="flex-fill js-item-lead-status" data-pipeline>
                        <div><?php echo lang('facebook_lead_stage') ?></div>
                        <select class="js-select2">
                        </select>
                    </div>
                </div>
            </div>

            <div class="js-list-forms py-20">
                <h6><?php echo lang('rout_leads_by_forms') ?></h6>
                <div class="my-10 rounded border px-10 py-6 js-one-form-fb js-item-example d-none">
                    <div class="js-name-form-fb" onclick="FBFunction.openCard(this)" role="button">
                        <span></span>
                        <span class="float-left"><i class="fas fa-angle-down"></i></span>
                    </div>

                    <div class="js-card-data-page p-15 d-none">
                        <div class="mb-15 js-item-branch">
                            <?php echo lang('branch') ?>
                            <select class="" name="branch" data-branch>
                            </select>
                        </div>
                        <div class="d-flex">
                            <div class="mie-16 flex-fill js-item-name-pipe-line ">
                                <div><?php echo lang('facebook_pipeline') ?></div>
                                <select class="" data-categories onchange="FBFunction.changePipeLineFB(this)">
                                </select>
                            </div>
                            <div class="flex-fill js-item-lead-status">
                                <div><?php echo lang('facebook_lead_stage') ?></div>
                                <select class="" data-pipeline>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    </div>
    <div class="d-flex">
        <a class="btn btn-white flex-fill mie-16 d-flex align-items-center justify-content-center"
           onclick="LeadsSettings.goTo(this, event);" data-next="js-tabs-facebook" href="javascript:;"
           style="height:48px;border-radius: 4px;border-color: #191919;"> <?php echo lang('cancel') ?> </a>
        <a class="btn btn-success  flex-fill d-flex align-items-center justify-content-center"
           onclick="FBFunction.saveFBPage(this, event);" data-next="js-tabs-facebook" href="javascript:;"
           style="height:48px;border-radius: 4px;"> <?php echo lang('save') ?> </a>
    </div>
</div>