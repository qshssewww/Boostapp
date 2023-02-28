<?php

require_once '../../app/init.php';

$userid = Auth::user()->id;
$time = date('Y-m-d G:i:s');
$option = $_POST['option'];

$segments = explode(':', $option);

$Status = array_shift ($segments);
$ListId = array_shift ($segments);	




//Log	
$LeadCardForLog = DB::table('leads')->where('id', '=', $ListId)->first();
$ClientCardForLog = DB::table('client')->where('id', '=', $LeadCardForLog->ClientId)->first();
if (@$LeadCardForLog->Seller == '') {
$AgentOldCardForLog =  "<a href='#'>��� ����</a>";
}
else {
$AgentOldCardForLogs = DB::table('users')->where('id', '=', @$LeadCardForLog->Seller)->first();
$AgentOldCardForLog = "<a href='SalesProfile.php?u=".$AgentOldCardForLogs->id."' target='_blank'>".$AgentOldCardForLogs->display_name."</a>";
}
$AgentNewCardForLogs = DB::table('users')->where('id', '=', $Status)->first();
$AgentNewCardForLog = "<a href='SalesProfile.php?u=".$AgentNewCardForLogs->id."' target='_blank'>".$AgentNewCardForLogs->display_name."</a>";

$LogUserId = Auth::user()->id;
$LogUserName = Auth::user()->display_name;
$LogDateTime = date('Y-m-d G:i:s');
$LogContent = "<i class='fa fa-search' aria-hidden='true'></i> ".$LogUserName." ����� �� ����� ����� ���� <a href='ClientProfile.php?u=".$LeadCardForLog->ClientId."' target='_blank'>".$ClientCardForLog->CompanyName."</a> ������ ".$AgentOldCardForLog." ����� ".$AgentNewCardForLog;
//DB::table('log')->insert(array('UserId' => $LogUserId, 'Text' => $LogContent, 'Dates' => $LogDateTime, 'ClientId' => $LeadCardForLog->ClientId));
//Log	





	DB::table('leads')
        ->where('id', $ListId)
        ->update(array('Seller' => $Status));



?>
