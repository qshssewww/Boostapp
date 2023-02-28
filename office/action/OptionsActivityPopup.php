<?php
require_once __DIR__ . '/../../app/initcron.php';

require_once __DIR__ . '/../Classes/ClientActivities.php';
require_once __DIR__ . '/../Classes/Client.php';
require_once __DIR__ . '/../Classes/Item.php';

if (Auth::userCan('55')):

$ItemId = $_REQUEST['ItemId'];
$CompanyNum = Auth::user()->CompanyNum;
/** @var ClientActivities $Items */
$Items = ClientActivities::find($ItemId);
/** @var Item $itemObj */
$itemObj = Item::find($Items->ItemId);
$SettingsInfo = DB::table('settings')->where('CompanyNum' ,'=', $CompanyNum)->first();
$Clients = Client::find($Items->ClientId);

if ($Items->Department=='1' || $Items->Department=='2' || $Items->Department=='3'){
$readonly = 'readonly';    
}
else {
$readonly = '';    
}


?>

                <span class="text-center font-weight-bold"><?php echo $Items->ItemText; ?> // מנוי מספר <?php echo $Items->CardNumber; ?></span>
                <hr>

                <div class="form-group" dir="rtl">
                <label><?php echo lang('select_action') ?></label>
                <select class="form-control" name="SelectOption" id="SelectOption">
                <?php if ($Items->Status=='2'){ ?>    
                <option value=""><?php echo lang('choose') ?></option>
                <!--todo-bp-909 (cart) remove-beta-->
                <?php if (Auth::userCan('60') && !in_array($SettingsInfo->beta, [1])): ?>
                <option value="7"><?php echo lang('manage_membership_activity') ?></option>
                <?php endif ?>
                <?php } else { ?>
                <option value="">בחר</option> 
                <?php if ($Items->BalanceMoney == ($Items->ItemPriceVatDiscount+$Items->VatAmount)) { ?>
<!--                        todo-bp-909 change after test - in_array($CompanySettingsDash->beta, [1])-->
                <?php if (Auth::userCan('123') && !in_array($SettingsInfo->beta, [1])): ?>
                <option value="1"><?php echo lang('settings_edit_membership') ?></option>
                <?php endif ?>    
                <?php } ?>       
                <?php if ($Items->Department=='1' || ($Items->Department == '2' && $Items->TrueDate != '') || ($Items->Department == '3' && $Items->TrueDate != '')) { ?>
                <?php if (Auth::userCan('56') && $Items->Freez != 1 && $Items->TrueDate >= date('Y-m-d')): ?>
                    <option value="2"><?php echo lang('freeze_membership_activity') ?></option>
                <?php endif ?>
                <?php if (Auth::userCan('58')): ?> 
                <option value="8"><?php echo lang('change_membership_activity') ?></option>
                <option value="3"><?php echo lang('change_membership_new_activity') ?></option>
                <?php endif ?>    
                <?php } ?>
                <?php if (in_array($Items->Department, [2,3]) && $Items->isForMeeting != 1) { ?>
                <?php if (Auth::userCan('59')): ?>      
                <option value="4"><?php echo lang('add_sub_subtract_class') ?></option>
                <?php endif ?>    
                <?php } ?>   
                <?php if (in_array($Items->Department, [1,2]) && $Items->isForMeeting != 1) { ?>
                <option value="6"><?php echo lang('family_membersip') ?></option>
                <?php } ?>
                <!-- todo-bp-909 (cart) remove-beta-->
                <?php if (
                        $Items->isForMeeting != 1
                        && Auth::userCan('60')
                        && !in_array($SettingsInfo->beta, [1])
                        && $Items->Department != Item::DEPARTMENT_TRIAL
                    ){ ?>
                <option value="7"><?php echo lang('manage_membership_activity') ?></option>
                <?php } ?>
                    
                <?php if ((int)$itemObj->isPaymentForSingleClass !== 1 && Auth::userCan('60')): ?>
                <option value="5"><?php echo lang('cancel_membership_activity') ?></option>
                <?php endif ?>     
                <?php } ?>    
                </select>
                </div>   
                <hr>



                <div id="Type1" style="display: none;">
                
 <form action="AddDiscountActivity" class="ajax-form" id="ajax-AddDiscountActivity">
  <input type="hidden" name="ClientId" value="<?php echo @$Items->ClientId ?>">
  <input type="hidden" name="ActivityId" value="<?php echo @$Items->id ?>">
  
  
  <div class="form-group" dir="rtl">
                <label><?php echo lang('customer_card_table_membership') ?></label>
                <input type="text" name="ItemText" min="0" class="form-control" value="<?php echo $Items->ItemText; ?>" <?php echo $readonly; ?> >
                </div>  
   
     	       <div class="row">   
 <div class="col-md-12">
                <div class="form-group">
                <label><?php echo lang('price') ?></label>
                <input type="text" name="ItemPrice" class="form-control" onkeypress='validate(event)' value="<?php echo $Items->ItemPrice; ?>">
                </div>     
                    
                </div>
     </div>
     
