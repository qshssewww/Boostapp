<?php

require_once __DIR__ . '/LoggerService.php';
require_once __DIR__ . '/ClientService.php';
require_once __DIR__ . '/../Classes/Utils.php';
require_once __DIR__ . '/../Classes/ClassStudioDate.php';
require_once __DIR__ . '/../Classes/Client.php';
require_once __DIR__ . '/../Classes/ClientActivities.php';
require_once __DIR__ . '/../Classes/CompanyProductSettings.php';
require_once __DIR__ . '/../Classes/ClassStudioDateRegular.php';
require_once __DIR__ . '/../Classes/MembershipType.php';
require_once __DIR__ . '/../Classes/Item.php';
require_once __DIR__ . '/../Classes/Settings.php';
require_once __DIR__ . '/../Classes/ClassStudioAct.php';
require_once __DIR__ . '/../Classes/MembershipFreezeNotifications.php';

/**
 * Class ClientActivityService
 */
class ClientActivityService extends Utils
{
    public const VALIDATION_ARRAY = [
        'clientId' => 'required|exists:boostapp.client,id',
        'itemId' => 'exists:boostapp.items,id',
        'clientActivityId' => 'exists:boostapp.client_activities,id',
        'itemDetailsId' => 'exists:boostapp.item_details,id',
        'fromCron' => 'integer|between:0,1',
        'calcType' => 'integer|between:0,5',
        'itemPrice' => 'numeric|between:0,999999999',
        'activityName' => 'max:256',
        'startDate' => 'date_format:Y-m-d',
        'endDate' => 'date_format:Y-m-d',
        'salesId' => 'integer',
        'discountAmount' => 'numeric|between:0,999999999',
        'discountType' => 'integer|between:0,2',
        'discountValue' => 'numeric|between:0,999999999',
        'isForMeeting' => 'integer|between:0,1',
        'isDisplayed' => 'integer|between:0,1',
        'itemQuantity' => 'integer'
    ];

    public const PROPERTIES_FIELDS_IN_DATA = [
        'FirstDate' => 'FirstDate' ,
        'FirstDateStatus' => 'FirstDateStatus' ,
        'MemberShipRule' => 'MemberShipRule',
        'NotificationDays' => 'NotificationDays',
        'StartDate' => 'startDate',
        'VaildDate' => 'classDate',
        'TrueDate' => 'classDate',
        'isForMeeting' => 'isForMeeting',
        'isDisplayed' => 'isDisplayed',
        'UserId' => 'userId',
        'SalesId' => 'salesId',
        'Vat' => 'vat',
        'ItemPriceVatDiscount' => 'itemPriceVat',
        'ItemPriceVat' => 'itemPriceVat',
        'VatAmount' => 'vatAmount',
        'DiscountAmount' => 'discountAmount',
        'DiscountType' => 'discountType',
        'Discount' => 'discountValue',
        'itemDetailsId' => 'itemDetailsId',
        'ItemQuantity' => 'itemQuantity',
        'ItemText' => 'ItemText',
    ];

    public static function getClientActivitiesInDebtWithoutDoc(){

    }



