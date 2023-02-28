<?php

    ini_set('display_errors', 1);
    include('config.php');
	include ('scheduler_connector.php');


    $newdate = strtotime($_REQUEST['date']);
	$year = date('Y', $newdate);
	$month = date('m', $newdate);
	$day = date('d', $newdate);
	
    $res=mysql_connect($mysql_server,$mysql_user,$mysql_pass); 
    mysql_select_db($mysql_db);
		
    $list = new OptionsConnector($res);
	//$list->render_table("weekly_work_schedule","section_id","section_id(value),user_name(label)");
	
	$sql = "SELECT id as value, firstname as label from workers where Status='0' ORDER BY firstname ASC";
    $list->render_sql($sql,"value","value,label");
	
	$scheduler = new schedulerConnector($res);
    //$scheduler->enable_log("log.txt",true);
	
	$scheduler->set_options("sections", $list);
    $scheduler->render_table("weekly_work_schedule","sectionid","start_date,end_date,text,section_id,StandBy,Extra,Holiday,Details,color,textColor,color2,end_time,start_time,start_dates");
	
    
			
?>