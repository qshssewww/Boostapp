<?php
require_once '../../app/initcron.php';

$userid = Auth::user()->id;
$CompanyNum = Auth::user()->CompanyNum;
$time = date('Y-m-d G:i:s');
$ClassId = $_REQUEST['ClassId'];
$SortRow = $_REQUEST['SortRow'];


$i='1';
foreach ($SortRow as $value) {

           DB::table('classstudio_act')
           ->where('id', '=', $value)
           ->where('ClassId', '=', $ClassId)
           ->where('Status', '=', '9')
           ->where('CompanyNum', $CompanyNum)  
           ->update(array('WatingListSort' => $i));   
    
    
    
    $i++;
}


//// לוג מערכת
	$LogUserId = Auth::user()->id;
	$CompanyNum = Auth::user()->CompanyNum;
	$ClientIds = '0';
	$LogDateTime = date('Y-m-d H:i:s');
	$LogContent = 'ערך סדר רשימת המתנה';
	DB::table('log')->insert(array('UserId' => $LogUserId, 'Text' => $LogContent, 'Dates' => $LogDateTime, 'ClientId' => $ClientIds, 'CompanyNum' => $CompanyNum, 'ClassId' => $ClassId));
?>
