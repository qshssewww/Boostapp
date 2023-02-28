<?php

$q = DB::table('class_type')->where('class_type.CompanyNum', '=', $rest->CompanyNum)->select('class_type.Type as className', 'class_type.Color as color')->orderBy('class_type.Type');

$rest->answer->items = $q->get();