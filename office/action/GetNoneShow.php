<?php

require_once '../../app/initcron.php';

$CompanyNum = Auth::user()->CompanyNum;
$Act = $_REQUEST['Act'];

if ($Act=='0'){
  
$TotalNoneShow = '0';        
$dt = date('Y-m-d',strtotime("-1 Months"));     
$StartDate = date("Y-m-d", strtotime($dt)); 
$EndDate = date("Y-m-d");  
        
$StartDateWeek = date( 'Y-m-d', strtotime( 'sunday last week -1 week' ) );
$EndDateWeek = date( 'Y-m-d', strtotime( 'saturday last week' ) );          
        
$ClientShowMonthCounts = DB::table('client')->where('CompanyNum','=',$CompanyNum)->where('Status','=','0')->whereBetween('LastClassDate', array($StartDate, $EndDate))->select('id')->get();        
foreach ($ClientShowMonthCounts as $ClientShowMonthCount) {

$ClientNoneShowMonthCount = DB::table('classstudio_act')->where('ClientId','=',$ClientShowMonthCount->id)->where('CompanyNum','=',$CompanyNum)->whereBetween('ClassDate', array($StartDateWeek, $EndDateWeek))->where('StatusCount','=','0')->select('id')->count(); 
    
if ($ClientNoneShowMonthCount=='0'){
$TotalNoneShow += '1';  
}
      
}      
    
}
else if ($Act=='1'){
 
    
$TotalNoneShow = '0';        
$dt = date('Y-m-d',strtotime("-2 Months"));     
$StartDate = date("Y-m-d", strtotime($dt)); 
$EndDate = date("Y-m-d");  
        
$StartDateWeek = date('Y-m-d',strtotime("-1 Months")); 
$EndDateWeek = date( 'Y-m-d');          
        
$ClientShowMonthCounts = DB::table('client')->where('CompanyNum','=',$CompanyNum)->where('Status','=','0')->whereBetween('LastClassDate', array($StartDate, $EndDate))->select('id')->get();        
foreach ($ClientShowMonthCounts as $ClientShowMonthCount) {

$ClientNoneShowMonthCount = DB::table('classstudio_act')->where('ClientId','=',$ClientShowMonthCount->id)->where('CompanyNum','=',$CompanyNum)->whereBetween('ClassDate', array($StartDateWeek, $EndDateWeek))->where('StatusCount','=','0')->select('id')->count(); 
    
if ($ClientNoneShowMonthCount=='0'){
$TotalNoneShow += '1';  
}

    
}     
    
}


echo $TotalNoneShow;