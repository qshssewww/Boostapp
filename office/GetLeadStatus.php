<?php
require_once '../app/init.php';
require_once 'Classes/Company.php';
require_once 'Classes/ClientFormFields.php';
require_once 'Classes/ClientForm.php';

if (Auth::check()) {
    if (Auth::userCan('31')) {
        try {
            $company = Company::getInstance();
            $company_num =  $company->__get("CompanyNum");
            $mainPipeId= DB::table('boostapp.pipeline_category')->where("CompanyNum", "=", $company_num)->where("Act","=",1)->first();
            if($mainPipeId){
                $data=DB::table('boostapp.leadstatus')->where("CompanyNum", "=", $company_num)->where("PipeId","=",$mainPipeId->id)->get();
            }else{
                $data = [];
            }
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            echo $e;
        }
    }
}
