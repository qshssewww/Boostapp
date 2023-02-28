<?php
require_once "Utils.php";

class RegistrationFees extends Utils
{
    private $id;
    private $CompanyNum;
    private $Type;
    private $ItemName;
    private $ItemPrice;
    private $ItemPriceVat;
    private $VatAmount;
    private $Vaild;
    private $Vaild_Type;
    private $Brand;
    private $NotificationDays;
    private $Status;
    private $Dates;
    private $AllMemberships;
    private $MembershipList;
    private $disabled;
    private  $table;

     public function __construct()
     {
         $this->table = "registration_fees";
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


    public function InsertRegistrationFeesNewData($arrayData){
         if($arrayData["vat"] == true){
             unset($arrayData["vat"]);
             $arrayData["VatAmount"] = 17;
         }
         else{
             unset($arrayData["vat"]);
             $arrayData["VatAmount"] = 0;
         }
        $arrayData["Brand"] = $arrayData["Brand"] ?? "BA999";
         $idInsert = DB::table($this->table)
            ->insertGetId($arrayData);
         return $idInsert;
    }

    public function GetRegistrationFeesByCompanyNum($CompanyNum){
        $data = DB::table($this->table)
            ->where('CompanyNum','=',$CompanyNum)->where("Status","=",0)
                ->get();
        return $data;
    }

    public function UpdateRegistrationFees($arrayData,$id)
    {
        unset($arrayData["id"]);
        if($arrayData["vat"] == true){
            unset($arrayData["vat"]);
            $arrayData["VatAmount"] = 17; //need to be changed to parameter in the future
        }
        else{
            unset($arrayData["vat"]);
            $arrayData["VatAmount"] = 0;
        }
        $arrayData["Brand"] = $arrayData["Brand"] ?? "BA999";
        $affact = DB::table($this->table)
            ->where('id', '=', $id)
            ->where("CompanyNum","=",$arrayData["CompanyNum"])
            ->update($arrayData);
        return $affact;
    }
    public function CompanyRegistretionCounter($company){
         $res = DB::table($this->table)->where("CompanyNum","=",$company)->where("Status", "=", 0)->get();
         return count($res);

    }

    public function getSingleRegistration($id){
        return $res = DB::table($this->table)->where("id","=",$id)->first();
    }
    public function disablePayment($id,$disabled,$companyNum){
        $res = DB::table($this->table)->where("id","=",$id)->where("CompanyNum","=",$companyNum)->update(array("disabled" => $disabled));
        return $res;
    }
    public function deletePayment($id,$companyNum){
        $res = DB::table($this->table)->where("id","=",$id)->where("CompanyNum","=",$companyNum)->update(array("status" => 1));
        return $res;
    }
    public function getFixedPaymentOfItem($companyNum,$membership){
        $res = DB::table($this->table)->where("CompanyNum", "=", $companyNum)->where("Status", "=", 0)->where("disabled", "=", 0)->where("AllMemberships", "=", 1)->get();
        $result = DB::table($this->table)->where("CompanyNum", "=", $companyNum)->where("Status", "=", 0)->where("disabled", "=", 0)->where("AllMemberships", "=", 0)->get();
        foreach ($result as $re) {
            $memberships = json_decode($re->MembershipList,true);
            foreach ($memberships as $mem){
                if($mem["item"] == $membership){
                    array_push($res,$re);
                }
            }
        }
        return $res;


    }


}
