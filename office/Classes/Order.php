<?php

require_once "Utils.php";

class Order extends Utils
{
    private static $table = "boostapp.order";

    protected $id;
    protected $ClientId;
    protected $CompanyNum;
    protected $Amount;
    protected $Discount;
    protected $Interest;
    protected $TotalAmount;
    protected $CouponId;
    protected $CouponCode;
    protected $PaymentType;
    protected $PaymentMethod;
    protected $NumPayment;
    protected $TransactionId;
    protected $TokenId;
    protected $Description;
    protected $CreatedAt;
    protected $Status;

    public function __construct($id = null) {
        if ($id) {
            $this->setData($id);
        }
    }

    public function setData($id) {
        $data = DB::table(self::$table)->where("id", $id)->first();
        if ($data) {
            foreach ($data as $key => $value) {
                $this->__set($key, $value);
            }
        }
    }

    public function __set($name, $value) {
        if(property_exists($this,$name)){
            $this->$name = $value;
        }
    }

    public function __get($name) {
        if(property_exists($this, $name)){
            return $this->$name;
        }
        return null;
    }

    public static function insert_into_table($data) {
        $id =  DB::table(self::$table)->insertGetId($data);
        return $id;
    }
}