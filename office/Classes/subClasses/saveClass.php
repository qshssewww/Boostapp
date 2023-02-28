<?php

require_once __DIR__ . '/../ClassOnline.php';
require_once __DIR__ . '/../../../app/helpers/GroupNumberHelper.php';
require_once __DIR__ . '/../Users.php';
require_once __DIR__ . '/../Section.php';

if (Auth::guest() || !isset($data)) exit;

if (!isset($data['Floor'])) {
    $FloorId = Section::getFirstFloor(Auth::user()->CompanyNum);
    if ($FloorId) {
        $data['Floor'] = $FloorId;
    }
}

$validator = Validator::make($data,
    [
        'StartDate' => 'required',
        'StartTime' => 'required',
        'duration' => 'required|numeric|min:1',
        'Floor' => 'required|numeric',
        'ClassName' => 'required',
        'GuideId' => 'required|numeric',
        'MaxClient' => 'required|numeric|min:0',
        'color' => ['required', 'regex:/#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?\b/'],
        'ClassNameType' => 'required',
        'ClassRepeat' => 'required',
        'TimeReminder' => 'required_if:SendReminder,0',
        'TypeReminder' => 'required_if:SendReminder,0',
        'purchaseAmount' => 'required_if:purchaseOptions,membership-cost',
        'CancelPeriodAmount' => 'required_if:CancelLaw,3',
        'MinClassNum' => 'required_if:MinClass,1',
        'ClassTimeCheck' => 'required_if:MinClass,1',
        'ClassTimeTypeCheck' => 'required_if:MinClass,1',
        'NumMaxWatingList' => 'required_if:MaxWatingList,0',
        'CancelPeriodType' => 'required_if:CancelLaw,3',
        'liveClassLink' => 'required_if:LiveClass,online',
        'onlineSendTime' => 'required_with:liveClassLink',
        'OpenOrderTime' => 'required_if:OpenOrder,0',
        'OpenOrderType' => 'required_if:OpenOrder,0',
        'CloseOrderTime' => 'required_if:CloseOrder,0',
        'CloseOrderType' => 'required_if:CloseOrder,0',
        'ageLimitNum2' => 'required_if:ageLimitType, 3',
        //'tag' => 'required', //TODO: add after not beta anymore
    ]
);

if ($validator->fails() && !isset($data['CalendarId'])) {
    return ['message' => $validator->messages()->first(), 'status' => 0];
}

$insertData = [];
$Company = Company::getInstance();
$studioDateObj = new ClassStudioDate();
$class_Users = new Users();

$CalendarId = $data['CalendarId'] ?? null;

$insertData['StartTime'] = $StartTime = date('H:i:s', strtotime($data['StartTime']));
$insertData['EndTime'] = $EndTime = date('H:i:s', strtotime("+" . $data['duration'] . " minutes", strtotime($StartTime)));
$insertData['StartDate'] = $StartDate = $data['StartDate'];
$insertData['EndDate'] = $EndDate = date('Y-m-d', strtotime("+" . $data['duration'] . " minutes", strtotime("$StartDate $StartTime")));


$insertData['UserId'] = Auth::user()->__get('id');
$insertData['CompanyNum'] = $CompanyNum = $Company->__get('CompanyNum');
$insertData['Floor'] = $FloorId = $data['Floor'];
$insertData['ClassNameType'] = $ClassNameType = $data['ClassNameType'];
$insertData['ClassName'] = $ClassName = $data['ClassName'];
$insertData['DayNum'] = $DayNum = date('w', strtotime($StartDate));
$insertData['Day'] = $Day = Utils::numberToDay($DayNum);
$insertData['MaxClient'] = $MaxClient = (int)$data['MaxClient'];
$insertData['color'] = $color = $data['color'];
$insertData['Dates'] = date('Y-m-d H:i:s');
$insertData['ClassRepeat'] = $ClassRepeat = $data['ClassRepeat'] ?? 0;


$insertData['GuideId'] = $GuideId = $data['GuideId'];
$GuideObj = Users::find($GuideId);
$insertData['GuideName'] = $GuideObj->display_name ?? '';

