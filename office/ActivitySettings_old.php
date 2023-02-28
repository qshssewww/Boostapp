<?php require_once '../app/init.php';?>

<?php if (Auth::guest()): redirect_to('index.php'); endif ?>

<?php echo View::make('headernew')->render() ?>


<?php if (Auth::check()):?>
<?php if (Auth::userCan('BusinessSettings')): ?>
<?php

 $AffID = Auth::user()->id;
 $AffName = Auth::user()->display_name;
 $CompanyNum = Auth::user()->CompanyNum;
 $Supplier = DB::table('settings')->where('CompanyNum',  $CompanyNum)->first();
 $UserId = User::find(Auth::user()->id);


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

<?php
//Log	
$LogUserId = Auth::user()->id;
$LogUserName = Auth::user()->display_name;
$LogDateTime = date('Y-m-d G:i:s');
$LogContent = "<i class='fa fa-user' aria-hidden='true'></i> ".$LogUserName." נכנס להגדרות מערכת";
//DB::table('log')->insert(array('UserId' => $LogUserId, 'Text' => $LogContent, 'Dates' => $LogDateTime, 'ClientId' => '0'));
//Log	
?>



<div class="col-md-12 col-sm-12">


<div class="row pb-3">

<div class="col-md-6 col-sm-12 order-md-1">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<?php echo $DateTitleHeader; ?>
</h3>
</div>

<div class="col-md-6 col-sm-12 order-md-4">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
<i class="fas fa-address-card fa-fw"></i> הגדרות מנויים
</div>
</h3>
</div>



</div>

<nav aria-label="breadcrumb" dir="rtl">
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-dark">ראשי</a></li>
  <li class="breadcrumb-item"><a href="SettingsDashboard.php" class="text-dark">הגדרות</a></li>
  <li class="breadcrumb-item active" aria-current="page">הגדרות מנויים</li>
  </ol>  
</nav>    



	
	

<div class="row" dir="rtl">

<?php include("SettingsInc/RightCards.php"); ?>
	
	<div class="col-md-10 col-sm-12 order-md-2">

	
	
	
	

<div class="tab-content">
  <div class="tab-pane fade show active text-right" role="tabpanel" id="generalsettings">
  <div class="card spacebottom">
			<div class="card-header text-right"><strong>פרטי הנהלת חשבונות</strong></div>    
 			<div class="card-body">  
 			    <form action="GeneralSettingsPage"  class="ajax-form clearfix" dir="rtl" autocomplete="off">
                <input type="hidden" name="CompanyNum" value="1">
 				<div class="form-group">
                <label>שם העסק</label>
                <input type="text" class="form-control" name="CompanyName" id="CompanyName" value="<?php echo @$Supplier->CompanyName ?>" readonly>
                </div>
                
                <div class="form-group">
                <label>סוג העסק</label>
                <select name="BusinessType" class="form-control" disabled>
                <option value="" <?php if (@$Supplier->BusinessType == '') {echo "selected";} ?>>בחר סוג עסק</option>
                <option value="2" <?php if (@$Supplier->BusinessType == '2') {echo "selected";} ?>>עוסק מורשה :: ע.מ</option>
                <option value="3" <?php if (@$Supplier->BusinessType == '3') {echo "selected";} ?>>חברה פרטית :: ח.פ</option>
                <option value="4" <?php if (@$Supplier->BusinessType == '4') {echo "selected";} ?>>חברה ציבורית :: ח.צ</option>
                <option value="5" <?php if (@$Supplier->BusinessType == '5') {echo "selected";} ?>>עוסק פטור :: ע.פ</option>
                <option value="6" <?php if (@$Supplier->BusinessType == '6') {echo "selected";} ?>>מלכ"ר</option>
                <option value="7" <?php if (@$Supplier->BusinessType == '7') {echo "selected";} ?>>משרד ממשלתי</option>
              </select>
                </div>
                
                <div class="form-group">
                <label>מספר עוסק</label>
                <input type="text" class="form-control" name="CompanyId" id="CompanyId" value="<?php echo @$Supplier->CompanyId ?>" readonly onkeypress='validate(event)'>
                </div>
                
                <div class="form-group">
                <label>% ניכוי מס במקור</label>
                <input type="number" class="form-control" name="NikuyMsBamakor" id="NikuyMsBamakor" value="<?php echo @$Supplier->NikuyMsBamakor ?>" onkeypress='validate(event)'>
                </div>
                
                <div class="form-group">
                <label>תוקף ניכוי מס במקור</label>
                <input type="date" class="form-control" name="NikuyMsBamakorDate" id="NikuyMsBamakorDate" value="<?php echo @$Supplier->NikuyMsBamakorDate ?>">
                </div>
                
				<hr>	
				
				<div class="form-group">
				<button type="submit" class="btn btn-success btn-lg">עדכן</button>	
				</div>
				</div>
				</form>
				</div></div>
    
    

</div>                               
 
</div>  
</div>
     
	</div> 
	
        
<script>
	
$(document).ready(function(){
  var windowWidth = $(window).width();
  if(windowWidth <= 1024) //for iPad & smaller devices
     $('#MenuSettingSystem').removeClass('show');
	 $('html,body').scrollTop(0);
});
	</script>

        
<?php else: ?>
<?php redirect_to('index.php'); ?>
<?php endif ?>


<?php endif ?>



<?php require_once '../app/views/footernew.php'; ?>