<?php



$dateFrom = !empty($_REQUEST['filter']['dateFrom']) ? DB::raw("'" . $_REQUEST['filter']['dateFrom'] . "'") : DB::raw('DATE_FORMAT(CURDATE(), "%Y-%m-01")');
$dateTo = !empty($_REQUEST['filter']['dateTo']) ? DB::raw("'" . $_REQUEST['filter']['dateTo'] . "'") : DB::raw('CURDATE()');

$dateFromNonAttendance = !empty($_REQUEST['filter']['dateFromNonAttendance']) ? DB::raw("'" . $_REQUEST['filter']['dateFromNonAttendance'] . "'") : DB::raw('DATE_ADD(CURDATE(), INTERVAL(1-DAYOFWEEK(CURDATE())) DAY)'); // last sunday
$dateToNonAttendance = !empty($_REQUEST['filter']['dateToNonAttendance']) ? DB::raw("'" . $_REQUEST['filter']['dateToNonAttendance'] . "'") : DB::raw('CURDATE()');

$query = "
    select
        `client`.`id` as `clientId`,
        `client`.`CompanyName` as `fullName`,
        `client`.`ContactMobile` as `phone`,
        DATE_FORMAT(`client`.`LastClassDate`, '%d/%m/%Y') as lastClassDate,
        `client`.`LastClassDate` as rawLastClassDate,
        CONCAT(JSON_UNQUOTE(JSON_EXTRACT(MemberShipText, '$.data[0].ItemText')), ' ', DATE_FORMAT(JSON_UNQUOTE(JSON_EXTRACT(MemberShipText, '$.data[0].TrueDate')), '%d/%m/%Y'), ' ', JSON_UNQUOTE(JSON_EXTRACT(MemberShipText, '$.data[0].TrueBalanceValue'))) AS membership,
        CONCAT(DATE_FORMAT($dateFrom, '%d/%m/%Y'), ' - ', DATE_FORMAT($dateTo, '%d/%m/%Y')) as dateRange,
        (SELECT COUNT(*) FROM classstudio_act WHERE `classstudio_act`.`ClientId` = `client`.`id` AND `classstudio_act`.`CompanyNum` = `client`.`CompanyNum` AND `classstudio_act`.`ClassDate` >= CURDATE()) as futureClasses
    from
        `client`
    where
        `client`.`CompanyNum` = $rest->CompanyNum and
        `client`.`Status` = 0 and
        `client`.`LastClassDate` between $dateFrom and $dateTo and
        `client`.id NOT IN (
                SELECT IF(TrueClientId=0, clientId, TrueClientId) as clientId FROM `classstudio_act`
                WHERE
                    (classstudio_act.ClassDate between $dateFromNonAttendance and $dateToNonAttendance) AND
                    classstudio_act.StatusCount = 0 and
                    `CompanyNum` = $rest->CompanyNum
                GROUP BY ClientId, TrueClientId
        )
";

// search by name
if (!empty($_GET['filter']['name']) && $_GET['filter']['name'] != "") {
    $query .= " AND (client.firstName LIKE '" . $_GET['filter']['name'] . "%' OR client.lastName LIKE '" . $_GET['filter']['name'] . "%' OR client.CompanyName LIKE '" . $_GET['filter']['name'] . "%')";
}

// search by phone
if (!empty($_GET['filter']['phone']) && $_GET['filter']['phone'] != "") {
    $query .= " AND client.ContactMobile LIKE '" . $_GET['filter']['phone'] . "%'";
}


$query = sprintf("SELECT * FROM (%s) as tbl", $query);

// search by member
$filter = false;
if (!empty($_GET['filter']['member']) && $_GET['filter']['member'] != "") {
    $query .= ($filter ? (' AND ') : (' WHERE ') ) ." tbl.membership LIKE '" . $_GET['filter']['member'] . "%'";
    $filter = true;
}

// search by futureClasses
if (!empty($_GET['filter']['futureClasses']) && $_GET['filter']['futureClasses'] != "") {

    switch($_GET['filter']['futureClasses']){
        case "false": $query .= ($filter ? (' AND ') : (' WHERE ') ) ." tbl.futureClasses = 0"; $filter = true; break;
        case "true": $query .= ($filter ? (' AND ') : (' WHERE ') ) ." tbl.futureClasses > 0"; $filter = true; break;
    }
    
}




// sort by column
if (!empty($_GET['order'][0]['column'])) {
    $sortName = $_GET['columns'][(int) $_GET['order'][0]['column']]['name'];
    $dir = $_GET['order'][0]['dir'];

    switch ($sortName) {
        case "dateRange":$query .= " ORDER BY tbl.dateRange $dir ";
            break;
        case "fullName":$query .= " ORDER BY tbl.fullName $dir ";
            break;
        case "phone":$query .= " ORDER BY tbl.phone $dir ";
            break;
        case "member":$query .= " ORDER BY tbl.membership $dir ";
            break;
        case "lastClass":$query .= " ORDER BY tbl.lastClassDate $dir ";
            break;
        case "futureClasses":$query .= " ORDER BY tbl.futureClasses $dir ";
            break;
    }
}

if(!empty($_GET['dashboard']) && $_GET['dashboard'] === "true"){
    $query .= " ORDER BY tbl.fullName ASC ";
}

$rest->answer->recordsFiltered = COUNT(DB::select($query));

$limit = (!empty($_GET['length']) && is_numeric($_GET['length'])) ? (int) $_GET['length'] : 100;
$start = (!empty($_GET['start']) && is_numeric($_GET['start'])) ? (int) $_GET['start'] : 0;

$query .= " LIMIT $limit OFFSET $start";

$q = DB::select($query);
$rest->answer->recordsTotal = COUNT($q);
// $rest->answer->sql = $query;
$rest->answer->items = $q;


if(!empty($_GET['dashboard']) && $_GET['dashboard'] === "true"){
    $rest->answer->last30Days = array();
    for($i = 0; $i < 30; $i++) 
        $rest->answer->last30Days[] = date("Y-m-d", strtotime('-'. $i .' days'));
    
    $rest->answer->last7Days = array();
        for($i = 0; $i < 7; $i++) 
            $rest->answer->last7Days[] = date("Y-m-d", strtotime('-'. $i .' days'));
}