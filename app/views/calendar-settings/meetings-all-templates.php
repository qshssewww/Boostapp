
<div class="bsapp-settings-panel all-templates d-none flex-column overflow-hidden position-absolute h-100 w-100 bg-white p-15 animated slideInStart" data-depth="2">

    <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" role="button" data-target="Meetings-navigation">
        <h5 class="d-flex align-items-start font-weight-bolder">
            <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
            <?php echo lang('back_single') ?>
        </h5>
    </a>

    <h5 class="d-flex align-items-center text-gray-700 font-weight-bolder mb-10 bsapp-fs-14">
        <i class="fal fa-th-large mie-6 text-gray-500 bsapp-fs-19"></i>
        <?php echo lang('path_cal_appointments_template') ?>
    </h5>
    <h5 class="d-flex align-items-center text-gray-700 font-weight-bolder mb-10 bsapp-fs-18">
        <?php echo lang('all_templates_cal') ?>
    </h5>

    <!-- Start of Scrollable Area -->
    <div class="scrollable">
        <div class="pb-50">

            <ul class="templates-list list-unstyled p-0 bsapp-fs-14">
                <li class="item-loading item-placeholder mb-10 animated fadeInUp">
                    <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
                        <div class="spinner-border spinner-border-sm text-success" role="status">
                            <span class="sr-only"><?php echo lang('loading') ?></span>
                        </div>
                    </div>
                </li>
                <li class="item-loading item-placeholder mb-10 animated fadeInUp delay-1">
                    <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
                        <div class="spinner-border spinner-border-sm text-success" role="status">
                            <span class="sr-only"><?php echo lang('loading') ?></span>
                        </div>
                    </div>
                </li>
                <li class="item-loading item-placeholder mb-10 animated fadeInUp delay-2">
                    <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
                        <div class="spinner-border spinner-border-sm text-success" role="status">
                            <span class="sr-only"><?php echo lang('loading') ?></span>
                        </div>
                    </div>
                </li>
            </ul>

        </div>
    </div>


    <div class="position-absolute bottom-0 left-0 bg-white p-15 pt-10 w-100">
        <a class="btn btn-lg btn-primary text-white rounded-lg font-weight-bolder shadow-none border-0 w-100 bsapp-fs-16"
           data-target="calendarSettings-meetings-templates-new" onclick="meetingTemplate.renderEmptyNewTemplateForm()"
           data-templates="multi"><?php echo lang('create_new') ?></a>
    </div>
</div>