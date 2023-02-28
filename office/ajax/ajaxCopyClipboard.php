<?php

require_once '../../app/init.php';

if (Auth::check()){

    $Brands = $_POST['Brands'];

    $UserId = Auth::user()->id;
    $CompanyNum = Auth::user()->CompanyNum;

    $GroupNumber = rand(1262055681, 1262055681);
    $GroupNumber = uniqid() . '' . strtotime(date('YmdHis')) . '' . $GroupNumber . '' . rand(1, 9999999);
    $CreateCode = uniqid($GroupNumber);


    $time = strtotime(date('Y-m-d'));

    $TimeOut = date("Y-m-d", strtotime("+1 month", $time));


    $AddTempClient = DB::table('tempclient')->insertGetId(

        array('CompanyNum' => $CompanyNum, 'Brands' => $Brands, 'CreateCode' => $CreateCode, 'UserId' => $UserId, 'TimeOut' => $TimeOut));


    $LunchLink = 'https://new.boostapp.co.il/online.php?CreateCode=' . $CreateCode;

    echo $LunchLink;

}
