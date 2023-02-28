<?php
require_once 'Classes/Settings.php';
require_once __DIR__.'/../Classes/TextSaved.php';
$countTextTemplates = TextSaved::getCompanyTemplates(Auth::user()->CompanyNum);
$show = false;
$CompanyNum = $CompanyNum ??Auth::user()->CompanyNum;
$companySettings = (new Settings())->getSettings($CompanyNum);
?>
<div class="col-md-2 col-sm-12 " >
      
    
    <div class="card spacebottom" style="margin-bottom: 20px;">
  <a data-toggle="collapse" href="#MenuSettings" aria-expanded="true" aria-controls="MenuItems" style="color: black;">
  <div class="card-header text-start">
    <strong><i class="fas fa-cogs fa-fw"></i> <?php echo lang('settings_menu') ?></strong>
  </div>
  </a>
  
  <div class="collapse show" id="MenuSettings">
  <div class="card-body">
      
<div class="nav nav-tabs flex-column nav-pills text-start" id="v-pills-tab" role="tablist" aria-orientation="vertical">
<?php if (Auth::userCan('1')): ?>    
<a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'SettingsDashboard.php') !== false) {echo "active";} ?>" href="SettingsDashboard.php" aria-selected="true"><i class="fas fa-angle-left fa-fw"></i> <?php echo lang('path_main') ?></a>
<?php endif ?>
    
<?php if (Auth::userCan('2')): ?>    
<a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'BusinessSettings.php') !== false) {echo "active";} ?>" href="BusinessSettings.php" aria-selected="true"><i class="fas fa-cog fa-fw"></i> <?php echo lang('settings_business') ?></a>
<?php endif ?>
    
<?php if ($show && Auth::userCan('115')): ?>
<a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'DeskPlanSettings.php') !== false) {echo "active";} ?>" href="DeskPlanSettings.php" aria-selected="true"><i class="fas fa-calendar-alt fa-fw"></i> <?php echo lang('settings_calendar') ?></a>
<?php endif ?>

<?php if (Auth::userCan('125')): ?>    
<a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'Roleslist.php') !== false) {echo "active";} ?>" href="Roleslist.php" aria-selected="true"><i class="fas fa-user-lock"></i> <?php echo lang('settings_permission') ?></a>
<?php endif ?>    
    
<?php if (Auth::userCan('3')): ?>    
<a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'AgentList.php') !== false) {echo "active";} ?>" href="AgentList.php" aria-selected="true"><i class="fas fa-user-circle fa-fw"></i> <?php echo lang('settings_users') ?></a>
<?php endif ?>
    
<?php /* if (Auth::userCan('151')): ?>
<a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'Branch.php') !== false) {echo "active";} ?>" href="Branch.php" aria-selected="true"><i class="fas fa-code-branch fa-fw"></i> <?php echo lang('settings_branch') ?></a>
<?php endif */ ?>
    
<?php if ($show && Auth::userCan('7')): ?>
<a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'ClassType.php') !== false) {echo "active";} ?>" href="ClassType.php" aria-selected="true"><i class="fas fa-university
 fa-fw"></i> <?php echo lang('settings_class') ?></a>
<?php endif ?>
    
<?php if ($show && Auth::userCan('5')): ?>
<a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'ActivitySettings.php') !== false) {echo "active";} ?>" href="ActivitySettings.php" aria-selected="true"><i class="fas fa-address-card
 fa-fw"></i><?php echo lang('class_membership_type') ?></a>
<?php endif ?>
    
    
 <?php if ($show && Auth::userCan('9')): ?>
<a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'DeviceNumbers.php') !== false) {echo "active";} ?>" href="DeviceNumbers.php" aria-selected="true"><i class="fas fa-list-ol
 fa-fw"></i> <?php echo lang('settings_equipment') ?></a>   
<?php endif ?>
    
<?php if (Auth::userCan('11')): ?>     
<a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'AppsSettings.php') !== false) {echo "active";} ?>" href="AppsSettings.php" aria-selected="true"><i class="fas fa-mobile-alt fa-fw"></i> <?php echo lang('application') ?></a>
<?php endif ?>
    
    
  