if (strtotime($StartDate) > strtotime("+1 year"))
    return ['message' => lang('unvalid_update_date'), 'status' => 0];

if (isset($data['ExtraGuideId'])) {
    $insertData['ExtraGuideId'] = $data['ExtraGuideId'];
    $extraGuide = Users::find($insertData['ExtraGuideId']);
    $insertData['ExtraGuideName'] = $extraGuide->display_name ?? '';
} else {
    $insertData['ExtraGuideId'] = 0;
    $insertData['ExtraGuideName'] = null;
}

if (isset($data['freqType'])) {
    if ($data['freqType'] == '0') {
        $ClassType = 1; //Permanent
    } else {
        $ClassType = 2; //Several Times
        $ClassCount = $data['freqType'] !== 'date' ? $data['freqType'] : 30;
    }
} else {
    $ClassType = 3; //Single
}
$insertData['ClassType'] = $ClassType;

$ClassRepeatType = $data['ClassRepeatType'] ?? 2;

$insertData['ClassRepeatType'] = $ClassRepeatType;

if (isset($data['SendReminder'])) $insertData['SendReminder'] = $data['SendReminder'];
if (isset($data['TimeReminder'])) $insertData['TimeReminder'] = $data['TimeReminder'];
if (isset($data['TypeReminder'])) $insertData['TypeReminder'] = $data['TypeReminder'];
if (isset($data['GenderLimit'])) $insertData['GenderLimit'] = $data['GenderLimit'];
if (isset($data["pageImgPath"])) $insertData['image'] = $data["pageImgPath"];
if (isset($data['LimitLevel'])) $insertData['LimitLevel'] = implode(',', $data['LimitLevel']);
if (isset($data['ShowClientNum'])) $insertData['ShowClientNum'] = $data['ShowClientNum'];
if (isset($data['ShowClientName'])) $insertData['ShowClientName'] = $data['ShowClientName'];
if (isset($data['WatingListOrederShow'])) $insertData['WatingListOrederShow'] = $data['WatingListOrederShow'];
if (isset($data['ClassDevice'])) $insertData['ClassDevice'] = $data['ClassDevice'];
if (isset($data['MaxWatingList'])) $insertData['MaxWatingList'] = $data['MaxWatingList'];
if (isset($data['NumMaxWatingList'])) $insertData['NumMaxWatingList'] = $data['NumMaxWatingList'];
if (isset($data['MinClass'])) $insertData['MinClass'] = $data['MinClass'];
if (isset($data['MinClassNum'])) $insertData['MinClassNum'] = $data['MinClassNum'];
if (isset($data['ClassTimeCheck'])) $insertData['ClassTimeCheck'] = $data['ClassTimeCheck'];
if (isset($data['ClassTimeTypeCheck'])) $insertData['ClassTimeTypeCheck'] = $data['ClassTimeTypeCheck'];
if (isset($data['ClassWating'])) $insertData['ClassWating'] = $data['ClassWating'];
if (isset($data['ShowClientNum'])) $insertData['ShowClientNum'] = $data['ShowClientNum'];
if (isset($data['ShowClientName'])) $insertData['ShowClientName'] = $data['ShowClientName'];
if (isset($data['Remarks'])) $insertData['Remarks'] = $data['Remarks'];
if (isset($data['registerLimit'])) $insertData['registerLimit'] = $data['registerLimit'];
if (isset($data['OpenOrder'])) $insertData['OpenOrder'] = $data['OpenOrder'];
if (isset($data['OpenOrderTime'])) $insertData['OpenOrderTime'] = $data['OpenOrderTime'];
if (isset($data['OpenOrderType'])) $insertData['OpenOrderType'] = $data['OpenOrderType'];
if (isset($data['CloseOrder'])) $insertData['CloseOrder'] = $data['CloseOrder'];
if (isset($data['CloseOrderTime'])) $insertData['CloseOrderTime'] = $data['CloseOrderTime'];
if (isset($data['CloseOrderType'])) $insertData['CloseOrderType'] = $data['CloseOrderType'];
if (isset($data['purchaseAmount'])) $insertData['purchaseAmount'] = $data['purchaseAmount'];
if (isset($data['ageLimitNum1'])) $insertData['ageLimitNum1'] = $data['ageLimitNum1'];
if (isset($data['ageLimitNum2'])) $insertData['ageLimitNum2'] = $data['ageLimitNum2'];

