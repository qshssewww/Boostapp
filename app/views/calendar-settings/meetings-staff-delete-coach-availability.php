<div class="bsapp-settings-panel delete-coach-availability d-none flex-column overflow-hidden position-absolute h-100 w-100 bg-white p-15 animated slideInStart" data-depth="5">
    <a class="text-black text-decoration-none text-start p-0 mie-30 mb-20" role="button"
       data-target="meetings-add-coach-availability">
        <h5 class="d-flex align-items-start font-weight-bolder">
            <i class="fal fa-angle-left mie-10 bsapp-fs-24"></i>
            <?php echo lang('back_single') ?>
        </h5>
    </a>
    <div class="d-flex">
        <h5 class="align-items-center text-gray-700 font-weight-bolder mb-10 bsapp-fs-14"
            style="white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">
            <i class="fal fa-user-clock mie-6 text-gray-500 bsapp-fs-19"></i>
            <?=lang('path_appointments_staff_delete_availability_warning') ?>
        </h5>
    </div>
    <div class="scrollable delete-section">
        <p class="text-start m-0 delete-warning">
            <?=lang('warning_delete_the_availability_of') ?>
            <span class="font-weight-bolder title-coach-name">
                name
            </span>
            <br>
            <?=lang('in') ?>
            <span class="font-weight-bolder title-date">
            יום ראשון - 21/10/22
            </span>
            <br>
            <?=lang('between_the_hours') ?>
            <span class="font-weight-bolder title-date-time">
              17:37 - 21:37
            </span>
        </p>

        <div class="periodic-edit-fields d-none">
            <div class="alert alert-warning bsapp-fs-14 px-10 text-start my-10" role="aler" >
                <?=lang('delete_periodic_availability_warning')?>
            </div>
            <div class="d-flex flex-column w-100 text-start mb-1 edit-mode-section">
                <span class="mb-7 bsapp-fs-16"><?=lang('select_delete_type')?></span>
                <div class="d-flex col-10 pis-0 pie-10">
                    <i class="fa fa-repeat-alt font-weight-normal text-gray-500 m-10" ></i>
                    <select class="js-select2-dropdown-arrow-template edit-mode" name="editMode"
                            onchange="meetingStaff.editModeChange(this)">
                        <option value="0" selected><?=lang('delete_this_instance_only')?></option>
                        <option value="1"><?=lang('delete_as_a_seriese')?></option>
                    </select>
                </div>

                <div class="flex-column mt-10 mr-15 text-start start-periodic-details periodic-details-mode d-none">
                    <span class="mb-7 bsapp-fs-16"><?=lang('beginning_deleting_series')?></span>
                    <div class="d-flex col-10 pis-0 pie-10">
                        <i class="fa fa-hourglass-start font-weight-normal text-gray-500 m-10" ></i>
                        <select class="js-select2-dropdown-arrow-template start-periodic edit-mode-1" name="startPeriodic">
                            <option value="0" selected><?=lang('from_beginning_of_series')?></option>
                            <option value="1" class="date-option"><?=lang('from_this_date')?></option>
                        </select>
                    </div>
                </div>
                <div class="flex-column mt-10 mr-15 text-start end-periodic-details periodic-details-mode d-none">
                    <span class="mb-7 bsapp-fs-16"><?=lang('delete_series_until')?></span>
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
                            <option value="1"><?=lang('after_cal') . " " .lang('show_desk_plan') . " " .lang('one')?></option>
                            <?php for ($i = 2; $i <= 20; $i++) : ?>
                                <option value=<?=$i;?> <?= 2 == $i ? 'selected' : '' ?>><?= lang('after_cal') . " " . $i . " " .lang('shows_desk_plan') ?></option>
                            <?php endfor; ?>

                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex flex-colume bg-white pt-10 px-10 w-100 save-delete-section">
        <div class="col-5 pie-6 pis-0">
            <a class="btn btn-lg text-gray-700 bg-light rounded-lg font-weight-bolder shadow-none border-0 w-100 bsapp-fs-16"
               data-target="meetings-add-coach-availability"><?=lang('cancel')?></a>
        </div>
        <div class="col-7 pis-6 pie-0 js-delete-availability">
            <a class=" btn btn-lg bg-danger text-white rounded-lg font-weight-bolder shadow-none border-0 w-100
         bsapp-fs-16 delete-availability" onclick="meetingStaff.removeAvailability(this)"><?=lang('delete')?></a>
        </div>
    </div>


</div>