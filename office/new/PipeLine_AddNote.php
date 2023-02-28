<?php require_once '../../app/initcron.php'; 


$Id = $_GET['Id'];
$ClientId = $_GET['ClientId'];

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

 <ul class="timeline list">
<?php
$NotesList = DB::table('clientcrm')->where('ClientId', '=', $ClientId)->where('CompanyNum' ,'=', $CompanyNum)->orderBy('Dates', 'DESC')->get();
$i = '1';
foreach ($NotesList as $ClassAct) {
$UsersName = DB::table('users')->where('id', '=', $ClassAct->User)->first()
?>
        <li>
          <div class="timeline-panel" style="font-size: 12px;">
            <div class="timeline-body">
              <div style="padding:10px;">
              <?php echo @$ClassAct->Remarks; ?>
              </div>
            </div>
            
            <div class="timeline-footer" style="padding: 0;margin: 0;padding: 10px; background-color: white;">
              <div class="row">
              <div class="col-md-6 col-sm-12">
                <a class="pull-right"><?php echo @$UsersName->display_name; ?></a>
              </div>
              <div class="col-md-6 col-sm-12">
                <a class="float-left" dir="ltr"><?php echo with(new DateTime($ClassAct->Dates))->format('d/m/Y H:i'); ?></a>
              </div>
              </div>
            </div>
          </div>
        </li>

<?php } ?>
</ul>

<hr>
            
 <form action="AddCRM" class="ajax-form text-right <?php if(isset($_GET['noReload']) && $_GET['noReload']) echo "js-no-reload" ?>" dir="rtl" autocomplete="off">
<input type="hidden" name="ClientId" value="<?php echo $ClientId; ?>">
     
<div class="form-group">
<label><?php echo lang('add_note_pipeline') ?></label>
<textarea name="Remarks" id="Remarks1" class="form-control" rows="3" dir="rtl"></textarea>
</div>
     
     
<div class="form-group">
<label><?php echo lang('important_note_star') ?></label>
<select name="StarIcon" class="form-control">
<option value="0"><?php echo lang('no') ?></option>
<option value="1"><?php echo lang('yes') ?></option>
</select>  
</div>        
     
<div class="form-group">
<label><?php echo lang('until_date') ?></label>
<input name="TillDate" type="date" min="<?php echo date('Y-m-d'); ?>" value="" class="form-control">    
</div>       
     
<div class="alertb alert-info"><?php echo lang('phone_records_notice') ?></div>
     
     
				<div class="ip-modal-footer">
                 
                <button type="button" class="btn btn-dark text-white ip-close" data-dismiss='modal'><?php echo lang('close') ?></button>
                 
                  <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-primary text-white"><?php echo lang('save_changes_button') ?></button>
                 </div>
                 
</form>
</div>

