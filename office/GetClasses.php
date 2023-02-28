<?php
require_once '../app/init.php';
require_once 'Classes/Company.php';

if (Auth::check()) {
    if (Auth::userCan('31')) {
        try {

            $data = file_get_contents('php://input');
            $data = json_decode($data, true);
            $className = $data['className'];

            $company = Company::getInstance();
            $companyNum = $company->__get('CompanyNum');

            $classes = DB::table('class_type')
                ->select('id', 'Type as value')
                ->where(
                    'CompanyNum',
                    '=',
                    $companyNum
                )
                ->where(
                    'Type',
                    'LIKE',
                    $className . '%'
                )
                ->get();
            // $classes = array_column($classes, 'Type');
            if (count($classes) > 0) {
                echo json_encode($classes, JSON_UNESCAPED_UNICODE);
            } else {
                throw new Exception("empty");
            }
        } catch (Exception $e) {
            echo json_encode($e, JSON_UNESCAPED_UNICODE);
        }
    }
}
