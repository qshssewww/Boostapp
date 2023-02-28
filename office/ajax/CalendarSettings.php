<?php
require_once '../../app/init.php';

require_once "../services/LoggerService.php";
require_once "../services/meetings/EditMeetingService.php";

require_once '../Classes/AppSettings.php';
require_once '../Classes/ClassTemplate.php';
require_once '../Classes/MeetingTemplates.php';
require_once '../Classes/Brand.php';
require_once '../Classes/ClientLevel.php';
require_once '../Classes/MeetingGeneralSettings.php';
require_once '../Classes/MeetingCancellationPolicy.php';
require_once '../Classes/MeetingCategories.php';
require_once '../Classes/MeetingStaffRuleAvailability.php';
require_once '../Classes/MeetingStaffDateAvailability.php';
require_once '../Classes/Models/TagsSection.php';
require_once '../Classes/Company.php';
require_once '../Classes/ClassSettings.php';
require_once '../Classes/ClassStudioAct.php';
require_once '../Classes/ClassStudioDate.php';
require_once '../Classes/Numbers.php';
require_once '../Classes/NumbersSub.php';
require_once '../Classes/ClassesType.php';
require_once '../Classes/Section.php';
require_once '../Classes/Users.php';
require_once '../Classes/ClassZoom.php';
require_once '../Classes/EncryptDecrypt.php';
require_once '../Classes/TemplateAvailability.php';
require_once '../Classes/ClassCalendar.php';
require_once '../Classes/UserSchedule.php';
require_once '../Classes/UserScheduleUpdate.php';
require_once '../Classes/userScheduleSettings.php';
require_once '../../app/enums/ClassType/EventType.php';

header('Content-Type: application/json');

const ERROR = 0;
const SUCCESS = 1;



$MeetingTemplates = new MeetingTemplates();
$GeneralMeetingSettings = new MeetingGeneralSettings();
$MeetingCancellationPolicy = new MeetingCancellationPolicy();
$Brand = new Brand();
$ClientLevel = new ClientLevel();
$MeetingCategories = new MeetingCategories();
$Users = new Users();
$MeetingStaffRuleAvailability = new MeetingStaffRuleAvailability();



$ClassSettings = new ClassSettings();
$Numbers = new Numbers();
$NumbersSub = new NumbersSub();
$ClassesType   = new ClassesType();
$Section = new Section();

$ClassZoom = new ClassZoom();
$EncryptDecrypt = new EncryptDecrypt();
$ClassCalendar = new ClassCalendar();

$ClassTemplate  = new ClassTemplate();//todo remove
$TemplateAvailability = new TemplateAvailability();//todo delete
$userSchedule = new UserSchedule();//todo delete
$userScheduleUp = new UserScheduleUpdate();//todo delete
$userScheduleSettings = new userScheduleSettings();//todo delete


$ClassStudioAct = new ClassStudioAct();
$classAppSettings = new AppSettings();

if (Auth::guest()) exit;

$companyNum = Company::getInstance()->__get('CompanyNum');


// create new category
/**
 * @throws Exception
 */
function createNewCategory($companyNum, $name) {
    $MeetingCategories = new MeetingCategories([
        'CompanyNum' => $companyNum,
        'CategoryName' => $name
    ]);
    $validator = Validator::make($MeetingCategories->getAttributes(), $MeetingCategories::$CreateRules);
    if ($validator->passes()) {
        $MeetingCategories->save();
        return $MeetingCategories->id;
    } else {
        throw new Exception(json_encode($validator->errors()->toArray()));
    }
}

//Dealing with errors
function catchErrors($e, $data=null, $errorEchoMassage=null): array
{
    LoggerService::error($e, LoggerService::CATEGORY_TEMPLATE_MEEETING);
    if($data) {
        LoggerService::info($data, LoggerService::CATEGORY_TEMPLATE_MEEETING);
    }
    return array("Message" => $errorEchoMassage ?? $e->getMessage(), "Status" => ERROR);
}

