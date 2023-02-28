<?php
require_once '../../app/initcron.php';

$CompanyNum = Auth::user()->CompanyNum;
$SettingsInfo = DB::table('settings')->where('CompanyNum' ,'=', $CompanyNum)->first();
$ClassSettingsInfo = DB::table('classsettings')->where('CompanyNum' ,'=', $CompanyNum)->first();

if (Auth::user()->BrandsMain=='0'){  
$TrueCompanyNum = $CompanyNum;
}
else {
$TrueCompanyNum = Auth::user()->BrandsMain;    
}


?>
<link href="../../assets/css/smart_wizard.css?<?php echo date('YmdHis');?>" rel="stylesheet" type="text/css" />
<link href="../../assets/css/smart_wizard_theme_arrows.css" rel="stylesheet" type="text/css" />
     
<script src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.min.js"></script>
<script type="text/javascript" src="../../assets/js/jquery.smartWizard.js"></script>  

<?php 
$GroupNumber = rand(1,9999999);
$GroupNumber;
	
?>

<div id="smartwizard">
            <ul class="MenuWizard">
                <li><a href="#step-1">שלב 1<br /><small>הגדרת שיעור</small></a></li>
                <li><a href="#step-2">שלב 2<br /><small>הגדרת מתאמנים</small></a></li>
            </ul>

            <div>
                <div id="step-1" style="padding-top: 10px;">
                    <h4><strong>הגדרת שיעור</strong></h4>
                    
                    <div id="form-step-0" role="form" data-toggle="validator">
                       
  <div class="form-group">
  <label>בחר תבנית שיעור</label>
<select class="form-control js-example-basic-single select2Desk text-right" name="Template" id="Template" dir="rtl" data-placeholder="בחר תבנית לשיעור" style="width:100%;" required >
    <option value=""></option>    
  <?php       
  $SectionInfos = DB::table('classstudio_date_template')->where('CompanyNum','=',$TrueCompanyNum)->where('Status','=','0')->orderBy('ClassName', 'ASC')->get();
  foreach ($SectionInfos as $SectionInfo) {	
  ?>  
  <option value="<?php echo $SectionInfo->id; ?>" ><?php echo $SectionInfo->ClassName; ?> :: <?php echo $SectionInfo->GuideName; ?></option>	  
  <?php 
		 }
  ?>  
  </select> 
<div class="help-block with-errors"></div> 
	</div> 
                        
                    
                        
    <?php 
function blockMinutesRound($hour, $minutes = '5', $format = "H:i") {
   $seconds = strtotime($hour);
   $rounded = round($seconds / ($minutes * 60)) * ($minutes * 60);
   return date($format, $rounded);
}     
?>  
    
  <div class="row">
 <div class="col-md-3">	     
  <div class="form-group">
  <label>תחילת שיבוץ השיעור</label>
  <input name="SetDate" id="SetDate" type="date"  value="<?php echo date('Y-m-d') ?>" class="form-control" required>
  <div class="help-block with-errors"></div>       
	</div>  
  </div>
     
  <div class="col-md-3">	     
  <div class="form-group">
  <label>יום השיעור</label>
 <select name="Day" id="DayNew" data-placeholder="בחר יום" class="form-control" style="width:100%;" required>
<option value="">בחר יום</option>  

     <option value="0">ראשון</option>
     <option value="1">שני</option>
     <option value="2">שלישי</option>
     <option value="3">רביעי</option>
     <option value="4">חמישי</option>
     <option value="5">שישי</option>
     <option value="6">שבת</option>

          </select>
