<?php
require_once '../../app/initcron.php';

$CompanyNum = Auth::user()->CompanyNum;
$GroupNumber = $_REQUEST['GroupNumber'];
$Vaule = $_REQUEST['Vaule'];
$GroupNum = $_REQUEST['GroupNum'];
$Type = $_REQUEST['Type'];
$Num = $_REQUEST['Num'];

//// מחיקת סימונים ישנים
DB::table('templistclass_option')->where('CompanyNum', '=', $CompanyNum)->where('Type', '=', $Type)->where('GroupNum', '=', $GroupNum)->where('GroupNumber', '=', $GroupNumber)->where('Num', '=', $Num)->delete();

//// עדכון פריטים קיימים
  
DB::table('templistclass_option')->insertGetId(
array('CompanyNum' => $CompanyNum, 'GroupNum' => $GroupNum, 'GroupNumber' => $GroupNumber, 'ClassId' => $Vaule, 'Type' => $Type, 'Num' => $Num) ); 


?>