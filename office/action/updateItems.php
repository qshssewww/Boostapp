<?php require_once '../../app/initcron.php'; ?>
<?php if (Auth::userCan('30')): ?>
<?php

$ItemId = $_REQUEST['ItemId'];
$CompanyNum = Auth::user()->CompanyNum;
$Items = DB::table('items')->where('id', '=' , $ItemId)->where('CompanyNum', '=' , $CompanyNum)->first();

if ($Items->Department=='1'){
$ClassDisplay1 = 'block';
$ClassDisplay2 = 'none';
$ClassDisplay3 = 'none';
$ClassDisplay4 = 'none'; 
$ClassDisplay55 = 'block';    
}
else if ($Items->Department=='2'){
$ClassDisplay1 = 'block';
$ClassDisplay2 = 'block';
$ClassDisplay3 = 'none';
$ClassDisplay4 = 'none';
$ClassDisplay55 = 'block';    
}
else if ($Items->Department=='3'){
$ClassDisplay1 = 'none';
$ClassDisplay2 = 'none';
$ClassDisplay3 = 'block';
$ClassDisplay4 = 'none';
$ClassDisplay55 = 'block';    
}
else if ($Items->Department=='4'){
$ClassDisplay1 = 'none';
$ClassDisplay2 = 'none';
$ClassDisplay3 = 'none';
$ClassDisplay4 = 'block';
$ClassDisplay55 = 'none';    
}

