<?php require_once '../../app/initcron.php'; 


$Id = $_REQUEST['Id'];
$CompanyNum = Auth::user()->CompanyNum;
$SettingsInfo = DB::table('settings')->where('CompanyNum' ,'=', Auth::user()->CompanyNum)->first();
$BrandsMain = $SettingsInfo->BrandsMain;
$ClassInfo = DB::table('classstudio_date')->where('id','=', $Id)->where('CompanyNum', $CompanyNum)->first();
$Floor = DB::table('sections')->where('id','=', $ClassInfo->Floor)->where('CompanyNum', $CompanyNum)->first();
$ClassDeviceName = DB::table('numbers')->where('CompanyNum', $CompanyNum)->where('id', '=', $ClassInfo->ClassDevice)->where('Status', '=', '0')->first();
?>


<div class="row">	   
<div class="col-md-12 col-sm-12 order-1">	  
<input type="hidden" id="CalPage" value="1">    
<input type="hidden" name="CalendarId" value="<?php echo $ClassInfo->id; ?>">     

 <div class="row">
 <div class="col-md-4">	 
  <div class="form-group">
  <label>מיקום שיעור</label>
    <select class="form-control js-example-basic-single text-right" name="FloorId" id="ChooseAgentForTaskA" dir="rtl" data-placeholder="בחר מיקום לשיעור" style="width: 100%" disabled>
  <?php 
  $SectionInfos = DB::table('sections')->where('CompanyNum','=',$CompanyNum)->where('Status','=','0')->orderBy('Floor', 'ASC')->get();
  foreach ($SectionInfos as $SectionInfo) {	
  ?>  
  <option value="<?php echo $SectionInfo->id; ?>" <?php if ($ClassInfo->Floor==$SectionInfo->id) { echo 'selected'; } else {} ?>><?php echo $SectionInfo->Title; ?></option>	  
  <?php 
		 }
  ?>  
  </select> 
  </div> 
 </div> 
     
 <div class="col-md-4">	 
  <div class="form-group">
  <label>סוג שיעור</label>
    <select class="form-control js-example-basic-single select2Desk text-right" name="ClassNameType" id="ClassNameTypeA" dir="rtl" data-placeholder="בחר סוג שיעור" disabled>
    <option value=""></option>    
  <?php 
  $SectionInfos = DB::table('class_type')->where('CompanyNum','=',$CompanyNum)->where('Status','=','0')->where('EventType','=','0')->orderBy('Type', 'ASC')->get();
  foreach ($SectionInfos as $SectionInfo) {	
  ?>  
  <option value="<?php echo $SectionInfo->id; ?>" <?php if ($ClassInfo->ClassNameType==$SectionInfo->id) { echo 'selected'; } else {} ?> ><?php echo $SectionInfo->Type; ?></option>	  
  <?php 
		 }
  ?>  
  </select> 
  </div> 
 </div>  
     
 <div class="col-md-4">	 
  <div class="form-group">
  <label>מוצג באפליקציה?</label>
    <select class="form-control js-example-basic-single text-right" name="ShowApp" id="ShowAppA" dir="rtl" disabled>
    <option value="1" <?php if ($ClassInfo->ShowApp=='1') { echo 'selected'; } else {} ?>>כן</option> 
    <option value="2" <?php if ($ClassInfo->ShowApp=='2') { echo 'selected'; } else {} ?>>לא</option>     
  </select> 
  </div> 
 </div>       
     
    
 </div>      
	
    
    
 <div class="row">
 <div class="col-md-4">	     
  <div class="form-group">
  <label>כותרת השיעור</label>
	<input type="text" class="form-control" name="ClassName" id="ClassNameA" value="<?php echo $ClassInfo->ClassName; ?>" disabled>  
	</div>  
  </div>
     
 <div class="col-md-4">	     
  <div class="form-group">
  <label>מדריך</label>
    <select class="form-control js-example-basic-single select2Desk text-right" name="GuideId" id="GuideIdA" dir="rtl" data-placeholder="בחר מדריך לשיעור" disabled>
    <option value=""></option>    
  <?php 
  if ($BrandsMain=='0'){       
  $SectionInfos = DB::table('users')->where('CompanyNum','=',$CompanyNum)->where('ActiveStatus','=','0')->where('Coach','=','1')->orderBy('display_name', 'ASC')->get();
  }
  else {
  $SectionInfos = DB::table('users')->where('BrandsMain','=',$BrandsMain)->where('ActiveStatus','=','0')->where('Coach','=','1')->orderBy('display_name', 'ASC')->get();    
  }  
  foreach ($SectionInfos as $SectionInfo) {	
  ?>  
  <option value="<?php echo $SectionInfo->id; ?>" <?php if ($ClassInfo->GuideId==$SectionInfo->id) { echo 'selected'; } else {} ?> ><?php echo $SectionInfo->display_name; ?></option>	  
  <?php 
		 }
  ?>  
  </select> 
	</div>  
  </div>
     
     
 <div class="col-md-4">	     
  <div class="form-group">
    <label>דרגת השיעור</label>
  <select class="form-control text-right" name="ClassLevel" id="ClassLevelA" dir="rtl" disabled>
    <option value="0" <?php if ($ClassInfo->ClassLevel=='0') { echo 'selected'; } else {} ?>>ללא דרגת שיעור</option>
    <option value="1" <?php if ($ClassInfo->ClassLevel=='1') { echo 'selected'; } else {} ?>>שיעור למתחילים</option>
	<option value="2" <?php if ($ClassInfo->ClassLevel=='2') { echo 'selected'; } else {} ?>>שיעור בקצב דינאמי</option>
	<option value="3" <?php if ($ClassInfo->ClassLevel=='3') { echo 'selected'; } else {} ?>>שיעור ברמה מתקדמת</option>
	</select>  
	</div>  
  </div>     
     
 </div>
     
 
 <div class="row">
     
 <div class="col-md-4">	     
    <div class="form-group">
  <label>מקסימום משתתפים</label>
	<input type="text" class="form-control" name="MaxClient" id="MaxClientA" value="<?php echo $ClassInfo->MaxClient; ?>" disabled>  
	</div> 
  </div>     
     
     
 <div class="col-md-4">	     
  <div class="form-group">
  <label>הגדר מינימום בשיעור?</label>
  <select class="form-control text-right" name="MinClass" id="MinClassA" dir="rtl" disabled>
    <option value="0" <?php if ($ClassInfo->MinClass=='0') { echo 'selected'; } else {} ?>>לא</option>
    <option value="1" <?php if ($ClassInfo->MinClass=='1') { echo 'selected'; } else {} ?>>כן</option>
	</select>  
	</div>  
  </div>
     
  <div id="DivMinClassNum1A" class="col-md-4" style="display: <?php if ($ClassInfo->MinClass=='1') { echo 'selected'; } else { echo 'none'; } ?>;">	     
  <div class="form-group">
  <label>מינימום משתתפים</label>
	<input type="number" class="form-control" name="MinClassNum" id="MinClassNum" value="<?php echo $ClassInfo->MinClassNum; ?>" disabled>  
	</div>  
  </div>
     

 </div>   
    
    <div id="DivMinClassNum2A" style="display: <?php if ($ClassInfo->MinClass=='1') { echo 'selected'; } else { echo 'none'; } ?>;">
   
   <div class="row">
   <div class="col-md-6">	    
    <div class="form-group">
  <label>זמן בדיקת מינימום משתתפים לפני השיעור</label>
	<input type="text" class="form-control" name="ClassTimeCheck" id="MinClassNumA" value="<?php echo @$ClassInfo->ClassTimeCheck; ?>" disabled>  
	</div>
    </div>   
     <div class="col-md-6">	   
    <div class="form-group">
    <label>אפשרות</label>
    <select class="form-control text-right" name="ClassTimeTypeCheck" id="ClassTimeTypeCheckA" dir="rtl" disabled>
    <option value="1" <?php if (@$ClassInfo->ClassTimeTypeCheck=='1') { echo 'selected'; } else {} ?> >דקות</option>
    <option value="2" <?php if (@$ClassInfo->ClassTimeTypeCheck=='2') { echo 'selected'; } else {} ?> >שעות</option>         
    </select> 
    </div> 
    </div>
    </div>   
    
    <div class="alertb alert-warning">שים לב! השיעור יבוטל אוטומטית במידה והשיעור לא הגיע למינימום משתתפים.<br>
