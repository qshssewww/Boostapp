<?php

    $q = DB::table('medicalindicatordic as m')
                ->select(
                    'm.id', 
                    DB::raw('IF(m.type IS NULL, "כללי", m.type) as type'), 
                    'm.value',
                    'm.input',
                    'm.description'
                )
                ->where('m.status', '=', '1');
    $rest->answer->items = $q->get();