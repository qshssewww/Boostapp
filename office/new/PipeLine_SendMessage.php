<?php
require_once '../../app/initcron.php';


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
      
<?php //if (Auth::userCan('87')): ?><!--         -->
<form action="SendNotificationClient"  class="ajax-form clearfix">
<input type="hidden" name="ClientId" id="ClientId" value="<?php echo $ClientId; ?>">     
     

     
     			<div class="alertb alert-info" style="font-size: 12px;">
  				<strong>באפשרותך להשתמש בפרמטרים בתוך תוכן ההודעה:</strong><br>
  				<strong>[[שם מלא]]</strong> יוחלף בשם המלא של הלקוח.<br>
  				<strong>[[שם פרטי]]</strong> יוחלף בשם הפרטי של הלקוח.<br>
  				<strong>[[שם נציג מלא]]</strong> יוחלף בשם המלא של הנציג השולח.<br>
  				<strong>[[שם הנציג]]</strong> יוחלף בשם הפרטי של הנציג השולח.<br>
                <strong>[[שם העסק]]</strong> יוחלף בשם העסק.
  				</div>
     
                <div class="form-group" dir="rtl">
                <label>אפשרות שליחה</label>
                <select class="form-control" name="TypeSend"> 
<!--                --><?php //if (Auth::userCan('88')): ?><!--      -->
                <option value="1">הודעת SMS (כרוך בעלויות נוספת)</option>
<!--                --><?php //endif ?><!--    -->
                <option value="2">הודעת EMAIL (חינם)</option>     
                </select>
                </div> 
    
                <div class="form-group" dir="rtl">
                <label>נושא</label>
                <input type="text" name="Subject" id="emailsubject" placeholder="נושא" class="form-control">
                </div> 
     
		        <div class="form-group" dir="rtl">
                <label>תוכן ההודעה <span dir="rtl" style="font-size: 12px;">(<span id="count">0 תווים שיחולקו בהודעת SMS ל-0 הודעות</span>)</span></label>
                <textarea name="Message" id="SmsContent" class="form-control" rows="3"></textarea>
                </div> 
     
     
				<div class="ip-modal-footer">
                 
                <button type="button" class="btn btn-dark text-white ip-close" data-dismiss='modal'>בטל/סגור</button> 
                 
                  <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-primary">שלח</button>
                 </div>
                 
                </form>
<!--      --><?php //endif ?><!-- -->
				</div>




</div>

<script>
		$("#SmsContent").keyup(function(){
  var LengthM = $(this).val().length;
  var LengthT = Math.ceil(($(this).val().length)/<?php echo $SettingsInfo->SMSLimit; ?>);
$("#count").text(LengthM + ' תווים שיחולקו ל-'+ LengthT +' הודעות :: צפי מוערך');
});
</script>

