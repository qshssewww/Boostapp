<?php
ini_set("max_execution_time" , 0);

require_once '../app/init.php';
require_once 'Classes/Settings.php';

$companySettings = (new Settings())->getSettings(Auth::user()->CompanyNum);

?>

<?php if (Auth::guest()): redirect_to('index.php'); endif ?>



<?php if (Auth::check()):?>
<?php if (Auth::userCan('11')): ?>
<?php

$pageTitle = lang('app_settings');
require_once '../app/views/headernew.php';
 $AffID = Auth::user()->id;
 $AffName = Auth::user()->display_name;
 $CompanyNum = Auth::user()->CompanyNum;
 $Supplier = DB::table('appsettings')->where('CompanyNum',  $CompanyNum)->first();
 
 $UserId = User::find(Auth::user()->id);
 $SettingsInfo = DB::table('settings')->where('CompanyNum',  $CompanyNum)->first();



?>

<style>
.card-header {
    cursor: pointer;
}	
</style>

<link href="assets/css/fixstyle.css" rel="stylesheet">

<!-- include summernote css/js -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.js"></script>




<div class="col-md-12 col-sm-12">



<!-- <div class="row pb-3">

<div class="col-md-6 col-sm-12 order-md-1">
<h3 class="page-header headertitlemain"  style="height:54px;">
<?php //echo $DateTitleHeader; ?>
</h3>
</div>

<div class="col-md-6 col-sm-12 order-md-4">
<h3 class="page-header headertitlemain"  style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
<i class="fas fa-mobile-alt fa-fw"></i> הגדרות אפליקציה
</div>
</h3>
</div>



</div>

<nav aria-label="breadcrumb" >
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-dark">ראשי</a></li>
  <li class="breadcrumb-item"><a href="SettingsDashboard.php" class="text-dark">הגדרות</a></li>
  <li class="breadcrumb-item active" aria-current="page">הגדרות אפליקציה</li>
  </ol>  
</nav>     -->



	
	

<div class="row" >

<?php include("SettingsInc/RightCards.php"); ?>
	
	<div class="col-md-10 col-sm-12">

	
	
	
	

<div class="tab-content">
    
    
      <div class="tab-pane fade show active text-start" role="tabpanel" id="appgeneral">
            <div class="card spacebottom">
			<div class="card-header text-start d-flex justify-content-between"><strong><?php echo lang('general') ?></strong></div>
 			<div class="card-body">  
             <form action="AppGeneral"  class="ajax-form clearfix"  autocomplete="off">

                 <?php if($CompanyNum && Auth::user()->role_id == 1) { ?>
             	<div class="form-group">
                <label><?php echo lang('app_renew_membership') ?></label>
                <select name="AppRenew" id="AppRenew" class="form-control">
                <option value="1" <?php if (@$Supplier->AppRenew == '1') {echo "selected";} ?>><?php echo lang('yes') ?></option>
                <option value="2" <?php if (@$Supplier->AppRenew == '2') {echo "selected";} ?>><?php echo lang('no') ?></option>
                </select>
                </div>
                <?php } ?>
                
                <div class="form-group" style="display: none;">
                <label><?php echo lang('app_renew_membership') ?></label>
                <select name="AppFreez" id="AppFreez" class="form-control">
                <option value="1" <?php if (@$Supplier->AppFreez == '1') {echo "selected";} ?>><?php echo lang('yes') ?></option>
                <option value="2" <?php if (@$Supplier->AppFreez == '2') {echo "selected";} ?>><?php echo lang('no') ?></option>
                </select>
                </div>
                <input type="hidden" name="AppFreez" value="<?php echo @$Supplier->AppFreez; ?>">
               <div class="alertb alert-warning" style="display: none;">
               <?php echo lang('app_settings_notice') ?>
               </div>  
                
                <div class="form-group" style="display: none;">
                <label><?php echo lang('q_app_recurring_payment') ?></label>
                <select name="AppKeva" id="AppKeva" class="form-control">
                <option value="1" <?php if (@$Supplier->AppKeva == '1') {echo "selected";} ?>><?php echo lang('yes') ?></option>
                <option value="2" <?php if (@$Supplier->AppKeva == '2') {echo "selected";} ?>><?php echo lang('no') ?></option>
                </select>
                </div>  
                <input type="hidden" name="AppKeva" value="<?php echo @$Supplier->AppKeva; ?>">
               <div class="alertb alert-warning" style="display: none;">
               <?php echo lang('app_recurring_payment_notice') ?>
               </div>  
                
               
                <div class="form-group">
                <label><?php echo lang('active_app_chat') ?></label>
                <select name="AppChat" id="AppChat" class="form-control">
                <option value="0" <?php if (@$Supplier->AppChat == '0') {echo "selected";} ?>><?php echo lang('yes') ?></option>
                <option value="1" <?php if (@$Supplier->AppChat == '1') {echo "selected";} ?>><?php echo lang('no') ?></option>
                </select>
                </div>   
                 
                 <?php if(Auth::user()->role_id == 1) { ?>
                <div class="form-group">
                <label><?php echo lang('morning_class_time') ?></label>
                <input type="time" name="MorningTime" class="form-control" value="<?php echo @$Supplier->MorningTime; ?>">
                </div>  
                
                <div class="form-group">
                <label><?php echo lang('evening_class_time') ?></label>
                <input type="time" name="EveningTime" class="form-control" value="<?php echo @$Supplier->EveningTime; ?>">
                </div>  
                <?php } ?>
                <hr>
                <h5><?php echo lang('app_settings_title_membership') ?></h5> 
                <div class="form-group">
                <label><?php echo lang('extra_days_class_order') ?></label>
                <input type="number" name="KevaDays" class="form-control" value="<?php echo @$Supplier->KevaDays; ?>" required>
                </div>    
                <div class="alertb alert-warning"><?php echo lang('app_settings_general_notice') ?><br>
                <?php echo lang('set_zero') ?></div>
                 
                 <?php if(Auth::user()->role_id == 1) { ?>
                 <div class="form-group">
                <label><?php echo lang('app_general_show_payments') ?></label>
                <select name="KevaTotal" id="KevaTotal" class="form-control">
                <option value="0" <?php if (@$Supplier->KevaTotal == '0') {echo "selected";} ?>><?php echo lang('yes') ?></option>
                <option value="1" <?php if (@$Supplier->KevaTotal == '1') {echo "selected";} ?>><?php echo lang('no') ?></option>
                </select>
                </div>   
                 <?php } ?>
                 
                 <div class="form-group">
                <label><?php echo lang('app_general_show_terms') ?></label>
                <select name="ShowTakanon" id="ShowTakanon" class="form-control">
                <option value="0" <?php if (@$Supplier->ShowTakanon == '0') {echo "selected";} ?>><?php echo lang('yes') ?></option>
                <option value="1" <?php if (@$Supplier->ShowTakanon == '1') {echo "selected";} ?>><?php echo lang('no') ?></option>
                </select>
                </div>   
                 
                 <div class="form-group">
                <label><?php echo lang('app_general_show_health') ?></label>
                <select name="ShowHealth" id="ShowHealth" class="form-control">
                <option value="0" <?php if (@$Supplier->ShowHealth == '0') {echo "selected";} ?>><?php echo lang('yes') ?></option>
                <option value="1" <?php if (@$Supplier->ShowHealth == '1') {echo "selected";} ?>><?php echo lang('no') ?></option>
                </select>
                </div>    
                 
                
            <hr>	
