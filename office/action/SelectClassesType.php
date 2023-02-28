<?php
require_once '../../app/initcron.php';

$CompanyNum = Auth::user()->CompanyNum;
$GroupNumber = $_REQUEST['GroupNumber'];
$Type = $_REQUEST['Type'];
$GroupNum = $_REQUEST['GroupNum'];

$CheckItems = DB::table('templistclass_option')->where('CompanyNum','=', $CompanyNum)->where('GroupNum','=', $GroupNum)->where('GroupNumber','=', $GroupNumber)->where('Type','=', $Type)->get();

if (!empty($CheckItems)){ 
    
$CountMax = DB::table('templistclass_option')->where('CompanyNum','=', $CompanyNum)->where('GroupNum','=', $GroupNum)->where('GroupNumber','=', $GroupNumber)->where('Type','=', '0')->where('ClassId','=', '1')->count();   
    
$CountTime = DB::table('templistclass_option')->where('CompanyNum','=', $CompanyNum)->where('GroupNum','=', $GroupNum)->where('GroupNumber','=', $GroupNumber)->where('Type','=', '0')->where('ClassId','=', '3')->count();       
    
$GetClasess = '';  
$i = '1';    
foreach ($CheckItems as $CheckItem){
if ($CountMax<'6' && $CheckItem->ClassId=='1' || $CountTime<'3' && $CheckItem->ClassId=='3'){}else {    
$GetClasess .= $CheckItem->ClassId.',';  
}
++$i; }
    
$Activity = rtrim($GetClasess, ',');    
$TrueClasess = explode(',', $Activity); 
    
$Items = DB::table('templistclass_data')->where('Type','=', $Type)->whereNotIn('id', $TrueClasess)->select('id as id', 'Text as text')->get();
echo '{"results": '.json_encode($Items).'}';
    
}

else {
    
$Items = DB::table('templistclass_data')->where('Type','=', $Type)->select('id as id', 'Text as text')->get();
echo '{"results": '.json_encode($Items).'}';
    
}
?>