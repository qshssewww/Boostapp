<!-- Calendar Settings Module :: Panel begin -->
<div class="bsapp-settings-panel calendarSettings-calendars-and-classes_classes d-none flex-column overflow-hidden position-absolute h-100 w-100 bg-white p-15 animated slideInStart" data-depth="2">

  <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" role="button" data-target="calendarSettings-calendars-and-classes">
    <h5 class="d-flex align-items-start font-weight-bolder">
      <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
        <?php echo lang('back_single') ?>
    </h5>
  </a>

  <h3 class="d-flex align-items-center text-gray-700 font-weight-bolder mb-20 bsapp-fs-14">
    <i class="fal fa-tasks-alt mie-6 text-gray-500 bsapp-fs-19"></i>
      <?php echo lang('path_cal_calendars_class_type') ?>
  </h3>

  <!-- Start of Scrollable Area -->
  <div class="scrollable">
    <div class="pb-50">

      <ul class="classes-list list-unstyled p-0 bsapp-fs-16">

        <li class="item-loading item-placeholder mb-10 animated fadeInUp">
          <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
            <div class="spinner-border spinner-border-sm text-success" role="status">
              <span class="sr-only"><?php echo lang('loading_datatables') ?></span>
            </div>
          </div>
        </li>
        <li class="item-loading item-placeholder mb-10 animated fadeInUp delay-1">
          <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
            <div class="spinner-border spinner-border-sm text-success" role="status">
              <span class="sr-only"><?php echo lang('loading_datatables') ?></span>
            </div>
          </div>
        </li>
        <li class="item-loading item-placeholder mb-10 animated fadeInUp delay-2">
          <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
            <div class="spinner-border spinner-border-sm text-success" role="status">
              <span class="sr-only"><?php echo lang('loading_datatables') ?></span>
            </div>
          </div>
        </li>
      </ul>

    </div>
  </div>

  <div class="position-absolute bottom-0 left-0 bg-white p-15 pt-10 w-100">
    <a class="new-class btn btn-lg btn-primary text-white rounded-lg font-weight-bolder shadow-none border-0 w-100 bsapp-fs-16" data-target="calendarSettings-classes--new"><?php echo lang('cal_calendars_add_class') ?></a>
  </div>
</div>