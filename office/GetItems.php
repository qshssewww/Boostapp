<?php require_once '../app/init.php'; ?>


<?php if (Auth::check()):?>

<?php

$UserId = Auth::user()->id;
$CompanyNum = Auth::user()->CompanyNum;

$Dates= date('Y-m-d H:i:s');

$TypeDoc = $_REQUEST['TypeDoc'];

$SettingsInfo = DB::table('settings')->where('id' ,'=', '1')->where('CompanyNum' ,'=', $CompanyNum)->first();

if ($SettingsInfo->CompanyVat=='0'){
$Vat = $SettingsInfo->Vat;    
}
else {
$Vat = '0'; 
}

//// פתיחת הזמנה זמנית לקופאי

$headerimages = DB::table('temp')->where('UserId' ,'=', Auth::user()->id)->where('TypeDoc' ,'=', $TypeDoc)->where('CompanyNum' ,'=', $CompanyNum)->where('Status' ,'=', '0')->get();

if (!empty($headerimages)){ 

            foreach($headerimages as $headerimage){ 
			
            /// update
		
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
        ->update(array('CompanyNum' ,'=', $CompanyNum, 'ClientId' => $ClientId, 'TypeDoc' => $TypeDoc, 'Dates' => $Dates));
			
            } 
        } else {
			
	/// Crate New
	
if (!empty($_REQUEST['ClientId'])){ 
$ClientId = $_REQUEST['ClientId'];
}
else {
$ClientId = '0';
}
			
    $TempId = DB::table('temp')->insertGetId(
    array('CompanyNum' ,'=', $CompanyNum, 'TypeDoc' => $TypeDoc, 'ClientId' => $ClientId, 'Dates' => $Dates, 'UserId' => $UserId, 'Vat' => $Vat)
    );
	
	
	
    } 



//// קליטת נתוני הפריטים


$ItemId = $_REQUEST['ItemId'];
$ItemTable = 'items';

$Items = DB::table('items')->where('id', $ItemId)->where('CompanyNum' ,'=', $CompanyNum)->first();


if (!empty($_REQUEST['ItemTextNew'])){ 
$ItemName = stripslashes($_REQUEST['ItemTextNew']);
}
else {
$ItemName = $Items->ItemName;
}

$ItemText = @$Items->Remarks;

$ItemPrice = $Items->ItemPrice;




$ItemQuantity = '1';

$Itemtotal = $ItemPrice;


if ($ItemId=='1'){
		
			/// Crate New
	
		$ItemDiscount =  '0';
		$ItemDiscountType =  '1';
        $SKU = '0';
		
    $TempList = DB::table('templist')->insertGetId(
    array('CompanyNum' ,'=', $CompanyNum, 'TempId' => $TempId, 'ItemId' => $ItemId, 'SKU' => $SKU, 'ItemName' => $ItemName, 'ItemPrice' => $ItemPrice, 'ItemQuantity' => $ItemQuantity, 'ItemDiscountType' => $ItemDiscountType, 'ItemDiscount' => $ItemDiscount, 'Itemtotal' => $Itemtotal, 'ItemTable' => $ItemTable)
    );
		
}
		
