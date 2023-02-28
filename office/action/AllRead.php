<?php

require_once '../../app/initcron.php';

$CompanyNum = Auth::user()->CompanyNum;
$userid = Auth::user()->id;
$time = date('Y-m-d G:i:s');
$Act = $_REQUEST['Act'];


if ($Act=='0'){


	DB::table('appnotification')
        ->where('Status', '0')
        ->where('Type', '3')
        ->where('CompanyNum', '=', $CompanyNum)
        ->update(array('Status' => '1'));


}

else {
	
	$ReminderId = $_REQUEST['ReminderId'];

	DB::table('appnotification')
        ->where('id', $ReminderId)
        ->where('Type', '3')
        ->where('CompanyNum', '=', $CompanyNum)
        ->update(array('Status' => '1'));	
	
}

echo '<div class="text-center"><span class="font-weight-bold">'.lang('no_new_notifications_modal').'</span></div>';

