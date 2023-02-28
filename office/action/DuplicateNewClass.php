<?php
require_once '../../app/initcron.php';

$CompanyNum = Auth::user()->CompanyNum;
$SettingsInfo = DB::table('settings')->where('CompanyNum' ,'=', $CompanyNum)->first();
$ClassSettingsInfo = DB::table('classsettings')->where('CompanyNum' ,'=', $CompanyNum)->first();

$Id = $_REQUEST['Id'];
$GetClassInfo = DB::table('classstudio_date')->where('id', '=', $Id)->where('CompanyNum', '=', $CompanyNum)->first();

//if (Auth::user()->BrandsMain=='0'){
//$TrueCompanyNum = $CompanyNum;
//}
//else {
//$TrueCompanyNum = Auth::user()->BrandsMain;
//}
$TrueCompanyNum = $CompanyNum;

?>
<link href="../../assets/css/smart_wizard.css?<?php echo date('YmdHis');?>" rel="stylesheet" type="text/css" />
<link href="../../assets/css/smart_wizard_theme_arrows.css" rel="stylesheet" type="text/css" />
     
<script src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.min.js"></script>
<script type="text/javascript" src="../../assets/js/jquery.smartWizard.js"></script>  

<?php 
$GroupNumber = rand(1,9999999);
$GroupNumber;
	
?>

<div id="smartwizard" >
            <ul class="MenuWizard">
                <li><a href="#step-1"><?php echo lang('class_details_stage_1') ?><br /><small><?php echo lang('lesson_settings') ?></small></a></li>
                <li><a href="#step-2"><?php echo lang('class_details_stage_2') ?><br /><small><?php echo lang('class_details_timing') ?></small></a></li>
                <li><a href="#step-3"><?php echo lang('class_details_stage_3') ?><br /><small><?php echo lang('class_details_register') ?></small></a></li>
                <li><a href="#step-4"><?php echo lang('class_details_stage_4') ?><br /><small><?php echo lang('class_details_cancel') ?></small></a></li>
                <li><a href="#step-5"><?php echo lang('class_details_stage_5') ?><br /><small><?php echo lang('class_details_display') ?></small></a></li>
            </ul>

            <div>
                <div id="step-1" style="padding-top: 10px;">
                    <h4><strong><?php echo lang('lesson_settings') ?></strong></h4>
                    
                    <div id="form-step-0" role="form" data-toggle="validator">
                   
                        
 <div class="row">
 <div class="col-md-4">	 
  <div class="form-group">
  <label><?php echo lang('lesson_location') ?></label>
    <select class="form-control js-example-basic-single text-start" id="ChooseFloorForTask" name="FloorId"  data-placeholder="<?php echo lang('choose_lesson_location') ?>" style="width: 100%" required >
  <?php 
  $SectionInfos = DB::table('sections')->where('CompanyNum','=',$CompanyNum)->where('Status','=','0')->orderBy('id', 'ASC')->get();
  foreach ($SectionInfos as $SectionInfo) {	
  ?>  
  <option value="<?php echo $SectionInfo->id; ?>" <?php if ($GetClassInfo->Floor==$SectionInfo->id) { echo 'selected'; } else {} ?> ><?php echo $SectionInfo->Title; ?></option>	  
  <?php 
		 }
  ?>  
  </select> 
  </div> 
 </div> 
     
 <div class="col-md-4">	 
  <div class="form-group">
  <label><?php echo lang('lesson_type') ?></label>
    <select class="form-control js-example-basic-single select2Desk text-start" name="ClassNameType" id="ClassNameTypeNew"  data-placeholder="<?php echo lang('choose_class_type') ?>" style="width:100%;" required>
    <option value=""></option>    
  <?php 
  $SectionInfos = DB::table('class_type')->where('CompanyNum','=',$CompanyNum)->where('Status','=','0')->orderBy('Type', 'ASC')->get();
  foreach ($SectionInfos as $SectionInfo) {	
  ?>  
  <option value="<?php echo $SectionInfo->id; ?>" <?php if ($GetClassInfo->ClassNameType==$SectionInfo->id) { echo 'selected'; } else {} ?> ><?php echo $SectionInfo->Type; ?></option>	  
  <?php 
		 }
  ?>  
  </select> 
  <div class="help-block with-errors"></div>       
  </div> 
 </div>  
     
 <div class="col-md-4">	 
 <div class="form-group">
 <label><?php echo lang('lesson_title') ?></label>
 <input type="text" class="form-control" name="ClassName" id="ClassNameNew" value="<?php echo $GetClassInfo->ClassName; ?>" required>
 <div class="help-block with-errors"></div>      
 </div>      
     
 </div>       
 </div> 
                        
 <div class="row">
<div class="col-md-4">	     
  <div class="form-group">
  <label><?php echo lang('instructor') ?></label>
    <select class="form-control js-example-basic-single select2Desk text-start" name="GuideId" id="GuideId"  data-placeholder="<?php echo lang('choose_lesson_instructor') ?>" required style="width:100%;" >
    <option value=""></option>    
  <?php 
  if (Auth::user()->BrandsMain=='0'){        
  $SectionInfos = DB::table('users')->where('CompanyNum','=',$TrueCompanyNum)->where('ActiveStatus','=','0')->where('Coach','=','1')->orderBy('display_name', 'ASC')->get();
  }
  else {
  $SectionInfos = DB::table('users')->where('CompanyNum','=',$TrueCompanyNum)->where('ActiveStatus','=','0')->where('Coach','=','1')->orderBy('display_name', 'ASC')->get();
  } 
  foreach ($SectionInfos as $SectionInfo) {	
  ?>  
  <option value="<?php echo $SectionInfo->id; ?>" <?php if ($GetClassInfo->GuideId==$SectionInfo->id) { echo 'selected'; } else {} ?> ><?php echo $SectionInfo->display_name; ?></option>	  
  <?php 
		 }
  ?>  
  </select> 
<div class="help-block with-errors"></div>       
	</div>  
  </div>  
     
