<?php
$companyProductSettings = $companyProductSettings ?? (new CompanyProductSettings)->getSingleByCompanyNum(Auth::user()->CompanyNum);
$hasBrands = Count($company->getBrands()) > 1;
$hasMemberships = isset($companyProductSettings) && $companyProductSettings->manageMemberships && $company->getMembershipTypes() > 0;

$classTypeLessonOptions = '';
$classTypeMeetingsOptions = '';
$classTypeRoomsOptions = '';
$firstClassFlag = false;
$firstRoomFlag = false;
$categoryArray = [];
$meetingsArray =[];

foreach ($ClassTypeForSelect as $classType) {
    switch ($classType->EventType) {
        case '0' :
            if(!$firstClassFlag) {
                $classTypeLessonOptions .= '<optgroup class="optgroup-class" label="'. lang('desk_new_classes') . '"><option value="_all_class">' . lang('all_classes') . '</option>';
                $firstClassFlag = true;
            }
            $classTypeLessonOptions .= '<option value="' . $classType->id . '">' . $classType->Type . '</option>';
            break;
        case '1':
            // close classes if opened
            if($firstClassFlag) {
                $classTypeLessonOptions.= '</optgroup>';
                $firstClassFlag = false;
            }
            if (!in_array( $classType->category_id  ,$categoryArray )) {
                //first category in meeting
                if(empty($categoryArray)) {
                    $classTypeMeetingsOptions .= '<optgroup class="optgroup-meeting"  label="'. lang('cal_appointments') . '"><option value="_all_meet">' . lang('meeting_all') . '</option>';
                }
                $categoryArray[] = $classType->category_id;
                $classTypeMeetingsOptions .= '<option value="" disabled>' . $classType->CategoryName . '</option>';
            }
            if (!in_array( $classType->MeetingTemplateId  ,$meetingsArray )) {
                $meetingsArray[] = $classType->MeetingTemplateId;
                $classTypeMeetingsOptions .= '<option value="' . 'me-' .$classType->MeetingTemplateId .'" data-multi-label="' .'me-' .$classType->MeetingTemplateId .'">' . $classType->TemplateName . '</option>';
            }
            $classTypeMeetingsOptions .= '<option value="' .$classType->id .'" data-multi="' . 'me-' .$classType->MeetingTemplateId .'">' . $classType->TemplateName . " " . $classType->Type . '</option>';
            break;
        case '2':
            // close classes if opened
            if($firstClassFlag) {
                $classTypeLessonOptions.= '</optgroup>';
                $firstClassFlag = false;
            }
            // close Meetings Options if opened
            if(!empty($categoryArray)) {
                $classTypeMeetingsOptions.= '</optgroup>';
            }
            //open optgroup of rooms
            if(!$firstRoomFlag) {
                $classTypeRoomsOptions .= '<optgroup class="optgroup-space" label="'. lang('entrance_to_complexes') . '"><option value="_all_space">' . lang('all_spaces') . '</option>';
                $firstRoomFlag = true;
            }
            $classTypeRoomsOptions .= '<option value="' . $classType->id . '">' . $classType->Type . '</option>';
            break;
        default:
            break;
    }
}
// close rooms Options if opened
if($firstRoomFlag) {
    $classTypeRoomsOptions .= '</optgroup>';
}
unset($categoryArray,$meetingsArray,$firstRoomFlag,$firstClassFlag);
?>