התראה תשלח למשתתפים הרשומים על ביטול השיעור.</div>
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
  <label>תאריך תחילת השיעור</label>
  <input name="SetDate" id="SetDateA" type="date"  value="<?php echo $ClassInfo->StartDate; ?>" class="form-control" disabled>
	</div>  
  </div>
     
  <div class="col-md-3">	     
  <div class="form-group">
  <label>יום השיעור</label>
 <select name="Day" id="DayA" data-placeholder="בחר יום" class="form-control" style="width:100%;" disabled>
     <option value="0" <?php if ($ClassInfo->DayNum=='0') { echo 'selected'; } else {} ?>>ראשון</option>
     <option value="1" <?php if ($ClassInfo->DayNum=='1') { echo 'selected'; } else {} ?>>שני</option>
     <option value="2" <?php if ($ClassInfo->DayNum=='2') { echo 'selected'; } else {} ?>>שלישי</option>
     <option value="3" <?php if ($ClassInfo->DayNum=='3') { echo 'selected'; } else {} ?>>רביעי</option>
     <option value="4" <?php if ($ClassInfo->DayNum=='4') { echo 'selected'; } else {} ?>>חמישי</option>
     <option value="5" <?php if ($ClassInfo->DayNum=='5') { echo 'selected'; } else {} ?>>שישי</option>
     <option value="6" <?php if ($ClassInfo->DayNum=='6') { echo 'selected'; } else {} ?>>שבת</option>

          </select>

	</div>  
  </div>
     
 <div class="col-md-3">	     
    <div class="form-group">
  <label>שעת התחלה</label>
	  <input name="SetTime" id="SetTimeA" type="time" step="300" value="<?php echo $ClassInfo->StartTime; ?>" class="form-control" disabled>  
	</div> 
  </div>
      
 <div class="col-md-3">	     
    <div class="form-group">
  <label>שעת סיום</label>
	 <input name="SetToTime" id="SetToTimeA" type="time" step="300" min="<?php echo blockMinutesRound(date(('H:i'), strtotime("+5 minutes", strtotime($ClassInfo->StartTime)))); ?>" value="<?php echo $ClassInfo->EndTime; ?>" class="form-control" disabled>  
	</div> 
  </div>      
       
     
 </div>     
    
   <div class="row">
 <div class="col-md-4">	        
  <div class="form-group">
  <label>אופי השיעור</label>
    <select class="form-control text-right" name="ClassType" id="ClassTypeA" dir="rtl" disabled>
  <option value="1" <?php if ($ClassInfo->ClassType=='1') { echo 'selected'; } else {} ?>>שיעור קבוע</option>
  <option value="2" <?php if ($ClassInfo->ClassType=='2') { echo 'selected'; } else {} ?>>שיעור מוגבל בחזרות</option>
  <option value="3" <?php if ($ClassInfo->ClassType=='3') { echo 'selected'; } else {} ?>>שיעור חד פעמי</option>         
          
  </select>  
  </div>
 </div>
       
 <div id="DivClassTypeA" class="col-md-3" style="display: <?php if ($ClassInfo->ClassType=='2') { echo 'selected'; } else { echo 'none'; } ?>;">	        
  <div class="form-group">
  <label>מספר חזרות (בשבועות)</label>
  <input type="text" class="form-control" name="ClassCount" id="ClassCountA" value="<?php echo $ClassInfo->ClassCount; ?>" min="1" disabled> 
  </div>
 </div>       
 
       
 <div class="col-md-5">	        
  <div class="form-group">
  <label>הצג בחירת מכשירים</label>
    <select class="form-control js-example-basic-single select2Desk text-right" name="ClassDevice" id="ClassDeviceA" dir="rtl" data-placeholder="בחר טבלת מכשירים"  disabled>
    <option value=""></option>    
  <?php 
  $SectionInfos = DB::table('numbers')->where('CompanyNum','=',$CompanyNum)->where('Status','=','0')->orderBy('Name', 'ASC')->get();
  foreach ($SectionInfos as $SectionInfo) {	
  ?>  
  <option value="<?php echo $SectionInfo->id; ?>" <?php if ($ClassInfo->ClassDevice==$SectionInfo->id) { echo 'selected'; } else {} ?> ><?php echo $SectionInfo->Name; ?></option>	  
  <?php 
		 }
  ?>  
  </select>
  </div>
 </div>       
       
 </div>       

