<?php

ini_set('max_execution_time', 0);
$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/initcron.php';
echo "-----------------     Start Script    ----------------------------------\n";
$cnt = 0;
if(Auth::guest() || (int)Auth::user()->role_id !== 1) {
    exit;
}

$cards = DB::table('client_activities')
    ->whereNotIn('CompanyNum', [882273])
    ->where('Status', 0)
    ->where('Department', 2)
    ->where('BalanceValue', '>', 0)
    ->where('CardNumber', '!=', 1)
    ->whereNull('BalanceValueLog')
    ->get();
echo "-----------------     total: " . count($cards) . "    ----------------------------------\n \n";
foreach ($cards as $card) {
    $settings = DB::table('settings')->where('Status', 0)->where('CompanyNum', $card->CompanyNum)->first();
    if ($settings) {
        $actCount = DB::table('classstudio_act')
            ->where('CompanyNum', $card->CompanyNum)
            ->where('ClientActivitiesId', '=', $card->id)
            ->whereIn('Status', array(1, 2, 4, 6, 8, 11, 15, 21))
            ->count();

        $activity = DB::table('client_activities')
            ->where('id', $card->id)
            ->whereNull('BalanceValueLog')
            ->first();
        if(!$activity) {
            continue;
        }

        $balance = $activity->BalanceValue - $actCount;
        if ($balance != $activity->TrueBalanceValue) {
            echo "activity " . $activity->id . ": TrueBalance = " . $activity->TrueBalanceValue . ". should be = " . $balance . "\n";
            $cnt++;

//            $update = DB::table('client_activities')
//                ->where('id', $activity->id)
//                ->update(['TrueBalanceValue' => $balance]);
//            if($update) {
//                echo "activity " . $activity->id . ": updated from TrueBalance = " . $activity->TrueBalanceValue . ". to = " . $balance . "\n";
//                $cnt++;
//            }
        }
    }
}


echo "total updated count: " . $cnt . " \n";
echo "-----------------     end Script    ----------------------------------\n";