<div class="col-md-4">	     
  <div class="form-group">
  <label><?php echo lang('instructor_help') ?></label>
    <select class="form-control js-example-basic-single select2Desk text-start" name="ExtraGuideId" id="ExtraGuideId"  data-placeholder="<?php echo lang('choose_lesson_instructor') ?>" style="width:100%;" >
    <option value=""></option>    
  <?php 
  if (Auth::user()->BrandsMain=='0'){        
  $SectionInfos = DB::table('users')->where('CompanyNum','=',$TrueCompanyNum)->where('ActiveStatus','=','0')->where('Coach','=','1')->orderBy('display_name', 'ASC')->get();
  }
  else {
  $SectionInfos = DB::table('users')->where('CompanyNum','=',$TrueCompanyNum)->where('ActiveStatus','=','0')->where('Coach','=','1')->orderBy('display_name', 'ASC')->get();
  } 
  foreach ($SectionInfos as $SectionInfo) {	
  ?>  
  <option value="<?php echo $SectionInfo->id; ?>" <?php if ($GetClassInfo->ExtraGuideId==$SectionInfo->id) { echo 'selected'; } else {} ?> ><?php echo $SectionInfo->display_name; ?></option>	  
  <?php 
		 }
  ?>  
  </select> 
	</div>  
  </div>       
     
  <div class="col-md-4">	 
  <div class="form-group">
  <label><?php echo lang('is_displayed_in_app') ?></label>
    <select class="form-control js-example-basic-single text-start" name="ShowApp" id="ShowApp" >
    <option value="1" <?php if ($GetClassInfo->ShowApp=='1') { echo 'selected'; } else {} ?> ><?php echo lang('yes') ?></option> 
    <option value="2" <?php if ($GetClassInfo->ShowApp=='2') { echo 'selected'; } else {} ?> ><?php echo lang('no') ?></option>     
  </select> 
  </div> 
 </div>  
     
</div>                       
      
 </div>

                </div>
                <div id="step-2" style="padding-top: 10px;">
                    <h4><strong><?php echo lang('class_details_timing') ?></strong></h4>
                    <div id="form-step-1" role="form" data-toggle="validator">
                        
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
  <label><?php echo lang('class_details_embed_start') ?></label>
  <input name="SetDate" id="SetDate" type="date"  value="" class="form-control" required>
  <div class="help-block with-errors"></div>       
	</div>  
  </div>
     
  <div class="col-md-3">	     
  <div class="form-group">
  <label><?php echo lang('lesson_day') ?></label>
 <select name="Day" id="DayNew" data-placeholder="<?php echo lang('choose_day') ?>" class="form-control" style="width:100%;" required>
     <option value="" selected><?php echo lang('choose_day') ?></option>
     <option value="0"><?php echo lang('sunday') ?></option>
     <option value="1"><?php echo lang('monday') ?></option>
     <option value="2"><?php echo lang('tuesday') ?></option>
     <option value="3"><?php echo lang('wednesday') ?></option>
     <option value="4"><?php echo lang('thursday') ?></option>
     <option value="5"><?php echo lang('friday') ?></option>
     <option value="6"><?php echo lang('saturday') ?></option>

</select>
      
<div class="help-block with-errors"></div> 
	</div>  
  </div>
     
 <div class="col-md-3">	     
    <div class="form-group">
  <label><?php echo lang('begin_time') ?></label>
	  <input name="SetTime" id="SetTimeNew" type="time" step="300" value="<?php echo blockMinutesRound($GetClassInfo->StartTime); ?>" class="form-control" required> 
    <div class="help-block with-errors"></div>     
	</div> 
  </div>
      
 <div class="col-md-3">	     
    <div class="form-group">
  <label><?php echo lang('finish_time') ?></label> 
	 <input name="SetToTime" id="SetToTimeNew" type="time" step="300" min="<?php echo blockMinutesRound(date(('H:i'), strtotime("+5 minutes", strtotime($GetClassInfo->StartTime)))); ?>" value="<?php echo $GetClassInfo->EndTime; ?>" class="form-control" required>  
    <div class="help-block with-errors"></div>     
	</div> 
  </div>      
       
     
 </div>  
                        
    <div class="row">
 <div class="col-md-4">	        
  <div class="form-group">
  <label><?php echo lang('lesson_prop') ?></label>
    <select class="form-control text-start" name="ClassType" id="ClassTypeNew"  required>
  <option value="1" <?php if ($GetClassInfo->ClassType=='1') { echo 'selected'; } else {} ?> ><?php echo lang('permanent_lesson') ?></option>
  <option value="2" <?php if ($GetClassInfo->ClassType=='2') { echo 'selected'; } else {} ?>><?php echo lang('limited_lesson') ?></option>
  <option value="3" <?php if ($GetClassInfo->ClassType=='3') { echo 'selected'; } else {} ?>><?php echo lang('single_time_lesson') ?></option>
  <option value="4" disabled><?php echo lang('new_class_in_week') ?></option>     
          
  </select>  
  </div>
 </div>
       
 <div id="DivClassTypeNew" class="col-md-3" style="display: <?php if ($GetClassInfo->ClassType=='2') { echo 'selected'; } else { echo 'none'; } ?>;">	        
  <div class="form-group">
  <label><?php echo lang('rep_times_weeks') ?></label>
  <input type="number" class="form-control" name="ClassCount" id="ClassCountNew" value="<?php echo $GetClassInfo->ClassCount; ?>" min="1" onkeypress='validate(event)'> 
  <div class="help-block with-errors"></div>      
  </div>
 </div>  
        
        
 <div id="DivClassTypeNew4" class="col-md-3" style="display: <?php if ($GetClassInfo->ClassType=='4') { echo 'selected'; } else { echo 'none'; } ?>;">	        
  <div class="form-group">
  <label><?php echo lang('new_class_define_week') ?></label>
  <input type="number" class="form-control" name="ClassRepeat" id="ClassRepeat" value="<?php echo $GetClassInfo->ClassRepeat; ?>" min="1" onkeypress='validate(event)'>
  <div class="help-block with-errors"></div>      
  </div>
 </div>          
 
   
 </div> 
        
                    </div>
                </div>
                
                
                <div id="step-3" style="padding-top: 10px;">
                <h4><strong><?php echo lang('class_details_register') ?></strong></h4>
                <div id="form-step-2" role="form" data-toggle="validator">
   
                    
  <div class="row">
                    
  <div class="col-md-6">	     
    <div class="form-group">
  <label><?php echo lang('max_participants') ?></label>
	<input type="number" min="1" class="form-control" name="MaxClient" id="MaxClientNew" value="<?php echo $GetClassInfo->MaxClient; ?>" onkeypress='validate(event)' required>  
    <div class="help-block with-errors"></div>    
	</div> 
  </div>                      