else {


$headerTemps = DB::table('templist')
        ->where('TempId', $TempId)
		->where('ItemId', $ItemId)
        ->where('CompanyNum' ,'=', $CompanyNum)
		->get();

if (!empty($headerTemps)){ 

        foreach($headerTemps as $headerTemp){ 
		
			
        /// update
		
		$ItemQuantity =  $headerTemp->ItemQuantity+1;
		
		
$ItemClientNew = DB::table('items_price_client')->where('CompanyNum' ,'=', $CompanyNum)->where('ClientId', '=', $ClientId)->where('ItemId', '=', $headerTemp->ItemId)->where('Status', '=', '0')->orderBy('Dates', 'DESC')->first();

if (!empty(@$ItemClientNew)){ 

$ItemPrice = @$ItemClientNew->Price;

}

else {

$ItemPrice = $headerTemp->ItemPrice;

}
		
        
            
        $Vat = DB::table('temp')->where('id', '=', $TempId)->where('CompanyNum' ,'=', $CompanyNum)->pluck('Vat');    
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
    
// בדיקת כמות והנחה קיימת לפריט

$DiscountsTypeItem = $headerTemp->ItemDiscountType;
$DiscountsItem = $headerTemp->ItemDiscount;
$DiscountsItems = $headerTemp->ItemDiscount;            
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

			
        $ItemTotal = round($Total+$TotalVat,2);
        $ItemDiscountAmount = $NewDiscount;
    
        $ItemPriceVat = round($ItemPrice,2);
        $ItemPriceVatDiscount = round($ItemPrice*$ItemQuantity,2);    
        $ItemPrice = round($ItemPrice+$TotalVatItemPrice,2);
               
	    /// בדיקת עיגול אגורות
    
        $CheckAgura = $ItemTotal-$Total;
     
        if ($TotalVat!=$CheckAgura){
          
        $MinusAgura = $TotalVat-$CheckAgura;
        $TotalVat = $TotalVat-$MinusAgura;    
            
        }    
              
		DB::table('templist')
        ->where('TempId', $TempId)
        ->where('ItemId', $ItemId)  
        ->where('CompanyNum' ,'=', $CompanyNum)    
        ->update(array('ItemPrice' => $ItemPrice, 'ItemPriceVat' => $ItemPriceVat, 'ItemPriceVatDiscount' => $Total, 'ItemQuantity' => $ItemQuantity, 'ItemDiscountType' => $DiscountsTypeItem, 'ItemDiscount' => $DiscountsItems, 'ItemDiscountAmount' => $ItemDiscountAmount, 'Itemtotal' => $ItemTotal, 'Vat' => $Vat, 'VatAmount' => $TotalVat));
			
        } 

		
        } else {
			
	    /// Crate New
	
		$ItemDiscount =  '0';
		$ItemDiscountType =  '1';
        $SKU = '0';
		
               
    $Vat = DB::table('temp')->where('id', '=', $TempId)->where('CompanyNum' ,'=', $CompanyNum)->pluck('Vat');    
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
        $ItemPrice = round($ItemPrice+$TotalVatItemPrice,1);
               
	    /// בדיקת עיגול אגורות
    
        $CheckAgura = $ItemTotal-$Total;
     
        if ($TotalVat!=$CheckAgura){
          
        $MinusAgura = $TotalVat-$CheckAgura;
        $TotalVat = $TotalVat-$MinusAgura;    
            
        }
    
        
    $TempList = DB::table('templist')->insertGetId(
    array('CompanyNum' ,'=', $CompanyNum, 'TempId' => $TempId, 'ItemId' => $ItemId, 'SKU' => $SKU, 'ItemName' => $ItemName, 'ItemPrice' => $ItemPrice, 'ItemPriceVat' => $ItemPriceVat, 'ItemPriceVatDiscount' => $ItemPriceVat, 'ItemQuantity' => $ItemQuantity, 'ItemDiscountType' => $ItemDiscountType, 'ItemDiscount' => $ItemDiscount, 'Itemtotal' => $ItemTotal, 'ItemTable' => $ItemTable, 'Vat' => $Vat, 'VatAmount' => $TotalVat)
    );
	
		
	
    } 

}


?>



<div id="MeItem">

<table class="table" dir="rtl" style="margin-bottom: 0px;">

    <thead>
    
<tr>
<th style="width: 5%; text-align:right;">X</th>
<th style="width: 30%; text-align:right;">שם פריט</th>
<th style="width: 15%; text-align:right;">מחיר ליח' לפני מע"מ</th>    
<th style="width: 15%; text-align:right;">מחיר ליח' כולל מע"מ</th>
<th style="width: 10%; text-align:right;">כמות</th>
<th style="width: 10%; text-align:right;">הנחת שורה</th>
<th style="width: 15%; text-align:right;">סה"כ</th>

</tr>
   
</thead>
<tbody>

<?php 

$headertemplists = DB::table('templist')->where('TempId', '=', $TempId)->where('CompanyNum' ,'=', $CompanyNum)->get();