<?php if (Auth::userCan('131')): ?>     
<a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'CheckInSettings.php') !== false) {echo "active";} ?>" href="CheckInSettings.php" aria-selected="true"><i class="fas fa-mobile-alt fa-fw"></i> <?php echo lang('checkin_app') ?></a>
<?php endif ?> 
   
    
<?php if ($show && Auth::userCan('13')): ?>
<a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'MettingsRoom.php') !== false) {echo "active";} ?>" href="MettingsRoom.php" aria-selected="true"><i class="fas fa-chess-rook fa-fw"></i> <?php echo lang('settings_studio_rooms') ?></a>
<?php endif ?>
    
   
<?php if (Auth::userCan('136')): ?>     
<a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'ClassTemplate.php') !== false) {echo "active";} ?>" href="ClassTemplate.php" aria-selected="true"><i class="fas fa-user-edit fa-fw"></i> <?php echo lang('settings_personal_training') ?></a>
<?php endif ?>      
     
    
<?php if($countTextTemplates > 0 && Auth::userCan('15')) { ?>
<a class="nav-link text-dark <?php echo (strpos(basename($_SERVER['REQUEST_URI']), 'SavedMessagesList.php') !== false) ?  "active" : '' ?>" href="SavedMessagesList.php" aria-selected="true"><i class="fas fa-comments fa-fw"></i> <?php echo lang('settings_message_template') ?></a>
<?php } ?>
 
<?php if (Auth::userCan('17')): ?>     
<a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'SavedNotificationList.php') !== false) {echo "active";} ?>" href="SavedNotificationList.php" aria-selected="true"><i class="fas fa-bell fa-fw"></i> <?php echo lang('settings_notifications_template') ?></a>
<?php endif ?>   
	
<?php //if (Auth::userCan('49')): ?><!--  -->
<!---->
<!--<a class="nav-link  --><?php //echo (strpos(basename($_SERVER['REQUEST_URI']), 'facebookAttachLeads.php') !== false) ? "active text-white" : "text-dark"; ?><!--" href="facebookAttachLeads.php" aria-selected="true"><i class="fab fa-facebook fa-fw"></i> --><?php //echo lang('settings_facebook') ?><!--</a>-->
<!--<a class="nav-link text-dark --><?php //if (strpos(basename($_SERVER['REQUEST_URI']), 'StatusList.php') !== false) {echo "active";} ?><!--" href="StatusList.php" aria-selected="true"><i class="fas fa-align-right fa-fw"></i> --><?php //echo lang('settings_pipeline') ?><!--</a>-->
<!--   -->
<!--<a class="nav-link text-dark --><?php //if (strpos(basename($_SERVER['REQUEST_URI']), 'SourceList.php') !== false) {echo "active";} ?><!--" href="SourceList.php" aria-selected="true"><i class="fas fa-project-diagram fa-fw"></i>--><?php //echo lang('settings_lead_source') ?><!--</a>-->
<!--    -->
<!--<a class="nav-link text-dark --><?php //if (strpos(basename($_SERVER['REQUEST_URI']), 'ReasonsList.php') !== false) {echo "active";} ?><!--" href="ReasonsList.php" aria-selected="true"><i class="fas fa-exclamation-triangle fa-fw"></i> --><?php //echo lang('settings_lead_fail') ?><!--</a>    -->
<!--    -->
<?php //endif ?>
    
<?php //if (Auth::userCan('129')): ?><!--	-->
<!--<a class="nav-link text-dark --><?php //if (strpos(basename($_SERVER['REQUEST_URI']), 'ClientLevel.php') !== false) {echo "active";} ?><!--" href="ClientLevel.php" aria-selected="true"><i class="fas fa-user-ninja fa-fw"></i> --><?php //echo lang('settings_client_rank') ?><!--</a>    -->
<?php //endif ?><!--  -->

<!--    -->
<?php //if (Auth::userCan('137')): ?><!--	-->
<!--<a class="nav-link text-dark --><?php //if (strpos(basename($_SERVER['REQUEST_URI']), 'CalType.php') !== false) {echo "active";} ?><!--" href="CalType.php" aria-selected="true"><i class="fas fa-thumbtack fa-fw"></i> --><?php //echo lang('settings_task') ?><!--</a>    -->
<?php //endif ?><!--    -->
    
    
<?php if (Auth::userCan('143')): ?>	
<a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'Automation.php') !== false) {echo "active";} ?>" href="Automation.php" aria-selected="true"><i class="fas fa-magic fa-fw"></i> <?php echo lang('settings_automation') ?></a>    
<?php endif ?>   
    
    
<?php if (Auth::userCan('152')): ?>	    
<a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'DynamicForms.php') !== false) {echo "active";} ?>" href="DynamicForms.php" aria-selected="true"><i class="fab fa-wpforms fa-fw"></i> <?php echo lang('settings_docs') ?></a>   
<?php endif ?>       