<div class="help-block with-errors"></div> 
	</div>  
  </div>
     
 <div class="col-md-3">	     
    <div class="form-group">
  <label>שעת התחלה</label>
	  <input name="SetTime" id="SetTimeNew" type="time" step="300" value="<?php echo blockMinutesRound(date('H:i')); ?>" class="form-control" required> 
    <div class="help-block with-errors"></div>     
	</div> 
  </div>
      
 <div class="col-md-3">	     
    <div class="form-group">
  <label>שעת סיום</label> 
	 <input name="SetToTime" id="SetToTimeNew" type="time" step="300" min="<?php echo blockMinutesRound(date(('H:i'), strtotime("+5 minutes"))); ?>" value="<?php echo blockMinutesRound(date(('H:i'), strtotime("+".$ClassSettingsInfo->EndClassTime." minutes"))); ?>" class="form-control" required>  
    <div class="help-block with-errors"></div>     
	</div> 
  </div>      
       
     
 </div>  
                        
    <div class="row">
 <div class="col-md-4">	        
  <div class="form-group">
  <label>אופי השיעור</label>
    <select class="form-control text-right" name="ClassType" id="ClassTypeNew" dir="rtl">
  <option value="2">שיעור מוגבל בחזרות</option>
  <option value="3" selected>שיעור חד פעמי</option>     
          
  </select>  
  </div>
 </div>
       
 <div id="DivClassTypeNew" class="col-md-3" style="display: none;">	        
  <div class="form-group">
  <label>מספר חזרות (בשבועות)</label>
  <input type="number" class="form-control" name="ClassCount" id="ClassCountNew" value="" max="30" min="1" onkeypress='validate(event)'>
  <div class="help-block with-errors"></div>      
  </div>
 </div>  
        
        
 <div id="DivClassTypeNew4" class="col-md-3" style="display: none;">	        
  <div class="form-group">
  <label>הגדר כל X שבועות</label>
  <input type="number" class="form-control" name="ClassRepeat" id="ClassRepeat" value="" min="1" onkeypress='validate(event)'>
  <div class="help-block with-errors"></div>      
  </div>
 </div>          
 
   
 </div>                        
                        
 <div class="row">
 <div class="col-md-4">	     
  <div class="form-group">
  <label>שלח תזכורת ללקוח?</label>
  <select class="form-control text-right" name="SendReminder" id="SendReminderNew" dir="rtl">
  <option value="0" selected>כן</option>
  <option value="1">לא</option>
  </select>  
	</div>  
  </div>
     
  <div class="col-md-4 SendReminderNew">	     
  <div class="form-group">
  <label>הגדר זמן לשליחת התזכורת</label>
  <select class="form-control text-right" name="TypeReminder" id="TypeReminderNew" dir="rtl">
  <option value="1" selected>ביום השיעור</option>
  <option value="2">יום לפני השיעור</option>
  </select>  

  </div>  
  </div>
     
  <div class="col-md-4 SendReminderNew">	     
  <div class="form-group">
  <label>הגדר שעת שליחת התזכורת</label>
  <input type="time" class="form-control" name="TimeReminder" id="TimeReminderNew" step="300" value="" max="" min="" required>
  <div class="help-block with-errors"></div>      
  </div> 
  </div>
        

 </div>                         
                        
    <hr>
   <div class="row">
 <div class="col-md-4">	     
  <div class="form-group">
  <label>בחר חוק ביטולים</label>
  <select class="form-control text-right" name="CancelLaw" id="CancelLawNew" dir="rtl">
  <option value="1" selected>ביום השיעור עד שעה</option>
  <option value="2">ביום לפני השיעור עד שעה</option>
  <option value="3">ביום לבחירה עד שעה</option>
  <option value="4">לא ניתן לביטול באפליקציה</option>       
  <option value="5">ביטול חופשי</option>       
  </select>  
	</div>  
  </div>
     
   <div id="DivCancelLawNew3" class="col-md-4" style="display: none;">	     
  <div class="form-group">
  <label>בחר יום לפני יום השיעור</label>
  <select name="CancelDay" id="CancelDayNew" data-placeholder="בחר יום" class="form-control" style="width:100%;">
  <option value="">בחר יום</option>  


  </select>
   <div class="help-block with-errors"></div>       
  </div>  
  </div>   
       
     
  <div id="DivCancelLawNew" class="col-md-4">	     
  <div class="form-group">
  <label>הגדר עד שעה לביטול</label>
  <input name="CancelTillTime" id="CancelTillTimeNew" type="time" step="300" min="" value="" class="form-control" required> 
   <div class="help-block with-errors"></div>       
  </div> 
  </div>

 </div>   
                        
        
    <div id="DivCancelLawNew6" class="alertb alert-warning" style="display: none;">שים לב! יש לבחור <u>יום</u> לפני יום השיעור שנקבע.<br>
לדוגמא: שיעורי יום ראשון בשעה 09:00 בבוקר ניתן לבטל עד שישי בשעה 12:00.</div> 
    
    
    <div id="DivCancelLawNew4" class="alertb alert-warning" style="display: none;">שים לב! באפשרות זו, ללקוח לא יופיע כפתור ביטול באפליקציה לאחר הזמנת שיעור זה.</div>   
       
    <div id="DivCancelLawNew5" class="alertb alert-warning" style="display: none;">שים לב! הלקוח יוכל לבטל את השיעור בכל שלב וללא חיוב.</div>                              
                        
                    </div>

                </div>
                <div id="step-2" style="padding-top: 10px;">
                    <h4><strong>הגדרת מתאמנים</strong></h4>
                    <div id="form-step-1" role="form" data-toggle="validator">
                      
<div class="form-group" dir="rtl">
<label>בחר לקוח/ות</label>
<select name="AddClientActivity" id="AddClientpopActivity" data-placeholder="בחר לקוח" class="form-control select2ClientDesk" style="width:100%;">
<option value=""></option>  
</select>    
</div>  
                         
<div id="ClientpopActivityInfo">

</div>                         
      
<div class="alertb alert-warning">שים לב! במידה ולא נבחר לקוח השיעור יוקם ללא שיבוץ לקוח.</div>                         
                        
</div>
                    
<input type="hidden" name="CalendarId" value="">
<input type="hidden" name="FixGroupNumber" value="<?php echo $GroupNumber; ?>">    
</div>
                

                
                

            
</div>


<style>

.select2-results__option[aria-selected=true] {
    display: none;
}
    
hr.hrclass {
    height: 1px;
    border: 0;
    color: #48AD42;
    background-color: #48AD42;
}    
      
</style>
    
    
<?php 
    
if ($ClassSettingsInfo->ReminderTimeType=='1'){
$ReminderTimeType = 'minutes';    
}
else {
$ReminderTimeType = 'hours';    
}  
                    
