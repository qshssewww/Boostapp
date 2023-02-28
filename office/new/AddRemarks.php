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
  <?php echo lang('class_start') ?>: <?php echo with(new DateTime($ClassInfo->StartTime))->format('H:i'); ?> 
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
 <label><?php echo lang('class_equipment_type') ?>:</label>
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
     <?php if (Auth::userCan('83')): ?>         
 <form action="AddClassRemarksPopUp"  class="ajax-form clearfix">
<input type="hidden" name="ClassIdCloseEvent" value="<?php echo $Id; ?>">
     
                 <div class="form-group" >
                <label><?php echo lang('class_show_class_in_app') ?></label>
                <select class="form-control" name="RemarksStatus">
                <option value="0" <?php if($ClassInfo->RemarksStatus=='0'){ echo 'selected'; } else {} ?>><?php echo lang('yes') ?></option>  
                <option value="1" <?php if($ClassInfo->RemarksStatus=='1'){ echo 'selected'; } else {} ?>><?php echo lang('no') ?></option>      
                </select>
                </div> 
     
		        <div class="form-group" >
                <label><?php echo lang('lesson_content') ?></label>
                <textarea name="Remarks" class="form-control" rows="3"><?php echo $ClassInfo->Remarks; ?></textarea>
                </div> 
     
     
                <div class="form-group" >
                <label><?php echo lang('class_update_lessons') ?></label>
                <select class="form-control" name="UpdateAllClass">
                <option value="0"><?php echo lang('yes') ?></option>  
                <option value="1" selected><?php echo lang('no') ?></option>      
                </select>
                </div> 
     
                <div class="form-group" >
                <label><?php echo lang('class_update_lesson_day') ?></label>
                <select class="form-control" name="UpdateAllClassDay">
                <option value="0"><?php echo lang('yes') ?></option>  
                <option value="1" selected><?php echo lang('no') ?></option>      
                </select>
                </div> 
     
     
				<div class="ip-modal-footer d-flex justify-content-between">
                 
                <button type="button" class="btn btn-dark text-white ip-close" data-dismiss='modal'><?php echo lang('cancle_or_close') ?></button> 
                 
                  <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-primary"><?php echo lang('save') ?></button>
                 </div>
                 
                </form>
      <?php endif ?> 
				</div>


