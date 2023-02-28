<!-- Calendar Settings Module :: Panel begin -->
<div class="bsapp-settings-panel calendarSettings-calendars-and-classes d-none flex-column overflow-hidden position-absolute h-100 w-100 bg-white p-15 animated slideInStart" data-depth="1">

  <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" role="button" data-target="main-settings-panel">
    <h5 class="d-flex align-items-start font-weight-bolder">
      <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
        <?php echo lang('cal_back_to_cal_settings') ?>
    </h5>
  </a>

  <h3 class="d-flex align-items-center text-gray-700 font-weight-bolder mb-20 bsapp-fs-14">
    <i class="fal fa-tasks-alt mie-6 text-gray-500 bsapp-fs-19"></i>
      <?php echo lang('cal_and_class') ?>
  </h3>

  <!-- Start of Scrollable Area -->
  <div class="scrollable">
    <div class="pb-50">

      <ul class="list-unstyled p-0">

        <li class="mb-30">
          <h6 class="text-gray-700 text-start font-weight-bolder mb-10"><?php echo lang('cal_and_classes_calendars') ?></h6>
          <p class="text-gray-500 text-start m-0 bsapp-fs-13 bsapp-lh-15"><?php echo lang('cal_and_classes_calendars_sub') ?></p>
          <a class="d-flex align-items-center text-primary text-decoration-none font-weight-bolder py-3" data-target="calendarSettings-calendars-and-classes_calendars" role="button" onclick="getCalendars()">
              <?php echo lang('manage') ?>
            <i class="fal fa-angle-right mx-5 bsapp-fs-24"></i>
          </a>
        </li>

        <li class="mb-30">
          <h6 class="text-gray-700 text-start font-weight-bolder mb-10"><?php echo lang('cal_and_classes_types') ?></h6>
          <p class="text-gray-500 text-start m-0 bsapp-fs-13 bsapp-lh-15"><?php echo lang('cal_and_classes_types_sub') ?></p>
          <a class="d-flex align-items-center text-primary text-decoration-none font-weight-bolder py-3"
             data-target="calendarSettings-calendars-and-classes_classes" role="button" onclick="calendarsAndClasses.getClasses()">
              <?php echo lang('manage') ?>
            <i class="fal fa-angle-right mx-5 bsapp-fs-24"></i>
          </a>
        </li>

      </ul>

    </div>
  </div>
</div>