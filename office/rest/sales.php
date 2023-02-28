<?php

$time_start = microtime(true); 
$dateFrom = !empty($_REQUEST['filter']['dateFrom']) ? DB::raw("'" . $_REQUEST['filter']['dateFrom'] . "'") : DB::raw('DATE_FORMAT(CURDATE(), "%Y-%m-01")');
$dateTo = !empty($_REQUEST['filter']['dateTo']) ? DB::raw("'" . $_REQUEST['filter']['dateTo'] . "'") : DB::raw('CURDATE()');

$q = DB::table('docs')
    ->select(
        DB::raw('DATE_FORMAT(docs.UserDate, "%d/%m/%Y") as date'),
        DB::raw("
            CASE items.Department
                WHEN 1 THEN 'מנוי תקופתי'
                WHEN 2 THEN 'כרטיסייה'
                WHEN 3 THEN 'מנוי התנסות'
                WHEN 4 THEN 'פריט כללי לרכישה'
            END as productType
        "),
        DB::raw('IF(docs.Brands=0, "סניף ראשי", brands.BrandName) as brunchName'),
        'client.CompanyName as fullName',
        'client.FirstName as firstName',
        'client.LastName as lastName',
        'items.ItemName as product',
        'client.id as clientId',
        'membership_type.Type as memberType',
        'docs.Amount as amount'

    )
    ->where('docs.CompanyNum', '=', $rest->CompanyNum)
    ->where('docs.TypeHeader', '=', 400)
    ->whereBetween('docs.UserDate', array($dateFrom, $dateTo))
    ->leftJoin('docs2item', function ($join) use ($rest) {
        $join
            ->on('docs2item.CompanyNum', '=', DB::raw($rest->CompanyNum))
            ->on('docs2item.DocsId', '=', 'docs.id');
    })
    ->leftJoin('items', function ($join) use ($rest) {
        $join
            ->on('items.CompanyNum', '=', DB::raw($rest->CompanyNum))
            ->on('docs2item.ItemId', '=', 'items.id');
    })
    ->leftJoin('client', function ($join) use ($rest) {
        $join
            ->on('client.CompanyNum', '=', DB::raw($rest->CompanyNum))
            ->on('client.id', '=', 'docs.ClientId');
    })
    ->leftJoin('membership_type', function ($join) use ($rest) {
        $join
            ->on('membership_type.CompanyNum', '=', DB::raw($rest->CompanyNum))
            ->on('membership_type.id', '=', 'items.MemberShip');
    })
    ->leftJoin('brands', function ($join) use ($rest) {
        $join
            ->on('brands.CompanyNum', '=', DB::raw($rest->CompanyNum))
            ->on('brands.id', '=', 'docs.Brands');
    })
;

// sort by column
if (!empty($_GET['order'][0]['column'])) {
    $sortName = $_GET['columns'][(int) $_GET['order'][0]['column']]['name'];
    $dir = $_GET['order'][0]['dir'];

    switch ($sortName) {
        case "date":$q->orderBy('docs.UserDate', $dir);
            break;
        case "product":$q->orderBy('items.ItemName', $dir);
            break;
        case "productType":$q->orderBy('items.Department', $dir);
            break;
        case "brunchName":$q->orderBy('brands.BrandName', $dir);
            break;
        case "fullName":$q->orderBy('client.CompanyName', $dir);
            break;
        case "memberType":$q->orderBy('membership_type.Type', $dir);
            break;
    }
}




$filter = false;
$sql = sprintf("select * from (%s) as tbl", $q->toString());

$rest->answer->productTypes = DB::select(sprintf("select productType as value from (%s) as tbl GROUP BY productType", $q->toString()));
$rest->answer->brunchNames = DB::select(sprintf("select brunchName as value from (%s) as tbl GROUP BY brunchName", $q->toString()));
$rest->answer->memberTypes = DB::select(sprintf("select memberType as value from (%s) as tbl GROUP BY memberType", $q->toString()));

$rest->answer->recordsFiltered = COUNT(DB::select($sql));

// search by productType
if (!empty($_GET['filter']['productType']) && $_GET['filter']['productType'] != "") {
    $sql .= (($filter) ? " AND " : " WHERE ") . "tbl.productType LIKE '" . $_GET['filter']['productType'] . "%'";
    $filter = true;
}

// search by product
if (!empty($_GET['filter']['product']) && $_GET['filter']['product'] != "") {
    $sql .= (($filter) ? " AND " : " WHERE ") . "tbl.product LIKE '" . $_GET['filter']['product'] . "%'";
    $filter = true;
}
// search by amount
if (!empty($_GET['filter']['amount']) && $_GET['filter']['amount'] != "") {
    $sql .= (($filter) ? " AND " : " WHERE ") . "ABS(tbl.amount) = '" . $_GET['filter']['amount'] . "'";
    $filter = true;
}

// search by brunchName
if (!empty($_GET['filter']['brunchName']) && $_GET['filter']['brunchName'] != "") {
    $sql .= (($filter) ? " AND " : " WHERE ") . "tbl.brunchName LIKE '" . $_GET['filter']['brunchName'] . "%'";
    $filter = true;
}

// search by fullName
if (!empty($_GET['filter']['fullName']) && $_GET['filter']['fullName'] != "") {
    $sql .= (($filter) ? " AND " : " WHERE ") . "tbl.fullName LIKE '" . $_GET['filter']['fullName'] . "%' OR tbl.lastName LIKE '".$_GET['filter']['fullName']."%' ";
    $filter = true;
}

// search by memberType
if (!empty($_GET['filter']['memberType']) && $_GET['filter']['memberType'] != "") {
    $sql .= (($filter) ? " AND " : " WHERE ") . "tbl.memberType LIKE '" . $_GET['filter']['memberType'] . "%'";
    $filter = true;
}

$limit = (!empty($_GET['length']) && is_numeric($_GET['length'])) ? (int) $_GET['length'] : 100;
$start = (!empty($_GET['start']) && is_numeric($_GET['start'])) ? (int) $_GET['start'] : 0;
$rest->answer->recordsFiltered = isset($filter) && $filter ? COUNT(DB::select($sql)) : $rest->answer->recordsFiltered;
$sql .= " LIMIT $limit OFFSET $start";

$results = DB::select($sql);

$rest->answer->recordsTotal = COUNT($results);
$rest->answer->items = $results;
$rest->answer->sql = $sql;
$time_end = microtime(true);
$rest->answer->time = ($time_end - $time_start)/60;



