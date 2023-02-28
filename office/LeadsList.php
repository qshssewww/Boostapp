<?php require_once '../app/init.php'; ?>

<?php echo View::make('headernew')->render() ?>


<?php if (Auth::check()):?>
<?php if (Auth::userCan('45') && @$_REQUEST['Act']=='0' || Auth::userCan('46') && @$_REQUEST['Act']=='1' || Auth::userCan('47') && @$_REQUEST['Act']=='2'): ?>
<?php
$CompanyNum = Auth::user()->CompanyNum;

CreateLogMovement('נכנס לניהול לידים', '0');

?>


<link href="<?php echo App::url('CDN/datatables/dataTables.bootstrap4.min.css') ?>" rel="stylesheet">
<link href="<?php echo App::url('CDN/datatables/buttons.bootstrap4.min.css') ?>" rel="stylesheet">

<link href="<?php echo App::url('CDN/datatables/responsive.bootstrap4.min.css') ?>" rel="stylesheet">
<link href="<?php echo App::url('CDN/datatables/fixedHeader.dataTables.min.css') ?>" rel="stylesheet">
<link href="<?php echo App::url('CDN/datatables/responsive.dataTables.min.css') ?>" rel="stylesheet">



<script src="<?php echo App::url('CDN/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?php echo App::url('CDN/datatables/dataTables.buttons.min.js') ?>"></script>

<script src="<?php echo App::url('CDN/datatables/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?php echo App::url('CDN/datatables/dataTables.responsive.js') ?>"></script>
<script src="<?php echo App::url('CDN/datatables/responsive.bootstrap4.min.js') ?>"></script>
<script src="//cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
<script src="<?php echo App::url('CDN/datatables/jszip.min.js') ?>"></script>
<script src="<?php echo App::url('CDN/datatables/pdfmake.min.js') ?>"></script>
<script src="<?php echo App::url('CDN/datatables/vfs_fonts.js') ?>"></script>
<script src="<?php echo App::url('CDN/datatables/buttons.html5.min.js') ?>"></script>

<!--<script src="--><?php //echo App::url('CDN/datatables/moment.min.js') ?><!--"></script>-->
<script src="<?php echo App::url('CDN/datatables/datetime-moment.js') ?>"></script>

<script src="<?php echo App::url('CDN/datatables/dataTables.fixedHeader.min.js') ?>"></script>


<script>



</script>

<link href="assets/css/fixstyle.css" rel="stylesheet">
<div class="col-md-12 col-sm-12">
<div class="row">

<div class="col-md-5 col-sm-12 order-md-1">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<?php echo $DateTitleHeader; ?>
</h3>
</div>

<div class="col-md-5 col-sm-12 order-md-3">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
<i class="fas fa-users"></i> ניהול מתעניינים <span style="color:#48AD42;"><?php echo @$resultcount; ?> </span>
</div>
</h3>
</div>

<div class="col-md-2 col-sm-12 order-md-2 pb-1">    
</div>














</div>

<nav aria-label="breadcrumb" dir="rtl">
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-dark">ראשי</a></li>
  <li class="breadcrumb-item active">ניהול מתעניינים</li>
  </ol>  
</nav>    

<div class="row">
<div class="col-md-12 col-sm-12">	

    <div class="card spacebottom">
    <div class="card-header text-right" dir="rtl">
    <i class="fas fa-th"></i> <b>ניהול מתענינים</b>
 	</div>    
  	<div class="card-body">       

<table class="table table-bordered table-hover dt-responsive text-right display wrap" id="categories" dir="rtl" cellspacing="0" width="100%">
		<thead class="thead-dark">
			<tr>
				<th class="text-right">#</th>
                <th class="text-right">שם לקוח</th>
				<th class="text-right">ת.ז</th>
				<th class="text-right">טלפון</th>
				<th class="text-right">דוא"ל</th>
                <th class="text-right">ת.הצטרפות</th>
                <th class="text-right" width="20%">מנוי</th>
                <th class="text-right">שיעור אחרון</th>
                <th class="text-right ">סטטוס</th>
                <th class="text-right" lastborder>סניף</th>
			</tr>
		</thead>
		<tbody>
              
        </tbody>
	
	
	<tfoot>
            <tr>
                <th><span>מספר לקוח</span></th>
                
                <th><span>שם לקוח</span></th>
				<th><span>ת.ז</span></th>
				<th><span>טלפון</span></th>
				<th><span>דואל</span></th>
                <th><span>ת.הצטרפות</span></th>
                <th><span>מנוי</span></th>
                <th><span>שיעור אחרון</span></th>
                <th><span>סטטוס</span></th>
                <th class="lastborder"><span>סניף</span></th>
            </tr>
        </tfoot>
	
        </table> 
		</div></div>
    
	</div> 
</div>

</div>


<?php include('InfoPopUpInc.php'); ?>


<?php else: ?>
<?php redirect_to('../index.php'); ?>
<?php endif ?>


<?php endif ?>

<?php if (Auth::guest()): ?>

<?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>