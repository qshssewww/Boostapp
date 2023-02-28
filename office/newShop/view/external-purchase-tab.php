    <!-- modal header start -->
    <div class="d-flex justify-content-start align-items-center px-15 py-15 border-bottom border-light bsapp-fs-18 font-weight-bold">
        <div class="d-flex cursor-pointer" onclick="externalPurchase.validationBeforeBack(this)" data-target="create-club-memberships-home">
            <a href="javascript:;" class="text-dark mie-10">
                <i class="fal fa-angle-left"></i>
            </a>
            <div>
                <?= lang('external_purchase_membership') ?>
            </div>
        </div>
    </div>

    <!-- modal body start -->
    <div class="bsapp-scroll overflow-auto sub-tab-body">
        <div id="purchase-app-section"">
            <div class="d-flex px-15" id="purchase-app-select-controller">
                <div class="form-group flex-fill my-15 ">
                    <label class="font-weight-bold "><?= lang('displayed_for_purchase')?></label>
                    <div class="is-invalid-container">
                        <select class="form-control js-select2-shop-new external-purchase js-post-value" name="Display" required
                                onchange="externalPurchase.displayExternalPurchaseChange(this)">
                            <option value="0" selected><?php echo lang('no') ?></option>
                            <option value="1" ><?php echo lang('yes') ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="app-view-settings d-none">
                <div id="validity-calculation-section">
                    <div class="d-flex flex-column py-10 border border-light">
                        <div class="form-group flex-fill px-15 my-0">
                            <label class="font-weight-bold "><?= lang('calculation_of_validity')?></label>
                            <div class="is-invalid-container">
                                <select class="form-control js-select2-shop-new type-start-validity js-post-value" name="membershipStartCount"
                                onchange="externalPurchase.typeStartValidity(this)">
                                    <option value="1" selected><?php echo lang('from_date_of_purchase') ?></option>
                                    <option value="3"><?php echo lang('from_first_class') ?></option>
                                    <option value="4"><?php echo lang('from_manually_date') ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="more-details d-none">
                            <div class="row form-group flex-fill mt-10 mb-15 mx-0">
                                <div class="col px-15">
                                    <label class="font-weight-bold"><?= lang('from_this_date') ?></label>
                                    <div class="is-invalid-container">
                                        <div class="is-invalid-container">
                                            <input name="membershipStartDate" type="date" placeholder="<?=lang('select_date')?>"
                                                   class="form-control border-gray-200 bsapp-rounded-8p membership-name late-register-date js-post-value">
                                        </div>
                                    </div>
                                </div>
                                <div class="col px-15">
                                    <label class="font-weight-bold"><?= lang('allow_late_booking') ?></label>
                                    <div class="is-invalid-container">
                                        <select name="membershipAllowLateReg" class="form-control js-select2-shop-new allow-late-register w-100 js-post-value"
                                        onchange="externalPurchase.allowLateRegisterChange(this)">
                                            <option value="0" selected><?php echo lang('no') ?></option>
                                            <option value="1"><?php echo lang('yes') ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row form-group flex-fill mt-10 mb-0 mx-0 d-none more-details-offset">
                                <div class="col px-15">
                                    <label class="font-weight-bold"><?= lang('offset_relatively_each_day_late') ?></label>
                                    <div class="is-invalid-container">
                                        <select class="form-control js-select2-shop-new offset-relatively-day-late js-post-value" name="membershipAllowRelativeDiscount">
                                            <option value="0" selected><?php echo lang('no') ?></option>
                                            <option value="1"><?php echo lang('yes') ?></option>
                                        </select>
                                        <div class="mt-5">
                                            <p class="text-gray-500 text-start m-0 bsapp-fs-13 bsapp-lh-15"><?=lang('explanation_of_relative_offsets')?></p>
                                        </div>
                                    </div>
                                </div>
                                <!--                todo Asked to remove this option                 <div class="col px-15 d-none cost-offset">-->
