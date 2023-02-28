<?php

$q = DB::table('pipeline')
        ->where('pipeline.CompanyNum', '=', $rest->CompanyNum)
        ->select('pipeline.Source as source')
        ->groupBy('pipeline.Source')
        ->orderBy('pipeline.Source');

$rest->answer->items = $q->get();
$rest->answer->sql = $q->toString();