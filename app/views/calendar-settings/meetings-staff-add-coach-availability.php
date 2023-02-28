<div class="bsapp-settings-panel meetings-add-coach-availability d-none flex-column overflow-hidden position-absolute
 h-100 w-100 bg-white p-15 animated " data-depth="4">
    <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" role="button" data-target="meetings-coach-weekly-availability">
        <h5 class="d-flex align-items-start font-weight-bolder">
            <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
            <?php echo lang('back_single') ?>
        </h5>
    </a>
    <div class="d-flex">
        <h5 class="align-items-center text-gray-700 font-weight-bolder mb-10 bsapp-fs-14"
            style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">
            <i class="fal fa-user-clock mie-6 text-gray-500 bsapp-fs-19"></i>
            <?=lang('path_appointments_staff_add_availability') ?>
            <span class="path-coach-name">Name</span>
        </h5>
    </div>

    <div class="d-flex justify-content-between remove-block px-15 py-10">
        <div class="w-25">
            <a role="button" data-target="delete-coach-availability" class="title-remove d-none" onclick="meetingStaff.renderDeleteAvailability(this)">
                <span class="time-delete-btn btn btn-danger btn-block text-white btn-sm">
                    <?=lang('delete')?></span>
            </a>
        </div>
        <div class="w-75 date-sub-title">
            <h6 class="font-weight-normal text-gray-700 title-date"></h6>
            <h6 class="font-weight-normal text-gray-500 title-date-time"></h6>
        </div>
    </div>
    <div class="list-unstyled p-0 list-of-loading h-100">
        <li class="item-loading item-placeholder mb-10 animated fadeInUp">
            <div class="form-static d-flex align-items-center justify-content-center bg-light rounded text-start m-0 py-15 px-10 bsapp-fs-14">
                <div class="spinner-border spinner-border-sm text-success" role="status">
                    <span class="sr-only"><?php echo lang('loading') ?></span>
                </div>
            </div>
        </li>
    </div>

    <div class="scrollable availability-details-section d-none">
        <div class="availability-fields">
            <input type="hidden" name="UserId"/>
            <input type="hidden" name="Date"/>
            <input type="hidden" name="Day"/>
            <input type="hidden" name="WasRepeatStatus"/>

            <div class="d-flex flex-column w-100 text-start">
                <span class="mb-7 bsapp-fs-16"><?=lang('hour_range')?></span>
                <div class="row px-10  bsapp-fs-14 pick-time-section">
                    <div class="col-6">
                        <div class="form-group">
                            <label><?=lang('begin_time')?></label>
                            <input name="StartTime" type="time" step="300" onchange="meetingStaff.timeChange(this)"
                                   class="form-control bg-light border rounded shadow-none py-2 bsapp-fs-14 time-input" required>
                            <div class="validation-warning d-none text-danger"></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label><?=lang('finish_time')?></label>
                            <input name="EndTime" type="time" step="300" onchange="meetingStaff.timeChange(this)"
                                   class="form-control bg-light border rounded shadow-none py-2 bsapp-fs-14 time-input" required>
                            <div class="validation-warning d-none text-danger"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex availability-details-part-1 flex-column w-100 text-start mb-15">
                <span class="mb-7 bsapp-fs-16"><?=lang('repeated')?></span>
                <div class="d-flex col-10 pis-0 pie-10">
                    <i class="fa fa-sync font-weight-normal text-gray-500 m-10" ></i>
                    <select class="js-select2-dropdown-arrow-template repeat-status" name="RepeatStatus"
                            onchange="meetingStaff.repeatStatusChange(this)" required>
                        <option value="0"><?=lang('without')?></option>
                        <option value="1" selected><?=lang('weekly_repeated')?></option>
                    </select>
                </div>
            </div>
            <div class="d-none availability-details-part-1 end-repeated-section flex-column w-100 text-start mb-15">
                <span class="mb-7 bsapp-fs-16"><?=lang('end_repeated')?></span>
                <div class="d-flex col-10 pis-0 pie-10">
                    <i class="fa fa-hand-paper font-weight-normal text-gray-500 m-10" ></i>
                    <select class="js-select2-dropdown-arrow-template end-periodic-date-status" name="EndPeriodicDateStatus"
                            onchange="meetingStaff.endRepeatedStatusChange(this)">
                        <option value="0" selected><?=lang('forever')?></option>
                        <option value="1"><?=lang('specific_date')?></option>
                    </select>
                </div>
                <div class="col-8 mt-10 text-start end-periodic-date-section d-none">
                    <input type="date" class="mr-20 form-control bg-light border rounded shadow-none py-2 bsapp-fs-14"
                           name="EndPeriodicDate" id="end-periodic-date" data-date="" data-date-format="DD/MM/YYYY"
                           onchange="meetingStaff.endRepeatedDateChange(this)"
                           placeholder="<?=lang('select_date')?>">
                </div>
            </div>

        </div>
        <div class="periodic-edit-fields d-none">
            <div class="alert alert-warning bsapp-fs-14 px-10 text-start" role="aler" >
                <?=lang('editing_periodic_availability_warning')?>
            </div>
            <div class="d-flex flex-column w-100 text-start mb-1 edit-mode-section">
                <span class="mb-7 bsapp-fs-16"><?=lang('select_edit_type')?></span>
                <div class="d-flex col-10 pis-0 pie-10">
                    <i class="fa fa-repeat-alt font-weight-normal text-gray-500 m-10" ></i>
                    <select class="js-select2-dropdown-arrow-template edit-mode" name="editMode"
                            onchange="meetingStaff.editModeChange(this)">
                        <option value="0" selected><?=lang('edit_this_instance_only')?></option>
                        <option value="1"><?=lang('edit_as_a_seriese')?></option>
                    </select>
                </div>

                <div class="flex-column mt-10 mr-15 text-start start-periodic-details periodic-details-mode d-none">
                    <span class="mb-7 bsapp-fs-16"><?=lang('beginning_editing_series')?></span>
                    <div class="d-flex col-10 pis-0 pie-10">
                        <i class="fa fa-hourglass-start font-weight-normal text-gray-500 m-10" ></i>
                        <select class="js-select2-dropdown-arrow-template start-periodic edit-mode-1" name="startPeriodic">
                            <option value="0" selected><?=lang('from_beginning_of_series')?></option>
                            <option value="1" class="date-option"><?=lang('from_this_date')?></option>
                        </select>
                    </div>
                </div>
                <div class="flex-column mt-10 mr-15 text-start end-periodic-details periodic-details-mode d-none">
                    <span class="mb-7 bsapp-fs-16"><?=lang('edit_series_until')?></span>
                    <div class="d-flex col-10 pis-0 pie-10">
                        <i class="fa fa-hourglass-end font-weight-normal text-gray-500 m-10" ></i>
                        <select class="js-select2-dropdown-arrow-template end-periodic edit-mode-1" name="endPeriodic"
                                onchange="meetingStaff.endPeriodicChange(this)">
                            <option value="2" selected><?=lang('until_end_series')?></option>
                            <option value="0"><?=lang('until_date')?></option>
                            <option value="1"><?=lang('number_of_repetitions')?></option>
                        </select>
                    </div>

                    <div class="mt-10 mr-30 col-8 pis-0 text-start end-periodic-date end-periodic-details d-none">
                        <input type="date" class="form-control bg-light border rounded shadow-none py-2 bsapp-fs-14"
                               name="endPeriodicDate" data-date="" data-date-format="DD/MM/YYYY"
                               placeholder="<?=lang('select_date')?>">
                    </div>

                    <div class="mt-10 mr-30 col-8 pis-0 text-start end-periodic-amount end-periodic-details d-none">
                        <select class="js-select2-dropdown-arrow-template end-periodic-amount" name="endPeriodicAmount">
                            <option value="0"><?=lang('after_cal') . " " .lang('show_desk_plan') . " " .lang('one')?></option>
                            <?php for ($i = 2; $i <= 20; $i++) : ?>
                                <option value=<?=$i;?> <?= 2 == $i ? 'selected' : '' ?>><?= lang('after_cal') . " " . $i . " " .lang('shows_desk_plan') ?></option>
                            <?php endfor; ?>

                        </select>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="d-flex bg-white pt-10 px-10 w-100 save-section">
        <div class="col-5 pie-6 pis-0">
            <a class="btn btn-lg text-gray-700 bg-light rounded-lg font-weight-bolder shadow-none border-0 w-100 bsapp-fs-16"
               data-target="meetings-coach-weekly-availability"><?=lang('back_new_add_credit')?></a>
        </div>
        <div class="col-7 pis-6 pie-0 js-save-time-availability">
            <a class=" btn btn-lg btn-primary text-white rounded-lg font-weight-bolder shadow-none border-0 w-100
             bsapp-fs-16 save-time-availability" onclick="meetingStaff.saveAvailability(this)"><?=lang('save')?></a>
        </div>
    </div>
</div>

<style>
    #end-periodic-date {
        position: relative;
        width: 150px;
        color: white;
    }

    #end-periodic-date:before {
        position: absolute;
        left: 3px;
        content: attr(data-date);
        display: inline-block;
        color: black;
    }

    #end-periodic-date::-webkit-datetime-edit, #end-periodic-date::-webkit-inner-spin-button, #end-periodic-date::-webkit-clear-button {
        display: none;
    }

    #end-periodic-date::-webkit-calendar-picker-indicator {
        position: absolute;
        right: 0;
        color: black;
        opacity: 1;
    }
</style>