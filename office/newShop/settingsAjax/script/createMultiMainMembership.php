<?php
require_once '../../../../app/init.php';

$Companies= DB::table('boostapp.settings')->select('*')->get();
foreach($Companies as $Company){
    $MainMemType=DB::table('boostapp.membership_type')->where('CompanyNum','=',$Company->CompanyNum)->where('MainMembership',"=","1")->first();
    if(!$MainMemType){
        $NewMainMemType=DB::table('boostapp.membership_type')->insertGetId(array(
            "Type"=>"כללי",
            "CompanyNum"=>$Company->CompanyNum,
            "mainMembership"=>"1"
        ));
        echo $NewMainMemType;
    }
}