<?php
$dateFrom = !empty($_REQUEST['filter']['dateFrom']) ? DB::raw("'" . $_REQUEST['filter']['dateFrom'] . "'") : DB::raw('DATE_FORMAT(CURDATE(), "%Y-%m-01")');
$dateTo = !empty($_REQUEST['filter']['dateTo']) ? DB::raw("'" . $_REQUEST['filter']['dateTo'] . "'") : DB::raw('CURDATE()');

$q = DB::table('client_activities')
        ->where('client_activities.Freez', '=', '1')
        ->where('client_activities.EndFreez', '<=', $dateTo)
        ->where('client_activities.StartFreez', '<=', $dateFrom)
        ->where('client_activities.CompanyNum', '=', $rest->CompanyNum)

        ->leftJoin('client', function ($join) use ($rest) {
            $join
                ->on('client.CompanyNum', '=', 'client_activities.CompanyNum')
                ->on('client.id', '=', 'client_activities.ClientId');
        })
        ->leftJoin('brands', function ($join) use ($rest) {
            $join
                ->on('brands.CompanyNum', '=', 'client_activities.CompanyNum')
                ->on('brands.id', '=', 'client_activities.Brands');
        })
        ->leftJoin('items', function ($join) use ($rest) {
            $join
                ->on('items.CompanyNum', '=', 'client_activities.CompanyNum')
                ->on('items.id', '=', 'client_activities.ItemId');
        })

        ->select(
            'client.id as clientId',
            'client.CompanyName as clientFullName',
            // 'client.FirstName as clientFirstName',
            // 'client.LastName as clientLastName',
            'client.ContactMobile as clientPhone',
            'client.email as clientEmail',
            'items.ItemName as productName',
            DB::raw('IF(client_activities.Brands=0, "סניף ראשי", brands.BrandName) as branchName'),
            'client_activities.FreezDays as frezeDays',
            DB::raw('CONCAT(DATE_FORMAT(client_activities.StartFreez, "%d/%m/%Y") ," - ", DATE_FORMAT(client_activities.EndFreez, "%d/%m/%Y")) as frezeDates')
        )
    ;

    // filter name
    if(!empty($_REQUEST['filter']['clientFullName']) && $_REQUEST['filter']['clientFullName'] != ''){
        $q
            ->where('client.CompanyName', 'LIKE', '"'.$_REQUEST['filter']['clientFullName'].'%"')
            ->orWhere('client.LastName', 'LIKE', '"'.$_REQUEST['filter']['clientFullName'].'%"');
    }
    // filter phone
    if(!empty($_REQUEST['filter']['clientPhone']) && $_REQUEST['filter']['clientPhone'] != ''){
        $q
            ->where('client.ContactMobile', 'LIKE', '"'.$_REQUEST['filter']['clientPhone'].'%"');
    }
    // filter email
    if(!empty($_REQUEST['filter']['clientEmail']) && $_REQUEST['filter']['clientEmail'] != ''){
        $q
            ->where('client.email', 'LIKE', '"'.$_REQUEST['filter']['clientEmail'].'%"');
    }
    // filter productName
    if(!empty($_REQUEST['filter']['productName']) && $_REQUEST['filter']['productName'] != ''){
        $q
            ->where('items.ItemName', 'LIKE', '"'.$_REQUEST['filter']['productName'].'%"');
    }
 
    // filter branchName
    if(!empty($_REQUEST['filter']['branchName']) && $_REQUEST['filter']['branchName'] != ''){

        if($_REQUEST['filter']['branchName'] == 'סניף ראשי'){
            $q
                ->where('paytoken.Brands', '=', '0');
        }else{
            $q
                ->where('brands.BrandName', '=', $_REQUEST['filter']['branchName']);
        }
    }  


    

    
    

$allItems = DB::select($q->toString());
$rest->answer->recordsFiltered = COUNT($allItems);

// sort by column
if (!empty($_GET['order'][0]['column'])) {
    $sortName = $_GET['columns'][(int) $_GET['order'][0]['column']]['name'];
    $dir = $_GET['order'][0]['dir'];

    switch ($sortName) {
        case "paymentLast":$q->orderBy('paytoken.LastPayment', $dir); break;
        case "paymentNext":$q->orderBy('paytoken.NextPayment', $dir); break;
        case "clientFullName":$q->orderBy('client.CompanyName', $dir); break;
        case "clientPhone":$q->orderBy('client.ContactMobile', $dir); break;
        case "clientEmail":$q->orderBy('client.email', $dir); break;
        case "productName":$q->orderBy('items.ItemName', $dir); break;
        case "branchName":$q->orderBy('brands.BrandName', $dir); break;
    }
}



    $limit = (!empty($_GET['length']) && is_numeric($_GET['length'])) ? (int) $_GET['length'] : 100;
    $start = (!empty($_GET['start']) && is_numeric($_GET['start'])) ? (int) $_GET['start'] : 0;
    
    $q->limit($limit)->offset($start);



$items =  DB::select($q->toString());
$rest->answer->items = $items;
$rest->answer->recordsTotal = COUNT($items);
// $rest->answer->sql = $q->toString();