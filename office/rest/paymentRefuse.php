<?php

$q = DB::table('payment')->where('payment.CompanyNum', $rest->CompanyNum)->where('payment.status', 2);
$rest->answer->items = $q->get();