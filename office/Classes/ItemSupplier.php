<?php

require_once 'Utils.php';

class ItemSupplier extends Utils
{
    /**
     * @var $id int
     */
    protected $id;

    /**
     * @var $CompanyNum int
     */
    protected $CompanyNum;

    /**
     * @var $name string
     */
    protected $name;

    /**
     * @var $status int
     */
    protected $status;

    /**
     * @var $date DateTime
     */
    protected $date;

    private $table;

    public function __construct()
    {
        $this->table = "boostapp.items_suppliers";
    }

    /**
     * @param $name
     * @param $value
     */
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

    public function getCompanySuppliers($companyNum){
        $suppliers = DB::table($this->table)->where("CompanyNum", "=", $companyNum)->where("status","=","1")->get();
        return $this->createArrayFromObjArr($suppliers);
    }

    public function getSingleSupplier($id){
        $supplier = DB::table($this->table)->where("id", "=", $id)->where("status","=","1")->get();
        if($supplier){
            return $this->stdClassToObj($supplier,"ItemSupplier");
        }
        return "supplier not available";
    }
    public function insertSupplier($data){
        $supId = DB::table($this->table)->insertGetId($data);
        if($supId != 0) {
            return $supId;
        }
        return "Insert Failed";
    }

    public function deleteSupplier($supId){
        DB::table($this->table)->where("id","=",$supId)->update(array("status" => 0));
    }
    public function updateSupplier($supId,$data){
        DB::table($this->table)->where("id","=",$supId)->update($data);
    }
}
