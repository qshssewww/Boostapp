<?php
require_once '../../app/initcron.php';

$ClientId = $_REQUEST['ClientId'];
$CalendarId = $_REQUEST['Id'];

$UserId = Auth::user()->id;
$CompanyNum = Auth::user()->CompanyNum;
$ItemId = Auth::user()->ItemId; 

$GetTasks = DB::table('calendar')->where('id','=', $CalendarId)->where('CompanyNum','=', $CompanyNum)->first();

if (@$GetTasks->id != '') {

if ($GetTasks->AgentId==$UserId){
$Disable = '0';    
}    
else {
$Disable = '1';    
}

    $ClientId = empty($ClientId) ? $GetTasks->ClientId : $ClientId;
$GetCientName = DB::table('client')->where('id','=', $ClientId)->where('CompanyNum','=', $CompanyNum)->first();    
    
if (@$GetCientName->CompanyName!=''){
$CompanyName = @$GetCientName->CompanyName;    
}   
else {
$CompanyName = 'ללא שיוך ללקוח';    
}    
    
$CalendarContent = array('Id' => $GetTasks->id, 'Title' => $GetTasks->Title, 'StartDate' => $GetTasks->StartDate, 'StartTime' => $GetTasks->StartTime, 'EndTime' => $GetTasks->EndTime, 'Type' => $GetTasks->Type, 'Floor' => $GetTasks->Floor, 'Level' => $GetTasks->Level, 'Content' => $GetTasks->Content, 'ClientId' => $GetTasks->ClientId, 'User' => $GetTasks->User, 'Status' => $GetTasks->Status, 'Disable' => $Disable, 'AgentId' => $GetTasks->AgentId, 'PipeLineId' => $GetTasks->PipeLineId, 'ClientName' => @$CompanyName, 'GroupPermission' => @$GetTasks->GroupPermission, 'ClientPhone' => @$GetCientName->ContactMobile);
$CalendarContent = json_encode($CalendarContent);
echo $CalendarContent;

}

?>