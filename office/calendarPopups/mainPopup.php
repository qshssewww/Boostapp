<?php
require_once '../app/init.php';
require_once './Classes/Company.php';
require_once './Classes/RepetitionSettings.php';
require_once './Classes/CancelationSettings.php';
$colors = DB::table('boostapp.colors')->where("calendar", "=", 1)->get();
$company = Company::getInstance();
$sections = DB::table('boostapp.sections')->where("CompanyNum", "=", $company->__get('CompanyNum'))->get();
$coachers = DB::table('boostapp.users')->where("CompanyNum", "=", $company->__get('CompanyNum'))->where("Coach", "=", 1)->get();
$users = DB::table('boostapp.client')->where("CompanyNum", "=", $company->__get('CompanyNum'))->get();
$levels = DB::table('boostapp.clientlevel')->where("CompanyNum", "=", $company->__get('CompanyNum'))->get();
$numbers = DB::table('boostapp.numbers')->where("CompanyNum", "=", $company->__get('CompanyNum'))->where("Status", "=", '0')->get();

$repetitionSettings = new RepetitionSettings();
$cancelationSettings = new CancelationSettings();

$repetition_settings1 = $repetitionSettings->getLastThreeRepititionByCompanyNumAndType($company->__get('CompanyNum'), "1");
$repetition_settings2 = $repetitionSettings->getLastThreeRepititionByCompanyNumAndType($company->__get('CompanyNum'), "2");

$cancelation_settings1 = $cancelationSettings->getLastThreeCancelationByCompanyNumAndType($company->__get('CompanyNum'), "1");
$cancelation_settings2 = $cancelationSettings->getLastThreeCancelationByCompanyNumAndType($company->__get('CompanyNum'), "2");
?>