<div class="col-md-6">
    
 <div class="form-group">
  <label><?php echo lang('class_details_membership_type') ?></label>
  <select class="form-control text-start" name="ClassLimitTypes" id="ClassLimitTypes" >
    <option value="0" <?php if ($GetClassInfo->ClassLimitTypes=='0') { echo 'selected'; } else {} ?> ><?php echo lang('new_class_reg_show') ?></option>
    <option value="1" <?php if ($GetClassInfo->ClassLimitTypes=='1') { echo 'selected'; } else {} ?> ><?php echo lang('yes') ?></option>
	</select>  
	</div> 
    
  </div>  

    
</div>   
                    
                    
<div id="DivClassLimitTypes" style="display: <?php if ($GetClassInfo->ClassLimitTypes=='1') { echo 'selected'; } else { echo 'none'; } ?>;">
   

<div id="GetGroupId">    
<?php 
$Fixi = '1';   
$CheckLimitCounts = DB::table('classstudio_date_roles')->where('CompanyNum','=',$CompanyNum)->where('ClassId','=',$GetClassInfo->id)->get(); 
$GetClassInfoCount = count($CheckLimitCounts);    
foreach ($CheckLimitCounts as $CheckLimitCount) {    
?>  
    
<div id="Group<?php echo $Fixi; ?>Div">     
 
<div id="GroupId">
<div class="row">
<div class="col-6">
<div class="form-group" >
<label><?php echo lang('choose_membership_type') ?></label>
<a id="ClickSelectAll" class="ClickSelectAll" data-num="<?php echo $Fixi; ?>" href="javascript:void(0)" style="float:left;display: none;"><?php echo lang('select_all') ?></a>
<select class="form-control js-example-basic-single select2multipleDesk newid<?php echo $Fixi ?> text-start" name="ClassMemberType<?php echo $Fixi ?>[]" id="ClassMemberType<?php echo $Fixi ?>"   multiple="multiple" data-select2order="true" style="width: 100%;">  
                <?php 
                $myArray = explode(',', $CheckLimitCount->MemberShipType);
                $SectionInfos = DB::table('membership_type')->where('CompanyNum','=',$CompanyNum)->get();
                foreach ($SectionInfos as $SectionInfo) {	
                $selected = (in_array($SectionInfo->id, $myArray)) ? ' selected="selected"' : '';  
                    
                if ($selected!='') {
                DB::table('templistmember')->insertGetId(
                array('CompanyNum' => $CompanyNum, 'GroupNum' => $Fixi, 'GroupNumber' => $GroupNumber, 'ClassId' => $SectionInfo->id) );     
                }    
                    
                    
                ?>  
                <option value="<?php echo $SectionInfo->id; ?>" <?php echo @$selected; ?> ><?php echo $SectionInfo->Type; ?></option>	  
                <?php 
                 }
                ?> 
    </select>
<input type="hidden" id="CheckClassMemberType<?php echo $Fixi; ?>" value="">
<div class="help-block with-errors"></div>
</div>
</div>
<div class="col-3">
<div class="form-group" >
<label><?php echo lang('max_participants') ?></label>
<input type="number" min="1" name="MaxClientMemberShip<?php echo $Fixi; ?>" id="MaxClientMemberShip<?php echo $Fixi; ?>" class="form-control MaxClientMemberShip" value="<?php echo $CheckLimitCount->Value; ?>">
</div>
</div>
<div class="col-md-3" style="padding-top: 35px;" >
<a href="javascript:;" class="btn btn-danger btn-sm" onclick='removeElementgroup("Group<?php echo $Fixi; ?>Div","<?php echo $Fixi; ?>")' title="<?php echo lang('remove_restrict') ?>"><?php echo lang('remove_restrict') ?> <i class="fas fa-trash-alt"></i></a>    

</div>
</div>
<hr class="hrclass">
</div>    
    
</div>    
<?php ++ $Fixi; } ?>     
</div>   
 
   
    
<a class="btn btn-dark btn-sm" href="javascript:void(0);" onclick="addElementgroup();"><?php echo lang('add_new_restrict') ?> +</a>   
<input type="hidden" value="<?php echo $GetClassInfoCount; ?>" id="theValueGroup" name="tGroups"/>         
                    
