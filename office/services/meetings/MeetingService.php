<?php

require_once __DIR__ . '/../LoggerService.php';
require_once __DIR__ . '/../../Classes/Brand.php';
require_once __DIR__ . '/../../Classes/ClassStudioAct.php';
require_once __DIR__ . '/../../Classes/Settings.php';
require_once __DIR__ . '/../../Classes/ClassStudioDate.php';
require_once __DIR__ . '/../../Classes/Client.php';
require_once __DIR__ . '/../../Classes/ClientActivities.php';
require_once __DIR__ . '/../../Classes/Clientcrm.php';
require_once __DIR__ . '/../../Classes/ClientMedical.php';
require_once __DIR__ . '/../../Classes/Company.php';
require_once __DIR__ . '/../../Classes/DocsClientActivities.php';
require_once __DIR__ . '/../../Classes/MeetingGeneralSettings.php';
require_once __DIR__ . '/../../Classes/MeetingGroupOrdersToAct.php';
require_once __DIR__ . '/../../Classes/MeetingTemplates.php';
require_once __DIR__ . '/../../Classes/MeetingCancellationPolicy.php';
require_once __DIR__ . '/../../Classes/Section.php';
require_once __DIR__ . '/../../Classes/Users.php';
require_once __DIR__ . '/../../Classes/Utils.php';
require_once __DIR__ . '/../../../app/enums/ClassStudioDate/MeetingStatus.php';
require_once __DIR__ . '/../../../app/helpers/PhoneHelper.php';

class MeetingService
{
    public const TYPE_ALL = 'all';
    public const TYPE_OPENED = 'opened';
    public const TYPE_NOT_APPROVED = 'notApproved';

    public const LIMIT_MEETINGS_PER_PAGE = 10;

    /**
     * @param $companyNum
     * @param $dateFrom
     * @param $dateTo
     * @return array
     */
    public static function getMeetings4Report($companyNum, $dateFrom, $dateTo, $filter = 0)
    {
        if (empty($companyNum)) return [];

        $timeType = $filter == 0 ? ClassStudioDate::getTable() . '.StartDate' : ClassStudioAct::getTable() . '.Dates';

        $Meetings = ClassStudioDate::select(
            ClassStudioDate::getTable() . '.id',
            Client::getTable() . '.CompanyName as ClientName',
            ClassStudioAct::getTable() . ".ClientId",
            ClassStudioDate::getTable() . '.ClassName',
            ClassStudioDate::getTable() . '.StartDate',
            ClassStudioDate::getTable() . '.StartTime',
            ClassStudioDate::getTable() . '.start_date',
            ClassStudioDate::getTable() . '.end_date',
            ClassStudioDate::getTable() . '.purchaseAmount AS Price',
            ClassStudioDate::getTable() . '.meetingStatus',
            ClassStudioAct::getTable() . '.Dates',
            Users::getTable() . '.display_name AS CoachName',
            ClientActivities::getTable() . ".BalanceMoney AS Balance",
            ClientActivities::getTable() . ".isPaymentForSingleClass",
            ClientActivities::getTable() . ".ItemPrice")
            ->join(Users::getTable(), ClassStudioDate::getTable() . '.GuideId', '=', Users::getTable() . '.id')
            ->join(ClassStudioAct::getTable(), ClassStudioAct::getTable() . ".ClassId", '=', ClassStudioDate::getTable() . '.id')
            ->join(Client::getTable(), ClassStudioAct::getTable() . ".ClientId", '=', Client::getTable() . '.id')
            ->join(ClientActivities::getTable(), ClassStudioAct::getTable() . ".ClientActivitiesId", '=', ClientActivities::getTable() . '.id')
            ->where(ClassStudioDate::getTable() . '.CompanyNum', '=', $companyNum)
            ->whereIn(ClassStudioAct::getTable() . '.Status', [1, 2, 3, 4, 5, 6, 7, 8, 10, 11, 12, 15, 16, 17, 21])
            ->whereIn(ClassStudioDate::getTable() . '.meetingStatus', [
                MeetingStatus::WAITING,
                MeetingStatus::ORDERED,
                MeetingStatus::STARTED,
                MeetingStatus::COMPLETED,
                MeetingStatus::DIDNT_ATTEND,
                MeetingStatus::DONE,
                MeetingStatus::CANCELED
            ])
            ->whereNotNull(ClassStudioDate::getTable() . '.meetingTemplateId')
            ->whereBetween($timeType, [$dateFrom, $dateTo])
            ->orderBy(ClassStudioDate::getTable() . '.StartTime', 'asc')
            ->get();

        $res = [];

        // post-processing + converting to array
        foreach ($Meetings as $meeting) {
            $startTS = strtotime($meeting->start_date);
            $endTS = strtotime($meeting->end_date);

            $meetingArray = [];
            $meetingArray[0] = $meeting->id;
            $meetingArray[1] = '<a href="/office/ClientProfile.php?u=' . $meeting->ClientId . '">' . $meeting->ClientName . '</a>';
            $meetingArray[2] = $meeting->ClassName;
            $meetingArray[3] = date('d/m/y H:i', strtotime($meeting->Dates));
            $meetingArray[4] = date('d/m/y H:i', strtotime($meeting->start_date));
            $meetingArray[5] = ($endTS - $startTS) / 60;    // in minutes
            $meetingArray[6] = $meeting->isPaymentForSingleClass == 1 ? $meeting->ItemPrice : $meeting->Price;
            $meetingArray[7] = MeetingStatus::name($meeting->meetingStatus);
            $meetingArray[8] = $meeting->CoachName;
            $meetingArray[9] = $meeting->isPaymentForSingleClass == 1 ? $meeting->Balance : 0;

            $res[] = $meetingArray;
        }

        return $res;
    }

