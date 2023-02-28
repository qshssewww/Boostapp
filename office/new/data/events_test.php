<?php

   require_once('../load.php');


    $newdate = strtotime($_REQUEST['date']);
	$year = date('Y', $newdate);
	$month = date('m', $newdate);
	$day = date('d', $newdate);

	
	$sql = "SELECT
    PR.section_id AS value,
    PH.nick_name AS label
FROM
    weekly_work_schedule PR
LEFT OUTER JOIN users PH ON
    PH.userid = PR.section_id
	
	WHERE YEAR(PR.start_date) = '$year' AND MONTH(PR.start_date) = '$month' AND DAY(PR.start_date) = '$day' and PR.work='true' ORDER BY PH.userid ASC
	
	";
	
	
    $CheckresultGuide = mysql_query($sql);
	$results = array();
	while ($rowsCheckresultGuide = mysql_fetch_assoc($CheckresultGuide)) {
		
   $results[] = array('key' => $rowsCheckresultGuide['value'], 'label' => $rowsCheckresultGuide['label']);
		
	}

	$object = json_encode($results);
	
   print_r($object); 
			
?>