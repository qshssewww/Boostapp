<?php require_once '../../app/initcron.php';

header('Content-Type: text/html; charset=utf-8');

if (Auth::guest()) exit;

$CompanyNum = Auth::user()->CompanyNum;

$StartDateWeek = $_REQUEST['StartDateWeek'];
$EndDateWeek = $_REQUEST['EndDateWeek'];

$Class = $_REQUEST['Class'] == 'BA999' ? 'BA999' : $_REQUEST['Class'];
$Guide = $_REQUEST['Guide'] == 'BA999' ? 'BA999' : $_REQUEST['Guide'];


function AddPlayTime($times)
{
    $minutes = 0; //declare minutes either it gives Notice: Undefined variable
    // loop throught all the times
    foreach ($times as $time) {
        list($hour, $minute) = explode(':', $time);
        $minutes += $hour * 60;
        $minutes += $minute;
    }

    $hours = floor($minutes / 60);
    $minutes -= $hours * 60;

    // returns the time already formatted
    return sprintf('%02d:%02d', $hours, $minutes);
}

$times = array();

if ($Class == 'BA999' && $Guide == 'BA999') {

    $ClientNoneShowMonthCounts = DB::table('classstudio_date')->where('CompanyNum', '=', $CompanyNum)->where('GuideId', '=', '99999999')->whereBetween('StartDate', array($StartDateWeek, $EndDateWeek))->where('Status', '!=', '2')->select('id', 'StartDate', 'StartTime', 'ClassName', 'ClientRegister', 'Day', 'EndTime', 'GuideName', 'ClassNameType', 'GuideId', 'ExtraGuideId', 'ExtraGuideName')
        ->orderBy('StartDate', 'ASC')->orderBy('StartTime', 'ASC')->get();

} else if ($Class == 'BA999' && $Guide != 'BA999') {

    $myArray = explode(',', $Guide);
    $ClientNoneShowMonthCounts = DB::table('classstudio_date')->where('CompanyNum', '=', $CompanyNum)->whereBetween('StartDate', array($StartDateWeek, $EndDateWeek))->where('Status', '!=', '2')->whereIn('GuideId', $myArray)
        ->Orwhere('CompanyNum', '=', $CompanyNum)->whereBetween('StartDate', array($StartDateWeek, $EndDateWeek))->where('Status', '!=', '2')->whereIn('ExtraGuideId', $myArray)->select('id', 'StartDate', 'StartTime', 'ClassName', 'ClientRegister', 'Day', 'EndTime', 'GuideName', 'ClassNameType', 'GuideId', 'ExtraGuideId', 'ExtraGuideName')
        ->orderBy('StartDate', 'ASC')->orderBy('StartTime', 'ASC')->get();

} else if ($Class != 'BA999' && $Guide == 'BA999') {

    $myArray = explode(',', $Class);
    $ClientNoneShowMonthCounts = DB::table('classstudio_date')->where('CompanyNum', '=', $CompanyNum)->whereBetween('StartDate', array($StartDateWeek, $EndDateWeek))->where('Status', '!=', '2')->whereIn('ClassNameType', $myArray)->select('id', 'StartDate', 'StartTime', 'ClassName', 'ClientRegister', 'Day', 'EndTime', 'GuideName', 'ClassNameType', 'GuideId', 'ExtraGuideId', 'ExtraGuideName')
        ->orderBy('StartDate', 'ASC')->orderBy('StartTime', 'ASC')->get();

} else if ($Class != 'BA999' && $Guide != 'BA999') {

    $myArray = explode(',', $Class);
    $myArrayGuideId = explode(',', $Guide);
    $ClientNoneShowMonthCounts = DB::table('classstudio_act')->where('CompanyNum', '=', $CompanyNum)->whereBetween('StartDate', array($StartDateWeek, $EndDateWeek))->where('Status', '!=', '2')->whereIn('ClassNameType', $myArray)->whereIn('GuideId', $myArrayGuideId)->select('id', 'StartDate', 'StartTime', 'ClassName', 'ClientRegister', 'Day', 'EndTime', 'GuideName', 'ClassNameType', 'GuideId', 'ExtraGuideId', 'ExtraGuideName')
        ->orderBy('StartDate', 'ASC')->orderBy('StartTime', 'ASC')->get();

}


$OpenTableCount = count($ClientNoneShowMonthCounts);
$TotalClassPayments = '0.00';
$TotalClinets = '0';
$ClientSum = '0';

