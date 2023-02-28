<?php
require_once '../../../../app/init.php';
if (Auth::guest()) exit;

if (Auth::userCan('31')) {
    $rawData = $_POST;
    $data = (object) $rawData;
    $res=[];
    if($data && isset($data->id) && isset($data->otherId)){
        if($data->otherId){
            $res[] = DB::table("boostapp.classstudio_date")->where('ClassNameType','=',$data->$id)->update(array(
                "ClassNameType"=>$data->otherId
            ));
        }else{
            $res[] = DB::table('classstudio_date')->where('ClassNameType','=',$data->$id)->where('Status',"=","0")->update(array('Status' => '2', 'displayCancel' => '1'));
        }
        $res[] = DB::table("boostapp.class_type")->where("id","=",$data->id)->delete();
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
    }else{
        echo "error";
    }
}
?>