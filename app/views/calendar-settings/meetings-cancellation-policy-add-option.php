
<!-- Calendar appointments Settings Module :: Panel begin -->
<div class="bsapp-settings-panel calendarSettings-meetings-cancellation-policy-add-option d-none flex-column
 overflow-hidden position-absolute h-100 w-100 bg-white p-15 animated slideInStart" data-depth="3">

  <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" role="button"
     data-target="calendarSettings-meetings-cancellation-policy">
    <h5 class="d-flex align-items-start font-weight-bolder">
      <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
        <?=lang('back_single') ?>
    </h5>
  </a>

    <h5 class="path-title d-flex align-items-center text-gray-700 font-weight-bolder mb-15 bsapp-fs-14">
        <i class="fal fa-th-large mie-6 text-gray-500 bsapp-fs-19"></i>
        <?=lang('path_cal_cancellation_policy_add_option') ?>
    </h5>

    <!-- Start of Scrollable Area -->
    <div class="scrollable">
        <div class="pb-50">
            <ul class="list-unstyled p-0">
                <li class="cancellation-policy-fields">
                    <div class="border-bottom mb-6 policy-item group-customer">
                        <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                            <?=lang('customer_type') ?>
                        </h6>
                        <select class="js-select2-dropdown-arrow-template type-group-customers" required
                                onchange="meetingCancellationPolicy.typeGroupCustomersChange(this)" name="TypeGroupCustomers">
                            <option value=0 selected><?=lang('selected_by_tag')?></option>
                            <option value=1 ><?=lang('selected_by_number_entries')?></option>
                            <option value=2 ><?=lang('selected_by_status')?></option>
                        </select>
                        <div class="my-8 more-details">
                            <div class="more-details-tags">
                                <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                                    <?=lang('tag_selection') ?>
                                </h6>
                                <select class="js-select2-dropdown-arrow-template level-customers select2-hidden-options" name="LevelId">
                                </select>
                            </div>
                            <div class="d-none more-details-entries">
                                <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                                    <?=lang('number_entries') ?>
                                </h6>
                                <select class="js-select2-dropdown-arrow-template min-meeting-amount select2-hidden-options" name="MinMeetingAmount">
                                    <?php for ($i = 1; $i <= 10; $i++): ?>
                                        <option <?=$i == 4 ? "selected" : "" ?> value=<?=$i?>>
                                            <?=lang('customers_who_were_least') . " " . $i ." ". lang('meetings')?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="d-none more-details-status">
                                <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                                    <?=lang('status_selection') ?>
                                </h6>
                                <select class="js-select2-dropdown-arrow-template client-status select2-hidden-options" name="ClientStatus">
                                    <option value=0 selected><?=lang('active')?></option>
                                    <option value=1 ><?=lang('archive')?></option>
                                    <option value=2 ><?=lang('interested_single')?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-6 policy-details">
                        <div class="border-bottom mb-6 policy-item mt-16" order="2">
                            <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                                <?=lang('select_manual_amount_non_arrival') ?>
                            </h6>
                            <select class="js-select2-dropdown-arrow-template cancellation-policy-status" required
                                    onchange="meetingCancellationPolicy.cancellationPolicyStatusChange(this)"
                                    name="NotArriveChargeStatus">
                                <option value=0 selected><?=lang('with_out_cancellation_fee')?></option>
                                <option value=1 ><?=lang('manual_cancellation_fee')?></option>
                                <option value=2 ><?=lang('full_cancellation_fee')?></option>
                            </select>
                            <div class="my-8 more-details">
                                <div class="partial-payment-details more-details-item d-none">
                                    <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                                        <?=lang('amount_of_payment') ?>
                                    </h6>
                                    <div class="row align-items-center mb-20 bsapp-fs-16">
                                        <div class="col">
                                            <input type="number" placeholder=<?=lang('store_fixed_val')?> min="1" required
                                                   id="not-arrive-charge-amount" onchange="meetingCancellationPolicy.paymentValueChange(this)" name="NotArriveChargeAmount"
                                                   class="form-control bg-light rounded shadow-none m-0 py-2 px-10 policy-amount-input">
                                        </div>
                                        <div class="col-7 d-flex align-items-center pis-0">
                                            <div class="form-check mie-10 d-none">
                                                <input type="radio" class="form-check-input position-relative mie-5 payment-value-not-percentage"
                                                       onclick="meetingCancellationPolicy.isPercentageChange(this)" value=0
                                                       id="not-arrive-value-not-percentage">
                                                <label class="form-check-label" for="not-arrive-value-not-percentage">₪</label>
                                            </div>
                                            <div class="form-check mie-10">
                                                <input type="radio" class="form-check-input position-relative mie-5 payment-value-in-percentage"
                                                       onclick="meetingCancellationPolicy.isPercentageChange(this)" value=1
                                                       id="not-arrive-value-in-percentage" checked>
                                                <label class="form-check-label" for="not-arrive-value-in-percentage">%</label>
                                            </div>
                                            <div class="warrning-input-not-valid text-danger d-none">
                                                <?=lang('invalid_value')?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="border-bottom mb-6 policy-item mt-16" order="1">
                            <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                                <?=lang('charge_if_customer_canceled_manual_scheduling') ?>
                            </h6>
                            <select class="js-select2-dropdown-arrow-template cancellation-policy-status" required
                                    onchange="meetingCancellationPolicy.cancellationPolicyStatusChange(this)"
                                    name="ManualChargeStatus">
                                <option value=0 selected><?=lang('with_out_cancellation_fee')?></option>
                                <option value=1 ><?=lang('manual_cancellation_fee')?></option>
                                <option value=2 ><?=lang('full_cancellation_fee')?></option>
                            </select>
                            <div class="my-8 more-details">
                                <div class="partial-payment-details more-details-item mb-6 d-none">
                                    <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                                        <?=lang('amount_of_payment') ?>
                                    </h6>
                                    <div class="row align-items-center bsapp-fs-16">
                                        <div class="col">
                                            <input type="number" placeholder=<?=lang('store_fixed_val')?> min="1" required
                                                   id="manual-charge-status" onchange="meetingCancellationPolicy.paymentValueChange(this)" name="ManualChargeAmount"
                                                   class="form-control bg-light rounded shadow-none m-0 py-2 px-10 policy-amount-input">
                                        </div>
                                        <div class="col-7 d-flex align-items-center pis-0">
                                            <div class="form-check mie-10 d-none">
                                                <input type="radio" class="form-check-input position-relative mie-5 payment-value-not-percentage"
                                                       onclick="meetingCancellationPolicy.isPercentageChange(this)" value=0
                                                       id="manual-charge-value-not-percentage">
                                                <label class="form-check-label" for="manual-charge-value-not-percentage">₪</label>
                                            </div>
                                            <div class="form-check mie-10">
                                                <input type="radio" class="form-check-input position-relative mie-5 payment-value-in-percentage"
                                                       onclick="meetingCancellationPolicy.isPercentageChange(this)" value=1
                                                       id="manual-charge-value-in-percentage" checked>
                                                <label class="form-check-label" for="manual-charge-value-in-percentage">%</label>
                                            </div>
                                            <div class="warrning-input-not-valid text-danger d-none">
                                                <?=lang('invalid_value')?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="manual-charge-timing more-details-item d-none">
                                    <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                                        <?=lang('timing') ?>
                                    </h6>
                                    <div class="row mb-6">
                                        <div class="form-group col-3 mb-6">
                                            <select class="js-select2-dropdown-arrow-template time-cancel " name="ManualChargeTime" required>
                                                <?php for ($i = 1; $i <= 24; $i++) : ?>
                                                    <option value=<?=$i;?> <?= 6 == $i ? 'selected' : '' ?>><?= $i; ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                        <div class="form-group col-9 mb-6">
                                            <select class="js-select2-dropdown-arrow-template time-cancel-type" required name="ManualChargeTimeType">
                                                <option data-text='<?=lang('hours_before_meeting')?>' value=1 selected><?=lang('hours_before_meeting')?></option>
                                                <option data-text='<?=lang('days_before_meeting')?>' value=2><?=lang('days_before_meeting')?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                        <div class="border-bottom mb-6 policy-item" order="0">
                            <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                                <?=lang('charge_if_customer_canceled') ?>
                            </h6>
                            <select class="js-select2-dropdown-arrow-template cancellation-policy-status" required
                                    onchange="meetingCancellationPolicy.cancellationPolicyStatusChange(this)"
                                    name="AfterPurchaseChargeStatus">
                                <option value=0 selected><?=lang('with_out_cancellation_fee')?></option>
                                <option value=1 ><?=lang('manual_cancellation_fee')?></option>
                                <option value=2 ><?=lang('full_cancellation_fee')?></option>
                            </select>
                            <div class="my-8 more-details">
                                <div class="partial-payment-details more-details-item d-none">
                                    <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                                        <?=lang('amount_of_payment') ?>
                                    </h6>
                                    <div class="row align-items-center mb-20 bsapp-fs-16">
                                        <div class="col">
                                            <input type="number" placeholder=<?=lang('store_fixed_val')?> min="1" required
                                                   id="after-purchase-charge-amount" onchange="meetingCancellationPolicy.paymentValueChange(this)" name="AfterPurchaseChargeAmount"
                                                   class="form-control bg-light rounded shadow-none m-0 py-2 px-10 policy-amount-input">
                                        </div>
                                        <div class="col-7 d-flex align-items-center pis-0">
                                            <div class="form-check mie-10 d-none">
                                                <input type="radio" class="form-check-input position-relative mie-5 payment-value-not-percentage"
                                                       onclick="meetingCancellationPolicy.isPercentageChange(this)" value=0
                                                       name="IsPercentage" id="after-purchase-charge-not-percentage">
                                                <label class="form-check-label" for="after-purchase-charge-not-percentage">₪</label>
                                            </div>
                                            <div class="form-check mie-10">
                                                <input type="radio" class="form-check-input position-relative mie-5 payment-value-in-percentage"
                                                       onclick="meetingCancellationPolicy.isPercentageChange(this)" value=1
                                                       name="IsPercentage" id="def-payment-value-in-percentage" checked>
                                                <label class="form-check-label" for="def-payment-value-in-percentage">%</label>
                                            </div>
                                            <div class="warrning-input-not-valid text-danger d-none">
                                                <?=lang('invalid_value')?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="position-absolute bottom-0 left-0 p-15 bg-white w-100">
                <a class="add-new-meetings-policy btn btn-lg btn-primary text-white rounded-lg font-weight-bolder
                 shadow-none border-0 w-100 mb-15 bsapp-fs-16"
                   onclick="meetingCancellationPolicy.saveDynamicPayment(this)" role="button"><?=lang('save') ?></a>
            </div>
        </div>
    </div>
</div>

