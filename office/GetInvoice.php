<?php require_once '../app/init.php'; ?>


<?php if (Auth::check()):?>

<?php

$UserId = Auth::user()->id;

$Dates= date('Y-m-d H:i:s');

$TypeDoc = $_REQUEST['TypeDoc'];

//// פתיחת הזמנה זמנית לקופאי

$headerimages = DB::table('temp')->where('UserId' ,'=', Auth::user()->id)->where('TypeDoc' ,'=', $TypeDoc)->where('Status' ,'=', '0')->get();

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
        ->update(array('ClientId' => $ClientId, 'TypeDoc' => $TypeDoc, 'Dates' => $Dates));
			
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
    array('UserId' => $UserId, 'ClientId' => $ClientId, 'TypeDoc' => $TypeDoc, 'Dates' => $Dates)
    );
	
	
	
    } 



//// קליטת נתוני הפריטים


$ItemId = $_REQUEST['ItemId'];
$ItemTable = 'items';

$Items = DB::table('invoicereceipt')->where('id', $ItemId)->first();


if (!empty($_REQUEST['ItemTextNew'])){ 
$ItemName = stripslashes($_REQUEST['ItemTextNew']);
}
else {
$ItemName = $Items->id;
}

$ItemText = @$Items->Remarks;

$ItemPrice = $Items->Amount;




$ItemQuantity = '1';

$Itemtotal = $ItemPrice;


if ($ItemId=='1'){
		
			/// Crate New
	
		$ItemDiscount =  '0';
		$ItemDiscountType =  '1';
        $SKU = '0';
		
    $TempList = DB::table('templist')->insertGetId(
    array('TempId' => $TempId, 'ItemId' => $ItemId, 'SKU' => $SKU, 'ItemName' => $ItemName, 'ItemPrice' => $ItemPrice, 'ItemQuantity' => $ItemQuantity, 'ItemDiscountType' => $ItemDiscountType, 'ItemDiscount' => $ItemDiscount, 'Itemtotal' => $Itemtotal, 'ItemTable' => $ItemTable)
    );
		
}
		
else {


$headerTemps = DB::table('templist')
        ->where('TempId', $TempId)
		->where('ItemId', $ItemId)
		->where('ItemTable', $ItemTable)
		->get();

if (!empty($headerTemps)){ 

        foreach($headerTemps as $headerTemp){ 
		
			
        /// update
		
		$ItemQuantity =  $headerTemp->ItemQuantity+1;
		
		
$ItemClientNew = DB::table('items_price_client')->where('ClientId', '=', $ClientId)->where('ItemId', '=', $headerTemp->ItemId)->where('Status', '=', '0')->orderBy('Dates', 'DESC')->first();

if (!empty($ItemClientNew)){ 

$ItemPrice = $ItemClientNew->Price;

}

else {

$ItemPrice = $headerTemp->ItemPrice;

}
		
	//	$ItemPrice =  $headerTemp->ItemPrice;
		$ItemDiscount =  $headerTemp->ItemDiscount;
		$ItemDiscountType =  $headerTemp->ItemDiscountType;
		
		if ($ItemDiscountType=='1'){
		
		$NewDiscount = $ItemPrice*$ItemDiscount/100;
	    $TotalNewPrice = $ItemPrice-$NewDiscount;
		$Itemtotal = $ItemQuantity*$TotalNewPrice;
			
		}
		
		else {
		
		$fixprice = $ItemPrice*$ItemQuantity;
	    $TotalNewPrice = $fixprice-$ItemDiscount;
		$Itemtotal = $TotalNewPrice;	
		
		}
			
		
		
					
		DB::table('templist')
        ->where('TempId', $TempId)
		->where('ItemId', $ItemId)
		->where('ItemTable', $ItemTable)
        ->update(array('ItemPrice' => $ItemPrice, 'ItemQuantity' => $ItemQuantity, 'ItemDiscountType' => $ItemDiscountType, 'ItemDiscount' => $ItemDiscount, 'Itemtotal' => $Itemtotal));
			
        } 

		
        } else {
			
	/// Crate New
	
		$ItemDiscount =  '0';
		$ItemDiscountType =  '1';
        $SKU = '0';
		
    $TempList = DB::table('templist')->insertGetId(
    array('TempId' => $TempId, 'ItemId' => $ItemId, 'SKU' => $SKU, 'ItemName' => $ItemName, 'ItemPrice' => $ItemPrice, 'ItemQuantity' => $ItemQuantity, 'ItemDiscountType' => $ItemDiscountType, 'ItemDiscount' => $ItemDiscount, 'Itemtotal' => $Itemtotal, 'ItemTable' => $ItemTable)
    );
	
		
	
    } 

}


