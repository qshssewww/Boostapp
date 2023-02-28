<?php
require_once '../../app/initcron.php';

$userid = Auth::user()->id;
$ListId = $_REQUEST['LeadId'];

@$LeadsShow = DB::table('pipeline')->where('id', '=', @$ListId)->first(); 
if (@$LeadsShow->UserId == '0') { 

	DB::table('pipeline')
        ->where('id', $ListId)
        ->update(array('UserId' => $userid));

}
?>
