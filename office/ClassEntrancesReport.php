<?php 
require_once '../app/init.php'; 
$pageTitle = lang('reports_entries');
require_once '../app/views/headernew.php';
?>


<?php if (Auth::check()):?>
<?php if (Auth::userCan('153')): ?>
<?php
CreateLogMovement(lang('report_entries_log'), '0');
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
<script src="//cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
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
<script src="<?php echo App::url('office/js/datatable/dataTables.checkboxes.min.js') ?>"></script>
<!--<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>-->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/he.js"></script>-->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<style>
.datepicker-dropdown {max-width: 300px;}
.datepicker {float: right}
.datepicker.dropdown-menu {right:auto}
</style>

<script>
$(document).ready(function(){
	 $('#categories tfoot th span').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="'+title+'" style="width:90%;" class="form-control"  />' );

    } );
    var table = $('#categories');

    $("#btnHidden").click(function() {
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                return data[11] == "999";
            }
        );
        table.draw();
    });

    $("#btnAll").click(function() {
        $.fn.dataTable.ext.search.pop();
        table.draw();
    });

    $('#checkbox1').change(function() {
        if($(this).is(":checked")) {
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    return data[11] == "999";
                }
            );
            table.draw();
        } else {
	        $.fn.dataTable.ext.search.pop();
            table.draw();
		   
        }
    });	

    var modal = $('#SendClientPush');
    var modalsClientIds = $('input[name="clientsIds"]', modal);
	
	
	BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
	var categoriesDataTable =   $('#categories').dataTable( {
            language: BeePOS.options.datatables,
			responsive: true,
		    processing: true,
            scrollY:        '100vh',
            scrollCollapse: true,
            paging:         false,

	      dom: '<<"d-flex justify-content-between w-100 mb-10" <rf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',

	    buttons: [
         <?php if (Auth::userCan('98')): ?>    
		//{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},
			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: '<?php echo lang('no_response_to_waitlist') ?>', className: 'btn btn-dark'},
			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: '<?php echo lang('no_response_to_waitlist') ?>', className: 'btn btn-dark'},
            {extend: 'print', text: '<?php echo lang('print') ?> <i class="fas fa-print" aria-hidden="true"></i>', className: 'btn btn-dark', customize: function ( win ) {
              // https://datatables.net/reference/button/print
             jQuery(win.document).ready(function(){
             $(win.document.body)
             .css( 'direction', 'rtl' )
             });                            
             }},
           // 'pdfHtml5'
		<?php endif ?>
		        {text: 'שלח הודעה <i class="fas fa-comments"></i>', className: 'btn btn-dark', action: function ( e, dt, node, config ) {
                            // rows_selected = table.column(0).checkboxes.selected();
                            var clientsIds = dt.column(0).checkboxes.selected().toArray();
                            if(!clientsIds.length) return alert('<?php echo lang('select_customers') ?>');
      
                            modalsClientIds.val(clientsIds.join(","));
                            modal.modal('show');

          }}, 	
        ],
       ajax:{
           url: 'ClassEntrancesReportPost.php',
           data: function(d) {
               d.SortItemText = $("#Items1").val();
           },
           method: 'POST'
       },
        "columnDefs": [
		    {
            'targets': 0,
            'checkboxes': {
               'selectRow': true
            }
         },
            {
                "targets": [ 11 ],
                "visible": false,
                "searchable": true
            },
		    ],
		order: [[1, 'asc']]

	   	 	   
        } );
		    var table = $('#categories').DataTable();  
			table.columns().every( function () {
            var that = this;
		 $( 'span input', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );		

    } );

    $('#categories tfoot tr').insertAfter($('#categories thead tr'));

    $('body').on('change','#Items1', function() {
        categoriesDataTable.DataTable().ajax.reload();
    });

    $('#table-filterType').on('change', function(){
        table.column('3').search(this.value).draw();
    });

});

</script>

<link href="assets/css/fixstyle.css" rel="stylesheet">
<div class="col-md-12 col-sm-12">
<!-- <div class="row">



<div class="col-md-4 col-sm-12 order-md-1">
<h3 class="page-header headertitlemain"  style="height:54px;">
<?php //echo $DateTitleHeader; ?>
</h3>
</div>

<div class="col-md-4 col-sm-12 order-md-4">
<h3 class="page-header headertitlemain"  style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
<i class="fas fa-sign-in-alt"></i> דוח כניסות</span>
</div>
</h3>
</div>


<div class="col-md-2 col-sm-12 order-md-2 pb-1">  

</div>    
    
<div class="col-md-2 col-sm-12 order-md-3">

</div>     

</div>

<nav aria-label="breadcrumb" >
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-dark">ראשי</a></li>
      <li class="breadcrumb-item"><a href="ReportsDash.php" class="text-dark">הגדרות</a></li>
  <li class="breadcrumb-item active">דוח כניסות</li>
  </ol>  