<div class="form-group" dir="rtl">   
<label class="radio-inline" style="text-decoration:underline;"><?php echo lang('discount_type') ?> </label>
<label class="radio-inline">
<input type="radio" name="DiscountsType" id="inlineRadio1" value="1" <?php if (@$Items->DiscountType=='0' || @$Items->DiscountType=='1') { echo 'checked'; } else {} ?>> %
</label>
<label class="radio-inline">
<input type="radio" name="DiscountsType" id="inlineRadio2" value="2" <?php if (@$Items->DiscountType=='2') { echo 'checked'; } else {} ?>> ₪
</label>
</div>



<div class="form-group" dir="rtl">
                <label><?php echo lang('discount') ?></label>
                <input type="number" name="Discounts" min="0" class="form-control" placeholder="<?php echo lang('only_numbers') ?>" value="<?php if (@$Items->DiscountType!='0') { echo @$Items->Discount; } else {  } ?>">
                </div> 


				<div class="ip-modal-footer">
                 <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-primary"><?php _e('main.save_changes') ?></button>
                 </div>    
				<button type="button" class="btn btn-dark ip-close ip-closePopUp" data-dismiss="modal"><?php _e('main.close') ?></button>
				</div>   
                
 </form>     
                    
                </div> 

                <div id="Type2" style="display: none;">
                    
                  <form action="AddFreez" class="ajax-form" id="ajax-AddFreez">
  <input type="hidden" name="ClientId" value="<?php echo @$Items->ClientId ?>">
  <input type="hidden" name="ActivityId" value="<?php echo @$Items->id ?>">
  

                <div class="form-group">
                <label><?php echo lang('freeze_start_date') ?> <em><?php echo lang('req_field') ?></em></label>
                <input type="date" class="form-control focus-me" id="ClassDate" name="ClassDate" value="<?php echo date('Y-m-d'); ?>">
                </div>
 
                <div class="form-group">
                <label><?php echo lang('freeze_end_date') ?> <em><?php echo lang('req_field') ?></em></label>
                <input type="date" min="<?php echo date('Y-m-d'); ?>" class="form-control" id="ClassDateEnd" name="ClassDateEnd" value="<?php echo date('Y-m-d'); ?>">
                </div>

  <div class="form-group">
  <label><?php echo lang('freeze_reason') ?></label>
  <textarea class="form-control" name="Reason" rows="2"></textarea>
  </div>                      
                      
                      
				<div class="ip-modal-footer">
                 <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-primary"><?php _e('main.save_changes') ?></button>
                 </div>    
				<button type="button" class="btn btn-dark ip-close ip-closePopUp" data-dismiss="modal"><?php _e('main.close') ?></button>
				</div>   
                
 </form>     
                    
                </div> 

                <div id="Type3" style="display: none;">
                    
                 <form action="AddDateCalss" class="ajax-form" id="ajax-AddDateCalss">
  <input type="hidden" name="ClientId" value="<?php echo @$Items->ClientId ?>">
  <input type="hidden" name="ActivityId" value="<?php echo @$Items->id ?>">                  
  

  <div class="form-group">
  <label><?php echo lang('membership_end_date') ?> <em><?php _e('main.required') ?></em></label>
  <input type="date" class="form-control" name="ClassDate">
   </div>
 
   <div class="form-group">
  <label><?php echo lang('change_date_reason') ?></label>
  <textarea class="form-control" name="Reason" rows="2"></textarea>
  </div>      
                     
                     
 
				<div class="ip-modal-footer">
                 <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-primary"><?php _e('main.save_changes') ?></button>
                 </div>    
				<button type="button" class="btn btn-dark ip-close ip-closePopUp" data-dismiss="modal"><?php _e('main.close') ?></button>
				</div>   
                
 </form>        
                    
                </div> 


<div id="Type8" style="display: none;">
                    
                 <form action="AddStartDateCalss" class="ajax-form" id="ajax-AddDateCalss">
  <input type="hidden" name="ClientId" value="<?php echo @$Items->ClientId ?>">
  <input type="hidden" name="ActivityId" value="<?php echo @$Items->id ?>">                  

