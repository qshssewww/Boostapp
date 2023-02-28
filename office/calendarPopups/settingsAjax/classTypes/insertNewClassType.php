<?php
require_once '../../../../app/init.php';
if (Auth::guest()) exit;

if (Auth::userCan('31')) {
    $rawData = $_POST;
    $data = (object) $rawData;
    if($data && isset($data->Name) && isset($data->CompanyNum) && isset($data->Color) && isset($data->duration) && isset($data->Memberships)){
        $res = DB::table('boostapp.class_type')->insertGetId(array(
            "Type"=>$data->Name,
            "CompanyNum"=>$data->CompanyNum,
            "duration"=>$data->duration,
            "durationType"=>"1",
            "Color"=>$data->Color
        ));        
        foreach($data->Memberships as $membership){
            DB::table('boostapp.items_roles')->where("ItemId",'=',$membership)->update(
                array(
                    "Class"=>DB::raw('CONCAT(Class,",'.$res.'")')
                )
            );
        }
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }else{
        echo "error";
    }
}
?>