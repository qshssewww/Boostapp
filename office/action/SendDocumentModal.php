<?php
require_once __DIR__.'/../../app/init.php';
require_once __DIR__.'/../Classes/Client.php';

$TypeId = $_POST['TypeId'];
$DocId = $_POST['DocId'];
$Way = $_POST['Way'];
$CompanyNum = Auth::user()->CompanyNum;



if ($Way == '1') {$WayName = 'דואר אלקטרוני';}
elseif ($Way == '2') {$WayName = 'הודעת SMS';}
elseif ($Way == '3') {$WayName = '<span style="color: #42bd28;"><strong>WhatsApp</strong><i class="fab fa-whatsapp fa-fw"></i></span>';}

$DocGet = DB::table('docs')->where('CompanyNum' ,'=', $CompanyNum)->where('TypeNumber','=',$DocId)->where('TypeDoc','=',$TypeId)->first();
$DocsTables = DB::table('docstable')->where('CompanyNum' ,'=', $CompanyNum)->where('id','=',$DocGet->TypeDoc)->first();
$client = Client::find($DocGet->ClientId);

CreateLogMovement(
	'נכנס לשלוח העתק '.$DocsTables->TypeTitleSingle.' מספר '.$DocGet->TypeNumber.' באמצעות '.$WayName, //LogContent
	'0' //ClientId
);
?>
<span  style="font-weight: bold;">
<?php echo $DocsTables->TypeTitleSingle; ?> מספר <span style="color: #48AD42;"><?php echo $DocGet->TypeNumber; ?></span> (העתק).
</span>


<div class="my-15">
    <a class="btn btn-light js-copy-link-to-clipboard" data-code=<?php echo get_newboostapp_domain() . '/office/PDF/DocsClient.php?RandomUrl=' . $DocGet->RandomUrl . '&ClientId=' . $DocGet->ClientId; ?>>
        <i class="fal fa-paste payment-icon" ></i>
        <?= lang('copy_link') ?>
    </a>
</div>

  <fieldset class="form-group" style="margin-bottom: 0 !important;padding-bottom: 0 !important;">
    <div class="row mb-15">
      <legend class="col-form-label col-sm-3 pt-0">דרך המשלוח</legend>
      <div class="col-sm-9">
        <div class="form-check">
          <input class="form-check-input" type="radio" name="Via" id="Email" value="Email" style="margin-top: 3px;margin-right: 0;" <?php if($Way == '1') {echo "checked";} ?>>
          <label class="form-check-label" for="Email" style="margin-right: 20px;padding-right: 5px;">
            דואר אלקטרוני
          </label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="radio" name="Via" id="Sms" value="Sms" style="margin-top: 3px;margin-right: 0;" <?php if($Way == '2') {echo "checked";} ?>>
          <label class="form-check-label" for="Sms" style="margin-right: 20px;padding-right: 5px;">
            הודעת SMS
          </label>
        </div>
        <div class="form-check" style="display: none;">
          <input class="form-check-input" type="radio" name="Via" id="Whatsapp" value="Whatsapp" style="margin-top: 3px;margin-right: 0;" <?php if($Way == '3') {echo "checked";} ?>>
          <label class="form-check-label" for="Whatsapp" style="margin-right: 20px;padding-right: 5px;">
            <span style="color: #42bd28;"><strong>WhatsApp</strong><i class="fab fa-whatsapp fa-fw"></i></span>
          </label>
        </div>
      </div>
    </div>     
  <div class="form-group row EmailField mb-15" <?php if($Way == '2' || $Way == '3') {echo 'style="display: none;"';} ?>>
    <label for="EmailAddress" class="col-sm-3 col-form-label">דוא״ל</label>
    <div class="col-sm-9">
      <input type="email" class="form-control" id="EmailAddress" name="EmailAddress" readonly value="<?= $client->Email ?? '' ?>" placeholder="כתובת דואר אלקטרוני למשלוח?">
    </div>
  </div>
  <div class="form-group row PhoneField mb-15" <?php if($Way == '1') {echo 'style="display: none;"';} ?>>
    <label for="PhoneNumber" class="col-sm-3 col-form-label">טלפון</label>
    <div class="col-sm-9">
      <input type="tel" class="form-control" id="PhoneNumber" name="PhoneNumber" value="<?php echo @$DocGet->Mobile; ?>" placeholder="מספר טלפון למשלוח?">
    </div>
  </div>
       
<div class="alertb alert-info WhatsAppInfo" style="font-size:12px;<?php if($Way == '1' || $Way == '2') {echo 'display: none;';} ?>">על מנת לבצע שליחה באמצעות <span style="color: #42bd28;"><strong>WhatsApp</strong><i class="fab fa-whatsapp fa-fw"></i></span> יש להתחבר למערכת מסמארטפון או להפעיל <span style="color: #42bd28;"><strong>WhatsApp Web</strong><i class="fab fa-whatsapp fa-fw"></i></span>.</div>

<div class="alertb alert-info SmsInfo" style="font-size:12px;<?php if($Way == '1' || $Way == '3') {echo 'display: none;';} ?>">לידיעתך: שליחה ב-SMS כרוכה בעלות נוספת.</div>

<input type="hidden" name="DocId" id="DocId" class="form-control" dir="ltr" value="<?php echo @$DocId; ?>">
<input type="hidden" name="DocType" id="DocType" class="form-control" dir="ltr" value="<?php echo @$TypeId; ?>">      
  

 <div class="d-flex justify-content-between pt-20">
     <div class="">
         <button type="button" class="btn btn-light ip-close btn-block" data-dismiss="modal"><?= lang('cancel') ?></button>
     </div>
     <div class="">
         <button type="submit" name="submit" class="btn btn-primary btn-block"><?= lang('send') ?> <i class="fab fa-telegram-plane"></i></button>
     </div>
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

// click on copy link trigger this function
$('.js-copy-link-to-clipboard').click(function (e) {
    e.preventDefault();
    var copyText = $(this).attr('data-code');

    document.addEventListener('copy', function(e) {
        e.clipboardData.setData('text/plain', copyText);
        e.preventDefault();
    }, true);

    document.execCommand('copy');
    if (!$(this).hasClass('js-after-copy-link')) {
        $(this).toggleClass('js-after-copy-link');
        $(".js-after-copy-link i").removeClass('fa-paste').addClass('fa-check text-success');
        setTimeout(function(){
            $(".js-after-copy-link i").removeClass('fa-check text-success').addClass('fa-paste');
            $(".js-after-copy-link").toggleClass('js-after-copy-link');
        }, 2000);
    }
});
</script>