<?php

require_once "Utils.php";
require_once "PaymentPage.php";

class MeshulamPayments extends Utils {
    protected $id;
    protected $company_num;
    protected $client_id;
    protected $card_token;
    protected $amount_paid;
    protected $payment_sum;
    protected $total_amount;
    protected $purchase_date;
    protected $last_payment_date;
    protected $last_payment_num;
    protected $total_payments;
    protected $payment_process_id;
    protected $transactions;
    protected $status;

    private static $table = 'boostapp.meshulam_payments';

    public function __construct($id = null){
        if($id != null){
            $this->setData($id);
        }
    }

    public function setData($id){
        $data = DB::table(self::$table)->where("id", "=", $id)->where('status', 0)->first();
        if($data != null) {
            foreach ($data as $key => $value) {
                $this->__set($key, $value);
            }
        }
    }

    public static function getByProcessId($id) {
        return DB::table(self::$table)->where("payment_process_id", $id)->first();
    }
    public static function getPayment($id) {
        return DB::table(self::$table)->where("id", $id)->first();
    }
    public static function getPaymentsDue() {
        $date =  Date('Y-m-d', strtotime("-1 month"));
        $e =  DB::table(self::$table)
            ->select('boostapp.client.CompanyName', 'boostapp.client.ContactMobile', 'boostapp.client.Email', self::$table . '.*')
            ->leftjoin('boostapp.client', 'boostapp.client.id', '=', self::$table .'.client_id')
            ->where(self::$table . '.last_payment_date', '<=', Date('Y-m-d', strtotime("-1 month")))
            ->where(self::$table . ".status","=", 1)
            ->get();
        return $e;
    }

    public static function insert_into_table($data) {
        return DB::table(self::$table)->insertGetId($data);
    }

    public static function staticUpdate($id, $data) {
        return DB::table(self::$table)->where("id", $id)->update($data);
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

    public function getPaymentsForClientProfile($clientId,$company){
        $pp = new PaymentPage();
        $payments = DB::table(self::$table)->where("client_id","=",$clientId)->where("company_num","=",$company)->get();
        $classArr = array();
        foreach ($payments as $pay){
            $classAct = new stdClass();
            $classAct->id = $pay->id;
            $classAct->CompanyNum = $company;
            $classAct->Brands = "";
            $classAct->ClientId = $clientId;
            $classAct->TokenId = $pay->token_id;
            $classAct->TypeKeva = $pay->type_shva;
            $classAct->NumDate = 1;
            $classAct->TypePayment = 3;
            $classAct->Amount = $pay->payment_sum;
            $classAct->CountPayment = $pay->total_payments == -1 ? "999" : $pay->total_payments;
            $classAct->LastPayment = date("Y-m-d",strtotime($pay->last_payment_date));
            $classAct->NumPayment = $pay->total_payments == -1 ? "999" : $pay->total_payments;
            $classAct->tashType = 0;
            $classAct->Tash = 1;
            $classAct->Status = $pay->status == 1 ? 0 : 1;
            $classAct->PageId = $pay->payment_page;
            $classAct->Dates = $pay->purchase_date;
            $classAct->UserId = 0;
            $classAct->NextPayment = date("d/m/Y",strtotime("+1 month",strtotime($pay->last_payment_date)));
            if($pay->last_payment_num == $pay->total_payments){
                $classAct->NextPayment = null;
            }
            $payPage = $pp->getRow($pay->payment_page);
            $classAct->Text = $payPage->Title;
            $classAct->ItemId = $payPage->ItemId;
            $classAct->newApi = 1;
            array_push($classArr,$classAct);
        }
        return $classArr;
    }
    public static function getSinglePaymentForClientProfile($id){
        $pp = new PaymentPage();
        $pay = DB::table(self::$table)->where("client_id","=",$id)->get();
        $classAct = new stdClass();
        $classAct->id = $pay->id;
        $classAct->CompanyNum = $pay->company_num;
        $classAct->Brands = "";
        $classAct->ClientId = $pay->client_id;
        $classAct->TokenId = $pay->token_id;
        $classAct->TypeKeva = $pay->type_shva;
        $classAct->NumDate = 1;
        $classAct->TypePayment = 3;
        $classAct->Amount = $pay->payment_sum;
        $classAct->CountPayment = $pay->total_payments == -1 ? "999" : $pay->total_payments;
        $classAct->LastPayment = date("Y-m-d",strtotime($pay->last_payment_date));
        $classAct->NumPayment = $pay->payment_sum;
        $classAct->tashType = 0;
        $classAct->Tash = 1;
        $classAct->Status = $pay->status == 1 ? 0 : 1;
        $classAct->PageId = $pay->payment_page;
        $classAct->Dates = $pay->purchase_date;
        $classAct->UserId = 0;
        $classAct->NextPayment = date("Y-m-d",strtotime("+1 month",strtotime($pay->last_payment_date)));
        if($pay->last_payment_num == $pay->total_payments){
            $classAct->NextPayment = null;
        }
        $payPage = $pp->getRow($pay->payment_page);
        $classAct->Text = $payPage->Title;
        $classAct->ItemId = $payPage->ItemId;
        $classAct->newApi = 1;

        return $classAct;
    }
}
