<?php
require_once '../../app/initcron.php';

$userid = Auth::user()->id;
$CompanyNum = Auth::user()->CompanyNum;
$ItemId = Auth::user()->ItemId;
$time = date('Y-m-d G:i:s');
$PipeId = $_REQUEST['PipeId'];
$LeadId = $_REQUEST['LeadId'];


$LeadPipeLineBeforeChange = DB::table('pipeline')->where('id', $LeadId)->where('CompanyNum', $CompanyNum)->first();
if ($LeadPipeLineBeforeChange->PipeId == $CompanyNum.'98') {$NameStatusBefore = 'הצלחה';$IdStatusBefore = $CompanyNum.'98';}
elseif ($LeadPipeLineBeforeChange->PipeId == $CompanyNum.'99') {$NameStatusBefore = 'כישלון';$IdStatusBefore = $CompanyNum.'99';}
else {
	$LeadStatusBeforeChange = DB::table('leadstatus')->where('id', $LeadPipeLineBeforeChange->PipeId)->first();
	$NameStatusBefore = $LeadStatusBeforeChange->Title;
	$IdStatusBefore = $LeadStatusBeforeChange->id;
}

if ($PipeId!=$CompanyNum.'100'){

	DB::table('pipeline')
        ->where('id', $LeadId)
        ->where('CompanyNum', $CompanyNum)
        ->update(array('PipeId' => $PipeId));
}


if ($PipeId == $CompanyNum.'98'){

	DB::table('client')
        ->where('id', $LeadPipeLineBeforeChange->ClientId)
        ->where('CompanyNum', $CompanyNum)
        ->update(array('Status' => '0'));    
    
} 

$ClientDetails = DB::table('client')->where('id', $LeadPipeLineBeforeChange->ClientId)->where('CompanyNum', $CompanyNum)->first();
if ($PipeId == $CompanyNum.'98') {$NameStatusAfter = 'הצלחה';$IdStatusAfter = $CompanyNum.'98';}
elseif ($PipeId == $CompanyNum.'99') {$NameStatusAfter = 'כישלון';$IdStatusAfter = $CompanyNum.'99';}
else {
	$LeadStatusAfterChange = DB::table('leadstatus')->where('id', $PipeId)->first();
	$NameStatusAfter = $LeadStatusAfterChange->Title;
	$IdStatusAfter = $LeadStatusAfterChange->id;
}


if ($IdStatusBefore != $IdStatusAfter) {
CreateLogMovement( //FontAwesome Icon
	'העביר את הליד '.$ClientDetails->CompanyName.' מסטטוס '.@$NameStatusBefore.' לסטטוס '.@$NameStatusAfter, //LogContent
	$LeadPipeLineBeforeChange->ClientId //ClientId
);
}
?>
