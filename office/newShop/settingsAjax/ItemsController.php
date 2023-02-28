<?php
require_once "../Classes/CompanyProductSettings.php";
require_once "../Classes/Brand.php";
require_once "../Classes/MembershipType.php";

class ItemsController{

    public function getItems($companyNum,$coupons = false){
        if($coupons == false) {
            return DB::table("boostapp.items")->select("id", "ItemName")->where("CompanyNum", "=", $companyNum)->where("Status", "=", "0")->get();
        }
        else{
            return DB::table("boostapp.items")->select("id", "ItemName")->where("CompanyNum", "=", $companyNum)->where("isPaymentForSingleClass",0)
                ->where("Status", "=", "0")->get();
        }

    }
    public function deleteOrMoveCategory($rawData){
        $data = (object) $rawData;
        $res=[];
        if($data && isset($data->id)){
            $item_cat = DB::table("boostapp.item_cat")->where("id", $data->id)->first();
            if  ($item_cat->CompanyNum != Company::getInstance()->CompanyNum) {
                echo "error";
                return;
            }
            if(isset($data->otherId)){
                if ($data->otherId == $data->id) {
                    echo "error";
                    return;
                }
                $item_cat = DB::table("boostapp.item_cat")->where("id", $data->otherId)->first();
                if ($item_cat) {
                    $res[] = DB::table("boostapp.items")->where('ItemCat','=',$data->id)->update(array(
                        "ItemCat" => $data->otherId
                    ));
                }
            } else {
                $res[] = DB::table("boostapp.items")->where('ItemCat','=',$data->id)->update(array( "Status" => 1 ));
            }
            $res[] = DB::table("boostapp.item_cat")->where("id","=",$data->id)->delete();
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        } else {
            echo "error";
        }
    }
    public function deleteOrMoveMembershipType($rawData){
        $data = (object) $rawData;
        $res=[];
        if (!isset($data->id)) {
            echo json_encode(array("Message" => "id is required", "Status" => "Error"));
        } elseif (!is_numeric($data->id) || $data->id < 0) {
            echo json_encode(array("Message" => "id must be a number greater than 0"));
        } elseif (isset($data->otherId) && (!is_numeric($data->otherId) || $data->otherId <= 0)) {
            echo json_encode(array("Message" => "otherId must be a number greater than 0"));
        } else {
            $membership = DB::table("boostapp.membership_type")->where("id", $data->id)->first();
            if ($membership->CompanyNum != Company::getInstance()->CompanyNum || $membership->mainMembership == 1) {
                echo "error";
                return;
            }
            if(isset($data->otherId)){
                $membership = DB::table("boostapp.membership_type")->where("id", $data->otherId)->first();
                if ($membership->CompanyNum != Company::getInstance()->CompanyNum) {
                    echo "error";
                    return;
                }
                $res[] = DB::table("boostapp.items")->where('MemberShip','=',$data->id)->update(array(
                    "MemberShip" => $data->otherId
                ));
                $res[] = DB::table("boostapp.membership_type")->where('mainMembership',"=","0")->where("id","=",$data->id)->update(array("Status" => 1));
            }else{
                $res[] = DB::table("boostapp.items")->where('MemberShip','=',$data->id)->update(array("Status" => 1));
                $res[] = DB::table("boostapp.membership_type")->where('mainMembership',"=","0")->where("id","=",$data->id)->update(array("Status" => 1));
            }
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        }
    }
    public function disableCategory($rawData){
        $data = (object) $rawData;
        if($data && isset($data->id) && isset($data->disabled)){
            $item_cat = DB::table("boostapp.item_cat")->where("id", "=", $data->id)->first();
            if ($item_cat->CompanyNum != Company::getInstance()->CompanyNum) {
                echo "error";
            }
            $res = DB::table("boostapp.item_cat")->where("id", "=", $data->id)->update(array(
                "disabled"=>$data->disabled
            ));
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        }else{
            echo "error";
        }

    }
    public function disableMembershipType($rawData){
        $data = (object) $rawData;
        if($data && isset($data->id) && isset($data->disabled)){
            $membership_type = DB::table("boostapp.membership_type")
                ->where("id", $data->id)->first();
            if ($membership_type->CompanyNum != Company::getInstance()->CompanyNum || $membership_type->mainMembership == 1) {
                echo "error";
                return;
            }
            $res = DB::table("boostapp.membership_type")->where("id", "=", $data->id)->update(array(
                "disabled"=>$data->disabled
            ));
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        }else{
            echo "error";
        }
    }
    public function getCategoriesAndAmounts($rawData){
        $data = (object) $rawData;
        $res = DB::select( DB::raw("SELECT item_cat.* , (SELECT COUNT(*) FROM items WHERE items.ItemCat = item_cat.id And items.Status = 0) AS countOfItems FROM item_cat where item_cat.CompanyNum=:cn order by `order` asc"), ["cn" => Company::getInstance()->CompanyNum] );
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }
    public function getItemsForSelectedCategory($rawData){
        $data = (object) $rawData;
        if($data && isset($data->id)){
            $res = DB::table('boostapp.items')
                ->where("CompanyNum", Company::getInstance()->CompanyNum)
                ->where('ItemCat',"=",$data->id)
                ->where('Status',"=", 0)
                ->where('Display', '=', 1)
                ->orderBy('order')->get();

            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        }else{
            echo "error";
        }
    }
    public function getItemsForSelectedMembership($rawData){
        $data = (object) $rawData;
        if($data && isset($data->id)){
            $res = DB::table('boostapp.items')->where('MemberShip',"=",$data->id)->where('Status',"=","0")->where("CompanyNum", Company::getInstance()->CompanyNum)->orderBy('order')->get();
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        }else{
            echo "error";
        }
    }
    public function getMembershipTypeAndAmounts($rawData){
        $data = (object) $rawData;
        $companyProductSettings = new CompanyProductSettings();
        $CategorySettings = $companyProductSettings->getSingleByCompanyNum(Company::getInstance()->CompanyNum);
        if($CategorySettings && $CategorySettings->manageMemberships=="1"){
            $res = DB::select( DB::raw("SELECT membership_type.* , (SELECT COUNT(*) FROM items WHERE items.MemberShip = membership_type.id and items.isPaymentForSingleClass = 0 and Department in (1, 2, 3) and Status = 0) AS countOfItems FROM membership_type where membership_type.CompanyNum=:cn and membership_type.mainMembership = 0 AND Status = 0 order by `order` asc"), ["cn" => Company::getInstance()->CompanyNum] );
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        }else{
            $res = DB::select( DB::raw("SELECT membership_type.* , (SELECT COUNT(*) FROM items WHERE items.MemberShip = membership_type.id and items.isPaymentForSingleClass = 0 and Department in (1, 2, 3) and Status = 0) AS countOfItems FROM membership_type where membership_type.mainMembership=1 and membership_type.CompanyNum=:cn AND Status = 0 order by `order` asc"), ["cn" => Company::getInstance()->CompanyNum] );
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        }
    }
    public function getCompanyMemberships($companyNum){
        $items = DB::table("boostapp.items")
            ->where("CompanyNum","=",$companyNum)
            ->whereIn("Department",[1,2,3])
            ->where("Status","=",0)
            ->where("Display",1)
            ->where('isPaymentForSingleClass', '=', 0)
            ->get();
        return $items;
    }

