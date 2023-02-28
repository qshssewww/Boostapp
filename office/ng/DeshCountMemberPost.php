<?php

require_once '../../app/initcron.php'; 

$CompanyNum = Auth::user()->CompanyNum;


$MemberShipCounts = DB::table('client_activities')->select('id')
            ->where('TrueDate','>=', date('Y-m-d'))->where('StartDate','<=', date('Y-m-d'))->where('Department','=', '1')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->where('MemberShip','!=', 'BA999')->where('ClientStatus','=', '0')->where('FirstDateStatus','=', '0')
            ->Orwhere('TrueDate','<', date('Y-m-d'))->where('Freez', '=', '1')->where('StartFreez', "<=", date('Y-m-d'))->where('EndFreez', '>=', date('Y-m-d'))->where('StartDate','<=', date('Y-m-d'))->whereIn('Department', array(1,2))->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->where('MemberShip','!=', 'BA999')->where('ClientStatus','=', '0')->where('FirstDateStatus','=', '0')
            ->Orwhere('TrueBalanceValue','>=', '1')->where('StartDate','<=', date('Y-m-d'))->whereNull('TrueDate')->where('Department','=', '2')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->where('MemberShip','!=', 'BA999')->where('ClientStatus','=', '0')->where('FirstDateStatus','=', '0')
            ->Orwhere('TrueBalanceValue','>=', '1')->where('StartDate','<=', date('Y-m-d'))->where('TrueDate','>=', date('Y-m-d'))->where('Department','=', '2')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->where('MemberShip','!=', 'BA999')->where('ClientStatus','=', '0')->where('FirstDateStatus','=', '0') 
            ->groupBy('ClientId')->get();
        
echo json_encode($MemberShipCounts);  



?>
