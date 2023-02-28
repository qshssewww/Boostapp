<?php require_once '../../app/init.php'; ?>

<?php

$TypeId = $_REQUEST['TypeId'];
$DocId = $_REQUEST['DocId'];
$TypeHeader = $_REQUEST['TypeHeader'];
$Action = $_REQUEST['Action'];
$CompanyNum = Auth::user()->CompanyNum;
$UserId = Auth::user()->id;
$Dates = date('Y-m-d H:i:s');

$DocGet = DB::table('docs')->where('CompanyNum' ,'=', $CompanyNum)->where('TypeNumber','=',$DocId)->where('TypeDoc','=',$TypeId)->first();
$DocsTables = DB::table('docstable')->where('CompanyNum' ,'=', $CompanyNum)->where('id','=',$DocGet->TypeDoc)->first();


if ($Action=='305'){
$TrueTypeHeader = '305';   
$DocAction = '7';
}
else if ($Action=='320'){
$TrueTypeHeader = '320';     
$DocAction = '7';    
}
else if ($Action=='300'){
$TrueTypeHeader = '300';     
$DocAction = '7';    
}

$TempId = DB::table('temp')->insertGetId(
array('CompanyNum' => $CompanyNum, 'TypeDoc' => $TrueTypeHeader, 'ClientId' => $DocGet->ClientId, 'Dates' => $Dates, 'UserId' => $UserId, 'Vat' => $DocGet->Vat, 'Discount' => $DocGet->Discount, 'DocsId' => $DocGet->id, 'ActDocs' => $DocAction)
);


$DocListGets = DB::table('docslist')->where('CompanyNum' ,'=', $CompanyNum)->where('DocsId','=',$DocGet->id)->get();
foreach ($DocListGets as $DocListGet){

DB::table('templist')->insertGetId(
array('CompanyNum' => $CompanyNum, 'TypeDoc' => $TrueTypeHeader, 'TempId' => $TempId, 'ItemId' => $DocListGet->ItemId, 'SKU' => $DocListGet->SKU, 'ItemName' => $DocListGet->ItemName, 'ItemPrice' => $DocListGet->ItemPrice, 'ItemPriceVat' => $DocListGet->ItemPriceVat, 'ItemPriceVatDiscount' => $DocListGet->ItemPriceVatDiscount, 'ItemQuantity' => $DocListGet->ItemQuantity, 'ItemDiscountType' => $DocListGet->ItemDiscountType, 'ItemDiscount' => $DocListGet->ItemDiscount, 'ItemDiscountAmount' => $DocListGet->ItemDiscountAmount, 'Itemtotal' => $DocListGet->Itemtotal, 'Vat' => $DocListGet->Vat, 'VatAmount' => $DocListGet->VatAmount, 'DocsId' => $DocListGet->id, 'ActDocs' => $DocAction)
); 
    
}


$OrderContent = array('TrueTypeHeader' => $TrueTypeHeader, 'DocAction' => $DocAction, 'EditClientId' => $DocGet->ClientId, 'EditTempId' => $TempId);
$OrderContent = json_encode($OrderContent);
echo $OrderContent;   



?>