<?php if (Auth::userCan('12')): ?>                 
<div class="form-group">
<button type="submit" class="btn btn-success btn-lg"><?php echo lang('update') ?></button>	
</div> 
 <?php endif ?>                  
                
            </div>
            </form>    
                
                </div></div>


<div class="tab-pane fade text-start" role="tabpanel" id="app-content">
  <div class="card spacebottom">
		<div class="card-header text-start"><strong><?php echo lang('edit_content_title') ?></strong>
                      
    </div>    
 		<div class="card-body">  
          <form action="AppContent"  class="ajax-form clearfix"  autocomplete="off">
            <input type="hidden" name="CompanyNum" value="<?php echo $CompanyNum; ?>">	
                  
                  <div class="form-group">
                    <label><?php echo lang('logo_for_app') ?></label>
                      
                      
                  <div class="avatar-container">
                  <?php if (Auth::userCan('12')): ?>
                  <button type="button" class="btn btn-light edit-avatar" data-ip-modal="#headerModal" title="<?php echo lang('edit_logo') ?>"><i class="fas fa-pencil-alt"></i></button>
                  <?php endif; ?>
                  <?php if (!empty($Supplier->logoImg)) { ?>    
                    <img src="files/logo/<?php echo $Supplier->logoImg; ?>" id="avatar">
                  <?php } else { ?>
                    <img src="/office/files/logo/smallDefault.png" id="avatar">    
                  <?php } ?>    
                  </div>    
                  </div>
                  <hr>
                  
                  <div class="form-group">
                    <label><?php echo lang('background_picture') ?></label>
                      
                      
                  <div class="avatar-container">
                  <?php if (Auth::userCan('12')): ?>
                  <button type="button" class="btn btn-light edit-avatar" data-ip-modal="#profileModal" title="<?php echo lang('edit_background') ?>"><i class="fas fa-pencil-alt"></i></button>
                  <?php endif; ?>
                  <?php if (!empty($Supplier->studioCoverImg)) { ?>    
                    <img src="files/cover/<?php echo $Supplier->studioCoverImg; ?>" id="profile-avatar">
                  <?php } else { ?>
                    <img src="/office/files/cover/default.png" id="profile-avatar">    
                  <?php } ?>    
                  </div>    
                  </div>
                  <hr>
                
                <div class="form-group">
                <label><?php echo lang('system_notice') ?></label>
                <textarea class="form-control summernote" name="Content" maxlength="250"><?php echo $Supplier->studioMsg; ?></textarea>
                </div>

                <hr>

                <div class="form-group">
                <label><?php echo lang('text_color') ?></label>
                <div id="SetDocBackPreview" style="background-color: <?php echo $Supplier->msgColor; ?>;width:50px;height:10px;display:inline-block;"></div>
                <select class="form-control" name="DocsBackgroundColor" id="DocsBackgroundColor" onchange="dsfsd()">
                  <option value="#000000" <?php if ($Supplier->msgColor == '#000000') {echo "selected";} ?>><?php echo lang('black_color') ?></option>
                	<option value="#e10025" <?php if ($Supplier->msgColor == '#e10025') {echo "selected";} ?>><?php echo lang('red_color') ?></option>
                	<option value="#f19218" <?php if ($Supplier->msgColor == '#f19218') {echo "selected";} ?>><?php echo lang('orange_color') ?></option>
                	<option value="#48AD42" <?php if ($Supplier->msgColor == '#48AD42') {echo "selected";} ?>><?php echo lang('green_color') ?></option>
                	<option value="#2b71b9" <?php if ($Supplier->msgColor == '#2b71b9') {echo "selected";} ?>><?php echo lang('blue_color') ?></option>
                	<option value="#b79bf7" <?php if ($Supplier->msgColor == '#b79bf7') {echo "selected";} ?>><?php echo lang('purple_color') ?></option>
                </select>
                </div>
				
                <hr>

                <?php if (Auth::userCan('12')): ?>                 
                <div class="form-group">
                  <button type="submit" class="btn btn-success btn-lg"><?php echo lang('update') ?></button>	
                </div> 
                <?php endif; ?> 
                </div>  
          </form>    
              
  </div>