if ($ClassSettingsInfo->CancelTimeType=='1'){
$CancelTimeType = 'minutes';    
}
else {
$CancelTimeType = 'hours';    
}                      
    
?>       
    
    
  <script>
    
  $( ".select2ClientDesk" ).select2( {
		theme:"bootstrap", 
		placeholder: "חפש לקוח",
		language: "he",
		allowClear: true,
		width: '100%',
     ajax: {
            url: 'SearchClient.php',
            type: 'POST',
            dataType: 'json',
            cache: true
        },
		minimumInputLength: 3,
        dir: "rtl" } ); 
      
  $('#AddClientpopActivity').on('change',function(){

  var ClientId = $(this).children('option:selected').val();  
  var ClassId = $('#Template').children('option:selected').val();    
  if ($('#AddClientpopActivity option:selected').length > 0 ||  ClientId!=null) {
  var urls= 'action/ClientActivityPrivate.php?GroupNumber=<?php echo @$GroupNumber; ?>&ClientId='+ClientId+'&ClassId='+ClassId;
  $('#ClientpopActivityInfo').load(urls);     
}
else {
 $( "#ClientpopActivityInfo" ).empty();    
}                               
    
}); 
      
  
$( ".selectAddItem" ).select2( {theme:"bootstrap", placeholder: "בחר", 'language':"he", dir: "rtl" } );
$( ".select2Desk" ).select2( {theme:"bootstrap", placeholder: "בחר", 'language':"he", dir: "rtl", allowClear:"true" } );
$( ".select2LimitLevel" ).select2( {theme:"bootstrap", placeholder: "בחר", 'language':"he", dir: "rtl" } );      
      
   
$("#DayNew").change(function() {

var Id = this.value; 
if (Id=='0') {    
/// ראשון    
$('#CancelDayNew').find('option').remove().end().append('<option value="">בחר יום</option><option value="6">שבת</option><option value="5">שישי</option><option value="4">חמישי</option><option value="3">רביעי</option><option value="2">שלישי</option><option value="1">שני</option><option value="0" disabled>ראשון</option>'); 
}
else if (Id=='1') {     
/// שני    
$('#CancelDayNew').find('option').remove().end().append('<option value="">בחר יום</option><option value="0">ראשון</option><option value="6">שבת</option><option value="5">שישי</option><option value="4">חמישי</option><option value="3">רביעי</option><option value="2">שלישי</option><option value="1" disabled>שני</option>');
}
else if (Id=='2') {      
/// שלישי    
$('#CancelDayNew').find('option').remove().end().append('<option value="">בחר יום</option><option value="1">שני</option><option value="0">ראשון</option><option value="6">שבת</option><option value="5">שישי</option><option value="4">חמישי</option><option value="3">רביעי</option><option value="2" disabled>שלישי</option>'); 
}
else if (Id=='3') {      
/// רביעי    
$('#CancelDayNew').find('option').remove().end().append('<option value="">בחר יום</option><option value="2">שלישי</option><option value="1">שני</option><option value="0">ראשון</option><option value="6">שבת</option><option value="5">שישי</option><option value="4">חמישי</option><option value="3" disabled>רביעי</option>'); 
}
else if (Id=='4') {      
/// חמישי    
$('#CancelDayNew').find('option').remove().end().append('<option value="">בחר יום</option><option value="3">רביעי</option><option value="2">שלישי</option><option value="1">שני</option><option value="0">ראשון</option><option value="6">שבת</option><option value="5">שישי</option><option value="4" disabled>חמישי</option>');
}
else if (Id=='5') {      
/// שישי    
$('#CancelDayNew').find('option').remove().end().append('<option value="">בחר יום</option><option value="4">חמישי</option><option value="3">רביעי</option><option value="2">שלישי</option><option value="1">שני</option><option value="0">ראשון</option><option value="6">שבת</option><option value="5" disabled>שישי</option>'); 
}
else if (Id=='6') {      
/// שבת    
$('#CancelDayNew').find('option').remove().end().append('<option value="">בחר יום</option><option value="5">שישי</option><option value="4">חמישי</option><option value="3">רביעי</option><option value="2">שלישי</option><option value="1">שני</option><option value="0">ראשון</option><option value="6" disabled>שבת</option>');
}
else {
$('#CancelDayNew').find('option').remove().end().append('<option value="">בחר יום</option>');    
}    
    
   
    
//.val('whatever')    
    
});       
      
  
$('#LimitLevel').on('select2:select', function (e) {    
var selected = $(this).val();

  if(selected != null)
  {
    if(selected.indexOf('0')>=0){
      $(this).val('0').select2( {theme:"bootstrap", placeholder: "בחר סוג שיעור", 'language':"he", dir: "rtl" } );
    }
  }
    
});
    
  $('#LimitLevel').on('select2:open', function () {
    // get values of selected option
    var values = $(this).val();
    // get the pop up selection
    var pop_up_selection = $('.select2-results__options');
    if (values != null ) {
      // hide the selected values
       pop_up_selection.find("li[aria-selected=true]").hide();

    } else {
      // show all the selection values
      pop_up_selection.find("li[aria-selected=true]").show();
    }

  });       
      
      
      
  
 $("#ClassNameTypeNew").change(function() {

  var ClassName = $('#ClassNameTypeNew').select2('data');     
  $('#ClassName').val(ClassName[0].text);   
     
  if ($('#ClassNameTypeNew option:selected').length > 0) {
   $('#ClassNameNew').val(ClassName[0].text);    
  }
else {
    $('#ClassNameNew').val('');  
}     
     
});	  
      
      
$("#ClassTypeNew").change(function() {
  
  var Id = this.value;
  if (Id=='1'){    
  DivClassTypeNew.style.display = "none";
  DivClassTypeNew4.style.display = "none";     
  $('#ClassCountNew').val('999');
  $('#ClassRepeat').val('');
  $("#ClassCountNew").prop('required',false);
  $("#ClassRepeat").prop('required',false);      
  } 
  else if (Id=='2'){
  DivClassTypeNew.style.display = "block";
  DivClassTypeNew4.style.display = "none";      
  $('#ClassCountNew').val('');
  $('#ClassRepeat').val(''); 
  $("#ClassCountNew").prop('required',true);
  $("#ClassRepeat").prop('required',false);        
  } 
  else if (Id=='4'){
  DivClassTypeNew.style.display = "none";
  DivClassTypeNew4.style.display = "block";      
  $('#ClassRepeat').val('');
  $("#ClassCountNew").prop('required',false);
  $("#ClassRepeat").prop('required',true);        
  }     
  else {
   $('#ClassCountNew').val('1');
   DivClassTypeNew.style.display = "none";
   DivClassTypeNew4.style.display = "none";
  $('#ClassCountNew').val('999');
  $('#ClassRepeat').val(''); 
  $("#ClassCountNew").prop('required',false);
  $("#ClassRepeat").prop('required',false);        
  }    
});	       
    
 
      
