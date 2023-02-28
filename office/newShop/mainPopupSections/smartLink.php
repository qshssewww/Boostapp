<div class="row mt-20">
    <input type="hidden" value="" id="hiddenIdInput<?php echo $type ?>">
    <div class="col-md-6 mb-10">
        <label for="smartLinkTitle"><?php echo lang('title_smart_link') ?></label>
        <input class="form-control bg-light border-light" placeholder="<?php echo lang('title_smart_link') ?>" onfocus="this.placeholder = ''" onblur="this.placeholder = '<?php echo lang('title_smart_link') ?>'" name="smartLinkTitle" id="smartLinkTitle" style="width: 100%" />
    </div>

    <div class="col-md-6 mb-10">
        <?php
        if ((Count($company->getBrands()) > 1)) {
            echo '<label for="smartLinkCustomer">'.lang('connect_custumer_to_branch').'</label>
            <select class="form-control bg-light border-light js-select2-shop" name="smartLinkCustomer" id="smartLinkCustomer" style="width: 100%">';
            foreach ($company->getBrands() as $brand) {
                echo '<option value="' . $brand->__get('id') . '" >' . $brand->__get('BrandName') . '</option>';
            }
            echo '</select>';
        }
        ?>
    </div>
    <div class="col-12 mb-10">
        <div class="rowInput">
            <div class="rowIconContainer">
                <i class="far fa-clone"></i>
            </div>
            <select id="smartLinkProductType" class="cute-input form-control bg-light border-light js-select2-shop">
                <option value="0"><?php echo lang('product_type_smart_link') ?></option>
                <option value="1"><?php echo lang('general_item_smart_link') ?></option>
                <option value="2"><?php echo lang('club_membership_smart_link') ?></option>
            </select>
        </div>
    </div>
    <div style="display:none;" id="chosenProductsContainer" class="col-12 productTypeDependent1">
        <!-- multiple -->
        <div class="rowInput fitContent w-200p" id="addSingleSmartLinkProduct" style="cursor:pointer;">
            <div class="rowIconContainer">
                <i class="fas fa-store"></i>
            </div>
            <select id="smartLinkHiddenProduct" class="cute-input form-control bg-light border-light js-select2-shop" style="width:33%; min-width: 350px; display:none!important">
                <option value="0"><?php echo lang('product_selection_smart_link') ?></option>
                <?php
                if ($productItems) {
                    foreach ($productItems as $product) {
                        echo '<option data-image="' . $product->Image . '" data-price="' . $product->ItemPrice . '" value="' . $product->id . '">' . $product->ItemName . '</option>';
                    }
                }
                ?>
            </select>
        </div>

    </div>
    <div style="display:none;" class="col-12 productTypeDependent2">
        <div class="rowInput">
            <div class="rowIconContainer">
                <i class="fas fa-store"></i>
            </div>
            <select id="smartLinkChosenMembership" class="cute-input form-control bg-light border-light js-select2-shop">
                <option value="0"><?php echo lang('select_subscription_sale') ?></option>
                <?php
                if ($membershipItems) {
                    foreach ($membershipItems as $membership) {
                        ?>
                        <option data-price='<?php echo $membership->ItemPrice ?>' data-image='<?php echo $membership->Image ?>' data-content='<?php
                        echo json_encode(
                                array(
                                    "valid" => $membership->Vaild,
                                    "validType" => $membership->Vaild_Type,
                                    "Payment" => $membership->Payment,
                                    "Department" => $membership->Department,
                                    "Balance" => $membership->BalanceClass,
                                    "StartMembership" => $membership->membershipStartCount
                                )
                        )
                        ?>' value='<?php echo $membership->id ?>'><?php echo $membership->ItemName ?>
                        </option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>
    </div>
    <div style="display:none;" class="col-12 productSelectDependent2">
        <div class="rowInput">
            <div class="rowIconContainer">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <span id="generatedMembershipString"></span>
        </div>
    </div>

    <div style="display:none;" class="col-12 productSelectDependent2">
        <div class="rowInput priceRow">
            <div class="rowIconContainer">
                <i class="fal fa-usd-circle"></i>
            </div>
            <input class="form-control bg-light border-light mie-7" id="linkPrice" placeholder="מחיר" type="number" />
            <!-- <label class="taxIncludeLabel p-0" for="linkTaxInclude">כולל מע"מ</label>
            <label class="CheckboxLabel p-0">
                <input type="checkbox" class="shopCheckbox form-control bg-light border-light" id="linkTaxInclude" name="taxInclude" checked="checked" />
                <span class="shopCheckmark"></span>
            </label> -->
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="linkTaxInclude" name="taxInclude" checked="checked">
                <label class="custom-control-label" for="linkTaxInclude"><?php echo lang('include_vat') ?></label>
            </div>

        </div>
    </div>

    <div style="display:none;" class="col-12 smartLinkExpiration">
        <div class="rowInput">
            <div class="rowIconContainer">
                <i class="fas fa-history"></i>
            </div>
            <select id="smartLinkExpiration" class="cute-input js-select2-shop">
                <option value="1"><?php echo lang('membership_start_purchase') ?></option>
                <!-- <option value="2">במועד הרכישה</option> -->
                <option value="3"><?php echo lang('membership_start_first_class') ?></option>
                <option value="4"><?php echo lang('selected_date_smart_link') ?></option>
            </select>
        </div>
        <div class="rowInput mt-5 mr-20">
            <input id="lateRegisterDateInput" placeholder="בחר תאריך" class="cute-input dateExpirationDependent" style="width: 110px; margin-left: 5px; text-align: right; padding-top: 8px;" />
            <div class="rowInput dateSelectedDependend ml-2" style="display:none">
                <label class="CheckboxLabel mt-10">
                    <input type="checkbox" class="shopCheckbox" id="allowLateRegister" name="taxInclude" />
                    <span class="shopCheckmark"></span>
                </label>
                <label class="taxIncludeLabel" style="font-size: 0.9em;" for="allowLateRegister"><?php echo lang('allow_late_booking') ?></label>
            </div>
            <div class="rowInput allowLateRegisterDependent" style="display:none">
                <label class="CheckboxLabel mt-10">
                    <input type="checkbox" class="shopCheckbox" id="allowRelativeCheckbox" name="taxInclude" />
                    <span class="shopCheckmark"></span>
                </label>
                <label class="taxIncludeLabel" style="font-size: 0.9em;" for="allowRelativeCheckbox"><?php echo lang('enable_relative_offset') ?></label>
            </div>
        </div>
        <div class="rowInput allowRelativeDependent mt-5 mr-20" style="display:none">
            <input class="form-control bg-light border-light mie-7" id="relativeReductionPrice" placeholder="<?php echo lang('offset_amount_smart_link') ?>" type="number" />
        </div>
    </div>
    <!-- <div class="parent col-6" style="display:inline-block;"> -->
    <div style="display:none;" class="col-12 productSelectDependent2 ticketsClasses">
        <div class="rowInput">
            <div class="rowIconContainer">
                <i class="far fa-calendar-alt"></i>
            </div>
            <div class="plus">
            <?php echo lang('book_selected_dates_smart_link') ?>
            </div>
        </div>
    </div>
    <div style="display:none;" class="col-12 productSelectDependent2  membershipClasses">
        <div class="rowInput ">
            <div class="rowIconContainer">
                <i class="far fa-calendar-alt"></i>
            </div>
            <div class="plus"><?php echo lang('book_number_class_smart_link') ?></div>
        </div>
    </div>

    <div style="display:none;" id="openRegisterForm" class="col-12  selectItemDependent">
        <div class="rowInput fitContent js-select-container" >
            <div class="rowIconContainer">
                <i class="fas fa-paperclip"></i>
            </div>
<!--            <div class="plus">--><?php //echo lang('registration_form_smart_link') ?><!--</div>-->
            <div style="width:100%;" class="hidden hiddenRegisterForm d-flex align-items-center w-250p">
                <select class="cute-input registerFormSelect js-select2-shop" id="registerFormSelect">
                    <option selected value="-1"><?php echo lang('select_form_smart_link') ?></option>
                    <?php
                    if ($healthForms) {
                        echo '<option value="__M' . $healthForms->id . '">' . $healthForms->name . '</option>';
                    }
                    if ($dynamicForms) {
                        foreach ($dynamicForms as $form) {
                            echo '<option value="__D' . $form->id . '">' . $form->name . '</option>';
                        }
                    }
                    ?>
                </select>
                <!-- <div class="stop mr-2" id="closeRegisterForm"></div> -->
                <div class="text-danger mis-9 cursor-pointer " id="closeRegisterForm"><i class="fas fa-do-not-enter"></i></div>
            </div>
        </div>
        <div class="rowInput fitContent js-add-btn-container" <?php
        if ((!$dynamicForms || !count($dynamicForms)) && !$healthForms) {
            echo "style=display:none!important";
        }
        ?> >
            <div class="rowIconContainer">
                <i class="fas fa-paperclip"></i>
            </div>
            <div class="plus"><?php echo lang('registration_form_smart_link') ?></div>
        </div>
    </div>

    <div style="display:none;" id="openRegisterInsurance" class="col-12  selectItemDependent">
        <div class="rowInput fitContent js-select-container hidden" >
            <div class="rowIconContainer">
                <i class="fas fa-cart-plus"></i>
            </div>

            <div style="width:100%;" class="hiddenRegisterInsurance d-flex align-items-center w-250p">
                <select class="cute-input registerInsuranceSelect js-select2-shop" id="registerInsuranceSelect">
                    <option selected value="-1"><?php echo lang('select_fee_smart_link') ?></option>
                    <?php
                    if ($registrationFees) {
                        foreach ($registrationFees as $fee) {
                            echo '<option value="' . $fee->id . '">' . $fee->ItemName . '</option>';
                        }
                    }
                    ?>
                </select>
                <!-- <div class="stop mr-2" id="closeRegisterInsurance"></div> -->
                <div class="text-danger mis-9 cursor-pointer" id="closeRegisterInsurance"><i class="fas fa-do-not-enter"></i></div>
            </div>
        </div>
        <div class="rowInput fitContent js-add-btn-container" <?php
        if (!$registrationFees || !count($registrationFees)) {
            echo "style=display:none!important";
        }
        ?> >
            <div class="rowIconContainer">
                <i class="fas fa-cart-plus"></i>
            </div>
            <div class="plus">+ צרף דמי הרשמה/ביטוח</div>
        </div>
    </div>

    <div style="display:none;" class="col-12 selectItemDependent">
        <div class="rowInput">
            <div class="edit-avatar classImg" id="imgPlus<?php echo $type ?>" title="<?php echo lang('edit_image') ?>" data-ip-modal="#itemModal" style="display: flex">
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
    </div>

    <div style="display:none;" id="openLink" class="col-12  selectItemDependent">
        <div class="rowInput fitContent">
            <div class="rowIconContainer">
                <i class="fas fa-external-link-alt"></i>
            </div>
            <div class="plus" id="openAfterLink"><?php echo lang('redirect_link_smart_link') ?></div>
            <div style="width:100%;" class="hidden hiddenLink d-flex align-items-center">
                <input id="afterLink" class="cute-input" />
                <!-- <div class="stop mr-2" id="closeLink"></div> -->
                <div class="text-danger mis-9" id="closeLink"><i class="fas fa-do-not-enter"></i></div>
            </div>
        </div>
    </div>

    <div style="display:none;" id="openTextareaLink" class="col-12  selectItemDependent">
        <div class="rowInput">
            <div class="rowIconContainer">
                <i class="far fa-comment-alt"></i>
            </div>
            <div class="plus"><?php echo lang('add_description_membership') ?></div>
            <div style="width:100%;" class="hidden hiddenTextareaLink d-flex align-items-center">
                <textarea id="linkContent"></textarea>
                <!-- <div class="stop mr-2" id="closeTextareaLink"></div> -->
                <div class="text-danger mis-9" id="closeTextareaLink"><i class="fas fa-do-not-enter"></i></div>
            </div>
        </div>
    </div>

    <!-- </div> -->


</div>
<style>
    .select2-results__option[aria-disabled='true']{
        background : var(--light);
        color : var( --gray-600);
        pointer-events: none;
        pointer : not-allowed;
    }
</style>
