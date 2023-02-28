<?php require_once '../../app/init.php'; ?>

<?php
$ClientId = $_POST['ClientId'];
$Way = $_POST['Way'];
$CompanyNum = Auth::user()->CompanyNum;

$SettingsInfo = DB::table('settings')->where('CompanyNum' ,'=', $CompanyNum)->first();

if ($Way == '1') {$WayName = 'דואר אלקטרוני';}
elseif ($Way == '2') {$WayName = 'הודעת SMS';}
elseif ($Way == '3') {$WayName = '<span style="color: #42bd28;"><strong>WhatsApp</strong><i class="fab fa-whatsapp fa-fw"></i></span>';}

$ClientInfo = DB::table('client')->where('CompanyNum' ,'=', $CompanyNum)->where('id','=',$ClientId)->first();

CreateLogMovement(
	'נכנס לאיפוס סיסמת אפליקציה '.$ClientInfo->CompanyName.' באמצעות '.$WayName, //LogContent
	'0' //ClientId
);
?>
<span style="font-weight: bold;">
איפוס סיסמת אפליקציה ללקוח <?php echo $ClientInfo->CompanyName; ?>
</span>

 <br><br>
  <fieldset class="form-group" style="margin-bottom: 0 !important;padding-bottom: 0 !important;">
    <div class="row">
      <legend class="col-form-label col-sm-3 pt-0">דרך המשלוח</legend>
      <div class="col-sm-9 ">
        <div class="form-check row">
          <input class="form-check-input" type="radio" name="Via" id="Email" value="Email" <?php if($Way == '1') {echo "checked";} ?>>
          <label class="form-check-label" for="Email" style="margin-right: 20px;padding-right: 5px;">
            דואר אלקטרוני
          </label>
        </div>
        <div class="form-check row">
          <input class="form-check-input p-0 m-0" type="radio" name="Via" id="Sms" value="Sms" <?php if($Way == '2') {echo "checked";} ?>>
          <label class="form-check-label" for="Sms" style="margin-right: 20px;padding-right: 5px;">
            הודעת SMS
          </label>
        </div>
        <div class="form-check row" style="display: none;">
          <input class="form-check-input p-0 m-0" type="radio" name="Via" id="Whatsapp" value="Whatsapp" <?php if($Way == '3') {echo "checked";} ?>>
          <label class="form-check-label" for="Whatsapp" style="margin-right: 20px;padding-right: 5px;">
            <span style="color: #42bd28;"><strong>WhatsApp</strong><i class="fab fa-whatsapp fa-fw"></i></span>
          </label>
        </div>
      </div>
    </div>     
  <div class="form-group row EmailField" <?php if($Way == '2' || $Way == '3') {echo 'style="display: none;"';} ?>>
    <label for="EmailAddress" class="col-sm-3 col-form-label">דוא״ל</label>
    <div class="col-sm-9">
      <input type="email" class="form-control" id="EmailAddress" name="EmailAddress" readonly value="<?php echo @$ClientInfo->Email; ?>" placeholder="כתובת דואר אלקטרוני למשלוח?">
    </div>
  </div>
  <div class="form-group row PhoneField" <?php if($Way == '1') {echo 'style="display: none;"';} ?>>
    <label for="PhoneNumber" class="col-sm-3 col-form-label">טלפון</label>
    <div class="col-sm-9">
      <input type="tel" class="form-control" id="PhoneNumber" name="PhoneNumber" readonly value="<?php echo @$ClientInfo->ContactMobile; ?>" placeholder="מספר טלפון למשלוח?">
    </div>
  </div>
       
<div class="alertb alert-info WhatsAppInfo" style="font-size:12px;<?php if($Way == '1' || $Way == '2') {echo 'display: none;';} ?>">על מנת לבצע שליחה באמצעות <span style="color: #42bd28;"><strong>WhatsApp</strong><i class="fab fa-whatsapp fa-fw"></i></span> יש להתחבר למערכת מסמארטפון או להפעיל <span style="color: #42bd28;"><strong>WhatsApp Web</strong><i class="fab fa-whatsapp fa-fw"></i></span>.</div>

<div class="alertb alert-info SmsInfo" style="font-size:12px;<?php if($Way == '1' || $Way == '3') {echo 'display: none;';} ?>">לידיעתך: שליחה ב-SMS כרוכה בעלות נוספת. עלות הודעה SMS עד <?php echo $SettingsInfo->SMSLimit; ?> תווים ב- ₪<?php echo $SettingsInfo->SMSPrice; ?></div>

<input type="hidden" name="ClientId" id="ClientId" class="form-control" dir="ltr" value="<?php echo @$ClientInfo->id; ?>">
 

  <div class="form-group">
  <button type="submit" name="submit" class="btn btn-primary btn-block"><i class="fab fa-telegram-plane"></i> בצע שליחה!</button>
  </div>

    
  <div class="form-group">
<button type="button" class="btn btn-dark text-white ip-close btn-block" data-dismiss="modal">בטל</button>      
  </div>  
      
      
  </fieldset>







<script>
$(document).ready(function() {
    $('input[type=radio][name=Via]').change(function() {
        if (this.value == 'Email') {
            $('.EmailField').show();
			$('.PhoneField').hide();
			$('.WhatsAppInfo').hide();
			$('.SmsInfo').hide();
        }
        else if (this.value == 'Sms') {
            $('.EmailField').hide();
			$('.PhoneField').show();
			$('.WhatsAppInfo').hide();
			$('.SmsInfo').show();
        }
        else if (this.value == 'Whatsapp') {
            $('.EmailField').hide();
			$('.PhoneField').show();
			$('.WhatsAppInfo').show();
			$('.SmsInfo').hide();
        }
    });
})
</script>