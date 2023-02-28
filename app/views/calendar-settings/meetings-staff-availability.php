<!-- Calendar Settings Module :: Panel begin -->
<div class="bsapp-settings-panel calendarSettings-meetings-staff-availability d-none flex-column overflow-hidden
 position-absolute h-100 w-100 bg-white p-15 animated " data-depth="2">

  <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" role="button" data-target="Meetings-navigation">
    <h5 class="d-flex align-items-start font-weight-bolder">
      <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
        <?php echo lang('back_single') ?>
    </h5>
  </a>

    <h5 class="d-flex align-items-center text-gray-700 font-weight-bolder mb-10 bsapp-fs-14">
        <i class="fal fa-user-clock mie-6 text-gray-500 bsapp-fs-19"></i>
        <?php echo lang('path_appointments_staff') ?>
    </h5>
    <h5 class="d-flex align-items-center text-gray-700 font-weight-bolder mb-10 bsapp-fs-18">
        <?php echo lang('cal_staff') ?>
    </h5>

  <!-- Start of Scrollable Area -->
  <div class="scrollable">
    <div class="pb-50">
        <ul class="coaches-list list-unstyled p-0 bsapp-fs-14">
            <li class="item-loading item-placeholder mb-10 animated fadeInUp">
                <div class="form-static d-flex align-items-center justify-content-center bg-light rounded
                 text-start m-0 py-15 px-10 bsapp-fs-14">
                    <div class="spinner-border spinner-border-sm text-success" role="status">
                        <span class="sr-only"><?php echo lang('loading') ?></span>
                    </div>
                </div>
            </li>
            <li class="item-loading item-placeholder mb-10 animated fadeInUp delay-1">
                <div class="form-static d-flex align-items-center justify-content-center bg-light rounded
                 text-start m-0 py-15 px-10 bsapp-fs-14">
                    <div class="spinner-border spinner-border-sm text-success" role="status">
                        <span class="sr-only"><?php echo lang('loading') ?></span>
                    </div>
                </div>
            </li>
            <li class="item-loading item-placeholder mb-10 animated fadeInUp delay-2">
                <div class="form-static d-flex align-items-center justify-content-center bg-light rounded
                 text-start m-0 py-15 px-10 bsapp-fs-14">
                    <div class="spinner-border spinner-border-sm text-success" role="status">
                        <span class="sr-only"><?php echo lang('loading') ?></span>
                    </div>
                </div>
            </li>
        </ul>
    </div>
  </div>
</div>