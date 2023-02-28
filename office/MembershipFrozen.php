<?php 
require_once '../app/init.php';
require_once 'Classes/FreezActivities.php';
$pageTitle = lang('reports_freeze');
require_once '../app/views/headernew.php';
?>


<?php if (Auth::check()):?>
<?php if (Auth::userCan('147')): ?>
<?php
CreateLogMovement(lang('freeze_reports_log'), '0');
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
<!--<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>-->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/he.js"></script>-->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<style>
.datepicker-dropdown {max-width: 300px;}
.datepicker {float: right}
.datepicker.dropdown-menu {right:auto}

.btn-freezOut {
    background-color: white;
    color: #24a3b8 !important;
    border: 1px solid #24a3b8;
}
.btn-freezOut:hover, .btn-freezOut:active {
    background-color: #24a3b8;
    color: white !important;
    /* border: 1px solid #24a3b8; */
}
</style>




<script>
$(document).ready(function(){

  var direction = false ;

  if( $("html").attr("dir") == 'rtl' ){
     direction = true ;  
  }

	
	 $('#categories tfoot th span').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="'+title+'" style="width:90%;" class="form-control"  />' );

    } );

 var table = $('#categories');    

    
	
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
            "paging":         false,
	         //fixedHeader: {headerOffset: 50},

	     //  bStateSave:true,
//		    serverSide: true,
//	        pageLength: 5000,
	      dom: '<<"d-flex justify-content-between w-100 mb-10" <rfl><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
		//info: true,
	   
	    buttons: [
         <?php if (Auth::userCan('98')): ?>    
		//{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},
			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: '<?php echo lang('reports_freeze') ?>', className: 'btn btn-dark'},
			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: '<?php echo lang('reports_freeze') ?>', className: 'btn btn-dark'},
            {extend: 'print', text: lang('print') + ' <i class="fas fa-print" aria-hidden="true"></i>', className: 'btn btn-dark', customize: function ( win ) {
              // https://datatables.net/reference/button/print
             jQuery(win.document).ready(function(){
             $(win.document.body)
             .css( 'direction', 'rtl' )
             });                            
             }},
           // 'pdfHtml5'
		<?php endif ?>
			
        ],
	   
       ajax:{
           url: 'MembershipFrozenPost.php',
           method: 'POST',
           },
       
//        serverSide: true,
		order: [[0, 'DESC']]

	   	 	   
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


$('#table-filterMembership').on('change', function(){
table.column('3').search(this.value).draw(); 
});
    
$('#table-filterMembership_type').on('change', function(){
table.column('4').search(this.value).draw(); 
});

$('#table-filterBrands').on('change', function(){
table.column('5').search(this.value).draw(); 
});    
    
    
    
    
});
    
<?php 
$activeFreezes = new FreezActivities();
$activeFreezes = $activeFreezes->getCurrentActiveFreezes(Auth::user()->CompanyNum);
?>  


</script>


<link href="assets/css/fixstyle.css" rel="stylesheet">
<div class="col-md-12 col-sm-12">
<div class="row">



<!-- <div class="col-md-5 col-sm-12 order-md-1">
<h3 class="page-header headertitlemain"  style="height:54px;">
<?php //echo $DateTitleHeader; ?>
</h3>
</div>

<div class="col-md-3 col-sm-12 order-md-4">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
<i class="fas fa-snowflake"></i> דוח הקפאות</span>
</div>
</h3>
</div> -->


<div class="w-100 row p-7 d-flex">
    <div class="margin-a">
        <a class="btn btn-outline-info" id="freezAllActivities"><i class="far fa-snowflake"></i> <?php echo lang('unfreeze_membership_button') ?></a>
        <?php if(!empty($activeFreezes)) { ?>
            <a href="javascript:;"  data-toggle="modal" data-target="#activeFreezes" class="btn btn-info"><i class="fas fa-snowflake"></i> <?php echo lang('active_freeze_button') ?> </a>
        <?php } ?>
    </div>  

</div>       

</div>

<!-- <nav aria-label="breadcrumb" >
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-dark">ראשי</a></li>
  <li class="breadcrumb-item active">דוחות</li>
  <li class="breadcrumb-item active">דוח הקפאות</li>
  </ol>  
</nav>     -->


<div class="row">

<?php include("ReportsInc/SideMenu.php"); ?>

<div class="col-md-10 col-sm-12 ">	


    <div class="card spacebottom">
    <div class="card-header d-flex justify-content-between" dir="rtl">
        <div class="d-flex">
            <b class="margin-a"><?php echo lang('reports_freeze') ?> <i class="fas fa-snowflake"></i></b>
        </div>
 	</div>  
  	<div class="card-body">       
                    
