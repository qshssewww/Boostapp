<?php
 require_once '../../app/init.php';
 // secure page
 if (!Auth::check()) 
    redirect_to('../../index.php');

$report = new StdClass();
$report->name = lang('reports_expired_memberships');
$pageTitle = $report->name;
require_once '../../app/views/headernew.php';


?>
<link
    href="//cdn.datatables.net/buttons/1.5.1/css/buttons.bootstrap4.min.css" rel="stylesheet">


<link href="https://cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap4.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/fixedheader/3.1.3/css/fixedHeader.dataTables.min.css" rel="stylesheet">
<link href="//cdn.datatables.net/responsive/2.2.1/css/responsive.dataTables.min.css" rel="stylesheet">
<link href="<?php echo get_loginboostapp_domain() ?>/CDN/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">



<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js">
</script>
<script src="//cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js">
</script>

<script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js">
</script>
<script src="https://cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.js">
</script>
<script src="https://cdn.datatables.net/responsive/2.2.1/js/responsive.bootstrap4.min.js">
</script>

<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js">
</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js">
</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js">
</script>
<script src="//cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js">
</script>

<!--<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>-->
<script src="//cdn.datatables.net/plug-ins/1.10.16/sorting/datetime-moment.js">
</script>

<link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet" />
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/select2.min.js">
</script>
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/js/i18n/he.js">
</script>

<script src="//cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js">
</script>
<script src="../js/datatable/dataTables.checkboxes.min.js">
</script>

<link href="../assets/css/fixstyle.css" rel="stylesheet">
<style>
    .bg-gray {
        background-color: #e9ecef;
    }

    .dataTables_scrollHead table {
        margin-bottom: 0px;
    }
    div.dataTables_wrapper div.dataTables_processing {
        position: fixed;
    top: 50%;
    left: 50%;
    -webkit-transform: translate(-50%, -50%);
    transform: translate(-50%, -50%);
    z-index: 999;
    background: #38ab4b;
    color: #fff;
}
th.appendInputs {white-space: nowrap;}
th.appendInputs input{display: inline-block; max-width: 60px}
th.appendInputs select{display: inline-block; max-width: 20px}
.select2-container .select2-search__field {
    width: 100% !important;
}
</style>



<div class="row"  >
    <div class="col-12 " >


        <!-- <nav aria-label="breadcrumb" >
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/index.php" class="text-info">ראשי</a>
                </li>
                <li class="breadcrumb-item active">דוחות</li>
                <li class="breadcrumb-item active" aria-current="page">
                    <?php //echo  $report->name ?>
                </li>
            </ol>
        </nav> -->

        <div class="row">

            <?php include("../ReportsInc/SideMenu.php"); ?>

            <div class="col-md-10 col-sm-12">
                <div class="tab-content">
                    <div class="tab-pane fade show active text-start" role="tabpanel" id="user-overview">
                        <div class="card spacebottom">
                            <div class="card-header text-start" onclick="jQuery('#invalidmembershipData').slideToggle()">
                                <i class="fas fa-user-plus"></i>
                                <strong>
                                <?php echo lang('reports_expired_membership_title') ?>
                                </strong>
                            </div>
                            <div class="card-body" id="invalidmembershipData" style="display: none">

                                <!-- page content -->
                                <hr>

                                <div class="row"  style="padding-left:15px; padding-right:15px;">

<table class="table table-bordered table-hover dt-responsive text-start display wrap" id="categories"  cellspacing="0" width="100%">
		<thead class="thead-dark">
			<tr>
				<th class="text-start">#</th>
				<th class="text-start"><?php echo lang('client_name') ?></th>
				<th class="text-start"><?php echo lang('id') ?></th>
				<th class="text-start"><?php echo lang('telephone') ?></th>
                <th class="text-start"><?php echo lang('type') ?></th>
				<th class="text-start"><?php echo lang('membership') ?></th>
                <th class="text-start"><?php echo lang('expires_at') ?></th>
                <th class="text-start"><?php echo lang('classes') ?></th>
                <th class="text-start"><?php echo lang('actions') ?></th>
			</tr>
		</thead>
		<tbody>
  

        </tbody>
		<tfoot>
            <tr>
                <th><span>#</span></th>
                <th><span><?php echo lang('client_name') ?></span></th>
				<th><span><?php echo lang('id') ?></span></th>
				<th><span><?php echo lang('telephone') ?></span></th>
                <th><span><?php echo lang('type') ?></span></th>
				<th><span><?php echo lang('membership') ?></span></th>
                <th><span><?php echo lang('expires_at') ?></span></th>
                <th><span><?php echo lang('classes') ?></span></th>
                <th></th>
            </tr>
        </tfoot>

	
        </table> 

                                    

                                    


                                </div>



                                


                            </div>
                            
                        </div> <!-- end invalid membership with product -->
                        

