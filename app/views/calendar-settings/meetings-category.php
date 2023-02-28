
<!-- Calendar appointments Settings Module :: Panel begin -->
<div class="bsapp-settings-panel calendarSettings-meetings-category d-none flex-column
 overflow-hidden position-absolute h-100 w-100 bg-white p-15 animated slideInStart" data-depth="2">

  <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" role="button" data-target="Meetings-navigation">
    <h5 class="d-flex align-items-start font-weight-bolder">
      <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
        <?=lang('back_single') ?>
    </h5>
  </a>

    <h5 class="d-flex align-items-center text-gray-700 font-weight-bolder mb-15 bsapp-fs-14">
        <i class="fal fa-layer-group mie-6 text-gray-500 bsapp-fs-19"></i>
        <?=lang('path_cal_appointments_categories') ?>
    </h5>

    <!-- Start of Scrollable Area -->
    <div class="scrollable">
        <div class="pb-50">
            <ul class="meetings-category-list list-unstyled mt-10 p-0">
                <li class="item-loading mb-10 animated fadeInUp">
                    <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
                        <div class="spinner-border spinner-border-sm text-success" role="status">
                            <span class="sr-only"><?php echo lang('loading') ?>...</span>
                        </div>
                    </div>
                </li>
                <li class="item-loading mb-10 animated fadeInUp delay-1">
                    <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
                        <div class="spinner-border spinner-border-sm text-success" role="status">
                            <span class="sr-only"><?php echo lang('loading') ?>...</span>
                        </div>
                    </div>
                </li>
                <li class="item-loading mb-10 animated fadeInUp delay-2">
                    <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
                        <div class="spinner-border spinner-border-sm text-success" role="status">
                            <span class="sr-only"><?php echo lang('loading') ?>...</span>
                        </div>
                    </div>
                </li>
            </ul>

            <div class="category-row position-absolute bottom-0 left-0 bg-white p-15 pt-10 w-100">
                <div class="js-add-category ">
                    <div class="form-static d-flex">
                        <a class="btn btn-lg btn-primary text-white rounded-lg font-weight-bolder w-100 shadow-none border-0 bsapp-fs-16"
                           role="button" onclick="meetingCategories.toggleButtonAddCategory(this)">
                            <?= lang('add_new_meeting_category') ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