foreach($headertemplists as $headertemplist){ ?>
    
    <tr>
            <td style="width: 5%;"><a href="javascript:void(0)" id="DellAll<?php echo $headertemplist->id ?>"><i class="fas fa-trash-alt fa-lg"></i></a></td>
        <td  style="width: 30%;"><?php echo $headertemplist->ItemName ?></td>
        <td style="width: 15%;"><a style="cursor:pointer;" id="NewPricesButton<?php echo $headertemplist->id ?>"><i class="fas fa-edit fa-fw"></i></a> <?php echo $headertemplist->ItemPriceVat ?> ₪</td>
        <td style="width: 15%;"><a style="cursor:pointer;" id="NewPriceButton<?php echo $headertemplist->id ?>"><i class="fas fa-edit fa-fw"></i></a> <?php echo $headertemplist->ItemPrice ?> ₪</td>
        <td style="width: 10%;"><a style="cursor:pointer;"  id="NewQuantityButton<?php echo $headertemplist->id ?>"><i class="fas fa-edit fa-fw"></i></a> <?php echo $headertemplist->ItemQuantity ?></td>
        <td style="width: 10%;"><a style="cursor:pointer;" id="DiscountButton<?php echo $headertemplist->id ?>"><i class="fas fa-edit fa-fw"></i></a> <?php if ($headertemplist->ItemDiscountType=='1') { echo $headertemplist->ItemDiscount; echo '%'; } else if ($headertemplist->ItemDiscountType=='2') { echo '₪'; echo $headertemplist->ItemDiscountAmount; } else { echo $headertemplist->ItemDiscount; echo '%'; } ; ?>
        
        <script>
		var discount<?php echo $headertemplist->id ?> = $('.discount<?php echo $headertemplist->id ?>');

$('#DiscountButton<?php echo $headertemplist->id ?>').click(function() {
           discount<?php echo $headertemplist->id ?>.show();
        //   white.hide();
    });

$('#DiscountClose<?php echo $headertemplist->id ?>').click(function() {
           discount<?php echo $headertemplist->id ?>.hide();
        //   white.hide();
    });
	
	
	$("#AddDiscountItem<?php echo $headertemplist->id ?>").submit(function(event) {

    /* Stop form from submitting normally */
    event.preventDefault();

    /* Clear result div*/
   // $("#GetItems").html('');
   var DiscountTempId = document.getElementById('DiscountTempId<?php echo $headertemplist->id ?>').value;
    /* Get some values from elements on the page: */
    var values = $(this).serialize();

    /* Send the data using post and put the results in a div */
    $.ajax({
        url: "Docs/AddDiscounts.php",
        type: "post",
        data: values,
        success: function(){
         //  alert("עודכן בהצלחה");
            $("#GetItems").load( 'UpdatesItems.php?TempId='+ DiscountTempId + '#MeItem');
        },
        error:function(){
        //    alert("התגלתה שגיאה");
            $("#GetItems").load( 'UpdatesItems.php?TempId='+ DiscountTempId + '#MeItem');
        }
    });
});	
		
		</script>
        
         <script>
		var NewPrice<?php echo $headertemplist->id ?> = $('.NewPrice<?php echo $headertemplist->id ?>');

$('#NewPriceButton<?php echo $headertemplist->id ?>').click(function() {
           NewPrice<?php echo $headertemplist->id ?>.show();
        //   white.hide();
    });

$('#NewPriceClose<?php echo $headertemplist->id ?>').click(function() {
           NewPrice<?php echo $headertemplist->id ?>.hide();
        //   white.hide();
    });
	
	
	$("#NewPriceItem<?php echo $headertemplist->id ?>").submit(function(event) {

    /* Stop form from submitting normally */
    event.preventDefault();

    /* Clear result div*/
   // $("#GetItems").html('');
   var NewPriceTempId = document.getElementById('NewPriceTempId<?php echo $headertemplist->id ?>').value;
    /* Get some values from elements on the page: */
    var values = $(this).serialize();

    /* Send the data using post and put the results in a div */
    $.ajax({
        url: "Docs/NewPrice.php",
        type: "post",
        data: values,
        success: function(){
         //  alert("עודכן בהצלחה");
            $("#GetItems").load( 'UpdatesItems.php?TempId='+ NewPriceTempId + '#MeItem');
        },
        error:function(){
        //    alert("התגלתה שגיאה");
            $("#GetItems").load( 'UpdatesItems.php?TempId='+ NewPriceTempId + '#MeItem');
        }
    });
});	
		
		</script>
   
<script>
		var NewPrices<?php echo $headertemplist->id ?> = $('.NewPrices<?php echo $headertemplist->id ?>');

    
$('#DellAll<?php echo $headertemplist->id ?>').click(function() {
        
        $.ajax({
        url: "Docs/DelItemAll.php?TempId=<?php echo $headertemplist->TempId ?>&TempList=<?php echo $headertemplist->id ?>",
        type: "post",
        success: function(){
         //  alert("עודכן בהצלחה");
            $("#GetItems").load( 'UpdatesItems.php?TempId=<?php echo $headertemplist->TempId ?>#MeItem');
        },
        error:function(){
        //    alert("התגלתה שגיאה");
            $("#GetItems").load( 'UpdatesItems.php?TempId=<?php echo $headertemplist->TempId ?>#MeItem');
        }
    });
    
    
    });    
    
    
$('#NewPricesButton<?php echo $headertemplist->id ?>').click(function() {
           NewPrices<?php echo $headertemplist->id ?>.show();
        //   white.hide();
    });

$('#NewPricesClose<?php echo $headertemplist->id ?>').click(function() {
           NewPrices<?php echo $headertemplist->id ?>.hide();
        //   white.hide();
    });
	
	
	$("#NewPricesItem<?php echo $headertemplist->id ?>").submit(function(event) {

    /* Stop form from submitting normally */
    event.preventDefault();

    /* Clear result div*/
   // $("#GetItems").html('');
   var NewPriceTempId = document.getElementById('NewPricesTempId<?php echo $headertemplist->id ?>').value;
    /* Get some values from elements on the page: */
    var values = $(this).serialize();

    /* Send the data using post and put the results in a div */
    $.ajax({
        url: "Docs/NewPrice.php",
        type: "post",
        data: values,
        success: function(){
         //  alert("עודכן בהצלחה");
            $("#GetItems").load( 'UpdatesItems.php?TempId='+ NewPriceTempId + '#MeItem');
        },
        error:function(){
        //    alert("התגלתה שגיאה");
            $("#GetItems").load( 'UpdatesItems.php?TempId='+ NewPriceTempId + '#MeItem');
        }
    });
});	
		
		</script>          
            
            
            
            
         <script>
		var NewQuantity<?php echo $headertemplist->id ?> = $('.NewQuantity<?php echo $headertemplist->id ?>');

$('#NewQuantityButton<?php echo $headertemplist->id ?>').click(function() {
           NewQuantity<?php echo $headertemplist->id ?>.show();
        //   white.hide();
    });

$('#NewQuantityClose<?php echo $headertemplist->id ?>').click(function() {
           NewQuantity<?php echo $headertemplist->id ?>.hide();
        //   white.hide();
    });
	
	
	$("#NewQuantityItem<?php echo $headertemplist->id ?>").submit(function(event) {

    /* Stop form from submitting normally */
    event.preventDefault();

    /* Clear result div*/
   // $("#GetItems").html('');
   var NewQuantityTempId = document.getElementById('NewQuantityTempId<?php echo $headertemplist->id ?>').value;
    /* Get some values from elements on the page: */
    var values = $(this).serialize();

    /* Send the data using post and put the results in a div */
    $.ajax({
        url: "Docs/AddItems.php",
        type: "post",
        data: values,
        success: function(){
         //  alert("עודכן בהצלחה");
            $("#GetItems").load( 'UpdatesItems.php?TempId='+ NewQuantityTempId + '#MeItem');
        },
        error:function(){
        //    alert("התגלתה שגיאה");
            $("#GetItems").load( 'UpdatesItems.php?TempId='+ NewQuantityTempId + '#MeItem');
        }
    });
});	
		
		</script>        
        </td>
        <td class="filterable-cell" style="width: 15%;"><?php echo number_format($headertemplist->ItemPriceVatDiscount, 2) ?> ₪</td>

    </tr>  
    
    <tr class="discount<?php echo $headertemplist->id ?> table-secondary" style="display:none;">
    <td colspan="8" height="50" style="height:50px; width:100%; align-content:center;">
    
<form name="AddDiscountItem<?php echo $headertemplist->id ?>" id="AddDiscountItem<?php echo $headertemplist->id ?>" class="form-inline" style="align-content:center;" autocomplete="off">
  <div class="form-row align-items-center">	
<input name="DiscountTempList" type="hidden" value="<?php echo $headertemplist->id ?>">
<input name="DiscountTempId" id="DiscountTempId<?php echo $headertemplist->id ?>"  type="hidden" value="<?php echo $headertemplist->TempId ?>">
<input name="DiscountPrice" type="hidden" value="<?php echo $headertemplist->ItemPrice ?>">
<input name="DiscountPriceItem" type="hidden" value="<?php echo number_format($headertemplist->Itemtotal, 2) ?>">
<input name="DiscountItemQuantity" type="hidden" value="<?php echo $headertemplist->ItemQuantity ?>">

<div class="form-check form-check-inline">
<label class="form-check-label" style="text-decoration:underline;">סוג הנחה </label>      
</div>
<div class="form-check form-check-inline">  
<input type="radio" name="DiscountsTypeItem" class="form-check-input" id="inlineRadio1<?php echo $headertemplist->id ?>" value="1" <?php if ($headertemplist->ItemDiscountType=='1') { echo 'checked'; } else {}  ; ?>> 
<label class="form-check-label" for="inlineRadio1<?php echo $headertemplist->id ?>" style="padding-right: 5px;"> % </label>	
</div>
<div class="form-check form-check-inline">	  
<input type="radio" name="DiscountsTypeItem" class="form-check-input" id="inlineRadio2<?php echo $headertemplist->id ?>" value="2" <?php if ($headertemplist->ItemDiscountType=='2') { echo 'checked'; } else {} ; ?>> 
<label class="form-check-label" for="inlineRadio2<?php echo $headertemplist->id ?>" style="padding-right: 5px;"> ₪ </label>
</div>
	
   <div class="form-group">
                <label class="col-form-label" style="padding-left: 5px; text-decoration:underline;">הקלד הנחה</label>
                <input type="text" name="DiscountsItem" min="0" class="form-control" placeholder="מספרים בלבד" value="<?php echo $headertemplist->ItemDiscount ?>" onkeypress='validate(event)'>
    </div>

	<div class="col-auto">
   <button type="submit" name="submit" class="btn btn-info"><?php _e('main.save_changes') ?></button>
	</div>
<div class="col-auto">	
   <button type="button" id="DiscountClose<?php echo $headertemplist->id ?>" class="btn btn-light"><?php _e('main.close') ?></button>
	</div>
	  
	</div>  
  </form>
    
    </td>
    
    </tr> 
    
    <tr class="NewPrice<?php echo $headertemplist->id ?> table-secondary" style="display:none;">
    <td colspan="8" height="50" style="height:50px; width:100%; align-content:center;">
    
<form name="NewPriceItem<?php echo $headertemplist->id ?>" id="NewPriceItem<?php echo $headertemplist->id ?>" class="form-inline" style="align-content:center;" autocomplete="off">
  <div class="form-row align-items-center">		
<input name="NewPriceTempList"  type="hidden" value="<?php echo $headertemplist->id ?>">
<input name="NewPriceAct"  type="hidden" value="1">      
<input name="NewPriceTempId" id="NewPriceTempId<?php echo $headertemplist->id ?>" type="hidden" value="<?php echo $headertemplist->TempId ?>">


    <div class="form-group">
                <label style="padding-left: 5px;">הקלד מחיר ליח' כולל מע"מ לפריט</label>
                <input type="text" name="NewPriceItem" min="0" class="form-control" placeholder="מספרים בלבד" value="<?php echo $headertemplist->ItemPrice ?>" onkeypress='validate(event)'>
    </div>

	<div class="col-auto">	
   <button type="submit" name="submit" class="btn btn-info"><?php _e('main.save_changes') ?></button>
	</div>
	<div class="col-auto">	
   <button type="button" id="NewPriceClose<?php echo $headertemplist->id ?>" class="btn btn-light"><?php _e('main.close') ?></button>
	</div>
	  
	</div>  
  </form>
    
    </td>
    
    </tr> 
    
    <tr class="NewPrices<?php echo $headertemplist->id ?> table-secondary" style="display:none;">
    <td colspan="8" height="50" style="height:50px; width:100%; align-content:center;">
    
<form name="NewPricesItem<?php echo $headertemplist->id ?>" id="NewPricesItem<?php echo $headertemplist->id ?>" class="form-inline" style="align-content:center;" autocomplete="off">
  <div class="form-row align-items-center">		
<input name="NewPriceTempList"  type="hidden" value="<?php echo $headertemplist->id ?>">
<input name="NewPriceAct"  type="hidden" value="0">      
<input name="NewPriceTempId" id="NewPricesTempId<?php echo $headertemplist->id ?>" type="hidden" value="<?php echo $headertemplist->TempId ?>">


    <div class="form-group">
                <label style="padding-left: 5px;">הקלד מחיר ליח' לא כולל מע"מ לפריט</label>
                <input type="text" name="NewPriceItem" min="0" class="form-control" placeholder="מספרים בלבד" value="<?php echo $headertemplist->ItemPriceVat ?>" onkeypress='validate(event)'>
    </div>

	<div class="col-auto">	
   <button type="submit" name="submit" class="btn btn-info"><?php _e('main.save_changes') ?></button>
	</div>
	<div class="col-auto">	
   <button type="button" id="NewPricesClose<?php echo $headertemplist->id ?>" class="btn btn-light"><?php _e('main.close') ?></button>
	</div>
	  
	</div>  
  </form>
    
    </td>
    
    </tr>     
    
    
    <tr class="NewQuantity<?php echo $headertemplist->id ?> table-secondary" style="display:none;">
    <td colspan="8" height="50" style="height:50px; width:100%; align-content:center;">
    
<form name="NewQuantityItem<?php echo $headertemplist->id ?>" id="NewQuantityItem<?php echo $headertemplist->id ?>" class="form-inline" style="align-content:center;" autocomplete="off">
  <div class="form-row align-items-center">		
<input name="NewQuantityTempList" type="hidden" value="<?php echo $headertemplist->id ?>">
<input name="NewQuantityTempId" id="NewQuantityTempId<?php echo $headertemplist->id ?>" type="hidden" value="<?php echo $headertemplist->TempId ?>">

<input name="NewQuantityItemId" type="hidden" value="<?php echo $headertemplist->ItemId ?>">
<input name="NewQuantityItemTable" type="hidden" value="<?php echo $headertemplist->ItemTable ?>">

    <div class="form-group">
    <label style="padding-left: 5px;">הקלד כמות לפריט</label>
    <input type="number" name="NewQuantityItem" min="0" class="form-control" placeholder="מספרים בלבד" value="<?php echo $headertemplist->ItemQuantity ?>" onkeypress='validate(event)'>
    </div>

	<div class="col-auto">	
   <button type="submit" name="submit" class="btn btn-info"><?php _e('main.save_changes') ?></button>
	</div>
<div class="col-auto">		
   <button type="button" id="NewQuantityClose<?php echo $headertemplist->id ?>" class="btn btn-light"><?php _e('main.close') ?></button>
	</div>
	</div>
  </form>

    </td>
    
    </tr> 
    
   <?php	} 
	
	$SettingsInfo = DB::table('settings')->where('id' ,'=', '1')->where('CompanyNum' ,'=', $CompanyNum)->first();
    $Vat = DB::table('temp')->where('id', '=', $TempId)->where('CompanyNum' ,'=', $CompanyNum)->pluck('Vat');	
	$DiscountsItem = DB::table('temp')->where('id', '=', $TempId)->where('CompanyNum' ,'=', $CompanyNum)->pluck('Discount');	
	$DiscountsTypeItem = DB::table('temp')->where('id', '=', $TempId)->where('CompanyNum' ,'=', $CompanyNum)->pluck('DiscountType');
    
	$Total = DB::table('templist')->where('TempId', '=', $TempId)->where('CompanyNum' ,'=', $CompanyNum)->sum('ItemPriceVatDiscount');
    
	 
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
    
	?> 
     
    </tbody>
    
