<?php require_once '../../app/initcron.php'; 
   $Id = $_GET['Id'];
   $ClientId = $_GET['ClientId'];
   $noReload = isset($_GET['noReload']) && $_GET['noReload'];
   $CompanyNum = Auth::user()->CompanyNum;
   $SettingsInfo = DB::table('settings')->where('CompanyNum' ,'=', $CompanyNum)->first();
   $ClientInfo = DB::table('client')->where('id' ,'=', $ClientId)->where('CompanyNum' ,'=', $CompanyNum)->first();
   $PipeInfo = DB::table('pipeline')->where('id' ,'=', $Id)->where('CompanyNum' ,'=', $CompanyNum)->first();
   @$AgentForThisLead = DB::table('users')->where('id', '=', @$PipeInfo->AgentId)->first();
   
   $MainPipeId = @$PipeInfo->MainPipeId;
   
   ///הצלחה
   $GetSuccessInfo = DB::table('leadstatus')->where('CompanyNum' ,'=', $CompanyNum)->where('PipeId' ,'=', $MainPipeId)->where('Act' ,'=', '1')->first();
   /// כשלון
   $GetFailsInfo = DB::table('leadstatus')->where('CompanyNum' ,'=', $CompanyNum)->where('PipeId' ,'=', $MainPipeId)->where('Act' ,'=', '2')->first();
   /// לא רלוונטי
   $GetNoneFailsInfo = DB::table('leadstatus')->where('CompanyNum' ,'=', $CompanyNum)->where('PipeId' ,'=', $MainPipeId)->where('Act' ,'=', '3')->first();
   
   $GetSuccess = $GetSuccessInfo->id;
   $GetFails = $GetFailsInfo->id;
   $GetNoneFails = $GetNoneFailsInfo->id;
   
   $CheckMemberShip = DB::table('client_activities')->where('CompanyNum','=', $CompanyNum)->where('ClientId','=', $ClientId)->where('Department','=', '3')->count(); 
   ?>
<style>
   input:required:invalid, input:focus:invalid {
   border: 1px solid #ff5d5d7a;
   }
   input:required:valid {
   border: 1px solid #00c700c7;
   }
</style>
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
      סניף : <?php echo @$ClientInfo->BrandName; ?> 
   </div>