<hr>
    
  <div class="form-group">
  <label>בחר סוג מנוי להזמנת שיעור זה</label>
    <select class="form-control js-example-basic-single select2multipleDesk text-right" name="ClassMemberType[]" id="ClassMemberTypeA" dir="rtl"  multiple="multiple" disabled>
    <option value=""></option>
    <option value="BA999" <?php if (@$ClassInfo->ClassMemberType=='BA999') { echo 'selected'; } else {} ?>>כל סוגי המנויים</option>    
  <?php 
        
  $myArray = explode(',', $ClassInfo->ClassMemberType);
  $SectionInfos = DB::table('membership_type')->where('CompanyNum','=',$CompanyNum)->where('Status','=','0')->orderBy('Type', 'ASC')->get();
  foreach ($SectionInfos as $SectionInfo) {	
  $selected = (in_array($SectionInfo->id, $myArray)) ? ' selected="selected"' : '';      
  ?>  
  <option value="<?php echo $SectionInfo->id; ?>" <?php echo @$selected; ?> ><?php echo $SectionInfo->Type; ?></option>	  
  <?php 
		 }
  ?>  
  </select> 
	</div>      

<hr>
  <div class="row">

  <div class="col-md-6">	     
  <div class="form-group">
  <label>להציג כמות משתתפים?</label>
  <select class="form-control text-right" name="ShowClientNum" id="ShowClientNumA" dir="rtl" disabled>
  <option value="0" <?php if (@$ClassInfo->ShowClientNum=='0') { echo 'selected'; } else {} ?>>כן</option>
  <option value="1" <?php if (@$ClassInfo->ShowClientNum=='1') { echo 'selected'; } else {} ?>>לא</option>
  </select>    
  </div> 
  </div>
      
 <div class="col-md-6">	     
    <div class="form-group">
  <label>להציג שמות משתתפים?</label>
  <select class="form-control text-right" name="ShowClientName" id="ShowClientNameA" dir="rtl" disabled>
  <option value="0" <?php if (@$ClassInfo->ShowClientName=='0') { echo 'selected'; } else {} ?>>כן</option>
  <option value="1" <?php if (@$ClassInfo->ShowClientName=='1') { echo 'selected'; } else {} ?>>לא</option>
  </select>  
	</div> 
  </div>      
       
     
 </div>    

	
 <div class="row">
	    
  <div class="col-md-4">	     
  <div class="form-group">
  <label>לאפשר רשימת המתנה?</label>
  <select class="form-control text-right" name="ClassWating" id="ClassWatingA" dir="rtl" disabled>
  <option value="0" <?php if (@$ClassInfo->ClassWating=='0') { echo 'selected'; } else {} ?>>כן</option>
  <option value="1" <?php if (@$ClassInfo->ClassWating=='1') { echo 'selected'; } else {} ?>>לא</option>
  </select>  

  </div>  
  </div>  
	  
	  
  <div class="col-md-4" id="WatingListDivA" style="display: block;">	     
  <div class="form-group">
  <label>הגבלת רשימת המתנה?</label>
  <select class="form-control text-right" name="MaxWatingList" id="WatingListActA" dir="rtl" disabled>
  <option value="0" <?php if (@$ClassInfo->MaxWatingList=='0') { echo 'selected'; } else {} ?> >כן</option>
  <option value="1" <?php if (@$ClassInfo->MaxWatingList=='1') { echo 'selected'; } else {} ?> >לא</option>
  </select>  

  </div>  
  </div>    
	   
  <div id="WatingListNumDivA" class="col-md-4"  style="display: none;">	     
  <div class="form-group">
  <label>מקסימום ממתינים?</label>
  <input type="number" class="form-control" name="NumMaxWatingList" id="WatingListNumA" value="<?php echo $ClassInfo->NumMaxWatingList ?>" onkeypress='validate(event)' disabled> 
  </div> 
  </div>
	  

  </div>	
	
  <hr>	
	
   <div class="row">
 <div class="col-md-4">	     
  <div class="form-group">
  <label>שלח תזכורת ללקוח?</label>
  <select class="form-control text-right" name="SendReminder" id="SendReminderA" dir="rtl" disabled>
  <option value="0" <?php if (@$ClassInfo->SendReminder=='0') { echo 'selected'; } else {} ?>>כן</option>
  <option value="1" <?php if (@$ClassInfo->SendReminder=='1') { echo 'selected'; } else {} ?>>לא</option>
  </select>  
	</div>  
  </div>
     
  <div class="col-md-4">	     
  <div class="form-group">
  <label>הגדר זמן לשליחת התזכורת</label>
  <select class="form-control text-right" name="TypeReminder" id="TypeReminderA" dir="rtl" disabled>
  <option value="1" <?php if (@$ClassInfo->TypeReminder=='1') { echo 'selected'; } else {} ?>>ביום השיעור</option>
  <option value="2" <?php if (@$ClassInfo->TypeReminder=='2') { echo 'selected'; } else {} ?>>יום לפני השיעור</option>
  </select>  

  </div>  
  </div>
     
  <div class="col-md-4">	     
  <div class="form-group">
  <label>הגדר שעת שליחת התזכורת</label>
  <input type="time" class="form-control" name="TimeReminder" id="TimeReminderA" step="300" value="<?php echo $ClassInfo->TimeReminder; ?>" max="" min="" disabled>     
  </div> 
  </div>
        

 </div>  
    
    
   <hr>
   <div class="row">
 <div class="col-md-4">	     
  <div class="form-group">
  <label>בחר חוק ביטולים</label>
  <select class="form-control text-right" name="CancelLaw" id="CancelLawA" dir="rtl" disabled>
  <option value="1" <?php if (@$ClassInfo->CancelLaw=='1') { echo 'selected'; } else {} ?>>ביום השיעור עד שעה</option>
  <option value="2" <?php if (@$ClassInfo->CancelLaw=='2') { echo 'selected'; } else {} ?>>ביום לפני השיעור עד שעה</option>
  <option value="3" <?php if (@$ClassInfo->CancelLaw=='3') { echo 'selected'; } else {} ?>>ביום לבחירה עד שעה</option>
  <option value="4" <?php if (@$ClassInfo->CancelLaw=='4') { echo 'selected'; } else {} ?>>לא ניתן לביטול באפליקציה</option>       
  <option value="5" <?php if (@$ClassInfo->CancelLaw=='5') { echo 'selected'; } else {} ?>>ביטול חופשי</option>       
  </select>  
	</div>  
  </div>
     
   <div id="DivCancelLaw3A" class="col-md-4" style="display: <?php if ($ClassInfo->CancelLaw=='3') { echo 'selected'; } else { echo 'none'; } ?>;">	     
  <div class="form-group">
  <label>בחר יום לפני יום השיעור</label>
  <select name="CancelDay" id="CancelDayA" data-placeholder="בחר יום" class="form-control" style="width:100%;" disabled>
  <option value="">בחר יום</option>  


  </select>
  </div>  
  </div>   
       
     
  <div id="DivCancelLawA" class="col-md-4">	     
  <div class="form-group">
  <label>הגדר עד שעה לביטול</label>
  <input name="CancelTillTime" id="CancelTillTimeA" type="time" step="300" min="" value="<?php echo $ClassInfo->CancelTillTime; ?>" class="form-control" disabled>           
  </div> 
  </div>

 </div>  
    
    
       
