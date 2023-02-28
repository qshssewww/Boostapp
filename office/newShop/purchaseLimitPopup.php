<div class="popupWrapper" id="purchaseLimitPopup">
    <div class="popupContainer smPopup scaleUp bsapp-max-w-700p">
        <!-- modal header start -->
        <div class="generalPopupHeader mt-3 mb-2 mx-0">
            <h5 class="generalPopupTitle" ><i class="fas fa-funnel-dollar"></i> <?php echo lang('app_buy_filter') ?></h5>
            <a href="javascript:;"  class="newCalendarPopupCloseTimes closePurchaseLimitPopup text-dark" data-target="purchaseLimitPopup" style="font-size:1.5rem;">
                <i class="fal fa-times"></i>
            </a>
        </div>
        <!-- modal header end -->
        <!-- modal body start -->
        <div class="generalPopupBody container bsapp-card-scroll overflow-auto">
            <div class="smallExplanaitionText">
                <?php echo lang('buy_notice_one') ?>
                <br>
                <?php echo lang('buy_notice_two') ?>
            </div>
            <input type="hidden" id="purchaseLimitPopupSrc" value="">
            <div class="extraRows">

                <div class="rowInput">
                    <div id="openAgeLimitP" >
                        <div class="plus">+ <?php echo lang('age_restriction') ?></div>
                    </div>
                    <div class="hiddenAgeLimitP hidden">
                        <div class="hiddenHeader"><?php echo lang('age_restriction') ?></div>
                        <div class="d-flex align-items-center">
                            <div class="mie-7" style="font-size: 1em;"><?php echo lang('from_age') ?></div>
                            <input class="form-control  bg-light border-light mie-7" id="fromAgeP" type="number" style='width:50px;'>
                            <div class="mie-7" style="font-size: 1em;"><?php echo lang('to_age') ?></div>
                            <input class="form-control  bg-light border-light mie-7" id="toAgeP" type="number" style='width:50px;'>
                            <!-- <button id="closeAgeLimitP" class="stop mie-2"></button> -->
                            <div class="text-danger mis-9" id="closeAgeLimitP"><i class="fas fa-do-not-enter"></i></div>
                        </div>
                    </div>
                </div>

                <div class="rowInput">
                    <div id="openGenderLimitP" >
                        <div class="plus">+ <?php echo lang('gender_restriction') ?></div>
                    </div>
                    <div class="hiddenGenderLimitP hidden">
                        <div class="hiddenHeader"><?php echo lang('gender_restriction') ?></div>
                        <div class="d-flex align-items-center">

                            <!-- <label class="CheckboxLabel">
                                <input type="checkbox" class="shopCheckbox" id="maleP" name="taxInclude"  />
                                <span class="shopCheckmark"></span>
                            </label>
                            <label class="taxIncludeLabel" style="margin-left: 10px!important;margin-right: 0px!important;" for="maleP"><?php echo lang('male') ?></label> -->
                            <div class="custom-control custom-checkbox mie-19">
                                <input type="radio" class="custom-control-input" id="maleP" name="gender" value="1">
                                <label class="custom-control-label" for="maleP"><?php echo lang('male') ?></label>
                            </div>
                            <!-- <label class="CheckboxLabel">
                                <input type="checkbox" class="shopCheckbox" id="femaleP" name="taxInclude"  />
                                <span class="shopCheckmark"></span>
                            </label>
                            <label class="taxIncludeLabel" style="margin-left: 10px!important;margin-right: 0px!important;" for="femaleP"><?php //echo lang('female') ?></label> -->

                            <div class="custom-control custom-checkbox mie-19">
                                <input type="radio" class="custom-control-input" id="femaleP" name="gender" value="2">
                                <label class="custom-control-label" for="femaleP"><?php echo lang('female') ?></label>
                            </div>

                            <!-- <label class="CheckboxLabel">
                                <input type="checkbox" class="shopCheckbox" id="otherP" name="taxInclude"  />
                                <span class="shopCheckmark"></span>
                            </label>
                            <label class="taxIncludeLabel" style="margin-left: 10px!important;margin-right: 0px!important;" for="otherP"><?php echo lang('other') ?></label> -->
                            
                            <div class="custom-control custom-checkbox mie-19">
                                <input type="radio" class="custom-control-input" id="otherP" name="gender" value="0">
                                <label class="custom-control-label" for="otherP"><?php echo lang('other') ?></label>
                            </div>
                            <!-- <button id="closeGenderLimitP" class="stop mie-2"></button> -->
                            <div class="text-danger mis-9" id="closeGenderLimitP"><i class="fas fa-do-not-enter"></i></div>
                        </div>
                    </div>
                </div>



                <div class="rowInput">
                    <div id="openSeniorityLimitP" >
                        <div class="plus">+ <?php echo lang('seniority_restriction') ?></div>
                    </div>
                    <div class="hiddenSeniorityLimitP hidden">
                        <div class="hiddenHeader"><?php echo lang('seniority_restriction') ?></div>
                        <div class="d-flex align-items-center">
                            <div class="mie-7" style="font-size: 1em;"><?php echo lang('joining_date_from') ?>:</div>
                            <input class="form-control  bg-light border-light mie-7" id="seniorityAge" style='width:110px;'>
                            <!-- <button id="closeSeniorityLimitP" class="stop mie-2"></button> -->
                            <div class="text-danger mis-9" id="closeSeniorityLimitP"><i class="fas fa-do-not-enter"></i></div>
                        </div>
                    </div>
                </div>




                <div class="rowInput">
                    <div id="openPurchaseAmountLimitP" >
                        <div class="plus">+ <?php echo lang('quantity_limit') ?></div>
                    </div>
                    <div class="hiddenPurchaseAmountLimitP hidden">
                        <div class="hiddenHeader"><?php echo lang('quantity_limit') ?></div>
                        <div class="d-flex align-items-center">
                            <div class="mie-3" style="font-size: 1em;"><?php echo lang('limit_to') ?></div>
                            <input class="form-control  bg-light border-light mie-7" step="1"  max="24" type="number" id="puchaseAmountP" style='width:50px;'
                                   onKeyUp="if(this.value>20){this.value='20';}else if(this.value<0){this.value='0';}">
                            <div class="mie-7" style="font-size: 1em;"><?php echo lang('purchase_for_customer') ?></div> 
                            <!-- <button id="closePurchaseAmountLimitP" class="stop mie-2"></button> -->
                            <div class="text-danger mis-9" id="closePurchaseAmountLimitP"><i class="fas fa-do-not-enter"></i></div>
                        </div>
                    </div>
                </div>
                <div class="rowInput">
                    <div id="openStatusLimitP" >
                        <div class="plus">+ <?php echo lang('client_status_restriction') ?></div>
                    </div>
                    <div class="hiddenStatusLimitP hidden">
                        <div class="hiddenHeader"><?php echo lang('client_status_restriction') ?></div>
                        <div class="d-flex align-items-center">
                            <label class="radio-container mie-3"><?php echo lang('actives') ?>
                                <input name="status" type="radio" value="1" checked>
                                <span class="checkmarkBlue"></span>
                            </label>
                            <label class="radio-container"><?php echo lang('interested') ?>
                                <input name="status" type="radio" value="2">
                                <span class="checkmarkBlue"></span>
                            </label>
                            <!-- <button id="closeStatusLimitP" class="stop mie-2"></button> -->
                            <div class="text-danger mis-9" id="closeStatusLimitP"><i class="fas fa-do-not-enter"></i></div>
                        </div>
                    </div>
                </div>
                <div class="rowInput">
                    <div id="openRankLimitP" >
                        <div class="plus">+ <?php echo lang('tag_restrictions') ?></div>
                    </div>
                    <div class="hiddenRankLimitP hidden" style="width:100%">
                        <div class="hiddenHeader"><?php echo lang('tag_restrictions') ?></div>
                        <div class="d-flex align-items-center" style="width:100%">
                            <select id="rankMultiSelectP" multiple="multiple" style="width:90%"> 
                                <?php
                                if ($levels) {
                                    foreach ($levels as $level) {
                                        echo "<option value='" . $level->id . "'>" . $level->Level . "</option>";
                                    }
                                }
                                ?>
                            </select>
                            <!-- <button id="closeRankLimitP" class="stop mie-2"></button> -->
                            <div class="text-danger mis-9" id="closeRankLimitP"><i class="fas fa-do-not-enter"></i></div>
                        </div>
                    </div>
                </div>
                <div class="rowInput">
                    <div id="openMembershipLimitP" >
                        <div class="plus">+ <?php echo lang('subscription_type_restrictions') ?></div>
                    </div>
                    <div class="hiddenMembershipLimitP hidden" style="width:100%">
                        <div class="hiddenHeader"><?php echo lang('subscription_type_restrictions') ?></div>
                        <div class="d-flex align-items-center" style="width:100%">
                            <select id="membershipMultiSelectP" multiple="multiple" style="width:90%"> 
                                <?php
                                if ($company->membership_types) {
                                    foreach ($company->membership_types as $membership) {
                                        echo "<option value='" . $membership->id . "'>" . $membership->Type . "</option>";
                                    }
                                }
                                ?>
                            </select>
                            <!-- <button id="closeMembershipLimitP" class="stop mie-2"></button> -->
                            <div class="text-danger mis-9" id="closeMembershipLimitP"><i class="fas fa-do-not-enter"></i></div>
                        </div>
                    </div>
                </div>




                <div class="rowInput">
                    <select class="form-control  bg-light border-light p-8" id="openHiddenRowInputsSelect">
                        <option value="0">+ <?php echo lang('app_purchase_terms') ?></option>
                        <option value="1"><?php echo lang('age_restriction') ?></option>
                        <option value="2"><?php echo lang('gender_restriction') ?></option>
                        <option value="3"><?php echo lang('seniority_restriction') ?></option>
                        <option value="4"><?php echo lang('quantity_limit') ?></option>
                        <option value="5"><?php echo lang('client_status_restriction') ?></option>
                        <option value="6"><?php echo lang('tag_restrictions') ?></option>
                        <option value="7" class="hidden-option" <?= $companyProductSettings->manageMemberships == 0 ? 'hidden' : '' ?>><?php echo lang('subscription_type_restrictions') ?></option>
                    </select>
                    <!-- <button id="closeMainSelect" style="display: none;margin-top: 15px;" class="stop mie-2"></button> -->
                    <div class="text-danger mis-9" id="closeMainSelect" style="display: none"><i class="fas fa-do-not-enter"></i></div>
                </div>





            </div>
        </div>
        <!-- modal content end -->
        <div class="generalPopupFooter d-flex justify-content-end">
            <button class="subCancel generalPopupButtonCancel closePurchaseLimitPopup" data-parent="mainPopup" data-target="purchaseLimitPopup"><?php echo lang('action_cacnel') ?></button>
            <button  id="purchaseLimitPopupButtonSave" class="subSave generalPopupButtonSave blueImportant"><?php echo lang('save') ?></button>
        </div>
    </div>
</div>
