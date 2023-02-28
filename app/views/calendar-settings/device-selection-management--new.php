<!-- Calendar Settings Module :: Panel begin -->
<div class="bsapp-settings-panel calendarSettings-device-selection-management--new d-none flex-column overflow-hidden position-absolute h-100 w-100 bg-white p-15 animated slideInStart" data-depth="2">

  <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" role="button" data-target="calendarSettings-device-selection-management">
    <h5 class="d-flex align-items-start font-weight-bolder">
      <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
        <?php echo lang('back_single') ?>
    </h5>
  </a>

  <h3 class="d-flex align-items-center text-gray-700 font-weight-bolder mb-20 bsapp-fs-14">
    <i class="fal fa-bicycle mie-6 text-gray-500 bsapp-fs-19"></i>
      <?php echo lang('device_selection_management') ?> <span class="mis-5"></span>
  </h3>

  <!-- Start of Scrollable Area -->
  <div class="scrollable">
    <div class="pb-50">

      <div class="d-flex flex-column">

        <div class="d-flex align-items-center mb-15 w-100">
          <i class="fal fa-bookmark bsapp-fs-16 mie-7"></i>
          <input type="text" class="form-control bg-light border-0 rounded shadow-none m-0 py-2 px-10" aria-label="Category name" placeholder="<?php echo lang('category_name') ?>" id="deviceSelection-name" required>
        </div>
        <p class="text-gray-500 text-start m-0 mb-20 bsapp-fs-13 bsapp-lh-15"><?php echo lang('device_equ_cal_desc') ?></p>

        <ul class="device-category-list list-unstyled p-0 mb-0 bsapp-fs-16">

          <li class="item-loading item-placeholder mb-10 animated fadeInUp">
            <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
              <div class="spinner-border spinner-border-sm text-success" role="status">
                <span class="sr-only"><?php echo lang('loading') ?></span>
              </div>
            </div>
          </li>
          <li class="item-loading item-placeholder mb-10 animated fadeInUp delay-1">
            <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
              <div class="spinner-border spinner-border-sm text-success" role="status">
                <span class="sr-only"><?php echo lang('loading') ?></span>
              </div>
            </div>
          </li>
          <li class="item-loading item-placeholder mb-10 animated fadeInUp delay-2">
            <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
              <div class="spinner-border spinner-border-sm text-success" role="status">
                <span class="sr-only"><?php echo lang('loading') ?></span>
              </div>
            </div>
          </li>

        </ul>

        <div class="form-toggle mb-15">
          <div class="form-static d-flex bsapp-fs-16">
            <a class="edit-device-selection text-start" role="button"><?php echo lang('cal_device_manage_add') ?></a>
          </div>
        </div>

      </div>

    </div>
  </div>

  <div class="position-absolute bottom-0 left-0 bg-white p-15 pt-10 w-100">
    <a class="save-device-group btn btn-lg btn-primary d-flex align-items-center justify-content-center text-white rounded-lg font-weight-bolder shadow-none border-0 w-100 bsapp-fs-16">
      <span><?php echo lang('save_changes_button') ?></span>
      <div class="spinner-border spinner-border-sm text-white d-none p-9 bsapp-fs-16" role="status">
        <span class="sr-only"><?php echo lang('loading') ?></span>
      </div>
    </a>
  </div>

</div>
