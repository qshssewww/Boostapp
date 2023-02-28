<div class="bsapp-settings-panel meetings-coach-weekly-availability d-none flex-column overflow-hidden
 position-absolute h-100 w-100 bg-white p-15 animated slideInStart" data-depth="3">

    <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" role="button" data-target="calendarSettings-meetings-staff-availability">
        <h5 class="d-flex align-items-start font-weight-bolder">
            <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
            <?php echo lang('back_single') ?>
        </h5>
    </a>

    <div class="d-flex">
        <h5 class="align-items-center text-gray-700 font-weight-bolder mb-10 bsapp-fs-14"
            style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">
            <i class="fal fa-user-clock mie-6 text-gray-500 bsapp-fs-19"></i>
            <?php echo lang('path_appointments_staff') . ' -' ?>
            <span class="path-coach-name">Shon</span>
        </h5>

    </div>

    <div class="w-100 d-flex time-selection-section">
            <a class="bg-light shadow-none text-center outline-none rounded py-6 px-20 pie-0 w-25 m-5" onclick="meetingStaff.changeDate(this)">
                <?= lang('today')?>
            </a>
            <div class="d-flex w-100 align-items-center justify-content-space-between position-relative">
                <input type='text' id='week-picker' class="text-center border-0 p-0"
                       style="width: 0;" onchange="meetingStaff.pickDateChange(this)" />
                <a data-toggle="tooltip" data-placement="top" title="<?=lang('previous') ?>"
                   class="btn btn-outline-gray-300 text-dark prev-week" onclick="meetingStaff.changeDate(this, -7)">
                    <i class="fas fa-angle-left"></i>
                </a>
                <div class="position-relative" id="alt-date">
                    <input title="" type="text" id="alt-date-input"
                           class="w-100 text-center btn btn-outline-gray-300 text-dark">
                    <i class="fal fa-calendar position-absolute" style="top: 10px;right: 10px;cursor: pointer;"></i>
                </div>
                <a data-toggle="tooltip" data-placement="top" title="<?=lang('next_client_profile')?>"
                   onclick="meetingStaff.changeDate(this, +7)" class="btn btn-outline-gray-300 text-dark next-week">
                    <i class="fas fa-angle-right"></i>
                </a>
            </div>

    </div>

    <div class="scrollable days-times days-time-section">
        <ul class="list-unstyled p-0 list-of-loading">
            <li class="item-loading item-placeholder mb-10 animated fadeInUp">
                <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
                    <div class="spinner-border spinner-border-sm text-success" role="status">
                        <span class="sr-only"><?php echo lang('loading') ?></span>
                    </div>
                </div>
            </li>
        </ul>
        <ul class="list-unstyled p-0 list-days-time d-none">
            <li class="border-top days" data-day=0>
                <div class="d-flex justify-content-between ">
                    <h6 class="title-day"><?=lang('sunday').'- '?>
                        <span class="date-of-day"></span></h6>
                    <a class="add-new-time" onclick="meetingStaff.renderNewAvailabilityPage(this)"
                       role="button" data-time=""  data-target="meetings-add-coach-availability">
                        <i class="fa fa-plus text-success"></i>
                    </a>
                </div>
                <div class="times-list">
                </div>
            </li>
            <li class="border-top days" data-day=1 >
                <div class="d-flex justify-content-between ">
                    <h6 class="title-day"><?=lang('monday').'- '?>
                        <span class="date-of-day"></span></h6>
                    <a class="add-new-time" onclick="meetingStaff.renderNewAvailabilityPage(this)" role="button"  data-target="meetings-add-coach-availability">
                        <i class="fa fa-plus text-success"></i>
                    </a>
                </div>
                <div class="times-list">

                </div>
            </li >
            <li class="border-top days" data-day=2 >
                <div class="d-flex justify-content-between ">
                    <h6 class="title-day"><?=lang('tuesday').'- '?>
                        <span class="date-of-day"></span></h6>
                    <a class="add-new-time" onclick="meetingStaff.renderNewAvailabilityPage(this)" role="button"  data-target="meetings-add-coach-availability">
                        <i class="fa fa-plus text-success"></i>
                    </a>
                </div>
                <div class="times-list">

                </div>
            </li>
            <li class="border-top days" data-day=3 >
                <div class="d-flex justify-content-between ">
                    <h6 class="title-day"><?=lang('wednesday').'- '?>
                        <span class="date-of-day"></span>
                    </h6>
                    <a class="add-new-time" onclick="meetingStaff.renderNewAvailabilityPage(this)" role="button"  data-target="meetings-add-coach-availability">
                        <i class="fa fa-plus text-success"></i>
                    </a>
                </div>
                <div class="times-list">

                </div>
            </li>
            <li class="border-top days" data-day=4>
                <div class="d-flex justify-content-between ">
                    <h6 class="title-day"><?=lang('thursday').'- '?>
                        <span class="date-of-day"></span></h6>
                    <a class="add-new-time" onclick="meetingStaff.renderNewAvailabilityPage(this)" role="button"  data-target="meetings-add-coach-availability">
                        <i class="fa fa-plus text-success"></i>
                    </a>
                </div>
                <div class="times-list">

                </div>
            </li>
            <li class="border-top days" data-day=5>
                <div class="d-flex justify-content-between ">
                    <h6 class="title-day"><?=lang('friday').'- '?>
                        <span class="date-of-day"></span></h6>
                    <a class="add-new-time" onclick="meetingStaff.renderNewAvailabilityPage(this)" role="button"
                       data-target="meetings-add-coach-availability">
                        <i class="fa fa-plus text-success"></i>
                    </a>
                </div>
                <div class="times-list">

                </div>
            </li>
            <li class="border-top days" data-day=6 >
                <div class="d-flex justify-content-between ">
                    <h6 class="title-day"><?=lang('saturday').' - '?>
                        <span class="date-of-day"></span></h6>
                    <a class="add-new-time" onclick="meetingStaff.renderNewAvailabilityPage(this)" role="button"  data-target="meetings-add-coach-availability">
                        <i class="fa fa-plus text-success"></i>
                    </a>
                </div>
                <div class="times-list">

                </div>
            </li>
        </ul>

    </div>
</div>



<style>
    .ui-datepicker-current-day .ui-state-default {
        border: 1px solid #003eff;
        background: #007fff;
        font-weight: normal;
        color: #fff;
    }
</style




