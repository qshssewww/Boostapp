<?php

class saveClassService extends Utils
{
    const REQUIRED_FIELDS = [
        'StartTime',
        'StartDate',
        'Floor',
        'ClassNameType',
        'ClassName',
        'MaxClient',
        'color',
        'ClassRepeat',
        'GuideId',
    ];

    const OPTIONAL_FIELDS = [
        'SendReminder',
        'TimeReminder',
        'TypeReminder',
        'GenderLimit',
        'ShowClientNum',
        'ShowClientName',
        'WatingListOrederShow',
        'ClassDevice',
        'MaxWatingList',
        'NumMaxWatingList',
        'MinClass',
        'MinClassNum',
        'ClassTimeCheck',
        'ClassTimeTypeCheck',
        'ClassWating',
        'Remarks',
        'registerLimit',
        'OpenOrder',
        'OpenOrderTime',
        'OpenOrderType',
        'CloseOrder',
        'CloseOrderTime',
        'CloseOrderType',
        'purchaseAmount',
        'ageLimitType',
        'ageLimitNum1',
        'ageLimitNum2',
        'image',
        'ShowApp',
        'ClassNameType', //Override in case of new type
    ];

    public function new($data) {
        if (Auth::guest() || !isset($data)) exit;

        $insertData = [];
        $isEdit = isset($data['CalendarId']);
        $CompanyNum = (Company::getInstance())->__get('CompanyNum');

        if (!$isEdit) {
            $validation = self::validateData($data);
            if ($validation->fails())
                return ['message' => $validation->messages()->first(), 'status' => 0];

            $insertData['CompanyNum'] = $CompanyNum;
            $insertData['GroupNumber'] = uniqid(uniqid() . strtotime(date('YmdHis')) . 1262055681 . rand(1, 9999999));
        } else {
            $editedClass = ClassStudioDate::getClassById($data['CalendarId'], $CompanyNum);
            $data['duration'] = Utils::calcMinutesDiff($editedClass->__get('StartTime'), $editedClass->__get('EndTime'));
        }

        $insertData['UserId'] = Auth::user()->__get('id');
        $insertData['Dates'] = date('Y-m-d H:i:s');

        foreach (self::REQUIRED_FIELDS as $field) {
            if (!isset($data[$field])) {
                if (!$isEdit)
                    return ['message' => 'Missing required field: '.$field, 'status' => 0];
                else
                    $insertData[$field] = $editedClass->__get($field);
            } else $insertData[$field] = $data[$field];
        }

        //dependent values
        if (isset($data['StartTime']))
            $insertData['EndTime'] = Utils::addInterval($insertData['StartTime'], "+".$data['duration']." minutes", 'H:i:s');

        if (isset($data['StartDate'])) {
            $insertData['DayNum'] = date('w', $insertData['StartDate']);
            $insertData['Day'] = Utils::numberToDay($insertData['DayNum']);

            if (isset($insertData['StartTime'])) {
                $insertData['EndDate'] = Utils::addInterval($insertData['StartDate'].' '.$insertData['StartTime'],
                    'Y-m-d', "+".$data['duration']." minutes");
            } else
                $insertData['EndDate'] = Utils::addInterval($insertData['StartDate'], "+".$data['duration']." minutes", 'Y-m-d');
        }

        if (isset($data['GuideId'])) {
            $GuideObj = new Users($insertData['GuideId']);
            $insertData['GuideName'] = $GuideObj->__get('display_name');
        }

        if (isset($data['ExtraGuideId'])) {
            $insertData['ExtraGuideId'] = $data['ExtraGuideId'];
            $insertData['ExtraGuideName'] = (new Users($insertData['ExtraGuideId']))->__get('display_name');
        }

        if (isset($data['ClassRepeat'])) {
            if ($insertData['ClassRepeat'] == 0)
                $insertData['ClassType'] = 3;
        }

        if (isset($data['freqType'])){
            if ($data['freqType'] == '0')
                $insertData['ClassType'] = 1; //Permanent
            else {
                $insertData['ClassType'] = 2; //Several Times
                $ClassCount = $data['freqType'];
            }
        }

        if (isset($data['ClassRepeatType']))
            $insertData['ClassRepeatType'] = $data['ClassRepeatType'];
        else if (!$isEdit)
            $insertData['ClassRepeatType'] = 2;

        if (isset($data['purchaseLocation'])){
            $insertData['purchaseLocation'] = $data['purchaseLocation'];
            if ($data['purchaseLocation'] == 0)
                $insertData['ShowApp'] = 2;
            else
                $insertData['ShowApp'] = 1;
        }

        if (isset($data['LimitLevel'])) $insertData['LimitLevel'] = implode($data['LimitLevel'], ',');

        if (isset($data['ClassMemberType'])){
            $insertData['ClassMemberType'] = implode(',', $data['ClassMemberType']);
            $insertData['ClassLimitTypes'] = in_array('BA999', $data['ClassMemberType']) ? 0 : 1;
        }

        if (isset($data['purchaseOptions'])){
            $insertData['purchaseOptions'] = 0;
            $insertData['FreeClass'] = 0;
            switch ($data['purchaseOptions']){
                case 'membership-cost':
                    $insertData['purchaseOptions'] = 1;
                    break;
                case 'free-register':
                    $insertData['FreeClass'] = 2;
                    break;
            }
        }

        if (isset($data['NewClassType'])){
            $data['colorId'] = isset($data['colorId']) && is_numeric($data['colorId']) ? $data['colorId'] : 1;
            $newClassType = [
                'Type' => $data['ClassNameType'],
                'CompanyNum' => $insertData['CompanyNum'],
                'Color' => $data['colorId'],
                'duration' => $data['duration'],
                'memberships' => isset($data['NewClassTypeMemberships']) ? explode(',', $data['NewClassTypeMemberships']) : null,
            ];
            $data['ClassNameType'] = (new ClassesType())->insertSingleClassType($newClassType);
        }

        if (isset($data['ClassNameType'])) {
            $insertData['text'] = (new ClassesType($insertData['ClassNameType']))->__get('Type');
        }

        if (isset($data['CancelLaw'])){
            $insertData['CancelLaw'] = $CancelLaw = $data['CancelLaw'];
            if ($CancelLaw == 3){
                $CancelPeriodType = $data['CancelPeriodType'] ?? 'hours';
                $CancelPeriodAmount = $data['CancelPeriodAmount'] ?? 3;
                $CancelTime = strtotime("-$CancelPeriodAmount $CancelPeriodType", strtotime($insertData['StartDate'].' '.$insertData['StartTime']));

                $insertData['CancelDay'] = $CancelDay = date('w', $CancelTime);
                $insertData['CancelDayName'] = $CancelDayName = Utils::numberToDay($CancelDay);
                $insertData['CancelTillTime'] = $CancelTillTime = date('H:i:s', $CancelTime);
                $insertData['CancelDayMinus'] = ($insertData['DayNum'] >= $CancelDay) ? $insertData['DayNum'] - $CancelDay : (7 + $insertData['DayNum']) - $CancelDay;
            } else {
                $insertData['CancelDay'] = $CancelDay = $insertData['DayNum'];
                $insertData['CancelDayName'] = $CancelDayName = $insertData['Day'];
            }
        } else {
            //place for default cancel else will go to DB defaults
        }

        if (isset($data['liveClassLink'])){
            $insertData['liveClassLink'] = $data['liveClassLink'];
            $onlineClassData = [];
            if (isset($data['onlineSendType'])) $onlineClassData['sendType'] = $data['onlineSendType'];
            if (isset($data['onlineSendTime'])) $onlineClassData['sendTime'] = $data['onlineSendTime'];
            if (isset($data['onlineSendTimeType'])) $onlineClassData['sendTimeType'] = $data['onlineSendTimeType'];
        }

        if(isset($data['LiveClass'])){
            if ($data['LiveClass'] == 'zoom')
                $insertData['is_zoom_class'] = 1;
            else
                $insertData['is_zoom_class'] = 0;

            if ($data['LiveClass'] != 'online') {
                $insertData['liveClassLink'] = null;
                $insertData['onlineClassId'] = null;
            }
        } else {
            $insertData['is_zoom_class'] = 0;
        }

        $SectionObj = Section::where('id', $insertData['Floor'])->first();
        if ($SectionObj->__get('id')) $insertData['Brands'] = $SectionObj->__get('Brands');

        foreach (self::OPTIONAL_FIELDS as $field) {
            if (isset($data[$field]))
                $insertData[$field] = $data[$field];
            else if (!empty($editedClass))
                $insertData[$field] = $editedClass->__get($field);
        }
    }