</div>
<hr>
<div class="row">
   <div class="col-3">
      <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
         <a class="nav-link text-dark active" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">עדכון פרטי הליד</a>
         <a class="nav-link text-dark" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">ניהול פתקים</a>
         <a class="nav-link text-dark" id="v-pills-messages-tab" data-toggle="pill" href="#v-pills-messages" role="tab" aria-controls="v-pills-messages" aria-selected="false">שלח הודעה</a>
         <?php if ($CheckMemberShip<='3'){ ?>    
         <a class="nav-link text-dark" id="v-pills-settings-tab" data-toggle="pill" href="#v-pills-settings" role="tab" aria-controls="v-pills-settings" aria-selected="false">הגדרת מנוי התנסות</a>
         <?php } ?>
         <?php if ($CheckMemberShip>'0'){ ?>    
         <a class="nav-link text-dark" id="v-pills-addclass-tab" data-toggle="pill" href="#v-pills-addclass" role="tab" aria-controls="v-pills-addclass" aria-selected="false">שבץ לשיעור</a>
         <?php } ?>    
         <a class="nav-link text-dark" id="v-pills-form-tab" data-toggle="pill" href="#v-pills-form" role="tab" aria-controls="v-pills-form" aria-selected="false">שלח טופס ע.פרטים</a>
         <a class="nav-link text-dark" id="v-pills-health-tab" data-toggle="pill" href="#v-pills-health" role="tab" aria-controls="v-pills-health" aria-selected="false">שלח טופס ה.בריאות</a>    
      </div>
   </div>
   <div class="col-9">
      <div class="tab-content" id="v-pills-tabContent">
         <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
            <form action="UpdateLeadInfo"  class="ajax-form clearfix <?php if($noReload) echo "js-no-reload" ?> ">
               <input type="hidden" name="ClientId" value="<?php echo $ClientId; ?>"> 
               <input type="hidden" name="PipelineId"  value="<?php echo $Id; ?>">     
               <div class="alertb alert-info" style="font-size: 12px;">
                  עדכון פרטי הליד 
               </div>
               <div class="form-group" >
                  <label>בחירת PIPELINE</label>
                  <select class="form-control text-start" name="PipeLine" id="PipeLineSelect2" >
                     <?php
                        $b = '1';    
                        $ClassTypes = DB::table('pipeline_category')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->orderBy('id', 'ASC')->get();   
                        if (!empty($ClassTypes)){     
                        foreach ($ClassTypes as $ClassType) { ?>  
                     <option value="<?php echo $ClassType->id; ?>" <?php if ($ClassType->id==$PipeInfo->MainPipeId) { echo 'selected'; } else {} ?> ><?php echo $ClassType->Title ?></option>
                     <?php ++$b; } } else { ?>     
                     <?php } ?>    
                  </select>
               </div>
               <div class="row">
                  <div class="col-md-6 col-sm-12 order-md-1">
                     <div class="form-group" >
                        <label>שם פרטי</label>
                        <input type="text" name="FirstName" class="form-control" value="<?php echo  htmlentities($ClientInfo->FirstName) ?>">
                     </div>
                  </div>
                  <div class="col-md-6 col-sm-12 order-md-2">
                     <div class="form-group" >
                        <label>שם משפחה</label>
                        <input type="text" name="LastName" class="form-control" value="<?php echo  htmlentities($ClientInfo->LastName) ?>">
                     </div>
                  </div>
               </div>
               <div class="form-group" >
                  <label>טלפון סלולרי</label>
                  <input type="text" name="ContactMobile" class="form-control" value="<?php echo $ClientInfo->ContactMobile ?>" <?php echo $ClientInfo->parentClientId == 0 ? 'required' : '' ?> pattern="^(\+972|\+91|\+1|\+44)?0?5(0|1|2|3|4|5|8|9){1}[0-9]{7}$" title="מספר נייד לא תקין">
               </div>
               <div class="form-group" >
                  <label>דואר אלקטרוני</label>
                  <input type="text" name="Email" class="form-control" value="<?php echo $ClientInfo->Email ?>">
               </div>
               <div class="form-group" >
                  <label>מתעניין בשיעור</label>
                  <select class="form-control select2multipleDeskClass text-start" name="ClassType[]" id="ClassTypeClassAction"   multiple="multiple" >
                     <option value="BA999" <?php if ('BA999'==$PipeInfo->ClassInfo) { echo 'selected'; } else {} ?> >כל השיעורים</option>
                     <?php
                        $myArray = explode(',', $PipeInfo->ClassInfo);    
                        $ClassTypes = DB::table('class_type')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->orderBy('Type', 'ASC')->get();    
                        foreach ($ClassTypes as $ClassType) {
                        $selected = (in_array($ClassType->id, $myArray)) ? ' selected="selected"' : '';      
                        ?>  
                     <option value="<?php echo $ClassType->id; ?>" <?php echo @$selected; ?> ><?php echo $ClassType->Type ?></option>
                     <?php } ?> 
                  </select>
               </div>
               <div class="form-group" >
                  <label>סניף</label>
                  <select class="form-control text-start" name="Brands">
                     <?php
                        $b = '1';    
                        $ClassTypes = DB::table('brands')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->orderBy('id', 'ASC')->get();   
                        if (!empty($ClassTypes)){     
                        foreach ($ClassTypes as $ClassType) { ?>  
                     <option value="<?php echo $ClassType->id; ?>" <?php if ($ClassType->id==$PipeInfo->Brands) { echo 'selected'; } else {} ?> ><?php echo $ClassType->BrandName ?></option>
                     <?php ++$b; } } else { ?>
                     <option value="0" <?php if ('0'==$PipeInfo->Brands) { echo 'selected'; } else {} ?> >סניף ראשי</option>
                     <?php } ?>    
                  </select>
               </div>
               <div class="form-group" >
                  <label>מקור הגעה</label>
                  <select class="form-control" name="Source">
                     <option value="0" <?php if ('0'==$PipeInfo->SourceId) { echo 'selected'; } else {} ?> >ללא</option>
                     <?php
                        $PipeSources = DB::table('leadsource')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->orderBy('Title', 'ASC')->get();    
                        foreach ($PipeSources as $PipeSource) { ?>  
                     <option value="<?php echo $PipeSource->id; ?>" <?php if ($PipeSource->id==$PipeInfo->SourceId) { echo 'selected'; } else {} ?> ><?php echo $PipeSource->Title ?></option>
                     <?php } ?>   
                  </select>
               </div>
               <div class="form-group" >
                  <label>סטטוס</label>
                  <select class="form-control" name="Status" id="StatusSelect2" required>
                     <option value="">בחר</option>
                     <?php
                        $PipeTitles = DB::table('leadstatus')->where('CompanyNum','=', $CompanyNum)->where('Act','=', '0')->where('Status','=', '0')->orderBy('Sort', 'ASC')->get();    
                        foreach ($PipeTitles as $PipeTitle) { ?>  
                     <option value="<?php echo $PipeTitle->id; ?>" data-ajax="<?php echo $PipeTitle->PipeId; ?>" <?php if ($PipeTitle->id==$PipeInfo->PipeId) { echo 'selected'; } else {} ?> ><?php echo $PipeTitle->Title ?></option>
                     <?php } ?> 
                  </select>
               </div>
               <div class="ip-modal-footer d-flex justify-content-between px-0 border-0">
                  <div class="ip-actions">
                     <button type="submit" name="submit" class="btn btn-primary">שמור</button>
                  </div>
            </form>
            </div>
         </div>
         <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
            <ul class="timeline list">
               <?php
                  $NotesList = DB::table('clientcrm')->where('ClientId', '=', $ClientId)->where('CompanyNum' ,'=', $CompanyNum)->orderBy('Dates', 'DESC')->get();
                  $i = '1';
                  foreach ($NotesList as $ClassAct) {
                  $UsersName = DB::table('users')->where('CompanyNum' ,'=', $CompanyNum)->where('id', '=', $ClassAct->User)->first()
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
                              <a class="float-left" ><?php echo with(new DateTime($ClassAct->Dates))->format('d/m/Y H:i'); ?></a>
                           </div>
                        </div>
                     </div>
                  </div>
               </li>
               <?php } ?>
            </ul>
            <hr>
            <form action="AddCRM" class="ajax-form text-start <?php if($noReload) echo "js-no-reload" ?>"  autocomplete="off">
               <input type="hidden" name="ClientId" value="<?php echo $ClientId; ?>">
               <div class="form-group">
                  <label>כתוב פתק</label>    
                  <textarea name="Remarks" id="Remarks1" class="form-control" rows="3" ></textarea>
               </div>
               <div class="form-group">
                  <label>הערה חשובה? (כוכב)</label>   
                  <select name="StarIcon" class="form-control">
                     <option value="0">לא</option>
                     <option value="1">כן</option>
                  </select>
               </div>
               <div class="form-group">
                  <label>עד תאריך</label>   
                  <input name="TillDate" type="date" min="<?php echo date('Y-m-d'); ?>" value="" class="form-control">    
               </div>
               <div class="alertb alert-info">לתיעוד שיחה קבוע השאר שדה 'עד תאריך' ריק.</div>
               <div class="ip-modal-footer  d-flex justify-content-between px-0">
                  <div class="ip-actions">
                     <button type="submit" name="submit" class="btn btn-primary text-white">שמור</button>
                  </div>
            </form>
            </div>    
         </div>
         <div class="tab-pane fade" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab">
