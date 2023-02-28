<!-- Calendar Settings Module :: Panel begin -->
<div class="bsapp-settings-panel calendarSettings-permanent-registration d-none flex-column overflow-hidden position-absolute h-100 w-100 bg-white p-15 animated slideInStart" data-depth="1">

  <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" role="button" data-target="main-settings-panel">
    <h5 class="d-flex align-items-start font-weight-bolder">
      <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
        <?php echo lang('cal_back_to_cal_settings') ?>
    </h5>
  </a>

  <h3 class="d-flex align-items-center text-gray-700 font-weight-bolder mb-20 bsapp-fs-14">
    <i class="fal fa-sync mie-6 text-gray-500 bsapp-fs-19"></i>
      <?php echo lang('setting_permanently') ?>
  </h3>

  <!-- Start of Scrollable Area -->
  <div class="scrollable">
    <div class="pb-50">

      <ul class="list-unstyled p-0">

        <li class="mb-30">
          <h6 class="text-gray-700 text-start font-weight-bolder mb-15"><?php echo lang('cal_permanent_reg_title') ?></h6>
          <div class="row">
            <div class="col-6">
              <select class="select2--reserve-when-expired js-select2" name="reserve-when-expired" data-setting="general">
                <option value="0" data-target="show-registrations-limit" selected><?php echo lang('yes') ?></option>
                <option value="1"><?php echo lang('no') ?></option>
              </select>
            </div>
          </div>
        </li>

        <li class="mb-30">
          <h6 class="text-gray-700 text-start font-weight-bolder mb-15"><?php echo lang('cal_permanent_reg_limit') ?></h6>
          <div class="row">
            <div class="col-6">
              <select class="select2--set-registrations-limit js-select2" name="reserve-when-expired" data-setting="general">
                <option value="0" data-target="show-cancel-permanent" selected><?php echo lang('after_sever_classes') ?></option>
                <option value="1"><?php echo lang('instantly') ?></option>
              </select>
            </div>
          </div>
        </li>

        <li class="mb-30">
          <h6 class="text-gray-700 text-start font-weight-bolder mb-15"><?php echo lang('cal_permanent_reg_cancel') ?></h6>
          <div class="input-group bsapp-fs-16">
            <input type="number" class="col-3 form-control bg-light border-0 shadow-none text-start py-2 pis-10 pie-0" aria-label="Monthly Payment" min="1" value="1" id="cancel-permanent-after" data-setting="general">
            <div class="input-group-append bg-light py-7 px-10">
              <span><?php echo lang('classes') ?></span>
            </div>
          </div>
        </li>

      </ul>

    </div>
  </div>

  <div class="position-absolute bottom-0 left-0 bg-white p-15 pt-10 w-100">
    <a class="d-none btn-save-calendar-settings btn btn-lg btn-primary text-white rounded-lg font-weight-bolder shadow-none border-0 w-100 bsapp-fs-16" role="button" onclick="updateCalendarSettings($(this))"><?php echo lang('save_changes_button') ?></a>
  </div>
</div>
