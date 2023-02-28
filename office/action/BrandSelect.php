<?php
require_once '../../app/initcron.php';


$CompanyNum = Auth::user()->CompanyNum;

$SettingsInfo = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();

$Cities = DB::table('brands')->where('Status', '=', '0')->where('CompanyNum', '=', $SettingsInfo->BrandsMain)->where('BrandName','like', '%'.@$_REQUEST['q'].'%')->select('id', 'BrandName as text')->get();

echo '{"results": '.json_encode($Cities).'}';
?>