<div class="mt-2" id="invalidData">
                <div class="tab-content">
                    <div class="tab-pane fade show active text-start" role="tabpanel" id="user-overview">
                        <div class="card spacebottom">
                            <div class="card-header text-start" onclick="jQuery('#invalidDataBody').slideToggle(400, function(){var el = jQuery(this); el.is(':visible') ?el.find('table').DataTable().ajax.reload():undefined})">
                                <i class="fas fa-user-plus"></i>
                                <strong>
                                <?php echo lang('non_membership_client') ?>
                                </strong>
                            </div>
                            <div class="card-body" id="invalidDataBody" style="display: none">

                                <!-- page content -->
                                <hr>

                                <div class="row"  style="padding-left:15px; padding-right:15px;">

                                    <table class="table table-hover dt-responsive text-start display wrap" id="dataTable"  cellspacing="0" width="100%">
                                        <thead>
                                            <tr class="bg-dark text-white">
                                                <th data-name="select" data-bSortable="false"></th>
                                                <th data-name="clientFullName"><?php echo lang('client_name') ?></th>
                                                <th data-name="clientPhone"><?php echo lang('telephone') ?></th>
                                            </tr>
                                            <tr>
                                                <th data-name="select"></th>
                                                <th data-name="clientFullName">
                                                    <input type="text" name="clientName" data-search="clientName"  class="form-control" placeholder="<?php echo lang('a_search_by_name') ?>">
                                                </th>
                                                <th data-name="clientPhone">
                                                <input type="text" name="clientPhone" data-search="clientPhone" class="form-control" placeholder="<?php echo lang('a_search_by_phone') ?>"> 
                                                </th>
                                            </tr>
                                        </thead>

                                        <tbody>

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th data-name="select"></th>
                                                <th data-name="clientFullName"></th>
                                                <th data-name="clientPhone"></th>
                                            </tr>
                                        </tfoot>
                                    </table>

                                    

                                    


                                </div>


                            </div>
                        </div>
                    </div>
                </div>



                    </div>
                </div>


                
    </div>





    

<!--    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>-->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!--    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/he.js"></script>-->
    <script src="../js/datatable/dataTables.checkboxes.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <style>
        .datepicker-dropdown {
            max-width: 300px;
        }

        .datepicker {
            float: right
        }

        .datepicker.dropdown-menu {
            right: auto
        }
    </style>

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

     
     var buttons = []
     var modal = $('#SendClientPush');
     var modalsClientIds = $('input[name="clientsIds"]', modal);

if (<?php echo Auth::userCan('98') ? "true" : "false"; ?>) buttons.push({
    text: 'שלח הודעה <i class="fas fa-comments"></i>',
    className: 'btn btn-dark',
    action: function (e, dt, node, config) {
        // rows_selected = table.column(0).checkboxes.selected();
        var clientsIds = dt.column(0).checkboxes.selected().toArray();
        if (!clientsIds.length) return alert('אנא בחר לקוחות');

        modalsClientIds.val(clientsIds.join(","));
        modal.modal('show');

    }
})

if (<?php echo Auth::userCan('98')? "true" : "false"; ?>) buttons.push({
    extend: 'excelHtml5',
    text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>',
    filename: 'דו״ח אי הרשמה',
    className: 'btn btn-dark',
    exportOptions: {}
})
if (<?php echo Auth::userCan('98')? "true" : "false"; ?>) buttons.push({
    extend: 'csvHtml5',
    text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>',
    filename: 'דו״ח אי הרשמה',
    className: 'btn btn-dark',
    exportOptions: {}
})
	
	var categoriesDataTable;
	BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
	debugger
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
	     pageLength: 100,
         lengthChange: true,
         lengthMenu: [10, 25, 50, 75, 100, 150, 200, 250, 300, 500],
	      dom: '<<"d-flex justify-content-between w-100 mb-10" <rfl><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
		//info: true,
	   
	    buttons: buttons,
	   
	   		ajax: { url: '../InvildMemberShipPost.php?<?php echo @$_SERVER['QUERY_STRING']; ?>', },

		order: [[1, 'asc']],
        columnDefs: [ {
            targets: 6,
            type: 'iso-date'
        },{
                    'targets': [0],
                    'checkboxes': {
                        'selectRow': true
                    },
                    bSortable: false,
                    ordering: false
                }]
        
	   	 	   
        } );
		
		    var table = $('#categories').DataTable();
		    debugger
			table.columns().every( function () {
            var that = this;
debugger
		 $( 'input', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );		
				
				
				
    } );
	
	
	
});

_isoDateSort = function(a, b) {
	var a = moment(a, 'DD/MM/YYYY').unix();
    var b = moment(b, 'DD/MM/YYYY').unix();
    
	return ((a < b) ? -1 : ((a > b) ? 1 : 0));    
}

jQuery.extend( jQuery.fn.dataTableExt.oSort, {
	"iso-date-asc": function (a, b) {
		return _isoDateSort(a, b);
	},
	"iso-date-desc": function (a, b) {
		return _isoDateSort(a, b) * -1;
	}
});

</script>

    <script>
        var BeePOS = BeePOS || {};
            BeePOS.options = BeePOS.options || {};
            BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
            BeePOS.options.datatables.processing = '<i class="fas fa-spinner fa-spin"></i> ' + BeePOS.options.datatables.processing
        var boostAppDataTable = {
            allowDebug: <?php echo Auth::user()->role_id == '1' ? 'true' : 'false'; ?>,
            buttons: {
                allowClientPush: true,
                excel: <?php echo Auth::userCan('98')?"true":"false"; ?>,
                csv: <?php echo Auth::userCan('98')?"true":"false"; ?>
            }
        };
    </script>
    <script src="./js/inactive.js"></script>


    <!-- popupSendByClientId -->
    <?php include('./popupSendByClientId.php'); ?>



    <?php 
        require_once '../../app/views/footernew.php';
    ?>