<div class="row">
<div class="col-md-9 col-sm-12">

</div>
<div class="col-md-3 col-sm-12">

</div>
	</div>
<!-- <hr> -->


<table class="table table-hover dt-responsive text-start display wrap" id="categories"  cellspacing="0" width="100%">
		<thead >
			<tr class="bg-dark text-white">
				<th class="text-start"><?php echo lang('client') ?></th>
                <th class="text-start"><?php echo lang('telephone') ?></th>
                <th class="text-start"><?php echo lang('item_single') ?></th>
                <th class="text-start"><?php echo lang('class') ?></th>
				<th class="text-start"><?php echo lang('membership_type_single') ?></th>
                <th class="text-start"><?php echo lang('branch') ?></th>
                <th class="text-start"><?php echo lang('table_start_date') ?></th>
                <th class="text-start"><?php echo lang('customer_card_end_date') ?></th>
                <th class="text-start"><?php echo lang('days') ?></th>
                <th class="text-start lastborder"><?php echo lang('actions') ?></th>
			</tr>
            
          
            
            
		</thead>
		<tbody>
  

        </tbody>

<tfoot>
<tr class="bg-white text-black filterHeader">
                <th><span><?php echo lang('client') ?></span></th>
                <th><span><?php echo lang('telephone') ?></span></th>
                <th><span><?php echo lang('item_single') ?></span></th>
                <th><select id="table-filterMembership" class="form-control">
<option value=""><?php echo lang('all') ?></option>
<?php 
$Memberships = DB::table('membership')->where('Status', '=', '0')->get();                   
foreach ($Memberships as $Membership) {                    
?>
<option><?php echo $Membership->MemberShip; ?></option>                    
<?php } ?>                   
</select></th>
                <th>
<select id="table-filterMembership_type" class="form-control">
<option value=""><?php echo lang('all') ?></option>
<?php 
$MembershipTypes = DB::table('membership_type')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->get();                   
foreach ($MembershipTypes as $MembershipType) {                    
?>
<option><?php echo $MembershipType->Type; ?></option>                    
<?php } ?> 
<option><?php echo lang('without_department') ?></option>     
</select>     
</th>
<th><select id="table-filterBrands" class="form-control">
<option value=""><?php echo lang('all') ?></option>
<?php 
$Brands = DB::table('brands')->where('CompanyNum', '=', $CompanyNum)->get();
if (!empty($Brands)){                     
foreach ($Brands as $Brand) {                    
?>
<option><?php echo $Brand->BrandName; ?></option>                    
<?php } } else { ?> 
<option><?php echo lang('primary_branch') ?></option>                      
<?php } ?>                    
</select>  </th>
                <th><span><?php echo lang('table_start_date') ?></span></th>
                <th><span><?php echo lang('customer_card_end_date') ?></span></th>
                <th><span><?php echo lang('days') ?></span></th>
                <th></th>
            </tr>      
    
</tfoot>
	
        </table> 
    
        </div>
    </div>

	</div> 
</div>

