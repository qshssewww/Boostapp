<?php require_once '../app/init.php'; ?>

<?php if (Auth::guest()): redirect_to('index.php'); endif ?>



<?php if (Auth::check()):?>

<?php if (Auth::userCan('116')): 

$pageTitle = lang('sms_report_title');
require_once '../app/views/headernew.php';
?>



<?php

$CompanyNum = Auth::user()->CompanyNum;



?>



<?php $BusinessSettings = DB::table('settings')->where('CompanyNum',  $CompanyNum)->first(); ?>





<?php






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

// $StartDate = $cYear.'-'.$cMonth.'-01';

// $EndDate = $cYear.'-'.$cMonth.'-'.date('t',strtotime($StartDate));
if(!isset($_GET['start']) && !isset($_GET['end'])) {
    $StartDate = $cYear.'/'.$cMonth.'/01';
    $EndDate = $cYear.'/'.$cMonth.'/30';
}
else {
    $StartDate = $_GET["start"];
    $EndDate = $_GET["end"];
}








?>

















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

var direction = false ;
$(document).ready(function(){

	if( $("html").attr("dir") == 'rtl' ){
        direction = true ;
    }

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

	        "scrollY":        "450px",

            "scrollCollapse": true,

            "paging":         true,



	     //  bStateSave:true,

		   // serverSide: true,

	        pageLength: 100,

	      dom: '<<"d-flex justify-content-between w-100 mb-10" <rf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',

		//info: true,

	    buttons: [

         <?php if (Auth::userCan('98')): ?>     

		//{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},

			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: '<?php echo lang('sms_message_report') ?>', className: 'btn btn-dark',exportOptions: {}},

			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: '<?php echo lang('sms_message_report') ?>', className: 'btn btn-dark',exportOptions: {}},

           // 'pdfHtml5'

	<?php endif ?>	

			

        ],

	//	order: [[0, 'DESC']]



	   	 	   

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

	

	    $('#categories tbody').on( 'click', 'tr', function () {

        if ( $(this).hasClass('selected') ) {

            $(this).removeClass('selected');

        }

        else {

            categoriesDataTable.$('tr.selected').removeClass('selected');

            $(this).addClass('selected');

        }

    } );

	



	

});





</script>



<link href="assets/css/fixstyle.css" rel="stylesheet">

<script type="text/javascript" charset="utf-8">     



function myFunction(value)

{

    window.location.href = 'ReportSms.php?Dates='+value;

}
function dateRange(start, end) {
    var StartDate = start.format("YYYY/MM/DD");
    var EndDate = end.format("YYYY/MM/DD");
    console.log(StartDate + " " + EndDate);

    window.location.href = 'ReportSms.php?start=' + StartDate + '&end=' + EndDate;
}



</script>


<!--<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>-->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/he.js"></script>-->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<!-- <div class="row pb-3">



<div class="col-md-6 col-sm-12 order-md-1">

<h3 class="page-header headertitlemain"  style="height:54px;">

<?php //echo $DateTitleHeader; ?>

</h3>

</div>



<div class="col-md-6 col-sm-12 order-md-4">

<h3 class="page-header headertitlemain"  style="height:54px;">

<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">

<i class="fas fa-chart-pie fa-fw"></i> דוח שליחות SMS בתאריכים <span style="color:#0074A4;"><?php //echo $EndDate. ' - '. $StartDate; ?></span>

</div>

</h3>

</div> 

</div> -->

<?php CreateLogMovement('fas fa-chart-pie',lang('sms_report_month ').$monthNames[$cMonth-1].' '.$cYear,'0'); ?>







<div class="row px-0 mx-0"  >

<div class="col-12 mx-0 px-0" >



<div class="row">



<?php include("ReportsInc/SideMenu.php"); ?>



<div class="col-md-10 col-sm-12">	

    <div class="tab-content">

                        

                        

                        

                       

							

							

							

<div class="tab-pane fade show active text-start" role="tabpanel" id="user-overview">

<div class="card spacebottom">

    <div class="card-header text-start" ><i class="fas fa-chart-pie fa-fw"></i><strong> <?php echo lang('reports_sms_title') ?>:  <span style="color:#0074A4;"><?php echo $EndDate. ' - ' .$StartDate ?></span></strong></div>    

<div class="card-body">       

                    

    

                      

<div class="row">
 




<div class="col-md-6 col-sm-12 d-flex justify-content-start flex-wrap spacebottom mx-0 " >

<span class="mie-6 mb-6" > <a href="<?php echo $_SERVER["PHP_SELF"] . "?month=". sprintf('%02d', $prev_month) . "&year=" . $prev_year; ?>"  class="btn btn-light"><?php echo lang('to_prev_month') ?></a></span>



<span class="mie-6 mb-6"> <a href="<?php echo $_SERVER["PHP_SELF"] . "?month=". sprintf('%02d', $next_month) . "&year=" . $next_year; ?>"  class="btn btn-light"><?php echo lang('to_next_month') ?></a></span>



                            

