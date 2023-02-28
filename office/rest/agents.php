<?php

$q = DB::table('users')
        ->where('users.CompanyNum', '=', $rest->CompanyNum)
        ->where('users.Coach', '=', '0')
        ->select('users.display_name as agent')
        ->orderBy('users.display_name');

$rest->answer->items = $q->get();
$rest->answer->sql = $q->toString();