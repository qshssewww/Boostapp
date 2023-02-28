<?php 

ini_set("max_execution_time" , 0 );

require_once '../app/init.php'; 
$pageTitle = lang('manage_dynamic_forms');
require_once '../app/views/headernew.php';
?>

<?php if (Auth::check()):?>
<?php if (Auth::userCan('152')): ?>
<?php

$CompanyNum = Auth::user()->CompanyNum;
$SettingsInfo = DB::table('settings')->where('CompanyNum','=', $CompanyNum)->first();

$Items = DB::table('dynamicforms')->where('CompanyNum','=', $CompanyNum)->orderBy('Status', 'ASC')->orderBy('created', 'DESC')->get();


 $Supplier = DB::table('appsettings')->where('CompanyNum',  $CompanyNum)->first();
 $UserId = User::find(Auth::user()->id);
 $SettingsInfo = DB::table('settings')->where('CompanyNum',  $CompanyNum)->first();


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
//	        "scrollY":        "450px",
//            "scrollCollapse": true,
            "paging":         false,
	   "ordering": false,
//	         fixedHeader: {
//        headerOffset: 50
//    },

	     //  bStateSave:true,
		   // serverSide: true,
	        pageLength: 100,
	     dom: '<<"d-flex justify-content-between w-100 mb-10" <rf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>' ,
		//info: true,
	  
	    buttons: [ 
        <?php if (Auth::userCan('98')): ?>
		//{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},
			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: '<?php echo lang('settings_automation') ?>', className: 'btn btn-dark',exportOptions: {columns: [ 0, 1, 2 ]}},
			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: '<?php echo lang('settings_automation') ?>' , className: 'btn btn-dark',exportOptions: {columns: [ 0, 1, 2 ]}},
           // 'pdfHtml5'
		<?php endif ?>
			
        ],
	   
	//	order: [[0, 'DESC']]

	   	 	   
        } );
		

	
	
	
});


</script>



<link href="assets/css/fixstyle.css" rel="stylesheet">
<!-- include summernote css/js -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.9/summernote-bs4.js"></script>


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
<i class="fab fa-wpforms"></i> ניהול טפסים דינאמיים</span>
</div>
</h3>
</div>


<div class="col-md-2 col-sm-12 order-md-2 pb-1"> 
<a href="AddForms.php" class="btn btn-success btn-block" name="Items"  ><i class="fas fa-plus-circle fa-fw"></i> צור טופס חדש</a>    
</div>    
    
    
</div>

<nav aria-label="breadcrumb" >
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-dark">ראשי</a></li>
  <li class="breadcrumb-item active">ניהול טפסים </li>
  </ol>  
</nav>     -->

<a href="AddForms.php" class="floating-plus-btn d-flex bg-primary" title="<?php echo lang('new_form') ?>">
    <i class="fal fa-plus fa-lg margin-a"></i>
</a>

<div class="row">
<?php include("SettingsInc/RightCards.php"); ?>

<div class="col-md-10 col-sm-12">	

<div class="tab-content">		
 <div class="tab-pane fade show active text-start" role="tabpanel" id="DynamicForms">		

    <div class="card spacebottom">
    <div class="card-header text-start" >
    <i class="fab fa-wpforms"></i> <b><?php echo lang('settings_docs') ?> </b>
 	</div>    
  	<div class="card-body text-start" >       
<table class="table table-bordered table-hover dt-responsive text-start display wrap" id="categories"  cellspacing="0" width="100%">
		<thead class="thead-dark">
			<tr>
				<th class="text-start">#</th>
				<th class="text-start" width="25%"><?php echo lang('task_title') ?></th>
				<th class="text-start"><?php echo lang('branch') ?></th>
				<th class="text-start"><?php echo lang('create_date') ?></th>
                <th class="text-start"><?php echo lang('signed_clients') ?></th>
				<th class="text-start"><?php echo lang('statistics') ?></th>
                <th class="text-start"><?php echo lang('status') ?></th>
                <th class="text-start lastborder"><?php echo lang('path_settings') ?></th>
			</tr>
		</thead>
		<tbody>