    /**
     * @param $data
     * @return array
     */
    public static function assignMembership($data)
    {
        if (!isset($data['clientId']))
            return ["Status" => 0, "Error" => "clientId is missing"];
        if (!isset($data['itemId']))
            return ["Status" => 0, "Error" => "itemId is missing"];

        $companyNum = empty($data['companyNum']) ? Auth::user()->CompanyNum : $data['companyNum'];
        $FirstDate = '0';
        $FirstDateStatus = '0';
        $salesId = isset($data['fromCron']) && $data['fromCron'] ? 0 :
            (empty($data["salesId"]) ? Auth::user()->id : $data["salesId"]);

        /** @var Client $client */
        $client = Client::find($data["clientId"]);
        if (!$client) {
            return ["Status" => 0, "Error" => "Client not found"];
        }
        
        if ($client->CompanyNum !== $companyNum) {
            return ["Status" => 0, "Error" => "invalid client id"];
        }

        $StartDate = $data["startDate"] ?? date('Y-m-d');


        $membershipCount = ClientActivities::where('ClientId', $client->id)
            ->where('CompanyNum', $companyNum)
            ->whereIn("Department", [1, 2])
            ->where("Status", "!=", "2")->count();

        // בדיקת תאריך הצטרפות
        if ($membershipCount == 0) {
            $client->__set("JoinDate", $StartDate);
        }

        $item = Item::find($data["itemId"]);

        if ($item->__get("CompanyNum") !== $companyNum
            || ($item->__get("Department") != 3
                && $client->Status == 2 && $item->__get('isPaymentForSingleClass') != 1)) {
            return ["Status" => 0, "Error" => lang('cant_add_subscription_to_lead')];
        }

        $LimitClass = $item->__get("LimitClass");
        $BalanceClass = $item->__get("BalanceClass");
        $BalanceValueLog = null;
        $NotificationDays = (new CompanyProductSettings())->getSingleByCompanyNum($companyNum)->NotificationDays ?? 0;

        $MemberShipRule = json_encode(["data" => [array_map('strval', [
            "LimitClass" => $LimitClass,
            "NotificationDays" => $NotificationDays,
            "StartTime" => $item->__get("StartTime"),
            "EndTime" => $item->__get("EndTime"),
            "CancelLimit" => $item->__get("CancelLimit"),
            "ClassSameDay" => $item->__get("ClassSameDay"),
            "FreezMemberShip" => $item->__get("FreezMemberShip"),
            "FreezMemberShipDays" => $item->__get("FreezMemberShipDays"),
            "FreezMemberShipCount" => $item->__get("FreezMemberShipCount"),
            "LimitClassMorning" => $item->__get("LimitClassMorning"),
            "LimitClassEvening" => $item->__get("LimitClassEvening"),
            "LimitClassMonth" => $item->__get("LimitClassMonth")
        ])]]);

        $AppSettings = new AppSettings($companyNum);
        $SettingsInfo = new Settings($companyNum);

        $MembershipType = $AppSettings->__get("MembershipType");

        $validTypeOptions = [
            1 => "day",
            2 => "week",
            3 => "month",
            4 => "year"
        ];

        $classStudioAct = new ClassStudioAct();

        //Set Membership Start Date
        $calcType = isset($data["calcType"]) ? $data["calcType"] : 0;

        if ($item->__get("Department") >= 1 && $item->__get("Department") <= 3) {
            if ($calcType == 2) { //By prev membership end date
                $query = ClientActivities::where('Status', '=', '0')
                    ->where('CompanyNum', '=', $companyNum)
                    ->where('ClientId', '=', $client->id);
                if ($MembershipType == 0) {
                    $query = $query
                        ->where('MemberShip', '=', $item->__get("MemberShip"));
                }
                $LastMembership = $query->orderBy('id', 'DESC')->first();

                if (!empty($LastMembership) && $LastMembership->TrueDate != '') {
                    $StartDate = $LastMembership->TrueDate;
                }
            } elseif ($calcType == 3) { //By last class date
                $LastClass = $classStudioAct->getLastClassForClient($companyNum, $client->id, $MembershipType, $item->__get("MemberShip"));

                if ($LastClass && $LastClass->TrueDate != '') {
                    $StartDate = $LastClass->TrueDate;
                }
            } elseif ($calcType == 5) { //By first class using the membership
                $FirstDate = 1;
                $FirstDateStatus = 1;
            }

            if ($item->__get("Department") == 3) {
                $MemberShipRule = null;
                $LimitClass = 999;
            }
        } else { // Department = 4
            $ClassDate = null;
            $MemberShipRule = null;
            $LimitClass = 0;
            $BalanceClass = 0;
        }

        $totalMemberships = ClientActivities::where("CompanyNum", $companyNum)
            ->where("ClientId", $client->id)
            ->count();

        $totalMemberships += 1;

        if (isset($data["endDate"]) && ($item->__get("Department") == 1 || $item->__get("Department") == 2)) {
            $ClassDate = $data["endDate"];
        } elseif ($item->__get('Vaild')) {
            $interval = "+ " . $item->__get('Vaild') . " " . $validTypeOptions[$item->__get("Vaild_Type")];
            $ClassDate = date("Y-m-d", strtotime($interval, strtotime($StartDate)));
        } else {
            $ClassDate = null;
        }

        $userId = isset($data['fromCron']) && $data['fromCron'] ? 0 : Auth::user()->id;
        $Dates = date("Y-m-d H:i:s");
        $validTypeOption = $validTypeOptions['1'];
        $itemTimeString = "-" . $NotificationDays . " " . $validTypeOption;
        $time = strtotime($ClassDate);
        $NotificationDate = date("Y-m-d", strtotime($itemTimeString, $time));

        if ($NotificationDays == '0' || $item->__get("Department") == 4 || $item->__get("Department") == 3 || $calcType == 5) {
            $NotificationDate = null;
        }

        $itemPrice = $data["itemPrice"] ?? $item->__get("ItemPrice");

        $companyVat = $SettingsInfo->__get("CompanyVat");

        $Vat = $SettingsInfo->__get("Vat");
        if ($companyVat == 0) {
            $Vats = '1.' . $Vat;
            $itemPriceVat = $itemPrice / $Vats;
            $itemPriceVat = round($itemPriceVat, 2);
        } else {
            $itemPriceVat = $itemPrice;
        }

        $vatAmount = $itemPrice - $itemPriceVat;
        $newClientActivityId = ClientActivities::insertGetId([
            "CompanyNum" => $companyNum,
            "CardNumber" => $totalMemberships,
            "ClientId" => $client->id,
            "Department" => $item->__get("Department"),
            "MemberShip" => $item->__get("MemberShip"),
            "ItemId" => $item->__get("id"),
            "ItemText" => $data['activityName'] ?? $item->__get("ItemName"),
            "ItemPrice" => $itemPrice,
            "ItemPriceVat" => $itemPriceVat,
            "ItemPriceVatDiscount" => $itemPriceVat,
            "Vat" => $Vat,
            "VatAmount" => $vatAmount,
            "StartDate" => $StartDate,
            "VaildDate" => $ClassDate,
            "TrueDate" => $ClassDate,
            "BalanceValue" => $BalanceClass,
            "TrueBalanceValue" => $BalanceClass,
            "ActBalanceValue" => $BalanceClass,
            "LimitClass" => $LimitClass,
            "Dates" => $Dates,
            "UserId" => $userId,
            "BalanceMoney" => $itemPrice,
            "MemberShipRule" => $MemberShipRule,
            "NotificationDays" => $NotificationDate,
            "BalanceValueLog" => $BalanceValueLog,
            "FirstDate" => $FirstDate,
            "FirstDateStatus" => $FirstDateStatus,
            "SalesId" => $salesId,
            "isPaymentForSingleClass" => $item->__get('isPaymentForSingleClass'),
            "isForMeeting" => $data['isForMeeting'] ?? 0,
            "isDisplayed" => $data['isDisplayed'] ?? 1,
            'DiscountType' => $data['DiscountType'] ?? 0,
            'Discount' => $data['Discount'] ?? 0,
            'DiscountAmount' => $data['DiscountAmount'] ?? 0,

        ]);

        try {
            $MinusCards = (new CompanyProductSettings())->getSingleByCompanyNum($companyNum)->offsetMemberships ?? 1;
            $itemRole = new ItemRoles($item->__get("id"));
            $TrueClassesFinal = $itemRole->__get("GroupId") ?? '';

            $activityData = [
                "CompanyNum" => $companyNum,
                "ClientId" => $client->id,
                "ActivityId" => $newClientActivityId,
                "MemberShip" => $item->__get("MemberShip"),
                "MembershipType" => $MembershipType,
                "MinusCards" => $MinusCards,
                "Department" => $item->__get("Department"),
                "TrueClasessFinal" => $TrueClassesFinal,
                "BalanceClass" => $BalanceClass,
                "StartDate" => $StartDate
            ];
            if ($item->__get('isPaymentForSingleClass') != 1) {
                (new ClientActivities())->moveClassesToNewActivity($activityData);
            }


            $tasks = DB::table('client_activities')
                ->where('TrueDate', '>=', date('Y-m-d'))->where('Department', '=', '1')->where('ClientId', '=', $client->id)->where('CompanyNum', '=', $companyNum)->where('Status', '=', '0')
                ->Orwhere('TrueBalanceValue', '>=', '1')->whereNull('TrueDate')->where('Department', '=', '2')->where('ClientId', '=', $client->id)->where('CompanyNum', '=', $companyNum)->where('Status', '=', '0')
                ->Orwhere('TrueBalanceValue', '>=', '1')->where('TrueDate', '>=', date('Y-m-d'))->where('Department', '=', '2')->where('ClientId', '=', $client->id)->where('CompanyNum', '=', $companyNum)->where('Status', '=', '0')
                ->Orwhere('TrueBalanceValue', '>=', '1')->whereNull('TrueDate')->where('Department', '=', '3')->where('ClientId', '=', $client->id)->where('CompanyNum', '=', $companyNum)->where('Status', '=', '0')
                ->Orwhere('TrueBalanceValue', '>=', '1')->where('TrueDate', '>=', date('Y-m-d'))->where('Department', '=', '3')->where('ClientId', '=', $client->id)->where('CompanyNum', '=', $companyNum)->where('Status', '=', '0')
                ->orderBy('CardNumber', 'ASC')->get();

            $taskCount = count($tasks);

            $dataArr = [];

            foreach ($tasks as $task) {
                $dataArr[] = array_map('strval', [
                    "ItemText" => $task->ItemText,
                    "TrueDate" => $task->TrueDate,
                    "TrueBalanceValue" => $task->TrueBalanceValue,
                    "Id" => $task->id,
                    "LimitClass" => $task->LimitClass,
                ]);
            }

            $MemberShipText = json_encode([
                "data" => $dataArr
            ]);

            // חוב לקוח
            $balance = 0.00;
            if ($client->PayClientId != 0) {
                $payClientId = $client->PayClientId;
                $payingClient = new Client($payClientId);

                $client->save();

                $debtClients = $client->getClientsByPayClientId($payClientId, $companyNum);
                foreach ($debtClients as $debtClient) {
                    if (isset($debtClient->id)) {
                        $balance += ClientActivities::where("ClientId", $debtClient->id)
                            ->where("CompanyNum", $companyNum)
                            ->where("CancelStatus", 0)
                            ->where('isDisplayed', 1)
                            ->sum("BalanceMoney");

                        DB::table("client")->where("id", $debtClient->id)->update(["BalanceAmount" => 0]);
                    }
                }

                ClientActivities::where("ClientId", $client->id)
                    ->where("CompanyNum", $companyNum)
                    ->update(["PayClientId" => $payClientId]);
            } else {
                $payClientId = $client->id;
                $payingClient = $client;

                ClientActivities::where("ClientId", $payClientId)
                    ->where("CompanyNum", $companyNum)
                    ->update(["PayClientId" => 0]);
            }
            $balance += ClientActivities::where("CancelStatus", 0)
                ->where("CompanyNum", $companyNum)
                ->where("ClientId", $payClientId)
                ->where('isDisplayed', 1)
                ->sum("BalanceMoney");

            $payingClient->__set("BalanceAmount", $balance);
            $payingClient->__set("MemberShipText", $MemberShipText);
            $payingClient->save();

// סגירת מנויים ישנים       
            $MemberShip = $item->__get("MemberShip");

            DB::table('client_activities')
                ->where('ClientId', $client->id)
                ->where('CompanyNum', $companyNum)
                ->where('MemberShip', '=', $item->__get("MemberShip"))
                ->where('Department', '=', 1)
                ->where('Status', '=', 0)
                ->where('TrueDate', '<=', date('Y-m-d'))
                ->update(array('Status' => 3));

            DB::table('client_activities')
                ->where('ClientId', $client->id)
                ->where('CompanyNum', $companyNum)
                ->where('Status', '=', 0)
                ->where(function ($query) use ($MemberShip) {
                    $query->where('Department', 2)->where('MemberShip', '=', $MemberShip)
                        ->Orwhere('Department', 3);
                })
                ->where(function ($q) {
                    $q->where('TrueDate', '<=', date('Y-m-d'))
                        ->Orwhere('TrueBalanceValue', '<=', 0);
                })
                ->update(array('Status' => 3));


            if ($calcType != 5 && $item->__get('isPaymentForSingleClass') != 1 && ($item->__get("Department") == 1 || ($item->__get("Department") == 2 && $BalanceClass > 1))) {
                $classes = $classStudioAct->getClassesByFixClientId($client->id, $companyNum, $StartDate);
                foreach ($classes as $class) {
                    $trueClasses = '';
                    $trueClassesFinal = '';
                    $classInfo = new ClassCalendar($class->ClassId);
                    $itemRoles = $itemRole->getItemRolesByClassNameAndItemId($companyNum, $item->__get("id"), $classInfo->__get("ClassNameType"));

                    if (!empty($itemRoles)) {
                        foreach ($itemRoles as $item_role) {
                            $trueClassesFinal = $item_role->GroupId;
                            $TrueClasses = $item_role->Class;
                        }
                    }

                    if ($trueClassesFinal != '') {
                        $trueClientId = $class->FixClientId;

                        if ($trueClientId == $client->id) {
                            $trueClientId = 0;
                        }

                        ClassStudioAct::find($class->id)->update([
                            "ClientId" => $client->id,
                            "TrueClientId" => $trueClientId,
                            "ClientActivitiesId" => $newClientActivityId,
                            "TrueClasess" => $trueClassesFinal,
                            "Department" => $item->__get("Department"),
                            "MemberShip" => $item->__get("MemberShip"),
                        ]);

                        ClassStudioDateRegular::updateById($class->RegularClassId, [
                            "ClientActivitiesId" => $newClientActivityId,
                            "MemberShipType" => $item->__get("MemberShip")
                        ]);
                    }
                }
            }

            if (in_array($item->__get("Department"), [1, 2, 3])) {
                $activityCount = 0;
                if ($item->__get("Department") == 1) {
                    $activityCount = ClientActivities::where("TrueDate", ">=", date("Y-m-d"))
                        ->where("StartDate", "<=", date("Y-m-d"))
                        ->where("MemberShip", $item->__get("MemberShip"))
                        ->where("Department", 1)
                        ->where("CompanyNum", $companyNum)
                        ->where("Status", 0)
                        ->where("FirstDateStatus", 0)
                        ->count();
                } elseif ($item->__get("Department") == 2) {
                    $activityCount = ClientActivities::where("CompanyNum", $companyNum)
                        ->where("StartDate", "<=", date("Y-m-d"))
                        ->where("MemberShip", $item->__get("MemberShip"))
                        ->where("Status", 0)
                        ->where("Department", 2)
                        ->where("FirstDateStatus", 0)
                        ->where("ActBalanceValue", ">=", 1)
                        ->where(function ($q) {
                            $q->whereNull("TrueDate")
                                ->Orwhere("TrueDate", ">=", date("Y-m-d"));
                        })
                        ->count();
                } elseif ($item->__get("Department") == 3) {
                    $activityCount = ClientActivities::where("CompanyNum", $companyNum)
                        ->where("StartDate", "<=", date("Y-m-d"))
                        ->where("Department", 3)
                        ->where("MemberShip", $item->__get("MemberShip"))
                        ->where("ActBalanceValue", ">=", 1)
                        ->where("Status", 0)
                        ->where("FirstDateStatus", 0)
                        ->count();
                }

                $membershipType = new MembershipType();
                $membershipType->updateById($item->__get("MemberShip"), $companyNum, ["Count" => $activityCount]);
            }

            return ["Status" => 1, "ClientActivityId" => $newClientActivityId];
        } catch (\Throwable $e) {
            LoggerService::error($e);

            return ["Status" => 0, "ClientActivityId" => $newClientActivityId];
        }
    }    
    
