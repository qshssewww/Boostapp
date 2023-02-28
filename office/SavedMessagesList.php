<?php 
require_once '../app/init.php'; 
$pageTitle = lang('message_management_title');
require_once '../app/views/headernew.php';
?>

<?php if (Auth::check()):?>
<?php if (Auth::userCan('15')): ?>
<?php
$SettingsInfo = DB::table('settings')->where('CompanyNum' ,'=', Auth::user()->CompanyNum)->first();

$Items = DB::table('textsaved')->where('CompanyNum' ,'=', Auth::user()->CompanyNum)->orderBy('Status', 'ASC')->get();
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

<!-- include summernote css/js -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.js"></script>




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
        <?php if (Auth::userCan('98')): ?>
		//{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},
			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: '<?php echo lang('settings_message_template') ?>' , className: 'btn btn-dark',exportOptions: {columns: [ 0, 1, 2, 3 ]}},
			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: '<?php echo lang('settings_message_template') ?>' , className: 'btn btn-dark',exportOptions: {columns: [ 0, 1, 2, 3 ]}},
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
<i class="fas fa-comments"></i> תבניות להודעות <span style="color:#48AD42;"><?php //echo $resultcount; ?> </span>
</div>
</h3>
</div>

<div class="col-md-2 col-sm-12 order-md-2 pb-1">
<?php //if (Auth::userCan('16')): ?>    
<a href="#" data-ip-modal="#MsgPopup" class="btn btn-success btn-block" name="Items"  ><i class="fas fa-plus-circle fa-fw"></i> תבנית חדשה</a>
<?php //endif ?>     
</div>


</div>

<nav aria-label="breadcrumb" >
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-dark">ראשי</a></li>
  <li class="breadcrumb-item active">הגדרות</li>
  <li class="breadcrumb-item active">תבניות להודעות</li>
  </ol>  
</nav>     -->

<?php if (Auth::userCan('16')): ?>    
	<a href="javascript:;" data-ip-modal="#MsgPopup" class="floating-plus-btn d-flex bg-primary" title="<?php echo lang('new_template') ?>">
		<i class="fal fa-plus fa-lg margin-a"></i>
	</a>
<?php endif; ?> 

<div class="row">
<?php include("SettingsInc/RightCards.php"); ?>

<div class="col-md-10 col-sm-12">	


    <div class="card spacebottom">
    <div class="card-header text-start d-flex justify-content-between " >
    <div> <i class="fas fa-comments"></i> <b><?php echo lang('settings_message_template') ?> <span style="color:#48AD42;"><?php echo $resultcount; ?> </span></b></div></div>
  	<div class="card-body">       
                    
                      
                       



<table class="table table-bordered table-hover dt-responsive text-start display wrap" id="categories"  cellspacing="0" width="100%">
		<thead class="thead-dark">
			<tr>
				<th class="text-start">#</th>
				<th class="text-start"><?php echo lang('internal_title') ?></th>
				<th class="text-start"><?php echo lang('email_title') ?></th>
				<th class="text-start"><?php echo lang('sms_content') ?></th>
				<th class="text-start"><?php echo lang('email_content') ?></th>
				<th class="text-start"><?php echo lang('status') ?></th>
                <?php if (Auth::userCan('16')): ?>
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
        <td class="text-start"><?php echo $Item->Title ?></td>
        <td class="text-start"><?php echo $Item->EmailTitle ?></td>
        <td class="text-start" style="font-size: 12px;"><div style="height: 100px;overflow: hidden;width: 200px;"><?php echo $Item->SmsContent ?></div> <strong><?php echo lang('display_next') ?></strong></td>
        <td class="text-start" style="font-size: 12px;"><div style="height: 100px;overflow: hidden;width: 200px;"><?php echo $Item->EmailTitle ?><hr style="margin: 0;padding: 0;"><?php echo $Item->EmailContent ?></div> <strong><?php echo lang('display_next') ?></strong></td>
        <td class="align-middle"><?php if ($Item->Status=='0'){ echo '<span class="text-dark"><i class="fa fa-eye"></i> '.lang('active').'</span>'; } else { echo '<span class="text-danger"><i class="fa fa-eye-slash"></i> '.lang('hidden').'</span>'; } ?></td>
        <?php if (Auth::userCan('16')): ?>    
        <td class="text-start"><a class="btn btn-success btn-sm" style="color: #FFFFFF !important;" href='javascript:UpdateSavedMsg("<?php echo $Item->id; ?>");'><?php echo lang('edit_template') ?></a></td>
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
	<div class="ip-modal" id="MsgPopup">
		<div class="ip-modal-dialog" <?php //_e('main.rtl') ?>>
			<div class="ip-modal-content text-start">
				<div class="ip-modal-header d-flex justify-content-between" >
				<h4 class="ip-modal-title"><?php echo lang('new_message_template') ?></h4>
                <a class="ip-close" title="Close" >&times;</a>
                
				</div>
				<div class="ip-modal-body" >
				<div class="alertb alert-warning" style="font-size: 12px;">
  				<strong><?php echo lang('link_notice') ?>:</strong><br>
  				<strong>[[<?php echo lang('name_table') ?>]]</strong> <?php echo lang('will_be_changed_in_client_full_name') ?><br>
  				<strong>[[<?php echo lang('first_name') ?>]]</strong> <?php echo lang('will_be_replaced_in_private_name') ?>.<br>
  				<strong>[[<?php echo lang('mail_short_link') ?>]]</strong> <?php echo lang('will_be_changed_to_client_mail') ?><br>
  				<strong>[[<?php echo lang('telephone') ?>]]</strong> <?php echo lang('will_be_change_to_phone') ?>.<br>
  				<strong>[[<?php echo lang('full_representative_name') ?>]]</strong> <?php echo lang('will_be_replaced_in_representative_fullname') ?><br>
  				<strong>[[<?php echo lang('representative_name') ?>]]</strong> <?php echo lang('will_be_replaced_in_representative_firstname') ?>
  				</div>