    /**
     * Get customer icons
     *
     * @param int $id
     * @param array $customer
     *
     */
    public static function getCustomerIcons(int $id, array &$customer)
    {
        $studioAct = ClassStudioAct::getMeetingActByClassId($id);
        if (!isset($studioAct)) {
            return;
        }

        $customerId = $studioAct->ClientId;
        $client = Client::find($customerId);

        /** @var ClientActivities $clientActivity */
        $clientActivity = ClientActivities::find($studioAct->ClientActivitiesId);

        if ((new ClientMedical())->getAllMedicalByClientId($studioAct->CompanyNum, $studioAct->ClientId)) {
            $customer["medical"] = true;
        }
        if ((new Clientcrm())->getAllClientcrmByClientId($studioAct->CompanyNum, $studioAct->ClientId)) {
            $customer['crm'] = true;
        }
        if ($studioAct->isFirstLesson($studioAct->CompanyNum, $studioAct->ClassDate, $studioAct->FixClientId)) {
            $customer['is_first'] = true;
        } elseif ($clientActivity && $clientActivity->Department == 3) {
            $customer['try_membership'] = true;
        }
        if ($client->Dob && $client->Dob != '0000-00-00'
            && date('m-d', strtotime($client->Dob)) === date('m-d')) {
            $customer['has_birthday'] = true;
        }
        if ($studioAct->RegularClass == 1 && $studioAct->RegularClassId != 0) {
            $customer['regular_assignment'] = true;
        }
        if (Company::getInstance()->greenPass) {
            $customer['greenpass'] = $client->getGreenPassIcon();
        }
        if ($clientActivity && $clientActivity->TrueClientId != 0) {
            $customer['family_membership'] = true;
        }
    }

