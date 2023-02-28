<div class="row  mt-20 selectContainers" id="type<?php echo $type ?>">

    <div class="col-md-12 mb-10" id="type<?php echo $type ?>">
        <input type="hidden" value="" id="hiddenIdInput<?php echo $type ?>">
        <label for="membershipName"><?php echo lang('subscribtion_name_membership') ?></label>
        <input class="form-control  bg-light border-light w-100" placeholder="<?php echo lang('subscribtion_name_membership') ?>" onfocus="this.placeholder = ''" onblur="this.placeholder = '<?php echo lang('subscribtion_name_membership') ?>'" id="membershipName<?php echo $type ?>" />
    </div>


    <div class="col-md-12 mb-10  membershipField" <?= empty($company->getMembershipTypes()) || count($company->getMembershipTypes()) <= 1 || $companyProductSettings->manageMemberships == 0 ? 'style="display:none;"' : '' ?>>        <input type="hidden" id="isMembershipTypeNew<?php echo $type ?>" value="0">
        <label for="membershipType<?php echo $type ?>"><?php echo lang('membership_type_single') ?></label>
        <div class="icon-container bsapp-z-1">
            <span class="newLabel"><?php echo lang('new') ?></span>
            <!-- <i class="fas fa-bolt"></i> -->

        </div>
        <select name="location" class="membershipType js-select2-shop w-100" id="membershipType<?php echo $type ?>">
            <?php
            foreach ($company->getMembershipTypes() as $membershipType) {
                echo '<option value="' . $membershipType->__get('id') . '">' . $membershipType->__get('Type') . '</option>';
            }
            ?>
        </select>
    </div>
    <?php if (Count($company->getBrands()) > 1) { ?>
        <div class="col-md-12 mb-10">
            <label class="shopLabel" for="shopCmpBranch<?php echo $type ?>">סניף</label>
            <select class="shopCmpBranch form-control bg-light border-light  js-select2-shop" id="shopCmpBranch<?php echo $type ?>">
                <option value="-1"><?php echo lang('all_branch') ?></option>
                <?php foreach ($company->getBrands() as $brand) { ?>
                    <option value=" <?php echo $brand->__get("id") ?>"> <?php echo $brand->__get("BrandName") ?></option>
                <?php } ?>
            </select>
        </div>
    <?php } ?>
