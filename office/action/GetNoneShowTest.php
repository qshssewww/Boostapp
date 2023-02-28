<?php

require_once '../../app/initcron.php';

$CompanyNum = Auth::user()->CompanyNum;
$Act = $_REQUEST['Act'];

if ($Act=='0'){
  
$TotalNoneShow = '0';          
$StartDate = '2018-04-18';
$EndDate = '2018-05-18';  
        
$StartDateWeek = '2018-05-01';
$EndDateWeek = '2018-05-18';      
        
$ClientShowMonthCounts = DB::table('classstudio_act')->where('CompanyNum','=',$CompanyNum)->get();        
foreach ($ClientShowMonthCounts as $ClientShowMonthCount) {


    
}   
}
    
     
?>