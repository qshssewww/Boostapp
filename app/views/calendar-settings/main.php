<!-- Calendar Settings Module :: Panel begin -->
<?php
$show = false;
$CompanySettingsDash = $CompanySettingsDash ?? Settings::getSettings(Auth::user()->CompanyNum);
?>
<div class="bsapp-settings-panel main-settings-panel d-flex flex-column position-absolute h-100 w-100 bg-white p-15 overflow-hidden animated fadeIn" data-depth="0">

    <h5 class="d-flex text-black font-weight-bolder mb-15 mie-30 p-0"><?php echo lang('settings_calendar') ?></h5>

    <!-- Start of Scrollable Area -->
    <div class="scrollable">
        <div class="d-flex flex-column justify-content-start bsapp-mh-100 bsapp-h-auto">
            <a class="d-flex text-decoration-none mb-20 pb-10 border-bottom border-light" role="button" data-target="calendarSettings-general-settings">
                <i class="fal fa-sliders-h-square fa-fw text-gray-500 mie-15 bsapp-fs-32"></i>
                <div>
                    <h6 class="d-flex align-items-center text-gray-700 text-start font-weight-bolder m-0"><?php echo lang('general_settings_admin') ?></h6>
                    <p class="text-start text-gray-500 pie-20 mb-15 bsapp-fs-14 bsapp-lh-16"><?php echo lang('cal_settings_sub_general') ?></p>
                </div>
            </a>

            <a class="d-flex text-decoration-none mb-20 pb-10 border-bottom border-light" role="button" data-target="calendarSettings-display-options">
                <i class="fal fa-eye fa-fw text-gray-500 mie-15 bsapp-fs-32"></i>
                <div>
                    <h6 class="d-flex align-items-center text-gray-700 text-start font-weight-bolder m-0"><?php echo lang('cal_display_options') ?></h6>
                    <p class="text-start text-gray-500 pie-20 mb-15 bsapp-fs-14 bsapp-lh-16"><?php echo lang('cal_settings_sub_display') ?></p>
                </div>
            </a>

            <a class="d-flex text-decoration-none mb-20 pb-10 border-bottom border-light" role="button" data-target="calendarSettings-calendars-and-classes">
                <i class="fal fa-tasks-alt fa-fw text-gray-500 mie-15 fal bsapp-fs-32"></i>
                <div>
                    <h6 class="d-flex align-items-center text-gray-700 text-start font-weight-bolder m-0"><?php echo lang('cal_and_class') ?></h6>
                    <p class="text-start text-gray-500 pie-20 mb-15 bsapp-fs-14 bsapp-lh-16"><?php echo lang('cal_settings_sub_class') ?></p>
                </div>
            </a>

            <a class="d-flex text-decoration-none mb-20 pb-10 border-bottom border-light"
                onclick="meetingGeneralSettings.init(this)" role="button" data-target="Meetings-navigation" >
              <i class="fal fa-calendar fa-fw text-gray-500 mie-15 bsapp-fs-32"></i>
              <div>
                <h6 class="d-flex align-items-center text-gray-700 text-start font-weight-bolder m-0">
                  <?=lang('cal_appointments')?>
                </h6>
                <p class="text-start text-gray-500 pie-20 mb-15 bsapp-fs-14 bsapp-lh-16"><?php echo lang('cal_appointments_sub') ?></p>
              </div>
            </a>

            <?php if (in_array($CompanySettingsDash->beta, [1, 2])) { ?>
                <a class="d-flex text-decoration-none mb-20 pb-10 border-bottom border-light"
                   role="button" data-target="calendarSettings-tasks-settings">
                    <i class="fal fa-tasks fa-fw text-gray-500 mie-15 bsapp-fs-32"></i>
                    <div>
                        <h6 class="d-flex align-items-center text-gray-700 text-start font-weight-bolder m-0">
                            <?= lang('tasks') ?>
                        </h6>
                        <p class="text-start text-gray-500 pie-20 mb-15 bsapp-fs-14 bsapp-lh-16"><?= lang('settings_tasks_description') ?></p>
                    </div>
                </a>
            <?php } ?>

            <?php if ($show && Auth::user()->role_id == 1) { ?>
                <a class="d-flex text-decoration-none mb-20 pb-10 border-bottom border-light" role="button" data-target="calendarSettings-permanent-registration">
                    <i class="fal fa-sync fa-fw text-gray-500 mie-15 bsapp-fs-32"></i>
                    <div>
                        <h6 class="d-flex align-items-center text-gray-700 text-start font-weight-bolder m-0"><?php echo lang('setting_permanently') ?></h6>
                        <p class="text-start text-gray-500 pie-20 mb-15 bsapp-fs-14 bsapp-lh-16"><?php echo lang('cal_settings_sub_permanent') ?></p>
                    </div>
                </a>
            <?php } ?>

            <a class="d-flex text-decoration-none mb-20 pb-10" role="button" data-target="calendarSettings-device-selection-management" onclick="getDevices()">
                <i class="fal fa-bicycle fa-fw text-gray-500 mie-15 bsapp-fs-32"></i>
                <div>
                    <h6 class="d-flex align-items-center text-gray-700 text-start font-weight-bolder m-0"><?php echo lang('cal_settings_device_management') ?></h6>
                    <p class="text-start text-gray-500 pie-20 mb-15 bsapp-fs-14 bsapp-lh-16"><?php echo lang('cal_settings_sub_device') ?></p>
                </div>
            </a>

        </div>
    </div>
</div>
