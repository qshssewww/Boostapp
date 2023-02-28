<?php

require_once "Utils.php";
require_once dirname(__FILE__) . "/RegistrationFees.php";

class ClientRegistrationFees extends Utils
{
    protected $id;
    protected $CompanyNum;
    protected $client_id;
    protected $registration_fee_id;
    protected $purchase_page_id;
    protected $purchase_time;
    protected $status;

    private $table;

    public function __construct()
    {
        $this->table = "boostapp.client_registration_fees";
    }

    public function getClientReg($client_id,$company){
        return DB::table($this->table)->where("client_id","=",$client_id)->where("CompanyNum","=",$company)->get();
    }

    public function __set($name, $value)
    {
        if(property_exists($this,$name)){
            $this->$name = $value;
        }
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if(property_exists($this,$name)){
            return $this->$name;
        }
        return null;
    }

    public function getReg($id,$company = null,$user = null){
        if($company == null && $user == null){
            return DB::table($this->table)->where("id","=",$id)->first();
        }
        else{
            return DB::table($this->table)->where("client_id","=",$user)->where("id","=",$id)->where("CompanyNum","=",$company)->first();
        }
    }
    public function getClientRegByRegId($id){
        return DB::table($this->table)->where("registration_fee_id","=",$id)->first();
    }
    public function clientRegForClientProfile($id,$company,$index){
        $regs = $this->getClientReg($id,$company);
        if(empty($regs)){
            return false;
        }
        $regFeeObj = new RegistrationFees();
        $regsArr = array();
        foreach ($regs as $reg){
            $clientReg = new stdClass();
            $regFee = $regFeeObj->getSingleRegistration($reg->registration_fee_id);
            $clientReg->id = $reg->registration_fee_id;
            $clientReg->MemberShip = 'BA999';
            $clientReg->stiilValid = ($reg->status == 1) ? 1 : 0;
            $clientReg->Status = ($reg->status == 1) ? 0 : 1;
            if($regFee->Type == 3){
                if ($regFee->Vaild_Type == "1") {
                    $unitType = "day";
                } else if ($regFee->Vaild_Type == "2") {
                    $unitType = "week";
                } else {
                    $unitType = "month";
                }
                $clientReg->TrueDate = date('Y-m-d', strtotime("+" . $regFee->Vaild . " " . $unitType, strtotime($reg->purchase_time)));
                $clientReg->VaildDate = date('Y-m-d', strtotime("+" . $regFee->Vaild . " " . $unitType, strtotime($reg->purchase_time)));
                if($clientReg->TrueDate < date('Y-m-d')){
                    $clientReg->stiilValid = 0;
                    $clientReg->Status = 1;
                    $data = array(
                        "status" => 0
                    );
                    $res =$this->update($reg->id,$data);
                }
            }
            else{
                $clientReg->TrueDate = null;
            }
            $clientReg->TrueBalanceValue = null;
            $clientReg->registration = 1;
            $clientReg->Department = 4;
            $clientReg->StartDate = date("Y-m-d",strtotime($reg->purchase_time));
            $clientReg->BalanceValue = 0;
            $clientReg->CardNumber = $index + 1;
            $index++;
            $clientReg->ItemText = $regFee->ItemName;
            $clientReg->ItemPrice = $regFee->ItemPrice;
            $clientReg->BalanceMoney = "0.00";
            $clientReg->Dates = $reg->purchase_time;
            $clientReg->FirstDateStatus = 0;
            $clientReg->StartFreez = null;
            $clientReg->EndFreez = null;
            $clientReg->Freez = 0;
            $clientReg->FreezDays = 0;
            $clientReg->FreezLog = null;
            $clientReg->FreezEndLog = null;
            $clientReg->BalanceValueLog = null;
            $clientReg->ClientId = $reg->client_id;
            $clientReg->TrueClientId = 0;
            $clientReg->StudioVaildDateLog = 0;
            $clientReg->isForMeeting = 0;
            $clientReg->isDisplayed = 1;
            $clientReg->isPaymentForSingleClass = 0;
            array_push($regsArr,$clientReg);
        }
        return $regsArr;
    }

    public function update($id,$data){
        return DB::table($this->table)->where("id","=",$id)->update($data);
    }
}
