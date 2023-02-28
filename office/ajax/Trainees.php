<?php

header('Content-Type: application/json');
require_once __DIR__ . "/../../app/init.php";
require_once __DIR__ . "/../Classes/Utils.php";
require_once __DIR__ . "/../Classes/AppNotification.php";
require_once __DIR__ . "/../Classes/Company.php";
require_once __DIR__ . "/../Classes/ClassCalendar.php";
require_once __DIR__ . "/../Classes/ClassStudioAct.php";
require_once __DIR__ . "/../Classes/ClassStudioDate.php";
require_once __DIR__ . "/../Classes/ClientActivities.php";
require_once __DIR__ . "/../Classes/ClassStatus.php";
require_once __DIR__ . "/../Classes/Numbers.php";
require_once __DIR__ . "/../Classes/NumbersSub.php";
require_once __DIR__ . "/../Classes/ClientMedical.php";
require_once __DIR__ . "/../Classes/Clientcrm.php";
require_once __DIR__ . "/../Classes/ClassStudioDateRegular.php";
require_once __DIR__ . "/../Classes/Pipeline.php";
require_once __DIR__ . '/../Classes/ClassesCanceledSeries.php';

require_once __DIR__ . "/../Classes/Client.php";
require_once __DIR__ . "/../Classes/ItemRoles.php";
require_once __DIR__ . "/../Classes/Item.php";
require_once __DIR__ . "/../Classes/ClassLog.php";
require_once __DIR__ . "/../Classes/EncryptDecrypt.php";

require_once __DIR__ . "/../services/ClientService.php";

if (!Auth::check()) {
    echo json_encode((array("Message" => "unauthorized", "Status" => "Error")));
    exit;
}

$CompanyNum = Company::getInstance()->CompanyNum;

$ClassCalendar = new ClassCalendar();
$ClassStudioAct = new ClassStudioAct();
$StdClient = new Client();
$ClientActivities = new ClientActivities();
$Numbers = new Numbers();
$NumbersSub = new NumbersSub();
$clientmedical = new ClientMedical();
$Clientcrm = new Clientcrm();
$ClassStudioDateRegular = new ClassStudioDateRegular();
$Pipeline = new Pipeline();

$ClassStudioDate = new ClassStudioDate();
$classStudioAct = new ClassStudioAct();
$ClientActivity = new ClientActivities();
$ItemRoles = new ItemRoles();
$Item = new Item();
$EncryptDecrypt = new EncryptDecrypt();

function getLogData($status) {
    /** @var ClassStatus $classStatusObj */
    $classStatusObj = ClassStatus::find($status);
    return ['date' => date('d/m/Y H:i'), 'status' => transDbVal(trim($classStatusObj->Title)), 'statusId' => $status, 'userName' => Auth::user()->display_name];
}


function updateClientStatusToActive($clientObj, $companyNum){
    $appPassword = mt_rand(100000, 999999);

    (new Client())->updateClient($clientObj->id, ['Status' => 0, 'ArchiveDate' => null]);
    (new ClientActivities())->updateTableByClientId($clientObj->id, $companyNum, ['ClientStatus' => 0]);

    $mobileRegex = Client::mobileRegex; // israeli phone number regex
    if (preg_match($mobileRegex, $clientObj->ContactMobile) && $clientObj->parentClientId == 0) { // check valid phone
        (new UserBoostappLogin())->addLoginAccount($clientObj, $appPassword,false);
    }

    CreateLogMovement(//FontAwesome Icon
        lang('log_client_status_ajax'), //LogContent
        $clientObj->id //ClientId
    );
}

function getClientForConclusion($clientObj) {
    $hexcode = dechex(crc32($clientObj->__get('CompanyName')));
    $hexcode = substr($hexcode, 0, 6);
    return [
        "clientId" => $clientObj->__get('id'),
        "companyName" => $clientObj->__get('CompanyName'),
        "profileImage" => $clientObj->__get('ProfileImage'),
        "firstName" => $clientObj->__get('FirstName'),
        "lastName" => $clientObj->__get('LastName'),
        "hexCode" => $hexcode
    ];
}
function getWithoutChargeRelevantStatus($currentStatus, $newStatus) {
    if($newStatus == 2) {
        $updateStatus = 23;
    } else if($newStatus == 8) {
        $updateStatus = 7;
    } else {
        $updateStatus = 16;
    }
    return $updateStatus;
}

function validateAssignClientForm($data) {
    $validator = Validator::make(
                    array(
                'classTypeId' => $data->classTypeId,
                'classId' => $data->classId,
                'clientId' => $data->clientId,
                'chargeOption' => $data->chargeOption,
                'assignType' => $data->assignType,
                'clientName' => $data->clientName,
                    ), array(
                'classTypeId' => 'required',
                'classId' => 'required',
                'clientId' => 'required',
                'chargeOption' => 'required',
                'assignType' => 'required',
                'clientName' => 'required',
                    )
    );

    if (!$validator->passes()) {
        die($validator->errors());
    }
}

function exitIfNotExist($data, $prop) {
    if (!isset($data->$prop)) {
        echo json_encode(array('Message' => $prop . ' is missing\wrong', 'Status' => 'Error'));
        exit;
    }
}

