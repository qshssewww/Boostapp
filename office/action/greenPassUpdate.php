<?php
require_once '../../app/init.php';
require_once '../Classes/Client.php';

$clientId = isset($_POST['clientId']) ? $_POST['clientId'] : null;
if(!$clientId) {
    echo json_encode(array('success' => false, 'msg' => lang('client_not_found')), true); 
    exit;
}
$client = new Client($clientId);
if(!$client->__get('id')) {
    echo json_encode(array('success' => false, 'msg' => lang('client_not_found')), true); 
    exit;
}
$status = isset($_POST['status']) ? $_POST['status'] : 0;
$validDate = isset($_POST['date']) ? $_POST['date'] : null;
if($validDate && strtotime($validDate) <= strtotime(date('Y-m-d')) && $status != 0) {
    echo json_encode(array('success' => false, 'msg' => lang('invalid_date_dashboard')), true); 
    exit;
}
if($status && !$validDate) {
    echo json_encode(array('success' => false, 'msg' => lang('invalid_date_dashboard')), true); 
    exit;
}
if(!$status) {
    $response = $client->updateGreenPass($status);
} else {
    $response = $client->updateGreenPass($status, $validDate);
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);