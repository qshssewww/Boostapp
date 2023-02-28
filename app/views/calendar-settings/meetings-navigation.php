<!-- Calendar Settings Module :: Panel begin -->
<div class="bsapp-settings-panel Meetings-navigation d-none flex-column overflow-hidden position-absolute h-100 w-100 bg-white p-15 animated slideInStart" data-depth="1">

    <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" role="button" data-target="main-settings-panel">
        <h6 class="d-flex align-items-start font-weight-bolder">
            <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
            <?php echo lang('cal_back_to_cal_settings') ?>
        </h6>
    </a>

    <h3 class="d-flex align-items-center text-gray-700 font-weight-bolder mb-20 bsapp-fs-14">
        <i class="fal fa-calendar fa-fw mie-6 text-gray-500 bsapp-fs-19"></i>
        <?php echo lang('cal_appointments') ?>
    </h3>

    <!-- Start of Scrollable Area -->
    <div class="scrollable">
        <div class="pb-50 pr-10 pt-5">
            <ul class="list-unstyled p-0">
            <li class="mb-20 mb-15 pb-10 border-bottom border-light">
                    <div class="d-flex">
                        <i class="fal fa-th-large mie-10 bsapp-fs-20"></i>
                        <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                            <?php echo lang('templates') ?>
                        </h6>
                    </div>
                    <p class="text-gray-500 text-start m-0 bsapp-fs-13 bsapp-lh-15">
                        <?php echo lang('appointment_template_desc') ?>
                    </p>
                    <a id="template-management" class="d-flex align-items-center text-primary text-decoration-none font-weight-bolder py-3"
                       data-target="all-templates" role="button" onclick="meetingTemplate.getAllTemplates(this)">
                        <?php echo lang('manage') ?>
                        <i class="fal fa-angle-right mx-5 bsapp-fs-24"></i>
                    </a>
                </li>
                <li class="mb-20 mb-15 pb-10 border-bottom border-light">
                    <div class="d-flex">
                        <i class="fal fa-user-clock mie-10 bsapp-fs-20"></i>
                        <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                            <?php echo lang('cal_appointments_staff') ?>
                        </h6>
                    </div>
                    <p class="text-gray-500 text-start m-0 bsapp-fs-13 bsapp-lh-15">
                        <?php echo lang('cal_appointments_staff_sub') ?>
                    </p>
                    <a class="d-flex align-items-center text-primary text-decoration-none font-weight-bolder py-3"
                       data-target="calendarSettings-meetings-staff-availability" role="button"
                        onclick="meetingStaff.getAllCoaches(this)">
                        <?php echo lang('manage') ?>
                        <i class="fal fa-angle-right mx-5 bsapp-fs-24"></i>
                    </a>
                </li>
                <li class="mb-20 mb-15 pb-10 border-bottom border-light">
                    <div class="d-flex">
                        <i class="fal fa-sliders-h-square mie-10 bsapp-fs-20"></i>
                        <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                            <?php echo lang('general_settings_admin') ?>
                        </h6>
                    </div>
                    <p class="text-gray-500 text-start m-0 bsapp-fs-13 bsapp-lh-15">
                        <?php echo lang('general_settings_sub') ?>
                    </p>
                    <a class="d-flex align-items-center text-primary text-decoration-none font-weight-bolder py-3"
                       data-target="calendarSettings-meetings-general-settings" role="button">
                        <?php echo lang('manage') ?>
                        <i class="fal fa-angle-right mx-5 bsapp-fs-24"></i>
                    </a>
                </li>
                
                
                <li class="mb-20 mb-15 pb-10 border-bottom border-light pre-payment-section">
                    <div class="d-flex">
                        <i class="fal fa-badge-dollar mie-10 bsapp-fs-20"></i>
                        <h6 class="text-gray-700 text-start font-weight-bolder mb-10">

                            <?php echo lang('title_cancellation_policy') ?>
                          
                        </h6>
                    </div>
                    <p class="text-gray-500 text-start m-0 bsapp-fs-13 bsapp-lh-15">
                        <?php echo lang('title_cancellation_policy_sub') ?>
                    </p>
                    <a class="d-flex align-items-center text-primary text-decoration-none font-weight-bolder py-3"
                       data-target="calendarSettings-meetings-cancellation-policy" role="button"
                       onclick="meetingCancellationPolicy.getAllMeetingCancellationPolicy(this)">
                        <?php echo lang('manage') ?>
                        <i class="fal fa-angle-right mx-5 bsapp-fs-24"></i>
                    </a>
                </li>
                <li class="mb-20 mb-15 pb-10 border-bottom border-light meeting-category">
                    <div class="d-flex">
                        <i class="fal fa-layer-group mie-10 bsapp-fs-20"></i>
                        <h6 class="text-gray-700 text-start font-weight-bolder mb-10">
                            <?php echo lang('meeting_category') ?>
                        </h6>
                    </div>
                    <p class="text-gray-500 text-start m-0 bsapp-fs-13 bsapp-lh-15">
                        <?php echo lang('meeting_category_sub') ?>
                    </p>
                    <a class="d-flex align-items-center text-primary text-decoration-none font-weight-bolder py-3"
                       data-target="calendarSettings-meetings-category" role="button"
                       onclick="meetingCategories.getAllMeetingCategories(this)">
                        <?php echo lang('manage') ?>
                        <i class="fal fa-angle-right mx-5 bsapp-fs-24"></i>
                    </a>
                </li>
                
            </ul>
        </div>
    </div>


</div>

