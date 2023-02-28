<?php 
require_once '../app/init.php'; 
$pageTitle = lang('checkin_settings');
require_once '../app/views/headernew.php';
require_once 'Classes/Translations.php';
?>


<?php if (Auth::check()):?>

<?php if (Auth::userCan('132')): ?>

<?php



$CompanyNum = Auth::user()->CompanyNum;

$ClassSettingsInfo = DB::table('checkinsettings')->where('CompanyNum' ,'=', Auth::user()->CompanyNum)->first();

$translation = new Translations();
$languages = $translation->getLanguages();

CreateLogMovement(lang('checkin_log'), '0');



?>





<link href="assets/css/fixstyle.css" rel="stylesheet">

<!-- <div class="col-md-12 col-sm-12">

<div class="row">







<div class="col-md-5 col-sm-12 order-md-1">

<h3 class="page-header headertitlemain"  style="height:54px;">

<?php //echo $DateTitleHeader; ?>

</h3>

</div>



<div class="col-md-5 col-sm-12 order-md-3">

<h3 class="page-header headertitlemain"  style="height:54px;">

<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">

<i class="fas fa-mobile-alt"></i> הגדרות אפליקצית CheckIn 

</div>

</h3>

</div>



<div class="col-md-2 col-sm-12 order-md-2 pb-1">



</div>





</div>



<nav aria-label="breadcrumb" >

  <ol class="breadcrumb">	

  <li class="breadcrumb-item"><a href="index.php" class="text-dark">ראשי</a></li>

  <li class="breadcrumb-item active">הגדרות</li>

  <li class="breadcrumb-item active">הגדרות אפליקצית CheckIn</li>

  </ol>  

</nav>     -->





<div class="row">

<?php include("SettingsInc/RightCards.php"); ?>



<div class="col-md-10 col-sm-12">	





    <div class="card spacebottom">

    <div class="card-header text-start" >

    <i class="fas fa-mobile-alt"></i> <b><?php echo lang('checkin_settings') ?></b>

 	</div>    

  	<div class="card-body text-start">
    <?php echo lang('login_details') ?><br>

