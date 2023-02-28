<?php
header('Content-Type: application/json');
require_once '../../app/init.php';

require_once "../Classes/ClassCalendar.php";
require_once "../Classes/Company.php";
$ClassCalendar = new ClassCalendar();

$company = Company::getInstance(false);
$companyNum = $company->__get("CompanyNum");

$ClassesAct = $ClassCalendar->getClassesAct($companyNum);


echo json_encode(array('ClassesAct' => $ClassesAct));