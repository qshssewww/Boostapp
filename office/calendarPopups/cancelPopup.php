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
<div class="popupWrapper" id="cancelPopup">
    <div class="popupContainer smPopup scaleUp">
        <!-- modal header start -->
        <div class="generalPopupHeader " style="margin-bottom:1em!important; margin-top:0.8em!important">
            <h4 class="generalPopupTitle" >אפשרויות ביטול מאוחר</h4>
            <button type="button"  class="close newCalendarPopupCloseTimes closeCancelPopup" data-target="createNewClassType" data-parent="mainPopup" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <!-- modal header end -->
        <!-- modal body start -->
        <div class="generalPopupBody container">
            <div class="row d-flex p-0 mb-5" style="margin-bottom:1em!important;">
                <div class="col-12 p-0 mb-5  d-flex align-items-center" style="opacity:0.5;margin-left: 0.5em!important;">
                    <label class="switch" style="margin-left: 0.5em!important;">
                        <input type="checkbox" id="allowLateCancel"  checked disabled>
                        <span class="slider round"></span>
                    </label>
                    אפשר ביטול מאוחר
                </div>
                <div class="col-12 d-flex align-items-center">
                    <input type="text" name="" class='cute-input' style="width:40px;margin-left: 0.5em!important;" value='3' id="lateCancelNumber">
                    <select name="" id="lateCancelUnitType" class='cute-input' style="margin-left: 0.5em!important;">
                        <option value="1">ימים</option> 
                        <option value="2">דקות</option>
                        <option value="3">שעות</option>
                    </select>
                    לפני תחילת השיעור
                </div>
            </div>
            <div class="row d-flex p-0 mb-5" style="margin-bottom:1em!important;">
                <div class="col-12 p-0 mb-5 d-flex align-items-center" style="margin-left: 0.5em!important;">
                    <label class="switch" style="margin-left: 0.5em!important;">
                        <input type="checkbox" id="disableCancelButton">
                        <span class="slider round"></span>
                    </label>
                    חסימת כפתור הביטול בטווח הנ"ל
                </div>
                <div class="col-12 d-flex align-items-center DISPLAYNONEIMPORTANT" id="hideDisableCancelButton">
                    <input type="text" name="" class='cute-input' style="width:40px;margin-left: 0.5em!important;" value='3' id="disableCancelButtonNumber">
                    <select name="" id="disableCancelButtonUnitType" class='cute-input' style="margin-left: 0.5em!important;">
                        <option value="1">ימים</option>
                        <option value="2">דקות</option>
                        <option value="3">שעות</option>
                    </select>
                    לפני תחילת השיעור
                </div>
            </div>
            <div class="row p-0 mb-5">
                <p>*בגין ביטול מאוחר תמומש יתרת הלקוח</p>
                <p>*חסימת כפתור הביטול לא תאפשר למתאמנים לבטל כלל</p>
            </div>
            <input type="hidden" id="cancelDataInput">
            <!-- modal body end -->
            <!-- modal footer start  -->
            <div class="generalPopupFooter d-flex justify-content-end">
                <button class="subCancel  generalPopupButtonCancel closeCancelPopup" data-parent="mainPopup" data-target="createNewClassType">בטל</button>
                <button id="cancelPopupButtonSave" class="subSave generalPopupButtonSave">שמור</button>
            </div>
            <!-- modal footer end -->
        </div>
        <!-- modal content end -->
    </div>

</div>