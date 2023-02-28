<?php 

$checkValue = $_GET['checkValue']; 

//// Check 
$CheckqueryGuide  = "SELECT * FROM `weekly_work_schedule` WHERE work_date = CURRENT_DATE and section_id='$checkValue'";
$CheckresultGuide = mysql_query($CheckqueryGuide);
$Check_count=mysql_num_rows($CheckresultGuide);

if ($Check_count=='0'){ 

$queryu="Update weekly_work_schedule set
work='false'
where section_id='".$checkValue."' ";
mysql_query($queryu) or die(mysql_error());

}

else {		
	
$queryu="Update weekly_work_schedule set
work='true'
where section_id='".$checkValue."' ";
mysql_query($queryu) or die(mysql_error());

}


?>