<hr>    

        

     <form action="CheckInAppSettings"  class="ajax-form clearfix"  autocomplete="off">

         

                <div class="row">

                <div class="col-md-6">	

         

                <div class="form-group">

                <label><?php echo lang('display_class_ahead') ?></label>

                <input type="text" name="ShowClass" class="form-control" value="<?php echo $ClassSettingsInfo->ShowClass; ?>" onkeypress='validate(event)' required>

                </div> 

                </div> 

                    

                <div class="col-md-6">

                <div class="form-group">

                <label><?php echo lang('option') ?></label>

                <select class="form-control text-start" name="ShowClassType" id="ShowClassType" >

                <option value="1" <?php if ($ClassSettingsInfo->ShowClassType=='1') { echo 'selected'; } else {} ?> ><?php echo lang('minutes') ?></option>

                <option value="2" <?php if ($ClassSettingsInfo->ShowClassType=='2') { echo 'selected'; } else {} ?> ><?php echo lang('hours') ?></option>         

                </select> 

                </div>     

                </div> 

                </div> 

         

               <div class="alertb alert-info"><?php echo lang('allow_checkin') ?></div>

         

         

                <div class="row">

                <div class="col-md-6">	

         

                <div class="form-group">

                <label><?php echo lang('time_after_class_start') ?></label>

                <input type="text" name="ShowLateClass" class="form-control" value="<?php echo $ClassSettingsInfo->ShowLateClass; ?>" onkeypress='validate(event)' required>

                </div> 

                </div> 

                    

                <div class="col-md-6">

                <div class="form-group">

                <label><?php echo lang('option') ?></label>

                <select class="form-control text-start" name="ShowLateClassType" id="ShowLateClassType" >

                <option value="1" <?php if ($ClassSettingsInfo->ShowLateClassType=='1') { echo 'selected'; } else {} ?> ><?php echo lang('minutes') ?></option>

                <option value="2" <?php if ($ClassSettingsInfo->ShowLateClassType=='2') { echo 'selected'; } else {} ?> ><?php echo lang('hours') ?></option>         

                </select> 

                </div>     

                </div> 

                </div> 

                    

                <div class="alertb alert-info"><?php echo lang('set_auto_refresh') ?></div>



                <div class="row">

                <div class="col-md-6">	

         

                <div class="form-group">

                <label><?php echo lang('time_to_auto_refresh') ?></label>

                <input type="text" name="RefreshTime" class="form-control" value="<?php echo $ClassSettingsInfo->RefreshTime; ?>" onkeypress='validate(event)' required>

                </div> 

                </div> 

                    

                <div class="col-md-6">

                <div class="form-group">

                <label><?php echo lang('option') ?></label>

                <select class="form-control text-start" name="RefreshTimeType" id="RefreshTimeType" >

                <option value="1" <?php if ($ClassSettingsInfo->RefreshTimeType=='1') { echo 'selected'; } else {} ?> ><?php echo lang('minutes') ?></option>

                <option value="2" <?php if ($ClassSettingsInfo->RefreshTimeType=='2') { echo 'selected'; } else {} ?> ><?php echo lang('hours') ?></option>         

                </select> 

                </div>     

                </div> 

                </div> 

 

                <hr>

                <div class="form-group">

                <label><?php echo lang('display_customer_full_name') ?></label>

                <select class="form-control text-start" name="ShowFullName" id="ShowFullName" >

                <option value="1" <?php if ($ClassSettingsInfo->ShowFullName=='1') { echo 'selected'; } else {} ?> ><?php echo lang('yes') ?></option>

                <option value="2" <?php if ($ClassSettingsInfo->ShowFullName=='2') { echo 'selected'; } else {} ?> ><?php echo lang('no') ?></option>         

                </select> 

                </div> 

         

         

                <hr>

                <div class="form-group">

                <label><?php echo lang('default_status') ?></label>

                <select class="form-control text-start" name="StatusClose" id="StatusClose" >

                <option value="8" <?php if ($ClassSettingsInfo->StatusClose=='8') { echo 'selected'; } else {} ?> ><?php echo lang('status_late_cancel') ?></option>

                <option value="4" <?php if ($ClassSettingsInfo->StatusClose=='4') { echo 'selected'; } else {} ?> ><?php echo lang('cacncled_and_charged') ?></option> 

                <option value="2" <?php if ($ClassSettingsInfo->StatusClose=='2') { echo 'selected'; } else {} ?> ><?php echo lang('arrived_fullfilled') ?></option>     

                </select> 

                </div>
        <?php if(Auth::user()->role_id == 1) { ?>
         <div class="form-group">

             <label><?php echo lang('language') ?></label>

             <select class="form-control text-start" name="language-select" id="language-select" >
                 <?php foreach ($languages as $lang){ ?>
                        <option value="<?php echo $lang->lang_code ?>" <?php echo ($lang->lang_code == $ClassSettingsInfo->language ) ? "selected" : "" ?>><?php echo $lang->name ?></option>
                 <?php } ?>
             </select>

         </div>
         <?php } ?>





         <hr>

<div class="form-group">

<button type="submit" class="btn btn-success btn-lg"><?php echo lang('update') ?></button>	

</div>    

             



<hr>

        

   <div class="alertb alert-info" ><?php echo lang('downlad_from_google') ?><br>

   <?php echo lang('search_boost_in') ?><br>

   <?php echo lang('app_for_tablet') ?><br>

<a href="https://play.google.com/store/apps/details?id=com.connect_computer.boostin"><?php echo lang('click_for_download') ?></a>

         

<hr>

 

<?php       

$AppLogins = DB::table('boostappcheckin.users')->where('CompanyNum' ,'=', $CompanyNum)->where('id' ,'!=', '1')->first();       

?>

       

       

<?php echo lang('username_single') ?>: <?php echo @$AppLogins->username; ?><br>

<?php echo lang('password_single') ?>: <?php echo @$AppLogins->TruePassword; ?>       

         

</div>           

         

         

            </div>

            </form>  



 

    

        </div>

    </div>



	</div> 

</div>



</div>





<?php else: ?>

<?php redirect_to('../index.php'); ?>

<?php endif ?>





<?php endif ?>



<?php if (Auth::guest()): ?>



<?php redirect_to('../index.php'); ?>



<?php endif ?>



<?php require_once '../app/views/footernew.php'; ?>