<div id="DivCancelLaw6A" class="alertb alert-warning" style="display: <?php if ($ClassInfo->CancelLaw=='3') { echo 'selected'; } else { echo 'none'; } ?>;">שים לב! יש לבחור <u>יום</u> לפני יום השיעור שנקבע.<br>
לדוגמא: שיעורי יום ראשון בשעה 09:00 בבוקר ניתן לבטל עד שישי בשעה 12:00.</div> 
    
    
<div id="DivCancelLaw4A" class="alertb alert-warning" style="display: <?php if ($ClassInfo->CancelLaw=='4') { echo 'selected'; } else { echo 'none'; } ?>;">שים לב! באפשרות זו, ללקוח לא יופיע כפתור ביטול באפליקציה לאחר הזמנת שיעור זה.</div>   
       
    <div id="DivCancelLaw5A" class="alertb alert-warning" style="display: <?php if ($ClassInfo->CancelLaw=='5') { echo 'selected'; } else { echo 'none'; } ?>;">שים לב! הלקוח יוכל לבטל את השיעור בכל שלב וללא חיוב.</div>      
    

	</div>

	  
</div>	 	

     
<div class="ip-modal-footer">  
<button type="button" class="btn btn-dark text-white ip-close" data-dismiss='modal'>סגור</button> 
</div>

