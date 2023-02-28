<!-- Calendar Settings Module :: Panel begin -->
<div class="bsapp-settings-panel calendarSettings-display-options d-none flex-column overflow-hidden h-100 w-100 bg-white  animated slideInStart" data-depth="1">
    <div class="h-100 d-flex flex-column justify-content-between">
        <a class="text-black text-decoration-none text-start p-15 mie-30 mb-20" role="button" data-target="main-settings-panel">
            <h5 class="d-flex align-items-start font-weight-bolder">
                <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
                <?php echo lang('cal_back_to_cal_settings') ?>
            </h5>
        </a>
        <h3 class="d-flex align-items-center text-gray-700 font-weight-bolder mb-20 bsapp-fs-14 px-15">
            <i class="fal fa-eye mie-6 text-gray-500 bsapp-fs-19"></i>
            <?php echo lang('cal_display_options') ?>
        </h3>
        <!-- Start of Scrollable Area -->
        <div class="bsapp-scroll bsapp-overflow-y-auto px-15 flex-fill" >
            <div class="pb-50">
                <ul class="list-unstyled p-0">
                    <li class="mb-15 border-bottom">
                        <h6 class="text-gray-700 text-start font-weight-bolder mb-20"><?php echo lang('cal_display_view_type') ?></h6>
                        <div class="label-boxes d-flex mb-15">
                            <div class="col-6 text-center">
                                <label class="bsapp-custom-radio active w-100" for="calendar-type-view" role="button">
                                    <div class="d-flex flex-column align-items-center justify-content-center border rounded py-30 mb-10">
                                        <i class="fal fa-calendar mb-20 bsapp-fs-40"></i>
                                        <b class="text-gray-500 bsapp-fs-14"><?php echo lang('cal_display_type_calendar') ?></b>
                                    </div>
                                    <input type="radio" onchange="timeRangeVisibility()" name="type-view" id="calendar-type-view" data-setting="general" checked>
                                    <span class="rounded-circle"></span>
                                </label>
                            </div>
                            <div class="col-6 text-center">
                                <label class="bsapp-custom-radio w-100" for="agenda-type-view" role="button">
                                    <div class="d-flex flex-column align-items-center justify-content-center border rounded py-30 mb-10">
                                        <i class="fal fa-th-list mb-20 bsapp-fs-40"></i>
                                        <b class="text-gray-500 bsapp-fs-14"><?php echo lang('cal_display_type_agenda') ?></b>
                                    </div>
                                    <input type="radio" onchange="timeRangeVisibility()" name="type-view" id="agenda-type-view" data-setting="general">
                                    <span class="rounded-circle"></span>
                                </label>
                            </div>
                        </div>
                    </li>

                    <li class="mb-15 d-none d-md-block">
                        <h6 class="text-gray-700 text-start font-weight-bolder mb-15"><?php echo lang('cal_display_split_view') ?></h6>
                        <div class="row mb-10">
                            <div class="col-7">
                                <select class="js-select2" name="form-of-payment" id="split-view" data-setting="general">
                                    <option value="1"><?php echo lang('cal_split_no_split') ?></option>
                                    <option value="0"><?php echo lang('cal_split_by_coach') ?></option>
                                    <option value="2"><?php echo lang('cal_split_by_calendar') ?></option>
                                </select>
                            </div>
                        </div>
                        <p class="text-gray-500 text-start m-0 mb-15 bsapp-fs-13 bsapp-lh-15"><?php echo lang('cal_display_split_view_sub') ?></p>
                    </li>
                    <li class="mb-15">
                        <h6 class="text-gray-700 text-start font-weight-bolder mb-15"><?php echo lang('cal_hour_range') ?></h6>
                        <div class="d-flex">
                            <input type="time" name="time_from" id="js-time-from" data-setting="general" class="form-control bg-light border border-light mie-15"/> <input type="time" data-setting="general" id="js-time-to" name="time_to" class="form-control bg-light border border-light"/>
                        </div>
                    </li>
                </ul>

            </div>
        </div>
        <div class="bg-white p-15 pt-10 w-100" >
            <a class="d-none btn-save-calendar-settings btn btn-lg btn-primary text-white rounded-lg font-weight-bolder shadow-none border-0 w-100 bsapp-fs-16" role="button" onclick="updateCalendarSettings($(this))"><?php echo lang('save_changes_button') ?></a>
        </div>
    </div>
</div>