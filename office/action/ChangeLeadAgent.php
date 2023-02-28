<?php
require_once '../../app/initcron.php';

$UserId = $_REQUEST['UserId'];
$LeadId = $_REQUEST['LeadId'];

@$LeadsShow = DB::table('pipeline')->where('id', '=', @$LeadId)->first(); 
if (Auth::userCan('141')) { 

	DB::table('pipeline')
        ->where('id', $LeadId)
        ->update(array('AgentId' => $UserId));

}
?>
