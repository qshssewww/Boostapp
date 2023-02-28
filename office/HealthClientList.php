<?php 
require_once '../app/init.php';

if (Auth::check()):
    $CompanyNum = Auth::user()->CompanyNum;
    if (empty($_GET['u'])) redirect_to(App::url());
    $PageInfo = DB::table('healthforms')->where('CompanyNum', $CompanyNum)->where('id', $_GET['u'])->first();
    if(!$PageInfo) 
        redirect_to(App::url());
    $pageTitle = lang('send_health_form').': '. $PageInfo->name;
    require_once '../app/views/headernew.php';

    $Pages = DB::table('healthforms_answers')->where('CompanyNum', $CompanyNum)->where('FormId', $_GET['u'])->orderBy('created', 'DESC')->get();
    $resultcount = count($Pages);
?>



<link href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap4.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/buttons/1.5.1/css/buttons.bootstrap4.min.css" rel="stylesheet">

<link href="https://cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap4.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/fixedheader/3.1.3/css/fixedHeader.dataTables.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/responsive/2.2.1/css/responsive.dataTables.min.css" rel="stylesheet">



<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="//cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js"></script>

<script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.1/js/responsive.bootstrap4.min.js"></script>

<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js"></script>

<!--<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>-->
<script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js"></script>

<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet" />
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/i18n/he.js"></script>

<script src="//cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js"></script>




<script>
$(document).ready(function(){
	

	$.fn.dataTable.moment = function ( format, locale ) {
    var types = $.fn.dataTable.ext.type;
 
    // Add type detection
    types.detect.unshift( function ( d ) {
        return moment( d, format, locale, true ).isValid() ?
            'moment-'+format :
            null;
    } );
 
    // Add sorting method - use an integer for the sorting
    types.order[ 'moment-'+format+'-pre' ] = function ( d ) {
        return moment( d, format, locale, true ).unix();
    };
};
	
	 $.fn.dataTable.moment( 'd/m/Y H:i' );
	
	
	var categoriesDataTable;
	BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
   var categoriesDataTable =   $('#categories').dataTable( {
            language: BeePOS.options.datatables,
			responsive: true,
		    processing: true,
	       // autoWidth: true,
	        //"scrollY":        "450px",
            //"scrollCollapse": true,
            "paging":         true,
	         fixedHeader: {
        headerOffset: 50
    },

	     //  bStateSave:true,
		   // serverSide: true,
	        pageLength: 100,
	      dom: "Bfrtip",
		//info: true,
	    buttons: [
		//{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},
			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: 'טופס הצהרת בריאות <?php echo $PageInfo->name; ?>', className: 'btn btn-dark'},
			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: 'טופס הצהרת בריאות <?php echo $PageInfo->name; ?>' , className: 'btn btn-dark'},
           // 'pdfHtml5'
		
			
        ],
	//	order: [[0, 'DESC']]

	   	 	   
        } );
		

	
});


</script>

<link href="assets/css/fixstyle.css" rel="stylesheet">
<div class="col-md-12 col-sm-12">
<!-- <div class="row">



<div class="col-md-5 col-sm-12 order-md-1">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<?php //echo $DateTitleHeader; ?>
</h3>
</div>

<div class="col-md-5 col-sm-12 order-md-4">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
<i class="fas fa-heartbeat fa-fw"></i> טופס הצהרת בריאות :: <?php //echo $PageInfo->name; ?> <span style="color:#0074A4;"><?php //echo $resultcount; ?> </span>
</div>
</h3>
</div>

<div class="col-md-2 col-sm-12 order-md-3 pb-1">
<?php //if (Auth::userCan('32')): ?>    
<a href="AppsSettings.php#appHealth" class="btn btn-primary text-white btn-block" name="Items"  dir="rtl"><i class="fas fa-plus-circle fa-fw"></i> הגדרות אפליקציה</a>
<?php //endif ?>    
</div>
    

</div>

<nav aria-label="breadcrumb" dir="rtl">
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-dark">ראשי</a></li>
  <li class="breadcrumb-item"><a href="SettingsDashboard.php" class="text-dark">הגדרות</a></li>
  <li class="breadcrumb-item"><a href="AppsSettings.php#appHealth" class="text-dark">הגדרות אפליקציה</a></li>
  <li class="breadcrumb-item active">טופס הצהרת בריאות :: <?php //echo $PageInfo->name; ?> <span style="color:#0074A4;"><?php //echo $resultcount; ?> </span></li>
  </ol>  
</nav>     -->


<div class="row">
<?php include("SettingsInc/RightCards.php"); ?>

<div class="col-md-10 col-sm-12 order-md-1">	

    <div class="card spacebottom">
    <div class="card-header text-right" dir="rtl">
    <i class="fas fa-heartbeat"></i> <b><?php echo lang('send_health_form').': '. $PageInfo->name.' '.lang('version_dynamic'); ?> <span style="color:#0074A4;"><?php echo $PageInfo->GroupNumber; ?> </span></b>
 	</div>    
  	<div class="card-body">       

<table class="table table-bordered table-hover dt-responsive text-right display wrap" id="categories" dir="rtl" cellspacing="0" width="100%">
		<thead class="thead-dark">
			<tr>
				<th class="text-right" width="20px" style="text-align: right;">#</th>
                <th class="text-right" width="80px;" style="text-align: right;">שם לקוח</th>
                <th class="text-right" width="20px;" style="text-align: right;">תאריך חתימה</th>
                <th class="text-right" width="80px;" style="text-align: right;">תצוגת PDF</th>
			</tr>
		</thead>
		<tbody>
        
        <?php 
		
$i = '1';

foreach ($Pages as $Page) {

$ClientInfo = DB::table('client')->where('CompanyNum', $CompanyNum)->where('id', $Page->ClientId)->first();                           
    
    
    
?>
        <tr>
        <td><?php echo $i; ?></td>      
        <td><a href="ClientProfile.php?u=<?php echo $ClientInfo->id; ?>"><span class="text-primary"><?php echo $ClientInfo->CompanyName; ?></span></a></td>
        <td><?php echo with(new DateTime($Page->created))->format('d/m/Y H:i'); ?></td>
        <td><a href="javascript:void(0);" onclick="TINY.box.show({iframe:'PDF/HealthPDF.php?id=<?php echo $Page->id; ?>',boxid:'frameless',width:750,height:470,fixed:false,maskid:'bluemask',maskopacity:40,closejs:function(){}})"><span class="text-primary">לחץ לצפיה</span></a></td>    
        </tr>
        
      
    <?php ++ $i;  } ?>
        

        </tbody>
	
	
        </table> 
		</div></div>
    
	</div> 
</div>

</div>



<?php else: ?>
<?php redirect_to('../index.php'); ?>
<?php endif ?>




<?php if (Auth::guest()): ?>

<?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>