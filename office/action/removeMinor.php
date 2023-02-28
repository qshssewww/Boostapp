<?php
require_once '../../app/init.php';
require_once '../Classes/Client.php';
$minorId = isset($_POST['minor-client-id']) ? $_POST['minor-client-id'] : '';
if(empty($minorId)) {
    echo json_encode(array('success' => false, 'msg' => 'לא נמצא לקוח')); 
    exit;
}

$minorClient = new Client($minorId);
if(empty($minorClient)) {
    echo json_encode(array('success' => false, 'msg' => 'לא נמצא לקוח'));
    exit;
}

$status = isset($_POST['archive_minor']) ? $_POST['archive_minor'] : '0';
$mobile = $_POST['minor-ContactMobile'];
$mobile = substr($mobile, 0, 1) == '0' ? substr($mobile, 1, strlen($mobile)) : $mobile;
$email = isset($_POST['minor-email']) ? $_POST['minor-email'] : '';

if($status == '0') {
    //// active
    $resArr = $minorClient->updateMinorDetails($mobile, $email);
} else {
    //// archive
    $resArr = $minorClient->archiveMinorClient($mobile, $email);
}
echo json_encode($resArr);

?>