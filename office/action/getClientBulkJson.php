<?php
require_once '../../app/initcron.php';
if(Auth::check()):

function hexcode($str) {
    $code = dechex(crc32($str));
    $code = substr($code, 0, 6);
    return $code;
}
$CompanyNum = Auth::user()->CompanyNum;

if(isset($_POST) && !empty($_POST['companyName'])) {
   
   $ClientsSearch = array();

    foreach ($_POST['companyName'] as $key => $searchString) {
        
        $Clients = DB::table('client')
        ->where('CompanyName','like', '%'.$searchString.'%')->where('CompanyNum', '=', $CompanyNum)
        ->Orwhere('FirstName','like', '%'.$searchString.'%')->where('CompanyNum', '=', $CompanyNum) 
        ->Orwhere('LastName','like', '%'.$searchString.'%')->where('CompanyNum', '=', $CompanyNum)     
        ->Orwhere('CompanyId','like', '%'.$searchString.'%')->where('CompanyNum', '=', $CompanyNum)
        ->Orwhere('ContactMobile','like', '%'.$searchString.'%')->where('CompanyNum', '=', $CompanyNum)
        ->Orwhere('ContactMobile','like', '%'.substr($searchString,1,strlen($searchString)).'%')->where('CompanyNum', '=', $CompanyNum)
        ->Orwhere('id', '=', $searchString)
        ->Orwhere('Email','like', '%'.$searchString.'%')->where('CompanyNum', '=', $CompanyNum)->limit(20)->orderBy('Status', 'ASC')->get();
        if(empty($Clients)) {
            continue;
        }
        $Client = $Clients[0];
        $img = !empty($Client->ProfileImage) ? $Client->ProfileImage : 'https://ui-avatars.com/api/?length=1&name='.$Client->FirstName.'&background=f3f3f4&color=000&font-size=0.5';
        $obj = array('name' => $Client->CompanyName, 'url' => '/office/ClientProfile.php?u='.$Client->id, 'email' => $Client->Email, 'img' => $img, 'phone' => $Client->ContactMobile, 'brand' => $Client->BrandName, 'id' => $Client->id);
        array_push($ClientsSearch, $obj);
    }

    $res = '{"results": '.json_encode($ClientsSearch, JSON_UNESCAPED_UNICODE).'}';
    echo $res;
}
endif;
?>