$("#ClassLimitTypes").change(function() {
  
  var Id = this.value;
  if (Id=='1'){ 
  DivClassLimitTypes.style.display = "block";
  $('#theValueGroup').val('0');
  $('#GetGroupId').html('');      
  }
  else {
  DivClassLimitTypes.style.display = "none";
  $('#theValueGroup').val('0');
  $('#GetGroupId').html('');      
  }    
    
});    
    
      
$("#MinClassNew").change(function() {
  
  var Id = this.value;
  if (Id=='1'){    
  $('.DivMinClassNumNew').css("display", "block");
  $("#MinClassNumNew").prop('required',true);
  $("#ClassTimeCheckNew").prop('required',true);
  var MaxClient = $('#MaxClientNew').val();      
  $('#MinClassNumNew').prop('max', MaxClient);
  $('#MinClassNumNew').prop('min', '1');  
  $('.MaxClientMemberShip').prop('max', MaxClient);    

  } 
  else {
  $('.DivMinClassNumNew').css("display", "none");   
  $("#MinClassNumNew").prop('required',false);
  $("#ClassTimeCheckNew").prop('required',false);      
      
  }   
    
});	      
      
      
$("#ClassWatingNew").change(function() {
  
  var Id = this.value;
  if (Id=='0'){    
  $('.WatingListDiv').css("display", "block");     
  } 
  else {
  $('.WatingListDiv').css("display", "none");
  $('.WatingListNumDiv').css("display", "none");      
  $("#WatingListNumNew").prop('required',false);
  $('#WatingListActNew').val('1');      
      
  }   
    
});	
      
      
$("#WatingListActNew").change(function() {
  
  var Id = this.value;
  if (Id=='0'){    
  $('.WatingListNumDiv').css("display", "block");
  $("#WatingListNumNew").prop('required',true);
  var MaxClient = $('#MaxClientNew').val();      
  $('#WatingListNumNew').prop('max', MaxClient);
  $('#WatingListNumNew').prop('min', '1'); 
  $('.MaxClientMemberShip').prop('max', MaxClient);       
  } 
  else {
  $('.WatingListNumDiv').css("display", "none");      
  $("#WatingListNumNew").prop('required',false);      
      
  }   
    
});	      
      
 
$("#MaxClientNew").change(function() {
  
  var MaxClient = this.value;
  $('.MaxClientMemberShip').prop('max', MaxClient);       

    
});	      
      
      
 $("#SendReminderNew").change(function() {
  
  var Id = this.value;
  if (Id=='0'){    
  $('.SendReminderNew').css("display", "block"); 
  $("#TimeReminderNew").prop('required',true);         
  } 
  else {
  $('.SendReminderNew').css("display", "none");
  $("#TimeReminderNew").prop('required',false); 
  $('#TimeReminderNew').prop('max', '');
  $('#TimeReminderNew').prop('min', '');       
  }   
    
});	      
      
      
      
 $("#StopCancel").change(function() {
  
  var Id = this.value;
  if (Id=='0'){    
  $('.StopCancel').css("display", "block");
  $("#StopCancelTime").prop('required',true);      
  } 
  else {
  $('.StopCancel').css("display", "none");
  $("#StopCancelTime").prop('required',false);      
  }   
    
});	 
      
      
$('#SetTimeNew').on('change', function() {


/// שנה גלילה לפי שעה	


var SetTime = $('#SetTimeNew').val();
var FixToTime = moment(SetTime,'HH:mm:ss').add(<?php echo @$ClassSettingsInfo->EndClassTime; ?>,'minutes').format('HH:mm:ss') ;   
var FixToTimes = moment(SetTime,'HH:mm:ss').add(5,'minutes').format('HH:mm:ss') ;
var FixToTimeCancel = moment(SetTime,'HH:mm:ss').add(-2,'hours').format('HH:mm:ss');
    
$('#SetToTimeNew').val(FixToTime); 
$('#SetToTimeNew').prop('min', FixToTimes);
$('#CancelTillTimeNew').prop('max', SetTime);   
$('#CancelTillTimeNew').val(FixToTimeCancel);    
  
    
    
 var TypeReminder = $('#TypeReminderNew').val();
 var SendReminderNew = $('#SendReminderNew').val();
    
  if (TypeReminder=='1' && SendReminderNew=='0'){
 
  var TimeReminderVal = moment(SetTime,'HH:mm:ss').add(-<?php echo $ClassSettingsInfo->ReminderTime; ?>,'<?php echo $ReminderTimeType; ?>').format('HH:mm:ss');
  var TimeReminderMax = moment(SetTime,'HH:mm:ss').add(-10,'minutes').format('HH:mm:ss');
  var TimeReminderMin = moment(SetTime,'HH:mm:ss').add(-10,'hours').format('HH:mm:ss');      

  $('#TimeReminderNew').prop('max', TimeReminderMax);
  $('#TimeReminderNew').prop('min', '');      
  $('#TimeReminderNew').val(TimeReminderVal);  
      
  }    
  else if (TypeReminder=='2' || SendReminderNew=='1') {
   
  $('#TimeReminderNew').prop('max', '');
  $('#TimeReminderNew').prop('min', '');      
  $('#TimeReminderNew').val('<?php echo $ClassSettingsInfo->ReminderTimeDayBefore ?>');      
        
      
  }  
    
    
  var CancelLaw = $('#CancelLawNew').val();
  if (CancelLaw=='1'){
 
  var CancelLawVal = moment(SetTime,'HH:mm:ss').add(-<?php echo $ClassSettingsInfo->CancelTime; ?>,'<?php echo $CancelTimeType; ?>').format('HH:mm:ss');
  var CancelLawMax = moment(SetTime,'HH:mm:ss').add(-10,'minutes').format('HH:mm:ss');
  var CancelLawMin = moment(SetTime,'HH:mm:ss').add(-10,'hours').format('HH:mm:ss');      

  $('#CancelTillTimeNew').prop('max', CancelLawMax);
  $('#CancelTillTimeNew').prop('min', '');      
  $('#CancelTillTimeNew').val(CancelLawVal);  
      
  }    
  else if (CancelLaw=='2' || CancelLaw=='3') {
   
  $('#CancelTillTimeNew').prop('max', '');
  $('#CancelTillTimeNew').prop('min', '');      
  $('#CancelTillTimeNew').val('<?php echo $ClassSettingsInfo->CancelTimeDayBefore ?>');      
        
      
  } 
  else if (CancelLaw=='4' || CancelLaw=='5') {
   
  $('#CancelTillTimeNew').prop('max', '');
  $('#CancelTillTimeNew').prop('min', '');      
  $('#CancelTillTimeNew').val('<?php echo $ClassSettingsInfo->CancelTimeDayBefore ?>');      
        
      
  }     
			   
});	
	