<?php 
$i = '1';
foreach ($Items as $Item){    
	
$FormsCounts = DB::table('dynamicforms_answers')->where('FormId', $Item->id)->where('Companynum', $CompanyNum)->where('AnswerStatus', '0')->count();
//$FormsCounts = count($FormsCounts);
	
$BarndSelect = @$Item->Brands;
if ($BarndSelect=='' || $BarndSelect=='0'){
$BrandsName = lang('all_branch');    
}
else if ($BarndSelect=='BA999'){
$BrandsName = lang('all_branch');	
}	
else {
$i = '1';
$myArray = explode(',', @$BarndSelect);	
$BrandsName = '';	
$BrandInfos = DB::table('brands')->whereIn('id', $myArray)->where('FinalCompanynum', $CompanyNum)->get();
$BrandCount = count($BrandInfos);
	
foreach ($BrandInfos as $BrandInfo){

$BrandsName .= $BrandInfo->BrandName;

if($BrandCount==$i){}else {	
$BrandsName .= ', ';	
}
	
++$i; 	
}	

$BrandsName = $BrandsName;      
                
}   	

$VaildIcon = '';	
if ($Item->VaildType!='0' && $Item->Status!='1'){
$VaildIcon = '// <i class="fas fa-history" data-toggle="tooltip" data-placement="top" data-original-title="'.lang('validity_set_dynamic').'"></i>';	
}	
	
	
?>            
<tr>
<td><?php echo $Item->GroupNumber; ?></td> 
<td><a href="AddForms.php?formId=<?php echo $Item->id; ?>"><span class="text-primary"><?php echo $Item->name; ?></span></a></td> 
<td><?php echo $BrandsName; ?></td>	
<td><?php echo with(new DateTime($Item->created))->format('d/m/Y H:i'); ?></td> 	
<td class="text-primary text-center"><a href="DynamicFormsResults.php?u=<?php echo $Item->id; ?>" class="text-primary" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo lang('clients_that_signed') ?>"><span class="text-primary"><strong><?php echo $FormsCounts; ?></strong></span></a></td>
	
<td class="text-primary text-center"><a href="DynamicFormsStatistics.php?u=<?php echo $Item->id; ?>" class="text-primary" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo lang('view_statistics') ?>"><span class="text-primary"><strong><i class="fas fa-chart-pie"></i></strong></span></a></td> 
	
<td><?php if ($Item->Status=='0') { echo lang('active'); } else { echo lang('not_active'); } ?> <?php echo $VaildIcon; ?></td> 
<td class="text-center"><a href="AddForms.php?formId=<?php echo $Item->id; ?>"><span class="text-info"><i class="fas fa-edit" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo lang('edit_two') ?>"></i></span></a>
<?php if ($Item->Status=='0') { ?>	
<a href="#link<?php echo $Item->id; ?>" data-Subject="<?php echo htmlentities($Item->name); ?>" data-brand="<?php if ($Item->Brands!='0') { echo htmlentities($Item->Brands); } else { echo ''; } ?>" data-link="<?php echo get_newboostapp_domain() ?>/forms/DynamicForms.php?GI=<?php echo $Item->id; ?>&GN=<?php echo $Item->GroupNumber; ?>"><span class="text-primary"><i class="fas fa-link" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo lang('send_link') ?>"></i></span></a>
<?php } ?>	
<a href='javascript:UpdateForms("<?php echo $Item->id; ?>");'><span class="text-dark"><i class="fas fa-cog" data-toggle="tooltip" data-placement="top" data-original-title="<?php echo lang('manage') ?>"></i></span></a> 	
    
</td>     
</tr>        
<?php ++$i; } ?>
        </tbody>
	
	
        </table>         

    
    </div>
    </div>
	 
	 
</div>		
	
	
<div class="tab-pane fade text-start" role="tabpanel" id="appHealth" >
<div class="card spacebottom">
<div class="card-header text-start"><strong><?php echo lang('health_declaration') ?></strong></div>    
<div class="card-body">  
 
      <div class="row text-start px-15" >
      <a href="AddHealth.php"><span class="btn btn-primary text-white"><?php echo lang('create_new_form') ?></span></a>            
      </div>     
    
    
