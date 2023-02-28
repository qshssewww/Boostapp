<!-- modal start -->
<style>
    .mb-5{
        margin-bottom:10px!important;
    }
    .mb-4{
        margin-bottom:8px!important;
    }
    .mb-3{
        margin-bottom:6px!important;
    }
    .mb-2{
        margin-bottom:4px!important;
    }
    .mb-1{
        margin-bottom:2px!important;
    }
    .mt-5{
        margin-bottom:10px!important;
    }
    .mt-4{
        margin-bottom:8px!important;
    }
    .mt-3{
        margin-bottom:6px!important;
    }
    .mt-2{
        margin-bottom:4px!important;
    }
    .mt-1{
        margin-bottom:2px!important;
    }
</style>
<div class="popupWrapper" id="frequencySettings">
    <div class="popupContainer smPopup scaleUp">
        <!-- modal header start -->
        <div class="generalPopupHeader mb-5 mt-3">
            <h4 class="generalPopupTitle">תדירות מותאמת אישית</h4>
            <button type="button"  class="close newCalendarPopupCloseTimes closeFrequencyPopup" data-target="createNewClassType" data-parent="mainPopup" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <!-- modal header end -->
        <!-- modal body start -->
        <div class="generalPopupBody container">
            <div class="row d-flex align-items-center p-0 m-0 mb-5">
                <div class="col-2 p-0 m-0 ml-2 ">
                    חזור כל
                </div>
                <div class="col-2 p-0 m-0">
                    <input type="text" name="frequencyNumber" class='cute-input' style="width:40px;" value='1' id="frequencyNumber">
                </div>
                <div class="col-7 p-0 m-0">
                    <select name="frequencyTypeOfUnit" id="frequencyTypeOfUnit" class='cute-input'>
                        <option value="1">ימים</option>
                        <option value="2">שבועות</option>
                    </select>
                </div>
            </div>
            <div class="row p-0 m-0 mb-5 weekDaysContainer hideWeekDaysList">
                <div class="col-12 p-0 m-0 mb-3">
                    בימים
                </div>
                <div class="col-12 p-0 m-0">
                    <div class="days">
                        <ul>
                            <li class="dayLi" value="0" data-selected="0">א</li>
                            <li class="dayLi" value="1" data-selected="0">ב</li>
                            <li class="dayLi" value="2" data-selected="0">ג</li>
                            <li class="dayLi" value="3" data-selected="0">ד</li>
                            <li class="dayLi" value="4" data-selected="0">ה</li>
                            <li class="dayLi" value="5" data-selected="0">ו</li>
                            <li class="dayLi" value="6" data-selected="0">ש</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row p-0 m-0 mb-5">
                <div class="col-12 p-0 m-0 mb-3">
                    <?= lang('ends_calendar') ?>
                </div>
                <div class="ends">
                    <div class="col-12 p-0 m-0 mb-4">
                        <label class="radio-container"><?= lang('cal_never') ?>
                            <input name="end" type="radio" id="never" value="1" checked>
                            <span class="checkmark"></span>
                        </label>
                    </div>
                    <div class="col-12 p-0 m-0 mb-4">
                        <label class="radio-container"><?= lang('in_date_cron') ?>
                            <input name="end" type="radio" id="given_date" value="2">
                            <span class="checkmark"></span>
                        </label>
                        <input class="cute-input d-inline-flex mr-2" style="width:150px;" type="text" id="ftime">
                    </div>
                    <div class="col-12 p-0 m-0 mb-4 d-flex align-items-center">
                        <label class="radio-container m-0"><?= lang('after_cal') ?>
                            <input name="end" type="radio" id="repeat_num" value="3">
                            <span class="checkmark"></span>
                        </label>
                        <input type="text" name="" class='cute-input mr-2' style="width:40px;" value='1' id="howManyRepeatsNumber">
                        <div style="margin-right:5px;">
                        <?= lang('shows_desk_plan') ?>
                        </div>
                        <select name="" id="howManyRepeatUnitType" class='cute-input' style="display:none">
                            <option value="<?= lang('shows_desk_plan') ?>"><?= lang('shows_desk_plan') ?></option>
                        </select>
                    </div>
                </div>
            </div>
            <input type="hidden" id="datesArrayValues">
            <input type="hidden" id="datesIsSystematic">
            <input type="hidden" id="repetitionTableId">
            <input type="hidden" id="repetitionTableName">
            
        </div>
        <!-- modal body end -->
        <!-- modal footer start  -->
        <div class="generalPopupFooter d-flex justify-content-end">
            <button class="subCancel generalPopupButtonCancel closeFrequencyPopup" data-parent="mainPopup" data-target="createNewClassType">בטל</button>
            <button  id="repeatPopupButtonSave" class="subSave generalPopupButtonSave">שמור</button>
        </div>
        <!-- modal footer end -->
    </div>
    <!-- modal content end -->
</div>