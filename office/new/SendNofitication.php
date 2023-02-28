<?php require_once '../../app/initcron.php'; 


$Id = $_REQUEST['Id'];
$CompanyNum = Auth::user()->CompanyNum;
$SettingsInfo = DB::table('settings')->where('CompanyNum' ,'=', Auth::user()->CompanyNum)->first();

$ClassInfo = DB::table('classstudio_date')->where('id','=', $Id)->where('CompanyNum', $CompanyNum)->first();
$Floor = DB::table('sections')->where('id','=', $ClassInfo->Floor)->where('CompanyNum', $CompanyNum)->first();
$ClassDeviceName = DB::table('numbers')->where('CompanyNum', $CompanyNum)->where('id', '=', $ClassInfo->ClassDevice)->where('Status', '=', '0')->first();

$ClassRegularCount = DB::table('classstudio_act')
->where('CompanyNum', '=', $CompanyNum)->where('ClassId', '=', $ClassInfo->id)->where('RegularClass', '=', '1')->whereIn('Status', array(9, 12))
->count();

if ($ClassInfo->ClassMemberType=='BA999'){
$MembershipType = lang('all_membership_types');   
}
else {
$z = '1';
$myArray = explode(',', $ClassInfo->ClassMemberType);	
$MembershipType = '';	
$SoftInfos = DB::table('membership_type')->where('CompanyNum', $CompanyNum)->whereIn('id', $myArray)->get();
$SoftCount = count($SoftInfos);
	
foreach ($SoftInfos as $SoftInfo){

$MembershipType .= $SoftInfo->Type;

if($SoftCount==$z){}else {	
$MembershipType .= ', ';	
}
	
++$z; 	
}	

$MembershipType = $MembershipType;
}
?>


            
 <div class="row">
 <div class="col-md-3">	 
 <?php echo $ClassInfo->ClassName ?> 
 </div>  
  <div class="col-md-3">	 
  <?php echo $ClassInfo->GuideName ?> 
 </div>  
 <div class="col-md-3">	 
 <?php echo $Floor->Title ?> 
 </div>   
  <div class="col-md-3">
 <?php if ($ClassInfo->MinClass=='0') { echo lang('without_min_patricipants'); } else { ?>   
  <?php echo lang('min_participants') ?>: <?php echo $ClassInfo->MinClassNum; } ?> 
 </div>  
</div>


 <div class="row">
 <div class="col-md-3">	 
 <?php echo lang('date') ?>: <?php echo with(new DateTime($ClassInfo->StartDate))->format('d/m/Y'); ?> 
 </div> 
  <div class="col-md-3">	 
  <?php echo lang('day') ?>: <?php echo $ClassInfo->Day ?> 
 </div>       
  <div class="col-md-3">	 
  <?php echo lang('class_start') ?> <?php echo with(new DateTime($ClassInfo->StartTime))->format('H:i'); ?> 
 </div>  
  <div class="col-md-3">	 
  <?php echo lang('class_end') ?>: <?php echo with(new DateTime($ClassInfo->EndTime))->format('H:i'); ?> 
 </div>  
</div>

  <hr>            
 <div class="row">
 <div class="col-md-<?php if ($ClassInfo->ClassDevice=='0'){ echo '12'; } else { echo '6'; }?>">	 
 <label><?php echo lang('class_membership_type') ?>:</label>
<?php echo $MembershipType; ?> 
 </div>  
<?php if ($ClassInfo->ClassDevice=='0'){} else {?>     
     <div class="col-md-6">	 
 <label><?php echo lang('eauipment_type_single') ?>:</label>
<?php echo @$ClassDeviceName->Name; ?> 
 </div>
<?php } ?>     
</div>
  <hr>            
 <div class="row">
 <div class="col-md-6">	 
 <label><?php echo lang('class_booking_num') ?>:</label>