if (empty($data['pageImgPath'])) {
    $insertData['image'] = null;
} else {
    $insertData['image'] = $data['pageImgPath'];
}
if (empty($data['Remarks'])) {
    $insertData['Remarks'] = null;
    $insertData['RemarksStatus'] = 1;
} else {
    $insertData['Remarks'] = $data['Remarks'];
    $insertData['RemarksStatus'] = 0;
}

if (isset($data['ClassMemberType'])) {
    $insertData['ClassMemberType'] = implode(',', $data['ClassMemberType']);
    $insertData['ClassLimitTypes'] = in_array('BA999', $data['ClassMemberType']) ? 0 : 1;
}

if (isset($data['purchaseOptions'])) {
    $insertData['purchaseOptions'] = 0;
    $insertData['FreeClass'] = 0;
    switch ($data['purchaseOptions']) {
        case 'membership-cost':
            $insertData['purchaseOptions'] = 1;
            break;
        case 'free-register':
            $insertData['FreeClass'] = 2;
            break;
    }
}

if (isset($data['purchaseLocation'])) {
    $insertData['purchaseLocation'] = $data['purchaseLocation'];
    if ($data['purchaseLocation'] == 0) {
        $insertData['ShowApp'] = 2;
    } else {
        $insertData['ShowApp'] = 1;
    }
}
if (isset($data['ShowApp'])) {
    $insertData['ShowApp'] = $data['ShowApp'];
} //Override ShowApp if got value

if (isset($data['NewClassType'])) {
    $data['colorId'] = isset($data['colorId']) && is_numeric($data['colorId']) ? $data['colorId'] : 1;
    $newClassType = [
        'Type' => $data['ClassNameType'],
        'CompanyNum' => $CompanyNum,
        'Color' => $data['colorId'],
        'duration' => $data['duration'],
        'memberships' => isset($data['NewClassTypeMemberships']) ? explode(',', $data['NewClassTypeMemberships']) : null,
    ];
    $data['ClassNameType'] = (new ClassesType())->insertSingleClassType($newClassType);
}
$insertData['ClassNameType'] = $ClassNameType = $data['ClassNameType'];

$FixGroupNumber = $data['FixGroupNumber'] ?? 0;

if (isset($data['CancelLaw'])) {
    $insertData['CancelLaw'] = $CancelLaw = $data['CancelLaw'];
    if ($CancelLaw == 3) {
        $CancelPeriodType = $data['CancelPeriodType'] ?? 'hours';
        $CancelPeriodAmount = $data['CancelPeriodAmount'] ?? 3;
        $CancelTime = strtotime("-$CancelPeriodAmount $CancelPeriodType", strtotime("$StartDate $StartTime"));

        $insertData['CancelDay'] = $CancelDay = date('w', $CancelTime);
        $insertData['CancelDayName'] = $CancelDayName = Utils::numberToDay($CancelDay);
        $insertData['CancelTillTime'] = $CancelTillTime = date('H:i:s', $CancelTime);
        $insertData['CancelDayMinus'] = ($DayNum >= $CancelDay) ? $DayNum - $CancelDay : (7 + $DayNum) - $CancelDay;
    } else {
        $insertData['CancelDay'] = $CancelDay = $DayNum;
        $insertData['CancelDayName'] = $CancelDayName = $Day;
    }
} else {
    //place for default cancel else will go to DB defaults
}

if(isset($data['StopCancel'])){
    $insertData['StopCancel'] = $data['StopCancel'] == '0' ? '0' : '1';
    if($insertData['StopCancel'] === '0'){
        $insertData['StopCancelType'] = $data['StopCancelType'] == '1' ? '1': '2';
        $insertData['StopCancelTime'] =  preg_replace( '/[^0-9]/', '', $data['StopCancelTime']);
        if(empty($insertData['StopCancelTime']) && !is_numeric($insertData['StopCancelTime'])){
            $insertData['StopCancelTime'] = "10";
        }
    }
}

