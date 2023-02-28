<!-- Store Settings Module :: Panel begin -->
<div class="bsapp-settings-panel storeSettings-coupons d-none flex-column overflow-hidden position-absolute h-100 w-100 bg-white p-15 animated slideInStart" data-depth="1">

  <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" role="button" data-target="main-settings-panel">
    <h5 class="d-flex align-items-start font-weight-bolder">
      <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
      <?php echo lang('back_to_store') ?>
    </h5>
  </a>

  <h3 class="d-flex align-items-center text-gray-700 font-weight-bolder mb-6 bsapp-fs-14">
    <i class="fal fa-ticket-alt mie-6 text-gray-500 bsapp-fs-19"></i>
    <?php echo lang('coupons') ?>
  </h3>

  <div class="scrollable">
    <div class="pb-50">

      <ul class="coupon-list list-unstyled mt-10 p-0">
        <li class="coupon-loading mb-10 animated fadeInUp">
          <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
            <div class="spinner-border spinner-border-sm text-success" role="status">
              <span class="sr-only">Loading...</span>
            </div>
          </div>
        </li>
        <li class="coupon-loading mb-10 animated fadeInUp delay-1">
          <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
            <div class="spinner-border spinner-border-sm text-success" role="status">
              <span class="sr-only">Loading...</span>
            </div>
          </div>
        </li>
        <li class="coupon-loading mb-10 animated fadeInUp delay-2">
          <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
            <div class="spinner-border spinner-border-sm text-success" role="status">
              <span class="sr-only">Loading...</span>
            </div>
          </div>
        </li>
      </ul>

    </div>
  </div>

  <div class="position-absolute bottom-0 left-0 bg-white p-15 pt-10 w-100">
    <a class="bsapp-new-coupon btn btn-lg bg-light text-gray-700 rounded-lg font-weight-bolder shadow-none border-0 w-100 bsapp-fs-16">+ <?php echo lang('new_coupon') ?></a>
  </div>

</div>