    /**
     * Cancel an activity and add debt to client if necessary
     *
     * @param int $CompanyNum
     * @param int $ClientId
     * @param int $ActivityId
     * @param $MinusMoney
     * @param $Reason
     * @param int $Act
     * @return void
     * @throws Throwable
     */
    public static function cancelMembership(int $CompanyNum, int $ClientId, int $ActivityId, $MinusMoney, $Reason, int $Act = 0)
    {
        // manual fix for log function
        if (!function_exists('CreateLogMovement')) {
            require_once __DIR__ . '/../../app/init.php';
        }

        try {
            /** @var Client $client */
            $client = Client::where('id', '=', $ClientId)->where('CompanyNum', $CompanyNum)->first();

            $CancelDate = date('Y-m-d H:i:s');
            $ActivityInfo = DB::table('client_activities')->where('CompanyNum', '=', $CompanyNum)->where('ClientId', '=', $ClientId)->where('id', '=', $ActivityId)->first();

            if ($MinusMoney == '0') {
                //// עדכון חוב ללקוח
                // set debt to client

                DB::table('client_activities')
                    ->where('ClientId', $ClientId)
                    ->where('id', $ActivityId)
                    ->where('CompanyNum', $CompanyNum)
                    ->update(array('Status' => '2', 'CancelStatus' => '1', 'CancelDate' => $CancelDate, 'Reason' => $Reason, 'BalanceMoney' => '0'));

                $client->updateBalanceAmount();
            } else {
                DB::table('client_activities')
                    ->where('ClientId', $ClientId)
                    ->where('id', $ActivityId)
                    ->where('CompanyNum', $CompanyNum)
                    ->update(array('Status' => '2', 'CancelStatus' => '0', 'CancelDate' => $CancelDate, 'Reason' => $Reason));
            }

            // check/delete freeze notifications
            MembershipFreezeNotifications::checkByActivityId($ActivityId);

            /////// עדכון כרטיס לקוח

            $GetTasks = DB::table('client_activities')
                ->where('TrueDate', '>=', date('Y-m-d'))->where('Department', '=', '1')->where('ClientId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')
                ->Orwhere('TrueBalanceValue', '>=', '1')->whereNull('TrueDate')->where('Department', '=', '2')->where('ClientId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')
                ->Orwhere('TrueBalanceValue', '>=', '1')->where('TrueDate', '>=', date('Y-m-d'))->where('Department', '=', '2')->where('ClientId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')
                ->Orwhere('TrueBalanceValue', '>=', '1')->whereNull('TrueDate')->where('Department', '=', '3')->where('ClientId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')
                ->Orwhere('TrueBalanceValue', '>=', '1')->where('TrueDate', '>=', date('Y-m-d'))->where('Department', '=', '3')->where('ClientId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')
                ->orderBy('CardNumber', 'ASC')->get();

            $MemberShipText = '{"data": [';
            foreach ($GetTasks as $GetTask) {
                $MemberShipText .= '{"ItemText": "' . $GetTask->ItemText . '", "TrueDate": "' . $GetTask->TrueDate . '", "TrueBalanceValue": "' . $GetTask->TrueBalanceValue . '", "Id": "' . $GetTask->id . '", "LimitClass": "' . $GetTask->LimitClass . '"},';
            }
            $MemberShipText = rtrim($MemberShipText, ',');
            $MemberShipText .= ']}';

            DB::table('client')
                ->where('id', $ClientId)
                ->where('CompanyNum', $CompanyNum)
                ->update(array('MemberShipText' => $MemberShipText));

            $MemberShip = $ActivityInfo->MemberShip;

            $MemberShipCounts = DB::table('client_activities')
                ->where('TrueDate', '>=', date('Y-m-d'))->where('StartDate', '<=', date('Y-m-d'))->where('Department', '=', '1')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('MemberShip', '=', $MemberShip)->where('ClientStatus', '=', '0')->where('FirstDateStatus', '=', '0')
                ->Orwhere('ActBalanceValue', '>=', '1')->where('StartDate', '<=', date('Y-m-d'))->whereNull('TrueDate')->where('Department', '=', '2')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('MemberShip', '=', $MemberShip)->where('ClientStatus', '=', '0')->where('FirstDateStatus', '=', '0')
                ->Orwhere('ActBalanceValue', '>=', '1')->where('StartDate', '<=', date('Y-m-d'))->where('TrueDate', '>=', date('Y-m-d'))->where('Department', '=', '2')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('MemberShip', '=', $MemberShip)->where('ClientStatus', '=', '0')->where('FirstDateStatus', '=', '0')
                ->Orwhere('ActBalanceValue', '>=', '1')->where('StartDate', '<=', date('Y-m-d'))->where('Department', '=', '3')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('MemberShip', '=', $MemberShip)->where('ClientStatus', '=', '0')->where('FirstDateStatus', '=', '0')
                ->get();

            $MemberShipCount = count($MemberShipCounts);
            DB::table('membership_type')
                ->where('id', $MemberShip)
                ->where('CompanyNum', $CompanyNum)
                ->update(array('Count' => $MemberShipCount));

            if ($Act == 1) {
                $deleteRegular = DB::table('classstudio_dateregular')
                    ->where('CompanyNum', '=', $CompanyNum)
                    ->where('ClientActivitiesId', '=', $ActivityId)
                    ->delete();

                if ($deleteRegular) {
                    CreateLogMovement(  //FontAwesome Icon
                        'הוסר שיבוץ קבוע בעקבות ביטול מנוי ' . htmlentities(@$ActivityInfo->ItemText), //LogContent
                        $ClientId //ClientId
                    );
                }

                $DeletesActClasses = ClassStudioAct::getToDeleteByClientActivityId($CompanyNum, $ActivityId, $ClientId);
                foreach ($DeletesActClasses as $DeletesActClass) {
                    /** @var ClassStudioDate $ClassStudioDate */
                    $ClassStudioDate = ClassStudioDate::find($DeletesActClass->ClassId);
                    if(!$ClassStudioDate || !$ClassStudioDate->meetingTemplateId){
                        DB::table('classlog')
                            ->where('CompanyNum', '=', $CompanyNum)
                            ->where('ClientId', '=', $DeletesActClass->ClientId)
                            ->where('ClassId', '=', $DeletesActClass->ClassId)
                            ->delete();
                        DB::table('classlog')
                            ->where('CompanyNum', '=', $CompanyNum)
                            ->where('ClientId', '=', $DeletesActClass->TrueClientId)
                            ->where('ClassId', '=', $DeletesActClass->ClassId)
                            ->delete();

                        (new ClassStudioAct())->deleteActById($DeletesActClass->id, $CompanyNum);
                    } else { //if ClassStudioAct is meeting, not delete act, cancel ClassStudioAct and ClassStudioDate
                        $ClassStudioDate->setStatusToCanceledMeeting();
                        $DeletesActClass->changeStatus(ClassStudioAct::STATUS_MEETING_CANCELED_BY_STUDIO);
                    }

                    ///// ספירת שיעורים
                    $ClassInfo = DB::table('classstudio_date')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('GroupNumber', '=', $DeletesActClass->GroupNumber)->where('ClassType', '=', '1')->orderBy('ClassCount', 'DESC')->first();
                    if ($ClassInfo) {
                        ClassStudioDate::updateClassRegistersCount($DeletesActClass->ClassId, $DeletesActClass->GroupNumber, $ClassInfo->Floor, $ClassInfo->StartDate);
                    }
                }

                CreateLogMovement(//FontAwesome Icon
                    lang('permanent_booking_cancled_ajax') . htmlentities(@$ActivityInfo->ItemText), //LogContent
                    $ClientId //ClientId
                );
            }

            CreateLogMovement(//FontAwesome Icon
                lang('log_cancled_membership_ajax') . htmlentities(@$ActivityInfo->ItemText), //LogContent
                $ClientId //ClientId
            );
        } catch (\Throwable $e) {
            LoggerService::error($e);
            throw $e;
        }
    }

