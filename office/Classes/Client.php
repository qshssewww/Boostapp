<?php

use Hazzard\Database\Model;

require_once __DIR__ . "/ClientActivities.php";
require_once __DIR__ . "/ClassStudioAct.php";
require_once __DIR__ . "/ClassStudioDate.php";
require_once __DIR__ . "/Clientcrm.php";
require_once __DIR__ . "/ClientMedical.php";
require_once __DIR__ . "/ClientLevel.php";
require_once __DIR__ . "/UserBoostappLogin.php";
require_once __DIR__ . "/Utils.php";
require_once __DIR__ . "/PipelineCategory.php";
require_once __DIR__ . "/Pipeline.php";
require_once __DIR__ . "/Brand.php";
require_once __DIR__ . "/LeadStatus.php";
require_once __DIR__ . "/LeadSource.php";
require_once __DIR__ . "/../services/GoogleCalendarService.php";
require_once __DIR__ . "/StudioBoostappLogin.php";
require_once __DIR__ . "/Rank.php";
require_once __DIR__ . "/../services/ClientService.php";
require_once __DIR__ . "/Notificationcontent.php";
require_once __DIR__ . "/Settings.php";

/**
 * @property $id
 * @property $CompanyNum
 * @property $Brands
 * @property $CompanyName
 * @property $Company
 * @property $BusinessType
 * @property $CompanyId
 * @property $Email
 * @property $Dates
 * @property $Status
 * @property $ContactMobile
 * @property $ChangeDate
 * @property $FirstName
 * @property $LastName
 * @property $Dob
 * @property $UserId
 * @property $Gender
 * @property $Age
 * @property $Vat
 * @property $PaymentRole
 * @property $BalanceAmount
 * @property $City
 * @property $Street
 * @property $StreetH
 * @property $Number
 * @property $PostCode
 * @property $POBox
 * @property $RemarkIcon
 * @property $ContactPhone
 * @property $ContactFax
 * @property $WebSite
 * @property $Remarks
 * @property $Flat
 * @property $Floor
 * @property $Entry
 * @property $GetSMS
 * @property $GetEmail
 * @property $MemberShipText
 * @property $LastClassDate
 * @property $PayClientId
 * @property $tokenFirebase
 * @property $OS
 * @property $AppLoginId
 * @property $Takanon
 * @property $Medical
 * @property $ClassLevel
 * @property $ParentsName
 * @property $AppPassword
 * @property $ProfileImage
 * @property $BrandName
 * @property $ArchiveDate
 * @property $OldId
 * @property $MemberId
 * @property $RFID
 * @property $ActiveMembership
 * @property $ActivityClientId
 * @property $PrivatePassWord
 * @property $FreezStatus
 * @property $TryStatus
 * @property $JoinDate
 * @property $ConvertDate
 * @property $AutoInsert
 * @property $AutoInsertToken
 * @property $additional_data
 * @property $parentClientId
 * @property $relationship
 * @property $greenPassStatus
 * @property $greenPassValid
 * @property $greenActionDate
 * @property $ArchiveReasonId
 * @property $isRandomClient
 *
 * Class Client
 */
class Client extends Model
{
    const mobileRegex = "/^(\+972|\+91|\+1|\+44)?0?5(0|1|2|3|4|5|8|9){1}(-){0,1}[0-9]{7}$/";
    protected $table = "boostapp.client";
    /** @var Client|null */
    private $_parent = false;
    private $_studioSettings;
    private $_tokens;

    public const STATUS_ACTIVE = 0;
    public const STATUS_ARCHIVE = 1;
    public const STATUS_LEAD = 2;

    public const GET_SMS_STATUS_ACTIVE = 0;
    public const GET_SMS_STATUS_0FF = 1;
    public const GET_MAIL_STATUS_ACTIVE = 0;
    public const GET_MAIL_STATUS_0FF = 1;

    public const IS_RANDOM_CLIENT = 1;
    public const IS_NOT_RANDOM_CLIENT = 0;


    /**
     * @param $attributes
     */
    public function __construct($attributes = [])
    {
        if (is_numeric($attributes)) {
            $model = self::find($attributes);
            if ($model) {
                $this->fill($model->toArray());
                $this->exists = true;
            }
            $attributes = [];
        }

        parent::__construct($attributes);
    }
//    public static function find($id, $columns = array('*'))
//    {
//        /** @var Client $Client */
//        $Client = parent::find($id, $columns);
//        return $Client;
//    }

    /**
     * @param $mobilePhone
     * @return mixed
     */
    public static function getClientByMobilePhone($mobilePhone)
    {
        $mobilePhone = preg_replace("/^(\+972|\+91|\+1|\+44|0)0?/", '', $mobilePhone); //remove prefix
        $mobileRegex = "^[\+972|\+91|\+1|\+44]*0?" . $mobilePhone . "$";
        return self::where('CompanyNum', '=', Auth::user()->CompanyNum)
            ->whereRaw("ContactMobile regexp '" . $mobileRegex . "'")
            ->first();
    }

    /**
     * @param $companyNum
     * @return int|null
     */
    public static function getRandomClientIdByCompanyNum($companyNum): ?int
    {
        return self::where('CompanyNum', '=', $companyNum)->where('isRandomClient', '=', self::IS_RANDOM_CLIENT)->pluck('id') ?? 0;
    }


    /**
     * @param $CompanyNum
     * @return self
     */
    public static function getRandomClient($CompanyNum): Client
    {
        $randomClient = self::where('CompanyNum', $CompanyNum)->where('isRandomClient', self::IS_RANDOM_CLIENT)->first();
        if ($randomClient) {
            return $randomClient;
        }

        $randomClient = new self();
        $randomClient->FirstName = 'לקוח';
        $randomClient->LastName = 'מזדמן';
        $randomClient->CompanyNum = $CompanyNum;
        $randomClient->ContactMobile = '0500000000';
        $randomClient->isRandomClient = 1;
        $randomClient->CompanyName = lang('occasional_customer');
        $randomClient->save();
        return self::getRandomClient($CompanyNum);
    }

    /**
     * return the client's avatar (profile picture)
     * @param $clientId
     * @param $CompanyNum
     * @return string|null
     */
    public static function getAvatar($clientId, $CompanyNum)
    {
        return StudioBoostappLogin::getAvatar($clientId, $CompanyNum);
    }

    /**
     * return the client's avatar (profile picture)
     * @return string|null
     */
    public function getAvatarFromUser(): ?string
    {
        return StudioBoostappLogin::getAvatar($this->id, $this->CompanyNum);
    }


    /**
     * @return bool
     */
    public function isLead(): bool
    {
        return (int)$this->Status === self::STATUS_LEAD;
    }

    /**
     * @return bool
     */
    public function isClient(): bool
    {
        return $this->Status == 0;
    }

    /**
     * @return bool
     */
    public function isArchived(): bool
    {
        return (int)$this->Status === self::STATUS_ARCHIVE;
    }

