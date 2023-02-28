<?php

require_once '../app/initcron.php';
require_once 'Classes/ClientActivities.php';
require_once 'Classes/Client.php';

header('Content-Type: text/html; charset=utf-8');

$CompanyNum = Auth::user()->CompanyNum;

if (Auth::guest()) exit;

$clientActivities = new ClientActivities();

$invalidMemberships = $clientActivities->getInvalidMemberships($CompanyNum);
$companyActivitiesFilters = $clientActivities->getBulkActiveMemberships($CompanyNum);
$invalidMemberships = ClientActivities::filterActiveMemberships($invalidMemberships, $companyActivitiesFilters)['filters'];

//foreach ($invalidMemberships as $key => $InvildeMember) {
//    $MemberShipClients = $clientActivities->getActiveMemberships($CompanyNum, $InvildeMember);
//    if ($MemberShipClients > 0) {
//        unset($invalidMemberships[$key]);
//    }
//}
//$invalidMemberships = array_values($invalidMemberships); // 'reindex' array

$OpenTableCount = count($invalidMemberships);

$jsonArr = array(
    "data" => array()
);

$ClientsIdUsed = [];
//$countUsed = 0;
//foreach ($invalidMemberships as $Client) {
//    $exist = false;
//    foreach ($ClientsIdUsed as $used) {
//
//        if ($used->ClientId == $Client->ClientId) {
//            $exist = true;
//            break;
//        }
//    }
//    if (!$exist) {
//        $ClientsIdUsed[] = $Client;
//        $countUsed++;
//    }
//}
//$ClientsIdUsed = [];
foreach ($invalidMemberships as $Client) {
    $exist = false;
    foreach ($ClientsIdUsed as $used) {

        if ($used->ClientId == $Client->ClientId) {
            $exist = true;
            break;
        }
    }

    if (!$exist) {
        $ClientsIdUsed[] = $Client;
        $SameClients = [];

        $SameClients[] = $Client;
        foreach ($invalidMemberships as $key => $Clientcheck) {
            if ($Client->ClientId == $Clientcheck->ClientId && $Client->id != $Clientcheck->id) {
                $SameClients[] = $Clientcheck;
            }
        }

        /* @var $ClientUserNameLogs Client */
        $ClientUserNameLogs = Client::find($Client->ClientId);
        if (!$ClientUserNameLogs) {
            continue;
        }

        $ClientUserNameLog2 = $ClientUserNameLogs->CompanyName ?? '';
        $textBtn = 'הסר מהדוח';
        $ClientUserNameLog = '<a href="/office/ClientProfile.php?u=' . $ClientUserNameLogs->id . '" >' . $ClientUserNameLog2 . '</a>';
        $ClientUserStatusBtn = '<button data-user-id="' . $ClientUserNameLogs->id . '" type="button" class="status btn btn-dark text-white">' . $textBtn . '</button>';
        if ((int)$ClientUserNameLogs->Status !== 1) {
            $Type = '';
            $ItemText = '';
            $Times = '';
            $TrueBalanceValue = '';
            foreach ($SameClients as $SC) {
                $TempItemText = $SC->ItemText;

                if ($SC->Department == '1') {
                    $Type = $Type . '<div class="d-block">' . $SC->Type . '</div>';
                    $ItemText = $ItemText . '<div class="d-block">' . $TempItemText . '</div>';

                } else if ($SC->Department == '2') {
                    if ($SC->BalanceValue == 1) {
                        $Type = $Type . '<div class="d-none">' . $SC->Type . '</div>';
                        $TrueBalanceValue = $TrueBalanceValue . '<div class="d-none">' . $SC->TrueBalanceValue . '</div>';
                        $ItemText = $ItemText . '<div class="d-none">' . $TempItemText . '</div>';
                    } else {
                        $Type = $Type . '<div class="d-block">' . $SC->Type . '</div>';
                        $TrueBalanceValue = $TrueBalanceValue . '<div class="d-block">' . $SC->TrueBalanceValue . '</div>';
                        $ItemText = $ItemText . '<div class="d-block">' . $TempItemText . '</div>';

                    }
                } else if ($SC->Department == '3') {
                    if ($SC->BalanceValue == 1) {
                        $Type = $Type . '<div class="d-none">' . $SC->Type . '</div>';
                        $TrueBalanceValue = $TrueBalanceValue . '<div class="d-none">' . $SC->TrueBalanceValue . '</div>';
                        $ItemText = $ItemText . '<div class="d-none">' . $TempItemText . '</div>';
                    } else {
                        $Type = $Type . '<div class="d-block">' . $SC->Type . '</div>';
                        $TrueBalanceValue = $TrueBalanceValue . '<div class="d-block">' . $SC->TrueBalanceValue . '</div>';
                        $ItemText = $ItemText . '<div class="d-block">' . $TempItemText . '</div>';

                    }
                } else if ($SC->Department == '4') {
                    $Type = $Type . '<div>' . $SC->Type . '</div>';
                }

                if ($SC->TrueDate != '') {
                    $TimeT = (new DateTime($SC->TrueDate))->format('d/m/Y');


                    if ($SC->BalanceValue == 1) {
                        $Times = $Times . '<div  class="d-none">' . $TimeT . '</div>';
                    } else {
                        $Times = $Times . '<div  class="d-block">' . $TimeT . '</div>';


                    }
                }

            }

            $dataArr = array(@$Client->ClientId, @$ClientUserNameLog, @$ClientUserNameLogs->ContactMobile, @$Type, @$ItemText, $Times, $TrueBalanceValue, $ClientUserStatusBtn);
            $jsonArr["data"][] = $dataArr;
        }
    }
}

echo json_encode($jsonArr, JSON_UNESCAPED_UNICODE);