<script>

$( ".select2Desk" ).select2( {theme:"bootstrap", placeholder: "Select a State", 'language':"he", dir: "rtl", allowClear:"true" } );  
$( ".select2multipleDesk" ).select2( {theme:"bootstrap", placeholder: "בחר סוג מנוי", 'language':"he", dir: "rtl" } );      

 $(document).ready(function(){      
$('#DayA').val('<?php echo $ClassInfo->DayNum; ?>').trigger('change');     
});      
    

$("#DayA").change(function() {

var Id = this.value; 
if (Id=='0') {    
/// ראשון    
$('#CancelDayA').find('option').remove().end().append('<option value="">בחר יום</option><option value="6">שבת</option><option value="5">שישי</option><option value="4">חמישי</option><option value="3">רביעי</option><option value="2">שלישי</option><option value="1">שני</option><option value="0" disabled>ראשון</option>'); 
}
else if (Id=='1') {     
/// שני    
$('#CancelDayA').find('option').remove().end().append('<option value="">בחר יום</option><option value="0">ראשון</option><option value="6">שבת</option><option value="5">שישי</option><option value="4">חמישי</option><option value="3">רביעי</option><option value="2">שלישי</option><option value="1" disabled>שני</option>');
}
else if (Id=='2') {      
/// שלישי    
$('#CancelDayA').find('option').remove().end().append('<option value="">בחר יום</option><option value="1">שני</option><option value="0">ראשון</option><option value="6">שבת</option><option value="5">שישי</option><option value="4">חמישי</option><option value="3">רביעי</option><option value="2" disabled>שלישי</option>'); 
}
else if (Id=='3') {      
/// רביעי    
$('#CancelDayA').find('option').remove().end().append('<option value="">בחר יום</option><option value="2">שלישי</option><option value="1">שני</option><option value="0">ראשון</option><option value="6">שבת</option><option value="5">שישי</option><option value="4">חמישי</option><option value="3" disabled>רביעי</option>'); 
}
else if (Id=='4') {      
/// חמישי    
$('#CancelDayA').find('option').remove().end().append('<option value="">בחר יום</option><option value="3">רביעי</option><option value="2">שלישי</option><option value="1">שני</option><option value="0">ראשון</option><option value="6">שבת</option><option value="5">שישי</option><option value="4" disabled>חמישי</option>');
}
else if (Id=='5') {      
/// שישי    
$('#CancelDayA').find('option').remove().end().append('<option value="">בחר יום</option><option value="4">חמישי</option><option value="3">רביעי</option><option value="2">שלישי</option><option value="1">שני</option><option value="0">ראשון</option><option value="6">שבת</option><option value="5" disabled>שישי</option>'); 
}
else if (Id=='6') {      
/// שבת    
$('#CancelDayA').find('option').remove().end().append('<option value="">בחר יום</option><option value="5">שישי</option><option value="4">חמישי</option><option value="3">רביעי</option><option value="2">שלישי</option><option value="1">שני</option><option value="0">ראשון</option><option value="6" disabled>שבת</option>');
}
else {
$('#CancelDayA').find('option').remove().end().append('<option value="">בחר יום</option>');    
}    
    
   
    
//.val('whatever')    
    
});  
    
