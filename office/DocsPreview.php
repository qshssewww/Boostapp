<?php require_once '../app/init.php'; 

if (Auth::guest()) redirect_to(App::url());

if (Auth::check()):
if (Auth::userCan('39')):

$DocId = $_REQUEST['Id'];

include('DocsInc/DocsParameters.php');
$pageTitle = 'תצוגה מקדימה :: '.$TypeTitle;
require_once '../app/views/headernew.php';
$DocInfo = DB::table('docs')->where('id' ,'=', $DocId)->first();

$ClientName = $DocInfo->Company;

if ($ClientName==''){
$ClientName = 'לקוח מזדמן';	
}

else {
$ClientName = $ClientName;	
}

?>


<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet" />
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/i18n/he.js"></script>

<link href="assets/css/fixstyle.css" rel="stylesheet">


<div class="col-md-12 col-sm-12">

<!-- <div class="row pb-3">

<div class="col-md-6 col-sm-12 order-md-1">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<?php //echo $DateTitleHeader; ?>
</h3>
</div>

<div class="col-md-6 col-sm-12 order-md-4">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
<i class="fas fa-file-alt fa-fw"></i><?php //echo $TypeTitle; ?>
</div>
</h3>
</div>




</div>





<nav aria-label="breadcrumb" dir="rtl">
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-info">ראשי</a></li>
  <li class="breadcrumb-item"><a href="DocsList.php?Types=<?php //echo $Types; ?>" class="text-info"><?php //echo $TypeList; ?></a></li>
  <li class="breadcrumb-item active" aria-current="page"><?php //echo $TypeNew; ?></li>
  </ol>  
</nav>     -->



   <div class="card spacebottom" style="margin-top: 20px;">
  <div class="card-header text-right" dir="rtl">
    <strong><?php echo $TypeTitleSingle;?> מס' <?php echo $DocInfo->TypeNumber; ?> </strong> <span style="font-size:13px;">הונפקה עבור: <?php echo $ClientName; ?></span>

  </div>
  
  <div class="collapse show">
  <div class="card-body text-right" dir="rtl">
      

      <?php if ($DocInfo->Status=='0'){ ?>
<a href="javascript:void(0);" class="btn btn-dark" onclick="TINY.box.show({iframe:'PDF/Docs.php?DocType=<?php echo $DocInfo->TypeDoc; ?>&DocId=<?php echo $DocInfo->TypeNumber; ?>',boxid:'frameless',width:750,height:470,fixed:false,maskid:'bluemask',maskopacity:40,closejs:function(){}})">
תצוגה מקדימה</a> 

<a href="javascript:void(0);" onclick="TINY.box.show({iframe:'PDF/DocsClose.php?DocType=<?php echo $DocInfo->TypeDoc; ?>&DocId=<?php echo $DocInfo->TypeNumber; ?>',boxid:'frameless',width:750,height:470,fixed:false,maskid:'bluemask',maskopacity:40,closejs:function(){dynamicJS()}})" class="btn btn-primary">הדפס ושמור <?php echo $TypeTitleSingle;?></a> 

<?php } else { ?>

<a href="javascript:void(0);" onclick="TINY.box.show({iframe:'PDF/DocsCopy.php?DocType=<?php echo $DocInfo->TypeDoc; ?>&DocId=<?php echo $DocInfo->TypeNumber; ?>',boxid:'frameless',width:750,height:470,fixed:false,maskid:'bluemask',maskopacity:40,closejs:function(){}})" class="btn btn-dark">הדפס העתק <?php echo $TypeTitleSingle;?></a> 

<?php } ?>

<a href="Docs.php?Types=<?php echo $Types; ?>" class="btn btn-light">צור <?php echo $TypeTitleSingle;?> חדשה</a>
            
                        
  </div>
	</div></div>


</div>


<script>
function dynamicJS()
{

    location.reload();

	
}
</script>

<?php else: ?>
<?php redirect_to('../index.php'); ?>
<?php endif ?>


<?php endif ?>

<?php if (Auth::guest()): ?>

<?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>