<div class="popupWrapper modal-dialog-centered " id="create-club-memberships-popup" style="text-align: start">
    <form class="add-club-memberships"  method="post"
          onsubmit="createClubMemberships.submitForm(this, event)" novalidate>
        <input type="hidden" class="js-post-value isTrial" name="isTrial" value="0">
        <div class="popupContainer smPopup scaleUp bsapp-max-w-420p p-0" style="overflow: hidden;text-align: initial;">
            <div class="js-tab js-tab-home animated flex-column h-100 w-100 overflow-hidden fadeIn d-flex" data-depth=1 data-herf="create-club-memberships-home">
                    <!-- modal header start -->
                    <div class="d-flex justify-content-between align-items-center border-bottom border-light px-15 bsapp-max-h-55p">
                        <div class="py-15">
                            <span class="bsapp-fs-18 font-weight-bold form-title"><?=lang('new_membership_title')?></span>
                        </div>
                        <a  href="javascript:;" class="newCalendarPopupCloseTimes toggleClosePopup text-dark"
                            data-target="create-club-memberships-popup" style="font-size:1.5rem;">
                            <i class="fal fa-times"></i>
                        </a>
                    </div>
                    <!-- modal body start -->
                    <div class="sub-tab-body-main bsapp-scroll d-flex flex-column">
                        <div id="basic-details-section">
                            <input type="hidden" class="js-post-value club-memberships-id" name="id">
                            <div class="d-flex px-15">
                                <div class="form-group flex-fill my-15 mie-15 ">
                                    <label class="font-weight-bold "><?= lang('subscription_name') ?></label>
                                    <div class="is-invalid-container">
                                        <input name="ClubMemberShipName" maxlength="50" placeholder="<?=lang('free_string')?>"
                                               class="form-control form-control-big border-gray-200 membership-name js-post-value"
                                               onKeyPress="return event.which != 45"
                                               type="text" required autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <?php if($hasBrands || $hasMemberships) { ?>
                                <div class="border border-light">
                                    <div class="row form-group flex-fill mt-10 mb-15 mie-15 mx-0">
                                        <?php if ($hasBrands) { ?>
                                            <div class="col px-15">
                                                <label class="font-weight-bold"><?= lang('branch') ?></label>
                                                <div class="is-invalid-container">
                                                    <select class="form-control form-control-big js-select2-shop-new brands js-post-value" name="BrandsId">
                                                        <option value="BA999"><?php echo lang('all_branch') ?></option>
                                                        <?php foreach ($company->getBrands() as $brand) { ?>
                                                            <option value="<?=$brand->__get("id") ?>"> <?=$brand->__get("BrandName") ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <?php if($hasMemberships){ ?>
                                            <div class="col px-15">
                                            <label class="font-weight-bold"><?= lang('membership_type_single') ?></label>
                                            <div class="is-invalid-container">
                                                <select name="MemberShipTypeId" class="form-control form-control-big js-select2-shop-new membershipType w-100 js-post-value ">
                                                    <?php
                                                    foreach ($company->getMembershipTypes() as $membershipType) {
                                                        echo '<option value="' . $membershipType->__get('id') . '">' . $membershipType->__get('Type') . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <div id="dynamic-blocks-details-section">
                            <div class="position-relative w-100 d-flex justify-content-between align-items-center px-15 pt-7 pb-5 border-bottom border-light item-roles-information-display">
                                <div class="">
                                    <div class="mb-7 font-weight-bold bsapp-fs-14"><?=lang('new_membership_restrictions_title')?></div>
                                    <div class="d-flex align-items-center mb-3 mt-7" style="line-height: 1;">
                                        <i class="mie-5 fal fa-walking bsapp-min-w-30p bsapp-fs-26" style="text-align:center;"></i>
                                        <div class="d-flex flex-column">
                                            <div class="js-preview-title bsapp-fs-18 mb-1">
                                                3 סוגי שיעורים, פעמיים בשוע
                                            </div>
                                            <div class="js-preview-sub-title bsapp-fs-14 text-muted">כאן נכניס משהו מוחץ</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="">
                                    <i class="fal fa-angle-right bsapp-fs-28"></i>
                                    <a class="stretched-link"onclick="shopMaim.switchTab(this)"  data-target="entry-and-registration-restrictions" role="button"></a>
                                </div>
                            </div>
                            <div class="position-relative w-100 d-flex justify-content-between align-items-center px-15 pt-7 pb-5 border-bottom border-light external-purchase-information-display">
                                <div class="">
                                    <div class="mb-7 font-weight-bold bsapp-fs-14"><?=lang('external_purchase_membership')?></div>
                                    <div class="d-flex align-items-center mb-3 mt-7" style="line-height: 1;">
                                        <i class="mie-5 fal fa-eye-slash bsapp-min-w-30p bsapp-fs-26 information-display-icon"></i>
                                        <div class="d-flex flex-column">
                                            <div class="js-preview-title bsapp-fs-18 mb-1">
                                                <?=lang('not_display_to_purchase_in_app')?>
                                            </div>
                                            <div class="js-preview-sub-title bsapp-fs-14 text-muted">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="">
                                    <i class="fal fa-angle-right bsapp-fs-28"></i>
                                    <a class="stretched-link js-show-slide" onclick="shopMaim.switchTab(this)" data-target="external-purchase" role="button"></a>
                                </div>
                            </div>
                        </div>
                        <div id="registration-packages-section" class=" d-flex flex-column flex-grow-1"
                             style="background:#F3F3F4;">
                            <div class="d-flex justify-content-between align-items-center px-15" id="registration-packages-header">
                                <div class="py-15">
                                    <span class="bsapp-fs-16 font-weight-bold"><?=lang('packages_new_membership')?></span>
                                </div>
                                <a class="btn btn-lg border border-secondary font-weight-bolder shadow-none bsapp-fs-14"
                                   onclick="createClubMemberships.addPackage(this)"><?=lang('add_new_package')?></a>
                            </div>
                            <div id="registration-packages-list" class="bsapp-scroll">
                                <div class="row package-row-block m-0" data-id=0>
                                    <input type="hidden" class="js-post-value item-id" name="id">
                                    <div class="col-1 align-items-center d-none remove-button">
                                        <a role="button" onclick="createClubMemberships.removePackage(this)">
                                            <i class="remove-icon p-0 fal fa-trash-alt bsapp-fs-24"></i>
                                        </a>
                                    </div>
                                    <div class="col bsapp-rounded-8p m-10 bg-white py-10 package-block">
                                        <div class="row p-0 m-0 mb-10 px-15">
                                            <div class="is-invalid-container w-100">
                                                <select class="form-control js-select-shop-new department-type js-post-value" name="Department"
                                                        onchange="createClubMemberships.departmentTypeChange(this)">
                                                    <option value=1><?= lang('cycle_membership')?></option>
                                                    <option value=2><?= lang('class_tabe_card')?></option>
                                                    <option value=5><?= lang('standing_order_cycle_charge')?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row p-0 m-0 price-elem">
                                            <div class="col p-0 m-0">
                                                <h6 class="text-gray-700 text-start font-weight-bolder mb-5 px-15">
                                                    <?=lang('price') ?>
                                                </h6>
                                            </div>
                                            <div class="col p-0 m-0 expire-elem">
                                                <h6 class="text-gray-700 text-start font-weight-bolder mb-5 px-15">
                                                    <?=lang('expires_at') ?>
                                                </h6>
                                            </div>
                                            <div class="col-4 p-0 m-0 entries-item d-none">
                                                <h6 class="text-gray-700 text-start font-weight-bolder mb-5 px-15">
                                                    <?=lang('entries') ?>
                                                </h6>
                                            </div>
                                        </div>
                                        <div class="row p-0 m-0">
                                            <div class="col p-0 m-0 px-15 price-elem">
                                                <div class="position-relative ">
                                                    <input inputmode="decimal" type="number" onchange="createClubMemberships.setTwoNumberDecimal(this)"
                                                           max=999999 min=0 step="0.01" name="ItemPrice" required
                                                           onKeyPress="if(this.value.length==8) return false;"
                                                           class="form-control border-gray-200 bsapp-rounded-8p m-0 py-2 item-price js-post-value"
                                                            placeholder="<?=lang('price') ?>">
                                                    <span class="position-absolute" style="top:6px;left:5px;">₪</span>
                                                </div>
                                            </div>
                                            <div class="col p-0 m-0 px-15 expire-elem expire-elem-cycle">
                                                <select class="form-control js-select-shop-new validity-membership js-post-value"
                                                        onchange="createClubMemberships.validityMembershipCycleChange(this)"
                                                        data-old-value="-1" name="validityMembership">
                                                    <?php
                                                    $timeArray = [
                                                        'days' => 10,
                                                        'weeks' => 10,
                                                        'months' => 14,
                                                    ];
                                                    $typeTime = 1;
                                                    foreach ($timeArray as $time => $value) {
                                                        for ($i = 1; $i <= $value; $i++) {
                                                            $i = ($i === 12)  ? 18 : $i ;
                                                            $intervalText =  $i . ' ' . lang($time); ?>
                                                            <option data-text="<?=$intervalText?>" value="<?=$i .'-'.$typeTime ?>">
                                                                <?=$intervalText ?>
                                                            </option>
                                                            <?php
                                                        }
                                                        $typeTime++;
                                                    }
                                                    ?>
                                                    <option data-text="<?=lang('year_js')?>" value="<?='12-3'?>">
                                                        <?=lang('year_js')?>
                                                    </option>
                                                    <option data-text="<?='2 ' . lang('years')?>" value="<?='24-3'?>">
                                                        <?='2 ' . lang('years')?>
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col p-0 m-0 px-15 expire-elem expire-elem-card d-none">
                                                <select class="form-control js-select-shop-new validity-membership js-post-value"
                                                        onchange="createClubMemberships.validityMembershipCardChange(this)"
                                                        data-old-value="-1" name="validityMembership">
                                                    <option data-text=<?=lang('without')?> value='0-1'>
                                                        <?=lang('without')?>
                                                    <?php
                                                    $timeArray = [
                                                        'days' => 10,
                                                        'weeks' => 10,
                                                        'months' => 14,
                                                    ];
                                                    $typeTime = 1;
                                                    foreach ($timeArray as $time => $value) {
                                                        for ($i = 1; $i <= $value; $i++) {
                                                            $i = ($i === 12)  ? 18 : $i ;
                                                            $intervalText =  $i . ' ' . lang($time); ?>
                                                            <option data-text="<?=$intervalText?>" value="<?=$i .'-'.$typeTime ?>">
                                                                <?=$intervalText ?>
                                                            </option>
                                                            <?php
                                                        }
                                                        $typeTime++;
                                                    }
                                                    ?>
                                                    <option data-text="<?=lang('year_js')?>" value="<?='12-3'?>">
                                                        <?=lang('year_js')?>
                                                    </option>
                                                    <option data-text="<?=lang('two_years')?>" value="<?='24-3'?>">
                                                        <?=lang('two_years')?>
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-4 p-0 m-0 px-15 entries-item d-none">
                                                <select class="form-control js-select-shop-new restriction-by-entries js-post-value" data-old-value="-1"
                                                        onchange="createClubMemberships.restrictionByEntriesChange(this)" name="BalanceClass">
                                                    <?php
                                                    for ($i = 1; $i <= 144; $i++) {
                                                        ?>
                                                        <option value="<?=$i?>"> <?=$i?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- modal content end -->
                    <div class="d-flex justify-content-end border-top border-light px-15 py-15">
                        <button type="button" class="btn btn-outline-secondary mie-12 px-30 toggleClosePopup"
                                data-target="create-club-memberships-popup"><?=lang('action_cacnel')?></button>
                        <button type="submit" class="btn btn-primary px-30"><?= lang('save') ?></button>
                    </div>
                </div>
            <div class="js-tab registration-restrictions-tab position-relative flex-column d-none flex-column overflow-hidden h-100 w-100 animated slideInStart"
                 data-depth=2 data-herf="entry-and-registration-restrictions">
                <?php include "registration-restrictions-tab.php"; ?>
            </div>


            <div class="js-tab external-purchase-tab flex-column d-none flex-column overflow-hidden h-100 w-100 animated slideInStart"
                 data-depth=2 data-herf="external-purchase">
                <?php include "external-purchase-tab.php"; ?>
            </div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function () {
        createClubMemberships.init();
        $(".js-select2-shop-new:not(.select2-hidden-accessible)").select2({
            minimumResultsForSearch: -1,
            theme: "bsapp-dropdown bsapp-outline-gray-300"
        });
    });
</script>


<?php
unset($classTypeLessonOptions, $classTypeMeetingsOptions, $classTypeRoomsOptions, $hasBrands, $hasMemberships);
?>



