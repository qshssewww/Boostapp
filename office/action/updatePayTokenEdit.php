<?php
require_once '../../app/initcron.php';
require_once '../Classes/Client.php';
require_once '../Classes/Token.php';
?>

<?php

$CompanyNum = Auth::user()->CompanyNum;
$SettingsInfo = DB::table('settings')->where('CompanyNum' ,'=', $CompanyNum)->first();

$TokenId = $_REQUEST['TokenId'] ?? 0;
$RowId = $_REQUEST['RowId'];

$Tokens = DB::table('paytoken')->where('CompanyNum', '=', $CompanyNum)->where('id', '=' , $TokenId)->first();
$TokenRowId = DB::table('payment')->where('CompanyNum', '=', $CompanyNum)->where('id', '=' , $RowId)->first();


$TokenInfo = DB::table('token')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $Tokens->TokenId)->first();

$client = DB::table('client')->where('id', $Tokens->ClientId)->where('CompanyNum', '=', $CompanyNum)->first();

if ($Tokens->Status=='0'){
$TokensStatus = '<span class="text-success">פעיל</span>';    
}
else {
$TokensStatus = '<span class="text-danger">בוטל</span>';     
}

if ($Tokens->TypeKeva=='0'){
$TypeToken = 'מתחדש';    
}
else {
$TypeToken = 'מוגבל בחזרות';     
}

$UsersName = DB::table('users')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', @$Tokens->UserId)->first();

?>
       <style>
       
           .DivScroll::-webkit-scrollbar {
             width: 5px;
             padding-left: 0px;
             margin-left: 0px;
           } 
           
             .DivScroll::-webkit-scrollbar-thumb {
             background-color: darkgrey;
             outline: 1px solid slategray;
            padding-left: 0px;
             margin-left: 0px;     
           }    
           
       
</style>   

<div class="row">

<div class="col-md-4">פריט: <?php echo $Tokens->Text; ?></div>
<div class="col-md-3">סוג ה.קבע: <?php echo $TypeToken; ?></div>
<div class="col-md-3">הוגדר: <?php if (@$UsersName->display_name=='') { echo 'אוטומטי'; } else { echo $UsersName->display_name; } ?></div>     
<div class="col-md-2">סטטוס: <?php echo $TokensStatus; ?></div>    

</div>

	<div class="row">  
    <div class="col-md-12"> 
    <span style="float:left; padding-top:5px; padding-left:15px;"><a href="javascript:void(0);" id="KevaBack" class="btn btn-sm btn-dark text-white">חזור</a></span>         
    </div>    
    </div>    

