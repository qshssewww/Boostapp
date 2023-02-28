<?php
require_once '../../app/init.php';;
require_once '../Classes/Client.php';
require_once '../services/ClientService.php';

if (Auth::check()):

$validator = Validator::make(

    array('Status' => $_POST['newValue'], 'ClientId' => $_POST['ClientId'], 'CompanyNum' => $_POST['CompanyNum']),
    array('Status' => 'required|integer|between:0,3', 'ClientId' => 'required|exists:boostapp.client,id|integer', 'CompanyNum' => 'required|integer')

);

if ($validator->passes()) {
    /** @var Client $Client */
    $Client = Client::find($_POST['ClientId']);
    $status = (int)$_POST['newValue'];
    $reasonId = $_REQUEST['ReasonId'] ?? 0;
    $reasonText = $_REQUEST['ReasonText'] ?? '';

    if(ClientService::updateStatus($Client, $status, (int)$reasonId, $reasonText)) {
        json_message();
    } else {
        json_message('לא עודכן סטטוס הלקוח', false);
    }
} else {
    json_message($validator->errors()->toArray(), false);
}
endif;