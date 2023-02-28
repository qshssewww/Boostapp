<?php
require_once '../../app/initcron.php';
$CompanyNum = Auth::user()->CompanyNum;



$Clients = DB::table('client')
->where('CompanyName','like', '%'.@$_REQUEST['q'].'%')->where('CompanyNum', '=', $CompanyNum)
->Orwhere('FirstName','like', '%'.@$_REQUEST['q'].'%')->where('CompanyNum', '=', $CompanyNum) 
->Orwhere('LastName','like', '%'.@$_REQUEST['q'].'%')->where('CompanyNum', '=', $CompanyNum)     
->Orwhere('CompanyId','like', '%'.@$_REQUEST['q'].'%')->where('CompanyNum', '=', $CompanyNum)
->Orwhere('ContactMobile','like', '%'.@$_REQUEST['q'].'%')->where('CompanyNum', '=', $CompanyNum)
->Orwhere('ContactMobile','like', '%'.substr($_REQUEST['q'],1,strlen($_REQUEST['q'])).'%')->where('CompanyNum', '=', $CompanyNum)
->Orwhere('Company','like', '%'.@$_REQUEST['q'].'%')->where('CompanyNum', '=', $CompanyNum)    
->Orwhere('Email','like', '%'.@$_REQUEST['q'].'%')->where('CompanyNum', '=', $CompanyNum)->get();

$ClientsSearch = array();
foreach ($Clients as $Client) {
    $ClientsSearch[] = array('id' => $Client->id, 'name' => $Client->CompanyName, 'email' => $Client->Email, 'companyid' => $Client->CompanyId, 'phone' => $Client->ContactMobile, 'barnd' => $Client->BrandName);
}



echo '{"results": '.json_encode($ClientsSearch).'}';
?>