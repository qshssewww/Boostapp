<?php

require_once '../../app/init.php';

$updateRecordsArray 	= $_POST['recordsArray'];

	
	$listingCounter = 1;
	foreach ($updateRecordsArray as $recordIDValue) {
		
		
		DB::table('RavMesserLists')
		   ->where('CompanyNum', Auth::user()->CompanyNum)
		   ->where('ItemId', Auth::user()->ItemId)
           ->where('ListId', $recordIDValue)
           ->update(array('Sort' => $listingCounter));
		
		$listingCounter = $listingCounter + 1;	
	}
	
	echo '<div id="status" class="alert alert-primary text-right" role="alert" dir="rtl" style="font-weight:bold;">הסדר עודכן בהצלחה!</div>';
?>