<?php

header('Content-type: application/json');

require_once '../../app/init.php';

$db_success = 1;
$rawData = file_get_contents("php://input");
$dataArray = (json_decode($rawData));

$insertedIds = [];
$errorsIds = [];

if($dataArray->CompanyNum && $dataArray->type){
    $CompanyNum = $dataArray->CompanyNum;
    $type = $dataArray->type;
    try {
        $item_data = DB::table($type)
                    ->where('CompanyNum','=',$CompanyNum)
                    ->where('Status','=',0)
                    ->get();
        
    } catch (Exception $e) {
        $db_success = 0;
    }
}


if($db_success){
    exit(json_encode([
        'success' => 1,
        'optionList' => $item_data,
        'optionList_count' => count($item_data)
    ]));    
}else{
    exit(json_encode([
        'success' => 0,
        'db_success' => $db_success,
        'message' => 'columns not found'
    ]));
}