<a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'onlineLibrary.php') !== false) {echo "active";} ?>" href="onlineLibrary.php" aria-selected="true"><i class="fal fa-video fa-fw"></i> <?php echo lang('vod_library') ?></a>
<a class="nav-link text-dark <?php if ($_SERVER['PHP_SELF'] == '/office/landings/index.php' || basename($_SERVER['PHP_SELF']) == 'landings') echo "active"; ?>"  href="/office/landings/" aria-selected="true"><i class="fab fa-elementor fa-fw"></i> <?php echo lang('landing_pages') ?></a>

</div>
      
  </div>
	</div></div>
      
   
  
   <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'BusinessSettings.php') !== false) { ?>
    <?php if (Auth::userCan('2')): ?> 
   <div class="card" style="margin-bottom: 20px;">
  <a data-toggle="collapse" href="#MenuSettingSystem" aria-expanded="true" aria-controls="MenuSettingSystem" style="color: black;">
  <div class="card-header text-start">
    <strong><i class="fas fa-cog fa-fw"></i> <?php echo lang('settings_business') ?></strong>
  </div>
  </a>
  
  <div class="collapse show" id="MenuSettingSystem">
  <div class="card-body">
<div class="nav nav-tabs flex-column nav-pills text-start" id="v-pills-tab" role="tablist" aria-orientation="vertical">
  <a class="nav-link text-dark active"  data-toggle="pill" href="#generalsettings" role="tab" aria-controls="v-pills-overview" aria-selected="true"><?php echo lang('general') ?></a>
  <a class="nav-link text-dark"  data-toggle="pill" href="#docsnum" role="tab" aria-controls="v-pills-overview" aria-selected="true"><?php echo lang('settings_main_docs') ?></a>
  <a class="nav-link text-dark"  data-toggle="pill" href="#docsdesign" role="tab" aria-controls="v-pills-overview" aria-selected="true"><?php echo lang('settings_docs_style') ?></a>
  <a class="nav-link text-dark"  data-toggle="pill" href="#contactinfo" role="tab" aria-controls="v-pills-overview" aria-selected="true"><?php echo lang('contact_details') ?></a>
  <a class="nav-link text-dark"  data-toggle="pill" href="#docsnotes" role="tab" aria-controls="v-pills-overview" aria-selected="true"><?php echo lang('permanent_notes') ?></a>
  <a class="nav-link text-dark"  data-toggle="pill" href="#accountmanager" role="tab" aria-controls="v-pills-overview" aria-selected="true"><?php echo lang('auto_reports') ?></a>
    <?php if(Auth::user()->role_id == 1) { ?>
  <a class="nav-link text-dark"  data-toggle="pill" href="#creditcard" role="tab" aria-controls="v-pills-overview" aria-selected="true"><?php echo lang('clearing_cc') ?></a>
  <a class="nav-link text-dark"  data-toggle="pill" href="#maasav" role="tab" aria-controls="v-pills-overview" aria-selected="true"><?php echo lang('settings_bank') ?></a>
  <a class="nav-link text-dark"  data-toggle="pill" href="#voicecenter" role="tab" aria-controls="v-pills-overview" aria-selected="true"><?php echo lang('interfacing_center') ?></a>
    <?php } ?>
  <?php if (Auth::userCan('151')): ?>
<a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'Branch.php') !== false) {echo "active";} ?>" href="Branch.php" aria-selected="true"> <?php echo lang('settings_branch') ?></a>
<?php endif ?>

 <a class="nav-link text-dark"  data-toggle="pill" href="#generalaccounts" role="tab" aria-controls="v-pills-overview" aria-selected="true"><?php echo lang('reports_boostapp_charge') ?></a>

</div>      
  </div>
	</div></div> 
