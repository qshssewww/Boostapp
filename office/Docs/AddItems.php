<?php

require_once '../../app/init.php';

$SettingsInfo = DB::table('settings')->where('id' ,'=', '1')->first();

 $TempId = $_REQUEST['NewQuantityTempId'];
 $ItemId = $_REQUEST['NewQuantityItemId'];
 $ItemTable = $_REQUEST['NewQuantityItemTable'];
 $TempList = $_REQUEST['NewQuantityTempList'];
 $NewQuantityItem = $_POST['NewQuantityItem'];

$headerTemps = DB::table('templist')
        ->where('TempId', $TempId)
		->where('id', $TempList)
		->get();

if (!empty($headerTemps)){ 

            foreach($headerTemps as $headerTemp){ 
			
        /// update
		
		if ($headerTemp->ItemQuantity<'1'){
		
		DB::table('templist')->where('id', '=', $TempList)->delete();
			
		}
		
		else {
			
		$ItemQuantity =   $NewQuantityItem;
		$ItemPrice =  $headerTemp->ItemPriceVat;
		$DiscountsItem =  $headerTemp->ItemDiscount;
		$DiscountsTypeItem =  $headerTemp->ItemDiscountType;
		$Total = $ItemPrice*$ItemQuantity;
   
$Vat = DB::table('temp')->where('id', '=', $TempId)->pluck('Vat');
if ($Vat=='0' || $DiscountsTypeItem=='1'){
$DiscountsItem = $DiscountsItem;     
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
		
					
		DB::table('templist')
        ->where('TempId', $TempId)
		->where('id', $TempList)
		->where('ItemTable', $ItemTable)
        ->update(array('ItemPriceVatDiscount' => $Total, 'ItemQuantity' => $ItemQuantity, 'ItemDiscountType' => $DiscountsTypeItem, 'ItemDiscount' => $DiscountsItem, 'ItemDiscountAmount' => $ItemDiscountAmount, 'Itemtotal' => $ItemTotal, 'VatAmount' => $TotalVat));
			
        } 	
			
		}
		
		
		
        } else {
			
	/// Crate New

    DB::table('templist')->where('id', '=', $TempList)->delete();
	
	
    } 

?>
