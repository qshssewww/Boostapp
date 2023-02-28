<div class="d-flex flex-column bg-white  bsapp-settings-panel  bsapp-page-tab" data-page-id="js-tabs-home"
     data-depth="0">
    <div class="d-flex justify-content-between bsapp-fs-20 bsapp-lh-24">
        <span class="font-weight-bold"><?= lang('tasks_settings_title') ?></span>
        <a href="javascript:;" onclick="TasksSettings.closeSettings(this)" class="text-black text-decoration-none">
            <i class="fal fa-times"></i>
        </a>
    </div>
    <div class="py-8 pt-10 ">
        <div class="d-flex">
            <i class="fal fa-tasks-alt mie-10 bsapp-fs-20"></i>
            <h6 class="bsapp-fs-16 bsapp-lh-22 mb-6 font-weight-bolder"><?php echo lang('tasks_type') ?></h6>
        </div>
        <div class="bsapp-fs-13 bsapp-lh-15 text-gray mb-4"><?= lang('tasks_type_description') ?></div>
        <a class="d-flex align-items-center text-primary text-decoration-none font-weight-bolder py-3"
           href="javascript:;" onclick="TasksSettings.goToTaskTypes(this, event);"
           data-next="js-tabs-page-1"><?= lang('manage') ?><i class="fal fa-angle-right mx-5 bsapp-fs-24"></i></a>
    </div>
</div>