<?php

    require_once '../../../app/initcron.php';

    $UserId = Auth::user()->id;
	$CompanyNum = Auth::user()->CompanyNum;


    ini_set('display_errors', 1);
    include('config.php');
	include ('scheduler_connector.php');
    require("db_mysqli.php");
	
	$mysqli = new mysqli($mysql_server, $mysql_user, $mysql_pass, $mysql_db); 

	$scheduler = new schedulerConnector($mysqli,"MySQLi");

    $list = new OptionsConnector($mysqli,"MySQLi");


	$sql = "SELECT
	id AS value,
    Title AS label
    FROM
    sections 
	
	WHERE Status='0' AND CompanyNum='".$CompanyNum."' ORDER BY Floor DESC
	
	";

    $list->render_sql($sql,"value","value,label");
	
	$scheduler->event->attach("afterInsert","doAfterProcessing");
	
	$scheduler->set_options("sections", $list);
    $scheduler->set_options("sectionslists", $list);
    $scheduler->render_sql("select * from classstudio_date where CompanyNum='$CompanyNum' AND Floor !='0' AND Status !='2' ","id", "start_date,end_date,text,textColor,color,Floor,ShowApp,ClassName,ClassLevel,GuideName,MinClass,MaxClient,Status,GuideId,ClassWating,ClientRegister,WatingList");


?>