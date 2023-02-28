<?php
require_once '../../app/init.php';
require_once __DIR__.'/../Classes/City.php';

if (Auth::guest()) exit;


if (!empty($_POST["fun"])) {
    $fun = $_POST["fun"];
    unset($_POST["fun"]);

    switch ($fun) {
        case "saveBranchSettings":

            $validator = Validator::make($_POST,
                ['BranchName' => 'Required']
            );

            if ($validator->passes()) {
                $Title = trim($_POST['BranchName']);
                $BranchId = $_POST['BranchId'] ?? '';
                $isNew = $BranchId == '';
                $Status = $_POST['BranchStatus'] ?? 0;

                $PlaceId = $_POST['BranchPlaceId'] ?? '';
                $PlaceString = $_POST['BranchPlaceString'] ?? '';
                $PlaceLatLng = $_POST['BranchPlaceLatLng'] ?? '';
                $PlaceCity = $_POST['BranchPlaceCity'] ?? null;
                if($PlaceCity) {
                    $cityId = (new City())->getCityIdByName($PlaceCity);
                }

                $CompanyNum = Auth::user()->CompanyNum;

                $Count = DB::table('brands')->where('CompanyNum', '=', $CompanyNum)->count();

                if ($isNew) {
                    $BranchId = DB::table('brands')->insertGetId([
                        'CompanyNum' => $CompanyNum,
                        'BrandName' => $Title,
                        'FinalCompanynum' => $CompanyNum,
                        'ShowBrand' => '1'
                    ]);
                } else {
                    DB::table('brands')
                        ->where('id', '=', $BranchId)
                        ->where('CompanyNum', '=', $CompanyNum)
                        ->update([
                            'BrandName' => $Title,
                            'Status' => $Status,
                        ]);

                    DB::table('client')
                        ->where('Brands', '=', $BranchId)
                        ->where('CompanyNum', '=', $CompanyNum)
                        ->update(array('BrandName' => $Title));

                    DB::table('pipeline')
                        ->where('Brands', '=', $BranchId)
                        ->where('CompanyNum', '=', $CompanyNum)
                        ->update(array('BrandsNames' => $Title));
                }

                // update branch place
                if ($PlaceString) {
                    if (DB::table('boostapp.branch_google_address')
                        ->where('branch_id', '=', $BranchId)
                        ->exists()) {
                        DB::table('boostapp.branch_google_address')
                            ->where('branch_id', '=', $BranchId)
                            ->update([
                                'place_id' => $PlaceId,
                                'address' => $PlaceString,
                                'lat_lng' => $PlaceLatLng ?? '',
                                'city_id' => $cityId ?? null,
                            ]);
                    } else {
                        DB::table('boostapp.branch_google_address')
                            ->insert([
                                'branch_id' => $BranchId,
                                'place_id' => $PlaceId,
                                'address' => $PlaceString,
                                'lat_lng' => $PlaceLatLng ?? '',
                                'city_id' => $cityId ?? null,
                            ]);
                    }
                } else {
                    DB::table('boostapp.branch_google_address')
                        ->where('branch_id', '=', $BranchId)
                        ->delete();
                }

                // new first branch
                if ($Count == '0') {
                    DB::table('client')
                        ->where('CompanyNum', '=', $CompanyNum)
                        ->update(array('Brands' => $BranchId, 'BrandName' => $Title));

                    DB::table('client_activities')
                        ->where('CompanyNum', '=', $CompanyNum)
                        ->update(array('Brands' => $BranchId));

                    DB::table('classstudio_act')
                        ->where('CompanyNum', '=', $CompanyNum)
                        ->update(array('Brands' => $BranchId));

                    DB::table('docs')
                        ->where('CompanyNum', '=', $CompanyNum)
                        ->update(array('Brands' => $BranchId));

                    DB::table('docs_payment')
                        ->where('CompanyNum', '=', $CompanyNum)
                        ->update(array('Brands' => $BranchId));

                    DB::table('docslist')
                        ->where('CompanyNum', '=', $CompanyNum)
                        ->update(array('Brands' => $BranchId));

                    DB::table('docs2item')
                        ->where('CompanyNum', '=', $CompanyNum)
                        ->update(array('Brands' => $BranchId));

                    DB::table('paytoken')
                        ->where('CompanyNum', '=', $CompanyNum)
                        ->update(array('Brands' => $BranchId));

                    DB::table('pipeline')
                        ->where('CompanyNum', '=', $CompanyNum)
                        ->update(array('Brands' => $BranchId, 'BrandsNames' => $Title));

                    DB::table('dynamicforms')
                        ->where('CompanyNum', '=', $CompanyNum)
                        ->update(array('Brands' => $BranchId));

                    DB::table('dynamicforms_answers')
                        ->where('CompanyNum', '=', $CompanyNum)
                        ->update(array('Brands' => $BranchId));
                }

                // success
                echo json_encode(array(
                    "Status" => 1
                ));
            } else {
                json_message($validator->errors()->toArray(), false);
            }
            break;
    }
} else {
    echo json_encode(array(
        "Message" => "No Function",
        "Status" => "Error"
    ));
}