</div>                    
                    
                    
                    
                    
    <hr>             
                    
  <div class="row">
          
 <div class="col-md-3">	     
  <div class="form-group">
  <label><?php echo lang('define_min_participants') ?></label>
  <select class="form-control text-start" name="MinClass" id="MinClassNew" >
    <option value="0" <?php if ($GetClassInfo->MinClass=='0') { echo 'selected'; } else {} ?> ><?php echo lang('no') ?></option>
    <option value="1" <?php if ($GetClassInfo->MinClass=='1') { echo 'selected'; } else {} ?> ><?php echo lang('yes') ?></option>
	</select>  
	</div>  
  </div>
     
  <div class="col-md-3 DivMinClassNumNew" style="display: <?php if ($GetClassInfo->MinClass=='1') { echo 'selected'; } else { echo 'none'; } ?>;">	     
  <div class="form-group">
  <label><?php echo lang('min_participants') ?></label>
	<input type="number" class="form-control" name="MinClassNum" id="MinClassNumNew" value="<?php echo $GetClassInfo->MinClassNum ?>" onkeypress='validate(event)'> 
    <div class="help-block with-errors"></div>  
	</div>  
  </div>
  
  <div class="col-md-3 DivMinClassNumNew" style="display: <?php if ($GetClassInfo->MinClass=='1') { echo 'selected'; } else { echo 'none'; } ?>;">	    
    <div class="form-group">
  <label><?php echo lang('check_time_before_class') ?></label>
	<input type="text" class="form-control" name="ClassTimeCheck" id="ClassTimeCheckNew" value="<?php echo $GetClassInfo->ClassTimeCheck ?>"> 
    <div class="help-block with-errors"></div>    
	</div>
    </div>   
     <div class="col-md-3 DivMinClassNumNew" style="display: <?php if ($GetClassInfo->MinClass=='1') { echo 'selected'; } else { echo 'none'; } ?>;">	   
    <div class="form-group">
    <label><?php echo lang('option') ?></label>
    <select class="form-control text-start" name="ClassTimeTypeCheck" id="ClassTimeTypeCheckNew" >
    <option value="1" <?php if ($GetClassInfo->ClassTimeTypeCheck=='1') { echo 'selected'; } else {} ?> ><?php echo lang('minutes') ?></option>
    <option value="2" <?php if ($GetClassInfo->ClassTimeTypeCheck=='2') { echo 'selected'; } else {} ?> ><?php echo lang('hours') ?></option>         
    </select> 
    </div> 
    </div>     
     

 </div>                     
                    
                    
   <hr>    
                    
     <div class="row">
	    
  <div class="col-md-4">	     
  <div class="form-group">
  <label><?php echo lang('allow_wlist') ?></label>
  <select class="form-control text-start" name="ClassWating" id="ClassWatingNew" >
  <option value="0" <?php if ($GetClassInfo->ClassWating=='0') { echo 'selected'; } else {} ?> ><?php echo lang('yes') ?></option>
  <option value="1" <?php if ($GetClassInfo->ClassWating=='1') { echo 'selected'; } else {} ?> ><?php echo lang('no') ?></option>
  </select>  

  </div>  
  </div>	  
	  
	  
  <div class="col-md-4 WatingListDiv" style="display: <?php if ($GetClassInfo->ClassWating=='0') { echo 'selected'; } else { echo 'none'; } ?>;">	     
  <div class="form-group">
  <label><?php echo lang('limit_wlist') ?></label>
  <select class="form-control text-start" name="MaxWatingList" id="WatingListActNew" >
  <option value="0" <?php if ($GetClassInfo->MaxWatingList=='0') { echo 'selected'; } else {} ?> ><?php echo lang('yes') ?></option>
  <option value="1" <?php if ($GetClassInfo->MaxWatingList=='1') { echo 'selected'; } else {} ?> ><?php echo lang('no') ?></option>
  </select>  

  </div>  
  </div>    
	   
  <div class="col-md-4 WatingListNumDiv"  style="display: <?php if ($GetClassInfo->MaxWatingList=='0') { echo 'selected'; } else { echo 'none'; } ?>;">	     
  <div class="form-group">
  <label><?php echo lang('max_waiting') ?></label>
  <input type="number" class="form-control" name="NumMaxWatingList" id="WatingListNumNew" value="<?php echo $GetClassInfo->NumMaxWatingList ?>" onkeypress='validate(event)'> 
  <div class="help-block with-errors"></div>       
  </div> 
  </div>
	  

  </div>     
                      

  <hr>
                    
   <div class="row">
                    
  <div class="col-md-4">	     
  <div class="form-group">
  <label><?php echo lang('class_details_waiting') ?></label>
  <select class="form-control js-example-basic-single select2LimitLevel text-start" data-placeholder="<?php echo lang('select_rank') ?>"  name="LimitLevel[]" id="LimitLevel"   multiple="multiple" data-select2order="true" style="width: 100%;">
  <option value="0" <?php if ($GetClassInfo->LimitLevel=='0' || $GetClassInfo->LimitLevel=='') { echo 'selected'; } else {} ?> ><?php echo lang('all_ranks') ?></option>
  <?php 
  $myArray = explode(',', $GetClassInfo->LimitLevel);      
  $ClinetLevels = DB::table('clientlevel')->where('CompanyNum','=',$CompanyNum)->get();
  foreach ($ClinetLevels as $ClinetLevel) {	
  $selected = (in_array($ClinetLevel->id, $myArray)) ? ' selected="selected"' : '';       
  ?>  
  <option value="<?php echo $ClinetLevel->id; ?>" <?php echo @$selected; ?> ><?php echo $ClinetLevel->Level; ?></option>	  
  <?php 
  }
  ?>         
  </select>  
  </div>  
  </div>                      
<div class="col-md-4">
<div class="form-group">
  <label><?php echo lang('class_details_gender') ?></label>
  <select class="form-control text-start" name="GenderLimit" id="GenderLimit" >
    <option value="0" <?php if ($GetClassInfo->GenderLimit=='0') { echo 'selected'; } else {} ?> ><?php echo lang('all') ?></option>
    <option value="1" <?php if ($GetClassInfo->GenderLimit=='1') { echo 'selected'; } else {} ?> ><?php echo lang('men') ?></option>
    <option value="2" <?php if ($GetClassInfo->GenderLimit=='2') { echo 'selected'; } else {} ?> ><?php echo lang('women') ?></option>  
	</select>  
	</div>  
  </div>  
       
<div class="col-md-4">
    
 <div class="form-group" style="display: none;">
  <label><?php echo lang('class_details_free_class') ?></label>
  <select class="form-control text-start" name="FreeClass" id="FreeClass" >
    <option value="0" <?php if ($GetClassInfo->FreeClass=='0') { echo 'selected'; } else {} ?> ><?php echo lang('no') ?></option>
    <option value="1" <?php if ($GetClassInfo->FreeClass=='1') { echo 'selected'; } else {} ?> ><?php echo lang('new_class_free_member') ?></option>
    <option value="2" <?php if ($GetClassInfo->FreeClass=='2') { echo 'selected'; } else {} ?> ><?php echo lang('new_class_free_all') ?></option>  
	</select>  
	</div> 
   <input type="hidden" name="FreeClass" value="0">    
  </div>         
       
       
