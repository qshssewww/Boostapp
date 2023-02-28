<?php
$q = DB::table('classstudio_act')
    ->select(DB::raw('  classstudio_act.ClassName as className,
    client.id as clientId,
            classstudio_act.ClassDate,
            classstudio_act.ClassStartTime,
            users.display_name as instructureName,
            CONCAT(client.FirstName, \' \', client.LastName) as fullName,
            client.FirstName as firstName,
            client.FirstName as lastName,
            client.ContactMobile as phone,
            client.Email as email ,
            sections.Title as classLocation
        '))
    ->join('client', 'client.id', '=', 'classstudio_act.ClientId')
    ->join('users', 'users.id', '=', 'classstudio_act.GuideId')

    ->join('sections', 'sections.id', '=', 'classstudio_act.FloorId')

    ->where('classstudio_act.CompanyNum', $rest->CompanyNum)
    ->where('classstudio_act.StatusCount', '0')
    ->groupBy('ClientId');

    if (!empty($_GET['filter']['dateFrom']) && $_GET['filter']['dateFrom'] != "") {
        $fromDate = $_GET['filter']['dateFrom'];
        if (!empty($_GET['filter']['dateTo']) && $_GET['filter']['dateTo'] != "") {
            $toDate = $_GET['filter']['dateTo'];
        }
    }

    if (!empty($fromDate) && !empty($toDate)) {
        $q->whereRaw(DB::raw("`classstudio_act`.`ClassDate` between '$fromDate' and '$toDate'"));
    } elseif (!empty($fromDate)) {
        $q->whereRaw(DB::raw("`classstudio_act`.`ClassDate` >= '$fromDate'"));
    } elseif (!empty($fromDate)) {
        $q->whereRaw(DB::raw("`classstudio_act`.`ClassDate` <= $fromDate"));
    } else {
        $q->whereRaw(DB::raw("`classstudio_act`.`ClassDate` between CURDATE() and CURDATE()"));
    }

    


// sort by column
if (!empty($_GET['order'][0]['column'])) {
    $sortName = $_GET['columns'][(int) $_GET['order'][0]['column']]['name'];
    $dir = $_GET['order'][0]['dir'];

    switch ($sortName) {
        case "fullName":$q->orderBy('client.FirstName', $dir);
            break;
        case "email":$q->orderBy('client.Email', $dir);
            break;
        case "phone":$q->orderBy('client.ContactMobile', $dir);
            break;
        case "className":$q->orderBy('classstudio_act.ClassName', $dir);
            break;
        case "instructureName":$q->orderBy('users.display_name', $dir);
            break;
        case "ClassDate":$q->orderBy('classstudio_act.ClassDate', $dir);
            break;
        case "ClassStartTime":$q->orderBy('classstudio_act.ClassStartTime', $dir);
            break;
        case "classLocation":$q->orderBy('sections.Title', $dir);
            break;
    }
}

$sql = sprintf("select * from (%s) as tbl", $q->toString());
$rest->answer->recordsTotal = COUNT(DB::select($sql));
$filter = false;

// search by name
if (!empty($_GET['filter']['name']) && $_GET['filter']['name'] != "") {  
    $sql .= (($filter) ? " AND " : " WHERE ") . "(tbl.firstName LIKE '" . $_GET['filter']['name'] . "%' OR tbl.lastName LIKE '" . $_GET['filter']['name'] . "%' OR CONCAT(tbl.firstName, ' ', tbl.lastName) LIKE '".$_GET['filter']['name']."%')";
    $filter = true;
}

// search by phone
if (!empty($_GET['filter']['phone']) && $_GET['filter']['phone'] != "") {
    $sql .= (($filter) ? " AND " : " WHERE ") . "tbl.phone LIKE '" . $_GET['filter']['phone'] . "%'";
    $filter = true;
}

// search by email
if (!empty($_GET['filter']['email']) && $_GET['filter']['email'] != "") {
    $sql .= (($filter) ? " AND " : " WHERE ") . "tbl.email LIKE '" . $_GET['filter']['email'] . "%'";
    $filter = true;
}

// search by className
if (!empty($_GET['filter']['className']) && $_GET['filter']['className'] != "") {
    $sql .= (($filter) ? " AND " : " WHERE ") . "tbl.ClassName LIKE '" . $_GET['filter']['className'] . "%'";
    $filter = true;
}

// search by instructureName
if (!empty($_GET['filter']['instructureName']) && $_GET['filter']['instructureName'] != "") {
    $sql .= (($filter) ? " AND " : " WHERE ") . "tbl.instructureName LIKE '" . $_GET['filter']['instructureName'] . "%'";
    $filter = true;
}

// search by classLocation
if (!empty($_GET['filter']['classLocation']) && $_GET['filter']['classLocation'] != "") {
    $sql .= (($filter) ? " AND " : " WHERE ") . "tbl.classLocation LIKE '" . $_GET['filter']['classLocation'] . "%'";
    $filter = true;
}

// search by time
if (!empty($_GET['filter']['ClassStartTime']) && $_GET['filter']['ClassStartTime'] != "") {
    $sql .= (($filter) ? " AND " : " WHERE ") . "tbl.ClassStartTime = TIME_FORMAT('".$_GET['filter']['ClassStartTime']."', '%H:%i:%s')";
    $filter = true;
}

$results = DB::select($sql);
// $rest->answer->qyery = $sql;
$rest->answer->recordsFiltered = COUNT($results);
$rest->answer->items = $results;
