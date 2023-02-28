<?php

require_once 'Utils.php';

class ItemColor extends Utils
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
     * @var $hex string
     */
    protected $hex;

    /**
     * @var $CompanyNum int
     */
    protected $CompanyNum;

    /**
     * @var $date DateTime
     */
    protected $date;


    private $table;

    public function __construct()
    {
        $this->table = "boostapp.item_colors";
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

    /**
     * @param $companyNum int
     * @return array
     */
    public function getCompanyColors($companyNum){
        $colors = DB::table("item_colors")->where("CompanyNum", "=", $companyNum)->get();
        $colorsArr = array();
        foreach ($colors as $color){
            $mType = new ItemColor();
            foreach ($color as $key => $value){
                $mType->__set($key,$value);
            }
            array_push($colorsArr,$mType);
        }
        return $colorsArr;
    }

    /**
     * @param $companyNum
     * @return array
     */
    public function getCompanyColorsIds($companyNum){
        $colors = $this->getCompanyColors($companyNum);
        $colorsIds = array();
        foreach ($colors as $color){
            array_push($colorsIds,$color->__get("id"));
        }
        return $colorsIds;
    }
    /**
     * @param $companyNum
     * @param $color
     * @return string
     */
    public function setCompanyColor($companyNum,$color){
        $colorId = DB::table("item_colors")->insertGetId(
            array(
                "name" => $color,
                "CompanyNum" => $companyNum
            )
        );
        if($colorId != null) {
            return $colorId;
        }
        return "Insert Failed";
    }

    public function getDefaultColors(){
        $colors = DB::table($this->table)->where("CompanyNum","=", -1)->get();
        if($colors){
            return $this->createArrayFromObjArr($colors);
        }
        return null;
    }

    public function getColorById($id){
        $color = DB::table($this->table)->where("id", "=", $id)->get();
        return $this->stdClassToObj($color,"ItemColor");
    }
}