if (!empty($_POST["fun"])) {
    switch ($_POST["fun"]) {
        case "GetClassDataByid":
            if (empty($_POST['ClassId'])) {
                echo json_encode(array("Message" => "ClassId required", "Status" => "Error"));
            } else {
                $ClassData = $ClassCalendar->GetClassById($CompanyNum, $_POST['ClassId']);
                echo json_encode(array('Message' => $ClassData, "Status" => "Success"));
            }



            break;
        case "getWaitingList":
            if (!isset($_POST['ClassId'])) {
                echo json_encode(array("Message" => "ClassId required", "Status" => "Error"));
            } else {
                $classStudioDate = new ClassCalendar($_POST["ClassId"]);
                $cnum = $classStudioDate->__get("CompanyNum");
                if ($classStudioDate->__get("CompanyNum") != $CompanyNum) {
                    echo json_encode((array("Message" => "Invalid ClassId", "Status" => "Error")));
                    break;
                }
                $waitingList = $ClassStudioAct->getWaitingList($_POST["ClassId"], $CompanyNum);
                echo json_encode(["Message" => $waitingList, "Status" => "Success"]);
            }
            break;
        case "checkClassLimits":
            if (!isset($_POST["ClassActId"])) {
                echo json_encode(["Message" => "ClassId required", "Status" => "Error"]);
            } else {
                $classAct = new ClassStudioAct($_POST["ClassActId"]);
                if ($classAct->__get("CompanyNum") != $CompanyNum || $classAct->__get("Status") != "9") {
                    echo json_encode(["Message" => "Invalid ClassActId", "Status" => "Error"]);
                    break;
                }

                $clientActivity = $ClientActivity->getActive($classAct->__get("ClientActivitiesId"), $classAct->__get("ClassDate"));
                if (!$clientActivity) {
                    echo json_encode(["Message" => ["No active membership"], "Status" => "Success"]);
                    break;
                }

                $limits = [];
                $classStudioDate = new ClassCalendar($classAct->__get("ClassId"));
                $activeClientCount = ClassStudioAct::getClassRegisterCount($classAct->__get("ClassId"), $CompanyNum);
                $maxClients = $classStudioDate->__get("MaxClient");

                if ($activeClientCount >= $maxClients && $maxClients != 0) {
                    array_push($limits, "The class is full");
                }

                $client = new Client($classAct->__get("ClientId"));

                if ($classStudioDate->__get("ageLimitType") > 0) {
                    switch ($classStudioDate->__get("ageLimitType")) {
                        case 1:
                            if ($client->__get("Age") < $classStudioDate->__get("ageLimitNum1")) {
                                array_push($limits, "Client is to young for this class");
                            }
                            break;
                        case 2:
                            if ($client->__get("Age") > $classStudioDate->__get("ageLimitNum1")) {
                                array_push($limits, "Client is to old for this class");
                            }
                            break;
                        case 3:
                            if ($client->__get("Age") < $classStudioDate->__get("ageLimitNum1") || $client->__get("Age") > $classStudioDate->__get("ageLimitNum2")) {
                                array_push($limits, "Client age is not in the range for this class");
                            }
                            break;
                    }
                }

                if ($classStudioDate->__get("LimitLevet") > 0 && $client->__get("ClassLevel") > 0) {
                    $levels = explode(',', $classStudioDate->__get("LimitLevel"));
                    if (!in_array($client->__get("ClassLevel"), $levels)) {
                        array_push($limits, "Client level does not match class level");
                    }
                }

                $clientActivity = new ClientActivities($clientActivity->id);
                $itemRoles = $ItemRoles->getItemRolesByItemId($clientActivity->__get("ItemId"));
                foreach ($itemRoles as $role) {
                    switch ($role->Group) {
                        case "Class":
                            $classArr = explode(",", $role->Class);
                            if (!in_array($classStudioDate->__get("ClassNameType"), $classArr)) {
                                array_push($limits, "Client membership does not include this class");
                            }
                            break;
                        case "Max":
                            if ($role->Item == "Morning" || $role->Item == "Evening") {
                                break;
                            }
                            $submissionCount = count($ClassStudioAct->getClassActsByClientId($client->__get("ClientId"), $classStudioDate->__get("StartDay"), strtolower($role->Item)));
                            if ($submissionCount >= $role->Value) {
                                array_push($limits, "Client maximum class registration limit exceeded");
                            }
                            break;
                        case "Day":
                            $days = ["ראשון", "שני", "שלישי", "רביעי", "חמישי", "שישי", "שבת"];
                            $classDay = date("w", strtotime($classStudioDate->__get("StartDate")));
                            $membershipDays = explode(",", $role->Value);
                            if (!in_array($days[$classDay], $membershipDays)) {
                                array_push($limits, "The class occures outside of client membership day allocation");
                            }
                            break;
                        case "Time":
                            $classStartTime = date("H:i", strtotime($classStudioDate->__get("start_date")));
                            $classEndTime = date("H:i", strtotime($classStudioDate->__get("end_date")));
                            $membershipTime = json_decode($role->Value)["data"][0];
                            if (strtotime($classStartTime) < strtotime($membershipTime["FromTime"]) || strtotime($classEndTime) > strtotime($membershipTime["ToTime"])) {
                                array_push($limits, "The class occures outside of client membership time allocation");
                            }
                    }
                }

                echo json_encode(["Message" => $limits, "Status" => "Success"]);
            }
            break;
        case "assignWaitingToClass":
            if (!isset($_POST["actIdArr"])) {
                echo json_encode(["Message" => "actIdArr required", "Status" => "Error"]);
            } elseif (!isset($_POST["classId"])) {
                echo json_encode(["Message" => "classId required", "Status" => "Error"]);
            } elseif (!isset($_POST["overridePopup"])) {
                echo json_encode(["Message" => "overridePoup required", "Status" => "Error"]);
            } else {

                $actCount = count($_POST['actIdArr']);
                $classDateObj = new ClassStudioDate($_POST['classId']);
                $overClients = ($classDateObj->__get('ClientRegister') + $actCount) - $classDateObj->__get('MaxClient');
                if ($overClients > 0 && !$_POST["overridePopup"]) {
                    echo json_encode(["Message" => "Too many client registered", "data" => ["overClients" => $overClients], "Status" => "over"]);
                    break;
                }

                $status = 15;
                $updatedClients = [];

                foreach ($_POST["actIdArr"] as $actId) {
                    $classActObj = new ClassStudioAct($actId);
                    if ($classActObj->__get("CompanyNum") != $CompanyNum) {
                        echo json_encode(["Message" => "Unauthorized", "Status" => "Error"]);
                        break;
                    }
                    if (!in_array($classActObj->__get("Status"), [9,17])) {
                        echo json_encode(["Message" => "Client is not in waiting list"]);
                        break;
                    }
                    
                    ClientActivities::CancelClassReturnBalance(ClassStudioAct::getClassActById($actId), $CompanyNum, $status);
                    $classActObj->changeStatus($status);

                    $clientObj = new Client($classActObj->__get('TrueClientId') ?: $classActObj->__get('ClientId'));
                    $updatedClients[] = getClientForConclusion($clientObj);

                    ////// send notification if assigned from waiting list
                    $todayDate = date('Y-m-d');
                    $startDate = date('Y-m-d', strtotime($classDateObj->StartDate));

                    if (date('Y-m-d H:i:s') <= date('Y-m-d H:i:s', strtotime($classDateObj->start_date))) {
                        $now = strtotime('now');
                        $classDateTs = strtotime($classDateObj->start_date);
                        $dateDiff = $classDateTs - $now;
                        $dateDiff = round(abs($dateDiff / (60 * 60 * 24)));

                        if ($dateDiff == 0) {
                            $ClassDate_Not = 'היום' . ' (' . date('d/m', strtotime($startDate)). ')';
                        } elseif ($dateDiff == 1) {
                            $ClassDate_Not = 'מחר' . ' (' . date('d/m', strtotime($startDate)) . ')';
                        } else {
                            $ClassDate_Not = 'בתאריך ' . date('d/m', strtotime($startDate));
                        }

                        $Subject = 'שובצת מרשימת המתנה על ידי הסטודיו';

                        $classTime = date('H:i', strtotime($classDateObj->start_date));

                        $Content = '<p>היי '.$clientObj->FirstName.',</p><p>שובצת בהצלחה לשיעור '.$classDateObj->ClassName.' '.$ClassDate_Not.' בשעה '.$classTime.' מרשימת המתנה.</p>
<br>
<p><b>במידה ואינך מתכוון/ת להגיע אנא דאג/י לבטל באפליקציה בהקדם האפשרי.</b></p>';

                        $notificationObj = new AppNotification([
                            'CompanyNum' => $CompanyNum,
                            'ClientId' => $clientObj->id,
                            'Subject' => $Subject,
                            'Text' => $Content,
                            'Dates' => date('Y-m-d H:i:s'),
                            'Type' => 0,
                            'Date' => date('Y-m-d'),
                            'Time' => date('H:i:s')
                        ]);
                        $notificationObj->save();

                    }
                }
                $ClientRegister = $classDateObj->updateClientRegisterCount();

                echo json_encode(["Message" => $ClientRegister, "data" => $updatedClients, "logData" => getLogData($status), "Status" => "Success"]);
            }
            break;
        case "moveToWaitingList":
            if (!isset($_POST["actId"])) {
                echo json_encode(["Message" => "actIdArr required", "Status" => "Error"]);
            } else {
                $classActObj = new ClassStudioAct($_POST['actId']);
                $classActObj->updateActiveToWaiting();


                $clientObj = new Client($classActObj->__get('TrueClientId') ?: $classActObj->__get('ClientId'));
                $updatedClient = getClientForConclusion($clientObj);

                $classDateObj = new ClassStudioDate($classActObj->__get('ClassId'));
                $ClientRegister = $classDateObj->updateClientRegisterCount();

                $classInfo = $classDateObj->getEmbeddedTrainersData();
                $GuideInfo = $classInfo['guide'];
                ob_start();
                require_once '../partials-views/char-popup/tabs-n-tables.php';
                $js_char_popup_updated_tables = ob_get_contents();
                ob_end_clean();
                echo json_encode(["Message" => "", "html" => $js_char_popup_updated_tables, "data" => "", "logData" => "", "Status" => "Success"]);
            }
            break;
        case "orderWaitingList":
            if (!isset($_POST["orderArr"])) {
                echo json_encode(["Message" => "orderArr required", "Status" => "Error"]);
            } elseif (!is_array($_POST["orderArr"])) {
                echo json_encode(["Message" => "orderArr must be an array", "Status" => "Error"]);
            } else {
                $res = $ClassStudioAct->reorderClassAct($_POST);
                if ($res) {
                    echo json_encode(["Message" => lang('action_done_beepos'), "Status" => "Success"]);
                } else {
                    echo json_encode(["Message" => lang('action_not_done'), "Status" => "Error"]);
                }
            }
            break;
        case "getSummaryReport":
            if (!isset($_POST["ClassId"])) {
                echo json_encode(["Message" => "ClassId required", "Status" => "Error"]);
            } else {
                $class = new ClassCalendar($_POST["ClassId"]);
                if ($class->__get("CompanyNum") != $CompanyNum) {
                    echo json_encode(["Message" => "Unauthorized", "Status" => "Error"]);
                    break;
                }
                $res = $ClassStudioAct->getSummary($_POST["ClassId"]);
                echo json_encode(["Message" => $res, "Status" => "Success"]);
            }
            break;
        case "getClassLogs":
            if (!isset($_POST["ClassId"])) {
                echo json_encode(["Message" => "ClassId required", "Status" => "Error"]);
            } else {
                $class = new ClassCalendar($_POST["ClassId"]);
                if ($class->__get("CompanyNum") != $CompanyNum) {
                    echo json_encode(["Message" => "Unauthorized", "Status" => "Error"]);
                    break;
                }
                $res = ClassLog::getLogByClassId($_POST["ClassId"]);
                echo json_encode(["Message" => $res, "Status" => "Success"]);
            }
            break;
        case "changeClassStatus":
            if (!isset($_POST["classId"])) {
                echo json_encode(["Message" => "classId required", "Status" => "Error"]);
            } elseif (!isset($_POST["status"])) {
                echo json_encode(["Message" => "Status required", "Status" => "Error"]);
            } elseif ($_POST["status"] != 0 && $_POST["status"] != 1 && $_POST["status"] != 2) {
                echo json_encode(["Message" => "Status can only be 0, 1 or 2"]);
            } else {
                $class = new ClassCalendar($_POST["classId"]);
                if ($class->__get("CompanyNum") != $CompanyNum) {
                    echo json_encode(["Message" => "Unauthorized", "Status" => "Error"]);
                    break;
                }

                $studioDateObj = new ClassStudioDate($_POST["classId"]);
                $oldStatus = $class->__get("Status");
                $newStatus = $_POST["status"];
                $res = '';

                if ($oldStatus != $newStatus) {
                    if ($oldStatus == 2) {
                        $traineesCanceledByStudio = $ClassStudioAct->getActsByClassIdAndStatus($_POST["classId"], [5]);
                        if (!isset($_POST["returnTrainees"])) {
                            $traineesCount = count($traineesCanceledByStudio);
                            if ($traineesCount > 0) {
                                echo json_encode(["Message" => "returnTrainees required", "data" => $traineesCount, "Status" => "Missing"]);
                                break;
                            } else
                                $_POST["returnTrainees"] = 0;
                        }
                        $res = $ClassStudioAct->changeCanceledToActive($traineesCanceledByStudio, $_POST["returnTrainees"]);
                        $studioDateObj->changeStatus($newStatus);
                        $studioDateObj->updateClientRegisterCount();
                    }
                    elseif ($newStatus == 1 && $oldStatus == 0) {
                        $res = $ClassStudioAct->completeClass($_POST["classId"]);
                        $studioDateObj->changeStatus(1);

                    } elseif ($newStatus == 0 && $oldStatus == 1) {
                        $res = $studioDateObj->changeStatus($newStatus);
                    }
                }
                echo json_encode([
                    "Message" => $res,
                    "Status" => "Success"]);
            }
            break;

        case "cancelClassOneTime":

            if (!isset($_POST["classId"])) {
                echo json_encode(["Message" => "classId required", "Status" => "Error"]);
                if (!isset($_POST["displayCancel"]))
                    echo json_encode(["Message" => "displayCancel required", "Status" => "Error"]);
            } else {
                $class = new ClassCalendar($_POST["classId"]);
                if ($class->__get("CompanyNum") != $CompanyNum) {
                    echo json_encode(["Message" => "Unauthorized", "Status" => "Error"]);
                    break;
                }
                $studioDateObj = new ClassStudioDate($_POST["classId"]);
                $res = ClassCalendar::cancelClass([$studioDateObj], $_POST["displayCancel"]);

                echo json_encode(["Message" => $res, "Status" => "Success"]);
            }
            break;

        case "cancelAllClasses":
            if (!isset($_POST["startDate"])) {
                echo json_encode(["Message" => "startDate is required", "Status" => "Error"]);
            } if (!isset($_POST["groupNumber"])) {
                echo json_encode(["Message" => "groupNumber is required", "Status" => "Error"]);
            } else {
                $ClassStudioDateRegular->deleteRegularAssignments($_POST["groupNumber"]);
                $studioDateObjArr = $ClassCalendar->getClassesByGroupNumber($_POST["groupNumber"], $_POST["startDate"], $CompanyNum);
                $affect = ClassCalendar::cancelClass($studioDateObjArr);

                ClassesCanceledSeries::insertCanceledSeries($CompanyNum, $_POST['groupNumber']);

                echo json_encode(["Message" => $affect, "Status" => "Success"]);
            }
            break;

        case "cancelClassesByDates":
            if (!isset($_POST["groupNumber"])) {
                echo json_encode(["Message" => "groupNumber is required", "Status" => "Error"]);
            } elseif (!isset($_POST["startDate"])) {
                echo json_encode(["Message" => "startDate is required", "Status" => "Error"]);
            } elseif (!DateTime::createFromFormat('Y-m-d', $_POST["startDate"])) {
                echo json_encode(["Message", "startDate is not valid"]);
            } elseif (!isset($_POST["endDate"])) {
                echo json_encode(["Message" => "endDate is required", "Status" => "Error"]);
            } elseif (!DateTime::createFromFormat('Y-m-d', $_POST["endDate"])) {
                echo json_encode(["Message", "endDate is not valid"]);
            } else {
                $studioDateObjArr = $ClassCalendar->getGroupClassesInRange(
                        $_POST["groupNumber"], $_POST["startDate"], $_POST["endDate"]
                );
                $affect = ClassCalendar::cancelClass($studioDateObjArr);
                $ClassCalendar->deleteRegularAssignmentsIfAllCanceled($_POST["startDate"], ($_POST["groupNumber"]));

                if (ClassStudioDate::isClassSeriesEnded($CompanyNum, $_POST["groupNumber"], $_POST['startDate'])) {
                    ClassesCanceledSeries::insertCanceledSeries($CompanyNum, $_POST['groupNumber']);
                }

                echo json_encode(["Message" => $affect, "Status" => "Success"]);
            }
            break;

        case "cancelClassesByQuantity":
            if (!isset($_POST["startDate"])) {
                echo json_encode(["Message" => "startDate is required", "Status" => "Error"]);
            } elseif (!isset($_POST["groupNumber"])) {
                echo json_encode(["Message" => "GroupNumber is required", "Status" => "Error"]);
            } elseif (!isset($_POST["quantity"])) {
                echo json_encode(["Message" => "quantity is required", "Status" => "Error"]);
            } elseif (!is_numeric($_POST["quantity"])) {
                echo json_encode(["Message" => "quantity must be numeric", "Status" => "Error"]);
            } else {
                $studioDateObjArr = $ClassCalendar->getClassesByGroupNumber(
                        $_POST["groupNumber"], $_POST["startDate"], $CompanyNum, $_POST["quantity"]
                );
                $affect = ClassCalendar::cancelClass($studioDateObjArr);
                $ClassCalendar->deleteRegularAssignmentsIfAllCanceled($_POST["startDate"], ($_POST["groupNumber"]));

                if (ClassStudioDate::isClassSeriesEnded($CompanyNum, $_POST["groupNumber"], $_POST['startDate'])) {
                    ClassesCanceledSeries::insertCanceledSeries($CompanyNum, $_POST['groupNumber']);
                }

                echo json_encode(["Message" => $affect, "Status" => "Success"]);
            }
            break;

        case "GetTraineesData":
            if (empty($_POST['ClassId'])) {
                echo json_encode(array("Message" => "ClassId requeired", "Status" => "Error"));
                break;
            } else {
                $Trainees = [];

                $ClientsList = [];
                $ClassData = $ClassCalendar->GetClassById($CompanyNum, $_POST['ClassId']);
                $ClientsAct = $ClassStudioAct->getClientsFromActs($_POST['ClassId']);

                foreach ($ClientsAct as $act) {
                    $Client = $StdClient->getRow($act->ClientId);
                    $Trainree['Client'] = $Client;
                    $Trainree['ClientActive'] = $ClientActivities->GetActiveMembershisByClient($CompanyNum, $act->ClientId);
                    $Trainree['ClassStatus'] = ClassStatus::GetStatusById($act->Status);
                    if ($ClassData->ClassDevice != 0) {
                        $Trainree['Device'] = $Numbers->GetNumbersById($CompanyNum, $ClassData->ClassDevice);
                    }
                    $Trainree['medical'] = $clientmedical->GetMdicalByClientId($CompanyNum, $Client->id);
                    $Trainree['crm'] = $Clientcrm->GetClientcrmByClientId($CompanyNum, $Client->id);
                    array_push($ClientsList, $Trainree);
                }
                $Trainees['ClientsList'] = $ClientsList;
                $Trainees['ClassData'] = $ClassData;
                echo json_encode(array('Message' => $Trainees, "Status" => "Success"));
            }
            break;


        case "ChangeArrivalStatus":
            if (empty($_POST['actId'])) {
                echo json_encode(array("Message" => "actId requeired", "Status" => "Error"));
                break;
            }
            if (empty($_POST['status'])) {
                echo json_encode(array("Message" => "status requeired", "Status" => "Error"));
                break;
            } else {
                $status = $_POST['status'];
                $updatedClients = [];
                foreach ($_POST['actId'] as $client) {
                    $studioActObj = new ClassStudioAct($client);
                    if ($studioActObj->__get('CompanyNum') != $CompanyNum) {
                        echo json_encode(["Message" => "Unauthorized", "Status" => "Error"]);
                        break;
                    }
                    if(in_array($studioActObj->__get('Status'), [7,16,23])) {
                        $updateStatus = getWithoutChargeRelevantStatus($studioActObj->__get('Status'), $status);
                    } else {
                        $updateStatus = $status;
                    }

                    if ($studioActObj->__get('Status') != $updateStatus) {
                        ClientActivities::CancelClassReturnBalance(ClassStudioAct::getClassActById($studioActObj->__get('id')), $CompanyNum, $updateStatus);
                        $studioActObj->changeStatus($updateStatus);
                        $clientObj = new Client($studioActObj->__get('ClientId'));
                        $updatedClients[] = getClientForConclusion($clientObj);
                    }
                }
            }
            echo json_encode(array("Message" => "Updated Success", "data" => $updatedClients, 'logData' => getLogData($status), "Status" => "Success"));
            break;
        case "CancelStatusSingleClass":
            if (empty($_POST['ClassId'])) {
                echo json_encode(array("Message" => "ClassId requeired", "Status" => "Error"));
                break;
            }
            if (empty($_POST['ClientId'])) {
                echo json_encode(array("Message" => "ClientId requeired", "Status" => "Error"));
                break;
            } else {
                $ClientAct = $ClassStudioAct->getClientsFromActs($_POST['ClientId']);
                $ClassStudioAct->changeStatus(3);
                echo json_encode(array('Message' => "Updated Success", "Status" => "Success"));
            }
            break;
        case "CancelStatusAllClasses":

            if (empty($_POST['ClientId'])) {
                echo json_encode(array("Message" => "ClientId requeired", "Status" => "Error"));
                break;
            }
            if (empty($_POST['GroupNumber'])) {
                echo json_encode(array("Message" => "GroupNumber requeired", "Status" => "Error"));
            } else {
                $ClientAct = $ClassStudioAct->getClientsActsByClientId($_POST['ClientId']);

                $actList = ClassStudioAct::where('CompanyNum', '=', Company::getInstance()->CompanyNum)
                    ->where('ClientId', '=', $_POST['ClientId'])
                    ->where('GroupNumber', '=', $_POST['GroupNumber'])
                    ->get();

                /** @var ClassStudioAct $act */
                foreach ($actList as $act) {
                    $act->changeStatus(3);
                }

                if ($ClientAct->RegularClass == 1) {
                    $deleteRes = $ClassStudioDateRegular->deleteRegularTraineeByGroupNumberClientID($ClientAct->GroupNumber, $_POST['ClientId']);
                    echo json_encode(array('Message' => "Updated Success", "Status" => "Success"));
                } else {
                    echo json_encode(array("Message" => "Client not regular", "Status" => "Error"));
                }
            }
            break;
        case "CancelStatusAllClassesByDate":

            if (empty($_POST['ClientId'])) {
                echo json_encode(array("Message" => "ClientId requeired", "Status" => "Error"));
                break;
            } elseif (empty($_POST['GroupNumber'])) {
                echo json_encode(array("Message" => "GroupNumber requeired", "Status" => "Error"));
                break;
            } elseif (empty($_POST['StartDate'])) {
                echo json_encode(array("Message" => "StartDate requeired", "Status" => "Error"));
                break;
            } elseif (empty($_POST['EndDate'])) {
                echo json_encode(array("Message" => "EndDate requeired", "Status" => "Error"));
                break;
            } else {
                $actList = ClassStudioAct::where('CompanyNum', '=', $CompanyNum)
                    ->where('ClientId', '=', $_POST['ClientId'])
                    ->where('GroupNumber', '=', $_POST['GroupNumber'])
                    ->where('ClassDate', '>=', $_POST['StartDate'])
                    ->where('ClassDate', '<=', $_POST['EndDate'])
                    ->get();

                /** @var ClassStudioAct $act */
                foreach ($actList as $act) {
                    $act->changeStatus(3);
                }
            }
            break;
        case "MarkClassWithoutCharge":
            if (empty($_POST['actId'])) {
                echo json_encode(array("Message" => "actId requeired", "Status" => "Error"));
                break;
            } else {
                $clientData = [];
                $studioActObj = new ClassStudioAct($_POST['actId']);
                if($studioActObj->__get('Status') == 2) {
                    $status = 23;   // attended to class without charge
                } elseif($studioActObj->__get('Status') == 8) {
                    $status = 7;    // not attended, without chrge
                } else {
                    $status = 16;   // active, without charge
                }
                if ($studioActObj->__get('Status') != $status) {
                    ClientActivities::CancelClassReturnBalance(ClassStudioAct::getClassActById($_POST['actId']), $CompanyNum, $status);
                    $studioActObj->changeStatus($status);
                    $clientObj = new Client($studioActObj->__get('ClientId'));
                    $clientData = getClientForConclusion($clientObj);
                }

                echo json_encode(array('Message' => "Updated Success", "data" => $clientData, "logData" => getLogData($status), "Status" => "Success"));
            }
            break;
        case "MarkClassWithCharge":
            if (empty($_POST['actId'])) {
                echo json_encode(array("Message" => "actId required", "Status" => "Error"));
                break;
            } else {
                $clientData = [];
                $studioActObj = new ClassStudioAct($_POST['actId']);
                if($studioActObj->__get('Status') == 23) {
                    $status = 2;   // attended to class without charge
                } elseif($studioActObj->__get('Status') == 7) {
                    $status = 8;    // not attended, without chrge
                } else {
                    $status = 1;   // active, without charge
                }

                if ($studioActObj->__get('Status') != $status) {
                    ClientActivities::CancelClassReturnBalance(ClassStudioAct::getClassActById($_POST['actId']), $CompanyNum, $status);
                    $studioActObj->changeStatus($status);
                    $clientObj = new Client($studioActObj->__get('ClientId'));
                    $clientData = getClientForConclusion($clientObj);
                }
                echo json_encode(array('Message' => "Updated Success", "data" => $clientData, "logData" => getLogData($status), "Status" => "Success"));
            }
            break;
        case "EditRemarks":
            if (empty($_POST['ClientId'])) {
                echo json_encode(array("Message" => "ClientId required", "Status" => "Error"));
            } elseif (empty($_POST['ActId'])) {
                echo json_encode(array("Message" => "ActId required", "Status" => "Error"));
            } elseif (!isset($_POST['ShowRemarks'])) {
                echo json_encode(array("Message" => "ShowRemarks required", "Status" => "Error"));
            } elseif (empty($_POST['Remarks'])) {
                echo json_encode(array("Message" => "Remarks required", "Status" => "Error"));
            } else {
                /** @var ClassStudioAct $classAct */
                $classAct = ClassStudioAct::find($_POST['ActId']);
                if ($classAct->ClientId == $_POST['ClientId']) {
                    $classAct->update([
                        'Remarks' => $_POST['Remarks'],
                        'ShowRemarks' => $_POST['ShowRemarks'],
                    ]);
                }

                echo json_encode(array('Message' => "Updated Success", "Status" => "Success"));
            }
            break;
        case "EditClassRemarks":
            if (empty($_POST['ClassId'])){
                echo json_encode(array("Message" => "ClassId is required", "Status" => "Error"));
                break;
            } elseif (empty($_POST['Remarks'])){
                $Remarks = '';
            } elseif (!isset($_POST['RemarksStatus'])){
                echo json_encode(array("Message" => "RemarksStatus is required", "Status" => "Error"));
                break;
            } else {
                $StudioDateObj = new ClassStudioDate($_POST['ClassId']);
                $dataArr = [
                  'Remarks' => $_POST['Remarks'],
                  'RemarksStatus' => $_POST['RemarksStatus'],
                ];


                if (isset($_POST['SaveOptions'])){
                    $SavingOptions = json_decode($_POST['SaveOptions']);
                    foreach ($SavingOptions as $option){
                        if ($option == 'series') {
                            $ClassCalendar::updateClassesByGroupAndDate(
                                $StudioDateObj->__get('GroupNumber'),
                                $StudioDateObj->__get('StartDate'),
                                $dataArr
                            );
                        }
                        elseif ($option == 'day') //Update all current day classes
                        {
                            $ClassCalendar::updateClassesByStartDate(
                                $StudioDateObj->__get('StartDate'),
                                $StudioDateObj->__get('CompanyNum'),
                                $dataArr
                            );
                        }
                    }
                } else {
                    $ClassCalendar::updateClass($dataArr, $StudioDateObj->__get('id'));
                }
                echo json_encode(array('Message' => "Updated Success", "Status" => "Success"));
            }
            break;
        case "GetMedicalAndMarksByClient":
            if (empty($_POST['ClientId'])) {
                echo json_encode(array("Message" => "ClientId required", "Status" => "Error"));
            } elseif (empty($_POST['ClassId'])) {
                echo json_encode(array("Message" => "ClassId required", "Status" => "Error"));
            } else {
                $TraineeData = [];
                $medical = $clientmedical->GetMdicalByClientId($CompanyNum, $_POST['ClientId']);
                $ClientAct = $ClassStudioAct->getClientsActsByClientIdAndClassId($_POST['ClientId'], $_POST['ClassId']);
                $TraineeData ["Medical"] = $medical;
                $TraineeData['Remarks'] = $ClientAct->Remarks;
                echo json_encode(array('Message' => $TraineeData, "Status" => "Success"));
            }

            break;
        case "GetNumbersAndNumbersSub":
            if (empty($_POST['ClassId'])) {
                echo json_encode(array("Message" => "ClassId requeired", "Status" => "Error"));
            } else {
                $Class = $ClassCalendar->GetClassById($CompanyNum, $_POST['ClassId']);
                $NumbersList = $Numbers->GetNumbersById($CompanyNum, $Class->ClassDevice);
                $NumSub = $NumbersSub->GetNumbersSubByCompanyNum($CompanyNum, $NumbersList->id);
                $NumbersList->NumberSub = $NumSub;
                echo json_encode(array('Message' => $NumbersList, "Status" => "Success"));
            }

            break;
        case "SearchClients":
            if (empty($_POST['SearchStr'])) {
                echo json_encode(array("Message" => "SearchStr requeired", "Status" => "Error"));
            } else {
                $ClientsList = $StdClient->SearchClients($_POST['SearchStr'], $CompanyNum);
                echo json_encode(array('Message' => $ClientsList, "Status" => "Success"));
            }
            break;

        case "GetItems":
            $ItemsList = $Item->GetItemsByCompany($CompanyNum);
            echo json_encode(array('Message' => $ItemsList, "Status" => "Success"));
            break;
        case 'GetMembershipOfClient':
            if (empty($_POST['ClientId'])) {
                echo json_encode(array("Message" => "ClientId required", "Status" => "Error"));
            } else {
                $MembershipList = $ClientActivities->GetActiveMembershisByClient($CompanyNum, $_POST['ClientId']);
                echo json_encode(array('Message' => $MembershipList, "Status" => "Success"));
            }
            break;

        case "HandleClassAssignment": {
            if (empty($_POST['data'])) {
                echo json_encode(array("Message" => "data is required", "Status" => "Error"));
                break;
            }

            $data = json_decode($_POST['data']);
            $data->chargeOption = $data->chargeOptionExist ?? $data->chargeOptionNew;
            validateAssignClientForm($data);

            $studioDateObj = ClassStudioDate::find($data->classId);

            if ($data->isNew == 'true') {
                //add new client and client id in date to new id
                if (!isset($data->overrideStatus)) {
                    $newClient = ClientService::addClientByPhoneAndName($data->jsUserPhone, $data->clientName, $studioDateObj->Brands);

                    if ($newClient['Status'] == "Error") {
                        echo json_encode($newClient);
                        exit;
                    }
                    $data->clientId = $newClient['Message']['client_id'];
                } else { //Client already created on first iteration
                    $tempClient = Client::getClientByMobilePhone($data->jsUserPhone);
                    $data->clientId = $tempClient->id;
                }
            }
            $clientObj = new Client($data->clientId);

                //preliminary over limit check if no overrideStatus - before adding membership!
                if (in_array($data->chargeOption, ['without-charge', 'single-payment', 'new-membership'])) {
                    if (!isset($data->overrideStatus) && $studioDateObj->isClassFull()) {
                        echo json_encode(["Status" => 'overLimit', "Message" => "Class count is over limit"]);
                        exit;
                    }
                }

                switch ($data->chargeOption) {
                    case 'without-charge':
                        //Checks whether the user is lead or not and if necessary creating record in the database
                        $itemId = $clientObj->Status == 2 ? Item::getSingleClassItemLead($data->classTypeId, $clientObj->id):
                            Item::getSingleClassItem($data->classTypeId);
                        if ($itemId === "overLimitLeadSubscription") {
                            echo json_encode(array('Message' => lang('over_limit_lead_subscription'), 'Status' => 'Error'));
                            exit;
                        }
                        $assignMembership = $ClientActivities::assignMembership([
                            "clientId" => $data->clientId,
                            "itemId" => $itemId,
                            "itemPrice" => 0
                        ]);
                        $ClientActivity->updateActivityToSingleClass($assignMembership['ClientActivityId'], $data->classId);
                        break;
                    case 'choose-membership':
                        //need to check if class type is exist on membership, if not display error
                        exitIfNotExist($data, 'chooseMembership');
                        $clientActivityObj = new ClientActivities($data->chooseMembership);
                        if (!$clientActivityObj->isClassExistOnMembership($data->classTypeId)) {
                            echo json_encode(array('Message' => lang('cannot_order_class'), 'Status' => 'Error'));
                            exit;
                        }
                        if (!isset($data->override) && $data->assignType == '1') {
                            $isForWaitingList = isset($data->overrideStatus) && $data->overrideStatus == 12;
                            $checkLimit = $clientActivityObj->checkMembershipLimitations($data->classId, $data->clientId, false, $isForWaitingList);
                            if ($checkLimit['Status'] == 0) {
                                echo json_encode(array('Message' => $checkLimit['Message'], 'Status' => 'limitation'));
                                exit;
                            }
                            if ($checkLimit['Status'] == 2) { //Limitation that cannot override
                                echo json_encode(array('Message' => $checkLimit['Message'], 'Status' => "Error"));
                                exit;
                            }
                        }
                        break;
                    case 'single-payment':
                        //not need to check limitation or membership
                        exitIfNotExist($data, 'singlePaymentAmount');
                        $itemId = $clientObj->Status == 2 ? Item::getSingleClassItemLead($data->classTypeId, $clientObj->id): Item::getSingleClassItem($data->classTypeId);
                        if ($itemId === "overLimitLeadSubscription") {
                            echo json_encode(array('Message' => lang('over_limit_lead_subscription'), 'Status' => 'Error'));
                            exit;
                        }
                        $assignMembership = ClientActivities::assignMembership([
                            "clientId" => $data->clientId,
                            "itemId" => $itemId,
                            "itemPrice" => $data->singlePaymentAmount
                        ]);
                        $ClientActivity->updateActivityToSingleClass($assignMembership['ClientActivityId'], $data->classId);
                        break;
                    case 'new-membership':
                        //need to add membership and save id
                        exitIfNotExist($data, 'newMembershipAmount');
                        exitIfNotExist($data, 'newMembershipSelect');
                        $assignMembership = ClientActivities::assignMembership([
                            "clientId" => $data->clientId,
                            "itemId" => $data->newMembershipSelect,
                            "itemPrice" => $data->newMembershipAmount,
                            "startDate" => $studioDateObj->__get('StartDate')
                        ]);
                        break;
                    default:
                        echo json_encode(array('Message' => 'chargeOption is wrong', 'Status' => 'Error'));
                        exit;
                }

                if ($data->isNew == 'true'){ //if new client and trial membership change client to lead
                    $pipelineId = $clientObj->checkActivityChangeToLead($assignMembership['ClientActivityId']);
                }

                if (isset($assignMembership["Status"])) {
                    if ($assignMembership["Status"])
                        $data->chooseMembership = $assignMembership['ClientActivityId'];
                    else {
                        echo json_encode(array('Message' => $assignMembership['Error'], 'Status' => 'Error'));
                        exit;
                    }
                }

                if($clientObj->Status == 1) { //change archive active status
                    updateClientStatusToActive($clientObj,$CompanyNum);
                }

                switch ($data->assignType) {
                    case '1':
                        //single class, should check if client to passed the limitation and should show popup
                        $newAct = ClassStudioAct::new($studioDateObj ,$data);
                        if ($newAct['Status'] === 'overLimit') {
                            echo json_encode(["Status" => $newAct['Status'], "Message" => "client is over his limits"]);
                            exit;
                        }
                        $res = ['isPermanent' => 0,
                            'actInfo' => $newAct,
                            'Status' => $newAct['Status'],
                            'Message' => $newAct['Message'] ?? null];
                    break;
                case '2': {
                    //permanent assignment, not checking for limitation
                    switch ($data->multiAssign){
                        case 'never':
                            $res = $ClassStudioDateRegular->newRegularAssigment($data->clientId, $data->classId, $data->chooseMembership, null, $data->overrideStatus ?? null);
                            break;
                        case 'by-date':
                            exitIfNotExist($data, 'assignUntilDate');
                            $res = $ClassStudioDateRegular->newRegularAssigment($data->clientId, $data->classId, $data->chooseMembership, [null, $data->assignUntilDate], $data->overrideStatus ?? null);
                            break;
                        case 'by-count':
                            exitIfNotExist($data, 'assignByCount');
                            $res = $ClassStudioDateRegular->newRegularAssigment($data->clientId, $data->classId, $data->chooseMembership, $data->assignByCount, $data->overrideStatus ?? null);
                            break;
                        default:
                            echo json_encode(array('Message' => 'multiAssign is wrong', 'Status' => 'Error'));
                            exit;
                    }
                    break;
                }
                default:
                    echo json_encode(array('Message' => 'assignType is wrong', 'Status' => 'Error'));
                    exit;
            }
            if ($res["Status"] != "Success"){
                if ($data->chargeOption != "choose-membership") { //If new membership added and assignment failed, delete membership
                    $ClientActivity->deleteClientActivityById($data->chooseMembership);
                }
                if ($data->isNew == 'true' && $res["Status"] != "full") { //If new client added and assignment failed, delete client
                    $StdClient->deleteClientById($data->clientId);
                }

                echo json_encode(["Status" => $res["Status"], "Message" => $res["Message"]]);
                exit;
            }

            echo json_encode([
                "Status" => "Success",
                "data" => [
                    "logData" => getLogData($res["actInfo"]["newStatus"]),
                    "actId" => $res["actInfo"]["actId"],
                    "clientCount" => $res["actInfo"]["clientCount"],
                    "clientInfo" => getClientForConclusion($clientObj),
                    "isPermanent" => $res["isPermanent"],
                    "updatedClassIds" => $res["updatedClassIds"] ?? null
            ]]);
            break;
        }
        case "AddCrmNotice":
            if (!isset($_POST['ClientId'])) {
                echo json_encode(array("Message" => "ClientId requeired", "Status" => "Error"));
            } else if (!isset($_POST['Remarks'])) {
                echo json_encode(array("Message" => "Remarks requeired", "Status" => "Error"));
            } else {
                $clientId = $_POST['ClientId'];
                $userId = Auth::user()->id;
                $remarks = nl2br($_POST['Remarks']);
                $tillDate = $_POST['TillDate'];

                if ($tillDate == '') {
                    $tillDate = NULL;
                }

                $res = $Clientcrm->addClientCrm($clientId, $userId, $remarks, $tillDate);

                $crm = $res[0];
                ob_start();
                require '../partials-views/char-popup/modal-client-info-crm-medical.php';
                $js_new_crm_content = ob_get_contents();
                ob_end_clean();
                echo $res ? json_encode(["Message" => "Added succesfully", "html" => $js_new_crm_content, "crmData" => $res, "Status" => "Success"]) : json_encode(["Message" => "failed to add", "Status" => "Error"]);
            }
            break;
        case "editCrmNoticeRemark":
            if (!isset($_POST['ClientId'])) {
                echo json_encode(array("Message" => "ClientId requeired", "Status" => "Error"));
            } else if (!isset($_POST['Remarks'])) {
                echo json_encode(array("Message" => "Remarks requeired", "Status" => "Error"));
            } else if (!isset($_POST['CrmId'])) {
                echo json_encode(array("Message" => "crm id requeired ", "Status" => "Error"));
            } else {
                $crmId = $_POST['CrmId'];
                $clientId = $_POST['ClientId'];
                $remarks = nl2br($_POST['Remarks']);
                $res = $Clientcrm->editClientCrmRemark($remarks, $clientId, $crmId, $_POST['TillDate'] ?? null);

                $crm = $res;
                ob_start();
                require '../partials-views/char-popup/modal-client-info-crm-medical.php';
                $js_new_crm_content = ob_get_contents();
                ob_end_clean();
                echo $res ? json_encode(["Message" => "Edited successfully", "html" => $js_new_crm_content, "response" => $res, "Status" => "Success"]) : json_encode(["Message" => "failed to add", "response" => $res, "Status" => "Error"]);
            }
            break;
        case "editMedicalContent":
            if (!isset($_POST['ClientId'])) {
                echo json_encode(array("Message" => "ClientId requeired", "Status" => "Error"));
            } else if (!isset($_POST['Content'])) {
                echo json_encode(array("Message" => "Content requeired", "Status" => "Error"));
            } else if (!isset($_POST['MedicalId'])) {
                echo json_encode(array("Message" => "crm id requeired ", "Status" => "Error"));
            } else {
                $medicalId = $_POST['MedicalId'];
                $clientId = $_POST['ClientId'];
                $content = nl2br($_POST['Content']);
                $tillDate = !empty($_POST['TillDate']) ? $_POST['TillDate'] : null;
                $res = $clientmedical->editClientMedicalContent($content, $clientId, $medicalId, $tillDate);
                $medical = $res;
                ob_start();
                require '../partials-views/char-popup/modal-client-info-crm-medical.php';
                $js_new_crm_content = ob_get_contents();
                ob_end_clean();
                echo $res ? json_encode(["Message" => "Edited successfully", "html" => $js_new_crm_content, "response" => $res, "Status" => "Success"]) : json_encode(["Message" => "failed to add", "response" => $res, "Status" => "Error"]);
            }
            break;
        case "sendMessage":
            if (!isset($_POST["clientIds"])) {
                echo json_encode(["Message" => "clientIds required", "Status" => "Error"]);
            } elseif (!is_array($_POST["clientIds"])) {
                echo json_encode(["Message" => "clientIds must be an array", "Status" => "Error"]);
            } elseif (!isset($_POST["sendType"])) {
                echo json_encode(["Message" => "sendType required", "Status" => "Error"]);
            } elseif ($_POST["sendType"] != 0 && $_POST["sendType"] != 1 && $_POST["sendType"] != 2 && $_POST["sendType"] != 3) {
                echo json_encode(["Message" => "sendType can be either 0, 1 or 2", "Status" => "Error"]);
            } elseif ($_POST["sendType"] == 2 && !isset($_POST["subject"])) {
                echo json_encode(["Message" => "subject required", "Status" => "Error"]);
            } elseif (!isset($_POST["content"])) {
                echo json_encode(["Message" => "content required", "Status" => "Error"]);
            } else {
                $clients = $StdClient->getClientsByIds($_POST["clientIds"], $CompanyNum);
                $template = (object) ["Content" => $_POST["content"]];
                if ($_POST["sendType"] == 2) {
                    $template->Subject = $_POST["subject"];
                }
                $ids = [];
                foreach ($clients as $client) {
                    $content = str_replace("{{שם לקוח}}", $client->CompanyName, $template->Content);
                    $data = [
                        "CompanyNum" => $CompanyNum,
                        "ClientId" => $client->id,
                        "Text" => $content,
                        "Dates" => date('Y-m-d H:i:s'),
                        "Date" => date('Y-m-d'),
                        "Time" => date('H:i:s'),
                        "UserId" => Auth::user()->id,
                        "Status" => 0,
                        "Type" => $_POST["sendType"]
//                        "EmailAddress" => $client->Email,
//                        "PhoneNumber" => $client->ContactMobile
                    ];
                    if ($_POST["sendType"] == 2) {
                        $subject = str_replace("{{שם לקוח}}", $client->CompanyName, $template->Subject);
                        $data["Subject"] = $subject;
                    }
                    $id = AppNotification::insertGetId($data);
                    array_push($ids, $id);
                }
                echo json_encode(["Message" => $ids, "Status" => "Success"]);
            }
            break;

        case "EncryptClassId":
            if (empty($_POST['ClassId'])) {
                echo json_encode(array("Message" => "ClassId required", "Status" => "Error"));
                break;
            } else {
                $EncryptClassId = $EncryptDecrypt->encryption($_POST['ClassId']);
                echo json_encode(array("Message" => $EncryptClassId, "Status" => "Success"));
                break;
            }

            break;

        case 'removeClientFromClass':
            if (!isset($_POST['classId']))
                echo json_encode(array("Message" => "classId required", "Status" => "Error"));
            if (!isset($_POST['actId']))
                echo json_encode(array("Message" => "actId required", "Status" => "Error"));
            else {
                $updatedClients = [];
                $studioDateObj = new ClassStudioDate($_POST['classId']);
                $overrideLate = isset($_POST['override_late']) && $_POST['override_late'];

                //If there is single client, without overide status check for cancel law
                if (count($_POST['actId']) == 1 && !isset($_POST['status']) && !$studioDateObj->checkCancelLaw() && !$overrideLate){
                    echo json_encode(["Message" => "Late Cancelation", "Status" => "Late"]);
                    break;
                }

                $status = $_POST['status'] ?? 3;

                foreach ($_POST['actId'] as $client) {
                    /** @var ClassStudioAct $studioActObj */
                    $studioActObj = $ClassStudioAct::find($client);
                    if ($studioActObj->CompanyNum != $CompanyNum) {
                        echo json_encode(["Message" => "Unauthorized", "Status" => "Error"]);
                        break;
                    }
                    if ($studioActObj->Status != $status) {
                        if (isset($_POST['status'])) {
                            $studioActObj->setKnasOption($status);
                        }
                        $clientObj = Client::find($studioActObj->ClientId);

                        ClientActivities::CancelClassReturnBalance($studioActObj, $CompanyNum, $status);
                        $studioActObj->changeStatus($status);
                        $studioActObj->DeviceId = 0;
                        $studioActObj->save();

                        $ClientRegister = $studioDateObj->updateClientRegisterCount();
                        $updatedClients[] = getClientForConclusion($clientObj);
                    }
                }
                echo json_encode(["Message" => $ClientRegister, "data" => $updatedClients, "logData" => getLogData($status), "Status" => "Success"]);
            }
            break;

        case "setDeviceForAct":
            if (!isset($_POST['deviceId']))
                echo json_encode(["Message" => "id is required", "Status" => "Error"]);
            else {
                /** @var ClassStudioAct $classAct */
                $classAct = ClassStudioAct::find($_POST['actId']);
                $classAct->DeviceId = $_POST['deviceId'];
                $classAct->save();

                if ($_POST['deviceId'] != 0) {
                    $deviceObj = new NumbersSub($_POST['deviceId']);
                    $deviceName = $deviceObj->__get('Name');
                }
                echo json_encode(["Message" => isset($deviceName) ? $deviceName : 0, "Status" => "Success"]);
            }
            break;

        case "removeClientMedical":
            if (!isset($_POST['medicalId'], $_POST['clientId']))
                echo json_encode(["Message" => "client id or medical id is missing", "Status" => "Error"]);
            else {
                $res = $clientmedical->editMedicalStatus($_POST['clientId'], $_POST['medicalId'], 1);
                echo $res ? json_encode(["Message" => "removed successfully", "Status" => "Success"]) : json_encode(["Message" => "failed to edit", "Status" => "Error"]);
            }

            break;

        case "removeClientCrm":
            if (!isset($_POST['crmId'], $_POST['clientId']))
                echo json_encode(["Message" => "client id or crm id is missing", "Status" => "Error"]);
            else {
                $res = $Clientcrm->editClientCrm($_POST['crmId'], $_POST['clientId'], 1);
                echo $res ? json_encode(["Message" => "removed successfully", "Status" => "Success"]) : json_encode(["Message" => "failed to edit", "Status" => "Error"]);
            }

            break;

        case "removeRegularAssignment":
            if (!isset($_POST['client_id'], $_POST['regularClassId'], $_POST['class_id'])) {
                echo json_encode(["Message" => "missing info", "Status" => "Error"]);
                break;
            }

            $regularId = $_POST['regularClassId'];
            $clientId = $_POST['client_id'];
            $classId = $_POST['class_id'];
            $quantity = $_POST['quantity'] ?? null;


            $classInfo = ClassStudioDate::getClassById($classId, $CompanyNum);
            if (!$classInfo) {
                echo json_encode(["Message" => lang('class_not_found_corona'), "Status" => "Error"]);
                break;
            }

            $getStudioActs = $ClassStudioAct->getClientRegularActs($CompanyNum, $clientId, $regularId, $classInfo->StartDate, $_POST['endDate'] ?? null);

            foreach ($getStudioActs as $key => $act) {
                $deleteAct = $ClassStudioAct->deleteActById($act->id, $CompanyNum);
                (new ClassLog())->deleteLogByClientId($CompanyNum, $clientId, $act->ClassId);
                //// עדכון שיעור ברשימת משתתפים
                $update = ClassStudioDate::updateClassRegistersCount($act->ClassId, $act->GroupNumber, $act->FloorId, $act->ClassDate);

                if (isset($quantity) && $key + 1 == $quantity)
                    break;
            }

            if (isset($_POST['endDate']))
                $remainingActs = $ClassStudioAct->getClientRegularActs($CompanyNum, $clientId, $regularId, $classInfo->StartDate);

            //Check if all acts was remove depending on remove type
            if ($quantity == count($getStudioActs) || (isset($_POST['endDate']) && empty($remainingActs)) || (!isset($quantity) && !isset($_POST['endDate']))) {
                $regularClass = $ClassStudioDateRegular->GetRegularById($regularId, $CompanyNum, $clientId);
                if (!$regularClass) {
                    echo json_encode(["Message" => lang('regular_classes_not_found_class'), "Status" => "Error"]);
                    break;
                }
                $deletedRegular = $ClassStudioDateRegular->deleteRegularAssignmentById($regularId, $CompanyNum, $clientId);
                if (!$deletedRegular) {
                    echo json_encode(["Message" => lang('action_not_done'), "Status" => "Error"]);
                    break;
                }

                CreateLogMovement(
                    lang('log_removed_booking_ajax') . ' ' . $classInfo->ClassName . ' ' . lang('a_day_ajax') . ' ' . $classInfo->Day . ' ' . lang('a_hour_ajax') . ' ' . $classInfo->StartTime,
                    $clientId
                );
            } else {
                $content = lang('regular_assignment') . ': ' . $classInfo->ClassName . ' ' . lang('removed');
                if (isset($_POST['endDate'])) {
                    $content .= ' ' . lang('between_dates') . ' ' . $classInfo->StartDate . ' ' . $_POST['endDate'];
                } elseif (isset($quantity)) {
                    $content .= ', ' . $quantity . ' ' . lang('shows_desk_plan') . ' ' . lang('coupon_from') . $classInfo->StartDate;
                }
                CreateLogMovement(
                    $content,
                    $clientId
                );
            }


            /// return the current amount of trainers
            $currentClass = new ClassStudioDate($classId);
            $data = [];
            if ($currentClass) {
                $data = [
                    "registered" => $currentClass->__get('ClientRegister'),
                    "waiting" => $currentClass->__get('WatingList'),
                    "regularRegistered" => $currentClass->__get('ClientRegisterRegular'),
                    "regularWaiting" => $currentClass->__get('ClientRegisterRegularWating'),
                    'isRegularRemoved' => $deletedRegular ?? 0,
                ];
            }


            echo json_encode(["Message" => lang('action_done'), "Status" => "Success", "data" => $data]);
            break;


        default:
            echo json_encode(array("Message" => "No Found Function", "Status" => "Error"));
            break;
    }
} else {
    echo json_encode(array("Message" => "No Function", "Status" => "Error"));
}