<div class="alertb alert-info"><?php echo lang('notice_date_new') ?></div>
                     
                     
<input type="date" class="form-control" value="<?php echo @$Items->StartDate; ?>" disabled>  
  <div class="form-group">
  <label><?php echo lang('change_membership_activity') ?> <em><?php _e('main.required') ?></em></label>
  <input type="date" class="form-control" name="ClassDate">
   </div>
 
   <div class="form-group">
  <label><?php echo lang('change_date_reason') ?></label>
  <textarea class="form-control" name="Reason" rows="2"></textarea>
  </div>      
                     
                     
 
				<div class="ip-modal-footer">
                 <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-primary"><?php _e('main.save_changes') ?></button>
                 </div>    
				<button type="button" class="btn btn-dark ip-close ip-closePopUp" data-dismiss="modal"><?php _e('main.close') ?></button>
				</div>   
                
 </form>        
                    
                </div> 



                <div id="Type4" style="display: none;">
                    
 <form action="AddCalss" class="ajax-form" id="ajax-AddCalss">
  <input type="hidden" name="ClientId" value="<?php echo @$Items->ClientId ?>">
  <input type="hidden" name="ActivityId" value="<?php echo @$Items->id ?>">                   
 
 
  <div class="form-group">
                <label><?php echo lang('add_subtract') ?></label>
                <select name="Act" class="form-control">
                
                <option value="0" selected><?php echo lang('add_single') ?></option>
                <option value="1"><?php echo lang('deduction_single') ?></option>
                
              </select>
                </div>
 
 
 <div class="form-group">
                <label><?php echo lang('number_of_class') ?> <em><?php _e('main.required') ?></em></label>
                <input type="number" min="0" value="0" class="form-control" name="ClassNumber" id="ClassNumber">
                </div>
 
   <div class="form-group">
  <label><?php echo lang('change_date_reason') ?></label>
  <textarea class="form-control" name="Reason" rows="2"></textarea>
  </div>      

 
     
				<div class="ip-modal-footer">
                 <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-primary"><?php _e('main.save_changes') ?></button>
                 </div>    
				<button type="button" class="btn btn-dark ip-close ip-closePopUp" data-dismiss="modal"><?php _e('main.close') ?></button>
				</div>   
     
     
 </form>     
                    
                    
                </div> 

                <div id="Type5" style="display: none;">
                  <?php if ((int)$itemObj->isPaymentForSingleClass !== 1 && Auth::userCan('60')): ?>
                  <form action="CancelNewActivity" class="ajax-form" id="ajax-CancelNewActivity">
  <input type="hidden" name="ClientId" value="<?php echo @$Items->ClientId ?>">
  <input type="hidden" name="ActivityId" value="<?php echo @$Items->id ?>">
  
                  
   <div class="alertb alert-info"><?php echo lang('notice_client_payment') ?> <?php echo @$Items->BalanceMoney; ?> ש"ח.</div>
                      
                <div class="form-group">
                <label><?php echo lang('customer_debt_offset') ?></label>
               <select name="MinusMoney" class="form-control" style="width:100%;"  data-placeholder="<?php echo lang('choose') ?>" required>
               <option value=""><?php echo lang('choose') ?></option>
              <?php if (Auth::userCan('61')): ?>       
               <option value="0"><?php echo lang('yes') ?></option>
               <?php endif ?>       
               <option value="1"><?php echo lang('no') ?></option>
               </select> 
               </div>                      
                      
              <div class="alertb alert-warning"><?php echo lang('note_options_activity') ?><br>
                  <?php echo lang('for_action_charege') ?></div>
                      
    <hr>     
    
                      
      <div class="form-group">
      <label><?php echo lang('remove_permanent_booking') ?></label>
      <select name="Act" class="form-control">
      <option value="0" selected><?php echo lang('no') ?></option>
      <option value="1"><?php echo lang('yes') ?></option>
      </select>
      </div>  
                      
     <div class="alertb alert-warning"><?php echo lang('activity_cant_restore') ?><br>
         <?php echo lang('will_cancel_meeting_without_cancellation_policy') ?></div>
                      
    <hr>                       
                      
  <div class="form-group">
  <label><?php echo lang('change_date_reason') ?></label>
  <textarea class="form-control" name="Reason" rows="2"></textarea>
  </div>  
                      
<hr>                      
                      
 <div class="form-group">
     <?php echo lang('q_sure_cancel_membership') ?>
 </div>