    /**
     * Collect all meetings for company by type, page and limit
     *
     * @param $CompanyNum
     * @param string $type
     * @param $limit
     * @param $lastDate
     * @param bool $withSettings
     * @return array
     */
    public static function getMeetingsData($CompanyNum, string $type = self::TYPE_ALL, $limit = null, $lastDate = null, bool $withSettings = false)
    {
        $meetingTypes = [];
        if ($type === self::TYPE_ALL) {
            $meetingTypes = [
                self::TYPE_OPENED,
                self::TYPE_NOT_APPROVED,
            ];
        } elseif ($type === self::TYPE_OPENED) {
            $meetingTypes = [
                self::TYPE_OPENED,
            ];
        } elseif ($type === self::TYPE_NOT_APPROVED) {
            $meetingTypes = [
                self::TYPE_NOT_APPROVED,
            ];
        }

        if ($limit === null) {
            $limit = self::LIMIT_MEETINGS_PER_PAGE;
        }
        foreach ($meetingTypes as $meetingType) {
            $result[$meetingType] = self::getMeetingsInfo(self::getMeetingsByType($CompanyNum, $meetingType, $lastDate, $limit), $meetingType);
            $result[$meetingType . 'MaxCount'] = self::getMeetingsCountByType($CompanyNum, $meetingType);
        }

        $meetingSettings = MeetingGeneralSettings::getByCompanyNum($CompanyNum);
        if (!$meetingSettings) {
            throw new LogicException('Meeting settings not found');
        }

        $result['studioAutoApproval'] = $meetingSettings->AutoApproval;

        if ($withSettings) {
            $result['MeetingSettings'] = $meetingSettings;
        }

        $result['MeetingStatuses'] = MeetingStatus::toList();

        return $result;
    }

    /**
     * @param $type
     * @return array
     */
    public static function getStatusesByType($type): array
    {
        switch ($type) {
            case self::TYPE_NOT_APPROVED:
                $statusList = [MeetingStatus::WAITING];
                break;
            case self::TYPE_OPENED:
                $statusList = [MeetingStatus::ORDERED];
                break;
            default:
                $statusList = [MeetingStatus::WAITING, MeetingStatus::ORDERED];
        }
        return $statusList;
    }

