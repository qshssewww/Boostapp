<?php
include_once __DIR__.'/../Classes/ClassStudioDate.php';
include_once __DIR__.'/../Classes/ClientActivities.php';
include_once __DIR__.'/../Classes/Docs.php';
include_once __DIR__.'/../Classes/Users.php';
include_once __DIR__.'/../Classes/PayToken.php';


$company_num = $CompanyNum ?? Auth::user()->CompanyNum;

$isClasses = ClassStudioDate::isStudioHasClasses($company_num);
$isMemberships = ClientActivities::isStudioHasMemberships($company_num);
$isDocs = Docs::isStudioHasDocs($company_num);
$isUsers = Users::isStudioHasUsers($company_num, Auth::user()->role_id == 1 ? Auth::user()->id : null);
$isPaytokens = PayToken::isStudioHasPaytokens($company_num);

$CompanySettingsDash = Settings::getSettings($CompanyNum ?? Auth::user()->CompanyNum);

?>

<div class="col-md-2 col-sm-12">

    <!-- Attendance reports | START | דוחות נוכחות -->
    <?php if ($isClasses) { ?>
    <div class="card spacebottom mb-20">
        <a data-toggle="collapse" href="#reports_menu_attendance" aria-expanded="true" aria-controls="CPAmenu" style="color: black;">
            <div class="card-header text-start">
                <strong><i class="fal fa-file-chart-line"></i> <?= lang('text_attendance_reports') ?></strong>
            </div>
        </a>

        <div class="collapse show" id="reports_menu_attendance">
            <div class="card-body">
                <div class="nav nav-tabs flex-column nav-pills text-start" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'MeetingsReport.php') !== false) {echo "active";} ?>" href="/office/Reports/MeetingsReport.php" aria-selected="true">
                        <?= lang('meeting_report_title') ?>
                    </a>
                    <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'firstTime.php') !== false) {echo "active";} ?>" href="/office/Reports/firstTime.php" aria-selected="true">
                        <?= lang('reports_new_entry') ?>
                    </a>
                    <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'tryOut.php') !== false) {echo "active";} ?>" href="/office/Reports/tryOut.php" aria-selected="true">
                        <?= lang('trial_lesson') ?>
                    </a>
                    <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'classesRegisters.php') !== false) {echo "active";} ?>" href="/office/Reports/classesRegisters.php" aria-selected="true">
                        <?= lang('reports_class_attend') ?>
                    </a>
                    <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'BookingsReport.php') !== false) {echo "active";} ?>" href="/office/Reports/BookingsReport.php" aria-selected="true">
                        <?= lang('reports_presence') ?>
                    </a>
                    <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'registeredVisitors.php') !== false) {echo "active";} ?>" href="/office/Reports/registeredVisitors.php" aria-selected="true">
                        <?= lang('reports_visitors') ?>
                    </a>
                    <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'generalOrders.php') !== false) {echo "active";} ?>" href="/office/Reports/generalOrders.php" aria-selected="true">
                        <?= lang('reports_registration_general') ?>
                    </a>
                    <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'occupancy.php') !== false) {echo "active";} ?>" href="/office/Reports/occupancy.php" aria-selected="true">
                        <?= lang('reports_lesson_occupancy') ?>
                    </a>
                    <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'NoneShow.php') !== false) {echo "active";} ?>" href="/office/NoneShow.php" aria-selected="true">
                        <?= lang('reports_absence') ?>
                    </a>
                    <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'NonRegisters.php') !== false) {echo "active";} ?>" href="/office/Reports/NonRegisters.php" aria-selected="true">
                        <?= lang('reports_unregistration') ?>
                    </a>
                    <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'RegularReport.php') !== false) {echo "active";} ?>" href="/office/RegularReport.php" aria-selected="true">
                        <?= lang('reports_permanent_reg') ?>
                    </a>
                    <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'ClassNotificationReport.php') !== false) {echo "active";} ?>" href="/office/ClassNotificationReport.php" aria-selected="true">
                        <?= lang('reports_no_response_waitlist') ?>
                    </a>
                    <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'ClassEntrancesReport.php') !== false) {echo "active";} ?>" href="/office/ClassEntrancesReport.php" aria-selected="true">
                        <?= lang('reports_entries') ?>
                    </a>
                    <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'BDay.php') !== false) {echo "active";} ?>" href="/office/Reports/BDay.php" aria-selected="true">
                        <?= lang('birthdays') ?>
                    </a>
                    <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'clients.php') !== false) {echo "active";} ?>" href="/office/Reports/clients.php" aria-selected="true">
                        <?= lang('reports_age_rank') ?>
                    </a>
                </div>
            </div>
        </div>

    </div>
    <?php } ?>
    <!-- Attendance reports | END | דוחות נוכחות -->

    <!-- Membership reports | START | דוחות מנויים -->
    <?php if ($isMemberships) { ?>
    <div class="card spacebottom mb-20">
        <a data-toggle="collapse" href="#reports_menu_memberships" aria-expanded="true" aria-controls="CPAmenu" style="color: black;">
            <div class="card-header text-start">
                <strong><i class="fal fa-file-chart-line"></i> <?= lang('memberships_single') ?></strong>
            </div>
        </a>

        <div class="collapse" id="reports_menu_memberships">
            <div class="card-body">
                <div class="nav nav-tabs flex-column nav-pills text-start" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'Register.php') !== false) {echo "active";} ?>" href="/office/Reports/Register.php" aria-selected="true">
                        <?= lang('reports_joining') ?>
                    </a>
                    <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'CheckClients.php') !== false) {echo "active";} ?>" href="/office/Reports/CheckClients.php" aria-selected="true">
                        <?= lang('reports_expired_memberships') ?>
                    </a>
                    <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'leftStudio.php') !== false) {echo "active";} ?>" href="/office/Reports/leftStudio.php" aria-selected="true">
                        <?= lang('reports_customers_left') ?>
                    </a>
                    <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'membership.php') !== false) {echo "active";} ?>" href="/office/Reports/membership.php" aria-selected="true">
                        <?= lang('reports_subs') ?>
                    </a>
                    <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'MembershipFrozen.php') !== false) {echo "active";} ?>" href="/office/MembershipFrozen.php" aria-selected="true">
                        <?= lang('reports_freeze') ?>
                    </a>
                    <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'freezReport.php') !== false) {echo "active";} ?>" href="/office/freezReport.php" aria-selected="true">
                        <?= lang('tax_freez_report') ?>
                    </a>
                </div>
            </div>
        </div>

    </div>
    <?php } ?>
    <!-- Membership reports | END | דוחות מנויים -->

    <!-- Form reports | START | דוחות טפסים -->
    <div class="card spacebottom mb-20">
        <a data-toggle="collapse" href="#reports_menu_forms" aria-expanded="true" aria-controls="CPAmenu" style="color: black;">
            <div class="card-header text-start">
                <strong><i class="fal fa-file-chart-line"></i> <?= lang('customer_card_forms') ?></strong>
            </div>
        </a>

        <div class="collapse" id="reports_menu_forms">
            <div class="card-body">
                <div class="nav nav-tabs flex-column nav-pills text-start" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'MedicalReport.php') !== false) {echo "active";} ?>" href="/office/MedicalReport.php" aria-selected="true">
                        <?= lang('health_declaration') ?>
                    </a>
                    <!-- <a class="nav-link text-dark <// ?php if (strpos(basename($_SERVER['REQUEST_URI']), 'CoronaReport.php') !== false) {echo "active";} ?>" href="/office/CoronaReport.php" aria-selected="true">
                        <// ?= lang('health_declaration_covid') ?>
                    </a> -->
                    <!-- <a class="nav-link text-dark <//?php if (strpos(basename($_SERVER['REQUEST_URI']), 'GreenPassReport.php') !== false) {echo "active";} ?>" href="/office/GreenPassReport.php" aria-selected="true">
                        <//?= lang('green_passport_report') ?>
                    </a> -->
                    <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'TakanonReport.php') !== false) {echo "active";} ?>" href="/office/TakanonReport.php" aria-selected="true">
                        <?= lang('terms') ?>
                    </a>
                </div>
            </div>
        </div>

    </div>
    <!-- Form reports | END | דוחות טפסים -->
      
