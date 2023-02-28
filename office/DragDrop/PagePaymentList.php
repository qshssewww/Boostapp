<?php

require_once '../../app/init.php';

$updateRecordsArray = $_POST['recordsArray'];

	
	$listingCounter = 1;
	foreach ($updateRecordsArray as $recordIDValue) {
		
       $listingCounter = sprintf("%02d", $listingCounter); 
		
		DB::table('payment_pages')
           ->where('id', $recordIDValue)   
		   ->where('CompanyNum', '=', Auth::user()->CompanyNum)
           ->update(array('Sort' => $listingCounter));
		
		$listingCounter = $listingCounter + 1;
        $listingCounter = sprintf("%02d", $listingCounter);
	}
	
	echo '<div id="status" class="alert alert-primary text-right" role="alert" dir="rtl" style="font-weight:bold;">הסדר עודכן בהצלחה!</div>';
?>