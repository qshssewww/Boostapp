<?php

require_once "ItemColor.php";
require_once "Company.php";
require_once "Size.php";
require_once "ItemSupplier.php";
require_once "Utils.php";

class ItemDetails extends Utils
{
    public const STATUS_ACTIVE = 1;
    public const STATUS_OFF = 0;

    /**
     * @var $id int
     */
    protected $id;

    /**
     * @var $itemId int
     */
    protected $itemId;

    /**
     * @var $CompanyNum int
     */
    protected $CompanyNum;

    /**
     * @var $barcode string
     */
    protected $barcode;

    /**
     * @var $sku string
     */
    protected $sku;

    /**
     * @var $inventory int
     */
    protected $inventory;

    /**
     * @var $used int
     */
    protected $used;


    /**
     * @var $suppliers ItemSupplier
     */
    protected $suppliers;

    /**
     * @var $colors ItemColor
     */
    protected $colors;

    /**
     * @var $status int
     */
    protected $status;
    /**
     * @var $sizes Size
     */
    protected $sizes;

    /**
     * @var $date DateTime
     */
    protected $date;

    public $table;

    public function __construct($id = null)
    {
        $this->table = "boostapp.item_details";
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

    public function getItemDetailsAsArr($itemId){
        $item = $this->getItemDetails($itemId);
        $arr = $this->createArrayFromObjArr($item);
        return $arr;
    }
    public function getItemDetails($itemId){
        $company = Company::getInstance();
        $details = DB::table("item_details")->where("ItemId", "=", $itemId)->where("status","=",1)->where("CompanyNum", "=", $company->__get("CompanyNum"))->get();
        $detArr = array();
        foreach ($details as $itemDet) {
            $detObj = new self();
            foreach ($itemDet as $key => $det){
                if($key == "colors" && $det != null){
                    $itemColor = new ItemColor();
                    $detObj->__set($key, $itemColor->getColorById($det));
                }
                else if($key == "sizes" && $det != null){
                    $itemSize = new Size();
                    $detObj->__set($key, $itemSize->getSizeById($det));
                }
                else if($key == "suppliers" && $det != null){
                    $itemSup = new ItemSupplier();
                    $detObj->__set($key, $itemSup->getSingleSupplier($det));
                }
                else {
                    $detObj->__set($key, $det);
                }
            }
            array_push($detArr,$detObj);
//            if ($key == "colors" && $value != null) {
//                $colorsArr = json_decode($value);
//                $colors = DB::table("item_colors")->whereIn("id", $colorsArr)->get();
//                $ItemColor = new ItemColor();
//                foreach ($colors as $color) {
//                    foreach ($color as $key => $value) {
//                        $ItemColor->__set($key, $value);
//                    }
//                    array_push($this->colors, $ItemColor);
//                }
//            }
//            elseif ($key == "sizes" && $value != null) {
//                $sizesArr = json_decode($value);
//                $sizes = DB::table("item_sizes")->whereIn("id", $sizesArr)->get();
//                $ItemSize = new Size();
//                foreach ($sizes as $size) {
//                    foreach ($size as $key => $value) {
//                        $ItemSize->__set($key, $value);
//                    }
//                    array_push($this->sizes, $ItemSize);
//                }
//            }
//            if ($key == "suppliers" && $value != null) {
//                $supArr = json_decode($value);
//                $suppliers = DB::table("items_suppliers")->whereIn("id", $supArr)->get();
//                $itemSup = new ItemSupplier();
//                foreach ($suppliers as $sup) {
//                    foreach ($sup as $key => $value) {
//                        $itemSup->__set($key, $value);
//                    }
//                    array_push($this->suppliers, $itemSup);
//                }
//            }
//            $this->__set($key, $value);
        }
        return $detArr;
    }

    public function isInStock(){
        if ($this->inventory <= $this->used)
            return false;
        return true;
    }

    public function getItemDetailsById($itemDetailsId){
        $itemDetail = DB::table($this->table)->where('id', $itemDetailsId)->first();
        $resObj = new stdClass();
        foreach ($itemDetail as $key => $value){
            if($key == "colors" && $value != null){
                $itemColor = new ItemColor();
                $resObj->$key = $itemColor->getColorById($value);
            }
            else if($key == "sizes" && $value != null){
                $itemSize = new Size();
                $resObj->$key = $itemSize->getSizeById($value);
            }
            else if($key == "suppliers" && $value != null){
                $itemSup = new ItemSupplier();
                $resObj->$key = $itemSup->getSingleSupplier($value);
            }
            else {
                $resObj->$key = $value;
            }
        }
        return $resObj;
    }

    //Return itemDetails objects sorted by size id
    public function getItemDetailsSorted($itemId){
        $items = $this->getItemDetails($itemId);
        $res = array();
        foreach ($items as $item){
            if ($item->isInStock()){
                $res[$item->__get('sizes')->id ?? 0][] = $item;
            }
        }
        return $res;
    }
    /**
     * @param $data
     * @param $itemId int
     * @return mixed
     */
    public function insertItemDetails($data,$itemId){
        $itemSupplier = new ItemSupplier();
        $sizeObj = new Size();
        $company = Company::getInstance();
        $CompanyNum = $company->__get("CompanyNum");
        $res = array();
        $supId = 0;
        $res['newSize'] = [];
        if(isset($data["extraOptions"]["extra"][0]["supplier"]) && is_array($data["extraOptions"]["extra"][0]["supplier"])){
            if($data["extraOptions"]["extra"][0]["supplier"]["id"] == 0){
                $supData = array(
                    "CompanyNum" => $CompanyNum,
                    "name" => $data["extraOptions"]["extra"][0]["supplier"]["name"]
                );
                $supId = $itemSupplier->insertSupplier($supData);
                if($supId == "Insert Failed"){
                    echo "error inserting supplier";
                }
            }
            else{
                $supId = $data["extraOptions"]["extra"][0]["supplier"]["id"];
            }
        }
        if(isset($data["extraOptions"]["extra"])) {
            foreach ($data["extraOptions"]["extra"] as $extra){
                if(isset($extra["size"]["id"])) {
                    if ($extra["size"]["id"] == 0) {
                        $sizeArr = array(
                            "CompanyNum" => $CompanyNum,
                            "name" => $extra["size"]["name"]
                        );
                        $sizeId = $sizeObj->setCompanySize($sizeArr);
                        if($sizeId == "Insert Failed"){
                            echo "error inserting size";
                        }
                        $res['newSize'][] = ["id" => $sizeId, "name" => $sizeArr["name"]];
                    } else {
                        $sizeId = $extra["size"]["id"];
                    }
                }

                $fsf = array(
                    "itemId" => $itemId,
                    "CompanyNum" =>$company->__get("CompanyNum"),
                    "barcode" => (isset($extra["barcode"])) ? $extra["barcode"] : null,
                    "sku" => (isset($extra["sku"])) ? $extra["sku"] : null,
                    "inventory" => (isset($extra["stock"])) ? $extra["stock"] : null,
                    "colors" => (isset($extra["color"])) ? ($extra["color"] > 0 ? $extra["color"] : null) : null,
                    "sizes" => (isset($sizeId)) ? $sizeId : null,
                    "suppliers" => (isset($supId)) ? $supId : null
                );

                $res["id"] = DB::table('item_details')->insertGetId(
                    array(
                        "itemId" => $itemId,
                        "CompanyNum" =>$company->__get("CompanyNum"),
                        "barcode" => (isset($extra["barcode"])) ? $extra["barcode"] : null,
                        "sku" => (isset($extra["sku"])) ? $extra["sku"] : null,
                        "inventory" => (isset($extra["stock"])) ? $extra["stock"] : null,
                        "colors" => (isset($extra["color"])) ? ($extra["color"] > 0 ? $extra["color"] : null) : null,
                        "sizes" => (isset($sizeId)) ? $sizeId : null,
                        "suppliers" => (isset($supId)) ? $supId : null
                    )
                );
            }
        }
        return $res;
    }
    public function updateDetail($data,$id){
        return DB::table("item_details")->where("id","=", $id)->update($data);
    }

    public function updateItemDetails($data){
        $itemSupplier = new ItemSupplier();
        $company = Company::getInstance(false);
        $CompanyNum = $company->__get("CompanyNum");
        $sizeObj = new Size();
        $supId = 0;
        $res = [];
        if(isset($data["extraOptions"]["extra"][0]["supplier"]) && is_array($data["extraOptions"]["extra"][0]["supplier"])){
            if($data["extraOptions"]["extra"][0]["supplier"]["id"] == 0){
                $supData = array(
                    "CompanyNum" => $CompanyNum,
                    "name" => $data["extraOptions"]["extra"][0]["supplier"]["name"]
                );
                $supId = $itemSupplier->insertSupplier($supData);
                if($supId == "Insert Failed"){
                    echo "error inserting supplier";
                }
                $res["newSupplier"] = ["id" => $supId, "name" => $supData["name"]];
            } elseif ($data["extraOptions"]["extra"][0]["supplier"]["id"] > 0) {
                $supId = $data["extraOptions"]["extra"][0]["supplier"]["id"];
            }
        }
        $res['newSize'] = [];
        if (isset($data["DelInventory"])) {
            foreach ($data["DelInventory"] as $DelId){
                $delArr = array(
                    "status" => 0
                );
                $this->updateDetail($delArr,$DelId);
            }
        }
        
        foreach ($data["extraOptions"]["extra"] as $extra) {
            if(isset($extra["size"]["id"])) {
                if ($extra["size"]["id"] == 0 && !empty($extra["size"]["name"])) {
                    $sizeArr = array(
                        "CompanyNum" => $CompanyNum,
                        "name" => $extra["size"]["name"]
                    );
                    $sizeId = $sizeObj->setCompanySize($sizeArr);
                    if($sizeId == "Insert Failed"){
                        echo "error inserting size";
                    }
                    $res['newSize'][] = ["id" => $sizeId, "name" => $sizeArr["name"]];
                } else {
                    $sizeId = $extra["size"]["id"];
                }
            }
            if(!isset($extra["id"]) || $extra["id"] <= 0) {
                $color = isset($extra["color"]) && $extra["color"] > 0 ? $extra["color"] : null;
                $size = isset($sizeId) && $sizeId > 0 ? $sizeId : null;
                $itemDetails = $this->isDetailsExist($color, $size, $data["id"]);
                if($itemDetails) {
                    $details = array(
                        "inventory" => $itemDetails->inventory + $extra["stock"],
                        "suppliers" => $supId
                    );
                    if (isset($extra["sku"])) {
                        $details["sku"] = $extra["sku"];
                    }
                    if (isset($extra["barcode"])) {
                        $details["barcode"] = $extra["barcode"];
                    }
                    $res[] = $this->updateDetail($details, $itemDetails->id);
                }
                else {
                    $res["id"] = DB::table('item_details')->insertGetId(
                        array(
                            "itemId" => $data["id"],
                            "CompanyNum" =>$company->__get("CompanyNum"),
                            "barcode" => $extra["barcode"] ?? null,
                            "sku" => $extra["sku"] ?? null,
                            "inventory" => $extra["stock"] ?? null,
                            "colors" => (isset($extra["color"])) ? ($extra["color"] > 0 ? $extra["color"] : null) : null,
                            "sizes" => $sizeId ?? null,
                            "suppliers" => $supId ?? null
                        )
                    );
                }
            } else {
                $itemDetailsObj = new self($extra["id"]);
                $used = $itemDetailsObj->__get('used') ?? 0;
                $details = array(
                    "itemId" => $data["id"],
                    "CompanyNum" =>$company->__get("CompanyNum"),
                    "inventory" => $extra["stock"] + $used,
                    "colors" => $extra["color"] > 0 ? $extra["color"] : null,
                    "sizes" => $sizeId ?? null,
                    "suppliers" => $supId
                );
                if (isset($extra["sku"])) {
                    $details["sku"] = $extra["sku"];
                }
                if (isset($extra["barcode"])) {
                    $details["barcode"] = $extra["barcode"];
                }
                $res[] = $this->updateDetail($details,$extra["id"]);
            }
        }
        return $res;
    }
    public function deleteDetails($id){
        DB::table('item_details')->where('itemId', '=', $id)->delete();
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
    public function isDetailsExist($color = null, $size = null, $itemId) {
        $company = Company::getInstance(false);
        $CompanyNum = $company->__get("CompanyNum");
        if($color && $size) {
            $details = DB::table('item_details')->where('CompanyNum', '=', $CompanyNum)->where('itemId', $itemId)->where('status', '=', 1)->where('colors', '=', $color)->where('sizes', '=', $size)->first();
        } else if($color == null && $size == null) {
            $details = DB::table('item_details')->where('CompanyNum', '=', $CompanyNum)->where('itemId', $itemId)->where('status', '=', 1)->whereNull('colors')
            ->where(function($q) {
                $q->whereNull('sizes')->orWhere('sizes', '=', '-1');
            })->first();
        } else if($color == null && $size) {
            $details = DB::table('item_details')->where('CompanyNum', '=', $CompanyNum)->where('itemId', $itemId)->where('status', '=', 1)->whereNull('colors')->where('sizes', '=', $size)->first();
        } else if($color && $size == null) {
            $details = DB::table('item_details')->where('CompanyNum', '=', $CompanyNum)->where('itemId', $itemId)->where('status', '=', 1)->where('colors', '=', $color)
            ->where(function($q) {
                $q->whereNull('sizes')->orWhere('sizes', '=', '-1');
            })->first();
        } else {
            return null;
        }
        
        return $details;
    }

    //Update use count for item details, return item_details id, if not exist return 0
    public function useItemDetails(){
        if (!$this->id)
            return 0;
        $this->used += 1;
        $this->updateDetail(['used' => $this->used], $this->id);
        return $this->id;
    }

    /**
     * @param int $quantity
     * @return int - 1 successes 0 not
     */
    public function increaseUsed(int $quantity = 1): int
    {
        $this->__set('used', $this->used + $quantity);
        return DB::table($this->table)->where('id', $this->id)->update(['used' => $this->used]);
    }

    /**
     * @param int $quantity
     * @return int - 1 successes 0 not
     */
    public function decreaseUsed(int $quantity = 1): int
    {
        $this->__set('used', $this->used - $quantity);
        return DB::table($this->table)->where('id', $this->id)->update(['used' => $this->used]);
    }

}