<!--            --><?php //if (Auth::userCan('87')): ?><!--         -->
            <form action="SendNotificationClient"  class="ajax-form clearfix <?php if($noReload) echo "js-no-reload" ?>">
               <input type="hidden" name="ClientId" id="ClientId" value="<?php echo $ClientId; ?>">     
               <div class="alertb alert-info" style="font-size: 12px;">
                  <strong>באפשרותך להשתמש בפרמטרים בתוך תוכן ההודעה:</strong><br>
                  <strong>[[שם מלא]]</strong> יוחלף בשם המלא של הלקוח.<br>
                  <strong>[[שם פרטי]]</strong> יוחלף בשם הפרטי של הלקוח.<br>
                  <strong>[[שם נציג מלא]]</strong> יוחלף בשם המלא של הנציג השולח.<br>
                  <strong>[[שם הנציג]]</strong> יוחלף בשם הפרטי של הנציג השולח.<br>
                  <strong>[[שם העסק]]</strong> יוחלף בשם העסק.
               </div>
               <div class="form-group" >
                  <label>אפשרות שליחה</label>
                  <select class="form-control" name="TypeSend">
<!--                     --><?php //if (Auth::userCan('88')): ?><!--      -->
                     <option value="1">הודעת SMS (כרוך בעלויות נוספת)</option>
