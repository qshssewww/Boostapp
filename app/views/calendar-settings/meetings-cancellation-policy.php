
<!-- Calendar appointments Settings Module :: Panel begin -->
<div class="bsapp-settings-panel calendarSettings-meetings-cancellation-policy d-none flex-column
 overflow-hidden position-absolute h-100 w-100 bg-white p-15 animated slideInStart" data-depth="2">

  <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" role="button" data-target="Meetings-navigation">
    <h5 class="d-flex align-items-start font-weight-bolder">
      <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
        <?=lang('back_single') ?>
    </h5>
  </a>

    <h5 class="d-flex align-items-center text-gray-700 font-weight-bolder mb-15 bsapp-fs-14">
        <i class="fal fa-th-large mie-6 text-gray-500 bsapp-fs-19"></i>
        <?=lang('path_cal_meeting_cancellation_policy') ?>
    </h5>

    <!-- Start of Scrollable Area -->
    <div class="scrollable">
        <div class="pb-50">
            <ul class="list-unstyled p-0 list-of-loading">
                <li class="item-loading item-placeholder mb-10 animated fadeInUp">
                    <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
                        <div class="spinner-border spinner-border-sm text-success" role="status">
                            <span class="sr-only"><?php echo lang('loading') ?></span>
                        </div>
                    </div>
                </li>
            </ul>
            <ul class="list-unstyled p-0 list-of-cancellation-blocks d-none">
            </ul>
            <a class="font-weight-bold js-add-payment-option d-flex text-start js-add-policy-option
                      my-15 mb-0 bsapp-fs-16" role="button" onclick="meetingCancellationPolicy.newPolicytOption(this)">
                + <?=lang('add_cancellation_policy_option') ?>
            </a>

<!--            <div class="position-absolute bottom-0 left-0 p-15 bg-white w-100 ">-->
<!--                <a class="btn btn-lg btn-primary text-white rounded-lg font-weight-bolder js-add-policy-option-->
<!--                 shadow-none border-0 w-100 mb-15 bsapp-fs-16 save-button"-->
<!--                   onclick="meetingCancellationPolicy.newPolicytOption(this)" role="button">-->
<!--                    + --><?//=lang('add_cancellation_policy_option') ?><!--</a>-->
<!--            </div>-->
        </div>
    </div>
</div>
