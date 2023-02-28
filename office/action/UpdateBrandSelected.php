<?php
require_once '../../app/initcron.php';

$UserId = Auth::user()->id;
$CompanyNum = Auth::user()->CompanyNum;

$BrandId = $_POST['BrandId'] ?? '';
if (empty($BrandId)) {
    echo "brand not found";
    exit;
}

$ItemDetails = DB::table('brands')->where('id', $BrandId)->first();
if (empty($ItemDetails)) {
    echo "brand not found";
    exit;
}

DB::table('users')
    ->where('id', $UserId)
    ->where('CompanyNum', $CompanyNum)
    ->update(array('CompanyNum' => $ItemDetails->FinalCompanynum));


CreateLogMovement(
    'החליף את הסניף לסניף <u>' . $ItemDetails->BrandName . '</u>', //LogContent
    '0' //ClientId
);
