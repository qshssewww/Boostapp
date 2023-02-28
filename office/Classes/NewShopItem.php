<?php

require_once "Company.php";
require_once "Brand.php";
require_once "MembershipType.php";
require_once "ShopPost.php";
require_once "ItemColor.php";
require_once "Size.php";
require_once "ItemCategory.php";

class NewShopItem
{
    /**
     * @var $company Company
     */
    private $company;

    /**
     * @var $shopPost ShopPost
     */
    private $shopPost;

    public function __construct()
    {
        $this->company = Company::getInstance();
        $this->shopPost = new ShopPost();
    }

    /**
     * @param $data
     * @return string|array
     */
    public function newMembershipItem($data){

        $dataArr = array();
        $department = $data["type"];
        if($department != 1 && $department != 2 && $department != 3){
            return "Error, Wrong Input";
        }
        $dataArr["type"] = $department;
        if(isset($data["firstBlock"])) {
            $dataArr = $this->firstBlockCheck($data["firstBlock"],$dataArr);
        }
        if(isset($data["secondBlock"])) {
            $dataArr = $this->secondBlockCheck($data["secondBlock"],$dataArr);
        }
        if(isset($data["calcMembershipBlock"])){
            $this->calcMembershipBlock($data["calcMembershipBlock"],$dataArr);
        }
        if(isset($data["thirdBlock"])) {
            $dataArr = $this->thirdBlockCheck($data["thirdBlock"],$dataArr);
        }
        if(isset($data["fourthBlock"])){
            $dataArr = $this->fourthBlockCheck($data["fourthBlock"],$dataArr);
        }
        return $dataArr;
    }

    /**
     * @param $data array
     * @return string|array
     */
    public function newItem($data){
        $dataArr = array();
        $department = $data["page"];
        if($department != 4){
            return "Error, Wrong Input";
        }
        $dataArr["type"] = $department;
        if(isset($data["mainItemBlock"])) {
            $dataArr = $this->itemMainBlock($data["mainItemBlock"],$dataArr);
        }
        if(isset($data["itemDetails"])){
            $dataArr = $this->itemDetailsBlock($data["itemDetails"],$dataArr);
        }
//        if(isset($data["fourthBlock"])){
//            $dataArr = $this->fourthBlockCheck($data["fourthBlock"],$dataArr);
//        }
        return  $dataArr;
    }

    /**
     * @param $data array
     * @return array|string
     */
    public function newPaymentPage($data){
        $dataArr = array();
        $department = $data["page"];
        if($department != 5){
            return "Error, Wrong Input";
        }
        if(isset($data["paymentMainBlock"])) {
            $dataArr = $this->paymentMainBlock($data["paymentMainBlock"], $dataArr);
        }
        if(isset($data["PayItemBlock"])) {
            $dataArr = $this->PayItemBlock($data["PayItemBlock"], $dataArr);
        }
        if(isset($data["calcMembershipBlock"])){
            $dataArr = $this->calcMembershipBlock($data["calcMembershipBlock"],$dataArr);
        }
        return $dataArr;
    }

    /**
     * @param $data
     * @return array|string
     */
    public function newInsurance($data){
        $dataArr = array();
        $department = $data["type"];
        if($department != 6){
            return "Error, Wrong Input";
        }
        if(isset($data["firstBlock"])) {
            $dataArr = $this->firstBlockCheck($data["firstBlock"],$dataArr);
        }
        if(isset($data["secondBlock"])) {
            $dataArr = $this->secondBlockCheck($data["secondBlock"],$dataArr);
        }
        return $dataArr;
    }


    /**
     * @param $data array
     * @param $dataArr array
     * @return array|string
     */
    private function firstBlockCheck($data, $dataArr){
        if(isset($data["brands"]) && $data["brands"] != -1) {
            $brand = $this->checkBrand($data["brands"]);
            if(gettype($brand) == "string" ){
                return $brand;
            }
            $dataArr["brand"] = $brand;
        }
        if(isset($data["membership"])) {
            $membershipType = $this->checkMembershipType($data["membership"]);
            if(gettype($membershipType) == "string" ){
                return $membershipType;
            }
            $dataArr["membershipType"] = $membershipType;
        }
        if(is_numeric($data["itemPrice"])) {
            $dataArr["price"] = $data["itemPrice"];
        }
        else{
            return "Error, Wrong Input";
        }
        $dataArr["name"] = $data["itemName"];
        if($data["taxInclude"] == 1 || $data["taxInclude"] == 0 ){
            $dataArr["taxInclude"] = ($data["taxInclude"] == 1) ? true : false;
        }
        return $dataArr;
    }