    /**
     * Returns meetings list by type, last date and company number
     *
     * @param $CompanyNum
     * @param $type
     * @param null $lastDate
     * @param null $limit
     * @param bool $onlyCount
     * @return ClassStudioDate[]|int
     */
    public static function getMeetingsByType($CompanyNum, $type, $lastDate = null, $limit = null, bool $onlyCount = false)
    {
        $statusList = self::getStatusesByType($type);

        $queryMeetings = ClassStudioDate::select(['sd.*', 'gr.MeetingGroupOrdersId'])
            ->from(ClassStudioDate::getTable() . ' as sd')
            ->join(ClassStudioAct::getTable() . ' as act', 'act.ClassId', '=', 'sd.id')
            ->join(MeetingGroupOrdersToAct::getTable() . ' as gr', 'gr.ClassStudioActId', '=', 'act.id', 'left')
            ->where('sd.CompanyNum', $CompanyNum)
            ->where('sd.Status', ClassStudioDate::STATUS_ACTIVE)
            ->whereIn('sd.meetingStatus', $statusList);
        if ($type === self::TYPE_NOT_APPROVED) {
            $order = 'asc';

            if ($lastDate !== null) {
                $queryMeetings->where('sd.StartDate', '>', $lastDate);
            }
        } else {
            $order = 'desc';

            $queryMeetings->where('sd.start_date', '<', date('Y-m-d H:i:s'))
                ->whereIn('act.Status', [1,11]);

            if ($lastDate !== null) {
                $queryMeetings->where('sd.StartDate', '<', $lastDate);
            }
        }

        if ($limit !== null) {
            $queryMeetings->limit($limit);
        }

        $meetings = $queryMeetings->orderBy('sd.start_date', $order)->get();
        $meetingsIds = [];
        $groupsIds = [];
        foreach ($meetings as $meeting) {
            $meetingsIds[] = $meeting->id;
            $groupsIds[] = $meeting->MeetingGroupOrdersId;
        }

        if (empty($meetingsIds)) {
            if ($onlyCount) {
                return 0;
            }

            return [];
        }

        if ($type === self::TYPE_NOT_APPROVED) {
            $meetingsInTheSameGroup = ClassStudioDate::select('sd.id')
                ->from(ClassStudioDate::getTable() . ' as sd')
                ->join(ClassStudioAct::getTable() . ' as act', 'act.ClassId', '=', 'sd.id')
                ->join(MeetingGroupOrdersToAct::getTable() . ' as gr', 'gr.ClassStudioActId', '=', 'act.id')
                ->where('sd.CompanyNum', $CompanyNum)
                ->where('sd.Status', ClassStudioDate::STATUS_ACTIVE)
                ->whereIn('sd.meetingStatus', $statusList)
                ->whereIn('sd.MeetingGroupOrdersId', $groupsIds)
                ->column('id');

            $meetingsIds = array_merge($meetingsIds, $meetingsInTheSameGroup);
        } else {
            $lastDateInQuery = ClassStudioDate::where('id', end($meetingsIds))->pluck('StartDate');

            // get all meetings for the last date from array above
            $lastDateMeetings = ClassStudioDate::select('sd.*')
                ->from(ClassStudioDate::getTable() . ' as sd')
                ->join(ClassStudioAct::getTable() . ' as act', 'act.ClassId', '=', 'sd.id')
                ->where('sd.CompanyNum', $CompanyNum)
                ->where('sd.Status', ClassStudioDate::STATUS_ACTIVE)
                ->whereIn('sd.meetingStatus', $statusList)
                ->where('sd.StartDate', $lastDateInQuery)
                ->orderBy('sd.start_date', $order)
                ->column('id');

            $meetingsIds = array_merge($meetingsIds, $lastDateMeetings);
        }

        if ($onlyCount) {
            if ($type === self::TYPE_NOT_APPROVED) {
                $groupCount = ClassStudioDate::select('gr.MeetingGroupOrdersId')
                    ->from(ClassStudioDate::getTable() . ' as sd')
                    ->join(ClassStudioAct::getTable() . ' as act', 'act.ClassId', '=', 'sd.id')
                    ->join(MeetingGroupOrdersToAct::getTable() . ' as gr', 'gr.ClassStudioActId', '=', 'act.id')
                    ->whereIn('sd.id', $meetingsIds)
                    ->groupBy('gr.MeetingGroupOrdersId')
                    ->get();

                return count($groupCount);
            }

            return count($meetingsIds);
        }

        return ClassStudioDate::whereIn('id', $meetingsIds)->orderBy('start_date', $order)->get();
    }

    /**
     * @param $CompanyNum
     * @param $client
     * @return mixed
     */
    public static function getWaitingMeetingIdsByTypeAndClient($CompanyNum, $client)
    {
        $meetingsList = ClassStudioDate::select(ClassStudioDate::getTable() . '.id')
            ->join(ClassStudioAct::getTable(), ClassStudioAct::getTable() . '.ClassId', '=', ClassStudioDate::getTable() . '.id')
            ->whereIn(ClassStudioAct::getTable() . '.Status', [ClassStudioAct::STATUS_MEETING_ACTIVE, 11])
            ->where(ClassStudioAct::getTable() . '.ClientId', $client)
            ->where(ClassStudioDate::getTable() . '.CompanyNum', $CompanyNum)
            ->where(ClassStudioDate::getTable() . '.Status', ClassStudioDate::STATUS_ACTIVE)
            ->where(ClassStudioDate::getTable() . '.meetingStatus', MeetingStatus::WAITING)
            ->get();

        return $meetingsList;
    }

