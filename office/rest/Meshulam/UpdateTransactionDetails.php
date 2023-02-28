<?php
require_once '../../../app/init.php';

//$UpdateTransactionDetails = print_r($_POST, true);

$UpdateTransactionDetails = serialize($_POST);

$CompanyNum = $_REQUEST['CompanyNum'];
$ClientId = $_REQUEST['ClientId'];
$UserId = $_REQUEST['UserId'];

//// קליטת נתונים בטבלה

$myarray =  unserialize($UpdateTransactionDetails);

$Status = $myarray['status'];
$err = $myarray['err'];

if ($Status=='1') {
    
$InsertTransaction = DB::table('transaction')->insertGetId(
array('CompanyNum' => $CompanyNum, 'ClientId' => $ClientId, 'UpdateTransactionDetails' => $UpdateTransactionDetails, 'UserId' => $UserId));
 
$Token = $myarray['data']['card_token_key'];
$Tokef = $myarray['data']['card_exp'];
$L4digit = $myarray['data']['card_suffix'];    
      
$InsertToken = DB::table('token')->insertGetId(
array('CompanyNum' => $CompanyNum, 'ClientId' => $ClientId, 'Token' => $Token, 'Tokef' => $Tokef, 'YaadCode' => '0', 'UserId' => $UserId, 'L4digit' => $L4digit, 'Type' => '1', 'TransactionId' => $InsertTransaction )
);    
    
    
}
else {
    
DB::table('transaction_error')->insertGetId(
array('CompanyNum' => $CompanyNum, 'ClientId' => $ClientId, 'UpdateTransactionDetails' => $UpdateTransactionDetails, 'UserId' => $UserId));    
    
}

