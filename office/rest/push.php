<?php
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
        COUNT(*) AS registered,
        classstudio_date.MaxClient AS maxClass,
        (classstudio_date.MaxClient-COUNT(*)) as spacesLeft,
        sections.Title  as classRoomName,
        classstudio_date.GuideName as guideName,
        classstudio_date.id as classDateId
    FROM
        `classstudio_act`
    LEFT JOIN classstudio_date ON classstudio_date.id = classstudio_act.ClassId
    LEFT JOIN brands ON brands.CompanyNum = classstudio_act.CompanyNum AND brands.id = classstudio_act.Brands
    LEFT JOIN sections ON sections.id = classstudio_date.Floor AND sections.CompanyNum = classstudio_act.CompanyNum
    WHERE
        classstudio_act.CompanyNum = $rest->CompanyNum AND classstudio_act.ClassDate BETWEEN $dateFrom AND $dateTo
        AND CONCAT(
            classstudio_act.ClassDate,
            ' ',
            classstudio_act.ClassStartTime
        ) > CURRENT_TIMESTAMP AND classstudio_act.StatusCount = 0
    GROUP BY
        classstudio_act.ClassId
) AS tbl
WHERE
    (tbl.maxClass/2) <= tbl.registered AND tbl.spacesLeft > 0;
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
if (!empty($_GET['filter']['className']) && $_GET['filter']['className'] != "") {
    $sql .= (($filter) ? " AND " : " WHERE ") . "tbl.className = '" . $_GET['filter']['className'] . "'";
    $filter = true;
}
if (!empty($_GET['filter']['classLocation']) && $_GET['filter']['classLocation'] != "") {
    $sql .= (($filter) ? " AND " : " WHERE ") . "tbl.classLocation = '" . $_GET['filter']['classLocation'] . "'";
    $filter = true;
}
if (!empty($_GET['filter']['guideName']) && $_GET['filter']['guideName'] != "") {
    $sql .= (($filter) ? " AND " : " WHERE ") . "tbl.guideName = '" . $_GET['filter']['guideName'] . "'";
    $filter = true;
}

// sort by column
if (!empty($_GET['order'][0]['column'])) {
    $sortName = $_GET['columns'][(int) $_GET['order'][0]['column']]['name'];
    $dir = $_GET['order'][0]['dir'];

    switch ($sortName) {
        case "date":$sql .= " ORDER BY tbl.displayDate $dir ";
            break;
        case "branch":$sql .= " ORDER BY tbl.branchName $dir ";
            break;
        case "classLocation":$sql .= " ORDER BY tbl.classLocation $dir ";
            break;
        case "classTime":$sql .= " ORDER BY tbl.classTime $dir ";
            break;
        case "className":$sql .= " ORDER BY tbl.className $dir ";
            break;
        case "guideName":$sql .= " ORDER BY tbl.guideName $dir ";
            break;
        case "spacesLeft":$sql .= " ORDER BY tbl.spacesLeft $dir ";
            break;
    }
}

//

// total amount without filter
if (!empty($_GET['length']) && !empty($_GET['start'])) {
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