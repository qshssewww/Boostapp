<?php
require_once '../../../../app/init.php';
if (Auth::guest()) exit;

if (Auth::userCan('31')) {

    function searchForId($id, $array) {
        foreach ($array as $key => $val) {
            if ($val->ItemId == $id) {
                return $key;
            }
        }
        return null;
     }


     $rawData = $_POST;
     $data = (object) $rawData;
    if($data && isset($data->Name) && isset($data->id) && isset($data->Color) && isset($data->duration) && isset($data->Memberships)){
        $res = DB::table('boostapp.class_type')->where("id",'=',$data->id)->update(array(
            "Type"=>$data->Name,
            "duration"=>$data->duration,
            "Color"=>$data->Color
        ));

        $MembershipsInDB = DB::select(DB::raw('SELECT * FROM boostapp.items_roles where  Item="Class" and (Class like "%,'.$data->id.',%" or class like "'.$data->id.',%" or class like "%,'.$data->id.'" OR class='.$data->id.')'));
        $membershipsArrayFromDb=[];
        if($MembershipsInDB){
            foreach($MembershipsInDB as $membership){
                $membershipsArrayFromDb[]=$membership->ItemId;
            }
        }
        $itemsToDelete=array_diff($membershipsArrayFromDb,$data->Memberships);
        $itemsToInsert=array_diff($data->Memberships,$membershipsArrayFromDb);
        foreach($itemsToInsert as $membership){
        $res=1;
            DB::table('boostapp.items_roles')->where("ItemId",'=',$membership)->update(
                array(
                    "Class"=>DB::raw('CONCAT(Class,",'.$data->id.'")')
                )
            );
        }

        foreach($itemsToDelete as $membership){
            $key=searchForId($membership,$MembershipsInDB);
            if($key){
                $val=$MembershipsInDB[$key];
                $array=explode(',',$val->Class);
                $newArray=array_diff($array,[$data->id]);
                $newString=join(',',$newArray);
                $res=1;
                DB::table('boostapp.items_roles')->where("ItemId",'=',$membership)->update(
                array(
                    "Class"=>$newString
                )
                );
            }
           
        }
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }else{
        echo "error";
    }
}
?>