<?php if (Auth::userCan('20') && $isDocs): ?>
   <div class="card spacebottom">
  <a data-toggle="collapse" href="#CPAmenu4" aria-expanded="true" aria-controls="CPAmenu" style="color: black;">
  <div class="card-header text-start">
    <strong><i class="fal fa-file-chart-line"></i> <?php echo lang('customer_card_bookkeeping') ?></strong>
  </div>
  </a>
  
  <div class="collapse" id="CPAmenu4">
  <div class="card-body">
      
<div class="nav nav-tabs flex-column nav-pills text-start" id="v-pills-tab" role="tablist" aria-orientation="vertical">
 <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'CartesetAll.php') !== false) {echo "active";} ?>" href="/office/CartesetAll.php" aria-selected="true"><?php echo lang('card_index') ?></a>
    
 <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'Debt.php') !== false) {echo "active";} ?>" href="/office/Reports/Debt.php" aria-selected="true"><?php echo lang('reports_debit') ?></a>
    
<a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'receipts.php') !== false) {echo "active";} ?>" href="/office/Reports/receipts.php" aria-selected="true"><?php echo lang('reports_receipt') ?></a>
    
 <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'Sales.php') !== false) {echo "active";} ?>" href="/office/Sales.php" aria-selected="true"><?php echo lang('sales') ?></a>
    
 <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'cartesetcheck.php') !== false) {echo "active";} ?>" href="/office/cartesetcheck.php" aria-selected="true"><?php echo lang('reports_checks') ?></a>
