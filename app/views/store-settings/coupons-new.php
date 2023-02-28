<!-- Store Settings Module :: Panel begin -->
<div class="bsapp-settings-panel storeSettings-coupons-new d-none flex-column overflow-hidden position-absolute h-100 w-100 bg-white p-15 animated slideInStart" data-depth="2">

  <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" role="button" data-target="storeSettings-coupons">
    <h5 class="d-flex align-items-start font-weight-bolder">
      <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
      <?php echo lang('back_single') ?>
    </h5>
  </a>

  <h3 class="d-flex align-items-center text-gray-700 font-weight-bolder mb-20 bsapp-fs-14">
  <?php echo lang('new_coupon_path') ?>
  </h3>

  <div class="scrollable">
    <div class="pb-50">

      <form>

        <input type="text" id="newCoupon-name" class="form-control bg-light border-0 shadow-none mb-20 py-2 px-10" placeholder=<?php echo lang('coupon_title') ?> required>

        <div class="row align-items-center mb-20 bsapp-fs-16">
          <div class="col">
            <input type="number" id="newCoupon-amount" class="form-control bg-light border-0 rounded shadow-none m-0 py-2 px-10" aria-label="Coupon Value" placeholder=<?php echo lang('store_fixed_val') ?> min="1" required>
          </div>
          <div class="col-7 d-flex align-items-center pis-0">
            <div class="form-check mie-10">
              <input type="radio" class="form-check-input position-relative mie-5" name="newCoupon-type" id="newCoupon-currency" checked>
              <label class="form-check-label" for="newCoupon-currency">₪</label>
            </div>
            <div class="form-check mie-10">
              <input type="radio" class="form-check-input position-relative mie-5" name="newCoupon-type" id="newCoupon-percentage">
              <label class="form-check-label" for="newCoupon-percentage">%</label>
            </div>
          </div>
        </div>

        <div class="row align-items-center mb-20 bsapp-fs-16">
          <div class="col">
            <input type="text" id="newCoupon-code" class="new-coupon-code form-control bg-light border-0 rounded shadow-none m-0 py-2 px-10 text-uppercase" aria-label="Coupon Code" placeholder=<?php echo lang('code') ?> required>
          </div>
          <div class="col-7 form-inline pis-0">
            <a class="d-flex align-items-center text-gray-700 bsapp-fs-16" role="button" onClick="randomCoupon($(this),5)">
              <i class="fal fa-random text-primary bsapp-fs-21 mie-10"></i>
              <span><?php echo lang('coupon_random') ?></span>
            </a>
          </div>
          <p id="code_exists" class="bsapp-validation-msg col-12 text-start text-danger bsapp-fs-13 m-0 d-none">* <?php echo lang('coupon_code_exists') ?></p>
        </div>

        <div class="form-toggle mb-20">

          <h6 class="d-flex align-items-center text-gray-700 text-start pis-5 mb-10">
            <div class="custom-control custom-switch mie-5">
              <input type="checkbox" class="toggle-manage custom-control-input" id="newCoupon-time-limit" checked>
              <label class="custom-control-label" for="newCoupon-time-limit" role="button"></label>
            </div>
            <span class="flex-grow-1 text-gray-700 font-weight-bolder bsapp-fs-16"><?php echo lang('time_limit') ?></span>
          </h6>

          <div class="toggle-content row d-flex align-items-center text-gray-700 mb-15 bsapp-fs-16">

            <span class="col-auto pie-10"><?php echo lang('coupon_from') ?></span>
            <div class="col-4 input-group d-flex align-items-center bg-light border rounded py-2 px-10">
              <input type="date" id="newCoupon-start-date" class="form-control border-0 bg-transparent shadow-none p-0 pt-3" name="startDate" aria-label="Pick start date" placeholder=<?php echo lang('select_date')?> required>
            </div>

            <span class="col-auto pie-10"><?php echo lang('coupon_till') ?></span>
            <div class="col-4 input-group d-flex align-items-center bg-light border rounded py-2 px-10">
              <input type="date" id="newCoupon-end-date" class="form-control border-0 bg-transparent shadow-none p-0 pt-3" name="endDate" aria-label="Pick end date" placeholder=<?php echo lang('select_date') ?> required>
            </div>
          </div>
        </div>

        <div class="form-toggle mb-20">
          <h6 class="d-flex align-items-center text-gray-700 text-start pis-5 mb-10">
            <div class="custom-control custom-switch mie-5">
              <input type="checkbox" class="toggle-manage custom-control-input" id="newCoupon-quantity-limit" checked>
              <label class="custom-control-label" for="newCoupon-quantity-limit" role="button"></label>
            </div>
            <span class="flex-grow-1 text-gray-700 font-weight-bolder bsapp-fs-16"><?php echo lang('coupon_quan_limit') ?></span>
          </h6>
          <div class="toggle-content d-flex align-items-center mb-15">
            <span><?php echo lang('coupon_limit') ?></span>
            <input type="number" id="newCoupon-quantity" class="form-control col-2 bg-light text-center border-0 rounded shadow-none mx-10 py-2 px-6" value="" min="1" aria-label="Quantity Limit" required>
            <span><?php echo lang('coupon_units') ?></span>
          </div>
        </div>

        <div class="form-toggle mb-15">
          <h6 class="d-flex align-items-center text-gray-700 text-start pis-5 mb-10">
            <div class="custom-control custom-switch mie-5">
              <input type="checkbox" class="toggle-manage custom-control-input" id="newCoupon-products-limit">
              <label class="custom-control-label" for="newCoupon-products-limit" role="button"></label>
            </div>
            <span class="flex-grow-1 text-gray-700 font-weight-bolder bsapp-fs-16"><?php echo lang('coupon_product') ?></span>
          </h6>
          <div class="toggle-content d-none flex-column">
            <p class="text-gray-500 text-start mb-5 bsapp-fs-12"><?php echo lang('coupon_select_item') ?></p>
            <select class="select2--limit-products border" id="newCoupon-products" style="height: auto !important"></select>
          </div>
        </div>

      </form>

    </div>
  </div>

  <div class="position-absolute bottom-0 left-0 bg-white p-15 pt-10 w-100">
    <a class="bsapp-save-coupon btn btn-lg btn-primary text-white rounded-lg font-weight-bolder shadow-none border-0 w-100 bsapp-fs-16"><?php echo lang('save') ?></a>
  </div>

</div>