if(!empty($_POST["fun"])) {

    switch ($_POST["fun"]) {
        /**************** Global Calendar Settings ****************/
        // get all color from db
        case "GetColors":
            unset($_POST["fun"]);
            $res = $ClassesType->getColors();
            echo json_encode(array("Colors" => $res, "Status" => SUCCESS));
            break;
        // get all levels from db
        case "GetLevels":
            unset($_POST["fun"]);
            if (empty($_POST['CompanyNum'])) {
                echo json_encode(array("Message" => "CompanyNum required", "Status" => ERROR));
            } elseif (!is_numeric($_POST['CompanyNum'])) {
                echo json_encode(array("Message" => "CompanyNum must be number", "Status" => ERROR));
            } else {
                $res = $ClientLevel->getAllByCompanyNum($_POST['CompanyNum']);
                echo json_encode(array("Levels" => $res, "Status" => SUCCESS));
                break;
            }
            break;

        /**************** Meeting Template  ****************/
        //Checks the correctness of the request, and returns all active and hidden templates
        case "GetAllTemplates":
            unset($_POST["fun"]);
            if(empty($_POST["CompanyNum"])){
                echo json_encode(array("Message" => "CompanyNum required", "Status" => ERROR));
            } elseif (!is_numeric($_POST["CompanyNum"])) {
                echo json_encode(array("Message" => "CompanyNum has to be numeric", "Status" => ERROR));
            } elseif($companyNum != $_POST["CompanyNum"]){
                echo json_encode(array("Message" => "CompanyNum is not valid", "Status" => ERROR));
            }
            else{
                $templateArray=[];
                $templates = $MeetingTemplates->getAllTemplatesByCompany($companyNum);
                foreach ($templates as $template) {
                    $template->setClassesType();
                    $templateArray[] =$template->returnTemplateDisplayArray();
                }
                echo json_encode(array("templates" => $templateArray,  "Status" => SUCCESS));
            }
            break;

        //get all tags sorted by categories and devided to favorite and others
        case "getTagsCategories":
            $res = ["tags" =>TagsService::getFavoriteAndOtherCategoriesTags($companyNum)];
            if(empty($res["tags"])) {
                echo json_encode(array('Message' => 'data not found', "Status" => "Error"));
                break;
            }

            echo json_encode(array('Message' => $res, "Status" => "Success"));
            break;


        //Checks valid and returns all active templates that related to the category
        case "GetAllTemplateByCategory":
            unset($_POST["fun"]);
            if(empty($_POST["CategoryId"])){
                echo json_encode(array("Message" => "CategoryId required", "Status" => ERROR));
            } elseif (!is_numeric($_POST["CategoryId"])) {
                echo json_encode(array("Message" => "CategoryId has to be numeric", "Status" => ERROR));
            }
            else{
                $templates = $MeetingTemplates->getAllTemplateByCategory($_POST["CategoryId"]);
                echo json_encode(array("templates" => $templates,  "Status" => SUCCESS));
            }
            break;
        //count item link to each class type in template
        case "CountItemLinkToTemplate":
            unset($_POST['fun']);
            if (empty($_POST['id'])) {
                echo json_encode(array("Message" => "id is required", "Status" => ERROR));
            } else {
                try {
                    $count = 0;
                    $MeetingTemplates = $MeetingTemplates->find($_POST['id']);
                    /** @var ClassesType $ClassType */
                    foreach ($MeetingTemplates->classesType() as $ClassType) {
                        $count += $ClassType->countItemsLink(EventType::EVENT_TYPE_MEETINGS);
                    }
                    echo json_encode(array('name' =>$MeetingTemplates->TemplateName, 'id' => $_POST['id'], 'count'=>$count ,"Status" => SUCCESS));
                } catch (Exception $e) {
                    $resp = catchErrors($e, null, 'error CountItemLinkToTemplate');
                    echo json_encode($resp);
                    exit();
                }
            }
            break;
        //change status for template by id //todo-> add logger
        case "ChangeStatusToTemplateId":
            unset($_POST['fun']);
            if (empty($_POST['id'])) {
                echo json_encode(array("Message" => "id is required", "Status" => ERROR));
            } else if (empty($_POST['Status']) && !is_numeric($_POST['Status'])) {
                echo json_encode(array("Message" => "status is required", "Status" => ERROR));
            } else {
                try {
                    $MeetingTemplates = $MeetingTemplates->find($_POST['id']);
                    if($_POST['Status'] == 0) {
                        foreach ($MeetingTemplates->classesType() as $classType) {
                            $classType->Status = 1;
                            $classType->EditDate = date('Y-m-d H:i:s');
                            $classType->save();
                        }
                    }
                    $MeetingTemplates->EditDate = date('Y-m-d H:i:s');
                    $MeetingTemplates->Status = $_POST['Status'];
                    $MeetingTemplates->save();
                    echo json_encode(array('id' => $_POST['id'], "Status" => SUCCESS));
                } catch (Exception $e) {
                    $resp = catchErrors($e, null, 'error ChangeStatusToTemplateId');
                    echo json_encode($resp);
                    exit();
                }
            }
            break;
        //get template and all relevant data by id
        case "GetTemplateById":
            unset($_POST["fun"]);
            if (empty($_POST['id'])) {
                echo json_encode(array("Message" => "id required", "Status" => ERROR));
            }
            else {
                $template = $MeetingTemplates->find($_POST['id']);
                $template->setClassesType();
                $templateArray = $template->returnArray();
                //get all calendars of template Meeting
                $templateArray['calendars'] = $template->getCalendarsOnlyId();
                //get all coaches and Meeting details
                if($template->AllCoaches != 1) {
                    $templateArray['coaches'] = $Users->getAllCoachesAndMeetingByCompanyNum($template->CompanyNum, $_POST['id'] );
                } else {
                    $templateArray['coaches'] = $Users->getAllCoachesAndMeetingByCompanyNum($template->CompanyNum);
                }
                echo json_encode(array("Template" => $templateArray, "Status" => SUCCESS));
            }
            break;
        // Add new template //todo-> add logger and fix validation
        case "UpdateTemplate":
            unset($_POST["fun"]);
            if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
                echo json_encode(array("Message" => "id not valid", "Status" => ERROR));
            } else if (!isset($_POST['NewCategory']) || !is_numeric($_POST['NewCategory'])) {
                echo json_encode(array("Message" => "NewCategory not valid", "Status" => ERROR));
            }
            /** Create new category  **/
            if ($_POST['NewCategory'] == 1) {
                try {
                    $_POST['CategoryId'] = createNewCategory($companyNum, $_POST['CategoryId']);
                } catch (Exception $e) {
                    $resp = catchErrors($e, $_POST,'error in ReplaceCategoryInTemplates - add Meeting Categories');
                    echo json_encode($resp);
                    exit();
                }
            }
            unset($_POST['NewCategory']);
            $meetingTemplate = MeetingTemplates::find($_POST['id']);
            /** Update Coaches  **/
            if (isset($_POST['AllCoaches'])) {
                try {
                    if ($_POST['AllCoaches'] == '1') {
                        foreach ($meetingTemplate->coaches() as $coach) {
                            $coach->delete();
                        }
                    } else {
                        foreach ($meetingTemplate->coaches() as $coach) {
                            if (!in_array($coach->CoachId, $_POST['CoachId'])) {
                                $coach->delete();
                                //todo add to logger CoachId removed
                            } else{
                                $key = array_search($coach->CoachId, $_POST['CoachId']);
                                unset($_POST['CoachId'][$key]);
                            }
                        }
                        foreach ($_POST['CoachId'] as $coach) {
                            $meetingTemplate->addTemplateCoachToDb([
                                'MeetingTemplateId' => $meetingTemplate->id,
                                'Status' => 1,
                                'CoachId' => $coach,
                            ]);
                            //todo add to logger add CoachId
                        }
                        unset($_POST['CoachId']);
                    }
                } catch (Exception $e) {
                    $resp = catchErrors($e, null, 'error in create coaches - Update Template ');
                    echo json_encode($resp);
                    exit();
                }
            }

            /** Update calendars  **/
            if (isset($_POST['AllCalendars'])) {
                try {
                    if ($_POST['AllCalendars'] == '1') {
                        foreach ($meetingTemplate->calendars() as $calender) {
                            $calender->delete();
                        }
                    } else {
                        foreach ($meetingTemplate->calendars() as $calender) {
                            if (!in_array($calender->CoachId, $_POST['CalendarId'])) {
                                $calender->delete();
                                //todo add to logger CalendarId removed
                            } else{
                                $key = array_search($calender->CoachId, $_POST['CalendarId']);
                                unset($_POST['CalendarId'][$key]);
                            }
                        }
                        foreach ($_POST['CalendarId'] as $calender) {
                            $meetingTemplate->addTemplateCalendarToDb([
                                'MeetingTemplateId' => $meetingTemplate->id,
                                'Status' => 1,
                                'CalendarId' => $calender,
                            ]);
                            //todo add to logger add CalendarId
                        }
                        unset($_POST['CalendarId']);
                    }
                } catch (Exception $e) {
                    $resp = catchErrors($e, null, 'error in create calendar - Update Template ');
                    echo json_encode($resp);
                    exit();
                }
            }

            /** Update class type  **/
            if (isset($_POST['classesTypeArray'])) {
                $classTypeArray = $_POST['classesTypeArray'];
                unset($_POST['classesTypeArray']);
                try {
                    $oldClassType = $meetingTemplate->classesType();
                    foreach ($classTypeArray as $classType) {
                        if ($classType['id'] != '0' ) {
                            //EDIT
                            foreach($oldClassType as $old) {
                                //update only same id and duration
                                if($old['id'] == $classType['id'] && $old['duration'] == $classType['duration'] ){
                                    $old->Price = $classType['price'];
                                    $old->Type = $classType['durationText'] . ' | ' . $classType['price'] . '₪' ;
                                    $old->EditDate = date('Y-m-d H:i:s');
                                    $old->save();
                                }
                                //todo add to logger edit class type in  $old['id']
                            }
                        } else {
                            //create new
                            $meetingTemplate->addTemplateClassesTypeToDb([
                                'MeetingTemplateId' => $meetingTemplate->id,
                                'EventType' => 1,
                                'Type' => $classType['durationText'] . ' | ' . $classType['price'] . '₪' ,
                                'CompanyNum' => $companyNum,
                                'Color' => $meetingTemplate->ColorId,
                                'Status' => 0,
                                'durationType' => 0,
                                'duration' => $classType['duration'],
                                'Price' => $classType['price']
                            ]); //todo add to logger - new
                        }
                    }
                } catch (Exception $e) {
                    $resp = catchErrors($e, null, 'error in create classtype Update Template ');
                    echo json_encode($resp);
                    exit();
                }
            }
            unset($_POST['id']);
            $meetingTemplate->setClassesType();
            foreach ($meetingTemplate->classesType() as $classType){
                $classTypeArrayOut[] = $classType['duration'];
            }

            //update SessionsLimit if SessionsLimitType = 0
            if(isset($_POST['SessionsLimitType'])) {
                if ($_POST['SessionsLimitType'] === '0') {
                    $_POST['SessionsLimit'] = 0;
                }
                unset($_POST['SessionsLimitType']);
            }

            /** Update Meeting Templates  **/
            try {
                foreach ($_POST as $key => $value) {
                    $meetingTemplate->$key = htmlentities($value);
                } // todo add logger
                $meetingTemplate->EditDate = date('Y-m-d H:i:s');
                $meetingTemplate->save();
            } catch (Exception $e) {
                $resp = catchErrors($e, null, 'error update Meeting Templates');
                echo json_encode($resp);
                exit();
            }

            /** Create response array **/
            $response = [
                'id' => $meetingTemplate->id,
                'Status' => 1,
                'TemplateName' =>  $meetingTemplate->TemplateName,
                'ColorId' =>  $meetingTemplate->ColorId,
                'isNew' => 0,
                'duration' => $classTypeArrayOut
            ];
            echo json_encode(array("response" => $response, "Status" => SUCCESS));
            break;
        //add new template //todo-> add logger
        case "CreateNewTemplate":
            unset($_POST["fun"]);
            if (!isset($_POST['NewCategory']) || !is_numeric($_POST['NewCategory'])) {
                echo json_encode(array("Message" => "NewCategory not valid", "Status" => ERROR));
            } elseif (!isset($_POST['CategoryId'])) {
                echo json_encode(array("Message" => "CategoryId required", "Status" => ERROR));
            } elseif (!isset($_POST['AllCalendars'])) {
                echo json_encode(array("Message" => "AllCalendars required", "Status" => ERROR));
            } elseif (($_POST['AllCalendars']) != 1 &&  empty($_POST['CalendarId'])) {
                echo json_encode(array("Message" => "CalendarId Not valid", "Status" => ERROR));
            } elseif (!isset($_POST['AllCoaches'])) {
                echo json_encode(array("Message" => "AllCoaches required", "Status" => ERROR));
            } elseif ($_POST['AllCoaches'] != 1 && empty($_POST['CoachId'])) {
                echo json_encode(array("Message" => "Coaches Not valid", "Status" => ERROR));
            } elseif (empty($_POST['classesTypeArray']) ) {
                echo json_encode(array("Message" => "classesTypeArray required", "Status" => ERROR));
            } elseif (!isset($_POST['SessionsLimitType'])) {
                echo json_encode(array("Message" => "SessionsLimitType required", "Status" => ERROR));
            } elseif ($_POST['SessionsLimit'] != 0 && !isset($_POST['SessionsLimit'])) {
                echo json_encode(array("Message" => "SessionsLimit Not valid", "Status" => ERROR));
            } else {

            /** Preparing the data to create a new template  **/
                unset($_POST['tag']);
                if ($_POST['AllCalendars'] != '1') {
                    $calendarArray = $_POST['CalendarId'];
                }
                unset($_POST['CalendarId']);

                if ($_POST['AllCoaches'] != '1') {
                    $coachIdArray = $_POST['CoachId'];
                    unset($_POST['CoachId']);
                }
                unset($_POST['CoachId']);

                $classesType = $_POST['classesTypeArray'];
                unset($_POST['classesTypeArray']);

                if ($_POST['SessionsLimitType'] = '0') {
                    $_POST['SessionsLimit'] = 0;
                }
                unset($_POST['SessionsLimitType']);


            /** Create new category  **/
                if ($_POST['NewCategory'] == 1) {
                    try {
                        $_POST['CategoryId'] = createNewCategory($companyNum, $_POST['CategoryId']);
                    } catch (Exception $e) {
                        $resp = catchErrors($e, $_POST,'error in create category  - NewCategory');
                        echo json_encode($resp);
                        exit();
                    }
                }
                unset($_POST['NewCategory']);


            /** Create new Meeting Templates  **/
                $meetingTemplate = new MeetingTemplates();
                try {
                    $meetingTemplate->createNewTemplate($_POST);
                } catch (Exception $e) {
                    $resp = catchErrors($e, null, 'error in create meeting template - meetingTemplat');
                    echo json_encode($resp);
                    exit();
                }

            /** Create coaches for this template**/
                if (!empty($coachIdArray)) {
                    foreach($coachIdArray as $coach){
                        try {
                            $meetingTemplate->addTemplateCoachToDb([
                            'MeetingTemplateId' => $meetingTemplate->id,
                            'Status' => 1,
                            'CoachId' => $coach,
                        ]);
                        } catch (Exception $e) {
                            $resp = catchErrors($e, null, 'error in create coaches - add coaches');
                            $meetingTemplate->Status = 0;
                            $meetingTemplate->save();
                            echo json_encode($resp);
                            exit();
                        }
                    }
                }
            /** Create calendar for this template**/
                if (!empty($calendarArray)) {
                    foreach($calendarArray as $calendar){
                        try {
                            $meetingTemplate->addTemplateCalendarToDb([
                                'MeetingTemplateId' => $meetingTemplate->id,
                                'Status' => 1,
                                'CalendarId' => $calendar,
                            ]);
                        } catch (Exception $e) {
                            $resp = catchErrors($e, null, 'error in create calendar - calendar');
                            $meetingTemplate->Status = 0;
                            $meetingTemplate->save();
                            echo json_encode($resp);
                            exit();
                        }
                    }
                }
                $classTypeArray = [];

            /** Create classType for this template**/
                if (!empty($classesType)) {
                    foreach($classesType as $key => $value){
                        try {
                            //create new
                            $meetingTemplate->addTemplateClassesTypeToDb([
                                'MeetingTemplateId' => $meetingTemplate->id,
                                'EventType' => 1,
                                'Type' => $value['durationText'] .' '. '| ' . '₪' . $value['price'],
                                'CompanyNum' => $companyNum,
                                'Color' => $meetingTemplate->ColorId,
                                'Status' => 0,
                                'durationType' => 0,
                                'duration' => $value['duration'],
                                'Price' => $value['price']
                            ]); //todo add to logger - new
                            $classTypeArray[] = $value['duration'];
                        } catch (Exception $e) {
                            $resp = catchErrors($e, null, 'error in create classType - classType');
                            $meetingTemplate->Status = 0;
                            $meetingTemplate->EditDate = date('Y-m-d H:i:s');
                            $meetingTemplate->save();
                            echo json_encode($resp);
                            exit();
                        }
                    }
                }

            /** Create response array **/
                $response = [
                    'id' => $meetingTemplate->id,
                    'Status' => 1,
                    'TemplateName' =>  $meetingTemplate->TemplateName,
                    'ColorId' =>  $meetingTemplate->ColorId,
                    'isNew' => 1,
                    'duration' => $classTypeArray
                ];
                echo json_encode(array("response" => $response, "Status" => SUCCESS));
            }
            break;
        case "RemoveClassType":
            unset($_POST['fun']);
            if (empty($_POST['id'])) {
                echo json_encode(array("Message" => "id is required", "Status" => ERROR));
            } else {
                try {
                    $classType = ClassesType::find($_POST['id']);
                    $classType->Status = 1;
                    $classType->EditDate = date('Y-m-d H:i:s');
                    $classType->save();
                    echo json_encode(array('id' => $_POST['id'], "Status" => SUCCESS));
                } catch (Exception $e) {
                    $resp = catchErrors($e, null, 'error RemoveClassType');
                    echo json_encode($resp);
                    exit();
                }
            }
            break;

        /**************** Meeting General Settings  ****************/
        //get General Settings from Db by companyNum
        case "GetGeneralSettings":
            unset($_POST["fun"]);
            if (empty($_POST['CompanyNum'])) {
                echo json_encode(array("Message" => "CompanyNum required", "Status" => ERROR));
            } elseif (!is_numeric($_POST['CompanyNum'])) {
                echo json_encode(array("Message" => "CompanyNum must be number", "Status" => ERROR));
            } else {
                $GeneralMeetingSettings = MeetingGeneralSettings::getByCompanyNum($_POST['CompanyNum']);
                if (empty($GeneralMeetingSettings)) {
                    $GeneralMeetingSettings = new  MeetingGeneralSettings([
                        'CompanyNum' =>  $_POST['CompanyNum']
                    ]);
                    $GeneralMeetingSettings->save();
                    $GeneralMeetingSettings = $GeneralMeetingSettings->find($GeneralMeetingSettings->id);
                    $MeetingCancellationPolicy = new  MeetingCancellationPolicy([
                        'GeneralMeetingSettingId' =>  $GeneralMeetingSettings->id
                    ]);
                    $MeetingCancellationPolicy->save();
                }
                echo json_encode(array("response" => $GeneralMeetingSettings->toArray(), "Status" => SUCCESS));
            }

            break;
        //update general settings and save to logger //todo-> add logger
        case "UpdateGeneralSettings":
            unset($_POST["fun"]);
            if (empty($_POST['id'])) {
                echo json_encode(array("response" => "id is required", "Status" => ERROR));
                break;
            } elseif (count($_POST) < 2) {
                echo json_encode(array("response" => "noting to change", "Status" => ERROR));
                break;
            }
            $GeneralMeetingSettings = $GeneralMeetingSettings::find($_POST['id']);
            foreach ($_POST as $key => $value) {
                $GeneralMeetingSettings->$key = $value;
            }
            $validator = Validator::make($GeneralMeetingSettings->getAttributes(), MeetingGeneralSettings::$updateRules);
            if ($validator->passes()) {
                $GeneralMeetingSettings->save();

                if (isset($_POST['AutoApproval']) && $_POST['AutoApproval'] == 1) {
                    // approve all awaiting meetings
                    EditMeetingService::approveMeeting('all');
                }

                // todo add to logger and row edit time?
                echo json_encode(array("response" => 'update was success', "Status" => SUCCESS));
            }
            else{
                echo json_encode(array("response" => $validator->errors()->toArray(), "Status" => ERROR));
            }
            break;
            //update general settings and save to logger //todo-> add logger

        /**************** Meeting Cancellation Policy  ****************/
        // return all Cancellation Policy (array), if none create new default
        case "GetAllMeetingCancellationPolicy":
            unset($_POST["fun"]);
            if (empty($_POST['generalMeetingSettingId'])) {
                echo json_encode(array("Message" => "generalMeetingSettingId required", "Status" => ERROR));
            } elseif (!is_numeric($_POST['generalMeetingSettingId'])) {
                echo json_encode(array("Message" => "generalMeetingSettingId must be +number", "Status" => ERROR));
            } else {
                $response = [];
                $MeetingCancellationPolicy = MeetingCancellationPolicy::getAllByGeneralMeetingSettingId($_POST['generalMeetingSettingId']);
                //if empty create default payment option
                if (empty($MeetingCancellationPolicy)) {
                    $MeetingCancellationPolicy = new  MeetingCancellationPolicy([
                        'GeneralMeetingSettingId' =>  $_POST['generalMeetingSettingId']
                    ]);
                    $MeetingCancellationPolicy->save();
                    $MeetingCancellationPolicy = $MeetingCancellationPolicy->find($MeetingCancellationPolicy->id);
                    $response[] = $MeetingCancellationPolicy->toArray();
                } else {
                    foreach ( $MeetingCancellationPolicy as $payment) {
                        $response[] = $payment->toArray();
                    }

                }
                echo json_encode(array("response" => $response, "Status" => SUCCESS));
            }
            break;
        //todo-> add logger
        case "ChangeStatusToCancellationPolicy":
            unset($_POST['fun']);
            if (empty($_POST['id'])) {
                echo json_encode(array("Message" => "id is required", "Status" => ERROR));
            } else if (!isset($_POST['Status']) || !is_numeric($_POST['Status'])) {
                echo json_encode(array("Message" => "status is required", "Status" => ERROR));
            } else {
                try {
                    $MeetingCancellationPolicy = $MeetingCancellationPolicy::find($_POST['id']);
                    $MeetingCancellationPolicy->Status = $_POST['Status'];
                    $MeetingCancellationPolicy->save();
                    echo json_encode(array('id' => $_POST['id'] ,"Status" => SUCCESS));
                } catch (Exception $e) {
                    $resp = catchErrors($e, null, 'error ChangeStatusToCancellationPolicy');
                    echo json_encode($resp);
                }
            }
            break;
        // validation and change pre-payment record //todo-> add logger
        case "UpdateCancellationPolicy":
            unset($_POST["fun"]);
            if (empty($_POST['id'])) {
                echo json_encode(array("response" => "id is required", "Status" => ERROR));
                break;
            } else if (count($_POST) < 2) {
                echo json_encode(array("response" => "noting to change", "Status" => ERROR));
                break;
            }
            try {
                /** @var MeetingCancellationPolicy $MeetingCancellationPolicy */
                $MeetingCancellationPolicy = $MeetingCancellationPolicy::find($_POST['id']);
                $NewMeetingCancellationPolicy = $MeetingCancellationPolicy->clone();
                unset($_POST["id"]);
                foreach ($_POST as $key => $value) {
                    if($value !== '') {
                        $NewMeetingCancellationPolicy->$key = $value;
                    }
                }
                $validator = Validator::make($NewMeetingCancellationPolicy->getAttributes(), $MeetingCancellationPolicy::$updateRules);
                if ($validator->passes()) {
                    $NewMeetingCancellationPolicy->chargeStatusWhenAmountZero();
                    $NewMeetingCancellationPolicy->save();
                    //old MeetingCancellationPolicy change status to not active
                    $MeetingCancellationPolicy->Status = 0;
                    $MeetingCancellationPolicy->chargeStatusWhenAmountZero();
                    $MeetingCancellationPolicy->save();
                    echo json_encode(array("response" => $NewMeetingCancellationPolicy->toArray(), "Status" => SUCCESS, "oldId" => $MeetingCancellationPolicy->id));
                } else {
                    echo json_encode(array("response" => $validator->errors()->toArray(), "Status" => ERROR));
                }
                // todo add to logger
            } catch (Exception $e) {
                $resp = catchErrors($e, null, 'error UpdateCancellationPolicy');
                echo json_encode($resp);
            }
            break;
        //add new pre payment //todo-> add logger
        case "CreateNewCancellationPolicy":
            unset($_POST["fun"]);
            $MeetingCancellationPolicy = new MeetingCancellationPolicy();
            foreach ($_POST as $key => $value) {
                if($value !== '') {
                    $MeetingCancellationPolicy->$key = $value;
                }
            }
            $validator = Validator::make($MeetingCancellationPolicy->getAttributes(), $MeetingCancellationPolicy::$createRules);
            if ($validator->passes()) {
                try {
                    $MeetingCancellationPolicy->chargeStatusWhenAmountZero();
                    $MeetingCancellationPolicy->save();
                    echo json_encode(array("response" => $MeetingCancellationPolicy->toArray(), "isNew" => 1, "Status" => SUCCESS));
                } catch (Exception $e) {
                    $resp = catchErrors($e, null, 'error CreateNewCancellationPolicy');
                    echo json_encode($resp);
                }
            } else{
                echo json_encode(array("response" => $validator->errors()->toArray(), "Status" => ERROR));
            }
            break;
        /**************** Meeting Categories  ****************/
        // get all meeting categories from db
        case "GetMeetingCategories":
            unset($_POST["fun"]);
            if (empty($_POST['CompanyNum'])) {
                echo json_encode(array("Message" => "CompanyNum required", "Status" => ERROR));
            } elseif (!is_numeric($_POST['CompanyNum'])) {
                echo json_encode(array("Message" => "CompanyNum must be number", "Status" => ERROR));
            } else {
                $res = $MeetingCategories->getAllByCompanyNum($_POST['CompanyNum']);
                echo json_encode(array("Categories" => $res, "Status" => SUCCESS));
                break;
            }
            break;
        //todo-> add logger
        case "EditMeetingCategory":
            unset($_POST["fun"]);
            if (empty($_POST['id'])) {
                echo json_encode(array("response" => "id is required", "Status" => ERROR));
                break;
            } else if (count($_POST) < 2) {
                echo json_encode(array("response" => "noting to change", "Status" => ERROR));
                break;
            }
            $MeetingCategories = $MeetingCategories::find($_POST['id']);
            unset($_POST["id"]);
            foreach ($_POST as $key => $value) {
                if($value !== '') {
                    $MeetingCategories->$key = $value;
                }
            }
            $validator = Validator::make($MeetingCategories->getAttributes(), $MeetingCategories::$updateRules);
            if ($validator->passes()) {
                try {
                    $MeetingCategories->save();
                    echo json_encode(array("response" => $MeetingCategories->toArray(), "action" => 'edit', "Status" => SUCCESS));
                } catch (Exception $e) {
                    $resp = catchErrors($e, null, 'error EditMeetingCategory');
                    echo json_encode($resp);
                }
            }
            else{
                echo json_encode(array("response" => $validator->errors()->toArray(),"Status" => ERROR));
            }
            break;
        //todo-> add logger
        case "CreateMeetingCategory":
            unset($_POST["fun"]);
            foreach ($_POST as $key => $value) {
                $MeetingCategories->$key = $value;
            }
            $validator = Validator::make($MeetingCategories->getAttributes(), $MeetingCategories::$CreateRules);
            if ($validator->passes()) {
                $MeetingCategories->save();
                echo json_encode(array("response" => $MeetingCategories->toArray(), "action" => 'new' ,"Status" => SUCCESS));
            }
            else{
                echo json_encode(array("response" => $validator->errors()->toArray(), "Status" => ERROR));
            }
            break;
        //todo-> add logger
        case "RemoveMeetingCategory":
            unset($_POST["fun"]);
            if (empty($_POST['id'])) {
                echo json_encode(array("response" => "id is required", "Status" => ERROR));
                break;
            } else if (!is_numeric($_POST['id'])) {
                echo json_encode(array("response" => "id not valid", "Status" => ERROR));
                break;
            }
            $MeetingCategories = $MeetingCategories::find($_POST['id']);
            try {
                $MeetingCategories->Status = 0;
                $MeetingCategories->save();
                echo json_encode(array("action" => 'remove', "Status" => SUCCESS));
            } catch (Exception $e) {
                $resp = catchErrors($e, null, 'error EditMeetingCategory');
                echo json_encode($resp);
            }
            break;
        //todo-> add logger
        case "ReplaceCategoryInTemplates":
            unset($_POST["fun"]);
            if (!isset($_POST['newCategoryId'])) {
                echo json_encode(array("response" => "newCategoryId is required", "Status" => ERROR));
                break;
            } else if (empty($_POST['oldCategoryId'])) {
                echo json_encode(array("response" => "oldCategoryId is required", "Status" => ERROR));
                break;
            } else if (!is_numeric($_POST['oldCategoryId'])) {
                echo json_encode(array("response" => "oldCategoryId not valid", "Status" => ERROR));
                break;
            } else if (!isset($_POST['isNewCategory']) || !is_numeric($_POST['isNewCategory'])) {
                echo json_encode(array("response" => "isNewCategory invalid", "Status" => ERROR));
                break;
            } else if (($_POST['isNewCategory']) == '0' && !is_numeric($_POST['newCategoryId'])) {
                echo json_encode(array("response" => "newCategoryId not valid", "Status" => ERROR));
                break;
            } else if (empty($_POST['template'])) {
                echo json_encode(array("response" => "templates array empty", "Status" => ERROR));
                break;
            }else {
                $newCategoryId = $_POST['newCategoryId'];
                // create new category
                if ($_POST['isNewCategory'] == 1) {
                    try {
                        $newCategoryId = createNewCategory($companyNum, $newCategoryId);
                    } catch (Exception $e) {
                        $resp = catchErrors($e, $_POST,'error in ReplaceCategoryInTemplates - add Meeting Categories');
                        echo json_encode($resp);
                        exit();
                    }
                }
                // replace category in each templates
                try {
                    foreach ($_POST['template'] as $id) {
                        $template = (new $MeetingTemplates)->find($id);
                        $template->CategoryId = $newCategoryId;
                        $template->save();
                    }
                } catch (Exception $e) {
                    $resp = catchErrors($e, null, 'error ReplaceCategoryInTemplates');
                    echo json_encode($resp);
                    exit();
                }
                // delete old category id
                $MeetingCategories = $MeetingCategories::find($_POST['oldCategoryId']);
                try {
                    $MeetingCategories->Status = 0;
                    $MeetingCategories->save();
                    echo json_encode(array("action" => 'replace', "Status" => SUCCESS));
                } catch (Exception $e) {
                    $resp = catchErrors($e, null, 'error delete old category id');
                    echo json_encode($resp);
                    exit();
                }
            }
            break;
       //get from users all coaches by CompanyNum
        case "GetCoachesByCompanyNum":
            unset($_POST["fun"]);
            if (empty($_POST['companyNum'])) {
                echo json_encode(array("Message" => "companyNum is required", "Status" => ERROR));
                break;
            }
            //get all active coaches
            $coachesList = $Users->getCoachesLimitData($companyNum);
            echo json_encode(array("coachesList" => $coachesList, "Status" => SUCCESS));
            break;
        //get brands and calendars
        case "GetBrandAndCalendarsOptions":
            unset($_POST["fun"]);
            if (empty($_POST['CompanyNum'])) {
                echo json_encode(array("Message" => "companyNum required", "Status" => ERROR));
            }
            else {
                $brandAndCalendarsArray = Section::getAllBrandAndCalendars($_POST['CompanyNum']);
                $result = array();
                foreach ($brandAndCalendarsArray as $element) {
                    //When there is no branch sets 0 and a main branch
                    if(!$element->id) {
                        $element->id = 0;
                        $element->BrandName = lang('primary_branch');
                    }
                    $result[$element->id][] = $element->toArray();
                }
                echo json_encode(array("calenderOptions" => $result, "Status" => SUCCESS));
            }
            break;

        /**************** Meeting staff availability  ****************/
        // get all the staff of this company num
        case "GetStaffByCompanyNum":
            unset($_POST["fun"]);
            $CoachList = $Users->getCoachesLimitData($companyNum);
            echo json_encode(array("CoachList" => $CoachList, "Status" => SUCCESS));
            break;
        // update Availability Status in users db
        case "ChangeAvailabilityStatus":
            unset($_POST['fun']);
            if (empty($_POST['id'])) {
                echo json_encode(array("Message" => "id is required", "Status" => ERROR));
            } else if (empty($_POST['Status']) && !is_numeric($_POST['Status'])) {
                echo json_encode(array("Message" => "status is required", "Status" => ERROR));
            } else {
                try {
                    $Users->updateAvailabilityStatus($_POST['id'],$_POST['Status']);
                    echo json_encode(array('id' => $_POST['id'], "Status" => SUCCESS));
                } catch (Exception $e) {
                    $resp = catchErrors($e, null, 'error ChangeAvailabilityStatus');
                    echo json_encode($resp);
                    exit();
                }
            }
            break;
        // returns the weekly availability of the coach
        case "GetCoachWeekAvailability":
            unset($_POST["fun"]);
            if (!isset($_POST["userId"])) {
                echo json_encode(["Message" => "userId is required", "Status" => ERROR]);
            } elseif(!is_numeric($_POST["userId"])) {
                echo json_encode(["Message" => "userId must be numeric", "Status" => ERROR]);
            } elseif(!isset($_POST["date"])) {
                echo json_encode(["Message" => "date is required", "Status" => ERROR]);
            }
            else {
                try {
                    $lastDay = date('Y.m.d',strtotime($_POST["date"]) + 518400);
                    $res = $MeetingStaffRuleAvailability->getCoachWeekAvailabilityArray($_POST["userId"],$_POST["date"],$lastDay);
                    echo json_encode(["weekAvailability" => $res, "userId"=> $_POST["userId"],  "Status" => SUCCESS]);
                } catch (Exception $e) {
                    $resp = catchErrors($e, null, 'error GetCoachWeekAvailability');
                    echo json_encode($resp);
                    exit();
                }
            }
            break;
        // create availability rule and availability date by reapet status
        case "CreateNewAvailability":
            unset($_POST["fun"]);
            unset($_POST["WasRepeatStatus"]);
            if (!isset($_POST["EndPeriodicDateStatus"]) || !is_numeric($_POST["EndPeriodicDateStatus"])) {
                echo json_encode(["Message" => "EndPeriodicDateStatus is required", "Status" => ERROR]);
            } elseif (!isset($_POST["Date"]) ) {
                echo json_encode(["Message" => "Date is required", "Status" => ERROR]);
            }
            if ($_POST['EndPeriodicDateStatus'] == '0') {
                unset($_POST['EndPeriodicDate']);
            }
            unset($_POST["EndPeriodicDateStatus"]);
            $date = $_POST['Date'];
            unset($_POST["Date"]);
            //  create availability rule
            $MeetingStaffRuleAvailability = new MeetingStaffRuleAvailability();
            foreach ($_POST as $key => $value) {
                if($value !== '') {
                    $MeetingStaffRuleAvailability->$key = $value;
                }
            }
            $validator = Validator::make($MeetingStaffRuleAvailability->getAttributes(), $MeetingStaffRuleAvailability::$CreateRules);
            if ($validator->passes()) {
                try {
                    $MeetingStaffRuleAvailability->save();
                    //  create availability date
                    $DateAvailabilityId = $MeetingStaffRuleAvailability->addNewStaffDateAvailability($date);
                    echo json_encode(array("response" => $MeetingStaffRuleAvailability->toArray(),
                                            "dateId" => $DateAvailabilityId,
                                            "isNew" => 1,
                                            "Status" => SUCCESS));
                } catch (Exception $e) {
                    $resp = catchErrors($e, null, 'error CreateNewAvailability');
                    echo json_encode($resp);
                }
            } else{
                echo json_encode(array("response" => $validator->errors()->toArray(), "Status" => ERROR));
            }
            break;
        // returns the weekly availability of the coach
        case "GetAvailabilityTime":
            unset($_POST["fun"]);
            if ((!isset($_POST["dateId"]) || !is_numeric($_POST["dateId"]))){
                echo json_encode(["Message" => "ruleId not valid", "Status" => ERROR]);
            } elseif ((!isset($_POST["ruleId"]) || !is_numeric($_POST["ruleId"]))) {
                echo json_encode(["Message" => "dateId not valid", "Status" => ERROR]);
            } else {
                try {
                    $res = $MeetingStaffRuleAvailability->find($_POST["ruleId"]);
                    $res->setDateAvailability($_POST['dateId']);
                    echo json_encode(["availability" => $res->returnArray(), "Status" => SUCCESS]);
                } catch (Exception $e) {
                    $resp = catchErrors($e, null, 'error GetAvailabilityTime');
                    echo json_encode($resp);
                    exit();
                }
            }
            break;
        case 'UpdateAvailability':
            unset($_POST["fun"]);
            if ((!isset($_POST["dateId"]) || !is_numeric($_POST["dateId"]))){
                echo json_encode(["Message" => "dateId not valid", "Status" => ERROR]);
            } elseif ((!isset($_POST["ruleId"]) || !is_numeric($_POST["ruleId"]))) {
                echo json_encode(["Message" => "dateId not valid", "Status" => ERROR]);
            } elseif ((!isset($_POST["WasRepeatStatus"]) || !is_numeric($_POST["WasRepeatStatus"]))) {
                echo json_encode(["Message" => "WasRepeatStatus not valid", "Status" => ERROR]);
            } else if (count($_POST) < 4) {
                echo json_encode(array("response" => "noting to change", "Status" => ERROR));
                break;
            }
            $dateAvailabilityId = $_POST['dateId'];
            unset($_POST["dateId"]);


            $MeetingStaffRuleAvailability = MeetingStaffRuleAvailability::find($_POST['ruleId']);
            $oldRuleId = $_POST['ruleId'];
            unset($_POST["ruleId"]);

            //Availability was not cyclical
            if($_POST["WasRepeatStatus"] == '0') {
                unset($_POST["WasRepeatStatus"]);
                if (isset($_POST['EndPeriodicDateStatus'])) {
                    unset($_POST["EndPeriodicDateStatus"]);
                }

                foreach ($_POST as $key => $value) {
                    if($value !== '') {
                        $MeetingStaffRuleAvailability->$key = $value;
                    }
                }
                $validator = Validator::make($MeetingStaffRuleAvailability->getAttributes(), $MeetingStaffRuleAvailability::$updateRules);
                if ($validator->passes()) {
                    try {
                        $MeetingStaffRuleAvailability->save();
                        // now availability cyclical and need add availability date
                        if($MeetingStaffRuleAvailability->RepeatStatus  === '1') {
                            $MeetingStaffDateAvailability = MeetingStaffDateAvailability::find($dateAvailabilityId);
                            $date = date('Y-m-d', strtotime('+7 day', strtotime($MeetingStaffDateAvailability->Date)));
                            $MeetingStaffRuleAvailability->addNewStaffDateAvailability($date);
                        }
                        echo json_encode(array("response" => $MeetingStaffRuleAvailability->toArray(),
                            "dateId" => $dateAvailabilityId,
                            "isNew" => 0,
                            "Status" => SUCCESS));
                        // todo add to logger
                    } catch (Exception $e) {
                        $resp = catchErrors($e, null, 'error UpdateAvailability');
                        echo json_encode($resp);
                    }
                }
                else{
                    echo json_encode(array("response" => $validator->errors()->toArray(), "Status" => ERROR));
                }
                break;
            } else {
                unset($_POST["WasRepeatStatus"]);
                try {
                    //Editing a single availability
                    if($_POST['editMode'] == '0') {
                        unset($_POST["editMode"]);
                        //create new availability rule
                        $MeetingStaffRuleAvailability->offsetUnset('id');
                        $newMeetingStaffRuleAvailability =  new MeetingStaffRuleAvailability($MeetingStaffRuleAvailability->toArray());
                        foreach ($_POST as $key => $value) {
                            if($value !== '') {
                                $newMeetingStaffRuleAvailability->$key = $value;
                            }
                        }
                        //new availability is not repeated
                        $newMeetingStaffRuleAvailability->RepeatStatus = "0";
                        $newMeetingStaffRuleAvailability->save();
                        //link availability date to new rule
                        $MeetingStaffDateAvailability = MeetingStaffDateAvailability::find($dateAvailabilityId);
                        $MeetingStaffDateAvailability->RuleAvailabilityId = $newMeetingStaffRuleAvailability->id;
                        $MeetingStaffDateAvailability->save();

                        echo json_encode(array("response" => $newMeetingStaffRuleAvailability->toArray(),
                            "dateId" => $dateAvailabilityId,
                            "isNew" => 0,
                            "Status" => SUCCESS));
                    //Editing an availability sequence
                    } else {
                        unset($_POST["editMode"]);
                        //get satart of periodic
                        if(isset($_POST['startPeriodic'])) {
                            $startPeriodic = $_POST['startPeriodic'] === '0' ? date('Y-m-d') :$_POST['startPeriodic'] ;
                            unset($_POST['startPeriodic']);
                        }
                        //get end of periodic
                        if(isset($_POST['endPeriodic'])) {
                            $endPeriodic = '0';
                            $endPeriodicAmount = '0';
                            unset($_POST['endPeriodic']);
                            if(isset($_POST['endPeriodicAmount'])) {
                                $endPeriodicAmount = $_POST['endPeriodicAmount'];
                                unset($_POST['endPeriodicAmount']);
                            }elseif (isset($_POST['endPeriodicDate'])) {
                                $endPeriodic = $_POST['endPeriodicDate'];
                                unset($_POST['endPeriodicDate']);
                            }
                        }
                        //change old EndPeriodicDate to satart of the new
                        $oldEndPeriodicDate = $MeetingStaffRuleAvailability->EndPeriodicDate;
                        $MeetingStaffRuleAvailability->EndPeriodicDate = $startPeriodic;
                        $MeetingStaffRuleAvailability->save();

                        //create new availability rule
                        $MeetingStaffRuleAvailability->offsetUnset('id');
                        $newMeetingStaffRuleAvailability =  new MeetingStaffRuleAvailability($MeetingStaffRuleAvailability->toArray());
                        foreach ($_POST as $key => $value) {
                            if($value !== '') {
                                $newMeetingStaffRuleAvailability->$key = $value;
                            }
                        }
                        $newMeetingStaffRuleAvailability->EndPeriodicDate = $oldEndPeriodicDate;
                        $newMeetingStaffRuleAvailability->save();

                        $lastDatw = (new MeetingStaffDateAvailability())->updateRuleId($oldRuleId,$newMeetingStaffRuleAvailability->id,$startPeriodic,$endPeriodic,$endPeriodicAmount);

                        if($endPeriodicAmount || $endPeriodic ) {
                            $endPeriodic = $lastDatw->Date > $endPeriodic ? $lastDatw->Date : $endPeriodic;
                            $newMeetingStaffRuleAvailability->EndPeriodicDate = $endPeriodic;
                            $newMeetingStaffRuleAvailability->save();
                        }

                        echo json_encode(array("response" => $newMeetingStaffRuleAvailability->toArray(),
                            "dateId" => $dateAvailabilityId,
                            "isNew" => 0,
                            "Status" => SUCCESS));

                    }

                } catch (Exception $e) {
                    $resp = catchErrors($e, null, 'error UpdateAvailability');
                    echo json_encode($resp);
                }
                break;

            }
        //todo-> add logger
        case 'DeleteAvailability':
            unset($_POST["fun"]);
            if ((!isset($_POST["dateId"]) || !is_numeric($_POST["dateId"]))){
                echo json_encode(["Message" => "dateId not valid", "Status" => ERROR]);
            } elseif ((!isset($_POST["ruleId"]) || !is_numeric($_POST["ruleId"]))) {
                echo json_encode(["Message" => "ruleId not valid", "Status" => ERROR]);
            } else{
                try {
                    $MeetingStaffRuleAvailability = MeetingStaffRuleAvailability::find($_POST['ruleId']);
                    if(isset($_POST['editMode'])) {
                        $editMode = $_POST['editMode'] ;
                    } else {
                        $editMode = '0';
                    }
                    if ($editMode == '0' ){
                        $MeetingStaffDateAvailability = MeetingStaffDateAvailability::find($_POST["dateId"]);
                        $MeetingStaffDateAvailability->Status = 0;
                        $MeetingStaffDateAvailability->save();
                    } else {
                        //get satart of periodic
                        if(isset($_POST['startPeriodic'])) {
                            $startPeriodic = $_POST['startPeriodic'] === '0' ? date('Y-m-d') :$_POST['startPeriodic'] ;
                        }
                        //get end of periodic
                        if(isset($_POST['endPeriodic'])) {
                            if($_POST['endPeriodic'] == '2') {
                                $MeetingStaffRuleAvailability->Status = '0';
                                $MeetingStaffRuleAvailability->save();
                            }
                            $endPeriodic = '0';
                            $endPeriodicAmount = '0';
                            if(isset($_POST['endPeriodicAmount'])) {
                                $endPeriodicAmount = $_POST['endPeriodicAmount'];
                            }elseif (isset($_POST['endPeriodicDate'])) {
                                $endPeriodic = $_POST['endPeriodicDate'];
                            }
                        }
                        (new MeetingStaffDateAvailability())->removeBetweenDate($_POST['ruleId'],$startPeriodic,$endPeriodic,$endPeriodicAmount);
                    }
                    echo json_encode(array('id' => $_POST['dateId'], "Day"=> $MeetingStaffRuleAvailability->Day, "Status" => SUCCESS));
                    break;
                } catch (Exception $e) {
                    $resp = catchErrors($e, null, 'error DeleteAvailability');
                    echo json_encode($resp);
                    exit();
                }
            }



        case "InsertClassSettingsNewData":
            unset($_POST["fun"]);
            if (!isset($_POST["MaxClient"])) {
                echo json_encode(array("Message" => "MaxClient Required", "Status" => "Error"));
            } elseif (!is_numeric($_POST["MaxClient"])) {
                echo json_encode(array("Message" => "MaxClient must be numeric", "Status" => "Error"));
            } elseif (!isset($_POST["MinClient"])) {
                echo json_encode(array("Message" => "MinClient Required", "Status" => "Error"));
            } elseif (!is_numeric($_POST["MinClient"])) {
                echo json_encode(array("Message" => "MinClient must be numeric", "Status" => "Error"));
            } elseif (!isset($_POST["DefaultStatusClass"])) {
                echo json_encode(array("Message" => "DefaultStatusClass Required", "Status" => "Error"));
            } elseif (!is_numeric($_POST["DefaultStatusClass"])) {
                echo json_encode(array("Message" => "DefaultStatusClass must be numeric", "Status" => "Error"));
            } elseif (!isset($_POST["CheckMinClient"])) {
                echo json_encode(array("Message" => "CheckMinClient Required", "Status" => "Error"));
            } elseif (!is_numeric($_POST["CheckMinClient"])) {
                echo json_encode(array("Message" => "CheckMinClient must be numeric", "Status" => "Error"));
            } elseif (!isset($_POST["CheckMinClientType"])) {
                echo json_encode(array("Message" => "CheckMinClientType Required", "Status" => "Error"));
            } elseif (!is_numeric($_POST["CheckMinClientType"])) {
                echo json_encode(array("Message" => "CheckMinClientType must be numeric", "Status" => "Error"));
            } elseif (!isset($_POST["EndClassTime"])) {
                echo json_encode(array("Message" => "EndClassTime Required", "Status" => "Error"));
            } elseif (!is_numeric($_POST["EndClassTime"])) {
                echo json_encode(array("Message" => "EndClassTime must be numeric", "Status" => "Error"));
            } elseif (!isset($_POST["ReminderTime"])) {
                echo json_encode(array("Message" => "ReminderTime Required", "Status" => "Error"));
            } elseif (!is_numeric($_POST["ReminderTime"])) {
                echo json_encode(array("Message" => "ReminderTime must be numeric", "Status" => "Error"));
            } elseif (!isset($_POST["ReminderTimeType"])) {
                echo json_encode(array("Message" => "ReminderTimeType Required", "Status" => "Error"));
            } elseif (!is_numeric($_POST["ReminderTimeType"])) {
                echo json_encode(array("Message" => "ReminderTimeType must be numeric", "Status" => "Error"));
            } elseif (!isset($_POST["ReminderTimeDayBefore"])) {
                echo json_encode(array("Message" => "ReminderTimeDayBefore Required", "Status" => "Error"));
            } elseif (!DateTime::createFromFormat('H:i:s', $_POST["ReminderTimeDayBefore"])) {
                echo json_encode(array("Message" => "ReminderTimeDayBefore is not in the format H:i:s", "Status" => "Error"));
            } elseif (!isset($_POST["CancelTime"])) {
                echo json_encode(array("Message" => "CancelTime Required", "Status" => "Error"));
            } elseif (!is_numeric($_POST["CancelTime"])) {
                echo json_encode(array("Message" => "CancelTime must be numeric", "Status" => "Error"));
            } elseif (!isset($_POST["CancelTimeType"])) {
                echo json_encode(array("Message" => "CancelTimeType Required", "Status" => "Error"));
            } elseif (!is_numeric($_POST["CancelTimeType"])) {
                echo json_encode(array("Message" => "CancelTimeType must be numeric", "Status" => "Error"));
            } elseif (!isset($_POST["CancelTimeDayBefore"])) {
                echo json_encode(array("Message" => "CancelTimeDayBefore Required", "Status" => "Error"));
            } elseif (!DateTime::createFromFormat('H:i:s', $_POST["CancelTimeDayBefore"])) {
                echo json_encode(array("Message" => "CancelTimeDayBefore is not in the format H:i:s", "Status" => "Error"));
            } elseif (!isset($_POST["WatingListPOPUP"])) {
                echo json_encode(array("Message" => "WatingListPOPUP Required", "Status" => "Error"));
            } elseif (!is_numeric($_POST["WatingListPOPUP"])) {
                echo json_encode(array("Message" => "WatingListPOPUP must be numeric", "Status" => "Error"));
            } elseif (!isset($_POST["RegularNum"])) {
                echo json_encode(array("Message" => "RegularNum Required", "Status" => "Error"));
            } elseif (!is_numeric($_POST["RegularNum"])) {
                echo json_encode(array("Message" => "RegularNum must be numeric", "Status" => "Error"));
            } elseif (!isset($_POST["CancelMinimum"])) {
                echo json_encode(array("Message" => "CancelMinimum Required", "Status" => "Error"));
            } elseif (!is_numeric($_POST["CancelMinimum"])) {
                echo json_encode(array("Message" => "CancelMinimum must be numeric", "Status" => "Error"));
            } elseif (!isset($_POST["GuideCheck"])) {
                echo json_encode(array("Message" => "GuideCheck Required", "Status" => "Error"));
            } elseif (!is_numeric($_POST["GuideCheck"])) {
                echo json_encode(array("Message" => "GuideCheck must be numeric", "Status" => "Error"));
            } else {
                $_POST["CompanyNum"] = $companyNum;
                $id = $ClassSettings->InsertClassSettingsNewData($_POST);
                echo json_encode(array("id" => $id, "Status" => "Success"));
            }

            break;
        case "UpdateClassSettings":
            $appSettingsObj = AppSettings::getByCompanyNum($companyNum);
            if($appSettingsObj) {
                $selectTimes = $_POST['SelectTimes'] ?? $appSettingsObj->SelectTimes;
                $viewClass = $_POST['ViewClass'] ?? $appSettingsObj->ViewClass;
                $viewClassDayNum = $_POST['ViewClassDayNum'] ?? $appSettingsObj->ViewClassDayNum;
                unset($_POST["fun"], $_POST['SelectTimes'], $_POST['ViewClass'], $_POST['ViewClassDayNum']);
                $affect = $ClassSettings->UpdateClassSettings($_POST,$companyNum);
                $appSettingsUpdate = $classAppSettings->updateAppSettingsByCompanyNum($companyNum, $viewClass, TimeHelper::GetDayByNum($viewClassDayNum), $selectTimes, $viewClassDayNum);
                echo json_encode(array('affect' => $affect,"Status" => "Success"));
            }

            break;
        case "GetClassSettingsByCompanyNum":
            $CompanyClassSettings = $ClassSettings->GetClassSettingsByCompanyNum($companyNum);
            $companyAppSettings = $classAppSettings->getAppSettingsByCompanyNum($companyNum);
            $filters = $ClassCalendar->GetLastFilterForUser($companyNum, Auth::user()->id);
            if(!$filters) {
                $filters = [
                    "TypeOfView" => 1,
                    "SplitView" => 1
                ];
            }
            echo json_encode(array('AppSettings' => $companyAppSettings->toArray(),'ClassSettings' => $CompanyClassSettings, 'Filters' => $filters, "Status" => "Success"));
            break;

        /***** calendars And Classes *****/
        case "GetFutureEventCount":
            unset($_POST["fun"]);
            if (!isset($_POST["ClassTypeId"])) {
                echo json_encode(["Message" => "ClassTypeId required", "Status" => "Error"]);
            } elseif(!is_numeric($_POST["ClassTypeId"])) {
                echo json_encode(["Message" => "ClassTypeId must be numeric", "Status" => "Error"]);
            } else {
                $count = $ClassCalendar->getFutureClassesCountByClassId($_POST["ClassTypeId"]);
                echo json_encode(["Message" => $count >= 0 ? $count : "Unauthorized", "Status" => $count >= 0 ? "Success" : "Error"]);
            }
            break;
        case "InsertSingleClassType":

            unset($_POST["fun"]);
            if (empty($_POST["Type"])) {
                echo json_encode(array("Message" => lang('req_field_class_type_ajax'), "Status" => "Error"));
            } elseif (empty($_POST["Color"])) {
                echo json_encode(array("Message" => lang('req_field_color_ajax'), "Status" => "Error"));
            } elseif (empty($_POST["duration"])) {
                echo json_encode(array("Message" => lang('req_field_lesson_length_ajax'), "Status" => "Error"));
            } elseif (!is_numeric($_POST["duration"])) {
                echo json_encode(array("Message" => lang('lesson_length_numeric'), "Status" => "Error"));
                // } elseif (empty($_POST["memberships"])) {
                //     echo json_encode(array("Message" => "memberships  required", "Status" => "Error"));
            } elseif (isset($_POST["memberships"]) && !is_array($_POST["memberships"])) {
                echo json_encode(array("Message" => "memberships should be an array", "Status" => "Error"));
            } else {
                $_POST["CompanyNum"] = $companyNum;
                $res = $ClassesType->insertSingleClassType($_POST);
                echo json_encode( array("insertedId" => $res, "Status" => "Success"));
            }
            break;
        case "getSingleClassTypeWithMemberships":
            unset($_POST["fun"]);
            if (empty($_POST["id"])) {
                echo json_encode(array("Message" => "id required", "Status" => ERROR));
            } elseif (!is_numeric($_POST["id"])) {
                echo json_encode(array("Message" => "id has to be numeric", "Status" => ERROR));
            } else {
                /** @var ClassesType $ClassTypeObj */
                $ClassTypeObj = ClassesType::find($_POST['id']);
                if($ClassTypeObj === null) {
                    $res = false;
                }else {
                    $res = $ClassTypeObj->toArray();
                    $res['memberships'] = $ClassTypeObj->getMembershipsIdByClassType();
                }
                echo json_encode(array("ClassType" => $res ? $res : "UnAuthorized", "Status" => $res ? SUCCESS : ERROR));
            }
            break;
        case "GetAllClassTypes":
            unset($_POST["fun"]);
            $_POST["CompanyNum"] = $companyNum;
            try {
                $res = $ClassesType->getAllClassTypes($_POST);
                echo json_encode(array("ClassTypes" => $res, "Status" => SUCCESS));
            } catch (Exception $e) {
                $resp = catchErrors($e, null, 'GetAllClassTypes');
                echo json_encode($resp);
            }
            break;
        case "EditClassType":
            unset($_POST["fun"]);
            if (empty($_POST['id'])) {
                echo json_encode(array("Message" => "id required", "Status" => "Error"));
            } elseif (!is_numeric($_POST["id"])) {
                echo json_encode(array("Message" => "id has to be numeric", "Status" => "Error"));
            } else {
                /** @var ClassesType $ClassTypeObj */
                $ClassTypeObj = ClassesType::find($_POST['id']);
                $res = empty($ClassTypeObj) ? null : $ClassTypeObj->editClassType($_POST);
                echo json_encode(array("Message" => $res ?: "Unauthorized", "Status" => $res ? "Success" : "Error"));
            }
            break;
        case "DeleteMoveClassType":
            unset($_POST["fun"]);
            if (empty($_POST["id"])) {
                echo json_encode(array("Message" => "id required", "Status" => "Error"));
            } elseif (!is_numeric($_POST["id"])) {
                echo json_encode(array("Message" => "id has to be numeric", "Status" => "Error"));
            } elseif (!empty($_POST["otherId"]) && !is_numeric($_POST["otherId"])) {
                echo json_encode(array("Message" => "otherid has to be numeric", "Status" => "Error"));
            } else {
                $res = $ClassesType->DeleteMoveClassType($_POST);
                echo json_encode(array("Message" => $res ? $res : "Unauthorized", "Status" => $res ? "Success" : "Error"));
            }
            break;

        case "ToggleHideCalendar":
            unset($_POST["fun"]);
            if (empty($_POST["id"])) {
                echo json_encode(array("Message" => "id  required", "Status" => "Error"));
            } elseif (!is_numeric($_POST["id"])) {
                echo json_encode(array("Message" => "id has to be numeric", "Status" => "Error"));
            } elseif (!isset($_POST["display"])) {
                echo json_encode(array("Message" => "display  required", "Status" => "Error"));
            } elseif (!is_numeric($_POST["display"]) || $_POST["display"] < 0 || $_POST["display"] > 1) {
                echo json_encode(array("Message" => "Status must be numberic and hold only '0' or '1'", "Status" => "Error"));
            } else {
                $res = $Section->getSectionById($_POST["id"]);
                if ($res->CompanyNum != $companyNum) {
                    echo json_encode(["Message" => "Unauthorized", "Status" => "Error"]);
                    break;
                }
                $res = $Section->hideSection($companyNum,$_POST["id"],$_POST["display"]);
                echo json_encode(array("Message" => !$res ? "Record not updated" : $res, "Status" => !$res ? "Error" : "Success", "id" => $_POST["id"]));
            }
            break;
        case "InsertNewCalendar":
            unset($_POST["fun"]);
            if (!isset($_POST["Brands"])) {
                echo json_encode(array("Message" => lang('must_choose_branch'), "Status" => "Error"));
            } elseif (!is_numeric($_POST["Brands"])) {
                echo json_encode(array("Message" => lang('must_choose_branch'), "Status" => "Error"));
            } elseif (!is_numeric($_POST["Outdoor"])) {
                echo json_encode(array("Message" => lang('must_choose_outdoor_status'), "Status" => "Error"));
            } elseif (empty($_POST["Title"])) {
                echo json_encode(array("Message" => lang('name_field_required'), "Status" => "Error"));
            } else {
                $data = $_POST;
                $data['CompanyNum'] = $companyNum;
                $data['Title'] = trim($data['Title']);

                $id = Section::insertNewSection($data);
                $spaceType = $_POST["SpaceType"] ?? 0;
                $price = $_POST["SpaceType"] ?? 0;
                if ((int)$spaceType) { //if space, 2 more tables: class type and tags_section
                    $tagId = $_POST['tagId'] ?? TagsSection::DEFAULT_SPACE_TAG_ID;

                    $ClassesType = new ClassesType();
                    $ClassesType->Type = htmlspecialchars($_POST["Title"]);
                    $ClassesType->EventType = 2;
                    $ClassesType->CompanyNum = $companyNum;
                    $ClassesType->SectionId = $id;
                    $ClassesType->Price = $price;
                    $ClassesType->EditDate = date('Y-m-d H:i:s');
                    $ClassesType->save();
                    $tagsSection = new TagsSection();
                    $tagsSection->sections_id = $id;
                    $tagsSection->tags_id = $tagId;
                    $tagsSection->company_num = $companyNum;
                    $tagsSection->save();

                }
                echo json_encode(array("InsertedId" => $id, "Status" => "Success"));
            }
            break;
        case "EditCalendar":
            unset($_POST["fun"]);
            if (empty($_POST["id"])) {
                echo json_encode(array("Message" => "id  required", "Status" => "Error"));
            } elseif (!is_numeric($_POST["id"])) {
                echo json_encode(array("Message" => "id has to be numeric", "Status" => "Error"));
            } elseif (empty($_POST["Brands"]) && !empty($Brand->getAllByCompanyNum($companyNum))) {
                echo json_encode(array("Message" => lang('must_choose_branch'), "Status" => "Error"));
            } elseif (!is_numeric($_POST["Brands"])) {
                echo json_encode(array("Message" => lang('must_choose_branch'), "Status" => "Error"));
            } elseif(!is_numeric($_POST["Outdoor"])) {
                echo json_encode(array("Message" => lang('must_choose_outdoor_status'), "Status" => "Error"));
            } elseif (empty($_POST["Title"])) {
                echo json_encode(array("Message" => lang('name_field_required'), "Status" => "Error"));
            } else {
                $res = $Section->getSectionById($_POST["id"]);
                if ($res->CompanyNum != $companyNum) {
                    echo json_encode(["Message" => "Unauthorized", "Status" => "Error"]);
                    break;
                }
                $spaceType = $_POST["SpaceType"] ?? 0;
                $price = $_POST["Price"] ?? 0;
                $title = trim($_POST["Title"]);
                $arrayForUpdate = ['Type' => $title, 'Price' => $price];
                if ($spaceType) {
                    $arrayForUpdate += ['EventType' => 2];
                }
                ClassesType::updateOrCreateClassTypeBySectionId((int)$_POST["id"], $arrayForUpdate, $companyNum);
                if($spaceType == 1) {
                    $tagId = $_POST["tagId"] ?? TagsSection::DEFAULT_SPACE_TAG_ID;    // default tag id for spaces
                    TagsSection::updateOrCreateTagsBySectionId((int)$_POST["id"],
                        [
                            'sections_id' => (int)$_POST["id"],
                            'tags_id' => $tagId,
                            'company_num' => $companyNum
                        ]
                    );
                }
                $arrayForUpdate = ['Title' => $title, "Brands" => (int)$_POST["Brands"], "outdoor" => (int)$_POST["Outdoor"], "SpaceType" => $spaceType];
                $res = $Section->editSection((int)$_POST["id"], $arrayForUpdate);
                ClassStudioDate::setClassesBrandBySection($companyNum, (int)$_POST['id'], (int)$_POST['Brands']);

                echo json_encode(array("Message" => $res, "Status" => "Success"));
            }
            break;
        case "GetAllBranches":
            unset($_POST["fun"]);
            $res = $Brand->getAllByCompanyNum($companyNum);
            echo json_encode(array("BranchList" => $res, "Status" => "Success"));
            break;
        case "GetAllCalendars":
            unset($_POST["fun"]);
            $res = $Section->getCalendarsByCompanyNum($companyNum);
            echo json_encode(array("CalendarList" => $res, "Status" => "Success"));
            break;

        //todo remove?
        case "GetCalendarsByCompanyNum":
            unset($_POST["fun"]);
            $CalendarsList = $Section->GetCalendarsByCompanyNum($companyNum);
            echo json_encode(array("CalendarsList" => $CalendarsList, "Status" => "Success"));
            break;
        //todo remove?
        case "GetClassesTypeByCompanyNum":
            unset($_POST["fun"]);
            $ClassesTypeList = $ClassesType->GetClassesTypeByCompanyNum($companyNum);
            echo json_encode(array("ClassesType" => $ClassesTypeList, "Status" => "Success"));
            break;
        //todo remove?
        case "GetMemberships":
            if (empty($_POST["CompanyNum"])) {
                echo json_encode(array("Message" => "CompanyNum  required", "Status" => "Error"));
            }else {
                $res = ClassesType::getMemberships($_POST["CompanyNum"]);
                echo json_encode(array("MembershipList" => $res, "Status" => "Success"));
            }
            break;





        /**************** Device Selection ****************/
        case "DeleteNumbersSub":
            unset($_POST["fun"]);
            if (!isset($_POST["id"])) {
                echo json_encode(["Message" => "id is required", "Status" => "Error"]);
            }
            $res = $NumbersSub->deleteNumberSub($_POST["id"]);
            echo json_encode(["Message" => $res, "Status" => "Success"]);
            break;
        case "DeleteNumbers":
            if (!isset($_POST["id"])) {
                echo json_encode(["Message" => "id is required", "Status" => "Error"]);
            }
            $res = $Numbers->deleteNumbers($_POST['id']);
            echo json_encode(["Message" => $res, "Status" => "Success"]);
            break;
        case "GetNumberSubsByCompanyNum":
            if (!isset($_POST['NumbersId']) ) {
                echo json_encode(array("Message" => "NumbersId is required", "Status" => "Error"));
            } else {
                $NumbersSubList = $NumbersSub->GetNumbersSubByCompanyNum($companyNum,$_POST['NumbersId']);
                echo json_encode(array('NumbersSubList' => $NumbersSubList, "Status" => "Success"));
            }
            break;
        case "InsertNumbersSubNewData":
            unset($_POST["fun"]);
            if (!isset($_POST['NumbersId'])) {
                echo json_encode(array("Message" => "NumbersId is required", "Status" => "Error"));
            } elseif (empty($_POST["Name"])) {
                echo json_encode(array("Message" => "Name required", "Status" => "Error"));
            } elseif (!isset($_POST["recordListingId"])) {
                echo json_encode(array("Message" => "recordListingId Required", "Status" => "Error"));
            } elseif (!is_numeric($_POST["recordListingId"])) {
                echo json_encode(array("Message" => "recordListingId must be numeric", "Status" => "Error"));
            } else {
                $_POST["CompanyNum"] = $companyNum;
                $_POST["Status"] = 0;
                $id = $NumbersSub->InsertNumbersSubNewData($_POST);
                echo json_encode(array("id" => $id, "Status" => "Success"));
            }
            break;
        case "UpdateNumbersSub":
            unset($_POST["fun"]);
            if (!isset($_POST['id']) ) {
                echo json_encode(array("Message" => "id is required", "Status" => "Error"));
            } else {
                $_POST["CompanyNum"] = $companyNum;
                $affect = $NumbersSub->UpdateNumbersSubById($_POST);
                echo json_encode(array('affect' => $affect,"Status" => "Success"));
            }
            break;
        case "InsertNumbersNewData":
            unset($_POST["fun"]);
            if (!isset($_POST["Name"])) {
                echo json_encode(array("Message" => "Name required", "Status" => "Error"));
            } elseif (!isset($_POST["recordListingId"])) {
                echo json_encode(array("Message" => "recordListingId Required", "Status" => "Error"));
            } elseif (!is_numeric($_POST["recordListingId"])) {
                echo json_encode(array("Message" => "recordListingId must be numeric", "Status" => "Error"));
            } elseif (!isset($_POST["Unique"])) {
                echo json_encode(array("Message" => "Unique Required", "Status" => "Error"));
            } elseif (!is_numeric($_POST["Unique"])) {
                echo json_encode(array("Message" => "Unique must be numeric", "Status" => "Error"));
            } else {
                $_POST["CompanyNum"] = $companyNum;
                $id = $Numbers->InsertNumbersNewData($_POST);
                echo json_encode(array("id" => $id, "Status" => "Success"));
            }
            break;
        case "UpdateNumbers":
            unset($_POST["fun"]);
            if (empty($_POST['id']) ) {
                echo json_encode(array("Message" => "id is   required", "Status" => "Error"));
            } else {
                $_POST["CompanyNum"] = $companyNum;
                $affect = $Numbers->UpdateNumbers($_POST);
                echo json_encode(array('affect' => $affect,"Status" => "Success"));
            }
            break;
        case "GetNumbersByCompanyNum":
            $NumbersList = $Numbers->GetNumbersByCompanyNum($companyNum);
            echo json_encode(array('NumbersList' => $NumbersList, "Status" => "Success"));
            break;
        default:
            echo json_encode(array("Message" => "No Found Function","Status" => "Error"));
            break;

    }

}
else{
    echo json_encode(array("Message" => "No Function","Status" => "Error"));
}

