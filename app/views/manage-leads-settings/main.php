<div class="d-flex flex-column bg-white  bsapp-settings-panel  bsapp-page-tab" data-page-id="js-tabs-home"
     data-depth="0">
    <div class="d-flex justify-content-between bsapp-fs-20 bsapp-lh-24">
        <span class="font-weight-bold"><?php echo lang('lead_settings_title') ?></span>
        <a href="javascript:;" onclick="LeadsSettings.closeSettings(this)" class="text-black text-decoration-none">
            <i class="fal fa-times"></i>
        </a>
    </div>
    <div class="py-8 pt-15 border-bottom border-light ">
        <div class="d-flex">
            <i class="fal fa-filter mie-10 bsapp-fs-20"></i>
            <h6 class="bsapp-fs-16 bsapp-lh-22 mb-6 font-weight-bolder"><?php echo lang('lead_salce_proccess') ?></h6>
        </div>
        <div class="bsapp-fs-13 bsapp-lh-15 text-gray mb-4"><?php echo lang('lead_status_description') ?></div>
        <a class="d-flex align-items-center text-primary text-decoration-none font-weight-bolder py-3" href="javascript:;" onclick="LeadsSettings.goToPipeLines(this, event);"
           data-next="js-tabs-pipe-categories"><?php echo lang('manage') ?><i class="fal fa-angle-right mx-5 bsapp-fs-24"></i></a>
    </div>
    <div class="py-8 pt-10 border-bottom border-light">
        <div class="d-flex">
            <i class="fal fa-sparkles mie-10 bsapp-fs-20"></i>
            <h6 class="bsapp-fs-16 bsapp-lh-22 mb-6 font-weight-bolder"><?php echo lang('settings_lead_source') ?></h6>
        </div>
        <div class="bsapp-fs-13 bsapp-lh-15 text-gray mb-4"><?php echo lang('lead_source_description') ?></div>
        <a class="d-flex align-items-center text-primary text-decoration-none font-weight-bolder py-3" href="javascript:;" onclick="LeadsSettings.goToLeadSources(this, event);"
           data-next="js-tabs-lead-source"><?php echo lang('manage') ?><i class="fal fa-angle-right mx-5 bsapp-fs-24"></i></a>
    </div>
    <div class="py-8 pt-10 border-bottom border-light">
        <div class="d-flex">
            <i class="fab fa-facebook-square mie-10 bsapp-fs-20"></i>
            <h6 class="bsapp-fs-16 bsapp-lh-22 mb-6 font-weight-bolder"><?php echo lang('settings_facebook') ?></h6>
        </div>
        <div class="bsapp-fs-13 bsapp-lh-15 text-gray mb-4"><?php echo lang('facebook_description') ?></div>
        <a class="d-flex align-items-center text-primary text-decoration-none font-weight-bolder py-3" href="javascript:;" onclick="LeadsSettings.goToFacebook(this, event);"
           data-next="js-tabs-facebook"><?php echo lang('manage') ?><i class="fal fa-angle-right mx-5 bsapp-fs-24"></i></a>
    </div>
</div>