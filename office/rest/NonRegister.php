<?php

$dateFromNonAttendance = !empty($_REQUEST['filter']['dateFromNonAttendance']) ? DB::raw("'" . $_REQUEST['filter']['dateFromNonAttendance'] . "'") : DB::raw('DATE_FORMAT(CURDATE(), "%Y-%m-01")');
$dateToNonAttendance = !empty($_REQUEST['filter']['dateToNonAttendance']) ? DB::raw("'" . $_REQUEST['filter']['dateToNonAttendance'] . "'") : DB::raw('CURDATE()');

$query = "
    select
        `client`.`id` as `clientId`,
        `client`.`CompanyName` as `fullName`,
        `client`.`ContactMobile` as `phone`,
        DATE_FORMAT(`client`.`LastClassDate`, '%d/%m/%Y') as lastClassDate,
        (SELECT GROUP_CONCAT(membership_type.Type SEPARATOR ', ') as type
            FROM client_activities LEFT JOIN membership_type ON membership_type.id=client_activities.MemberShip WHERE client_activities.ClientId = client.id AND client_activities.Status = 0 AND client_activities.Freez != 1)  as type,
        (SELECT GROUP_CONCAT(items.ItemName SEPARATOR ', ') as membership 
            FROM client_activities LEFT JOIN items ON items.id=client_activities.ItemId WHERE client_activities.ClientId = client.id AND client_activities.Status = 0 AND client_activities.Freez != 1)  as membership,
        (SELECT COUNT(*) FROM classstudio_act WHERE classstudio_act.ClientId = client.id AND classstudio_act.CompanyNum = client.CompanyNum AND classstudio_act.ClassDate >= CURDATE()) as clientFutureClassCount
    from
        `client`
    where
        `CompanyNum` = $rest->CompanyNum and
        `Status` = 0 and
        client.id NOT IN (
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
    $query .= " AND (client.firstName LIKE '" . $_GET['filter']['name'] . "%' OR client.lastName LIKE '" . $_GET['filter']['name'] . "%' OR CONCAT(client.firstName, ' ',client.lastName) LIKE '" . $_GET['filter']['name'] . "%')";
}

// search by phone
if (!empty($_GET['filter']['phone']) && $_GET['filter']['phone'] != "") {
    $query .= " AND client.ContactMobile LIKE '" . $_GET['filter']['phone'] . "%'";
}



if (!empty($_GET['filter']['lastClassStart']) && $_GET['filter']['lastClassStart'] != "") {
    
    $query .= "AND client.LastClassDate between '".$_GET['filter']['lastClassStart']."' and '".$_GET['filter']['lastClassEnd']."'";
    
//    $query->whereBetween(DB::raw('client.LastClassDate'), array("'".$_GET['filter']['lastClassStart']."'", "'".$_GET['filter']['lastClassEnd']."'"));
}



$query = sprintf("SELECT * FROM (%s) as tbl", $query);
$where = false;


if (!empty($_GET['filter']['futureClasses']) && $_GET['filter']['futureClasses'] === "true") {
    $query .= (($where==true)?' AND':' WHERE'). ' (tbl.clientFutureClassCount >= 1)';
    $where = true;
}

if (!empty($_GET['filter']['futureClasses']) && $_GET['filter']['futureClasses'] === "false") {
    $query .= (($where==true)?' AND':' WHERE') . ' (tbl.clientFutureClassCount <= 0 OR tbl.clientFutureClassCount IS NULL)';
    $where = true;
}

// search by member
// if (!empty($_GET['filter']['member']) && $_GET['filter']['member'] != "" && !is_array($_GET['filter']['member'])) {

//     foreach($_GET['filter']['member'] as $keyword){
//         if($keyword === "NULL"){
//             $query .= (($where==true)?' OR':' WHERE'). "  tbl.membership IS NULL ";
//             $where = true;
//             continue;
//         }
//         $query .= (($where==true)?' OR':' WHERE'). "  tbl.membership LIKE '%" . $keyword . "%' ";
//         $where = true;
//     }
// }

if (!empty($_GET['filter']['type']) && $_GET['filter']['type'] != "") {
    $query .= (($where==true)?' AND':' WHERE'). "  tbl.type LIKE '" . $_GET['filter']['type'] . "%'";
    $where = true;
}




if(isset($_GET['filter']['member']) && is_array($_GET['filter']['member'])){
    array_walk($_GET['filter']['member'], function(&$x) {$x = "'$x'";});

    $query .= (($where==true)?' AND':' WHERE'). "  tbl.membership IN (".implode(',', $_GET['filter']['member']).")";
    $where = true;
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
        case "type":$query .= " ORDER BY tbl.type $dir ";
            break;
        case "member":$query .= " ORDER BY tbl.membership $dir ";
            break;
        case "lastClass":$query .= " ORDER BY tbl.lastClassDate $dir ";
            break;
    }
}

$rest->answer->recordsFiltered = COUNT(DB::select($query));

$limit = (!empty($_GET['length']) && is_numeric($_GET['length'])) ? (int) $_GET['length'] : 100;
$start = (!empty($_GET['start']) && is_numeric($_GET['start'])) ? (int) $_GET['start'] : 0;

$query .= " LIMIT $limit OFFSET $start";

$q = DB::select($query);
$rest->answer->recordsTotal = COUNT($q);
$rest->answer->sql = $query;
$rest->answer->items = $q;
