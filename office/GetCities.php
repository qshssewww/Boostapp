<?php
require_once '../app/init.php';
require_once 'Classes/Company.php';
require_once 'Classes/ClientFormFields.php';
require_once 'Classes/ClientForm.php';

if (Auth::check()) {
    if (Auth::userCan('31')) {
        try {
            $data = file_get_contents('php://input');
            $data = json_decode($data, true);
            $city = $data['city'];

            $cities = DB::table('cities')
                ->select('City')
                ->where(
                    'City',
                    'LIKE',
                    $city . '%'
                )
                ->get();
            $cities = array_column($cities, 'City');
            if (count($cities) > 0) {
                echo json_encode($cities, JSON_UNESCAPED_UNICODE);
            } else {
                throw new Exception("empty");
            }
        } catch (Exception $e) {
            echo json_encode($e, JSON_UNESCAPED_UNICODE);
        }
    }
}
