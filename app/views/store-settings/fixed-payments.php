<!-- Store Settings Module :: Panel begin -->
<div class="bsapp-settings-panel storeSettings-fixed-payments d-none flex-column overflow-hidden position-absolute h-100 w-100 bg-white p-15 animated slideInStart" data-depth="1">

  <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" role="button" data-target="main-settings-panel">
    <h5 class="d-flex align-items-start font-weight-bolder">
      <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
      <?php echo lang('back_to_store') ?>
    </h5>
  </a>

  <h3 class="d-flex align-items-center bsapp-fs-14 text-gray-700 font-weight-bolder mb-20">
    <i class="fal fa-file-invoice-dollar mie-6 text-gray-500 bsapp-fs-19"></i>
    <?php echo lang('fixed_payments') ?>
  </h3>

  <div class="scrollable">
    <div class="pb-50">

      <ul class="registration-fees-list list-unstyled mt-15 p-0 bsapp-fs-14">

        <li class="fees-placeholder d-flex font-weight-bolder py-5 mb-10">
          <span class="col-7 text-start pis-12 pie-0"><?php echo lang('store_name') ?></span>
          <span class="col-2 text-center px-5"><?php echo lang('status_table') ?></span>
          <span class="col-2 text-center"><?php echo lang('store_fixed_val') ?></span>
        </li>

        <li class="fees-loading fees-placeholder mb-10 animated fadeInUp">
          <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
            <div class="spinner-border spinner-border-sm text-success" role="status">
              <span class="sr-only"><?php echo lang('loading') ?>...</span>
            </div>
          </div>
        </li>

        <li class="fees-loading fees-placeholder mb-10 animated fadeInUp delay-1">
          <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
            <div class="spinner-border spinner-border-sm text-success" role="status">
              <span class="sr-only"><?php echo lang('loading') ?>...</span>
            </div>
          </div>
        </li>

        <li class="fees-loading fees-placeholder mb-10 animated fadeInUp delay-2">
          <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
            <div class="spinner-border spinner-border-sm text-success" role="status">
              <span class="sr-only"><?php echo lang('loading') ?>...</span>
            </div>
          </div>
        </li>

      </ul>

    </div>
  </div>

  <div class="position-absolute bottom-0 left-0 bg-white p-15 pt-10 w-100">
    <a class="bsapp-new-registration-fee btn btn-lg bg-light text-gray-700 rounded-lg font-weight-bolder shadow-none border-0 w-100 bsapp-fs-16" role="button">+ <?php echo lang('create_new') ?></a>
  </div>

</div>