    /**
     * @param array $data
     * @return array
     */
    public static function assignItemToClient(array $data): array
    {
        $validation = self::validate($data);
        if ($validation->fails()) {
            return ['Error' => $validation->messages()->first(), 'Status' => self::ERROR_STATUS];
        }
        try {
            /** @var Item $Item */
            $Item = Item::find($data['itemId']);
            /** @var Client $Client */
            $Client = Client::find($data['clientId']);
            $response = self::createClientActivity($Item, $Client, $data);
            if (!isset($response['Status']) || $response['Status'] === self::ERROR_STATUS) {
                return $response;
            }
            if ($Item->Department < 4) {
                $calcStartTimeType = $data['calcType'] ?? Item::MEMBERSHIP_START_COUNT_FROM_PURCHASE;
                self::unionAndArrangementActivities($response['ClientActivityId'], $Client, $calcStartTimeType);
            } else if (isset($data['itemDetailsId'])) {
                (new ItemDetails($data['itemDetailsId']))->increaseUsed($data['itemQuantity'] ?? 1); //add log use updat?
            }
            //update status client if was not active to active or archive
            $changeToStatus = (int)$Item->Department === Item::DEPARTMENT_TRIAL ? Client::STATUS_LEAD : Client::STATUS_ACTIVE;
            if($Client->isArchived() && !ClientService::updateStatus($Client, $changeToStatus)) {
                LoggerService::info('Failed to update client - ' . $Client->id. 'status to active after assign item to client', LoggerService::CATEGORY_CLIENT);
            }

        } catch (LogicException $e) {
            return ['Error' => $validation->messages()->first(), 'Status' => self::ERROR_STATUS];
        } catch (\Throwable $e) {
            if (isset($response['ClientActivityId'])) {
                LoggerService::error($e);

                ClientActivities::where('id', $response['ClientActivityId'])->delete();

                return ['Error' => $validation->messages()->first(), 'Status' => self::SUCCESS_STATUS];
            }
            return ['Error' => $validation->messages()->first(), 'Status' => self::ERROR_STATUS];
        }
        return $response;
    }

