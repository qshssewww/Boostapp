<?php

require_once '../../app/init.php';
require_once '../Classes/Client.php';
require_once '../Classes/Brand.php';
require_once '../Classes/ClientActivities.php';
require_once '../Classes/ClassStatus.php';
require_once '../Classes/Users.php';

$dateFrom = !empty($_POST['start']) ? $_POST['start'] : date("Y-m-d", strtotime("-7 days"));
$dateTo = !empty($_POST['end']) ? $_POST['end'] : date("Y-m-d");
$companyNum = $_POST['companyNum'] ?? '';

//create brand array
$brandIdToNameArray= [0 => lang('primary_branch')];
$brandsArray = Brand::getBrandsByCompany($companyNum);
if(!empty($brandsArray)) {
    foreach ($brandsArray as $Brand){
        $brandIdToNameArray[$Brand->id] = $Brand->BrandName?? "";
    }
}
//create status of classes array
$classStatusesIdToTitleArray = [];
$classStatuses = ClassStatus::getAllStatusesInSystem();
if(!empty($classStatuses)) {
    foreach ($classStatuses as $ClassStatus){
        $classStatusesIdToTitleArray[$ClassStatus->id] = $ClassStatus->Title;
    }
}

//create coaches array
$coachIdToNameArray = [];
$coaches = Users::getAllCoachesByCompanyNum($companyNum);
if(!empty($coaches)) {
    foreach ($coaches as $User){
        $coachIdToNameArray[$User->id] = $User->display_name ?? 'שם מדריך לא נמצא';
    }
}
//get all data
$attendanceReportData = ClassStudioAct::getAttendanceReportDataBetweenDates($dateFrom, $dateTo, $companyNum);

$resArr = ["data" => []];
foreach ($attendanceReportData as $Task) {
    if($Task->StatusCount != 0) {
        continue;
    }
    $reportArray = array();
    //find client id and client details
    $clientId = $Task->FixClientId ?? $Task->ClientId ?? 0;
    if($clientId === 0 || !isset($Task->ItemName)){
        continue;
    }
    $membershipName = ClientActivities::getMembershipAttendanceReport($Task->ItemName, $Task->Department??1, $Task->TrueDate??'', $Task->TrueBalanceValue??'');

    $reportArray[0] = $clientId;
    $reportArray[1] = '<a href="/office/ClientProfile.php?u='.$clientId.'">'.$Task->DisplayName.'</a>';
    $reportArray[2] = $brandIdToNameArray[$Task->ClientBrand] ?? '';
    $reportArray[3] = $Task->PhoneNumber ?? "";
//    $reportArray[4] = $Task->Email ?? "";
    $reportArray[4] = $membershipName;
    $reportArray[5] = $Task->ClassName ?? "";
    $reportArray[6] = date("d/m/Y", strtotime($Task->ClassDate));
    $reportArray[7] = date("H:i", strtotime($Task->ClassStartTime));
    $reportArray[8] = $classStatusesIdToTitleArray[$Task->Status] ?? lang('not_found');
    $reportArray[9] = $coachIdToNameArray[$Task->GuideId] ?? '';
    array_push($resArr["data"], $reportArray);
}

echo json_encode($resArr, JSON_UNESCAPED_UNICODE);
