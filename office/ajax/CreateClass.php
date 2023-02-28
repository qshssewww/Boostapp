<?php

require_once '../../app/init.php';
require_once '../Classes/ClassOnline.php';
require_once '../Classes/Company.php';
require_once '../Classes/ClassStudioDate.php';
require_once '../Classes/ClassZoom.php';
require_once '../Classes/Models/Tags.php';
require_once '../Classes/Models/TranslationKeys.php';
require_once '../Classes/Models/TagsStudio.php';
require_once '../Classes/MeetingStaffRuleAvailability.php';

CONST ERROR = 0;
CONST SUCCESS = 1;

if (Auth::guest()) exit;

$ClassStudioDate = new ClassStudioDate();
$ClassZoom = new ClassZoom();
$CompanyNum = Auth::user()->__get('CompanyNum');

if(!empty($_POST["fun"])) {
    switch ($_POST["fun"]) {
        case "SetNewCalendarClass":
            require_once '../calendarPopups/SetNewCalendarClass.php';
            break;
        case "SaveClass":
            $res = $ClassStudioDate->saveClass($_POST['data']);
            echo json_encode($res);
            break;

        case "GetClassData":
            $id = (int)$_POST['classId'];
            $classData = ClassStudioDate::getClassById($id, $CompanyNum);
            if ($classData){
                if ($classData->ClassType == 2)
                    $classData->LastClassDate = $ClassStudioDate->getLastClass($classData->GroupNumber, $CompanyNum)->StartDate;
                if ($classData->is_zoom_class == 1)
                    $classData->zoomData = $ClassZoom->getByClassId($classData->id, $CompanyNum);
                if ($classData->onlineClassId) {
                    $onlineClassData = ClassOnline::find($classData->onlineClassId);
                    $classData->onlineSendType = $onlineClassData->sendType;
                    $classData->onlineSendTime = $onlineClassData->sendTime;
                    $classData->onlineSendTimeType = $onlineClassData->sendTimeType;
                }

            try {
                $tagId = TagsStudio::getTagByLessonId($id)->tags_id;
                $translation = Tags::find($tagId)->translation_id;
                $key = TranslationKeys::find($translation)->key;
                $classData->tag = lang($key);
                $classData->tagId = $tagId;
            } catch (Exception $e) {
//                $classData->tag = lang('tag_treatment_lessoncategory_personal');
//                $classData->tagId = 99;
            }

                echo json_encode(["data" => $classData]);
            } else {
                header('HTTP/1.1 500 Internal Server Error');
                header('Content-Type: application/json; charset=UTF-8');
                die();
            }
            break;

        default:
            echo json_encode(array("message" => "No Found Function","status" => ERROR));
            break;
    }
}
else{
    echo json_encode(array("message" => "No Function","status" => ERROR));
}

function errorOccurred ($message) {
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: application/json; charset=UTF-8');
    die(json_encode(array("message" => $message,"status" => ERROR)));
}

function successResponse ($message, $data) {
    echo json_encode(array("message" => $message,"status" => SUCCESS, "data" => $data));
    exit(200);
}