<a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'cartesetcredit.php') !== false) {echo "active";} ?>" href="/office/cartesetcredit.php" aria-selected="true"><?php echo lang('reports_cc') ?></a>
    
<a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'ExpectedIncome.php') !== false) {echo "active";} ?>" href="/office/ExpectedIncome.php" aria-selected="true"><?php echo lang('reports_future_invoice') ?></a>
    
<!-- <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'CreateMasFile.php') !== false) {echo "active";} ?>" href="/office/CreateMasFile.php" aria-selected="true">הפקת קובץ אחיד</a>-->
</div>      
      
  </div>
	</div></div>
<?php endif ?>

 <?php if (Auth::userCan('149') && $isUsers > 1): ?>
   <div class="card spacebottom"  style="margin-top: 20px;">
  <a data-toggle="collapse" href="#CPAmenu5" aria-expanded="true" aria-controls="CPAmenu" style="color: black;">
  <div class="card-header text-start">
    <strong><i class="fal fa-file-chart-line"></i> <?php echo lang('reports_payroll') ?></strong>
  </div>
  </a>
  
  <div class="collapse" id="CPAmenu5">
  <div class="card-body">
      
<div class="nav nav-tabs flex-column nav-pills text-start" id="v-pills-tab" role="tablist" aria-orientation="vertical">
    
<a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'ClassHours.php') !== false) {echo "active";} ?>" href="/office/Reports/ClassHours.php" aria-selected="true"><?php echo lang('reports_payroll_guide') ?></a>
    
<a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'ClassClient.php') !== false) {echo "active";} ?>" href="/office/Reports/ClassClient.php" aria-selected="true"><?php echo lang('reports_payroll_customers') ?></a>
    
<a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'ClassClientFix.php') !== false) {echo "active";} ?>" href="/office/Reports/ClassClientFix.php" aria-selected="true"><?php echo lang('reports_fixed_payroll') ?></a>
    
<a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'signInClockNew.php') !== false) {echo "active";} ?>" href="/office/Reports/signInClockNew.php" aria-selected="true"><?php echo lang('reports_time_clock') ?></a>

