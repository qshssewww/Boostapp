<?php
require_once '../app/initcron.php';

if (Auth::guest()) exit;

if (Auth::userCan('116')) {
    header('Content-Type: text/html; charset=utf-8');
    $CompanyNum = Auth::user()->CompanyNum;

    if (!isset($_REQUEST['Dates'])){
        if (isset($_REQUEST['month']) && isset($_REQUEST['year'])){
            $cMonth = $_REQUEST['month'];
            $cYear = $_REQUEST['year'];
        }
        else {
            $cMonth = date('m');
            $cYear = date('Y');
            $Dates = date('Y-m');
        }
    }

    else {

        $Dates = $_REQUEST['Dates'];
        $cMonth = with(new DateTime($_REQUEST['Dates']))->format('m');
        $cYear = with(new DateTime($_REQUEST['Dates']))->format('Y');

    }

    $prev_year = $cYear;
    $next_year = $cYear;
    $prev_month = $cMonth-1;
    $next_month = $cMonth+1;

    if ($prev_month == 0 ) {
        $prev_month = 12;
        $prev_year = $cYear - 1;
    }
    if ($next_month == 13 ) {
        $next_month = 1;
        $next_year = $cYear + 1;
    }

    $StartDate = $cYear.'-'.$cMonth.'-01';
    $EndDate = $next_year.'-'.$next_month.'-01';

    $OpenTables = DB::table('boostapplogin.log')
        ->leftJoin('boostapp.client as c', 'log.ClientId', '=', 'c.id')
        ->select('log.*', 'c.CompanyName')
        ->where('log.CompanyNum', '=', $CompanyNum)
        ->whereBetween('log.Dates', array($StartDate, $EndDate.' 00:00:00'))
        ->get();

    $OpenTableCount = count($OpenTables);

    $data = array("data" => array());

    foreach ($OpenTables as $key => $Client) {
        if (empty($Client->ClientId)) {
            $ClientLink = 'לא מוגדר';
        } else {
            $ClientLink = '<a href="ClientProfile.php?u=' . $Client->ClientId . '" >' . $Client->CompanyName . '</a>';
        }

        $arr = array(
            $key + 1,
            $Client->Text,
            date('d/m/Y', strtotime($Client->Dates)),
            date('H:i:s', strtotime($Client->Dates)),
            $ClientLink
        );
        $data["data"][] = $arr;
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}




