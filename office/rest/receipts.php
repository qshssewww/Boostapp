<?php

// $dateFrom = !empty($_REQUEST['dateFrom']) ? DB::raw("'" . $_REQUEST['dateFrom'] . "'") : DB::raw('DATE_FORMAT(CURDATE(), "%Y-%m-01")');
$dateFrom = !empty($_REQUEST['filter']['dateFrom']) ? DB::raw("'" . $_REQUEST['filter']['dateFrom'] . "'") : DB::raw('CURDATE()');
$dateTo = !empty($_REQUEST['filter']['dateTo']) ? DB::raw("'" . $_REQUEST['filter']['dateTo'] . "'")  : DB::raw('CURDATE()');

$q = DB::table('docs_payment')
        ->select(
            'docs_payment.UserDate as date',
            // DB::raw("
            // CASE docs_payment.TypePayment
            //     WHEN 1 THEN 'מזומן'
            //     WHEN 2 THEN 'המחאה'
            //     WHEN 3 THEN 'כרטיס אשראי'
            //     WHEN 4 THEN 'העברה בנקאית'
            //     WHEN 5 THEN 'תווי קנייה'
            //     WHEN 6 THEN 'שובר החלפה'
            //     WHEN 7 THEN 'שטר חוב'
            //     WHEN 8 THEN 'הוראת קבע בנקאית'
            //     WHEN 9 THEN 'אחר'
            //     ELSE 'אחר'
            // END as paymentType
            // "),
            // 'docs_payment.TypePayment as TypePayment',
            // DB::raw("
            // CASE docs_payment.Bank
            //     WHEN 6 THEN 'לאומי קארד'
            //     WHEN 4 THEN 'אמריקן אקספרס'
            //     WHEN 3 THEN 'דיינרס'
            //     WHEN 2 THEN 'וויזה כאל'
            //     WHEN 1 THEN 'ישרכארט'
            //     ELSE NULL
            // END as processor
            // "),
            'docs_payment.Amount as amount',
            DB::raw('IF(docs_payment.Brands=0, "סניף ראשי", brands.BrandName) as branchName'),
            'docs_payment.Brands as branchId'

        )
        ->whereRaw(DB::raw("(docs_payment.CompanyNum = $rest->CompanyNum OR docs_payment.TrueCompanyNum = $rest->CompanyNum)"))
        ->whereBetween(DB::raw('DATE_FORMAT(docs_payment.UserDate, "%Y-%m-%d")'), array($dateFrom, $dateTo))
        ->leftJoin('brands', function ($join) use ($rest) {
            $join
                ->on('brands.CompanyNum', '=', DB::raw($rest->CompanyNum))
                ->on('brands.id', '=', 'docs_payment.Brands');
        })
        ->groupBy('docs_payment.UserDate')
        ->groupBy('docs_payment.Brands')
        ->toString()
        // ->groupBy('docs_payment.TypePayment')
        ;

        // $rest->answer->items = DB::select($q->toString());
        // $rest->answer->sql = $q->toString();


$cash = DB::table('docs_payment')
->select(DB::raw('IF(SUM(docs_payment.Amount), SUM(docs_payment.Amount), 0) as total'))
            ->whereRaw(DB::raw("(docs_payment.CompanyNum = $rest->CompanyNum OR docs_payment.TrueCompanyNum = $rest->CompanyNum)"))
            ->where('docs_payment.TypePayment', '=', '1')
            ->where('docs_payment.UserDate', '=', 'tbl.date')
            ->where('docs_payment.Brands', '=', 'tbl.branchId')
            ->toString();
 
$check = DB::table('docs_payment')
->select(DB::raw('IF(SUM(docs_payment.Amount), SUM(docs_payment.Amount), 0) as total'))
->whereRaw(DB::raw("(docs_payment.CompanyNum = $rest->CompanyNum OR docs_payment.TrueCompanyNum = $rest->CompanyNum)"))
->where('docs_payment.TypePayment', '=', '2')
->where('docs_payment.UserDate', '=', 'tbl.date')
->where('docs_payment.Brands', '=', 'tbl.branchId')
->toString();  

