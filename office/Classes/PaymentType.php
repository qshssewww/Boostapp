<?php
require_once "Utils.php";

Class PaymentType extends Utils {
    protected $id;
    protected $CompanyNum;
    protected $amount;
    protected $client_id;
    protected $true_client_id;
    protected $client_activity_id;
    protected $type;
    protected $bank_account_num;
    protected $bank;
    protected $bank_branch;
    protected $cheque_num;
    protected $due_date;
    protected $reference_num;
    protected $deposit_date;
    protected $date;
    protected $status;

    private static $table = "boostapp.payment_type";

    public function __construct($id = null){
        if($id != null){
            $this->setData($id);
        }
    }

    public function setData($id){
        $data = DB::table(self::$table)->where("id", $id)->first();
        if($data != null) {
            foreach ($data as $key => $value) {
                $this->__set($key, $value);
            }
        }
    }

    public function __set($name, $value){
        if(property_exists($this,$name)){
            $this->$name = $value;
        }
    }

    public function __get($name){
        if(property_exists($this,$name)){
            return $this->$name;
        }
        return null;
    }

    public static function insertIntoTable($data) {
        return DB::table(self::$table)->insertGetId($data);
    }

    public static function getAllByCompanyNum($companyNum) {
        return DB::table(self::$table)->where("CompanyNum", $companyNum)->where("status", 0)->get();
    }

    public static function getPaymentsByClientId($clientId) {
        return DB::table(self::$table)->where("client_id", $clientId)->get();
    }

    public function update($data) {
        unset($data["id"]);
        $clientArr = $this->createArrayFromObj($this);
        $res = DB::table(self::$table)->where("id", $this->id)->update($data);
        return $res;
    }

    public function delete() {
        return DB::table(self::$table)->where("id", $this->id)->update(["status", 1]);
    }
}