<?php
require_once '../../app/initcron.php';
require_once '../Classes/Utils.php';

if (Auth::guest()) exit;
$CompanyNum = Auth::user()->CompanyNum;

$Dates = $_GET['InputDate'];

$companyNum = Auth::user()->CompanyNum;

$guide = $_GET['Guide'] ?? '';

if ($guide == ''){
    $allDaysOfMonth[]=(array(lang('total'),'','',0));
}
else {
    $entrances = DB::table('timekeeper')
        ->where('CompanyNum', '=', $CompanyNum)
        ->where('UserId', '=', $guide)
        ->where(DB::raw('DATE_FORMAT(Dates, "%Y-%m")'), '=', $Dates)
        ->orderBy('Times')
        ->get();


    $startDate = strtotime($Dates . '-01');
    $endDate = strtotime("+1 month", $startDate);
    $allDaysOfMonth = [];
    $hebrewDays = array(
        7 => "ראשון",
        1 => "שני",
        2 => "שלישי",
        3 => "רביעי",
        4 => "חמישי",
        5 => "שישי",
        6 => "שבת"
    );

    $noTimeEntry = '<div><span class="d-block js-string-time js-studiouser-entry">' . "0" . '</span> 
                     <span class = "js-input-time d-none"><input type="time" name="appt-time" value=""> 
                             <i class="fal fa-minus-circle fa-xs text-danger" role="button"></i>
                        <i class="fal fa-plus-circle fa-xs text-success" role="button"></i>
                     </span></div>';
    $noTimeExit = '<div><span class="d-block js-string-time js-studiouser-exit">' . "0" . '</span> 
                     <span class = "js-input-time d-none"><input type="time" name="appt-time" value=""> 
                             <i class="fal fa-minus-circle fa-xs text-danger" role="button"></i>
                        <i class="fal fa-plus-circle fa-xs text-success" role="button"></i>
                     </span></div>';
    for ($i = $startDate; $i < $endDate; $i += 86400) {
        $dayOfWeekHe = $hebrewDays[date('N', $i)];
        $dateHeWithIcon = '<div class="bsapp-changed-time" data-value="' . date(
                'Y-m-d',
                $i
            ) . '" data-guide="' . $guide . '" data-company = "' . $companyNum . ' ">'
            . date(
                'd/m',
                $i
            ) . ' - ' . $dayOfWeekHe . '&nbsp <button class = "btn btn-success btn-rounded btn-sm js-savebtn d-none"></button></div>';
        $allDaysOfMonth[date('Y-m-d-D', $i)] = [
            $dateHeWithIcon, //date
            $noTimeEntry, //entry
            $noTimeExit,//exit
            '00:00'
        ];//total time
    }
    $arrayForTotal = array();
    $twoEntries = '';


    foreach ($entrances as $entrance) {
        $day = $allDaysOfMonth[date('Y-m-d-D', strtotime($entrance->Dates))];
        $entry = $day[1] == $noTimeEntry ? '' : $day[1];
        $exit = $day[2] == $noTimeExit ? '' : $day[2];
        $total = $day[3];
        if ($entrance->Act == 0) {
            $entry .= '<div><span class="d-block js-string-time js-studiouser-entry">' . date(
                    'H:i',
                    strtotime(
                        $entrance->Times
                    )
                ) .
                '</span><span class = "js-input-time d-none"><input type="time" name="appt-time" value="' . date(
                    'H:i',
                    strtotime(
                        $entrance->Times
                    )
                ) . '">
<i class="fal fa-minus-circle fa-xs text-danger" role="button"></i>
<i class="fal fa-plus-circle fa-xs text-success" role="button"></i></span></div>';
        } else {
            $exit .= '<div><span class="d-block js-string-time js-studiouser-exit">' . date(
                    'H:i',
                    strtotime($entrance->Times)
                ) .
                '</span><span class = "js-input-time d-none"><input type="time" name="appt-time" value="' . date(
                    'H:i',
                    strtotime(
                        $entrance->Times
                    )
                ) . '">
<i class="fal fa-minus-circle fa-xs text-danger" role="button"></i>
<i class="fal fa-plus-circle fa-xs text-success" role="button"></i></span></div>';
        }
        $obj = new stdClass();
        $obj->act = $entrance->Act;
        $obj->Times = $entrance->Times;
        if (empty($twoEntries)) {
            $twoEntries = array($entrance->Dates, $obj);
        } elseif (count($twoEntries) == 2) {
            if ($twoEntries[0] == $entrance->Dates) {
                $twoEntries[2] = $obj;
                $arrayForTotal[] = $twoEntries;
                $twoEntries = '';
            } else {
                $arrayForTotal[] = $twoEntries;
                $twoEntries = array($entrance->Dates, $obj);
            }
        }

        if($entry == "") {$entry = $noTimeEntry;}
        $allDaysOfMonth[date('Y-m-d-D', strtotime($entrance->Dates))][1] = $entry;


        if($exit == "") {$exit = $noTimeExit;}
        $allDaysOfMonth[date('Y-m-d-D', strtotime($entrance->Dates))][2] = $exit;

        $allDaysOfMonth[date('Y-m-d-D', strtotime($entrance->Dates))][3] = $total;
    }

    if (!empty($twoEntries) && count($twoEntries) == 2) {
        $arrayForTotal[] = $twoEntries;
    }


//new loop to control total result better
    $utils = new Utils();
    foreach ($arrayForTotal as $pair) {
        if ($allDaysOfMonth[date('Y-m-d-D', strtotime($pair[0]))][3] == lang('error_no_info')) {
            continue;
        } elseif (count($pair) < 3 || $pair[1]->act == 1 || ($pair[2]->act == 0)) {
            $allDaysOfMonth[date('Y-m-d-D', strtotime($pair[0]))][3] = lang('error_no_info');
        } else {
            if ($allDaysOfMonth[date('Y-m-d-D', strtotime($pair[0]))][3] == "00:00") {
                $allDaysOfMonth[date('Y-m-d-D', strtotime($pair[0]))][3] = (new DateTime($pair[2]->Times))->diff(
                    new DateTime($pair[1]->Times)
                )->format("%H:%I");
            } else {
                $diff = strtotime($pair[2]->Times) - strtotime($pair[1]->Times);
                $totalPrev = $utils->timeToSec($allDaysOfMonth[date('Y-m-d-D', strtotime($pair[0]))][3]);
                $totalSec = $totalPrev + $diff;
                $allDaysOfMonth[date('Y-m-d-D', strtotime($pair[0]))][3] = $utils->secToTime($totalSec);
            }
        }
    }
    $monthTotal = 0;
    foreach ($allDaysOfMonth as $day) {
        if ($day[3] != lang('error_no_info') && $day[3] != '00:00') {
            $totalPrev = $utils->timeToSec($day[3]);
            $monthTotal += $totalPrev;
        }
    }
    $monthTotal = '<span id = total>' . $utils->secToTime($monthTotal) . '</span>';

    $allDaysOfMonth[] = (array(lang('total'), '', '', $monthTotal));
}

echo json_encode(array('data'=>array_values($allDaysOfMonth)), JSON_UNESCAPED_UNICODE);
