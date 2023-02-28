<?php

header('Content-type: application/json');

require_once '../../app/init.php';

$rawData = file_get_contents("php://input");
$dataArray = (json_decode($rawData));
if(
    isset($dataArray->CompanyNum) && !empty($dataArray->CompanyNum) &&
    isset($dataArray->uId) && !empty($dataArray->uId) &&
    isset($dataArray->type) && !empty($dataArray->type) &&
    isset($dataArray->col_name) && !empty($dataArray->col_name) &&
    isset($dataArray->datatype) && !empty($dataArray->datatype)
    
    ){
    $addColQuery = false;
    $msg = '';
    try {
        $CompanyNum = $dataArray->CompanyNum;
        $uId = $dataArray->uId;
        $type = $dataArray->type;
        $column_name = $dataArray->col_name;
        $datatype = $dataArray->datatype;
        
        if($type === 'active'){
            $type = 'client';
        }


        $forms_id =  DB::table('client_forms')
                    ->where('company_num', '=', $CompanyNum)
                    ->where('type', '=', $type)->pluck('form_id');
                            
        if(!$forms_id){
            $insert_data = array(
                "company_num"=>$CompanyNum,
                "type"=>$type,
                "user_id"=>$uId
            );
            DB::table('client_forms')->insert($insert_data);
            $forms_id = DB::getPdo()->lastInsertId();
        }
        if($forms_id){
            $insert_data = array(
                "name" => $column_name
            );
            DB::table('form_fields')->insert($insert_data);
            $field_id = DB::getPdo()->lastInsertId();
        }else{
            $addColQuery = false;
            $msg .= 'forms_id not found';
        }

        if($forms_id && $field_id){
            $option = null;
            if($datatype == 'list' || $datatype == 'radio' ){
                if(isset($dataArray->data) && !empty($dataArray->data)){
                    $option = json_encode($dataArray->data, JSON_UNESCAPED_UNICODE);
                }
            }
            $max_order = DB::table('client_form_fields')->max('order');
            $new_order = (int)$max_order + 1;
            $mandatory = 0;
            $show = 1;
        
            $insert_data_form_fields = array(
                "field_id"=>$field_id,
                "form_id"=>$forms_id,
                "mandatory"=>$mandatory,
                "show"=>$show,
                "order"=>$new_order,
                "type"=>$datatype,
                "options"=>$option
            );
            
            DB::table('client_form_fields')->insert($insert_data_form_fields);
            $client_form_fields_id = DB::getPdo()->lastInsertId();
            $addColQuery = true;
        }
        else{
            $addColQuery = false;
            $msg .= $insert_data;
        }
         
    } catch (Exception $e) {
        $addColQuery = false;
        $msg = 'exception found';

    }

    if($addColQuery){
        exit(json_encode([
            'success' => 1,
            'client_form_fields_id' => $client_form_fields_id,
            'field_name' => $column_name,
            'field_data_type' => $datatype,
        ]));    
    }else{
        exit(json_encode([
            'success' => 0,
            'column_added' => $addColQuery,
            'message' => $msg
        ]));
    }
}else{
    exit(json_encode([
        'success' => 0,
        'message' => 'Bad Request'
    ]));
    
}

