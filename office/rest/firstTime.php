<?php
// search from prevvous date and not future logic
$dateFrom = !empty($_REQUEST['filter']['dateFrom']) ? DB::raw("'" . $_REQUEST['filter']['dateFrom'] . "'") : DB::raw('DATE_ADD(CURDATE(), INTERVAL -1 DAY)');
$dateTo = !empty($_REQUEST['filter']['dateTo']) ? DB::raw("'" . $_REQUEST['filter']['dateTo'] . "'") : DB::raw('DATE_ADD(CURDATE(), INTERVAL 1 DAY)');

$sql = "
    SELECT
        `classstudio_act`.`ClassName` AS `className`,
        `sections`.`Title` AS `classRoomName`,
        `classstudio_date`.`GuideName` AS `guideName`,
        `classstudio_act`.`ClassId` AS `classId`,
        IF(
            `classstudio_act`.`Brands` = 0,
            'סניף ראשי',
            `brands`.`BrandName`
        ) AS `branchName`,
        DATE_FORMAT(
            `classstudio_act`.`ClassDate`,
            '%d/%m/%Y'
        ) AS `displayDate`,
        `classstudio_act`.`ClassDate` AS `date`,
        DATE_FORMAT(
            `classstudio_act`.`ClassStartTime`,
            '%H:%i'
        ) AS `classTime`,
        `client`.`id` AS `clientId`,
        `client`.`CompanyName` AS `fullName`,
        `client`.`FirstName` AS `firstName`,
        `client`.`LastName` AS `lastName`,
        `client`.`ContactMobile` AS `phone`,
        `client`.`Email` AS `email`
    FROM
        `classstudio_act`
    LEFT JOIN `client` ON
        (
            IF(
                `classstudio_act`.`TrueClientId` = 0,
                `classstudio_act`.`ClientId`,
                `classstudio_act`.`TrueClientId`
            )
        ) = `client`.`id`
    LEFT JOIN `brands` ON `brands`.`CompanyNum` = `classstudio_act`.`CompanyNum` AND `brands`.`id` = `classstudio_act`.`Brands`
    LEFT JOIN `classstudio_date` ON `classstudio_date`.`id` = `classstudio_act`.`ClassId` AND `classstudio_date`.`CompanyNum` = `classstudio_act`.`CompanyNum`
    LEFT JOIN `sections` ON `sections`.`id` = `classstudio_date`.`Floor` AND `sections`.`CompanyNum` = `classstudio_act`.`CompanyNum`
    WHERE
        `classstudio_act`.`CompanyNum` = $rest->CompanyNum AND 
        `classstudio_act`.`FirstClass` = 1 AND
        `classstudio_act`.`ClassDate` BETWEEN $dateFrom AND $dateTo 
";



$sql = sprintf("SELECT * FROM (%s) as tbl", str_replace(array("\n", "\r", "\t"), ' ', $sql));

$filter = false;
if(!empty($_POST['filter']['classTime']) && $_POST['filter']['classTime'] !== ''){
    $sql .= (($filter) ? " AND " : " WHERE "). "tbl.classTime = \"".$_POST['filter']['classTime']."\"";
    $filter = true;
}
if(!empty($_POST['filter']['name']) && $_POST['filter']['name'] !== ''){
    $name = $_POST['filter']['name'];
    $sql .= (($filter) ? " AND " : " WHERE "). "(tbl.firstName LIKE '$name%' OR tbl.lastName LIKE '$name%')";
    $filter = true;
}
if(!empty($_POST['filter']['email']) && $_POST['filter']['email'] !== ''){
    $sql .= (($filter) ? " AND " : " WHERE "). "tbl.email LIKE \"".$_POST['filter']['email']."%\"";
    $filter = true;
}
if(!empty($_POST['filter']['phone']) && $_POST['filter']['phone'] !== ''){
    $sql .= (($filter) ? " AND " : " WHERE "). "tbl.phone LIKE \"".$_POST['filter']['phone']."%\"";
    $filter = true;
}
if(!empty($_POST['filter']['branchName']) && $_POST['filter']['branchName'] !== ''){
    $sql .= (($filter) ? " AND " : " WHERE "). "tbl.branchName = \"".$_POST['filter']['branchName']."\"";
    $filter = true;
}

if(!empty($_POST['filter']['guideName']) && $_POST['filter']['guideName'] !== ''){
    $sql .= (($filter) ? " AND " : " WHERE "). "tbl.guideName = \"".$_POST['filter']['guideName']."\"";
    $filter = true;
}

if(!empty($_POST['filter']['className']) && $_POST['filter']['className'] !== ''){
    $sql .= (($filter) ? " AND " : " WHERE "). "tbl.className = \"".$_POST['filter']['className']."\"";
    $filter = true;
}

if(!empty($_POST['filter']['classRoomName']) && $_POST['filter']['classRoomName'] !== ''){
    $sql .= (($filter) ? " AND " : " WHERE "). "tbl.classRoomName LIKE \"".$_POST['filter']['classRoomName']."%\"";
    $filter = true;
}

// sort by column
if (!empty($_GET['order'][0]['column'])) {
    $sortName = $_GET['columns'][(int) $_GET['order'][0]['column']]['name'];
    $dir = $_GET['order'][0]['dir'];

    switch ($sortName) {
        case "date": $sql .= " ORDER BY tbl.date $dir "; break;
        case "classTime": $sql .= " ORDER BY tbl.classTime $dir "; break;
        case "fullName": $sql .= " ORDER BY tbl.fullName $dir "; break;
        case "email": $sql .= " ORDER BY tbl.email $dir "; break;
        case "phone": $sql .= " ORDER BY tbl.phone $dir "; break;
        case "branchName": $sql .= " ORDER BY tbl.branchName $dir "; break;
        case "guideName": $sql .= " ORDER BY tbl.guideName $dir "; break;
        case "className": $sql .= " ORDER BY tbl.className $dir "; break;
        case "classRoomName": $sql .= " ORDER BY tbl.classRoomName $dir "; break;
    }
}



// total amount without filter
$rest->answer->recordsFiltered = COUNT(DB::select($sql));


if(!empty($_GET['length']) && !empty($_GET['start'])){

    $limit = (!empty($_GET['length']) && is_numeric($_GET['length'])) ? (int) $_GET['length'] : 100;
    $start = (!empty($_GET['start']) && is_numeric($_GET['start'])) ? (int) $_GET['start'] : 0;
    
    $sql .= " LIMIT $limit OFFSET $start";
}

$items = DB::select($sql);

$rest->answer->recordsTotal = COUNT($items);
$rest->answer->sql = str_replace(array("\n", "\r", "\t"), ' ', $sql);
$rest->answer->items = $items;
$rest->answer->today = date('Y-m-d');
$rest->answer->yestarday = date('Y-m-d', strtotime('-1 day'));
$rest->answer->tommorow = date('Y-m-d', strtotime('1 day'));