    /**
     * @param Item $Item
     * @param Client $Client
     * @param array $data
     * @return array
     */
    public static function createClientActivity(Item $Item, Client $Client, array $data): array
    {
        if ($Client->CompanyNum !== $Item->CompanyNum) {
            return ["Status" => self::ERROR_STATUS, "Error" => 'CompanyNumA not valid'];
        }
        if (!isset($data->fromCron) || $data->fromCron === 0) {
            if ($Client->CompanyNum !== Auth::user()->CompanyNum) {
                return ["Status" => self::ERROR_STATUS, "Error" => 'Auth not valid'];
            }
        }
        //todo-ask-alex
        if (!$Client->updateLeadClientIfTrial($Item->Department)) {
            return ["Status" => self::ERROR_STATUS, "Error" => lang('cant_add_subscription_to_lead')];
        }
        self::updateItemData($data, $Item);
        $ClientActivity = new ClientActivities();
        $ClientActivity->setPropertiesByClient($Client);
        self::setPropertiesFieldsFromData($data, $ClientActivity);// The order of the functions is important because of the quantity
        $ClientActivity->setPropertiesByItem($Item);
        $validator = Validator::make($ClientActivity->getAttributes(), ClientActivities::$createRules);
        if ($validator->fails()) {
            return ['Error' => $validator->messages()->first(), 'status' => self::ERROR_STATUS];
        }
        $ClientActivity->save();
        $Client->save();// update client if need to
        if ($ClientActivity->id) {
            return ["Status" => self::SUCCESS_STATUS, "ClientActivityId" => $ClientActivity->id];
        }
        return ["Status" => self::ERROR_STATUS, "Error" => 'error in create ClientActivity'];
    }

