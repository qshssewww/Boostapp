<?php

require_once __DIR__ . "/Utils.php";
require_once __DIR__ . "/StudioBoostappLogin.php";
require_once __DIR__ . "/../../app/helpers/ImageHelper.php";


class UserBoostappLogin extends Utils {
    private static $table = "boostapplogin.users";

    protected $id;
    protected $username;
    protected $email;
    protected $newUsername;
    protected $password;
    protected $display_name;
    protected $joined;
    protected $status;
    protected $role_id;
    protected $last_session;
    protected $reminder;
    protected $remember;
    protected $FirstName;
    protected $LastName;
    protected $ContactMobile;
    protected $AppLoginId;
    protected $UploadImage;
    protected $tokenFirebase;
    protected $OS;
    protected $newPassword;
    protected $Private;
    protected $PassAct;
    protected $parentId;
    protected $blockCounter;
    protected $blockedTime;
    protected $language;

    public static function insert_into_table($data) {
        $id = DB::table(self::$table)->insertGetId($data);
        return $id;
    }

    public static function email_exists($email) {
        if (!$email) return false;
        $users = DB::table(self::$table)->where('email', $email)->get();
        return (count($users) > 0);
    }

    /**
     * @param $phone
     * @param $withoutPrefixPhone
     * @return UserBoostappLogin|null
     */
    public static function find_by_phone($phone, $withoutPrefixPhone = -1)
    {
        $res = DB::table(self::$table)->where('ContactMobile', $phone)
            ->Orwhere('newUsername', '=', $phone);
        if ($withoutPrefixPhone != -1) {
            $res = $res->Orwhere('ContactMobile', '=', $withoutPrefixPhone)
                ->Orwhere('newUsername', '=', $withoutPrefixPhone);
        }
        $res = $res->first();

        if (!$res)
            return null;
        return
            new self($res->id);
    }

    public static function find_by_id($userId) {
        $res = DB::table(self::$table)->where('id', $userId)->first();
        if (!$res)
            return null;
        return
            new self($res->id);
    }


    public function __construct($id = null) {
        if ($id) {
            $this->setData($id);
        }
    }

    public function setData($id) {
        $data = DB::table(self::$table)->where("id", "=", $id)->first();
        if($data != null) {
            foreach ($data as $key => $value) {
                $this->__set($key, $value);
            }
        }
    }

    public function __set($name, $value) {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }

    public function __get($name) {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return null;
    }

    public function update() {
        $userArr = $this->createArrayFromObj($this);
        $res = DB::table(self::$table)->where("id", $this->id)->update($userArr);
        return $res;
    }
    public static function updateClient($userId,$data) {
        $res = DB::table(self::$table)->where("id","=", $userId)->update($data);
        return $res;
    }
    
    public function addLoginAccount($clientObj, $appPassword, $sendNotification = true){
        $SettingsInfo = Company::getInstance();
        $password = Hash::make($appPassword);

        if ($clientObj->__get('id') !== null) {
            $AppUsers = DB::table(self::$table)->where('newUsername', $clientObj->ContactMobile)->first();

            if(empty($AppUsers)) {
                $AppUserId = self::insert_into_table([
                    'username' => $clientObj->Email ?? '',
                    'email' => $clientObj->Email ?? '',
                    'newUsername' => $clientObj->ContactMobile,
                    'display_name' => $clientObj->CompanyName, 
                    'FirstName' => $clientObj->FirstName,
                    'LastName' => $clientObj->LastName, 
                    'ContactMobile' => $clientObj->ContactMobile, 
                    'AppLoginId' => $clientObj->ContactMobile,
                    'password' => $password, 
                    'status' => 1
                ]);
            } else {
                $AppUserId = $AppUsers->id;

                DB::table(self::$table)
                    ->where('id', $AppUserId)    
                    ->update(array('password' => $password, 'PassAct' => 0));

            }

            $studioObj = StudioBoostappLogin::findByClientIdAndCompanyNum($clientObj->id, $clientObj->CompanyNum);
            if (empty($studioObj)) {
                StudioBoostappLogin::insert_into_table([
                    'StudioUrl' => $SettingsInfo->__get('StudioUrl'), 
                    'StudioName' => $SettingsInfo->__get('AppName'), 
                    'CompanyNum' => $clientObj->CompanyNum, 
                    'UserId' => $AppUserId, 
                    'ClientId' => $clientObj->id, 
                    'Status' => 0, 
                    'LastDate' => date('Y-m-d'), 
                    'LastTime' => date('H:i:s'), 
                    'Memotag' => $SettingsInfo->__get('Memotag'), 
                    'Folder' => $SettingsInfo->__get('Folder')
                ]);
            
            } else if($studioObj->UserId != $AppUserId) {

                $studioData = [
                    'UserId' => $AppUserId,
                    'LastDate' => date('Y-m-d'),
                    'LastTime' => date('H:i:s'),
                ];

                StudioBoostappLogin::updateStudioById($studioObj->id, $studioData);
            }
            if($sendNotification) {
                AppNotification::sendRegistrationDetails($clientObj);
            }
        } else {
            json_message(lang('error_customer_ajax'), false);
            exit;
        }
    }

    public function findUserByClientIDCompanyNum($clientId, $companyNum){
        $user =  StudioBoostappLogin::findByClientIdAndCompanyNum($clientId, $companyNum);
        if($user) {
            return new UserBoostappLogin($user->UserId);
        }
        return new UserBoostappLogin();
    }

    /**
     * return user's avatar (profile picture)
     * @param $userId
     * @return string
     */
    public static function getAvatar($userId){
        $user = new self($userId);
        if (!empty($user->__get('UploadImage'))) {
            return ImageHelper::getImageWithAppPrefix($user->__get('UploadImage'));
        }
        return null;
    }
}
