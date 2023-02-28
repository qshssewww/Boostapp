<?php

require_once '../../app/init.php';

$SettingsInfo = DB::table('settings')->where('id' ,'=', '1')->first();

			$TempId = $_POST['DiscountTempId'];
			$TempList = $_POST['DiscountTempList'];
			$DiscountsItem = $_POST['DiscountsItem'];
            $DiscountsItem2 = $_POST['DiscountsItem'];
			$DiscountsTypeItem = $_POST['DiscountsTypeItem'];			
			$DiscountsItemtotal = $_POST['DiscountPrice'];
			$DiscountsItemtotalItem = $_POST['DiscountPriceItem'];
			$DiscountItemQuantity = $_POST['DiscountItemQuantity'];

$Vat = DB::table('temp')->where('id', '=', $TempId)->pluck('Vat');
if ($Vat=='0' || $DiscountsTypeItem=='1'){
$TotalVat = '0';     
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


$headerTemps = DB::table('templist')
        ->where('TempId', $TempId)
		->where('id', $TempList)
		->first();

	
$Total = $headerTemps->ItemPriceVat; 
$ItemQuantity = $headerTemps->ItemQuantity;    
$Total = $Total*$ItemQuantity;
$VatAmount = $headerTemps->VatAmount;

if ($headerTemps->ItemQuantity==$DiscountsItem && $headerTemps->ItemDiscountType==$DiscountsTypeItem){ } else {
			
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

			DB::table('templist')
           ->where('id', $TempList)
		   ->where('TempId', $TempId)
           ->update(array('ItemPriceVatDiscount' => $Total, 'ItemDiscount' => $DiscountsItem2, 'ItemDiscountAmount' => $ItemDiscountAmount, 'ItemDiscountType' => $DiscountsTypeItem,'Itemtotal' => $ItemTotal, 'VatAmount' => $TotalVat));

}

?>