</div>  
    
    
  <div class="tab-pane fade text-start" role="tabpanel" id="generalsettings">
  <div class="card spacebottom">
			<div class="card-header text-start d-flex justify-content-between"><strong><?php echo lang('settings_calss_view') ?></strong></div>
 			<div class="card-body">
 			    <form action="ViewClass"  class="ajax-form clearfix"  autocomplete="off">

                <div class="form-group">
                <label><?php echo lang('open_class_register') ?></label>
                <select name="ViewClass" id="ViewClass" class="form-control">
                <option value="1" <?php if (@$Supplier->ViewClass == '1') {echo "selected";} ?>><?php echo lang('register_time_7_days') ?></option>
                <option value="4" <?php if (@$Supplier->ViewClass == '4') {echo "selected";} ?>><?php echo lang('register_choose_days') ?></option>
                <option value="2" <?php if (@$Supplier->ViewClass == '2') {echo "selected";} ?>><?php echo lang('register_week_day_hour') ?></option>
                <option value="3" <?php if (@$Supplier->ViewClass == '3') {echo "selected";} ?>><?php echo lang('register_choose_dates') ?></option>    
              </select>
                </div>
                
               <div class="alertb alert-info" id="ViewClass1" style="display: <?php if (@$Supplier->ViewClass == '1') {echo "block";} else { echo 'none'; } ?>;">
               <?php echo lang('app_display_notice') ?>
               </div>  
                
               <?php 
                
                if (@$Supplier->SelectDay == 'Sunday') {
                $SelectDay = lang('sunday');    
                }  
                else if (@$Supplier->SelectDay == 'Monday') {
                $SelectDay = lang('monday');    
                }  
                else if (@$Supplier->SelectDay == 'Tuesday') {
                $SelectDay = lang('tuesday');    
                }  
                else if (@$Supplier->SelectDay == 'Wednesday') {
                $SelectDay = lang('wednesday');    
                }  
                else if (@$Supplier->SelectDay == 'Thursday') {
                $SelectDay = lang('thursday');    
                }  
                else if (@$Supplier->SelectDay == 'Friday') {
                $SelectDay = lang('friday');    
                }  
                else if (@$Supplier->SelectDay == 'Saturday') {
                $SelectDay = lang('saturday');    
                }  
                    
                    
                    
                ?>
                    
                    
                    
               <div id="ViewClass2" style="display: <?php if (@$Supplier->ViewClass == '2') {echo "block";} else { echo 'none'; } ?>;">
                   
               <div class="alertb alert-info">       
               <?php echo lang('app_settings_once') ?> <u><?php echo @$SelectDay ?></u> <?php echo lang('in_hour') ?> <u><?php echo with(new DateTime(@$Supplier->SelectTimes))->format('H:i'); ?></u> <?php echo lang('app_settings_register') ?> 
               </div> 
                    
               <div class="form-group">
                <label>בחר יום</label> 
                <select name="SelectDay" id="SelectDay" class="form-control">   
                <option value="Sunday" <?php if (@$Supplier->SelectDay == 'Sunday') {echo "selected";} ?>><?php echo lang('sunday') ?></option>
                <option value="Monday" <?php if (@$Supplier->SelectDay == 'Monday') {echo "selected";} ?>><?php echo lang('monday') ?></option>
                <option value="Tuesday" <?php if (@$Supplier->SelectDay == 'Tuesday') {echo "selected";} ?>><?php echo lang('tuesday') ?></option>    
                <option value="Wednesday" <?php if (@$Supplier->SelectDay == 'Wednesday') {echo "selected";} ?>><?php echo lang('wednesday') ?></option>   
                <option value="Thursday" <?php if (@$Supplier->SelectDay == 'Thursday') {echo "selected";} ?>><?php echo lang('thursday') ?></option>   
                <option value="Friday" <?php if (@$Supplier->SelectDay == 'Friday') {echo "selected";} ?>><?php echo lang('friday') ?></option>
                <option value="Saturday" <?php if (@$Supplier->SelectDay == 'Saturday') {echo "selected";} ?>><?php echo lang('saturday') ?></option>   
               </select>
                </div>       
                    
                <div class="form-group">
                <label>הגדר שעה</label>
                <input type="time" class="form-control" name="SelectTimes" value="<?php echo @$Supplier->SelectTimes ?>">
                </div>       
                    
                    
                
                </div>  
                
                    
               <div id="ViewClass4" style="display: <?php if (@$Supplier->ViewClass == '4') {echo "block";} else { echo 'none'; } ?>;">     
               <div class="alertb alert-info">
               <?php echo lang('app_settings_select_days') ?>
               </div> 
                   
                   
                <div class="form-group">
                <label><?php echo lang('set_days_for_display') ?></label>
                <input type="numbers" min="1" max="30" class="form-control" name="ViewClassDayNum" value="<?php echo @$Supplier->ViewClassDayNum ?>" onkeypress='validate(event)'>
                </div>   
                   
                   
               </div>    
                   
               <div id="ViewClass5" style="display: <?php if (@$Supplier->ViewClass == '5') {echo "block";} else { echo 'none'; } ?>;">     
               <div class="alertb alert-info">
               <?php echo lang('select_hour_display') ?>
               </div>
   
                <div class="form-group">
                <label><?php echo lang('sunday_set_hour') ?></label>
                <input type="time" class="form-control" name="Sunday" value="<?php echo @$Supplier->Sunday ?>">
                </div>  
                   
                <div class="form-group">
                <label><?php echo lang('monday_set_hour') ?></label>
                <input type="time" class="form-control" name="Monday" value="<?php echo @$Supplier->Monday ?>">
                </div>  
                   
                <div class="form-group">
                <label><?php echo lang('tuesday_set_hour') ?></label>
                <input type="time" class="form-control" name="Tuesday" value="<?php echo @$Supplier->Tuesday ?>">
                </div>  
                
                   
                <div class="form-group">
                <label><?php echo lang('wednesday_set_hour') ?></label>
                <input type="time" class="form-control" name="Wednesday" value="<?php echo @$Supplier->Wednesday ?>">
                </div>  
                   
                <div class="form-group">
                <label><?php echo lang('thursday_set_hour') ?></label>
                <input type="time" class="form-control" name="Thursday" value="<?php echo @$Supplier->Thursday ?>">
                </div>  
                   
                <div class="form-group">
                <label><?php echo lang('friday_set_hour') ?></label>
                <input type="time" class="form-control" name="Friday" value="<?php echo @$Supplier->Friday ?>">
                </div>  
                   
                <div class="form-group">
                <label><?php echo lang('saturday_set_hour') ?></label>
                <input type="time" class="form-control" name="Saturday" value="<?php echo @$Supplier->Saturday ?>">
                </div>    
               </div>        
                    
                    
                    
                    
               <div id="ViewClass3" style="display: <?php if (@$Supplier->ViewClass == '3') {echo "block";} else { echo 'none'; } ?>;">     
               <div class="alertb alert-info">
               <?php echo lang('app_register_notice') ?>
               </div>   
  
                 <div class="form-group">
                <label><?php echo lang('date_range') ?></label>
                <select name="ViewClassDates" class="form-control">
                <option value="" <?php  if (@$Supplier->ViewClassDates == '0') {echo "selected";} ?>><?php echo lang('choose_date_range') ?></option>
                <option value="1" <?php if (@$Supplier->ViewClassDates == '1') {echo "selected";} ?>><?php echo lang('register_month') ?></option>
                <option value="2" <?php if (@$Supplier->ViewClassDates == '2') {echo "selected";} ?>><?php echo lang('register_2_months') ?></option>
                <option value="3" <?php if (@$Supplier->ViewClassDates == '3') {echo "selected";} ?>><?php echo lang('register_3_months') ?></option>
                <option value="4" <?php if (@$Supplier->ViewClassDates == '4') {echo "selected";} ?>><?php echo lang('register_4_months') ?></option>
                <option value="5" <?php if (@$Supplier->ViewClassDates == '5') {echo "selected";} ?>><?php echo lang('register_5_months') ?></option>
                <option value="6" <?php if (@$Supplier->ViewClassDates == '6') {echo "selected";} ?>><?php echo lang('register_6_months') ?></option>    
              </select>
                </div>    
                    </div>
				<hr>	
				
                <?php if (Auth::userCan('12')): ?> 
				<div class="form-group">
				<button type="submit" class="btn btn-success btn-lg"><?php echo lang('update') ?></button>	
				</div>
                <?php endif ?>     
				</div>
				</form>
				</div></div>
  <div class="tab-pane fade text-start" role="tabpanel" id="appcancel">
            <div class="card spacebottom">
			<div class="card-header text-start d-flex justify-content-between"><strong><?php echo lang('cacellation') ?></strong></div>
 			<div class="card-body">  
 			    <form action="AppCancel"  class="ajax-form clearfix"  autocomplete="off">
					
 			    <div class="form-group">
                <label><?php echo lang('app_settings_change_class') ?></label>
                <select name="DifrentTime" id="DifrentTime" class="form-control">
                <option value="1" <?php if (@$Supplier->DifrentTime == '1') {echo "selected";} ?>><?php echo lang('yes') ?></option>
                <option value="2" <?php if (@$Supplier->DifrentTime == '2') {echo "selected";} ?>><?php echo lang('no') ?></option>
                </select>
                </div>     
                
                <div id="DifrentTime1" style="display: <?php if (@$Supplier->DifrentTime == '1') {echo "block";} else { echo 'none'; } ?>;">  
                    
               <div class="alertb alert-warning">
               <?php echo lang('app_settings_change_class_notice') ?>
               </div>        
                 
                    
                <div class="form-group">
                <label><?php echo lang('choose_option') ?></label>
                <select name="TypeDifrentTime" id="TypeDifrentTime" class="form-control">
                <option value="0" <?php if (@$Supplier->TypeDifrentTime == '0') {echo "selected";} ?>><?php echo lang('app_in_minutes') ?></option>
                <option value="1" <?php if (@$Supplier->TypeDifrentTime == '1') {echo "selected";} ?>><?php echo lang('lesson_day_until_one_hour') ?></option>
                <option value="2" <?php if (@$Supplier->TypeDifrentTime == '2') {echo "selected";} ?>><?php echo lang('day_before_until_one_hour') ?></option>    
                </select>
                </div>       
                    
                    
                <div class="form-group" id="TypeDifrentTimeDiv1" style="display: <?php if (@$Supplier->TypeDifrentTime == '0') {echo "block";} else { echo 'none'; } ?>;">
                <label><?php echo lang('app_settings_time_before_change') ?></label>
                <input type="text" class="form-control" name="DifrentTimeMin" value="<?php echo @$Supplier->DifrentTimeMin ?>" onkeypress='validate(event)'>
                </div>  
                    
                <div class="form-group" id="TypeDifrentTimeDiv2" style="display: <?php if (@$Supplier->TypeDifrentTime == '1' || @$Supplier->TypeDifrentTime == '2') {echo "block";} else { echo 'none'; } ?>;">
                <label><?php echo lang('set_time_to_change') ?></label>
                <input type="time" class="form-control" name="DifrentTimeOption" id="DifrentTimeOption" value="<?php echo @$Supplier->DifrentTimeOption ?>">
                </div>      
                    
                
                </div>
                    
                <hr>
                    
                <h5><?php echo lang('app_penatlie_title') ?></h5>    
                    
                <div class="form-group">
                <label><?php echo lang('choose_penaltie_type') ?></label>
                <select name="MemberShipLimitType" id="MemberShipLimitType" class="form-control">
                <option value="2" <?php if (@$Supplier->MemberShipLimitType == '2') {echo "selected";} ?>><?php echo lang('penaltie_none') ?></option>
                <option value="0" <?php if (@$Supplier->MemberShipLimitType == '0') {echo "selected";} ?>><?php echo lang('penaltie_app_ban') ?></option>
                <option value="1" <?php if (@$Supplier->MemberShipLimitType == '1') {echo "selected";} ?>><?php echo lang('penatlie_duration') ?></option>    
                </select>
                </div>     
                    
                    
                <div id="MemberShipLimit" style="display: <?php if (@$Supplier->MemberShipLimitType != '2') {echo "block";} else { echo 'none'; } ?>;">   
                    
                <div id="MemberShipLimits" style="display: <?php if (@$Supplier->MemberShipLimitType != '2' && @$Supplier->MemberShipLimitType == '0') {echo "block";} else { echo 'none'; } ?>;">  
                   
                    
                <div class="form-group">
                <label><?php echo lang('penatlie_ban_days') ?></label>
                <select name="MemberShipLimitUnBlock" id="MemberShipLimitUnBlock" class="form-control">
                <option value="1" <?php if (@$Supplier->MemberShipLimitUnBlock == '1') {echo "selected";} ?>><?php echo lang('no') ?></option>
                <option value="0" <?php if (@$Supplier->MemberShipLimitUnBlock == '0') {echo "selected";} ?>><?php echo lang('yes') ?></option>
                </select>
                </div>        
                    
                <div id="MemberShipLimitUnBlockDiv" style="display: <?php if (@$Supplier->MemberShipLimitUnBlock == '0' && @$Supplier->MemberShipLimitType == '0') {echo "block";} else { echo 'none'; } ?>;">    
                   
                <div class="form-group">
                <label><?php echo lang('ban_release_days') ?></label>
                <input type="text" class="form-control" name="MemberShipLimitUnBlockDays" value="<?php echo @$Supplier->MemberShipLimitUnBlockDays ?>" onkeypress='validate(event)'>
                </div>      
                    
                </div>   
                    
                <hr>
                    
                </div>    
                
                    
                <div id="MemberShipLimitDaysDiv" style="display: <?php if (@$Supplier->MemberShipLimitType != '2' && @$Supplier->MemberShipLimitType == '1') {echo "block";} else { echo 'none'; } ?>;">  
                   
                <div class="form-group">
                <label><?php echo lang('penaltie_duration_days') ?></label>
                <input type="text" class="form-control" name="MemberShipLimitDays" value="<?php echo @$Supplier->MemberShipLimitDays ?>" onkeypress='validate(event)'>
                </div>      
 
                <hr>   
                    
                </div>      
                <label><?php echo lang('choose_status') ?></label>    
                  <div class="row">
                          
               <div class="col-md-6 col-sm-12">

               
                   
   <div class="input-group">
  <div class="input-group-icon" style="padding:5px;text-align: center; vertical-align: middle;"><center><input type="checkbox" type="checkbox" id="MemberShipLimitLateCancel" name="MemberShipLimitLateCancel" value="1" style="width: 20px; height: 20px;text-align: center;margin:0;padding:0; text-align: center; vertical-align: middle;margin-top: 3px;margin-left: 3px;" <?php if (@$Supplier->MemberShipLimitLateCancel == '1') {echo "checked";} else {} ?> ></center></div>
  <div class="input-group-area"><label for="MemberShipLimitLateCancel" class="text-start" style="text-align:start;padding: 0;margin:0;padding:5px;width: 100%;"><?php echo lang('late_cancellation') ?></label></div>