$('#CancelDayA').val('<?php echo $ClassInfo->CancelDay; ?>');    
    
    
$("#MinClassA").change(function() {
  
  var Id = this.value;
  if (Id=='0'){    
  DivMinClassNum1A.style.display = "none";
  DivMinClassNum2A.style.display = "none";      
  } 
  else {
  DivMinClassNum1A.style.display = "block";
  DivMinClassNum2A.style.display = "block";  
    
  var MaxClient = $('#MaxClientA').val();      
  $('#MinClassNumA').prop('max', MaxClient);
  $('#MinClassNumA').prop('min', '1');      
      
  }    
});	

    
$('#ClassMemberTypeA').on('select2:select', function (e) {    
var selected = $(this).val();

  if(selected != null)
  {
    if(selected.indexOf('BA999')>=0){
      $(this).val('BA999').select2( {theme:"bootstrap", placeholder: "בחר סוג מנוי", 'language':"he", dir: "rtl" } );
    }
  }
    
});	    
    
    
 
    
$("#MaxClientA").change(function() {

  var MaxClient = $('#MaxClientA').val();      
  $('#MinClassNumA').prop('max', MaxClient);
  $('#MinClassNumA').prop('min', '1');      
      
});	  
   
	
	
$("#ClassWatingA").change(function() {
  
  var Id = this.value;
  if (Id=='0'){    
  WatingListDivA.style.display = "block";      
  } 
  else {
  WatingListDivA.style.display = "none";
  WatingListNumDivA.style.display = "none";	  
  }  
	
  $('#WatingListActA').val('1').trigger('change');
  $('#WatingListNumA').prop('min', '');	
	
});	
	
$("#WatingListActA").change(function() {
  
  var Id = this.value;
  if (Id=='0'){    
  WatingListNumDivA.style.display = "block"; 
	  
  $('#WatingListNumA').prop('min', '1'); 	  
	  
	  
  } 
  else {
  WatingListNumDivA.style.display = "none";      
  }    
});		
	
	
 $("#ClassNameTypeA").change(function() {

  var ClassName = $('#ClassNameTypeA').select2('data');     
  $('#ClassNameA').val(ClassName[0].text);   
     
  if ($('#ClassNameTypeA option:selected').length > 0) {
   $('#ClassNameA').val(ClassName[0].text);    
  }
else {
    $('#ClassNameA').val('');  
}     
     
     
      
});	     
    
$(document).ready(function(){      
$('#CancelDayA').val('<?php echo $ClassInfo->CancelDay; ?>').trigger('change'); 
$('#ClassWatingA').val('<?php echo $ClassInfo->ClassWating; ?>').trigger('change');
$('#WatingListActA').val('<?php echo $ClassInfo->MaxWatingList; ?>').trigger('change');		
});        
    
$("#ClassTypeA").change(function() {
  
  var Id = this.value;
  if (Id=='1'){    
  DivClassTypeA.style.display = "none";
  $('#ClassCountA').val('999');      
  } 
  else if (Id=='2'){
  DivClassTypeA.style.display = "block";
  $('#ClassCountA').val('');   
  }    
  else {
   $('#ClassCountA').val('1');
   DivClassTypeA.style.display = "none";      
  }    
});	    
 
$("#TypeReminderA").change(function() {
    
var SetTime = $('#SetTimeA').val();
var SetToTime = $('#SetToTimeA').val();
    
    
 var TypeReminder = $('#TypeReminderA').val();
  if (TypeReminder=='1'){
 
  var TimeReminderVal = moment(SetTime,'hh:mm:ss').add(-2,'hours').format('hh:mm:ss');
  var TimeReminderMax = moment(SetTime,'hh:mm:ss').add(-10,'minutes').format('hh:mm:ss');
  var TimeReminderMin = moment(SetTime,'hh:mm:ss').add(-10,'hours').format('hh:mm:ss');      

  $('#TimeReminderA').prop('max', TimeReminderMax);
  $('#TimeReminderA').prop('min', '');      
  $('#TimeReminderA').val(TimeReminderVal);  
      
  }    
  else if (TypeReminder=='2') {
   
  $('#TimeReminderA').prop('max', '');
  $('#TimeReminderA').prop('min', '');      
  $('#TimeReminderA').val('17:00');      
        
      
  }  
    
    
  var CancelLaw = $('#CancelLawA').val();
  if (CancelLaw=='1' || CancelLaw=='4' || CancelLaw=='5'){
 
  var CancelLawVal = moment(SetTime,'hh:mm:ss').add(-2,'hours').format('hh:mm:ss');
  var CancelLawMax = moment(SetTime,'hh:mm:ss').add(-10,'minutes').format('hh:mm:ss');
  var CancelLawMin = moment(SetTime,'hh:mm:ss').add(-10,'hours').format('hh:mm:ss');      

  $('#CancelTillTimeA').prop('max', CancelLawMax);
  $('#CancelTillTimeA').prop('min', '');      
  $('#CancelTillTimeA').val(CancelLawVal);  
      
  }    
  else if (CancelLaw=='2' || CancelLaw=='3') {
   
  $('#CancelTillTimeA').prop('max', '');
  $('#CancelTillTimeA').prop('min', '');      
  $('#CancelTillTimeA').val('17:00');      
        
      
  }          
    
 });	   