<div class="alertb alert-warning"><?php echo lang('note_cant_restore_action') ?></div>
 
          
				<div class="ip-modal-footer">
                 <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-danger"><?php echo lang('yes') ?></button>
                 </div>    
				<button type="button" class="btn btn-dark ip-close ip-closePopUp" data-dismiss="modal"><?php _e('main.close') ?></button>
				</div>   
                
 </form>     
 <?php endif;?>
                    
                    
                </div> 




  <div id="Type6" style="display: none;">
                    
  <form action="AddMultiClients" class="ajax-form" id="ajax-AddMultiClients">
  <input type="hidden" name="ClientId" value="<?php echo @$Items->ClientId ?>">
  <input type="hidden" name="ActivityId" value="<?php echo @$Items->id ?>">                  
  

  <div class="form-group">
  <label><?php echo lang('choose_client') ?> <em><?php _e('main.required') ?></em></label>
  <select name="AddClientMultiActivity[]" id="AddClientMultiActivity" multiple="multiple" data-placeholder="בחר לקוח" class="form-control select2ClientMulti" style="width:100%;">
  <option value=""></option> 
      

      
  </select>      
  </div>

    <div class="alertb alert-info"><?php echo lang('selected_client_order_class') ?></div>
      
   
   <div class="form-group">
   <label><?php echo lang('membership_limits') ?></label>
   <select name="LimitMultiActivity" class="form-control" style="width:100%;">    
   <option value="1" <?php if ($Items->LimitMultiActivity=='1') { echo 'selected'; } else {} ?> ><?php echo lang('one_restriction') ?></option>
   <option value="0" <?php if ($Items->LimitMultiActivity=='0') { echo 'selected'; } else {} ?> ><?php echo lang('apply_restriction_for_client') ?></option>
   </select>
   </div>  
      
      
 
				<div class="ip-modal-footer">
                 <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-primary"><?php _e('main.save_changes') ?></button>
                 </div>    
				<button type="button" class="btn btn-dark ip-close ip-closePopUp" data-dismiss="modal"><?php _e('main.close') ?></button>
				</div>   
                
 </form> 
      
 </div> 



  <div id="Type7" style="display: none;">
                    
  <form action="EditMenegmentMemberShip" class="ajax-form" id="ajax-EditMenegmentMemberShip">
  <input type="hidden" name="ClientId" value="<?php echo @$Items->ClientId ?>">
  <input type="hidden" name="ActivityId" value="<?php echo @$Items->id ?>">                  
  

                <div class="form-group">
                <label><?php echo lang('update_customer_debt') ?></label>
                <input type="text" name="ItemPrice" class="form-control" onkeypress='validate(event)' value="<?php echo $Items->BalanceMoney; ?>" required>
                </div>   
      
      
                 <div class="form-group">
                <label><?php echo lang('q_standing_order_subscription') ?></label>
               <select name="KevaAction" class="form-control" style="width:100%;">    
               <option value="1" <?php if ($Items->KevaAction=='1') { echo 'selected'; } else {} ?> ><?php echo lang('yes') ?></option>
               <option value="0" <?php if ($Items->KevaAction=='0') { echo 'selected'; } else {} ?> ><?php echo lang('no') ?></option>
                </select>
                </div>
      
      
                <div class="form-group">
                <label><?php echo lang('status_table') ?></label>
                
               <select name="Status" class="form-control" style="width:100%;">
               <?php if($Clients->Status =="0"): ?>
               <option value="0" <?php if ($Items->Status=='0') { echo 'selected'; } else {} ?> ><?php echo lang('active') ?></option>
               <?php endif; ?>
                <option value="3" <?php if ($Items->Status=='3') { echo 'selected'; } else {} ?> ><?php echo lang('completed_client_profile') ?></option>
               <option value="2" <?php if ($Items->Status=='2') { echo 'selected'; } else {} ?>><?php echo lang('canceled') ?></option>
               </select>
                </div>
    
              <div class="form-group">
              <label><?php echo lang('remove_permanent_booking') ?></label>
              <select name="Act" class="form-control">
              <option value="0" selected><?php echo lang('no') ?></option>
              <option value="1"><?php echo lang('yes') ?></option>
              </select>
              </div>

           <div class="alertb alert-warning"><?php echo lang('activity_cant_restore') ?><br>
               <?php echo lang('will_cancel_meeting_without_cancellation_policy') ?></div>
                      
            <hr> 
 
				<div class="ip-modal-footer">
                 <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-primary"><?php _e('main.save_changes') ?></button>
                 </div>    
				<button type="button" class="btn btn-dark ip-close ip-closePopUp" data-dismiss="modal"><?php _e('main.close') ?></button>
				</div>   
                
 </form> 
      
 </div> 




				<div class="ip-modal-footer" id="OptionClose">
                 <div class="ip-actions">
               
                 </div>    
				<button type="button" class="btn btn-dark ip-close ip-closePopUp" data-dismiss="modal"><?php _e('main.close') ?></button>
				</div>  


