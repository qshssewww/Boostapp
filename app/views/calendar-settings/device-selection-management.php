<!-- Calendar Settings Module :: Panel begin -->
<div class="bsapp-settings-panel calendarSettings-device-selection-management d-none flex-column overflow-hidden position-absolute h-100 w-100 bg-white p-15 animated slideInStart" data-depth="1">

  <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" role="button" data-target="main-settings-panel">
    <h5 class="d-flex align-items-start font-weight-bolder">
      <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
        <?php echo lang('cal_back_to_cal_settings') ?>
    </h5>
  </a>

  <h3 class="d-flex align-items-center text-gray-700 font-weight-bolder mb-20 bsapp-fs-14">
    <i class="fal fa-bicycle mie-6 text-gray-500 bsapp-fs-19"></i>
      <?php echo lang('studio_device_cat_desk') ?>
  </h3>

  <!-- Start of Scrollable Area -->
  <div class="scrollable">
    <div class="pb-50">

      <ul class="devices-list list-unstyled p-0 bsapp-fs-14">

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
    <a class="new-device-selection btn btn-lg btn-primary text-white rounded-lg font-weight-bolder shadow-none border-0 w-100 bsapp-fs-16" data-target="calendarSettings-device-selection-management--new"><?php echo lang('add_device_cat_desk') ?></a>
  </div>

</div>