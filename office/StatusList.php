<?php
redirect_to(__DIR__.'/manage-leads.php');
exit;

require_once '../app/init.php'; 
$pageTitle = lang('lead_status_management');
require_once '../app/views/headernew.php';
?>

<?php if (Auth::check()):?>
<?php if (Auth::userCan('49')): ?>
<?php
$CompanyNum = Auth::user()->CompanyNum;
$Items = DB::table('pipeline_category')->where('CompanyNum' ,'=', $CompanyNum)->orderBy('id', 'ASC')->orderBy('Status', 'ASC')->get();
$resultcount = count($Items);



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
	        "scrollY":        "450px",
            "scrollCollapse": true,
            "paging":         true,
	         fixedHeader: {
        headerOffset: 50
    },

	     //  bStateSave:true,
		   // serverSide: true,
	        pageLength: 100,
	       dom: '<<"d-flex justify-content-between w-100 mb-10" <rf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>' ,
		//info: true,
	 
	    buttons: [
		//{extend: 'copy', text: '???????? <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},
			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: '<?php echo lang('lead_status') ?>', className: 'btn btn-dark',exportOptions: {columns: [ 0, 1, 2 ]}},
			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: '<?php echo lang('lead_status') ?>', className: 'btn btn-dark',exportOptions: {columns: [ 0, 1, 2 ]}},
           // 'pdfHtml5'
		
			
        ],

	//	order: [[0, 'DESC']]

	   	 	   
        } );
		
	
	
});


</script>

<link href="assets/css/fixstyle.css" rel="stylesheet">
<!-- <div class="col-md-12 col-sm-12">
<div class="row">



<div class="col-md-5 col-sm-12 order-md-1">
<h3 class="page-header headertitlemain"  style="height:54px;">
<?php //echo $DateTitleHeader; ?>
</h3>
</div>

<div class="col-md-5 col-sm-12 order-md-3">
<h3 class="page-header headertitlemain"  style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
<i class="fas fa-align-right"></i> ?????????? PIPELINE <span style="color:#48AD42;"><?php //echo $resultcount; ?> </span>
</div>
</h3>
</div>

<div class="col-md-2 col-sm-12 order-md-2 pb-1">
<?php //if (Auth::userCan('48')): ?>    
<a href="#" data-ip-modal="#PipelinePopup" class="btn btn-success btn-block" name="Items"  ><i class="fas fa-plus-circle fa-fw"></i> PIPELINE ??????</a>
<?php //endif; ?>    
</div>


</div>

<nav aria-label="breadcrumb" >
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-dark">????????</a></li>
  <li class="breadcrumb-item"><a href="SettingsDashboard.php" class="text-dark">????????????</a></li>
  <li class="breadcrumb-item active">?????????? PIPELINE</li>
  </ol>  
</nav>     -->

<?php if (Auth::userCan('48')): ?>    
    <a href="javascript:;" data-ip-modal="#PipelinePopup" class="floating-plus-btn d-flex bg-primary" title="<?php echo lang('new_pipeline') ?>">
		<i class="fal fa-plus fa-lg margin-a"></i>
	</a>
<?php endif; ?> 

<div class="row">
<?php include("SettingsInc/RightCards.php"); ?>

<div class="col-md-10 col-sm-12">	


    <div class="card spacebottom">
    <div class="card-header text-start d-flex justify-content-between" >
	    <div>
	    	<i class="fas fa-align-right"></i> <b><?php echo lang('settings_pipeline') ?><span class="text-primary"> <?php echo $resultcount; ?> </span></b>
		</div>
 	</div>
  	<div class="card-body">       
                    
                      
                       



<table class="table table-bordered table-hover dt-responsive text-start display wrap" id="categories"  cellspacing="0" width="100%">
		<thead class="thead-dark">
			<tr>
				<th class="text-start"><?php echo lang('pipeline_title') ?></th>
                <th class="text-start"><?php echo lang('management') ?></th>
				<th class="text-start"><?php echo lang('status') ?></th>
                <th class="text-start"><?php echo lang('pipeline_id') ?></th>
                <th class="text-start lastborder"><?php echo lang('actions') ?></th>
			</tr>
		</thead>
		<tbody>
              <?php 

$i = 1;