</table>

<input id="Temptotal" name="Temptotal" type="hidden" value="<?php echo number_format((float)($ItemTotal+$NewDiscount)-$TotalVat, 2, '.', ''); ?>">
<input id="TempVat" name="TempVat" type="hidden" value="<?php echo number_format((float)$TotalVat, 2, '.', ''); ?>">

<input id="TempDiscount" name="TempDiscount" type="hidden" value="<?php echo number_format((float)$ItemDiscountAmount, 2, '.', ''); ?>">
<input id="TempDiscount2" name="TempDiscount2" type="hidden" value="<?php echo $DiscountType2; ?>">
<input id="TempNEWDiscount" name="TempNEWDiscount" type="hidden" value="<?php echo number_format((float)$NewDiscount, 2, '.', ''); ?>">

<input id="VATIn" name="VATIn" type="hidden" value="<?php echo number_format((float)$Vat, 2, '.', ''); ?>">

<input id="Temptotal2" name="Temptotal2" type="hidden" value="<?php echo number_format((float)$ItemTotal, 2, '.', ''); ?>">

<input id="TempId" name="TempId" type="hidden" value="<?php echo $TempId; ?>">


<?php

DB::table('temp')
        ->where('UserId', $UserId)
		->where('id', $TempId)
        ->where('CompanyNum' ,'=', $CompanyNum)
        ->update(array('Amount' => number_format((float)$ItemTotal, 2, '.', ''),'VatAmount' => number_format((float)$TotalVat, 2, '.', ''), 'DiscountAmount' => number_format((float)$NewDiscount, 2, '.', '')));