    public function unArchive()
    {
        return $this->updateClient($this->id, ['Status' => 0]);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function insert_into_table($data)
    {
        if (!isset($data["MemberId"])) {
            $ClientCheckId = DB::table($this->table)->where('CompanyNum', $data["CompanyNum"])->max('MemberId');
            $data["MemberId"] = $ClientCheckId ? $ClientCheckId + 1 : 1;
        }

        return DB::table($this->table)->insertGetId($data);
    }

    /**
     * @param $id
     * @param $data
     * @return mixed
     */
    public function updateClient($id, $data)
    {
        $res = DB::table($this->table)->where("id", "=", $id)->update($data);

        if (isset($data['Status']) && $data['Status'] == 1) {
            GoogleCalendarService::disconnectClient($id);
        }

        return $res;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getRow($id)
    {
        $returnedObj = DB::table($this->table)->where("id", "=", $id)->first();
        return $returnedObj;
    }

    /**
     * @return mixed
     */
    public function getAllRows()
    {
        return DB::table($this->table)->get();
    }

    /**
     * @param $companyNum
     * @param int $status
     * @return mixed
     */
    public function getClientsByCompany($companyNum, $status = 0)
    {
        return DB::table($this->table)->where('CompanyNum', '=', $companyNum)->where('status', '=', $status)->get();
    }

    /**
     * @param $companyNum
     * @param $id
     * @return mixed
     */
    public function getClientByCompanyAndId($companyNum, $id)
    {
        return DB::table($this->table)->where('CompanyNum', '=', $companyNum)->where('id', '=', $id)->first();
    }

    /**
     * @param $companyNum
     * @param int $status
     * @return mixed
     */
    public function getClientsByCompanyOrderByName($companyNum, $status = 0)
    {
        return DB::table($this->table)
            ->where('CompanyNum', '=', $companyNum)
            ->where('status', '=', $status)
            ->orderBy('CompanyName', 'ASC')
            ->get();
    }

    /**
     * @param $companyNum
     * @return array
     */
    public function getClientsMembershipCheckCounter($companyNum)
    {
        return ['clientsMembershipCounter' => $this->getClientMembership($companyNum)];
    }

    /**
     * @param $CompanyNum
     * @return int
     */
    public function getClientMembership($CompanyNum)
    {

        $MemberShipCounts = DB::table('client_activities as ca')->select('ca.ClientId as ClientId', 'ca.TrueClientId as TrueClientId')
            ->leftJoin("membership_type as mt", "ca.MemberShip", "=", "mt.id")
            ->leftJoin("client as cl", "ca.ClientId", "=", "cl.id")
            ->leftJoin("items as it", "ca.ItemId", "=", "it.id")
            ->where('ca.TrueDate', '>=', date('Y-m-d'))->where('ca.Department', '=', '1')->where('ca.CompanyNum', '=', $CompanyNum)->where('ca.Status', '=', '0')->where('ca.MemberShip', '!=', 'BA999')->where('ca.ClientStatus', '=', '0')
            ->Orwhere('ca.TrueBalanceValue', '>=', '1')->whereNull('ca.TrueDate')->where('ca.Department', '=', '2')->where('ca.CompanyNum', '=', $CompanyNum)->where('ca.Status', '=', '0')->where('ca.MemberShip', '!=', 'BA999')->where('ca.ClientStatus', '=', '0')
            ->Orwhere('ca.TrueDate', '<', date('Y-m-d'))->where('ca.Freez', '=', '1')->where('ca.StartFreez', "<=", date('Y-m-d'))->where('ca.EndFreez', '>=', date('Y-m-d'))->whereIn('ca.Department', array(1, 2))->where('ca.CompanyNum', '=', $CompanyNum)->where('ca.Status', '=', '0')->where('ca.MemberShip', '!=', 'BA999')->where('ca.ClientStatus', '=', '0')
            ->Orwhere('ca.TrueBalanceValue', '>=', '1')->where('ca.TrueDate', '>=', date('Y-m-d'))->where('ca.Department', '=', '2')->where('ca.CompanyNum', '=', $CompanyNum)->where('ca.Status', '=', '0')->where('ca.MemberShip', '!=', 'BA999')->where('ca.ClientStatus', '=', '0')
            ->get();

        $members = [];

        foreach ($MemberShipCounts as $member) {
            $members[$member->ClientId] = true;
            if ($member->TrueClientId != 0) {
                $subMembers = explode(',', $member->TrueClientId);
                foreach ($subMembers as $subMember) {
                    $members[$subMember] = true;
                }
            }
        }

        return count($members);
    }

    /**
     * @param $clientId
     */
    public function deleteClientById($clientId)
    {
        DB::table($this->table)->where('id', $clientId)->delete();
    }

    /**
     * @param $companyNum
     * @return array
     */
    public function getActiveCheck($companyNum)
    {
        $clientCheckCounter = $this->GetInvildMemberShip($companyNum);
        $clients = DB::table($this->table)
            ->where('CompanyNum', '=', $companyNum)
            ->where('Status', 0)
            ->where('isRandomClient', 0)
            ->count();

        return ['clientsActiveCounter' => $clients, 'clientCheckCounter' => $clientCheckCounter];
    }

    /**
     * @param $CompanyNum
     * @return int
     */
    public function GetInvildMemberShip($CompanyNum)
    {

        $clientActivities = new ClientActivities();

        $invalidMemberships = $clientActivities->getInvalidMemberships($CompanyNum);
        $ExpiringClients = $clientActivities->getExpiringClients($CompanyNum);
        $companyActivitiesFilters = $clientActivities->getBulkActiveMemberships($CompanyNum);

        $invalidMemberships = ClientActivities::filterActiveMemberships($invalidMemberships, $companyActivitiesFilters);
        $ExpiringClients = ClientActivities::filterActiveMemberships($ExpiringClients, $companyActivitiesFilters);

        return $invalidMemberships['uniqueCount'] + $ExpiringClients['uniqueCount'];
    }


    /**
     * Calculates the balance of all customers
     * @param $companyNum
     * @return mixed
     */
    public function GetBalanceAmountClients($companyNum)
    {
        $ClientsBalanceAmount = DB::table($this->table)
            ->where('CompanyNum', '=', $companyNum)
            ->where('balanceAmount', '>', 0)
            //->where('status','=',0) add it to show only active users
            ->sum('balanceAmount');
        return $ClientsBalanceAmount;
    }

    /**
     * @param $amount
     */
    public function addToBalanceAmount($amount)
    {
        DB::table($this->table)->where('id', $this->id)
            ->update([
                'BalanceAmount' => $this->BalanceAmount + $amount
            ]);
    }

    /**
     * Updates the balance amount of the client
     * @return void
     */
    public function updateBalanceAmount()
    {
        $BalanceAmount = 0;

        //// בדיקת כרטיסית אב
        if ($this->PayClientId != '0') {
            $PayClientId = $this->PayClientId;
            $BalanceAmount = ClientActivities::where('ClientId', '=', $PayClientId)
                ->where('CompanyNum', $this->CompanyNum)
                ->where('CancelStatus', '=', '0')
                ->where('isDisplayed',  1)->sum('BalanceMoney');
        } else {
            $PayClientId = $this->id;
        }

        $CheckClientInfoer = self::where('CompanyNum', $this->CompanyNum)->where('PayClientId', $PayClientId)->get();
        if (!empty($CheckClientInfoer)) {
            foreach ($CheckClientInfoer as $CheckClientInfo) {
                $BalanceAmount += ClientActivities::where('ClientId', '=', $CheckClientInfo->id)
                    ->where('CompanyNum', $this->CompanyNum)
                    ->where('CancelStatus', '=', '0')
                    ->where('isDisplayed',  1)->sum('BalanceMoney');
            }
        } else {
            $BalanceAmount = ClientActivities::where('ClientId', '=', $this->id)
                ->where('CompanyNum', $this->CompanyNum)
                ->where('CancelStatus', '=', '0')
                ->where('isDisplayed',  1)->sum('BalanceMoney');
        }

        if ($BalanceAmount === null) {
            $BalanceAmount = 0;
        }

        self::where('id', $PayClientId)
            ->where('CompanyNum', $this->CompanyNum)
            ->update(['BalanceAmount' => $BalanceAmount]);
    }

    /**
     * @param $companyNum
     * @return mixed
     */
    public function GetLastweekDateOfBirth($companyNum)
    {
        return DB::table($this->table)->where('CompanyNum', '=', $companyNum)->whereIn('Status', array(0, 2))
            ->whereRaw('DAYOFYEAR(curdate()) -1 <= DAYOFYEAR(Dob) AND DAYOFYEAR(curdate()) - 1 + 7 >=  dayofyear(Dob)')->orderBy(DB::raw("DATE_FORMAT(Dob,'%m-%d')"), 'ASC')->get();
    }

    /**
     * @param $idArr
     * @param $companyNum
     * @return mixed
     */
    public function getClientsByIds($idArr, $companyNum)
    {
        return DB::table($this->table)
            ->where("CompanyNum", $companyNum)
            ->whereIn("id", $idArr)->get();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getClientsByPayClientId($id, $companyNum = 0)
    {
        $companyNum = $companyNum === 0 ? $this->CompanyNum : $companyNum;
        return DB::table($this->table)
            ->where("CompanyNum", $companyNum)
            ->where("PayClientId", $id)->get();
    }

    /**
     * @param $SearchStr
     * @param $CompanyNum
     * @return mixed
     */
    public function SearchClients($SearchStr, $CompanyNum)
    {
        $Search = '%' . $SearchStr . '%';
        return DB::table($this->table)
            ->where('CompanyName', 'LIKE', $Search)->where('CompanyNum', '=', $CompanyNum)->where('Status', '!=', '1')
            ->Orwhere('CompanyNum', '=', $CompanyNum)->where('ContactMobile', '=', $SearchStr)->where('Status', '!=', '1')
            ->Orwhere('CompanyId', '=', $SearchStr)->where('CompanyNum', '=', $CompanyNum)->where('Status', '!=', '1')
            ->Orwhere('id', '=', $SearchStr)->where('CompanyNum', '=', $CompanyNum)->where('Status', '!=', '1')->get();
    }

    /**
     * @return array
     */
    public function getChilds()
    {
        $childs = DB::table($this->table)->where('parentClientId', '=', $this->id)->where('CompanyNum', '=', $this->CompanyNum)->get();
        $childsArr = array();
        foreach ($childs as $child) {

            $childObj = new Client();
            foreach ($child as $key => $value) {
                $childObj->__set($key, $value);
            }
            array_push($childsArr, $childObj);
        }
        return $childsArr;
    }

    /**
     * @return array
     */
    public function getRelatives()
    {
        $relatives = DB::table($this->table)->where('id', '=', $this->parentClientId)->where('CompanyNum', '=', $this->CompanyNum)
            ->Orwhere('parentClientId', '=', $this->parentClientId)->where('CompanyNum', '=', $this->CompanyNum)->where('id', '!=', $this->id)
            ->orderBy('parentClientId', 'ASC')->get();
        $relativesArr = array();
        foreach ($relatives as $client) {
            $clientObj = new Client();
            foreach ($client as $key => $value) {
                $clientObj->__set($key, $value);
            }
            array_push($relativesArr, $clientObj);
        }
        return $relativesArr;
    }

    /**
     * @param $mobile
     * @param $email
     * @return array
     */
    public function updateMinorDetails($mobile, $email)
    {
        /// check if mobile exists
        $resArr = array('success' => true, "msg" => '');
        if (empty($mobile)) {
            $resArr['success'] = false;
            $resArr['msg'] = 'מספר נייד שדה חובה';
            return $resArr;
        }
        $checkMobile = DB::table($this->table)->where('id', '!=', $this->id)->where('CompanyNum', '=', $this->CompanyNum)->where('ContactMobile', 'LIKE', '%' . $mobile . '%')->first();
        if (!empty($checkMobile)) {
            $resArr['success'] = false;
            $resArr['msg'] = 'מספר נייד קיים';
            return $resArr;
        }
        $mobile = '+972' . $mobile;
        $updateClient = DB::table($this->table)->where('id', $this->id)->update(array('ContactMobile' => $mobile, 'Email' => $email, 'Status' => 0, 'parentClientId' => 0, 'relationship' => 0));
        if (!$updateClient) {
            $resArr['success'] = false;
            $resArr['msg'] = 'אירעה שגיאה בעדכון לקוח';
            return $resArr;
        }
        $AppPassword = mt_rand(100000, 999999);
        $password = Hash::make($AppPassword);
        $SettingsInfo = DB::table('settings')->where('CompanyNum', '=', $this->CompanyNum)->first();

        $getStudio = DB::table('boostapplogin.studio')->where('ClientId', '=', $this->id)->where('CompanyNum', '=', $this->CompanyNum)->first();
        if (!empty($getStudio)) {
            $getUser = DB::table('boostapplogin.users')->where('id', '=', $getStudio->UserId)->first();
            if (!empty($getUser)) {
                $userId = $getUser->id;
                $updateUser = DB::table('boostapplogin.users')->where('id', $userId)->update(array('username' => $email, 'email' => $email, 'newUsername' => $mobile, 'password' => $password, 'parentId' => 0, 'status' => 1));
            } else {
                $userId = DB::table('boostapplogin.users')->insertGetId(
                    array('username' => $email, 'email' => $email, 'newUsername' => $mobile, 'password' => $password, 'display_name' => $this->CompanyName, 'FirstName' => $this->FirstName, 'LastName' => $this->LastName, 'ContactMobile' => $mobile, 'AppLoginId' => $mobile, 'status' => 1));
            }
            $updateStudio = DB::table('boostapplogin.studio')->where('id', $getStudio->id)->update(array('UserId' => $userId, 'LastDate' => date('Y-m-d'), 'LastTime' => date('H:i:s'), 'Status' => 0));
        } else {
            $getUser = DB::table('boostapplogin.users')->where('newUsername', '=', $mobile)->first();
            if (empty($getUser)) {
                $userId = DB::table('boostapplogin.users')->insertGetId(
                    array('username' => $email, 'email' => $email, 'newUsername' => $mobile, 'password' => $password, 'display_name' => $this->CompanyName, 'FirstName' => $this->FirstName, 'LastName' => $this->LastName, 'ContactMobile' => $mobile, 'AppLoginId' => $mobile, 'status' => 1));
            } else {
                $userId = $getUser->id;
                $updateUser = DB::table('boostapplogin.users')->where('id', $userId)->update(array('username' => $email, 'email' => $email, 'password' => $password, 'parentId' => 0, 'status' => 1));
            }
            $getStudio = DB::table('boostapplogin.studio')->where('ClientId', '=', $this->id)->where('CompanyNum', '=', $this->CompanyNum)->first();
            if (empty($getStudio)) {
                $studioId = DB::table('boostapplogin.studio')->insertGetId(
                    array('StudioUrl' => $SettingsInfo->StudioUrl, 'StudioName' => $SettingsInfo->AppName, 'CompanyNum' => $this->CompanyNum, 'UserId' => $userId, 'ClientId' => $this->id, 'LastDate' => date('Y-m-d'), 'LastTime' => date('H:i:s'), 'Memotag' => $SettingsInfo->Memotag, 'Folder' => $SettingsInfo->Folder)
                );
            } else {
                $studioId = $getStudio->id;
                $updateStudio = DB::table('boostapplogin.studio')->where('id', $studioId)->update(array('UserId' => $userId, 'LastDate' => date('Y-m-d'), 'LastTime' => date('H:i:s'), 'Status' => 0));
            }
        }

        $Template = DB::table('notificationcontent')->where('CompanyNum', '=', $this->CompanyNum)->where('Type', '=', '21')->first();
        /// עדכון תבנית הודעה
        $GooglePlayLink = 'https://play.google.com/store/apps/details?id=com.connect_computer.boostnew&gl=IL';
        $AppStoreLink = 'https://apps.apple.com/us/app/boost-%D7%91%D7%95%D7%A1%D7%98/id1479519489';

        if (!empty($SettingsInfo->GooglePlayLink)) {
            $GooglePlayLink = $SettingsInfo->GooglePlayLink;
        }
        if (!empty($SettingsInfo->AppStoreLink)) {
            $AppStoreLink = $SettingsInfo->AppStoreLink;
        }

        $AppStore = '<a href="' . $AppStoreLink . '">App Store</a>';
        $GooglePlay = '<a href="' . $GooglePlayLink . '">Google Play</a>';

        $Content1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], $SettingsInfo->AppName, $Template->Content);
        $Content2 = str_replace(Notificationcontent::REPLACE_ARR["name_table"], $this->CompanyName, $Content1);
        $Content3 = str_replace(Notificationcontent::REPLACE_ARR["first_name"], $this->FirstName, $Content2);
        $Content4 = str_replace(Notificationcontent::REPLACE_ARR["username_single"], $email, $Content3);
        $Content5 = str_replace(Notificationcontent::REPLACE_ARR["password_single"], $AppPassword, $Content4);
        $Content6 = str_replace("App Store", $AppStore, $Content5);
        $Content7 = str_replace("Google Play", $GooglePlay, $Content6);
        $Subject = $Template->Subject;
        $Text = $Content7;

        if (!empty($email)) {
            $AddNotification = DB::table('appnotification')->insertGetId(
                array('CompanyNum' => $this->CompanyNum, 'ClientId' => $this->id, 'Type' => 2, 'Subject' => $Subject, 'Text' => $Text, 'Dates' => date('Y-m-d H:i:s'), 'UserId' => Auth::user()->id, 'Date' => date('Y-m-d'), 'Time' => date('H:i:s'), 'priority' => 1));
        }

        return $resArr;
    }

    /**
     * @param $mobile
     * @param $email
     * @return array
     */
    public function archiveMinorClient($mobile, $email)
    {
        $resArr = array('success' => true, 'msg' => '');
        if (!empty($mobile)) {
            $checkMobile = DB::table($this->table)->where('id', '!=', $this->id)->where('CompanyNum', '=', $this->CompanyNum)->where('ContactMobile', 'LIKE', '%' . $mobile . '%')->first();
            if (!empty($checkMobile)) {
                $resArr['success'] = false;
                $resArr['msg'] = 'מספר נייד קיים';
                return $resArr;
            }
            $mobile = '+972' . $mobile;
        }

        $updateClient = DB::table($this->table)->where('id', $this->id)->update(array('ContactMobile' => $mobile, 'Email' => $email, 'Status' => 1, 'parentClientId' => 0, 'relationship' => 0));
        GoogleCalendarService::disconnectClient($this->id);
        if (!$updateClient) {
            $resArr['success'] = false;
            $resArr['msg'] = 'אירעה שגיאה בעדכון לקוח';
            return $resArr;
        }
        $SettingsInfo = DB::table('settings')->where('CompanyNum', '=', $this->CompanyNum)->first();
        $getStudio = DB::table('boostapplogin.studio')->where('ClientId', '=', $this->id)->where('CompanyNum', '=', $this->CompanyNum)->first();
        if (!empty($getStudio) && $getStudio->Status == 0) {
            $updateStudio = DB::table('boostapplogin.studio')->where('id', $getStudio->id)->update(array('Status' => 1));
        }
        return $resArr;
    }

    /**
     * @param $post
     * @return array
     * @throws Exception
     */
    public function addMinorClient($post)
    {
        $addMinorClient = ClientService::addClient([
            'CompanyNum' => $this->CompanyNum,
            'Brands' => $this->Brands,
            'Company' => $this->CompanyName, // שם לחשבונית
            'FirstName' => $post['minor_firstName'],
            'LastName' => $post['minor_lastName'],
            'areaCode' => isset($post['minor-ContactMobile']) ? "+972" : null,
            'ContactMobile' => $post['minor-ContactMobile'] ?? null,
            'Dob' => $post['Dob'] ?? null,
            'City' => $this->City,
            'Street' => $this->Street,
            'StreetH' => $this->StreetH,
            'Number' => $this->Number,
            'Gender' => $post['Gender'] ?? 0,
            'AppLoginId' => $this->ContactMobile,
            'UserId' => Auth::user()->id,
            'parentClientId' => $post['parent_client_id'],
            'relationship' => $post['relationship'] ?? 0,
        ]);

        $resArr = array('success' => true, 'msg' => '', 'minor_id' => '', 'companyName' => '');
        $resArr['minor_id'] = $addMinorClient['Message']['client_id'];
        $resArr['companyName'] = trim($post["minor_firstName"]) . ' ' . trim($post["minor_lastName"]);
        return $resArr;
    }

    /**
     * @param $post
     * @return array
     */
    public function setMinorById($post)
    {
        $resArr = array('success' => true, 'msg' => '', 'minor_id' => '', 'companyName' => '');
        $minorClient = new Client($post['exist_minor_id']);
        if (empty($minorClient)) {
            $resArr['success'] = false;
            $resArr['msg'] = 'לא נמצא לקוח';
            return $resArr;
        }
        if ($minorClient->isParentClient()) {
            $resArr['success'] = false;
            $resArr['msg'] = 'לקוח זה מוגדר כלקוח אב, לא ניתן לשייך אותו כלקוח קטין';
            return $resArr;
        }
        if ($minorClient->isMinorClient()) {
            $resArr['success'] = false;
            $resArr['msg'] = 'לקוח זה מוגדר כבר כלקוח קטין';
            return $resArr;
        }
        $updateClient = DB::table($this->table)->where('id', $minorClient->id)->update(array(
            "parentClientId" => $this->id,
            "relationship" => $post['relationship'] ?? 0,
            "Company" => $this->CompanyName
        ));
        if ($updateClient == 0) {
            $resArr['success'] = false;
            $resArr['msg'] = 'שגיאה בעדכון הלקוח';
            return $resArr;
        }
        $AppPassword = mt_rand(100000, 999999);
        $password = Hash::make($AppPassword);
        $SettingsInfo = DB::table('settings')->where('CompanyNum', '=', $minorClient->__get('CompanyNum'))->first();
        $newParentUser = $this->setMinorAppUser($minorClient->__get('id'), $password);
        if ($newParentUser) {

            $Template = DB::table('notificationcontent')->where('CompanyNum', '=', $this->CompanyNum)->where('Type', '=', '21')->first();
            /// עדכון תבנית הודעה
            $GooglePlayLink = 'https://play.google.com/store/apps/details?id=com.connect_computer.boostnew&gl=IL';
            $AppStoreLink = 'https://apps.apple.com/us/app/boost-%D7%91%D7%95%D7%A1%D7%98/id1479519489';

            if (!empty($SettingsInfo->GooglePlayLink)) {
                $GooglePlayLink = $SettingsInfo->GooglePlayLink;
            }
            if (!empty($SettingsInfo->AppStoreLink)) {
                $AppStoreLink = $SettingsInfo->AppStoreLink;
            }
            $AppStore = '<a href="' . $AppStoreLink . '">App Store</a>';
            $GooglePlay = '<a href="' . $GooglePlayLink . '">Google Play</a>';

            $Content1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], $SettingsInfo->AppName, $Template->Content);
            $Content2 = str_replace(Notificationcontent::REPLACE_ARR["name_table"], $this->CompanyName, $Content1);
            $Content3 = str_replace(Notificationcontent::REPLACE_ARR["first_name"], $this->FirstName, $Content2);
            $Content4 = str_replace(Notificationcontent::REPLACE_ARR["username_single"], $this->Email ?? '', $Content3);
            $Content5 = str_replace(Notificationcontent::REPLACE_ARR["password_single"], $AppPassword, $Content4);
            $Content6 = str_replace("App Store", $AppStore, $Content5);
            $Content7 = str_replace("Google Play", $GooglePlay, $Content6);
            $Text = $Content7;
            $Subject = $Template->Subject;

            $AddNotification = DB::table('appnotification')->insertGetId(
                array(
                    'CompanyNum' => $this->CompanyNum,
                    'ClientId' => $this->id,
                    'Type' => 2,
                    'Subject' => $Subject,
                    'Text' => $Text,
                    'Dates' => date('Y-m-d H:i:s'),
                    'UserId' => Auth::user()->id,
                    'Date' => date('Y-m-d'),
                    'Time' => date('H:i:s'),
                    'priority' => 1
                ));
        }
        $resArr['minor_id'] = $minorClient->__get('id');
        $resArr ['companyName'] = $minorClient->__get('CompanyName');
        return $resArr;
    }

    /**
     * @return bool
     */
    public function isParentClient()
    {
        $hasChilds = DB::table($this->table)->where('parentClientId', '=', $this->id)->where('CompanyNum', '=', $this->CompanyNum)->count();
        return $hasChilds > 0;
    }

    /**
     * @return bool
     */
    public function isMinorClient()
    {
        return $this->parentClientId != 0;
    }

    /**
     * @param $minor_id
     * @param $password
     * @return bool
     */
    public function setMinorAppUser($minor_id, $password)
    {
        $minorClient = new Client($minor_id);
        $parentMobile = !empty($this->ContactMobile) ? $this->ContactMobile : '';
        $newParentUser = false;
        $SettingsInfo = Settings::getSettings($this->CompanyNum);

        if (preg_match(ClientService::MOBILE_REGEX, $this->ContactMobile)) {
            $mobile_match = true;
            $parentMobile = substr($this->ContactMobile, 0, 4) == '+972' ? substr($this->ContactMobile, 4, strlen($this->ContactMobile)) : $this->ContactMobile;
            $parentMobile = substr($parentMobile, 0, 1) == '0' ? substr($parentMobile, 1, strlen($parentMobile)) : $parentMobile;
            // israeli phone number with country code
            $parentMobile = '+972' . $parentMobile;
        }
        if (!empty($parentMobile) && $mobile_match) {
            $getParentStudio = DB::table('boostapplogin.studio')->where('ClientId', '=', $this->id)->where('CompanyNum', '=', $this->CompanyNum)->first();
            if (!empty($getParentStudio)) {
                $parentStudioId = $getParentStudio->id;
                $getParentUser = DB::table('boostapplogin.users')->where('id', $getParentStudio->UserId)->first();
                if (empty($getParentUser)) {
                    $parentUserId = DB::table('boostapplogin.users')->insertGetId(
                        array(
                            'username' => $this->Email,
                            'email' => $this->Email,
                            'newUsername' => $parentMobile,
                            'password' => $password,
                            'display_name' => $this->CompanyName,
                            'FirstName' => $this->FirstName,
                            'LastName' => $this->LastName,
                            'ContactMobile' => $this->ContactMobile,
                            'AppLoginId' => $this->ContactMobile,
                            'status' => 1
                        ));
                    $newParentUser = true;
                } else {
                    $parentUserId = $getParentUser->id;
                    $updateParentUser = DB::table('boostapplogin.users')
                        ->where('id', $parentUserId)
                        ->update(array('email' => trim($this->Email), 'newUsername' => $parentMobile));
                }

                DB::table('boostapplogin.studio')
                    ->where('id', $parentStudioId)
                    ->where('CompanyNum', $this->CompanyNum)
                    ->update(array('UserId' => $parentUserId, 'LastDate' => date('Y-m-d'), 'LastTime' => date('H:i:s')));
            } else {
                $getParentUser = DB::table('boostapplogin.users')->where('newUsername', '=', $parentMobile)->first();
                if (empty($getParentUser)) {
                    $parentUserId = DB::table('boostapplogin.users')->insertGetId(
                        array('username' => $this->Email,
                            'email' => $this->Email,
                            'newUsername' => $this->ContactMobile,
                            'password' => $password,
                            'display_name' => $this->CompanyName,
                            'FirstName' => $this->FirstName,
                            'LastName' => $this->LastName,
                            'ContactMobile' => $this->ContactMobile,
                            'AppLoginId' => $this->ContactMobile,
                            'status' => 1
                        ));
                    $newParentUser = true;
                } else {
                    $parentUserId = $getParentUser->id;
                }
                $parentStudioId = DB::table('boostapplogin.studio')->insertGetId(
                    array('StudioUrl' => $SettingsInfo->StudioUrl,
                        'StudioName' => $SettingsInfo->AppName,
                        'CompanyNum' => $this->CompanyNum,
                        'UserId' => $parentUserId,
                        'ClientId' => $this->id,
                        'Status' => 1,
                        'LastDate' => date('Y-m-d'),
                        'LastTime' => date('H:i:s'),
                        'Memotag' => $SettingsInfo->Memotag,
                        'Folder' => $SettingsInfo->Folder
                    ));
            }
            $minorUser = DB::table('boostapplogin.users')
                ->where('newUsername', '=', '-1')
                ->where('parentId', '=', $parentUserId)
                ->where('display_name', '=', $minorClient->__get("CompanyName"))
                ->first();
            if (empty($minorUser)) {
                $minorUserId = DB::table('boostapplogin.users')->insertGetId(
                    array('username' => '',
                        'email' => '',
                        'newUsername' => '-1',
                        'password' => $password,
                        'display_name' => $minorClient->__get("CompanyName"),
                        'FirstName' => $minorClient->__get("FirstName"),
                        'LastName' => $minorClient->__get("LastName"),
                        'ContactMobile' => '',
                        'AppLoginId' => $this->ContactMobile,
                        'status' => '1',
                        'parentId' => $parentUserId
                    ));
            } else {
                $minorUserId = $minorUser->id;
            }
            if (!empty($minorUserId)) {
                $getMinorStudio = DB::table('boostapplogin.studio')
                    ->where('ClientId', '=', $minorClient->__get("id"))
                    ->where('CompanyNum', '=', $minorClient->__get("CompanyNum"))
                    ->first();
                if (empty($getMinorStudio)) {
                    $minorStudioId = DB::table('boostapplogin.studio')->insertGetId(
                        array('StudioUrl' => $SettingsInfo->StudioUrl,
                            'StudioName' => $SettingsInfo->AppName,
                            'CompanyNum' => $this->CompanyNum,
                            'UserId' => $minorUserId,
                            'ClientId' => $minorClient->__get("id"),
                            'LastDate' => date('Y-m-d'),
                            'LastTime' => date('H:i:s'),
                            'Memotag' => $SettingsInfo->Memotag,
                            'Folder' => $SettingsInfo->Folder
                        ));
                } else {
                    $minorStudioId = $getMinorStudio->id;
                    $updateStudio = DB::table('boostapplogin.studio')->where('id', $minorStudioId)->update(
                        array(
                            'UserId' => $minorUserId,
                            'Status' => 0,
                            'LastDate' => date('Y-m-d'),
                            'LastTime' => date('H:i:s')
                        ));
                }
            }
        }
        return $newParentUser;
    }

    /**
     * @param $companyNum
     * @return mixed
     */
    public function getGreenPassReport($companyNum)
    {
        return DB::table($this->table)->select("id", "CompanyName", "ContactMobile", "greenPassStatus", "greenPassValid", "greenActionDate")->where("CompanyNum", "=", $companyNum)->where("Status", "=", 0)->get();
    }

    /**
     * @param $status
     * @param null $validDate
     * @return array
     */
    public function updateGreenPass($status, $validDate = null)
    {
        $res = ["success" => true, "msg" => ''];
        if (!$validDate && !$status) {
            $update = DB::table($this->table)->where('id', $this->__get('id'))->update(array('greenPassStatus' => $status, 'greenPassValid' => null, 'greenActionDate' => date('Y-m-d H:i:s')));
            CreateLogMovement(lang('update_greenpass_as_inactive'), $this->__get('id'));
            return $res;
        } else {
            if ($status && $validDate) {
                $update = DB::table($this->table)->where('id', $this->__get('id'))->update(array('greenPassStatus' => $status, 'greenPassValid' => $validDate, 'greenActionDate' => date('Y-m-d H:i:s')));
                if ($status == 1) {
                    CreateLogMovement(lang('update_greenpass_to_pending'), $this->__get('id'));
                } else if ($status == 2) {
                    CreateLogMovement(lang('update_greenpass_to_confirm'), $this->__get('id'));
                }
                return $res;
            }
        }
        $res['success'] = false;
        $res['msg'] = lang('action_cancled');
        return $res;
    }

    /**
     * @return array
     */
    public function resetGreenPassDateToAll()
    {
        $res = ["success" => true, "msg" => ''];
        $update = DB::table($this->table)
            ->where('CompanyNum', Auth::user()->CompanyNum)
            ->update([
                'greenPassStatus' => 0,
                'greenPassValid' => null,
                'greenActionDate' => date('Y-m-d H:i:s')
            ]);
        if ($update > 0) {
            CreateLogMovement(lang('reset_green_pass_date'), 0);
            return $res;
        }

        $res['success'] = false;
        $res['msg'] = lang('action_cancled');
        return $res;
    }

    /**
     * @param $param
     * @param $value
     * @param $company
     * @return mixed
     */
    public function getClientBy($param, $value, $company)
    {
        return DB::table($this->table)->where($param, "=", $value)->where("CompanyNum", "=", $company)->first();
    }

    /**
     * @return array[]
     */
    public function getTakanonReport()
    {
        $CompanyNum = Auth::user()->CompanyNum;

        $OpenTables = DB::table($this->table)
            ->where('client.CompanyNum', '=', $CompanyNum)->whereIn('client.Status', array(0, 2))->orderBy('client.CompanyName', 'ASC')->get();

        $resArray = array('data' => array());
        foreach ($OpenTables as $Task) {
            $tempArray = array();

            $ClientName = $Task->CompanyName;
            $ClientLink = '<a href="ClientProfile.php?u=' . $Task->id . '">' . $ClientName . '</a>';
            $ClientMobile = '<span class="unicode-plaintext" >' . $Task->ContactMobile . '</span>';

            $StatusText = $Task->Takanon == '1' ? '<span class="text-primary"><i class="fas fa-address-book"></i> יש</span>' :
                '<span class="text-danger"><i class="fas fa-address-book"></i> אין</span>';

            $ClientStatus = $Task->Status == '0' ? lang('active') : lang('interested_single');

            $tempArray[0] = $ClientLink;
            $tempArray[1] = $ClientMobile;
            $tempArray[2] = $ClientStatus;
            $tempArray[3] = $StatusText;
            $tempArray[4] = !empty($Task->TakanonDate) ? date('d/m/Y H:i', strtotime($Task->TakanonDate)) : '';

            array_push($resArray['data'], $tempArray);
        }
        return $resArray;
    }

    /**
     * @param $dateFrom
     * @param $dateTo
     * @return array[]
     */
    public function getNoneShow($dateFrom, $dateTo)
    {
        $CompanyNum = Auth::user()->CompanyNum;

        $OpenTables = DB::table($this->table)->select('id', 'CompanyName', 'ContactMobile', 'LastClassDate', 'BrandName', 'ActiveMembership')
            ->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('FreezStatus', '=', '0')->orderBy('CompanyName', 'ASC')->get();

        $resArr = array('data' => array());
        foreach ($OpenTables as $Task) {
            $tempArr = array();

            $classStudioActObj = new ClassStudioAct();
            // runtime is too big with this query
//            $latest = $classStudioActObj->GetlatestClass($Task->id);
            $classStudioActs = $classStudioActObj->getClassesByFixClientIdBetween($Task->id, $dateFrom, $dateTo);

            //If the client is in active status on all the classes
            if (!$classStudioActs) {
                $sinceTodayClasses = $classStudioActObj->getClassesByFixClientIdSince($Task->id, date('Y-m-d'));
                $TextClass = $sinceTodayClasses ? lang('yes') : lang('no');

                $clientActivitiesObj = new ClientActivities();
                $TextActivity = ($clientActivitiesObj->isActiveClient($Task->id)) ? lang('yes') : lang('no');

                $ClientLink = '<a href="ClientProfile.php?u=' . $Task->id . '"><span class="text-dark">' . $Task->CompanyName . '</span></a>';

                $tempArr[0] = "";
                $tempArr[1] = $ClientLink;
                $tempArr[2] = $Task->ContactMobile;
                $tempArr[3] = $TextActivity;
                $tempArr[4] = !empty($Task->LastClassDate) ? date('d/m/Y', strtotime($Task->LastClassDate)) : null;
                $tempArr[5] = htmlentities($Task->BrandName);
                $tempArr[6] = $TextClass;

                $resArr['data'][] = $tempArr;
            }
        }
        return $resArr;
    }

    /**
     * @param $CompanyNum
     * @return array[]
     */
    public function getMedicalInfo($CompanyNum)
    {

        $OpenTables = DB::table('client')->where('CompanyNum', '=', $CompanyNum)->whereIn('Status', array(0, 2))->orderBy('CompanyName', 'ASC')->get();
        $resArr = array("data" => array());

        foreach ($OpenTables as $Task) {
            $reportArray = array();
            $ClientUserName = $Task->CompanyName;
            $reportArray[0] = '<a href="ClientProfile.php?u=' . $Task->id . '"><span class="text-dark">' . $ClientUserName . '</span></a>';
            $reportArray[1] = $Task->ContactMobile;
            $reportArray[2] = ($Task->Status == '0' ? lang('active') : lang('interested_single'));
            $reportArray[3] = ($Task->Medical == '1' ? '<span class="text-primary"><i class="fas fa-briefcase-medical"></i> ' . lang('there_is') . '</span>' : '<span class="text-danger"><i class="fas fa-briefcase-medical"></i> ' . lang('there_is_not') . '</span>');

            $resArr["data"][] = $reportArray;
        }
        return $resArr;
    }

    /**
     * @param $CompanyNum
     * @param $OpenTables
     * @return array[]
     * @throws Exception
     */
    public function getLogInfo($CompanyNum, $OpenTables)
    {

        $OpenTableCount = count($OpenTables);
        $resArr = array("data" => array());

        foreach ($OpenTables as $Client) {

            if ($Client->UserId == '0') {
                $UserNameLog = lang('customer_card_gender');
            } else {
                $UserNameLogs = DB::table('users')->where('CompanyNum', '=', $CompanyNum)->where('role_id', '!=', '1')->where('id', $Client->UserId)->first();
                $UserNameLog = (isset($UserNameLogs) ? '<a href="AgentProfile.php?u=' . $UserNameLogs->id . '" >' . $UserNameLogs->display_name . '</a>' : '');
            }
            if ($Client->ClientId == '0') {
                $ClientUserNameLog = lang('customer_card_gender');
            } else {
                $ClientUserNameLogs = DB::table('client')->where('CompanyNum', '=', $CompanyNum)->where('id', $Client->ClientId)->first();
                $ClientUserNameLog = (isset($ClientUserNameLogs) ? '<a href="ClientProfile.php?u=' . $ClientUserNameLogs->id . '" >' . $ClientUserNameLogs->CompanyName . '</a>' : '');
            }
            $reportArray = array();
            $reportArray[0] = $OpenTableCount;
            $reportArray[1] = $Client->Text;
            $reportArray[2] = (new DateTime($Client->Dates))->format('d/m/Y');
            $reportArray[3] = (new DateTime($Client->Dates))->format('H:i:s');
            $reportArray[4] = $ClientUserNameLog;
            $reportArray[5] = $UserNameLog;

            $resArr["data"][] = $reportArray;
            $OpenTableCount--;
        }
        return $resArr;
    }

    /**
     * @param $actId
     * @param int $activityId
     * @return array
     */
    public function getClientPopUpInfo($actId, $activityId = 0)
    {
        $classActObj = new ClassStudioAct($actId);
        $activityId = $activityId ? $activityId : $classActObj->__get('ClientActivitiesId');

        $resArr = [
            "clientInfo" => $this,
            "ClientCrm" => (new Clientcrm())->getAllClientcrmByClientId($this->CompanyNum, $this->id),
            "ClientMedical" => (new ClientMedical())->getAllMedicalByClientId($this->CompanyNum, $this->id),
            "activity" => $activityId ? (new ClientActivities())->getActivityById($activityId, $this->CompanyNum) : null,
            "clientAct" => $actId ? $classActObj : null,
            "isFirstClass" => $classActObj->isFirstLesson($this->CompanyNum, $classActObj->__get('ClassDate'), $this->id),
            "clientLevel" => $this->ClassLevel != 0 ? (new ClientLevel())->getLevelById($this->ClassLevel) : 0
        ];
        return $resArr;
    }

    /**
     * @return string
     */
    public function getGreenPassIcon()
    {
        switch ($this->greenPassStatus) {
            case 0:
                $greenPassText = lang('no_green_pass');
                $cssClass = 'text-danger';
                $badgeIcon = 'far fa-badge';
                break;
            case 1:
                $greenPassText = lang('green_pass_pending_notice');
                $cssClass = 'text-orange';
                $badgeIcon = 'far fa-badge-check';
                break;
            default:
                $greenPassText = lang('green_pass_confirmed_notice');
                $cssClass = 'text-success';
                $badgeIcon = 'fas fa-badge-check';
                break;
        }
        $coronaIcon = '<i data-status="' . $this->greenPassStatus . '" data-id="' . $this->id .
            '" class="js-green-pass-icon ' . $badgeIcon . ' cursor-pointer mis-5 ' . $cssClass .
            '" data-toggle="tooltip" data-placement="top" title="' . $greenPassText . '"></i>';
        return $coronaIcon;
    }

    /**
     *
     */
    public function getNotExistClient()
    {
        $this->FirstName = lang('client_single');
        $this->LastName = lang('not_exist');
        $this->CompanyName = $this->FirstName . ' ' . $this->LastName;
        $this->id = 0;
    }

    /**
     * Check if activity is department 3 (Trial Membership),
     * if to does - change client status and create pipeline
     * @param $activityId
     */
    public function checkActivityChangeToLead($activityId)
    {
        $activityObj = new ClientActivities($activityId);
        $pipelineObj = new Pipeline();
        $brandObj = new Brand();

        if ($activityObj->__get('Department') == 3 || $activityObj->__get('isPaymentForSingleClass') == 1) {
            $this->__set('Status', 2);
            DB::table($this->table)->where('id', $this->id)->update(['Status' => 2]);

            $MainPipe = PipelineCategory::get_main_category($this->CompanyNum);
            $Pipe = LeadStatus::getNewLeadStatus($this->CompanyNum);

            return $pipelineObj->insert_into_table([
                'CompanyNum' => $this->CompanyNum,
                'Brands' => $brandObj->getMainBranchId($this->CompanyNum),
                'MainPipeId' => $MainPipe->id,
                'PipeId' => $Pipe->id,
                'ClientId' => $this->id,
                'FirstName' => $this->FirstName,
                'LastName' => $this->LastName,
                'CompanyName' => $this->CompanyName,
                'Email' => $this->Email,
                'ContactInfo' => $this->ContactMobile,
                'UserId' => Auth::user()->__get('id'),
                'ItemId' => $activityObj->__get('ItemId'),
                'Source' => lang('embed_trainers_new_pipe'),
            ]);
        }
    }

    /**
     * @param $companyNum
     * @param null $phone
     * @param null $email
     * @return bool
     */
    public function isDuplicatePhoneEmail($companyNum, $phone = null, $email = null)
    {
        $check = DB::table($this->table)->where('CompanyNum', $companyNum)
            ->where('id', '!=', $this->id ? $this->id : 0)
            ->where(function ($query) use ($phone, $email) {
                $query->where('Email', strtolower($email))->whereNotNull('Email')->where('Email', '!=', '')
                    ->Orwhere('ContactMobile', $phone)->whereNotNull('ContactMobile')->where('ContactMobile', '!=', '');
            })->get();
        return count($check) > 0;
    }

    /**
     * @param $companyNum
     * @param $phone
     * @return bool
     */
    public function isDuplicatePhone($companyNum, $phone)
    {
        $check = DB::table($this->table)
            ->where('CompanyNum', '=', $companyNum)
            ->where('parentClientId', '=', 0)
            ->where(function ($query) use ($phone) {
                $query->Orwhere('ContactMobile', 'like', '%' . $phone . '%')
                    ->Orwhere('ContactMobile', 'like', '%' . substr($phone, 1, strlen($phone)) . '%');
            })
            ->get();

        return count($check) > 0;
    }

    /**
     * @return Client|null
     */
    public function parent()
    {
        if ($this->_parent === false) {
            if (!$this->parentClientId) {
                $this->_parent = null;
            } else {
                $this->_parent = new Client($this->parentClientId);
            }
        }
        return $this->_parent;
    }

    /**
     * Getting the first matching activity for a client
     * @param $classTypeId
     * @return null|int id of matching activity or null if no matching activity found
     */
    public function getMatchingActivity($classTypeId)
    {
        /** @var ClientActivities[] $activities */
        $activities = ClientActivities::getClientActivities($this->__get("id"));
        foreach ($activities as $activity) {
            if ($activity->isValidForMeeting($classTypeId)) {
                return (int)$activity->id;
            }
        }
        return null;
    }

    /**
     * @param $classTypeId
     * @return array
     */
    public function getMatchingActivitiesList($classTypeId)
    {
        /** @var ClientActivities[] $activities */
        $activities = ClientActivities::getClientActivities($this->__get("id"));
        $result = [];
        foreach ($activities as $activity) {
            if ($activity->isValidForMeeting($classTypeId)) {
                $result[] = $activity->id;
            }
        }
        return $result;
    }

    /**
     * @return Rank[]
     */
    public function ranks()
    {
        return Rank::getRanks($this->__get('id'));
    }

    /**
     * @return int
     */
    public function entranceCount()
    {
        return ClassStudioAct::countByClient($this->__get('id'));
    }

    /**
     * @return Settings
     */
    public function studioSettings()
    {
        if (!$this->_studioSettings) {
            $this->_studioSettings = new Settings($this->CompanyNum);
        }
        return $this->_studioSettings;
    }

    /**
     * @return Token[]
     */
    public function tokens()
    {
        if (!$this->_tokens) {
            $this->_tokens = Token::getTokens($this->CompanyNum, $this->id);
        }
        return $this->_tokens;
    }

    /**
     * @return int
     */
    public function getCountActivities(): int
    {
        return ClientActivities::countActivitiesForClient($this->id, $this->CompanyNum);
    }

    /**
     * @param int $department
     * @return bool false if Over Trail Activities
     */
    public function updateLeadClientIfTrial(int $department): bool
    {
        if ((int)$this->Status === self::STATUS_LEAD) {
            if ($department === Item::DEPARTMENT_TRIAL) {
                if (ClientActivities::isOverTrialActivities($this->id, $this->CompanyNum)) {
                    return false;
                }
            } else {
                return ClientService::updateStatus($this, self::STATUS_ACTIVE);
            }
        } else if ($this->JoinDate === null) {
            $this->JoinDate = date('Y-m-d');
        }
        return true;
    }

    /**
     * @param int $classStudioDateId
     * @return bool
     */
    public function isAssignToClass(int $classStudioDateId):bool
    {
        return ClassStudioAct::clientInClass($this->id, $classStudioDateId);
    }

    /**
     * todo can remove updateBalanceAmount and change this function name
     *A function to update the customer's balance, including consideration of a paying customer
     */
    public function updateBalanceAmountNew(): void
    {
        if ((int)$this->PayClientId !== 0) {
            $payClientId = $this->PayClientId;
            $balanceAmount = ClientActivities::getBalanceAmountOfClient($payClientId);
        } else {
            $payClientId = $this->id;
            $balanceAmount = '0.00';
        }
        $ClientInfoerArray = $this->getClientsByPayClientId($payClientId);
        if (!empty($ClientInfoerArray)) {
            foreach ($ClientInfoerArray as $CheckClientInfo) {
                $balanceAmount += ClientActivities::getBalanceAmountOfClient($CheckClientInfo->id);
            }
        } else {
            $balanceAmount += ClientActivities::getBalanceAmountOfClient($payClientId);
        }
        $this->BalanceAmount = $balanceAmount;
        $this->save();
    }

    /**
     * @return string
     */
    public function getCityName(): string
    {
        if(isset($this->City) && (int)$this->City !== 0) {
            return City::getNameByCityId($this->City) ?? '';
        }
        return '';
    }


    /**
     * @return string
     */
    public function getStreetName(): string
    {
        if(!isset($this->Street)) {
            return '';
        }
        if((int)$this->Street === 0 || (int)$this->Street === 99999999){
            return $this->StreetH ?? '';
        }
        return Street::getNameById($this->Street);
    }

    /**
     * Checks if there is an email for him or his father
     * @return bool
     */
    public function hasEmail():bool {
        if(!empty($this->Email)) {
            return true;
        }
        if($this->isMinorClient()) {
            return !empty(self::where('id', $this->parentClientId)->pluck('Email') ?? '');
        }
        return false;
    }

    /**
     * @param $companyNum
     * @param $phone
     * @return int 0-not found
     */
    public static function findByPhoneAndStudio($companyNum, $phone): int
    {
        return self::where('CompanyNum', '=', $companyNum)
            ->where(function ($query) use ($phone) {
                $query->Orwhere('ContactMobile', 'like', '%' . $phone . '%')
                    ->Orwhere('ContactMobile', 'like', '%' . substr($phone, 1, strlen($phone)) . '%');
            })
            ->pluck('id') ?? 0;
    }

    /**
     * @param $clientId
     * @return string
     */
    public static function getNameById($clientId): string
    {
        return self::where('id', '=', $clientId)
                ->pluck('CompanyName') ?? '';
        ;
    }

    public function getFullName(): string
    {
        return $this->FirstName . ' ' . $this->LastName;
    }


}
