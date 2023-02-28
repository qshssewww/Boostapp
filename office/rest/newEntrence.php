<?php

$query = "
SELECT 
    IF(SUM(newEntrence.newEntrence),SUM(newEntrence.newEntrence),0) as count 
FROM 
    (SELECT
         COUNT(*) as newEntrence, 
         classstudio_act.ClassDate 
     FROM 
        classstudio_act 
     WHERE
        classstudio_act.Status = 11 and 
        classstudio_act.ClassDate BETWEEN (CURDATE() - INTERVAL 30 DAY) and CURDATE() and 
        classstudio_act.CompanyNum = $rest->CompanyNum 
     GROUP BY 
        classstudio_act.ClassDate
    ) as newEntrence
";

$q = DB::select($query);
$rest->answer->items = $q;