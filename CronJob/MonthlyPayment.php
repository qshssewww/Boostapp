<?php

require_once "../app/init.php";
require_once "../office/Classes/MeshulamPayments.php";
require_once "../office/Classes/Settings.php";
require_once "../office/Classes/MeshulamUtils.php";
require_once "../office/Classes/Transaction.php";
require_once "../office/Classes/TransactionError.php";
require_once "../office/Classes/YaadUtils.php";
require_once "../office/Classes/Client.php";
require_once "../office/Classes/Functions.php";
require_once "../office/Classes/PaymentPage.php";
require_once "../office/Classes/Item.php";

//$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
//require_once $_SERVER['DOCUMENT_ROOT'] . '/app/initcron.php';

$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();

$paymentsDue = MeshulamPayments::getPaymentsDue();
$settingsObj = new Settings();
$clientObj = new Client();
$meshulam = new MeshulamUtils();
$func = new Functions();
$yaad = new YaadUtils();
foreach ($paymentsDue as $payment) {
    $error = 0;
    if($payment->last_payment_num < $payment->total_payments || $payment->total_payments == -1) {
        $client = $clientObj->getRow($payment->client_id);
        $settingsObj = new Settings($payment->company_num);
        $paymentPageObj = new PaymentPage();
        $itemObj = new Item();
        $paymentPage = $paymentPageObj->getRow($payment->payment_page);
        $item = $itemObj->getRow($paymentPage->ItemId);
        if ($settingsObj->__get("TypeShva") == 1) {
            $res = $meshulam->createTransactionWithToken($payment, "376e8f46589f62aa");
            if($res["err"] != ""){
                $error = 1;
            }
            else {
                $approve = $meshulam->approveTransactions($res);
            }
        } else {
            $YaadNumber = "0010158521"; // temp
            $res = $yaad->createTransactionWithToken($payment,$YaadNumber);
            if($res["CCode"] != 0){
                $error = 1;
            }
        }
        if (!$error) {
            $transaction = (new Transaction([
                "CompanyNum" => $payment->company_num,
                "ClientId" => $payment->client_id,
                "UpdateTransactionDetails" => ($settingsObj->__get("TypeShva") == 1) ? json_encode($res["data"], JSON_UNESCAPED_UNICODE) : json_encode($res, JSON_UNESCAPED_UNICODE),
            ]));
            $transaction->save();

            $tranId = $transaction->id;
            $transArr = json_decode($payment->transactions, true);
            array_push($transArr, $tranId);
            $lastPayment = $payment->last_payment_num + 1;
            $status = 1;
            if ($lastPayment == $payment->total_payments) {
                $status = 2;
            }
            $updateMeshulam = array(
                "amount_paid" => ($settingsObj->__get("TypeShva") == 1) ? $payment->amount_paid + $res["data"]["sum"] : $payment->amount_paid + $res["Amount"],
                "last_payment_date" => Date('Y-m-d H:i:s'),
                "last_payment_num" => $lastPayment,
                "transactions" => json_encode($transArr, JSON_UNESCAPED_UNICODE),
                "last_payment_status" => 1,
                "status" => $status
            );
            $update = MeshulamPayments::staticUpdate($payment->id, $updateMeshulam);
            $docData = $func->docsAfterPayment($payment->company_num,$client,$payment,$item);
            $docPayment = $func->docsPayments($docData,$res,$payment,$tranId);
            $docToItem = $func->docsToItems($docData,$payment,$item);
        } else {
            $transactionError = (new TransactionError([
                "CompanyNum" => $payment->company_num,
                "ClientId" => $payment->client_id,
                "UpdateTransactionDetails" => ($settingsObj->__get("TypeShva") == 1) ? json_encode($res["err"], JSON_UNESCAPED_UNICODE) : json_encode($res, JSON_UNESCAPED_UNICODE),
                "Error" => ($settingsObj->__get("TypeShva") == 1) ? $res["err"]["message"] : $res["errMsg"],
            ]));
            $transactionError->save();

            $FailedId = $transactionError->id;
            $traErrArr = json_decode($payment->failed_transactions);
            array_push($traErrArr, $FailedId);
            $transactionData = array(
                "failed_transactions" => json_encode($traErrArr, JSON_UNESCAPED_UNICODE),
                "last_payment_status" => 0
            );
            $update = MeshulamPayments::staticUpdate($payment->id, $transactionData);
        }
    }
}

$Cron->end();