    /**
     * @param int $clientActivityId
     * @param Client $Client
     * @param int $calcStartTimeType
     */
    private static function unionAndArrangementActivities(int $clientActivityId, Client $Client , int $calcStartTimeType = 1): bool
    {
        try {
            /** @var ClientActivities $ClientActivity */
            $ClientActivity = ClientActivities::find($clientActivityId);
            //start---- todo -  add to clientActivities class : moveClassesToNewActivity -> p
            $MinusCards = (new CompanyProductSettings())->getSingleByCompanyNum($ClientActivity->CompanyNum)->offsetMemberships ?? 1;
            $membershipType = (new AppSettings($ClientActivity->CompanyNum))->__get("MembershipType") ?? 0;
            $itemRole = new ItemRoles($ClientActivity->ItemId);
            if ((int)$ClientActivity->isPaymentForSingleClass !== 1) {
                (new ClientActivities())->moveClassesToNewActivity(
                    [
                        "CompanyNum" => $ClientActivity->CompanyNum,
                        "ClientId" => $ClientActivity->ClientId,
                        "ActivityId" => $ClientActivity->id,
                        "MemberShip" => $ClientActivity->MemberShip,
                        "MembershipType" => $membershipType,
                        "MinusCards" => $MinusCards,
                        "Department" => $ClientActivity->Department,
                        "TrueClasessFinal" => $itemRole->__get("GroupId") ?? '',
                        "BalanceClass" => $ClientActivity->BalanceValue,
                        "StartDate" => $ClientActivity->StartDate
                    ]
                );
            }
            //end---- todo -  add to clientActivities class : moveClassesToNewActivity -> p


            //start ------ todo - add to clientActivities class : get $MemberShipText
            $tasks = DB::table('client_activities')
                ->where('TrueDate', '>=', date('Y-m-d'))->where('Department', '=', '1')->where('ClientId', '=', $ClientActivity->ClientId)->where('CompanyNum', '=', $ClientActivity->CompanyNum)->where('Status', '=', '0')
                ->Orwhere('TrueBalanceValue', '>=', '1')->whereNull('TrueDate')->where('Department', '=', '2')->where('ClientId', '=', $ClientActivity->ClientId)->where('CompanyNum', '=', $ClientActivity->CompanyNum)->where('Status', '=', '0')
                ->Orwhere('TrueBalanceValue', '>=', '1')->where('TrueDate', '>=', date('Y-m-d'))->where('Department', '=', '2')->where('ClientId', '=', $ClientActivity->ClientId)->where('CompanyNum', '=', $ClientActivity->CompanyNum)->where('Status', '=', '0')
                ->Orwhere('TrueBalanceValue', '>=', '1')->whereNull('TrueDate')->where('Department', '=', '3')->where('ClientId', '=', $ClientActivity->ClientId)->where('CompanyNum', '=', $ClientActivity->CompanyNum)->where('Status', '=', '0')
                ->Orwhere('TrueBalanceValue', '>=', '1')->where('TrueDate', '>=', date('Y-m-d'))->where('Department', '=', '3')->where('ClientId', '=', $ClientActivity->ClientId)->where('CompanyNum', '=', $ClientActivity->CompanyNum)->where('Status', '=', '0')
                ->orderBy('CardNumber', 'ASC')->get();

            $dataArr = [];
            foreach ($tasks as $task) {
                $dataArr[] = array_map('strval', [
                    "ItemText" => $task->ItemText,
                    "TrueDate" => $task->TrueDate,
                    "TrueBalanceValue" => $task->TrueBalanceValue,
                    "Id" => $task->id,
                    "LimitClass" => $task->LimitClass,
                ]);
            }
            $MemberShipText = json_encode([
                "data" => $dataArr
            ]);
            //end ------ todo - add to clientActivities class : get $MemberShipText

            // חוב לקוח
            $balance = 0.00;
            if ((int)$Client->PayClientId !== 0) {
                $PayingClient = new Client($Client->PayClientId);
                $debtClients = $Client->getClientsByPayClientId($PayingClient->id, $ClientActivity->CompanyNum);
                foreach ($debtClients as $debtClient) {
                    if (isset($debtClient->id)) {
                        $balance += ClientActivities::where("ClientId", $debtClient->id)
                            ->where("CompanyNum", $ClientActivity->CompanyNum)
                            ->where("CancelStatus", 0)
                            ->where('isDisplayed', 1)
                            ->sum("BalanceMoney");
                        DB::table("client")->where("id", $debtClient->id)->update(["BalanceAmount" => 0]);
                    }
                }
                ClientActivities::where("ClientId", $ClientActivity->ClientId)
                    ->where("CompanyNum", $ClientActivity->CompanyNum)
                    ->update(["PayClientId" => $PayingClient->id]);
            } else {
                $PayingClient = $Client;
                ClientActivities::where("ClientId", $Client->id)
                    ->where("CompanyNum", $ClientActivity->CompanyNum)
                    ->update(["PayClientId" => 0]);
            }
            $balance += ClientActivities::where("CancelStatus", 0)
                ->where("CompanyNum", $ClientActivity->CompanyNum)
                ->where("ClientId", $PayingClient->id)
                ->where('isDisplayed', 1)
                ->sum("BalanceMoney");

            $PayingClient->__set("BalanceAmount", $balance);
            $PayingClient->__set("MemberShipText", $MemberShipText);
            $PayingClient->save();

// סגירת מנויים ישנים
            $MemberShip = $ClientActivity->MemberShip;

            DB::table('client_activities')
                ->where('ClientId', $Client->id)
                ->where('CompanyNum', $ClientActivity->CompanyNum)
                ->where('MemberShip', '=', $ClientActivity->MemberShip)
                ->where('Department', '=', 1)
                ->where('Status', '=', 0)
                ->where('TrueDate', '<=', date('Y-m-d'))
                ->update(array('Status' => 3));

            DB::table('client_activities')
                ->where('ClientId', $Client->id)
                ->where('CompanyNum', $ClientActivity->CompanyNum)
                ->where('Status', '=', 0)
                ->where(function ($query) use ($MemberShip) {
                    $query->where('Department', 2)->where('MemberShip', '=', $MemberShip)
                        ->Orwhere('Department', 3);
                })
                ->where(function ($q) {
                    $q->where('TrueDate', '<=', date('Y-m-d'))
                        ->Orwhere('TrueBalanceValue', '<=', 0);
                })
                ->update(array('Status' => 3));


            if ($calcStartTimeType !== Item::MEMBERSHIP_START_COUNT_FROM_DATE && $ClientActivity->isPaymentForSingleClass != 1 && (in_array($ClientActivity->Department, [1, 2]) && $ClientActivity->BalanceValue > 1)) {
                $classes = (new ClassStudioAct())->getClassesByFixClientId($Client->id, $ClientActivity->CompanyNum, $ClientActivity->StartDate);
                foreach ($classes as $class) {
                    $trueClasses = '';
                    $trueClassesFinal = '';
                    $classInfo = new ClassCalendar($class->ClassId);
                    $itemRoles = $itemRole->getItemRolesByClassNameAndItemId($ClientActivity->CompanyNum, $ClientActivity->ItemId, $classInfo->__get("ClassNameType"));

                    if (!empty($itemRoles)) {
                        foreach ($itemRoles as $item_role) {
                            $trueClassesFinal = $item_role->GroupId;
                            $TrueClasses = $item_role->Class;
                        }
                    }

                    if ($trueClassesFinal != '') {
                        $trueClientId = $class->FixClientId;

                        if ($classes->FixClientId == $Client->id) {
                            $trueClientId = 0;
                        }

                        ClassStudioAct::find($class->id)->update([
                            "ClientId" => $Client->id,
                            "TrueClientId" => $trueClientId,
                            "ClientActivitiesId" => $ClientActivity->id,
                            "TrueClasess" => $trueClassesFinal,
                            "Department" => $ClientActivity->Department,
                            "MemberShip" => $ClientActivity->MemberShip,
                        ]);

                        ClassStudioDateRegular::updateById($class->RegularClassId, [
                            "ClientActivitiesId" => $ClientActivity->id,
                            "MemberShipType" => $ClientActivity->MemberShip
                        ]);
                    }
                }
            }

            if (in_array($ClientActivity->Department, [1, 2, 3])) {
                $activityCount = 0;
                if ($ClientActivity->Department == 1) {
                    $activityCount = ClientActivities::where("TrueDate", ">=", date("Y-m-d"))
                        ->where("StartDate", "<=", date("Y-m-d"))
                        ->where("MemberShip", $ClientActivity->MemberShip)
                        ->where("Department", 1)
                        ->where("CompanyNum", $ClientActivity->CompanyNum,)
                        ->where("Status", 0)
                        ->where("FirstDateStatus", 0)
                        ->count();
                } elseif ($ClientActivity->Department == 2) {
                    $activityCount = ClientActivities::where("CompanyNum", $ClientActivity->CompanyNum,)
                        ->where("StartDate", "<=", date("Y-m-d"))
                        ->where("MemberShip", $ClientActivity->MemberShip)
                        ->where("Status", 0)
                        ->where("Department", 2)
                        ->where("FirstDateStatus", 0)
                        ->where("ActBalanceValue", ">=", 1)
                        ->where(function ($q) {
                            $q->whereNull("TrueDate")
                                ->Orwhere("TrueDate", ">=", date("Y-m-d"));
                        })
                        ->count();
                } elseif ($ClientActivity->Department == 3) {
                    $activityCount = ClientActivities::where("CompanyNum", $ClientActivity->CompanyNum,)
                        ->where("StartDate", "<=", date("Y-m-d"))
                        ->where("Department", 3)
                        ->where("MemberShip", $ClientActivity->MemberShip)
                        ->where("ActBalanceValue", ">=", 1)
                        ->where("Status", 0)
                        ->where("FirstDateStatus", 0)
                        ->count();
                }

                $membershipType = new MembershipType();
                $membershipType->updateById($ClientActivity->MemberShip, $ClientActivity->CompanyNum, ["Count" => $activityCount]);
            }
        } catch (\Throwable $e) {
            throw new LogicException($e->getMessage());
        }

        return true;
    }