$credit = DB::table('docs_payment')
->select(DB::raw('IF(SUM(docs_payment.Amount), SUM(docs_payment.Amount), 0) as total'))
->whereRaw(DB::raw("(docs_payment.CompanyNum = $rest->CompanyNum OR docs_payment.TrueCompanyNum = $rest->CompanyNum)"))
->where('docs_payment.TypePayment', '=', '3')
->where('docs_payment.UserDate', '=', 'tbl.date')
->where('docs_payment.Brands', '=', 'tbl.branchId')
->toString();   

$bank = DB::table('docs_payment')
->select(DB::raw('IF(SUM(docs_payment.Amount), SUM(docs_payment.Amount), 0) as total'))
->whereRaw(DB::raw("(docs_payment.CompanyNum = $rest->CompanyNum OR docs_payment.TrueCompanyNum = $rest->CompanyNum)"))
->where('docs_payment.TypePayment', '=', '4')
->where('docs_payment.UserDate', '=', 'tbl.date')
->where('docs_payment.Brands', '=', 'tbl.branchId')
->toString();          

$sql = "
        SELECT *, (
            CAST(t.cash AS DECIMAL(10,2)) +
            CAST(t.checks AS DECIMAL(10,2)) +
            CAST(t.creditCards AS DECIMAL(10,2)) +
            CAST(t.bank AS DECIMAL(10,2)) 
            ) as total FROM (SELECT 
            DATE_FORMAT(tbl.date, '%d/%m/%Y') as date,
            tbl.branchName branch,
            tbl.branchId branchId,
            ($cash) as cash,
            ($check) as checks,
            ($credit) as creditCards,
            ($bank) as bank
        FROM ($q) as tbl) as t
";
$filter = false;
if (!empty($_GET['filter']['branch']) && $_GET['filter']['branch'] != "") {
    $sql .= (($filter) ? " AND " : " WHERE ") . "t.branch = '" . $_GET['filter']['branch'] . "'";
    $filter = true;
}


if (!empty($_GET['order'][0]['column'])) {
    $sortName = $_GET['columns'][(int) $_GET['order'][0]['column']]['name'];
    $dir = $_GET['order'][0]['dir'];
    switch($sortName){
        case "branch": $sql .= " ORDER BY t.branch $dir "; break;
        case "date": $sql .= " ORDER BY t.date $dir "; break;
        case "cash": $sql.= " ORDER BY t.cash $dir "; break;
        case "checks": $sql.= " ORDER BY t.checks $dir "; break;
        case "creditCards": $sql.= " ORDER BY t.creditCards $dir "; break;
        case "bank": $sql.= " ORDER BY t.bank $dir "; break;
        case "total": $sql.= " ORDER BY total $dir "; break;
    }
}

// $rest->answer->sql = $sql;
$items = DB::Select($sql);

$cash = 0;
$checks = 0;
$creditCards = 0;
$bank = 0;
$total = 0;

foreach ($items as $item) {
   $cash += (float) $item->cash;
   $checks += (float) $item->checks;
   $creditCards += (float) $item->creditCards;
   $bank += (float) $item->bank;
   $total += (float) $item->total;
}

$t = new stdClass();
$t->date = lang('total');
$t->branch = '';
$t->cash = number_format($cash, 2, '.', '');
$t->checks = number_format($checks, 2, '.', '');
$t->creditCards = number_format($creditCards, 2, '.', '');
$t->bank = number_format($bank, 2, '.', '');
$t->total = number_format($total, 2, '.', '');


 $items[] = $t;
$rest->answer->items = $items;
$rest->answer->sql = $sql;
$rest->answer->recordsFiltered = $rest->answer->recordsTotal = COUNT($rest->answer->items);