</div>      
      
  </div>
	</div></div>
<?php endif ?>      
    
    
    
<?php if (Auth::userCan('21') && $isPaytokens): ?>
    <div class="card spacebottom" style="margin-top: 20px;">
  <a data-toggle="collapse" href="#HokMenu" aria-expanded="true" aria-controls="HokMenu" style="color: black;">
  <div class="card-header text-start">
    <strong><i class="fas fa-archive fa-fw"></i><?php echo lang('standing_orders') ?></strong>
  </div>
  </a>
  
  <div class="collapse" id="HokMenu">
  <div class="card-body">
      
<div class="nav nav-tabs flex-column nav-pills text-start" id="v-pills-tab" role="tablist" aria-orientation="vertical">
 <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'HokList.php') !== false) {echo "active";} ?>" href="/office/HokList.php" aria-selected="true"><?php echo lang('reports_direct_debit') ?></a>
 <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'Carteseterror.php') !== false) {echo "active";} ?>" href="/office/Carteseterror.php" aria-selected="true"><?php echo lang('reports_direct_fail') ?></a>
</div>      
      
  </div>
	</div></div>
<?php endif ?>  
    
    
<?php if (Auth::userCan('116')): ?>    
    
    <div class="card spacebottom" style="margin-top: 20px;">
  <a data-toggle="collapse" href="#System" aria-expanded="true" aria-controls="System" style="color: black;">
  <div class="card-header text-start">
    <strong><i class="fal fa-file-chart-line"></i> <?php echo lang('reports_system_usage') ?></strong>
  </div>
  </a>
  
  <div class="collapse" id="System">
  <div class="card-body">
      
<div class="nav nav-tabs flex-column nav-pills text-start" id="v-pills-tab" role="tablist" aria-orientation="vertical">
 <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'ReportSms.php') !== false) {echo "active";} ?>" href="/office/ReportSms.php" aria-selected="true"><?php echo lang('reports_sms') ?></a>
    <?php if ($CompanySettingsDash->WhatsAppEnabled == 1): ?>
        <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'ReportWhatsApp.php') !== false) {
            echo "active";
        } ?>" href="/office/ReportWhatsApp.php" aria-selected="true"><?php echo lang('report_whatsapp') ?></a>
    <?php endif; ?>
 <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'ReportMail.php') !== false) {echo "active";} ?>" href="/office/ReportMail.php" aria-selected="true"><?php echo lang('reports_email') ?></a>
 <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'LogChat.php') !== false) {echo "active";} ?>" href="/office/LogChat.php" aria-selected="true"><?php echo lang('reports_log_chat') ?></a>
 <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'LogList.php') !== false) {echo "active";} ?>" href="/office/LogList.php" aria-selected="true"><?php echo lang('reports_system_log') ?></a>
 <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'AppLog.php') !== false) {echo "active";} ?>" href="/office/AppLog.php" aria-selected="true"><?php echo lang('reports_application_log') ?></a>
 <a class="nav-link text-dark <?php if (strpos(basename($_SERVER['REQUEST_URI']), 'LogNotification.php') !== false) {echo "active";} ?>" href="/office/LogNotification.php" aria-selected="true"><?php echo lang('reports_notifications_log') ?></a>
 <?php if (Auth::userCan('2')): ?>   
 <a class="nav-link text-dark" href="/office/BusinessSettings.php#generalaccounts" aria-selected="true"><?php echo lang('reports_boostapp_charge') ?></a>
 <?php endif ?>     
</div>      
      
  </div>
	</div></div>
<?php endif ?>  

    
</div>





<script>
	
$(document).ready(function(){
  var windowWidth = $(window).width();
  if(windowWidth <= 1024) //for iPad & smaller devices
     $('#CPAmenu, #HokMenu, #System, #Leads').removeClass('show');
	 $('html,body').scrollTop(0);
});
	</script>




<script>

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

</script>