<?php endif ?>     
    
    
<script>
	
$(document).ready(function(){
     $('#MenuSettings').removeClass('show');
	 $('html,body').scrollTop(0);
});
	</script>

	
<?php } else if (strpos(basename($_SERVER['REQUEST_URI']), 'AppsSettings.php') !== false) { ?>
    
<?php if (Auth::userCan('11')): ?> 
	   <div class="card" style="margin-bottom: 20px;">
  <a data-toggle="collapse" href="#MenuAppSettingSystem" aria-expanded="true" aria-controls="MenuAppSettingSystem" style="color: black;">
  <div class="card-header text-start">
    <strong><i class="fas fa-mobile-alt fa-fw"></i> <?php echo lang('app_settings') ?></strong>
  </div>
  </a>
  
  <div class="collapse show" id="MenuAppSettingSystem">
  <div class="card-body">
<div class="nav nav-tabs flex-column nav-pills text-start" id="v-pills-tab" role="tablist" aria-orientation="vertical">
  <a class="nav-link text-dark active"  data-toggle="pill" href="#appgeneral" role="tab" aria-controls="v-pills-overview" aria-selected="true"><?php echo lang('general') ?></a>
  <a class="nav-link text-dark"  data-toggle="pill" href="#app-content" role="tab" aria-controls="v-pills-overview" aria-selected="true"><?php echo lang('edit_content') ?></a>
  <a class="nav-link text-dark"  data-toggle="pill" href="#appcancel" role="tab" aria-controls="v-pills-overview" aria-selected="true"><?php echo lang('cacellation') ?></a>
  <a class="nav-link text-dark"  data-toggle="pill" href="#watinglist" role="tab" aria-controls="v-pills-overview" aria-selected="true"><?php echo lang('settings_app_waitlist') ?></a>
  <?php $Supplier = DB::table('appsettings')->where('CompanyNum',  $CompanyNum)->first(); ?>
</div>       
  </div>
	</div></div> 
<?php endif ?>    
    
    
<script>
	
$(document).ready(function(){
     $('#MenuSettings').removeClass('show');
	 $('html,body').scrollTop(0);
});
	</script>
	
	
<?php } else if (strpos(basename($_SERVER['REQUEST_URI']), 'DynamicForms.php') !== false) { ?>
    
 
<div class="card" style="margin-bottom: 20px;">
  <a data-toggle="collapse" href="#MenuFormsSettingSystem" aria-expanded="true" aria-controls="MenuFormsSettingSystem" style="color: black;">
  <div class="card-header text-start">
    <strong><i class="fab fa-wpforms fa-fw"></i> <?php echo lang('settings_docs') ?></strong>
  </div>
  </a>
  
  <div class="collapse show" id="MenuFormsSettingSystem">
  <div class="card-body">
<div class="nav nav-tabs flex-column nav-pills text-start" id="v-pills-tab" role="tablist" aria-orientation="vertical">
    <?php if ($Supplier && $Supplier->ShowHealth == 0) { ?>
        <a class="nav-link text-dark"  data-toggle="pill" href="#appHealth" role="tab" aria-controls="v-pills-overview" aria-selected="true"><?php echo lang('health_declaration') ?></a>
    <?php } ?>

    <?php if ($Supplier && $Supplier->ShowTakanon == 0) { ?>
        <a class="nav-link text-dark"  data-toggle="pill" href="#appTakanon" role="tab" aria-controls="v-pills-overview" aria-selected="true"><?php echo lang('terms') ?></a>
    <?php } ?>
    <a class="nav-link text-dark active" data-toggle="pill" href="#DynamicForms" role="tab" aria-controls="v-pills-overview" aria-selected="true"><?php echo lang('settings_dynamic_forms') ?></a>

</div>       
  </div>
	</div></div> 
  
    
    
<script>
	
$(document).ready(function(){
     $('#MenuSettings').removeClass('show');
	 $('html,body').scrollTop(0);
});
	</script>
	
	
<?php } else { ?>
    
 <?php } ?>   
</div>


<script>
	
$(document).ready(function(){
  var windowWidth = $(window).width();
  if(windowWidth <= 1024) //for iPad & smaller devices
     $('#MenuSettings').removeClass('show');
	 $('html,body').scrollTop(0);
});
	</script>




