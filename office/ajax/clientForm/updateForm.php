<?php
require_once "../../Classes/ClientForm.php";
require_once "../../Classes/ClientFormFields.php";
require_once "../../Classes/FormFields.php";
require_once '../../../app/init.php';

if (Auth::check()) {
    $userId = Auth::user()->id;
    $CompanyNum = Auth::user()->CompanyNum;
    $data = isset($_POST["form"]) ? $_POST : null;
    if($data == null){
        exit;
    }
    $message = "";
    switch ($data["form"]){
        case 1:
            foreach ($data as $field){
                if($field == "1"){
                    continue;
                }
                $formFields = new FormFields();
                $clientFields = new ClientFormFields();
                $clientFields->__set("form_id",$field["form_id"]);
                $clientFields->__set("show",$field["display"]);
                $clientFields->__set("mandatory",$field["require"]);
                $clientFields->__set("order",$field["order"]);
                $formFields->__set("name",$field["name"]);
                $clientFields->__set("type",$field["type"]);
                $clientFields->__set("options",null);
                if($field["type"] == "list" || $field["type"] == "radio"){
                    if(isset($field["options"])){
                        $clientFields->__set("options",json_encode($field["options"], JSON_UNESCAPED_UNICODE));
                    }
                    else{
                        $message = "Field Name " . $field["name"] . " Need options";
                        echo $message;
                    }
                }
                if(isset($field["id"]) && $field["id"] != ""){
                    $formFields->__set("field_id",$field["id"]);
                    $formFields->updateFormField();
                    $clientFields->__set("field_id",$field["id"]);
                    $clientFields->updateClientFormFields();
                }
                else{
                    $fieldId = $formFields->insertFormField();
                    if($fieldId != 0) {
                        $clientFields->__set("field_id", $fieldId);
                        $clientFieldId = $clientFields->insertClientFormField();
                    }
                    else{
                        $message = "Something Went Wrong With Field Name " . $field["name"];
                        echo $message;
                    }

                }
            }
            break;
        default:
            $message = "Something Went Wrong";
            break;
    }
    echo $message;
}
?>
