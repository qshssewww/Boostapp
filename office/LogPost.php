<?php require_once '../app/initcron.php';
require_once __DIR__ . "/Classes/Client.php";
require_once __DIR__ . "/Classes/Utils.php";

if (Auth::userCan('116')):

    header('Content-Type: text/html; charset=utf-8');

    $CompanyNum = Auth::user()->CompanyNum;

    if (Auth::guest()) exit;

    $_REQUEST["month"] = !isset($_REQUEST["month"]) ? date("m") : $_REQUEST["month"];
    $_REQUEST["year"] = !isset($_REQUEST["year"]) ? date("Y") : $_REQUEST["year"];

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
    $prev_year = $temp[2];

    $OpenTables = DB::table('log')->where('CompanyNum', '=', $CompanyNum)->whereBetween('Dates', array($StartDate, $EndDate))->orderBy('id', 'DESC')->get();
    $Client = new Client();
    $result = $Client->getLogInfo($CompanyNum, $OpenTables, $StartDate, $EndDate);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);


endif;