<?php
require_once '../../app/initcron.php';

$CompanyNum = Auth::user()->CompanyNum;
$GroupNumber = $_REQUEST['GroupNumber'];

$GroupNum = $_REQUEST['GroupNum'];
$Num = $_REQUEST['Num'];

//// מחיקת סימונים ישנים
DB::table('templistclass_option')->where('CompanyNum', '=', $CompanyNum)->where('GroupNum', '=', $GroupNum)->where('Num', '=', $Num)->where('GroupNumber', '=', $GroupNumber)->delete();

?>