<?php

require_once '../app/init.php';
require_once __DIR__."/Classes/Client.php";
require_once __DIR__."/Classes/ClientActivities.php";
require_once __DIR__."/Classes/ClassStudioAct.php";
require_once __DIR__.'/Classes/Pipereasons.php';

header('Content-Type: text/html; charset=utf-8');

if (Auth::guest()) {
    exit;
}
if (Auth::userCan('45')):

    $CompanyNum = Auth::user()->CompanyNum;
    $_GET['Act'] = $_GET['Act'] && in_array($_GET['Act'], [0,1]) ? $_GET['Act'] : 0;
    $OpenTables = (new Client())->getClientsByCompanyOrderByName($CompanyNum, $_GET['Act']);

    $resArr = array("data" => array());

    $clientIdArr = array();
        foreach ($OpenTables as $Client) {
            $clientIdArr[] = $Client->id;
        }

    $clientActivitiesArr = (new ClientActivities())->getClientActivByClientIdArray($CompanyNum, $clientIdArr);

    foreach ($OpenTables as $key => $Client) {
        $tempArr = array();
        $ClientId = $Client->id;
        $ContactMobile = $Client->ContactMobile ?? '';
        $Email = $Client->Email ?? '';
        $ReasonId = $Client->ArchiveReasonId;
        if($Client->Brands != 0) {
            $brand = (new Brand())->getBrandById($Client->Brands);
            $brandName = $brand->BrandName ?? lang('primary_branch');
        } else {
            $brandName = lang('primary_branch');
        }
        $showItFirst = false;
        $showIfNoActive = false;
        // query time is too long
//        $lastClass = ClassStudioAct::getLastActiveClass($Client->id, date('Y-m-d', strtotime($Client->LastClassDate)));
        foreach ($clientActivitiesArr as $clientActivities) {
            $MemberShipText = '';

            if ($clientActivities->ClientId == $ClientId || $clientActivities->TrueClientId == $ClientId) {
                if ($clientActivities->Department == '1') {
                    if ($clientActivities->TrueDate > date('Y-m-d')) {
                        $MemberShipText = '<span>'. $clientActivities->ItemText .'</span> 
                           <span class="text-success">'. date('d/m/Y', (strtotime($clientActivities->TrueDate))) . '</span>';
                        $showItFirst =true;

                    } else {
                        if (!$showIfNoActive) {
                            $MemberShipText = '<span>' . $clientActivities->ItemText .'</span> 
                           <span class="text-danger">'.  date('d/m/Y', (strtotime($clientActivities->TrueDate))) . '</span>';
                            $showIfNoActive = true;
                        }
                    }
                }
                if (in_array($clientActivities->Department, [2,3])) {
                    if ($clientActivities->TrueBalanceValue > 0) {
                        $MemberShipText = '<span>' . $clientActivities->ItemText . '</span> <span class="text-success"> ' .
                            lang('classes') . ": " . $clientActivities->TrueBalanceValue . '</span>';
                        $showItFirst =true;
                    }
                    if ($clientActivities->TrueBalanceValue <= 0 || (!empty($clientActivities->TrueDate) && $clientActivities->TrueDate < date('Y-m-d'))){
                        if (!$showIfNoActive) {
                            $trueDate = isset($clientActivities->TrueDate) ? date('d/m/Y', (strtotime($clientActivities->TrueDate))) : '';
                            $MemberShipText = '<span>' . $clientActivities->ItemText .'</span> <span class="text-danger"> '.
                                lang('classes') .": ".  $clientActivities->TrueBalanceValue . " ". $trueDate.'</span>' ;
                            $showIfNoActive = true;
                        }
                    }

                }
                if ($showItFirst){
                    break;}
            }
        }

        $tempArr[0] = $key + 1;
        $tempArr[1] = ($_GET['Act']==0) ?
            '<a class="text-success"  href="ClientProfile.php?u=' . $Client->id . '">
          <span class="fa-layers fa-fw text-success">'
            . $Client->CompanyName . ' </span></a>' : (

            $_GET['Act']==1 ?
            '<a class="text-danger"  href="ClientProfile.php?u=' . $Client->id . '">
          <span class="fa-layers fa-fw text-danger">'
            . $Client->CompanyName . ' </span></a>' :

                '<span class="text-info">'.lang('interested_single').'</span>'
            );


        $tempArr[2] = '<span class="unicode-plaintext">'. $ContactMobile .'</span> ';
        $tempArr[3] = $Email;
        if ($_GET['Act'] === 0) {
            $tempArr[4] = date('d/m/Y', (strtotime($Client->Dates)));
            $tempArr[5] = $MemberShipText ?? '';
            $tempArr[6] = isset($Client->LastClassDate) ? date('d/m/Y', strtotime($Client->LastClassDate)) : '';
            $tempArr[7] = $brandName ?? lang('primary_branch');
        } else {
            $tempArr[4] = isset($Client->ArchiveDate) ? date('d/m/Y', strtotime($Client->ArchiveDate)) : '';
            if(!empty($ReasonId)){
                $pipeReason = Pipereasons::find($ReasonId);
                $tempArr[5] = $pipeReason->Title;
            } else {
                $tempArr[5] = '';
            }
            $tempArr[6] = $brandName ?? lang('primary_branch');
        }

        array_push($resArr['data'], $tempArr);
    }

    echo json_encode($resArr, JSON_UNESCAPED_UNICODE);

endif;

