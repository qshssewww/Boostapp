<?php

require_once "Utils.php";
require_once "Users.php";

class UserActivation extends Utils {
    protected $id;
    protected $user_id;
    protected $activation_hash;
    protected $registration_date;

    private static $table = "boostapp.user_activation";

    public function __construct($id = null)
    {
        if($id != null){
            $this->setData($id);
        }
    }

    public function __set($name, $value)
    {
        if(property_exists($this, $name)){
            $this->$name = $value;
        }
    }

    public function __get($name)
    {
        if(property_exists($this, $name)){
            return $this->$name;
        }
        return null;
    }

    public function setData($id){
        $userActivation = DB::table(self::$table)->where("id", $id)
            ->first();
            
        if($userActivation != null) {
            foreach ($userActivation as $key => $value) {
                $this->__set($key, $value);
            }
        }
    }

    /**
     * @param $userId
     * @param $email
     * @return false
     */
    public static function sendActivation($userId, $email)
    {
        $hash = sha1(rand(1000, 9999) . $userId . $email);
        $user = Users::find($userId);

        if (!$user) {
            return false;
        }

        return DB::table(self::$table)->insertGetId([
            "user_id" => $userId,
            "activation_hash" => $hash
        ]);
    }
}
