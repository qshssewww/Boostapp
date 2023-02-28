<?php

require_once '../../app/init.php';
require_once '../Classes/ClientFormFields.php';
require_once '../Classes/ClientForm.php';
require_once '../Classes/FormFields.php';
require_once "../Classes/Company.php";

header('Content-Type: application/json');

$clientForm = new ClientForm();
$company = Company::getInstance(false);
$companyNum = $company->__get("CompanyNum");

if (Auth::guest()) exit;

if (!empty($_POST["fun"])) {
    switch ($_POST["fun"]) {
        case "GetOrCreateForm":
            if (!isset($_POST["type"])) {
                echo json_encode(["Message" => "type required", "Status" => "Error"]);
            } elseif (!is_numeric($_POST["type"])) {
                echo json_encode(["Message" => "type must be numeric", "Status" => "Error"]);
            } else {
                $type = "";
                switch ($_POST["type"]) {
                    case 0:
                        $type = "client";
                        break;
                    case 1:
                        $type = "lead";
                        break;
                    default:
                        $type = '';
                        break;
                }
                if ($type == '') {
                    echo json_encode(["Message" => "Type not available", "Status" => "Error"]);
                } else {
                    $form = $clientForm->getCompanyForm($companyNum, $type, Auth::user()->id);
                    $res = $clientForm->getFormByCompanyNumAndType($companyNum, $type);
                    echo json_encode(["Message" => $res, "form_id" => $form[0]->form_id, "Status" => "Success"]);
                }
            }
            break;
        case "InsertNewField":
            if (!isset($_POST["form_id"])) {
                echo json_encode(["Message" => "form_id required", "Status" => "Error"]);
            } elseif (!is_numeric($_POST["form_id"])) {
                echo json_encode(["Message" => "form_id must be numeric", "Status" => "Error"]);
            } elseif (empty($_POST["name"])) {
                echo json_encode(["Message" => "name required", "Status" => "Error"]);
            } elseif (!isset($_POST["mandatory"])) {
                echo json_encode(["Message" => "mandatory required", "Status" => "Error"]);
            } elseif ($_POST["mandatory"] != 0 && $_POST["mandatory"] != 1) {
                echo json_encode(["Message" => "mandatory must be 0 or 1", "Status" => "Error"]);
            } elseif (!isset($_POST["show"])) {
                echo json_encode(["Message" => "show required", "Status" => "Error"]);
            } elseif ($_POST["show"] != 0 && $_POST["show"] != 1) {
                echo json_encode(["Message" => "show must be 0 or 1", "Status" => "Error"]);
            } elseif (empty($_POST["type"])) {
                echo json_encode(["Message" => "type required", "Status" => "Error"]);
            } elseif (($_POST["type"] == 'multi' || $_POST["type"] == 'multiCheck') && !isset($_POST["options"])) {
                echo json_encode(["Message" => "type = ".$_POST['type']." so options are required", "Status" => "Error"]);
            } elseif (isset($_POST["options"]) && !is_array($_POST["options"])) {
                echo json_encode(["Message" => "options must be an array", "Status" => "Error"]);
            } else {
                $form = $clientForm->getFormById($_POST["form_id"]);
                
                if ($form->company_num != $companyNum) {
                    echo json_encode(["Message" => "Unauthorized", "Status" => "Error"]);
                    break;
                }

                $formFields = new FormFields();
                $formFields->__set("name", $_POST["name"]);
                $id = $formFields->insertFormField();

                $clientFormFields = new ClientFormFields();
                $order = $clientFormFields->getCountOfRecordById($_POST["form_id"]);

                $clientFormFields->__set("field_id", $id);
                $clientFormFields->__set("form_id", $_POST["form_id"]);
                $clientFormFields->__set("mandatory", $_POST["mandatory"]);
                $clientFormFields->__set("show", $_POST["show"]);
                $clientFormFields->__set("order", $order + 1);
                $clientFormFields->__set("type", $_POST["type"]);
                if (isset($_POST["options"]) && ($_POST["type"] == "multi" || $_POST["type"] == "multiCheck")) {
                    $clientFormFields->__set("options", json_encode($_POST["options"]));
                }
                $res = $clientFormFields->insertClientFormField();

                // remove from comment if  want to add for both lead and client
//                $otherId = $clientForm->getOtherFormId($_POST["form_id"], $companyNum, Auth::user()->id);
//                $clientFormFields->__set("form_id", $otherId);
//                $clientFormFields->__set("mandatory", 0);
//                $clientFormFields->__set("show", 0);
//                $clientFormFields->insertClientFormField();

                echo json_encode(["Message" => $res, "Status" => "Success"]);
            }
            break;
        case "UpdateField":
            if (!isset($_POST["form_id"])) {
                echo json_encode(["Message" => "form_id required", "Status" => "Error"]);
            } else {
                $form = $clientForm->getFormById($_POST["form_id"]);

                if ($form->company_num != $companyNum) {
                    echo json_encode(["Message" => "Unauthorized", "Status" => "Error"]);
                    break;
                }
                $res = updateField($_POST);
                echo json_encode(["Message" => $res > 0 ? "Updated" : "Error updating field", "Status" => $res > 0 ? "Success" : "Error"]);
            }
            break;
        case "UpdateFields":
            $e = "";
            if (!isset($_POST["form_id"])) {
                echo json_encode(["Message" => "form_id required", "Status" => "Error"]);
            } elseif (!isset($_POST["fields"])) {
                echo json_encode(["Message" => "fields required", "Status" => "Error"]);
            } elseif (!is_array($_POST["fields"])) {
                echo json_encode(["Message" => "fields must be an array", "Status" => "Error"]);
            } else {
                $form = $clientForm->getFormById($_POST["form_id"]);

                if ($form->company_num != $companyNum) {
                    echo json_encode(["Message" => "Unauthorized", "Status" => "Error"]);
                    break;
                }

                $res = 0;
                $breakFlag = false;

                foreach ($_POST["fields"] as $field) {
                    $update = updateField($field);
                    if ($update == -1) {
                        echo json_encode(["Message" => "Unauthorized", "Status" => "Error"]);
                        $breakFlag = true;
                        break;
                    }
                    $res += $update;
                }
                if ($breakFlag) {
                    break;
                }
                echo json_encode(["Message" => $res > 0 ? "Updated" : "Error updating fields", "Status" => $res > 0 ? "Success" : "Error"]);
            }
            break;
        case 'DeleteField':
            if (!isset($_POST["form_id"])) {
                echo json_encode(["Message" => "form_id required", "Status" => "Error"]);
                } elseif (!is_numeric($_POST["form_id"])) {
                echo json_encode(["Message" => "form_id must be numeric", "Status" => "Error"]);
            } elseif (!isset($_POST["field_id"])) {
                echo json_encode(["Message" => "field_id required", "Status" => "Error"]);
            } elseif (!is_numeric($_POST["field_id"])) {
                echo json_encode(["Message" => "field_id must be numeric", "Status" => "Error"]);
            } else {
                $form = $clientForm->getFormById($_POST["form_id"]);

                if ($form->company_num != $companyNum) {
                    echo json_encode(["Message" => "Unauthorized", "Status" => "Error"]);
                    break;
                }

                $formFields = new FormFields();
                $formFields->getFormFieldById($_POST["field_id"]);
                if ($formFields->isDefault()) {
                    echo json_encode(["Message" => "You cannot delete default fields", "Status" => "Error"]);
                } else {
                    $clientFormFields = new ClientFormFields();
                    $res = $clientFormFields->deleteViaFormAndFieldId($_POST["form_id"], $_POST["field_id"]);
                    echo json_encode(["Message" => $res > 0 ? $res : "Error deleting field", "Status" => $res > 0 ? "Success" : "Error"]);
                }
            }
            break;
        case 'ChangeFieldOrder':
            if (!isset($_POST["form_id"])) {
                echo json_encode(["Message" => "form_id required", "Status" => "Error"]);
            } elseif (!is_numeric($_POST["form_id"])) {
                echo json_encode(["Message" => "form_id must be numeric", "Status" => "Error"]);
            } elseif (!isset($_POST["order"])) {
                echo json_encode(["Message" => "order are required", "Status" => "Error"]);
            } elseif (!is_array($_POST["order"])) {
                echo json_encode(["Message" => "order must be an array", "Status" => "Error"]);
            } else {
                $formFields = new FormFields();
                $clientFormFields = new ClientFormFields();
                $clientFormFields->__set("form_id", $_POST["form_id"]);
                $res = 0;
                foreach ($_POST["order"] as $index => $field_id) {
                    $formFields->getFormFieldById($field_id);
                    $name = $formFields->__get("enName");
                    if ($name == "first_name" || $name == "last_name" || $name == "phone") {
                        continue;
                    }
                    $clientFormFields->__set("field_id", $field_id);
                    $clientFormFields->__set("order", $index + 1);
                    $res += $clientFormFields->updateClientFormFields();
                }
            }
            echo json_encode(["Message" => $res, "Status" => "Success"]);
            break;
        default: 
            echo json_encode(["Message" => "Function not found", "Status" => "Error"]);
            break;
    }
} else {
    echo json_encode(array("Message" => "No Function","Status" => "Error"));
}