    /**
     * @param array $data
     * @param Item $Item
     */
    public static function updateItemData(array &$data, Item $Item): void
    {

        $Item->NotificationDays = (new CompanyProductSettings())->getSingleByCompanyNum($Item->CompanyNum)->NotificationDays ?? 0;
        $data['userId'] = isset($data['fromCron']) && $data['fromCron'] === 1 ? 0 : Auth::user()->id;
        $data['salesId'] = isset($data['fromCron']) && $data['fromCron'] === 1 ?
            0 : $data['salesId'] ?? Auth::user()->id;
        $data['startDate'] = $data['startDate'] ?? date('Y-m-d');
        $membershipType = (new AppSettings($Item->CompanyNum))->__get("MembershipType") ?? 0;
        $membershipTypeId = (int)$membershipType === 0 ? $Item->MemberShip : 0;
        switch ($Item->Department) {
            case Item::DEPARTMENT_PERIODIC:
            case Item::DEPARTMENT_TICKET:
                $data['MemberShipRule'] = $Item->returnMemberShipRule();
                switch ($data['calcType'] ?? Item::MEMBERSHIP_START_COUNT_FROM_PURCHASE) {
                    case Item::MEMBERSHIP_START_COUNT_FROM_PURCHASE:
                        $data['startDate'] = date('Y-m-d');
                        $data['classDate']  = $Item->geEndDate($data['startDate']);
                        break;
                    case Item::MEMBERSHIP_START_COUNT_FROM_PREV_ACTIVITY:
                        $data['startDate'] = ClientActivities::findPrevActivityEndDay($data['clientId'], $Item->CompanyNum, $membershipTypeId);
                        $data['classDate']  = $Item->geEndDate($data['startDate']);
                        break;
                    case Item::MEMBERSHIP_START_COUNT_FROM_FIRST_LESSON:
                        $data['startDate'] = ClassStudioAct::getTrueDateFromPrevClassForClient($Item->CompanyNum, $data['clientId'], $membershipTypeId);
                        break;
                    case Item::MEMBERSHIP_START_COUNT_FROM_DATE:
                        isset($data['endDate']) ? $data['classDate'] = $data['endDate'] : null;
                        break;
                    case Item::MEMBERSHIP_START_COUNT_FROM_NEXT_LESSON:
                        $data['FirstDate'] = 1;
                        $data['FirstDateStatus'] = 1;
                        break;
                }
                break;
            case Item::DEPARTMENT_TRIAL:
                $Item->LimitClass = 999;
                break;
            case Item::DEPARTMENT_PRODUCT:
                $Item->LimitClass = 0;
                $Item->BalanceClass = 0;
        }
        //update NotificationDays before end
        if(isset($data['classDate']) ) {
            $itemTimeString = "-" . $Item->NotificationDays . " day";
            $data['NotificationDays'] = date("Y-m-d", strtotime($itemTimeString, strtotime($data['classDate'])));
        }
        $Item->ItemName = $data['activityName'] ?? $Item->ItemName;
        $Item->ItemPrice = $data['itemPrice'] ??  $Item->ItemPrice;
        $SettingsInfo = new Settings($Item->CompanyNum);
        $data['vat'] = $SettingsInfo->__get("Vat") ?? 17;
        if (empty($SettingsInfo->__get("CompanyVat"))) {
            $data['itemPriceVat'] = round(($Item->ItemPrice / ('1.' . $data['vat'])), 2);
        } else {
            $data['itemPriceVat'] = $Item->ItemPrice;
        }
        $data['vatAmount'] = $Item->ItemPrice - $data['itemPriceVat'];
    }

