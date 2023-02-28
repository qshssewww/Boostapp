<?php
require_once __DIR__ . '/../../app/init.php';
require_once __DIR__ . '/../../app/helpers/GroupNumberHelper.php';
require_once __DIR__ . '/../Classes/ClassesType.php';
require_once __DIR__ . '/../Classes/ZoomClasses.php';
require_once __DIR__ . '/../Classes/ClassCalendar.php';
require_once __DIR__ . '/../Classes/Brand.php';
require_once __DIR__ . '/../Classes/Section.php';
require_once __DIR__ . '/../Classes/Company.php';
require_once __DIR__ . '/../Classes/RepetitionSettings.php';
require_once __DIR__ . '/../Classes/CancelationSettings.php';
require_once __DIR__ . '/../Classes/ClassCalendarTrainees.php';

if (Auth::guest()) exit;
if (Auth::check()) {
    if (Auth::userCan('31')) {
        try {
            $company = Company::getInstance();
            $companyNum = $company->__get("CompanyNum");
            $data =(object) $_POST;
            $isEdit = ($data->isEdit && $data->isEdit != "");
            if (!isset($data->dates) || $isEdit) {
                $data->dates = [$data->date];
            }
            $index=1;
            $data->isNewClass = filter_var($data->isNewClass, FILTER_VALIDATE_BOOLEAN);
            $data->isNewLocation = filter_var($data->isNewLocation, FILTER_VALIDATE_BOOLEAN);
            foreach ($data->dates as $singleDate) {
                if ($isEdit) {
                    $GroupNumber = $data->GroupNumber;
                } else {
                    $GroupNumber = GroupNumberHelper::generate();
                }
                if (isset($data->minimumAtendeesCheckType)) {
                    $minimumAtendeesCheckType = CheckTime($data->minimumAtendeesCheckType);
                }
                if (isset($data->purchaseLocation)) {
                    $purchaseLocation = purchaseLocation($data->purchaseLocation);
                }
                if (isset($data->timingSettings)) {
                    foreach ($data->timingSettings as $setting) {
                        if ($setting['type'] == "open") {
                            $OpenOrder = 0;
                            $OpenOrderType = CheckTime($setting['aountUnits']);
                            $OpenOrderTime = $setting['amount'];
                        } else if (setting['type'] == "close") {
                            $CloseOrder = 0;
                            $CloseOrderType = CheckTime($setting['aountUnits']);
                            $CloseOrderTime = $setting['amount'];
                        }
                    }
                }

                $studioClass = [
                    "CompanyNum" => $companyNum,
                    "ShowApp" => ($data->justCalendar == 'false' && $data->calendarAndApp == 'true') ? 0 : 2,
                    "MaxClient" => $data->maxAtendees ?? "12",
                    "ClassWating" => (isset($data->allowWaitingList) && $data->allowWaitingList) ? 1 : 0,
                    "MaxWatingList" => (isset($data->waitingListAmount) && $data->waitingListAmount != null) ? $data->waitingListAmount : 0,
                    "WatingListOrederShow" => (isset($data->showAmount) && $data->showAmount) ? 0 : 1,
                    "ShowClientNum" => (isset($data->showWaitingListLocation) && $data->showWaitingListLocation) ? 0 : 1,
                    "ShowClientName" => (isset($data->showImage) && $data->showImage) ? 0 : 1,
                    "MinClass" => (isset($data->minimumAtendees) && $data->minimumAtendees) ? 1 : 0,
                    "MinClassNum" => (isset($data->minimumAtendeesAmount)) ? $data->minimumAtendeesAmount : 0,
                    "ClassTimeCheck" => isset($data->minimumAtendeesCheckAmount) ? $data->minimumAtendeesCheckAmount : 0,
                    "ClassTimeTypeCheck" => isset($minimumAtendeesCheckType) ? $minimumAtendeesCheckType : '1',
                    "purchaseOptions" => (isset($data->purchaseOptions) && $data->purchaseOptions) ? 1 : 0,
                    "purchaseAmount" => (isset($data->purchaseAmount)) ? $data->purchaseAmount : 0,
                    "purchaseLocation" => (isset($purchaseLocation)) ? $purchaseLocation : 0,
                    "image" => (isset($data->image)) ? $data->image : null,
                    "OpenOrder" => isset($OpenOrder) ? $OpenOrder : 1,
                    "OpenOrderType" => (isset($OpenOrderType)) ? $OpenOrderType : 0,
                    "OpenOrderTime" => (isset($OpenOrderTime)) ? $OpenOrderTime : 0,
                    "CloseOrder" => (isset($CloseOrder)) ? $CloseOrder : 1,
                    "CloseOrderType" => (isset($CloseOrderType)) ? $CloseOrderType : 0,
                    "CloseOrderTime" => (isset($CloseOrderTime)) ? $CloseOrderTime : 0,
                    "GuideId" => (isset($data->trainer1id)) ? $data->trainer1id : null,
                    "GuideName" => (isset($data->trainer1name)) ? $data->trainer1name : null,
                    "ExtraGuideId" => (isset($data->trainer2id)) ? $data->trainer2id : null,
                    "ExtraGuideName" => (isset($data->trainer2name)) ? $data->trainer2name : null,
                    "color" => (isset($data->color)) ? $data->color : null,
                    "StartTime" => (isset($data->timeFrom)) ? date("H:i:s", strtotime($data->timeFrom)) : null,
                    "EndTime" => (isset($data->timeTo)) ? date("H:i:s", strtotime($data->timeTo)) : null,
                    "FreeClass" => (isset($data->freeRegister) && $data->freeRegister == true) ? 1 : 0,
                    "content" => $data->content ?? '', "contentShow" => isset($data->contentShow) ? $data->contentShow : "0",
                    "start_date" => (isset($data->timeFrom) && isset($singleDate)) ? toDateTime($singleDate, $data->timeFrom) : '',
                    "end_date" => (isset($data->timeTo) && isset($singleDate)) ? toDateTime($singleDate, $data->timeTo) : '',
                    "DayNum" => isset($singleDate) ? getDay($singleDate) ['dayNum'] : '', "Day" => isset($singleDate) ? getDay($singleDate) ['dayName'] : '',
                    "EndDate" => isset($singleDate) ? getYearMonthDayDashed($singleDate) : "",
                    "StartDate" => isset($singleDate) ? getYearMonthDayDashed($singleDate) : "", "Floor" => "1",
                    "ClassMemberType" => "BA999", "SendReminder" => (isset($data->reminderBool) && $data->reminderBool == 'true') ? 0 : 1,
                    "UserId" => Auth::user()->id, "is_zoom_class" => isset($data->is_zoom_class) ? $data->is_zoom_class : 0,
                    "liveClassLink" => isset($data->broadcastLink) ? $data->broadcastLink : null, "ClassLevel" => '0',
                    'onlineSendType' => isset($data->broadcastReminderType) ? $data->broadcastReminderType : null,
                    'registerLimit' => "1", "GroupNumber" => $GroupNumber, "onlineReminderType" => isset($data->broadcastReminderType) ? $data->broadcastReminderType : "0",
                    "onlineReminderNum" => isset($data->broadcastNumber) ? $data->broadcastNumber : "0",
                    "ReminderUnits" => (isset($data->reminderBool) && $data->reminderBool == 'true') ? $data->reminderType : "0",
                    "ReminderNum" => (isset($data->reminderBool) && $data->reminderBool == 'true') ? $data->remiderTime : "0",
                    "ClassDevice" => isset($data->deviceSettings) && $data->deviceSettings ? $data->deviceSettings : null];
                if (isset($data->reminderBool) && $data->reminderBool) {
                    $studioClass['TypeReminder'] = "3";
                }
                if ($data->cancelation == "no") {
                    $studioClass['CancelLaw'] = "4";
                }
                if ($data->cancelation == "free") {
                    $studioClass['CancelLaw'] = "5";
                }

                $hasLimitType = false;
                $membershipTypesLimit;
                if (isset($data->registerLimitations)) {
                    foreach ($data->registerLimitations as $limitation) {
                        if ($limitation ['type']== "age") {
                            $studioClass['ageLimitNum1'] = $limitation['limitationNumber'];
                            $studioClass['ageLimitNum2'] = isset($limitation['limitationSecondaryNumber']) ??"0";
                            $studioClass['ageLimitType'] = $limitation['ageLimitationType'];
                        }
                        if ($limitation ['type'] == "degree") {
                            $studioClass['LimitLevel'] = implode(",", $limitation['ranks']);
                        }
                        if ($limitation ['type'] == "type") {
                            $studioClass['ClassLimitTypes'] = "1";
                            $hasLimitType = true;
                            $membershipTypesLimit = implode(',', $limitation['memberships']);
//                            $studioClass['MaxClientMemberShip'] = $studioClass['MaxClient'];

                        }
                        if ($limitation['type'] == "gender") {
                            $studioClass['GenderLimit'] = $limitation['gender'];
                        }
                    }
                }
                //Inserting new Class type and getting th id
                $classTypeId =  $data->class;

                if ($data->isNewClass && $index==1) {
                    $fullDateNow = date('Y-m-d H:i:s');
                    $dataToInsert = array('Type' => $data->class['name'], 'Color' => $data->color,
                        'ClassContent' => $data->class['content'], 'duration' => $data->class['duration'],
                        'durationType' => $data->class['durationType'], 'CompanyNum' => $companyNum,
                        'EditDate' =>  $fullDateNow, 'CreatedDate' =>  $fullDateNow);
                    $classTypeId = ClassesType::insertNewClassType($dataToInsert);
                    $studioClass["ClassName"] = $data->class['name'];
                    $studioClass["ClassNameType"] = $classTypeId;
                } else {
                    foreach ($company->getClassTypes() as $classType) {
                        if ($classType->__get("id") == $data->class) {
                            $studioClass["ClassName"] = $classType->__get("Type");
                            $studioClass["ClassNameType"] = $data->class;
                        }
                    }
                }
                $locationId = $data->isNewLocation ? $data->location['name'] : '';
                if ($data->isNewLocation && $index==1) {
                    if (!isset($data->location['brand'])) $data->location['brand']=0;
                    $dataToInsert = array('Title' => trim($data->location['name']), 'Brands' => $data->location['brand'], 'CompanyNum' => $companyNum);
                    $locationId = Section::insertNewSection($dataToInsert);
                }
                if ($locationId) {
                    $studioClass['Brands'] = $locationId;
                    $studioClass['Floor']=$locationId;
                }
                //repetition block
                if (!$isEdit) {
                    $isNotSystematic = $data->frequency != "once" && !filter_var($data->frequency['systematic'], FILTER_VALIDATE_BOOLEAN);
                    $isSystematic = $data->frequency != "once";
                    $frequency = $data->frequency;
                    if ($isNotSystematic) {
                        $dataToInsert = array("name" => $frequency['name'], "repeatNumber" => $frequency['frequencyNumber'], "repeatType" => $frequency['frequencyType'], "repeatDays" => implode(',', $frequency['selectedDays']), "endType" => $frequency['endType'], "endDate" => $frequency['endDate'], "endNumber" => $frequency['endRepeats'], "CompanyNum" => $companyNum, "Type" => $frequency['type']);
                        if($index==1){
                            $repetitionId = RepetitionSettings::insertNewRepitition($dataToInsert);
                        }
                        $studioClass["frequencyId"] = $repetitionId;
                    } else if ($isSystematic) {
                        $studioClass["frequencyId"] = $frequency['id'];
                    }
                }
                //cancelation block
                $isNotSystematic2 = !is_string($data->cancelation) && !$data->cancelation->systematic;
                $isSystematic2 = !is_string($data->cancelation);
                $cancelation = $data->cancelation;
                if ($isNotSystematic2) {
                    $dataToInsert2 = array("name" => $cancelation->name, "cancelationNumber" => $cancelation->lateNumber, "cancelationType" => $cancelation->lateUnits, "buttonBlockNumber" => $cancelation->disableNumber, "buttonBlockType" => $cancelation->disableUnits, "allowCancel" => $cancelation->allowLateCancel === true ? 1 : 0, "allowButtonBlock" => $cancelation->allowDisableButton === true ? 1 : 0, "CompanyNum" => $companyNum, "Type" => $cancelation->type,);
                    if($index==1){
                        $cancelationId = CancelationSettings::insertNewCancelation($dataToInsert2);
                    }
                    $studioClass["cancelationId"] = $cancelationId;
                } else if ($isSystematic2) {
                    $studioClass["cancelationId"] = $cancelation->id;
                }
                if ($isEdit) {
                    if($data->editType=="byDays"){
                        ClassCalendar::updateClassViaGroupAndDays($studioClass, $data->GroupNumber,$data->editDays);
                    }else if($data->editType=="group"){
                        ClassCalendar::updateClassViaGroup($studioClass, $data->GroupNumber);
                    }else{
                        ClassCalendar::updateClass($studioClass, $data->isEdit);
                    }
                    $newClassCalendarID= $data->isEdit;
                } else {
                    $newClassCalendarID = ClassCalendar::insertNewClass($studioClass);
                }
                echo $newClassCalendarID;
                if (isset($data->is_zoom_class) && $data->is_zoom_class == "1" && $index==1) {
                    $zoom_class = new ZoomClasses();
                    if ($isEdit) {
                        DB::table('boostapp.class_zoom')->where('class_id', "=", $newClassCalendarID)->delete();
                    }
                    $newZoomID = $zoom_class->insertNewZoomClass(array("class_id" => $newClassCalendarID, "CompanyNum" => $companyNum, "meeting_id" => isset($data->zoomMeetingId) ? $data->zoomMeetingId : "", "password" => isset($data->zoomPassword) ? $data->zoomPassword : null));
                }
                if ($hasLimitType) {
                    if ($isEdit) {
                        DB::table('boostapp.classstudio_date_roles')->where('ClassId', "=", $newClassCalendarID)->delete();
                    }
                    $dateRoles = DB::table('classstudio_date_roles')->insertGetId(array("ClassId" => $newClassCalendarID, "CompanyNum" => $companyNum, "MemberShipType" => $membershipTypesLimit,"value" =>$studioClass['MaxClient']));
                }
                /// need to know this?
                if($isEdit){
                    $trainees=DB::table('boostapp.classstudio_act')->where("ClassId","=", $newClassCalendarID)->where('CompanyNum', $companyNum)->whereIn('StatusCount', array(0, 1))->get();
                    if(isset($trainees)){
                        foreach($trainees as $trainee){
                            $classActivity= DB::table('boostapp.client_activities')->where("id","=", $trainee->ClientActivitiesId)->where('ClientId', $trainee->ClientId)->first();
                            $Item= DB::table('boostapp.items')->where("id","=", $classActivity->ItemId)->first();
                            if($Item->isPaymentForSingleClass==1){
                                $client=DB::table('boostapp.client')->where("id","=", $trainee->ClientId)->first();
                                $clientAmount=(float)$client->BalanceAmount;
                                $classAmount= (float)$classActivity->ItemPrice;
                                $newClientAmount= $clientAmount-$classAmount;
                                DB::table('boostapp.client')->where('id',"=",$trainee->ClientId)->update(array(
                                    'BalanceAmount'=>$newClientAmount
                                ));
                            }
                        }
                    }

                    ClassCalendar::cancelClassesForSingleClass($newClassCalendarID);
                }
                if (isset($data->trainees)) {
                    $traineesFunction = new ClassCalendarTrainees();
                    foreach ($data->trainees as $trainee) {
                        $traineesFunction->setNewCalendarTrainee(array("Trainee" => $trainee, "ClassId" => $newClassCalendarID));
                    }
                }
                $index++;
            }
        }
        catch(Exception $e) {
            echo $e;
        }
    }
}
//                                    ▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼   utils ahead ▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼▼
function toDateTime($dateString, $timeString) {
    $arr = explode('/', $dateString);
    $newDate = $arr[2] . '/' . $arr[1] . '/' . $arr[0];
    $s = $newDate . ' ' . $timeString . ':00';
    $date = date_create($s);
    if ($date) return date_format($date, 'Y-m-d H:i:s');
    else return '';
}
function getDay($dateString) {
    $s = $dateString . ' ' . '00:00:00';
    $timestamp = strtotime($s);
    $day = date('w', $timestamp);
    return array("dayNum" => $day, "dayName" => numberToHebDayString($day));
}
function getYearMonthDayDashed($givenDate) {
    $arr = explode('/', $givenDate);
    $newDate = $arr[2] . '/' . $arr[1] . '/' . $arr[0];
    $date = date_create($newDate);
    if ($date) return date_format($date, 'Y-m-d');
    else return '';
}
function numberToHebDayString($num) {
    switch ($num) {
        case '0': {
            return 'ראשון';
        }
        case '1': {
            return 'שני';
        }
        case '2': {
            return 'שלישי';
        }
        case '3': {
            return 'רביעי';
        }
        case '4': {
            return 'חמישי';
        }
        case '5': {
            return 'שישי';
        }
        case '6': {
            return 'שבת';
        }
    }
}
function CheckTime($checkType) {
    $type = 0;
    if ($checkType == "ימים") {
        $type = 3;
    } else if ($checkType == "שעות") {
        $type = 2;
    } else if ($checkType == "דקות") {
        $type = 1;
    }
    return $type;
}
function purchaseLocation($location) {
    $type = 0;
    if ($location == "app") {
        $type = 1;
    } else if ($location == "link") {
        $type = 2;
    } else if ($location == "everywhere") {
        $type = 3;
    }
    return $type;
}
