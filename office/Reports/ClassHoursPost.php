<?php require_once '../../app/initcron.php'; 

header('Content-Type: application/json; charset=utf-8');

if (Auth::guest()) exit;

$CompanyNum = Auth::user()->CompanyNum;

$StartDateWeek = isset($_GET['StartDateWeek']) ? $_GET['StartDateWeek'] : date('Y-m-d');
$EndDateWeek = isset($_GET['EndDateWeek']) ? $_GET['EndDateWeek'] : date('Y-m-d');

$Class = (!isset($_GET['Class']) || $_GET['Class']=='') ? 'BA999' : $_GET['Class'];
$Guide = (!isset($_GET['Guide']) || $_GET['Guide']=='') ? 'BA999' : $_GET['Guide'];

if ($Class=='BA999' && $Guide=='BA999'){
  $ClientNoneShowMonthCounts = DB::table('classstudio_date')->where('CompanyNum','=',$CompanyNum)->whereBetween('StartDate', array($StartDateWeek, $EndDateWeek))->where('Status','!=','2')->select('id','StartDate','StartTime','ClassName','ClientRegister','Day','EndTime','GuideName','ClassNameType','GuideId','ExtraGuideId', 'ExtraGuideName')
  ->orderBy('StartDate', 'ASC')->orderBy('StartTime', 'ASC')->get();  
}
else if ($Class=='BA999' && $Guide!='BA999') {
  $myArray = explode(',', $Guide);      
      
  $ClientNoneShowMonthCounts = DB::table('classstudio_date')->where('CompanyNum','=',$CompanyNum)->whereBetween('StartDate', array($StartDateWeek, $EndDateWeek))->where('Status','!=','2')->whereIn('GuideId', $myArray)
  ->Orwhere('CompanyNum','=',$CompanyNum)->whereBetween('StartDate', array($StartDateWeek, $EndDateWeek))->where('Status','!=','2')->whereIn('ExtraGuideId', $myArray)->select('id','StartDate','StartTime','ClassName','ClientRegister','Day','EndTime','GuideName','ClassNameType','GuideId','ExtraGuideId', 'ExtraGuideName')
  ->orderBy('StartDate', 'ASC')->orderBy('StartTime', 'ASC')->get();      
}
else if ($Class!='BA999' && $Guide=='BA999') {
  $myArray = explode(',', $Class);      
  
  $ClientNoneShowMonthCounts = DB::table('classstudio_date')->where('CompanyNum','=',$CompanyNum)->whereBetween('StartDate', array($StartDateWeek, $EndDateWeek))->where('Status','!=','2')->whereIn('ClassNameType', $myArray)->select('id','StartDate','StartTime','ClassName','ClientRegister','Day','EndTime','GuideName','ClassNameType','GuideId','ExtraGuideId', 'ExtraGuideName')
  ->orderBy('StartDate', 'ASC')->orderBy('StartTime', 'ASC')->get();       
}

else if ($Class!='BA999' && $Guide!='BA999') {
  $myArray = explode(',', $Class);
  $myArrayGuideId = explode(',', $Guide);     
  
  $ClientNoneShowMonthCounts = DB::table('classstudio_act')->where('CompanyNum','=',$CompanyNum)->whereBetween('StartDate', array($StartDateWeek, $EndDateWeek))->where('Status','!=','2')->whereIn('ClassNameType', $myArray)->whereIn('GuideId', $myArrayGuideId)->select('id','StartDate','StartTime','ClassName','ClientRegister','Day','EndTime','GuideName','ClassNameType','GuideId','ExtraGuideId', 'ExtraGuideName')
  ->orderBy('StartDate', 'ASC')->orderBy('StartTime', 'ASC')->get();    
}

$OpenTableCount = count($ClientNoneShowMonthCounts);
$AmountPerHour = '0.00'; 
$TotalClassPayments = '0.00';
$TotalHours = '0.0';
$data = array();

