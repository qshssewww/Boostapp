<?php
require_once '../../app/initcron.php';

$CompanyNum = Auth::user()->CompanyNum;
$GroupNumber = $_REQUEST['GroupNumber'];

$GroupNum = $_REQUEST['GroupNum'];

//// מחיקת סימונים ישנים
DB::table('templistmember')->where('CompanyNum', '=', $CompanyNum)->where('GroupNum', '=', $GroupNum)->where('GroupNumber', '=', $GroupNumber)->delete();

?>