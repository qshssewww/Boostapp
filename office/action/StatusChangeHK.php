<?php

require_once __DIR__ . '/../../app/initcron.php';
require_once __DIR__ . '/../services/LoggerService.php';

$CompanyNum = Auth::user()->CompanyNum;
$Acts = $_POST['Act'];

$segments = explode(':', $Acts);

$EventId = array_shift($segments);
$NewStatus = array_shift($segments);

///  קבלת סטטוס ישן

$HoraatKeva = DB::table('payment')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $EventId)->first();

if ($NewStatus == '2') {
    DB::table('payment')
        ->where('id', $EventId)
        ->where('CompanyNum', '=', $CompanyNum)
        ->update(array('Status' => '2'));
} elseif ($NewStatus == '3' && $HoraatKeva->TryDate != date('Y-m-d')) {
    DB::table('payment')
        ->where('id', $EventId)
        ->where('CompanyNum', '=', $CompanyNum)
        ->update(array('Status' => '2', 'TryDate' => date('Y-m-d'), 'NumTry' => 1));
} elseif ($NewStatus == '4') {
    DB::table('payment')
        ->where('id', $EventId)
        ->where('CompanyNum', '=', $CompanyNum)
        ->update(array('Status' => '4'));
}

$statusLables = [
    2 => lang('failed_single'),
    3 => lang('charge_again'),
    4 => lang('mark_as_lost_debt'),
];

CreateLogMovement(
    lang('log_change_horaat_keva_status', [
        'newStatus' => $statusLables[$NewStatus] ?? $NewStatus,
    ]),
    $HoraatKeva->ClientId
);

LoggerService::info('Status of Horaat Keva ' . $HoraatKeva->id . ' changed to ' . $NewStatus, LoggerService::CATEGORY_HORAAT_KEVA_CHANGE_STATUS);

/// שלח הודעת עדכון פעולה
echo 'הפקודה נשלחה בהצלחה';
