<?php
require_once '../../app/init.php';
require_once '../Classes/Client.php';

$parentId = isset($_POST['parent_client_id']) ? $_POST['parent_client_id'] : '';
if(empty($parentId)) {
    echo json_encode(array('success' => false, 'msg' => 'לא נמצא לקוח')); 
    exit;
}
$parent = new Client($parentId);
if(isset($_POST['exist']) && $_POST['exist'] == true) {
    if(!isset($_POST['exist_minor_id'])) {
        echo json_encode(array('success' => false, 'msg' => 'לא נמצא לקוח')); 
        exit;
    }
    $resposne = $parent->setMinorById($_POST);
} else {
    $resposne = $parent->addMinorClient($_POST);
}

echo json_encode($resposne);

