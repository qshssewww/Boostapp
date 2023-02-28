<!-- Store Settings Module :: Panel begin -->
<div class="bsapp-settings-panel storeSettings-fixed-payments-new d-none flex-column overflow-hidden position-absolute h-100 w-100 bg-white p-15 animated slideInStart" data-depth="2">

  <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" role="button" data-target="storeSettings-fixed-payments">
    <h5 class="d-flex align-items-start font-weight-bolder">
      <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
      <?php echo lang('back_single') ?>
    </h5>
  </a>

  <h3 class="d-flex align-items-center text-gray-700 font-weight-bolder mb-20 bsapp-fs-14">
  <?php echo lang('fixed_payment_path') ?>
  </h3>

  <div class="scrollable">
    <div class="pb-50">

      <form>

        <div class="border-bottom pb-5">

          <input type="text" class="form-control bg-light border-0 shadow-none mb-15 py-2 px-10" placeholder=<?php echo lang('table_title')?> id="newPayment-title" required>

          <div class="row align-items-center mb-15 bsapp-fs-16">
            <div class="col">
              <input type="number" class="form-control bg-light border-0 rounded shadow-none m-0 py-2 px-10" aria-label="Price" placeholder=<?php echo lang('price')?> id="newPayment-price" required>
            </div>
            <div class="col-7 form-inline pis-0">
              <label class="bsapp-custom-checkbox d-flex align-items-center mb-0" role="button">
                <input id="newPayment-vat" name="payments-vat" type="checkbox" checked>
                <span></span>
                <?php echo lang('include_vat') ?>
              </label>
            </div>
          </div>

          <p class="bsapp-branches-label text-gray-500 text-start bsapp-fs-12 my-5"><?php echo lang('branches') ?></p>
            <div class="col-7 mb-7 pr-0">
                <select class="select2--branches d-none" id="newPayment-branch"></select>
            </div>
        </div>

        <div class="form-toggle border-top border-bottom py-5">
          <h6 class="d-flex align-items-center text-gray-700 text-start pis-5">
            <div class="custom-control custom-switch">
              <input type="checkbox" class="toggle-manage custom-control-input" id="newPayment-required" checked>
              <label class="custom-control-label" for="newPayment-required" role="button"></label>
            </div>
            <span class="flex-grow-1 mis-10 my-10"><?php echo lang('fixed_payments_notice') ?></span>
          </h6>
          <div class="toggle-content flex-column d-none">
            <p class="text-gray-500 text-start mb-10 bsapp-fs-12"><?php echo lang('fixed_payments_select_item') ?></p>
            <select class="select2--memberships select2 bg-light border-0 rounded shadow-none mb-10 py-2 px-10" multiple="multiple" id="newPayment-memberships"></select>
          </div>
        </div>

        <div class="border-top text-start py-15">
          <label class="text-gray-700 mb-10 bsapp-fs-16" for="form-of-payment"><?php echo lang('fixed_form_payment') ?></label>

          <div class="d-flex flex-wrap">
            <div class="bsapp-payment-form col-7 pis-0 pie-10">
              <select class="select2--payment-form select2 bg-light border-0 rounded shadow-none mb-10 py-2 px-10" name="form-of-payment" id="newPayment-form">
                <option value="1"><?php echo lang('one_time_payment') ?></option>
                <option value="2"><?php echo lang('fixed_every_purchase') ?></option>
                <option value="3"><?php echo lang('periodic_payment') ?></option>
              </select>
            </div>
            <div class="bsapp-payment-periodic col-5 d-none px-0">
              <input type="number" id="newPayment-periodic-val" class="col-4 bg-light border-0 rounded-start shadow-none rounded text-center py-8 pis-10 mie-8" aria-label="Number of days/weeks/months" value="" min="1">
              <div class="col-8 bg-light rounded-end px-0">
                <select class="select2--periodic-type select2" name="periodic-type" id="newPayment-periodic-type">
                  <option value="1"><?php echo lang('days') ?></option>
                  <option value="2"><?php echo lang('week') ?></option>
                  <option value="3"><?php echo lang('month') ?></option>
                </select>
              </div>
            </div>

            <p class="bsapp-payment-description col-12 text-gray-500 mt-10 mb-15 bsapp-fs-14 bsapp-lh-16 px-0">
                <span class="d-none d-block"><?php echo lang('fixed_payment_notice_one') ?></span>
                <span class="d-none"><?php echo lang('fixed_payment_notice_two') ?></span>
                <span class="d-none"><?php echo lang('fixed_payment_notice_three') ?></span>
            </p>
          </div>
        </div>

      </form>

    </div>
  </div>

  <div class="position-absolute bottom-0 left-0 bg-white p-15 pt-10 w-100">
    <a class="bsapp-save-payment btn btn-lg btn-primary text-white rounded-lg font-weight-bolder shadow-none border-0 w-100 bsapp-fs-16"><?php echo lang('save') ?></a>
  </div>

</div>