    /**
     * @param $data
     * @param $dataArr array
     * @return string|array
     */
    private function secondBlockCheck($data, $dataArr){
        $dataArr["timePeriod"] = $this->checkPeriodTime($data["timePeriod"]);
        if(gettype($dataArr["timePeriod"]) == "string"){
            return $dataArr["timePeriod"];
        }
        if($data["renew-membership"] == 1){
            $dataArr["renew-membership"] = true;
        }
        elseif ($data["renew-membership"] == 0){
            $dataArr["renew-membership"] = false;
        }
        else{
            return "Wrong Input";
        }
        $dataArr["timePeriodRenew"] = $this->checkPeriodTime($data["timePeriodRenew"]);
        if(gettype($dataArr["timePeriodRenew"]) == "string"){
            return $dataArr["timePeriodRenew"];
        }
        return $dataArr;
    }

    /**
     * @param $data
     * @param $dataArr
     * @return array|ClassesType|string
     */
    private function thirdBlockCheck($data, $dataArr){
        $classes = array();
        foreach ($data["classes"] as $classArr){
            $classesLimits = array();
            foreach ($classArr["classes"] as $class){
                $classObj = $this->checkClassType($class);
                if(gettype($classObj) == "string" ){
                    return $classObj;
                }
                array_push($classesLimits,$classObj);
            }
            $classesLimits["limits"] = array();
            if (isset($classArr["limits"])) {
                foreach ($classArr["limits"] as $limit) {
                    $limitArr = array();
                    if ($limit["limitType"] == 1) {
                        $limitArr["Group"] = "Max";
                        $limitArr["Value"] = $limit["maxLimit"];
                        $limitArr["Item"] = $limit["maxSelect"];
                    } elseif ($limit["limitType"] == 2) {
                        $limitArr["Group"] = "Day";
                        $limitArr["Item"] = "Days";
                        $days = "";
                        foreach ($limit["daySelect"] as $day) {
                            switch ($day) {
                                case 1:
                                    $days .= "ראשון,";
                                    break;
                                case 2:
                                    $days .= "שני,";
                                    break;
                                case 3:
                                    $days .= "שלישי,";
                                    break;
                                case 4:
                                    $days .= "רביעי,";
                                    break;
                                case 5:
                                    $days .= "חמישי,";
                                    break;
                                case 6:
                                    $days .= "שישי,";
                                    break;
                                case 7:
                                    $days .= "שבת,";
                                    break;
                            }
                        }
                        $limitArr["Value"] = substr($days, 0, -1);;
                    }
                    array_push($classesLimits["limits"], $limitArr);
                }
            }
            if($classArr["freeReg"]){
                $standByArr = array(
                    "Group" => "Item",
                    "Item" => "StandBy"
                );
                $standBy = array(
                    "data" => array(
                          "StandByCount" => $classArr["regNumber"],
                          "StandByVaild_Type" => $classArr["regTime"],
                          "StandByTime" => $classArr["regBefore"],
                          "StandByTimeVaild_Type" => $classArr["freeReg"],
                          "StandByOption" => $classArr["regTimeBefore"]
                    )
                );
                $standBy = json_encode($standBy);
                $standByArr["Value"] = $standBy;
            }
            if(isset($standByArr)) {
                array_push($classesLimits["limits"], $standByArr);
            }
            array_push($classes,$classesLimits);
        }
        $dataArr["classes"] = $classes;
        return $dataArr;
    }

