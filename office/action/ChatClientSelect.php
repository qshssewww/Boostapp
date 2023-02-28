<?php
require_once '../../app/initcron.php';

if (@$_REQUEST['q'] != '') {
$Agents = DB::table('client')->where('CompanyNum', '=', Auth::user()->CompanyNum)->where('CompanyName','like', '%'.@$_REQUEST['q'].'%')->Orwhere('Email','like', '%'.@$_REQUEST['q'].'%')->where('CompanyNum', '=', Auth::user()->CompanyNum)->Orwhere('ContactMobile','like', '%'.@$_REQUEST['q'].'%')->where('CompanyNum', '=', Auth::user()->CompanyNum)->orderBy('CompanyName', 'ASC')->select('id as id', 'CompanyName as text')->get();

echo '{"results": '.json_encode($Agents).'}';
}

else {
echo '{"results": []}';
}
?>