<?php

require_once '../app/initcron.php';
$CompanyNum = Auth::user()->CompanyNum;

function validateDate($date, $format = 'd/m/Y')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}


$answer = [];

if (isset($_GET['q']) && (!empty($_GET['q']))) {

    $q = '%' . $_GET['q'] . '%';
    $ClassId = $_GET['ClassId'];
    $ClientAddClassDate = @$_GET['ClientAddClassDate'];

    if ($ClassId == '1') {

        $StartDate = '';
        $CheckDate = validateDate($_GET['q']);
        if ($CheckDate == 'true') {
            $DateNew = str_replace("/", "-", $_GET['q']);
            $StartDate = date('Y-m-d', strtotime($DateNew));
        }
        $Today = date('Y-m-d');

        $Items = DB::table('classstudio_date')->where('ClassName', 'LIKE', $q)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('StartDate', '>=', $Today)->Orwhere('GuideName', 'LIKE', $q)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('StartDate', '>=', $Today)->Orwhere('StartDate', '=', @$StartDate)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('EndDate', '>=', $Today)->Orwhere('Day', 'LIKE', $q)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('StartDate', '>=', $Today)->orderBy('StartDate', 'ASC')->orderBy('StartTime', 'ASC')->get();

    } elseif ($ClassId == '2' || $ClassId == '3') {

        if ($ClientAddClassDate == '') {
            $StartDate = date('Y-m-d');
        } else {
            $StartDate = $ClientAddClassDate;
        }
        $EndDate = date('Y-m-d', strtotime('+40 day', strtotime($StartDate)));

        $Items = DB::table('classstudio_date')
            ->where('ClassName', 'LIKE', $q)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('ClassType', '!=', '3')
            ->whereBetween('StartDate', array($StartDate, $EndDate))
            ->Orwhere('GuideName', 'LIKE', $q)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('ClassType', '!=', '3')
            ->whereBetween('StartDate', array($StartDate, $EndDate))
            ->Orwhere('Day', 'LIKE', $q)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('ClassType', '!=', '3')
            ->whereBetween('StartDate', array($StartDate, $EndDate))
            ->orderBy('StartDate', 'ASC')->orderBy('DayNum', 'ASC')->orderBy('StartTime', 'ASC')
            ->groupBy('DayNum')->groupBy('StartTime')->groupBy('Floor')
            ->get();


    }

    $ItemCount = count($Items);
    foreach ($Items as $Item) {
        if ($Item->MaxClient == 0)
            continue;

        $Date = with(new DateTime($Item->StartDate))->format('d/m/Y');
        $Time = with(new DateTime($Item->StartTime))->format('H:i');
        $Floor = DB::table('sections')->select('Title')->where('id', '=', $Item->Floor)->where('CompanyNum', $CompanyNum)->first();
        $title = $Floor->Title ?? '';

        if ($ClassId == '1') {
            $answer[] = ['id' => $Item->id, 'text' => $Item->ClassName . ' :: תאריך: ' . $Date . ' :: יום: ' . $Item->Day . ' :: שעה: ' . $Time . ' :: מדריך: ' . $Item->GuideName . ' :: מיקום: ' . $title];
        } elseif ($ClassId == '2' || $ClassId == '3') {
            $answer[] = ['id' => $Item->id, 'text' => $Item->ClassName . ' :: יום: ' . $Item->Day . ' :: שעה: ' . $Time . ' :: מדריך: ' . $Item->GuideName . ' :: מיקום: ' . $title];
        }

    }

} else {
    $answer[] = ['id' => 0, 'text' => 'לא נמצאו שיעורים פעילים...'];
}


echo '{"results": ' . json_encode($answer) . '}';



