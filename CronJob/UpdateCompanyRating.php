<?php

$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Utils/HttpClient.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/247SoftNew/ClientGoogleAddress.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/247SoftNew/GooglePlace.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/247SoftNew/SoftClient.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/CompanyInfo.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/services/LoggerService.php';

$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();

set_time_limit(0);
ini_set("memory_limit", "-1");

$offset = 100;

try {
    $companyQuery = SoftClient::select(SoftClient::getTable() . '.*')
        ->join('boostapp.settings', SoftClient::getTable() . '.FixCompanyNum', '=', 'boostapp.settings.CompanyNum')
        ->where('boostapp.settings.Status', 0);

    $companyCount = $companyQuery->count();

    for ($k = 0; $k < $companyCount; $k += $offset) {
        $companies = $companyQuery
            ->skip($k)
            ->take($offset)
            ->get();

        foreach ($companies as $company) {
            // take google place info
            $googlePlaceInfo = GooglePlace::where('company_id', $company->id)->pluck('info');
            $googlePlaceInfo = json_decode($googlePlaceInfo, true);

            $ratingGoogle = $googlePlaceInfo['rating'] ?? null;
            $reviewsCountGoogle = $googlePlaceInfo['user_ratings_total'] ?? null;

            $ratingClient = 0;

            $ratingData = [
                'CompanyNum' => $company->FixCompanyNum,
                'rating_google' => $ratingGoogle,
                'reviews_count' => $reviewsCountGoogle,
            ];

            $companyInfo = CompanyInfo::where('CompanyNum', $company->FixCompanyNum)->first();
            if (!$companyInfo) {
                $companyInfo = new CompanyInfo();
            }

            $companyInfo->fill($ratingData);
            $companyInfo->save();

            $companyInfo->recalculateTotalRating();
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
