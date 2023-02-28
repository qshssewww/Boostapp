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
מתעניין ב- : <?php echo $PipeInfo->ClassInfoNames; ?> 
 </div>  
</div>

<hr>
<!--There is an appropriate permission, or permission to edit leads users-->
<?php if (Auth::userCan('87') || ($ClientInfo->Status == 2 && Auth::userCan('158'))): ?>
<form action="SendPipeFormClient"  class="ajax-form clearfix">
<input type="hidden" name="ClientId" id="ClientId" value="<?php echo $ClientId; ?>">     
     
          		<div class="alertb alert-info" style="font-size: 12px;">
  				<?= lang('send_update_form_pipeline') ?>
  				</div>

                <div class="form-group" >
                <label><?= lang('sending_option') ?></label>
                <select class="form-control" name="TypeSend" id="TypeSend">
<!--                <option value="1">הודעת SMS (כרוך בעלויות נוספת)</option>-->
                <option value="2" selected><?= lang('email_free') ?></option>
                </select>
                </div> 
    
                <div class="form-group DivPipeEmailoDiv" >
                <label><?= lang('email') ?></label>
                <input type="text" name="Email" id="Email" class="form-control" value="<?php echo $ClientInfo->Email ?>" required>
                </div> 
     
				<div class="ip-modal-footer">
                 
                <button type="button" class="btn btn-dark text-white ip-close" data-dismiss='modal'><?= lang('cancel') ?></button>
                 
                  <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-primary"><?= lang('send') ?></button>
                 </div>
                 
                </form>
      <?php endif ?> 
				</div>




</div>

<script>

$("#TypeSend").change(function() {
var Id = this.value;   
if (Id=='2'){
$('.DivPipeEmailoDiv').css("display", "block");
$("#Email").prop('required',true);    
}  
else if (Id=='1'){
$('.DivPipeEmailoDiv').css("display", "none");
$("#Email").prop('required',false);       
}     
else {
$('.DivPipeEmailoDiv').css("display", "none");
$("#Email").prop('required',false);     
}    
    
    
     
 });
    
</script>

