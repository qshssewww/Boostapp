<?php 
require_once '../app/init.php';
$CompanyNum = Auth::user()->CompanyNum;
$PageInfo = DB::table('dynamicforms')->where('CompanyNum', $CompanyNum)->where('id', $_GET['u'])->first();
if (empty($_GET['u']) || empty($PageInfo)) redirect_to(App::url());
$pageTitle = "טופס :: ".$PageInfo->name;
require_once '../app/views/headernew.php';


if (Auth::check()):
if (Auth::userCan('152')):

$Pages = DB::table('dynamicforms_answers')->where('CompanyNum', $CompanyNum)->where('FormId', $_GET['u'])->where('AnswerStatus', '0')->orderBy('created', 'DESC')->get();
$resultcount = count($Pages);

CreateLogMovement(
'נכנס לצפיה בחתימות עבור טופס דינאמי <u>'.$PageInfo->name.'</u>  גרסה #'.$PageInfo->GroupNumber,
'0');

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
			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: 'טופס <?php echo $PageInfo->name; ?>', className: 'btn btn-dark'},
			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: 'טופס <?php echo $PageInfo->name; ?>' , className: 'btn btn-dark'},
           // 'pdfHtml5'
		
			
        ],
	//	order: [[0, 'DESC']]

	   	 	   
        } );
		

	
});


</script>

<link href="assets/css/fixstyle.css?<?php echo date("YmdHis") ?>" rel="stylesheet">
<div class="col-md-12 col-sm-12">
<!-- <div class="row"> -->



<!-- <div class="col-md-5 col-sm-12 order-md-1">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<?php //echo $DateTitleHeader; ?>
</h3>
</div>

<div class="col-md-5 col-sm-12 order-md-4">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
<i class="fab fa-wpforms fa-fw"></i> טופס :: <?php //echo $PageInfo->name; ?> <span style="color:#0074A4;">(<?php echo $resultcount; ?>)</span>
</div>
</h3>
</div> -->

<!-- <div class="col-md-2 col-sm-12 order-md-3 pb-1">
    
<a href="DynamicForms.php" class="btn btn-primary text-white btn-block" name="Items"  dir="rtl"><i class="fab fa-wpforms fa-fw"></i> ניהול טפסים דינאמיים</a>
   
</div>
    

</div> -->

<!-- <nav aria-label="breadcrumb" dir="rtl">
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-dark">ראשי</a></li>
  <li class="breadcrumb-item"><a href="SettingsDashboard.php" class="text-dark">הגדרות</a></li>
  <li class="breadcrumb-item"><a href="DynamicForms.php" class="text-dark">ניהול טפסים</a></li>	  
  <li class="breadcrumb-item active">טופס :: <?php //echo $PageInfo->name; ?> <span style="color:#0074A4;">(<?php echo $resultcount; ?>)</span></li>
  </ol>  
</nav>     -->


<div class="row">
<?php include_once "SettingsInc/RightCards.php"; ?>

<div class="col-md-10 col-sm-12 order-md-1">	

    <div class="card spacebottom">
    <div class="card-header text-right" dir="rtl">
    <i class="fab fa-wpforms"></i> <b>טופס: <?php echo $PageInfo->name; ?> גרסה: <span style="color:#0074A4;"><?php echo $PageInfo->GroupNumber; ?> </span></b>
 	</div>    
  	<div class="card-body">       

<table class="table table-bordered table-hover dt-responsive text-right display wrap" id="categories" dir="rtl" cellspacing="0" width="100%">
		<thead class="thead-dark">
			<tr>
				<th class="text-right" width="20px" style="text-align: right;">#</th>
                <th class="text-right" width="80px;" style="text-align: right;">שם לקוח</th>
				<th class="text-right" width="80px;" style="text-align: right;">סניף</th>
                <th class="text-right" width="20px;" style="text-align: right;">תאריך חתימה</th>
                <th class="text-right" width="80px;" style="text-align: right;">תצוגת PDF</th>
				<th class="text-right" width="80px;" style="text-align: right;">תוקף</th>
			</tr>
		</thead>
		<tbody>
        
        <?php 
		
$i = '1';

foreach ($Pages as $Page) {

$ClientInfo = DB::table('client')->where('CompanyNum', $CompanyNum)->where('id', $Page->ClientId)->first();                           
$BarndSelect = @$Page->Brands;
if ($BarndSelect=='' || $BarndSelect=='0'){
$BrandsName = 'סניף ראשי';    
}
else {
$BrandInfo = DB::table('brands')->where('id', $BarndSelect)->where('FinalCompanynum', $CompanyNum)->first();

$BrandsName = @$BrandInfo->BrandName;
   
}
?>
        <tr>
        <td><?php echo $i; ?></td>      
        <td><a href="ClientProfile.php?u=<?php echo $ClientInfo->id; ?>"><span class="text-primary"><?php echo $ClientInfo->CompanyName; ?></span></a></td>
		<td><?php echo $BrandsName; ?></td>		
        <td><?php echo with(new DateTime($Page->created))->format('d/m/Y H:i'); ?></td>
        <td><a href="javascript:void(0);" onclick="TINY.box.show({iframe:'PDF/FormsPDF.php?id=<?php echo $Page->id; ?>',boxid:'frameless',width:750,height:470,fixed:false,maskid:'bluemask',maskopacity:40,closejs:function(){}})"><span class="text-primary">לחץ לצפיה</span></a></td>
		<td><?php if ($Page->VaildDate!='' && $Page->ActStatus!='2' && $Page->AnswerStatus=='0' || $Page->VaildDate!='' && $Page->ActStatus=='0' && $Page->AnswerStatus=='0') echo with(new DateTime($Page->VaildDate))->format('d/m/Y'); ?></td>	
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


<?php endif ?>

<?php if (Auth::guest()): ?>

<?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>