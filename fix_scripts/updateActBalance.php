<?php
require_once '../app/init.php';
if(Auth::user()->role_id == 1) {

//    $companies = [268807,536847,219704,804671,846981,371590];

//Balance - (count here) past lessons from act
    $dataToUpdate = DB::table('boostapp.client_activities as ca')
//    ->select('ca.id','csAct.id as csActID','ca.ClientID', 'csAct.ClientId as ClientID2' )
        ->select('ca.id',  DB::raw('count(*) as total'), 'ca.ActBalanceValue','ca.BalanceValue','ca.TrueBalanceValue', 'ca.BalanceValueLog' )
        ->join('boostapp.classstudio_act as csAct', function ($join) {
            $join->on('ca.id', '=', 'csAct.ClientActivitiesId')
                ->on('ca.ClientID', '=', 'csAct.ClientID');
        })
        ->leftjoin('settings', 'settings.CompanyNum','=','ca.CompanyNum')
        ->where ('settings.Status', 0)
        ->where('ca.Department','=', '2')
        ->where('ca.Status', '0')
        ->whereIN('csAct.Status', array(1,2,4,6,10,11,8,12,15,21))
        ->where ('csAct.ClassDate', '<', date('Y-m-d'))
//        ->whereIN('ca.CompanyNum', $companies)
        //->where ('csAct.ClassDate', '>', '2020-01-01')
        ->groupBy('ca.id')
        ->get();



    $arrayToUpdate = array();

    foreach ($dataToUpdate as $rowToUpdate) {

        $Loops = json_decode($rowToUpdate->BalanceValueLog, true);
        if (isset($Loops)) {
            foreach ($Loops['data'] as $key => $val) {
                $changeBalance = $val['ClassNumber'];
                $rowToUpdate->BalanceValue += $changeBalance;
            }
        }

        if($rowToUpdate->ActBalanceValue != $rowToUpdate->BalanceValue - $rowToUpdate->total){
            $rowToUpdate->valueToUpdateByBalance = $rowToUpdate->BalanceValue - $rowToUpdate->total;
            //    $rowToUpdate->valueToUpdateByTrueBalance = $rowToUpdate->TrueBalanceValue - $dataToUpdate2[$i]->total2;
            $arrayToUpdate[]=$rowToUpdate;
        }

    }
    print("<pre>".print_r($arrayToUpdate,true)."</pre>");
    $count = 0;
    foreach ($arrayToUpdate as $rowToUpdate){
        $res = DB::table('boostapp.client_activities')
            ->where('id',$rowToUpdate->id)
            ->update(array('ActBalanceValue' => $rowToUpdate->valueToUpdateByBalance));
        if($res) {
            $count++;
        }
    }
    echo '<br> updated: '.$count;
}