    /**
     *
     * @param $CompanyNum
     * @param $type
     * @return bool
     */
    public static function hasMeetingByStatus($CompanyNum, $type): bool
    {
        $queryMeetings = ClassStudioDate::select(['sd.*', 'gr.MeetingGroupOrdersId'])
            ->from(ClassStudioDate::getTable() . ' as sd')
            ->join(ClassStudioAct::getTable() . ' as act', 'act.ClassId', '=', 'sd.id')
            ->join(MeetingGroupOrdersToAct::getTable() . ' as gr', 'gr.ClassStudioActId', '=', 'act.id', 'left')
            ->where('sd.CompanyNum', $CompanyNum)
            ->where('sd.Status', ClassStudioDate::STATUS_ACTIVE)
            ->where('sd.meetingStatus', $type);
        //todo - We can later perform for additional meeting statuses
        switch ($type) {
            case MeetingStatus::WAITING:
                return $queryMeetings->exists();
            default:
                return $queryMeetings->where('start_date', '<', date('Y-m-d H:i:s')) //past
                ->exists();
        }
    }



    /**
     * @param $CompanyNum
     * @param $type
     * @return mixed
     */
    public static function getMeetingsCountByType($CompanyNum, $type): int
    {
        return self::getMeetingsByType($CompanyNum, $type, null, null, true);
    }

    /**
     * @param ClassStudioDate[] $meetingsList
     * @return array
     */
    public static function getMeetingsInfo(array $meetingsList, $meetingType)
    {
        $guides = Users::whereIn('id', array_column($meetingsList, 'GuideId'))->indexBy('id')->get();
        $sections = Section::whereIn('id', array_column($meetingsList, 'Floor'))->indexBy('id')->get();
        $brands = Brand::whereIn('id', array_column($meetingsList, 'Brands'))->indexBy('id')->get();
        $classStudioActs = ClassStudioAct::whereIn('ClassId', array_column($meetingsList, 'id'))->indexBy('ClassId')->get();
        $clients = Client::whereIn('id', array_column($classStudioActs, 'ClientId'))->indexBy('id')->get();
        $clientActivities = ClientActivities::whereIn('id', array_column($classStudioActs, 'ClientActivitiesId'))->indexBy('id')->get();

        $result = [];
        $templatesIds = [];

        for ($meeting = current($meetingsList), $i = key($meetingsList); $meeting; $meeting = next($meetingsList), $i = key($meetingsList)) {
            $guide = $guides[$meeting->GuideId] ?? null;
            $section = $sections[$meeting->Floor] ?? null;
            $brand = $brands[$meeting->Brands] ?? null;

            /** @var ClassStudioAct|null $classStudioAct */
            $classStudioAct = $classStudioActs[$meeting->id] ?? null;
            if ($classStudioAct) {
                $client = $clients[$classStudioAct->ClientId] ?? null;
                $classStudioAct->setClient($client);

                $clientActivity = $clientActivities[$classStudioAct->ClientActivitiesId] ?? null;
                $classStudioAct->setClientActivity($clientActivity);
            }

            $meeting->setClassStudioAct($classStudioAct);
            $meeting->setGuide($guide);
            $meeting->setBrand($brand);
            $meeting->setSection($section);

            $meetingData = self::collectMeetingData($meeting);
            if (!$meetingData) {
                continue;
            }

            if ($meetingType === self::TYPE_NOT_APPROVED) {
                $groupId = MeetingGroupOrdersToAct::where('ClassStudioActId', $classStudioAct->id)->pluck('MeetingGroupOrdersId');
                if ($groupId !== null) {
                    /** @var MeetingGroupOrdersToAct[] $groupOfActs */
                    $groupOfActs = MeetingGroupOrdersToAct::where('MeetingGroupOrdersId', $groupId)->get();
                    if (!empty($groupOfActs) && count($groupOfActs) > 1) {
                        foreach ($groupOfActs as $groupOfAct) {
                            $act = $groupOfAct->classStudioAct();
                            if (!$act) {
                                continue;
                            }

                            $classStudioDate = $act->classStudioDate();
                            if (!$classStudioDate || $classStudioDate->meetingStatus !== $meeting->meetingStatus) {
                                continue;
                            }

                            $meetingTemplate = MeetingTemplates::find($classStudioDate->meetingTemplateId);
                            if ($meetingTemplate) {
                                $meetingData->templates[] = [
                                    'id' => $groupOfAct->ClassStudioActId,
                                    'title' => $meetingTemplate->TemplateName,
                                    'price_total' => $classStudioDate->purchaseAmount,
                                    'start' => $classStudioDate->start_date,
                                    'end' => $classStudioDate->end_date,
                                ];

                                // don't include current meeting's classstudio_act id in templates ids
                                // we use $templatesIds to check if we already added this template
                                if ($groupOfAct->ClassStudioActId != $meeting->classStudioAct()->id) {
                                    $templatesIds[] = $groupOfAct->ClassStudioActId;
                                }

                                // TODO: check options for meeting on approval tab
                            }
                        }
                    }
                }

                if (!empty($templatesIds)) {
                    foreach ($meetingsList as $k => $m) {
                        if (in_array($m->classStudioAct()->id, $templatesIds) && $m->id != $meeting->id) {
                            unset($meetingsList[$k]);
                        }
                    }

                    $templatesIds = [];
                }
            }

            $result[] = $meetingData;
        }

        return $result;
    }

