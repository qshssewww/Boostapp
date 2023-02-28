




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
    $userId = Auth::user()->id;
    $postdata = file_get_contents("php://input");
    $obj = json_decode($postdata);
    $data = array();
    $company = Company::getInstance();
    $company_num =  $company->__get("CompanyNum");
    $form = new ClientForm();
    $type = $obj->type;
    $form->getCompanyForm($company_num,$type,$userId);
    $data['form']=$form->getCompanyFormAsArray();
    $clientForm = new ClientFormFields();
    $fields = $clientForm->getClientFormFields($type,$form->__get("form_id"));
    $parsedFields=[];
    foreach ($fields as $key => $value) {
        $parsedFields[]=$value->returnThisAsArray();
     }
    $data['fields']=$parsedFields;
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
}
}
?>
