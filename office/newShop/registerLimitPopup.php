<div class="popupWrapper" id="registerLimitPopup">
    <div class="popupContainer smPopup scaleUp bsapp-max-w-700p">
        <!-- modal header start -->
        <div class="generalPopupHeader mt-3 mb-5">
            <h5 class="generalPopupTitle"> <i class="far fa-hand-paper"></i> <?php echo lang('add_register_restrict') ?></h5>
            <a  href="javascript:;" class="newCalendarPopupCloseTimes closeRegisterLimitPopup text-dark" data-target="registerLimitPopup">
                <i class="fal fa-times"></i>
            </a>
        </div>
        <!-- modal header end -->
        <!-- modal body start -->
        <div class="generalPopupBody container bsapp-card-scroll overflow-auto">
            <input type="hidden" id="registerLimitLineId" value="">
            <div class="selectContainers">
                <label for="calendarPopupClassSelect"><?php echo lang('select_class') ?></label>
                <select multiple="multiple" id="registerPopupClassSelect" style="width: 100%"
                        class="registerLimitClassSelect">
                    <option value="all"><?php echo lang('all_classes') ?></option>
                    <?php
                    if ($company->classTypes) {
                        foreach ($company->classTypes as $class) {
                            if($class->EventType == 0 ) {
                                echo '<option value="' . $class->__get('id') . '">' . $class->__get('Type') . '</option>';
                            }
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="extraRows">
                <div class="rowInput classSelectDependent maxLimitContainer" style="display:none">
                    <div class="hiddenHeader" id="hiddenMaxLimitHeader" style="display:none;order:1;"><?php echo lang('max_restriction') ?></div>
                    <div id="openMaxLimit" style="order:3">
                        <div class="plus">+ <?php echo lang('max_restriction') ?></div>
                    </div>         
                </div>
                <div class="rowInput classSelectDependent" id="openDaysLimit" style="display:none">
                    <div class="plus">+ <?php echo lang('days_restriction') ?></div>
                    <div class="hiddenDaysLimit hidden">
                        <div class="hiddenHeader"><?php echo lang('days_restriction') ?></div>
                        <div class="d-flex align-items-center">
                            <div class="days">
                                <ul>
                                    <li class="limitDayLi" value="0" data-selected="0"><?php echo lang('sunday_short') ?></li>
                                    <li class="limitDayLi" value="1" data-selected="0"><?php echo lang('monday_short') ?></li>
                                    <li class="limitDayLi" value="2" data-selected="0"><?php echo lang('tuesday_short') ?></li>
                                    <li class="limitDayLi" value="3" data-selected="0"><?php echo lang('wednesday_short') ?></li>
                                    <li class="limitDayLi" value="4" data-selected="0"><?php echo lang('thursday_short') ?></li>
                                    <li class="limitDayLi" value="5" data-selected="0"><?php echo lang('friday_short') ?></li>
                                    <li class="limitDayLi" value="6" data-selected="0"><?php echo lang('saturday_short') ?></li>
                                </ul>
                            </div>
                            <!-- <button id="closeDaysLimit" class="stop mis-2"></button> -->
                            <div class="text-danger mis-9" id="closeDaysLimit"><i class="fas fa-do-not-enter"></i></div>
                        </div>
                    </div>
                </div>
                <div class="rowInput classSelectDependent mb-14" id="openHoursLimit" style="display:none">
                    <div class="plus">+ <?php echo lang('hours_restriction') ?></div>
                    <div class="hiddenHoursLimit hidden">
                        <div class="hiddenHeader"><?php echo lang('hours_restriction') ?></div>
                        <div style="hourLimitFlex">
                            <div class="d-flex align-items-center">
                                <div class="mie-5" style="font-size: 0.8em;"><?php echo lang('start_hour') ?></div>
                                <div class="d-flex flex-row align-items-center mie-17">
                                    <input class="cute-input form-control mie-5 bg-light border-light w-80p" id="limitFromHour">
                                    <i class="far fa-clock"></i>
                                </div>
                                <div class="mie-5" style="font-size: 0.8em;"><?php echo lang('end_hour') ?></div>
                                <div class="d-flex flex-row align-items-center">
                                    <input class="cute-input form-control mie-5 bg-light border-light w-80p" id="limitToHour">
                                    <i class="far fa-clock"></i>
                                </div>  
                                <!-- <button id="closeHoursLimit" class="stop mis-2"></button> -->
                                <div class="text-danger mis-9" id="closeHoursLimit"><i class="fas fa-do-not-enter"></i></div>
                            </div>
                        </div>
                        <div id="addAnotherHourLimit" class="addAnotherButton">+ <?php echo lang('hours_restriction') ?></div>
                        <div class="extraHoursLimitContainer" style="display:none">
                            <div class="d-flex align-items-center" id="extraHourLimits">
                                <div class="mie-5" style="font-size: 0.8em;"><?php echo lang('start_hour') ?></div>
                                <div class="d-flex flex-row align-items-center mie-17">
                                    <input class="cute-input form-control mie-5 bg-light border-light w-80p" id="limitFromHour2">
                                    <i class="far fa-clock"></i>
                                </div>
                                <div class="mie-5" style="font-size: 0.8em;"><?php echo lang('end_hour') ?></div>
                                <div class="d-flex flex-row align-items-center">
                                    <input class="cute-input form-control mie-5 bg-light border-light w-80p" id="limitToHour2">
                                    <i class="far fa-clock"></i>
                                </div>
                                <!-- <button id="closeExtraHoursLimit" class="stop mis-2"></button> -->
                                <div class="text-danger mis-9" id="closeExtraHoursLimit"><i class="fas fa-do-not-enter"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="rowInput classSelectDependent"  style="display:none">
                <select id="registerLimitPopupOpenSelect" class="cute-input p-8">
                    <option value="0">+ <?php echo lang('add_register_restrict') ?></option>
                    <option value="1"><?php echo lang('max_restriction') ?></option>
                    <option value="2"><?php echo lang('days_restriction') ?></option>
                    <option value="3"><?php echo lang('hours_restriction') ?></option>
                    <!--                    <option value="4">מגבלת הרשמה על בסיס מקום פנוי</option>-->
                </select>   
            </div>
            <div class="rowInput oneMaxDependent mt-20" id="openRegisterLimits" style="display:none">
                <div class="plus">+ <?php echo lang('reg_availability_restrictions') ?></div>
                <div class="hiddenRegisterLimits hidden">
                    <div class="hiddenHeader"><?php echo lang('registration_availability') ?></div>
                    <div class="d-flex align-items-center">
                        <div class="maxColumnFlex">
                            <div class="d-flex align-items-center pb-7">
                                <input class="form-control  bg-light border-light mie-7 w-50p" id="registerLimitNumber" type="number">
                                <div class="mie-7" style="font-size: 1em;"><?php echo lang('class_registration') ?></div>
                                <select id="registerLimitType" class="cute-input p-6">
                                    <option value="1"><?php echo lang('a_day') ?></option>
                                    <option value="2"><?php echo lang('a_week') ?></option>
                                    <option value="3"><?php echo lang('a_month') ?></option>
                                    <option value="4"><?php echo lang('a_year') ?></option>
                                </select>
                            </div>
                            <div class="d-flex align-items-center" >
                                <div class="mie-7" style="font-size: 1em;"><?php echo lang('timing') ?></div>
                                <input class="form-control  bg-light border-light mie-7 w-50p" id="registerTimingInput" type="number">
                                <select id="registerLimitTimingType" class="form-control  bg-light border-light mie-7 p-6">
                                    <option value="1"><?php echo lang('minutes') ?></option>
                                    <option value="2"><?php echo lang('hours') ?></option>
                                    <option value="3"><?php echo lang('days') ?></option>
                                </select>
                                <div class="mie-7" style="font-size: 1em;"><?php echo lang('before_class') ?></div>
                            </div>
                        </div>
                        <!-- <button id="closeRegisterLimits" class="stop mis-2"></button> -->
                        <div class="text-danger mis-9" id="closeRegisterLimits"><i class="fas fa-do-not-enter"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="generalPopupFooter d-flex justify-content-end">
            <button class="subCancel generalPopupButtonCancel closeRegisterLimitPopup btn btn-light mt-10" data-parent="mainPopup"
                    data-target="registerLimitPopup"><?php echo lang('action_cacnel') ?></button>
            <button id="registerLimitPopupButtonSave" class="subSave generalPopupButtonSave blueImportant  btn btn-light mt-10"><?php echo lang('save') ?></button>
        </div>
    </div>
    <!-- modal content end -->
</div>
</div>
