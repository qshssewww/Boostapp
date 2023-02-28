<?php
$range = !empty($_REQUEST['filter']['range'])?$_REQUEST['filter']['range']:'LastPayment';
$dateFrom = !empty($_REQUEST['filter']['dateFrom']) ? DB::raw("'" . $_REQUEST['filter']['dateFrom'] . "'") : DB::raw('DATE_ADD(CURDATE(), INTERVAL -1 MONTH)');
$dateTo = !empty($_REQUEST['filter']['dateTo']) ? DB::raw("'" . $_REQUEST['filter']['dateTo'] . "'") : DB::raw('CURDATE()');

$q = DB::table('paytoken')
        ->where('paytoken.CompanyNum', '=', $rest->CompanyNum)
        ->where('paytoken.Status', '=', 0)
        ->whereBetween('paytoken.'.$range, [$dateFrom, $dateTo])
        ->leftJoin('client', function ($join) use ($rest) {
            $join
                ->on('client.CompanyNum', '=', 'paytoken.CompanyNum')
                ->on('client.id', '=', 'paytoken.ClientId');
        })
        ->leftJoin('brands', function ($join) use ($rest) {
            $join
                ->on('brands.CompanyNum', '=', 'paytoken.CompanyNum')
                ->on('brands.id', '=', 'paytoken.Brands');
        })
        ->leftJoin('items', function ($join) use ($rest) {
            $join
                ->on('items.CompanyNum', '=', 'paytoken.CompanyNum')
                ->on('items.id', '=', 'paytoken.ItemId');
        })
        ->leftJoin('users', function ($join) use ($rest) {
            $join
                ->on('users.CompanyNum', '=', 'paytoken.CompanyNum')
                ->on('users.id', '=', 'paytoken.UserId');
        })
        ->leftJoin('token', function ($join) use ($rest) {
            $join
                ->on('token.CompanyNum', '=', 'paytoken.CompanyNum')
                ->on('token.id', '=', 'paytoken.TokenId');
        })
        ->select(
            'client.id as clientId',
            'client.CompanyName as clientFullName',
            // 'client.FirstName as clientFirstName',
            // 'client.LastName as clientLastName',
            'client.ContactMobile as clientPhone',
            'items.ItemName as productName',
            DB::raw('IF(paytoken.Brands=0, "סניף ראשי", brands.BrandName) as branchName'),
            DB::raw("
                CASE paytoken.TypePayment
                    WHEN 1 THEN 'יומי'
                    WHEN 2 THEN 'שבועי'
                    WHEN 3 THEN 'חודשי'
                    WHEN 4 THEN 'שנתי'
                END as paymentType
            "),
            'paytoken.Amount as paymentAmount',
            'paytoken.NumPayment as paymentNum',
            'paytoken.CountPayment as paymentPaid',
            'token.L4digit as paymentlastFourDigits',
            DB::raw('IF(paytoken.Status=0, "פעיל", "לא פעיל") as paymentStatus'), // JS time multiply by 1000
            DB::raw('UNIX_TIMESTAMP(paytoken.LastPayment)*1000 as paymentLastUnix'), // JS time multiply by 1000
            DB::raw('UNIX_TIMESTAMP(paytoken.NextPayment)*1000 as paymentNextUnix'), // JS time multiply by 1000
            DB::raw('UNIX_TIMESTAMP(paytoken.Dates)*1000 as paymentInit'), // JS time multiply by 1000
            'users.display_name as agentFullName',
            'users.FirstName as agentFirstName',
            'users.LastName as agentLastName'
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
    // filter productName
    if(!empty($_REQUEST['filter']['productName']) && $_REQUEST['filter']['productName'] != ''){
        $q
            ->where('items.ItemName', 'LIKE', '"'.$_REQUEST['filter']['productName'].'%"');
    }
    // filter paymentAmount
    if(!empty($_REQUEST['filter']['paymentAmount']) && $_REQUEST['filter']['paymentAmount'] != ''){
        $q
            ->where('paytoken.Amount', '=', $_REQUEST['filter']['paymentAmount']);
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

    // filter paymentType
    if(!empty($_REQUEST['filter']['paymentType']) && $_REQUEST['filter']['paymentType'] != ''){
        $q
            ->where('paytoken.TypePayment', '=', (int) $_REQUEST['filter']['paymentType']);
    }

    // filter paymentNum
    if(!empty($_REQUEST['filter']['paymentNum']) && $_REQUEST['filter']['paymentNum'] != ''){
        $q
            ->where('paytoken.NumPayment', '=', (int) $_REQUEST['filter']['paymentNum']);
    }

    

    
    

$allItems = DB::select($q->toString());
$rest->answer->recordsFiltered = COUNT($allItems);
$rest->answer->totalSum = 0;
foreach($allItems as $item){
    $rest->answer->totalSum += (float) $item->paymentAmount;
}


// sort by column
if (!empty($_GET['order'][0]['column'])) {
    $sortName = $_GET['columns'][(int) $_GET['order'][0]['column']]['name'];
    $dir = $_GET['order'][0]['dir'];

    switch ($sortName) {
        case "paymentLast":$q->orderBy('paytoken.LastPayment', $dir); break;
        case "paymentNext":$q->orderBy('paytoken.NextPayment', $dir); break;
        case "clientFullName":$q->orderBy('client.CompanyName', $dir); break;
        case "clientPhone":$q->orderBy('client.ContactMobile', $dir); break;
        case "productName":$q->orderBy('items.ItemName', $dir); break;
        case "paymentAmount":$q->orderBy('paytoken.Amount', $dir); break;
        case "paymentlastFourDigits":$q->orderBy('token.L4digit', $dir); break;
        case "branchName":$q->orderBy('brands.BrandName', $dir); break;
        case "paymentType":$q->orderBy('paytoken.TypePayment', $dir); break;
        case "paymentPaid":$q->orderBy('paytoken.NumPayment', $dir); break;

    }
}



    $limit = (!empty($_GET['length']) && is_numeric($_GET['length'])) ? (int) $_GET['length'] : 100;
    $start = (!empty($_GET['start']) && is_numeric($_GET['start'])) ? (int) $_GET['start'] : 0;
    
    $q->limit($limit)->offset($start);



$items =  DB::select($q->toString());
$rest->answer->items = $items;
$rest->answer->recordsTotal = COUNT($items);
$rest->answer->sql = $q->toString();