</div>
	</div>
               
                
       <div class="col-md-6 col-sm-12">         
   <div class="input-group">
  <div class="input-group-icon" style="padding:5px;text-align: center; vertical-align: middle;"><center><input type="checkbox" type="checkbox" id="MemberShipLimitNoneShow" name="MemberShipLimitNoneShow" value="1" style="width: 20px; height: 20px;text-align: center;margin:0;padding:0; text-align: center; vertical-align: middle;margin-top: 3px;margin-left: 3px;" <?php if (@$Supplier->MemberShipLimitNoneShow == '1') {echo "checked";} else {} ?> ></center></div>
  <div class="input-group-area"><label for="MemberShipLimitNoneShow" class="text-start" style="text-align:start;padding: 0;margin:0;padding:5px;width: 100%;"><?php echo lang('status_late_cancel') ?></label></div>
</div>
													</div></div>   
                    
                    <br>
                <div class="form-group">
                <label><?php echo lang('late_cancle_times') ?></label>
                <input type="text" class="form-control" name="MemberShipLimit" value="<?php echo @$Supplier->MemberShipLimit ?>" onkeypress='validate(event)'>
                </div> 
                    
                    
                <div class="form-group">
                <label><?php echo lang('date_range_count') ?></label>
                <input type="number" min="0" class="form-control" name="DaysMemberShipLimit" value="<?php echo @$Supplier->DaysMemberShipLimit ?>" onkeypress='validate(event)' required>
                </div>     
                    
                </div>   
                    
               <div class="alertb alert-warning">
               <?php echo lang('counts_appsettings') ?> <u><?php echo @$Supplier->DaysMemberShipLimit ?></u> <?php echo lang('day_appsettings') ?> <br>
               <?php echo lang('app_cancle_notice_two') ?><br>
               <?php echo lang('app_cancel_notice_3') ?><br>
               </div>        
                    
               <input type="hidden" name="MemberShipLimitMoney" value="<?php echo @$Supplier->MemberShipLimitMoney ?>">
                    
                    
                    
                    
                    
