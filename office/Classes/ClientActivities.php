<?php

require_once __DIR__ . "/Utils.php";
require_once __DIR__ . "/ClassStudioAct.php";
require_once __DIR__ . "/Client.php";
require_once __DIR__ . "/ClassesType.php";
require_once __DIR__ . "/Membership.php";
require_once __DIR__ . "/ClassCalendar.php";
require_once __DIR__ . "/Item.php";
require_once __DIR__ . "/ItemRoles.php";
require_once __DIR__ . "/MembershipType.php";
require_once __DIR__ . "/AppSettings.php";
require_once __DIR__ . "/Settings.php";
require_once __DIR__ . "/Docs.php";
require_once __DIR__ . "/ClassStudioDateRegular.php";
require_once __DIR__ . "/Notificationcontent.php";
require_once __DIR__ . "/AppNotification.php";
require_once __DIR__ . "/MembershipFreezeNotifications.php";
require_once __DIR__ . "/ClassStatus.php";
require_once __DIR__ . "/CompanyProductSettings.php";
require_once __DIR__ . "/../services/ClientActivityService.php";
require_once __DIR__ . "/../services/LoggerService.php";
require_once __DIR__ . '/../../app/enums/ClassType/EventType.php';


/**
 * @property $id
 * @property $CompanyNum
 * @property $Brands
 * @property $CardNumber
 * @property $ClientId
 * @property $TrueClientId
 * @property $Department
 * @property $MemberShip
 * @property $ItemId
 * @property $ItemDetailsId
 * @property $ItemText
 * @property $ItemPrice
 * @property $ItemPriceVat
 * @property $ItemPriceVatDiscount
 * @property $Vat
 * @property $VatAmount
 * @property $DiscountType
 * @property $Discount
 * @property $DiscountAmount
 * @property $DiscountAmountVat
 * @property $StartDate
 * @property $VaildDate
 * @property $StudioVaildDate
 * @property $TrueDate
 * @property $StartFreez
 * @property $EndFreez
 * @property $Freez
 * @property $FreezDays
 * @property $BalanceValue
 * @property $TrueBalanceValue
 * @property $ActBalanceValue
 * @property $LimitClass
 * @property $Dates
 * @property $UserId
 * @property $Status
 * @property $BalanceMoney
 * @property $BalanceRefoundMoney
 * @property $TrueBalanceRefoundMoney
 * @property $ReceiptId
 * @property $InvoiceId
 * @property $BalanceValueLog
 * @property $StudioVaildDateLog
 * @property $FreezLog
 * @property $FreezEndLog
 * @property $MemberShipRule
 * @property $TrueBalanceValueStatus
 * @property $CancelStatus
 * @property $CancelDate
 * @property $Reason
 * @property $NotificationDays
 * @property $KevaAction
 * @property $ClientStatus
 * @property $TruePays
 * @property $StudioStartDateLog
 * @property $PayClientId
 * @property $LimitMultiActivity
 * @property $CardStatus
 * @property $FirstDate
 * @property $FirstDateStatus
 * @property $ChangeStatus
 * @property $SalesId
 * @property $RegistrationFees
 * @property $isPaymentForSingleClass
 * @property $isForMeeting
 * @property $isDisplayed
 * @property $ItemQuantity
 *
 * Class ClientActivities
 */
class ClientActivities extends \Hazzard\Database\Model
{
    const STATUS_ACTIVE = 0;
    const STATUS_FREEZE = 1;
    const STATUS_CANCEL = 2;
    const STATUS_CLOSED_END = 3;

    const MAX_TRAIL_ACTIVITIES_PER_CLIENT = 3;

    const DISPLAYED_ON = 1;
    const DISPLAYED_OFF = 0;

    const IS_FOR_MEETING = 1;
    const IS_NOT_FOR_MEETING = 0;


    protected $table = "boostapp.client_activities";

    /**
     * @param $attributes
     */
    public function __construct($attributes = [])
    {
        if (is_numeric($attributes)) {
            $user = self::find($attributes);
            if ($user) {
                $this->fill($user->toArray());
                $this->exists = true;
            }
            $attributes = [];
        }

        parent::__construct($attributes);
    }

    public function update($data)
    {
        return DB::table($this->table)->where('id', $this->id)->update($data);
    }

    public function setObject ($clientAct) {
        if (!empty($clientAct)) {
            foreach ($clientAct as $key => $value) {
                $this->__set($key, $value);
            }
        }
    }

    public function getActiveClientActivityByClientId($ClientId, $CompanyNum)
    {
        $Client = DB::table($this->table)
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('Status', '=', 0)
            ->where('ClientId', '=', $ClientId)
            ->first();
        return $Client;
    }

    public function getClientFamilyMemberships($ClientId, $CompanyNum)
    {
        $result = DB::table($this->table)
            ->where('ClientId', '=', $ClientId)
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('TrueClientId', '!=', '0')->get();
        return $result;
    }

    public function getClientActivByClientIdArray($CompanyNum, $clientIdArray)
    {
        $resultArray = DB::table($this->table)
            ->select('TrueDate', 'ItemText', 'TrueBalanceValue', 'TrueClientId', 'ClientId', 'Department')
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('Status', '=', 0)
            ->whereIn('ClientId', $clientIdArray)
            ->get();

        $tempFamilyArray = DB::table($this->table)
            ->select('TrueDate', 'ItemText', 'TrueBalanceValue', 'TrueClientId', 'ClientId', 'Department')
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('Status', '=', 0)
            ->where('TrueClientId', '!=', '0')
            ->get();

        foreach ($tempFamilyArray as $familySubs) {
            $familyMembers = explode(",", $familySubs->TrueClientId);
            foreach ($familyMembers as $member) {
                if (in_array($member, $clientIdArray)) {
                    $tempObj = clone($familySubs);
                    $tempObj->TrueClientId = $member;
                    $resultArray[] = $tempObj;
                }
            }
        }
        return $resultArray;
    }

    public static function getClientActivities($clientId)
    {
        return
            self::where(function ($q) use ($clientId) {
                $q->whereRaw('FIND_IN_SET(' . $clientId . ', TrueClientId)')
                    ->orWhere('ClientId', $clientId);
            })
                ->where('CompanyNum', Auth::user()->CompanyNum)
                ->where('Status', 0)
//                ->where('StartDate', '<=', $classDate) //התחלת שיעור אחרי תחילת המנוי
//                ->where(function ($qu) use ($classDate){
//                     $qu->whereNull('TrueDate')->whereIn('Department', [2,3])->where('TrueBalanceValue', '>', 0)  //כרטסיה או התנסות  והכרטיסיה לא ריקה
//                            ->orWhere('TrueDate', '>=', $classDate); //הזמן תוקף יותר מהשיעור
//                })
                ->get();
    }

    public function getActivityById($activityId, $CompanyNum)
    {
        $activity = DB::table($this->table)
            ->where('id', $activityId)
            ->where('CompanyNum', '=', $CompanyNum)
            ->first();
        return $activity;
    }

    public function deleteClientActivityById($activityId)
    {
        DB::table($this->table)->where('id', $activityId)->delete();
    }

    /**
     * Cancel class including the business logic
     * @param ClassStudioAct $classStudioAct
     * @param $CompanyNum
     * @param $NewStatus
     * @return ClassStatus|null
     */
    public static function CancelClassReturnBalance($classStudioAct, $CompanyNum, $NewStatus): ?ClassStatus
    {
        /** @var ClientActivities $ClientBalanceValue */
        $ClientBalanceValue = self::where('CompanyNum', '=', $CompanyNum)
            ->where('id', '=', $classStudioAct->ClientActivitiesId)
            ->first();
        $TrueBalanceValue = $ClientBalanceValue->TrueBalanceValue;
        $ActBalanceValue = $ClientBalanceValue->ActBalanceValue;
        /** @var ClassStatus $CheckOldStatus */
        $CheckOldStatus = ClassStatus::where('id', '=', $classStudioAct->Status)->first();
        /** @var ClassStatus $CheckNewStatus */
        $CheckNewStatus = ClassStatus::where('id', '=', $NewStatus)->first();

        if ($ClientBalanceValue->Department == '2' || $ClientBalanceValue->Department == '3') {
            if ($CheckOldStatus->Act == '0' && $CheckNewStatus->Act != '0') {
                $FinalTrueBalanceValue = $TrueBalanceValue + 1; // מחזיר ניקוב
                $FinalActBalanceValue = $ActBalanceValue + 1;
            } elseif ($CheckOldStatus->Act != '0' && $CheckNewStatus->Act == '0') {
                $FinalTrueBalanceValue = $TrueBalanceValue - 1; // מחסיר ניקוב
                $FinalActBalanceValue = $ActBalanceValue - 1;
            } else {
                $FinalTrueBalanceValue = $TrueBalanceValue; // ללא שינוי
                $FinalActBalanceValue = $ActBalanceValue;
            }

            self::where('CompanyNum', '=', $CompanyNum)
                ->where('id', '=', $ClientBalanceValue->id)
                ->update(array('TrueBalanceValue' => $FinalTrueBalanceValue));

            if ($classStudioAct->ActStatus == '1') {
                self::where('CompanyNum', '=', $CompanyNum)
                    ->where('id', '=', $ClientBalanceValue->id)
                    ->update(array('ActBalanceValue' => $FinalActBalanceValue));
            }
        }
        return $CheckNewStatus;

    }