<span style="font-weight:bold; color:forestgreen"><?php echo $ClassInfo->ClientRegister; ?> <?php echo lang('of_user_manage') ?> <?php echo $ClassInfo->MaxClient; ?> <?php echo lang('registered') ?> (<?php echo $ClassInfo->MaxClient-$ClassInfo->ClientRegister; ?> <?php echo lang('available_spaces') ?>)</span>
 </div>  
     
<div class="col-md-6">	 
<label><?php echo lang('w_list') ?>:</label>
<span style="font-weight:bold; color:orangered"><?php echo $ClassInfo->WatingList; ?> <?php echo lang('class_in_waitlist') ?></span>
 </div>   
     
</div>


<hr>
<!--     --><?php //if (Auth::userCan('87')): ?><!--         -->
 <form action="SendNotificationEventPopUp"  class="ajax-form clearfix">
<input type="hidden" name="ClassIdCloseEvent" id="ClassIdCloseEvent" value="<?php echo $Id; ?>">
     
     <div class="alertb alert-warning"><?php echo lang('class_send_notice') ?></div>	        
	

     
     			<div class="alertb alert-info" style="font-size: 12px;">
  				<strong><?php echo lang('option_to_use_params_inside_message') ?>:</strong><br>
  				<strong>[[<?php echo lang('full_name') ?>]]</strong> <?php echo lang('will_be_changed_in_client_full_name') ?><br>
  				<strong>[[<?php echo lang('first_name') ?>]]</strong> <?php echo lang('will_be_replaced_in_private_name') ?><br>
  				<strong>[[<?php echo lang('full_representative_name') ?>]]</strong> <?php echo lang('will_be_replaced_in_representative_fullname') ?>.<br>
  				<strong>[[<?php echo lang('representative_name') ?>]]</strong> <?php echo lang('will_be_replaced_in_representative_firstname') ?>.<br>
                <strong>[[<?php echo lang('studio_name') ?>]]</strong> <?php echo lang('will_be_replaced_in_studio_name') ?>   
  				</div>
     
                <div class="form-group" >
                <label><?php echo lang('class_send_options') ?></label>
                <select class="form-control" name="Type">
                <option value="0" selected><?php echo lang('free_push_message') ?></option> 
<!--                --><?php //if (Auth::userCan('88')): ?><!--      -->
                <option value="1"><?php echo lang('sms_message_pay') ?></option>
<!--                --><?php //endif ?><!--    -->
                <option value="2"><?php echo lang('email_free') ?></option>     
                </select>
                </div> 
     
                 <div class="form-group" >
                <label><?php echo lang('registered') ?></label>
                <select class="form-control" name="TypeSend">
                <option value="0" selected><?php echo lang('class_send_active') ?></option>  
                <option value="1"><?php echo lang('class_send_active_waiting') ?></option>      
                </select>
                </div> 
     
		        <div class="form-group" >
                <label><?php echo lang('class_send_message') ?><span  style="font-size: 12px;">(<span id="count"><?php echo lang('zero_chars_zero_messages') ?></span>)</span></label>
                <textarea name="Content" id="SmsContent" class="form-control" rows="3" maxlength="200"></textarea>
                </div> 
     
     
				<div class="ip-modal-footer d-flex justify-content-between">
                 
                <button type="button" class="btn btn-dark text-white ip-close" data-dismiss='modal'><?php echo lang('cancle_or_close') ?></button> 
                 
                  <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-danger"><?php echo lang('send') ?></button>
                 </div>
                 
                </form>
<!--      --><?php //endif ?><!-- -->
				</div>

<script>
		$("#SmsContent").keyup(function(){
  var LengthM = $(this).val().length;
  var LengthT = Math.ceil(($(this).val().length)/<?php echo $SettingsInfo->SMSLimit; ?>);
$("#count").text(LengthM + ' <?php echo lang('') ?>'+ LengthT + ' <?php echo lang(' messegaes_expected') ?>');
});
</script>