    private static function validateData($data) {
        return Validator::make($data,
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
            ]
        );
    }

    private static function createClass($insertData, $data, $SectionObj, $GuideObj) {
        $ClassSettingsInfo = (new ClassSettings())->GetClassSettingsByCompanyNum($insertData['CompanyNum']);

        switch ($insertData['ClassType']) {
            case '1':
                $ClassCount = 30;
            case '2':
                $StartDates = date('Y-m-d', strtotime($insertData['StartDate']));
                if ($insertData['ClassRepeat'] == 'date'){
                    $EndDates = $data['regularEndDate'] ?? date('Y-m-d');
                } else {
                    $interval = '+' . $ClassCount * $insertData['ClassRepeat'] . ' week';
                    $EndDates = date('Y-m-d', strtotime($insertData['StartDate'] . $interval));
                }
                $dateArr = Utils::createDateRangeWeek($StartDates, $EndDates, $insertData['ClassRepeat']);
                break;
            case '3':
                $dateArr = [$insertData['StartDate']];
                break;
        }

        DB::table('classstudio_date')
            ->where('id', $data['CalendarId'])
            ->update($insertData);

        $occupiedCheck = $SectionObj->isOccupied($dateArr, $insertData['StartDate'], $insertData['EndTime']);
        if (isset($occupiedCheck->id)){
            $errorText = lang('the_calendar').' '.$SectionObj->__get('Title');
        } else {
            if ($ClassSettingsInfo->GuideCheck == '1') {
                $occupiedCheck = $GuideObj->isOccupied($dateArr, $insertData['StartDate'], $insertData['EndTime']);
                if (isset($occupiedCheck->id))
                    $errorText = lang('the_guide').' '.$GuideObj->__get('display_name');
            }
        }
        if (isset($errorText)){

            if (isset($data['NewClassType'])) ClassesType::delete($insertData['ClassNameType']);
            return [
                'message' => $errorText.' '. lang('is_occupied_in').' '.date('d/m/Y H:i', strtotime($occupiedCheck->start_date)),
                'status' => 0
            ];
        }

        if (!empty($data['liveClassLink']))
            $insertData['onlineClassId'] = ClassOnline::insertGetId($onlineClassData ?? []);

        DB::table('classstudio_date')
            ->where('id', $data['CalendarId'])
            ->update($insertData);

        foreach ($dateArr as $key => $value) {
            if ($key == 30) break;

            $insertData['ClassCount'] = $key + 1;
            $insertData['start_date'] = $value . ' ' . $insertData['StartTime'];
            $insertData['end_date'] = $value . ' ' . $insertData['EndTime'];
            $insertData['StartDate'] = $insertData['EndDate'] = $value;

            // *** Database Insertion ***
            $newClassId = DB::table('classstudio_date')->insertGetId($insertData);

            if(empty($newClassId)== 'integer' && isset($insertData['is_zoom_class']))
                ClassStudioDate::insertIntoClass_zoom($newClassId, $data); //todo: handle data
        }

        return ['message' => 'Success', 'status' => 1];
    }

    private static function editSingleClass($insertData, $data, $SectionObj, $GuideObj, $editedClass) {
        $ClassSettingsInfo = (new ClassSettings())->GetClassSettingsByCompanyNum($insertData['CompanyNum']);

        if ($editedClass->__get('StartDate') != $insertData['StartDate']) {
            $occupiedCheck = $SectionObj->isOccupied([$insertData['StartDate']], $insertData['StartTime'], $insertData['EndTime'], $insertData['GroupNumber']);
            if (isset($occupiedCheck->id)) {
                $errorText = lang('the_calendar') . ' ' . $SectionObj->__get('Title');
            } else {
                if ($ClassSettingsInfo->GuideCheck == '1') {
                    $occupiedCheck = $GuideObj->isOccupied([$insertData['StartDate']], $insertData['StartTime'], $insertData['EndTime'], $insertData['GroupNumber']);
                    if (isset($occupiedCheck->id))
                        $errorText = lang('the_guide') . ' ' . $GuideObj->__get('display_name');
                }
            }
            if (isset($errorText)) {
                if (isset($data['NewClassType'])) ClassesType::delete($insertData['ClassNameType']);
                return [
                    'message' => $errorText . ' ' . lang('is_occupied_in') . ' ' . date('d/m/Y H:i', strtotime($occupiedCheck->start_date)),
                    'status' => 0
                ];
            }
        }

        $ClassInfoOne = $insertData['ClassName'].' '.lang('in_date_ajax').' '.
            $insertData['StartDate'].' '.lang('at_time_ajax').' '.$insertData['StartTime'].' '.
            lang('at_day_ajax').' '.$insertData['Day'].' '.lang('in_room_ajax').' ' .
            htmlentities($SectionObj->__get('Title'));

        CreateLogMovement(
            lang('log_edit_class_ajax').' '.$ClassInfoOne, //LogContent
            '0' //ClientId
        );

        DB::table('classstudio_date')
            ->where('id', $data['CalendarId'])
            ->update($insertData);

        if(isset($insertData['is_zoom_class']) && $insertData['is_zoom_class'] == 1)
            ClassStudioDate::insertIntoClass_zoom($data['CalendarId'], $data, true);
    }