$('#SetToTimeNew').on('change', function() {


var SetTime = $('#SetTimeNew').val();
var SetToTime = $('#SetToTimeNew').val();
    
    
 var TypeReminder = $('#TypeReminderNew').val();
 var SendReminderNew = $('#SendReminderNew').val();    
  if (TypeReminder=='1' && SendReminderNew=='0'){
 
  var TimeReminderVal = moment(SetTime,'HH:mm:ss').add(-<?php echo $ClassSettingsInfo->ReminderTime; ?>,'<?php echo $ReminderTimeType; ?>').format('HH:mm:ss');
  var TimeReminderMax = moment(SetTime,'HH:mm:ss').add(-10,'minutes').format('HH:mm:ss');
  var TimeReminderMin = moment(SetTime,'HH:mm:ss').add(-10,'hours').format('HH:mm:ss');      

  $('#TimeReminderNew').prop('max', TimeReminderMax);
  $('#TimeReminderNew').prop('min', '');      
  $('#TimeReminderNew').val(TimeReminderVal);  
      
  }    
  else if (TypeReminder=='2' || SendReminderNew=='1') {
   
  $('#TimeReminderNew').prop('max', '');
  $('#TimeReminderNew').prop('min', '');      
  $('#TimeReminderNew').val('<?php echo $ClassSettingsInfo->ReminderTimeDayBefore ?>');      
        
      
  }  
    
    
  var CancelLaw = $('#CancelLawNew').val();
  if (CancelLaw=='1'){
 
  var CancelLawVal = moment(SetTime,'HH:mm:ss').add(-<?php echo $ClassSettingsInfo->CancelTime; ?>,'<?php echo $CancelTimeType; ?>').format('HH:mm:ss');
  var CancelLawMax = moment(SetTime,'HH:mm:ss').add(-10,'minutes').format('HH:mm:ss');
  var CancelLawMin = moment(SetTime,'HH:mm:ss').add(-10,'hours').format('HH:mm:ss');      

  $('#CancelTillTimeNew').prop('max', CancelLawMax);
  $('#CancelTillTimeNew').prop('min', '');      
  $('#CancelTillTimeNew').val(CancelLawVal);  
      
  }    
  else if (CancelLaw=='2' || CancelLaw=='3') {
   
  $('#CancelTillTimeNew').prop('max', '');
  $('#CancelTillTimeNew').prop('min', '');      
  $('#CancelTillTimeNew').val('<?php echo $ClassSettingsInfo->CancelTimeDayBefore ?>');      
        
      
  }
  else if (CancelLaw=='4' || CancelLaw=='5') {
   
  $('#CancelTillTimeNew').prop('max', '');
  $('#CancelTillTimeNew').prop('min', '');      
  $('#CancelTillTimeNew').val('<?php echo $ClassSettingsInfo->CancelTimeDayBefore ?>');      
        
      
  }     
			   
});	 
      
      
$("#TypeReminderNew").change(function() {
    
var SetTime = $('#SetTimeNew').val();
var SetToTime = $('#SetToTimeNew').val();
    
    
 var TypeReminder = $('#TypeReminderNew').val();
var SendReminderNew = $('#SendReminderNew').val();     
  if (TypeReminder=='1' && SendReminderNew=='0'){
 
  var TimeReminderVal = moment(SetTime,'HH:mm:ss').add(-<?php echo $ClassSettingsInfo->ReminderTime; ?>,'<?php echo $ReminderTimeType; ?>').format('HH:mm:ss');
  var TimeReminderMax = moment(SetTime,'HH:mm:ss').add(-10,'minutes').format('HH:mm:ss');
  var TimeReminderMin = moment(SetTime,'HH:mm:ss').add(-10,'hours').format('HH:mm:ss');      

  $('#TimeReminderNew').prop('max', TimeReminderMax);
  $('#TimeReminderNew').prop('min', '');      
  $('#TimeReminderNew').val(TimeReminderVal);  
      
  }    
  else if (TypeReminder=='2' || SendReminderNew=='1') {
   
  $('#TimeReminderNew').prop('max', '');
  $('#TimeReminderNew').prop('min', '');      
  $('#TimeReminderNew').val('<?php echo $ClassSettingsInfo->ReminderTimeDayBefore ?>');      
        
      
  }  
    
    
  var CancelLaw = $('#CancelLawNew').val();
  if (CancelLaw=='1'){
 
  var CancelLawVal = moment(SetTime,'HH:mm:ss').add(-<?php echo $ClassSettingsInfo->CancelTime; ?>,'<?php echo $CancelTimeType; ?>').format('HH:mm:ss');
  var CancelLawMax = moment(SetTime,'HH:mm:ss').add(-10,'minutes').format('HH:mm:ss');
  var CancelLawMin = moment(SetTime,'HH:mm:ss').add(-10,'hours').format('HH:mm:ss');      

  $('#CancelTillTimeNew').prop('max', CancelLawMax);
  $('#CancelTillTimeNew').prop('min', '');      
  $('#CancelTillTimeNew').val(CancelLawVal);  
      
  }    
  else if (CancelLaw=='2' || CancelLaw=='3') {
   
  $('#CancelTillTimeNew').prop('max', '');
  $('#CancelTillTimeNew').prop('min', '');      
  $('#CancelTillTimeNew').val('<?php echo $ClassSettingsInfo->CancelTimeDayBefore ?>');      
        
      
  }
  else if (CancelLaw=='4' || CancelLaw=='5') {
   
  $('#CancelTillTimeNew').prop('max', '');
  $('#CancelTillTimeNew').prop('min', '');      
  $('#CancelTillTimeNew').val('<?php echo $ClassSettingsInfo->CancelTimeDayBefore ?>');      
        
      
  }     
    
 });      
      
 $("#CancelLawNew").change(function() {
  
  var Id = this.value;
  if (Id=='1'){    
  DivCancelLawNew.style.display = "block";      
  DivCancelLawNew3.style.display = "none";
  DivCancelLawNew4.style.display = "none";
  DivCancelLawNew5.style.display = "none";
  DivCancelLawNew6.style.display = "none";
  $("#CancelTillTimeNew").prop('required',true);
  $("#CancelDayNew").prop('required',false);      
  } 
  else if (Id=='2'){
  DivCancelLawNew.style.display = "block";      
  DivCancelLawNew3.style.display = "none";
  DivCancelLawNew4.style.display = "none";
  DivCancelLawNew5.style.display = "none";
  DivCancelLawNew6.style.display = "none";
  $("#CancelTillTimeNew").prop('required',true);
  $("#CancelDayNew").prop('required',false);      
  }  
  else if (Id=='3'){
  DivCancelLawNew.style.display = "block";      
  DivCancelLawNew3.style.display = "block";
  DivCancelLawNew4.style.display = "none";
  DivCancelLawNew5.style.display = "none";
  DivCancelLawNew6.style.display = "block";
  $("#CancelTillTimeNew").prop('required',true);
  $("#CancelDayNew").prop('required',true);      
  }  
  else if (Id=='4'){
  DivCancelLawNew.style.display = "none";      
  DivCancelLawNew3.style.display = "none";
  DivCancelLawNew4.style.display = "block";
  DivCancelLawNew5.style.display = "none";
  DivCancelLawNew6.style.display = "none"; 
  $("#CancelTillTimeNew").prop('required',false);
  $("#CancelDayNew").prop('required',false);
  $('#CancelTillTimeNew').prop('max', '');
  $('#CancelTillTimeNew').prop('min', '');       
  }  
  else if (Id=='5'){
  DivCancelLawNew.style.display = "none";      
  DivCancelLawNew3.style.display = "none";
  DivCancelLawNew4.style.display = "none";
  DivCancelLawNew5.style.display = "block";
  $("#CancelTillTimeNew").prop('required',false); 
  $("#CancelDayNew").prop('required',false);
  $('#CancelTillTimeNew').prop('max', '');
  $('#CancelTillTimeNew').prop('min', '');       
  }      
  else {
  DivCancelLawNew.style.display = "block";      
  DivCancelLawNew3.style.display = "none";
  DivCancelLawNew4.style.display = "none";
  DivCancelLawNew5.style.display = "none"; 
  DivCancelLawNew6.style.display = "none";
  $("#CancelTillTimeNew").prop('required',true);
  $("#CancelDayNew").prop('required',false);      
  } 
    
    
var SetTime = $('#SetTimeNew').val();
var SetToTime = $('#SetToTimeNew').val();
    
    
    
  var CancelLaw = $('#CancelLawNew').val();
  if (CancelLaw=='1'){
 
  var CancelLawVal = moment(SetTime,'HH:mm:ss').add(-<?php echo $ClassSettingsInfo->CancelTime; ?>,'<?php echo $CancelTimeType; ?>').format('HH:mm:ss');
  var CancelLawMax = moment(SetTime,'HH:mm:ss').add(-10,'minutes').format('HH:mm:ss');
  var CancelLawMin = moment(SetTime,'HH:mm:ss').add(-10,'hours').format('HH:mm:ss');      

  $('#CancelTillTimeNew').prop('max', CancelLawMax);
  $('#CancelTillTimeNew').prop('min', '');      
  $('#CancelTillTimeNew').val(CancelLawVal);  
      
  }    
  else if (CancelLaw=='2' || CancelLaw=='3') {
   
  $('#CancelTillTimeNew').prop('max', '');
  $('#CancelTillTimeNew').prop('min', '');      
  $('#CancelTillTimeNew').val('<?php echo $ClassSettingsInfo->CancelTimeDayBefore ?>');      
        
      
  }      
   else if (CancelLaw=='4' || CancelLaw=='5') {
   
  $('#CancelTillTimeNew').prop('max', '');
  $('#CancelTillTimeNew').prop('min', '');      
  $('#CancelTillTimeNew').val('<?php echo $ClassSettingsInfo->CancelTimeDayBefore ?>');      
        
      
  } 
});	
      
      
      
      
  $(document).ready(function(){

            // Toolbar extra buttons
            var btnFinish = $('<button></button>').text('סיום')
                                             .addClass('btn btn-success')
                                             .on('click', function(){
                                                    if( !$(this).hasClass('disabled')){
                                                        var elmForm = $("#AddsClassNewPop");
                                                        if(elmForm){
                                                            elmForm.validator('validate');
                                                            var elmErr = elmForm.find('.has-error');
                                                            if(elmErr && elmErr.length > 0){
                                                                alert('יש למלא את השדות חובה לפני שמירה');
                                                                return false;
                                                            }
                                                            else{
                                                                //alert('מוכן לשליחה');
                                                                elmForm.submit();
                                                               // SubmitPayment();
                                                                return false;
                                                            }
                                                        }
                                                    }
                                                });
            var btnCancel = $('<button type="button" class="BtnClassWizs"></button>').text('בטל')
                                             .addClass('btn btn-danger')
                                             .on('click', function(){
                                                    var modal = $('#AddsNewClass');
                                                    modal.modal('hide');
                                                    location.hash = "";
                                                    $('#ResultAddsNewClass').html("");
                                                });



            // Smart Wizard
            $('#smartwizard').smartWizard({
                    selected: 0,
                    theme: 'arrows',
                    transitionEffect:'fade',
                    toolbarSettings: {toolbarPosition: 'bottom',
                                      toolbarExtraButtons: [btnFinish],
                                      toolbarExtraCancelButtons: [btnCancel],
                                    },
                    anchorSettings: {
                                markDoneStep: true, // add done css
                                markAllPreviousStepsAsDone: true, // When a step selected by url hash, all previous steps are marked done
                                removeDoneStepOnNavigateBack: true, // While navigate back done step after active step will be cleared
                                enableAnchorOnDoneStep: true // Enable/Disable the done steps navigation
                            }
                 });

            $("#smartwizard").on("leaveStep", function(e, anchorObject, stepNumber, stepDirection) {
                var elmForm = $("#form-step-" + stepNumber);
                // stepDirection === 'forward' :- this condition allows to do the form validation
                // only on forward navigation, that makes easy navigation on backwards still do the validation when going next
                if(stepDirection === 'forward' && elmForm){
              //     $('#ClassMemberType1').parent().removeClass('has-error');   
                    elmForm.validator('validate');
                    var elmErr = elmForm.find('.has-error');
                    if(elmErr && elmErr.length > 0){
                        // Form validation failed
                        return false;
                    }
                }
                
                return true;
            });

            $("#smartwizard").on("showStep", function(e, anchorObject, stepNumber, stepDirection) {
                // Enable finish button only on last step
                if(stepNumber == 3){
                    $('.btn-finish').removeClass('disabled');
                  
                }else{
                    $('.btn-finish').addClass('disabled');
                    
                }
            });

        });
        
      