$("#CancelLawA").change(function() {
  
  var Id = this.value;
  if (Id=='1'){    
  DivCancelLawA.style.display = "block";      
  DivCancelLaw3A.style.display = "none";
  DivCancelLaw4A.style.display = "none";
  DivCancelLaw5A.style.display = "none";
  DivCancelLaw6A.style.display = "none";      
  } 
  else if (Id=='2'){
  DivCancelLawA.style.display = "block";      
  DivCancelLaw3A.style.display = "none";
  DivCancelLaw4A.style.display = "none";
  DivCancelLaw5A.style.display = "none";
  DivCancelLaw6A.style.display = "none";      
  }  
  else if (Id=='3'){
  DivCancelLawA.style.display = "block";      
  DivCancelLaw3A.style.display = "block";
  DivCancelLaw4A.style.display = "none";
  DivCancelLaw5A.style.display = "none";
  DivCancelLaw6A.style.display = "block";      
  }  
  else if (Id=='4'){
  DivCancelLawA.style.display = "none";      
  DivCancelLaw3A.style.display = "none";
  DivCancelLaw4A.style.display = "block";
  DivCancelLaw5A.style.display = "none";
  DivCancelLaw6A.style.display = "none";      
  }  
  else if (Id=='5'){
  DivCancelLawA.style.display = "none";      
  DivCancelLaw3A.style.display = "none";
  DivCancelLaw4A.style.display = "none";
  DivCancelLaw5A.style.display = "block";   
  }      
  else {
  DivCancelLawA.style.display = "block";      
  DivCancelLaw3A.style.display = "none";
  DivCancelLaw4A.style.display = "none";
  DivCancelLaw5A.style.display = "none"; 
  DivCancelLaw6A.style.display = "none";      
  } 
    
    
var SetTime = $('#SetTimeA').val();
var SetToTime = $('#SetToTimeA').val();
    
    
 var TypeReminder = $('#TypeReminderA').val();
  if (TypeReminder=='1'){
 
  var TimeReminderVal = moment(SetTime,'hh:mm:ss').add(-2,'hours').format('hh:mm:ss');
  var TimeReminderMax = moment(SetTime,'hh:mm:ss').add(-10,'minutes').format('hh:mm:ss');
  var TimeReminderMin = moment(SetTime,'hh:mm:ss').add(-10,'hours').format('hh:mm:ss');      

  $('#TimeReminderA').prop('max', TimeReminderMax);
  $('#TimeReminderA').prop('min', '');      
  $('#TimeReminderA').val(TimeReminderVal);  
      
  }    
  else if (TypeReminder=='2') {
   
  $('#TimeReminderA').prop('max', '');
  $('#TimeReminderA').prop('min', '');      
  $('#TimeReminderA').val('17:00');      
        
      
  }  
    
    
  var CancelLaw = $('#CancelLawA').val();
  if (CancelLaw=='1' || CancelLaw=='4' || CancelLaw=='5'){
 
  var CancelLawVal = moment(SetTime,'hh:mm:ss').add(-2,'hours').format('hh:mm:ss');
  var CancelLawMax = moment(SetTime,'hh:mm:ss').add(-10,'minutes').format('hh:mm:ss');
  var CancelLawMin = moment(SetTime,'hh:mm:ss').add(-10,'hours').format('hh:mm:ss');      

  $('#CancelTillTimeA').prop('max', CancelLawMax);
  $('#CancelTillTimeA').prop('min', '');      
  $('#CancelTillTimeA').val(CancelLawVal);  
      
  }    
  else if (CancelLaw=='2' || CancelLaw=='3') {
   
  $('#CancelTillTimeA').prop('max', '');
  $('#CancelTillTimeA').prop('min', '');      
  $('#CancelTillTimeA').val('17:00');      
        
      
  }      
    
    
});	     
    

	
$('#SetTimeA').on('change', function() {


var SetTime = $('#SetTimeA').val();
var FixToTime = moment(SetTime,'hh:mm:ss').add(50,'minutes').format('hh:mm:ss') ;   
var FixToTimes = moment(SetTime,'hh:mm:ss').add(5,'minutes').format('hh:mm:ss') ;
var FixToTimeCancel = moment(SetTime,'hh:mm:ss').add(-2,'hours').format('hh:mm:ss');
    
$('#SetToTimeA').val(FixToTime); 
$('#SetToTimeA').prop('min', FixToTimes);
$('#CancelTillTimeA').prop('max', SetTime);   
$('#CancelTillTimeA').val(FixToTimeCancel);    
  
    
    
 var TypeReminder = $('#TypeReminderA').val();
  if (TypeReminder=='1'){
 
  var TimeReminderVal = moment(SetTime,'hh:mm:ss').add(-2,'hours').format('hh:mm:ss');
  var TimeReminderMax = moment(SetTime,'hh:mm:ss').add(-10,'minutes').format('hh:mm:ss');
  var TimeReminderMin = moment(SetTime,'hh:mm:ss').add(-10,'hours').format('hh:mm:ss');      

  $('#TimeReminderA').prop('max', TimeReminderMax);
  $('#TimeReminderA').prop('min', '');      
  $('#TimeReminderA').val(TimeReminderVal);  
      
  }    
  else if (TypeReminder=='2') {
   
  $('#TimeReminderA').prop('max', '');
  $('#TimeReminderA').prop('min', '');      
  $('#TimeReminderA').val('17:00');      
        
      
  }  
    
    
  var CancelLaw = $('#CancelLawA').val();
  if (CancelLaw=='1' || CancelLaw=='4' || CancelLaw=='5'){
 
  var CancelLawVal = moment(SetTime,'hh:mm:ss').add(-2,'hours').format('hh:mm:ss');
  var CancelLawMax = moment(SetTime,'hh:mm:ss').add(-10,'minutes').format('hh:mm:ss');
  var CancelLawMin = moment(SetTime,'hh:mm:ss').add(-10,'hours').format('hh:mm:ss');      

  $('#CancelTillTimeA').prop('max', CancelLawMax);
  $('#CancelTillTimeA').prop('min', '');      
  $('#CancelTillTimeA').val(CancelLawVal);  
      
  }    
  else if (CancelLaw=='2' || CancelLaw=='3') {
   
  $('#CancelTillTimeA').prop('max', '');
  $('#CancelTillTimeA').prop('min', '');      
  $('#CancelTillTimeA').val('17:00');      
        
      
  }       
    

	
				   
});	
	