</div>                         

                </div>
                </div>
                
                <div id="step-4" style="padding-top: 10px;">
                    <h4><strong><?php echo lang('class_details_cancel') ?></strong></h4>
               
                    <div id="form-step-3" role="form" data-toggle="validator">

                     
 <div class="row">
 <div class="col-md-4">	     
  <div class="form-group">
  <label><?php echo lang('send_reminder_to_client') ?></label>
  <select class="form-control text-start" name="SendReminder" id="SendReminderNew" >
  <option value="0" <?php if ($GetClassInfo->SendReminder=='0') { echo 'selected'; } else {} ?> ><?php echo lang('yes') ?></option>
  <option value="1" <?php if ($GetClassInfo->SendReminder=='1') { echo 'selected'; } else {} ?> ><?php echo lang('no') ?></option>
  </select>  
	</div>  
  </div>
     
  <div class="col-md-4 SendReminderNew" style="display: <?php if ($GetClassInfo->SendReminder=='0') { echo 'selected'; } else { echo 'none'; } ?>;">	     
  <div class="form-group">
  <label><?php echo lang('define_time_to_send_reminder') ?></label>
  <select class="form-control text-start" name="TypeReminder" id="TypeReminderNew" >
  <option value="1" <?php if ($GetClassInfo->TypeReminder=='1') { echo 'selected'; } else {} ?> ><?php echo lang('in_lesson_day') ?></option>
  <option value="2" <?php if ($GetClassInfo->TypeReminder=='2') { echo 'selected'; } else {} ?> ><?php echo lang('day_before_lesson_day') ?></option>
  </select>  

  </div>  
  </div>
     
  <div class="col-md-4 SendReminderNew" style="display: <?php if ($GetClassInfo->SendReminder=='0') { echo 'selected'; } else { echo 'none'; } ?>;">	     
  <div class="form-group">
  <label><?php echo lang('set_time_sending_reminder') ?></label>
  <input type="time" class="form-control" name="TimeReminder" id="TimeReminderNew" step="300" value="<?php echo $GetClassInfo->TimeReminder ?>" max="" min="" required>
  <div class="help-block with-errors"></div>      
  </div> 
  </div>
        

 </div>                        
  
   <hr>
   <div class="row">
 <div class="col-md-4">	     
  <div class="form-group">
  <label><?php echo lang('choose_cancel_rules') ?></label>
  <select class="form-control text-start" name="CancelLaw" id="CancelLawNew" >
  <option value="1" <?php if ($GetClassInfo->CancelLaw=='1') { echo 'selected'; } else {} ?> ><?php echo lang('lesson_day_until_one_hour') ?></option>
  <option value="2" <?php if ($GetClassInfo->CancelLaw=='2') { echo 'selected'; } else {} ?> ><?php echo lang('day_before_until_one_hour') ?></option>
  <option value="3" <?php if ($GetClassInfo->CancelLaw=='3') { echo 'selected'; } else {} ?> ><?php echo lang('day_selection_until_hour') ?></option>
  <option value="4" <?php if ($GetClassInfo->CancelLaw=='4') { echo 'selected'; } else {} ?> ><?php echo lang('unable_to_cancel_in_app') ?></option>       
  <option value="5" <?php if ($GetClassInfo->CancelLaw=='5') { echo 'selected'; } else {} ?> ><?php echo lang('free_cancel') ?></option>       
  </select>  
	</div>  
  </div>
     
   <div id="DivCancelLawNew3" class="col-md-4" style="display: <?php if ($GetClassInfo->CancelLaw=='3') { echo 'selected'; } else { echo 'none'; } ?>;">	     
  <div class="form-group">
  <label><?php echo lang('choose_day_before_lesson_day') ?></label>
  <select name="CancelDay" id="CancelDayNew" data-placeholder="<?php echo lang('choose_day') ?>" class="form-control" style="width:100%;">
  <option value=""><?php echo lang('choose_day') ?></option>  


  </select>
   <div class="help-block with-errors"></div>       
  </div>  
  </div>   
       
     
  <div id="DivCancelLawNew" class="col-md-4" style="display: <?php if ($GetClassInfo->CancelLaw=='1' || $GetClassInfo->CancelLaw=='2' || $GetClassInfo->CancelLaw=='3') { echo 'selected'; } else { echo 'none'; } ?>;">	     
  <div class="form-group">
  <label><?php echo lang('set_time_to_cancel') ?></label>
  <input name="CancelTillTime" id="CancelTillTimeNew" type="time" step="300" min="" value="<?php echo $GetClassInfo->CancelTillTime; ?>" class="form-control" required> 
   <div class="help-block with-errors"></div>       
  </div> 
  </div>

 </div>   
                        
        
    <div id="DivCancelLawNew6" class="alertb alert-warning" style="display: <?php if ($GetClassInfo->CancelLaw=='3') { echo 'selected'; } else { echo 'none'; } ?>;"><?php echo lang('select_day_notice_one') ?> <u><?php echo lang('day') ?></u> <?php echo lang('before_class_day_notice') ?><br>
    <?php echo lang('attention_must_choose_day_before_lesson') ?></div> 
    
    
    <div id="DivCancelLawNew4" class="alertb alert-warning" style="display: <?php if ($GetClassInfo->CancelLaw=='4') { echo 'selected'; } else { echo 'none'; } ?>;"><?php echo lang('attention_this_option_wont_appear_to_client') ?></div>   
       
    <div id="DivCancelLawNew5" class="alertb alert-warning" style="display: <?php if ($GetClassInfo->CancelLaw=='5') { echo 'selected'; } else { echo 'none'; } ?>;"><?php echo lang('attention_client_cannot_cancel_anytime_free') ?></div>                          
  <hr>
                        
 <div class="row">
 <div class="col-md-4">	     
  <div class="form-group">
  <label><?php echo lang('class_details_cancel_button') ?></label>
  <select class="form-control text-start" name="StopCancel" id="StopCancel" >
  <option value="0" <?php if ($GetClassInfo->StopCancel=='0') { echo 'selected'; } else {} ?> ><?php echo lang('yes') ?></option>
  <option value="1" <?php if ($GetClassInfo->StopCancel=='1') { echo 'selected'; } else {} ?> ><?php echo lang('no') ?></option>
  </select>  
	</div>  
  </div>
     
  <div class="col-md-4 StopCancel" style="display: <?php if ($GetClassInfo->StopCancel=='0') { echo 'selected'; } else { echo 'none'; } ?>;">	     
   <div class="form-group">
  <label><?php echo lang('set_time_before_class') ?></label>
  <input type="number" class="form-control" name="StopCancelTime" id="StopCancelTime" value="<?php echo $GetClassInfo->StopCancelTime; ?>" onkeypress='validate(event)'>        
  <div class="help-block with-errors"></div>        
  </div> 
  </div>
     
  <div class="col-md-4 StopCancel" style="display: <?php if ($GetClassInfo->StopCancel=='0') { echo 'selected'; } else { echo 'none'; } ?>;">	     
  <div class="form-group">
  <label><?php echo lang('option') ?></label>
  <select class="form-control text-start" name="StopCancelType" id="StopCancelType" >
  <option value="1" <?php if ($GetClassInfo->StopCancelType=='1') { echo 'selected'; } else {} ?> ><?php echo lang('minutes') ?></option>
  <option value="2" <?php if ($GetClassInfo->StopCancelType=='2') { echo 'selected'; } else {} ?> ><?php echo lang('hours') ?></option>
  </select>  

  </div> 
  </div>
 </div>   
                        
  <div class="alertb alert-warning StopCancel" style="display: <?php if ($GetClassInfo->StopCancel=='0') { echo 'selected'; } else { echo 'none'; } ?>;"><?php echo lang('customer_cant_cancel') ?></div>                          
                        
                        
                        
                        
                </div>
                </div>
                
                
                <div id="step-5" style="padding-top: 10px;" class="">
                    <h4><strong><?php echo lang('class_details_display') ?></strong></h4>
  
                    <div id="form-step-4" role="form" data-toggle="validator">
        <div class="row">

  <div class="col-md-3">	     
  <div class="form-group">
  <label><?php echo lang('display_participants_amount') ?></label>
  <select class="form-control text-start" name="ShowClientNum" id="ShowClientNum" >
  <option value="0" <?php if ($GetClassInfo->ShowClientNum=='0') { echo 'selected'; } else {} ?> ><?php echo lang('yes') ?></option>
  <option value="1" <?php if ($GetClassInfo->ShowClientNum=='1') { echo 'selected'; } else {} ?> ><?php echo lang('no') ?></option>
  </select>    
  </div> 
  </div>
      
 <div class="col-md-3">	     
    <div class="form-group">
  <label><?php echo lang('display_participants_names') ?></label>
  <select class="form-control text-start" name="ShowClientName" id="ShowClientName" >
  <option value="0" <?php if ($GetClassInfo->ShowClientName=='0') { echo 'selected'; } else {} ?> ><?php echo lang('yes') ?></option>
  <option value="1" <?php if ($GetClassInfo->ShowClientName=='1') { echo 'selected'; } else {} ?> ><?php echo lang('no') ?></option>
  </select>  
	</div> 
  </div>   
      
 <div class="col-md-3">	     
    <div class="form-group">
  <label><?php echo lang('class_details_waitlist_display') ?></label>
  <select class="form-control text-start" name="WatingListOrederShow" id="WatingListOrederShow" >
  <option value="0" <?php if ($GetClassInfo->WatingListOrederShow=='0') { echo 'selected'; } else {} ?>><?php echo lang('yes') ?></option>
  <option value="1" <?php if ($GetClassInfo->WatingListOrederShow=='1') { echo 'selected'; } else {} ?> ><?php echo lang('no') ?></option>
  </select>  
	</div> 
  </div>         
   
 <div class="col-md-3">	     
  <div class="form-group">
    <label><?php echo lang('lesson_level') ?></label>
  <select class="form-control text-start" name="ClassLevel" >
    <option value="0" <?php if ($GetClassInfo->ClassLevel=='0') { echo 'selected'; } else {} ?> ><?php echo lang('without_class_level') ?></option>
    <option value="1" <?php if ($GetClassInfo->ClassLevel=='1') { echo 'selected'; } else {} ?> ><?php echo lang('beginners_class') ?></option>
	<option value="2" <?php if ($GetClassInfo->ClassLevel=='2') { echo 'selected'; } else {} ?> ><?php echo lang('dynamic_speed_lesson') ?></option>
	<option value="3" <?php if ($GetClassInfo->ClassLevel=='3') { echo 'selected'; } else {} ?> ><?php echo lang('high_level_class') ?></option>
	</select>  
	</div>  
  </div>              
            
     
 </div>  
                        
 <div class="row">
