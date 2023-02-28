<?php
require_once '../../app/init.php';
header('Content-Type: application/json');

require_once '../Classes/Company.php';
require_once '../Classes/Utils.php';
require_once '../Classes/ClassCalendar.php';
require_once "../Classes/Client.php";
require_once "../Classes/ClassesType.php";
require_once "../Classes/ClassStudioDate.php";
require_once "../Classes/Users.php";
require_once "../Classes/EncryptDecrypt.php";
require_once "../services/EmailService.php";
require_once "../services/TagsService.php";


if (!Auth::check()) {
    echo json_encode((array("Message" => "unauthorized", "Status" => "Error")));
    exit;
}
$CompanyNum = Company::getInstance()->__get('CompanyNum');
$classCalendarObj = new ClassCalendar();
$clientObj = new Client();
$classStudioDateObj = new ClassStudioDate();
$classTypeObj = new ClassesType();
$encryptDecrypt = new EncryptDecrypt();

if(!empty($_POST["fun"])) {
    switch ($_POST["fun"]) {

        case "getDefaultClassTypeSettings":
            if (!isset($_POST['classTypeId'])) {
                echo json_encode(array("Message" => "class type id required", "Status" => "Error"));
            } else {
                $res = $classTypeObj->getClassTypeById($_POST['classTypeId']);
                if(!$res) {
                    echo json_encode(array('Message' => 'missing class type id', "Status" => "Error"));
                    break;
                }
                echo json_encode(array('Message' => $res, "Status" => "Success"));
            }
            break;

        case "getClassData":
            $res = [];
            echo json_encode(array('Message' => $res, "Status" => "Success"));
            break;

        case "getDefaultTag":
            if (!isset($_POST['data']) || !isset($_POST['data']['name']) || !isset($_POST['data']['typeId']) || !isset($_POST['data']['typeName']) || !isset($_POST['data']['isLesson'])) {
                echo json_encode(array("Message" => "class type and class name required", "Status" => "Error"));
            }
            if($_POST['data']['isLesson'] == 'true') {
                $res = TagsService::getLessonPredictionTagKey(
                    trim($_POST['data']['name']),
                    trim($_POST['data']['typeName']),
                    $CompanyNum,
                    trim($_POST['data']['typeId'])
                );
            } else {
                $res = TagsService::getMeetingPredictionTagKey(
                    trim($_POST['data']['name']),
                    trim($_POST['data']['typeName']),
                    $CompanyNum,
                    trim($_POST['data']['typeId']));
            }
            echo json_encode(array('Message' => $res, "Status" => "Success"));
            break;

        case "sendTagRequest":
            if (!isset($_POST['tagName'])) {
                echo json_encode(array("Message" => "tag name required", "Status" => "Error"));
            }
            $res = EmailService::sendTagRequest(htmlentities($_POST['tagName']));
            if($res['status'] == 1) {
                echo json_encode(array('Message' => $res, "Status" => "Success"));
            } else {
                echo json_encode(array('Message' => $res['message'], "Status" => "Error"));
            }
            break;

        case "getAllTags":
            $res = TagsService::getAllTagsTranslationsArray();
            echo json_encode(array('Message' => $res, "Status" => "Success"));
            break;

        default:
            echo json_encode(array("Message" => "No Found Function","Status" => "Error"));
            break;
    }
}
else{
    echo json_encode(array("Message" => "No Function","Status" => "Error"));
}