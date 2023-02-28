<?php
require_once "Utils.php";

class Payments extends Utils
{
    private $id;
    private $CompanyNum;
    private $MaxDistribution;
    private $LimitPayments;
    private $Interest;
    private $Paymentscol;
    private $RegularPayment;
    private $PeriodicPayments;
    private $table;

    public function __construct()
    {
        $this->table = "Payments";
    }
    public function __set($name, $value)
    {
        if(property_exists($this,$name)){
            $this->$name = $value;
        }
    }

    public function __get($name)
    {
        if(property_exists($this,$name)){
            return $this->$name;
        }
        return null;
    }


    public function InsertPaymentsNewData($arrayData){
        $idInsert = DB::table($this->table)
               ->insertGetId($arrayData);
        return $idInsert;
    }

    public function GetPaymentsByCompanyNum($CompanyNum){
        $data = DB::table($this->table)
            ->where('CompanyNum','=',$CompanyNum)
            ->first();
        if (!$data) {
            $id = $this->InsertPaymentsNewData(["CompanyNum" => $CompanyNum, "PeriodicPayments" => 0, "RegularPayment" => false, "MaxDistribution" => 12]);
            $data = DB::table($this->table)
                ->where('id', $id)->first();
        }
        return $data;
    }

    public function UpdatePayments($arrayData,$CompanyNum){
        $affact = DB::table($this->table)
            ->where('CompanyNum','=',$CompanyNum)
            ->update($arrayData);
        return $affact;
    }



    
}