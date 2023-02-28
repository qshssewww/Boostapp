<?php

ini_set('max_execution_time', 0);
//require_once '../app/init.php';
$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/initcron.php';
//$companies = [958848];
if(Auth::guest() || (int)Auth::user()->role_id !== 1) {
    exit;
}
echo "-----------------     Start Script    ----------------------------------\n";
$classes = DB::table('classstudio_date')->where('Status', '=', 0)
    ->where('StartDate', '>=', date('Y-m-d'))
    ->where('MaxClient', '>', 1)->where('ClassType', '=', 1)
    ->where('ClientRegisterRegular', '>', 0)
//    ->whereIn('CompanyNum', $companies)
    ->get();
$cnt = 0;

foreach($classes as $class) {

//    $ClientRegisterRegular = DB::table('classstudio_dateregular')
//        ->where('CompanyNum', '=', $class->CompanyNum)->where('GroupNumber', '=', $class->GroupNumber)
//        ->where('Status', '=', 0)
//        ->whereIn('RegularClassType', [1, 2])->where('StatusType', '=', 12)
//        ->count();

    $ClientRegisterRegular = DB::table('classstudio_dateregular')
        ->where('CompanyNum', '=', $class->CompanyNum)
        ->where('GroupNumber', '=', $class->GroupNumber)
        ->where('StatusType', '=', '12')
        ->where(function ($q) use ($class) {
            $q->where('RegularClassType', '=', 1)
                ->Orwhere('RegularClassType', '=', 2)->where('EndDate', '>=', $class->StartDate);
        })->count();

//    $ClientRegisterRegularWating = DB::table('classstudio_dateregular')
//        ->where('CompanyNum', '=', $class->CompanyNum)->where('GroupNumber', '=', $class->GroupNumber)
//        ->where('Status', '=', 0)
//        ->whereIn('RegularClassType', [1, 2])->where('StatusType', '=', 9)
//        ->count();

    $ClientRegisterRegularWating = DB::table('classstudio_dateregular')
        ->where('CompanyNum', '=', $class->CompanyNum)
        ->where('GroupNumber', '=', $class->GroupNumber)
//        ->where('Floor', '=', $ClassInfo->Floor)
        ->where('StatusType', '=', '9')
        ->where(function ($q) use ($class) {
            $q->where('RegularClassType', '=', 1)
                ->Orwhere('RegularClassType', '=', 2)->where('EndDate', '>=', $class->StartDate);
        })->count();


    $ClientRegister = DB::table('classstudio_act')->where('ClassId', '=', $class->id)
        ->where('CompanyNum', '=', $class->CompanyNum)->whereIn('Status', array(1,2,6,10,11,12,15,16,21,22,23))
        ->count();
    $WatingList = DB::table('classstudio_act')->where('ClassId', '=', $class->id)
        ->where('CompanyNum', '=', $class->CompanyNum)->where('Status', '=', 9)->where('StatusCount', '=', 1)
        ->count();

    $arr = array(
        'ClientRegister' => $ClientRegister,
        'WatingList' => $WatingList,
        'ClientRegisterRegular' => $ClientRegisterRegular,
        'ClientRegisterRegularWating' => $ClientRegisterRegularWating
    );

    if($ClientRegister != $class->ClientRegister || $WatingList != $class->WatingList || $ClientRegisterRegular != $class->ClientRegisterRegular || $ClientRegisterRegularWating != $class->ClientRegisterRegularWating) {
        $update = DB::table('classstudio_date')
            ->where('id', $class->id)
            ->update($arr);
        if($update) {
            $cnt++;
            echo "update class id: ".$class->id. " register count \n";
        }
    } else {
        echo "count is correct on ".$class->id."\n";
    }

}

echo "total updated count: ".$cnt." \n";
echo "-----------------     end Script    ----------------------------------\n";