<?php

require_once __DIR__ . '/../../../app/helpers/GroupNumberHelper.php';
require_once __DIR__ . '/../../../app/helpers/TimeHelper.php';
require_once __DIR__ . '/../../Classes/ClassesType.php';
require_once __DIR__ . '/../../Classes/ClassStudioDate.php';
require_once __DIR__ . '/../../Classes/Client.php';
require_once __DIR__ . '/../../Classes/ClientActivities.php';
require_once __DIR__ . '/../../Classes/Clientcrm.php';
require_once __DIR__ . '/../../Classes/MeetingClient.php';
require_once __DIR__ . '/../../Classes/MeetingTemplates.php';
require_once __DIR__ . '/../../Classes/MeetingGeneralSettings.php';
require_once __DIR__ . '/../../Classes/MeetingGroupOrders.php';
require_once __DIR__ . '/../../Classes/MeetingGroupOrdersToAct.php';
require_once __DIR__ . '/../../Classes/Section.php';
require_once __DIR__ . '/../../Classes/Users.php';
require_once __DIR__ . '/../../Classes/Utils.php';
require_once __DIR__ . '/../../Classes/ClassStudioDateRegular.php';
require_once __DIR__ . '/../../Classes/MeetingStaffRuleAvailability.php';
require_once __DIR__ . '/../../../app/enums/ClassStudioDate/MeetingStatus.php';
require_once __DIR__ . '/EditMeetingService.php';
require_once __DIR__ . '/../ClientService.php';


class CreateMeetingService extends Utils
{
    const ClassRepeatType = [
        'day' => 1,
        'week' => 2,
        'month' => 3,
    ];

    const REQUIRED_FIELDS = [
        'GuideId',
        'Floor',
        'StartDate'
    ];

    const PERMANENT_ASSIGNMENT_FIELDS = [
        'CompanyNum' => 'CompanyNum',
        'Dates' => 'Dates',
        'ClassTime' => 'StartTime',
        'Floor' => 'Floor',
        'ClassName' => 'ClassName',
        'ClassId' => 'ClassNameType',
        'GroupNumber' => 'GroupNumber',
        'UserId' => 'UserId',
        'RegularClassType' => 'ClassType',
        'StartDate' => 'StartDate',
    ];