<table class="table">
    
<thead>
    <th><?php echo lang('health_version_number') ?></th>
    <th><?php echo lang('task_title') ?></th>
    <th><?php echo lang('version_date') ?></th>
    <th><?php echo lang('clients_signed') ?></th>
    <th><?php echo lang('app_resign') ?></th>
    <th><?php echo lang('actions') ?></th>
    
</thead>    

<tbody>
<?php 
$i = '1';    
$HealthInfos = DB::table('healthforms')->where('CompanyNum',  $CompanyNum)->orderBy('id','DESC')->get();  
foreach ($HealthInfos as $HealthInfo) {   
 
$HealthCounts = DB::table('healthforms_answers')->where('CompanyNum',  $CompanyNum)->where('FormId',  $HealthInfo->id)->count();    
    
    
if ($i=='1'){
$HealthClass = 'table-success';    
}  
else {
$HealthClass = '';     
}  
    
if ($HealthInfo->forceRenew=='1'){
$HealthRenew = lang('yes');   
}    
else {
$HealthRenew = lang('no');     
} 
    
?>    
<tr class="<?php echo $HealthClass; ?>">
<td><?php echo $HealthInfo->GroupNumber; ?></td>
<td><?php echo $HealthInfo->name; ?></td>
<td><?php echo with(new DateTime($HealthInfo->created))->format('d/m/Y H:i'); ?></td>
<td class="text-primary"><a href="HealthClientList.php?u=<?php echo $HealthInfo->id; ?>" class="text-primary"><span class="text-primary"><?php echo $HealthCounts; ?></span></a></td>
<td><?php echo $HealthRenew; ?></td>
<td><a href="AddHealth.php?formId=<?php echo $HealthInfo->id; ?>"><span class="text-primary"><?php echo lang('edit_or_update_version') ?></span></a></td>    
</tr>
<?php ++ $i; } ?>    
</tbody>    
    
</table>    
    
    
    
</div></div></div>   
   
    
<div class="tab-pane fade text-start" role="tabpanel" id="appTakanon" >
<div class="card spacebottom">
<div class="card-header text-start"><strong><?php echo lang('terms') ?></strong></div>    
<div class="card-body">  
<form action="AppTakanon"  class="ajax-form clearfix"  autocomplete="off">
				
  				<div class="form-group">
                <textarea class="form-control summernote" name="Content"><?php echo @$Supplier->Takanon ?></textarea>
                </div>

	
				<div class="form-group"> 
                <label><?php echo lang('app_terms_resign') ?></label>
                <select name="SignAgian" id="SignAgian" class="form-control">
                <option value="1"><?php echo lang('yes') ?></option>
                <option value="2" selected><?php echo lang('no') ?></option>
                </select>
                </div>  	
	
	
<hr>	
	
	
	
	
<?php if (Auth::userCan('12')): ?>     
<div class="form-group">
<button type="submit" class="btn btn-success btn-lg"><?php echo lang('update') ?></button>	
</div>
<?php endif ?>    
</div>
</form>
</div></div> 	
	
	
	
	
	
	
	
	
	
</div>	 
	 
	 
	 
	 
	 
	 

	</div> 
</div>

</div>


<!-- Forms -->
	<div class="ip-modal" id="UpdateFormsPopup" tabindex="-1" data-backdrop="static" data-keyboard="false" aria-hidden="true">
		<div class="ip-modal-dialog">
			<div class="ip-modal-content text-start">
				<div class="ip-modal-header d-flex justify-content-between" >
				<h4 class="ip-modal-title"><?php echo lang('manage_form') ?></h4>
                <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" >&times;</a>
                
				</div>
				<div class="ip-modal-body" >
<form action="UpdateForms"  class="ajax-form clearfix">
<input type="hidden" name="FormsId">
<div id="resultUpdateFormsPopup">


  
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
	<!-- end Forms -->

