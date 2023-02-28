<?php
require_once '../../../app/init.php';

//$UpdateTransactionDetails = print_r($_POST, true);

$UpdateTransactionDetails = serialize($_POST);

$CompanyNum = $_REQUEST['CompanyNum'];
$ClientId = $_REQUEST['ClientId'];
$UserId = $_REQUEST['UserId'];
$TypeDoc = $_REQUEST['TypeDoc'];

//// קליטת נתונים בטבלה

$myarray =  unserialize($UpdateTransactionDetails);

$Status = $myarray['status'];
$err = $myarray['err'];

if ($Status=='1') {
    
$InsertTransaction = DB::table('transaction')->insertGetId(
array('CompanyNum' => $CompanyNum, 'ClientId' => $ClientId, 'UpdateTransactionDetails' => $UpdateTransactionDetails, 'UserId' => $UserId));
   
    
                    $Dates= date('Y-m-d H:i:s');
                    $UserDate= date('Y-m-d');
    
                    $CCode = '0';
                    $L4digit = $myarray['data']['card_suffix'];
                    $YaadCode = $myarray['data']['id'];
                    $PayToken = $myarray['data']['token'];
                        
                    $Bank = '9'; /// משולם
                    $Brand = '0';
                    $Issuer = '0';
                        
                    if ($myarray['data']['card_type']=='Local'){
                    $Local = 'ישראלי';    
                    }    
                    else {
                    $Local = 'תייר';    
                    }   
                        
                    $BrandName = 'כרטיס '.$myarray['data']['card_brand'].' - '.$Local;  
                        
                    $ACode =  $myarray['data']['asmachta'];   
                    $tashType = $myarray['data']['payment_type'];
                    $Payments = $myarray['data']['all_payments_num']; 
                    $Amount = str_replace(',','',$myarray['data']['payment_sum']); 
                    $CreditType = 'עסקה טלפונית';
    
                    if ($tashType=='2'){
                    $tashTypeDB = '1';     
                    }    
                    else if ($tashType=='4') {
                    $tashTypeDB = '2'; 
                    }         
                    else {
                    $tashTypeDB = '5';     
                    } 
     
    
DB::table('temp_receipt_payment_client')->insertGetId(
array('CompanyNum' => $CompanyNum, 'TypeDoc' => $TypeDoc, 'TempId' => $ClientId, 'TypePayment' => '3', 'Amount' => $Amount, 'L4digit' => $L4digit, 'YaadCode' => $YaadCode, 'CCode' => $CCode, 'ACode' => $ACode, 'Bank' => $Bank, 'Payments' => $Payments, 'Brand' => $Brand, 'BrandName' => @$BrandName, 'Issuer' => $Issuer, 'tashType' => $tashTypeDB, 'CheckDate' => $UserDate, 'Dates' => $Dates, 'UserId' => $UserId, 'UserDate' => $UserDate, 'CreditType' => $CreditType, 'PayToken' => $PayToken, 'TransactionId' => $InsertTransaction));           
    
    
}
else {
    
DB::table('transaction_error')->insertGetId(
array('CompanyNum' => $CompanyNum, 'ClientId' => $ClientId, 'UpdateTransactionDetails' => $UpdateTransactionDetails, 'UserId' => $UserId));    
    
}