function updateField($data) {
    $e = "";
    if (!isset($_POST["form_id"])) {
        echo json_encode(["Message" => "form_id required", "Status" => "Error"]);
    } elseif (!is_numeric($_POST["form_id"])) {
        echo json_encode(["Message" => "form_id must be numeric", "Status" => "Error"]);
    } elseif (!isset($data["field_id"])) {
        echo json_encode(["Message" => "field_id required", "Status" => "Error"]);
    } elseif (!is_numeric($data["field_id"])) {
        echo json_encode(["Message" => "field_id must be numeric", "Status" => "Error"]);
    } elseif (isset($data["mandatory"]) && $data["mandatory"] != 0 && $data["mandatory"] != 1) {
        echo json_encode(["Message" => "mandatory must be 0 or 1", "Status" => "Error"]);
    } elseif (isset($data["show"]) && $data["show"] != 0 && $data["show"] != 1) {
        echo json_encode(["Message" => "show must be 0 or 1", "Status" => "Error"]);
    } elseif (!empty($data["type"]) && ($data["type"] == 'multi' || $data["type"] == 'multiCheck') && !isset($data["options"])) {
        echo json_encode(["Message" => "type = ".$data['type']." so options are required", "Status" => "Error"]);
    } elseif (isset($data["options"]) && !is_array($data["options"])) {
        echo json_encode(["Message" => "options must be an array", "Status" => "Error"]);
    } else {
        $formFields = new FormFields($data["field_id"]);
        $currentDefaultId= $formFields->__get("default_id");
        $formFieldArr = array(
            "name" =>  $data["name"],
            "customer_default_field" => $formFields->__get("customer_default_field"),
            "lead_default_field" => $formFields->__get("lead_default_field"),
        );
        $res = 0;

        if (isset($data["name"]) && !$formFields->isDefault()) {
            $formFields->__set("name", $data["name"]);
            $res += $formFields->updateFormField($formFieldArr,$data["field_id"]);
        }

        if ($formFields->__get("system_field")) {
            echo json_encode(["Message" => "cannot edit system_field", "Status" => "Error"]);
            die();
        }

        $clientFormFields = new ClientFormFields();
        $clientForm = array();
        if (isset($data["mandatory"])) {
            $clientForm["mandatory"] = $data["mandatory"];
        }
        if (isset($data["show"])) {
            $clientForm["show"] = $data["show"];
        }
        if (isset($data["type"]) && !$formFields->isDefault()) {
            $clientForm["type"] = $data["type"];
            if ($data["type"] != "multi" && $data["type"] != "multiCheck") {
                unset($data["options"]);
            }
        }
        if (isset($data["options"]) && !$formFields->isDefault()) {
            $clientForm["options"] = json_encode($data["options"]);
        }
        if (isset($data["order"])) {
            $clientForm["order"] = $data["order"] + 3; //todo after fix letts add number of system_field
        }
        return $clientFormFields->updateClientFields($clientForm,$data["field_id"],$_POST["form_id"]);
    }
    return null;
}
?>