<hr>

 <div class="col-md-12 DivScroll" style='min-height:320px; max-height:550px; overflow-y:scroll; overflow-x:hidden;'> 
     
                <input type="hidden" name="ClientId" value="<?php echo $Tokens->ClientId; ?>">
                <input type="hidden" name="PayTokenId" value="<?php echo $TokenId; ?>">
                <input type="hidden" name="RowId" value="<?php echo $RowId; ?>">
     
                <div class="form-group" dir="rtl">
                <label>חיוב מספר</label>
                <input type="text" class="form-control" value="<?php echo $TokenRowId->NumPayment; ?>" disabled>
                </div>
    
                <div class="form-group" dir="rtl">
                <label>סכום לחיוב</label>
                <input type="number" min="1" step="any" name="Amount" class="form-control" value="<?php echo $TokenRowId->Amount; ?>">
                </div>
    
                <div class="form-group" dir="rtl">
                <label>שינוי תאריך חיוב</label>
                <input type="date" name="NextPayment" class="form-control" value="<?php echo $TokenRowId->Date; ?>" min="<?php echo date('Y-m-d'); ?>">
                </div>
    
              <div class="form-group">
              <label>סטטוס </label>
              <select name="Status" id="Status" class="form-control" style="width:100%;"  data-placeholder="בחר סטטוס"  >
               <option value="0" <?php if ($TokenRowId->ActStatus=='0') { echo 'selected'; } else {} ?>>פעיל</option>
               <option value="1" <?php if ($TokenRowId->ActStatus=='1') { echo 'selected'; } else {} ?>>בוטל</option>

              </select>  
              
              </div>
    
    
              <div class="form-group">
              <label>ערוך כסדרה? </label>
              <select name="EditKevaAll" id="EditKevaAll" class="form-control">
               <option value="0" selected>לא, ערוך חיוב זה בלבד</option>
               <option value="1">כן, ערוך את כל החיובים מחיוב זה והלאה</option>
               </select>  
               </div>
    
               
    
    
    
               <div id="DivEditNumPayment" style="display: none;">
                   
               <a href="javascript:void(0);" id="EditAdvanceKevaAll">הצג הגדרות מתקדמות</a>
                   
               <div id="DivEditNumPaymentAdvance" style="display: none;">  
                   
               <?php 
               if ($TokenRowId->TypeKeva=='0') {
               ?>
    
 	          <div class="form-group">
              <label>משויך לפריט</label>
              <select name="ItemId" id="ItemsKevaEdit" class="form-control" style="width:100%;"  data-placeholder="בחר פריט">
			  <option value="">בחר</option>
              <?php 
              $PagesInfos = DB::table('items')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', 0)->where('isPaymentForSingleClass', '=', 0)->orderBy('ItemName', 'ASC')->get();
             
              foreach ($PagesInfos as $PagesInfo) {      
              if ($PagesInfo->MemberShip!='BA999'){     
              $DepartmentInfo = DB::table('membership_type')->where('CompanyNum', $CompanyNum)->where('id','=', $PagesInfo->MemberShip)->first();    
              }
              ?>      
                  
              <option value="<?php echo $PagesInfo->id; ?>" <?php if ($PagesInfo->id==$Tokens->ItemId) { echo 'selected'; } else {} ?> ><?php if ($PagesInfo->MemberShip!='BA999'){   echo @$DepartmentInfo->Type; ?> :: <?php } ?> <?php echo $PagesInfo->ItemName; ?> :: <?php echo $PagesInfo->ItemPrice; ?> ₪</option>
              <?php } ?>
                  
              </select>  
              </div>   
         
              <?php } else { ?>
              <input type="hidden" name="ItemId" value="<?php echo $Tokens->ItemId; ?>">
              <?php } ?>      
                   
                   
                   
               <?php if(Auth::user()->role_id == 1) { ?>
               <div class="row">
               <div class="col-md-6 col-sm-12">

               <div class="form-group" dir="rtl">
                           <label>חשב תאריך חיוב הבא כל</label>
                           <input type="number" name="NumDate" max="10" min="1" class="form-control" value="<?php echo $Tokens->NumDate; ?>" onkeypress='validate(event)' >
                           </div>
                </div>
               
                
               <div class="col-md-6 col-sm-12">         
                   <div class="form-group">
                              <label>בחר אפשרות</label>
                              <select name="PayStep" class="form-control" style="width:100%;"  data-placeholder="בחר אפשרות חיוב">
                              <option value="1" <?php if ($Tokens->TypePayment=='1') { echo 'selected'; } else {} ?>>ימים</option>
                              <option value="2" <?php if ($Tokens->TypePayment=='2') { echo 'selected'; } else {} ?>>שבועות</option>
                              <option value="3" <?php if ($Tokens->TypePayment=='3') { echo 'selected'; } else {} ?>>חודשים</option>
                              <option value="4" <?php if ($Tokens->TypePayment=='4') { echo 'selected'; } else {} ?>>שנים</option>      
                              </select>  
                              </div>
                </div>

                </div>
               <?php } ?>
    
              <div class="form-group">
              <label>עדכון כ.אשראי </label>
              <select name="TokenId" id="TokenId" class="form-control" style="width:100%;"  data-placeholder="בחר כרטיס אשראי">

                  <?php
                  $clientIdsArr = [$Tokens->ClientId];
                  if ($client->parentClientId != 0) {
                      $clientIdsArr[] = $client->parentClientId;
                  }
                  if ($client->PayClientId != 0) {
                      $clientIdsArr[] = $client->PayClientId;
                  }
                  $TokenInfos = Token::where('CompanyNum', '=', $CompanyNum)
                      ->whereIn('ClientId', $clientIdsArr)
                      ->where('Status', '=', '0')
                      ->get();

                  foreach ($TokenInfos as $TokenInfo) {
                      $CardTokef = $TokenInfo->Tokef ?? '';

                      if ($TokenInfo->Type == '0') {
                          $L4digit = substr($TokenInfo->Token, -4);
                          $Month = mb_substr($CardTokef, 2);
                          $Year = '20' . mb_substr($CardTokef, 0, 2);
                      } else {
                          $L4digit = $TokenInfo->L4digit;
                          $Month = mb_substr($CardTokef, 0, 2);
                          $Year = '20' . mb_substr($CardTokef, 2);
                      }


                      ?>
                  
              <option value="<?php echo $TokenInfo->id; ?>" <?php echo ($Tokens->TokenId == $TokenInfo->id) ? 'selected' : '' ?>>
                  <?= '****' . $L4digit ?> ::
                  <?= $Month ?>/
                  <?= $Year ?>
                  <?php
                  if($client->parentClientId == $TokenInfo->ClientId || $client->PayClientId == $TokenInfo->ClientId) {
                      $payerName = new Client($TokenInfo->ClientId);
                      echo ' - '.lang('card_of').' '.$payerName->CompanyName;
                  }
                  ?>
              </option>
              <?php } ?>
                  
              </select>  
              </div>       
              
              <input type="hidden" name="tashTypeKeva" value="0">
              <input type="hidden" name="TashKeva" value="1">       
                   
                   
              <div class="row" style="display: none;">
                  
               <div class="col-md-6 col-sm-12">         
               <div class="form-group">
               <label>בחר סוג תשלום</label>
               <select name="tashTypeKeva_old" id="tashTypeKeva" class="form-control tashTypeKevap" style="width:100%;"  data-placeholder="בחר אפשרות תשלום">
               <option value="0" <?php if ($Tokens->tashType=='0') { echo 'selected'; } else {} ?>>רגיל</option>
               <option value="1" <?php if ($Tokens->tashType=='1') { echo 'selected'; } else {} ?>>תשלומים</option>
               <option value="6" <?php if ($Tokens->tashType=='6') { echo 'selected'; } else {} ?>>קרדיט</option>     
               </select>  
               </div>
                </div>  
                  
               <div class="col-md-6 col-sm-12">

               <div class="form-group" dir="rtl">
               <label>מספר תשלומים בכרטיס אשראי</label>
               <select name="TashKeva_old" id="TashKeva" class="form-control TashKevap" style="width:100%;"  data-placeholder="בחר מספר תשלומים">
               <option value="1" <?php if ($Tokens->Tash=='0') { echo 'selected'; } else {} ?>>1</option>   
               </select> 
               </div>
               </div>


                </div>
    
           
                   
            </div>       
                
              <div class="alertb alert-danger" dir="rtl">
             <strong>שים לב! בעת עריכה כסדרה</strong><br>
             <ul>
             <li>ישפיע על כל החיובים העתיידים.</li>     
             <li>שינוי תאריך חיוב הבא יעדכן את יום החיוב הקבוע.</li>          
             </ul>
                 
             </div>       
                   
                   
            </div>
          


</div>


<script>
$( "#ItemsKevaEdit" ).select2( {theme:"bootstrap", placeholder: "Select a State", 'language':"he", dir: "rtl" } );        
$("#KevaBack").click(function(){
        
   document.getElementById("resultPayToken").innerHTML=" ";
   $('#resultPayToken').load('/office/action/updatePayToken.php?TokenId=<?php echo $TokenId; ?>'); 
   $('#ShowSaveKeva').hide(); 
   var modal = $('#PayTokenEditPopup');    
   modal.find('.alert').hide();    
 });  
  

$("#EditKevaAll").change( function()
{

var Id = $(this).val();

if (Id=='0') {
$('#DivEditNumPayment').hide();   
}    
else {
$('#DivEditNumPayment').show();    
}    
    
}
);     
    
    
$("#EditAdvanceKevaAll").click( function()
{

if($('#DivEditNumPaymentAdvance').is(":hidden"))
{   
$('#DivEditNumPaymentAdvance').show();   
}
else {
$('#DivEditNumPaymentAdvance').hide();      
}    
     
  
    
}
);      
    
    
</script>