    /**
     * @param ClassStudioDate $classStudioDate
     * @return false|stdClass
     */
    public static function collectMeetingData(ClassStudioDate $classStudioDate)
    {
        $CompanyNum = $classStudioDate->CompanyNum;

        $data = new stdClass();
        $data->id = $classStudioDate->id;
        $data->Type = $classStudioDate->meetingTemplateId;
        $data->title = $classStudioDate->ClassName;
        $data->full_title = $classStudioDate->ClassName;
        $data->backgroundColor = $classStudioDate->color;
        $data->start = $classStudioDate->start_date;
        $data->end = $classStudioDate->end_date;
        $data->ClassTypeId = $classStudioDate->ClassNameType;
        $data->status = $classStudioDate->meetingStatus;
        $data->ownerId = $classStudioDate->GuideId;
        $data->price_total = $classStudioDate->purchaseAmount;
        $data->repeat_type = $classStudioDate->ClassType;
        $data->Floor = $classStudioDate->Floor;
        $data->GroupNumber = $classStudioDate->GroupNumber;
        $data->period_value = $classStudioDate->ClassRepeat;
        $data->period_unit = $classStudioDate->ClassRepeatType;

        //todo-bp-909 (cart) remove-beta - remove this
        $betaCode = Settings::getBetaCode($CompanyNum);
        $data->isBeta = in_array($betaCode, [1]);


        $data->templates = [];

        /** @var ClassStudioAct $classStudioAct */
        $classStudioAct = $classStudioDate->classStudioAct();
        if (!$classStudioAct || !in_array($classStudioAct->Status, [1, 11])) {
            return false;
        }

        /** @var Users $user */
        $user = $classStudioAct->guide();
        $data->owner = $user->display_name ?? null;

        /** @var Section $section */
        $section = $classStudioDate->section();
        $data->locationId = $section->Brands;
        $data->calendar_name = $section->Title;

        /** @var Brand|null $brand */
        $brand = $classStudioDate->brand();
        if ($brand) {
            $data->location = $brand->BrandName;
        }

        if ((int)$data->repeat_type === ClassStudioDate::CLASS_TYPE_EXPIRATION) {
            $data->regularEndDate = (ClassStudioDate::getLastClass($data->GroupNumber, $CompanyNum))->StartDate;
        }

        if ($classStudioAct) {
            $data->customer['id'] = $classStudioAct->ClientId;
            $data->customer['classActInfo'] = $classStudioAct->id;
            $data->clientActivityId = $classStudioAct->ClientActivitiesId;

            /** @var ClientActivities $clientActivity */
            $clientActivity = $classStudioAct->clientActivity();

            /** @var Client $client */
            $client = $classStudioAct->client();
            $data->customer['name'] = $client->CompanyName;
            // try to convert phone number to +972531234567 instead of 0531234567. If phone number is not valid - return as is
            $data->customer['phone'] = PhoneHelper::processPhone($client->ContactMobile) ?? $client->ContactMobile;
            $data->customer['avatar'] = Client::getAvatar($data->customer['id'], $CompanyNum);

            if (!$client->isRandomClient) {
                $meetingInfo = MeetingClient::where('GroupNumber', $data->GroupNumber)->first();
                if ($meetingInfo && $meetingInfo->token()) {
                    $data->customer['token'] = [
                        'id' => $meetingInfo->TokenId,
                        'payment_url' => 'https://app.boostapp.co.il/payment/some-url-here',
                    ];
                } elseif (!empty($client->tokens())) {
                    $lastToken = array_values($client->tokens())[0];

                    $data->customer['token'] = [
                        'id' => $lastToken->TokenId,
                        'payment_url' => 'https://app.boostapp.co.il/payment/some-url-here',
                    ];
                }
                // icons part
                if ((new ClientMedical())->getAllMedicalByClientId($classStudioAct->CompanyNum, $classStudioAct->ClientId)) {
                    $data->customer['medical'] = true;
                }
                if ((new Clientcrm())->getAllClientcrmByClientId($classStudioAct->CompanyNum, $classStudioAct->ClientId)) {
                    $data->customer['crm'] = true;
                }
                if ($classStudioAct->isFirstLesson($classStudioAct->CompanyNum, $classStudioAct->ClassDate, $classStudioAct->FixClientId)) {
                    $data->customer['is_first'] = true;
                } elseif ($clientActivity->Department == 3) {
                    $data->customer['try_membership'] = true;
                }
                if ($client->Dob && $client->Dob != '0000-00-00'
                    && date('m-d', strtotime($client->Dob)) === date('m-d', time())) {
                    $data->customer['has_birthday'] = true;
                }
                if ($classStudioAct->RegularClass == 1 && $classStudioAct->RegularClassId != 0) {
                    $data->customer['regular_assignment'] = true;
                }
                if (Company::getInstance()->greenPass) {
                    $data->customer['greenpass'] = $client->getGreenPassIcon();
                }
                if ($clientActivity->TrueClientId != 0) {
                    $data->customer['family_membership'] = true;
                }
            } else {
                $data->customer['is_random'] = true;
            }

            $docsList = [];

            $docs = DocsClientActivities::getDocs($data->clientActivityId);
            foreach ($docs as $doc) {
                if ($doc->docsPayment()) {
                    $docsPayment = $doc->docsPayment()->toArray();
                    $docsPayment['TypeTitleSingle'] = $doc->docsPayment()->docsTable()->TypeTitleSingle;
                    $docsList[] = $docsPayment;
                }
            }

            $data->docs = !empty($docsList) ? $docsList : null;

            if ($clientActivity) {
                if ($clientActivity->isPaymentForSingleClass) {
                    $data->debt = $clientActivity->BalanceMoney;
                    $data->price_total = $clientActivity->ItemPrice;
                } else {
                    $data->client_activities = [
                        "id" => $clientActivity->id,
                        "entries" => $clientActivity->TrueBalanceValue,
                        "MemberShip" => $clientActivity->ItemId,
                        "Status" => $clientActivity->Status,
                        "StartDate" => $clientActivity->StartDate,
                        "TrueDate" => $clientActivity->TrueDate,
                        "ItemText" => $clientActivity->ItemText,
                        "ItemPrice" => $clientActivity->ItemPrice,
                        "BalanceMoney" => $clientActivity->BalanceMoney,
                    ];
                }
            }

            if ($data->status != MeetingStatus::COMPLETED && $client->getMatchingActivity($data->ClassTypeId)) {
                $relatedActivityIds = $client->getMatchingActivitiesList($data->ClassTypeId);

                foreach ($relatedActivityIds as $relatedActivityId) {
                    $relatedClientActivity = ClientActivities::where('id', $relatedActivityId)->first();
                    $restriction = $relatedClientActivity->checkMembershipLimitations($classStudioDate->id, $client->id, true);

                    $data->customer['MemberShipText']['data'][] = [
                        'Id' => $relatedClientActivity->id,
                        'ItemText' => $relatedClientActivity->ItemText,
                        'balance' => in_array($relatedClientActivity->Department, [2, 3]) ? $relatedClientActivity->TrueBalanceValue : '',
                        'dateEnd' => date("d/m/y", strtotime($relatedClientActivity->TrueDate)),
                        'restriction' => $restriction,
                    ];
                }
            }

            $data->show_cancellation_policy = false;

            /** @var MeetingCancellationPolicy $policy */
            $policy = MeetingCancellationPolicy::find($classStudioAct->MeetingCancellationPolicy); // find cancellation policy from classStudioAct
            if(!$policy) { // if not found cancellation policy from classStudioAct, find for this Client
                $policy = MeetingCancellationPolicy::getPolicyForClient($client);
            }
            if($client->isRandomClient){
                $policy = null;
            }
            if ($policy) {
                if ((int)$policy->AfterPurchaseChargeStatus !== 0) {
                    $data->show_cancellation_policy = true;
                    $data->cancellation_share = ((int)$policy->AfterPurchaseChargeStatus === 2) ? 100.00
                        : $policy->AfterPurchaseChargeAmount;
                }
                if ((int)$policy->ManualChargeStatus !== 0) {
                    $lateCancelTime =
                        Utils::addInterval($data->start, $policy->getManualChargeInterval(), 'Y-m-d H:i:s');
                    if ($lateCancelTime <= date('Y-m-d H:i:s')) {
                        $data->show_cancellation_policy = true;
                        $data->cancellation_share = ((int)$policy->ManualChargeStatus === 2) ? 100.00
                            : $policy->ManualChargeAmount;
                    }
                }
                if ((int)$policy->NotArriveChargeStatus !== 0) {
                    $data->show_not_arrived_policy = true;
                    $data->not_arrived_share = ((int)$policy->NotArriveChargeStatus === 2) ? 100.00
                        : $policy->NotArriveChargeAmount;
                } elseif (isset($data->show_cancellation_policy) && $data->show_cancellation_policy) {
                    $data->show_not_arrived_policy = true;
                    $data->not_arrived_share = $data->cancellation_share;
                }
            }
        }

        $branchId = $brand->id ?? 0;
        if (Section::countActiveByBranch($CompanyNum, $branchId) <= 1) {
            unset($data->calendar_name);
        }
        if (Users::countActiveCoaches($CompanyNum) <= 1) {
            unset($data->owner);
        }
        if (isset($data->location) && Brand::countActive($CompanyNum) <= 1) {
            unset($data->location);
        }

        return $data;
    }

    /**
     * @param $CompanyNum
     * @return array
     */
    public static function getAllWaitingMeetingsIds($CompanyNum)
    {
        $meetingsList = self::getMeetingsByType($CompanyNum, self::TYPE_NOT_APPROVED);

        $res = [];
        foreach ($meetingsList as $meeting) {
            $res[] = $meeting->id;
        }
        return $res;
    }


    /**
     * @param $CompanyNum
     * @return boolean
     */
    public static function hasOpenedOrWaitingMeetings($CompanyNum): bool
    {
        $countOfNotApproved = self::getMeetingsCountByType($CompanyNum, self::TYPE_OPENED);
        return $countOfNotApproved > 0 || self::hasMeetingByStatus($CompanyNum, MeetingStatus::WAITING);
    }
}