</div>


	<div class="ip-modal" id="FreezOutActivityPopup"  data-backdrop="static">
		<div class="ip-modal-dialog" <?php //_e('main.rtl') ?>>
			<div class="ip-modal-content text-start">
				<div class="ip-modal-header d-flex justify-content-between" >
				<h4 class="ip-modal-title"><?php echo lang('unfreeze_membership') ?></h4>
                <a class="ip-close" title="Close"  data-dismiss="modal">&times;</a>
                
				</div>
       <div class="ip-modal-body" >
       <form action="FreezOutActivity"  class="ajax-form clearfix">
       <input type="hidden" name="ClientId">
       <input type="hidden" name="ActivityId">
    
                <div class="form-group" >
                <label><?php echo lang('q_unfreeze_membership') ?></label>
                </div>    
				</div>
				<div class="ip-modal-footer d-flex justify-content-between">
             
                <button type="submit" name="submit" class="btn btn-primary ip-close"><?php echo lang('yes') ?></button>
                
 
        <button type="button" class="btn btn-dark text-white ip-close ip-closePopUp" data-dismiss="modal"><?php echo lang('no') ?></button>
                </form>
				</div>
			</div>
		</div>
	</div>
    <?php if(!empty($activeFreezes)) { ?>
    <div class="ip-modal" id="activeFreezes">
        <div class="ip-modal-dialog" <?php _e('main.rtl') ?>  style="width: 870px">
            <div class="ip-modal-content text-right">
                <div class="ip-modal-header" dir="rtl">
                    <a class="ip-close" title="Close" style="float:left;" data-dismiss="modal">&times;</a>
                    <h4 class="ip-modal-title"><?php echo lang('active_freeze_button') ?></h4>
                </div>
                <div class="ip-modal-body" dir="rtl">
                <div class="alert alert-primary text-center" id="successAlert" role="alert" style="display: none"></div>
                <div class="alert alert-danger text-center" id="failedAlert" role="alert" style="display: none"></div>
                    <table class="table table-hover borderless" id="freez_table">
                        <thead>
                            <tr>
                            <th scope="col">#</th>
                            <th scope="col"><?php echo lang('table_start_date') ?></th>
                            <th scope="col"><?php echo lang('customer_card_end_date') ?></th>
                            <th scope="col"><?php echo lang('membership_number') ?></th>
                            <th scope="col"><?php echo lang('class_membership_type') ?></th>
                            <th scope="col"><?php echo lang('reason') ?></th>
                            <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            foreach($activeFreezes as $key => $freez) {
                            $membershipsIds = json_decode($freez->memberships);
                            $memberships = implode(", ", $freez->getMembershipText());
                            $sub_memberships = strlen($memberships) >= 20 ? mb_substr($memberships, 0, 20). ' ...' : $memberships;   
                            ?>
                            <tr id="<?php echo $freez->id ?>">
                            <input type="hidden" value="<?php echo $freez->end_freez; ?>" id="end<?php echo $freez->id ?>">
                            <input type="hidden" class="freezId<?php echo $freez->id ?>" value="<?php echo $freez->id ?>" id="freezId">
                            <th scope="row"><?php echo $key + 1; ?></th>
                            <td><?php echo $freez->start_freez; ?></td>
                            <td id="end_date"><span><?php echo $freez->end_freez; ?></span> <a class="info js-edit_endDate" href="javascript:;"><i class="fal fa-edit"></i></a></td>
                            <td><?php echo $freez->activities_count; ?></td>
                            <td><a class="js-show_more" href="javascript:;" title="<?php echo !empty($memberships) ? $memberships : ''; ?>"><?php echo !empty($sub_memberships) ? $sub_memberships : ''; ?></a></td>
                            <td><?php echo $freez->reason; ?></td>
                            <td><a href="javascript:;" id="freezOutBtn<?php echo $freez->id ?>" onclick="freezOut('<?php echo $freez->id ?>')" class="info"><?php echo lang('unfreeze_memberships') ?> </a></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div class="ip-modal-footer">
                    <button type="button" class="btn btn-dark text-white ip-close ip-closePopUp" data-dismiss="modal"><?php echo lang('close') ?></button>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>   

    <div class="ip-modal" id="FreezOutAllActivitiesPopup">
        <div class="ip-modal-dialog" <?php //_e('main.rtl') ?>>
            <div class="ip-modal-content text-start">
                <div class="ip-modal-header d-flex justify-content-between" >
                <h4 class="ip-modal-title"><?php echo lang('unfreeze_membership_button') ?></h4>
                <a class="ip-close" title="Close"  data-dismiss="modal">&times;</a>
                
                </div>
    <div class="ip-modal-body" >
    <form  action="FreezOutAllActivities"  class="ajax-form clearfix">
    <!-- <input type="hidden" name="ClientId">
    <input type="hidden" name="ActivityId"> -->
    
                <div class="form-group" >
                <label><?php echo lang('q_unfreeze_all_membership') ?></label>
                </div>    
                </div>
                <div class="ip-modal-footer d-flex justify-content-between">
            
                <button type="submit" name="submit" class="btn btn-freezOut ip-close"><?php echo lang('yes') ?></button>
                

        <button type="button" class="btn btn-dark text-white ip-close ip-closePopUp" data-dismiss="modal"><?php echo lang('no') ?></button>
                </form>
                </div>
            </div>
        </div>
    </div>        

<script>
    function updateDate(freez_id) {
        let tr = $('tr#'+freez_id);
        let end_date = $("#update_date"+freez_id).val();
        let curr_date = $("#end"+freez_id).val();
        if(end_date == null || end_date == '') {
            $("#failedAlert").text('<?php echo lang('unvalid_update_date') ?>');
            $("#failedAlert").show();
            setTimeout(() => {
                $("#failedAlert").fadeOut();
            }, 2000);
            return;
        }
        if(end_date == curr_date) {
            $("#failedAlert").text('<?php echo lang('date_unchanged') ?>');
            $("#failedAlert").show();
            setTimeout(() => {
                $("#failedAlert").fadeOut();
            }, 2000);
            return;
        }
        if(!$("#update"+freez_id+ " i").length) {
            $("#update"+freez_id).append('<i class="fad fa-circle-notch fast-spin">');
        }
        data = {
            freezId: freez_id,
            end_date: end_date,
            method: "update"
        }
        $.ajax({
            url: 'ajax/updateEndFreez.php',
            type: 'POST',
            data: data,
            success: function(response) {
                $("#update"+freez_id+ " i").remove();
                var res = JSON.parse(response);
                if(res.count > 0) {
                    if(res.action == 'freezOut') {
                        cancelUpdate(freez_id);
                        $("#successAlert").text(res.message);
                        $("#successAlert").show();
                        setTimeout(() => {
                            $("#successAlert").fadeOut();
                        }, 2000);
                        $('#categories').DataTable().ajax.reload();
                        
                    } else if(res.action == 'update') {
                        $('#end'+freez_id).val(end_date);
                        cancelUpdate(freez_id);
                        $("#successAlert").text(res.message);
                        $("#successAlert").show();
                        setTimeout(() => {
                            $("#successAlert").fadeOut();
                        }, 2000);
                        $('#categories').DataTable().ajax.reload();
                        
                    } else {
                        $("#failedAlert").text(res.message);
                        $("#failedAlert").show();
                        setTimeout(() => {
                            $("#failedAlert").fadeOut();
                        }, 2000);
                    }
                }


            },
            error: function(xhr, status, error) {
                $("#update"+freez_id+ " i").remove();
                $("#failedAlert").text(lang('action_cancled'));
                $("#failedAlert").show();
                setTimeout(() => {
                    $("#failedAlert").fadeOut();
                }, 2000);
            }
        })
    }

    function freezOut(freez_id) {
        let tr = $('tr#'+freez_id);
        if(!$("#freezOutBtn"+freez_id+ " i").length) {
            $("#freezOutBtn"+freez_id).append('<i class="fad fa-circle-notch fast-spin">');
        }
        data = {
            freezId: freez_id,
            method: "freezOut"
        }
        $.ajax({
            url: 'ajax/updateEndFreez.php',
            type: 'POST',
            data: data,
            success: function(response){
                $("#freezOutBtn"+freez_id+ " i").remove();
                var res = JSON.parse(response);
                if(res.count > 0) {
                    tr.remove();
                    $("#successAlert").text(res.message);
                    $("#successAlert").show();
                    setTimeout(() => {
                        $("#successAlert").fadeOut();
                    }, 2000);
                    $('#categories').DataTable().ajax.reload();
                }
            },
            error: function(xhr, status, error) {
                $("#freezOutBtn"+freez_id+ " i").remove();
                $("#failedAlert").text('<?php echo lang('action_cancled') ?>');
                $("#failedAlert").show();
                setTimeout(() => {
                    $("#failedAlert").fadeOut();
                }, 2000);
            }
        })
    }

    function cancelUpdate(freez_id) {
        var end = $('#end'+freez_id).val();
        var tr = $('tr#'+freez_id);
        var elm = tr.find('td#end_date')
        elm.empty();
        elm.append('<span>'+end+'</span> <a class="info js-edit_endDate" href="javascript:;"><i class="fal fa-edit"></i></a>');

        
    }

    $(document).ready(function() {
        $("#freezAllActivities").on('click', function() {
            $("#FreezOutAllActivitiesPopup").modal('show');
        });

    
        $("#freez_table").on('click', '.js-edit_endDate', function() {
            var elm = $(this).closest('#end_date');
            let freez_id = $(this).closest("tr").find("#freezId").val();
            var end = $('#end'+freez_id).val(); 
            elm.empty();
            elm.append('<a href="javascript:;" style="color: #FF003B !important" onclick="cancelUpdate('+freez_id+')" class="update_end-date"><i class="fal fa-times"></i></a> <input type="date" id="update_date'+freez_id+'" min="<?php echo date('Y-m-d') ?>" value="'+end+'"> <a href="javascript:;" onclick="updateDate('+freez_id+')" id="update'+freez_id+'" class="update_end-date" style="color: #00c736 !important">'+lang('update')+' </a>');
        });


    });

</script>

<?php else: ?>
<?php redirect_to('../index.php'); ?>
<?php endif ?>


<?php endif ?>

<?php if (Auth::guest()): ?>

<?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>