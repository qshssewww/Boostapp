<?php

require_once '../../app/init.php';

        $SettingsInfo = DB::table('settings')->where('id' ,'=', '1')->first();

        $UserId = Auth::user()->id;

        $Dates= date('Y-m-d H:i:s');

        $TypeDoc = $_REQUEST['TypeDoc'];

        $SettingsInfo = DB::table('settings')->where('id' ,'=', '1')->first();

        if ($SettingsInfo->CompanyVat=='0'){
        $Vat = $SettingsInfo->Vat;    
        }
        else {
        $Vat = '0'; 
        }

        //// פתיחת הזמנה זמנית

        $headerimage = DB::table('temp')->where('UserId' ,'=', Auth::user()->id)->where('TypeDoc' ,'=', $TypeDoc)->where('Status' ,'=', '0')->first();

        if (!empty($headerimage)){ 

        $TempId =  $headerimage->id;

        if (!empty($_REQUEST['ClientId'])){ 
        $ClientId = $_REQUEST['ClientId'];
        }
        else {
        $ClientId = $headerimage->ClientId;	
        }
			
		DB::table('temp')
        ->where('UserId', $UserId)
		->where('id', $TempId)
        ->update(array('ClientId' => $ClientId, 'TypeDoc' => $TypeDoc, 'Dates' => $Dates));
			

        } else {
			
        /// Crate New

        if (!empty($_REQUEST['ClientId'])){ 
        $ClientId = $_REQUEST['ClientId'];
        }
        else {
        $ClientId = '0';
        }

        $TempId = DB::table('temp')->insertGetId(
        array('TypeDoc' => $TypeDoc, 'ClientId' => $ClientId, 'Dates' => $Dates, 'UserId' => $UserId, 'Vat' => $Vat)
        );

        }  

        if (!empty($_REQUEST['ItemId'])){ 
        $ItemId = $_REQUEST['ItemId'];
            
        $Items = DB::table('items')->where('id', $ItemId)->first();    
        if (!empty($_REQUEST['ItemTextNew'])){ 
        $ItemName = stripslashes($_REQUEST['ItemTextNew']);
        }
        else {
        $ItemName = $Items->ItemName;
        }
        $ItemText = @$Items->Remarks;
            
        /// בדיקת קבלת סכום חדש    
        $ItemPrice = $Items->ItemPrice;
        $ItemQuantity = '1';
        $Itemtotal = $ItemPrice;    
              
        $headerTemps = DB::table('templist')
        ->where('TempId', $TempId)
        ->where('ItemId', $ItemId) 
        ->where('ItemId', '!=', '1')     
		->get();
        
        $AddQuantity = '1';    
            
        }
        else {
        
        @$TempListId = @$_REQUEST['TempListId'];    
         
        if (!empty($_REQUEST['DellId'])){ 
        $DellId = $_REQUEST['DellId'];
        DB::table('templist')->where('id', '=', $DellId)->delete();    
        }       
         
            
        if (@$_REQUEST['ActTemp']!=''){ 
        $AddQuantity = '0'; 
        /// עדכון רשימת פריטים    
        $headerTemps = DB::table('templist')
        ->where('TempId', $TempId)    
		->get(); 
        } 
        else {     
        /// עדכון פריט בודד    
        $headerTemps = DB::table('templist')
        ->where('TempId', $TempId) 
        ->where('id', $TempListId)     
		->get(); 
        }
            
        }



        if (!empty($headerTemps)){ 

        foreach($headerTemps as $headerTemp){ 
        
$Vat = DB::table('temp')->where('id', '=', $TempId)->pluck('Vat');    

if (@$_REQUEST['ItemPrice']!=''){ 
$ItemPrice = $_REQUEST['ItemPrice'];
$Act = $_REQUEST['Act'];
$AddQuantity = '0';     
}
else {
$ItemPrice = $headerTemp->ItemPrice; 
$Act = '1';    
}  

if (@$_REQUEST['ActTemp']!=''){            
if ($Vat=='0' && @$_REQUEST['ActTemp']!=''){
$ItemPrice = $headerTemp->ItemPriceVat;     
}
else {
$ItemPrice = $headerTemp->ItemPrice; 
$Vat = $Vat;    
$TotalVatItemPrice = $ItemPrice*$Vat/100;
$TotalVatItemPrice = round($TotalVatItemPrice,2);   
$ItemPrice = round($ItemPrice+$TotalVatItemPrice,2);     
}  
}
            
if ($Vat=='0'){
$ItemPrice = $ItemPrice;  
$TotalVatItemPrice = '0';    
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
        
        
if (@$_REQUEST['ItemDiscountType']!=''){ 
$DiscountsTypeItem = $_REQUEST['ItemDiscountType'];
$AddQuantity = '0';     
}
else {
$DiscountsTypeItem = $headerTemp->ItemDiscountType;   
}              
            
if (@$_REQUEST['ItemDiscount']!=''){ 
$DiscountsItem = $_REQUEST['ItemDiscount'];
$DiscountsItemDB = $_REQUEST['ItemDiscount'];  
$AddQuantity = '0'; 
}
else {
$DiscountsItem = $headerTemp->ItemDiscount;  
$DiscountsItemDB = $headerTemp->ItemDiscount;     
}  
                   
if (@$_REQUEST['ItemQuantity']!=''){ 
$ItemQuantity = $_REQUEST['ItemQuantity'];
if ($ItemQuantity=='0'){
$ItemQuantity = '1';    
}    
    
    
}
else {  
$ItemQuantity = $headerTemp->ItemQuantity+$AddQuantity;   
}  
            
     
$Total = $ItemPrice*$ItemQuantity; 
            
$CheckTotal =  round($ItemPrice*$ItemQuantity+$TotalVatItemPrice,2);             

            if ($DiscountsTypeItem=='1'){
            
            if ($DiscountsItem>'100'){
            $DiscountsItem = '100';
            $DiscountsItemDB = '100';    
            } 
                
            }   
            else {
             
            if ($DiscountsItem>$CheckTotal){
            $DiscountsItem = $CheckTotal; 
            $DiscountsItemDB = $CheckTotal;    
            }
                
            }            
            
            


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
        $ItemPrice = round($ItemPrice+$TotalVatItemPrice,2);
               
	    /// בדיקת עיגול אגורות
    
        $CheckAgura = $ItemTotal-$Total;
     
        if ($TotalVat!=$CheckAgura){
          
        $MinusAgura = $TotalVat-$CheckAgura;
        $TotalVat = $TotalVat-$MinusAgura;    
            
        }
         
   
			DB::table('templist')
           ->where('id', $headerTemp->id)
		   ->where('TempId', $TempId)
           ->update(array('TypeDoc' => $TypeDoc, 'ItemPrice' => $ItemPrice, 'ItemPriceVat' => $ItemPriceVat, 'ItemPriceVatDiscount' => $Total, 'ItemDiscount' => $DiscountsItemDB, 'ItemDiscountAmount' => $ItemDiscountAmount, 'ItemQuantity' => $ItemQuantity, 'ItemDiscountType' => $DiscountsTypeItem, 'Itemtotal' => $ItemTotal, 'VatAmount' => $TotalVat));    
            
            
            
            
            
            

        }
            
        }

        //// צור חדש
        
        else {
         
        if (!empty($_REQUEST['DellId'])){ 
        $DellId = $_REQUEST['DellId'];
        DB::table('templist')->where('id', '=', $DellId)->delete();    
        }         
        else {    
            
        if (@$ItemId=='1'){
            
       /// יצירת פריט כללי
	
		$ItemDiscount =  '0';
		$ItemDiscountType =  '1';
        $SKU = '0';
		
        $TempList = DB::table('templist')->insertGetId(
        array('TypeDoc' => $TypeDoc, 'TempId' => $TempId, 'ItemId' => $ItemId, 'SKU' => $SKU, 'ItemName' => $ItemName, 'ItemPrice' => $ItemPrice, 'ItemQuantity' => $ItemQuantity, 'ItemDiscountType' => $ItemDiscountType, 'ItemDiscount' => $ItemDiscount, 'Itemtotal' => $Itemtotal, 'Vat' => $Vat)
        );    
            
        } else {
            
        if (@$ItemId!=''){    
            
        /// יצירת פריט קיים
	
		$ItemDiscount =  '0';
		$ItemDiscountType =  '1';
        $SKU = '0';
		
               
        $Vat = DB::table('temp')->where('id', '=', $TempId)->pluck('Vat');    
        $TotalVatItemPrice = '0';
        if ($Vat=='0'){
        $ItemPrice = $ItemPrice;     
        }
        else {
        if ($SettingsInfo->CompanyVat=='0'){ 

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
  
    
        $Total = $ItemPrice;    


            if ($Vat=='0'){
               $TotalVat = '0';     
               }
               else {

                $Vat = $Vat;    
                $TotalVat = $Total*$Vat/100;
                $TotalVat = round($TotalVat,2); 

                }  


                $ItemTotal = round($Total+$TotalVat,1);    
                $ItemPriceVat = round($ItemPrice,2);
                $ItemPrice = round($ItemPrice+$TotalVatItemPrice,2);

                
    
        
            $TempList = DB::table('templist')->insertGetId(
            array('TypeDoc' => $TypeDoc, 'TempId' => $TempId, 'ItemId' => $ItemId, 'SKU' => $SKU, 'ItemName' => $ItemName, 'ItemPrice' => $ItemPrice, 'ItemPriceVat' => $ItemPriceVat, 'ItemPriceVatDiscount' => $ItemPriceVat, 'ItemQuantity' => $ItemQuantity, 'ItemDiscountType' => $ItemDiscountType, 'ItemDiscount' => $ItemDiscount, 'Itemtotal' => $ItemTotal, 'Vat' => $Vat, 'VatAmount' => $TotalVat)
            );    
            
        }
        }
        }
        }













//// עדכון טבלה ראשית

    $Vat = DB::table('temp')->where('id', '=', $TempId)->pluck('Vat');	
	$DiscountsItem = DB::table('temp')->where('id', '=', $TempId)->pluck('Discount');	
	$DiscountsTypeItem = DB::table('temp')->where('id', '=', $TempId)->pluck('DiscountType');
    $Total = DB::table('templist')->where('TempId', '=', $TempId)->sum('ItemPriceVatDiscount');
    $TotalNew = $Total;
	
    if ($TotalNew=='0' || $TotalNew==''){
     
    DB::table('temp')
		->where('id', $TempId)
        ->update(array('Amount' => '0', 'VatAmount' => '0', 'RoundDiscount' => '0', 'AmountVat' => '0',  'DiscountType' => '1', 'Discount' => '0' , 'DiscountAmount' => '0'));    
        
        
    }
    
    else {
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
    
    
    
    if ($DiscountsTypeItem=='1'){
			
				$NewDiscount = $Total*$DiscountsItem/100;
				$NewDiscount = $NewDiscount;
	            $TotalNewPrice = round($Total-$NewDiscount,2);
				$Total = $TotalNewPrice;
                $DiscountType2 = '%';
		 	}
			
			else {

	       $TotalNewPrice = $Total-$DiscountsItem;
           $NewDiscount = $DiscountsItem;        
		   $Total = $TotalNewPrice;
           $DiscountType2 = '₪';        
				
			}


       if ($Vat=='0'){
       $TotalVat = '0';     
       }
       else {
       
	    $Vat = $Vat;    
        $TotalVat = $Total*$Vat/100; 
	    $TotalVat = round($TotalVat,2);
           
            
        }  

			
        $ItemTotal = round($Total+$TotalVat,2);
        $ItemDiscountAmount = $NewDiscount;
        
        //// עיגול אגורות

       $A = round($Total+$TotalVat,2);
       $B = round($Total+$TotalVat,1);
       $RoundDiscount = '0';

       if ($A>=$B){
           
       $RoundDiscount = $B-$A; 
           
       }
       else if ($B>=$A){
       $RoundDiscount = $B-$A;     
       }

       $ItemTotal = $ItemTotal+$RoundDiscount;

        
        
        DB::table('temp')
		->where('id', $TempId)
        ->update(array('Amount' => number_format((float)$ItemTotal, 2, '.', ''), 'AmountVat' => number_format((float)$TotalNew, 2, '.', ''), 'RoundDiscount' => number_format((float)$RoundDiscount, 2, '.', ''), 'VatAmount' => number_format((float)$TotalVat, 2, '.', ''), 'DiscountAmount' => number_format((float)$ItemDiscountAmount, 2, '.', '')));

         }




///// החזרת מספר טבלה זמנית

echo $TempId;


?>