<hr>
<?php if (Auth::userCan('12')): ?>                     
<div class="form-group">
<button type="submit" class="btn btn-success btn-lg"><?php echo lang('update') ?></button>	
</div>
<?php endif ?>                     
		</div>
				</form>
		</div></div>
	  
	  
	  <div class="tab-pane fade text-start" role="tabpanel" id="watinglist">
            <div class="card spacebottom">
			<div class="card-header text-start d-flex justify-content-between"><strong><?php echo lang('settings_app_waitlist') ?></strong></div>
 			<div class="card-body"> 

 			    <form action="AppWatingList"  class="ajax-form clearfix"  autocomplete="off">

               <div class="form-group">
                <label><?php echo lang('app_auto_waitlist') ?></label>
                <select name="Watinglist" id="WatinglistOption" class="form-control">
                <option value="1" <?php if (@$Supplier->Watinglist == '1') {echo "selected";} ?>><?php echo lang('yes') ?></option>
                <option value="2" <?php if (@$Supplier->Watinglist == '2') {echo "selected";} ?>><?php echo lang('no') ?></option>
               </select>
                </div>
                
                    
               <div id="Watinglist1" style="display: <?php if (@$Supplier->Watinglist == '1') {echo "block";} else { echo 'none'; } ?>;">         
                <div class="form-group">
                <label><?php echo lang('app_settings_waitlist_notice') ?></label>
                <input type="text" class="form-control" name="WatinglistEndMin" value="<?php echo @$Supplier->WatinglistEndMin ?>" onkeypress='validate(event)'>
                </div> 
                </div>        
                    
                 <div id="Watinglist2" style="display: <?php if (@$Supplier->Watinglist == '2') {echo "block";} else { echo 'none'; } ?>;">         

                </div>

                <div class="form-group">
                <label><?php echo lang('app_waitlist_time') ?></label>
                <input type="text" class="form-control" name="WatinglistMin" value="<?php echo @$Supplier->WatinglistMin ?>" onkeypress='validate(event)'>
                </div>  
                    
                <hr>

                <?php if($CompanyNum && Auth::user()->role_id == 1) { ?>
                <div class="form-group">
                <label><?php echo lang('app_waitlist_and_class') ?></label>
                <select name="WatinglistOrder" id="WatinglistOrder" class="form-control">
                <option value="1" <?php if (@$Supplier->WatinglistOrder == '1') {echo "selected";} ?>><?php echo lang('yes') ?></option>
                <option value="2" <?php if (@$Supplier->WatinglistOrder == '2') {echo "selected";} ?>><?php echo lang('no') ?></option>
                </select>
                </div>

                 
                <div id="WatinglistOrder1" style="display: <?php @$Supplier->WatinglistOrder == '1' ? 'block' : 'none'  ?>;">
                <div class="form-group">
                <label><?php echo lang('app_time_to_change') ?></label>
                <input type="text" class="form-control" name="WatinglistOrderTime" value="<?php echo @$Supplier->WatinglistOrderTime ?>" onkeypress='validate(event)'>
                </div>

               <div class="alertb alert-warning" style="display: <?php echo (@$Supplier->WatinglistOrder == '1') ? "block" : "none" ?>">
               <?php echo lang('app_waitlist_notice_one') ?><br>
               <?php echo lang('app_waitlist_notice_two') ?>
               </div> 
               </div>

               <hr>

                <?php } ?>


<?php if (Auth::userCan('12')): ?>                     
<div class="form-group">
<button type="submit" class="btn btn-success btn-lg"><?php echo lang('update') ?></button>	
</div>
<?php endif ?>                     
		</div>
				</form>
		</div></div>
	  
	  
	  
	  
	  
	  
	  