<!-- Modal -->
<div class="modal fade text-start" id="sendUrlToClientPush" tabindex="-1" role="dialog" aria-labelledby="sendUrlToClientPushLabel" aria-hidden="true" >
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header d-flex justify-content-between">
            <input type="hidden" value="">
            <h5 class="modal-title"
                id="sendUrlToClientPushLabel"><?php echo lang('send_link_to_clients') ?> </h5>
            <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div>
            <input class="js-studio-url" type="hidden" value="<?php echo $SettingsInfo->StudioUrl ?>">
        </div>
      <div class="modal-body">



      <table class="table table-hover dt-responsive text-start display wrap" id="dataTableClients"  cellspacing="0" width="100%">
        <thead>
            <tr class="bg-dark text-white">
                <th data-name="select" data-bSortable="false"></th>
                <th data-name="fullName"><?php echo lang('client_name') ?></th>
                <th data-name="status"><?php echo lang('status') ?></th>
                <th data-name="phone"><?php echo lang('telephone') ?></th>
                <th data-name="age"><?php echo lang('age') ?></th>
                <th data-name="rank"><?php echo lang('rank') ?></th>
                <!-- <th data-name="membership" data-bSortable="false">סוג מנוי</th> -->
                <th data-name="branch" data-bSortable="false"><?php echo lang('branch') ?></th>
                <th data-name="copy" data-bSortable="false"><?php echo lang('link') ?></th>
                
            </tr>
            <tr>
                <th ></th>
                <th >
                    <input data-search="clientName" type="text" name="clientName" id="clientNameFilter" class="form-control" placeholder="חפש לקוח">
                </th>
                <th>
                <select data-search="clientStatus" id="clientStatusFilter" class="form-control">
                    <option value="0"><?php echo lang('active') ?></option>
                    <option value="2"><?php echo lang('interested_single') ?></option>
					<option value="1"><?php echo lang('not_active') ?></option>
                    <option value=""><?php echo lang('all') ?></option>
                </select>
                </th>
                <th >
                    <input data-search="clientPhone" type="text" name="clientPhone" id="clientPhoneFilter" class="form-control" placeholder="<?php echo lang('search_phone') ?>">
                </th>
                <th >
                    <input data-search="clientAge" type="text" name="clientAge" id="clientAgeFilter" class="form-control" placeholder="<?php echo lang('search_age') ?>">
                </th>
                <th >
                    <select data-search="clientRanks" multiple="multiple" id="clientRanksFilter" class="form-control" size="1" style="width:100%;" placeholder="<?php echo lang('search_membership') ?>"></select>
                </th>
                <!-- <th >
                    <select data-search="productIds" multiple="multiple" id="productsFilter" class="form-control" size="1" style="width:100%;" placeholder="חפש מוצר"></select>
                </th> -->
                <th >
                    <select data-search="branchIds" multiple="multiple" id="branchFilter" class="form-control" size="1" style="width:100%;" placeholder="<?php echo lang('search_by_branch') ?>"></select>
                </th>
                <th ></th>
                
            </tr>
        </thead>

        <tbody></tbody>
        <tfoot>
            <tr>
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

      </div>
        
<!--
      <div class="ip-modal-footer">
                <div class="ip-actions">
                <button type="button" name="submit" class="btn btn-primary text-white">שמור שינויים</button>
                </div>
                
				<button type="button" class="btn btn-dark ip-close" data-dismiss="modal">סגור</button>
                
				</div> 
-->
        
        
        
      <div class="modal-footer">
        <button type="button" class="btn btn-dark" data-dismiss="modal"><?php echo lang('close') ?></button>
      </div>
    </div>
  </div>
</div>

<?php 
$Subject = "";
$Content = lang('url_ajax');
include('Reports/popupSendByClientId.php'); 
?>

<script>
	
$(document).ready(function() {	
	 
 $('.summernote').summernote({
        placeholder: '<?php echo lang('type_content') ?>',
        tabsize: 2,
        height: 400,
        callbacks: {
            onPaste: function(event) {
                event.preventDefault();
                let pastedData = event.originalEvent.clipboardData.getData('text');
                pastedData = pastedData.replaceAll('\r', "").replaceAll('\n', "<br/>");
                document.querySelector('.note-editable').innerHTML += pastedData;
            }
        },
	   toolbar: [
    // [groupName, [list of button]]
    ['style', ['bold', 'italic', 'underline', 'clear']],
    ['font', ['strikethrough']],
    ['para', ['ul', 'ol']]
  ]
      });
});	
	
	
$('[data-toggle="tabajax"]').click(function(e) {
    var $this = $(this),
        loadurl = $this.attr('href'),
        targ = $this.attr('data-target');

    $.get(loadurl, function(data) {
        $(targ).html(data);
    });

    $this.tab('show');
    return false;
});		

	//שינוי עמוד בהתאם לטאב