$resArr = array("data" => array());
foreach ($ClientNoneShowMonthCounts as $ClientNoneShowMonthCount) {


    $FixPricePerGroup = '0.00';
    $StartTime = with(new DateTime($ClientNoneShowMonthCount->StartTime))->format('H:i');
    $EndTime = with(new DateTime($ClientNoneShowMonthCount->EndTime))->format('H:i');


///// חישוב שכר
    $GuideId = ($ClientNoneShowMonthCount->GuideId == $Guide) ? $ClientNoneShowMonthCount->GuideId : $ClientNoneShowMonthCount->ExtraGuideId;
    //$CheckPricess = DB::select('select * from coach_paymentstep where CompanyNum = "' . $CompanyNum . '" AND Status = 0 AND Type = 3 AND CoachId = "' . $ClientNoneShowMonthCount->GuideId . '" AND FIND_IN_SET("' . $ClientNoneShowMonthCount->ClassNameType . '",ClassType) > 0 order By `NumClient` ASC ');
    $CheckPricess = DB::table('coach_paymentstep')
        ->where('CompanyNum', $CompanyNum)
        ->where('Status', 0)
        ->where('Type', 3)
        ->whereRaw('FIND_IN_SET('.$ClientNoneShowMonthCount->ClassNameType.',ClassType) > 0')
        ->where(function ($q) use ($ClientNoneShowMonthCount) {
            return $q->where('CoachId',$ClientNoneShowMonthCount-> GuideId)
                ->orWhere('CoachId', $ClientNoneShowMonthCount->ExtraGuideId);
        })->get();

    foreach ($CheckPricess as $CheckPrices) {
        $NumClient = $CheckPrices->NumClient;
        $AmountPerHour = ($ClientNoneShowMonthCount->GuideId == $Guide) ? $CheckPrices->Amount : $CheckPrices->ExtraAmount;
        $NoneShow = $CheckPrices->NoneShow;
        $LateCancel = $CheckPrices->LateCancel;

        if ($NoneShow == '0' && $LateCancel == '0') {
            $ClientSum = DB::table('classstudio_act')->where('ClassId', '=', $ClientNoneShowMonthCount->id)->whereIn('Status', array(1, 2, 6, 10, 11, 12, 15, 16, 21, 23))->where('CompanyNum', '=', $CompanyNum)->where('GuideId', '=', $ClientNoneShowMonthCount->GuideId)->count();
        } else if ($NoneShow == '1' && $LateCancel == '0') {
            $ClientSum = DB::table('classstudio_act')->where('ClassId', '=', $ClientNoneShowMonthCount->id)->whereIn('Status', array(1, 2, 6, 8, 10, 11, 12, 15, 16, 21, 23))->where('CompanyNum', '=', $CompanyNum)->where('GuideId', '=', $ClientNoneShowMonthCount->GuideId)->count();
        } else if ($NoneShow == '0' && $LateCancel == '1') {
            $ClientSum = DB::table('classstudio_act')->where('ClassId', '=', $ClientNoneShowMonthCount->id)->whereIn('Status', array(1, 2, 4, 6, 10, 11, 12, 15, 16, 21, 23))->where('CompanyNum', '=', $CompanyNum)->where('GuideId', '=', $ClientNoneShowMonthCount->GuideId)->count();
        } else {
            $ClientSum = DB::table('classstudio_act')->where('ClassId', '=', $ClientNoneShowMonthCount->id)->whereIn('Status', array(1, 2, 4, 6, 8, 10, 11, 12, 15, 16, 21, 23))->where('CompanyNum', '=', $CompanyNum)->where('GuideId', '=', $ClientNoneShowMonthCount->GuideId)->count();
        }
        $FixPricePerGroup = ($NumClient <= $ClientSum || $ClientSum > $NumClient ? $FixPricePerGroup + $AmountPerHour : $FixPricePerGroup + '0');
    }

    $ClassPayments = $FixPricePerGroup;
    $TotalClassPayments += $ClassPayments;
    $TotalClinets += $ClientSum;
    $ExtraGuideName = ($ClientNoneShowMonthCount->ExtraGuideName != '' ? ', ' . lang('instructor_help') . ': ' . $ClientNoneShowMonthCount->ExtraGuideName : '');

    $reportArray = array();
    $reportArray[0] = ($ClientNoneShowMonthCount->StartDate != '') ? htmlentities(with(new DateTime($ClientNoneShowMonthCount->StartDate))->format('d/m/Y')) : '';
    $reportArray[1] = htmlentities($ClientNoneShowMonthCount->Day);
    $reportArray[2] = ($ClientNoneShowMonthCount->StartDate != '') ? htmlentities(with(new DateTime($ClientNoneShowMonthCount->StartTime))->format('H:i')) : '';
    $reportArray[3] = htmlentities($ClientSum);
    $reportArray[4] = number_format($ClassPayments, 2) . '₪';
    $reportArray[5] = htmlentities($ClientNoneShowMonthCount->ClassName);
    $reportArray[6] = htmlentities($ClientNoneShowMonthCount->GuideName) . ($ExtraGuideName);
    array_push($resArr["data"], $reportArray);
}
$reportArray = ['', htmlentities($OpenTableCount), '', htmlentities($TotalClinets), number_format($TotalClassPayments, 2) . '₪', '', ''];
array_push($resArr["data"], $reportArray);

echo json_encode($resArr, JSON_UNESCAPED_UNICODE);


?>














