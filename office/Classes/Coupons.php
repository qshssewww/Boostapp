<?php


class Coupons
{
    private $id;

    private $CompanyNum;

    private $PageId;

    private $Title;

    private $Code;

    private $Amount;

    private $StartDate;

    private $EndDate;

    private $Status;

    private $Limit;

    private $CountLimit;

    private $disabled;

    private $isPercentage;

    private $limitForProducts;

    private $table;

    public function __construct($id = null) {
        $this->table = "boostapp.coupon";
        if ($id) {
            $data = DB::table($this->table)->where("id", $id)->first();
            $this->setData($data);
        }
    }

    public function setData($data) {
        if ($data) {
            foreach ($data as $key => $value) {
                $this->__set($key, $value);
            }
        }
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

    public function getMultipleByCompanyNum($CompanyNum){
        $coupons = DB::table($this->table)->where("CompanyNum", "=", $CompanyNum)->where('Status',"=",0)->get();
        if ($coupons){
            return $coupons;
        }else{
            return [];
        }
    }

    public function getSingleById($id){
        $coupon = DB::table($this->table)->where("id", "=", $id)->first();
        if ($coupon){
            return $coupon;
        }else{
            return null;
        }
    }
    public function testByCompanyNumAndCodeExists($code,$CompanyNum){
        $coupon = DB::table($this->table)->where("Code", "=", $code)->where('CompanyNum',"=",$CompanyNum)->where('Status',"=",0)->first();
        if ($coupon){
            return true;
        }else{
            return false;
        }
    }


    /**
     * @param $data
     * @return null
     */
    public function insertNew($data)
    {
        $insertedId = DB::table($this->table)->insertGetId($data);
        $newCoupon = $this->getSingleById($insertedId);
        return $newCoupon;
    }

    /**
     * @param $id
     * @param $data
     * @return null
     */
    public function updateById($id, $data)
    {
        DB::table($this->table)->where("id", "=", $id)->update($data);
        $updatedCoupon = $this->getSingleById($id);
        return $updatedCoupon;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteById($id)
    {
        $updated = DB::table($this->table)->where("id", $id)->update(array('Status' => 1));
        return $updated;
    }
}