$('#newnavid a').click(function(e) {
  e.preventDefault();
  $(this).pill('show');
$('.tab-content > .tab-pane.active').jScrollPane();   
$('html,body').scrollTop(0);
});


$("a").on("shown.bs.tab", function(e) {
    
  var id = $(e.target).attr("href").substr(1);
  window.location.hash = id;
  $('html,body').scrollTop(0);

});    
    
    
    
// on load of the page: switch to the currently selected tab
var hash = window.location.hash;
$('.nav-tabs a[href="' + hash + '"]').tab('show');
//סיום שינוי עמוד בהתאם לטאב
	
	
	
        function copyToClipboard(value) {

            // Create a "hidden" input
            var aux = document.createElement("input");

            // Assign it the value of the specified element
            aux.setAttribute("value", value);

            // Append it to the body
            document.body.appendChild(aux);

            // Highlight its content
            aux.select();

            // Copy the highlighted text
            document.execCommand("copy");

            // Remove it from the body
            document.body.removeChild(aux);

        }	
	
var table = $('#categories');
	
table.on('click', '[data-link]', function (e) {
            e.preventDefault();
            var el = $(this);
            var url = el.attr('data-link');
	        var Subject = el.attr('data-Subject');
	        var brand = el.attr('data-brand');
            sendShortLinkToClient(url, el, Subject, brand);

            
        });

          var sendUrlToClientPush = $('#sendUrlToClientPush').on('shown.bs.modal', function (e) {
            boostAppDataTable.skipAjax = true;
            $('#dataTableClients').dataTable().fnAdjustColumnSizing();
        });

        var sendUrlToClientPush = $('#sendUrlToClientPush');

        var popupClientPushFORM = $('form[action="SendClientPushReport"]');
        var clientPopUpClientPushFROMHTML = $('div:first', popupClientPushFORM);
        clientPopUpClientPushFROMHTML.html(clientPopUpClientPushFROMHTML.html() + '<br><strong>[['+'<?php echo lang('link') ?>'+']]</strong> '+'<?php echo lang('replace_link_dynamicforms') ?>');
        popupClientPushFORM.append('<input type="hidden" name="formsUrl" value="">');

        var formsUrl = $('[name="formsUrl"]', popupClientPushFORM);    
	
	
	function sendShortLinkToClient(url, el, Subject, brand) {

            copyToClipboard(url);
            formsUrl.val(encodeURI(url));
		    if (brand!='' || brand!='0'){	
			var values = brand;
            var selectedValues = values.split(",");
			$("#branchFilter").val(selectedValues).trigger("change");	
			}
		    $('[name="Subject"]').val(decodeURI(Subject));
            var data = table.DataTable().data()[el.closest('tr').index()]
            sendUrlToClientPush
                .find('.modal-header input').val(url).end()
                // .find('.modal-body').html(JSON.stringify(data)).end()
                .modal('toggle')
        }


</script>


    <script>
        
        var categoriesDataTable;
	    BeePOS.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
        
        boostAppDataTable = {
            id: '#dataTableClients',
            language: BeePOS.options.datatables,
            buttons: {
                allowClientPush: true,
                excel: false,
                csv: false
            }
        }
        

        
    </script>
    <script src="Reports/js/clients.js?v=<?php echo filemtime('Reports/js/clients.js')?>"></script>
    <script src="<?php echo app()->url('office/js/datatable/dataTables.checkboxes.min.js') ?>?v=<?php echo filemtime(app_path('../office/js/datatable/dataTables.checkboxes.min.js'))?>"></script>



<?php else: ?>
<?php redirect_to('../index.php'); ?>
<?php endif ?>


<?php endif ?>

<?php if (Auth::guest()): ?>

<?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>
