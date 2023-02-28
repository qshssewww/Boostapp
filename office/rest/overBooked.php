<?php

if(!empty($_GET['details'])){
    $classId = $_GET['details'];
    $sql = "
    SELECT * FROM (
        SELECT 
            classstudio_act.ClassName as className,
            sections.Title  as classRoomName,
            DATE_FORMAT(
                classstudio_act.ClassDate,
                '%d/%m/%Y'
            ) AS displayDate,
            DATE_FORMAT(
                classstudio_act.ClassDate,
                '%Y-%m-%d'
            ) AS date,
            DATE_FORMAT(classstudio_act.ClassStartTime, '%H:%i') as classTime,
            classstudio_date.GuideName  as guideName,
            client.id as clientId,
            client.CompanyName as fullName,
            client.ContactMobile as phone,
            client.Email as email,
            IF(client.Gender=2, \"נקבה\", IF(client.Gender=1, \"זכר\", \"לא ידוע\")) as gender,
            client.Age as age
        FROM `classstudio_act` 
        LEFT JOIN classstudio_date ON classstudio_act.ClassId = classstudio_date.id AND classstudio_date.CompanyNum = classstudio_act.CompanyNum
        LEFT JOIN sections ON sections.id = classstudio_date.Floor AND sections.CompanyNum = classstudio_act.CompanyNum
        LEFT JOIN client ON client.id = classstudio_act.ClientId AND client.CompanyNum = classstudio_act.CompanyNum
        WHERE 
            classstudio_act.ClassId = $classId AND 
            classstudio_act.StatusCount = 1 and 
            classstudio_act.CompanyNum = $rest->CompanyNum
        ORDER BY classstudio_act.WatingListSort asc, classstudio_act.id asc
    ) as tbl
    ";

    $items = DB::select($sql);
    // General class information
    if(count($items)){
        $rest->answer->class = array(
            "className"=>$items[0]->className,
            "classRoomName"=> $items[0]->classRoomName,
            "classTime"=> $items[0]->classTime,
            "displayDate"=> $items[0]->displayDate,
            "date"=> $items[0]->date,
            "guideName"=> $items[0]->guideName
        );
    }


    // clean up rows from junk data (save bandwidth)
    foreach ($items as &$item) {
        unset($item->className);
        unset($item->classRoomName);
        unset($item->classTime);
        unset($item->displayDate);
        unset($item->date);
        unset($item->guideName);
    }

    $rest->answer->items = $items;

    exit;
}


$dateFrom = !empty($_REQUEST['filter']['dateFrom']) ? DB::raw("'" . $_REQUEST['filter']['dateFrom'] . "'") : DB::raw('CURDATE()');
$dateTo = !empty($_REQUEST['filter']['dateTo']) ? DB::raw("'" . $_REQUEST['filter']['dateTo'] . "'") : DB::raw('DATE_ADD(CURDATE(), INTERVAL +1 DAY)');

$sql = "
SELECT
    *
FROM
    (
    SELECT
        classstudio_act.ClassName AS className,
        classstudio_act.ClassId AS classId,
        IF(
            classstudio_act.Brands = 0,
            'סניף ראשי',
            brands.BrandName
        ) AS branchName,
        DATE_FORMAT(
            classstudio_act.ClassDate,
            '%d/%m/%Y'
        ) AS displayDate,
        DATE_FORMAT(
            classstudio_act.ClassDate,
            '%Y-%m-%d'
        ) AS date,
        DATE_FORMAT(classstudio_act.ClassStartTime, '%H:%i') as classTime,
        COUNT(*) AS overBooked,
        classstudio_date.GuideName  as guideName,
        sections.Title  as classRoomName,
        classstudio_date.id as classDateId
    FROM
        `classstudio_act`
    LEFT JOIN classstudio_date ON classstudio_date.id = classstudio_act.ClassId
    LEFT JOIN brands ON brands.CompanyNum = classstudio_act.CompanyNum AND brands.id = classstudio_act.Brands
    LEFT JOIN sections ON sections.id = classstudio_date.Floor
    WHERE
        classstudio_act.CompanyNum = $rest->CompanyNum AND classstudio_act.ClassDate BETWEEN $dateFrom AND $dateTo
        AND CONCAT(
            classstudio_act.ClassDate,
            ' ',
            classstudio_act.ClassStartTime
        ) > CURRENT_TIMESTAMP AND classstudio_act.StatusCount = 1
    GROUP BY
        classstudio_act.ClassId
) AS tbl
WHERE
    tbl.overBooked > 2
";


$filter = true;
if (!empty($_GET['filter']['branch']) && $_GET['filter']['branch'] != "") {
    $sql .= (($filter) ? " AND " : " WHERE ") . "tbl.branchName = '" . $_GET['filter']['branch'] . "'";
    $filter = true;
}
if (!empty($_GET['filter']['classTime']) && $_GET['filter']['classTime'] != "") {
    $sql .= (($filter) ? " AND " : " WHERE ") . "tbl.classTime = '" . $_GET['filter']['classTime'] . "'";
    $filter = true;
}
if (!empty($_GET['filter']['className']) && $_GET['filter']['className'] != "") {
    $sql .= (($filter) ? " AND " : " WHERE ") . "tbl.className = '" . $_GET['filter']['className'] . "'";
    $filter = true;
}

// total amount without filter
if(!empty($_GET['length']) && !empty($_GET['start'])){
    $rest->answer->recordsFiltered = COUNT(DB::select($query));

    $limit = (!empty($_GET['length']) && is_numeric($_GET['length'])) ? (int) $_GET['length'] : 100;
    $start = (!empty($_GET['start']) && is_numeric($_GET['start'])) ? (int) $_GET['start'] : 0;
    
    $sql .= " LIMIT $limit OFFSET $start";
}

$items = DB::select($sql);

$rest->answer->recordsFiltered = $rest->answer->recordsTotal = COUNT($items);
// $rest->answer->sql = str_replace(array("\n", "\r"), ' ', $sql);
$rest->answer->items = $items;
$rest->answer->today = date('Y-m-d');
$rest->answer->tommorow = date('Y-m-d', strtotime('+1 day'));