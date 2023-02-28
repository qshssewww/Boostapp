<?php


class CoronaHealthCheck
{
    private $id;

    private $classstudio_act_id;

    private $CompanyNum;

    private $checking_place;

    private $date;

    private $classstudio_act;

    private $client;

    private $table;

    public function __construct()
    {
        $this->table = "corona_health_check";
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

    public function getCoronaCheckById($studio_act_id){
        $corona = DB::table($this->table)->where("classstudio_act_id","=", $studio_act_id)->first();
        if($corona != null) {
            foreach ($corona as $key => $value) {
                $this->__set($key, $value);
            }
            $this->classstudio_act = DB::table("classstudio_act")->where("id","=", $studio_act_id)->first();
        }
    }
    public function getCoronaCheck($company){
        $corona = DB::table($this->table)->where("CompanyNum","=", $company)->get();
        $coronaArr = array();
        foreach ($corona as $cor){
            $corObj = new CoronaHealthCheck();
            foreach ($cor as $key => $value){
                $corObj->__set($key,$value);
            }
            $classstudio_act = DB::table("classstudio_act")->where("id","=", $corObj->__get("classstudio_act_id"))->where('CompanyNum', '=', $corObj->__get('CompanyNum'))->first();
            if($classstudio_act) {
                $client = DB::table("client")->where("id","=", $classstudio_act->ClientId)->first();
                $corObj->__set("classstudio_act",$classstudio_act);
                $corObj->__set("client",$client);
                array_push($coronaArr,$corObj);
            }

        }
        return $coronaArr;
    }
}