$('#SetToTimeA').on('change', function() {


var SetTime = $('#SetTimeA').val();
var SetToTime = $('#SetToTimeA').val();
    
    
 var TypeReminder = $('#TypeReminderA').val();
  if (TypeReminder=='1'){
 
  var TimeReminderVal = moment(SetTime,'hh:mm:ss').add(-2,'hours').format('hh:mm:ss');
  var TimeReminderMax = moment(SetTime,'hh:mm:ss').add(-10,'minutes').format('hh:mm:ss');
  var TimeReminderMin = moment(SetTime,'hh:mm:ss').add(-10,'hours').format('hh:mm:ss');      

  $('#TimeReminderA').prop('max', TimeReminderMax);
  $('#TimeReminderA').prop('min', '');      
  $('#TimeReminderA').val(TimeReminderVal);  
      
  }    
  else if (TypeReminder=='2') {
   
  $('#TimeReminderA').prop('max', '');
  $('#TimeReminderA').prop('min', '');      
  $('#TimeReminderA').val('17:00');      
        
      
  }  
    
    
  var CancelLaw = $('#CancelLawA').val();
  if (CancelLaw=='1' || CancelLaw=='4' || CancelLaw=='5'){
 
  var CancelLawVal = moment(SetTime,'hh:mm:ss').add(-2,'hours').format('hh:mm:ss');
  var CancelLawMax = moment(SetTime,'hh:mm:ss').add(-10,'minutes').format('hh:mm:ss');
  var CancelLawMin = moment(SetTime,'hh:mm:ss').add(-10,'hours').format('hh:mm:ss');      

  $('#CancelTillTimeA').prop('max', CancelLawMax);
  $('#CancelTillTimeA').prop('min', '');      
  $('#CancelTillTimeA').val(CancelLawVal);  
      
  }    
  else if (CancelLaw=='2' || CancelLaw=='3') {
   
  $('#CancelTillTimeA').prop('max', '');
  $('#CancelTillTimeA').prop('min', '');      
  $('#CancelTillTimeA').val('17:00');      
        
      
  }      
    

				   
});	

$( "#ChooseAgentForTaskA" ).select2( {theme:"bootstrap", placeholder: "Select a State", 'language':"he", dir: "rtl" } );
    
</script>
