<?php
require_once '../../app/initcron.php';

if (Auth::check() && Auth::user()->role_id == 1) {
    $resArr = ["results" => []];
    $queryString = $_GET['q'] ?? '';

    $queryString = preg_replace('/[^\w\s\-_&]/u', '', $queryString);

    $Companies = DB::table('settings')->select('settings.AppName', 'settings.CompanyNum', 'appsettings.logoImg')
        ->leftJoin('appsettings', 'appsettings.CompanyNum', '=', 'settings.CompanyNum')
    ->where('settings.CompanyName','like', '%'.$queryString.'%')->where('Status','=', '0')
    ->Orwhere('settings.AppName','like', '%'.$queryString.'%')->where('Status','=', '0')
    ->Orwhere('settings.CompanyNum','like', '%'.$queryString.'%')->where('Status','=', '0')
    ->Orwhere('settings.ClientName','like', '%'.$queryString.'%')->where('Status','=', '0')
    ->orderBy('settings.AppName', 'ASC')->limit(10)->get();
    
    $CompanysSearch = array();
    foreach ($Companies as $Company) {
        $logo = !empty($Company->logoImg) ? '/office/files/logo/'.$Company->logoImg : '/office/files/logo/smallDefault.png';
        $obj = array('id' => $Company->CompanyNum, 'name' => $Company->AppName, 'logo' => $logo);
        array_push($CompanysSearch, $obj);
    }
    $resArr["results"] = $CompanysSearch;
    echo json_encode($resArr, JSON_UNESCAPED_UNICODE);
}

