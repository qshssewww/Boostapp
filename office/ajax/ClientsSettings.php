<?php
require_once __DIR__ . '/../../app/init.php';
require_once __DIR__ . '/../Classes/ClientLevel.php';
require_once __DIR__ . '/../Classes/Rank.php';
require_once __DIR__ . '/../Classes/Pipereasons.php';
require_once __DIR__ . '/../Classes/Client.php';
header('Content-Type: application/json');
const ERROR = 0;
const SUCCESS = 1;
$ClientLevel = new ClientLevel();
$Pipereasons = new Pipereasons();

if (Auth::guest()) exit;
$companyNum = Company::getInstance()->__get('CompanyNum');
if (!empty($_POST["fun"])) {

    switch ($_POST["fun"]) {
        case "GetClientsTags":
            $clientsLevel = (new ClientLevel())->getAllByCompanyNum($companyNum);
            if (!$clientsLevel || count($clientsLevel) == 0) {
                echo json_encode(array("Message" => "Client Tags Not Found", "Status" => ERROR));
            } else {
                $CurrLevel = new Rank();
                foreach ($clientsLevel as $level) {
                    $level->count = $CurrLevel->getCountRanksId($level->id);
                }
                echo json_encode(array("response" => $clientsLevel, "Status" => SUCCESS));

            }
            break;

        case "UpdateClientsTag":
            unset($_POST["fun"]);
            if (!isset($_POST["id"]) || !is_numeric($_POST["id"])) {
                echo json_encode(array("Message" => "Id Not Valid", "Status" => ERROR));
            } elseif (!isset($_POST["Title"])) {
                echo json_encode(array("Message" => "Title is required", "Status" => ERROR));
            } else {
                $newLevel = $_POST["Title"];
                $id = $_POST["id"];
                $validator = Validator::make(array('id' => $id, 'Level' => $newLevel), ClientLevel::$updateRules);
                if ($validator->passes()) {
                    $ClientLevel = $ClientLevel::find($id);
                    if (!$ClientLevel || $ClientLevel->CompanyNum != $companyNum) {
                        echo json_encode(array("Message" => "Not Found Client Tag To Update", "Status" => ERROR));
                    } else {
                        $ClientLevel['Level'] = $newLevel;
                        $ClientLevel->save();
                        echo json_encode(array("response" => $ClientLevel->toArray(), "Status" => SUCCESS));
                    }
                } else {
                    echo json_encode(array("response" => $validator->errors()->toArray(), "Status" => ERROR));
                }
            }
            break;
        case "AddClientsTag":
            unset($_POST["fun"]);
            if (!isset($_POST["Title"])) {
                echo json_encode(array("Message" => "Title is required", "Status" => ERROR));
            } else {
                $dataToSave = array('CompanyNum' => $companyNum, 'Level' => $_POST["Title"]);
                $validator = Validator::make($dataToSave, ClientLevel::$CreateRules);
                if ($validator->passes()) {
                    $res = new ClientLevel($dataToSave);
                    $res->save();
                    echo json_encode(array("response" => $res->toArray(), "Status" => SUCCESS));
                } else {
                    echo json_encode(array("response" => $validator->errors()->toArray(), "Status" => ERROR));
                }
            }
            break;

        case "GetReasonsLeave":
            unset($_POST["fun"]);
            $reasonsLeave = $Pipereasons->getAllReasonsByCompany($companyNum);
            if (!$reasonsLeave || count($reasonsLeave) == 0) {
                echo json_encode(array("Message" => "Reasons Leave Not Found", "Status" => ERROR));
            } else {
                echo json_encode(array("response" => $reasonsLeave, "Status" => SUCCESS));
            }
            break;

        case "AddReasonLeave":
            if (!isset($_POST["Title"])) {
                echo json_encode(array("Message" => "Title is required", "Status" => ERROR));
            } else {
                $dataToSave = array('CompanyNum' => $companyNum, 'Title' => $_POST["Title"]);
                $validator = Validator::make($dataToSave, Pipereasons::$CreateRules);
                if ($validator->passes()) {
                    $res = new Pipereasons($dataToSave);
                    $res->save();
                    echo json_encode(array("response" => $res->toArray(), "Status" => SUCCESS));
                } else {
                    echo json_encode(array("response" => $validator->errors()->toArray(), "Status" => ERROR));
                }
            }
            break;

        case "UpdateReasonLeave":
            if (!isset($_POST["id"]) || !is_numeric($_POST["id"])) {
                echo json_encode(array("Message" => "Id Not Valid", "Status" => ERROR));
            } elseif ((!isset($_POST["Title"]) && !isset($_POST["Status"]))) {
                echo json_encode(array("Message" => "Title or Status required to update ReasonLeave", "Status" => ERROR));
            } else {
                $keyData = (isset($_POST["Title"])) ? 'Title' : 'Status';
                $validator = Validator::make(array('id' => $_POST['id'], $keyData => $_POST[$keyData]), Pipereasons::$updateRules);
                if ($validator->passes()) {
                    $ReasonLeave = $Pipereasons::find($_POST["id"]);
                    if (!$ReasonLeave || $ReasonLeave->CompanyNum != $companyNum) {
                        echo json_encode(array("Message" => "Not Found Lead Source To Update", "Status" => ERROR));
                    } else {
                        $ReasonLeave[$keyData] = $_POST[$keyData];
                        $ReasonLeave->save();
                        echo json_encode(array("response" => $ReasonLeave->toArray(), "Status" => SUCCESS));
                    }
                } else {
                    echo json_encode(array("response" => $validator->errors()->toArray(), "Status" => ERROR));
                }
            }
            break;

        case "checkExistingClient":
            if (!isset($_POST["phone"])) {
                echo json_encode(array("Message" => "Phone is required", "Status" => ERROR));
            } else {
                $res = (new Client())->isDuplicatePhone(Auth::user()->CompanyNum, $_POST["phone"]);

                echo json_encode(array("response" => ['isDuplicate' => $res], "Status" => SUCCESS));
            }

            break;

        default:
            echo json_encode(array("Message" => "No Found Function", "Status" => ERROR));
            break;
    }

} else {
    echo json_encode(array("Message" => "No Function", "Status" => ERROR));
}
