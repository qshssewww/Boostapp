<?php
require_once "Utils.php";

class InvildMemberShip extends Utils
{
    private $CompanyNum;


    private  $user;
    public function __construct()
    {
        $this->user = Auth::user();
        $company = Company::getInstance(false);
        $this->CompanyNum = $company->__get("CompanyNum");
    }
    public function GetInvildMemberShip(){
        $OpenTables = DB::table('client_activities')
            ->where('TrueDate','<=', date('Y-m-d'))->where('Freez', '!=', 1)->where('Department','=', '1')->where('CompanyNum','=', $this->CompanyNum)->where('Status','=', '0')->where('ClientStatus','=', '0')->where('KevaAction','=', '0')
            ->Orwhere('TrueDate','<=', date('Y-m-d'))->where('Freez', '=', 1)->where('EndFreez', '<=', date('Y-m-d'))->where('Department','=', '1')->where('CompanyNum','=', $this->CompanyNum)->where('Status','=', '0')->where('ClientStatus','=', '0')->where('KevaAction','=', '0')
            ->Orwhere('TrueBalanceValue','<=', '0')->where('Department','=', '2')->where('CompanyNum','=', $this->CompanyNum)->where('Status','=', '0')->where('ClientStatus','=', '0')->where('KevaAction','=', '0')
            ->Orwhere('TrueDate','<=', date('Y-m-d'))->where('Freez', '!=', 1)->where('Department','=','2')->where('CompanyNum','=', $this->CompanyNum)->where('Status','=', '0')->where('ClientStatus','=', '0')->where('KevaAction','=', '0')
            ->Orwhere('TrueDate','<=', date('Y-m-d'))->where('Freez', '=', 1)->where('EndFreez', '<=', date('Y-m-d'))->where('Department','=','2')->where('CompanyNum','=', $this->CompanyNum)->where('Status','=', '0')->where('ClientStatus','=', '0')->where('KevaAction','=', '0')
            ->get();

        $OpenTableCount = count($OpenTables);


        $number = $OpenTableCount;
        $i=1;
        $ClientsIdUsed = [];
        $countUsed = 0;
        foreach($OpenTables as $Client) {
            $exist = false;
            foreach ($ClientsIdUsed as $used) {

                if ($used->ClientId == $Client->ClientId) {
                    $exist = true;
                    break;
                }
            }
            if(!$exist){
                array_push($ClientsIdUsed,$Client);
                $countUsed++;
            }
        }
        $OpenTableCount = $countUsed;
        $number = $OpenTableCount;
        $ClientsIdUsed = [];
        return $number;
    }


}