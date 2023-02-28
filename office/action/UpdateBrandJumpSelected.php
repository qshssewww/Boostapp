<?php
require_once '../../app/initcron.php';

$UserId = Auth::user()->id;
$CompanyNum = Auth::user()->CompanyNum;
$BrandId = $_REQUEST['BrandId'];

$JumpBrandsId = Auth::user()->JumpBrandsId;
$role_id = DB::table('roles')->where('CompanyNum', '=', $JumpBrandsId)->orderBy('id','ASC')->first();
$ItemDetailsHeader = DB::table('settings')->where('CompanyNum', '=', $JumpBrandsId)->where('Status', '=', '0')->first();

	DB::table('users')
        ->where('id', $UserId)
        ->where('CompanyNum', $CompanyNum)
        ->update(array('CompanyNum' => $JumpBrandsId, 'role_id' => $role_id->id, 'JumpBrandsId' => $CompanyNum));



CreateLogMovement(
	'החליף את הסניף לסניף <u>'.@$ItemDetailsHeader->AppName.'</u>', //LogContent
	 '0' //ClientId
);
?>