    /**
     * @param $data
     * @param $dataArr
     * @return string|array
     */
    private function fourthBlockCheck($data,$dataArr){
        $dataArr["appCheck"] = $data["appCheck"];
        if(isset($data["appName"])){
            $dataArr["appName"] = $data["appName"];
            if (is_numeric($data["appPrice"])) {
                $dataArr["appPrice"] = $data["appPrice"];
            } else {
                return "Error, Wrong Input";
            }
            $dataArr["appDesc"] = $data["appDesc"];
            if ($data["dealType"] == "1" || $data["dealType"] == "2") {
                $dataArr["dealType"] = $data["dealType"];
            } else {
                return "Error, Wrong Input";
            }
            if (isset($data["paymentNum"])) {
                if ($data["paymentNum"] > 1) {
                    $payment = array(
                        "paymentNum" => $data["paymentNum"],
                        "credit-payment" => ($data["credit-payment"] == 1) ? true : false,
                        "paymentFrame" => ($data["paymentFrame"] == 1) ? true : false
                    );
                    $dataArr["payment"] = $payment;
                } else {
                    $dataArr["payment"] = $data["paymentNum"];
                }
            }
            if (isset($data["limits"])) {
                $limits = array();
                foreach ($data["limits"] as $limit) {
                    if (isset($limit["selected"]) && $limit["selected"] == 1) {
                        $limits["itemLimitId"] = $limit["itemLimitId"];
                    } else if (isset($limit["selected"]) && $limit["selected"] == 2) {
                        $limits["gender"] = $limit["gender"];
                    } else if (isset($limit["selected"]) && $limit["selected"] == 3) {
                        if ($limit["startAge"] <= $limit["endAge"]) {
                            $limits["startAge"] = $limit["startAge"];
                            $limits["endAge"] = $limit["endAge"];
                        } else {
                            return "Error, Wrong Input";
                        }
                    } else if (isset($limit["selected"]) && $limit["selected"] == 4) {
                        $limits["rank"] = $limit["rank"];
                    } else if (isset($limit["selected"]) && $limit["selected"] == 5) {
                        $limits["seniority"] = $limit["seniority"];
                    } else if (isset($limit["selected"]) && $limit["selected"] == 6) {
                        if (gettype($this->checkMembershipType($limit["limitClassType"])) != "string") {
                            $limits["limitClassTypeId"] = $limit["limitClassType"];
                        }
                    } else {
                        return "Error, Wrong Input";
                    }

                }
                $dataArr["limits"] = $limits;
            }
        }
        return $dataArr;
    }

    /**
     * @param $data
     * @param $dataArr
     * @return string|array
     */
    private function itemMainBlock($data,$dataArr){
        $dataArr["itemName"] = $data["itemName"];
        if(is_numeric($data["itemPrice"])) {
            $dataArr["price"] = $data["itemPrice"];
        }
        else{
            return "Error, Wrong Input";
        }
        if(is_numeric($data["priceSale"])) {
            $dataArr["priceSale"] = $data["priceSale"];
        }
        else{
            return "Error, Wrong Input";
        }
        if($data["taxInclude"] == 1 || $data["taxInclude"] == 0 ){
            $dataArr["taxInclude"] = ($data["taxInclude"] == 1) ? true : false;
        }
        if(isset($data["category"])) {
            $itemCat = new ItemCategory();
            $categories = $itemCat->getCompanyCategoriesIds($this->company->__get("CompanyNum"));
            $catsIds = array();
            foreach ($data["category"] as $cat) {
                if(in_array($cat, $categories)){
                    array_push($catsIds,$cat);
                }
                else{
                    $catId = $itemCat->setCompanyCategory($this->company->__get("CompanyNum"),$cat);
                    array_push($catsIds,strval($catId));
                }
            }
            $dataArr["category"] = $catsIds;
        }
        return $dataArr;
    }

    /**
     * @param $data
     * @param $dataArr
     * @return mixed
     */
    private function itemDetailsBlock($data,$dataArr){
        $colorObj = new ItemColor();
        $sizesObj = new Size();
        if(isset($data["itemBarcode"])) {
            $dataArr["itemBarcode"] = $data["itemBarcode"];
        }
        if(isset($data["itemSku"])) {
            $dataArr["itemSku"] = $data["itemSku"];
        }
        if(isset($data["itemColor"])){
            $colors = $colorObj->getCompanyColorsIds($this->company->__get("CompanyNum"));
            $colorsIds = array();
            foreach ($data["itemColor"] as $color){
                if(in_array($color,$colors)){
                    array_push($colorsIds,$color);
                }
                else{
                    $colorId = $colorObj->setCompanyColor($this->company->__get("CompanyNum"),$color);
                    array_push($colorsIds,strval($colorId));
                }
            }
            $dataArr["itemColor"] = $colorsIds;
        }

        if(isset($data["itemSize"])){
            $sizes = $sizesObj->getCompanySizesIds($this->company->__get("CompanyNum"));
            $sizesIds = array();
            foreach ($data["itemSize"] as $size){
                if(in_array($size,$sizes)){
                    array_push($sizesIds,$size);
                }
                else{
                    $sizeId = $sizesObj->setCompanyColor($this->company->__get("CompanyNum"),$size);
                    array_push($sizesIds,strval($sizeId));
                }
            }
            $dataArr["itemSize"] = $sizesIds;
        }
        if(isset($data["itemStock"])){
            $dataArr["itemStock"] = $data["itemStock"];
        }
        if(isset($data["itemSupplier"])) {
            $dataArr["itemSupplier"] = $data["itemSupplier"];
        }
        return $dataArr;
    }

