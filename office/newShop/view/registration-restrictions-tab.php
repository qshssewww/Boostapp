<?php
    $optionLimitsNum = 0;
?>


<!-- registration-restrictions-tab start -->

    <div id="registration-restrictions-page" class="reg--with-bottom-text">
        <!-- modal header start -->
        <div class="d-flex justify-content-start align-items-center px-15 py-15 border-bottom border-light bsapp-fs-18 font-weight-bold">
            <div class="d-flex cursor-pointer" onclick="registrationRestrictions.backToMainTab(this)" data-target="create-club-memberships-home">
                <a href="javascript:;" class="text-dark mie-10">
                    <i class="fal fa-angle-left"></i>
                </a>
                <div>
                    <?= lang('registration_options') ?>
                </div>
            </div>
        </div>
        <!-- modal body start -->
        <!--    <div class="bsapp-scroll overflow-auto sub-tab-body">-->
        <div id="registration-restrictions-section" class="reg--with-full-height">
            <div class="container reg--with-full-height">
                <div class="row reg--with-full-height">
                    <div class="col-12 m-0 p-0 reg--with-full-height">
                        <div id="registration-option-tabs-section" style="background: #F3F3F4;box-shadow: inset -3px -7px 9px #eeeeeeb3;">
                            <!-- Nav tabs -->
                            <ul id="registration-option-tabs-list" class="nav nav-tabs p-0 m-0 pt-10 " role="tablist">
                                <li class="registration-option-tab active col-6 bsapp-fs-24 text-center py-10 bg-white" style="z-index: 100;" data-id=1>
                                    <a class="text-black bold link-option" href="#option1" role="tab"
                                       onclick="registrationRestrictions.selectedTab(this,event)">
                                        <?=lang('option') . " 1" ?></a>
                                </li>
                                <li class="registration-option-tab col-2 bsapp-fs-24 text-center py-10 bg-white" style="z-index: 99;"data-id=2>
                                    <a class="text-black bold add-option" href="#option2" role="tab"
                                       onclick="registrationRestrictions.selectedTab(this,event)">+</a>
                                </li>
                                <li class="registration-option-tab d-none col-2 bsapp-fs-24 text-center py-10 bg-white" style="z-index: 98;" data-id=3>
                                    <a class="text-black disabled-option bold " href="#option3" role="tab"
                                       onclick="registrationRestrictions.selectedTab(this,event)">+</a>
                                </li>
                                <li class="registration-option-tab d-none col-2 bsapp-fs-24 text-center py-10 bg-white" style="z-index: 97" data-id="4">
                                    <a class="text-black bold disabled-option" href="#option4" role="tab"
                                       onclick="registrationRestrictions.selectedTab(this,event)">+</a>
                                </li>
                            </ul>
                        </div>

                        <!-- Tab panes -->
                        <div id="tab-content" class="tab-content reg--with-bottom-text">
                            <div class="tab-pane tab-body-option reg--with-full-height active" id="option1" data-option="1">
                                <?php include "registration-restrictions-option.php"; ?>
                            </div>
                            <div class="tab-pane tab-body-option reg--with-full-height" id="option2" data-option="2">
                                <?php include "registration-restrictions-option.php"; ?>
                            </div>
                            <div class="tab-pane tab-body-option reg--with-full-height" id="option3" data-option="3">
                                <?php include "registration-restrictions-option.php"; ?>
                            </div>
                            <div class="tab-pane tab-body-option reg--with-full-height" id="option4" data-option="4">
                                <?php include "registration-restrictions-option.php";
                                unset($optionLimitsNum); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!-- registration-restrictions-tab:: end -->


<div class="d-none" style="position:fixed;width: 100%;background-color:rgba(0,0,0,0.2);height: 100vh;"
     id="limit-based-availability-background">
    <!-- limit based availability modal -->
    <div class="popupWrapper based-availability-modal-size position-absolute" data-backdrop="static"
         tabindex="-1" role="dialog" id="limit-based-availability-modal">
        <div class="popupContainer w-100 h-100 p-0" style="overflow: hidden;text-align: initial;">
            <div class="border-0 shadow-lg">
                <div class="modal-body h-100 bsapp-overflow-y-auto position-relative px-0 py-0 d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-center border-bottom border-light">
                        <div class="px-15 py-15">
                            <span class="bsapp-fs-18 font-weight-bold"><?= lang('registration_availability') ?></span>
                        </div>
                        <a href="javascript:;" class="text-dark bsapp-fs-20 p-15 font-weight-bold" onclick="registrationRestrictions.closeLimitBasedAvailabilityPopup()">
                            <i class="fal fa-times"></i>
                        </a>
                    </div>
                    <div class="limit-based-availability-explanation-section py-10 px-15">
                        <span>
                            <?=lang('description_availability')?>
                        </span>
                    </div>
                    <div class="limit-based-availability-inputs-section px-10 pb-10 border-bottom border-light">
                        <div class="d-flex px-15 amount-section">
                            <div class="form-group flex-fill ">
                                <label class="font-weight-bold "><?= lang('quantity') ?></label>
                                <div class="">
                                    <select class="form-control w-100 js-select2-shop-new limit-based based-availability-amount" name="basedAvailabilityAmount-popup">
                                        <?php
                                        for ($i = 1; $i <=10; $i++) {
                                            echo '<option value=' .$i . '>'.$i.'</option>';
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex px-15 period-section">
                            <div class="form-group flex-fill">
                                <label class="font-weight-bold "><?= lang('in_period_shop_render') ?></label>
                                <div class="">
                                    <select class="form-control w-100 js-select2-shop-new limit-based based-availability-period-type" name="basedAvailabilityPeriodType-popup">
                                        <option value="1"><?=lang('a_day') ?></option>
                                        <option value="2"><?=lang('a_week') ?></option>
                                        <option value="3"><?=lang('a_month') ?></option>
                                        <option value="4"><?=lang('a_year') ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex px-15 before-class-section">
                            <div class="form-group flex-fill">
                                <label class="font-weight-bold "><?= lang('time_before_the_class_availability') ?></label>
                                <div class="">
                                    <select class="form-control w-100 js-select2-shop-new limit-based based-availability-time-before" name="basedAvailabilityTimeBefore-popup">
                                        <?php
                                        for ($i = 1; $i <=24; $i++) {
                                            echo '<option value='.$i . '-2>'.$i . ' '. lang('hours').'</option>';
                                        } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex px-15 pb-15 pt-5 w-100">
                        <a class="btn btn-lg btn btn-dark text-white  font-weight-bolder shadow-none border-0 w-100 bsapp-fs-16"
                           onclick="registrationRestrictions.addLimitBasedAvailability(this)"><?= lang('save') ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- limit based availability modal:: end -->
</div>
