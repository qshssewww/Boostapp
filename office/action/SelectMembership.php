<?php
require_once '../../app/initcron.php';

$CompanyNum = Auth::user()->CompanyNum;
$GroupNumber = $_REQUEST['GroupNumber'];

$CheckItems = DB::table('templistmember')->where('CompanyNum','=', $CompanyNum)->where('GroupNumber','=', $GroupNumber)->get();
if (!empty($CheckItems)){ 
$GetClasess = '';    
foreach ($CheckItems as $CheckItem){
$GetClasess .= $CheckItem->ClassId.',';    
}
    
$Activity = rtrim($GetClasess, ',');    
$TrueClasess = explode(',', $Activity); 
    
$Items = DB::table('membership_type')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->whereNotIn('id', $TrueClasess)->select('id as id', 'Type as text')->get();
echo '{"results": '.json_encode($Items).'}';
    
}

else {
    
$Items = DB::table('membership_type')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->select('id as id', 'Type as text')->get();
echo '{"results": '.json_encode($Items).'}';
    
}
?>