<?php

require_once '../../app/initcron.php';
require_once '../Classes/ClassStudioAct.php';

$CompanyNum = Auth::user()->CompanyNum;
$UserName = Auth::user()->display_name;
$UserId = Auth::user()->id;

$AppSettings = DB::table('appsettings')->where('CompanyNum', '=', $CompanyNum)->first();

$MemberShipLimitMoney = $AppSettings->MemberShipLimitMoney;
$MemberShipLimit = $AppSettings->MemberShipLimit;    
$DaysMemberShipLimit = $AppSettings->DaysMemberShipLimit; 

$MemberShipLimitType = $AppSettings->MemberShipLimitType;
$MemberShipLimitLateCancel = $AppSettings->MemberShipLimitLateCancel;
$MemberShipLimitNoneShow = $AppSettings->MemberShipLimitNoneShow;
$MemberShipLimitDays = $AppSettings->MemberShipLimitDays; 
$MemberShipLimitUnBlockDays = $AppSettings->MemberShipLimitUnBlockDays; 
$MemberShipLimitUnBlock = $AppSettings->MemberShipLimitUnBlock; 


$Acts = $_POST['Act'];

$segments = explode(':', $Acts);

$EventId = array_shift ($segments);
$ClientId = array_shift ($segments);
$NewStatus = array_shift ($segments);

$ReClass = '1';
$FinalTrueBalanceValue = '0';
$KnasOption = '0'; 
$KnasOptionVule = '0.00';
$Cards = ''; 
$WatingListSort = '0';
$TestClass = '1';
$TestClassStatus = '0';

$ClassAct = DB::table('classstudio_act')->where('CompanyNum', '=' , $CompanyNum)->where('id', '=', $EventId)->first(); 

$TestClass = $ClassAct->TestClass; 
$FixClientId = $ClassAct->FixClientId; 
/// עדכון רשימת המתנה
$WatingListSorts = DB::table('classstudio_act')->where('ClassId', '=', $ClassAct->ClassId)->where('Status', '=', '9')->where('CompanyNum', $CompanyNum)->where('WatingListSort', '!=', '0')->orderBy('WatingListSort','DESC')->first(); 


if (@$WatingListSorts->WatingListSort!=''){
$WatingListSort = $WatingListSorts->WatingListSort+1;
}

///  קבלת סטטוס ישן

 
$ClientBalanceValue = DB::table('client_activities')->where('CompanyNum', '=' , $CompanyNum)->where('id', '=', $ClassAct->ClientActivitiesId)->first();
$TrueBalanceValue = $ClientBalanceValue->TrueBalanceValue;
$OrigenalBalanceValue = $ClientBalanceValue->BalanceValue;
$ActBalanceValue = $ClientBalanceValue->ActBalanceValue;

$CheckOldStatus = DB::table('class_status')->where('id', '=', $ClassAct->Status)->first();
$CheckNewStatus = DB::table('class_status')->where('id', '=', $NewStatus)->first();   

$StatusCount = $CheckNewStatus->StatusCount;

/// מנוי תקופתי
if ($ClientBalanceValue->Department=='1') {

if ($NewStatus=='4' || $NewStatus=='8'){
$KnasOption = '1'; 
$KnasOptionVule = $MemberShipLimitMoney;      
}   


}

/// כרטיסיה
else if ($ClientBalanceValue->Department=='2' || $ClientBalanceValue->Department=='3') {

if ($CheckOldStatus->Act=='0' && $CheckNewStatus->Act=='0'){
$FinalTrueBalanceValue = $TrueBalanceValue; // ללא שינוי
$FinalActBalanceValue = $ActBalanceValue;
}   
else if ($CheckOldStatus->Act=='0' && $CheckNewStatus->Act=='1'){
$FinalTrueBalanceValue = $TrueBalanceValue+1; // מחזיר ניקוב
$FinalActBalanceValue = $ActBalanceValue+1;
} 
else if ($CheckOldStatus->Act=='0' && $CheckNewStatus->Act=='2'){
$FinalTrueBalanceValue = $TrueBalanceValue+1; // מחזיר ניקוב
$FinalActBalanceValue = $ActBalanceValue+1;
}  
else if ($CheckOldStatus->Act=='1' && $CheckNewStatus->Act=='0'){
$FinalTrueBalanceValue = $TrueBalanceValue-1; // מחסיר ניקוב
$FinalActBalanceValue = $ActBalanceValue-1;
}  
else if ($CheckOldStatus->Act=='1' && $CheckNewStatus->Act=='1'){
$FinalTrueBalanceValue = $TrueBalanceValue; // ללא שינוי
$FinalActBalanceValue = $ActBalanceValue;
}   
else if ($CheckOldStatus->Act=='1' && $CheckNewStatus->Act=='2'){
$FinalTrueBalanceValue = $TrueBalanceValue; // ללא שינוי
$FinalActBalanceValue = $ActBalanceValue;
} 
else if ($CheckOldStatus->Act=='2' && $CheckNewStatus->Act=='0'){
$FinalTrueBalanceValue = $TrueBalanceValue-1; // מחסיר ניקוב
$FinalActBalanceValue = $ActBalanceValue-1;
}  
else if ($CheckOldStatus->Act=='2' && $CheckNewStatus->Act=='1'){
$FinalTrueBalanceValue = $TrueBalanceValue; // ללא שינוי
$FinalActBalanceValue = $ActBalanceValue;
}   
else if ($CheckOldStatus->Act=='2' && $CheckNewStatus->Act=='2'){
$FinalTrueBalanceValue = $TrueBalanceValue; // ללא שינוי
$FinalActBalanceValue = $ActBalanceValue;
}       
 
if ($FinalTrueBalanceValue>='1' && $ClientBalanceValue->Status!='2' && $ClientBalanceValue->TrueDate==''){
$ActStatus = '0';     
} 
else if ($FinalTrueBalanceValue>='1' && $ClientBalanceValue->Status!='2' && $ClientBalanceValue->TrueDate!='' && $ClientBalanceValue->TrueDate>date('Y-m-d')){
$ActStatus = '0';    
}    
else {
$ActStatus = $ClientBalanceValue->Status;    
}    
    
DB::table('client_activities')
->where('CompanyNum', '=' , $CompanyNum)
->where('id', '=' , $ClientBalanceValue->id)
->update(array('TrueBalanceValue' => $FinalTrueBalanceValue, 'Status' => $ActStatus));
    
$Cards = $FinalTrueBalanceValue.' / '.$OrigenalBalanceValue;  

if ($ClassAct->ActStatus=='1'){
  
DB::table('client_activities')
->where('CompanyNum', '=' , $CompanyNum)
->where('id', '=' , $ClientBalanceValue->id)
->update(array('ActBalanceValue' => $FinalActBalanceValue));
    
    
}
        
    
    
}

