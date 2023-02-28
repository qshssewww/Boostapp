<!-- Calendar appointments Settings Module :: Panel begin -->
<div class="bsapp-settings-panel calendarSettings-meetings-category-remove d-none flex-column overflow-hidden position-absolute
 h-100 w-100 bg-white p-15 animated slideInStart" data-depth="3">

    <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" role="button"
       onclick="meetingCategories.removeDeleteLoaders()" data-target="calendarSettings-meetings-category">
    <h5 class="d-flex align-items-start text-black font-weight-bolder">
      <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
      <?php echo lang('back_single') ?>
    </h5>
  </a>
  <h5 class="d-flex align-items-center text-gray-700 font-weight-bolder mb-15 bsapp-fs-14">
      <i class="fal fa-layer-group mie-6 text-gray-500 bsapp-fs-19"></i>
        <?=lang('path_cal_appointments_categories_remove') ?>
  </h5>


    <!-- Start of Scrollable Area -->
    <div class="scrollable">
        <div class="pb-50">
            <div class="loading-confirm-deleted-section">
                <div class="item-loading mb-10 animated fadeInUp delay-1">
                    <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
                        <div class="spinner-border spinner-border-sm text-success" role="status">
                            <span class="sr-only"><?php echo lang('loading') ?>...</span>
                        </div>
                    </div>
                </div>

            </div>
            <div class="d-none category-confirm-deleted-section" required>
                <div>
                    <p class="text-center mt-20 mb-50 bsapp-fs-18">
                        <?=lang('make_sure_delete')?>
                        <br>
                        <span class="category-name font-weight-bold"> קטגוריה עם שם ארוך </span>
                        ?</p>
                    <p class="text-right bsapp-fs-16 text-right">
                        <?=lang('this_category_have')?>
                        <span class="template-count font-weight-bold">10</span>
                        <?=lang('different_meetings')?>
                    </p>
                </div>
                <div class="text-right mt-20">
                    <p><?=lang('confirm_delete_category_message')?></p>
                    <div class="form-group">
                        <select id='js-select2-template-category' name="CategoryId" required></select>
                    </div>
                </div>
            </div>
        </div>
    </div>

  <div class="js-remove-button position-absolute bottom-0 left-0 bg-white p-15 pt-10 w-100 d-none">
    <div class="js-replace-category-button btn btn-lg btn-primary text-white rounded-lg font-weight-bolder shadow-none border-0
     w-100 mb-15 bsapp-fs-16"
    ><?php echo lang('change_and_delete') ?></div>
    <a class="btn btn-lg bg-light text-gray-700 rounded-lg font-weight-bolder shadow-none border-0 w-100 bsapp-fs-16"
       onclick="meetingCategories.removeDeleteLoaders()" role="button" data-target="calendarSettings-meetings-category"><?php echo lang('cancel') ?></a>
  </div>

</div>
