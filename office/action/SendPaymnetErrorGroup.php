<?php

require_once '../../app/initcron.php';

$CompanyNum = Auth::user()->CompanyNum;


			DB::table('payment')
		   ->where('Status', '2')
           ->where('CompanyNum', '=', $CompanyNum)
           ->where('TryDate', '!=', date('Y-m-d'))        
           ->update(array('Status' => '2', 'TryDate' => date('Y-m-d')));	



echo 'הפקודה נשלחה בהצלחה';

	
?>
