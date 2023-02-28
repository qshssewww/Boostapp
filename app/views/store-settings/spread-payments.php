<!-- Store Settings Module :: Panel begin -->
<div class="bsapp-settings-panel storeSettings-spread-payments d-none flex-column overflow-hidden position-absolute h-100 w-100 bg-white p-15 animated slideInStart" data-depth="2">

  <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" role="button" data-target="storeSettings-payment-and-billing">
    <h5 class="d-flex align-items-start font-weight-bolder">
      <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
      <?php echo lang('back_single') ?>
    </h5>
  </a>

  <h3 class="d-flex align-items-center text-gray-700 font-weight-bolder mb-10 bsapp-fs-14">
  <?php echo lang('spread_payments_path') ?>
  </h3>

  <div class="scrollable">
    <div class="pb-50">

      <form>
        <ul class="list-unstyled p-0">

          <li class="border-top py-10">
            <h6 class="d-flex align-items-center text-start text-gray-700 font-weight-bolder mb-10">
              <span class="flex-grow-1"><?php echo lang('spread_payments_method') ?></span>
            </h6>
            <label class="bsapp-custom-checkbox d-flex align-items-center mb-0" role="button">
              <input id="spread-periodic" name="periodic-payments" data-prop="PeriodicPayments" type="checkbox" checked>
              <span></span>
              <?php echo lang('store_pereodic_payments') ?>
            </label>
          </li>

          <li class="border-top mb-15 py-15">
            <h6 class="d-flex align-items-center text-start text-gray-700 font-weight-bolder mb-10">
              <span class="flex-grow-1"><?php echo lang('spread_payments_limit') ?></span>
            </h6>

            <div class="row align-items-center mb-15 bsapp-fs-16">
              <span class="col-auto pie-0"><?php echo lang('max_payments') ?></span>
              <div class="col-2 border-bottom mis-10 p-0 bsapp-lh-16">
                <input type="number" class="w-100 border-0 outline-none shadow-none text-center py-5 px-10" value="24" aria-label="Max Payment Option" min="0" data-prop="MaxDistribution" id="spread-distribution">
              </div>
            </div>

              <div class="row align-items-center mb-15 bsapp-fs-16">
                  <div class="col-auto pie-0">
                      <label class="bsapp-custom-checkbox d-flex align-items-center mb-0" role="button">
                          <input id="max-payment-numbers-by-valid" name="max-payment-numbers-by-valid" data-prop="MaxPaymentsNumberByValid" type="checkbox">
                          <span></span>
                          <?php echo lang('limit_payments_by_membership_months') ?>
                      </label>
                  </div>
              </div>

            <a class="add-payout-limit text-start mb-0 bsapp-fs-16 d-none" role="button" onClick="addPayoutLimit($(this))">+ <?php echo lang('spread_payment_restrict') ?></a>
          </li>

          <li class="border-top mb-15 py-15">
            <h6 class="d-flex align-items-center text-start text-gray-700 font-weight-bolder mb-15">
              <span class="flex-grow-1"><?php echo lang('add_billing_for_spread_payments') ?></span>
            </h6>
            <a class="add-interest-rate d-flex text-start mb-0 bsapp-fs-16" role="button" onClick="setInterestRate($(this))">+ <?php echo lang('spread_payments_interest') ?></a>
          </li>

        </ul>
      </form>

    </div>
  </div>

  <div class="position-absolute bottom-0 left-0 bg-white p-15 pt-10 w-100">
    <a class="d-none btn-save-spread btn btn-lg btn-primary text-white rounded-lg font-weight-bolder shadow-none border-0 w-100 bsapp-fs-16" role="button" onclick="updateSpreadPayments($(this))"><?php echo lang('save') ?></a>
  </div>

</div>
