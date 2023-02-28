<?php

$q = DB::table('brands')
    ->where('brands.CompanyNum', '=', $rest->CompanyNum)
    ->select('brands.BrandName as branch')->orderBy('brands.BrandName');

$rest->answer->items = $q->get();