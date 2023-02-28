<!-- Tasks Settings Module :: Panel begin -->
<div class="bsapp-settings-panel calendarSettings-tasks-settings d-none flex-column overflow-hidden position-absolute h-100 w-100 bg-white p-15 animated slideInStart"
     data-depth="1">

    <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" role="button"
       data-target="main-settings-panel">
        <h5 class="d-flex align-items-start font-weight-bolder">
            <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
            <?= lang('cal_back_to_cal_settings') ?>
        </h5>
    </a>

    <h3 class="d-flex align-items-center text-gray-700 font-weight-bolder mb-20 bsapp-fs-14">
        <i class="fal fa-tasks mie-6 text-gray-500 bsapp-fs-19"></i>
        <?= lang('tasks') ?>
    </h3>

    <!-- Start of Scrollable Area -->
    <div class="scrollable">
        <div class="pb-50">

            <ul class="list-unstyled p-0">

                <li class="mb-30">
                    <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                        <i class="fal fa-tasks-alt mie-10 bsapp-fs-20"></i>
                        <?= lang('tasks_type') ?>
                    </h6>
                    <p class="text-gray-500 text-start m-0 bsapp-fs-13 bsapp-lh-15"><?= lang('tasks_type_description') ?></p>
                    <a class="d-flex align-items-center text-primary text-decoration-none font-weight-bolder py-3"
                       data-target="calendarSettings-tasks-settings-types" role="button"
                       onclick="TaskTypesFunction.getTaskTypes();">
                        <?= lang('manage') ?>
                        <i class="fal fa-angle-right mx-5 bsapp-fs-24"></i>
                    </a>
                </li>

                <li class="mb-30">
                    <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                        <i class="fal fa-clipboard-check mie-10 bsapp-fs-20"></i>
                        <?= lang('settings_status_title') ?>
                    </h6>
                    <p class="text-gray-500 text-start m-0 bsapp-fs-13 bsapp-lh-15"><?= lang('settings_status_description') ?></p>
                    <a class="d-flex align-items-center text-primary text-decoration-none font-weight-bolder py-3"
                       data-target="calendarSettings-tasks-settings-statuses" role="button"
                       onclick="TaskStatusFunction.getTaskStatuses();">
                        <?= lang('manage') ?>
                        <i class="fal fa-angle-right mx-5 bsapp-fs-24"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