//    private static function editManyClasses(){ //Group Class
//        $TrueClassInfos = DB::table('classstudio_date')
//            ->where('GroupNumber', $GroupNumber)
//            ->where('StartTime', $editedClass->__get('StartTime'))
//            ->where('DayNum', $CheckClassInfo->DayNum)
//            ->where('Floor', $CheckClassInfo->Floor)
//            ->where('StartDate', '>=', $CheckClassInfo->StartDate)
//            ->whereIn('Status', array(0, 1))
//            ->where('CompanyNum', $CompanyNum)
//            ->orderBy('StartDate', 'ASC')
//            ->get();
//
//
//        if (count($TrueClassInfos)) {
//            $firstClass = $TrueClassInfos[0];
//            $pastClassesCount = ClassStudioDate::getActiveClassCount($CompanyNum, $GroupNumber, $CheckClassInfo->StartDate);
//            //If something related to frequency, or end date was changed
//            if ($insertData['StartDate'] != $CheckClassInfo->StartDate ||
//                $insertData['ClassRepeat'] != $firstClass->ClassRepeat ||
//                $insertData['ClassType'] != $firstClass->ClassType ||
//                (isset($data['regularEndDate']) && $data['regularEndDate'] != end($TrueClassInfos)->StartDate)) {
//                if ($ClassType != 3) {
//                    $StartDates = date('Y-m-d', strtotime($insertData['StartDate']));
//                    if ($ClassType == 2)
//                        $EndDates = $data['regularEndDate'];
//                    else if ($ClassType == 1){
//                        $interval = '+'. $ClassRepeat * 30 .' weeks';
//                        $EndDates = date('Y-m-d', strtotime("$StartDates $interval"));
//                    }
//                    $newClassDates = Utils::createDateRange($StartDates, $EndDates, $ClassRepeat);
//                } else
//                    $newClassDates = [$firstClass->StartDate];
//
//                $occupiedCheck = $SectionObj->isOccupied($newClassDates, $StartTime, $EndTime, $GroupNumber);
//                if (isset($occupiedCheck->id)){
//                    $errorText = lang('the_calendar').' '.$SectionObj->__get('Title');
//                } else {
//                    if ($ClassSettingsInfo->GuideCheck == '1') {
//                        $occupiedCheck = $GuideObj->isOccupied($newClassDates, $StartTime, $EndTime, $GroupNumber);
//                        if (isset($occupiedCheck->id))
//                            $errorText = lang('the_guide').' '.$GuideObj->__get('display_name');
//                    }
//                }
//                if (isset($errorText)){
//                    if (isset($data['NewClassType'])) ClassesType::delete($ClassNameType);
//                    return [
//                        'message' => $errorText.' '. lang('is_occupied_in').' '.date('d/m/Y H:i', strtotime($occupiedCheck->start_date)),
//                        'status' => 0
//                    ];
//                }
//
//                if ($insertData['StartDate'] != $CheckClassInfo->StartDate){
//                    $interval = '+'. ($CheckClassInfo->ClassRepeat * count($TrueClassInfos)) .' weeks';
//                    $updatedDates = Utils::createDateRange(
//                        $insertData['StartDate'],
//                        date('Y-m-d', strtotime("$StartDate $interval")),
//                        $CheckClassInfo->ClassRepeat
//                    );
//
//                    foreach ($TrueClassInfos as $key => $TrueClassInfo) {
//                        $TrueClassInfos[$key]->StartDate = $updatedDates[$key];
//                        $TrueClassInfos[$key]->EndDate = $updatedDates[$key];
//
//                        DB::table('classstudio_date')
//                            ->where('id', $TrueClassInfo->id)
//                            ->where('CompanyNum', $CompanyNum)
//                            ->update([
//                                'EndDate' => $updatedDates[$key],
//                                'StartDate' => $updatedDates[$key],
//                                'start_date' => ($updatedDates[$key] . ' ' . $StartTime),
//                                'end_date' => ($updatedDates[$key] . ' ' . $EndTime),
//                            ]);
//                    }
//
//                    ClassStudioDateRegular::updateByGroupNumber($GroupNumber, [
//                        'ClassDay' => $insertData['Day'],
//                        'DayNum' => $insertData['DayNum'],
//                        'ClassTime' => $insertData['StartTime'],
//                        'Floor' => $insertData['Floor'],
//                    ]);
//                }
//
//                foreach ($newClassDates as $key => $date) {
//                    $insertData['ClassCount'] = $ClassCount = $firstClass->ClassCount + $key;
//
//                    if ($key >= (30 - $pastClassesCount)){
//                        $newClassDates = array_slice($newClassDates, 0, $key);
//                        break;
//                    }
//
//                    $insertData['start_date'] = $date . ' ' . $StartTime;
//                    $insertData['end_date'] = $date . ' ' . $EndTime;
//                    $insertData['StartDate'] = $insertData['EndDate'] = $date;
//
//                    $classExistId = ClassStudioDate::isExist($CompanyNum, $GroupNumber, $date);
//                    if (!$classExistId || empty($classExistId)){
//                        $AddClassDesk = DB::table('classstudio_date')->insertGetId($insertData);
//                        (new ClassStudioDateRegular())->createActsByRegularAssignment($AddClassDesk);
//                    } else {
//
////                        $insertData['Status'] = 0;
//                        DB::table('classstudio_date')
//                            ->where('id', $classExistId)
//                            ->where('CompanyNum', $CompanyNum)
//                            ->update($insertData);
//                    }
//                }
//            }
//        }
//
//        foreach ($TrueClassInfos as $TrueClassInfo) {
//            $insertData['start_date'] = $TrueClassInfo->StartDate . ' ' . $StartTime;
//            $insertData['end_date'] = $TrueClassInfo->StartDate . ' ' . $EndTime;
//            unset($insertData['StartDate']);
//            unset($insertData['EndDate']);
//            unset($insertData['ClassCount']);
//
//            $insertData['Status'] = (isset($newClassDates) && !in_array($TrueClassInfo->StartDate, $newClassDates)) ? 2 : 0;
//            if ($insertData['Status'] == 2)
//                ClassStudioAct::cancelClassActs($TrueClassInfo->id);
//
//            DB::table('classstudio_date')
//                ->where('id', $TrueClassInfo->id)
//                ->where('CompanyNum', $CompanyNum)
//                ->update($insertData);
//
//            if($is_zoom_class == 1)
//                $studioDateObj->insertIntoClass_zoom($TrueClassInfo->id, $data,true);
//        }
//
//        $ClassInfoRegular = $insertData['ClassName'].' '.lang('at_time_class_ajax').' '.
//            $StartTimeF.' '.lang('at_day_ajax').' '.$DayF.' '.lang('in_room_ajax').' '.
//            htmlentities($SectionObj->__get('Title'));
//        CreateLogMovement(
//            lang('log_edit_class_group_ajax').' '.$ClassInfoRegular, //LogContent
//            '0' //ClientId
//        );
//
//        //Permanent assignment update
//        $updateDateRegular = ['ClassTime' => $StartTime];
//        if (isset($newClassDates)){
//            if ($ClassType == 3)
//                $updateDateRegular['Status'] = 1;
//            else if ($ClassType == 2)
//                $updateDateRegular['RegularClassType'] = $ClassType;
//        }
//
//        DB::table('classstudio_dateregular')
//            ->where('GroupNumber', $TrueClassInfo->GroupNumber)
//            ->where('DayNum', $TrueClassInfo->DayNum)
//            ->where('ClassTime', $TrueClassInfo->StartTime)
//            ->where('Floor', $FloorId)
//            ->where('CompanyNum', $CompanyNum)
//            ->update($updateDateRegular);
//    }
}