foreach ($Items as $Item) {

?>        
        <tr>	
        <td class="text-start"><a href="ManageLeads.php?u=<?php echo $Item->id; ?>"><span class="text-primary"><?php echo $Item->Title ?></span></a></td>
        <td><a href="StatusListSub.php?Id=<?php echo $Item->id; ?>"><span class="text-primary"><?php echo lang('edit_status') ?></span></a></td>    
        <td class="align-middle"><?php if ($Item->Status=='0'){ echo '<span class="text-dark"><i class="fa fa-eye"></i> '.lang('displayed').'</span>'; } else { echo '<span class="text-danger"><i class="fa fa-eye-slash"></i> '.lang('hidden').'</span>'; } ?></td>
        <td class="text-start"><?php echo $Item->id; ?></td>    
        <td class="text-start">
        <?php if (Auth::userCan('48')): ?>     
		<a class="btn btn-success btn-sm" style="color: #FFFFFF !important;" href='javascript:UpdatePipeline("<?php echo $Item->id; ?>");'><?php echo lang('edit_pipeline') ?></a>
        <?php endif; ?>    
		</td>
        </tr>
        
        
        
 <?php
    
  ++ $i; } ?>       
        

        </tbody>
	
	
        </table> 
    
        </div>
    </div>

	</div> 
</div>

</div>




<!-- DepartmentsPopup -->
	<div class="ip-modal" id="PipelinePopup">
		<div class="ip-modal-dialog" <?php //_e('main.rtl') ?>>
			<div class="ip-modal-content text-start">
				<div class="ip-modal-header d-flex justify-content-between" >
				<h4 class="ip-modal-title"><?php echo lang('new_pipeline') ?></h4>
                <a class="ip-close" title="Close" >&times;</a>
                
				</div>
				<div class="ip-modal-body" >
<form action="AddPipeline"  class="ajax-form clearfix">
    
    
                <div class="form-group" >
                <label><?php echo lang('pipeline_title') ?></label>
                <input type="text" name="Title" id="Title" class="form-control" placeholder="<?php echo lang('pipeline_title') ?>">
                </div>     

                <div class="form-group">
                <label><?php echo lang('display_by_representative') ?></label>
                <select name="PipeAgentView" class="form-control" style="width:100%;" >
                <option value="0"><?php echo lang('display_all') ?></option>
                <option value="1" selected ><?php echo lang('display_by_represent') ?></option>
                </select>  
                </div>
    
                <div class="form-group" >
                <label><?php echo lang('max_leads_load') ?></label>
                <input type="number" max="150" min="1" name="MaxRecord" class="form-control" value="30" onkeypress="validate(event)">
                </div>   
    
				</div>
				<div class="ip-modal-footer d-flex justify-content-between" >
                <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-success"><?php echo lang('save_changes_button') ?></button>
                </div>
                
				<button type="button" class="btn btn-dark ip-close"><?php echo lang('close') ?></button>
                </form>
				</div>
			</div>
		</div>
	</div>
	<!-- end DepartmentsPopup -->




<!-- Edit DepartmentsPopup -->
	<div class="ip-modal" id="PipelineEditPopup" tabindex="-1">
		<div class="ip-modal-dialog" <?php //_e('main.rtl') ?>>
			<div class="ip-modal-content text-start">
				<div class="ip-modal-header d-flex justify-content-between" >
				<h4 class="ip-modal-title"><?php echo lang('edit_pipeline') ?></h4>
                <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true">&times;</a>
                
				</div>
				<div class="ip-modal-body" >
<form action="EditPipeline"  class="ajax-form clearfix">
<input type="hidden" name="ItemId">
<div id="result">


  
</div>

				</div>
				<div class="ip-modal-footer d-flex justify-content-between">
                <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-success"><?php echo lang('save_changes_button') ?></button>
                </div>
                
				<button type="button" class="btn btn-dark ip-close" data-dismiss="modal"><?php echo lang('close') ?></button>
                </form>
				</div>
			</div>
		</div>
	</div>
	<!-- end Edit DepartmentsPopup -->




<script> 

$(function() {
			var time = function(){return'?'+new Date().getTime()};
			
			// Header setup
			$('#PipelinePopup').imgPicker({
			});
			// Header setup
			$('#PipelineEditPopup').imgPicker({
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