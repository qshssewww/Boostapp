<?php 
require_once '../app/init.php'; 
$pageTitle = lang('settings_permission');
require_once '../app/views/headernew.php';
?>


<?php if (Auth::check()):?>
<?php if (Auth::userCan('125')): ?>
<?php
$CompanyNum = Auth::user()->CompanyNum;
$Items = DB::table('roles')->where('CompanyNum','=', $CompanyNum)->orderBy('id', 'ASC')->get();
$resultcount = count($Items);


CreateLogMovement(lang('permission_settings_log'), '0');	

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

   var dt_dom = '<<"d-flex justify-content-between w-100 mb-10" <rf><B>> t <"mt-10 d-flex justify-content-between w-100" <><ip> >>' ;  
	 if( $("html").attr("dir") == "rtl"){
      dt_dom = "Bfrtip" ;
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
			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: '<?php echo lang('settings_permission') ?>', className: 'btn btn-dark',exportOptions: {columns: [ 0, 1, 2 ]}},
			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: '<?php echo lang('settings_permission') ?>' , className: 'btn btn-dark',exportOptions: {columns: [ 0, 1, 2 ]}},
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
<i class="fas fa-user-lock fa-fw"></i> ניהול הרשאות <span style="color:#48AD42;"><?php //echo $resultcount; ?> </span>
</div>
</h3>
</div>

<div class="col-md-2 col-sm-12 order-md-2 pb-1">
<a href="#" data-ip-modal="#RolesPopup" class="btn btn-success btn-block" name="Items"  ><i class="fas fa-plus-circle fa-fw"></i> הוסף הרשאה חדשה</a>  
</div>


</div>

<nav aria-label="breadcrumb" >
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-dark">ראשי</a></li>
  <li class="breadcrumb-item"><a href="SettingsDashboard.php" class="text-dark">הגדרות</a></li>
  <li class="breadcrumb-item active" aria-current="page">ניהול הרשאות</li>
  </ol>  
</nav>     -->

<a href="javascript:;" class="floating-plus-btn d-flex bg-primary" data-ip-modal="#RolesPopup" title="<?php echo lang('new_permission') ?>">
    <i class="fal fa-plus fa-lg margin-a"></i>
</a>

<div class="row">
<?php include("SettingsInc/RightCards.php"); ?>

<div class="col-md-10 col-sm-12 order-md-1">	


    <div class="card spacebottom">
    <div class="card-header text-start d-flex justify-content-between" >
      <div>
    <i class="fas fa-user-lock"></i> <b><?php echo lang('settings_permission') ?> <span style="color:#48AD42;"><?php echo $resultcount; ?> </span></b>
    </div>
 	</div>
  	<div class="card-body">       
                    
                      
                       



<table class="table table-bordered table-hover dt-responsive text-start display wrap" id="categories"  cellspacing="0" width="100%">
		<thead class="thead-dark">
			<tr>
				<th class="text-start">#</th>
				<th class="text-start"><?php echo lang('task_title') ?></th>
                <th class="text-start"><?php echo lang('permissions_single') ?></th>
                <th class="text-start lastborder"><?php echo lang('actions') ?></th> 
			</tr>
		</thead>
		<tbody>
              <?php 

$i = 1;