?>



<div id="MeItem">

<table class="table table-striped" dir="rtl">

    <thead>
    
<tr>
<th style="width: 5%; text-align:right;">X</th>
<th style="width: 45%; text-align:right;">שם פריט</th>
<th style="width: 15%; text-align:right;">מחיר ליח'</th>
<th style="width: 10%; text-align:right;">כמות</th>
<th style="width: 10%; text-align:right;">הנחת שורה</th>
<th style="width: 15%; text-align:right;">סה"כ</th>

</tr>
   
</thead>
<tbody>

<?php 

$headertemplists = DB::table('templist')->where('TempId', '=', $TempId)->get();

foreach($headertemplists as $headertemplist){ ?>
    
    <tr>
            <td style="width: 5%;"><a data-href="Docs/DelItemAll.php?TempList=<?php echo  $headertemplist->id ?>&TempId=<?php echo $headertemplist->TempId ?>" class="btn btn-default btn-xs Del_Items" data-toggle="tooltip" data-placement="top" title="הסר">X</a></td>
        <td class="filterable-cell" style="width: 30%;"><?php echo $headertemplist->ItemName ?></td>
        <td class="filterable-cell" style="width: 15%;"><?php echo $headertemplist->ItemPrice ?> ₪ <a style="cursor:pointer;" data-toggle="tooltip" id="NewPriceButton<?php echo $headertemplist->id ?>" data-placement="top" title='הגדרת מחיר לפריט'><span class="glyphicon glyphicon-edit" style="font-size:12px; text-align:right;"></span></a></td>
        <td class="filterable-cell" style="width: 10%;"><?php echo $headertemplist->ItemQuantity ?> <a style="cursor:pointer;" data-toggle="tooltip" id="NewQuantityButton<?php echo $headertemplist->id ?>" data-placement="top" title='הגדרת כמות לפריט'><span class="glyphicon glyphicon-edit" style="font-size:12px; text-align:right;"></span></a> </td>
        <td class="filterable-cell" style="width: 10%;"><?php echo $headertemplist->ItemDiscount ?> <?php if ($headertemplist->ItemDiscountType=='1') { echo '%'; } else if ($headertemplist->ItemDiscountType=='2') { echo '₪'; } else { echo '%'; } ; ?> <a style="cursor:pointer;" data-toggle="tooltip" id="DiscountButton<?php echo $headertemplist->id ?>" data-placement="top" title='הגדרת הנחה לפריט'><span class="glyphicon glyphicon-edit" style="font-size:12px; text-align:right;"></span></a>
        
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
   var DiscountTempId = document.getElementById('DiscountTempId').value;
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
   var NewPriceTempId = document.getElementById('NewPriceTempId').value;
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
   var NewQuantityTempId = document.getElementById('NewQuantityTempId').value;
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
        <td class="filterable-cell" style="width: 15%;"><?php echo number_format($headertemplist->Itemtotal, 2) ?> ₪</td>

    </tr>  
    
    <tr class="discount<?php echo $headertemplist->id ?> info" style="display:none;">
    <td colspan="8" height="50" style="height:50px; width:100%; align-content:center;">
    
<form name="AddDiscountItem<?php echo $headertemplist->id ?>" id="AddDiscountItem<?php echo $headertemplist->id ?>" class="form-inline" style="align-content:center;" autocomplete="off">
<input name="DiscountTempList" id="DiscountTempList" type="hidden" value="<?php echo $headertemplist->id ?>">
<input name="DiscountTempId" id="DiscountTempId" type="hidden" value="<?php echo $headertemplist->TempId ?>">
<input name="DiscountPrice" id="DiscountPrice" type="hidden" value="<?php echo $headertemplist->ItemPrice ?>">
<input name="DiscountPriceItem" id="DiscountPriceItem" type="hidden" value="<?php echo number_format($headertemplist->Itemtotal, 2) ?>">
<input name="DiscountItemQuantity" id="DiscountItemQuantity" type="hidden" value="<?php echo $headertemplist->ItemQuantity ?>">

<div class="form-group">
<label class="radio-inline" style="text-decoration:underline;">סוג הנחה </label>               
<label class="radio-inline">
<input type="radio" name="DiscountsTypeItem" id="inlineRadio1" value="1" <?php if ($headertemplist->ItemDiscountType=='1') { echo 'checked'; } else {}  ; ?>> %
</label>
<label class="radio-inline">
<input type="radio" name="DiscountsTypeItem" id="inlineRadio2" value="2" <?php if ($headertemplist->ItemDiscountType=='2') { echo 'checked'; } else {} ; ?>> ₪
</label>
    </div>
    <div class="form-group">
                <label>הקלד הנחה</label>
                <input type="text" name="DiscountsItem" id="DiscountsItem" min="0" class="form-control" placeholder="מספרים בלבד" value="<?php echo $headertemplist->ItemDiscount ?>" onkeypress='validate(event)'>
    </div>

   <button type="submit" name="submit" class="btn btn-success"><?php _e('main.save_changes') ?></button>
   <button type="button" id="DiscountClose<?php echo $headertemplist->id ?>" class="btn btn-default"><?php _e('main.close') ?></button>
  </form>
    
    </td>
    
    </tr> 
    
    <tr class="NewPrice<?php echo $headertemplist->id ?> info" style="display:none;">
    <td colspan="8" height="50" style="height:50px; width:100%; align-content:center;">
    
<form name="NewPriceItem<?php echo $headertemplist->id ?>" id="NewPriceItem<?php echo $headertemplist->id ?>" class="form-inline" style="align-content:center;" autocomplete="off">
<input name="NewPriceTempList" id="NewPriceTempList" type="hidden" value="<?php echo $headertemplist->id ?>">
<input name="NewPriceTempId" id="NewPriceTempId" type="hidden" value="<?php echo $headertemplist->TempId ?>">

    <div class="form-group">
                <label>הקלד מחיר לפריט</label>
                <input type="text" name="NewPriceItem" id="NewPriceItem" min="0" class="form-control" placeholder="מספרים בלבד" value="<?php echo $headertemplist->ItemPrice ?>" onkeypress='validate(event)'>
    </div>

   <button type="submit" name="submit" class="btn btn-success"><?php _e('main.save_changes') ?></button>
   <button type="button" id="NewPriceClose<?php echo $headertemplist->id ?>" class="btn btn-default"><?php _e('main.close') ?></button>
  </form>
    
    </td>
    
    </tr> 
    
    
    <tr class="NewQuantity<?php echo $headertemplist->id ?> info" style="display:none;">
    <td colspan="8" height="50" style="height:50px; width:100%; align-content:center;">
    
<form name="NewQuantityItem<?php echo $headertemplist->id ?>" id="NewQuantityItem<?php echo $headertemplist->id ?>" class="form-inline" style="align-content:center;" autocomplete="off">
<input name="NewQuantityTempList" id="NewQuantityTempList" type="hidden" value="<?php echo $headertemplist->id ?>">
<input name="NewQuantityTempId" id="NewQuantityTempId" type="hidden" value="<?php echo $headertemplist->TempId ?>">

<input name="NewQuantityItemId" id="NewQuantityItemId" type="hidden" value="<?php echo $headertemplist->ItemId ?>">
<input name="NewQuantityItemTable" id="NewQuantityItemTable" type="hidden" value="<?php echo $headertemplist->ItemTable ?>">

    <div class="form-group">
    <label>הקלד כמות לפריט</label>
    <input type="number" name="NewQuantityItem" id="NewQuantityItem" min="0" class="form-control" placeholder="מספרים בלבד" value="<?php echo $headertemplist->ItemQuantity ?>" onkeypress='validate(event)'>
    </div>

   <button type="submit" name="submit" class="btn btn-success"><?php _e('main.save_changes') ?></button>
   <button type="button" id="NewQuantityClose<?php echo $headertemplist->id ?>" class="btn btn-default"><?php _e('main.close') ?></button>
  </form>

    </td>
    
    </tr> 
    
   <?php	} 
	
	
    $Vat = DB::table('temp')->where('id', '=', $TempId)->pluck('Vat');	
	$Discount = DB::table('temp')->where('id', '=', $TempId)->pluck('Discount');	
	$DiscountType = DB::table('temp')->where('id', '=', $TempId)->pluck('DiscountType');
	$Total = DB::table('templist')->where('TempId', '=', $TempId)->sum('Itemtotal');
	$Totals = DB::table('templist')->where('TempId', '=', $TempId)->sum('Itemtotal');
	 
	if ($DiscountType=='1'){
	
	/// %
	
	$NewDiscount = $Total*$Discount/100;
	$NewDiscount = round($NewDiscount,1);
	$Total = $Total-$NewDiscount;
	$DiscountType2 = '%';
	
	}
	
    else if ($DiscountType=='2') {
	
	$NewDiscount  = $Discount;
	$NewDiscount = round($NewDiscount,1);
	$Total = $Total-$Discount;
	$DiscountType2 = '₪';
	
		
	}
	
	else {
	
	$NewDiscount  = $Discount;
	$Total = $Total;
	$DiscountType2 = '%';	
		
	}
	
	if (!empty($Vat)){ 
	
	$Vats = '1.'.$Vat;
	$Vat = $Vat;
	
	}
	else {
	
	$Vats = '0';
	$Vat = '0';

	}
	
	
	$TotalVat = $Total/$Vats;
	$TotalVat = round($Total-$TotalVat,2);
	
	$Totals = $Totals-$NewDiscount;
	
	?> 
     
    </tbody>
    
