<!-- modal start -->
<div class="popupWrapper" id="createNewClassType">
    <div class="popupContainer  smPopup scaleUp">

        <!-- modal header start -->
        <div class="generalPopupHeader">
            <h4 class="generalPopupTitle" >יצירת סוג שיעור חדש</h4>
            <button type="button"  class="close newCalendarPopupCloseTimes classPopupButtonClose " data-target="createNewClassType" data-parent="mainPopup" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <!-- modal header end -->
        <!-- modal body start -->
        <div class="generalPopupBody container">
            <div class="row">
                <div class="col-12 mb-2">
                    <p>יש לבחור צבע ברירת מחדל ליומן</p>
                    <div class="rowInput">
                        <div class="rowIconContainer">
                            <i class="far fa-calendar-alt"></i>
                        </div>
                        <div class="colorGridSelect">
                            <div class="colorCube selectedColor"></div>
                            <span class="downArrow">
                                <i class="fas fa-sort-down"></i>
                            </span>
                            <input type="hidden" id="selectedColor">
                            <div class="colorGridContainer">
                                <div class="colorGrid">
                                    <?php
                                    if ($colors) {
                                        foreach ($colors as $color) {
                                            echo '<div class="colorCube" style="background-color:' . $color->hex . '"></div>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-2">
                    <p>יש להגדיר משך זמן השיעור כברירת מחדל</p>
                    <div class="rowInput">
                        <div class="rowIconContainer">
                            <i class="far fa-clock"></i>
                        </div>
                        <input class="cute-input ml-2" style='width:50px;' type="number" value="60" max="60" name="newClassDurationNumber" id="newClassDurationNumber">
                        <select name="newClassDurationUnitType" id="newClassDurationUnitType" class='cute-input'>
                            <option value="דקות">דקות</option>
                            <option value="שעות">שעות</option>
                        </select>
                    </div>
                </div>
                <div class="col-12 mb-2">
                    <p>
                        שימו לב! חלק מהמנויים במערכת עלולים שלא לכלול אותם
                        שיעור חדש זה, הדבר לא יאפשר לאותם בעלי מנויים
                        להזמין שיעור זה.
                    </p>
                </div>
                <div class="col-12 mb-2">
                    <p>
                        סמן האם ברצונך לאפשר לבעלי המנויים הבאים להירשם
                        לשיעור חדש זה(*תחת אותם מגבלות המוגדרות במנוי).
                    </p>
                </div>
                <div class="col-12 cards-check">
                    <?php
                        if($company->membership_types){
                            if(count($company->membership_types)>1){
                                echo '
                                <div class="row mb-2">
                                <label for="selectAll" class="checkbox-container"><span style="text-decoration:underline;font-weight:bold" id="selectAllText">סמן הכל</span>
                                    <input class="membershipcheckbox" type="checkbox" name="selectAll" id="selectAll" value="selectAll">
                                    <span class="checkmark"></span>
                                </label>
                                </div>  
                                ';
                            }
                            foreach($company->membership_types as $membership){
                                echo '
                                <div class="row mb-2">
                                <label for="membership'.$membership->__get('id').'" class="checkbox-container">'.$membership->__get('Type').'
                                    <input class="membershipcheckbox" type="checkbox" name="" id="membership'.$membership->__get('id').'" value="'.$membership->__get('id').'">
                                    <span class="checkmark"></span>
                                </label>
                                </div>  
                                ';
                            }
                        }
                    ?>
                </div>
            </div>
        </div>
        <!-- modal body end -->
        <!-- modal footer start  -->
        <div class="generalPopupFooter d-flex justify-content-end">
            <button class="subCancel  generalPopupButtonCancel classPopupButtonClose" data-parent="mainPopup" data-target="createNewClassType">בטל</button>
            <button  id="classPopupButtonSave" class="subSave generalPopupButtonSave">שמור</button>
        </div>
        <!-- modal footer end -->
    </div>
    <!-- modal content end -->
</div>