<form action="AddSavedMsg"  class="ajax-form clearfix">
                <div class="form-group" >
                <label><?php echo lang('internal_message_title') ?></label>
                <input type="text" name="Title" id="Title" class="form-control" placeholder="<?php echo lang('message_title') ?>">
                </div>     
                <div class="form-group" >
                <label><?php echo lang('message_content_for_sms') ?> <span  style="font-size: 12px;">(<span id="count"><?php echo lang('zero_chars_zero_messages') ?></span>)</span></label>
                <textarea name="SmsContent" id="SmsContent" class="form-control" rows="3"></textarea>
                </div>     
                <div class="form-group" >
                <label><?php echo lang('email_full_title') ?></label>
                <input type="text" name="EmailTitle" id="EmailTitle" class="form-control" placeholder="<?php echo lang('message_title') ?>">
                </div>     
                <div class="form-group" >
                <label><?php echo lang('email_full_content') ?></label>
                <textarea name="EmailContent" id="EmailContent" class="form-control summernote" rows="15"></textarea>
                </div>     

				</div>
				<div class="ip-modal-footer d-flex justify-content-between">
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
	<div class="ip-modal" id="MsgEditPopup" data-backdrop="static" data-keyboard="false" aria-hidden="true">
		<div class="ip-modal-dialog" <?php //_e('main.rtl') ?>>
			<div class="ip-modal-content text-start">
				<div class="ip-modal-header d-flex justify-content-between" >
				<h4 class="ip-modal-title"><?php echo lang('edit_message_template') ?></h4>
                <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" >&times;</a>
                
				</div>
				<div class="ip-modal-body" >
				<div class="alertb alert-warning" style="font-size: 12px;">
  				<strong><?php echo lang('link_notice') ?>:</strong><br>
  				<strong>[[<?php echo lang('name_table') ?>]]</strong> <?php echo lang('will_be_changed_in_client_full_name') ?>.<br>
  				<strong>[[<?php echo lang('first_name') ?>]]</strong> <?php echo lang('will_be_replaced_in_private_name') ?><br>
  				<strong>[[<?php echo lang('mail_short_link') ?>]]</strong> <?php echo lang('will_be_changed_to_client_mail') ?><br>
  				<strong>[[<?php echo lang('telephone') ?>]]</strong> <?php echo lang('will_be_change_to_phone') ?><br>
  				<strong>[[<?php echo lang('full_representative_name') ?>]]</strong> <?php echo lang('will_be_replaced_in_representative_fullname') ?><br>
  				<strong>[[<?php echo lang('') ?>]]</strong> <?php echo lang('will_be_replaced_in_representative_firstname') ?>
  				</div>

<form action="EditSavedMsg"  class="ajax-form clearfix">
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
		$("#SmsContent").keyup(function(){
  var LengthM = $(this).val().length;
  var LengthT = Math.ceil(($(this).val().length)/<?php echo $SettingsInfo->SMSLimit; ?>);
$("#count").text(LengthM + lang(' chars_divided_to')+ LengthT +lang(' messegaes_expected'));
});

	$(document).ready(function() {
 $('.summernote').summernote({
        placeholder: '<?php echo lang('type_message_content') ?>',
        tabsize: 2,
        height: 153,
	   toolbar: [
    // [groupName, [list of button]]
    ['style', ['bold', 'italic', 'underline', 'clear']],
    ['font', ['strikethrough']],
    ['para', ['ul', 'ol','link']]
  ]
      });
});	


$(function() {
			var time = function(){return'?'+new Date().getTime()};
			
			// Header setup
			$('#MsgPopup').imgPicker({
			});
			// Header setup
			$('#MsgEditPopup').imgPicker({
			});
	
});


</script>
<script>
$(function() {
			
			// Header setup
			$('#AddTechPopup').imgPicker({
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