//// שכפול קבוצה      
function addElementgroup() 
	{
		var ni = document.getElementById('GetGroupId');
		var numi = document.getElementById('theValueGroup');
		var num = (document.getElementById('theValueGroup').value-0)+ 1;
		numi.value = num;
		var newdiv = document.createElement('div');
		var divIdName = 'Group'+num+'Div';
		newdiv.setAttribute('id',divIdName);
		newdiv.innerHTML = ' <div id="GroupId"><div class="row"><div class="col-6"><div class="form-group" dir="rtl"><label>בחר סוג מנוי</label> <a id="ClickSelectAll" class="ClickSelectAll" data-num="'+num+'" href="javascript:void(0)" style="float:left;display: none;">סמן הכל</a> <select class="form-control js-example-basic-single select2multipleDesk newid'+num+' text-right" name="ClassMemberType'+num+'[]" id="ClassMemberType'+num+'" multiple="multiple" dir="rtl" data-select2order="true" style="width: 100%;"></select><input type="hidden" id="CheckClassMemberType'+num+'" value=""><div class="help-block with-errors"></div></div></div><div class="col-3"><div class="form-group" dir="rtl"><label>מקסימום משתתפים</label><input type="number" min="1" name="MaxClientMemberShip'+num+'" id="MaxClientMemberShip'+num+'" class="form-control MaxClientMemberShip" value="1"></div></div><div class="col-md-3" style="padding-top: 35px;" ><a href="javascript:;" class="btn btn-danger btn-sm" onclick=\'removeElementgroup(\"'+divIdName+'\",\"'+num+'\")\' title="הסר">הסר מגבלה <i class="fas fa-trash-alt"></i></a></div></div><hr class="hrclass"></div>  ';
		ni.appendChild(newdiv);
        $(".select2multipleDesk").select2( {theme:"bootstrap", placeholder: "בחר סוג מנוי", 'language':"he", dir: "rtl",ajax: {
        url: 'action/SelectMembership.php?GroupNumber=<?php echo @$GroupNumber; ?>',
        dataType: 'json'
        } } ); 
        removeselectionclass(num); 
        
       var MaxClient = $('#MaxClientNew').val();      
       $('#MaxClientMemberShip'+num).prop('max', MaxClient);
       $('#MaxClientMemberShip'+num).val(MaxClient);  
  
	}      
      
 function removeElementgroup(divNum,num) 
	{
		var d = document.getElementById('GetGroupId');
		var olddiv = document.getElementById(divNum);
		var numis = document.getElementById('theValueGroup');
		var nums = (document.getElementById('theValueGroup').value);
		numis.value = nums;
		d.removeChild(olddiv);
        
    $.ajax({
    url: 'action/TempMemberDel.php?GroupNumber=<?php echo @$GroupNumber; ?>&GroupNum='+num,
    type: 'POST',
    success: function(data) {}
    });
    
        
	}         
 
 
