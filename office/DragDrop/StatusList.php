<?php

require_once '../../app/init.php';

$updateRecordsArray = $_POST['recordsArray'];
$PipeId = $_POST['PipeId'];

	
	$listingCounter = 1;
	foreach ($updateRecordsArray as $recordIDValue) {
		
		
		DB::table('leadstatus')
           ->where('id', $recordIDValue)
           ->where('PipeId', $PipeId)    
		   ->where('CompanyNum', '=', Auth::user()->CompanyNum)
           ->update(array('Sort' => $listingCounter));
		
		$listingCounter = $listingCounter + 1;	
	}
	
	echo '<div id="status" class="alert alert-primary text-right" role="alert" dir="rtl" style="font-weight:bold;">הסדר עודכן בהצלחה!</div>';
?>