<div class="tab-pane fade text-start" role="tabpanel" id="appnotification">
            <div class="card spacebottom">
			<div class="card-header text-start d-flex justify-content-between"><strong><?php echo lang('notifications') ?></strong></div>
 			<div class="card-body"> 
			    
			<form action="AppNotification"  class="ajax-form clearfix"  autocomplete="off">
				
                <div class="form-group"> 
                <label><?php echo lang('app_notifications_sms') ?> </label>
                <select name="SendSMS" id="SendSMS" class="form-control">
                <option value="1" <?php if (@$Supplier->SendSMS == '1') {echo "selected";} ?>><?php echo lang('yes') ?></option>
                <option value="2" <?php if (@$Supplier->SendSMS == '2') {echo "selected";} ?>><?php echo lang('no') ?></option>
                </select>
                </div>      
				
			   <div id="SendSMS1" class="alertb alert-warning" style="display: <?php if (@$Supplier->SendSMS == '1') {echo "block";} else { echo 'none'; } ?>;" >
				   <span><?php echo lang('sms_appsettings') ?></span> <span > ₪<?php echo $SettingsInfo->SMSPrice ?></span> <?php echo lang('to_user_manage') ?> <?php echo $SettingsInfo->SMSLimit; ?> <?php echo lang('characters_appsettings') ?>.
               </div>  
				
				<div class="form-group"> 
                <label><?php echo lang('app_notification_week') ?> </label>
                <select name="ClassWeek" id="ClassWeek" class="form-control">
                <option value="1" <?php if (@$Supplier->ClassWeek == '1') {echo "selected";} ?>><?php echo lang('yes') ?></option>
                <option value="2" <?php if (@$Supplier->ClassWeek == '2') {echo "selected";} ?>><?php echo lang('no') ?></option>
                </select>
                </div>  

                <div id="ClassWeek1" style="display: <?php if (@$Supplier->ClassWeek == '1') {echo "block";} else { echo 'none'; } ?>;">         
                <div class="form-group">
                <label><?php echo lang('app_notification_week_days') ?></label>
                <input type="text" class="form-control" name="ClassWeekMonth" value="<?php echo @$Supplier->ClassWeekMonth ?>" onkeypress='validate(event)'>
                </div>  
                </div>
                
                
                
                <div class="form-group">
                <label><?php echo lang('app_notification_mail') ?></label>
                <select name="SendNotification" class="form-control">
                <option value="0" <?php if (@$Supplier->SendNotification == '0') {echo "selected";} ?>><?php echo lang('no') ?></option>
                <option value="1" <?php if (@$Supplier->SendNotification == '1') {echo "selected";} ?>><?php echo lang('yes') ?></option>
                </select>
                </div>    
                
                <div class="alertb alert-warning" >
                <?php echo lang('app_notification_notice') ?>
                </div>  
				
				
<hr>	
<?php if (Auth::userCan('12')): ?>                 
<div class="form-group">
<button type="submit" class="btn btn-success btn-lg"><?php echo lang('update') ?></button>	
</div>
<?php endif ?>                 
		</div>
				</form>
							<script>
function dsfsd() {
    var x = document.getElementById("DocsBackgroundColor").value;
    document.getElementById("SetDocBackPreview").style.backgroundColor = x;
}
</script>

		</div></div>	  
	  

	         
<div class="tab-pane fade text-start" role="tabpanel" id="appHealth">
<div class="card spacebottom">
<div class="card-header text-start"><strong><?php echo lang('health_declaration') ?></strong></div>    
<div class="card-body">  
 
      <div class="row text-start px-15" style="padding-bottom: 15px;" >
      <a href="AddHealth.php"><span class="btn btn-primary text-white"><?php echo lang('create_new_form') ?></span></a>            
      </div>     
    
    
<table class="table">
    
<thead>
    <th><?php echo lang('health_version_number') ?></th>
    <th><?php echo lang('task_title') ?></th>
    <th><?php echo lang('version_date') ?></th>
    <th><?php echo lang('clients_signed') ?></th>
    <th><?php echo lang('app_resign') ?></th>
    <th><?php echo lang('actions') ?></th>
    
</thead>    

<tbody>
<?php 
$i = '1';    
$HealthInfos = DB::table('healthforms')->where('CompanyNum',  $CompanyNum)->orderBy('id','DESC')->get();  
foreach ($HealthInfos as $HealthInfo) {   
 
$HealthCounts = DB::table('healthforms_answers')->where('CompanyNum',  $CompanyNum)->where('FormId',  $HealthInfo->id)->count();    
    
    
if ($i=='1'){
$HealthClass = 'table-success';    
}  
else {
$HealthClass = '';     
}  
    
if ($HealthInfo->forceRenew=='1'){
$HealthRenew = 'כן';   
}    
else {
$HealthRenew = 'לא';     
} 
    
?>    
<tr class="<?php echo $HealthClass; ?>">
<td><?php echo $HealthInfo->GroupNumber; ?></td>
<td><?php echo $HealthInfo->name; ?></td>
<td><?php echo with(new DateTime($HealthInfo->created))->format('d/m/Y H:i'); ?></td>
<td class="text-primary"><a href="HealthClientList.php?u=<?php echo $HealthInfo->id; ?>" class="text-primary"><span class="text-primary"><?php echo $HealthCounts; ?></span></a></td>
<td><?php echo $HealthRenew; ?></td>
<td><a href="AddHealth.php?formId=<?php echo $HealthInfo->id; ?>"><span class="text-primary"><?php echo lang('edit_or_update_version') ?></span></a></td>    
</tr>
<?php ++ $i; } ?>    
</tbody>    
    
</table>    
    
    
    
</div></div></div>   
   
    
<div class="tab-pane fade text-start" role="tabpanel" id="appTakanon">
<div class="card spacebottom">
<div class="card-header text-start"><strong><?php echo lang('terms') ?></strong></div>    
<div class="card-body">  
<form action="AppTakanon" class="ajax-form clearfix"  autocomplete="off">
				
  				<div class="form-group">
                <textarea class="form-control summernote" rows="5" name="Content"><?php echo @$Supplier->Takanon ?></textarea>
                </div>

	
				<div class="form-group"> 
                <label><?php echo lang('app_terms_resign') ?></label>
                <select name="SignAgian" id="SignAgian" class="form-control">
                <option value="1"><?php echo lang('yes') ?></option>
                <option value="2" selected><?php echo lang('no') ?></option>
                </select>
                </div>  	
	
	
<hr>	
	
	
	
	
<?php if (Auth::userCan('12')): ?>     
<div class="form-group">
<button type="submit" class="btn btn-success btn-lg"><?php echo lang('update') ?></button>	
</div>
<?php endif ?>    
</div>
</form>
</div></div>   
    
    
</div>                               
 
