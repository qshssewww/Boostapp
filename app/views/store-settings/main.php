<!-- Store Settings Module :: Panel begin -->
<div class="bsapp-settings-panel main-settings-panel d-flex flex-column position-absolute h-100 w-100 bg-white p-15 overflow-hidden animated fadeIn" data-depth="0">

  <h5 class="d-flex text-black font-weight-bolder mb-15 mie-30 p-0"><?php echo lang('store_settings') ?></h5>

  <div class="scrollable">
    <div class="d-flex flex-column justify-content-around bsapp-mh-100 bsapp-h-auto">

      <a class="d-flex text-decoration-none" role="button" data-target="storeSettings-manage-settings">
        <i class="fal fa-layer-group fa-fw text-gray-500 mie-15 bsapp-fs-32"></i>
        <div>
          <h6 class="d-flex align-items-center text-gray-700 text-start font-weight-bolder m-0"><?php echo lang('general_settings_admin') ?></h6>
          <p class="text-start text-gray-500 pie-20 mb-15 bsapp-fs-14 bsapp-lh-16"><?php echo lang('manage_items_desc') ?></p>
        </div>
      </a>

      <a class="d-flex text-decoration-none" role="button" data-target="storeSettings-coupons" onclick="getCoupons()">
        <i class="fal fa-ticket-alt fa-fw text-gray-500 mie-15 bsapp-fs-32"></i>
        <div>
          <h6 class="d-flex align-items-center text-gray-700 text-start font-weight-bolder m-0"><?php echo lang('coupons') ?></h6>
          <p class="text-start text-gray-500 pie-20 mb-15 bsapp-fs-14 bsapp-lh-16"><?php echo lang('store_coupon_description') ?></p>
        </div>
      </a>

      <a class="d-flex text-decoration-none" role="button" data-target="storeSettings-payment-and-billing" onclick="GetPaymentsSettings()">
        <i class="fal fa-badge-dollar fa-fw text-gray-500 mie-15 bsapp-fs-32"></i>
        <div>
          <h6 class="d-flex align-items-center text-gray-700 text-start font-weight-bolder m-0"><?php echo lang('billing_management') ?></h6>
          <p class="text-start text-gray-500 mb-15 pie-20 bsapp-fs-14 bsapp-lh-16"><?php echo lang('store_payment_desc') ?></p>
        </div>
      </a>

      <a class="d-flex text-decoration-none" role="button" data-target="storeSettings-order-products" onclick="getDisplayOrders('memberships')">
        <i class="fal fa-sort fa-fw text-gray-500 mie-15 bsapp-fs-32"></i>
        <div>
          <h6 class="d-flex align-items-center text-gray-700 text-start font-weight-bolder m-0"><?php echo lang('store_display_order') ?></h6>
          <p class="text-start text-gray-500 mb-15 pie-20 bsapp-fs-14 bsapp-lh-16"><?php echo lang('store_display_desc') ?></p>
        </div>
      </a>

      <a class="d-flex text-decoration-none" role="button" data-target="storeSettings-fixed-payments" onclick="getRegistrationFees()">
        <i class="fal fa-file-invoice-dollar fa-fw text-gray-500 mie-15 bsapp-fs-32"></i>
        <div>
          <h6 class="d-flex align-items-center text-gray-700 text-start font-weight-bolder m-0">
          <?php echo lang('fixed_payments') ?>
            <span class="bsapp-fees-counter d-none badge badge-pill badge-success font-weight-normal mis-6 bsapp-fs-13">3</span>
          </h6>
          <p class="text-start text-gray-500 mb-15 pie-20 bsapp-fs-14 bsapp-lh-16"><?php echo lang('fix_payments_desc') ?></p>
        </div>
      </a>

    </div>
  </div>

</div>