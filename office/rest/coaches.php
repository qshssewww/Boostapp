<?php

$q = DB::table('users')
        ->where('users.CompanyNum', '=', $rest->CompanyNum)
        ->where('users.Coach', '=', '1')
        ->select('users.display_name as coach', 'users.id as coachId')
        ->orderBy('users.display_name');

$rest->answer->items = $q->get();
// $rest->answer->sql = $q->toString();