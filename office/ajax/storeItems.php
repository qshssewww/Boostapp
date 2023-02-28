<?php
require_once '../../app/init.php';
require_once "../Classes/ClubMemberships.php";

header('Content-Type: text/html; charset=utf-8');

$companyNum = Company::getInstance()->__get('CompanyNum');

/**
 * @param $subMembership
 * @return array
 */
function processSubMembership($subMembership): array
{
    // type
    if ($subMembership->Department == 1) {
        $type = lang("periodic_payment");
        // check if standing order
        if (($subMembership->Payment == null && $subMembership->PaymentType == 4)
            || $subMembership->Payment == 2) {
            $type = lang("standing_order");
        }
    } else {
        $type = lang('class_tabe_card') . " (" . $subMembership->BalanceClass . ")";
    }

    // period
    $period = $subMembership->Vaild ?? 0;
    if ($period == 0) {
        $period = lang('without') . " " . lang('store_period');
    } else {
        switch ($subMembership->Vaild_Type) {
            case 1:
                $period .= " " . lang("days");
                break;
            case 2:
                $period .= " " . lang("weeks");
                break;
            case 3:
                $period .= " " . lang("months");
                break;
        }
    }


    return [
//        'id' => $subMembership->id,
        'type' => $type,
        'period' => $period,
        'price' => $subMembership->ItemPrice,
    ];
}

function printClubMembershipElement($elem)
{
    ?>
    <div class="card border-0<?php echo($elem['isDisabled'] ? ' card-disabled' : '') ?>"
         id="card<?php echo $elem['id'] ?>">
        <div class="card-header bg-white rounded border border-2 p-0" id="heading<?php echo $elem['id'] ?>">
            <div class="d-flex justify-content-end btn-block text-start collapsed p-0 position-relative" type="button"
                 data-toggle="collapse"
                 data-target="#collapse<?php echo $elem['id'] ?>" aria-expanded="true"
                 aria-controls="collapse<?php echo $elem['id'] ?>">
                <div class="d-block table-header-text table-pis-20 w-100">
                    <?php if ($elem['isIntro']): ?>
                        <span class="d-inline table-pie-10"><i class="fal fa-star-of-life"></i></span>
                    <?php endif; ?>
                    <?php echo strlen($elem['name']) > 70 ? substr($elem['name'],0,50)."..." : $elem['name']; ?>
                    <?php if (isset($elem['type'])): ?>
                        <span class="d-inline table-px-10">|</span>
                        <?php echo $elem['type'] ?>
                    <?php endif; ?>
                    <?php if (isset($elem['branch'])) : ?>
                        <span class="d-inline membership-branch-name"><?php echo "(" . $elem['branch'] . ")" ?></span>
                    <?php endif; ?>
                </div>
                <div class="d-flex align-items-center align-self-start table-line-controls position-absolute">
                    <div class="d-flex table-toggle-edit">
                        <div class="d-flex custom-control custom-switch" data-id="<?php echo $elem['id'] ?>">
                            <input type="checkbox" class="custom-control-input sliderCheckbox"
                                   data-id="<?php echo $elem['id'] ?>"
                                   id="customSwitch<?php echo $elem['id'] ?>" <?php echo($elem['isDisabled'] ? '' : 'checked') ?>>
                            <label class="custom-control-label cursor-pointer"
                                   for="customSwitch<?php echo $elem['id'] ?>"></label>
                        </div>
                    </div>
                    <div class="d-flex table-edit-arrow">
                        <div data-id="<?php echo $elem['id'] ?>" class="rowBox-item">
                            <i class="fal fa-edit"></i>
                        </div>
                    </div>
                    <div class="d-flex table-line-end">
                        <i class="fas fa-caret-down arrow-toggle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div id="collapse<?php echo $elem['id'] ?>"
             class="collapse"
             aria-labelledby="heading<?php echo $elem['id'] ?>">
            <div class="card-body d-flex flex-column p-0">
                <?php for ($i = 0, $iMax = count($elem['subs']); $iMax > $i; $i++): ?>
                    <div class="d-flex align-items-center table-pis-20 bg-light border-white rounded border border-2 mt-3 h-50p">
                        <div class="text-start table-width-number">
                            <?php echo $i + 1 . "."; ?>
                        </div>
                        <div class="text-start table-width-type">
                            <?php echo $elem['subs'][$i]['type']; ?>
                        </div>
                        <div class="text-start table-width-period">
                            <?php echo $elem['subs'][$i]['period']; ?>
                        </div>
                        <div class="text-start w-auto">
                            &lrm;<i class="fa fa-shekel-sign bsapp-fs-12 pr-4"></i><?php echo $elem['subs'][$i]['price']; ?>
                        </div>
<!--                        <div class="text-end flex-fill table-pie-10">-->
<!--                            id: --><?//= $elem['subs'][$i]['id']; ?>
<!--                        </div>-->
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
    <?php
}