function removeselectionclass(num) 
{
    
    
$("#ClassMemberType"+num).on("select2:select select2:unselect", function (e) {

    //this returns all the selected item
    var items= $(this).val(); 
    var Oldarray = $('#ChangeMe').val();
    var array = $('#ChangeMe').val(items);
    $('#CheckClassMemberType'+num).val(items);
    //// עדכון טבלה זמנית
    
    $.ajax({
    url: 'action/TempMember.php?GroupNumber=<?php echo @$GroupNumber; ?>&Clases='+items+'&GroupNum='+num,
    type: 'POST',
    success: function(data) {}
    });
    
    //Gets the last selected item
    var lastSelectedItem = e.params.data.id;

}); 
      
    
}     
    
 $(".select2multipleDesk").select2( {theme:"bootstrap", placeholder: "בחר סוג מנוי", 'language':"he", dir: "rtl",ajax: {
        url: 'action/SelectMembership.php?GroupNumber=<?php echo @$GroupNumber; ?>',
        dataType: 'json'
        } } ); 
      
      
$("#ClassMemberType1").on("select2:select select2:unselect", function (e) {

    //this returns all the selected item
    var items= $(this).val(); 
    var Oldarray = $('#ChangeMe').val();
    var array = $('#ChangeMe').val(items);
    $('#CheckClassMemberType1').val(items);
    
    
    //// עדכון טבלה זמנית
    
    $.ajax({
    url: 'action/TempMember.php?GroupNumber=<?php echo @$GroupNumber; ?>&Clases='+items+'&GroupNum=1',
    type: 'POST',
    success: function(data) {}
    });
    
    //Gets the last selected item
    var lastSelectedItem = e.params.data.id;

});    
      
      
  </script>
