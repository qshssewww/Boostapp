<?php

$query = "
    SELECT
    d.*,
    e.*,
    ROUND(d.total/e.entrence, 1) as avgIncome
    FROM `client`

        LEFT JOIN (
            SELECT SUM(docs.Amount) as total FROM `docs`
            WHERE
                docs.TypeHeader IN (400)
                and docs.UserDate BETWEEN (CURDATE() - INTERVAL 30 DAY) AND CURDATE()
                and docs.CompanyNum=$rest->CompanyNum
        ) as d ON d.total = d.total

        LEFT JOIN (
            SELECT COUNT(*) as entrence from classstudio_act
            WHERE
                classstudio_act.ClassDate BETWEEN (CURDATE() - INTERVAL 30 DAY) AND CURDATE()
                    and classstudio_act.StatusCount = '0'
                    and classstudio_act.CompanyNum=$rest->CompanyNum
        ) as e ON 1=1

    WHERE client.CompanyNum=$rest->CompanyNum LIMIT 1
";
$q = DB::select($query);
$rest->answer->items = $q;