    /**
     * @param $data
     * @return mixed
     */
    public static function validate($data)
    {
        return Validator::make($data, self::VALIDATION_ARRAY);
    }

    /**
     * @param array $data
     * @param ClientActivities $ClientActivities
     */
    private static function setPropertiesFieldsFromData(array $data, ClientActivities $ClientActivities): void
    {
        foreach (self::PROPERTIES_FIELDS_IN_DATA as $dbName => $fieldName) {
            if(isset($data[$fieldName])) {
                $ClientActivities->$dbName = $data[$fieldName];
            }
        }
    }

    /**
     * @param array $data
     * @return array
     */
    public static function updateClientActivities(array $data): array
    {
        try {
            $validation = self::validate($data);
            if ($validation->fails()) {
                return ['Error' => $validation->messages()->first(), 'status' => self::ERROR_STATUS];
            }
            if(!isset($data['clientActivityId'])) {
                return ['Error' => 'clientActivityId not valid', 'status' => self::ERROR_STATUS];
            }
            /** @var Client $Client */
            $Client = Client::find($data['clientId']);
            /** @var ClientActivities $ClientActivity */
            $ClientActivity = ClientActivities::find($data['clientActivityId']);
            if($ClientActivity->isForMeeting) {
                $classStudioDateId = ClassStudioAct::getClassStudioDateIdMeetingByActivityId($ClientActivity->id);
                $isSuccess = EditMeetingService::changeToCompletedAndShow($classStudioDateId); //todo-add message -> cahnge status
                if($isSuccess) {
                    $ClientActivity->isDisplayed = 1;
                    $isSuccess = $ClientActivity->save();
                }
                if(!$isSuccess) {
                    LoggerService::debug('not Success update classStudioDate status or ca status - ca-id :' . $ClientActivity->id,LoggerService::CATEGORY_ACT_MEETING);
                }
            }
            $Settings = Settings::getByCompanyNum($ClientActivity->CompanyNum);
            if(isset($data['discountAmount'])) {
                $discountType = $data['discountType'] ?? 1; // 1-DISCOUNT_TYPE_PERCENT
                $discountValue = $data['discountValue'] ?? 0;
                $discountAmount = $data['discountAmount'] ?? 0;
                if ((int)$discountType === 1 && $discountValue > 100) {
                    $discountValue = 100;
                    $discountAmount = $ClientActivity->BalanceMoney;
                } elseif ((int)$discountType === 2 && $discountValue > $ClientActivity->BalanceMoney) {
                    $discountValue = $ClientActivity->BalanceMoney;
                    $discountAmount = $ClientActivity->BalanceMoney;
                }
                $ClientActivity->ItemPrice = $ClientActivity->BalanceMoney;
                if((int)$Settings->Vat !== 0 && (int)$Settings->CompanyVat === 0) {
                    $ClientActivity->ItemPriceVat = round($ClientActivity->ItemPrice / ('1.' . $Settings->Vat), 2);
                    $ClientActivity->VatAmount = $ClientActivity->ItemPrice - $ClientActivity->ItemPriceVat;
//                    $ClientActivity->DiscountAmountVat = round($discountAmount / ('1.' . $Settings->Vat),2);
                } else{
                    $ClientActivity->ItemPriceVat = $ClientActivity->BalanceMoney;
                    $ClientActivity->VatAmount = 0;
                }
                $ClientActivity->ItemPrice = $ClientActivity->BalanceMoney;
                $ClientActivity->Discount = $discountValue;
                $ClientActivity->DiscountType = $discountType;
                $ClientActivity->DiscountAmount = $discountAmount;
                $ClientActivity->BalanceMoney -= $discountAmount;
                $ClientActivity->save();

                $Client->updateBalanceAmountNew();
            }
        } catch (Exception $e) {
            return ['Error' => $e->getMessage(), 'status' => self::ERROR_STATUS];
        }
        return ['Status' => self::SUCCESS_STATUS];

    }

    /**
     * @param float $amount
     * @param array $clientActivitiesIds
     */
    public static function updateBalanceMoney(float $amount, array $clientActivitiesIds): void
    {
        if($amount >= 0) {
            foreach ($clientActivitiesIds as $clientActivitiesId) {
                /** @var ClientActivities $clientActivity */
                $clientActivity = ClientActivities::find($clientActivitiesId);
                if ($amount <= 0 || !$clientActivity) {
                    continue;
                }
                $tempAmount = $amount;
                $amount -= $clientActivity->BalanceMoney;
                if ($tempAmount >= $clientActivity->BalanceMoney) {
                    $clientActivity->BalanceMoney = 0;
                } else {
                    $clientActivity->BalanceMoney -= $tempAmount;
                }
                $clientActivity->save();
            }
        } else {
            $amount *= -1;
            foreach ($clientActivitiesIds as $clientActivitiesId) {
                /** @var ClientActivities $clientActivity */
                $clientActivity = ClientActivities::find($clientActivitiesId);
                if ($amount <= 0 || !$clientActivity) {
                    continue;
                }
                $maxCanAdd = $clientActivity->ItemPrice - $clientActivity->BalanceMoney;
                if($maxCanAdd <= 0) {
                    continue;
                }
                $tempAmount = $amount;//10
                if ($maxCanAdd <= $tempAmount) {
                    $clientActivity->BalanceMoney = $clientActivity->ItemPrice;
                    $amount -= $maxCanAdd;
                } else {
                    $clientActivity->BalanceMoney += $tempAmount;
                    $amount = 0;
                }
                $clientActivity->save();
            }
        }
    }

}
