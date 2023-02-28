
<!-- modal start -->
<div class="popupWrapper" id="editPopup">
    <div class="popupContainer smPopup scaleUp">
        <!-- modal header start -->
        <div class="generalPopupHeader mb-3 mt-3">
            <h4 class="generalPopupTitle"><?php echo lang('edit_lesson') ?></h4>
            <button type="button"  class="close newCalendarPopupCloseTimes closeEditPopup" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <!-- modal header end -->
        <!-- modal body start -->
        <div class="generalPopupBody container">

            <div class="row p-0 m-0 mb-5">
                <div>
                    <div class="col-12 p-0 m-0 mb-4">
                        <label class="radio-container"><?php echo lang('single_class_popup') ?>
                            <input name="type" type="radio" id="single" value="single" checked>
                            <span class="checkmark"></span>
                        </label>
                    </div>
                    <div class="col-12 p-0 m-0 mb-4">
                        <label class="radio-container"><?php echo lang('class_group_popup') ?>
                            <input name="type" type="radio" id="group" value="group">
                            <span class="checkmark"></span>
                        </label>
                    </div>
                    <div class="col-12 p-0 m-0 mb-4 d-flex align-items-center">
                        <label class="radio-container m-0"><?php echo lang('by_days_popup') ?>
                            <input name="type" type="radio" id="byDays" value="byDays">
                            <span class="checkmark"></span>
                        </label>
                    </div>
                    <div class="col-12 p-0 m-0 groupDaysContainer hideGroupDaysContainer">
                        <p><?php echo lang('in_days_popup') ?></p>
                        <div class="groupDays">
                            <ul>
                                <li class="groupDayLi" value="0" data-selected="0"><?php echo lang('sunday_day') ?></li>
                                <li class="groupDayLi" value="1" data-selected="0"><?php echo lang('monday_day') ?></li>
                                <li class="groupDayLi" value="2" data-selected="0"><?php echo lang('monday_day') ?></li>
                                <li class="groupDayLi" value="3" data-selected="0"><?php echo lang('monday_day') ?></li>
                                <li class="groupDayLi" value="4" data-selected="0"><?php echo lang('monday_day') ?></li>
                                <li class="groupDayLi" value="5" data-selected="0"><?php echo lang('monday_day') ?></li>
                                <li class="groupDayLi" value="6" data-selected="0">ש<?php echo lang('saturday_day') ?></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-12 p-0 mt-5 mb-3" style="display:flex; align-items:center;">
                        <label class="switch">
                            <input type="checkbox" id="toCancelClass">
                            <span class="slider round"></span>
                        </label>
                        <div class="plus">
                           <?php echo lang('cancel_class_popup') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- modal body end -->
        <!-- modal footer start  -->
        <div class="generalPopupFooter d-flex justify-content-end">
            <button class="subCancel generalPopupButtonCancel closeEditPopup"><?php echo lang('action_cacnel') ?></button>
            <button  id="editPopupButtonNext" class="subSave generalPopupButtonSave"><?php echo lang('continue_main') ?></button>
        </div>
        <!-- modal footer end -->
    </div>
    <!-- modal content end -->
</div>
