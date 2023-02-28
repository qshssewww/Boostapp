<?php

require_once '../../app/initcron.php'; 

header('Content-Type: application/json; charset=utf-8');

if (Auth::guest()) exit;

$CompanyNum = Auth::user()->CompanyNum;

if (isset($_GET['StartDateWeek'])) {
  $StartDateWeek = $_GET['StartDateWeek'];
} else {
  $StartDateWeek = date('Y-m-d');
}

if (isset($_GET['EndDateWeek'])){
  $EndDateWeek = $_GET['EndDateWeek'];
} else {
  $EndDateWeek = date('Y-m-d');
}

if (isset($_GET['Class']) && $_GET['Class']=='BA999'){
  $Class = 'BA999';    
}
else {
  $Class = $_GET['Class']; 
}      

if (isset($_GET['Guide']) && $_GET['Guide'] == ''){
  $Guide = 'BA999';
}
else {
  $Guide = $_GET['Guide'];
} 								    

$Class = str_replace('"', '', $Class);
$Guide = str_replace('"', '', $Guide);


function AddPlayTime($times) {
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

if ($Class=='BA999' && $Guide=='BA999'){
  $ClientNoneShowMonthCounts = DB::table('classstudio_date')->where('CompanyNum','=',$CompanyNum)->whereBetween('StartDate', array($StartDateWeek, $EndDateWeek))->where('Status','!=','2')->select('id','StartDate','StartTime','ClassName','ClientRegister','Day','EndTime','GuideName','ClassNameType','GuideId','ExtraGuideId', 'ExtraGuideName')
    ->orderBy('StartDate', 'ASC')->orderBy('StartTime', 'ASC')->get(); 
}
else if ($Class=='BA999' && $Guide!='BA999') {
  $myArray = explode(',', $Guide);      
    
  $ClientNoneShowMonthCounts = DB::table('classstudio_date')->where('CompanyNum','=',$CompanyNum)->whereBetween('StartDate', array($StartDateWeek, $EndDateWeek))->where('Status','!=','2')->whereIn('GuideId', $myArray)
    ->Orwhere('CompanyNum','=',$CompanyNum)->whereBetween('StartDate', array($StartDateWeek, $EndDateWeek))->where('Status','!=','2')->whereIn('ExtraGuideId', $myArray)
    ->select('id','StartDate','StartTime','ClassName','ClientRegister','Day','EndTime','GuideName','ClassNameType','GuideId','ExtraGuideId', 'ExtraGuideName')
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

$TodayDates = date('Y-m-d');
$AmountPerHour = '0.00'; 
$TotalClassPayments = '0.00';
$TotalClinets = '0';

$output = new StdClass;
$output->data = array();

$number = $OpenTableCount+1;
$i=1;
$TotalHours = '0.0';
$FixTota1 = '';
$ClientSum = '0';

$data = array();

foreach($ClientNoneShowMonthCounts as $ClientNoneShowMonthCount){

  $FixPricePerGroup = '0.00';  
  $StartTime = with(new DateTime(@$ClientNoneShowMonthCount->StartTime))->format('H:i');
  $EndTime = with(new DateTime(@$ClientNoneShowMonthCount->EndTime))->format('H:i');    
  
  ///// חישוב שכר    
  $ClientSum = DB::table('classstudio_act')->where('ClassId','=',$ClientNoneShowMonthCount->id)->where('StatusCount','=','0')->where('CompanyNum','=',$CompanyNum)->where('GuideId','=',$ClientNoneShowMonthCount->GuideId)->count();     

  if ($Guide == 'BA999'){ //אם לא הוזן מדריך
    if ($ClientNoneShowMonthCount->ExtraGuideId){ //אם קיים עוזר מדריך, חישוב מחיר
      $CheckPricess = DB::select('select * from coach_paymentstep where CompanyNum = "'.$CompanyNum.'" AND Status = 0 AND Type = 4 AND CoachId = "'.$ClientNoneShowMonthCount->ExtraGuideId.'" AND FIND_IN_SET("'.$ClientNoneShowMonthCount->ClassNameType.'",ClassType) > 0 order By `NumClient` ASC '); 
    
      $q = '1';
      foreach ($CheckPricess as $CheckPrices) {
        $AmountPerHour = $CheckPrices->ExtraAmount; 
        $FixPricePerGroup += $AmountPerHour; 
        ++$q;
      }

      $ClassPayments = $FixPricePerGroup;    
      $TotalClassPayments += $ClassPayments;   
      
      $FixPricePerGroup = 0;
      $ExtraGuideName = ', ע.מדריך: '.$ClientNoneShowMonthCount->ExtraGuideName;	

      $output->data[] = array(
        (@$ClientNoneShowMonthCount->StartDate=='')?'': htmlentities(with(new DateTime(@$ClientNoneShowMonthCount->StartDate))->format('d/m/Y')),
        htmlentities(@$ClientNoneShowMonthCount->Day),
        (@$ClientNoneShowMonthCount->StartTime=='')?'':htmlentities(with(new DateTime(@$ClientNoneShowMonthCount->StartTime))->format('H:i')),
        htmlentities(@$ClientSum),
        number_format(@$ClassPayments, 2) . ' ₪',
        htmlentities(@$ClientNoneShowMonthCount->ClassName),
        htmlentities(@$ClientNoneShowMonthCount->GuideName) . ' ' . @$ExtraGuideName
      );
    }
      
    if ($ClientNoneShowMonthCount->GuideId){ //אם קיים מדריך, חישוב מחיר
      $CheckPricess = DB::select('select * from coach_paymentstep where CompanyNum = "'.$CompanyNum.'" AND Status = 0 AND Type = 4 AND CoachId = "'.$ClientNoneShowMonthCount->GuideId.'" AND FIND_IN_SET("'.$ClientNoneShowMonthCount->ClassNameType.'",ClassType) > 0 order By `NumClient` ASC '); 
      
      $q = '1';
      foreach ($CheckPricess as $CheckPrices) {
        $AmountPerHour = $CheckPrices->Amount; 
        $FixPricePerGroup += $AmountPerHour; 
      
      ++$q;
      }
    }
  }
  else {   //אם הוזן מדריך
    if ($ClientNoneShowMonthCount->GuideId==$Guide){ //אם המדריך הוא מדריך

      $CheckPricess = DB::select('select * from coach_paymentstep where CompanyNum = "'.$CompanyNum.'" AND Status = 0 AND Type = 4 AND CoachId = "'.$ClientNoneShowMonthCount->GuideId.'" AND FIND_IN_SET("'.$ClientNoneShowMonthCount->ClassNameType.'",ClassType) > 0 order By `NumClient` ASC '); 
        
      $q = '1';
      foreach ($CheckPricess as $CheckPrices) {
        $AmountPerHour = $CheckPrices->Amount; 
        $FixPricePerGroup += $AmountPerHour;
        ++$q; 
      }  	  
    } 
    else { //אם המדריך הוא עוזר מדריך
      $CheckPricess = DB::select('select * from coach_paymentstep where CompanyNum = "'.$CompanyNum.'" AND Status = 0 AND Type = 4 AND CoachId = "'.$ClientNoneShowMonthCount->ExtraGuideId.'" AND FIND_IN_SET("'.$ClientNoneShowMonthCount->ClassNameType.'",ClassType) > 0 order By `NumClient` ASC ');  
      $q = '1';
      foreach ($CheckPricess as $CheckPrices) {
        $AmountPerHour = $CheckPrices->ExtraAmount; 
        $FixPricePerGroup += $AmountPerHour;  
        ++$q; 
      }  	
      
    }
  }
      
  $ClassPayments = $FixPricePerGroup;    
  $TotalClassPayments += $ClassPayments;   
      
  $TotalClinets += $ClientSum;
    
  if ($ClientNoneShowMonthCount->ExtraGuideName!=''){
    $ExtraGuideName = ', ע.מדריך: '.$ClientNoneShowMonthCount->ExtraGuideName;	
  }
  else {
    $ExtraGuideName = '';
  }	

  $output->data[] = array(
    (@$ClientNoneShowMonthCount->StartDate=='')?'': htmlentities(with(new DateTime(@$ClientNoneShowMonthCount->StartDate))->format('d/m/Y')),
    htmlentities(@$ClientNoneShowMonthCount->Day),
    (@$ClientNoneShowMonthCount->StartTime=='')?'':htmlentities(with(new DateTime(@$ClientNoneShowMonthCount->StartTime))->format('H:i')),
    htmlentities(@$ClientSum),
    number_format(@$ClassPayments, 2) . ' ₪',
    htmlentities(@$ClientNoneShowMonthCount->ClassName),
    htmlentities(@$ClientNoneShowMonthCount->GuideName) . ' ' . @$ExtraGuideName
  );
}


$output->data[] = array(
'',
htmlentities(@$OpenTableCount),
'',
htmlentities(@$TotalClinets),
number_format(@$TotalClassPayments, 2). ' ₪',
'',
''
);

echo json_encode($output);