if (Auth::guest()) exit;

if (Auth::userCan('31')) {
    $results = [];
    $indexes = [];

    $companyProductSettings = $companyProductSettings ?? (new CompanyProductSettings)->getSingleByCompanyNum($companyNum);
    $hasMemberships = isset($companyProductSettings) && $companyProductSettings->manageMemberships;
    // get club memberships
    $clubMemberships = (new ClubMemberships())->getAllClubMembershipsByCompany($companyNum);
    foreach ($clubMemberships ?? [] as $membership) {
        $indexes[$membership->id] = count($results);
        $results[] = [
            'id' => $membership->id,
            'name' => $membership->ClubMemberShipName,
            'type' => $hasMemberships ? $membership->type : null,
            'branch' => $membership->BrandName,
            'isIntro' => false,
            'isSingleItem' => false,
            'isDisabled' => $membership->Status == 2,
            'subs' => [],
        ];
    }

    // get sub-memberships
    $subMemberships = (new Item())->getAllSubItemsByCompanyNum($companyNum);
    foreach ($subMemberships ?? [] as $subMembership) {
        // fix intro status
        if ($subMembership->Department == 3) {
            $results[$indexes[$subMembership->ClubMembershipsId]]['isIntro'] = true;
        }

        $results[$indexes[$subMembership->ClubMembershipsId]]['subs'][] = processSubMembership($subMembership);
    }

    // get memberships without club memberships
    // TODO remove if don't need
//    $singleItems = (new Item())->getItemsWithoutClubMembershipByCompanyNum($companyNum);
//    foreach ($singleItems ?? [] as $singleItem) {
//        $results[] = [
//            'id' => $singleItem->id,
//            'name' => $singleItem->ItemName,
//            'type' => $singleItem->type,
//            'branch' => $singleItem->BrandName,
//            'isIntro' => $singleItem->Department == 3,
//            'isSingleItem' => true,
//            'isDisabled' => $singleItem->Disabled == 1,
//            'subs' => [[
//                processSubMembership($singleItem)
//            ]],
//        ];
//    }

    if (count($results) == 0): ?>
        <div class="text-center membership-type-header align-middle"><?php echo lang('no_data') ?></div>
    <?php endif;
    if (count(array_filter($results, function ($record) {
            return !$record['isIntro'];
        })) > 0): ?>
        <div class="d-flex text-start membership-type-header align-items-center h-50p table-pis-20">
            <div><?php echo lang('subscriptions') ?></div>
        </div>
        <div id="subscriptions" class="accordion text-start">
            <?php foreach (array_filter($results, function ($record) {
                return !$record['isIntro'] && !$record['isDisabled'];
            }) as $elem) {
                printClubMembershipElement($elem);
            } ?>
            <?php foreach (array_filter($results, function ($record) {
                return !$record['isIntro'] && $record['isDisabled'];
            }) as $elem) {
                printClubMembershipElement($elem);
            } ?>
        </div>
    <?php endif;
    if (count(array_filter($results, function ($record) {
            return $record['isIntro'];
        })) > 0): ?>
        <div class="d-flex text-start membership-type-header align-items-center h-50p table-pis-20">
            <div><?php echo lang('a_trial') ?></div>
        </div>
        <div id="trials" class="accordion text-start">
            <?php foreach (array_filter($results, function ($record) {
                return $record['isIntro'] && !$record['isDisabled'];
            }) as $elem) {
                printClubMembershipElement($elem);
            } ?>
            <?php foreach (array_filter($results, function ($record) {
                return $record['isIntro'] && $record['isDisabled'];
            }) as $elem) {
                printClubMembershipElement($elem);
            } ?>
        </div>
    <?php endif;
}