<div class="col-md-12">	                        
<div class="form-group">
  <label><?php echo lang('display_gym_equipment') ?></label>
    <select class="form-control js-example-basic-single select2Desk text-start" name="ClassDevice" id="ClassDevice"  data-placeholder="<?php echo lang('choose_gym_equipment_table') ?>"  style="width: 100%;" >
    <option value=""></option>    
  <?php 
  $SectionInfos = DB::table('numbers')->where('CompanyNum','=',$CompanyNum)->where('Status','=','0')->orderBy('Name', 'ASC')->get();
  foreach ($SectionInfos as $SectionInfo) {	
  ?>  
  <option value="<?php echo $SectionInfo->id; ?>" <?php if ($GetClassInfo->ClassDevice==$SectionInfo->id) { echo 'selected'; } else {} ?> ><?php echo $SectionInfo->Name; ?></option>	  
  <?php 
		 }
  ?>  
  </select>
  </div>
 </div>                       
 </div>                       
   
                        
<hr>
                        

                        
 <div class="row">
 <div class="col-md-4">	     
  <div class="form-group">
  <label><?php echo lang('class_details_order_class') ?></label>
  <select class="form-control text-start" name="OpenOrder" id="OpenOrder" >
  <option value="0" <?php if ($GetClassInfo->OpenOrder=='0') { echo 'selected'; } else {} ?> ><?php echo lang('yes_set_time') ?></option>
  <option value="1" <?php if ($GetClassInfo->OpenOrder=='1') { echo 'selected'; } else {} ?> ><?php echo lang('no_anytime') ?></option>
  </select>  
	</div>  
  </div>
     
  <div class="col-md-4 OpenOrder" style="display: <?php if ($GetClassInfo->OpenOrder=='0') { echo 'selected'; } else { echo 'none'; } ?>;">	     
   <div class="form-group">
  <label><?php echo lang('set_time_before_class') ?></label>
  <input type="number" class="form-control" name="OpenOrderTime" id="OpenOrderTime" value="<?php echo $GetClassInfo->OpenOrderTime; ?>" onkeypress='validate(event)'>        
  <div class="help-block with-errors"></div>        
  </div> 
  </div>
     
  <div class="col-md-4 OpenOrder" style="display: <?php if ($GetClassInfo->OpenOrder=='0') { echo 'selected'; } else { echo 'none'; } ?>;">	     
  <div class="form-group">
  <label><?php echo lang('option') ?></label>
  <select class="form-control text-start" name="OpenOrderType" id="OpenOrderType" >
  <option value="1" <?php if ($GetClassInfo->OpenOrderType=='1') { echo 'selected'; } else {} ?> ><?php echo lang('minutes') ?></option>
  <option value="2" <?php if ($GetClassInfo->OpenOrderType=='2') { echo 'selected'; } else {} ?> ><?php echo lang('hours') ?></option>
  </select>  

  </div> 
  </div>
 </div>   
                        
  <div class="alertb alert-warning OpenOrder" style="display: <?php if ($GetClassInfo->OpenOrder=='0') { echo 'selected'; } else { echo 'none'; } ?>;"><?php echo lang('customer_cant_cancel') ?></div>                          
                        
                        
 <div class="row">
 <div class="col-md-4">	     
  <div class="form-group">
  <label><?php echo lang('class_details_order_class_cancel') ?></label>
  <select class="form-control text-start" name="CloseOrder" id="CloseOrder" >
  <option value="0" <?php if ($GetClassInfo->CloseOrder=='0') { echo 'selected'; } else {} ?> ><?php echo lang('yes_set_time') ?></option>
  <option value="1" <?php if ($GetClassInfo->CloseOrder=='1') { echo 'selected'; } else {} ?> ><?php echo lang('no_anytime') ?></option>
  </select>  
	</div>  
  </div>
     
  <div class="col-md-4 CloseOrder" style="display: <?php if ($GetClassInfo->CloseOrder=='0') { echo 'selected'; } else { echo 'none'; } ?>;">	     
   <div class="form-group">
  <label><?php echo lang('set_time_before_class') ?></label>
  <input type="number" class="form-control" name="CloseOrderTime" id="CloseOrderTime" value="<?php echo $GetClassInfo->CloseOrderTime; ?>" onkeypress='validate(event)'>        
  <div class="help-block with-errors"></div>        
  </div> 
  </div>
     
  <div class="col-md-4 CloseOrder" style="display: <?php if ($GetClassInfo->CloseOrder=='0') { echo 'selected'; } else { echo 'none'; } ?>;">	     
  <div class="form-group">
  <label><?php echo lang('option') ?></label>
  <select class="form-control text-start" name="CloseOrderType" id="CloseOrderType" >
  <option value="1" <?php if ($GetClassInfo->CloseOrderType=='1') { echo 'selected'; } else {} ?> ><?php echo lang('minutes') ?></option>
  <option value="2" <?php if ($GetClassInfo->CloseOrderType=='2') { echo 'selected'; } else {} ?> ><?php echo lang('hours') ?></option>
  </select>  

  </div> 
  </div>
 </div>   
                        
  <div class="alertb alert-warning CloseOrder" style="display: <?php if ($GetClassInfo->CloseOrder=='0') { echo 'selected'; } else { echo 'none'; } ?>;"><?php echo lang('customer_cant_cancel') ?></div>                         
            
                        
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
  