    public function getSingleCompanySettings($rawData){
        $data = (object) $rawData;
        $companyProductSettings = new CompanyProductSettings();
        $res = $companyProductSettings->getSingleByCompanyNum(Company::getInstance()->CompanyNum);
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }
    public function insertNewCategory($rawData){
        $data = (object) $rawData;
        if($data && isset($data->name)){
            $catHighestOrder = DB::table("boostapp.item_cat")->where("CompanyNum", Company::getInstance()->CompanyNum)->orderBy("order", "DESC")->first()->order;
            $res = DB::table("boostapp.item_cat")->insertGetId(array(
                'CompanyNum'=>Company::getInstance()->CompanyNum,
                "Name"=>$data->name,
                "userId"=>Auth::user()->id,
                "order"=>$catHighestOrder + 1
            ));
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        }else{
            echo "error";
        }
    }
    public function insertNewMembershipType($rawData){
        $companyProductSettings = new CompanyProductSettings();
        $CategorySettings = $companyProductSettings->getSingleByCompanyNum(Company::getInstance()->CompanyNum);
        if ($CategorySettings && $CategorySettings->manageMemberships != 1) {
            echo "error";
            return;
        }
        $data = (object) $rawData;
        if($data && isset($data->name)){
            $res = DB::table("boostapp.membership_type")->insertGetId(array(
                'CompanyNum'=>Company::getInstance()->CompanyNum,
                "Type"=>$data->name
            ));
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        }else{
            echo "error";
        }
    }
    public function renameMembershipCategory($rawData){
        $data = (object) $rawData;
        if($data && isset($data->id) && isset($data->name)){
            $membership_type = DB::table("boostapp.membership_type")->where("id", $data->id)->first();
            if ($membership_type->CompanyNum != Company::getInstance()->CompanyNum || $membership_type->mainMembership == 1) {
                echo "error";
                return;
            }
            $res = DB::table("boostapp.membership_type")->where("id", "=", $data->id)->where('mainMembership',"=","0")->update(array(
                "Type"=>$data->name
            ));
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        }else{
            echo "error";
        }
    }
    public function renameProductCategory($rawData){
        $data = (object) $rawData;
        if($data && isset($data->id) && isset($data->name)){
            $item_cat = DB::table("boostapp.item_cat")->where("id", $data->id)->first();
            if ($item_cat->CompanyNum != Company::getInstance()->CompanyNum) {
                echo "error";
                return;
            }
            $res = DB::table("boostapp.item_cat")->where("id", "=", $data->id)->update(array(
                "Name"=>$data->name
            ));
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        }else{
            echo "error";
        }
    }
    public function toggleManageMemberships($rawData){
        $data = (object) $rawData;
        if (empty($data->id)) {
            echo json_encode(array("Message" => "id is required", "Status" => "Error"));
        } elseif (isset($data->toggle) && $data->toggle != 0 && $data->toggle != 1) {
            echo json_encode(array("Message" => "toggle is required and must be 0 or 1", "Status" => "Error"));
        } else {
            $companyProductSettings = new CompanyProductSettings();
            $thisCompany=$companyProductSettings->getSingleById($data->id);
            if ($thisCompany->CompanyNum != Company::getInstance()->CompanyNum) {
                echo "error";
                return;
            }
            $test = Company::getInstance();
            $allItemsWithMemership=DB::table('boostapp.items')->where('MemberShip','!=','null')->where('Department',"!=",4)->where('CompanyNum','=',Company::getInstance()->CompanyNum)->get();
            $defaultMembership=DB::table('boostapp.membership_type')->where('CompanyNum','=',Company::getInstance()->CompanyNum)->where('mainMembership',"=","1")->first();
            if (!$defaultMembership) {
                $defaultMembership = MembershipType::createDefaultMembership();
            }
            foreach($allItemsWithMemership as $memItem){
                if($data->toggle=="1"){
                    if($memItem->oldMemberShip){
                        DB::table('boostapp.items')->where('id',"=",$memItem->id)->update(array('MemberShip'=>$memItem->oldMemberShip));
                    }
                }else{
                    DB::table('boostapp.items')->where('id',"=",$memItem->id)->update(array('oldMemberShip'=>$memItem->MemberShip,'MemberShip'=>$defaultMembership->id));
                }
            }
            $res = $companyProductSettings->updateById($data->id,array(
                "manageMemberships"=>$data->toggle
            ));
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        }
    }
    public function toggleOffsetSetting($rawData){
        $data = (object) $rawData;
        if($data && isset($data->offset)){
            $companyProductSettings = new CompanyProductSettings();
            $id = $companyProductSettings->getSingleByCompanyNum(Company::getInstance()->CompanyNum)->id;
            $company = $companyProductSettings->getSingleById($id);
            if ($company->CompanyNum != Company::getInstance()->CompanyNum) {
                echo "error";
                return;
            }
            $res = $companyProductSettings->updateById($data->id,array(
             "offsetMemberships"=>$data->offset
            ));
            return json_encode($res, JSON_UNESCAPED_UNICODE);
        }else{
            echo "error";
        }
    }