</div>  
</div>
     
	</div> 

	<div class="ip-modal" id="headerModal">
		<div class="ip-modal-dialog">
			<div class="ip-modal-content text-start">
				<div class="ip-modal-header  d-flex justify-content-between">
					<h4 class="ip-modal-title"><?php echo lang('logo_for_app') ?></h4>
					<a class="ip-close" title="Close" >&times;</a>
				</div>
				<div class="ip-modal-body" >
                    
                    <div class="alertb alert-info"><?php echo lang('logo_guide_1') ?><br><?php echo lang('logo_guide_2') ?></div> 
                    
					<div class="btn btn-primary ip-upload"><?php echo lang('upload_image') ?> <input type="file" name="file" class="ip-file"></div>
					<!-- <button class="btn btn-primary ip-webcam">Webcam</button> -->
					<button type="button" class="btn btn-info ip-edit"><?php echo lang('edit_logo') ?></button>
					<button type="button" class="btn btn-danger ip-delete"><?php echo lang('delete_logo') ?></button>
					
					<div class="alert ip-alert"></div>
					<div class="ip-info"><?php echo lang('crop_image_business_settings') ?></div>
					<div class="ip-preview"></div>
					<div class="ip-rotate">
						<button type="button" class="btn btn-default ip-rotate-ccw" title="Rotate counter-clockwise"><i class="icon-ccw"></i></button>
						<button type="button" class="btn btn-default ip-rotate-cw" title="Rotate clockwise"><i class="icon-cw"></i></button>
					</div>
					<div class="ip-progress">
						<div class="text"><?php echo lang('uploading_appsettings') ?></div>
						<div class="progress progress-striped active"><div class="progress-bar"></div></div>
					</div>
				</div>
				<div class="ip-modal-footer d-flex justify-content-between">
					<div class="ip-actions">
						<button class="btn btn-success ip-save"><?php echo lang('save_image_business_settings') ?></button>
						<button class="btn btn-primary ip-capture"><?php echo lang('capture_business_settings') ?></button>
						<button class="btn btn-default ip-cancel"><?php echo lang('action_cacnel') ?></button>
					</div>
					<button class="btn btn-default ip-close"><?php echo lang('close') ?></button>
				</div>
			</div>
		</div>
	</div>


  	<div class="ip-modal" id="profileModal">
		<div class="ip-modal-dialog">
			<div class="ip-modal-content text-start">
				<div class="ip-modal-header d-flex justify-content-between" >
					<h4 class="ip-modal-title"><?php echo lang('background_image_app') ?></h4>
					<a class="ip-close" title="Close"  >&times;</a>
				</div>
				<div class="ip-modal-body" >
                    
                    <div class="alertb alert-info"><?php echo lang('image_guide_1') ?><br><?php echo lang('image_guide_2') ?></div>
                    
					<div class="btn btn-primary ip-upload"><?php echo lang('upload_image') ?> <input type="file" name="file" class="ip-file"></div>
					<!-- <button class="btn btn-primary ip-webcam">Webcam</button> -->
					<button type="button" class="btn btn-info ip-edit"><?php echo lang('edit_image') ?></button>
					<button type="button" class="btn btn-danger ip-delete"><?php echo lang('remove_image') ?></button>
					
					<div class="alert ip-alert"></div>
					<div class="ip-info"><?php echo lang('crop_image_business_settings') ?></div>
					<div class="ip-preview"></div>
					<div class="ip-rotate">
						<button type="button" class="btn btn-default ip-rotate-ccw" title="Rotate counter-clockwise"><i class="icon-ccw"></i></button>
						<button type="button" class="btn btn-default ip-rotate-cw" title="Rotate clockwise"><i class="icon-cw"></i></button>
					</div>
					<div class="ip-progress">
						<div class="text"><?php echo lang('uploading_appsettings') ?></div>
						<div class="progress progress-striped active"><div class="progress-bar"></div></div>
					</div>
				</div>
				<div class="ip-modal-footer d-flex justify-content-between">
					<div class="ip-actions">
						<button class="btn btn-success ip-save"><?php echo lang('save_image_business_settings') ?></button>
						<button class="btn btn-primary ip-capture"><?php echo lang('capture_business_settings') ?></button>
						<button class="btn btn-default ip-cancel"><?php echo lang('action_cacnel') ?></button>
					</div>
					<button class="btn btn-default ip-close"><?php echo lang('close') ?></button>
				</div>
			</div>
		</div>
	</div>  
	 
	  
	   
<script>

$(function() {
			var time = function(){return'?'+new Date().getTime()};
    
$('#headerModal').imgPicker({
				url: 'Server/upload_logo.php',
				aspectRatio: 20/20,
                setSelect: [160, 160, 0, 0],
				deleteComplete: function() {
				$('#avatar').attr('src', '/office/files/logo/smallDefault.png');
				this.modal('hide');
				},
                loadComplete: function(image) {
                        // Set #avatar image src
                        <?php if ($Supplier->logoImg!=''){ ?>
                        $('#avatar').attr('src', 'files/logo/<?php echo $Supplier->logoImg; ?>');
                        <?php } else { ?>
                        $('#avatar').attr('src', '/office/files/logo/smallDefault.png');
                        <?php } ?>
                        // Set the image for re-crop
                        this.setImage(image);
                    },
				cropSuccess: function(image) {
				$('#avatar').attr('src', image.versions.logo.url + time());
				this.modal('hide');
				}
			});
    
});

$(function() {
			var time = function(){return'?'+new Date().getTime()};
    
$('#profileModal').imgPicker({
				url: 'Server/upload_cover.php',
				aspectRatio: 33/20,
                setSelect: [400, 260, 0, 0],
				deleteComplete: function() {
				$('#profile-avatar').attr('src', '/office/files/cover/default.png');
				this.modal('hide');
				},
                loadComplete: function(image) {
                        // Set #avatar image src
                        <?php if ($Supplier->studioCoverImg != ''){ ?>
                        $('#profile-avatar').attr('src', 'files/cover/<?php echo $Supplier->studioCoverImg; ?>');
                        <?php } else { ?>
                        $('#profile-avatar').attr('src', '/office/files/cover/default.png');
                        <?php } ?>
                        // Set the image for re-crop
                        this.setImage(image);
                    },
				cropSuccess: function(image) {
				$('#profile-avatar').attr('src', image.versions.cover.url + time());
				this.modal('hide');
				}
			});
    
});	

$(document).ready(function() {
    
$("#ViewClass").trigger('change');
$("#DifrentTime").trigger('change'); 
$("#WatinglistOption").trigger('change');
$("#WatinglistOrder").trigger('change');    
 
    
  $('.summernotes').summernote({
        placeholder: '<?php echo lang('type_content') ?>',
        tabsize: 2,
        height: 100,
	   toolbar: [
    // [groupName, [list of button]]
    ['style', ['bold', 'italic', 'underline', 'clear']],
    ['font', ['strikethrough']],
    ['para', ['ul', 'ol']]
  ]
      });
 
 
 $('.summernote').summernote({
        placeholder: '<?php echo lang('type_content') ?>',
        tabsize: 2,
        height: 400,
	   toolbar: [
    // [groupName, [list of button]]
    ['style', ['bold', 'italic', 'underline', 'clear']],
    ['font', ['strikethrough']],
    ['para', ['ul', 'ol']]
  ]
      });
});	
	
	
$('[data-toggle="tabajax"]').click(function(e) {
    var $this = $(this),
        loadurl = $this.attr('href'),
        targ = $this.attr('data-target');

    $.get(loadurl, function(data) {
        $(targ).html(data);
    });

    $this.tab('show');
    return false;
});		

	//שינוי עמוד בהתאם לטאב
$('#newnavid a').click(function(e) {
  e.preventDefault();
  $(this).pill('show');
$('.tab-content > .tab-pane.active').jScrollPane();   
$('html,body').scrollTop(0);
});