foreach($ClientNoneShowMonthCounts as $ClientNoneShowMonthCount){
  
  $StartTime = date('H:i', strtotime($ClientNoneShowMonthCount->StartTime));
  $EndTime = date('H:i', strtotime($ClientNoneShowMonthCount->EndTime)); 
      
  $totalMinutes = (strtotime($EndTime) - strtotime($StartTime))/60;
  $totalTimeDisplay = date('H:i', strtotime(floor($totalMinutes/60).':'.$totalMinutes%60));

  ///// חישוב שכר
  if ($Guide == 'BA999'){
    if ($ClientNoneShowMonthCount->ExtraGuideId){
      $CheckPrices = DB::select('select * from coach_paymentstep where CompanyNum = "'.$CompanyNum.'" AND Status = 0  AND Type = 2 AND CoachId = "'.$ClientNoneShowMonthCount->ExtraGuideId.'" AND FIND_IN_SET("'.$ClientNoneShowMonthCount->ClassNameType.'",ClassType) > 0 LIMIT 1');     
      foreach ($CheckPrices as $CheckPrice){
        $AmountPerHour = $CheckPrice->ExtraAmount;  
      }
      $Payment = floor($totalMinutes*($AmountPerHour/60));
      $TotalClassPayments += $Payment; 
      $TotalHours += $totalMinutes;
      
      $data[] = array(
        ($ClientNoneShowMonthCount->StartDate=='')?"": htmlentities(date('d/m/Y', strtotime($ClientNoneShowMonthCount->StartDate))),
        htmlentities($ClientNoneShowMonthCount->Day),
        ($ClientNoneShowMonthCount->StartTime=='')?"":htmlentities(date('H:i', strtotime($ClientNoneShowMonthCount->StartTime))),
        ($ClientNoneShowMonthCount->EndTime=='')?"":htmlentities(date('H:i', strtotime($ClientNoneShowMonthCount->EndTime))),  
        htmlentities($totalTimeDisplay),
        number_format($Payment, 2) ." ".lang('currency_symbol'),
        htmlentities($ClientNoneShowMonthCount->ClassName),
        htmlentities($ClientNoneShowMonthCount->ClientRegister),
        htmlentities(lang('instructor_help').': '.$ClientNoneShowMonthCount->ExtraGuideName)
      );
    }

    $CheckPrices = DB::select('select * from coach_paymentstep where CompanyNum = "'.$CompanyNum.'" AND Status = 0  AND Type = 2 AND CoachId = "'.$ClientNoneShowMonthCount->GuideId.'" AND FIND_IN_SET("'.$ClientNoneShowMonthCount->ClassNameType.'",ClassType) > 0 LIMIT 1');
    $GuideName = $ClientNoneShowMonthCount->GuideName;
    foreach ($CheckPrices as $CheckPrice) {
      $AmountPerHour = $CheckPrice->Amount;   
    }
  }
  else{  
    if ($ClientNoneShowMonthCount->GuideId==$Guide){
      $CheckPrices = DB::select('select * from coach_paymentstep where CompanyNum = "'.$CompanyNum.'" AND Status = 0  AND Type = 2 AND CoachId = "'.$ClientNoneShowMonthCount->GuideId.'" AND FIND_IN_SET("'.$ClientNoneShowMonthCount->ClassNameType.'",ClassType) > 0 LIMIT 1');
      $GuideName = $ClientNoneShowMonthCount->GuideName;
      foreach ($CheckPrices as $CheckPrice) {
        $AmountPerHour = $CheckPrice->Amount;   
      }    
    }
    else {
      $CheckPrices = DB::select('select * from coach_paymentstep where CompanyNum = "'.$CompanyNum.'" AND Status = 0  AND Type = 2 AND CoachId = "'.$ClientNoneShowMonthCount->ExtraGuideId.'" AND FIND_IN_SET("'.$ClientNoneShowMonthCount->ClassNameType.'",ClassType) > 0 LIMIT 1');     
      $GuideName = lang('instructor_help').': '.$ClientNoneShowMonthCount->ExtraGuideName;
      foreach ($CheckPrices as $CheckPrice) {
      $AmountPerHour = $CheckPrice->ExtraAmount;   
      }  	
    }
  }
    
  $Payment = floor($totalMinutes*($AmountPerHour/60));
  $TotalClassPayments += $Payment; 
  $TotalHours += $totalMinutes;

  $data[] = array(
    ($ClientNoneShowMonthCount->StartDate=='')?"": htmlentities(date('d/m/Y', strtotime($ClientNoneShowMonthCount->StartDate))),
    htmlentities($ClientNoneShowMonthCount->Day),
    ($ClientNoneShowMonthCount->StartTime=='')?"":htmlentities(date('H:i', strtotime($ClientNoneShowMonthCount->StartTime))),
    ($ClientNoneShowMonthCount->EndTime=='')?"":htmlentities(date('H:i', strtotime($ClientNoneShowMonthCount->EndTime))),  
    htmlentities($totalTimeDisplay),
    number_format($Payment, 2) .' '.lang('currency_symbol'),
    htmlentities($ClientNoneShowMonthCount->ClassName),
    htmlentities($ClientNoneShowMonthCount->ClientRegister),
    htmlentities($GuideName)
  );
}

$TotalHours = sprintf('%02d:%02d', floor($TotalHours/60), $TotalHours%60);

$data[] = array(
  lang('total_classes'),
  htmlentities($OpenTableCount),
  "",
  "",
  htmlentities($TotalHours),
  number_format($TotalClassPayments, 2) .' '.lang('currency_symbol'),
  "",
  "",
  ""
);

echo json_encode(array('data'=>$data), JSON_UNESCAPED_UNICODE);