<span class="mie-6 mb-6" > <a href="<?php echo $_SERVER["PHP_SELF"] . "?month=". sprintf('%02d', date('m')) . "&year=" . date('Y'); ?>"  class="btn btn-dark"><?php echo lang('this_month') ?></a></span>

	</div>

<div class="col-md-6 col-sm-12 d-flex justify-content-end " >

        <span>

            <!-- <input type="month" class="form-control" id="CDate" value="<?php //echo $Dates;?>" onChange="myFunction(this.value);"> -->
            <input type="text" name="daterange" value="<?php echo $StartDate.'-'.$EndDate ?>"/></span>

        </span>  



	</div>

 

</div>



<hr>

     

<div class="row" >

<table class="table table-hover dt-responsive text-start display wrap" id="categories"  cellspacing="0" width="100%">



<thead>

          <tr class="bg-dark text-white">

            <th style="text-align:start;">#</th>

            <th style="text-align:start;"><?php echo lang('to_number') ?></th>

            <th style="text-align:start;"><?php echo lang('client') ?></th>

            <th style="text-align:start;"><?php echo lang('table_by') ?></th>

            <th style="text-align:start;"><?php echo lang('date') ?></th>

            <th style="text-align:start;"><?php echo lang('hour') ?></th>

            <th style="text-align:start;"><?php echo lang('message_price') ?></th>

            <th style="text-align:start;"><?php echo lang('table_message_count') ?></th>

            <th style="text-align:start;"><?php echo lang('cost') ?></th>

          </tr>



</thead>



<tbody>

<?php

$SmsLogList = DB::table('appnotification')->where('CompanyNum', '=', $CompanyNum)->where('Type', '=', '1')->where('System', '=', '0')->whereBetween('Date', array($StartDate, $EndDate))->orderBy('Date', 'DESC')->orderBy('Time', 'DESC')->get();

$SmsLogListSMSSumPrice = DB::table('appnotification')->where('Type', '=', '1')->where('System', '=', '0')->where('CompanyNum', '=', $CompanyNum)->where('Status', '!=', '2')->whereBetween('Date', array($StartDate, $EndDate))->sum('SMSSumPrice');

$sumSMS = 0;

foreach ($SmsLogList as $SmsLog) {

@$UsersDB = DB::table('users')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', @$SmsLog->UserId)->first();

@$ClietsDB = DB::table('client')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', @$SmsLog->ClientId)->first();

$ClientInfoMsgLog = json_decode(@$SmsLog->ClientJson);

$sumSMS += (int) $SmsLog->Count;

?>

<tr>

<td><?php echo $SmsLog->id; ?></td>

<td><?php echo @$ClientInfoMsgLog->data['0']->Mobile; ?></td>

<td ><?php echo @$ClietsDB->CompanyName; ?> :: <?php echo @$ClietsDB->id; ?></td>

<td><?php echo @$UsersDB->display_name; ?> :: <?php echo @$UsersDB->id; ?></td>

<td><?php echo with(new DateTime($SmsLog->Date))->format('d/m/Y'); ?></td>

<td><?php echo with(new DateTime($SmsLog->Time))->format('H:i:s'); ?></td>

<?php if ($SmsLog->Status != '2') { ?>

<td><?php echo $SmsLog->SMSPrice; ?> ₪</td>

<?php } else {echo '<td>נכשל</td>';} ?>

<?php if ($SmsLog->Status != '2') { ?>

<td><?php echo $SmsLog->Count; ?></td>

<?php } else {echo '<td>נכשל</td>';} ?>

<?php if ($SmsLog->Status != '2') { ?>

<td><?php echo $SmsLog->SMSSumPrice; ?> ₪</td>

<?php } else {echo '<td>נכשל</td>';} ?>

</tr>

<?php

	}

	?>

</tbody>



<?php if (@$SmsLogListSMSSumPrice != '') { ?>

<tfoot>

<tr>

<td></td>

<td></td>

<td></td>

<td></td>

<td></td>

<td></td>

<td></td>

<td><strong><?php echo $sumSMS; ?></strong></td>

<td><strong><?php echo $SmsLogListSMSSumPrice; ?> ₪</strong></td>

</tr>

</tfoot>

<?php } ?>

</table>


<script>
$(function() {
  $('input[name="daterange"]').daterangepicker({
    isRTL: direction,
    langauge: 'he',
    opens: 'left',
    startDate: moment('<?php echo $StartDate ?>').format('YYYY/MM/DD'),
    endDate: moment('<?php echo $EndDate ?>').format('YYYY/MM/DD'),
    locale: {
        format: 'YYYY/MM/DD',
        "applyLabel": "<?php echo lang('approval') ?>",
        "cancelLabel": "<?php echo lang('cancel') ?>",
    }
  },function(start, end, label) {
      dateRange(start, end);
    })
  });

</script>


</div>



</div></div></div></div></div></div></div>

    

    

    

    

<?php else: ?>

<?php //redirect_to('index.php');  ?>

<?php ErrorPage (lang('permission_blocked'), lang('no_page_persmission')); ?>

<?php endif ?>





<?php endif ?>





<?php require_once '../app/views/footernew.php'; ?>