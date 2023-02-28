<?php

require_once 'Utils.php';

class Size extends Utils
{
    /**
     * @var $id int
     */
    protected $id;

    /**
     * @var $name string
     */
    protected $name;

    /**
     * @var $CompanyNum int
     */
    protected $CompanyNum;

    /**
     * @var $date DateTime
     */
    protected $date;


    protected $status;


    private $table;


    public function __construct($id = null)
    {
        $this->table = "boostapp.item_sizes";
        if ($id != null)
            $this->setData($id);
    }

    public function setData($id) {
        $data = DB::table($this->table)->where("id", "=", $id)->first();
        if ($data != null) {
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

    /**
     * @param $companyNum int
     * @return array
     */
    public function getCompanySizes($companyNum){
        $sizes = DB::table($this->table)->where("CompanyNum", "=", $companyNum)->get();
        $sizesArr = array();
        foreach ($sizes as $size){
            $mType = new ItemColor();
            foreach ($size as $key => $value){
                $mType->__set($key,$value);
            }
            array_push($sizesArr,$mType);
        }
        return $sizesArr;
    }

    /**
     * @param $companyNum
     * @return array
     */
    public function getCompanySizesIds($companyNum){
        $sizes = $this->getCompanySizes($companyNum);
        $sizesIds = array();
        foreach ($sizes as $size){
            array_push($sizesIds,$size->__get("id"));
        }
        return $sizesIds;
    }
    /**
     * @param $data array
     * @return string
     */
    public function setCompanySize($data){
        $sizeId = DB::table($this->table)->insertGetId($data);
        if($sizeId != null) {
            return $sizeId;
        }
        return "Insert Failed";
    }

    public function getDefaultSizes(){
        $company = Company::getInstance(false);
        $sizes = DB::table($this->table)->where("status","=",1)->where("CompanyNum","=", -1)
            ->orWhere("CompanyNum","=",$company->__get("CompanyNum"))->where("status","=",1)->get();
        if($sizes){
            return $this->createArrayFromObjArr($sizes);
        }
        return null;
    }
    public function getSizeById($id){
        $size = DB::table($this->table)->where("id", "=", $id)->get();
        return $this->stdClassToObj($size,"Size");
    }
}
