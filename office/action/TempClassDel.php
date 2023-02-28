<?php
require_once '../../app/initcron.php';

$CompanyNum = Auth::user()->CompanyNum;
$GroupNumber = $_REQUEST['GroupNumber'];

$GroupNum = $_REQUEST['GroupNum'];

//// מחיקת סימונים ישנים
DB::table('templistclass')->where('CompanyNum', '=', $CompanyNum)->where('GroupNum', '=', $GroupNum)->where('GroupNumber', '=', $GroupNumber)->delete();
DB::table('templistclass_option')->where('CompanyNum', '=', $CompanyNum)->where('GroupNum', '=', $GroupNum)->where('GroupNumber', '=', $GroupNumber)->delete();

?>