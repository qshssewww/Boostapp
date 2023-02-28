<?php
require_once '../app/init.php';
$colors = DB::table('boostapp.colors')->where("calendar", "=", 1)->get();
$company = Company::getInstance();
?>
<!-- modal start -->
<div class="popupWrapper" id="createNewCalendar">
    <div class="popupContainer  smPopup scaleUp">

        <!-- modal header start -->
        <div class="generalPopupHeader">
            <h4 class="generalPopupTitle" >יצירת יומן חדש</h4>
            <button type="button" id="newCalendarPopupCloseTimes" class="close newCalendarPopupCloseTimes calendarPopupButtonClose" data-target="createNewClassType" data-parent="mainPopup" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <!-- modal header end -->
        <!-- modal body start -->
        <div class="generalPopupBody container">
            <div class="row">
                <div class="col-12 mb-2">
                    <p>הקמת יומן חדש במערכת ועליך לשייך אותו לסניף.</p>
                    <div class="row">
                        <?php
                        if ($company->brands) {
                            $selected=true;
                            foreach ($company->brands as $brand) {
                                if($selected){
                                    echo ' <div class="col-12 mb-3">
                                    <label class="radio-container">' . $brand->__get('BrandName') . '
                                        <input name="brand" checked type="radio" value="' . $brand->__get('id') . '">
                                        <span class="checkmark"></span>
                                    </label>
                                </div>';
                                }else{
                                    echo ' <div class="col-12 mb-3">
                                    <label class="radio-container">' . $brand->__get('BrandName') . '
                                        <input name="brand" type="radio" value="' . $brand->__get('id') . '">
                                        <span class="checkmark"></span>
                                    </label>
                                </div>';
                                }
                            $selected=false;
                            }
                        }
                        ?>
                    </div>
                </div>

            </div>
        </div>
        <!-- modal body end -->
        <!-- modal footer start  -->
        <div class="generalPopupFooter d-flex justify-content-end">
            <button class="subCancel  generalPopupButtonCancel calendarPopupButtonClose" data-parent="mainPopup" data-target="createNewClassType">בטל</button>
            <button id="calendarPopupButtonSave" class="subSave calendarPopupButtonSave">שמור</button>
        </div>
        <!-- modal footer end -->
    </div>
    <!-- modal content end -->
</div>