</table>

<input id="Temptotal" name="Temptotal" type="hidden" value="<?php echo number_format((float)$Totals-$TotalVat, 2, '.', ''); ?>">
<input id="TempVat" name="TempVat" type="hidden" value="<?php echo number_format((float)$TotalVat, 2, '.', ''); ?>">

<input id="TempDiscount" name="TempDiscount" type="hidden" value="<?php echo number_format((float)$Discount, 2, '.', ''); ?>">
<input id="TempDiscount2" name="TempDiscount2" type="hidden" value="<?php echo $DiscountType2; ?>">
<input id="TempNEWDiscount" name="TempNEWDiscount" type="hidden" value="<?php echo number_format((float)$NewDiscount, 2, '.', ''); ?>">

<input id="VATIn" name="VATIn" type="hidden" value="<?php echo number_format((float)$Vat, 2, '.', ''); ?>">

<input id="Temptotal2" name="Temptotal2" type="hidden" value="<?php echo number_format((float)$Totals, 2, '.', ''); ?>">

<input id="TempId" name="TempId" type="hidden" value="<?php echo $TempId; ?>">


<?php

DB::table('temp')
        ->where('UserId', $UserId)
		->where('id', $TempId)
        ->update(array('Amount' => number_format((float)$Totals, 2, '.', ''),'VatAmount' => number_format((float)$TotalVat, 2, '.', ''), 'DiscountAmount' => number_format((float)$NewDiscount, 2, '.', '')));

?>

 <script>
 
$("#Items6").select2("val", ""); 
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

//$('#ChashRecived1').val(price2);
//$('#ChashRecived2').val(price2);
//$('#ChashRecived3').val(price2);
//$('#ChashRecived4').val(price2);


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


$(function() {
	$('[data-toggle="tooltip"]').tooltip()
});


});
 
 
 
 </script>
 
 

</div>

<?php endif ?>