<?php


class ItemCategory
{
    /**
     * @var $id int
     */
    private $id;

    /**
     * @var $Name string
     */
    private $Name;

    /**
     * @var $CompanyNum int
     */
    private $CompanyNum;

    /**
     * @var $userId int
     */
    private $userId;

    /**
     * @var $insertDate DateTime
     */
    private $insertDate;

    /**
     * @var $updateDate DateTime
     */
    private $updateDate;
    /**
     * @var $Favorite int
     */
    private $Favorite;

    private $table;

    public function __construct() {
        $this->table = "boostapp.item_cat";
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

    /**
     * @var $companyNum
     * @return ItemCategory[]
     */
    public function getCompanyItemsCategories($companyNum){
        $categories = array();
        $ItemCat = DB::table($this->table)->where('CompanyNum', '=', $companyNum)->where("disabled","=",0)->get();
        foreach ($ItemCat as $cat){
            $category = new ItemCategory();
            foreach ($cat as $key => $value){
                $category->__set($key,$value);
            }
            array_push($categories,$category);
        }
        return $categories;
    }

    /**
     * @param $companyNum
     * @return array
     */
    public function getCompanyCategoriesIds($companyNum){
        $categories = $this->getCompanyItemsCategories($companyNum);
        $catIds = array();
        foreach ($categories as $cat){
            array_push($catIds, $cat->__get("id"));
        }
        return $catIds;
    }

    public function getSetMainCategory($companyNum) {
        $mainCat = DB::table($this->table)
            ->where("CompanyNum", $companyNum)
            ->where("mainCategory", 1)
            ->first();
        
        if (!$mainCat) {
            $id = DB::table($this->table)
                ->insertGetId([
                    "Name" => "כללי",
                    "CompanyNum" => $companyNum,
                    "mainCategory" => 1
                ]);
            $mainCat = DB::table($this->table)->where("id", $id)->first();
        }
        return $mainCat;
    }

    public function setCompanyCategory($companyNum,$cat){
        $catHighestOrder = DB::table($this->table)->where("CompanyNum", $companyNum)->orderBy("order", "DESC")->first()->order;
        $catId = DB::table($this->table)->insertGetId(
            array(
                "name" => $cat,
                "CompanyNum" => $companyNum,
                "userId" => Auth::user()->id,
                "order" => $catHighestOrder + 1
            )
        );
        if($catId != null) {
            return $catId;
        }
        return "Insert Failed";
    }

    /**
     * @param int $id
     * @param int $status
     * @return int
     */
    public function changeFavorite(int $id, int $status): int
    {
        return DB::table($this->table)
            ->where('id', '=', $id)
            ->update(['Favorite' => $status]);
    }

}