$( ".selectAddItem" ).select2( {theme:"bootstrap", placeholder: "<?php echo lang('choose') ?>", 'language':"he", dir: "rtl" } );
$( ".select2Desk" ).select2( {theme:"bootstrap", placeholder: "<?php echo lang('choose') ?>", 'language':"he", dir: "rtl", allowClear:"true" } );
$( ".select2LimitLevel" ).select2( {theme:"bootstrap", placeholder: "<?php echo lang('choose') ?>", 'language':"he", dir: "rtl" } );      
      
$(document).ready(function(){      
$('#DayNew').val('<?php echo $GetClassInfo->DayNum; ?>').trigger('change'); 
$('#ClassTypeNew').val('<?php echo $GetClassInfo->ClassType; ?>').trigger('change');     
});  
      
$("#DayNew").change(function() {

var Id = this.value; 
if (Id=='0') {    
/// ראשון    
$('#CancelDayNew').find('option').remove().end().append('<option value=""><?php echo lang('choose_day') ?></option><option value="6"><?php echo lang('saturday') ?></option><option value="5"><?php echo lang('friday') ?></option><option value="4"><?php echo lang('thursday') ?></option><option value="3"><?php echo lang('wednesday') ?></option><option value="2"><?php echo lang('tuesday') ?></option><option value="1"><?php echo lang('monday') ?></option><option value="0" disabled><?php echo lang('sunday') ?></option>'); 
}
else if (Id=='1') {     
/// שני    
$('#CancelDayNew').find('option').remove().end().append('<option value=""><?php echo lang('choose_day') ?></option><option value="0"><?php echo lang('sunday') ?></option><option value="6"><?php echo lang('saturday') ?></option><option value="5"><?php echo lang('friday') ?></option><option value="4"><?php echo lang('thursday') ?></option><option value="3"><?php echo lang('wednesday') ?></option><option value="2"><?php echo lang('tuesday') ?></option><option value="1" disabled><?php echo lang('monday') ?></option>');
}
else if (Id=='2') {      
/// שלישי    
$('#CancelDayNew').find('option').remove().end().append('<option value=""><?php echo lang('choose_day') ?></option><option value="1"><?php echo lang('monday') ?></option><option value="0"><?php echo lang('sunday') ?></option><option value="6"><?php echo lang('saturday') ?></option><option value="5"><?php echo lang('friday') ?></option><option value="4"><?php echo lang('thursday') ?></option><option value="3"><?php echo lang('wednesday') ?></option><option value="2" disabled><?php echo lang('tuesday') ?></option>'); 
}
else if (Id=='3') {      
/// רביעי    
$('#CancelDayNew').find('option').remove().end().append('<option value=""><?php echo lang('choose_day') ?></option><option value="2"><?php echo lang('tuesday') ?></option><option value="1"><?php echo lang('monday') ?></option><option value="0"><?php echo lang('sunday') ?></option><option value="6"><?php echo lang('saturday') ?></option><option value="5"><?php echo lang('friday') ?></option><option value="4"><?php echo lang('thursday') ?></option><option value="3" disabled><?php echo lang('wednesday') ?></option>'); 
}
else if (Id=='4') {      
/// חמישי    
$('#CancelDayNew').find('option').remove().end().append('<option value=""><?php echo lang('choose_day') ?></option><option value="3"><?php echo lang('wednesday') ?></option><option value="2"><?php echo lang('tuesday') ?></option><option value="1"><?php echo lang('monday') ?></option><option value="0"><?php echo lang('sunday') ?></option><option value="6"><?php echo lang('saturday') ?></option><option value="5"><?php echo lang('friday') ?></option><option value="4" disabled><?php echo lang('thursday') ?></option>');
}
else if (Id=='5') {      
/// שישי    
$('#CancelDayNew').find('option').remove().end().append('<option value=""><?php echo lang('choose_day') ?></option><option value="4"><?php echo lang('thursday') ?></option><option value="3"><?php echo lang('wednesday') ?></option><option value="2"><?php echo lang('tuesday') ?></option><option value="1"><?php echo lang('monday') ?></option><option value="0"><?php echo lang('sunday') ?></option><option value="6"><?php echo lang('saturday') ?></option><option value="5" disabled><?php echo lang('friday') ?></option>'); 
}
else if (Id=='6') {      
/// שבת    
$('#CancelDayNew').find('option').remove().end().append('<option value=""><?php echo lang('choose_day') ?></option><option value="5"><?php echo lang('friday') ?></option><option value="4"><?php echo lang('thursday') ?></option><option value="3"><?php echo lang('wednesday') ?></option><option value="2"><?php echo lang('tuesday') ?></option><option value="1"><?php echo lang('monday') ?></option><option value="0"><?php echo lang('sunday') ?></option><option value="6" disabled><?php echo lang('saturday') ?></option>');
}
else {
$('#CancelDayNew').find('option').remove().end().append('<option value=""><?php echo lang('choose_day') ?></option>');    
}
    
});       
      
 
$(document).ready(function(){  
$('#CancelDayNew').val('<?php echo $GetClassInfo->CancelDay; ?>');         
});        
      
      
$('#LimitLevel').on('select2:select', function (e) {    
var selected = $(this).val();

  if(selected != null)
  {
    if(selected.indexOf('0')>=0){
      $(this).val('0').select2( {theme:"bootstrap", placeholder: "<?php echo lang('choose_class_type') ?>", 'language':"he", dir: "rtl" } );
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
  $('#ClassCountNew').val('1');
  $('#ClassRepeat').val('1'); 
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
  $('#MinClassNumNew').prop('max', '1000');
  $('#MinClassNumNew').prop('min', '0');
  $('#MinClassNumNew').val('0');      
      
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
      
     
 $("#OpenOrder").change(function() {
  
  var Id = this.value;
  if (Id=='0'){    
  $('.OpenOrder').css("display", "block");
  $("#OpenOrderTime").prop('required',true);      
  } 
  else {
  $('.OpenOrder').css("display", "none");
  $("#OpenOrderTime").prop('required',false);      
  }   
    
});	 
      
 $("#CloseOrder").change(function() {
  
  var Id = this.value;
  if (Id=='0'){    
  $('.CloseOrder').css("display", "block");
  $("#CloseOrderTime").prop('required',true);      
  } 
  else {
  $('.CloseOrder').css("display", "none");
  $("#CloseOrderTime").prop('required',false);      
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
            var btnFinish = $('<button></button>').text('<?php echo lang('class_end') ?>')
                                             .addClass('btn btn-success')
                                             .on('click', function(){
                                                    if( !$(this).hasClass('disabled')){
                                                        var elmForm = $("#DuplicateClassNewPop");
                                                        if(elmForm){
                                                            elmForm.validator('validate');
                                                            var elmErr = elmForm.find('.has-error');
                                                            if(elmErr && elmErr.length > 0){
                                                                alert('<?php echo lang('fill_before_save') ?>');
                                                                return false;
                                                            }
                                                            else{
                                                                //alert('מוכן לשליחה');
                                                                elmForm.submit();
                                                                $('#DuplicateNewClass').modal('hide')
                                                               // SubmitPayment();
                                                                return false;
                                                            }
                                                        }
                                                    }
                                                });
            var btnCancel = $('<button type="button" class="BtnClassWizs"></button>').text('<?php echo lang('action_cacnel') ?>')
                                             .addClass('btn btn-danger')
                                             .on('click', function(){
                                                    var modal = $('#DuplicateNewClass');
                                                    modal.modal('hide');
                                                    location.hash = "";
                                                    $('#ResultDuplicateNewClass').html("");
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
                //    var CheckClassMemberType1 = $('#CheckClassMemberType1').val();
                    var MembershipNew = $('#MembershipNew').val();
                    if(elmErr && elmErr.length > 0){
                        // Form validation failed
                        return false;
                    }
                }
                
                return true;
            });

            $("#smartwizard").on("showStep", function(e, anchorObject, stepNumber, stepDirection) {
                // Enable finish button only on last step
                if(stepNumber == 5){
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
		newdiv.innerHTML = ' <div id="GroupId1"><div class="row"><div class="col-6"><div class="form-group" ><label><?php echo lang('choose_membership_type') ?></label> <a id="ClickSelectAll" class="ClickSelectAll" data-num="'+num+'" href="javascript:void(0)" style="float:left;display: none;"><?php echo lang('select_all') ?></a> <select class="form-control js-example-basic-single select2multipleDesk newid'+num+' text-start" name="ClassMemberType'+num+'[]" id="ClassMemberType'+num+'" multiple="multiple"  data-select2order="true" style="width: 100%;"></select><input type="hidden" id="CheckClassMemberType'+num+'" value=""><div class="help-block with-errors"></div></div></div><div class="col-3"><div class="form-group" ><label><?php echo lang('max_participants') ?></label><input type="number" min="1" name="MaxClientMemberShip'+num+'" id="MaxClientMemberShip'+num+'" class="form-control MaxClientMemberShip" value="1"></div></div><div class="col-md-3" style="padding-top: 35px;" ><a href="javascript:;" class="btn btn-danger btn-sm" onclick=\'removeElementgroup(\"'+divIdName+'\",\"'+num+'\")\' title="<?php echo lang('a_remove_single') ?>"><?php echo lang('remove_restrict') ?> <i class="fas fa-trash-alt"></i></a></div></div><hr class="hrclass"></div>  ';
		ni.appendChild(newdiv);
        $(".select2multipleDesk").select2( {theme:"bootstrap", placeholder: "<?php echo lang('choose_membership_type') ?>", 'language':"he", dir: "rtl",ajax: {
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
    
 $(".select2multipleDesk").select2( {theme:"bootstrap", placeholder: "<?php echo lang('choose_membership_type') ?>", 'language':"he", dir: "rtl",ajax: {
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