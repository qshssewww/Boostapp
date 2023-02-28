<?php
require_once '../../app/initcron.php';

$CompanyNum = Auth::user()->CompanyNum;
$GroupNumber = $_REQUEST['GroupNumber'];
$Clases = $_REQUEST['Clases'];
$GroupNum = $_REQUEST['GroupNum'];

//// מחיקת סימונים ישנים
DB::table('templistmember')->where('CompanyNum', '=', $CompanyNum)->where('GroupNum', '=', $GroupNum)->where('GroupNumber', '=', $GroupNumber)->delete();

//// עדכון פריטים קיימים

$GetClasses = explode(',', $_REQUEST['Clases']);
foreach ($GetClasses as $ClassId) {
    
DB::table('templistmember')->insertGetId(
array('CompanyNum' => $CompanyNum, 'GroupNum' => $GroupNum, 'GroupNumber' => $GroupNumber, 'ClassId' => $ClassId) ); 

}

?>