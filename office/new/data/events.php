<?php


    require_once '../../../app/init.php';

    
	$CompanyNum = Auth::user()->CompanyNum;
    $RoleId = Auth::user()->role_id;

    $RoleInfo = DB::table('roles')->where('id','=',$RoleId)->first();
    if (@$RoleInfo->permissions=='*'){
    $SeeAll = '1';    
    } 
    else {
    $SeeAll = '0';     
    }


    ini_set('display_errors', 1);
//    include('config.php');
    $databaseArr = require $_SERVER['DOCUMENT_ROOT'] . '/app/config/database.php';

	include ('scheduler_connector.php');
    require("db_mysqli.php");

    if(isDevEnviroment()){
	    $mysqli = new mysqli($databaseArr['dev']['hostname'], $databaseArr['dev']['username'], $databaseArr['dev']['password'], $databaseArr['dev']['database']);
        mysqli_set_charset($mysqli, $databaseArr['dev']['charset']);
    } else {
        $mysqli = new mysqli($databaseArr['live']['hostname'], $databaseArr['live']['username'], $databaseArr['live']['password'], $databaseArr['live']['database']);
        mysqli_set_charset($mysqli, $databaseArr['live']['charset']);
    }




	$scheduler = new schedulerConnector($mysqli,"MySQLi");

    $UserId = @$_REQUEST['UserId']; 

    if ($UserId==''){
    $UserId = Auth::user()->id;     
    }
    else {
    $UserId = @$_REQUEST['UserId'];
    }
    
    if (@$_REQUEST['UserId']==''){  
    $scheduler->render_sql("select * from calendar where CompanyNum='$CompanyNum' AND AgentId ='$UserId' ","id", "start_date,end_date,text,textColor,color,Type,TypeTitle,Floor,Level,AgentId,CompanyNum,Status,GuideName,FloorName,ClientId");
    } 
    else if (@$_REQUEST['UserId']=='BA999') {
    $scheduler->render_sql("select * from calendar where CompanyNum='$CompanyNum' ","id", "start_date,end_date,text,textColor,color,Type,TypeTitle,Floor,Level,AgentId,CompanyNum,Status,GuideName,FloorName,ClientId");     
    }
    else if (@$_REQUEST['UserId']=='BV999') {
    $scheduler->render_sql("select * from calendar where CompanyNum='$CompanyNum' AND FIND_IN_SET('$RoleId',`GroupPermission`) > 0 ","id", "start_date,end_date,text,textColor,color,Type,TypeTitle,Floor,Level,AgentId,CompanyNum,Status,GuideName,FloorName,ClientId");     
    }
	else {
    $scheduler->render_sql("select * from calendar where CompanyNum='$CompanyNum' AND AgentId ='$UserId' ","id", "start_date,end_date,text,textColor,color,Type,TypeTitle,Floor,Level,AgentId,CompanyNum,Status,GuideName,FloorName,ClientId");     
    }		
?>