if (isset($data['liveClassLink'])) {
    $insertData['liveClassLink'] = $data['liveClassLink'];
    $onlineClassData = [];
    if (isset($data['onlineSendType'])) $onlineClassData['sendType'] = $data['onlineSendType'];
    if (isset($data['onlineSendTime'])) $onlineClassData['sendTime'] = $data['onlineSendTime'];
    if (isset($data['onlineSendTimeType'])) $onlineClassData['sendTimeType'] = $data['onlineSendTimeType'];
}

if (isset($data['LiveClass'])) {
    if ($data['LiveClass'] == 'zoom')
        $insertData['is_zoom_class'] = $is_zoom_class = 1;
    else
        $insertData['is_zoom_class'] = $is_zoom_class = 0;

    if ($data['LiveClass'] != 'online') {
        $insertData['liveClassLink'] = null;
        $insertData['onlineClassId'] = null;
    }
} else {
    $is_zoom_class = 0;
}

if (!$CalendarId) {
    $GroupNumber = $insertData['GroupNumber'] = GroupNumberHelper::generate();
}

$insertData['text'] = $ClassNameTypeTitle = ($ClassNameType == 0 ? '' : (ClassesType::find($ClassNameType))->__get('Type'));
$SectionObj = Section::where('id', $FloorId)->first();
if ($SectionObj->__get('id')) $insertData['Brands'] = $SectionObj->__get('Brands');

$ClassSettingsInfo = (new ClassSettings())->GetClassSettingsByCompanyNum($CompanyNum);

