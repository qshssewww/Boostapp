<?php

require_once '../../app/init.php';

$SettingsInfo = DB::table('settings')->where('id' ,'=', '1')->first();

			$TempId = $_REQUEST['NewPriceTempId'];
            $Act = $_REQUEST['NewPriceAct'];
			$TempList = $_REQUEST['NewPriceTempList'];
			$ItemPrice = $_REQUEST['NewPriceItem'];
            $TotalVatItemPrice = '0';

$headerTemps = DB::table('templist')
        ->where('TempId', $TempId)
		->where('id', $TempList)
		->first();

if ($ItemPrice==''){ } else {

$Vat = DB::table('temp')->where('id', '=', $TempId)->pluck('Vat');    

if ($Vat=='0'){
$ItemPrice = $ItemPrice;     
}
else {
if ($SettingsInfo->CompanyVat=='0' && $Act=='1'){ 
    
$Vats = '1.'.$Vat;
$Vat = $Vat;
        
$TotalVatItemPrice = $ItemPrice/$Vats;
$TotalVatItemPrice = $TotalVatItemPrice;    
$TotalVatItemPrice = round($ItemPrice-$TotalVatItemPrice,2);  
    
$ItemPrice = round($ItemPrice-$TotalVatItemPrice,2);    
    
    
}
else { 
    
$ItemPrice = $ItemPrice;    
    
$Vat = $Vat;    
$TotalVatItemPrice = $ItemPrice*$Vat/100;
$TotalVatItemPrice = round($TotalVatItemPrice,2);     
    
    
}
}       
    
// בדיקת כמות והנחה קיימת לפריט


$ItemQuantity = $headerTemps->ItemQuantity;
$DiscountsTypeItem = $headerTemps->ItemDiscountType;
$DiscountsItem = $headerTemps->ItemDiscount;
$Total = $ItemPrice*$ItemQuantity;    


if ($Vat=='0' || $DiscountsTypeItem=='1'){
$TotalVatDiscount = $DiscountsItem;
}
else {
if ($SettingsInfo->CompanyVat=='0'){ 
    
$Vats = '1.'.$Vat;
$Vat = $Vat;
        
$TotalVatDiscount = $DiscountsItem/$Vats;
$TotalVatDiscount = $TotalVatDiscount;    
$TotalVatDiscount = round($DiscountsItem-$TotalVatDiscount,2);  
    
$DiscountsItem = $DiscountsItem-$TotalVatDiscount;    
    
    
}
else { 
$DiscountsItem = $DiscountsItem;    
}
}           
            
            
            
            
		if ($DiscountsTypeItem=='1'){
			
		$NewDiscount = $Total*$DiscountsItem/100;
		$NewDiscount = $NewDiscount;
	    $TotalNewPrice = round($Total-$NewDiscount,2);
		$Total = $TotalNewPrice;
		}
			
		else {

	    $TotalNewPrice = $Total-$DiscountsItem;
        $NewDiscount = $DiscountsItem;        
		$Total = $TotalNewPrice;
				
		}

       if ($Vat=='0'){
       $TotalVat = '0';     
       }
       else {
       
	    $Vat = $Vat;    
        $TotalVat = $Total*$Vat/100;
	    $TotalVat = round($TotalVat,2); 
            
        }  

			
        $ItemTotal = round($Total+$TotalVat,1);
        $ItemDiscountAmount = $NewDiscount;
    
        $ItemPriceVat = round($ItemPrice,2);
        $ItemPrice = round($ItemPrice+$TotalVatItemPrice,1);
               
	    /// בדיקת עיגול אגורות
    
        $CheckAgura = $ItemTotal-$Total;
     
        if ($TotalVat!=$CheckAgura){
          
        $MinusAgura = $TotalVat-$CheckAgura;
        $TotalVat = $TotalVat-$MinusAgura;    
            
        }
         
   
			DB::table('templist')
           ->where('id', $TempList)
		   ->where('TempId', $TempId)
           ->update(array('ItemPrice' => $ItemPrice, 'ItemPriceVat' => $ItemPriceVat, 'ItemPriceVatDiscount' => $Total, 'ItemDiscount' => $DiscountsItem, 'ItemDiscountAmount' => $ItemDiscountAmount, 'ItemDiscountType' => $DiscountsTypeItem, 'Itemtotal' => $ItemTotal, 'VatAmount' => $TotalVat));
		   
		   

				
								
}

?>