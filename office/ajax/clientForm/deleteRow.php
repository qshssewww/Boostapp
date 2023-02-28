<?php
require_once '../../Classes/Company.php';
require_once "../../Classes/ClientForm.php";
require_once "../../Classes/ClientFormFields.php";
require_once "../../Classes/FormFields.php";
require_once '../../../app/init.php';
header("Content-Type: application/json", true);
if (Auth::guest()) exit;
if (Auth::check()) {
    if (Auth::userCan('31')) {
    $postdata = file_get_contents("php://input");
    $obj = json_decode($postdata);
    $field_id=$obj->id;
    $form_id=$obj->form_id;
    $clientFormFields = new ClientFormFields();
    $formFields= new FormFields();
    $data=[];
    $data[]=$clientFormFields->deleteViaFormAndFieldId($form_id,$field_id);
    $data[]=$formFields->deleteViaFieldId($field_id);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}
}
?>