?>

 <script>
 
$("#Items1").select2("val", ""); 
$("#TextValue").val(""); 
 
$(document).ready(function(){

var price = document.getElementById('Temptotal').value;
var vat = document.getElementById('TempVat').value;
var discount = document.getElementById('TempDiscount').value;
var discount2 = document.getElementById('TempDiscount2').value;
var TempNEWDiscount = document.getElementById('TempNEWDiscount').value;
var price2 = document.getElementById('Temptotal2').value;
var VAT2 = document.getElementById('VATIn').value;
var TempId = document.getElementById('TempId').value;
document.getElementById('resultFinal').innerHTML = parseFloat(price).toFixed(2);
document.getElementById('resultVAT').innerHTML = parseFloat(vat).toFixed(2);

document.getElementById('resultFinalDiscount').innerHTML = parseFloat(TempNEWDiscount).toFixed(2);

document.getElementById('resultDiscountIn2').innerHTML = parseFloat(discount);
$("#resultDiscountIn").text(discount2);

document.getElementById('resultFinal2').innerHTML = parseFloat(price2).toFixed(2);
document.getElementById('resultVATIn').innerHTML = parseFloat(VAT2);
$("#TempsId").val(TempId);
$("#TempsIds").val(TempId);
$("#TempsIdRemarks").val(TempId);
$("#TempsIdDiscount").val(TempId);
$("#CancelPayments_TempsId").val(TempId);
$("#CancelDocs_TempsId").val(TempId);    
$("#TempsIdVat").val(TempId);
$("#Fild1").val(TempId);
$("#Fild1Cash").val(TempId);
$("#Fild1Ceck").val(TempId);
$("#Fild1Agent").val(TempId);
$("#Prints").data('ajax', TempId);
$("#DocTempId").val(TempId);


//// חישוב סה"כ לתשלום
document.getElementById('TotalFinal').innerHTML = parseFloat(price2).toFixed(2);
$("#TotalHide2").val(parseFloat(price2).toFixed(2));

 $(".Del_Items").click(function(e){
   var url= $(this).data('href');
  //alert(url);
    $('#GetItems').load( url + '#MeItem');
     return false;/* edit to prevent browser following link*/
  });


 $(".Add_Items").click(function(e){
   var url= $(this).data('href');
  //alert(url);
    $('#GetItems').load( url + '#MeItem');
     return false;/* edit to prevent browser following link*/
  });



});
 
 
 
 </script>
 
 

</div>

<?php endif ?>