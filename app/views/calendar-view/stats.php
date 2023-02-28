<div class="js-modal-view-filter d-flex justify-content-center position-absolute w-100  bsapp-bottom-stats" style="bottom:0;">
    <div class="d-flex align-items-center w-100  bg-white py-8 w-100 js-div-stats">

        <div class="flex-fill d-flex align-items-center justify-content-center p-0">
            <div class="d-flex flex-column text-center mie-10">
                <i class="fad fa-calendar-check text-gray-500 bsapp-fs-20"></i>
                <span class="bsapp-fs-11 bsapp-lh-9"><?php echo lang('classes') ?></span>
            </div>
            <span id="total_classes" class="text-dark bsapp-fs-24"></span>            
        </div>
        <div class="flex-fill d-flex align-items-center justify-content-center">
            <div class="d-flex flex-column text-center mie-10">
                <i class="fad fa-users text-gray-500 bsapp-fs-20"></i>
                <span class="bsapp-fs-11 bsapp-lh-9"><?php echo lang('cal_trainee') ?></span>
            </div>
            <span id="total_trainers" class="text-dark bsapp-fs-24"></span>
        </div>
    </div>
    <div class="d-none align-items-center w-100 justify-content-between  bg-white py-15 px-8 w-100 js-div-filter-apply">
        <a href="javascript:;" class="btn btn-sm  btn-light js-restore-filter-state mie-20 flex-fill" onclick="RestoreFilterState()">
            <?php echo lang('cancel') ?>
        </a>
        <a href="javascript:;" class="btn btn-sm btn-primary js-save-filter-state  flex-fill" onclick="SaveFilterState()">
            <?php echo lang('save') ?>
        </a>
    </div>
</div>