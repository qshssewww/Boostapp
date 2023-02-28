<?php

$q = DB::table('sections')
        ->where('sections.CompanyNum', '=', $rest->CompanyNum)
        ->select('sections.Title as room')
        ->orderBy('sections.Title');

$rest->answer->items = $q->get();
$rest->answer->sql = $q->toString();