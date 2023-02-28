<?php
if(isset($optionLimitsNum) && is_numeric($optionLimitsNum)) {
    $optionLimitsNum++;
} else {
    $optionLimitsNum = 0;
}
?>

<div class="registration-restrictions-option-section reg--with-full-height">
    <div class="d-flex flex-column reg--with-full-height">
        <div class="limit-membership-club-to px-15">
            <div class="form-group flex-fill my-15 mie-15">
                <label class="font-weight-bold "><?= lang('membership_restrictions_new_select') ?></label>
                <div class="is-invalid-container class-type-limit-container">
                    <select multiple class="bsappMultiSelect class-type-limits js-post-value" name="Class" id="class-type-limits-input-<?=$optionLimitsNum?>">
                        <?=$classTypeLessonOptions?>
                        <?=$classTypeMeetingsOptions?>
                        <?=$classTypeRoomsOptions?>
                    </select>
                </div>
            </div>
        </div>
        <div class="reg--with-child-overflow">
            <div id="membership-club-limit-section" class="d-flex flex-column bsapp-scroll overflow-auto limits-section-size reg--with-full-height">
                <div id="default-membership-club-limit-block" class="d-flex flex-column h-100 py-30 align-items-center justify-content-center">
                    <input type="hidden" id='no-limit-input-<?=$optionLimitsNum?>' name="NoLimits" class="js-post-value no-limit-input" value=1>
                    <h6 class="bsapp-fs-22 font-weight-normal mb-10"><?=lang('free_entrance_without_restriction')?></h6>
                    <a class="btn btn-lg border border-secondary font-weight-bold shadow-none bsapp-fs-16"
                       onclick="registrationRestrictions.createFirstLimitBlock(this)"><?=lang('create_limits')?></a>
                </div>
                <div id="custom-membership-club-limit-blocks" class="d-none pb-10">
                    <div class="d-flex justify-content-between align-items-center px-15 mb-15" id="membership-club-limit-header">
                        <div class="py-15">
                            <span class="bsapp-fs-16 font-weight-bold"><?=lang('entries_restrictions_title')?></span>
                        </div>
                        <a class="btn btn-lg border border-secondary font-weight-bolder shadow-none bsapp-fs-14 add-block"
                           onclick="registrationRestrictions.addLimit(this)"><?=lang('add_purchase_restriction_block')?></a>
                    </div>
                    <div id="membership-club-limit-blocks-list" class="">

                        <div class="row based-availability-limit-row-block m-0 mb-10 d-none">
                            <div class="col-1 align-items-center d-flex remove-button">
                                <a role="button" onclick="registrationRestrictions.removeBlockBasedAvailability(this)">
                                    <i class="remove-icon p-0 fal fa-trash-alt bsapp-fs-24"></i>
                                </a>
                            </div>
                            <div class="col bsapp-rounded-8p mx-15 bg-white py-10 based-availability-limit-block shadow bg-white">
                                <div class="based-availability-limit-inputs">
                                    <input type="hidden" class="based-availability-amount" name="basedAvailabilityAmount">
                                    <input type="hidden" class="based-availability-periodType" name="basedAvailabilityPeriodType">
                                    <input type="hidden" class="based-availability-timeBefore" name="basedAvailabilityTimeBefore">
                                    <input type="hidden" class="based-availability-typeTimeBefore" name="basedAvailabilityTypeTimeBefore">
                                </div>
                                <div class="row p-0 m-0 px-15 text-center d-flex flex-column"
                                     onclick="registrationRestrictions.openLimitBasedAvailabilityPopup(this)">
                                    <h6><?=lang('registration_availability') . ':' ?></h6>
                                    <h6 class="block-limit-text"> 3 כניסות ולאפשר רק שעתיים לפני תחילת השיעור</h6>
                                </div>
                            </div>
                        </div>

                        <div class="row membership-club-limit-row-block m-0 mb-10" data-id=0>
                            <div class="col-1 align-items-center d-flex remove-button">
                                <a role="button" onclick="registrationRestrictions.removeBlock(this)">
                                    <i class="remove-icon p-0 fal fa-trash-alt bsapp-fs-24"></i>
                                </a>
                            </div>
                            <div class="col bsapp-rounded-8p mx-15 bg-white py-10 membership-club-limit-block shadow bg-white">
                                <div class="row p-0 m-0 mb-10 px-15">
                                    <div class="is-invalid-container w-100">
                                        <select class="form-control js-select-shop-new membership-club-limit-type"
                                                data-old-value="-1" name="membershipClubLimitType"
                                                onchange="registrationRestrictions.membershipClubLimitTypeChange(this)">
                                            <option value="1"><?= lang('max_restriction') ?></option>
                                            <option value="2"><?= lang('days_restriction') ?></option>
                                            <option value="3"><?= lang('hours_restriction') ?></option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row form-group flex-fill m-0 max-entrances-limit limit-more-details">
                                    <div class="col px-15">
                                        <label class="font-weight-bold bsapp-fs-14 m-0"><?=lang('entries_number_membership')?></label>
                                        <div class="">
                                            <select class="form-control js-select-shop-new max-number-entries"
                                                    name="maxNumberEntries">
                                                <?php for ($i = 0; $i <= 144; $i++) { ?>
                                                    <option value="<?=$i?>"> <?=lang('up_to') ." " .$i?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col px-15">
                                        <label class="font-weight-bold bsapp-fs-14 m-0"><?= lang('in_period_shop_render') ?></label>
                                        <div class=>
                                            <select class="form-control js-select-shop-new type-period-time"
                                                    data-old-value="-1" name="typePeriodTime"
                                                    onchange="registrationRestrictions.typePeriodTimeChange(this)">
                                                <option value="1"><?=lang('a_day')?></option>
                                                <option value="2"><?=lang('a_week')?></option>
                                                <option value="3"><?=lang('a_month')?></option>
                                                <option value="4"><?=lang('a_year')?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group flex-fill m-0 days-limits limit-more-details d-none">
                                    <div class="col px-10">
                                        <label class="font-weight-bold bsapp-fs-14 m-0"><?= lang('selection_days_can_register') ?></label>
                                        <div class="limit-day-selector text-center"></div>
                                    </div>
                                </div>


                                <div class="row form-group flex-fill m-0 hours-limits limit-more-details d-none">
                                    <div class="col px-15">
                                        <label class="font-weight-bold bsapp-fs-14 m-0"><?=lang('can_register_from')?></label>
                                        <div class="">
                                            <select class="form-control js-select-shop-new hours-limit-from"
                                                    data-old-value="-1" name="hoursLimitFrom"
                                                        onchange="registrationRestrictions.hoursLimitFromChange(this)">
                                                <?php
                                                $steps = 30;
                                                $current = 0;
                                                $loops = 24*(60/$steps);
                                                for ($i = 0; $i < $loops; $i++) {
                                                    $time = sprintf('%02d:%02d', $i/(60/$steps), $current%60);
                                                    echo '<option data-index='.$i.' value=' .$time . '>'.$time.'</option>';
                                                    $current += $steps;
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col px-15">
                                        <label class="font-weight-bold bsapp-fs-14 m-0"><?= lang('register_until') ?></label>
                                        <div class=>
                                            <select class="form-control js-select-shop-new hours-limit-until"
                                                    data-old-value="-1" name="hoursLimitUntil"
                                                    onchange="registrationRestrictions.hoursLimitUntilChange(this)">
                                                <?php
                                                $steps = 30;
                                                $current = 0;
                                                $loops = 24*(60/$steps);
                                                for ($i = 0; $i < $loops; $i++) {
                                                    $time = sprintf('%02d:%02d', $i/(60/$steps), $current%60);
                                                    echo '<option data-index='.$i.' value=' .$time . '>'.$time.'</option>';
                                                    $current += $steps;
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- modal content end -->

        <div class="d-none justify-content-center align-items-center border-top border-light py-10 flex-column limit-based-availability-section">
            <span class="bsapp-fs-14">
                <?=lang('add_maximum_limit_note')?>
            </span>
            <a class="font-weight-bold bsapp-fs-14" role="button"
               onclick="registrationRestrictions.openLimitBasedAvailabilityPopup()"><?= '+ ' . lang('create_limit_based_availability')?>
            </a>
        </div>
    </div>
</div>