<!-- modal start -->
<div class="popupWrapper justCalendarPopup" id="mainPopup">
    <div class="popupContainer mdPopup">
        <input type="hidden" value="" id="mainPopupIsEditId"/>
        <input type="hidden" value="" id="mainPopupIsEditGroup"/>
        <div>
            <!-- modal content start -->
            <div>
                <!-- modal header start -->
                <div class="generalPopupHeader">
                    <h4 class="generalPopupTitle">הקמת שיעור חדש</h4>
                    <button type="button" class="close newCalendarPopupCloseTimes toggleClosePopup" data-target="mainPopup" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <!-- modal header end -->
                <!-- modal body start -->
                <div class="typeButtons">
                    <div id="justCalendar" data-value="1" class="btn current-btn">מוצג ביומן</div>
                    <div id="calendarAndApp" data-value="2" class="btn">מוצג ביומן ובאפליקציה</div>
                    <input id="classFormType" type="hidden" value="1">
                </div>
                <div class="generalPopupBody">
                    <!-- select class type -->

                    <!-- select 2 section start -->
                    <div class="selectContainers">
                        <div class="selectContainersHalf">
                            <label for="calendarPopupClassSelect">שיעור</label>
                            <select name="ClassName" id="calendarPopupClassSelect" style="width: 100%">
                                <?php
                                if ($company->classTypes) {
                                    foreach ($company->classTypes as $class) {
                                        if($class->EventType == 0 ) {
                                            echo '<option data-color="' . $class->__get('Color') . '" value="' . $class->__get('id') . '">' . $class->__get('Type') . '</option>';
                                        }
                                    }
                                }
                                ?>
                            </select>
                            <div class="icon-container">
                                <span class="newLabel">חדש</span>
                                <i class="fas fa-bolt"></i>
                            </div>
                            <input type="hidden" id="isNewClass" value="0">
                        </div>
                        <div class="selectContainersHalf">
                            <label for="calendarPopupLocationSelect">מיקום</label>
                            <select name="location" id="calendarPopupLocationSelect" data-openpopup="<?php echo ($company->brands && count($company->brands) > 1) ? "1" : "0" ?>" style="width: 100%">
                                <?php
                                if ($sections) {
                                    foreach ($sections as $section) {
                                        echo '<option value="' . $section->id . '">' . $section->Title . '</option>';
                                    }
                                }
                                ?>
                            </select>
                            <div class="icon-container">
                                <span class="newLabel">חדש</span>
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <input type="hidden" id="isNewLocation" value="0">
                        </div>
                        <div class="selectContainersHalf">
                            <label for="calendarPopupDateSelect">תאריך</label>
                            <input type="text" id="calendarPopupDateSelect" style="width: 100%">
                            <div class="icon-container">
                                <i class="far fa-calendar"></i>
                            </div>
                        </div>
                        <div class="selectContainersHalf">
                            <label for="calendarPopupTimeSelect">שעה</label>
                            <div class="calendarPopupsTimeInPutsContainer"  id="timePair">
                                <input type="text" id="calendarPopupTimeSelectFrom" class="time start">
                                <div class="smallTimeDiv"> - </div>
                                <input type="text" id="calendarPopupTimeSelectTo" class="time end">
                                <div id="dateTextOutput">
                                </div>
                                <input type="hidden" id="calendarPopupTimeDurtaion">
                            </div>
                            <div class="icon-container">
                                <i class="far fa-clock"></i>
                            </div>
                        </div>
                        <div class="selectContainersHalf calendarAndApp">
                            <label for="calendarPopupDateSelect">מקסימום נרשמים לשיעור</label>
                            <input type="number" id="maxNumberOfAtendees" style="width: 100%" value="1">
                            <div class="icon-container">
                                <i class="far fa-user-circle"></i>
                            </div>
                        </div>
                        <div class="selectContainersHalf calendarAndApp selectContainerFlex">
                            <div class="rowIconContainer">
                                <i class="fas fa-list"></i>
                            </div>
                            <select class="cute-input" id="allowAsStandBy">
                                <option value="1">אפשר להירשם כממתין לשיעור</option>
                                <option value="2">לא ניתן להירשם כממתין לשיעור</option>
                                <option value="3">כמות מוגבלת של ממתינים לשיעור</option>
                            </select>
                            <input type="number" style="width:50px; display:none; " name="" id="limitStandbyList" value="1" class="cute-input mr-2">
                        </div>
                    </div>
                    <!-- everything else -->
                    <div class="extraRows" duplicate-wrapper="1">
                        <div class="rowInputDouble">
                            <div class="rowInput shallBeDuplicated">
                                <div class="rowIconContainer">
                                    <i class="far fa-user"></i>
                                </div>
                                <select class="cute-input" id="selectedTrainer1">
                                    <?php
                                    if ($coachers) {
                                        foreach ($coachers as $coacher) {
                                            echo '<option value="' . $coacher->id . '">' . $coacher->display_name . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
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
                        <div class="rowInput" id="addTrainer" style="width:fit-content">
                            <div class="rowIconContainer">
                                <i class="far fa-user"></i>
                            </div>
                            <div class="plus">
                                +
                                מדריך נוסף
                            </div>
                        </div>
                        <div class="rowInput justCalendar HIDEIFEDIT">
                            <div class="rowIconContainer">
                                <i class="fas fa-sync"></i>
                            </div>
                            <select id="repetitionInput1" class="cute-input">
                                <option value="once">שיעור חד-פעמי</option>
                                <?php
                                if ($repetition_settings1) {
                                    foreach ($repetition_settings1 as $repetition) {
                                        echo "<option data-content='" . json_encode($repetition, JSON_UNESCAPED_UNICODE) . "' value='" . $repetition->id . "'>" . $repetition->name . "</option>";
                                    }
                                }
                                ?>
                                <option value="advanced">אפשרויות מתקדמות...</option>
                            </select>
                        </div>
                        <div class="rowInput calendarAndApp HIDEIFEDIT">
                            <div class="rowIconContainer">
                                <i class="fas fa-sync"></i>
                            </div>
                            <select id="repetitionInput2" class="cute-input">
                                <option value="once">שיעור חד-פעמי</option>
                                <?php
                                if ($repetition_settings2) {
                                    foreach ($repetition_settings2 as $repetition) {
                                        echo "<option data-content='" . json_encode($repetition, JSON_UNESCAPED_UNICODE) . "' value='" . $repetition->id . "'>" . $repetition->name . "</option>";
                                    }
                                }
                                ?>
                                <option value="advanced">אפשרויות מתקדמות...</option>
                            </select>
                        </div>
                        <div class="rowInput justCalendar">
                            <div class="rowIconContainer">
                                <i class="far fa-window-close"></i>
                            </div>
                            <select id="cancelationInput1" class="cute-input">
                                <option value="no">לא ניתן לבטל מהאפליקציה</option>
                                <option value="free">ביטול חופשי וללא עלות</option>
                                <?php
                                if ($cancelation_settings1) {
                                    foreach ($cancelation_settings1 as $cancelation) {
                                        echo "<option data-content='" . json_encode($cancelation, JSON_UNESCAPED_UNICODE) . "' value='" . $cancelation->id . "'>" . $cancelation->name . "</option>";
                                    }
                                }
                                ?>
                                <option value="advanced">אפשרויות מתקדמות...</option>
                            </select>
                        </div>
                        <div class="rowInput calendarAndApp">
                            <div class="rowIconContainer">
                                <i class="far fa-window-close"></i>
                            </div>
                            <select id="cancelationInput2" class="cute-input">
                                <option value="no">לא ניתן לבטל מהאפליקציה</option>
                                <option value="free">ביטול חופשי וללא עלות</option>
                                <?php
                                if ($cancelation_settings2) {
                                    foreach ($cancelation_settings2 as $cancelation) {
                                        echo "<option data-content='" . json_encode($cancelation, JSON_UNESCAPED_UNICODE) . "' value='" . $cancelation->id . "'>" . $cancelation->name . "</option>";
                                    }
                                }
                                ?>
                                <option value="advanced">אפשרויות מתקדמות...</option>
                            </select>
                        </div>
                        <div class="rowInput" id="openReminder" style="width:fit-content">
                            <div class="rowIconContainer">
                                <i class="far fa-bell"></i>
                            </div>
                            <div class="plus">
                                +
                                תזכורת
                            </div>
                            <div class="d-flex align-items-center hiddenReminder hidden">
                                <input class="cute-input ml-2" style='width:50px;' type="number" value="60" name="newClassDurationNumber" id="newClassDurationNumber">
                                <select name="newClassDurationUnitType" id="newClassDurationUnitType" class='cute-input ml-2'>
                                    <option value="1">דקות</option>
                                    <option value="2">שעות</option>
                                    <option value="3">ימים</option>
                                </select>
                                לפני תחילת השיעור
                                <!-- <button id="closeReminder" class="stop mr-2"></button> -->
                                <div class="text-danger mis-9" id="closeReminder"><i class="fas fa-do-not-enter"></i></div>
                            </div>
                        </div>
                        <div class="rowInput" id="openSimpleVid" style="width:fit-content">
                            <div class="rowIconContainer">
                                <i class="fas fa-video"></i>
                            </div>
                            <!-- <div class="plus">
                            +
                            אפשרויות שידור
                            </div> -->
                            <select id="prodcastOptions" class="plus">
                                <option value="">+
                                    אפשרויות שידור</option>
                                <option value="openVideoLink">לינק לשידור אונליין</option>
                                <option value="openZoom">זום</option>
                            </select>
                            <div class="d-flex align-items-center hidden hiddenSimpleVid">
                                <div style="display:flex;flex-direction:column;">
                                    <input id="prodcastLink" class="cute-input" placeholder="https://www.youtube.com" style='width:350px; margin-left:5px;margin-bottom:5px;' type="text">
                                    <div class="d-flex align-items-center">
                                        <div style="margin-left:5px;">שלח</div>
                                        <input id="broadCastNum" class="cute-input" style='width:50px;margin-left:5px;' type="number" value="60">
                                        <select class='cute-input' style="margin-left:5px;" id="broadCastType">
                                            <option value="1">דקות</option>
                                            <option value="2">שעות</option>
                                        </select>
                                        <div style="margin-left:5px;">לפני השיעור</div>
                                        <select class='cute-input' style="margin-left:5px;" id="broadCastReminderType">
                                            <option value="3">מייל + SMS</option>
                                            <option value="1">SMS</option>
                                            <option value="2">מייל</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- <button id="closeSimpleVid" class="stop mr-2"></button> -->
                                <div class="text-danger mis-9" id="closeSimpleVid"><i class="fas fa-do-not-enter"></i></div>
                            </div>

                            <div class="d-flex align-items-center hidden hiddenZoomVid">
                                <div style="display:flex;flex-direction:column;">
                                    <input id="zoomMeetingId" class="cute-input ml-2" style="margin-bottom:5px;" placeholder="Meeting Id" type="text">
                                    <input id="zoomMeetingPassword" class="cute-input ml-2" placeholder="Meeting Password" type="text">
                                </div>
                                <!-- <button id="closeZoomVid" class="stop mr-2"></button> -->
                                <div class="text-danger mis-9" id="closeZoomVid"><i class="fas fa-do-not-enter"></i></div>
                            </div>

                        </div>
                        <div class="rowInput rowInputAlignStart " id="openTextarea" >
                            <div class="rowIconContainer">
                                <i class="far fa-comment-alt"></i>
                            </div>
                            <div class="plus">
                                +
                                תוכן לשיעור
                            </div>
                            <div style="width:100%;" class="hidden hiddenTextarea d-flex align-items-center">
                                <textarea id="classContent"></textarea>
                                <select class="cute-input" id="contentShow">
                                    <option value="0">לא מוצג ללקוח</option>
                                    <option value="1">מוצג ללקוח</option>
                                    <select>
                                        <!-- <div class="stop mr-2" id="closeTextarea"></div> -->
                                        <div class="text-danger mis-9" id="closeTextarea"><i class="fas fa-do-not-enter"></i></div>
                                        </div>

                                        </div>

                                        <div id="placeClient" class="rowInput justCalendar" style="width:fit-content">
                                            <div class="rowIconContainer">
                                                <i class="far fa-user-circle"></i>
                                            </div>
                                            <div class="plus">
                                                +
                                                שבץ מתאמן
                                            </div>
                                            <select id="hiddenUsersName" style="display:none">
                                                <?php
                                                if ($users) {
                                                    foreach ($users as $user) {
                                                        echo "<option value='" . $user->id . "'>" . $user->CompanyName . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                            <select id="hiddenUsersPhone" style="display:none">
                                                <?php
                                                if ($users) {
                                                    foreach ($users as $user) {
                                                        echo "<option value='" . $user->id . "'>" . $user->ContactMobile . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div id="openMinimum" style="width:fit-content" class="rowInput calendarAndApp showMoreChangeable hideShowMoreChangeable">
                                            <div class="rowIconContainer">
                                                <i class="fas fa-shield-alt"></i>
                                            </div>
                                            <div class="plus">
                                                +
                                                הגדרות מינימום משתתפים
                                            </div>

                                            <div class="hidden hiddenMinimum d-flex align-items-center">
                                                <div class="ml-2">מינימום</div>
                                                <input class="cute-input ml-2" style='width:50px;' type="number" value="60" id="minimumAtendeesAmount">
                                                <div class="ml-2">משתתפים, בדוק </div>
                                                <input class="cute-input ml-2" style='width:50px;' type="number" value="3" id="minimumAtendeesCheckAmount">
                                                <select name="newClassDurationUnitType" id="minimumAtendeesCheckType" class='cute-input ml-2'>
                                                    <option value="ימים">ימים</option>
                                                    <option value="שעות">שעות</option>
                                                    <option value="דקות">דקות</option>
                                                </select>
                                                <div class="ml-2">
                                                    לפני תחילת השיעור
                                                </div>
                                                <!-- <div class="stop mr-2" id="closeMinimum"></div> -->
                                                <div class="text-danger mis-9" id="closeMinimum"><i class="fas fa-do-not-enter"></i></div>
                                            </div>
                                        </div>
                                        <div style="width:fit-content" id="openPurchaseOptions" class="rowInput calendarAndApp showMoreChangeable hideShowMoreChangeable">
                                            <div class="rowIconContainer">
                                                <i class="fas fa-male"></i>
                                            </div>
                                            <div class="plus">
                                                +
                                                אפשרות לרכוש ולהרשם לשיעור
                                            </div>
                                            <div class="hidden hiddenPurchaseOptions d-flex align-items-center">
                                                <div class="ml-2">
                                                    ניתן לרכוש שיעור זה בעלות של
                                                </div>
                                                <input class="cute-input ml-2" style='width:50px;' type="number" value="35" id="purchaseAmount">
                                                <div class="ml-2">
                                                    ₪
                                                </div>
                                                <select id="purchaseLocation" class='cute-input ml-2'>
                                                    <option value="app">מאפליקציית המתאמנים</option>
                                                    <option value="link">מקישור חיצוני</option>
                                                    <option value="everywhere">מכל מקום</option>
                                                </select>
                                                <!-- <div class="stop" id="closePurchaseOptions"></div> -->
                                                <div class="text-danger mis-9" id="closePurchaseOptions"><i class="fas fa-do-not-enter"></i></div>
                                            </div>
                                        </div>
                                        <div id="placeLimitation" class="rowInput calendarAndApp showMoreChangeable hideShowMoreChangeable">
                                            <select id="hiddenMembershipInput" style="display:none">
                                                <?php
                                                if ($company->membership_types) {
                                                    foreach ($company->membership_types as $membership) {
                                                        echo "<option value='" . $membership->id . "'>" . $membership->Type . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                            <select id="hiddenLevelsInput" style="display:none">
                                                <?php
                                                if ($levels) {
                                                    foreach ($levels as $level) {
                                                        echo "<option value='" . $level->id . "'>" . $level->Level . "</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                            <div class="rowIconContainer">
                                                <i class="far fa-hand-paper"></i>
                                            </div>
                                            <div class="plus">
                                                +
                                                מגבלות ההרשמה
                                            </div>
                                        </div>
                                        <div style="width:fit-content" id="openDevices" class="rowInput calendarAndApp showMoreChangeable hideShowMoreChangeable">
                                            <div class="rowIconContainer">
                                                <i class="fas fa-bicycle"></i>
                                            </div>
                                            <div class="plus">
                                                +
                                                הגדרות מכשירים לבחירה
                                            </div>
                                            <div class="hidden hiddenDevices d-flex align-items-center">
                                                <select  id="devicesInput" class='cute-input ml-2' style="width:250px;">
                                                    <?php
                                                    if ($numbers) {
                                                        foreach ($numbers as $number) {
                                                            echo "<option value='" . $number->id . "'>" . $number->Name . "</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                <!-- <div class="stop" id="closeDevices"></div> -->
                                                <div class="text-danger mis-9" id="closeDevices"><i class="fas fa-do-not-enter"></i></div>
                                            </div>
                                        </div>
                                        <div id="placeSignTiming" class="rowInput calendarAndApp showMoreChangeable hideShowMoreChangeable">
                                            <div class="rowIconContainer">
                                                <i class="far fa-eye"></i>
                                            </div>
                                            <div class="plus">
                                                +
                                                תזמון אפשרות להרשמה באתר
                                            </div>
                                        </div>
                                        <div id="imagePlus" class="rowInput calendarAndApp showMoreChangeable hideShowMoreChangeable">
                                            <div class="edit-avatar classImg" data-ip-modal="#itemModal" title="ערוך תמונה" style="display: flex">
                                                <div class="rowIconContainer">
                                                    <i class="far fa-image"></i>
                                                </div>
                                                <div class="plus ImgEmpty">
                                                    +
                                                    תמונה
                                                </div>
                                                <div class="hidden hiddenImg d-flex align-items-center">
                                                    <div class="ImgName">
                                                    </div>
                                                    <!-- <div class="stop" id="removeImg"></div> -->
                                                    <div class="text-danger mis-9" id="removeImg"><i class="fas fa-do-not-enter"></i></div>
                                                </div>
                                            </div>
                                            <input type="hidden" id="pageImgPath" name="pageImgPath" value=""/>
                                        </div>
                                        <div class="rowInput calendarAndApp showMoreChangeable hideShowMoreChangeable">
                                            <label class="switch">
                                                <input type="checkbox" id="freeRegister">
                                                <span class="slider round"></span>
                                            </label>
                                            <div class="plus">
                                                רישום חופשי ללא עלות
                                            </div>
                                        </div>
                                        <div id="showMore" class="showMore calendarAndApp" data-toggle='0'>
                                            הצג אפשרויות נוספות
                                        </div>
                                        <div id="extraCheckboxes" class="calendarAndApp">
                                            <span class="smallTextCheckbox">הגדרות תצוגה למתאמנים שנרשמו</span>
                                            <div class="extraCheckboxesContainerFlex">
                                                <div class="row mb-2">
                                                    <label for="checkbox1" class='checkbox-container'>הצג כמות משתתפים
                                                        <input type="checkbox" name="" id="checkbox1">
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </div>
                                                <div class="row mb-2">
                                                    <label for="checkbox2" class='checkbox-container'>הצג שם ותמונה של המשתתפים
                                                        <input type="checkbox" name="" id="checkbox2">
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </div>
                                                <div class="row mb-2">
                                                    <label for="checkbox3" class='checkbox-container'>הצג מיקום ברשימת המתנה
                                                        <input type="checkbox" name="" id="checkbox3">
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        </div>
                                        </div>
                                        <!-- modal body end -->
                                        <!-- modal footer start  -->
                                        <div class="generalPopupFooter">
                                            <button id="generalPopupButtonCancel" type="button" class="btn calendarPopupButton generalPopupButtonCancel toggleClosePopup" data-target="mainPopup">בטל</button>
                                            <button id="mainPopupButtonSave" type="button" class="btn calendarPopupButton generalPopupButtonSave">שמור</button>
                                        </div>
                                        <!-- modal footer end -->
                                        </div>
                                        <!-- modal content end -->
                                        </div>
                                        </div>
                                        <!-- modal end -->

                                        </div>