<!--                     --><?php //endif ?><!--    -->
                     <option value="2">הודעת EMAIL (חינם)</option>
                  </select>
               </div>
               <div class="form-group" >
                  <label>נושא</label>
                  <input type="text" name="Subject" id="emailsubject" placeholder="נושא" class="form-control">
               </div>
               <div class="form-group" >
                  <label>תוכן ההודעה <span  style="font-size: 12px;">(<span id="count">0 תווים שיחולקו בהודעת SMS ל-0 הודעות</span>)</span></label>
                  <textarea name="Message" id="SmsContent" class="form-control" rows="3"></textarea>
               </div>
               <div class="ip-modal-footer  d-flex justify-content-between px-0">
                  <div class="ip-actions">
                     <button type="submit" name="submit" class="btn btn-primary">שלח</button>
                  </div>
            </form>
            </div>
<!--            --><?php //endif ?><!--       -->
         </div>
         <div class="tab-pane fade" id="v-pills-settings" role="tabpanel" aria-labelledby="v-pills-settings-tab">
            <form action="AddActivity" class="ajax-form text-start <?php if($noReload) echo "js-no-reload" ?>"  autocomplete="off">
               <input type="hidden" name="ClientId" value="<?php echo $ClientId; ?>">
               <input type="hidden" name="Vaild_LastCalss" value="1">     
               <div class="form-group">
                  <label>בחר מנוי <em><?php _e('main.required') ?></em></label>
                  <select name="Items1" id="Items1" class="form-control select22" style="width:100%;"  data-placeholder="בחר מנוי"  >
                     <option value=""></option>
                     <?php
                        if (@$ClientInfo->Status!='2') {     
                            $Activities = DB::table('items')
                                ->where('CompanyNum', '=', $CompanyNum)
                                ->where('Status', '=', '0')
                                ->where('Disabled', '=', 0)
                                ->orderBy('Department', 'ASC')->get();
                        } else {
                            $Activities = DB::table('items')
                                ->where('CompanyNum', '=', $CompanyNum)
                                ->whereIn('Department', array(3, 4))
                                ->where('Status', '=', '0')
                                ->where('Disabled', '=', 0)
                                ->orderBy('Department', 'ASC')->get();
                        }    
                        foreach ($Activities as $Activitie) {
                        $membership_type = DB::table('membership_type')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $Activitie->MemberShip)->first();  
                        if ($Activitie->MemberShip=='BA999' || !$membership_type){
                        $Type = 'ללא סוג מנוי';    
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
                  <label>תאריך תחילת מנוי <em><?php _e('main.required') ?></em></label>
                  <input type="date" class="form-control focus-me" name="ClassDate" value="<?php echo date('Y-m-d'); ?>">
               </div>
               <div class="ip-modal-footer  d-flex justify-content-between px-0">
                  <div class="ip-actions">
                     <button type="submit" name="submit" class="btn btn-primary text-white">שמור</button>
                  </div>
            </form>
            </div>
         </div>
         <div class="tab-pane fade" id="v-pills-addclass" role="tabpanel" aria-labelledby="v-pills-addclass-tab">
            <form action="AddClientAddClass" class="ajax-form text-start <?php if($noReload) echo "js-no-reload" ?>"  autocomplete="off">
               <input type="hidden" name="ClientId" value="<?php echo $ClientId; ?>">
               <input type="hidden" name="TrueClientId" value="0">     
               <div class="form-group">
                  <label>סוג שיבוץ</label>     
                  <select class="form-control" name="ClientAddClassType" id="ClientAddClassType">
                     <option value="1">שיבוץ חד פעמי</option>
                  </select>
               </div>
               <div class="form-group">
                  <label>בחר שיעור</label>     
                  <select name="ClientAddClassId" data-placeholder="בחר שיעור" class="form-control ClientAddClassId" style="width:100%;" >
                     <option value=""></option>
                  </select>
               </div>
               <div class="alertb alert-info" id="DivClientAddClassType1_1">טיפ! לחיפוש שיעור מהיר אנא הקלד תאריך ו/או יום ו/או שם מדריך ו/או שם שיעור<br>
                  פורמט תאריך: DD/MM/YYYY
               </div>
               <div id="ClientAddClassActivites">
               </div>
            </form>
         </div>
         <div class="tab-pane fade" id="v-pills-form" role="tabpanel" aria-labelledby="v-pills-form-tab">
<!--            --><?php //if (Auth::userCan('87')): ?><!--         -->
            <form action="SendPipeFormClient"  class="ajax-form clearfix <?php if($noReload) echo "js-no-reload" ?>">
               <input type="hidden" name="ClientId" id="ClientId" value="<?php echo $ClientId; ?>">     
               <div class="alertb alert-info" style="font-size: 12px;">
                   <?= lang('send_update_form_pipeline') ?>
               </div>
               <div class="form-group" >
                  <label><?= lang('sending_option') ?></label>
                  <select class="form-control" name="TypeSend" id="TypeSend">
<!--                     <option value="1">הודעת SMS (כרוך בעלויות נוספת)</option>-->
                     <option value="2" selected><?= lang('email_free') ?></option>
                  </select>
               </div>
               <div class="form-group DivPipeEmailoDiv" >
                  <label><?= lang('email') ?></label>
                  <input type="text" name="Email" id="Email" class="form-control" value="<?php echo $ClientInfo->Email ?>" required>
               </div>
               <div class="ip-modal-footer  d-flex justify-content-between px-0">
                  <div class="ip-actions">
                     <button type="submit" name="submit" class="btn btn-primary"><?= lang('send') ?></button>
                  </div>
            </form>
            </div>
<!--            --><?php //endif ?><!--     -->
         </div>
         <div class="tab-pane fade" id="v-pills-health" role="tabpanel" aria-labelledby="v-pills-health-tab">
            <?php if (Auth::userCan('87')): ?>         
            <form action="SendPipeFormMedicalClient"  class="ajax-form clearfix <?php if($noReload) echo "js-no-reload" ?>">
               <input type="hidden" name="ClientId" id="ClientId" value="<?php echo $ClientId; ?>">     
               <div class="alertb alert-info" style="font-size: 12px;">
                  <?= lang('send_health_declaration_form') ?>
               </div>
               <div class="form-group" >
                  <label><?= lang('sending_option') ?></label>
                  <select class="form-control" name="TypeSend" id="TypeSend2">
<!--                     <option value="1">הודעת SMS (כרוך בעלויות נוספת)</option>-->
                     <option value="2" selected><?= lang('email_free') ?></option>
                  </select>
               </div>
               <div class="form-group DivPipeEmailoDiv2" >
                  <label><?= lang('email') ?></label>
                  <input type="text" name="Email" id="Email2" class="form-control" value="<?php echo $ClientInfo->Email ?>" required>
               </div>
               <div class="ip-modal-footer  d-flex justify-content-between px-0">
                  <div class="ip-actions">
                     <button type="submit" name="submit" class="btn btn-primary"><?= lang('send') ?></button>
                  </div>
            </form>
            </div>
            <?php endif ?>    
         </div>
      </div>
   </div>
</div>
<br>        
<div class="ip-modal-footer  d-flex justify-content-between">
   <button type="button" class="btn btn-dark text-white ip-close" data-dismiss='modal'><?= lang('close') ?></button>
</div>
</div>
<script>
   $(document).ready(function() {    
   $('#PipeLineSelect2').val('<?php echo @$PipeInfo->MainPipeId; ?>').trigger('change'); 
   }); 
       
   $('#PipeLineSelect2').on('change', function() {   
   var Id = this.value;
   
    $('#StatusSelect2 option')
           .hide() // hide all
           .filter('[data-ajax="'+$(this).val()+'"]') // filter options with required value
           .show(); // and show them    
    
    if (Id!='<?php echo @$PipeInfo->MainPipeId; ?>'){   
    $('#StatusSelect2').val('');   
    }
       
   });     
       
       
   $( ".select22" ).select2( { theme:"bootstrap",placeholder: "בחר מנוי", minimumInputLength: 0,  allowClear: false, width: '100%' } );
       
   $( ".select2multipleDeskClass" ).select2( {theme:"bootstrap", placeholder: "בחר סוג שיעור" } );    
     
   $('#ClassTypeClassAction').on('select2:select', function (e) {    
   var selected = $(this).val();
   
     if(selected != null)
     {
       if(selected.indexOf('BA999')>=0){
         $(this).val('BA999').select2( {theme:"bootstrap", placeholder: "בחר סוג שיעור" } );
       }
     }
       
   });   
       
   $("#SmsContent").keyup(function(){
   var LengthM = $(this).val().length;
   var LengthT = Math.ceil(($(this).val().length)/<?php echo $SettingsInfo->SMSLimit; ?>);
   $("#count").text(LengthM + ' תווים שיחולקו ל-'+ LengthT +' הודעות :: צפי מוערך');
   });    
       
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
   
       
   $("#TypeSend2").change(function() {
   var Id = this.value;   
   if (Id=='2'){
   $('.DivPipeEmailoDiv2').css("display", "block");
   $("#Email2").prop('required',true);    
   }  
   else if (Id=='1'){
   $('.DivPipeEmailoDiv2').css("display", "none");
   $("#Email2").prop('required',false);       
   }     
   else {
   $('.DivPipeEmailoDiv2').css("display", "none");
   $("#Email2").prop('required',false);     
   }    
        
    });    
       
       
   $(document).ready(function(){  
       
   var ClassId = $('.ClientAddClassId').children('option:selected').val();
   
   $( ".ClientAddClassId" ).select2( {
           
      theme:"bootstrap", 
      placeholder: "חפש שיעור",
      language: "he",
      allowClear: false,
      width: '100%',
           ajax: {
                   url: 'SearchClass.php',
                   dataType: 'json',
                   type: 'GET',
                   cache: true,
                   data: function (params) {
                       return {
                           q: params.term, // search term
                           ClassId: $('#ClientAddClassType').children('option:selected').val(),
                       };
                   },
           },
      minimumInputLength: 3 } );    
       
   
   });
   
    $('.ClientAddClassId').on('change',function(){
   
     var ClassId = $(this).children('option:selected').val();  
     var ClientId = '<?php echo $ClientId; ?>';
     var ClientAddClassType = $('#ClientAddClassType').val();
     var ClassStatus = '0';       
       
     if ($('.ClientAddClassId option:selected').length > 0 ||  ClassId!=null) {
     var urls= 'action/ClientActivityMemberShip.php?ClientId='+ClientId+'&ClassId='+ClassId+'&ClientAddClassType='+ClientAddClassType+'&ClassStatus='+ClassStatus;;
     $('#ClientAddClassActivites').load(urls,function(){     
     return false;    
     });
   }
   else {
    $( "#ClientAddClassActivites" ).empty();    
   }                               
   
   });     
       
</script>