</nav>     -->


<div class="row">

<?php include("ReportsInc/SideMenu.php"); ?>

<div class="col-md-10 col-sm-12">	


    <div class="card spacebottom">
    <div class="card-header text-start" style="display: flex; justify-content: space-between;" >
        <div>
            <i class="fas fa-sign-in-alt"></i> <b><?php echo lang('reports_entries') ?></b>
        </div>
            <select name="Items1" id="Items1" class="d-inline-block form-control select2 form-control-sm" style="width:20%; float:left;"  data-placeholder="<?php echo lang('select_subscription') ?>"  >
                <option value="<?php echo lang('all') ?>">
                    <?php echo lang('all') ?>
                </option>
                <?php
                $companyNum = Auth::user()->CompanyNum;
                $items = DB::table('items')
                    ->where('CompanyNum', '=', $companyNum)->where('Status', '=', '0')
                    ->where('isPaymentForSingleClass', '=', 0)
                    ->where('Disabled', '=', 0)
                    ->where('Department', '!=', 4)
                    ->orderBy('Department', 'ASC')->get();
                foreach ($items as $item) {
                    ?>
                    <option value="<?php echo $item->id ?>"  data-name="<?php echo $item->ItemName; ?>" >
                        <?php echo $item->ItemName; ?>
                    </option>
                <?php } ?>
            </select>
 	</div>
  	<div class="card-body">       
                    
<div class="row">
<div class="col-md-3 col-sm-12 " >


	
	
</div>
<div class="col-md-9 col-sm-12 text-start" >
	
<?php echo lang('reports_entries_notice_week') ?>
	<hr>
              <div class="checkbox">
                  <label>
                  <input type="checkbox" class="pull-right" id="checkbox1"> <?php echo lang('reports_entries_notice_clients') ?> <i class="fas fa-info-circle" data-toggle="tooltip" data-placement="top" title="<?php echo lang('entries_report_notice') ?>"></i>
                  </label>
              </div>
    </div>
	</div>
    <hr>


<table class="table table-hover dt-responsive text-start display wrap" id="categories"  cellspacing="0" width="100%">
		<thead >
			<tr class="bg-dark text-white">
				<th class="text-start">#</th>
				<th class="text-start"><?php echo lang('client') ?></th>
                <th class="text-start"><?php echo lang('telephone') ?></th>
                <th class="text-start"><?php echo lang('week_eight') ?> </th>
				<th class="text-start"><?php echo lang('week_seven') ?> </th>
				<th class="text-start"><?php echo lang('week_six') ?> </th>
				<th class="text-start"><?php echo lang('week_five') ?> </th>
				<th class="text-start"><?php echo lang('week_four') ?> </th>
				<th class="text-start"><?php echo lang('week_three') ?> </th>
				<th class="text-start"><?php echo lang('week_two') ?> </th>
				<th class="text-start lastborder"><?php echo lang('last_week') ?></th>
				<th class="text-start lastborder"><?php echo lang('internal_single') ?></th>
			</tr>
            
          
            
            
		</thead>
		<tbody>
  

        </tbody>

<tfoot>
<tr class="bg-white text-black filterHeader">
	            <th></th>
                <th><span><?php echo lang('client') ?></span></th>
                <th><span><?php echo lang('telephone') ?></span></th>
				<th></th>
	            <th></th>
	            <th></th>
	            <th></th>
	            <th></th>
	            <th></th>
	            <th></th>
				<th></th>
	            <th></th>
</tr>      
    
</tfoot>
	
        </table> 
    
        </>
    </div>

	</div> 
</div>

</div>

<?php include('Reports/popupSendByClientId.php'); ?>
<style>
div#spinners
{
    display: table;
    width:100%;
    height: 100%;
    position: fixed;
    top: 0%;
    left: 0%;
    background:url(assets/img/Preloader_8.gif) no-repeat center rgba(255, 255, 255, .5);
    text-align:center;
    padding:10px;
    font:normal 16px "Rubik", Geneva, sans-serif;
    margin-left: 0px;
    margin-top: 0px;
    z-index:10000;
    overflow: auto;
} 

#spinners #b 
{

    display: table-cell;
    padding-top: 350px;


    text-align: center;
    vertical-align: middle;

}    
    
#spinners span 
{
    font-size: 18px;
    font-weight: 400;
    background-color: white;
    padding: 10px;
    margin: auto;

}
#categories_processing
{
    top: 30%;
    z-index: 100;
}
    
</style>

        <div id="spinners" class="payment_loader"  style="display: none;">

<div id="b"> 
<span id="Text1" style="display: none;"><?php echo lang('generating_report_notice') ?></span> 
<span id="Text2" style="display: none;"><?php echo lang('counting_members_notice') ?></span>  
<span id="Text3" style="display: none;"><?php echo lang('thanks_for_waiting') ?>.</span>       
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