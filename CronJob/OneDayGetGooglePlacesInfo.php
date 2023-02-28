<?php

$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Utils/HttpClient.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/247SoftNew/ClientGoogleAddress.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/247SoftNew/GooglePlace.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/CompanyInfo.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/services/LoggerService.php';

$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();

set_time_limit(0);
ini_set("memory_limit", "-1");


$GOOGLE_API_KEY = 'AIzaSyClZJ-petZoC59mDxjw7mjCw9ALwpORbQA';

$offset = 100;

try {
    $studiosCount = ClientGoogleAddress::count();
    $url = 'https://maps.googleapis.com/maps/api/place/details/json';

    for ($k = 0; $k < $studiosCount; $k += $offset) {
        $studiosList = ClientGoogleAddress::select('*')
            ->join('247softnew.client', '247softnew.client_google_address.client_id', '=', '247softnew.client.id')
            ->where('247softnew.client.CompanyName', 'NOT LIKE', '%סיטון%')
            ->skip($k)
            ->take($offset)
            ->get();

        foreach ($studiosList as $studio) {
            if (!$studio->place_id) {
                continue;
            }

            $googlePlaceModel = GooglePlace::where('company_id', $studio->id)->where('place_id', $studio->place_id)->first();

            if (!$googlePlaceModel) {
                $googlePlaceModel = new GooglePlace();
            }

            if ($googlePlaceModel->updated_at > date('Y-m-d H:i:s', strtotime('-7 days'))) {
                continue;
            }

            $data = [
                'placeid' => $studio->place_id,
                'key' => $GOOGLE_API_KEY,
                'fields' => 'address_component,adr_address,business_status,formatted_address,geometry,icon,icon_mask_base_uri,icon_background_color,name,geometry,place_id,type,rating,wheelchair_accessible_entrance,website,reviews,user_ratings_total,url',
                'reviews_no_translations' => 'false',
            ];

            $companyInfo = HttpClient::sendRequest('GET', $url, $data);

            if ($companyInfo['status'] !== 'OK') {
                LoggerService::error([
                    'message' => 'Google place details request failed',
                    'studio_id' => $studio->clint_id,
                    'place_id' => $studio->place_id,
                    'status' => $companyInfo['status'],
                    'error_message' => $companyInfo['error_message'] ?? null,
                ]);
                continue;
            }

            $placeInfo = [
                'company_id' => $studio->id,
                'place_id' => $studio->place_id,
                'info' => json_encode($companyInfo['result'], JSON_PRETTY_PRINT),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $googlePlaceModel->fill($placeInfo);
            $googlePlaceModel->save();
        }
    }

    $Cron->end();
} catch (\Throwable $e) {
    $arr = array(
        "line" => $e->getLine(),
        "message" => $e->getMessage(),
        "file_path" => $e->getFile(),
        "trace" => $e->getTraceAsString()
    );

    $Cron->cronLog($arr);
}
