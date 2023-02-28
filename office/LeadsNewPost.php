<?php

require_once '../app/initcron.php'; 

$CompanyNum = Auth::user()->CompanyNum;
$OpenTables = DB::table('pipeline_copy')->where('pipeline_copy.CompanyNum','=',$CompanyNum)
->select('pipeline_copy.id as pipelineid', 'pipeline_copy.ClientId', 'pipeline_copy.UserId', 'pipeline_copy.PipeId', 'pipeline_copy.Status as pipelinestatus', 'client.CompanyName', 'client.ContactMobile', 'client.Email', 'users.display_name', 'leadstatus.Title', DB::raw('DATE_FORMAT(client.Dates, "%d/%m/%Y") as datesnew')) 
->join('client', 'pipeline_copy.ClientId', '=', 'client.id')
->join('users', 'pipeline_copy.UserId', '=', 'users.id') 
->join('leadstatus', 'pipeline_copy.PipeId', '=', 'leadstatus.id')     
->get();

echo json_encode($OpenTables);  



?>