if (empty($CalendarId)) { //New Class
    if ($ClassType == '1' || $ClassType == '2') {
        if ($ClassType == '1')
            $ClassCount = 30;

        $StartDates = date('Y-m-d', strtotime($StartDate));
        if ($data['freqType'] == 'date') {
            $EndDates = $data['regularEndDate'] ?? date('Y-m-d');
        } else {
            $interval = '+' . $ClassCount * $ClassRepeat . ' week';
            $EndDates = date('Y-m-d', strtotime($StartDate . $interval));
        }
        $dateArr = Utils::createDateRangeWeek($StartDates, $EndDates, $ClassRepeat);

    } elseif ($ClassType == '3') {
        $dateArr = [$StartDate];
    }

    if ($MaxClient == 0) {
        /** @var Users $Guide */
        $Guide = Users::find($GuideId);
        $occupiedCheck = $Guide->getOccupied($dateArr, $StartTime, $EndTime);

        if (!isset($data['overrideLimitation']) && count($occupiedCheck) > 0) {
            return [
                'status' => 0,
                'data' => $occupiedCheck,
                'message' => lang('the_guide') . ' ' . $Guide->display_name . ' ' . lang('is_occupied_in'),
            ];
        }

        // system log
        $LogText = 'הקים את בלוק חסימת יומן ' . $insertData['ClassName'] . ', בתאריך ' . $insertData['StartDate']
            . ' ובשעה ' . $insertData['StartTime'] . ' - ' . $insertData['EndTime'] . ', איש צוות ' . $Guide->display_name;
        CreateLogMovement($LogText, null);
    } else {
        $occupiedCheck = $SectionObj->isOccupied($dateArr, $StartTime, $EndTime);
        if (isset($occupiedCheck->id)) {
            $errorText = lang('the_calendar') . ' ' . $SectionObj->__get('Title');
        } elseif ($ClassSettingsInfo->GuideCheck == '1') {
            $occupiedCheck = $class_Users->isOccupied($GuideId, $CompanyNum, $dateArr, $StartTime, $EndTime);
            if (isset($occupiedCheck->id)) {
                $errorText = lang('the_guide') . ' ' . $GuideObj->__get('display_name');
            }
        }
    }
    if (isset($errorText)) {

        if (isset($data['NewClassType'])) {
            (new ClassesType)->delete($ClassNameType);
        }
        return [
            'message' => $errorText . ' ' . lang('is_occupied_in') . ' ' . date('d/m/Y H:i', strtotime($occupiedCheck->start_date)),
            'status' => 0
        ];
    }

    if (!empty($data['liveClassLink'])) {
        $insertData['onlineClassId'] = ClassOnline::insertGetId($onlineClassData ?? []);
    }

    $tagStudioId = $data['tag'] ?? TagsService::getLessonPredictionTagKey($insertData['ClassName'], $insertData['ClassNameType'], $CompanyNum)['id'] ?? null;
    foreach ($dateArr as $key => $value) {
        if ($key == 30)
            break;

        $insertData['ClassCount'] = $key + 1;
        $insertData['start_date'] = $value . ' ' . $StartTime;
        $insertData['end_date'] = $value . ' ' . $EndTime;
        $insertData['StartDate'] = $insertData['EndDate'] = $value;

        // *** Database Insertion ***
        $AddClassDesk = DB::table('classstudio_date')->insertGetId($insertData);

        if($tagStudioId && $AddClassDesk > 0) {

            $tagStudioOld = new TagsStudio();
            $tagStudioOld->studio_date_id = $AddClassDesk;
            $tagStudioOld->company_num = $CompanyNum;
            $tagStudioOld->isCron = 0;
            $tagStudioOld->tags_id = $tagStudioId;
            $tagStudioOld->save();
        }



        if(isset($insertData['is_zoom_class']) && gettype($AddClassDesk)== 'integer') {
            $studioDateObj->insertIntoClass_zoom($AddClassDesk, $data);
        }
    }
} else { //Edit Class

    $CheckClassInfo = ClassStudioDate::getClassById($CalendarId, $CompanyNum);
    $insertData['GroupNumber'] = $GroupNumber = $CheckClassInfo->GroupNumber;
    $insertData['start_date'] = $StartDate . ' ' . $StartTime;
    $insertData['end_date'] = $EndDate . ' ' . $EndTime;
    $ClassNameF = $CheckClassInfo->ClassName;
    $StartTimeF = $CheckClassInfo->StartTime;
    $DayF = $CheckClassInfo->Day;
    $insertData['Change'] = 1; //Class was edited flag

    if (!empty($data['liveClassLink'])) {
        $insertData['onlineClassId'] = ClassStudioDate::updateOnlineClass(
            $CheckClassInfo->ClassType == '3',
            $onlineClassData ?? [],
            $CheckClassInfo->onlineClassId
        );
    }

    //Single Class
    if (!isset($data['GroupEdit']) || $data['GroupEdit'] == '0') {
        if ($CheckClassInfo->StartDate != $StartDate
            || $CheckClassInfo->StartTime != $StartTime
            || $CheckClassInfo->EndTime != $EndTime
            || $CheckClassInfo->GuideId != $GuideId) {

            if ($MaxClient == 0) {
                /** @var Users $Guide */
                $Guide = Users::find($GuideId);
                $occupiedCheck = $Guide->getOccupied([$StartDate], $StartTime, $EndTime, $GroupNumber);

                if (!isset($data['overrideLimitation']) && count($occupiedCheck) > 0) {
                    return [
                        'status' => 0,
                        'data' => $occupiedCheck,
                        'message' => lang('the_guide') . ' ' . $Guide->display_name . ' ' . lang('is_occupied_in'),
                    ];
                }

                // system log
                $LogText = 'ערך את בלוק חסימת יומן '
                    . ($CheckClassInfo->ClassName != $insertData['ClassName'] ? ' ' . $CheckClassInfo->ClassName . ' ← ' : '')
                    . $insertData['ClassName'] . ', הגדרות חדשות:<br>';
                // add only what changed
                if ($CheckClassInfo->GuideId != $insertData['GuideId']) {
                    /** @var Users $OldGuide */
                    $OldGuide = Users::find($CheckClassInfo->GuideId);

                    $LogText .= ':איש צוות ' . $OldGuide->display_name . ' ← ' . $Guide->display_name . '<br>';
                }
                if ($CheckClassInfo->StartDate != $insertData['StartDate']) {
                    $LogText .= 'תאריך: ' . $CheckClassInfo->StartDate . ' ← ' . $insertData['StartDate'] . '<br>';
                }
                if ($CheckClassInfo->StartTime != $insertData['StartTime'] || $CheckClassInfo->EndTime != $insertData['EndTime']) {
                    $LogText .= 'שעה: ' . substr($CheckClassInfo->StartTime, 0, 5) . '-' . substr($CheckClassInfo->EndTime, 0, 5) . ' ← '
                        . substr($insertData['StartTime'], 0, 5) . '-' . substr($insertData['EndTime'], 0, 5) . '<br>';
                }
                CreateLogMovement($LogText, null);
            } else {
                $occupiedCheck = $SectionObj->isOccupied([$StartDate], $StartTime, $EndTime, $GroupNumber);
                if (isset($occupiedCheck->id)) {
                    $errorText = lang('the_calendar') . ' ' . $SectionObj->__get('Title');
                } else {
                    if ($ClassSettingsInfo->GuideCheck == '1') {
                        $occupiedCheck = $class_Users->isOccupied($GuideId, $CompanyNum, [$StartDate], $StartTime, $EndTime, $GroupNumber);
                        if (isset($occupiedCheck->id))
                            $errorText = lang('the_guide') . ' ' . $GuideObj->__get('display_name');
                    }
                }
            }
            if (isset($errorText)) {
                if (isset($data['NewClassType'])) {
                    ClassesType::delete($ClassNameType);
                }
                return [
                    'message' => $errorText . ' ' . lang('is_occupied_in') . ' ' . date('d/m/Y H:i', strtotime($occupiedCheck->start_date)),
                    'status' => 0
                ];
            }
        }

        if ($MaxClient != 0) {
            $ClassInfoOne = $ClassNameF . ' ' . lang('in_date_ajax') . ' ' . $StartDate . ' ' . lang('at_time_ajax') . ' ' . $StartTimeF . ' ' . lang('at_day_ajax') . ' ' . $DayF . ' ' . lang('in_room_ajax') . ' ' . htmlentities($SectionObj->__get('Title'));
            CreateLogMovement( //FontAwesome Icon
                lang('log_edit_class_ajax') . ' ' . $ClassInfoOne, //LogContent
                '0' //ClientId
            );
        }

        ClassStudioDate::updateById($CalendarId, $insertData);

        $tagStudioOld = TagsStudio::getTagByLessonId($CalendarId);
        $tagStudioId = $data['tag'] ?? $tagStudioOld->tags_id ?? null;
        if (!isset($tagStudioOld) && $tagStudioId && $CalendarId > 0) {

            $tagStudioOld = new TagsStudio([
                'studio_date_id' => $CalendarId,
                'tags_id' => $tagStudioId,
                'company_num' => $CompanyNum,
                'isCron' => 0
            ]);

            $tagStudioOld->save();
        } elseif($tagStudioId && $tagStudioOld->tags_id != $tagStudioId) {
            $tagStudioOld->tags_id = $tagStudioId;
            $tagStudioOld->save();
        }

        if ($is_zoom_class == 1) {
            ClassStudioDate::insertIntoClass_zoom($CalendarId, $data, true);
        }

    } elseif ($data['GroupEdit'] == '1') { //Group Class
        $ClassInfoRegular = $ClassNameF . ' ' . lang('at_time_class_ajax') . ' ' . $StartTimeF . ' ' . lang('at_day_ajax') . ' ' . $DayF . ' ' . lang('in_room_ajax') . ' ' . htmlentities($SectionObj->__get('Title'));
        CreateLogMovement(
            lang('log_edit_class_group_ajax') . ' ' . $ClassInfoRegular, //LogContent
            '0' //ClientId
        );

        $TrueClassInfos = DB::table('classstudio_date')
            ->where('CompanyNum', $CompanyNum)
            ->where('GroupNumber', $GroupNumber)
            ->where('StartDate', '>=', $CheckClassInfo->StartDate)
            ->whereIn('Status', array(0, 1))
            ->orderBy('StartDate', 'ASC')
            ->get();


        if (count($TrueClassInfos)) {
            $firstClass = $TrueClassInfos[0];
            $pastClassesCount = ClassStudioDate::getActiveClassCount($CompanyNum, $GroupNumber, $CheckClassInfo->StartDate);

            //If something related to frequency, or end date was changed
            if ($insertData['StartDate'] != $CheckClassInfo->StartDate ||
                $insertData['StartTime'] != $CheckClassInfo->StartTime ||
                $insertData['EndTime'] != $CheckClassInfo->EndTime ||
                $insertData['ClassRepeat'] != $firstClass->ClassRepeat ||
                $insertData['ClassType'] != $firstClass->ClassType ||
                (isset($data['regularEndDate']) && $data['regularEndDate'] != end($TrueClassInfos)->StartDate)) {
                if ($ClassType != 3) {
                    $StartDates = date('Y-m-d', strtotime($insertData['StartDate']));
                    if ($ClassType == 2) {
                        $EndDates = $data['regularEndDate'];
                    } elseif ($ClassType == 1) {
                        $interval = '+' . $ClassRepeat * 30 . ' weeks';
                        $EndDates = date('Y-m-d', strtotime("$StartDates $interval"));
                    }
                    $ClassRepeat = (int)$ClassRepeat === 0 ? 1 : $ClassRepeat;
                    $newClassDates = Utils::createDateRangeWeek($StartDates, $EndDates, $ClassRepeat);
                } else {
                    $newClassDates = [$insertData['StartDate']];
                }

                $occupiedCheck = $SectionObj->isOccupied($newClassDates, $StartTime, $EndTime, $GroupNumber);
                if (isset($occupiedCheck->id)) {
                    $errorText = lang('the_calendar') . ' ' . $SectionObj->__get('Title');
                } else {
                    if ($ClassSettingsInfo->GuideCheck == '1') {
                        $occupiedCheck = $class_Users->isOccupied($GuideId, $CompanyNum, $newClassDates, $StartTime, $EndTime, $GroupNumber);
                        if (isset($occupiedCheck->id))
                            $errorText = lang('the_guide').' '.$GuideObj->__get('display_name');
                    }
                }
                if (isset($errorText)) {
                    if (isset($data['NewClassType'])) {
                        ClassesType::delete($ClassNameType);
                    }
                    return [
                        'message' => $errorText . ' ' . lang('is_occupied_in') . ' ' . date('d/m/Y H:i', strtotime($occupiedCheck->start_date)),
                        'status' => 0
                    ];
                }

                if ($insertData['StartDate'] != $CheckClassInfo->StartDate || $insertData['StartTime'] != $CheckClassInfo->StartTime || $insertData['EndTime'] != $CheckClassInfo->EndTime){
                    $currentRepeat = (int)$CheckClassInfo->ClassRepeat !== 0 ? $CheckClassInfo->ClassRepeat : 1;
                    $interval = '+'. ($currentRepeat * count($TrueClassInfos)) .' weeks';
                    $updatedDates = Utils::createDateRangeWeek(
                        $insertData['StartDate'],
                        date('Y-m-d', strtotime("$StartDate $interval")),
                        $currentRepeat
                    );

                    foreach ($TrueClassInfos as $key => $TrueClassInfo) {
                        $TrueClassInfos[$key]->StartDate = $updatedDates[$key];
                        $TrueClassInfos[$key]->EndDate = $updatedDates[$key];

                        DB::table('classstudio_date')
                            ->where('id', $TrueClassInfo->id)
                            ->where('CompanyNum', $CompanyNum)
                            ->update([
                                'EndDate' => $updatedDates[$key],
                                'StartDate' => $updatedDates[$key],
                                'start_date' => ($updatedDates[$key] . ' ' . $StartTime),
                                'end_date' => ($updatedDates[$key] . ' ' . $EndTime),
                            ]);
                        DB::table('classstudio_act')
                            ->where('ClassId', $TrueClassInfo->id)
                            ->where('CompanyNum', $CompanyNum)
                            ->update([
                                'ClassDate' => $updatedDates[$key],
                                'ClassStartTime' => ($StartTime),
                                'ClassEndTime' => ($EndTime)
                            ]);
                    }

                    ClassStudioDateRegular::updateByGroupNumber($GroupNumber, [
                        'ClassDay' => $insertData['Day'],
                        'DayNum' => $insertData['DayNum'],
                        'ClassTime' => $insertData['StartTime'],
                        'Floor' => $insertData['Floor'],
                    ]);
                }

                foreach ($newClassDates as $key => $date) {
                    $insertData['ClassCount'] = $ClassCount = $firstClass->ClassCount + $key;

                    if ($key >= (30 - $pastClassesCount)) {
                        $newClassDates = array_slice($newClassDates, 0, $key);
                        break;
                    }

                    $insertData['start_date'] = $date . ' ' . $StartTime;
                    $insertData['end_date'] = $date . ' ' . $EndTime;
                    $insertData['StartDate'] = $insertData['EndDate'] = $date;

                    $classExist = ClassStudioDate::getByGroupAndDate($CompanyNum, $GroupNumber, $date);
                    if (!$classExist) {
                        $AddClassDesk = DB::table('classstudio_date')->insertGetId($insertData);

                        $tagStudioOld = TagsStudio::getTagByLessonId($CalendarId);
                        $tagStudioId = $data['tag'] ?? $tagStudioOld->tags_id ?? null;
                        if ($tagStudioId && $AddClassDesk > 0) {

                            $tagStudioOld = new TagsStudio([
                                'studio_date_id' => $AddClassDesk,
                                'tags_id' => $tagStudioId,
                                'company_num' => $CompanyNum,
                                'isCron' => 0
                            ]);
                            $tagStudioOld->save();
                        }
                        ClassStudioDateRegular::createActsByRegularAssignment($AddClassDesk);
                    } else {
                        ClassStudioDate::updateById($classExist->id, $insertData);
                    }
                }
            }
        }

        $lessonsIdArray = []; //for one tags_studio update
        foreach ($TrueClassInfos as $TrueClassInfo) {
            $lessonsIdArray[] = $TrueClassInfo->id;
            $insertData['start_date'] = $TrueClassInfo->StartDate . ' ' . $StartTime;
            $insertData['end_date'] = $TrueClassInfo->StartDate . ' ' . $EndTime;
            unset($insertData['StartDate'], $insertData['EndDate'], $insertData['ClassCount']);

            $insertData['Status'] = (isset($newClassDates) && !in_array($TrueClassInfo->StartDate, $newClassDates)) ? 2 : 0;
            if ($insertData['Status'] == 2) {
                ClassStudioAct::cancelClassActs($TrueClassInfo->id);
            }

            ClassStudioDate::updateById($TrueClassInfo->id, $insertData);

            if ($is_zoom_class == 1)
                ClassStudioDate::insertIntoClass_zoom($TrueClassInfo->id, $data, true);
        }
        $tagStudioId = $data['tag'] ?? TagsStudio::getTagByLessonId($lessonsIdArray[0])->tags_id ?? null;
        if($tagStudioId) {
            TagsStudio::groupLessonUpdate($lessonsIdArray, $tagStudioId, $CompanyNum);
        }

        //Permanent assignment update
        $updateDateRegular = ['ClassTime' => $StartTime];
        if (isset($newClassDates)) {
            if ($ClassType == 3) {
                $updateDateRegular['Status'] = 1;
            } elseif ($ClassType == 2) {
                $updateDateRegular['RegularClassType'] = $ClassType;
            }
        }

        if (isset($TrueClassInfo)) {
            DB::table('classstudio_dateregular')
                ->where('GroupNumber', $TrueClassInfo->GroupNumber)
                ->where('DayNum', $TrueClassInfo->DayNum)
                ->where('ClassTime', $TrueClassInfo->StartTime)
                ->where('Floor', $FloorId)
                ->where('CompanyNum', $CompanyNum)
                ->update($updateDateRegular);
        }
    }
}

return ['message' => 'Success', 'status' => 1];

