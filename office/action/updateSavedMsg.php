<?php require_once '../../app/init.php'; ?>

<?php
$SettingsInfo = DB::table('settings')->where('CompanyNum' ,'=', Auth::user()->CompanyNum)->first();

$ItemId = $_POST['ItemId'];

$Items = DB::table('textsaved')->where('CompanyNum' ,'=', Auth::user()->CompanyNum)->where('id', '=' , $ItemId)->first();

?>

                <div class="form-group" >
                <label>כותרת ההודעה (לשימוש פנימי בלבד)</label>
                <input type="text" name="Title" id="Title" class="form-control" placeholder="כותרת ההודעה" value="<?php echo htmlentities($Items->Title); ?>">
                </div>     
                <div class="form-group" >
                <label>תוכן ההודעה ל-SMS <span  style="font-size: 12px;">(<span id="count2">0 תווים שיחולקו ל-0 הודעות</span>)</span></label>
                <textarea name="SmsContent" id="SmsContent2" class="form-control" rows="3"><?php echo $Items->SmsContent; ?></textarea>
                </div>     
                <div class="form-group" >
                <label>כותרת דואר אלקטרוני</label>
                <input type="text" name="EmailTitle" id="EmailTitle" class="form-control" placeholder="כותרת ההודעה" value="<?php echo htmlentities($Items->EmailTitle); ?>">
                </div>     
                <div class="form-group" >
                <label>תוכן ההודעה לדואר אלקטרוני</label>
                <textarea name="EmailContent" id="EmailContent" class="form-control summernote" rows="10"><?php echo htmlentities($Items->EmailContent); ?></textarea>
                </div>     
              <div class="form-group">
              <label>סטטוס </label>
              <select name="Status" id="Status" class="form-control" style="width:100%;" >
              <option value="0" <?php if ($Items->Status=='0') { echo 'selected'; } else {} ?>>מוצג</option>
              <option value="1" <?php if ($Items->Status=='1') { echo 'selected'; } else {} ?>>מוסתר</option>
              </select>  
              </div>


<script>
		$(document).ready(function() {

			$("#SmsContent2").keyup(function(){
  var LengthM = $(this).val().length;
  var LengthT = Math.ceil(($(this).val().length)/<?php echo $SettingsInfo->SMSLimit; ?>);
$("#count2").text(LengthM + ' תווים שיחולקו ל-'+ LengthT +' הודעות :: צפי מוערך');
});
  var LengthM = $("#SmsContent2").val().length;
  var LengthT = Math.ceil(($("#SmsContent2").val().length)/<?php echo $SettingsInfo->SMSLimit; ?>);
$("#count2").text(LengthM + ' תווים שיחולקו ל-'+ LengthT +' הודעות :: צפי מוערך');

 $('.summernote').summernote({
        placeholder: 'הקלד תוכן להודעה',
        tabsize: 2,
        height: 153,
	   toolbar: [
    // [groupName, [list of button]]
    ['style', ['bold', 'italic', 'underline', 'clear']],
    ['font', ['strikethrough']],
    ['para', ['ul', 'ol', 'link']]
  ]
      });
});	

</script>

