<div class="bsapp-settings-panel edit-periodic-availability d-none flex-column overflow-hidden position-absolute h-100
 w-100 bg-white p-15 animated slideInStart" data-depth="5">
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
            <?=lang('path_appointments_staff_edit_availability_warning') ?>
            <span class="path-coach-name">Shon</span>
        </h5>
    </div>

    <div class="w-100 text-start mb-30 date-sub-title">
        <h6 class="font-weight-normal text-gray-700 title-date"></h6>
        <h6 class="font-weight-normal text-success title-date-time"></h6>
    </div>
    <p class="text-gray-500 text-start m-0 bsapp-fs-13 bsapp-lh-15">
        <?=lang('warning_editing_range_part_1')?>
        <span class="path-coach-name font-weight-bold">Shon</span>
        <?=lang('warning_editing_range_part_2')?>
    </p>

    <div class="pie-10">
        <a role="button" class="btn btn-light" data-target="meetings-coach-weekly-availability"><?=lang('action_cacnel')?></a>
    </div>
    <div class="position-absolute bottom-0 left-0 bg-white p-15 pt-10 w-100">
        <a role="button" class="btn btn-primary bg-white border-success text-success mb-10 btn-lg btn-block"
           data-target="meetings-coach-weekly-availability" onclick="SaveUpcomingRanges()">שמור זמינות מחזורית</a>
        <a role="button" class="btn btn-secondary bg-success text-white
        btn-lg btn-block" data-target="meetings-coach-weekly-availability" onclick="SaveThisRangeOnly()">שמור את זמינות זו בלבד!</a>
    </div>
</div>