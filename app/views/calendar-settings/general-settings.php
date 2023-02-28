<!-- Calendar Settings Module :: Panel begin -->
<div class="bsapp-settings-panel calendarSettings-general-settings d-none flex-column overflow-hidden position-absolute h-100 w-100 bg-white p-15 animated slideInStart" data-depth="1">

  <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" role="button" data-target="main-settings-panel">
    <h5 class="d-flex align-items-start font-weight-bolder">
      <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
        <?php echo lang('cal_back_to_cal_settings') ?>
    </h5>
  </a>

  <h3 class="d-flex align-items-center text-gray-700 font-weight-bolder mb-20 bsapp-fs-14">
    <i class="fal fa-sliders-h-square mie-6 text-gray-500 bsapp-fs-19"></i>
      <?php echo lang('general_settings_admin') ?>
  </h3>

  <!-- Start of Scrollable Area -->
  <div class="scrollable">
    <div class="pb-50">

      <ul class="list-unstyled p-0">

        <li class="mb-15 border-bottom">
          <h6 class="text-gray-700 text-start font-weight-bolder mb-15"><?php echo lang('cal_general_attendance') ?></h6>
          <div class="row mb-10">
            <div class="col-7">
              <select class="js-select2 select2--class-default-status select2" name="form-of-payment" data-setting="general">
                <option value="0"><?php echo lang('arrived_cal_general') ?></option>
                <option value="1" selected><?php echo lang('did_not_arrive_cal_general') ?></option>
              </select>
            </div>
          </div>
          <p class="text-gray-500 text-start m-0 mb-15 bsapp-fs-13 bsapp-lh-15"><?php echo lang('cal_general_attendance_sub') ?></p>
        </li>

        <li class="mb-15 border-bottom">
          <h6 class="d-flex align-items-center text-gray-700 text-start font-weight-bolder mb-10">
            <span class="flex-grow-1"><?php echo lang('cal_genereal_guide_busy') ?></span>
            <div class="custom-control custom-switch mis-10 mie-5">
              <input type="checkbox" class="custom-control-input" id="allow-busy-scheduling" data-setting="general" checked>
              <label class="custom-control-label" for="allow-busy-scheduling" role="button"></label>
            </div>
          </h6>
          <p class="text-gray-500 text-start m-0 mb-15 bsapp-fs-13 bsapp-lh-15"><?php echo lang('cal_genereal_guide_busy_sub') ?></p>
        </li>

        <li class="mb-15 border-bottom">
          <h6 class="text-gray-700 text-start font-weight-bolder mb-15"><?php echo lang('cal_general_min_attendance') ?></h6>
          <div class="row mb-10">
            <div class="col-7">
              <select class="js-select2 select2" id="cancel-minumum" name="form-of-payment" data-setting="general">
                <option value="0" selected><?php echo lang('cal_min_cancel_class') ?></option>
                <option value="1"><?php echo lang('cal_min_notify') ?></option>
              </select>
            </div>
          </div>
          <p class="text-gray-500 text-start m-0 mb-15 bsapp-fs-13 bsapp-lh-15"><?php echo lang('cal_general_min_attendance_sub') ?></p>
        </li>

        <li class="mb-15 border-bottom">
          <h6 class="text-gray-700 text-start font-weight-bolder mb-15"><?php echo lang('cal_general_class_avaiable_notification') ?></h6>
          <div class="row mb-10">
            <div class="col-7">
              <select id="send-class-available-alert" class="js-select2 select2" name="form-of-payment" data-setting="general">
                <option value="1" selected><?php echo lang('cal_yes_always') ?></option>
                <option value="0"><?php echo lang('run_auto_wait_list') ?></option>
              </select>
            </div>
          </div>
          <p class="text-gray-500 text-start m-0 mb-15 bsapp-fs-13 bsapp-lh-15"><?php echo lang('cal_general_class_avaiable_notification_sub') ?></p>
        </li>

        <li class="mb-15 text-start">
            <h6 class="text-gray-700 font-weight-bolder mb-15"><?= lang('settings_calss_view'); ?></h6>
            <select id="js-appDisplayTimeSettingsSelect" class="js-select2" onchange="globalCalendarSettings.classesDisplayOptions(this);" data-setting="general">
                <option><?= lang('date_range'); ?></option>
                <option><?= lang('register_week_day_hour'); ?></option>
            </select>

            <div class="mt-15 collapse">
                <h6 class="text-gray-700 font-weight-bolder mt-15"><?= lang('choose_date_range'); ?></h6>
                <select class="js-select2" data-setting="general"> <!-- DONT FORGER CHANGE TO LANG -->
                    <option>1 <?= lang('days'); ?></option>
                    <option>2 <?= lang('days'); ?></option>
                    <option>3 <?= lang('days'); ?></option>
                    <option>4 <?= lang('days'); ?></option>
                    <option>5 <?= lang('days'); ?></option>
                    <option>6 <?= lang('days'); ?></option>
                    <option>7 <?= lang('days'); ?></option>
                    <option>2 <?= lang('weeks'); ?></option>
                    <option>3 <?= lang('weeks'); ?></option>
                    <option>4 <?= lang('weeks'); ?></option>
                    <option>2 <?= lang('months'); ?></option>
                    <option>3 <?= lang('months'); ?></option>
                    <option>4 <?= lang('months'); ?></option>
                    <option>5 <?= lang('months'); ?></option>
                    <option>6 <?= lang('months'); ?></option>
                </select>
            </div>

            <div class="mt-15 collapse">
                <h6 class="text-gray-700 font-weight-bolder mt-15"><?= lang('select_day_ajax'); ?></h6>
                <select class="js-select2" data-setting="general">
                    <option><?= lang('sunday'); ?></option>
                    <option><?= lang('monday'); ?></option>
                    <option><?= lang('tuesday'); ?></option>
                    <option><?= lang('wednesday'); ?></option>
                    <option><?= lang('thursday'); ?></option>
                    <option><?= lang('friday'); ?></option>
                    <option><?= lang('saturday'); ?></option>
                </select>

                <h6 class="text-gray-700 font-weight-bolder mt-15"><?= lang('select_hour_ajax'); ?></h6>
                <input type="time" class="form-control bg-light border-light" value="06:00" data-setting="general" style="box-shadow: none;">
            </div>
        </li>

      </ul>

    </div>
  </div>

  <div class="position-absolute bottom-0 left-0 bg-white p-15 pt-10 w-100">
    <a class="d-none btn-save-calendar-settings btn btn-lg btn-primary text-white rounded-lg font-weight-bolder shadow-none border-0 w-100 bsapp-fs-16" role="button" onclick="updateCalendarSettings($(this))"><?php echo lang('save_changes_button') ?></a>
  </div>
</div>