<!--                                    <label class="font-weight-bold">--><?//= lang('cost_for_each_day_late') ?><!--</label>-->
<!--                                    <div class="position-relative is-invalid-container">-->
<!--                                        <input inputmode="decimal" type="number" onchange="createClubMemberships.setTwoNumberDecimal(this)"  max=999999 min=0 name="=costDayLate"-->
<!--                                               onKeyPress="if(this.value.length==7) return false;"  class="form-control border-gray-200 rounded m-0 py-2 cost-day-late"-->
<!--                                               placeholder="--><?//=lang('cost') ?><!--">-->
<!--                                        <span class="position-absolute" style="top:6px;left:5px;">₪</span>-->
<!--                                    </div>-->
<!--                                </div>-->
                            </div>
                        </div>
                    </div>
                </div>
                <div id="membership-club-information-section" class="px-15">
                    <div class="d-flex flex-column py-10">
                        <div class="d-flex flex-row justify-content-between">
                            <label class="font-weight-bold "><?= lang('more_info_about_club_membership')?></label>
                            <div class="font-weight-bold character-amount-section">
                                <span>250/</span>
                                <span id="current-character-amount">0</span>
                            </div>
                        </div>
                        <div class="is-invalid-container">
                            <textarea class="form-control border border-gray-200 bsapp-rounded-8p js-post-value" id="membership-club-information" draggable="false"
                                      onkeyup="shopMaim.changeDirection(this)" onkeyup="shopMaim.changeDirection(this)"
                                      rows="4" maxlength="250" style="overflow:auto;resize:none"
                                      name="Content" placeholder='<?=lang('enter_content_here')?>'></textarea>
                        </div>
                    </div>
                </div>
                <div id="add-picture-section" class="px-15 py-10 border-light border">
                    <div class="border-gray-200 bsapp-rounded-8p border classImg"
                         style="background:#F3F3F4;height: 130px;align-items:center;display:grid;">
                        <div class="position-relative overflow-hidden h-100 d-none image-section">
                            <div class="position-absolute" style="top:0;bottom:0;left:0;right:0;" id="selected-image"></div>
                            <div class="position-absolute btn btn-sm btn-light remove-picture-btn" role="button"
                                 onclick="externalPurchase.removeImage(this)" style="top:10px;right:10px;">
                                <i class="remove-icon fal fa-trash-alt bsapp-fs-24"></i>
                            </div>
                        </div>
                        <div class="d-flex flex-column text-center edit-avatar add-picture-btn" id="imgPlus"
                             data-ip-modal="#itemModal" role="button">
                            <i class="far fa-camera"></i>
                            <span><?=lang('cal_template_add_img')?></span>
                        </div>
                    </div>
                    <input type="hidden" class="js-post-value" id="pageImgPath" name="Image" value=""/>
                </div>

                <div id="purchase-restrictions-section" class="d-flex flex-column">
                    <div id="default-purchase-restriction-block" class="d-flex flex-column py-30 align-items-center">
                        <h6 class="bsapp-fs-22 font-weight-normal mb-10"><?=lang('package_purchase_restrictions')?></h6>
                        <a class="btn btn-lg border border-secondary font-weight-bold shadow-none bsapp-fs-16"
                           onclick="externalPurchase.createFirstPurchaseRestrictions(this)"><?=lang('create_purchase_restrictions')?></a>
                    </div>
                    <div id="custom-purchase-restriction-blocks" class="d-none pb-10">
                        <div class="d-flex justify-content-between align-items-center px-15" id="purchase-restrictions-header">
                            <div class="py-15">
                                <span class="bsapp-fs-16 font-weight-bold"><?=lang('purchase_limit_shop_render')?></span>
                            </div>
                            <a class="btn btn-lg border border-secondary font-weight-bolder shadow-none bsapp-fs-14"
                               onclick="externalPurchase.addPurchaseRestriction(this)"><?=lang('add_purchase_restriction_block')?></a>
                        </div>
                        <div id="purchase-restriction-blocks-list">
                            <div class="row purchase-restriction-row-block m-0 mb-10" data-id=0>
                                <div class="col-1 align-items-center d-flex remove-button">
                                    <a role="button" onclick="externalPurchase.removeBlock(this)">
                                        <i class="remove-icon p-0 fal fa-trash-alt bsapp-fs-24"></i>
                                    </a>
                                </div>
                                <div class="col bsapp-rounded-8p mx-15 bg-white py-10 purchase-restriction-block shadow bg-white">
                                    <div class="row p-0 m-0 mb-10 px-15">
                                        <div class="is-invalid-container w-100">
                                            <select class="form-control js-select-shop-new purchase-restriction-type"
                                                    data-old-value="-1"
                                                    name="purchaseRestrictionType"
                                                    onchange="externalPurchase.purchaseRestrictionTypeChange(this)">
                                                <option data-short-text="<?=lang('age') ?>" value="1"><?=lang('age_restriction')?></option>
                                                <option data-short-text="<?=lang('table_gender') ?>" value="2"><?= lang('gender_restriction') ?></option>
                                                <option data-short-text="<?=lang('client_status') ?>" value="5"><?=lang('client_status_restriction') ?></option>
                                                <option data-short-text="<?=lang('tag') ?>" value="6"><?=lang('tag_restrictions') ?></option>
                                                <option data-short-text="<?=lang('seniority') ?>" value="3"><?=lang('seniority_restriction') ?></option>
                                                <option data-short-text="<?=lang('quantity_amount') ?>" value="4"><?=lang('quantity_limit') ?></option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row form-group flex-fill m-0 age-restriction restriction-more-details ">
                                        <div class="col px-15">
                                            <label class="font-weight-bold bsapp-fs-14 m-0"><?= lang('from_age') ?></label>
                                            <div class="">
                                                <input class="form-control border-gray-200 bsapp-rounded-8p m-0 py-2 form-age-restriction js-post-value"
                                                       inputmode="decimal" type="number" max=120 min=1 name="startAge"
                                                       onchange="externalPurchase.ageRestrictionChange(this)"
                                                       onKeyPress="return (this.value.length < 3 && (event.charCode >= 48 && event.charCode <= 57))"
                                                       placeholder="<?=lang('age') ?>">
                                            </div>
                                        </div>
                                        <div class="col px-15">
                                            <label class="font-weight-bold bsapp-fs-14 m-0"><?= lang('to_age') ?></label>
                                            <div class=>
                                                <input class="form-control border-gray-200 bsapp-rounded-8p m-0 py-2 to-age-restriction js-post-value"
                                                       type="number" max=120 min=1 name="endAge" inputmode="decimal"
                                                       onchange="externalPurchase.ageRestrictionChange(this)"
                                                       onKeyPress="return (this.value.length < 3 && (event.charCode >= 48 && event.charCode <= 57))"
                                                       placeholder="<?=lang('age') ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group flex-fill m-0 gender-restriction restriction-more-details d-none">
                                        <div class="col px-15">
                                            <label class="font-weight-bold bsapp-fs-14 m-0"><?= lang('table_gender') ?></label>
                                                <div class="">
                                                    <select class="form-control js-select-shop-new gender-restriction-input js-post-value" name="gender">
                                                        <option value=2><?= lang('women')?></option>
                                                        <option value=1><?= lang('men')?></option>
                                                        <option value=0><?= lang('other')?></option>
                                                    </select>
                                                </div>
                                        </div>
                                    </div>

                                    <div class="row form-group flex-fill m-0 client-status-restriction restriction-more-details d-none">
                                        <div class="col px-15">
                                            <label class="font-weight-bold bsapp-fs-14 m-0"><?= lang('choose_status') ?></label>
                                            <div>
                                                <select class="form-control js-select-shop-new client-status js-post-value" name="customerStatus">
                                                    <option value=1><?php echo lang('actives')?></option>
                                                    <option value=2><?php echo lang('interested')?></option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group flex-fill m-0 rank-restriction restriction-more-details d-none">
                                        <div class="col px-15">
                                            <label class="font-weight-bold bsapp-fs-14 m-0"><?= lang('tag_selection') ?></label>
                                            <div class="is-invalid-container">
                                                <select multiple="multiple" style="width:100%"
                                                        class="form-control w-100 js-select2-shop-new rank-restriction-input js-post-value" name="rank">
                                                    <?php
                                                    if ($levels) {
                                                        foreach ($levels as $level) {
                                                            echo "<option value='" . $level->id . "'>" . $level->Level . "</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group flex-fill m-0 seniority-restriction restriction-more-details d-none">
                                        <div class="col px-15">
                                            <label class="font-weight-bold bsapp-fs-14 m-0"><?= lang('only_client_join_before_date') ?></label>
                                                <div class="">
                                                    <input class="form-control form-control border-gray-200 bsapp-rounded-8p m-0 py-2 seniority-restriction-input js-post-value"
                                                           max="<?= date('Y-m-d');?>" placeholder="<?=lang('select_date_rest')?>" name="seniority" type="date">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row form-group flex-fill m-0 quantity-limit-restriction restriction-more-details d-none">
                                        <div class="col px-15">
                                            <label class="font-weight-bold bsapp-fs-14 m-0"><?=lang('limit_quantity_of_purchases_to')?></label>
                                                <div class="is-invalid-container">
                                                    <select class="form-control js-select-shop-new quantity-limit js-post-value" name="maxPurchase">
                                                        <?php for ($i = 1; $i <= 20; $i++) { ?>
                                                            <option value="<?=$i?>"> <?=$i . ' ' . lang('for_each_client')?></option>
                                                        <?php } ?>
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
            </div>
        </div>
    </div>