</div>
<div class="extraRows row">
    <div class="rowInput priceRow col-md-12 ">
        <div class="rowIconContainer">
            <i class="fal fa-usd-circle"></i>
        </div>
        <input id="membershipPrice<?php echo $type ?>" class="membershipPrice  form-control  bg-light border-light mie-9" placeholder="<?php echo lang('price') ?>" type="number" />
        <!-- <label class="CheckboxLabel">
            <input type="checkbox" class="shopCheckbox form-control  bg-light border-light" id="taxInclude<?php //echo $type ?>" name="taxInclude" checked="checked" />
            <span class="shopCheckmark"></span>
        </label>
        <label class="taxIncludeLabel" for="taxInclude">כולל מע"מ</label> -->
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="taxInclude<?php echo $type ?>" name="taxInclude" checked="checked">
            <label class="custom-control-label" for="taxInclude<?php echo $type ?>"><?php echo lang('include_vat') ?></label>
        </div>
    </div>
    <?php if ($type == 1) { ?>
        <div class="rowInput priceSelect col-md-12" style="display:none">
            <div class="rowIconContainer">
                <i class="fa fa-sync"></i>
            </div>
            <select id="priceSelectOptions" class="cute-input priceSelectOptions js-select2-shop">
<!--                <option class="d-none" value="0">--><?php //echo lang('billing_type_membership') ?><!--</option>-->
                <option value="1"><?php echo lang('regular_billing_membership') ?></option>
                <option value="2"><?php echo lang('recurring_billing_membership') ?></option>
            </select>
        </div>
        <?php
    }
    if ($type == 4 || $type == 5) {
        ?>
        <div class="rowInput tickets col-md-12" style="display:none">
            <div class="rowIconContainer">
                <i class="fal fa-dot-circle"></i>
            </div>
            <span class="mie-5"><?php echo lang('entries_number_membership') ?></span>
            <input type="number" id="ticketEntries<?php echo $type ?>" class="cute-input ticketEntries form-control  bg-light border-light" style="width:50px;">
        </div>
    <?php } ?>
    <div class="rowInput priceSelect1Dependent col-md-12" style="display:none">
        <div class="rowIconContainer">
            <i class="far fa-hourglass"></i>
        </div>
        <?php if ($type != 1) { ?>
            <div class="plus addMembershipLengthFlex" >
            <?php echo lang('define_validity') ?>
            </div>
        <?php } ?>
        <div class="membershipLengthFlex  flex-fill <?php
        if ($type != 1) {
            echo "membershipLenghtFLexHide";
        }
        ?>">
            <span class="mie-5"><?php echo lang('subscription_period_membership') ?></span>
            <input type="number" id="membershipLength<?php echo $type ?>" class="cute-input membershipLength form-control  bg-light border-light mie-9 w-50p">
            <div class="w-100p">
                <select id="membershipUnits<?php echo $type ?>" class="cute-input membershipUnits js-select2-shop">
                    <option value="1"><?php echo lang('days') ?></option>
                    <option value="2"><?php echo lang('weeks') ?></option>
                    <option value="3"><?php echo lang('months') ?></option>
                </select>
            </div>
            <?php if ($type != 1) { ?>
                <!-- <button class="stop mr-2 closeMembershipLengthFlex"></button> -->
                <div class="text-danger mis-9 closeMembershipLengthFlex"><i class="fas fa-do-not-enter"></i></div>
            <?php } ?>
        </div>
    </div>
    <div class="d-none rowInput membershipLengthDependent col-md-12" style="display:none">
        <div class="rowIconContainer">
            <i class="far fa-bell"></i>
        </div>
        <div class="alertOnEndFlex">
            <label style="visibility:hidden;opacity:0;width:0;" class="CheckboxLabel p-0 m-0">
                <input type="checkbox" class="shopCheckbox alertOnEnd" id="alertOnEnd<?php echo $type ?>" name="alertOnEnd" />
                <span class="shopCheckmark"></span>
            </label>
            <label class="alertOnEndLabel plus mb-0 pt-0" for="alertOnEnd<?php echo $type ?>"><?php echo lang('send_notification_membership') ?></label>
            <div class="alertOnEndOpend closed d-flex justify-content-center align-items-center">
                <input type="hidden" disabled class="cute-input" value='<?php echo lang('note_send_notification_membership') ?>'>
                <span class="cute-input"><?php echo lang('note_send_notification_membership') ?></span>
                <!-- <button id="closeAlertOnEndFlex" class="stop mr-2 "></button> -->
                <div class="text-danger mis-9" id="closeAlertOnEndFlex"><i class="fas fa-do-not-enter"></i></div>
            </div>
        </div>
    </div>
    <div class="d-none rowInput  fitContent membershipLengthDependent openMembershipAlertSettings col-md-12" style="display:none" id="openMembershipAlertSettings<?php echo $type ?>">
        <div class="rowIconContainer">
            <i class="far fa-bell"></i>
        </div>
        <div class="plus">
            +
            <?php echo lang('send_notification_before_membership') ?>
        </div>
        <div class="d-flex align-items-center hiddenMembershipAlertSettings hidden">
            <input class="cute-input membershipAlertSettingsNumber w-50p mie-7" type="number" id="membershipAlertSettingsNumber<?php echo $type ?>">
            <div class="w-100p mie-7">
                <select id="membershipAlertSettingsUnitType<?php echo $type ?>" class="cute-input membershipAlertSettingsUnitType js-select2-shop">
                    <option value="1"><?php echo lang('days') ?></option>
                    <option value="2"><?php echo lang('weeks') ?></option>
                    <option value="3"><?php echo lang('months') ?></option>
                </select>
            </div>
            <?php echo lang('before_membership_ends') ?>
            <!-- <button id="closeMembershipAlertSettings<?php //echo $type ?>" class="stop mis-2 closeMembershipAlertSettings"></button> -->
            <div class="text-danger mis-9 closeMembershipAlertSettings" id="closeMembershipAlertSettings<?php echo $type ?>"><i class="fas fa-do-not-enter"></i></div>
        </div>
    </div>
    <div class="rowInput fitContent priceSelectDependent registerPopupContainer col-md-12" id="registerPopupContainer<?php echo $type ?>" style="display:none">
        <div id="openRegisterLimitPopup<?php echo $type ?>" class="openRegisterLimitPopup">
            <div class="rowIconContainer">
                <i class="far fa-hand-paper"></i>
            </div>
            <div><?php echo lang('class_booking_restrictions_membership') ?></div>
        </div>
    </div>
    <div class="rowInput priceSelectDependent col-md-12" style="display:none">
        <div class="rowIconContainer">
            <i class="fas fa-mobile-alt"></i>
        </div>
        <select id="allowBuyFromApp<?php echo $type ?>" class="cute-input allowBuyFromApp js-select2-shop">
            <option value="0"><?php echo lang('app_disable_purschase') ?></option>
            <option value="1"><?php echo lang('app_enable_purschase') ?></option>
        </select>
    </div>
    <div class="rowInput allowBuyDependent  col-md-12" style="display:none;">
        <div class="rowIconContainer">
            <i class="fas fa-history"></i>
        </div>
        <select id="membershipStartSelect<?php echo $type ?>" class="cute-input membershipStartSelect  js-select2-shop">
            <option value="1"><?php echo lang('membership_start_purchase') ?></option>
            <option value="3"><?php echo lang('membership_start_first_class') ?></option>
            <option value="4"><?php echo lang('date') ?></option>
        </select>
        <input id="lateRegisterDateInputMembership<?php echo $type ?>" placeholder="<?php echo lang('select_date') ?>" class="cute-input dateExpirationDependentMembership lateRegisterDateInputMembership w-150p mx-2" style="width:20%;display:none;" />

    </div>
    <div class="allowBuyDependent  col-md-12" style="display:none;">

        <div class="rowInput dateSelectedDependendMembership" style="display:none">
            <label class="CheckboxLabel">
                <input type="checkbox" class="shopCheckbox allowLateRegisterMembership" id="allowLateRegisterMembership<?php echo $type ?>" name="taxInclude" />
                <span class="shopCheckmark"></span>
            </label>
            <label class="taxIncludeLabel" for="allowLateRegisterMembership<?php echo $type ?>"><?php echo lang('allow_late_booking') ?></label>
        </div>
        <div class="rowInput allowLateRegisterDependentMembership" style="display:none">
            <label class="CheckboxLabel">
                <input type="checkbox" class="shopCheckbox allowRelativeCheckboxMembership" id="allowRelativeCheckboxMembership<?php echo $type ?>" name="taxInclude" />
                <span class="shopCheckmark"></span>
            </label>
            <label class="taxIncludeLabel" for="allowRelativeCheckboxMembership<?php echo $type ?>">אפשר קיזוז יחסי</label>
