<?php
require_once '../app/initcron.php';
require_once 'Classes/ClientActivities.php';
require_once 'Classes/Client.php';
require_once 'Classes/MembershipType.php';
header('Content-Type: text/html; charset=utf-8');
$CompanyNum = Auth::user()->CompanyNum;
if (Auth::guest()) exit;

$ClientActivities = new ClientActivities();

$results = $ClientActivities->getExpiringClients($CompanyNum);


$client = new Client();

foreach ($results as $key => $Client) {

    $cActivities = $ClientActivities->getActiveMemberships($CompanyNum, $Client);

    if ($cActivities > 0) {
        unset($results[$key]);
    }
}

$TableCount = count($results);
$jsonArr = array(
    "data" => array()
);

$number = $TableCount;

$ClientsIdUsed = [];
$countUsed = 0;
foreach($results as $Client) {
    $exist = false;
    foreach ($ClientsIdUsed as $used) {

        if ($used->ClientId == $Client->ClientId) {
            $exist = true;
            break;
        }
    }
    if(!$exist){
        $ClientsIdUsed[] = $Client;
        $countUsed++;
    }
}


$TableCount = $countUsed;
$number = $TableCount;
$ClientsIdUsed = [];
foreach ($results as $Client) {
    $exist = false;
    foreach ($ClientsIdUsed as $used) {

        if($used->ClientId == $Client->ClientId) {
            $exist= true;
            break;
        }
    }
    if(!$exist) {
        $ClientsIdUsed[] = $Client;
        $SameClients= [];

        $SameClients[] = $Client;
        foreach ($results as $key => $Clientcheck ){
            if ($Client->ClientId == $Clientcheck->ClientId && $Client->id != $Clientcheck->id) {
                $SameClients[] = $Clientcheck;
            }
        }

        if(!empty($Client->ClientId)) {

            $ClientID = $Client->ClientId;
            $ClientName = '<a href="/office/ClientProfile.php?u='.@$Client->ClientId.'" >'. $Client->FirstName .' '. $Client->LastName .'</a>';
            $Phone = $Client->ContactMobile;



            if ($Client->Status!='1'){

                $Type='';
                $Item='';
                $validityDate = '';

                foreach ($SameClients as $SC) {
                    $Type = $Type .  '<div class="d-block" >'.$SC->Type.'</div>';

                    $Item = $Item .'<div class="d-block">'. $SC->ItemText. '</div>';



                    if (isset($SC->TrueDate)){
                        $date = date_create($SC->TrueDate);

                        $validityDate = $validityDate  .'<div  class="d-block">' . date_format($date,"d/m/Y") . '</div>';
                    }
                }


                $dataArr = array($ClientID, $ClientName, $Phone, $Type, $Item, $validityDate, isset($balance) ? $balance : '');
                $jsonArr["data"][] = $dataArr;
            }
        } 
        $TableCount--;
    }
}
echo json_encode($jsonArr, JSON_UNESCAPED_UNICODE);


