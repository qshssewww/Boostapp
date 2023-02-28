<?php 
require_once '../app/init.php'; 
$pageTitle = lang('settings_class');
require_once '../app/views/headernew.php';
?>


<?php if (Auth::check()):?>
<?php if (Auth::userCan('7')): ?>
<?php
$CompanyNum = Auth::user()->CompanyNum;
$Items = DB::table('class_type')->where('CompanyNum','=', $CompanyNum)->orderBy('Status', 'ASC')->get();
$resultcount = count($Items);

CreateLogMovement(lang('settings_class_log'), '0');

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

<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.js"></script>


<script>
$(document).ready(function(){
	
     var dt_dom = '<<"d-flex justify-content-between w-100 mb-10" <rf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>' ;  
     
   

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
	         fixedHeader: {
        headerOffset: 50
    },

	     //  bStateSave:true,
		   // serverSide: true,
	        pageLength: 100,
	      dom: dt_dom,
		//info: true,
	  
	    buttons: [
         <?php if (Auth::userCan('98')): ?>    
		//{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},
			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: '<?php echo lang('settings_class') ?>', className: 'btn btn-dark',exportOptions: {columns: [ 0, 1, 2 ]}},
			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: '<?php echo lang('settings_class') ?>', className: 'btn btn-dark',exportOptions: {columns: [ 0, 1, 2 ]}},
           // 'pdfHtml5'
		 <?php endif ?>	
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
<i class="fas fa-address-card fa-fw"></i> סוגי שיעורים <span style="color:#48AD42;"><?php //echo $resultcount; ?> </span>
</div>
</h3>
</div>

<div class="col-md-2 col-sm-12 order-md-2 pb-1">
<?php //if (Auth::userCan('8')): ?>    
<a href="#" data-ip-modal="#ClassTypePopup" class="btn btn-success btn-block" name="Items"  ><i class="fas fa-plus-circle fa-fw"></i> הוסף שיעור חדש</a>
<?php //endif ?>    
</div>


</div> -->

<!-- <nav aria-label="breadcrumb" >
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-dark">ראשי</a></li>
  <li class="breadcrumb-item"><a href="SettingsDashboard.php" class="text-dark">הגדרות</a></li>
  <li class="breadcrumb-item active" aria-current="page">סוגי שיעורים</li>
  </ol>  
</nav>     -->

<?php if (Auth::userCan('8')): ?>   
<a href="javascript:;" data-ip-modal="#ClassTypePopup" class="floating-plus-btn d-flex bg-primary" title="<?php echo lang('a_new_class') ?>">
    <i class="fal fa-plus fa-lg margin-a"></i>
</a>
<?php endif; ?>
<div class="row">
<?php include("SettingsInc/RightCards.php"); ?>

<div class="col-md-10 col-sm-12 order-md-1">	


    <div class="card spacebottom">
    <div class="card-header text-start d-flex justify-content-between" >
    <div><i class="fas fa-sync"></i> <b><?php echo lang('settings_class') ?> <span class="text-primary"><?php echo $resultcount; ?> </span></b></div></div>
  	<div class="card-body">       
                    
                      
                       



<table class="table table-bordered table-hover dt-responsive text-start display wrap" id="categories"  cellspacing="0" width="100%">
		<thead class="thead-dark">
			<tr>
				<th class="text-start">#</th>
				<th class="text-start"><?php echo lang('lesson_type') ?></th>
				<th class="text-start"><?php echo lang('status') ?></th>
                <?php if (Auth::userCan('8')): ?>   
                <th class="text-start lastborder"><?php echo lang('actions') ?></th>
                <?php endif ?>  
			</tr>
		</thead>
		<tbody>
              <?php 

$i = 1;

foreach ($Items as $Item) {

?>        
        <tr>
        <td class="text-start"><?php echo $i?></td>
        <td class="text-start"><?php echo $Item->Type ?></td>
        <td class="align-middle"><?php if ($Item->Status=='0'){ echo '<span class="text-dark"><i class="fa fa-eye"></i> '.lang('active').'</span>'; } else { echo '<span class="text-danger"><i class="fa fa-eye-slash"></i> '.lang('hidden').'</span>'; } ?></td>
        <?php if (Auth::userCan('8')): ?>       
        <td class="text-start"><a class="btn btn-success btn-sm" style="color: #FFFFFF !important;" href='javascript:UpdateClassType("<?php echo $Item->id; ?>");'><?php echo lang('edit_class_type') ?></a></td>
        <?php endif ?>      
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
	<div class="ip-modal" id="ClassTypePopup" data-backdrop="static" data-keyboard="false" aria-hidden="true">
		<div class="ip-modal-dialog BigDialog" <?php //_e('main.rtl') ?>>
			<div class="ip-modal-content text-start">
				<div class="ip-modal-header d-flex justify-content-between" >
				<h4 class="ip-modal-title"><?php echo lang('new_class_type') ?></h4>
                <a class="ip-close" title="Close">&times;</a>
                
				</div>
				<div class="ip-modal-body" >
                <form action="AddClassType"  class="ajax-form clearfix">
                <div class="form-group" >
                <label><?php echo lang('lesson_title') ?></label>
                <input type="text" name="Type" id="Type" class="form-control" placeholder="<?php echo lang('lesson_title') ?>">
                </div>     

                <div class="form-group">
                <label><?php echo lang('class_description') ?></label>
                <textarea class="form-control summernote" name="ClassNotes" rows="5"></textarea>
                </div>   
                    
                <div class="form-group">
                <label><?php echo lang('background_color') ?></label>
                <div id="SetDocBackPreview" style="background-color:#b79bf7;width:50px;height:10px;display:inline-block;"></div>
                <select class="form-control" name="DocsBackgroundColor" id="DocsBackgroundColor" onchange="dsfsd()">
                	<option value="#e10025"><?php echo lang('red_color') ?></option>
                	<option value="#bd1a2f"><?php echo lang('dark_red') ?></option>
                	<option value="#f19218"><?php echo lang('orange_color') ?></option>
                	<option value="#f8b43d"><?php echo lang('yellow_color') ?></option>
                	<option value="#48AD42"><?php echo lang('green_color') ?></option>
                	<option value="#648426"><?php echo lang('dark_green_color') ?></option>
                	<option value="#17a2b8"><?php echo lang('turquoise_color') ?></option>
                	<option value="#2b71b9"><?php echo lang('blue_color') ?></option>
                	<option value="#2B619D"><?php echo lang('dark_blue_color') ?></option>
                	<option value="#e83e8c"><?php echo lang('pink_color') ?></option>
                	<option value="#b79bf7" selected><?php echo lang('purple_color') ?></option>
                	<option value="#6610f2"><?php echo lang('dark_purple_color') ?></option>
                    
                    <option value="#DDAA33"><?php echo lang('ochre_color') ?></option>
                    <option value="#4B0082"><?php echo lang('indigo_color') ?></option>
                    <option value="#7F003F"><?php echo lang('a_purple_color') ?></option>
                    <option value="#C3B091"><?php echo lang('khaki_color') ?></option>
                    <option value="#7F3F00"><?php echo lang('brown_color') ?></option>
                    <option value="#FF00FF"><?php echo lang('magenta_color') ?></option>
                    <option value="#00FFFF"><?php echo lang('cyan_color') ?></option>
                    <option value="#C41E3A"><?php echo lang('cardinal_color') ?></option>
                    <option value="#7F0000"><?php echo lang('kermes_color') ?></option>
                    <option value="#007FFF"><?php echo lang('azure_color') ?></option>
                    <option value="#FFDF00"><?php echo lang('gold_color') ?></option>


                </select>
                </div>    
                
                    
                <?php if ($CompanyNum=='569121'){ ?>    
                <div class="form-group">
                <label><?php echo lang('app_background_color') ?></label>
                <select class="form-control" name="Color2">
                	<option value="#E30613">AEROBIC</option>
                	<option value="#0083E1">CORE</option>
                	<option value="#AEAEAE">NUTRITION</option>
                	<option value="#000000">ANAEROBIC</option>
                </select>
                </div>     
                <?php } else { ?>    
                <input type="hidden" name="Color2" value="">    
                <?php } ?>    
				</div>
				<div class="ip-modal-footer">
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
	<div class="ip-modal" id="EditClassTypePopup" data-backdrop="static" data-keyboard="false" aria-hidden="true">
		<div class="ip-modal-dialog BigDialog" <?php //_e('main.rtl') ?>>
			<div class="ip-modal-content text-start">
				<div class="ip-modal-header d-flex justify-content-between" >
				<h4 class="ip-modal-title"><?php echo lang('edit_class_type') ?></h4>
                <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" >&times;</a>
                
				</div>
				<div class="ip-modal-body" >
<form action="EditClassType"  class="ajax-form clearfix">
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

$(document).ready(function() {
 $('.summernote').summernote({
    //    placeholder: 'הקלד תיאור לשיעור',
        tabsize: 2,
        height: 100,
	   toolbar: [
    // [groupName, [list of button]]
    ['style', ['bold', 'italic', 'underline', 'clear']],
    ['font', ['strikethrough']],
    ['para', ['ul', 'ol']]
  ]
      });
});	    
    
$(function() {
			var time = function(){return'?'+new Date().getTime()};
			
			// Header setup
			$('#ClassTypePopup').imgPicker({
			});
			// Header setup
			$('#EditClassTypePopup').imgPicker({
			});
	
});


function dsfsd() {
    var x = document.getElementById("DocsBackgroundColor").value;
    document.getElementById("SetDocBackPreview").style.backgroundColor = x;
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