if ($ClientBalanceValue->Department=='1' && $MemberShipLimitType!='2'){
if ($NewStatus=='8' && $MemberShipLimitNoneShow=='1' || $NewStatus=='4' && $MemberShipLimitLateCancel=='1'){}
else {    
DB::table('boostapplogin.badpoint')->where('CompanyNum', '=' , $CompanyNum)->where('ClinetId', '=', $FixClientId)->where('ClassId', '=', $ClassAct->ClassId)->delete();     
}
}

/// תיעוד שינוי סטטוס
 
$Dates = date('Y-m-d H:i:s');   
$UserId = Auth::user()->id;

$StatusJson = '';    
$StatusJson .= '{"data": [';  

if ($ClassAct->StatusJson!=''){                  
$Loops =  json_decode($ClassAct->StatusJson,true);	
foreach($Loops['data'] as $key=>$val){ 

$DatesDB = $val['Dates'];
$UserIdDB = $val['UserId'];
$StatusDB = $val['Status']; 
$StatusTitleDB = $val['StatusTitle']; 
$UserNameDB = $val['UserName'];     

$StatusJson .= '{"Dates": "'.$DatesDB.'", "UserId": "'.$UserIdDB.'", "Status": "'.$StatusDB.'", "StatusTitle": "'.$StatusTitleDB.'", "UserName": "'.$UserNameDB.'"},';    

}  
}

$StatusJson .= '{"Dates": "'.$Dates.'", "UserId": "'.$UserId.'", "Status": "'.$NewStatus.'", "StatusTitle": "'.$CheckNewStatus->Title.'", "UserName": "'.$UserName.'"}';

$StatusJson .= ']}';  


//// השלמת שיעור

if ($NewStatus=='10'){
$ReClass = '2';    
}

/// שיעור נסיון
if ($NewStatus=='11'){
$TestClass = '2';    
}

if ($NewStatus=='7' || $NewStatus=='8' || $NewStatus=='3' || $NewStatus=='4' || $NewStatus=='5'){
$TestClassStatus = '1';    
}

/// עדכון לסטטוס חדש
(new ClassStudioAct($EventId))->update([
    'Status' => $NewStatus,
    'StatusJson' => $StatusJson,
    'StatusCount' => $StatusCount,
    'ReClass' => $ReClass,
    'KnasOption' => $KnasOption,
    'KnasOptionVule' => $KnasOptionVule,
    'WatingListSort' => $WatingListSort,
    'TestClass' => $TestClass,
    'TestClassStatus' => $TestClassStatus,
]);

//// עדכון שיעור ברשימת משתתפים
            
$ClientRegister = DB::table('classstudio_act')->where('ClassId', '=' , $ClassAct->ClassId)->where('CompanyNum', '=' , $CompanyNum)->where('StatusCount', '=' , '0')->count();
$WatingList = DB::table('classstudio_act')->where('ClassId', '=' , $ClassAct->ClassId)->where('CompanyNum', '=' , $CompanyNum)->where('StatusCount', '=' , '1')->count();
               
               
DB::table('classstudio_date')
->where('CompanyNum', '=' , $CompanyNum)
->where('id', '=' , $ClassAct->ClassId)
->update(array('ClientRegister' => $ClientRegister, 'WatingList' => $WatingList));  

if ($WatingList > '0' && $CheckNewStatus->StatusCount!='0'){
$True = 'True';

}
else {
$True = 'False';    
}


if ($NewStatus == '10'){
$TrueReClass = 'True';
}
else {
$TrueReClass = 'False';    
}

///// Class Log
DB::table('classlog')->insertGetId(
array('CompanyNum' => $CompanyNum, 'ClassId' => $ClassAct->ClassId, 'ClientId' => $ClassAct->FixClientId, 'Status' => $CheckNewStatus->Title, 'UserName' => $UserId, 'numOfClients' => $ClientRegister)); 
/////////////////////////////////////////


header('Content-Type: application/json');
echo json_encode(array('Cards' => $Cards, 'WatingList'=> $True, 'WatingListText'=> 'שים לב! נא לשבץ מתאמן אחד מרשימת המתנה', 'ReClass' => $TrueReClass));
?>