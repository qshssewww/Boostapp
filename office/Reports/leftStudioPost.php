<?php
require_once '../../app/controllers/PostController.php';

if (Auth::guest() || !Auth::userCan('174')) {
    return;
}

$CompanyNum = Auth::user()->CompanyNum;
$resContent = [];

$dateFrom = $_POST["dateFrom"] ?? date('Y-m-d');
$dateTo = $_POST["dateTo"] ?? date('Y-m-d');

$OpenTables = DB::table('client')->select('id', 'CompanyName', 'ContactMobile', 'Email', 'Dates', 'ArchiveDate', 'BrandName', 'Gender')
    ->where('CompanyNum', '=', $CompanyNum)
    ->where('Status', '=', 1)
    ->whereBetween('ArchiveDate', array($dateFrom, $dateTo))
    ->orderBy('CompanyName', 'ASC')
    ->get();


foreach ($OpenTables as $Task) {

    $TotalDays = 0;

    $ClientUserNameLog = '<a class="text-danger font-weight-bold" href="/office/ClientProfile.php?u=' . $Task->id . '">' . $Task->CompanyName . '</a>';

    if ($Task->Gender == 1) {
        $Gender = lang('male');
    } elseif ($Task->Gender == 2) {
        $Gender = lang('female');
    } else {
        $Gender = lang('other');
    }

    $StartDates = isset($Task->Dates) ? date('Y-m-d', strtotime($Task->Dates)) : null;
    $ArchiveDate = isset($Task->ArchiveDate) ? date('Y-m-d', strtotime($Task->ArchiveDate)) : null;
    if (isset($StartDates, $ArchiveDate)) {
        $startTimeStamp = strtotime($StartDates);
        $endTimeStamp = strtotime($ArchiveDate);

        $timeDiff = abs($endTimeStamp - $startTimeStamp);

        $numberDays = $timeDiff / 86400;  // 86400 seconds in one day
        $TotalDays = (int)$numberDays + 1;
    } else {
        $TotalDays = '';
    }


    $CheckMembership = DB::table('client_activities')
        ->select('id', 'ItemText', 'Department')
        ->where('CompanyNum', '=', $CompanyNum)
        ->where('ClientId', '=', $Task->id)
        ->orderBy('CardNumber', 'DESC')
        ->first();

    $Memberships = $CheckMembership ? DB::table('membership')->where('id', '=', $CheckMembership->Department)->first() : null;

    $resItem = [];

    $resItem[] = $ArchiveDate ? date('d/m/Y', strtotime($ArchiveDate)) : '--';
    $resItem[] = $ClientUserNameLog;
    $resItem[] = $Task->ContactMobile ?? '--';
    $resItem[] = $Task->Email ?? '--';
    $resItem[] = $Gender;
    $resItem[] = $CheckMembership->ItemText ?? '--';
    $resItem[] = $Memberships->MemberShip ?? '--';
    $resItem[] = htmlentities($Task->BrandName);
    $resItem[] = $TotalDays;

    $resContent[] = $resItem;
}

echo (new PostController())->toDataJson($resContent);



