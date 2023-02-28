<?php 
require_once '../app/init.php'; 

$pageTitle = lang('reports_log_chat');
require_once '../app/views/headernew.php';
?>


<?php if (Auth::check()):?>
<?php if (Auth::userCan('116')): ?>
<?php


CreateLogMovement(lang('log_of_chat'), '0');


if (!isset($_REQUEST["month"])) $_REQUEST["month"] = date("m");
if (!isset($_REQUEST["year"])) $_REQUEST["year"] = date("Y");

if (@$_REQUEST['Dates']==''){

$cMonth = $_REQUEST["month"];
$cYear = $_REQUEST["year"];
$Dates = $_REQUEST["year"].'-'.$_REQUEST["month"];
}

else {

$Dates = $_REQUEST['Dates'];
$cMonth = with(new DateTime($_REQUEST['Dates']))->format('m');
$cYear = with(new DateTime($_REQUEST['Dates']))->format('Y');	
	
}
 
$prev_year = $cYear;
$next_year = $cYear;
$prev_month = $cMonth-1;
$next_month = $cMonth+1;
 
if ($prev_month == 0 ) {
    $prev_month = 12;
    $prev_year = $cYear - 1;
}
if ($next_month == 13 ) {
    $next_month = 1;
    $next_year = $cYear + 1;
}

$StartDate = $cYear.'-'.$cMonth.'-01';
$EndDate = $next_year.'-'.$next_month.'-01';

$Items = DB::table('chat')->where('CompanyNum','=',$CompanyNum)->where('UserId', '!=', '0')->where('ToUserId', '!=', '0')->orderBy('id', 'DESC')->whereBetween('Dates', array($StartDate, $EndDate))->get();
$resultcount = count($Items);

//Log	
$LogUserId = Auth::user()->id;
$LogUserName = Auth::user()->display_name;
$LogDateTime = date('Y-m-d G:i:s');
$LogContent = "<i class='fa fa-comment-alt' aria-hidden='true'></i> ".$LogUserName."<a href='LogList.php' target='_blank'>".lang('chat_log_logchat')."</a>";
//DB::table('log')->insert(array('UserId' => $LogUserId, 'Text' => $LogContent, 'Dates' => $LogDateTime, 'ClientId' => '0'));
//Log	

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

<script type="text/javascript" charset="utf-8">     

function myFunction(value)
{

window.location.href = 'LogChat.php?Dates='+value;

}

</script>



<script>
$(document).ready(function(){
	
	 $('#categories tfoot th span').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="'+title+'" style="width:90%;" class="form-control"  />' );

    } );
	

	
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
	         //fixedHeader: {headerOffset: 50},

	     //  bStateSave:true,
		   // serverSide: true,
	        pageLength: 10,
	      dom: '<<"d-flex justify-content-between w-100 mb-10" <rf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
		//info: true,
	   
	    buttons: [
        <?php if (Auth::userCan('98')): ?>
		//{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},
			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: '<?php echo lang('reports_log_chat') ?>', className: 'btn btn-dark'},
			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: '<?php echo lang('reports_log_chat') ?>' , className: 'btn btn-dark'},
           // 'pdfHtml5'
		<?php endif ?>
			
        ],
	   
	   		ajax: { url: 'LogChatPost.php?<?php echo @$_SERVER['QUERY_STRING']; ?>', },

		order: [[0, 'DESC']]

	   	 	   
        } );
		
		    var table = $('#categories').DataTable();
			table.columns().every( function () {
            var that = this;

		 $( 'input', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );		
				
				
				
    } );
	
	
	
});


</script>


<link href="assets/css/fixstyle.css" rel="stylesheet">

<div class="col-md-12 col-sm-12">
<!-- <div class="row">



<div class="col-md-6 col-sm-12 order-md-1">
<h3 class="page-header headertitlemain"  style="height:54px;">
<?php //echo $DateTitleHeader; ?>
</h3>
</div>

<div class="col-md-6 col-sm-12 order-md-3">
<h3 class="page-header headertitlemain"  style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
<i class="fas fa-comment-alt"></i> לוג צא׳ט <span style="color:#48AD42;"><?php //echo $resultcount; ?> </span>
</div>
</h3>
</div>


</div>

<nav aria-label="breadcrumb" >
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-dark">ראשי</a></li>
  <li class="breadcrumb-item active">הגדרות</li>
  <li class="breadcrumb-item active">לוג צא׳ט</li>
  </ol>  
</nav>     -->


<div class="row">

<?php include("ReportsInc/SideMenu.php"); ?>

<div class="col-md-10 col-sm-12">	


    <div class="card spacebottom">
    <div class="card-header text-start" >
    <i class="fas fa-comment-alt"></i> <b><?php echo lang('reports_log_chat') ?></b>
 	</div>    
  	<div class="card-body">       
                    
<div class="row">
<div class="col-md-9 col-sm-12 d-flex justify-content-start flex-wrap">
<span class="mie-6 mb-6" > <a href="<?php echo $_SERVER["PHP_SELF"] . "?month=". sprintf('%02d', $prev_month) . "&year=" . $prev_year; ?>"  class="btn btn-light"><?php echo lang('to_prev_month') ?></a></span>

<span class="mie-6 mb-6"> <a href="<?php echo $_SERVER["PHP_SELF"] . "?month=". sprintf('%02d', $next_month) . "&year=" . $next_year; ?>"  class="btn btn-light"><?php echo lang('to_next_month') ?></a></span>

                            
<span class="mie-6 mb-6"> <a href="<?php echo $_SERVER["PHP_SELF"]; ?>"  class="btn btn-dark"><?php echo lang('this_month') ?></a></span> 
</div>
<div class="col-md-3 col-sm-12 d-flex justify-content-end">
<span><input type="month" class="form-control" id="CDate" value="<?php echo $Dates;?>" onChange="myFunction(this.value);"></span>  
</div>
	</div>
<hr>


<table class="table table-bordered table-hover dt-responsive text-start display wrap" id="categories"  cellspacing="0" width="100%">
		<thead class="thead-dark">
			<tr>
				<th class="text-start">#</th>
				<th class="text-start"><?php echo lang('from_user') ?></th>
				<th class="text-start"><?php echo lang('to_user') ?></th>
				<th class="text-start"><?php echo lang('date') ?></th>
				<th class="text-start"><?php echo lang('hour') ?></th>
				<th class="text-start"><?php echo lang('message_content') ?></th>
				<th class="text-start"><?php echo lang('message_recieved') ?></th>
                <th class="text-start lastborder"><?php echo lang('message_read') ?></th>
			</tr>
		</thead>
		<tbody>
  

        </tbody>
		<tfoot>
            <tr>
                <th><span>#</span></th>
                <th><span><?php echo lang('from_user') ?></span></th>
				<th><span><?php echo lang('to_user') ?></span></th>
				<th><span><?php echo lang('date') ?></span></th>
				<th><span><?php echo lang('hour') ?></span></th>
				<th><span><?php echo lang('message_content') ?></span></th>
				<th><span><?php echo lang('message_recieved') ?></span></th>
                <th class="lastborder"><span><?php echo lang('message_read') ?></span></th>
            </tr>
        </tfoot>

	
        </table> 
    
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