?>

 <input type="hidden" name="Membership" value="<?php echo $Items->Department; ?>">

                <div id="Type6A">
    	       <div class="row">
               <div class="col-md-4">
                <div class="form-group">
                 <label>קטגוריה</label>
               <select name="membership_type"  class="form-control selectAddItem" style="width:100%;"  data-placeholder="בחר קטגוריה"  >
               <option value=""></option>  
               <option value="BA999" <?php if ($Items->MemberShip=='BA999') { echo 'selected'; } else {} ?>>ללא קטגוריה</option>         
              <?php
	          $Activities = DB::table('membership_type')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->orderBy('Type', 'ASC')->get();
              foreach ($Activities as $Activitie) {
	          ?>
              <option value="<?php echo $Activitie->id ?>" <?php if ($Items->MemberShip==$Activitie->id) { echo 'selected'; } else {} ?> ><?php echo $Activitie->Type; ?></option>
              <?php } ?>
              
              </select>  
                </div>
               		</div>
               		<div class="col-md-4">
                <div class="form-group">
                 <label>שם פריט</label>
                <input type="text" name="ItemName" class="form-control" placeholder="לדוגמא: כרטיסית 12 כניסות" value="<?php echo $Items->ItemName; ?>">
                </div>
               		</div>
                   
                <div class="col-md-2">
                <div class="form-group">
                <label>מחיר</label>
                <input type="text" name="ItemPrice" class="form-control" onkeypress='validate(event)' value="<?php echo $Items->ItemPrice; ?>">
                </div>     
                    
                </div>        
              
                <div class="col-md-2">          
                <div class="form-group">
                <label>כולל מע"מ?</label>
               <select name="Vat" class="form-control" style="width:100%;"  data-placeholder="בחר">    
               <option value="0" selected>כן</option>
               <option value="1">לא</option>   
               </select> 
                </div>
               </div>     
                    
               	</div>
               	 <hr>
    
               	 </div>
    
                <div id="Type1A" style="display: none;">
                </div>    
               
    
               <div id="Type2A" style="display: <?php echo $ClassDisplay2; ?>;">
                 
                <div class="row">
                    
                <div class="col-md-6">
                <div class="form-group" dir="rtl">
                <label>כמות שיעורים</label>
                <input type="text" name="BalanceClass" class="form-control" onkeypress='validate(event)' value="<?php echo @$Items->BalanceClass; ?>">
                </div>       
                    
               </div>        
              
                <div class="col-md-6">          
               <div class="form-group" dir="rtl">
               <label>האם לקזז מינוס מפעילות קודמת?</label>
               <select name="MinusCards" class="form-control" style="width:100%;"  data-placeholder="בחר">    
               <option value="0" <?php if ($Items->MinusCards=='0') { echo 'selected'; } else {} ?>>כן</option>
               <option value="1" <?php if ($Items->MinusCards=='1') { echo 'selected'; } else {} ?>>לא</option>   
               </select> 
               </div>  
               </div>          
   
                    
               </div><hr>    

                   
               </div>

               <div id="Type3A" style="display: <?php echo $ClassDisplay3; ?>;">
                   
                <div class="form-group" dir="rtl">
                <label>כמות שיעורים</label>
                <input type="number" max="<?php if ($Items->Department=='3') { echo '5'; } else { echo '999'; } ?>" name="BalanceClassTry" class="form-control" onkeypress='validate(event)' value="<?php echo @$Items->BalanceClass; ?>">
                </div>       
                   
               </div>
    
                <div id="Type55A" style="display: <?php echo $ClassDisplay55; ?>;">
                <div class="row">
               	<div class="col-md-3">   
                <div class="form-group" dir="rtl">
                <label>תוקף</label>
                <input type="text" name="Vaild" class="form-control" onkeypress='validate(event)' value="<?php echo @$Items->Vaild; ?>">
                </div> 
                </div>     
                <div class="col-md-3">
                <div class="form-group" dir="rtl">
                <label>חשב לפי</label>
               <select name="Vaild_Type"  class="form-control" style="width:100%;"  data-placeholder="בחר"  >    
               <option value="1" <?php if ($Items->Vaild_Type=='1') { echo 'selected'; } else {} ?>>ימים</option>
               <option value="2" <?php if ($Items->Vaild_Type=='2') { echo 'selected'; } else {} ?>>שבועות</option>
               <option value="3" <?php if ($Items->Vaild_Type=='3') { echo 'selected'; } else {} ?>>חודשים</option>       
               </select> 
                </div>     
                    
               </div>
                    
                <div class="col-md-3">
                <div class="form-group" dir="rtl">
                <label>התראה לסיום מנוי</label>
                <input type="text" name="NotificationDays" class="form-control" placeholder="הקלד בימים" value="3" onkeypress='validate(event)' value="<?php echo @$Items->NotificationDays; ?>">
                </div>     
                </div> 
                    
   
                    
               </div>   
               <hr>  
               </div>


               <div id="Type5A" style="display: <?php echo $ClassDisplay1; ?>;">

                <div class="row">
                    
                <div class="col-md-3">
                <div class="form-group" dir="rtl">
                <label>מנוי ניתן להקפאה?</label> 
               <select name="FreezMemberShip" id="FreezMemberShipA" class="form-control" style="width:100%;"  data-placeholder="בחר">    
               <option value="0" <?php if ($Items->FreezMemberShip=='0') { echo 'selected'; } else {} ?>>כן</option>
               <option value="1" <?php if ($Items->FreezMemberShip=='1') { echo 'selected'; } else {} ?>>לא</option>   
               </select>    
               </div>   
                </div>      
                    
                <div class="col-md-3">
                 <div class="form-group" dir="rtl" id="DivFreezMemberShip0A" style="display: <?php if ($Items->FreezMemberShip=='0') { echo 'block'; } else { echo 'none';} ?>;">
                <label>מינימום ימים להקפאה</label>
                <input type="text" name="FreezMemberShipDaysMin" class="form-control" onkeypress='validate(event)' value="<?php echo @$Items->FreezMemberShipDaysMin; ?>">
                </div>  
                </div>    
                   
                <div class="col-md-3">
                <div class="form-group" dir="rtl" id="DivFreezMemberShip0A" style="display: <?php if ($Items->FreezMemberShip=='0') { echo 'block'; } else { echo 'none';} ?>;">
                <label>מקסימום ימים להקפאה</label>
                <input type="text" name="FreezMemberShipDays" class="form-control" onkeypress='validate(event)' value="<?php echo @$Items->FreezMemberShipDays; ?>">
                </div>       
                    
               </div>        
              
                <div class="col-md-3">          
               <div class="form-group" dir="rtl" id="DivFreezMemberShip0A" style="display: <?php if ($Items->FreezMemberShip=='0') { echo 'block'; } else { echo 'none';} ?>;">
               <label>מספר פעמים להקפאה</label>
               <input type="text" name="FreezMemberShipCount" class="form-control" onkeypress='validate(event)' value="<?php echo @$Items->FreezMemberShipCount; ?>">
               </div>  
               </div>          
   
                    
               </div><hr>    
                   
                   
               <div class="row">
                
                <div class="col-md-6">   
                <div class="form-group" dir="rtl">
                <label>מקסימום שיעורים בחודש</label>
                <input type="text" name="LimitClassMonth" class="form-control" onkeypress='validate(event)'  value="<?php echo @$Items->LimitClassMonth; ?>">
                </div> 
                </div>        
                   
                   
                <div class="col-md-6">   
                <div class="form-group" dir="rtl">
                <label>מקסימום שיעורים בשבוע</label>
                <input type="text" name="LimitClass" class="form-control" onkeypress='validate(event)' value="<?php echo @$Items->LimitClass; ?>">
                </div> 
                </div>     
                   
                <div class="col-md-6">
                <div class="form-group" dir="rtl">
                <label>הגבלת שיעורים בשבוע בבוקר</label> 
                <input type="text" name="LimitClassMorning" class="form-control" onkeypress='validate(event)' value="<?php echo @$Items->LimitClassMorning; ?>">   
               </div>       
                </div>    
                   
                <div class="col-md-6">
                <div class="form-group" dir="rtl">
                <label>הגבלת שיעורים בשבוע בערב</label>
                <input type="text" name="LimitClassEvening" class="form-control" onkeypress='validate(event)' value="<?php echo @$Items->LimitClassEvening; ?>">
                </div>       
                    
               </div>        

               </div>
               
                <div class="alertb alert-warning">שים לב! הגבלת שיעורי בוקר ו/או ערב הן ע"פ השעות שהוגדרו בהגדרות האפליקציה. השאר ריק או הקלד 999 במידה ואין הגבלה.</div>     
                   
                <hr>    
  
                <div class="row">
                <div class="col-md-6">   
                <div class="form-group" dir="rtl">
                <label>אפשר הזמנת שיעור שמתחילים החל מהשעה</label>
                <input type="time" name="StartTime" class="form-control" value="<?php echo @$Items->StartTime; ?>">
                </div> 
                </div>     
                    
                <div class="col-md-6">   
                <div class="form-group" dir="rtl">
                <label>עד השעה</label>
                <input type="time" name="EndTime" class="form-control" value="<?php echo @$Items->EndTime; ?>">
                </div> 
                </div>     
                </div>  
                   
                <hr>   
 
                    
                <div class="form-group" dir="rtl">
                <label>האם לבטל הגבלת שיעורים ע"ב מקום פנוי באותו היום?</label>
               <select name="CancelLImit" id="CancelLImitA" class="form-control" style="width:100%;"  data-placeholder="בחר"  >    
               <option value="0" <?php if ($Items->CancelLImit=='0') { echo 'selected'; } else {} ?>>כן</option>
               <option value="1" <?php if ($Items->CancelLImit=='1') { echo 'selected'; } else {} ?>>לא</option>   
               </select> 
                </div>  
    
              <div id="CancelLImit0A" class="alertb alert-warning" style="display: <?php if ($Items->CancelLImit=='0') { echo 'block'; } else { echo 'none';} ?>;">שים לב! אפשרות זו מתעלמת מהגבלת שיעורים לשבוע ומאפשרת למנוי להזמין שיעור נוסף ע"ב מקום פנוי ביום השיעור בלבד.</div>          
                    
                <div class="form-group" dir="rtl">
                <label>האם לאפשר הזמנת שיעור נוסף באותו היום?</label>
               <select name="ClassSameDay" id="ClassSameDayA" class="form-control" style="width:100%;"  data-placeholder="בחר"  >    
               <option value="0" <?php if ($Items->ClassSameDay=='0') { echo 'selected'; } else {} ?>>כן</option>
               <option value="1" <?php if ($Items->ClassSameDay=='1') { echo 'selected'; } else {} ?>>לא</option>   
               </select> 
                </div>      
                    
               <div id="ClassSameDay0A" class="alertb alert-warning" style="display:  <?php if ($Items->ClassSameDay=='0') { echo 'block'; } else { echo 'none';} ?>;">שים לב! אפשרות זו מאפשרת להזמין שיעור כפול ו/או נוסף באותו היום בשעה אחרת.<br>
                חוק מגבלת שיעורים שבועיים נשאר תקף.</div>      
                   
               </div>
    
               <div id="Type4A" style="display: <?php echo $ClassDisplay4; ?>;">
                <div class="form-group" dir="rtl">
                <label>מחיר עלות</label>
                <input type="text" name="CostPrice" value="0" class="form-control" onkeypress='validate(event)' value="<?php echo @$Items->CostPrice; ?>">
                </div>          
               </div>

                <hr>

                <div class="form-group" dir="rtl">
                <label>סטטוס</label>
                <select class="form-control" name="Status">
                <option value="0" <?php if ($Items->Status=='0') { echo 'selected'; } else {} ?>>פעיל</option>  
                <option value="1" <?php if ($Items->Status=='1') { echo 'selected'; } else {} ?>>לא פעיל</option>      
                </select>
                </div>   

<script>

$( ".selectAddItem" ).select2( {theme:"bootstrap", placeholder: "Select a State", 'language':"he", dir: "rtl" } );   
    
    
$("#CancelLImitA").change(function() {
  
  var Id = this.value;
  if (Id=='0'){    
  CancelLImit0A.style.display = "block";     
  } 
  else {
  CancelLImit0A.style.display = "none";       
  }    
});	
    
$("#ClassSameDayA").change(function() {
  
  var Id = this.value;
  if (Id=='0'){    
  ClassSameDay0A.style.display = "block";     
  } 
  else {
  ClassSameDay0A.style.display = "none";       
  }    
});	
    
$("#FreezMemberShipA").change(function() {
  
  var Id = this.value;
  if (Id=='0'){    
  DivFreezMemberShip0A.style.display = "block"; 
  DivFreezMemberShip1A.style.display = "block"; 
  DivFreezMemberShip2A.style.display = "block";       
  } 
  else {
  DivFreezMemberShip0A.style.display = "none";
  DivFreezMemberShip1A.style.display = "none"; 
  DivFreezMemberShip2A.style.display = "none";       
  }    
});	     
</script>
<?php endif ?>