    /**
     * @param array $data
     * @return array
     * @throws Exception
     */
    public static function create(array $data): array
    {
        $insertData = self::getAuthData();
        $data = array_merge($insertData, $data);

        $validation = self::validate($data);
        if ($validation->fails())
            return ['message' => $validation->messages()->first(), 'status' => self::ERROR_STATUS];

        foreach (self::REQUIRED_FIELDS as $field) {
            if (!isset($data[$field]))
                return ['message' => "Missing field: $field", 'status' => self::ERROR_STATUS];
            $insertData[$field] = $data[$field];
        }
        //todo-bp-909 (cart) remove-beta - remove this
        $betaCode = Settings::getBetaCode($insertData['CompanyNum']);
        $isBeta = in_array($betaCode, [1]);


        $MeetingGeneralSettings = MeetingGeneralSettings::getByCompanyNum($insertData['CompanyNum']);
        /** @var Section $Section */
        $Section = Section::where('id', $insertData['Floor'])->first();
        if ($Section->id) $insertData['Brands'] = $Section->Brands;
        /** @var Users $Guide */
        $Guide = Users::find($insertData['GuideId']);
        $insertData['GuideName'] = $Guide->display_name;

        $insertData = array_merge($insertData, self::getClassFrequencyType($data['freqType'] ?? null));
        $insertData = array_merge($insertData, self::getClassRepeat($data['ClassRepeat'] ?? null));
        $dateArr = self::getDateArr($insertData, $data['freqType'] ?? null, $data['regularEndDate'] ?? null);

        if (!isset($data['overrideLimitation'])) {
            $dataWarningsLimitation = [];
            foreach ($data['treatments'] as $treatment) {
                /** @var MeetingTemplates $templateData */
                $templateData = MeetingTemplates::find($treatment['templateId']);
                $insertData['StartTime'] = $treatment['time'];
                $insertData['EndTime'] = Utils::addInterval($insertData['StartTime'], '+' . $treatment['duration'] . ' minutes', 'H:i');

                $warningsLimitation = self::checkLimitation(
                    $dateArr,
                    array_merge(['CompanyNum' => $insertData['CompanyNum']], self::getTimeIncludingPreparation($insertData, $templateData)),
                    $Section,
                    $Guide,
                    $data['StartDate'],
                    $templateData->TemplateName,
                    $templateData->id,
                );
                if($warningsLimitation){
                    $dataWarningsLimitation[] = $warningsLimitation;
                }
            }
            if (count($dataWarningsLimitation) > 0)
                return ['status' => self::WARNING_STATUS, 'data' => $dataWarningsLimitation];
        }

        $fromCartFlag = isset($data['from_cart']) && $data['from_cart'];

        $restrictions = [];
        $createdClassIds = [];
        $createdGroupNumbers = [];
        foreach ($data['treatments'] as $treatment) {
            /** @var ClassesType $volumeData */
            $volumeData = ClassesType::find($treatment['classTypeId']);
            /** @var MeetingTemplates $templateData */
            $templateData = MeetingTemplates::find($volumeData->MeetingTemplateId);
            $insertData = array_merge($insertData, self::getDataFromClassType($volumeData));
            $insertData = array_merge($insertData, self::getDataFromTemplate($templateData));

            $insertData['GroupNumber'] = GroupNumberHelper::generate();
            $insertData['purchaseAmount'] = $treatment['cost'];
            $insertData['StartTime'] = $treatment['time'];
            $insertData['EndTime'] = Utils::addInterval($insertData['StartTime'], '+' . $treatment['duration'] . ' minutes', 'H:i');
            $insertData['DayNum'] = date('w', strtotime($insertData['StartDate']));
            $insertData['Day'] = Utils::numberToDay($insertData['DayNum']);

            foreach ($dateArr as $key => $value) {
                if ($key == 30)
                    break;

                $insertData['ClassCount'] = $key + 1;
                $insertData['StartDate'] = $value;
                $startDateTime = date('Y-m-d H:i:s', strtotime($insertData['StartDate'] . ' ' . $insertData['StartTime']));
                $insertData['EndDate'] = Utils::addInterval($startDateTime, '+' . $treatment['duration'] . ' minutes');

                $insertData['start_date'] = $insertData['StartDate'] . ' ' . $insertData['StartTime'];
                $insertData['end_date'] = $insertData['EndDate'] . ' ' . $insertData['EndTime'];
                $insertData['DayNum'] = date('w', strtotime($insertData['StartDate']));
                $insertData['Day'] = Utils::numberToDay($insertData['DayNum']);

                $insertData = array_merge($insertData, self::getDataFromMeetingGeneralSettings($MeetingGeneralSettings, $insertData['start_date']));

                $insertData['meetingStatus'] =
                    (!empty($treatment['membershipId']) || $fromCartFlag ) ? MeetingStatus::COMPLETED : MeetingStatus::ORDERED;
                // *** Database Insertion ***
                $newClassId = ClassStudioDate::insertGetId($insertData);

                if (empty($newClassId)) {
                    return ['message' => lang('action_not_done'), 'status' => self::ERROR_STATUS];
                }
                self::checkClassZoom($templateData, $newClassId);

                $createdClassIds[] = [
                    'classId' => $newClassId,
                    'groupNumber' => $insertData['GroupNumber'],
                    'className' => $insertData['ClassName'],
                    'classDate' => $insertData['StartDate'],
                    'classDay' => $insertData['Day'],
                    'classTypeId' => $treatment['classTypeId'],
                    'membershipId' => $treatment['membershipId'] ?? null,
                    'price' => $treatment['cost'] ?? null,
                    'DiscountType' => $data['DiscountType'] ?? 0,
                    'Discount' => $data['Discount'] ?? 0,
                    'DiscountAmount' => $data['DiscountAmount'] ?? 0,
                ];

                if ($key == 0 && $insertData['ClassType'] != 3) {
                    $createdGroupNumbers[] = [
                        'classData' => $insertData,
                        'endDate' => $insertData['ClassType'] == 2 ? end($dateArr) : null,
                        'membershipId' => $treatment['membershipId'] ?? null
                    ];
                }
            }
        }

        if (!empty($data['IsNew'])) {
            $name = $data['ClientName'] ?? $data['user'];
            $phone = $data['UserPhone'] ?? $data['user'];
            $res = ClientService::addClientByPhoneAndName($phone, $name, $insertData['Brands'] ?? 0);

            if ($res['Status'] == "Error") {
                return ['message' => $res['Message'], 'status' => self::ERROR_STATUS];
            }
            $data['ClientId'] = $res['Message']['client_id'];
        }

        $clientActivityIds = [];

        if (!empty($data['ClientId'])) {
            /** @var Client $Client */
            $Client = Client::find($data['ClientId']);
            if ($Client->isArchived() || $Client->isLead()) {
                ClientService::updateStatus($Client, Client::STATUS_ACTIVE);
            }
        } else {
            $Client = Client::getRandomClient($insertData['CompanyNum']);
            $data['ClientId'] = $Client->id;
        }

        if ($insertData['ClassType'] != 3) {
            $createdPermanentClassId = [];
            foreach ($createdGroupNumbers as $groupNumber) {
                $permanentAssignmentId = self::createPermanentAssignment($groupNumber, $Client->id);
                $createdPermanentClassId[$groupNumber['classData']['GroupNumber']] = $permanentAssignmentId;
            }
        }

        foreach ($createdClassIds as $assignData) {
            $insertData['id'] = $assignData['classId'];
            if (empty($assignData['membershipId'])) {
                $assignRes = ClientActivities::assignMembership([
                    "clientId" => $Client->id,
                    "itemId" => Item::getSingleClassItem($assignData['classTypeId']),
                    "activityName" =>
                        $assignData['className'] . ': ' . $assignData['classDay'] . ' ' .
                        date('d/m', strtotime($assignData['classDate'])),
                    "itemPrice" => $assignData['price'],
                    "isForMeeting" => 1,
                    "isDisplayed" => $fromCartFlag ? 1 : 0,
                    'DiscountType' => $assignData['DiscountType'],
                    'Discount' => $assignData['Discount'],
                    'DiscountAmount' => $assignData['DiscountAmount'],
                ]);

                if (empty($assignRes['Status']) || $assignRes['Status'] === 0) {
                    return ['message' => $assignRes['Error'], 'status' => self::ERROR_STATUS];
                }

                $meetingClient = new MeetingClient();
                $meetingClient->CompanyNum = $Client->CompanyNum;
                $meetingClient->ClientId = $Client->id;
                $meetingClient->GroupNumber = $assignData['groupNumber'];
                $meetingClient->save();

                $membershipId = $assignRes['ClientActivityId'];
            } else {
                $membershipId = $assignData['membershipId'];
                $relatedClientActivity = ClientActivities::find($membershipId);
                $restriction = $relatedClientActivity->checkMembershipLimitations($assignData['classId'], $Client->id, true);
                if ($restriction['Status'] != 1) {
                    $changed = false;
                    // get all matching subscriptions
                    $candidates = $Client->getMatchingActivitiesList($assignData['classTypeId']);
                    foreach ($candidates as $candidate) {
                        $candidateClientActivity = ClientActivities::find($candidate);
                        $tmpRestriction = $candidateClientActivity->checkMembershipLimitations($assignData['classId'], $Client->id, true);
                        if ($tmpRestriction['Status'] == 1) {
                            $membershipId = $candidate;
                            $changed = true;
                            break;
                        }
                    }

                    if (!$changed) {
                        $restrictions[] = ['classId' => $assignData['classId'], 'checkRes' => $restriction];
                    }
                }
            }

            $studioAct = ClassStudioAct::new(ClassStudioDate::find($assignData['classId']),
                [
                    'clientId' => $Client->id,
                    'activityId' => $membershipId,
                    'regularClassId' => $createdPermanentClassId[$assignData['groupNumber']] ?? null,
                ]
            );

            $clientActivityIds[] = $membershipId;

            $meetingGroupOrderId = MeetingGroupOrders::createMeetingGroupOrder($insertData['CompanyNum'], $Client->id, [$studioAct['actId']]);

            AppNotification::sendMeetingStatusUpdateToClient(
                $Client->id,
                $insertData['ClassName'],
                $insertData['start_date'],
                29,
                $meetingGroupOrderId
            );
        }
        if (!empty($data['Remarks'])) {
            Clientcrm::insertGetId([
                'CompanyNum' => $data['CompanyNum'],
                'ClientId' => $Client->id,
                'Remarks' => $data['Remarks'],
                'User' => $data['UserId'],
                'StarIcon' => 1
            ]);
        }

        $data = array_merge($data, $insertData);
        self::createLogRecord($data, $dateArr[0], end($dateArr));

        $clientActivityIds = implode(',', $clientActivityIds);


        return [
            'message' => lang('action_done'),
            'status' => self::SUCCESS_STATUS,
            'data' => [
                'isBeta' => $isBeta,//todo-bp-909 (cart) remove-beta - remove this
                'clientId' => $Client->id ?? 0,
                'clientActivityIds' => $clientActivityIds,
                'restrictions' => $restrictions,
            ]
        ];
    }

