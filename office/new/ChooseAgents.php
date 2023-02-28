<?php require_once '../../app/initcron.php'; 


$Id = $_REQUEST['Id'];
$PipeId = $_REQUEST['PipeId'];
$CompanyNum = Auth::user()->CompanyNum;


DB::table('pipeline')
->where('CompanyNum', '=' , $CompanyNum)
->where('id', '=' , $PipeId)
->update(array('AgentId' => $Id)); 

?>