    public function refundCanceledClass($clients, $newStatus = 5, $logs = true)
    {
        $company = Company::getInstance(false);
        $AppSettings = DB::table('appsettings')->where('CompanyNum', '=', $company->__get("CompanyNum"))->first();
        foreach ($clients as $client) {
            /**
             * @var  $client ClassStudioAct
             * @var  $clientBalanceObj ClientActivities
             */
//            $clientBalance = DB::table($this->table)->where('CompanyNum', '=', $company->__get("CompanyNum"))->where('id', '=', $client->__get("ClientActivitiesId"))->first();
//            $clientBalanceObj = $this->arrayIntoObject($clientBalance, "ClientActivities");
            $clientBalanceObj = self::find($client->__get("ClientActivitiesId"));
            $checkStatus = DB::table('class_status')->where('id', '=', $client->__get("Status"))->orWhere('id', '=', $newStatus)->get();
            $checkNewStatus = "";
            $checkOldStatus = "";

            $ReClass = '1';
            $KnasOption = '0';
            $KnasOptionVule = '0.00';
            $WatingListSort = 0;


            foreach ($checkStatus as $status) {
                if ($status->id == $newStatus) {
                    $checkNewStatus = $status;
                } else {
                    $checkOldStatus = $status;
                }
            }
            if ($clientBalanceObj->__get("Department") == '1') { //membership
                if ($newStatus == '4' || $newStatus == '8') {
                    $KnasOption = '1';
                    $KnasOptionVule = $AppSettings->MemberShipLimitMoney;
                }
            } elseif ($clientBalanceObj->__get("Department") == '2' || $clientBalanceObj->__get("Department") == '3') {
                if ($checkOldStatus->Act == '0' && $checkNewStatus->Act == '0') {
                    $FinalTrueBalanceValue = $clientBalanceObj->__get("TrueBalanceValue"); // ללא שינוי
                    $FinalActBalanceValue = $clientBalanceObj->__get("ActBalanceValue");
                } elseif ($checkOldStatus->Act == '0' && $checkNewStatus->Act == '1') {
                    $FinalTrueBalanceValue = $clientBalanceObj->__get("TrueBalanceValue") + 1; // מחזיר ניקוב
                    $FinalActBalanceValue = $clientBalanceObj->__get("ActBalanceValue") + 1;
                } elseif ($checkOldStatus->Act == '0' && $checkNewStatus->Act == '2') {
                    $FinalTrueBalanceValue = $clientBalanceObj->__get("TrueBalanceValue") + 1; // מחזיר ניקוב
                    $FinalActBalanceValue = $clientBalanceObj->__get("ActBalanceValue") + 1;
                } elseif ($checkOldStatus->Act == '1' && $checkNewStatus->Act == '0') {
                    $FinalTrueBalanceValue = $clientBalanceObj->__get("TrueBalanceValue") - 1; // מחסיר ניקוב
                    $FinalActBalanceValue = $clientBalanceObj->__get("ActBalanceValue") - 1;
                } elseif ($checkOldStatus->Act == '1' && $checkNewStatus->Act == '1') {
                    $FinalTrueBalanceValue = $clientBalanceObj->__get("TrueBalanceValue"); // ללא שינוי
                    $FinalActBalanceValue = $clientBalanceObj->__get("ActBalanceValue");
                } elseif ($checkOldStatus->Act == '1' && $checkNewStatus->Act == '2') {
                    $FinalTrueBalanceValue = $clientBalanceObj->__get("TrueBalanceValue"); // ללא שינוי
                    $FinalActBalanceValue = $clientBalanceObj->__get("ActBalanceValue");
                } elseif ($checkOldStatus->Act == '2' && $checkNewStatus->Act == '0') {
                    $FinalTrueBalanceValue = $clientBalanceObj->__get("TrueBalanceValue") - 1; // מחסיר ניקוב
                    $FinalActBalanceValue = $clientBalanceObj->__get("ActBalanceValue") - 1;
                } elseif ($checkOldStatus->Act == '2' && $checkNewStatus->Act == '1') {
                    $FinalTrueBalanceValue = $clientBalanceObj->__get("TrueBalanceValue"); // ללא שינוי
                    $FinalActBalanceValue = $clientBalanceObj->__get("ActBalanceValue");
                } elseif ($checkOldStatus->Act == '2' && $checkNewStatus->Act == '2') {
                    $FinalTrueBalanceValue = $clientBalanceObj->__get("TrueBalanceValue"); // ללא שינוי
                    $FinalActBalanceValue = $clientBalanceObj->__get("ActBalanceValue");
                } else {
                    return;
                }


                DB::table('client_activities')->where('CompanyNum', '=', $company->__get("CompanyNum"))->where('id', '=', $clientBalanceObj->__get("id"))->update(array('TrueBalanceValue' => $FinalTrueBalanceValue));
                $Cards = $FinalTrueBalanceValue . ' / ' . $clientBalanceObj->__get("BalanceValue");
                if ($client->__get("ActStatus") == '1') {
                    DB::table('client_activities')->where('CompanyNum', '=', $company->__get("CompanyNum"))->where('id', '=', $clientBalanceObj->__get("id"))->update(array('ActBalanceValue' => $FinalActBalanceValue));
                }
            }

            $UserId = Auth::user()->id;
            if ($newStatus == '10') {
                $ReClass = '2';
            }

            /** @var ClassStudioAct $classStudioAct */
            $classStudioAct = ClassStudioAct::find($client->__get("id"));
            $classStudioAct->changeStatus($newStatus);
            $classStudioAct->update([
                'ReClass' => $ReClass,
                'KnasOption' => $KnasOption,
                'KnasOptionVule' => $KnasOptionVule,
                'WatingListSort' => $WatingListSort,
            ]);

            if ($logs) {
                $Date = date('Y-m-d');
                $Time = date('H:i:s');
                $Dates = date('Y-m-d H:i:s');

                $Template = DB::table('notificationcontent')->where('CompanyNum', '=', $company->__get("CompanyNum"))->where('Type', '=', '18')->first();
                if ($client->__get("TrueClientId") == '0') {
                    $clientObj = new Client($client->__get("ClientId"));
                } else {
                    $clientObj = new Client($client->__get("TrueClientId"));
                }

                $ClassDate_Not = with(new DateTime($client->__get("ClassDate")))->format('d/m/Y');
                $ClassTime_Not = with(new DateTime($client->__get("ClassStartTime")))->format('H:i');
                $ClassName_Not = $client->__get("ClassName");

                $Content1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], $company->__get("AppName"), $Template->Content);
                $Content2 = str_replace(Notificationcontent::REPLACE_ARR["name_table"], $clientObj->__get("CompanyName"), $Content1);
                $Content3 = str_replace(Notificationcontent::REPLACE_ARR["first_name"], $clientObj->__get("FirstName"), $Content2);
                $Content4 = str_replace(Notificationcontent::REPLACE_ARR["cal_new_class_type_name"], $ClassName_Not, $Content3);
                $Content5 = str_replace(Notificationcontent::REPLACE_ARR["class_date_single"], $ClassDate_Not, $Content4);
                $Content6 = str_replace(Notificationcontent::REPLACE_ARR["time_of_a_class"], $ClassTime_Not, $Content5);
                $ContentTrue = $Content6;

                $Subject = $Template->Subject;
                if ($Template->Status != 1) {
                    $AddNotification = DB::table('appnotification')->insertGetId(
                        array('CompanyNum' => $company->__get("CompanyNum"), 'ClientId' => $clientObj->__get("id"),
                            'Type' => '0', 'Subject' => $Subject, 'Text' => $ContentTrue, 'Dates' => $Dates, 'UserId' => $UserId, 'Date' => $Date, 'Time' => $Time));
                }
                $ClientRegister = DB::table('classstudio_act')->where('ClassId', '=', $client->__get("ClassId"))->where('CompanyNum', '=', $client->__get("CompanyNum"))->where('StatusCount', '=', '0')->count();
                DB::table('classlog')->insertGetId(
                    array('CompanyNum' => $company->__get("CompanyNum"), 'ClassId' => $client->__get("ClassId"),
                        'ClientId' => $clientObj->__get("id"), 'Status' => $checkNewStatus->Title, 'UserName' => $UserId, 'numOfClients' => $ClientRegister));

                $classCalendar = new ClassCalendar($client->__get("ClassId"));
                $SectioClassInfo = DB::table('sections')->where('id', '=', $classCalendar->__get("Floor"))->where('CompanyNum', '=', $company->__get("CompanyNum"))->first();
                $ClassInfoOne = $classCalendar->__get("ClassName") . ' בתאריך: ' . $classCalendar->__get("StartDate") . ', בשעה: ' . $classCalendar->__get("StartTime") . ', ביום: ' . $classCalendar->__get("Day") . ', בחדר: ' . htmlentities($SectioClassInfo->Title);
                CreateLogMovement('ביטל את השיעור ' . $ClassInfoOne, '0');
            }
        }
    }

    public function GetFreezClients($companyNum)
    {
        $ClientsFreez = DB::table($this->table)
            ->where('CompanyNum', '=', $companyNum)
            ->where('freez', '=', 1)->where('status', '=', 0)->count();
        return $ClientsFreez;
    }

    public function getExpiringClients($companyNum)
    {
        $res = DB::table($this->table)
            ->join("membership_type", "MemberShip", "=", "membership_type.id")
            ->join("client", "ClientId", "=", "client.id")
            ->select("membership_type.Type", "client.FirstName", "client.LastName", "client.ContactMobile", $this->table . ".*")
            ->where($this->table . '.CompanyNum', $companyNum)->where($this->table . '.Status', 0)->where('ClientStatus', 0)->where('KevaAction', 0)->where('client.Status', '=', 0)
            ->where(function ($query) {
                $query->whereIn('Department', [1, 2])
                    ->where('TrueDate', '>=', date('Y-m-d'))
                    ->where(function ($query) {
                        $query
                            ->where('TrueDate', '<=', date('Y-m-d', strtotime("+5 days")))
                            ->Orwhere('NotificationDays', '<=', date('Y-m-d'))->whereRaw('TrueDate - interval 30 day < NotificationDays');
                    })
                    ->Orwhere('TrueBalanceValue', '=', 1)->where('BalanceValue', '>', 1)->where('Department', '=', 2)
                    ->where(function ($query) {
                        $query->where('TrueDate', '>=', date('Y-m-d'))->OrwhereNull('TrueDate');
                    });
            })->get();
        return $res;
    }

    public function getInvalidMemberships($companyNum)
    {
        $res = DB::table($this->table)
            ->join("membership_type", $this->table . '.MemberShip', "=", "membership_type.id")
            ->join("client", "ClientId", "=", "client.id")
            ->select("membership_type.Type", $this->table . ".id", $this->table . ".CompanyNum", $this->table . ".ClientId", $this->table . ".TrueClientId", $this->table . ".ItemText", $this->table . ".Department", $this->table . ".MemberShip", $this->table . ".BalanceValue", $this->table . ".TrueBalanceValue", $this->table . ".TrueDate")
            ->where($this->table . '.TrueDate', '<', date('Y-m-d'))->where($this->table . '.Freez', '!=', 1)->where($this->table . '.Department', '=', '1')->where($this->table . '.CompanyNum', '=', $companyNum)->where($this->table . '.Status', '=', '0')->where($this->table . '.ClientStatus', '=', '0')->where($this->table . '.KevaAction', '=', '0')->where('client.Status', '=', 0) // double checks of client.Status
            ->Orwhere($this->table . '.TrueDate', '<', date('Y-m-d'))->where($this->table . '.Freez', '=', 1)->where($this->table . '.EndFreez', '<=', date('Y-m-d'))->where($this->table . '.Department', '=', '1')->where($this->table . '.CompanyNum', '=', $companyNum)->where($this->table . '.Status', '=', '0')->where($this->table . '.ClientStatus', '=', '0')->where($this->table . '.KevaAction', '=', '0')->where('client.Status', '=', 0)// double checks of client.Status
            ->Orwhere($this->table . '.TrueBalanceValue', '<=', '0')->where($this->table . '.Department', '=', '2')->where($this->table . '.CompanyNum', '=', $companyNum)->where($this->table . '.Status', '=', '0')->where($this->table . '.ClientStatus', '=', '0')->where($this->table . '.KevaAction', '=', '0')->where('client.Status', '=', 0)
            ->Orwhere($this->table . '.TrueDate', '<', date('Y-m-d'))->where($this->table . '.Freez', '!=', 1)->where($this->table . '.Department', '=', '2')->where($this->table . '.CompanyNum', '=', $companyNum)->where($this->table . '.Status', '=', '0')->where($this->table . '.ClientStatus', '=', '0')->where($this->table . '.KevaAction', '=', '0')->where('client.Status', '=', 0)
            ->Orwhere($this->table . '.TrueDate', '<', date('Y-m-d'))->where($this->table . '.Freez', '=', 1)->where($this->table . '.EndFreez', '<=', date('Y-m-d'))->where($this->table . '.Department', '=', '2')->where($this->table . '.CompanyNum', '=', $companyNum)->where($this->table . '.Status', '=', '0')->where($this->table . '.ClientStatus', '=', '0')->where($this->table . '.KevaAction', '=', '0')->where('client.Status', '=', 0)
            ->get();
        return $res;
    }

    public function getActiveMemberships($companyNum, $Client)
    {

        return DB::table($this->table)
            ->where('CompanyNum', $companyNum)
            ->where('id', '!=', $Client->id)
            ->where('MemberShip', $Client->MemberShip)
            ->where('Department', '!=', 4)
            ->where('Status', 0)
            ->where('ClientStatus', 0)
            ->where('ClientId', $Client->ClientId)
            ->where(function ($query) {
                $query->where('KevaAction', 1)
                    ->Orwhere('NotificationDays', '>', date('Y-m-d'))
                    ->where('StartDate', '<=', date('Y-m-d'))
                    ->where('TrueDate', '>=', date('Y-m-d'));
            })->count();
    }

    public function getBulkActiveMemberships($CompanyNum) {
        return DB::table($this->table)
            ->where('CompanyNum', $CompanyNum)
            ->where('Department', '!=', 4)
            ->where('Status', 0)
            ->where('ClientStatus', 0)
            ->where(function ($query) {
                $query->where('KevaAction', 1)
                    ->Orwhere('NotificationDays', '>', date('Y-m-d'))
                    ->where('StartDate', '<=', date('Y-m-d'))
                    ->where('TrueDate', '>=', date('Y-m-d'));
            })->get();
    }

    /**
     * @param $memberships
     * @param $filters
     * @return array
     */
    public static function filterActiveMemberships($memberships, $filters) {
        $uniqueClientIds = [];
        $filters = array_filter($memberships, static function ($item) use (&$uniqueClientIds, $filters) {
            foreach ($filters as $filter) {
                if ((int)$item->ClientId === (int)$filter->ClientId &&
                    (int)$item->id !== (int)$filter->id &&
                    $item->MemberShip == $filter->MemberShip) {
                    return false;
                }
            }

            $uniqueClientIds[$item->ClientId] = true;
            return true;
        });
        return [
            'filters' => $filters,
            'uniqueCount' => count(array_keys($uniqueClientIds))
        ];
    }

    /**
     * @param $data array [clientId, itemId] Optional: salesId, startDate, calcType, endDate, itemPrice, activityName
     * @return mixed array [Status => end of action status, Error\ClientActivityId => Error details\New Client Activity Id]
     */
    public static function assignMembership($data) {
        return ClientActivityService::assignMembership($data);
    }

    public static function assignMembershipGetId($data) {
        $assignRes = self::assignMembership($data);
        if (empty($assignRes['Status'])) {
            throw new LogicException('Error assigning membership');
        } else {
            return $assignRes['ClientActivityId'];
        }
    }

    public function getFreezesByDates($start, $end)
    {
        $companyNum = Auth::user()->CompanyNum;
        $resArr = array("data" => array());
        $activities = DB::table($this->table)->where('CompanyNum', $companyNum)->whereIn('Freez', [1, 2])->whereNotNull('FreezLog')->where('Department', '!=', 4)->get();
        foreach ($activities as $key => $activity) {

            $client = new Client($activity->ClientId);
            if ($client) {
                $freezLog = json_decode($activity->FreezLog, true);

//                $item = new Item($activity->ItemId);
                $item = Item::find($activity->ItemId);
                if ($item && $item->__get('Vaild') > 0) {
                    if ($item->__get('Vaild_Type') == 3) {    /// months
                        $pricePerDay = number_format((float)$activity->ItemPrice / ($item->__get('Vaild') * 30), 2);
                        $pricePerMonth = number_format((float)$activity->ItemPrice / ((int)$item->__get('Vaild')), 2);
                    } elseif ($item->__get('Vaild_Type') == 2) {  /// weeks
                        $pricePerDay = number_format((float)$activity->ItemPrice / ($item->__get('Vaild') * 7), 2);
                        $pricePerMonth = number_format((float)$activity->ItemPrice / ((int)($item->__get('Vaild') * 7) / 30), 2);
                    } elseif ($item->__get('Vaild_Type') == 1) {    /// days
                        $pricePerDay = number_format((float)$activity->ItemPrice / $item->__get('Vaild'), 2);
                        $pricePerMonth = number_format((float)$activity->ItemPrice / ((int)$item->__get('Vaild') * 30), 2);
                    } else {
                        $pricePerDay = null;
                    }
                } elseif (!empty($activity->VaildDate) && !empty($activity->StartDate)) {
                    $startDate = strtotime($activity->StartDate);
                    $endDate = strtotime($activity->VaildDate);
                    $days_between = ceil(abs($endDate - $startDate) / 86400);
                    $pricePerDay = number_format((float)$activity->ItemPrice / $days_between, 2);
                    $pricePerMonth = number_format((float)$pricePerDay * 30, 2);
                } else {
                    $pricePerDay = null;
                    $pricePerMonth = null;
                }

                foreach ($freezLog['data'] as $k => $val) {

                    if ((strtotime($val['EndFreez']) <= strtotime($end) && strtotime($val['EndFreez']) >= strtotime($start)) || (strtotime($val['StartFreez']) >= strtotime($start) && strtotime($val['StartFreez']) <= strtotime($end)) || (strtotime($val['StartFreez']) >= strtotime($start) && strtotime($val['EndFreez']) <= strtotime($end)) || (strtotime($val['StartFreez']) <= strtotime($start) && strtotime($val['EndFreez']) >= strtotime($end))) {
                        $actArr = array();
                        $startFreez = date('d/m/Y', strtotime($val['StartFreez']));
                        $endFreez = date('d/m/Y', strtotime($val['EndFreez']));
                        $freezDays = $val['FreezDays'] > 0 ? (int)$val['FreezDays'] : 0;
                        $reason = $val['Reason'];
                        // $endPeriod = date('Y-m-d' ,$endDate) > date('Y-m-d') ? strtotime(date('Y-m-d')) : strtotime(date('Y-m-d' ,$endDate)); 
                        // $exploit = ($endPeriod / 86400) - $freezDays;

                        $actArr[0] = '<a href="ClientProfile.php?u=' . $client->__get('id') . '">' . $client->__get('CompanyName') . '</a>';
                        $actArr[1] = $client->__get('ContactMobile');
                        $actArr[2] = $activity->ItemText;
                        $actArr[3] = date('d/m/Y', strtotime($activity->StartDate));
                        $actArr[4] = !empty($activity->VaildDate) ? date('d/m/Y', strtotime($activity->VaildDate)) : '--';
                        $actArr[5] = $startFreez;
                        $actArr[6] = $endFreez;
                        $actArr[7] = !empty($freezDays) ? (int)$freezDays : 0;
                        $actArr[8] = $reason;
                        $actArr[9] = $activity->ItemPrice;
                        $actArr[10] = $pricePerMonth ? $pricePerMonth : '--';
                        $actArr[11] = $pricePerDay ? $pricePerDay : '--';
                        $actArr[12] = $pricePerDay && $freezDays ? number_format((float)$pricePerDay * (int)$freezDays, 2) : '--';
                        array_push($resArr["data"], $actArr);
                    }

                }


            }
        }
        return $resArr;
    }

    /**
     * @param $activityId
     * @param $clientId
     * @param $companyNum
     * @param $ClassNameType
     * @return array|null
     */
    public static function findActiveMembership($activityId, $clientId, $companyNum, $ClassNameType): ?array
    {
        $activities = self::where('id', '!=', $activityId)
            ->where('ClientId', '=', $clientId)
            ->where('CompanyNum', '=', $companyNum)
            ->where('Status', '=', 0)
            ->where('isPaymentForSingleClass', 0)
            ->where('Freez', '!=', 1)
            ->where(function ($q) {
                $q->where('Department', '=', 1)->where('TrueDate', '>', date('Y-m-d'))
                    ->Orwhere('Department', '=', 2)->whereNotNull('TrueDate')->where('TrueDate', '>', date('Y-m-d'))->where('TrueBalanceValue', '>', 0)
                    ->Orwhere('Department', '=', 2)->whereNull('TrueDate')->where('TrueBalanceValue', '>', 0);
            })->orderBy('CardNumber', 'ASC')->get();
        if (!empty($activities)) {
            /** @var ClientActivities $activity */
            foreach ($activities as $activity) {
                $CheckItemsRole = $activity->getRoleByClassType($ClassNameType);
                if ($CheckItemsRole) {
                    $groupId = $CheckItemsRole->GroupId;
                    $arr = array(
                        'ClientActivitiesId' => $activity->id,
                        'Department' => $activity->Department,
                        'MemberShip' => $activity->MemberShip,
                        'ItemText' => $activity->ItemText,
                        'CardNumber' => $activity->CardNumber
                    );
                    if ($groupId) {
                        $arr["TrueClasess"] = $groupId;
                    }
                    if (in_array($activity->Department, [2, 3])) {
                        $arr['TrueBalanceValue'] = $activity->TrueBalanceValue - 1;
                    }
                    return $arr;
                }
            }
            return null;
        } else {
            return null;
        }
    }

    public function isActiveClient($ClientId)
    {
        $CompanyNum = Auth::user()->CompanyNum;

        $activeMembership = DB::table($this->table)
            ->where('ClientId', '=', $ClientId)
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('Status', '=', '0')
            ->where(function ($q) {
                $q->where('Department', '=', '1')->where('TrueDate', '>=', date('Y-m-d'))
                    ->Orwhere('Department', '=', '2')->where('TrueBalanceValue', '>', '0')->whereNull('TrueDate')
                    ->Orwhere('Department', '=', '2')->where('TrueBalanceValue', '>', '0')->where('TrueDate', '>=', date('Y-m-d'));
            })
            ->count();

        return $activeMembership > 0;
    }


    public function membershipFrozenInfo($CompanyNum)
    {
        $OpenTables = DB::table('client_activities')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('Freez', '=', '1')->orderBy('ItemText', 'ASC')->get();
        $resArr = array("data" => array());
        foreach ($OpenTables as $Task) {
            if ($Task->Brands != 0) {
                $branch = DB::table('brands')->where('CompanyNum', '=', $CompanyNum)->where('id', $Task->Brands)->first();
                $Brands = $branch->BrandName ?? lang('primary_branch');
            } else {
                $Brands = lang('primary_branch');
            }
            if ($Task->MemberShip == "BA999") {
                $Type = lang('without_department');
            } else {
                $membership_type = DB::table('membership_type')->where('CompanyNum', '=', $CompanyNum)->where('id', $Task->MemberShip)->first();
                $Type = $membership_type->Type ?? lang('without_department');
            }
            $ClientUserNameLogs = DB::table('client')->where('CompanyNum', '=', $CompanyNum)->where('id', $Task->ClientId)->first();
            $ClientUserNameLog = '<a href="ClientProfile.php?u=' . $ClientUserNameLogs->id . '"><span class="text-dark">' . $ClientUserNameLogs->CompanyName . '</span></a>';
            $TaskTitle = $Task->ItemText;
            $membership = DB::table('membership')->where('id', $Task->Department)->first();

            $reportArray[0] = $ClientUserNameLog;
            $reportArray[1] = $ClientUserNameLogs->ContactMobile;
            $reportArray[2] = $TaskTitle;
            $reportArray[3] = $membership->MemberShip;
            $reportArray[4] = $Type;
            $reportArray[5] = $Brands;
            $reportArray[6] = (new DateTime($Task->StartFreez))->format('d/m/Y');
            $reportArray[7] = (new DateTime($Task->EndFreez))->format('d/m/Y');
            $reportArray[8] = $Task->FreezDays . lang('days');
            $reportArray[9] = '<a href="javascript:CancelFreez(' . $Task->id . ', ' . $Task->ClientId . ');"> ' . lang('unfreeze_membership') . ' </a>';


            array_push($resArr["data"], $reportArray);
        }
        return $resArr;
    }

    /**
     * @param $classTypeId
     * @return bool
     */
    public function isClassExistOnMembership($classTypeId): bool
    {
        return ItemRoles::isClassTypeMatchToItem($this->CompanyNum, $this->ItemId, $classTypeId);
    }


    /**
     * @param $classTypeId
     * @return mixed
     */
    public function getRoleByClassType($classTypeId)
    {
        return ItemRoles::getFirstGroupClassByItemIdAndClassType($this->CompanyNum, $this->ItemId, $classTypeId);
    }


    //Check if $this Client Activities assignment exceeding limits for $ClassId
    //Return array ["Status" => 0: Exceed with override option, 1: Succeed, 2: Exceed without override option,
    //              "Message" => More info about output]
    public function checkMembershipLimitations($ClassId, $ClientId, $isMeeting = false, $isForWaitingList = false)
    {
        $res = require 'subClasses/checkMembershipLimit.php';
        return $res;
    }

    public function updateActivityToSingleClass($activityId, $classId)
    {
        $studioDateObj = new ClassStudioDate($classId);
        DB::table($this->table)->where('id', $activityId)->update(
            ['ItemText' => $studioDateObj->__get('text') .
                ': ' . $studioDateObj->__get('Day') .
                ' ' . date('d/m', strtotime($studioDateObj->__get('StartDate')))]
        );
    }

    /**
     * @param $dateFrom Date
     * @param $dateTo Date
     * @return array[] array
     */
    public function getSalesReports($dateFrom, $dateTo)
    {
        $CompanyNum = Auth::user()->CompanyNum;

        $OpenTables = DB::table($this->table)
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('Status', '!=', '2')
            ->whereBetween('Dates', array($dateFrom, $dateTo))
            ->orderBy('Dates', 'ASC')->get();

        $resArray = array("data" => array());
        foreach ($OpenTables as $Task) {
            $tempArr = array();

            $ClientObj = new Client($Task->ClientId);
            $ClientLink = '<a href="ClientProfile.php?u=' . $ClientObj->__get('id') . '"><span class="text-dark">' . $ClientObj->__get('CompanyName') . '</span></a>';

            $membership = Membership::find($Task->Department);

            if ($Task->MemberShip == 'BA999') {
                $Type = lang('without_department');
            } else {
                $membership_type = new MembershipType();
                $membership_type = $membership_type->getRow($Task->MemberShip);
                $Type = $membership_type->Type ?? lang('no_membership_type');
            }

            if ($Task->Brands == 0) {
                $Brands = lang('primary_branch');
            } elseif ($Task->Brands) {
                $brandsTypes = new Brand();
                $brandsTypes = $brandsTypes->getAllByCompanyNum($CompanyNum);
                foreach ($brandsTypes as $brandsType) {
                    $Brands = $brandsType->id == $Task->Brands ? $brandsType->BrandName : null;
                }
            }

            if ($Task->ItemPrice >= '0.00') {
                $StatusClass = 'text-primary';
            } else {
                $StatusClass = 'text-danger';
            }

            if ($Task->BalanceMoney > '0.00') {
                $StatusSubClass = 'text-danger';
            } else {
                $StatusSubClass = 'text-primary';
            }


            $tempArr[0] = with(date('d/m/Y', strtotime($Task->Dates)));
            $tempArr[1] = $Task->ItemText;
            $tempArr[2] = $membership->MemberShip;
            $tempArr[3] = $Type;
            $tempArr[4] = '<span dir="ltr" class="' . $StatusClass . '"> ' . $Task->ItemPrice . ' </span> <input type="hidden" class="TotalAmounts"  name="Amounts" value="' . $Task->ItemPrice . '">';
            $tempArr[5] = '<span dir="ltr" class="' . $StatusSubClass . '"> ' . $Task->BalanceMoney . ' </span> <input type="hidden" class="TotalBalanceMoney"  name="BalanceMoney" value="' . $Task->BalanceMoney . '">';
            $tempArr[6] = $Brands;
            $tempArr[7] = $ClientLink;
            $tempArr[8] = $ClientObj->ContactMobile;

            array_push($resArray["data"], $tempArr);
        }
        return $resArray;
    }

    public function cardReminding()
    {
        $cards = $this->getCardsforReminding();
        $arrayCardStatus1 = [];
        $arrayCardStatus2 = [];

        foreach ($cards as $card) {
            $lessonsLeft = $card->TrueBalanceValue;
            $checkBalance = $this->checkTrueBalance($card->BalanceValueLog, $card->BalanceValue, $card->totalLessons, $card->id, $card->TrueBalanceValue);
            if ($checkBalance == 0) {
                continue;
            } elseif ($checkBalance == 1) {
                $lessonsLeft = 0;
            }
            if ($lessonsLeft == 1) {        //send one last class in card
                $cardStatusNew = 1;
                $notificationType = 8;

            } else {        //send card is over
                $cardStatusNew = 2;
                $notificationType = 9;
            }
            $settings = (new Settings())->getSettings($card->CompanyNum);
            if ($settings && $settings->Status == 0) {
                $Template = (new Notificationcontent())->getByTypeAndCompany($card->CompanyNum, $notificationType);
                if ($Template && $Template->Status != 1) {
                    $CheckSameMembership = $this->getActiveSubscription($card->id, $card->CompanyNum, $card->ClientId, $card->MemberShip);
                    if (!$CheckSameMembership) {
                        (new AppNotification())->sendStandartNotification(
                            $card->CompanyNum,
                            $card->ClientId,
                            $settings->AppName,
                            $Template->SendOption,
                            $Template->SendStudioOption,
                            $Template->Subject,
                            $Template->Content,
                            $card->ItemText,
                            date('Y-m-d'),
                            date('Y-m-d H:i:s'),
                            date('08:00:00')
                        );
                    }
                }

            }
            if ($cardStatusNew == 1) {
                $arrayCardStatus1[] = $card->id;
            } else {
                $arrayCardStatus2[] = $card->id;
            }

        }
        DB::table('client_activities')
            ->whereIn('id', $arrayCardStatus1)
            ->update(array('CardStatus' => 1));

        DB::table('client_activities')
            ->whereIn('id', $arrayCardStatus2)
            ->update(array('CardStatus' => 2));
    }

    public function getCardsforReminding()
    {
        return DB::table('client_activities as ca')
            ->leftjoin('classstudio_act as act', 'ca.id', '=', 'act.ClientActivitiesId')
            ->select('ca.id', 'ca.BalanceValue', 'ca.BalanceValueLog', 'ca.CompanyNum', 'ca.ItemText', 'ca.ClientId', 'ca.MemberShip', 'ca.TrueBalanceValue', DB::raw('count(*) as totalLessons'))
            ->where('ca.Status', '=', 0)
            ->where('ca.Department', 2)
            ->where('ca.BalanceValue', '>', 1)
            ->where(
                function ($query) {
                    $query->where('ca.TrueBalanceValue', '=', 1)->where('ca.CardStatus', '=', 0)
                        ->orWhere('ca.TrueBalanceValue', '<', 1)->where('ca.CardStatus', '<=', 1);
                }
            )
            ->where('ca.ClientStatus', '=', 0)
            ->where('ca.Freez', '!=', 1)
            ->where('act.ClassDate', '<', date('Y-m-d'))
            ->whereIn('act.Status', [1, 2, 4, 6, 8, 11, 12, 15, 21])
            ->groupBy('ca.id')
            ->get();
    }


    public function checkTrueBalance($BalanceValueLog, $BalanceValue, $totalLessons, $id, $TrueBalanceValue)
    {
        $Loops = json_decode($BalanceValueLog, true);
        if (isset($Loops)) {
            foreach ($Loops['data'] as $key => $val) {
                $changeBalance = $val['ClassNumber'];
                $BalanceValue += $changeBalance;
            }
        }
        $checkTrueBalance = $BalanceValue - $totalLessons;
        $LogContent = "TrueBalance should be: " . $checkTrueBalance . " ClientActivitiesId: " . $id;
        if ($checkTrueBalance > 1 || ($checkTrueBalance == 0 && $checkTrueBalance != $TrueBalanceValue)) {

//            if ($checkTrueBalance < $TrueBalanceValue){
//                LoggerService::info($LogContent, LoggerService::CATEGORY_CRON_CARDS);
//            }

            if ($checkTrueBalance > 1) {
                return 0; //don't send reminding
            } else {
                return 1; //send "card is over" instead of "last lesson"
            }
        }
        return 2; //check is ok, send notification according to db data
    }

    public function getActiveSubscription($id, $CompanyNum, $ClientId, $MemberShip)
    {
        return DB::table($this->table)
            ->where('id', '!=', $id)
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('ClientId', '=', $ClientId)
            ->where('Status', '=', '0')
            ->where('MemberShip', '=', $MemberShip)
            ->where(
                function ($query) {
                    $query->where('TrueDate', '!=', '')->where('TrueDate', '>', date('Y-m-d'))
                        ->Orwhere('Department', '=', '2')->where('TrueBalanceValue', '>=', '1');
                }
            )
            ->exists();
    }


    /**
     * @param string $itemName
     * @param int $department
     * @param string $trueDate
     * @param string $trueBalanceValue
     * @return string
     */
    public static function getMembershipAttendanceReport(string $itemName, int $department, string $trueDate = '', string $trueBalanceValue = ''): string
    {
        $itemName .= isset($trueDate) ? ' ' . date("d/m/Y", strtotime($trueDate)) : '';
        if ($department === 2) {
            $itemName .= ' ' . $trueBalanceValue;
        }
        return $itemName;
    }

    public function updateTableByClientId($clientId, $companyNum, $data)
    {
        return DB::table($this->table)
            ->where('ClientId', $clientId)
            ->where('CompanyNum', $companyNum)
            ->update($data);
    }

    public function getActivitiesForRegularAssignment($CompanyNum, $ClientId)
    {
        return DB::table($this->table)
            ->where('CompanyNum', $CompanyNum)
            ->where('Department', '!=', '4')
            ->where('Status', 0)
            ->where('isPaymentForSingleClass', 0)
            ->where(function ($q) use ($ClientId) {
                $q->whereRaw('FIND_IN_SET(' . $ClientId . ', TrueClientId)')
                    ->Orwhere('ClientId', $ClientId);
            })->orderBy('CardNumber', 'DESC')
            ->get();
    }

    public function getActivitiesForRegularClassAssignment($CompanyNum, $ClientId)
    {
        return DB::table($this->table)
            ->where('CompanyNum', $CompanyNum)
            ->whereNotIn('Department', array(3,4))
            ->where('Status', 0)
            ->where('isPaymentForSingleClass', 0)
            ->where(function ($q) use ($ClientId) {
                $q->whereRaw('FIND_IN_SET(' . $ClientId . ', TrueClientId)')
                    ->Orwhere('ClientId', $ClientId);
            })->orderBy('CardNumber', 'DESC')
            ->get();
    }

    /**
     * @param $data
     * @return void
     */
    public function moveClassesToNewActivity($data): void
    {
        try {
            if ($data["Department"] == 1) {

                $query = DB::table($this->table)
                    ->whereNotIn('id', array($data["ActivityId"]))
                    ->where('CompanyNum', '=', $data["CompanyNum"])
                    ->where('ClientId', '=', $data["ClientId"])
                    ->where('Department', '=', 1)
                    ->where('KevaAction', '=', 1)
                    ->where('TrueBalanceValueStatus', '=', 0);

                if ($data["MembershipType"] == 0) {
                    $query->where('MemberShip', '=', $data["MemberShip"]);
                }
                $activities = $query->orderBy('id', 'DESC')->get();

                foreach ($activities as $activity) {

                    $acts = DB::table('classstudio_act')
                        ->where('CompanyNum', '=', $data["CompanyNum"])
                        ->where('ClientId', '=', $data["ClientId"])
                        ->where('ClientActivitiesId', '=', $activity->id)
                        ->where('ClassDate', '>=', $data["StartDate"])
                        ->get();

                    foreach ($acts as $act) {
                        $studioActObj = new ClassStudioAct($act->id);
                        $statusJson = $studioActObj->getTransferStatusJson($act->Status, $activity->CardNumber);

                        $updateArr = [
                            'ClientActivitiesId' => $data["ActivityId"],
                            'TrueClasess' => $data["TrueClasessFinal"],
                            'Department' => $data["Department"],
                            'MemberShip' => $data["MemberShip"],
                            'StatusJson' => $statusJson
                        ];
                        $studioActObj->update($updateArr);
                    }

                    DB::table($this->table)
                        ->where('id', $activity->id)
                        ->where('CompanyNum', $data["CompanyNum"])
                        ->update(['TrueBalanceValueStatus' => 1]);

                }

            } elseif ($data["Department"] == 2 && $data["MinusCards"] == 1 && $data["BalanceClass"] > 1) {

                $query = DB::table($this->table)
                    ->whereNotIn('id', array($data["ActivityId"]))
                    ->where('CompanyNum', '=', $data["CompanyNum"])
                    ->where('ClientId', '=', $data["ClientId"])
                    ->where('Department', '=', 2)
                    ->where('TrueBalanceValue', '<', 0)
                    ->where('TrueBalanceValueStatus', '=', 0);

                if ($data["MembershipType"] == 0) {
                    $query->where('MemberShip', '=', $data["MemberShip"]);
                }
                $activities = $query->orderBy('id', 'DESC')->get();
                $tempBalance = (int)$data["BalanceClass"];
                foreach ($activities as $activity) {

                    $LimitBalance = abs((int)$activity->TrueBalanceValue);
                    $actMovedCounter = 0;

                    $acts = DB::table('classstudio_act')
                        ->where('CompanyNum', '=', $data["CompanyNum"])
                        ->where('FixClientId', '=', $data["ClientId"])
                        ->whereIn('Status', [1, 2, 4, 6, 8, 10, 11, 15])
                        ->where('ClientActivitiesId', '=', $activity->id)
                        ->orderBy('ClassDate', 'desc')
                        ->limit($LimitBalance)->get();

                    foreach ($acts as $act) {
                        $studioActObj = new ClassStudioAct($act->id);
                        $statusJson = $studioActObj->getTransferStatusJson($act->Status, $activity->CardNumber);
                        $updateArr = [
                            'ClientActivitiesId' => $data["ActivityId"],
                            'TrueClasess' => $data["TrueClasessFinal"],
                            'Department' => $data["Department"],
                            'MemberShip' => $data["MemberShip"],
                            'StatusJson' => $statusJson
                        ];
                        $updateResult = $studioActObj->update($updateArr);
                        if ($updateResult) {
                            $actMovedCounter++;
                        }
                    }

                    if ($actMovedCounter > 0) {
                        $currentBalance = $tempBalance - $actMovedCounter;
                        $oldActivityBalance = (int)$activity->TrueBalanceValue + $actMovedCounter;

                        DB::table($this->table)
                            ->where('id', $activity->id)
                            ->where('CompanyNum', $data["CompanyNum"])
                            ->update(['TrueBalanceValueStatus' => 1, 'TrueBalanceValue' => $oldActivityBalance]);

                        DB::table($this->table)
                            ->where('id', $data["ActivityId"])
                            ->where('CompanyNum', $data["CompanyNum"])
                            ->update(['TrueBalanceValue' => $currentBalance, 'ActBalanceValue' => $currentBalance]);

                        /// update current balance
                        $tempBalance = $currentBalance;
                    }


                }
            }
        } catch(\Throwable $e) {
            LoggerService::error($e->getMessage(), LoggerService::CATEGORY_MOVE_CLASSES);
        }

    }

    public function updateClientActivityByStatus($companyNum, $clientId, $status, $data)
    {
        DB::table($this->table)
            ->where('CompanyNum', '=', $companyNum)
            ->where('ClientId', '=', $clientId)
            ->where('Status', '=', $status)
            ->update($data);
    }

    /**
     * @param $classTypeId
     * @return bool
     */
    public function isValidForMeeting($classTypeId)
    {
        return $this->isClassExistOnMembership($classTypeId) && empty($this->isPaymentForSingleClass);
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function getDebt($id)
    {
        return self::where('id', $id)->pluck('BalanceMoney');
    }

    /**
     * Update activity to cancellation fee when client don't have credit card
     * @param $updatedPrice
     * @return bool
     */
    public function applyCancellationPolicy($updatedPrice): bool
    {
        $this->BalanceMoney = $updatedPrice;
        $this->ItemPrice = $updatedPrice;
        $this->ItemText = lang('cancel_fee').'- '.$this->__get('ItemText');
        $this->isDisplayed = 1;
        return $this->save();
    }

    /**
     * @param $cancellationShare mixed cancellation policy share in percentage
     * @param ClassStudioAct $meetingAct
     * @return int
     */
    public static function applyCancellationOnMembership($cancellationShare, ClassStudioAct $meetingAct)
    {
        if ((int)$cancellationShare === 100) {
            $meetingAct->ChangeStatus(ClassStudioAct::STATUS_MEETING_LATE_CANCEL);
            $cancellationActivityId = $meetingAct->ClientActivitiesId;
        } else {
            $meeting = $meetingAct->classStudioDate();
            $client = $meetingAct->client();
            $chargeAmount = $meeting->purchaseAmount * $cancellationShare / 100;
            $cancellationActivityId = ClientActivities::assignMembershipGetId([
                "clientId" => $client->id,
                "itemId" => Item::getSingleClassItem($meeting->ClassNameType),
                "activityName" => lang('cancel_fee') . '- ' . $meeting->getSingleItemName(),
                "itemPrice" => $chargeAmount,
                "isForMeeting" => 1,
                "isDisplayed" => 1
            ]);
            self::CancelClassReturnBalance(
                $meetingAct,
                $meetingAct->CompanyNum,
                ClassStudioAct::STATUS_MEETING_CANCELED
            );
            $meetingAct->ClientActivitiesId = $cancellationActivityId;
            $meetingAct->save();
        }
        return $cancellationActivityId;
    }

    /**
     * Cancel activity for unattended client
     * @return bool
     */
    public function didntAttendCancelActivity(): bool
    {
        $this->Status = self::STATUS_CANCEL;
        $this->CancelStatus = 1;
        $this->Reason = lang('uncoming_meeting');
        return $this->save();
    }

    /**
     * @var string[]
     */
    public static $createRules = [
        'CompanyNum' => 'required|integer',
        'CardNumber' => 'required|integer',
        'ClientId' => 'exists:boostapp.client,id',
        'Department' => 'required|integer|between:1,4',
        'ItemId' => 'exists:boostapp.items,id',
        'ItemText' => 'required|max:256',
        'ItemPrice' => 'required|numeric|between:0,999999999',
        'ItemQuantity' => 'integer',
        'ItemPriceVat' => 'numeric',
        'ItemPriceVatDiscount' => 'numeric',
        'VatAmount' => 'numeric',
        'BalanceMoney' => 'numeric|between:0,999999999',
//        'NotificationDays' => 'integer',
        'FirstDate' => 'integer',
        'isPaymentForSingleClass' => 'integer|between:0,1',
        'isForMeeting' => 'integer|between:0,1',
        'isDisplayed' => 'integer|between:0,1'
    ];

    public static function isStudioHasMemberships($company_num) {
        $res = DB::table(self::getTable())
            ->where('CompanyNum', $company_num)
            ->where('isPaymentForSingleClass', 0)
            ->where('isForMeeting', 0)
            ->first();

        return !empty($res);
    }

    /**
     * The function check on $idCheck if this first clientActivity active
     * @param string $idCheck
     * @param string $clientId
     * @param string $companyNum
     * @return bool
     */
    public static function isFirstActivityClientForClient(string $idCheck, string $clientId, string $companyNum): bool
    {
        return !self::where('ClientId', $clientId)
            ->where('CompanyNum', $companyNum)
            ->whereIn('Department', [1,2])
            ->where('Status', '!=', self::STATUS_CANCEL)
            ->where('isDisplayed', self::DISPLAYED_ON)
            ->where('id', '!=', $idCheck)
            ->exists();
    }

    /**
     * @param int $clintId
     * @param int $companyNum
     * @return ClientActivities[]
     */
    public static function getClientActivitiesInDebt(int $clintId, int $companyNum) : array
    {
        return self::where('CompanyNum','=',$companyNum)
            ->where('ClientId','=', $clintId)
            ->where('BalanceMoney','>',0)
            ->where('CancelStatus', '=', 0)
//                ->where('isDisplayed', 1)
            ->orderBy('id', 'ASC')
            ->get();
    }


    /**
     * get all client activty that in debt and not have Invoice
     * @param int $clintId
     * @param int $companyNum
     * @return ClientActivities[]
     */
    public static function getClientActivitiesInDocsDebt(int $clintId, int $companyNum) : array
    {
        return self::where('CompanyNum','=',$companyNum)
            ->where(function ($q) use ($clintId) {
                $q->where('ClientId','=', $clintId)
                    ->Orwhere('PayClientId','=', $clintId);
            })
            ->where('BalanceMoney','>',0)
            ->where('CancelStatus', '=', 0)
            ->whereNull('InvoiceId')
            ->orderBy('id', 'ASC')
            ->get();
    }


    /**
     * @param int $clintId
     * @param int $companyNum
     * @return int
     */
    public static function getAmountTrialActivities(int $clintId, int $companyNum) : int
    {
        return self::where('CompanyNum', $companyNum)
            ->where('ClientId', $clintId)
            ->where('Department', Item::DEPARTMENT_TRIAL)
            ->where('Status', '!=', self::STATUS_CANCEL)
            ->count();
    }


    /**
     * @param int $clintId
     * @param int $companyNum
     * @return bool
     */
    public static function isOverTrialActivities(int $clintId, int $companyNum) : bool
    {
        return self::getAmountTrialActivities($clintId, $companyNum) > self::MAX_TRAIL_ACTIVITIES_PER_CLIENT;
    }

    /**
     * @param int $clintId
     * @param int $companyNum
     * @param int $membershipTypeId
     * @return string|null
     */
    public static function findPrevActivityEndDay(int $clintId, int $companyNum, int $membershipTypeId=0) : ?string
    {
        $query = self::where('Status', '=', self::STATUS_ACTIVE)
            ->where('CompanyNum', '=', $companyNum)
            ->where('ClientId', '=', $clintId);
        if ($membershipTypeId !== 0) {
            $query = $query->where('MemberShip', '=', $membershipTypeId);
        }
        return $query->orderBy('id', 'DESC')->pluck('TrueDate');
    }

    /**
     * @param int $clintId
     * @param int $companyNum
     * @return int
     */
    public static function countActivitiesForClient(int $clintId, int $companyNum) : int
    {
        return self::where('CompanyNum', '=', $companyNum)
            ->where('ClientId', '=', $clintId)
            ->count();
    }

    /**
     * @param Item $Item
     */
    public function setPropertiesByItem(Item $Item): void
    {
        $quantity = $this->ItemQuantity ?? 1;
        $this->CompanyNum = $Item->CompanyNum;
        $this->Department = $Item->Department;
        $this->MemberShip = $Item->MemberShip;
        $this->ItemId = $Item->id;
        if($quantity > 1) {
            $this->ItemText = $Item->ItemName . " (" .$quantity .")";
        } else {
            $this->ItemText = $this->ItemText ?? $Item->ItemName;
        }
        $this->ItemPrice = $this->ItemPrice ?? $Item->ItemPrice * $quantity;
        $this->BalanceValue = $this->BalanceValue ?? $Item->BalanceClass * $quantity;
        $this->TrueBalanceValue = $this->TrueBalanceValue ?? $Item->BalanceClass * $quantity;
        $this->ActBalanceValue =$this->ActBalanceValue ?? $Item->BalanceClass * $quantity;
        $this->LimitClass = $Item->LimitClass ?? 0;
        $this->Dates = $this->Dates ?? date("Y-m-d H:i:s");
        $this->BalanceMoney = $this->BalanceMoney ?? (($Item->ItemPrice * $quantity) - ($this->DiscountAmount ?? 0));
        $this->BalanceValueLog = $this->BalanceValueLog ?? null;
        $this->isPaymentForSingleClass = $this->isPaymentForSingleClass ?? $Item->isPaymentForSingleClass;
    }

    /**
     * @param Client $Client
     */
    public function setPropertiesByClient(Client $Client): void
    {
        $this->CardNumber = $Client->getCountActivities() + 1;
        $this->ClientId = $Client->id;
    }

    /**
     * @param $data
     * @return void
     * @throws Throwable
     */
    public static function freezeMembership($data)
    {
        $UserId = Auth::user()->id;
        $CompanyNum = $data['CompanyNum'];
        $ClassDate = $data['ClassDate'];
        $ClassDateEnd = $data['ClassDateEnd'];
        $ClientId = $data['ClientId'];
        $ActivityId = $data['ActivityId'];
        $Reason = htmlentities($data['Reason']);

        $numberDays = abs(strtotime($ClassDateEnd) - strtotime($ClassDate)) / 86400;  // 86400 seconds in one day
        $FreezDays = (int)$numberDays + 1;
        /** @var ClientActivities $ActivityInfo */
        $ActivityInfo = self::find($ActivityId);

        $oldStatuses = json_decode($ActivityInfo->FreezLog)->data ?? [];
        $oldStatuses[] = [
            "StartFreez" => $ClassDate,
            "EndFreez" => $ClassDateEnd,
            "FreezDays" => $FreezDays,
            "Dates" => date('Y-m-d G:i:s'),
            "UserId" => $UserId,
            "Reason" => $Reason,
        ];
        $FreezeLog = json_encode(["data" => $oldStatuses]);

        $ActivityInfo->update([
            'Freez' => 1,
            'StartFreez' => $ClassDate,
            'EndFreez' => $ClassDateEnd,
            'FreezDays' => $FreezDays,
            'FreezLog' => $FreezeLog,
        ]);

        // check/delete freeze notifications
        MembershipFreezeNotifications::checkByActivityId($ActivityId);

        // send notification
        $notificationId = AppNotification::sendMembershipFreeze($ActivityId);

        if ($notificationId) {
            // create notification info
            MembershipFreezeNotifications::insertGetId([
                'clientActivityId' => $ActivityId,
                'appnotificationId' => $notificationId,
            ]);
        }

        DB::table('client')
            ->where('id', $ClientId)
            ->update(array('FreezStatus' => '1'));

        $dayBeforeEnd = date('Y-m-d', strtotime($ClassDateEnd . ' -1 day'));
        $GetClientClasses = DB::table('classstudio_act')
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('ClientId', '=', $ClientId)
            ->where('ClientActivitiesId', $ActivityId)
            ->whereIn('Status', array(1, 2, 6, 9, 10, 11, 12, 15, 16, 17, 18, 21, 23))
            ->whereBetween('ClassDate', array($ClassDate, $dayBeforeEnd))
            ->get();

        foreach ($GetClientClasses as $GetClientClass) {
            /** @var ClassStudioAct $ClassAct */
            $ClassAct = ClassStudioAct::find($GetClientClass->id);
            /** @var ClassStudioDate $ClassDate */
            $ClassDate = ClassStudioDate::find($GetClientClass->ClassId);
            if (!empty($ClassDate->meetingTemplateId)) {
                // cancel meeting
                EditMeetingService::cancelMeeting($ClassDate->id, CancelReason::MEMBERSHIP_FREEZE);
                continue;
            }
            ClientActivities::CancelClassReturnBalance($GetClientClass, $CompanyNum, 19);
            $ClassAct->changeStatus(19, false, true);

            // עדכון שיעור ברשימת משתתפים
            $ClassDate->updateClientRegisterCount();
        }
    }

    /**
     * @param int $payClientId
     * @return float|int
     */
    public static function getBalanceAmountOfClient(int $payClientId) {
        return self::where('ClientId', '=', $payClientId)
            ->where('CancelStatus', '=', '0')
            ->where('isDisplayed',  1)
            ->sum('BalanceMoney') ?? 0;
    }

    /**
     * @param $clientId
     * @param $companyNum
     * @param $familyMembershipSetting
     * @param $membershipType
     * @return array
     */
    public static function transferFamilyMembership($clientId,$companyNum,$familyMembershipSetting, $membershipType): array {
        $clientActivities = self::where('ClientId', $clientId)
            ->where('CompanyNum', $companyNum)
            ->whereIn('Status', [0,3])
            ->where(function ($q) {
                return $q->whereIn('Department', [1,2,3])
                    ->where(function ($q1) {
                        return $q1->whereNull('TrueDate')
                            ->orWhere('TrueDate', '>=', date('Y-m-d'));
                    });
            })
            ->get();

        $family_membership_transferees = [];

        foreach ($clientActivities as $activity) {
            if (!empty($activity->TrueClientId)) {
                if ($familyMembershipSetting == 2) {
                    $true_client_ids = explode(',', $activity->TrueClientId);
                } elseif ($familyMembershipSetting == 1 && $activity->MemberShip == $membershipType) {
                    $true_client_ids = explode(',', $activity->TrueClientId);
                } else {
                    $true_client_ids = [0];
                }

                foreach ($true_client_ids as $true_client_id) {
                    if (!in_array($true_client_id, $family_membership_transferees)) {
                        $family_membership_transferees[] = $true_client_id;
                    }
                }
            }
        }

        return $family_membership_transferees ?? [];
    }

    public static function getBalanceAmount($companyNum, $clientId) {
        return self::where(function ($q) use ($clientId) {
            return $q->where('ClientId', $clientId)
                ->orWhere('PayClientId', $clientId);
        })
            ->where('CompanyNum', $companyNum)
            ->where('CancelStatus', 0)
            ->where('isDisplayed', 1)
            ->sum('BalanceMoney');
    }


    /**
     * @param $docId
     * @return bool
     */
    public function addReceiptIdToJson($docId): bool
    {
        $ReceiptIdJson = '{"data": [';
        if (!empty($this->ReceiptId)) {
            $Loops = json_decode($this->ReceiptId, true);
            foreach ($Loops['data'] as $key => $val) {
                $DocIdDB = $val['DocId'];
                $ReceiptIdJson .= '{"DocId": "' . $DocIdDB . '"},';
            }
        }
        $ReceiptIdJson .= '{"DocId": "' . $docId . '"}';
        $ReceiptIdJson .= ']}';
        $this->ReceiptId = $ReceiptIdJson;
        return $this->save();
    }

    /**
     * @param int $clientId
     * @param int $docId
     * @return bool
     */
    public static function updateByInvoiceIdBalanceMoneyTo0(int $clientId ,int $docId): bool
    {
        return (bool) self::where('ClientId', $clientId)->where('InvoiceId', '=', $docId)
            ->update(array('BalanceMoney' => 0));
    }

    /**
     * @param string $reason
     */
    public function separateMeetingFromClientActivity(string $reason = '') {
        //if client activity is meeting then separate from doc and balance = amount
        if((int)$this->isForMeeting === self::IS_FOR_MEETING) {
            $this->BalanceMoney = ($this->ItemPrice * ($this->ItemQuantity ?? 1)) - ($this->DiscountAmount ?? 0);
            $this->InvoiceId = null;
            $this->isDisplayed = self::DISPLAYED_OFF;
            $this->Reason = $reason;
            $this->save();
        }
    }

    /**
     * Checks whether the invoice linked to the subscriber is in debt
     * @return float
     */
    public function getInvoiceInDebt() : float
    {
        if(!empty($this->InvoiceId)) {
            return Docs::getBalanceAmount($this->InvoiceId);
        }
        return 0;
    }



    }

