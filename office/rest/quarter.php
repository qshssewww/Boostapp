<?php
$q = DB::table('docs')
    ->select(DB::raw('SUM(Amount) as total'), DB::raw('CONCAT("₪ ", FORMAT(SUM(Amount), 2)) as formatTotal'), DB::raw('DATE_FORMAT(UserDate, \'%Y-%m\') as date'))
    ->where('CompanyNum', $rest->CompanyNum)
    ->whereIn('TypeHeader', [305, 330])
    ->where('UserDate', '>=', DB::raw('DATE_FORMAT(CURDATE(), \'%Y-%m-01\') - INTERVAL 3 MONTH'))
    ->groupBy(DB::raw('MONTH(UserDate)'))
    ->groupBy(DB::raw('YEAR(UserDate)'));

$rest->answer->items = $q->get();
