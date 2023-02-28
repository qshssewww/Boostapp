<?php

$q = DB::table('users')
        ->where('users.CompanyNum', '=', $rest->CompanyNum)
        ->select('id as userId','users.display_name as fullName', 'email', 'ContactMobile as phone')
        ->orderBy('users.display_name');

$rest->answer->items = $q->get();
// $rest->answer->sql = $q->toString();