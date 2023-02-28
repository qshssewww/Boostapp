<?php

    ini_set('display_errors', 1);
    include('config.php');
	include ('scheduler_connector.php');

    $res=mysql_connect($mysql_server,$mysql_user,$mysql_pass); 
    mysql_select_db($mysql_db); 
	
    $list = new OptionsConnector($res);
	$list->render_table("types","section_id","section_id(value),name(label)");
	
	$scheduler = new schedulerConnector($res);
    //$scheduler->enable_log("log.txt",true);
	
	$scheduler->set_options("sections", $list);
    $scheduler->render_table("deskplan","id","start_date,end_date,text,details,color,textColor,section_id");
    
	
			
?>