<?php
require_once  '../app/init.php';
if(Auth::guest() || (int)Auth::user()->role_id !== 1) {
    exit;
}
echo "-----------------     Start Script    ----------------------------------<br>";

$getActs = DB::table('classstudio_act')
    ->whereIn('CompanyNum', [20914, 44097])
    ->where('ClassDate', '>=', date('Y-m-d'))
    ->whereBetween('Dates', [date('2022-03-28 17:11:46'), date('2022-03-28 17:12:25')])
    ->get();
$count = 0;
$notUpdated = 0;
foreach($getActs as $act) {
    $class = DB::table('classstudio_date')
        ->where('Status', '=', 0)
        ->where('id', $act->ClassId)
        ->where('CompanyNum', '=', $act->CompanyNum)
        ->first();
    if($class) {
        $diffAct = DB::table('classstudio_act')->where('ClassId', '=', $act->ClassId)->where('CompanyNum', '=', $act->CompanyNum)->whereNotNull('CancelJson')->first();
        if($diffAct) {
            $update = DB::table('classstudio_act')
                ->where('id', $act->id)
                ->where('CompanyNum', '=', $act->CompanyNum)
                ->update(array(
                    'CancelJson' => $diffAct->CancelJson,
                ));
            echo "update act: ".$act->id." by different act <br>";
            $count++;
        } else {
            $CancelLaw = $class->CancelLaw;
            if ($CancelLaw == 1) {
                $CancelDate = $class->StartDate;
                $CancelDay = '';
                $CancelTime = $class->CancelTillTime;
            } elseif ($CancelLaw == 2 || $CancelLaw == 3) {
                $CancelDate = date("Y-m-d", strtotime('-1 day', strtotime($class->StartDate)));
                $CancelDay = '';
                $CancelTime = $class->CancelTillTime;
            } else {
                $CancelDate = '';
                $CancelDay = '';
                $CancelTime = '';
            }
            $CancelJson = '';
            $CancelJson .= '{"data": [';
            $CancelJson .= '{"CancelDate": "' . $CancelDate . '", "CancelDay": "' . $CancelDay . '", "CancelTime": "' . $CancelTime . '", "CancelLaw": "' . $CancelLaw . '"}';
            $CancelJson .= ']}';


            $count++;
            $update = DB::table('classstudio_act')
                ->where('id', $act->id)
                ->where('CompanyNum', '=', $act->CompanyNum)
                ->update([
                    'CancelJson' => $CancelJson,
                ]);
            echo "update by class rules act: ".$act->id." <br>";
        }


    }
}
echo "-----------------     update: ".$count."    ----------------------------------<br>";
echo "-----------------     not updated: ".$notUpdated."    ----------------------------------<br>";
echo "-----------------     Start Script    ----------------------------------<br>";