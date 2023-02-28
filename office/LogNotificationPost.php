<?php
require_once '../app/initcron.php';
require_once __DIR__ . "/Classes/Client.php";
require_once __DIR__ . "/Classes/Utils.php";

if (Auth::userCan('116')):

    header('Content-Type: text/html; charset=utf-8');

    $CompanyNum = Auth::user()->CompanyNum;

    if (Auth::guest()) exit;

    if (!isset($_REQUEST["month"])) $_REQUEST["month"] = date("m");
    if (!isset($_REQUEST["year"])) $_REQUEST["year"] = date("Y");

    if (!isset($_REQUEST['Dates'])) {
        $cMonth = $_REQUEST["month"];
        $cYear = $_REQUEST["year"];
        $Dates = $_REQUEST["year"] . '-' . $_REQUEST["month"];
    } else {
        $Dates = $_REQUEST['Dates'];
        $cMonth = with(new DateTime($_REQUEST['Dates']))->format('m');
        $cYear = with(new DateTime($_REQUEST['Dates']))->format('Y');
    }
    $Dates = new Utils();
    $temp = $Dates->nextAndPreviousMonthChecking($cMonth, $cYear);
    $StartDate = $temp[0];
    $EndDate = $temp[1];

    $OpenTables = DB::table('appnotification')->where('CompanyNum', '=', $CompanyNum)->where('Type', '=', '3')->whereBetween('Date', array($StartDate, $EndDate))->orderBy('Date', 'DESC')->orderBy('Time', 'DESC')->get();
    $Client = new Client();
    $result = $Client->getLogInfo($CompanyNum, $OpenTables, $StartDate, $EndDate);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);


endif;










