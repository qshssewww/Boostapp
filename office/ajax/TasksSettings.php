<?php
require_once __DIR__ . '/../../app/init.php';
require_once __DIR__ . '/../Classes/CalType.php';
require_once __DIR__ . '/../Classes/TaskStatus.php';

const ERROR = 0;
const SUCCESS = 1;
$TaskType = new CalType();

if (Auth::guest()) exit;
$companyNum = Company::getInstance()->__get('CompanyNum');

if(!empty($_POST["fun"])) {

    switch ($_POST["fun"]) {

        case "GetTaskTypes":
            $res = $TaskType->getAllCalType($companyNum);
            if(!$res || count($res) == 0){
                echo json_encode(array("Message" => "Task Types Not Found", "Status" => ERROR));
            } else {
                echo json_encode(array("response" => $res, "Status" => SUCCESS));
            }
            break;

        case "AddTaskType":
            if (!isset($_POST["Name"]) && !isset($_POST["Type"]))
                echo json_encode(array("Message" => "Type is required", "Status" => ERROR));
            else {
                $dataToSave = array('CompanyNum' => $companyNum, 'Type' => $_POST["Name"] ?? $_POST["Type"]);
                $validator = Validator::make($dataToSave, CalType::$CreateRules);
                if ($validator->passes()) {
                    $res = new CalType($dataToSave);
                    $res->save();
                    echo json_encode(array("response" => $res->toArray(), "Status" => SUCCESS));
                } else {
                    echo json_encode(array("response" => $validator->errors()->toArray(), "Status" => ERROR));
                }
            }
            break;

        case "UpdateTaskType":
            if (!isset($_POST["id"]) || !is_numeric($_POST["id"]))
                echo json_encode(array("Message" => "Id Not Valid", "Status" => ERROR));
            elseif (!isset($_POST["Name"]) && !isset($_POST["Type"]) && !isset($_POST["Status"]))
                echo json_encode(array("Message" => "Type or Status required to update", "Status" => ERROR));
            else {
                $keyName = (!isset($_POST["Status"])) ? 'Type' : 'Status';
                $keyData = $_POST["Status"] ?? $_POST["Name"] ?? $_POST["Type"];
                $validator = Validator::make(array('id' => $_POST['id'], $keyName => $keyData), CalType::$updateRules);
                if ($validator->passes()) {
                    $TaskType = $TaskType::find($_POST["id"]);
                    if (!$TaskType || $TaskType->CompanyNum != $companyNum ) {
                        echo json_encode(array("Message" => "Not Found Lead Source To Update", "Status" => ERROR));
                    } else {
                        $TaskType[$keyName] = $keyData;
                        $TaskType->save();
                        echo json_encode(array("response" => $TaskType->toArray(), "Status" => SUCCESS));
                    }
                } else {
                    echo json_encode(array("response" => $validator->errors()->toArray(), "Status" => ERROR));
                }
            }
            break;

        case "GetTaskStatuses":
            $res = TaskStatus::getAllByCompanyNum($companyNum);

            echo json_encode(array("response" => $res, "Status" => SUCCESS));
            break;

        case "AddTaskStatus":
            if (!isset($_POST["Name"]))
                echo json_encode(array("Message" => "Name is required", "Status" => ERROR));
            else {
                $dataToSave = array('CompanyNum' => $companyNum, 'Name' => $_POST["Name"]);
                $validator = Validator::make($dataToSave, TaskStatus::$CreateRules);
                if ($validator->passes()) {
                    $res = new TaskStatus($dataToSave);
                    $res->save();
                    echo json_encode(array("response" => $res->toArray(), "Status" => SUCCESS));
                } else {
                    echo json_encode(array("response" => $validator->errors()->toArray(), "Status" => ERROR));
                }
            }
            break;

        case "UpdateTaskStatus":
            if (!isset($_POST["id"]) || !is_numeric($_POST["id"]))
                echo json_encode(array("Message" => "Id Not Valid", "Status" => ERROR));
            elseif ((!isset($_POST["Name"]) && !isset($_POST["Status"])))
                echo json_encode(array("Message" => "Name or Status required to update", "Status" => ERROR));
            else {
                $keyData = (isset($_POST["Name"])) ? 'Name' : 'Status';
                $validator = Validator::make(array('id' => $_POST['id'], $keyData => $_POST[$keyData]), TaskStatus::$updateRules);
                if ($validator->passes()) {
                    /** @var TaskStatus $TaskStatus */
                    $TaskStatus = TaskStatus::find($_POST["id"]);
                    if (!$TaskStatus || $TaskStatus->CompanyNum != $companyNum) {
                        echo json_encode(array("Message" => "Not Found Task Status To Update", "Status" => ERROR));
                    } else {
                        $TaskStatus[$keyData] = $_POST[$keyData];
                        $TaskStatus->save();
                        echo json_encode(array("response" => $TaskStatus->toArray(), "Status" => SUCCESS));
                    }
                } else {
                    echo json_encode(array("response" => $validator->errors()->toArray(), "Status" => ERROR));
                }
            }
            break;

        default:
            echo json_encode(array("Message" => "No Found Function","Status" => ERROR));
            break;
    }
}
else{
    echo json_encode(array("Message" => "No Function","Status" => ERROR));
}