<script>
 
    
 $("#SelectOption").change(function() {
  
  var Id = this.value;
  if (Id=='1'){    
  Type1.style.display = "block";    
  Type2.style.display = "none";
  Type3.style.display = "none"; 
  Type4.style.display = "none";
  Type5.style.display = "none";
  Type6.style.display = "none"; 
  Type7.style.display = "none";
  Type8.style.display = "none";      
  OptionClose.style.display = "none";      
  }
  else if (Id=='2') {
  Type1.style.display = "none";    
  Type2.style.display = "block";
  Type3.style.display = "none"; 
  Type4.style.display = "none";
  Type5.style.display = "none";
  Type6.style.display = "none";
  Type7.style.display = "none";
  Type8.style.display = "none";      
  OptionClose.style.display = "none";       
  }   
  else if (Id=='3') {
  Type1.style.display = "none";    
  Type2.style.display = "none";
  Type3.style.display = "block"; 
  Type4.style.display = "none";
  Type5.style.display = "none";
  Type6.style.display = "none"; 
  Type7.style.display = "none";
  Type8.style.display = "none";      
  OptionClose.style.display = "none";       
  }   
  else if (Id=='4') {
  Type1.style.display = "none";    
  Type2.style.display = "none";
  Type3.style.display = "none"; 
  Type4.style.display = "block";
  Type5.style.display = "none"; 
  Type6.style.display = "none"; 
  Type7.style.display = "none"; 
  Type8.style.display = "none";      
  OptionClose.style.display = "none";       
            
  } 
  else if (Id=='5') {
  Type1.style.display = "none";    
  Type2.style.display = "none";
  Type3.style.display = "none"; 
  Type4.style.display = "none";
  Type5.style.display = "block";
  Type6.style.display = "none"; 
  Type7.style.display = "none";
  Type8.style.display = "none";      
  OptionClose.style.display = "none";       
  } 
  else if (Id=='6') {
  Type1.style.display = "none";    
  Type2.style.display = "none";
  Type3.style.display = "none"; 
  Type4.style.display = "none";
  Type5.style.display = "none";
  Type6.style.display = "block";
  Type7.style.display = "none";
  Type8.style.display = "none";      
  OptionClose.style.display = "none";       
  }  
  else if (Id=='7') {
  Type1.style.display = "none";    
  Type2.style.display = "none";
  Type3.style.display = "none"; 
  Type4.style.display = "none";
  Type5.style.display = "none";
  Type6.style.display = "none";
  Type7.style.display = "block";
  Type8.style.display = "none";      
  OptionClose.style.display = "none";       
  }
  else if (Id=='8') {
  Type1.style.display = "none";    
  Type2.style.display = "none";
  Type3.style.display = "none"; 
  Type4.style.display = "none";
  Type5.style.display = "none";
  Type6.style.display = "none";
  Type7.style.display = "none";
  Type8.style.display = "block";      
  OptionClose.style.display = "none";       
  }     
  else {
  Type1.style.display = "none";    
  Type2.style.display = "none";
  Type3.style.display = "none"; 
  Type4.style.display = "none";
  Type5.style.display = "none";
  Type6.style.display = "none";
  Type7.style.display = "none";      
  Type8.style.display = "none";  
  OptionClose.style.display = "block";       
  }  
     
     
     
    $( ".select2ClientMulti" ).select2( {
		theme:"bootstrap", 
		placeholder: "חפש לקוח",
		language: "he",
		width: '100%',
     ajax: {
            url: 'SearchClient.php',
            type: 'POST',
            dataType: 'json',
            cache: true
        },
		minimumInputLength: 3,
        dir: "rtl" } );  
 
     
  <?php 

   if ($Items->TrueClientId!='0'){ 
   $myArray = explode(',', $Items->TrueClientId); 
   $GetClientMulits = DB::table('client')->where('CompanyNum','=',$CompanyNum)->whereIn('id', $myArray)->orderBy('id', 'ASC')->get();
   foreach ($GetClientMulits as $GetClientMulit) {      
       
    $CompanyName = str_replace('"',"``",@$GetClientMulit->CompanyName); 
    $CompanyName = str_replace("'","`",@$CompanyName);     
       
   ?>    
   $('#AddClientMultiActivity').append('<option value="<?php echo @$GetClientMulit->id; ?>" selected><?php echo htmlentities(@$CompanyName); ?></option>').trigger('change');  
     
   <?php } } ?>      
     
  
     
});	
    
  
  
</script>

<?php endif ?>
