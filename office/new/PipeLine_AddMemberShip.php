<?php require_once '../../app/initcron.php'; 


$Id = $_REQUEST['Id'];
$ClientId = $_REQUEST['ClientId'];

$CompanyNum = Auth::user()->CompanyNum;
$SettingsInfo = DB::table('settings')->where('CompanyNum' ,'=', $CompanyNum)->first();
$ClientInfo = DB::table('client')->where('id' ,'=', $ClientId)->where('CompanyNum' ,'=', $CompanyNum)->first();
$PipeInfo = DB::table('pipeline')->where('id' ,'=', $Id)->where('CompanyNum' ,'=', $CompanyNum)->first();


?>


            
 <div class="row">
 <div class="col-md-3">	 
 <?php echo $ClientInfo->CompanyName; ?> 
 </div>  
  <div class="col-md-3">	 
  <?php echo $ClientInfo->ContactMobile ?> 
 </div>  
 <div class="col-md-3">	 
 <?php echo $ClientInfo->Email ?> 
 </div>   
  <div class="col-md-3">
      <?php echo lang('intrested_in_pipeline') ?> <?php echo $PipeInfo->ClassInfoNames; ?>
 </div>  
</div>


<hr>



<hr>
            
 <form action="AddActivity" class="ajax-form text-right" dir="rtl" autocomplete="off">
<input type="hidden" name="ClientId" value="<?php echo $ClientId; ?>">
<input type="hidden" name="Vaild_LastCalss" value="1">     
     
              <div class="form-group">
              <label><?php echo lang('select_membership') ?> <em><?php _e('main.required') ?></em></label>
              <select name="Items1" id="Items1" class="form-control select22" style="width:100%;"  data-placeholder="<?php echo lang('select_membership') ?>"  >
              <option value=""></option>

<?php
if (@$ClientInfo->Status!='2') {     
$Activities = DB::table('items')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->orderBy('Department', 'ASC')->get();
}
else {
$Activities = DB::table('items')->where('CompanyNum', '=', $CompanyNum)->where('Department', '=', '3')->where('Status', '=', '0')->orderBy('Department', 'ASC')->get();    
}    
foreach ($Activities as $Activitie) {
$membership_type = DB::table('membership_type')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $Activitie->MemberShip)->first();  
if ($Activitie->MemberShip=='BA999'){
$Type = lang('no_membership_type');
} 
else {
$Type = $membership_type->Type;     
} 
    
	          ?>
              <option value="<?php echo $Activitie->id ?>"><?php echo $Type; ?> :: <?php echo $Activitie->ItemName; ?> - ₪<?php echo $Activitie->ItemPrice; ?></option>
              <?php } ?>
              
              </select>  
              
              </div>

			
			   <div class="form-group">
                <label><?php echo lang('membership_start') ?> <em><?php _e('main.required') ?></em></label>
                <input type="date" class="form-control focus-me" name="ClassDate" value="<?php echo date('Y-m-d'); ?>">
                </div>
     
				<div class="ip-modal-footer">
                 
                <button type="button" class="btn btn-dark text-white ip-close" data-dismiss='modal'><?php echo lang('close') ?></button>
                 
                  <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-primary text-white"><?php echo lang('save_changes_button') ?></button>
                 </div>
                 
</form>
</div>

<script>
$( ".select22" ).select2( { theme:"bootstrap",placeholder: "<?php echo lang('select_membership') ?>", minimumInputLength: 0,language: "he", allowClear: false, width: '100%' } );
</script>  