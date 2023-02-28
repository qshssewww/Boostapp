<?php

require_once '../../app/initcron.php';

if (Auth::check()) {


    $CompanyNum = Auth::user()->CompanyNum;
    $Acts = $_POST['Act'] ?? '';
    if(!empty($Acts)) {
        $segments = explode(':', $Acts);
        $EventId = array_shift($segments);
        $NewStatus = array_shift($segments);

        $Clients = DB::table('appnotification')->where('id', '=', $EventId)->where('Type', '3')->where('CompanyNum', '=', $CompanyNum)->first();

/// בצע פעולה לפי ססטוס ישן
        if ($NewStatus == 0) {

            DB::table('appnotification')
                ->where('id', $EventId)
                ->where('CompanyNum', '=', $CompanyNum)
                ->where('Type', '3')
                ->update(array('Status' => 0));


        } elseif ($NewStatus == 1) {
            $CloseDate = date('Y-m-d H:i:s');
            $CloseUser = Auth::user()->id;

            DB::table('appnotification')
                ->where('id', $EventId)
                ->where('CompanyNum', '=', $CompanyNum)
                ->where('Type', 3)
                ->update(array('Status' => 1));


        }

        echo lang('action_done');
    } else {
        echo lang('action_cancled');
    }
}


