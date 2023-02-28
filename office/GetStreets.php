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
            $street = $data['street'];

            $streets = DB::table('street')
                ->select('Street')
                ->where(
                    'City',
                    '=',
                    $city
                )->Where(
                    'Street',
                    'LIKE',
                    $street . '%'
                )
                ->get();
            $streets = array_column($streets, 'Street');
            if (count($streets) > 0) {
                echo json_encode($streets, JSON_UNESCAPED_UNICODE);
            } else {
                throw new Exception("empty");
            }
        } catch (Exception $e) {
            echo json_encode($e, JSON_UNESCAPED_UNICODE);
        }
    }
}