    public function getCompanyBranches($rawData){
        try {
            $data = (object) $rawData;
            $branchObj = new Brand();
            $branches = $branchObj->getAllByCompanyNum(Company::getInstance()->CompanyNum);
            if($branches){
                return $branches;
            }
            else{
                return [];
            }
        }
        catch (Exception $e){
            return $e->getMessage();
        }

    }

    public function toggleSpreadPayment($rawData){
        try{
            $data = (object) $rawData;
            if($data && isset($data->payment)) {
                $companyProductSettings = new CompanyProductSettings();
                $id = $companyProductSettings->getSingleByCompanyNum(Company::getInstance()->CompanyNum)->id;
                $res = $companyProductSettings->updateById($id, ["spreadPayments" => $data->payment]);
                return $res;
            }
            else{
                return "Wrong Data";
            }
        }
        catch (Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * @param $rawData
     * @return string
     */
    public function toggleBitPayments($rawData)
    {
        try {
            $data = (object)$rawData;
            if ($data && isset($data->payment)) {
                $companyProductSettings = new CompanyProductSettings();
                $id = $companyProductSettings->getSingleByCompanyNum(Company::getInstance()->CompanyNum)->id;
                $res = $companyProductSettings->updateById($id, ["bitPayments" => $data->payment]);
                return $res;
            } else {
                return "Wrong Data";
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
