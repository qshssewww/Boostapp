<?php
class OrderController{
    private function createStringFromObjArr($arr){
        $newArr=[];
        foreach($arr as $idAndOrder){
            $newArr[]='('.DB::getPdo()->quote($idAndOrder["id"]).','.DB::getPdo()->quote($idAndOrder["order"]).')';
        }
        $string= join(',',$newArr);
        return $string;
    }

    private function checkIds($arr, $table) {
        $ids = [];

        foreach ($arr as $idAndOrder) {
            array_push($ids, $idAndOrder["id"]);
        }

        $rows = DB::table($table)
            ->whereIn("id", $ids)
            ->get();

        foreach ($rows as $row) {
            if ($row->CompanyNum != Company::getInstance()->CompanyNum) {
                return false;
            }
        }
        return true;
    }

    public function reorderItems($rawData){
        $data = (object) $rawData;
        if($data && isset($data->orderArr)){
            if (!$this->checkIds($data->orderArr, "boostapp.items")) {
                echo "error";
                return;
            }
            $string = $this->createStringFromObjArr($data->orderArr);
            $res = DB::insert("INSERT INTO boostapp.items (id,`order`) VALUES ".$string." ON DUPLICATE KEY UPDATE `order`= VALUES(`order`)");
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        }else{
            echo "error";
        }
    }
    public function reorderCategories($rawData){
        $data = (object) $rawData;
        if($data && isset($data->orderArr)){
            if (!$this->checkIds($data->orderArr, "boostapp.item_cat")) {
                echo "error";
                return;
            }
            $string = $this->createStringFromObjArr($data->orderArr);
            $res = DB::insert("INSERT INTO boostapp.item_cat (id,`order`) VALUES ".$string." ON DUPLICATE KEY UPDATE `order`= VALUES(`order`)");
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        }else{
            echo "error";
        }
    }
    public function reorderMembershipTypes($rawData){
        $data = (object) $rawData;
        if($data && isset($data->orderArr)){
            if (!$this->checkIds($data->orderArr, "boostapp.membership_type")) {
                echo "error";
                return;
            }
            $string = $this->createStringFromObjArr($data->orderArr);
            $res = DB::insert("INSERT INTO boostapp.membership_type (id,`order`) VALUES ".$string." ON DUPLICATE KEY UPDATE `order`= VALUES(`order`)");
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
        }else{
            echo "error";
        }
    }
}