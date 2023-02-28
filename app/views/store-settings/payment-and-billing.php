<!-- Store Settings Module :: Panel begin -->
<div class="bsapp-settings-panel storeSettings-payment-and-billing d-none flex-column overflow-hidden position-absolute h-100 w-100 bg-white p-15 animated slideInStart" data-depth="1">

  <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" role="button" data-target="main-settings-panel">
    <h5 class="d-flex align-items-start font-weight-bolder">
      <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
      <?php echo lang('back_to_store') ?>
    </h5>
  </a>

  <h3 class="d-flex align-items-center text-gray-700 font-weight-bolder mb-20 bsapp-fs-14">
    <i class="fal fa-badge-dollar mie-6 text-gray-500 bsapp-fs-19"></i>
    <?php echo lang('billing_management') ?>
  </h3>

  <div class="scrollable">
    <div class="pb-50">

      <ul class="list-unstyled p-0">

        <li class="d-none mb-20">
          <div class="form-toggle">
            <h6 class="d-flex align-items-center text-gray-700 text-start font-weight-bolder mb-10">
              <span class="flex-grow-1"><?php echo lang('allow_bit_payments') ?> <img class="w-25p h-25p" src="/assets/img/Bit-e1593420737601.png"></span>
              <div class="custom-control custom-switch">
                <input type="checkbox" class="toggle-manage custom-control-input" id="bit-payments-switch">
                <label class="custom-control-label" for="bit-payments-switch" role="button"></label>
              </div>
            </h6>
            <p class="text-gray text-start mb-6 bsapp-fs-13 bsapp-lh-15"><?php echo lang('bit_desc') ?></p>
<!--            <p class="text-gray text-start mb-6 bsapp-fs-13 bsapp-lh-15">האפשרות תהיה זמינה לשימוש בימים הקרובים!</p>-->
        </li>

        <li class="mb-15">
          <div class="form-toggle">
            <h6 class="d-flex align-items-center text-gray-700 text-start font-weight-bolder mb-10">
              <span class="flex-grow-1"><?php echo lang('spread_payments') ?></span>
              <div class="custom-control custom-switch">
                <input type="checkbox" class="toggle-manage custom-control-input" id="spread-payments-switch" checked>
                <label class="custom-control-label" for="spread-payments-switch" role="button"></label>
              </div>
            </h6>
            <p class="text-gray text-start mb-6 bsapp-fs-13 bsapp-lh-15"><?php echo lang('spread_payments_notice') ?></p>
            <a class="toggle-content d-flex align-items-center text-primary text-decoration-none font-weight-bolder py-3"  role="button" data-target="storeSettings-spread-payments">
            <?php echo lang('manage_spread') ?>
              <i class="fal fa-angle-right mx-5 bsapp-fs-24"></i>
            </a>
        </li>
        <li class="mb-15 d-none">
          <h6 class="d-flex align-items-center text-gray-700 text-start font-weight-bolder mb-10">
            <span class="flex-grow-1"><?php echo lang('standing_orders') ?></span>
          </h6>
          <p class="text-gray text-start mb-6 bsapp-fs-13 bsapp-lh-15"><?php echo lang('Direct Debit') ?></p>
          <a class="d-flex align-items-center text-primary text-decoration-none font-weight-bolder py-3"  role="button" data-target="storeSettings-direct-debit">
          <?php echo lang('manage_spread') ?>
            <i class="fal fa-angle-right mx-5 bsapp-fs-24"></i>
          </a>
        </li>

      </ul>

    </div>
  </div>

</div>