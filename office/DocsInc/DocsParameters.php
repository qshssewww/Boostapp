<?php
if(Auth::guest()) {
    exit;
}
$CompanyNum = Auth::user()->CompanyNum;
$Types = @$_REQUEST['Types'];
$DocsTablesHeader = DB::table('docstable')->where('CompanyNum','=',$CompanyNum)->where('TypeHeader','=',@$Types)->where('Status','=','0')->first();
$typeTitleArr = array(
    "0" => lang('settings_bids'),
    "200" => lang('Shipping_documents'),
    "100" => lang('docs_orders'),
    "305" => lang('tax_invoice'),
    "320" => lang('Tax_invoices_receipts'),
    "310" => lang('concentration_invoices'),
    "330" => lang('credit_tax_invoices'),
    "400" => lang('receipts'),
    "300" => lang('transaction_invoices'),
    "210" => lang('return_certificates'),
    "1" => lang('manual_tax_invoices'),
    "2" => lang('refund_receipt')
);

$typeTitleSingleArr = array(
    "0" => lang('settings_bid'),
    "200" => lang('shipping_document'),
    "100" => lang('docs_order'),
    "305" => lang('tax_invoice_single'),
    "320" => lang('Tax_invoice_receipt'),
    "310" => lang('concentration_invoice'),
    "330" => lang('credit_tax_invoice'),
    "400" => lang('receipt'),
    "300" => lang('transaction_invoice_singe'),
    "210" => lang('return_certificates'),
    "1" => lang('manual_tax_invoice'),
    "2" => lang('refund_receipt')
);

$TypeTitle = @$typeTitleArr[$DocsTablesHeader->TypeHeader];
$TypeTitleSingle = @$typeTitleSingleArr[$DocsTablesHeader->TypeHeader];
$TypeList = lang('archive'). ' ' .@$typeTitleArr[$DocsTablesHeader->TypeHeader];
$TypeNew = @$typeTitleSingleArr[$DocsTablesHeader->TypeHeader];
$PaymentRole = @$DocsTablesHeader->PaymentRole;
$Remarks = @$DocsTablesHeader->Remarks;
$PaymentBalance = @$DocsTablesHeader->PaymentBalance;
$Payment = @$DocsTablesHeader->Payment;
$ShowSelect = @$DocsTablesHeader->ShowSelect;



if (!isset($_REQUEST["month"])) $_REQUEST["month"] = date("m");
if (!isset($_REQUEST["year"])) $_REQUEST["year"] = date("Y");

if (@$_REQUEST['Dates']==''){

$cMonth = $_REQUEST["month"];
$cYear = $_REQUEST["year"];
$Dates = $_REQUEST["year"].'-'.$_REQUEST["month"];
}

else {

$Dates = $_REQUEST['Dates'];
$cMonth = with(new DateTime($_REQUEST['Dates']))->format('m');
$cYear = with(new DateTime($_REQUEST['Dates']))->format('Y');	
	
}
 
$prev_year = $cYear;
$next_year = $cYear;
$prev_month = $cMonth-1;
$next_month = $cMonth+1;
 
if ($prev_month == 0 ) {
    $prev_month = 12;
    $prev_year = $cYear - 1;
}
if ($next_month == 13 ) {
    $next_month = 1;
    $next_year = $cYear + 1;
}

$StartDate = $cYear.'-'.$cMonth.'-01';
$EndDate = $cYear.'-'.$cMonth.'-'.date('t',strtotime($StartDate));


@$SearchValue = @$_GET['SearchValue'];
@$Act = @$_GET['Act'];



if ((@$Act == 'Search') && (@$SearchValue != '')) {
$DocGets = DB::table('docs')->where('TypeDoc','=', @$DocsTablesHeader->id)->where('CompanyNum','=',$CompanyNum)->where('Company', 'LIKE', "%$SearchValue%")->ORwhere('TypeNumber', $SearchValue)->where('CompanyNum','=',$CompanyNum)->orderBy('id', 'ASC')->get();
$DocSum = DB::table('docs')->where('TypeDoc','=', @$DocsTablesHeader->id)->where('CompanyNum','=',$CompanyNum)->where('Company', 'LIKE', "%$SearchValue%")->ORwhere('TypeNumber', $SearchValue)->where('CompanyNum','=',$CompanyNum)->orderBy('id', 'ASC')->sum('Amount');
$DocCount = count($DocGets);
}
else {
$DocGets = DB::table('docs')->where('TypeDoc','=', @$DocsTablesHeader->id)->where('CompanyNum','=',$CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->get();
$DocSum = DB::table('docs')->where('TypeDoc','=', @$DocsTablesHeader->id)->where('CompanyNum','=',$CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->sum('Amount');
$DocCount = count($DocGets);
}
?>