foreach ($Items as $Item) {

 if ($Item->permissions=='*'){
$SoftNames = lang('all_permissions');    
}   
else {    
$z = '1';
$myArray = explode(',', $Item->permissions);	
$SoftNames = '';

$SoftInfos = DB::table('roleslist')->whereIn('id', $myArray)->where('Status',0)->get();
$SoftCount = count($SoftInfos);
	
foreach ($SoftInfos as $SoftInfo){

$SoftNames .= $SoftInfo->Title.' :: '.$SoftInfo->Action;

if($SoftCount==$z){}else {	
$SoftNames .= ', ';	
}
	
++$z; 	
}	

$SoftNames = $SoftNames;	    
 
}
    
?>        
        <tr>
        <td class="text-start"><?php echo $i?></td>
        <td class="text-start"><?php echo $Item->Title ?></td>
        <td class="text-start"><?php echo $SoftNames; ?></td>   
        <?php  if ($i=='1'){ ?>
        <td class="text-start"></td>    
        <?php } else { ?>    
        <td class="text-start"><a class="btn btn-success btn-sm" style="color: #FFFFFF !important;" href='javascript:UpdateRoles("<?php echo $Item->id; ?>");'><?php echo lang('edit_permission') ?></a></td> 
        <?php } ?>    
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

    
<style>
    
.BigDialog .card-header {
    padding: .10rem 1.25rem;
    
    }
    
.BigDialog .custom-checkbox .custom-control-input:checked ~ .custom-control-label::before {
    background-color: #48AD42;
}    
  
.glyphicon:before {
 visibility: visible;
}
.glyphicon.glyphicon-star-empty:checked:before {
   content: "\e006";
}
input[type=checkbox].glyphicon{
    visibility: hidden;
    
}
</style>   


<!-- DepartmentsPopup -->
	<div class="ip-modal" id="RolesPopup">
		<div class="ip-modal-dialog BigDialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content text-start">
				<div class="ip-modal-header  d-flex justify-content-between" >
				      <h4 class="ip-modal-title"><?php echo lang('add_new_permission') ?></h4>
              <a class="ip-close" title="Close" style="float:left;">&times;</a>
				</div>
				<div class="ip-modal-body" >
                <form action="AddRoles"  class="ajax-form clearfix">
                <div class="form-group" >
                <label><?php echo lang('permission_title') ?></label>
                <input type="text" name="Title" id="Title" class="form-control" placeholder="<?php echo lang('permission_title') ?>">
                </div>     

                <hr>

                    
                <div id="accordion">
                    
                 <?php 
                $RolesCategories = DB::table('rolescategory')->where('Status', 0)->orderBy('id', 'ASC')->get();
                foreach ($RolesCategories as $RolesCategorie) {
                ?>  
                    
  <div class="card">
    <div class="card-header" id="headingOne<?php echo $RolesCategorie->id; ?>"  data-toggle="collapse" data-target="#collapseOne<?php echo $RolesCategorie->id; ?>" aria-expanded="false" aria-controls="collapseOne<?php echo $RolesCategorie->id; ?>">
      <h5 class="mb-0">
        <button type="button" class="btn btn-link" data-toggle="collapse" data-target="#collapseOne<?php echo $RolesCategorie->id; ?>" aria-expanded="false" aria-controls="collapseOne<?php echo $RolesCategorie->id; ?>">
          <?php echo $RolesCategorie->Title; ?>
        </button>
      </h5>
    </div>

    <div id="collapseOne<?php echo $RolesCategorie->id; ?>" class="collapse" aria-labelledby="headingOne<?php echo $RolesCategorie->id; ?>" data-parent="#accordion">
      <div class="card-body">
                <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="CheckAll<?php echo $RolesCategorie->id; ?>">
                <label class="custom-control-label" for="CheckAll<?php echo $RolesCategorie->id; ?>"><?php echo lang('select_all') ?></label>
                </div>
  
  <table class="table">
  <thead>
    <tr>
      <th width="5%">#</th>
      <th><?php echo lang('task_title') ?></th>
      <th width="10%"><?php echo lang('watch_single') ?></th>
      <th width="10%"><?php echo lang('edit') ?></th>
    </tr>
  </thead>
  <tbody>
  <?php 
  $i = '1';

  $Roles1 = DB::table('roleslist')->where('Category', '=', $RolesCategorie->id)
      ->where('Status',0)
      ->groupBy('Group')->orderBy('id', 'ASC')->get();
  foreach ($Roles1 as $Role1) {
  ?>    
   <tr>
   <td><?php echo $i; ?></td>
   <td><?php echo $Role1->Action; ?></td>  
  <?php      
  $RoleGroups = DB::table('roleslist')->where('Category', '=', $RolesCategorie->id)
      ->where('Group', '=', $Role1->Group)->where('Status',0)
      ->orderBy('View', 'ASC')->orderBy('Action', 'ASC')->get();
  foreach ($RoleGroups as $RoleGroup) {
  ?>  
        
   <?php if ($RoleGroup->Single=='1' && $RoleGroup->View=='0') { ?>       
   <td align="center">
   <div class="pretty p-icon p-toggle p-smooth p-plain">
        <input type="checkbox" class="CheckAll<?php echo $RolesCategorie->id; ?>" name="CheckBoxRoles[]" value="<?php echo $RoleGroup->id; ?>" />
        <div class="state p-success-o p-on">
            <i class="icon fas fa-eye"></i>
            <label></label>
        </div>
        <div class="state p-off">
            <i class="icon fas fa-eye-slash"></i>
            <label></label>
        </div>
    </div>
   </td>   
   <td></td>       
   <?php } else if ($RoleGroup->Single=='1' && $RoleGroup->View=='1'){   ?> 
   <td></td>       
   <td align="center">
   <div class="pretty p-icon p-toggle p-smooth p-plain">
        <input type="checkbox" class="CheckAll<?php echo $RolesCategorie->id; ?>" name="CheckBoxRoles[]" value="<?php echo $RoleGroup->id; ?>" />
        <div class="state p-success-o p-on">
            <i class="icon fas fa-edit"></i>
            <label></label>
        </div>
        <div class="state p-off">
            <i class="icon fas fa-times"></i>
            <label></label>
        </div>
    </div>       
   </td>       
   <?php } else { ?>       
   <td align="center">
       
     <?php if ($RoleGroup->View=='0'){ ?>
       
    <div class="pretty p-icon p-toggle p-smooth p-plain">
        <input type="checkbox" class="CheckAll<?php echo $RolesCategorie->id; ?>" name="CheckBoxRoles[]" value="<?php echo $RoleGroup->id; ?>" />
        <div class="state p-success-o p-on">
            <i class="icon fas fa-eye"></i>
            <label></label>
        </div>
        <div class="state p-off">
            <i class="icon fas fa-eye-slash"></i>
            <label></label>
        </div>
    </div>     
       
    <?php } else  {  ?>  
     <div class="pretty p-icon p-toggle p-smooth p-plain">
        <input type="checkbox" class="CheckAll<?php echo $RolesCategorie->id; ?>" name="CheckBoxRoles[]" value="<?php echo $RoleGroup->id; ?>" />
        <div class="state p-success-o p-on">
            <i class="icon fas fa-edit"></i>
            <label></label>
        </div>
        <div class="state p-off">
            <i class="icon fas fa-times"></i>
            <label></label>
        </div>
    </div>   
    <?php } ?>   
       
       
       
  </td>
  <?php  } } ?>     
   </tr>      
   <?php ++$i; } ?>     
      
      
 </tbody>
      
          </table>
      

          
          
      </div>
    </div>
  </div>
                    
        <?php } ?>              

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
	<div class="ip-modal" id="EditRolespPopup" tabindex="-1">
		<div class="ip-modal-dialog BigDialog" <?php _e('main.rtl') ?>>
			<div class="ip-modal-content text-start">
				<div class="ip-modal-header" >
                <a class="ip-close" title="Close" data-dismiss="modal" aria-hidden="true" style="float:left;">&times;</a>
				<h4 class="ip-modal-title"><?php echo lang('edit_permission') ?></h4>
                
				</div>
				<div class="ip-modal-body" >
<form action="EditRoles"  class="ajax-form clearfix">
<input type="hidden" name="ItemId">
<div id="result">


  
</div>

				</div>
				<div class="ip-modal-footer">
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
			$('#RolesPopup').imgPicker({
			});
			// Header setup
			$('#EditRolesPopup').imgPicker({
			});
	
});
    
 <?php 
$RolesCategories = DB::table('rolescategory')->orderBy('id', 'ASC')->get();
foreach ($RolesCategories as $RolesCategorie) {
?>      
    
$('#CheckAll<?php echo $RolesCategorie->id ?>').click(function(event) {   
    if(this.checked) {
        // Iterate each checkbox
        $('.CheckAll<?php echo $RolesCategorie->id ?>').each(function() {
            this.checked = true;                        
        });
    } else {
        $('.CheckAll<?php echo $RolesCategorie->id ?>').each(function() {
            this.checked = false;                       
        });
    }
});      
    
<?php } ?>    
    
</script>


<?php else: ?>
<?php redirect_to('../index.php'); ?>
<?php endif ?>


<?php endif ?>

<?php if (Auth::guest()): ?>

<?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>