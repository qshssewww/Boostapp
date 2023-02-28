<?php require_once '../../app/init.php'; ?>

<?php

$TypeId = $_REQUEST['TypeId'];
$DocId = $_REQUEST['DocId'];
$TypeHeader = $_REQUEST['TypeHeader'];
$CompanyNum = Auth::user()->CompanyNum;
$UserId = Auth::user()->id;
$Dates = date('Y-m-d H:i:s');

$DocGet = DB::table('docs')->where('CompanyNum' ,'=', $CompanyNum)->where('TypeNumber','=',$DocId)->where('TypeDoc','=',$TypeId)->first();
$DocsTables = DB::table('docstable')->where('CompanyNum' ,'=', $CompanyNum)->where('id','=',$DocGet->TypeDoc)->first();


if ($TypeHeader=='305'){
$TrueTypeHeader = '330';   
$DocAction = '1';
}
else if ($TypeHeader=='400'){
$TrueTypeHeader = '400';     
$DocAction = '2';    
}
else if ($TypeHeader=='320'){
$TrueTypeHeader = '320';     
$DocAction = '3';    
}
else if ($TypeHeader=='300'){
$TrueTypeHeader = '300';   
$DocAction = '4';    
}

$TempId = DB::table('temp')->insertGetId(
array('CompanyNum' => $CompanyNum, 'TypeDoc' => $TrueTypeHeader, 'ClientId' => $DocGet->ClientId, 'Dates' => $Dates, 'UserId' => $UserId, 'Vat' => $DocGet->Vat, 'DocsId' => $DocGet->id, 'ActDocs' => $DocAction)
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