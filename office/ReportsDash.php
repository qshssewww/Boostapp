<?php require_once '../app/init.php'; ?>

<?php if (Auth::guest()): redirect_to('index.php'); endif ?>


<?php if (Auth::check()):?>
<?php if (Auth::userCan('19')): ?>



<?php
$pageTitle = lang('reports');
require_once '../app/views/headernew.php';
$CompanyNum = Auth::user()->CompanyNum;
?>

<?php $BusinessSettings = DB::table('settings')->where('CompanyNum',  $CompanyNum)->first(); ?>

<?php CreateLogMovement(lang('reports_log_status'),'0'); ?>



<link href="assets/css/fixstyle.css" rel="stylesheet">



<!-- <div class="row pb-3">

<div class="col-md-6 col-sm-12 order-md-1">
<h3 class="page-header headertitlemain"  style="height:54px;">
<?php //echo $DateTitleHeader; ?>
</h3>
</div>

<div class="col-md-6 col-sm-12 order-md-4">
<h3 class="page-header headertitlemain"  style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
<i class="fas fa-chart-pie fa-fw"></i> דוחות
</div>
</h3>
</div> 


</div> -->
<div class="row px-0 mx-0">
<div class="col-12  px-0 mx-0" >


<!-- <nav aria-label="breadcrumb" >
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-info">ראשי</a></li>
  <li class="breadcrumb-item active" aria-current="page">דוחות</li>
  </ol>  
</nav>     -->

<div class="row">

<?php include("ReportsInc/SideMenu.php"); ?>

<div class="col-md-10 col-sm-12">	
    <div class="tab-content">
                        
                        
                        
                       
							
							
							
<div class="tab-pane fade show active text-start" role="tabpanel" id="user-overview">
<div class="card spacebottom">
      <div class="card-header text-start" ><i class="fas fa-chart-pie fa-fw"></i><strong> <?php echo lang('reports') ?></strong></div>    
  <div class="card-body">       
                    
<i class="fas fa-angle-double-right"></i> <?php echo lang('select_report_from_list') ?>



</div></div></div></div></div></div></div>


<?php else: ?>
<?php //redirect_to('index.php');  ?>
<?php ErrorPage (lang('permission_blocked'), lang('no_page_persmission')); ?>
<?php endif ?>


<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>