    /**
     * @param array $data
     * @return array
     * @throws Throwable
     */
    public static function fixMeetings(array $data): array
    {
        foreach ($data as $meeting) {
            EditMeetingService::unsubscribeFromSubscription($meeting['classId']);
        }
        return ['message' => lang('action_done'), 'status' => self::SUCCESS_STATUS];;
    }

    /**
     * @param array $data data from form
     * @param ClassStudioDate $meeting main edited meeting
     * @return array ['message', 'status']
     */
    public static function update(array $data, ClassStudioDate $meeting): array
    {
        if (self::dataIsEmpty($data))
            return ['message' => lang('no_field_updated'), 'status' => self::ERROR_STATUS];
        $originalMeeting = clone $meeting;
        // check if you need check limitation, check limitation
        if(self::needToCheckOccupation($data, $originalMeeting)) {
            $dataCheck['ClassType'] = $data['ClassType'] ?? $originalMeeting->ClassType;
            $dataCheck['ClassRepeat'] = $data['ClassRepeat'] ?? $originalMeeting->ClassRepeat;
            $dataCheck['ClassRepeatType'] = $data['ClassRepeatType'] ?? $originalMeeting->ClassRepeatType;
            $dataCheck['StartDate'] = $data['StartDate'] ?? $originalMeeting->StartDate;
            $dataCheck['freqType'] = $data['freqType'] ?? null;
            $dataCheck['regularEndDate'] = $data['regularEndDate'] ?? (ClassStudioDate::getLastClass($originalMeeting->GroupNumber, $originalMeeting->CompanyNum))->StartDate;
            $dateArrToCheck = self::getDateArr($dataCheck, $data['freqType'] ?? null, $data['regularEndDate'] ?? null);

            $existWarnings = self::checkLimitationForUpdate($dateArrToCheck, $data, $originalMeeting);
            if ($existWarnings) {
                return $existWarnings;
            }
        }
        self::setAttributes($meeting, self::getAuthData());
        $meeting->Change = 1;
        $MeetingGeneralSettings = MeetingGeneralSettings::getByCompanyNum($meeting->CompanyNum);

        // update act
        /** @var ClassStudioAct $StudioAct */
        $StudioAct = ClassStudioAct::getMeetingActByClassId($meeting->id);

        if (isset($data['GuideId'])) {
            $meeting->GuideId = $data['GuideId'];

            /** @var Users $User */
            $User = Users::find($meeting->GuideId);
            $meeting->GuideName = $User->display_name;
        }

        if (isset($data['Floor'])) {
            $meeting->Floor = $data['Floor'];

            /** @var Section $Section */
            $Section = Section::find($meeting->Floor);
            if ($Section)
                $meeting->Brands = $Section->Brands;
        }

        if (isset($data['StartTime'])) {
            $meeting->StartTime = $data['StartTime'];

            if ($StudioAct) {
                $StudioAct->ClassStartTime = $meeting->StartTime;
                $StudioAct->save();
            }
        }

        if (isset($data['StartTime']) || isset($data['duration'])) {
            $duration = $data['duration'] ??
                Utils::calcMinutesDiff($meeting->start_date, $meeting->end_date);
            $meeting->EndTime =
                Utils::addInterval($meeting->StartTime, "+$duration minutes", 'H:i');

            if ($StudioAct) {
                $StudioAct->ClassEndTime = $meeting->EndTime;
                $StudioAct->save();
            }
        }

        if (isset($data['StartDate'])) {
            $meeting->StartDate = $data['StartDate'];
            $duration = $duration ?? Utils::calcMinutesDiff($meeting->start_date, $meeting->end_date);
            $startDateTime = date('Y-m-d H:i:s', strtotime($meeting->StartDate . ' ' . $meeting->StartTime));
            $meeting->EndDate = Utils::addInterval($startDateTime, "+$duration minutes");

            // update day information
            $meeting->DayNum = date('w', strtotime($meeting->StartDate));
            $meeting->Day = Utils::numberToDay($meeting->DayNum);

            // update act
            if ($StudioAct) {
                $StudioAct->ClassDate = $meeting->StartDate;
                $StudioAct->DayNum = $meeting->DayNum;
                $StudioAct->Day = $meeting->Day;
                $StudioAct->save();
            }
        }

        if (isset($data['classTypeId'])) {
            /** @var ClassesType $ClassesType */
            $ClassesType = ClassesType::find($data['classTypeId']);
            /** @var MeetingTemplates $MeetingTemplate */
            $MeetingTemplate = MeetingTemplates::find($ClassesType->MeetingTemplateId);
            self::setAttributes($meeting, self::getDataFromClassType($ClassesType));
            self::setAttributes($meeting, self::getDataFromTemplate($MeetingTemplate));

            if ($StudioAct) {
                $StudioAct->ClassName = $MeetingTemplate->TemplateName;
                $StudioAct->save();
            }
        }

        if (isset($data['freqType'])) {
            if ($data['freqType'] === 'date' || $data['freqType'] > 2) {
                $meeting->ClassType = ClassStudioDate::CLASS_TYPE_EXPIRATION;
            } elseif ((int)$data['freqType'] === 0) {
                $meeting->ClassType = ClassStudioDate::CLASS_TYPE_PERMANENT;
            }
        }

        $oldClientId = $StudioAct->FixClientId ?? null;

        if (isset($data['user']) || isset($data['ChargeType'])) {
            if (isset($data['IsNew']) && (int)$data['IsNew'] === 1) {
                $name = $data['ClientName'] ?? $data['user'];
                $phone = $data['UserPhone'] ?? $data['user'];
                $res = ClientService::addClientByPhoneAndName($phone, $name, $meeting->Brands);

                if ($res['Status'] == "Error") {
                    return ['message' => $res['Message'], 'status' => self::ERROR_STATUS];
                }
                $clientId = $res['Message']['client_id'];
            } else {
                $clientId = $data['user'] ?? null;
            }

            $clientId = $clientId ?? $StudioAct->ClientId ?? null;
            if (isset($clientId)) {
                if ($StudioAct) {
                    $StudioAct->changeMeetingClient($clientId, $data['ChargeType'] ?? 0);
                } else {
                    $StudioAct = ClassStudioAct::createMeetingAct($meeting, $clientId, $data['ChargeType'] ?? 0);
                }
            }
        } else if (isset($data['StartDate']) || isset($data['classTypeId'])) {
            /** @var ClientActivities $ClientActivity */
            $ClientActivity = $StudioAct->clientActivity();
            if ($ClientActivity && $ClientActivity->isPaymentForSingleClass) {
                $ClientActivity->ItemText = $meeting->getSingleItemName();
                $ClientActivity->save();
            }
        }

        // after user change
        if (isset($data['cost'])) {
            $meeting->purchaseAmount = $data['cost'];

            if ($StudioAct) {
                /** @var ClientActivities $ClientActivity */
                $ClientActivity = $StudioAct->clientActivity();
                if ($ClientActivity)
                    self::updatePrice($ClientActivity, $data['cost']);
            }
        }

        if (isset($data['ClassRepeat'])) {
            if ((int)$data['ClassRepeat'] === 0)
                $meeting->ClassType = ClassStudioDate::CLASS_TYPE_SINGLE;
            self::setAttributes($meeting, self::getClassRepeat($data['ClassRepeat']));
        }

        /** Meeting updated fields */
        $updatedDataOriginal = $meeting->getDirty();
        $updatedData = $updatedDataOriginal;

        if ($StudioAct) {
            $meeting->start_date = $meeting->StartDate . ' ' . $meeting->StartTime;
            $meeting->end_date = $meeting->EndDate . ' ' . $meeting->EndTime;
            $meeting->save();
            // update in Google Calendar after all changes
            GoogleCalendarService::updateCreateIfVisible($StudioAct->id);
        }

        if (isset($data['GroupEdit']) && $data['GroupEdit'] == 1) { // Multi meeting edit
            /** @var ClassStudioDate[] $groupMeetings */
            $groupMeetings = ClassStudioDate::where('GroupNumber', $meeting->GroupNumber)
                ->where('StartDate', '>=', $originalMeeting->StartDate)
                ->orderBy('StartDate', 'asc')
                ->get();

            // check for change that require to update all group meetings dates
            if (self::needToUpdateDates($updatedData, $originalMeeting) ||
                (isset($data['regularEndDate']) && $data['regularEndDate'] != end($groupMeetings)->StartDate)) {
                $dateArr = self::getDateArr(
                    $meeting->toArray(),
                    $data['freqType'] ?? null,
                    $data['regularEndDate'] ?? null
                );

//                $occupation = self::checkOccupation(
//                    $dateArr,
//                    $meeting->toArray(),
//                    $Section ?? Section::find($meeting->Floor),
//                    $User ?? Users::find($meeting->GuideId)
//                );
//
//                if ($occupation && !isset($data['overrideOccupied'])) {
//                    $occupation['name'] =
//                        $meeting->ClassName . ' (' . $meeting->EndTime . '-' . $meeting->StartTime . ')';
//                    return ['status' => self::WARNING_STATUS, 'data' => [$occupation]];
//                }

                $duration = $duration ??
                    Utils::calcMinutesDiff($meeting->start_date, $meeting->end_date);
                //update existing meetings to new dates
                foreach ($groupMeetings as $key => $groupMeeting) {
                    // no update meeting if status completed
                    if(in_array($groupMeeting->meetingStatus, [MeetingStatus::COMPLETED, MeetingStatus::CANCELED, MeetingStatus::DIDNT_ATTEND])){
                        continue;
                    }
                    if (!isset($dateArr[$key]))
                        break;
                    if (self::meetingDateExistInGroup($groupMeetings, $dateArr[$key]))
                        continue;
                    $groupMeeting->start_date = $dateArr[$key] . ' ' . $meeting->StartTime;
                    $groupMeeting->end_date =
                        Utils::addInterval($groupMeeting->start_date, "+$duration minutes", 'Y-m-d H:i:s');
                    $groupMeeting->StartDate = date('Y-m-d', strtotime($groupMeeting->start_date));
                    $groupMeeting->EndDate = date('Y-m-d', strtotime($groupMeeting->end_date));
                    $reminderUpdate = self::getDataFromMeetingGeneralSettings($MeetingGeneralSettings, $groupMeeting->start_date);
                    $groupMeeting->SendReminder = $reminderUpdate['SendReminder'];
                    $groupMeeting->TypeReminder = $reminderUpdate['TypeReminder'];
                    $groupMeeting->TimeReminder = $reminderUpdate['TimeReminder'];
                    $groupMeeting->save();
                }

                $pastClassesCount = ClassStudioDate::getActiveClassCount(
                    $meeting->CompanyNum, $meeting->GroupNumber, $meeting->StartDate
                );

                $ClientActivity = $ClientActivity ?? ClientActivities::find($StudioAct->ClientActivitiesId);
                //update the rest of the meetings and create new ones if needed
                foreach ($dateArr as $key => $date) {
                    if ($key >= (30 - $pastClassesCount)) {
                        $dateArr = array_slice($dateArr, 0, $key);
                        break;
                    }

                    $updatedData['start_date'] = $date . ' ' . $meeting->StartTime;
                    $updatedData['end_date'] =
                        Utils::addInterval($updatedData['start_date'], "+$duration minutes", 'Y-m-d H:i:s');
                    $updatedData['StartDate'] = date('Y-m-d', strtotime($updatedData['start_date']));
                    $updatedData['EndDate'] = date('Y-m-d', strtotime($updatedData['end_date']));
                    $updatedData['ClassCount'] = $meeting->ClassCount + $key;
                    $updatedData = array_merge($updatedData, self::getDataFromMeetingGeneralSettings($MeetingGeneralSettings, $updatedData['start_date']));

                    $classExist = ClassStudioDate::getByGroupAndDate($meeting->CompanyNum, $meeting->GroupNumber, $date, true);
                    // no update meeting if status completed
                    if($classExist && in_array($classExist->meetingStatus, [MeetingStatus::COMPLETED, MeetingStatus::CANCELED, MeetingStatus::DIDNT_ATTEND])){
                        continue;
                    }
                    if ($classExist) {
                        ClassStudioDate::where('id', $classExist->id)->update($updatedData);
                        $classId = $classExist->id;
                    } else {
                        $meetingData = $meeting->toArray();
                        unset($meetingData['id']);
                        $classId = ClassStudioDate::insertGetId(array_merge($meetingData, $updatedData));
                        if ($classId) {

                            $newStudioAct = ClassStudioAct::createMeetingAct(
                                ClassStudioDate::find($classId),
                                $data['user'] ?? $StudioAct->ClientId,
                                $ClientActivity->isPaymentForSingleClass == 1 ? 0 : $ClientActivity->id
                            );

                            $meetingGroupOrderId = MeetingGroupOrders::createMeetingGroupOrder($StudioAct->CompanyNum, $StudioAct->ClientId, [$newStudioAct ?? $StudioAct->id]);
                            AppNotification::sendMeetingStatusUpdateToClient($StudioAct->ClientId, $StudioAct->ClassName, $updatedData['start_date'], 29, $meetingGroupOrderId);
                        }
                    }

                }
            } else {
                if (self::needToCheckOccupation($updatedData, $originalMeeting))
                    $checkOccupation = true;
            }

            $duration = $duration ??
                Utils::calcMinutesDiff($meeting->start_date, $meeting->end_date);

            $newClassDates = [];
            //update existing meetings, if there was change on dates - canceling the unneeded ones
            foreach ($groupMeetings as $groupMeeting) {
                // no update meeting if status completed
                if(in_array($groupMeeting->meetingStatus, [MeetingStatus::COMPLETED, MeetingStatus::CANCELED, MeetingStatus::DIDNT_ATTEND])){
                    continue;
                }
                $updatedData = $updatedDataOriginal;
                if (empty($updatedData))
                    break;

                unset($updatedData['StartDate'], $updatedData['EndDate'], $updatedData['ClassCount']);
                $updatedData['start_date'] = $groupMeeting->StartDate . ' ' . $meeting->StartTime;
                $updatedData['end_date'] =
                    Utils::addInterval($updatedData['start_date'], "+$duration minutes", 'Y-m-d H:i:s');
                $updatedData = array_merge($updatedData, self::getDataFromMeetingGeneralSettings($MeetingGeneralSettings, $updatedData['start_date']));

                $updatedData['Status'] = (isset($dateArr) && !in_array($groupMeeting->StartDate, $dateArr)) ?
                    ClassStudioDate::STATUS_CANCELLED : ClassStudioDate::STATUS_ACTIVE;

                if ($updatedData['Status'] == ClassStudioDate::STATUS_ACTIVE) {
                    $newClassDates[] = $groupMeeting->StartDate;
                } else {
                    $updatedData['meetingStatus'] = MeetingStatus::CANCELED;
                }

                ClassStudioDate::where('id', $groupMeeting->id)
                    ->where('CompanyNum', $meeting->CompanyNum)
                    ->update($updatedData);
                if ($updatedData['Status'] == ClassStudioDate::STATUS_CANCELLED) {
                    ClassStudioAct::cancelClassActs($groupMeeting->id);
                } else {
                    $groupMeetingAct = ClassStudioAct::getMeetingActByClassId($groupMeeting->id);
                    $clientId = $clientId ?? $groupMeetingAct->ClientId ?? null;
                    // enter this if only when user changed (if exist $data['user'])
                    if (isset($data['user']) && isset($clientId)) {
                        if ($groupMeetingAct) {
                            $groupMeetingAct->changeMeetingClient($clientId, $data['ChargeType'] ?? 0);
                            $meetingGroupOrdersId = null;
                        } else {
                            $groupMeetingAct = ClassStudioAct::createMeetingAct($groupMeeting, $clientId, $data['ChargeType'] ?? 0);
                            $meetingGroupOrdersId = MeetingGroupOrders::createMeetingGroupOrder($meeting->CompanyNum, $clientId, [$groupMeetingAct->id]);
                        }

                        $templateType = 29; // 29 = פרטי פגישה
                    }

                    if (
                        !empty($data['user'])
                        || !empty($data['StartTime'])
                        || (!empty($data['StartDate']) && $data['StartDate'] != $originalMeeting->StartDate)
                        || (!empty($data['classTypeId']) && $originalMeeting->ClassNameType != $data['classTypeId'])
                    ) {
                        AppNotification::sendMeetingStatusUpdateToClient(
                            $data['user'] ?? $clientId,
                            $groupMeeting->ClassName,
                            $updatedData['start_date'],
                            $templateType ?? 32, // 32 = שינוי פרטי הפגישה
                            $meetingGroupOrdersId ?? null
                        );
                    }

                    if ($groupMeetingAct && isset($data['cost'])) {
                        $groupMeetingClientActivity = $groupMeetingAct->clientActivity();
                        if ($groupMeetingClientActivity)
                            self::updatePrice($groupMeetingClientActivity, $data['cost']);
                    }
                }
            }
//            if (isset($checkOccupation) && isset($newClassDates)) {
//                $occupation = self::checkOccupation(
//                    $newClassDates,
//                    $meeting->toArray(),
//                    $Section ?? Section::find($meeting->Floor),
//                    $User ?? Users::find($meeting->GuideId)
//                );
//
//                if ($occupation && !isset($data['overrideOccupied'])) {
//                    $occupation['name'] =
//                        $meeting->ClassName . ' (' . $meeting->EndTime . '-' . $meeting->StartTime . ')';
//                    return ['status' => self::WARNING_STATUS, 'data' => [$occupation]];
//                }
//            }

            if (isset($clientId) && (isset($data['user']) || isset($data['ClassRepeat']))) {
                // check if exist regular for this meeting
                $existRegular = ClassStudioDateRegular::isExistsRegularForGroupNumberAndClientId($meeting->CompanyNum, $clientId, $meeting->GroupNumber);
                if (!$existRegular) { // if no exist regular, create regular for this meeting
                    $PermanentId = self::createPermanentAssignment([
                        'classData' => $meeting->toArray(),
                        'membershipId' => $data['ChargeType'] ?? null,
                        'endDate' => $meeting->ClassType == 2 ?
                            ClassStudioDate::getLastClass($meeting->GroupNumber, $meeting->CompanyNum)->StartDate : null,

                    ], $clientId);

                    foreach ($groupMeetings as $groupMeeting) {
                        $groupMeetingAct = ClassStudioAct::getMeetingActByClassId($groupMeeting->id);

                        if ($groupMeetingAct) {
                            $groupMeetingAct->RegularClass = 1;
                            $groupMeetingAct->RegularClassId = $PermanentId;
                            $groupMeetingAct->save();
                        }
                    }
                }
            }
        } else { // Single meeting edit
            if (array_key_exists('StartDate', $updatedData) || array_key_exists('StartTime', $updatedData) ||
                array_key_exists('EndDate', $updatedData) || array_key_exists('EndTime', $updatedData)) {
                $meeting->start_date = $meeting->StartDate . ' ' . $meeting->StartTime;
                $meeting->end_date = $meeting->EndDate . ' ' . $meeting->EndTime;
            }

            $meetingGroupOrderId = MeetingGroupOrders::createMeetingGroupOrder($StudioAct->CompanyNum, $StudioAct->FixClientId, [$StudioAct->id]);

            if (!empty($data['user']) && $oldClientId != $data['user']) {
                $templateType = 29;
            } elseif (!empty($updatedData['StartDate']) && ($updatedData['StartDate'] != $originalMeeting->StartDate)
                || !empty($updatedData['StartTime']) && ($updatedData['StartTime'] != $originalMeeting->StartTime)
                || !empty($updatedData['ClassNameType']) && ($updatedData['ClassNameType'] != $originalMeeting->ClassNameType)) {
                $templateType = 32;
            }

            if (!empty($templateType)) {
                AppNotification::sendMeetingStatusUpdateToClient(
                    $data['user'] ?? $StudioAct->ClientId,
                    $meeting->ClassName,
                    $meeting->StartDate . ' ' . $meeting->StartTime,
                    $templateType,
                    $meetingGroupOrderId
                );
            }

            $MeetingGeneralSettings = MeetingGeneralSettings::getByCompanyNum($meeting->CompanyNum);
            $reminderUpdate = self::getDataFromMeetingGeneralSettings($MeetingGeneralSettings, $meeting->start_date);
            $meeting->SendReminder = $reminderUpdate['SendReminder'];
            $meeting->TypeReminder = $reminderUpdate['TypeReminder'];
            $meeting->TimeReminder = $reminderUpdate['TimeReminder'];
            $meeting->save();
        }
        return ['message' => lang('action_done'), 'status' => self::SUCCESS_STATUS];
    }

