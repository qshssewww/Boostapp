<?php
require_once '../../app/initcron.php';
$CompanyNum = Auth::user()->CompanyNum;

$ChatCountNew = DB::table('chat')->where('CompanyNum' ,'=', $CompanyNum)->where('Status', '=', '0')->where('SendFrom', '=', '1')->count();

echo $ChatCountNew;
?>