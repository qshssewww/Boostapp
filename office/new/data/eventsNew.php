<?php


    require_once '../../../app/init.php';

    $UserId = Auth::user()->id;
	$CompanyNum = Auth::user()->CompanyNum;
    $RoleId = Auth::user()->role_id;

    ini_set('display_errors', 1);
    include('config.php');
	include ('scheduler_connector.php');
    require("db_mysqli.php");
	
	$mysqli = new mysqli($mysql_server, $mysql_user, $mysql_pass, $mysql_db); 

	$scheduler = new schedulerConnector($mysqli,"MySQLi");


    $FloorId = @$_REQUEST['FloorId'];    
    
    if ($FloorId!=''){
    $scheduler->render_sql("select * from calendar where CompanyNum='$CompanyNum' AND Floor ='$FloorId' ","id", "start_date,end_date,text,textColor,color,Type,TypeTitle,Floor,Level,AgentId,CompanyNum,Status,GuideName,FloorName,ClientId");    
    }
    else {

    $scheduler->render_sql("select * from calendar where CompanyNum='$CompanyNum' ","id", "start_date,end_date,text,textColor,color,Type,TypeTitle,Floor,Level,AgentId,CompanyNum,Status,GuideName,FloorName,ClientId");    

        
    }

			
?>