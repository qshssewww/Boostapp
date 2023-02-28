<!-- Store Settings Module :: Panel begin -->
<div class="bsapp-settings-panel storeSettings-direct-debit d-none flex-column overflow-hidden position-absolute h-100 w-100 bg-white p-15 animated slideInStart" data-depth="2">

  <a class="text-decoration-none font-weight-bolder p-0 mie-30 mb-20" role="button" data-target="storeSettings-payment-and-billing">
    <h5 class="d-flex align-items-start text-black font-weight-bolder">
      <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
      <?php echo lang('back_single') ?>
    </h5>
  </a>

  <h3 class="d-flex align-items-center text-gray-700 font-weight-bolder pb-13 bsapp-fs-14">
  <?php echo lang('recurring_billing_path') ?>
  </h3>

  <div class="scrollable">
    <div class="pb-50">

      <form>
        <ul class="list-unstyled p-0 m-0">

          <li class="form-toggle border-top py-13">
            <h6 class="d-flex text-gray-700 text-start font-weight-bolder mb-10">
              <span class="flex-grow-1"><?php echo lang('store_direct_debit_notice') ?></span>
              <div class="custom-control custom-switch">
                <input type="checkbox" class="toggle-manage custom-control-input" id="managing-periodic-switch" checked>
                <label class="custom-control-label" for="managing-periodic-switch" role="button"></label>
              </div>
            </h6>
            <div class="toggle-content d-flex align-items-center justify-content-between text-gray-700 mb-10 bsapp-fs-16">
              <span class="col-auto p-0"><?php echo lang('set_direct_debit_payment') ?></span>
              <input type="text" class="form-control text-center border-0 bg-light shadow-none mx-10 px-10" aria-label="Type date" id="periodic-charge-day">
              <span class="col-auto p-0"><?php echo lang('set_each_month') ?></span>
            </div>
            <p class="text-gray text-start mb-0 bsapp-fs-13 bsapp-lh-16"><?php echo lang('direct_debit_notice_store') ?></p>
          </li>

          <li class="form-toggle border-top py-13">
            <h6 class="d-flex text-gray-700 text-start font-weight-bolder mb-10">
              <span class="flex-grow-1"><?php echo lang('direct_debit_prevent_booking') ?></span>
              <div class="custom-control custom-switch">
                <input type="checkbox" class="toggle-manage custom-control-input" id="prevent-booking-switch" checked>
                <label class="custom-control-label" for="prevent-booking-switch" role="button"></label>
              </div>
            </h6>
            <div class="toggle-content d-flex flex-column py-15">
              <div class="form-inline mb-6">
                <input class="mis-0 mie-7" type="radio" name="preventBooking" id="prevent-booking-instantly" value="instantly" checked>
                <label class="form-check-label bsapp-fs-16" for="prevent-booking-instantly">
                <?php echo lang('instantly') ?>
                </label>
              </div>
              <div class="d-flex align-items-center">
                <input class="mis-0 mie-7" type="radio" name="preventBooking" id="prevent-booking-after" value="after">
                <label class="form-check-label bsapp-fs-16" for="prevent-booking-after">
                <?php echo lang('after') ?>
                </label>
                <div class="input-group mis-10 bsapp-fs-16">
                  <input type="number" class="col-2 form-control bg-light border-0 shadow-none text-start rounded-0 rounded-start py-2 pis-10 pie-0" aria-label="Monthly Payment" min="0" id="prevent-booking-days">
                  <div class="input-group-append bg-light rounded-end py-7 px-10">
                    <span><?php echo lang('days') ?></span>
                  </div>
                </div>
              </div>
            </div>
            <p class="text-gray text-start mb-0 bsapp-fs-13 bsapp-lh-16"><?php echo lang('direct_debit_contact_notice') ?></p>
          </li>

          <li class="form-toggle border-top py-13">
            <h6 class="d-flex text-gray-700 text-start font-weight-bolder mb-10">
              <span class="flex-grow-1"><?php echo lang('direct_debit_cancel') ?></span>
              <div class="custom-control custom-switch">
                <input type="checkbox" class="toggle-manage custom-control-input" id="cancel-subsription-switch" checked>
                <label class="custom-control-label" for="cancel-subsription-switch" role="button"></label>
              </div>
            </h6>

            <div class="toggle-content d-flex flex-column py-15">
              <div class="form-inline mb-6">
                <input class="mis-0 mie-7" type="radio" name="cancelSubscription" id="cancel-subscrption-instantly" value="instantly" checked>
                <label class="form-check-label bsapp-fs-16" for="cancel-subscrption-instantly">
                <?php echo lang('instantly') ?>
                </label>
              </div>
              <div class="d-flex align-items-center">
                <input class="mis-0 mie-7" type="radio" name="cancelSubscription" id="cancel-subscrption-after" value="after">
                <label class="form-check-label bsapp-fs-16" for="cancel-subscrption-after">
                <?php echo lang('after') ?>
                </label>
                <div class="input-group mis-10 bsapp-fs-16">
                  <input type="number" class="col-2 form-control bg-light border-0 shadow-none text-start rounded-0 rounded-start py-2 pis-10 pie-0" aria-label="Monthly Payment" min="0" id="prevent-classes-days">
                  <div class="input-group-append bg-light rounded-end py-7 px-10">
                    <span><?php echo lang('days') ?></span>
                  </div>
                </div>
              </div>
            </div>
          </li>

        </ul>
      </form>

    </div>
  </div>

  <div class="position-absolute bottom-0 left-0 bg-white p-15 pt-10 w-100">
    <a class="d-none btn-save-debit btn btn-lg btn-primary text-white rounded-lg font-weight-bolder shadow-none border-0 w-100 bsapp-fs-16" role="button" data-target="storeSettings-payment-and-billing" onclick="updatePeriodicPayments($(this))"><?php echo lang('save') ?></a>
  </div>

</div>