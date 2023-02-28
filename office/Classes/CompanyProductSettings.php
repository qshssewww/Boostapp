<?php


class CompanyProductSettings
{
    protected $id;
    protected $CompanyNum;
    protected $manageMemberships;
    protected $offsetMemberships;
    protected $spreadPayments;
    protected $bitPayments;
    protected $date_created;
    protected $notificationAtEnd;
    protected $NotificationDays;
    protected $familyMembershipTransfer;
    
    private $table;

    public function __construct()
    {
        $this->table = "boostapp.CompanyProductSettings";
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

    public function getSingleByCompanyNum($CompanyNum){
        $settings = DB::table($this->table)->where("CompanyNum", "=", $CompanyNum)->first();
        if ($settings){
            return $settings;
        }else{
            $settings=$this->insertNew($CompanyNum);
            return $settings;
        }
    }
    public function getSingleById($id){
        return DB::table($this->table)->where("id", "=", $id)->first();
    }

    public function insertNew($CompanyNum){
        $insertedId = DB::table($this->table)->insertGetId(array(
            "CompanyNum" => $CompanyNum
        ));
        return $this->getSingleByCompanyNum($CompanyNum);
    }

    public function updateById($id,$data){
        return DB::table($this->table)->where("id", "=", $id)->update($data);
    }

    /**
     * @param $company
     * @param $data
     * @return mixed
     */
    public function updateByCompanyNum($company, $data)
    {
        return DB::table($this->table)->where("CompanyNum", $company)->update($data);
    }
}
