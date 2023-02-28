<?php
require_once __DIR__ . "/../../../office/Classes/Settings.php";
require_once __DIR__ . "/../../../office/Classes/ClassCalendar.php";

$CompanySettingsDash = $CompanySettingsDash ?? Settings::getSettings(Auth::user()->CompanyNum);
$calendarData = $calendarData ?? (new ClassCalendar())->getCalendarData();

$calendarDataJSON = json_decode($calendarData);
?>
<!-- Calendar Filters -->
<div id="calendarFilters" class="bsapp-fs-14 mb-20 mt-20 js-modal-view-filter">          
    <div class="mb-20  " >
        <select class="js-select-branches">
        </select>        
    </div>
    <div id="calendarFilters-none" class="d-none text-gray text-center">
        <i class="fal fa-calendar-exclamation fa-3x"></i>
        <h6 class="my-10 mx-5"><?php echo lang('no_info_selected_time'); ?></h6>
    </div>
    <div id="calendarFilters-all" class="text-gray text-start">
        <div class="text-center text-gray-400 mb-12">
            <div class="bg-light border-radius-8p p-8">
                <?php echo lang('show_filters_notice') ?>
            </div>
        </div>

<!--        TODO remove after beta - BS-1823         -->
        <?php if (in_array($CompanySettingsDash->beta, [1, 2])) : ?>
        <?php if (isset($calendarDataJSON->filters->tasks)
            && count($calendarDataJSON->filters->tasks) > 0
            && $calendarDataJSON->filters->tasks[0] > 0) : ?>
            <!-- Filter Tasks -->
            <div class="d-flex flex-column mb-15">
                <div id="calendarFilters-tasks" class="collapse show">
                    <ul class="list-unstyled mb-0 px-0 d-flex flex-column align-items-start">
                        <li>
                            <div class="custom-control custom-checkbox" data-branch-id="undefined"><input
                                        type="checkbox" class="fillter-check custom-control-input" value="all"
                                        id="js-filter-check-tasks-all" data-type="tasks"
                                    <?= ($calendarDataJSON->FilterState->Tasks == 1) ? 'checked' : '' ?>>
                                <label class="custom-control-label font-weight-bold pt-5" for="js-filter-check-tasks-all">
                                    <?= lang('tasks') ?>
                                </label>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
        <?php endif; ?>

        <div class="location-container d-flex flex-column mb-6">
            <a type="button"   class=" text-gray py-8"   >
                <span class="font-weight-bold"><?php echo lang('my_cal_new_desk') ?></span>
            </a>
            <div id="calendarFilters-location" class="" >
                <ul class="list-unstyled mb-0 px-0 d-flex flex-column align-items-start"></ul>
            </div>
        </div>
        <!-- Filter by Coaches -->
        <div class="owner-container flex-column mb-15 mt-15 <?php echo Auth::userCan('161') ? 'd-flex': 'd-none';?>">
            <a type="button" class="text-gray py-8 d-flex justify-content-between">
                <span class="font-weight-bold"><?php echo lang('desk_new_coaches') ?></span>
                <!--i class="fal fa-chevron-down"></i-->
            </a>
            <div id="calendarFilters-owner" class="collapse show" >
                <ul class="list-unstyled mb-0 px-0 d-flex flex-column align-items-start"></ul>
            </div>
        </div>
        <!-- Filter by Classes -->
        <div class="d-flex flex-column mb-50">
            <a type="button" class=" text-gray py-8 d-flex justify-content-between "  >
                <span class="font-weight-bold"><?php echo lang('desk_new_classes') ?></span>
                <!--i class="fal fa-chevron-down"></i-->
            </a>
            <div id="calendarFilters-title" class="collapse show">
                <ul class="list-unstyled mb-0 px-0 d-flex flex-column align-items-start"></ul>
            </div>
        </div>
    </div>
</div>