    /**
     * @param $data array
     * @param $dataArr array
     * @return array|string
     */
    private function paymentMainBlock($data,$dataArr){
        $dataArr["linkName"] = $data["linkName"];
        if(isset($data["brands"]) && $data["brands"] != -1) {
            $brand = $this->checkBrand($data["brands"]);
            if(gettype($brand) == "string" ){
                return $brand;
            }
            $dataArr["brand"] = $brand;
        }
        $dataArr["linkContent"] = $data["linkContent"];
        $dataArr["linkRedirect"] = $data["linkRedirect"];
        return $dataArr;
    }

    private function payItemBlock($data,$dataArr){
        if(isset($data["saleType"]) && $data["saleType"] == "1"){
            $dataArr["saleType"]  = $data["saleType"];
            if(isset($data["membershipType"])) {
                if($data["department"] == 1 || $data["department"] == 2 || $data["department"] == 3 || $data["department"] == 4) {
                    $items = $this->shopPost->getItemAndMembership($data["department"]);
                    $item = $this->checkItem($data["membershipType"], $items);
                    if(gettype($item) == "string" ){
                        return $item;
                    }
                    $dataArr["item"] = $item;
                    $dataArr["itemPrice"] = $data["membershipPrice"];
                }
                else{
                    return "Error, Wrong Input";
                }
            }
        }
        else if(isset($data["saleType"]) && $data["saleType"] == "2"){
            $dataArr["saleType"]  = $data["saleType"];
        }
        return $dataArr;
    }

    private function calcMembershipBlock($data,$dataArr){
        if($data["startCollect"] == 1){
            if($data["regularStart"] != 1 && $data["regularStart"] != 2 && $data["regularStart"] != 3){
                return "Error, Wrong Input";
            }
            $dataArr["regularStart"] = $data["regularStart"];
        }
        else if($data["startCollect"] == 2){
            $dataArr["startDate"] = $data["startDate"];
            $dataArr["endDate"] = $data["endDate"];
            if($data["latePurchase"] == 1){
                $dataArr["untilDate"] = $data["untilDate"];
                if($data["priceCut"] == 1){
                    $dataArr["priceCut"] = $data["priceCut"];
                    if($data["priceCutDDl"] == 1){
                        $dataArr["priceCutDDl"] = "days";
                    }
                    elseif($data["priceCutDDl"] == 2){
                        $dataArr["priceCutDDl"] = "classes";
                    }
                }

            }
        }
        return $dataArr;
    }

    /**
     * @param $itemId
     * @param $items
     * @return mixed|string
     */
    private function checkItem($itemId,$items){
        $itemObj = "";
        foreach ($items as $item){
            if($itemId == $item->id){
                $itemObj = $item;
                break;
            }
        }
        if($itemObj == ""){
            return "Error, Wrong Input";
        }
        return $itemObj;
    }

    /**
     * @param $brand_id
     * @return Brand|string
     */
    private function checkBrand($brand_id){
        $brand = new Brand();
        foreach ($this->company->getBrands() as $companyBrand){
            if($brand_id == $companyBrand->__get("id")){
                $brand = $companyBrand;
                break;
            }
        }
        if($brand->__get("id") == null || $brand->__get("id") == ""){
            return "Error, Wrong Input";
        }
        return $brand;
    }

    /**
     * @param $membership_id
     * @return MembershipType|string
     */
    private function checkMembershipType($membership_id){
        $membership = new MembershipType();
        foreach ($this->company->getMembershipTypes() as $membershipType){
            if($membership_id == $membershipType->__get("id")){
                $membership = $membershipType;
            }
        }
        if($membership->__get("id") == null || $membership->__get("id") == ""){
            return "Error, Wrong Input";
        }
        return $membership;
    }

    /**
     * @param $class_id
     * @return ClassesType|string
     */
    private function checkClassType($class_id){
        $classType = new ClassesType();
        foreach ($this->company->getClassTypes() as $class){
            if($class_id == $class->__get("id")){
                $classType = $class;
            }
        }
        if($classType->__get("id") == null || $classType->__get("id") == ""){
            return "Error, Wrong Input";
        }
        return $classType;
    }

    /**
     * @param $period
     * @return array|string
     */
    private function checkPeriodTime($period){
        $arr = explode(" ",$period);
        if(count($arr) == 2){
            if(is_numeric($arr[0]) && ($arr[1] == "days" || $arr[1] == "months" || $arr[1] == "weeks" || $arr[1] == "years")){
                return $arr;
            }
            else{
                return "Error: Wrong Input";
            }
        }
        return "Error: Wrong Input";
    }
}