<?php
require_once '../app/init.php';
require_once 'Classes/Company.php';
require_once 'Classes/ClientFormFields.php';
require_once 'Classes/ClientForm.php';

if (Auth::check()) {
    if (Auth::userCan('31')) {

        try {
            $data = file_get_contents('php://input');
            $data = json_decode($data,true);
            
           $type = $data['type'];

            //GET ALL STREETS AND CITIES NAMES
            // $cities = DB::table('cities')->get();
            // $streets = DB::table('street')->get();

            // get company data
            $company = Company::getInstance();
            $company_num =  $company->__get("CompanyNum");

            $form = ClientForm::getFormByCompanyNumAndType($company_num, $type);

            if($form == null){
                $clientForm = new ClientForm();
                $clientForm->getCompanyForm($company_num,$type,Auth::user()->id);
            }
            $form = ClientForm::getFormByCompanyNumAndType($company_num, $type);
            $return = [
                'form' => $form,
                // 'cities' => $cities,
                // 'streets' => $streets
            ];

            echo json_encode($return, JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {

            echo $e;
        }
    }
}