    /**
     * @param array $updatedFields
     * @param ClassStudioDate $originalMeeting
     * @return bool
     */
    private static function needToUpdateDates(array $updatedFields, ClassStudioDate $originalMeeting): bool
    {
        $fieldsToCheck = ['StartTime', 'ClassRepeat', 'ClassType', 'StartDate'];
        foreach ($fieldsToCheck as $field) {
            if (isset($updatedFields[$field]) && $updatedFields[$field] != $originalMeeting->$field) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param ClassStudioDate[] $StudioDates
     * @param string $StartDate
     * @return bool
     */
    private static function meetingDateExistInGroup(array $StudioDates, string $StartDate): bool
    {
        $exist = false;
        foreach ($StudioDates as $StudioDate) {
            if ($StudioDate->StartDate == $StartDate) {
                return true;
            }
        }
        return false;
    }

    private static function needToCheckOccupation(array $updatedFields, ClassStudioDate $originalMeeting): bool
    {
        $fieldsToCheck = ['StartTime', 'EndTime', 'Floor', 'GuideId', 'meetingTemplateId', 'ClassType', 'ClassRepeat', 'ClassRepeatType', 'StartDate', 'freqType', 'regularEndDate'];
        foreach ($fieldsToCheck as $field) {
            if (isset($updatedFields[$field]) && $updatedFields[$field] != $originalMeeting->$field) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if got at least one field to update
     * @param array $data
     * @return bool
     */
    private static function dataIsEmpty(array $data): bool
    {
        if (empty($data))
            return true;
        if (count($data) == 1 && (isset($data['id']) || isset($data['GroupEdit'])))
            return true;
        if (count($data) == 2 && isset($data['id']) && isset($data['GroupEdit']))
            return true;
        else return false;
    }

    /**
     * @param ClassStudioDate $meeting
     * @param array $attributes
     */
    private static function setAttributes(ClassStudioDate $meeting, array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $meeting->$key = $value;
        }
    }

    private static function updatePrice(ClientActivities $ClientActivity, $cost)
    {
        if ($ClientActivity->isPaymentForSingleClass) {
            if ($ClientActivity->ItemPrice === $ClientActivity->BalanceMoney)
                $ClientActivity->BalanceMoney = $cost;
            $ClientActivity->ItemPrice = $cost;
            $ClientActivity->save();
        }
    }

    /**
     * @param $data
     * @param $firstClassDate
     * @param $lastClassDate
     * @return void
     */
    private static function createLogRecord($data, $firstClassDate, $lastClassDate)
    {
        $message = lang('the_meeting') . ' ' . $data['ClassName'] . ' ' . lang('has_been_created');
        switch ($data['ClassType']) {
            case 1:
                $message = ' ' . lang('short_from') . ' ' . $firstClassDate;
                break;
            case 2:
                $message = ' ' . lang('short_from') . ' ' . $firstClassDate . ' ' . lang('until') . ' ' . $lastClassDate;
                break;
            case 3:
                $message .= ' ' . lang('in_date_cron') . ' ' . $firstClassDate;
                break;
        }
        createLogMovement($message, $data['ClientId'] ?? null);
    }

    /**
     * @param $data
     * @param $clientId
     * @return mixed
     */
    private static function createPermanentAssignment($data, $clientId)
    {
        $insertData = [];
        foreach (self::PERMANENT_ASSIGNMENT_FIELDS as $dbName => $fieldName) {
            $insertData[$dbName] = $data['classData'][$fieldName];
        }
        $insertData['EndDate'] = ($data['classData']['ClassType'] == 2) ? $data['endDate'] : null;
        $insertData['ClientId'] = $clientId;
        $insertData['ClientActivitiesId'] = $data['membershipId'] ?? '';
        $insertData['MembershipType'] = 0;
        $insertData['StatusType'] = 12;

        return ClassStudioDateRegular::insertGetId($insertData);
    }

    /**
     * @param $data array
     * @param $templateObj MeetingTemplates
     * @return array|void
     */
    private static function getTimeIncludingPreparation(array $data, MeetingTemplates $templateObj)
    {
        switch ($templateObj->PreparationTimeStatus) {
            case '0':
                return [
                    'StartTime' => $data['StartTime'],
                    'EndTime' => $data['EndTime']
                ];
            case '2':
                return [
                    'StartTime' => $data['StartTime'],
                    'EndTime' => Utils::addInterval(
                        $data['StartTime'],
                        $templateObj->PreparationTimeValue . ' ' .
                        MeetingTemplates::PreparationTimeType[$templateObj->PreparationTimeType],
                        'H:i'
                    )
                ];
            case '1':
                return [
                    'StartTime' => Utils::addInterval(
                        $data['StartTime'],
                        '-' . $templateObj->PreparationTimeValue . ' ' .
                        MeetingTemplates::PreparationTimeType[$templateObj->PreparationTimeType],
                        'H:i'
                    ),
                    'EndTime' => $data['EndTime']
                ];
        }
    }

    /**
     * @param MeetingTemplates $templateObj
     * @return array
     */
    private static function getDataFromTemplate(MeetingTemplates $templateObj): array
    {
        $insertData = [];
        $insertData['GenderLimit'] = $templateObj->RegistrationLimitedTo;
        $insertData['ClassName'] = $templateObj->TemplateName;
        $insertData['color'] = (Color::find($templateObj->ColorId))->hex;
        $insertData['MaxClient'] = 1;
        $insertData['purchaseLocation'] = ($templateObj->ExternalRegistration == 1) ? 3 : 0;
        $insertData['ShowApp'] = 2;
        $insertData['Remarks'] = $templateObj->MoreInfoText;
        $insertData['RemarksStatus'] = empty($templateObj->MoreInfoText) ?
            ClassStudioDate::REMARKS_STATUS_OFF : ClassStudioDate::REMARKS_STATUS_ON;

        if ($templateObj->MeetingType == 1) {
            $insertData['liveClassLink'] = $templateObj->LiveClassLink;
            $insertData['onlineClassId'] = ClassOnline::insertGetId([
                'sendType' => $templateObj->OnlineSendType, //verify values
                'sendTime' => $templateObj->OnlineReminderValue,
                'sendTimeType' => $templateObj->OnlineReminderType == 1 ? 2 : 1,
            ]);
        } else if ($templateObj->MeetingType == 2) {
            $insertData['is_zoom_class'] = 1;
        }
        return $insertData;
    }

    /**
     * @param MeetingTemplates $templateObj
     * @param $classId
     * @return void
     */
    private static function checkClassZoom(MeetingTemplates $templateObj, $classId): void
    {
        if ((int)$templateObj->MeetingType === 2 && $classId) {
            ClassStudioDate::insertIntoClass_zoom($classId, [
                'meetingNumber' => $templateObj->__get('ZoomMeetingNumber'),
                'ZoomPassword' => $templateObj->__get('ZoomMeetingPassword')
            ]);
        }
    }

    /**
     * @param ClassesType $classesType
     * @return array
     */
    private static function getDataFromClassType(ClassesType $classesType): array
    {
        $insertData = [];
        $insertData['text'] = $classesType->Type;
        $insertData['ClassNameType'] = $classesType->id;
        $insertData['meetingTemplateId'] = $classesType->MeetingTemplateId;
        return $insertData;
    }

    /**
     * Check if there is event in the same time for calendar
     * @param $dateArr array
     * @param $insertData array
     * @param $Section Section
     * @return array|null
     */
    private static function checkOccupationSection(array $dateArr, array $insertData, Section $Section): ?array
    {
        $isOccupied = $Section->getOccupied(
            $dateArr, $insertData['StartTime'], $insertData['EndTime'], $insertData['GroupNumber'] ?? null
        );
        if (count($isOccupied) > 0) {
            return [
                'data' => $isOccupied,
                'message' => lang('the_section') . ' ' . $Section->Title . ' ' . lang('is_occupied_in'),
            ];
        }
        return null;
    }

    /**
     * Check if there is event in the same time for guide
     * @param $dateArr array
     * @param $insertData array
     * @param $Guide Users
     * @return array|null
     */
    private static function checkOccupationGuide(array $dateArr, array $insertData, Users $Guide): ?array
    {

        /** @var ClassSettings $ClassSettings */
        $ClassSettings = $ClassSettings ?? ClassSettings::where('CompanyNum', $Guide->CompanyNum)->first();
        if ((int)$ClassSettings->GuideCheck === 1) {
            $isOccupied = $Guide->getOccupied(
                $dateArr, $insertData['StartTime'], $insertData['EndTime'], $insertData['GroupNumber'] ?? null
            );
            if (count($isOccupied) > 0) {
                return [
                    'data' => $isOccupied,
                    'message' => lang('the_guide') . ' ' . $Guide->display_name . ' ' . lang('is_occupied_in'),
                ];
            }
        }
        return null;
    }

    /**
     * Check if there is event in the same time for calendar
     * @param $dateArr array
     * @param $insertData array
     * @param Users $Guide
     * @return array|null
     */
    private static function checkCoachAvailability(array $dateArr, array $insertData, Users $Guide): ?array
    {
        $isNoCoachAvailability = MeetingStaffRuleAvailability::isAvailabilityCoach($dateArr, $insertData['StartTime'], $insertData['EndTime'], $Guide->id);
        if (count($isNoCoachAvailability) > 0) {
            return [
                'data' => $isNoCoachAvailability,
                'message' => lang('the_guide') . ' ' . $Guide->display_name . ' ' . lang('warning_coach_not_availability_time'),
            ];
        }
        return null;
    }

    /**
     * Check if there is event in the same time for calendar
     * @param $dateArr array
     * @param string $meetingTemplateId
     * @param bool $isUpdateMeeting
     * @return array|null
     */
    private static function checkPassMaxMeeting(array $dateArr, string $meetingTemplateId, bool $isUpdateMeeting): ?array
    {
        // check SessionsLimit
        $checkMaxOfMeeting = ClassStudioDate::isExistMaxOfMeeting($dateArr, $meetingTemplateId, $isUpdateMeeting) ?? 0;

        if (count($checkMaxOfMeeting) > 0) {
            return [
                'data' => $checkMaxOfMeeting,
                'message' => lang('warning_meeting_over_max_for_day'),
            ];
        }
        return null;
    }

    /**
     * The function check limitation of meeting, max of day, availability coach, if guide occupied and if section occupied
     * @param array $newClassDates
     * @param array $arrayOfMeeting
     * @param Section $Section
     * @param Users $User
     * @param string|null $startDate
     * @param string|null $name
     * @param string|null $meetingTemplateId
     * @param string|null $guideId
     * @param bool $isUpdateMeeting
     * @return array|null
     */
    private static function checkLimitation(array   $newClassDates,
                                            array   $arrayOfMeeting,
                                            Section $Section,
                                            Users   $User,
                                            string  $startDate = null,
                                            string  $name = null,
                                            string  $meetingTemplateId = null,
                                            bool $isUpdateMeeting = false): ?array
    {
        try {
            $startDate = $startDate ?? $arrayOfMeeting['StartDate'] ?? null;
            $name = $name ?? $arrayOfMeeting['ClassName'] ?? null;
            $meetingTemplateId = $meetingTemplateId ?? $arrayOfMeeting['ClassName'] ?? null;
            $meetingTemplateId = $meetingTemplateId ?? $arrayOfMeeting['meetingTemplateId'] ?? null;
            $startTime = $arrayOfMeeting['StartTime'] ?? null;
            $endTime = $arrayOfMeeting['EndTime'] ?? null;

            if(!$startDate || !$name || !$meetingTemplateId || !$startTime || !$endTime){
                throw new Exception('missing data to checkLimitation');
            }
            $warningLimitation = [];

            // check SessionsLimit
            $checkPassMaxMeeting = self::checkPassMaxMeeting($newClassDates,
                $meetingTemplateId,
                $isUpdateMeeting
            );
            if ($checkPassMaxMeeting) {
                $warningLimitation[] = $checkPassMaxMeeting;
            }

            // check coach availability
            $checkAvailability = self::checkCoachAvailability($newClassDates,
                $arrayOfMeeting,
                $User
            );
            if ($checkAvailability) {
                $warningLimitation[] = $checkAvailability;
            }

            // check occupation section
            $occupationTestSection = self::checkOccupationSection(
                $newClassDates,
                $arrayOfMeeting,
                $Section
            );
            if ($occupationTestSection) {
                $warningLimitation[] = $occupationTestSection;
            }

            // check occupation guide
            $occupationTestGuide = self::checkOccupationGuide(
                $newClassDates,
                $arrayOfMeeting,
                $User
            );
            if ($occupationTestGuide) {
                $warningLimitation[] = $occupationTestGuide;
            }

            if (count($warningLimitation) > 0) {
                return array('warnings' => $warningLimitation,
                    'name' => $name . ' (' . $endTime . '-' . $startTime . ')');
            }
        } catch (Exception $error){
            LoggerService::error($error, LoggerService::CATEGORY_COMMON);
        }
        return null;
    }

    /**
     * The function check limitation for update meeting
     * @param array $dateArr
     * @param array $data
     * @param ClassStudioDate $originalMeeting
     * @return array|null
     */
    public static function checkLimitationForUpdate(array $dateArr, array $data, ClassStudioDate $originalMeeting): ?array
    {
        try {
            $floor = $data['Floor'] ?? $originalMeeting->Floor;
            $Section = $Section ?? Section::find($floor);
            $guideId = $data['GuideId'] ?? $originalMeeting->GuideId;
            $User = $User ?? Users::find($guideId);
            $startDate = $data['StartDate'] ?? $originalMeeting->StartDate;
            $name = $data['ClassName'] ?? null;
            $meetingTemplateId = $data['meetingTemplateId'] ?? null;
            $arrayToCheck = $originalMeeting->toArray();
            $arrayToCheck['StartDate'] = $data['StartDate'] ?? $originalMeeting->StartDate;
            $arrayToCheck['StartTime'] = $data['StartTime'] ?? $originalMeeting->StartTime;
            $duration = $data['duration'] ??
                Utils::calcMinutesDiff($originalMeeting->start_date, $originalMeeting->end_date);
            $arrayToCheck['EndTime'] = $duration ? Utils::addInterval($arrayToCheck['StartTime'], '+' . $duration . ' minutes', 'H:i') : $originalMeeting->StartTime;

            if (!isset($data['overrideLimitation'])) {
                $dataWarningLimitation = self::checkLimitation(
                    $dateArr,
                    $arrayToCheck,
                    $Section,
                    $User,
                    $startDate,
                    $name,
                    $meetingTemplateId,
                    true
                );
                if ($dataWarningLimitation) {
                    return ['status' => self::WARNING_STATUS, 'data' => [$dataWarningLimitation]];
                }
            }
        } catch (Exception $error){
            LoggerService::error($error, LoggerService::CATEGORY_COMMON);
        }
        return null;
    }

    /**
     * @param $insertData array of data to insert
     * @param $freqType int|string 'date' of number of occurences
     * @param $until string (optional) end date in case of date
     * @return array of date range
     */
    private static function getDateArr(array $insertData, $freqType = null, $until = null): array
    {
        switch ($insertData['ClassType']) {
            case '1':
                $freqType = 30;
            case '2':
                $classRepeatType = array_search($insertData['ClassRepeatType'], self::ClassRepeatType);
                $StartDate = date('Y-m-d', strtotime($insertData['StartDate']));
                if ((isset($freqType) && $freqType == 'date') || (empty($freqType) && !empty($until))) {
                    $EndDate = $until ?? date('Y-m-d');
                } else {
                    $EndDate = Utils::addInterval($StartDate, '+' . (($freqType - 1) * $insertData['ClassRepeat']) . ' ' . $classRepeatType);
                }
                return Utils::createDateRange($StartDate, $EndDate, '+' . $insertData['ClassRepeat'] . ' ' . $classRepeatType);
            case '3':
                return [$insertData['StartDate']];
        }
        return [];
    }

    /**
     * @param $ClassRepeat
     * @return array
     */
    private static function getClassRepeat($ClassRepeat = null): array
    {
        $ClassRepeat = explode(' ', $ClassRepeat);
        if (count($ClassRepeat) > 1) {
            foreach (self::ClassRepeatType as $key => $value) {
                if ($ClassRepeat[1] == $key)
                    return ['ClassRepeatType' => $value, 'ClassRepeat' => $ClassRepeat[0]];

            }
        }
        return [];
    }

    /**
     * @param $freqType
     * @return int[]
     */
    private static function getClassFrequencyType($freqType = null): array
    {
        if ($freqType == null)
            $ClassType = 3;
        else if ($freqType == '0')
            $ClassType = 1;
        else
            $ClassType = 2;
        return ['ClassType' => $ClassType];
    }

    /**
     * @return array
     */
    public static function getAuthData(): array
    {
        $user = Auth::user();
        return [
            'UserId' => $user->__get('id'),
            'CompanyNum' => $user->__get('CompanyNum'),
            'Dates' => date('Y-m-d H:i:s'),
        ];
    }

    /**
     * @param $data
     * @return mixed
     */
    public static function validate($data)
    {
        $validationArr = [
//            'ClassRepeat' => 'required',
            'treatments' => 'required|array',
            'UserPhone' => 'required_if:IsNew,1',
        ];
        foreach (self::REQUIRED_FIELDS as $field)
            $validationArr[$field] = "required";

        return Validator::make($data, $validationArr);
    }

    /**
     * @param MeetingGeneralSettings $MeetingGeneralSettings
     * @param string $startFullTime
     * @return array
     */
    private static function getDataFromMeetingGeneralSettings(MeetingGeneralSettings $MeetingGeneralSettings, string $startFullTime): array
    {
        $timeReminderBeforeMinute = TimeHelper::returnMinuteTime($MeetingGeneralSettings->TimeReminder, $MeetingGeneralSettings->TypeReminder, 1);
        $fullTimeReminder = Utils::addInterval($startFullTime, '-' . $timeReminderBeforeMinute . ' minutes', 'Y-m-d H:i');
        $dateReminder = date('Y-m-d', strtotime($fullTimeReminder));
        $timeReminder = date('H:i', strtotime($fullTimeReminder));

        return [
            'SendReminder' => (int)$MeetingGeneralSettings->SendReminder === MeetingGeneralSettings::SEND_REMINDER_ON ?
                ClassStudioDate::SEND_REMINDER_ON : ClassStudioDate::SEND_REMINDER_OFF,
            'TypeReminder' => $dateReminder === date('Y-m-d', strtotime($startFullTime)) ?
                ClassStudioDate::TYPE_REMINDER_SAME_DAY : ClassStudioDate::TYPE_REMINDER_DAY_BEFORE, //checking same day or not
            'TimeReminder' => $timeReminder
        ];

    }
}