<!--        </div>

        <div class="rowInput relativeDiscount" style="display:none">-->
            <label class="CheckboxLabel relativeDiscount"  style="display:none">
                <input type="number" class="cute-input w-100p membershipRelativeDiscount mx-2" id="membershipRelativeDiscount<?php echo $type ?>" />
            </label>
        </div>
    </div>
    <div class="rowInput allowBuyDependent  col-md-12 " style="display:none">
        <div class="edit-avatar classImg" id="imgPlus<?php echo $type ?>" data-ip-modal="#itemModal" title="<?php echo lang('edit_image') ?>" style="display: flex">
            <div class="rowIconContainer">
                <i class="far fa-image"></i>
            </div>
            <div class="plus ImgEmpty">
                +
                <?php echo lang('add_image_membership') ?>
            </div>
            <div class="hidden hiddenImg d-flex align-items-center">
                <div class="ImgName" id="ImgName<?php echo $type ?>">
                </div>
            </div>
        </div>
        <!-- <div class="stop removeImg" id="removeImg" style="display: none"></div> -->
        <div class="text-danger mis-9 removeImg" id="removeImg" style="display: none"><i class="fas fa-do-not-enter"></i></div>
        <input type="hidden" id="pageImgPath<?php echo $type ?>" name="pageImgPath" value="" />
    </div>


    <div class="rowInput allowBuyDependent openTextarea  col-md-12" style="display:none" id="openTextarea<?php echo $type ?>">
        <div class="rowIconContainer">
            <i class="far fa-comment-alt"></i>
        </div>
        <div class="plus"><?php echo lang('add_description_membership') ?></div>
        <div style="width:100%;" class="hidden hiddenTextarea d-flex align-items-center">
            <textarea class="membershipContent" id="membershipContent<?php echo $type ?>"></textarea>
            <!-- <div class="stop mr-2 closeTextarea" id="closeTextarea<?php //echo $type ?>"></div> -->
            <div class="text-danger mis-9 closeTextarea" id="closeTextarea<?php echo $type ?>"><i class="fas fa-do-not-enter"></i></div>
        </div>
    </div>

    <div id="purchaseLimitPopupMembership<?php echo $type ?>" data-type="1" class="rowInput  col-md-12 openPurchaseLimitPopup allowBuyDependent purchaseLimitPopupMembership fitContent" style="display:none">
        <div class="rowIconContainer">
            <i class="far fa-eye-slash"></i>
        </div>
        <div><?php echo lang('purchase_limit_membership') ?></div>
    </div>
    <div id="purchaseLimitPopupMembershipHidden<?php echo $type ?>" class="rowInput purchaseLimitPopupMembershipHidden  col-md-12 mx-0" style="display:none">
        <div class="rowIconContainer">
            <i class="far fa-eye-slash"></i>
        </div>
        <input type="hidden" class="hiddenPurchaseInput purchaseLimitPopupMembershipHiddenInput" data-id="" id="purchaseLimitPopupMembershipHiddenInput<?php echo $type ?>" />
        <div class="popupLineText purchaseLimitPopupMembershipHiddenText" id="purchaseLimitPopupMembershipHiddenText<?php echo $type ?>"></div>
        <div class="mr-2 editPurchaseLine"><i class="fas fa-pencil-alt"></i></div>
        <!-- <div class="stop mr-2 purchaseLimitPopupMembershipHiddenClose" id="purchaseLimitPopupMembershipHiddenClose<?php //echo $type ?>"></div> -->
        <div class="text-danger mis-9 purchaseLimitPopupMembershipHiddenClose" id="purchaseLimitPopupMembershipHiddenClose<?php echo $type ?>"><i class="fas fa-do-not-enter"></i></div>
    </div>

</div>
