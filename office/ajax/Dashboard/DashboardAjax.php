<?php
require_once '../../../app/init.php';
require_once '../../../app/views/headernew.php';
require_once "../../Classes/calendar.php";
require_once "../../Classes/Company.php";
$company = Company::getInstance(false);
$companyNum = $company->__get("CompanyNum");
$calendar = new calendar();
if(isset($_POST["fun"]) ){
    switch ($_POST["fun"] ) {
        case "TaskCompleted":
            $calendar->UpdareMissionStatus($companyNum,$_POST["id"],1);
            break;
        case "TaskCanceled":
            $calendar->UpdareMissionStatus($companyNum,$_POST["id"],2);
            break;
    }
}