$("a").on("shown.bs.tab", function(e) {
    
  var id = $(e.target).attr("href").substr(1);
  window.location.hash = id;
  $('html,body').scrollTop(0);

});    
    
    
    
// on load of the page: switch to the currently selected tab
var hash = window.location.hash;
$('.nav-tabs a[href="' + hash + '"]').tab('show');
//סיום שינוי עמוד בהתאם לטאב

</script>
	     
	      
	       
	        
<script>
	
$(document).ready(function(){
  var windowWidth = $(window).width();
  if(windowWidth <= 1024) //for iPad & smaller devices
     $('#MenuSettingSystem').removeClass('show');
	 $('html,body').scrollTop(0);
});
	</script>

	          
	           
           
	             
	               
<script>
$( ".js-example-basic-single" ).select2( { placeholder: "1Select a State", maximumSelectionSize: 6,   allowClear: true } );	
	
$(function() {
	$('[data-toggle="tooltip"]').tooltip()
});	
	
	
$("#select1").change(function() {
  
  $("#select2").val('').trigger('change');
  if ($("#select1").data('options') == undefined) {
    /*Taking an array of all options-2 and kind of embedding it on the select1*/
    $(this).data('options', $('#select2 option').clone());

  }
  var id = $(this).val();
  var options = $(this).data('options').filter('[asa=' + id + ']');
  $('#select2').html(options);  
});	
	
	
	 
$("#ViewClass").change(function() {
  
  var Id = this.value;
  if (Id=='1'){    
  ViewClass1.style.display = "block";
  ViewClass2.style.display = "none";
  ViewClass3.style.display = "none";
  ViewClass4.style.display = "none";
  ViewClass5.style.display = "none";      
  }
  else if (Id=='2') {
  ViewClass1.style.display = "none";
  ViewClass2.style.display = "block";
  ViewClass3.style.display = "none"; 
  ViewClass4.style.display = "none";
  ViewClass5.style.display = "none";      
  }   
  else if (Id=='3') {
  ViewClass1.style.display = "none";
  ViewClass2.style.display = "none";
  ViewClass3.style.display = "block";
  ViewClass4.style.display = "none";
  ViewClass5.style.display = "none";      
  } 
  else if (Id=='4') {
  ViewClass1.style.display = "none";
  ViewClass2.style.display = "none";
  ViewClass3.style.display = "none";
  ViewClass4.style.display = "block";
  ViewClass5.style.display = "none";      
  } 
  else if (Id=='5') {
  ViewClass1.style.display = "none";
  ViewClass2.style.display = "none";
  ViewClass3.style.display = "none";
  ViewClass4.style.display = "none";
  ViewClass5.style.display = "block";      
  }     
  else {
  ViewClass1.style.display = "none";
  ViewClass2.style.display = "none";
  ViewClass3.style.display = "none";
  ViewClass4.style.display = "none";
  ViewClass5.style.display = "none";      
  }    
});		
	
	
$("#DifrentTime").change(function() {
  
  var Id = this.value;
  if (Id=='1'){    
  DifrentTime1.style.display = "block";     
  } 
  else {
  DifrentTime1.style.display = "none";       
  }    
});	
    
    
$("#TypeDifrentTime").change(function() {
  
  var Id = this.value;
  if (Id=='0'){    
  TypeDifrentTimeDiv1.style.display = "block";
  TypeDifrentTimeDiv2.style.display = "none";
  $('#DifrentTimeOption').prop('max', '');      
  } 
  else if (Id=='2') {
  TypeDifrentTimeDiv2.style.display = "block";
  TypeDifrentTimeDiv1.style.display = "none"; 
  $('#DifrentTimeOption').prop('max', '23:59');       
  }      
  else {
  TypeDifrentTimeDiv2.style.display = "block";
  TypeDifrentTimeDiv1.style.display = "none";
  $('#DifrentTimeOption').prop('max', '');      
  }    
});	    
    
    
  
    
    
    
$("#WatingListNight").change(function() {
  
  var Id = this.value;
  if (Id=='1'){    
  WatingListNight1.style.display = "block";
  $("#WatingListStartTime").prop('required',true);
  $("#WatingListEndTime").prop('required',true);       
  } 
  else {
  WatingListNight1.style.display = "none"; 
  $("#WatingListStartTime").prop('required',false);
  $("#WatingListEndTime").prop('required',false);       
  }    
});	    
    
$("#WatinglistOption").change(function() {
  
  var Id = this.value;
  if (Id=='1'){    
  Watinglist1.style.display = "block";
  Watinglist2.style.display = "none";     
  }   
  else {
  Watinglist1.style.display = "none"; 
  Watinglist2.style.display = "block";     
  }    
});	    


$("#WatinglistOrder").change(function() {

  var Id = this.value;
  if (Id=='1'){
  WatinglistOrder1.style.display = "block";
  }
  else {
  WatinglistOrder1.style.display = "none";
  }
});

$("#SendSMS").change(function() {
  
  var Id = this.value;
  if (Id=='1'){    
  SendSMS1.style.display = "block";     
  } 
  else {
  SendSMS1.style.display = "none";       
  }    
});	
    

 $("#ClassWeek").change(function() {
  
  var Id = this.value;
  if (Id=='1'){    
  ClassWeek1.style.display = "block";        
  } 
  else {
  ClassWeek1.style.display = "none";        
  }    
});	   
    
    
 $("#MemberShipLimitType").change(function() {
  
  var Id = this.value;
     
     
  if (Id=='0'){    
  MemberShipLimit.style.display = "block"; 
  MemberShipLimits.style.display = "block";
  MemberShipLimitDaysDiv.style.display = "none";      
  }
  else if (Id=='1'){
  MemberShipLimit.style.display = "block";
  MemberShipLimits.style.display = "none";
  MemberShipLimitDaysDiv.style.display = "block";      
  }     
  else {
  MemberShipLimit.style.display = "none";
  MemberShipLimits.style.display = "none";
  MemberShipLimitDaysDiv.style.display = "none";      
  }    
});	    
    
 $("#MemberShipLimitUnBlock").change(function() {
  
  var Id = this.value;

  if (Id=='1'){    
  MemberShipLimitUnBlockDiv.style.display = "none";        
  }   
  else {
  MemberShipLimitUnBlockDiv.style.display = "block";        
  }    
});	    
    
    
    
$(document).ready(function() {



$( ".select2a" ).select2( {   allowClear: true,theme:"bootstrap" } );	

});


//  $("#WatingListStartTime").change(function() {
//
//   var WatingListStartTime = this.value;
//   $('#WatingListEndTime').prop('min', WatingListStartTime);
//
//
// });



</script>              
                   

<?php else: ?>
<?php redirect_to('index.php'); ?>
<?php endif ?>


<?php endif ?>



<?php require_once '../app/views/footernew.php'; ?>