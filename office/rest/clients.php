<?php

$q = DB::table('client_activities')->select(
    //'client_activities.ItemId',
    'items.ItemName as product',

    //'client_activities.MemberShip',
    'membership_type.Type as memberType',

    //'client_activities.Department',
    DB::raw("
        CASE items.Department
            WHEN 1 THEN 'מנוי תקופתי'
            WHEN 2 THEN 'כרטיסייה'
            WHEN 3 THEN 'מנוי התנסות'
            WHEN 4 THEN 'פריט כללי לרכישה'
        END as department
    "),


    //DB::raw('IF(client_activities.TrueClientId=0, client_activities.clientId, client_activities.TrueClientId) as clientId'),
    'client.id as clientId',
    'client.CompanyName as fullName',

    DB::raw('IF(items.Department IN (2,3), client_activities.BalanceValue, null) as ticket'),
    DB::raw('IF(items.Department IN (2,3), client_activities.TrueBalanceValue, null) as ticketLeft'),
    DB::raw('DATE_FORMAT(client_activities.TrueDate, "%d/%m/%Y") as ticketExp'),
    DB::raw('DATE_FORMAT(client_activities.Dates, "%d/%m/%Y") as date'),

    //'client_activities.UserId as userId'
    'users.display_name as agentName',

    //'client.Brands as brunchId',
    DB::raw('IF(client.Brands=0, "סניף ראשי", brands.BrandName) as brunchName')
)
->where('client_activities.CompanyNum', '=', $rest->CompanyNum)
->leftJoin('items', function ($join) use ($rest) {
    $join
        ->on('items.CompanyNum', '=', DB::raw($rest->CompanyNum))
        ->on('client_activities.ItemId', '=', 'items.id');
})
->leftJoin('membership_type', function ($join) use ($rest) {
    $join
        ->on('membership_type.CompanyNum', '=', DB::raw($rest->CompanyNum))
        ->on('membership_type.id', '=', 'client_activities.MemberShip');
})
->leftJoin('client', function ($join) use ($rest) {
    $join
        ->on('client.CompanyNum', '=', DB::raw($rest->CompanyNum))
        ->on('client.id', '=', DB::raw('IF(client_activities.TrueClientId=0, client_activities.clientId, client_activities.TrueClientId)'));
})
->leftJoin('users', function ($join) use ($rest) {
    $join
        ->on('users.CompanyNum', '=', DB::raw($rest->CompanyNum))
        ->on('users.id', '=', 'client_activities.UserId');
})
->leftJoin('brands', function ($join) use ($rest) {
    $join
        ->on('brands.CompanyNum', '=', DB::raw($rest->CompanyNum))
        ->on('brands.id', '=', 'client.Brands');
});

// sort by date or ticket expire not both
if(!empty($_REQUEST['filter']['dateFilter'])){
    switch($_REQUEST['filter']['dateFilter']){
        case "ticketExp": 
            $dateFrom = !empty($_REQUEST['filter']['dateFrom']) ? DB::raw("'" . $_REQUEST['filter']['dateFrom'] . "'") : DB::raw('DATE_FORMAT(CURDATE(), "%Y-%m-01")');
            $dateTo = !empty($_REQUEST['filter']['dateTo']) ? DB::raw("'" . $_REQUEST['filter']['dateTo'] . "'") : DB::raw('CURDATE()');
            $q->whereBetween(DB::raw('client_activities.TrueDate'), array($dateFrom, $dateTo));
        break;
        default:
            $dateFrom = !empty($_REQUEST['filter']['dateFrom']) ? DB::raw("'" . $_REQUEST['filter']['dateFrom'] . "'") : DB::raw('DATE_FORMAT(CURDATE(), "%Y-%m-01")');
            $dateTo = !empty($_REQUEST['filter']['dateTo']) ? DB::raw("'" . $_REQUEST['filter']['dateTo'] . "'") : DB::raw('CURDATE()');
            $q->whereBetween(DB::raw('DATE_FORMAT(client_activities.Dates, "%Y-%m-%d")'), array($dateFrom, $dateTo));
        break;
        
    }
}else{
    $dateFrom = !empty($_REQUEST['filter']['dateFrom']) ? DB::raw("'" . $_REQUEST['filter']['dateFrom'] . "'") : DB::raw('DATE_FORMAT(CURDATE(), "%Y-%m-01")');
    $dateTo = !empty($_REQUEST['filter']['dateTo']) ? DB::raw("'" . $_REQUEST['filter']['dateTo'] . "'") : DB::raw('CURDATE()');
    $q->whereBetween(DB::raw('DATE_FORMAT(client_activities.Dates, "%Y-%m-%d")'), array($dateFrom, $dateTo));
}





$rest->answer->memberTypes = DB::select(sprintf("select memberType as value from (%s) as tbl GROUP BY tbl.memberType ORDER BY tbl.memberType", $q->toString()));
$rest->answer->departments = DB::select(sprintf("select department as value from (%s) as tbl GROUP BY tbl.department ORDER BY tbl.department", $q->toString()));


// sort by column
if (!empty($_GET['order'][0]['column'])) {
    $sortName = $_GET['columns'][(int) $_GET['order'][0]['column']]['name'];
    $dir = $_GET['order'][0]['dir'];

    switch ($sortName) {
        case "date":$q->orderBy('client_activities.Dates', $dir);
            break;
        case "memberType":$q->orderBy('membership_type.Type', $dir);
            break;
        case "department":$q->orderBy('items.Department', $dir);
            break;
        case "fullName":$q->orderBy('client.CompanyName', $dir);
            break;
        case "ticket":$q->orderBy('client_activities.TrueBalanceValue', $dir);
            break;
        case "ticketLeft":$q->orderBy('client_activities.BalanceValue', $dir);
            break;
        case "ticketExp":$q->orderBy('client_activities.TrueDate', $dir);
            break;
        case "agentName":$q->orderBy('users.display_name', $dir);
            break;
        case "brunchName":$q->orderBy('client.Brands', $dir);
            break;
    }
}



$rest->answer->agentNames = DB::select(sprintf("select productType as value from (%s) as tbl GROUP BY agentName", $q->toString()));
$rest->answer->brunchNames = DB::select(sprintf("select brunchName as value from (%s) as tbl GROUP BY brunchName", $q->toString()));
$rest->answer->departments = DB::select(sprintf("select department as value from (%s) as tbl GROUP BY department", $q->toString()));
$rest->answer->agentNames = DB::select(sprintf("select agentName as value from (%s) as tbl GROUP BY agentName", $q->toString()));

$filter = false;
$sql = sprintf("select * from (%s) as tbl", $q->toString());
$rest->answer->recordsFiltered = COUNT(DB::select($sql));


$fillters = array(
    'memberType'=>array(), 
    'department'=>array(), 
    'fullName'=>array(), 
    'ticket'=>array(), 
    'ticketLeft'=>array(), 
    'ticketExp'=>array(), 
    'agentName'=>array(), 
    'brunchName'=>array(),
    'product'=>array()
);

foreach ($fillters as $key => $options) {

    // if($key === 'ticketLeft' && !empty($_GET['filter'][$key]) && $_GET['filter'][$key] != ""){
    //     // when searching ticket only in the valid department
    //     $sql .= (($filter) ? " AND " : " WHERE ") . "tbl.".$key." <= '" . $_GET['filter'][$key] . "' AND items.Department IN (2,3)";
    //     $filter = true;
    //     continue;
    // }

    if($key === 'ticketExp' && !empty($_GET['filter'][$key]) && $_GET['filter'][$key] != ""){
        $sql .= (($filter) ? " AND " : " WHERE ") . "tbl.".$key." <= '" . $_GET['filter'][$key] . "'";
        $filter = true;
        continue;
    }


    if (!empty($_GET['filter'][$key]) && $_GET['filter'][$key] != "") {
        $sql .= (($filter) ? " AND " : " WHERE ") . "tbl.".$key." LIKE '" . $_GET['filter'][$key] . "%'";
        $filter = true;
    }
}

$limit = (!empty($_GET['length']) && is_numeric($_GET['length'])) ? (int) $_GET['length'] : 100;
$start = (!empty($_GET['start']) && is_numeric($_GET['start'])) ? (int) $_GET['start'] : 0;
$rest->answer->recordsFiltered = isset($filter) && $filter ? COUNT(DB::select($sql)) : $rest->answer->recordsFiltered;
$sql .= " LIMIT $limit OFFSET $start";

$results = DB::select($sql);

$rest->answer->recordsTotal = COUNT($results);
$rest->answer->items = $results;
// $rest->answer->sql = $sql;