<!-- Calendar appointments Settings Module :: Panel begin -->
<div class="bsapp-settings-panel meetings-template-class-type-remove d-none flex-column overflow-hidden position-absolute
 h-100 w-100 bg-white p-15 animated slideInStart" data-depth="4">
    <a class="text-black text-right text-right text-decoration-none text-start p-0 mie-30 mb-20" role="button"
       data-target="calendarSettings-meetings-templates-new" onclick="meetingTemplate.removeDeleteLoaders(this)">
    <h5 class="d-flex align-items-start text-black font-weight-bolder">
      <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
      <?php echo lang('back_single') ?>
    </h5>
  </a>
  <h5 class="d-flex align-items-center text-right text-gray-700 font-weight-bolder mb-15 bsapp-fs-14">
      <i class="fal fa-th-large mie-6 text-gray-500 bsapp-fs-19"></i>
        <?=lang('path_edit_appointments_remove_pay_option') ?>
  </h5>

    <!-- Start of Scrollable Area -->
    <div class="scrollable">
        <div class="pb-50">
            <div class="class-type-confirm-deleted-section">
                <div>
                    <p class="text-center mt-20 mb-50 bsapp-fs-18">
                        <?=lang('make_sure_delete')?>
                        <br>
                        <span class="font-weight-bold payment-option-text"><?=lang('payment_option')?></span>
                        <span class="class-type-name font-weight-bold">X דקות | Y מחיר </span>
                        ?</p>
                    <p class="text-right bsapp-fs-16 text-right">
                        <span class="link-item-text"><?=lang('this_payment_option_existing')?></span>
                        <span class="item-link-count font-weight-bold">10</span>
                        <span><?=lang('link_items')?></span>
                    </p>
                </div>
                <div class="text-right mt-20">
                    <p class="delete-explanation"><?=lang('confirm_delete_class-type_message')?></p>
                </div>
            </div>
        </div>
    </div>

  <div class="js-remove-button position-absolute bottom-0 left-0 bg-white p-15 pt-10 w-100">
    <div class="js-remove-class-type-button btn btn-lg btn-danger text-white rounded-lg font-weight-bolder
     shadow-none border-0 w-100 mb-15 bsapp-fs-16" onclick="meetingTemplate.removeClassType(this)"><?php echo lang('delete') ?></div>
    <a class="btn btn-lg bg-light text-gray-700 rounded-lg font-weight-bolder shadow-none border-0 w-100 bsapp-fs-16"
       onclick="meetingTemplate.removeDeleteLoaders(this)" role="button" data-target="calendarSettings-meetings-templates-new"><?php echo lang('cancel') ?></a>
  </div>

</div>
