<?php



$dateFrom = !empty($_REQUEST['filter']['dateFrom']) ? DB::raw("'" . $_REQUEST['filter']['dateFrom'] . "'") : DB::raw('DATE_FORMAT((CURDATE() - INTERVAL 1 Month), "%Y-%m-01")');
$dateTo = !empty($_REQUEST['filter']['dateTo']) ? DB::raw("'" . $_REQUEST['filter']['dateTo'] . "'") : DB::raw('CURDATE()');

$query = "
SELECT 
    client.id as clientId,
    CONCAT(client.FirstName, ' ', client.LastName) as fullName,
    client.Email as email,
    client.ContactMobile as mobile,
    IF(client.Gender = 0, 'לא ידוע', IF(client.Gender = 1, 'זכר', 'נקבה')) as gender,
    totalClient.amount as totalClient, 
    leftClient.amount as leftClient,
    ROUND((leftClient.amount/totalClient.amount), 2) as percentLeft,
    DATE_FORMAT(client.ArchiveDate, '%d/%m/%Y') as date,
    IF(client.Brands=0, 'סניף ראשי', brands.BrandName) as branchName
FROM 
    client
LEFT JOIN 
    (SELECT COUNT(*) as amount FROM `client` WHERE client.CompanyNum = $rest->CompanyNum and client.Status IN (1,0)) as totalClient On 1 = 1
LEFT JOIN 
    (SELECT COUNT(*) as amount FROM `client` WHERE client.CompanyNum = $rest->CompanyNum and client.Status IN (1)) as leftClient On 1 = 1
LEFT JOIN brands
    ON brands.CompanyNum = $rest->CompanyNum AND brands.id = client.Brands
WHERE
    client.CompanyNum = $rest->CompanyNum and 
    client.Status IN (1) 
    and (DATE_FORMAT(client.ArchiveDate, '%Y-%m-%d') BETWEEN $dateFrom and $dateTo)
";

// 

// search by name
if (!empty($_GET['filter']['name']) && $_GET['filter']['name'] != "") {
    $query .= " AND (client.firstName LIKE '" . $_GET['filter']['name'] . "%' OR client.lastName LIKE '" . $_GET['filter']['name'] . "%')";
}

// search by phone
if (!empty($_GET['filter']['phone']) && $_GET['filter']['phone'] != "") {
    $query .= " AND client.ContactMobile LIKE '" . $_GET['filter']['phone'] . "%'";
}

// search by email
if (!empty($_GET['filter']['email']) && $_GET['filter']['email'] != "") {
    $query .= " AND client.Email LIKE '" . $_GET['filter']['email'] . "%'";
}

// search by gender
if (isset($_GET['filter']['gender']) && $_GET['filter']['gender'] != "") {
    $query .= " AND client.Gender = '" . $_GET['filter']['gender'] . "'";
}
// search by branch
if (isset($_GET['filter']['branch']) && $_GET['filter']['branch'] != "") {
    $query .= " AND brands.BrandName = '" . $_GET['filter']['branch'] . "'";
}

// search by date
//if (( !empty($_GET['filter']['dateFrom']) && $_GET['filter']['dateFrom'] != "" ) && ( !empty($_GET['filter']['dateTo']) && $_GET['filter']['dateTo'] != "" ) ) {
 //   $query .= " AND client.Dates BETWEEN '".$_GET['filter']['dateFrom']."' and '".$_GET['filter']['dateTo']."' ";
//}

// sort by column
if (!empty($_GET['order'][0]['column'])) {
    $sortName = $_GET['columns'][(int) $_GET['order'][0]['column']]['name'];
    $dir = $_GET['order'][0]['dir'];

    switch ($sortName) {
        case "date": $query .= " ORDER BY client.ArchiveDate $dir";
            break;
            case "fullName": $query .= " ORDER BY client.FirstName $dir ";
                break;
        case "email": $query .= " ORDER BY client.Email $dir ";
            break;
        case "phone": $query .= " ORDER BY client.ContactMobile $dir ";
            break;
        case "date": $query .= " ORDER BY client.Dates $dir ";
            break;
    }
}
$rest->answer->recordsFiltered = COUNT(DB::select($query));

$limit = (!empty($_GET['length']) && is_numeric($_GET['length'])) ? (int) $_GET['length'] : 100;
$start = (!empty($_GET['start']) && is_numeric($_GET['start'])) ? (int) $_GET['start'] : 0;

$query .= " LIMIT $limit OFFSET $start";

$q = DB::select($query);
$rest->answer->recordsTotal = COUNT($q);
// $rest->answer->sql = $query;
$rest->answer->items = $q;
$rest->answer->query = $query;