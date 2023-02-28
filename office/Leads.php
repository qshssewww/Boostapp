<?php 


require_once '../app/init.php'; 

$pageTitle = lang('manage_interested');
require_once '../app/views/headernew.php';
?>


<?php if (Auth::check()):?>
<?php if (Auth::userCan('47')): ?>
<?php
$CompanyNum = Auth::user()->CompanyNum;

CreateLogMovement(lang('entered_manage_leads'), '0');
$Category2 = DB::table('automation')->where('CompanyNum','=', $CompanyNum)->where('Category','=', '2')->where('Type','=', '1')->where('Status','=', '0')->count();
?>


<link href="<?php echo App::url('CDN/datatables/dataTables.bootstrap4.min.css') ?>" rel="stylesheet">
<link href="<?php echo App::url('CDN/datatables/buttons.bootstrap4.min.css') ?>" rel="stylesheet">

<link href="<?php echo App::url('CDN/datatables/responsive.bootstrap4.min.css') ?>" rel="stylesheet">
<link href="<?php echo App::url('CDN/datatables/fixedHeader.dataTables.min.css') ?>" rel="stylesheet">
<link href="<?php echo App::url('CDN/datatables/responsive.dataTables.min.css') ?>" rel="stylesheet">



<script src="<?php echo App::url('CDN/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?php echo App::url('CDN/datatables/dataTables.buttons.min.js') ?>"></script>

<script src="<?php echo App::url('CDN/datatables/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?php echo App::url('CDN/datatables/dataTables.responsive.js') ?>"></script>
<script src="<?php echo App::url('CDN/datatables/responsive.bootstrap4.min.js') ?>"></script>
<script src="//cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
<script src="<?php echo App::url('CDN/datatables/jszip.min.js') ?>"></script>
<script src="<?php echo App::url('CDN/datatables/pdfmake.min.js') ?>"></script>
<script src="<?php echo App::url('CDN/datatables/vfs_fonts.js') ?>"></script>
<script src="<?php echo App::url('CDN/datatables/buttons.html5.min.js') ?>"></script>

<script src="<?php echo App::url('CDN/datatables/moment.min.js') ?>"></script>
<script src="<?php echo App::url('CDN/datatables/datetime-moment.js') ?>"></script>

<script src="<?php echo App::url('CDN/datatables/dataTables.fixedHeader.min.js') ?>"></script>
<script src="<?php echo App::url('office/js/datatable/dataTables.checkboxes.min.js') ?>"></script>

<!--<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>-->

<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/he.js"></script>-->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<style>

/* not active */
.pill-1:not(.active) {
/*    background-color: rgba(255, 0, 0, 0.5);*/
color: #838383 !important;
border-top: none !important;
border-left: none !important;
border-right: none !important;     
border-bottom: none!important;
}

.pill-2:not(.active) { 
color: #838383 !important; 
border-top: none !important;
border-left: none !important;
border-right: none !important;     
border-bottom: none!important;    
}

.pill-3:not(.active) {  
color: #838383 !important;
border-top: none !important;
border-left: none !important;
border-right: none !important;     
border-bottom: none!important;    
}


/* active (faded) */
.pill-1 {
    color: #1d2124 !important;
    border-top: none !important;
    border-left: none !important;
    border-right: none !important;      
    border-bottom-width: medium !important;
    border-bottom-color: #1d2124 !important;
}

.pill-2 {
    color: #48AD42 !important;  
    border-top: none !important;
    border-left: none !important;
    border-right: none !important;    
    border-bottom-width: medium !important;
    border-bottom-color: #48AD42 !important;
}

.pill-3{
    color: #dc3545 !important;
    border-top: none !important;
    border-left: none !important;
    border-right: none !important;  
    border-bottom-width: medium !important;
    border-bottom-color: #dc3545 !important;
}

</style>

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
<i class="fas fa-users"></i> <?//= lang('manage_interested') ?> <span style="color:#48AD42;"><?php //echo @$resultcount; ?> </span>
</div>
</h3>
</div>

<div class="col-md-2 col-sm-12 order-md-2 pb-1">  
    

    
</div>


</div> -->

<!-- <nav aria-label="breadcrumb" >
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-dark"><?//= lang('main') ?></a></li>
  <li class="breadcrumb-item active"><?//= lang('manage_interested') ?></li>
  </ol>  
</nav>     -->
<?php if (Auth::userCan('51')): ?>    
<a href="javascript:void(0);" class="floating-plus-btn d-flex bg-primary" data-ip-modal="#AddNewLead" title="<?= lang('new_lead') ?>">
    <i class="fal fa-plus fa-lg margin-a"></i>
</a>
<?php endif ?>    

<div class="row">
<div class="col-md-12 col-sm-12" >	

    
   <nav>
   <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
   <a class="nav-item nav-link pill-1 active" data-target="#nav-open" id="nav-open-tab" data-toggle="tabajax" href="LeadsList/Opens.php" role="tooltip" aria-controls="nav-open" aria-selected="true"><i class="fas  fa-fw"></i> <?= lang('opened_multi') ?></a>
   <a class="nav-item nav-link pill-2" data-target="#nav-success" id="nav-success-tab" data-toggle="tabajax" href="LeadsList/success.php" role="tooltip" aria-controls="nav-success" aria-selected="false"><i class="fas fa-trophy fa-fw"></i> <?= lang('successes') ?></a>
   <a class="nav-item nav-link pill-3" data-target="#nav-fails" id="nav-fails-tab" data-toggle="tabajax" href="LeadsList/fails.php" role="tooltip" aria-controls="nav-fails" aria-selected="false"><i class="fas fa-times fa-fw"></i> <?= lang('lost_multi') ?></a>
   </div>
   </nav>   
    
    <div class="tab-content" id="nav-tabContent">
    <div class="tab-pane fade show active" id="nav-open" role="tabpanel" aria-labelledby="nav-open-tab">
     
    <div class="card spacebottom text-start">
    <div class="card-header " >
    <i class="fas fa-fw"></i> <b><?= lang('manage_open_leads') ?></b>
 	</div>    
  	<div class="card-body">       



    </div>
    </div>    
        
        
    </div>     
    
        
    <div class="tab-pane fade" id="nav-success" role="tabpanel" aria-labelledby="nav-success-tab">
    
    <div class="card spacebottom">
    <div class="card-header " >
    <i class="fas fa-trophy fa-fw"></i> <b><?= lang('manage_success_leads') ?></b>
 	</div>    
  	<div class="card-body">       



    </div>
    </div>       
        
    </div>
        
    <div class="tab-pane fade" id="nav-fails" role="tabpanel" aria-labelledby="nav-fails-tab">
    
    <div class="card spacebottom">
    <div class="card-header " >
    <i class="fas fa-times fa-fw"></i> <b><?= lang('manage_lost_leads') ?></b>
 	</div>    
  	<div class="card-body">       



    </div>
    </div>       
        
    </div>        
        
    </div>    
    
    
    
    
    

    
	</div> 
</div>

</div>

<div class="ip-modal text-start" id="AddNewLead">
		<div class="ip-modal-dialog">
			<div class="ip-modal-content">
				<div class="ip-modal-header d-flex justify-content-between">
				<h4 class="ip-modal-title"><?= lang('add_new_lead') ?></h4>
                <a class="ip-close" title="Close" data-dismiss="modal">&times;</a>

				</div>
				<div class="ip-modal-body">

				<form action="AddNewLead"  class="ajax-form clearfix" autocomplete="off">
                
                <div class="form-group" >
                <label><?= lang('choose_pipeline') ?></label>
                <select class="form-control" name="PipeLine" id="PipeLineSelect" required>
                <option value=""><?= lang('choose') ?></option>
				<?php
                $b = '1';    
				$ClassTypes = DB::table('pipeline_category')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->orderBy('id', 'ASC')->get();   
                if (!empty($ClassTypes)){     
				foreach ($ClassTypes as $ClassType) { ?> 	
				<option value="<?php echo $ClassType->id; ?>"><?php echo $ClassType->Title ?></option>
				<?php ++$b; } } else { ?>     
                <?php } ?>    
				</select>
                </div>	       
                    
                <div class="row">    
                <div class="col-md-6 col-sm-12 order-md-1">    
				<div class="form-group" >
                    <label><?= lang('first_name') ?> <em class="text-danger font-rubik">*</em></label>
                <input type="text" name="FirstName" class="form-control" required>
                </div>
				</div>	
                <div class="col-md-6 col-sm-12 order-md-2">     
				<div class="form-group" >
                    <label><?= lang('last_name') ?> <em class="text-danger font-rubik">*</em></label>
                <input type="text" name="LastName" class="form-control" required>
                </div>	
                </div>    
                    
                </div> 
                <div class= "row">
                    <div class="col-md-9">
                        <div class="form-group" >
                            <label><?= lang('phone') ?> <em class="text-danger font-rubik">*</em></label>
                            <input type="text" name="ContactMobile" id="ContactMobile" class="form-control" required pattern="^[0]*[5][0|1|2|3|4|5|8|9]{1}[0-9]{7}$" title="מספר נייד לא תקין">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>קידומת</label>
                            <select name="areaCode" id="areaCode" class="form-control">
                                <option dir="ltr" value="+972" selected>+972</option>	
                                <option dir="ltr" value="+91">+91</option>
                                <option dir="ltr" value="+1">+1</option>
                                <option dir="ltr" value="+44">+44</option>
                            </select>
                        </div>
                    </div>
                </div>	
					
                <div class="form-group">
                    <label><?= lang('email_table') ?></label>
                    <input type="email" name="Email" class="form-control" pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-z]{2,4}$" title="מייל לא תקין">
                </div>

                <!-- minor section -->
                <div class="py-10">
                    <div class="custom-control custom-checkbox mb-3">
                        <input type="checkbox" class="custom-control-input" id="minor_checkbox" name="minor_checkbox">
                        <label class="custom-control-label" for="minor_checkbox">ממלא עבור לקוח קטין</label>
                    </div>
                </div>
                <div id="minor-lead-div" class="form-group" style="display: none">
                    <div class="mt-11 font-weight-bold">
                        <label>פרטי הקטין</label>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>שם פרטי <span class="text-primary">(קטין) </span><em class="text-danger font-rubik">*</em></label>
                            <input type="text" name="minor_firstName" id="minor_firstName" class="form-control">
                        </div>
                        <div class="col-md-6 form-group">
                            <label>שם משפחה <span class="text-primary">(קטין) </span><em class="text-danger font-rubik">*</em></label>
                            <input type="text" name="minor_lastName" id="minor_lastName" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group" dir="rtl">
                                <label><?= lang('cellular') ?> <span class="text-primary">(קטין) </span></label>
                                <input type="tel" name="minor_ContactMobile" id="minor_ContactMobile" class="form-control" pattern="^[0]*[5][0|1|2|3|4|5|8|9]{1}[0-9]{7}$" title="מספר נייד לא תקין">
                            </div>
                        </div>
                        <div class="col-md-4 form-group">
                            <label>קרבה</label>
                            <select name="relationship" id="minor_relationship" class="form-control">
                                <option value="1">אב</option>	
                                <option value="2">אם</option>
                                <option value="3">אח/אחות</option>
                                <option value="4">קרוב משפחה</option>
                                <option value="5">אחר</option>
                            </select>
                        </div>
                    </div>
                </div>	
                <!-- end minor section -->	
				 
                <div class="form-group" >
                    <label><?= lang('interested_in_lesson') ?></label>
                <select class="form-control js-example-basic-single select2multipleDesk " name="ClassType[]" id="ClassType" multiple="multiple" >
                <option value="BA999"><?= lang('all_classes') ?></option>
				<?php
				$ClassTypes = DB::table('class_type')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->orderBy('Type', 'ASC')->get();    
				foreach ($ClassTypes as $ClassType) { ?> 	
				<option value="<?php echo $ClassType->id; ?>"><?php echo $ClassType->Type ?></option>
				<?php } ?>	
				</select>
                </div>	    
                    
                <div class="form-group" >
                    <label><?= lang('branch') ?></label>
                <select class="form-control " name="Brands" id="BrandsTypeClass" >
				<?php
                $b = '1';    
				$ClassTypes = DB::table('brands')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->orderBy('id', 'ASC')->get();   
                if (!empty($ClassTypes)){     
				foreach ($ClassTypes as $ClassType) { ?> 	
				<option value="<?php echo $ClassType->id; ?>" <?php if ($b=='1'){ echo 'selected';} else {} ?>><?php echo $ClassType->BrandName ?></option>
				<?php ++$b; } } else { ?>
                <option value="0"><?= lang('primary_branch') ?></option>
                <?php } ?>    
				</select>
                </div>	
                    
                <div class="form-group">
                    <label><?= lang('incoming_source') ?></label>
                <select class="form-control" name="Source">
				<option value="0" selected><?= lang('without') ?></option>
				<?php
				$PipeSources = DB::table('leadsource')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->orderBy('Title', 'ASC')->get();    
				foreach ($PipeSources as $PipeSource) { ?> 	
				<option value="<?php echo $PipeSource->id; ?>"><?php echo $PipeSource->Title ?></option>
				<?php } ?>	
				</select>
                </div>		
					
					
                    
				<div class="form-group" >
                    <label><?= lang('status') ?></label>
                <select class="form-control" name="Status" id="StatusSelect" required>
                <option value=""><?= lang('choose') ?></option>
				<?php
				$PipeTitles = DB::table('leadstatus')->where('CompanyNum','=', $CompanyNum)->where('Act','=', '0')->where('Status','=', '0')->orderBy('Sort', 'ASC')->get();    
				foreach ($PipeTitles as $PipeTitle) { ?> 	
				<option value="<?php echo $PipeTitle->id; ?>" data-ajax="<?php echo $PipeTitle->PipeId; ?>" ><?php echo $PipeTitle->Title ?></option>
				<?php } ?>	
				</select>
                </div>	
					
                <?php
				if (Auth::userCan('141')) {
				?>
                    <div class="form-group" >
                    <label><?= lang('choose_representative') ?></label>
                    <select name="Agents" class="form-control  ChangeLeadAgentp"  style="width: 100%" data-placeholder="<?= lang('choose_representative') ?>">
                    <option value="0"><?= lang('without_representative') ?></option>
                    <?php
					$AgentLoops = DB::table('users')->where('CompanyNum', '=', $CompanyNum)->where('ActiveStatus', '=', '0')->get();
					foreach ($AgentLoops as $AgentLoop) {
			        echo '<option value="'.$AgentLoop->id.'" >'.$AgentLoop->display_name.'</option>';
                    }
					?>
                    </select>
                    </div>	    
				<?php
				}else { ?>
				<input type="hidden" name="Agents" value="0">
				<?php } ?>       
                    
                    
                <?php if ($Category2=='1'){ ?>
                <div class="form-group">
                <label><?= lang('set_automation') ?></label>
                <select class="form-control" name="Automation">   
                <option value="0" selected><?= lang('activated') ?></option>
                <option value="1"><?= lang('turned_off') ?></option>
                </select>
                </div>   
                <?php } else { ?>
                <input type="hidden" name="Automation" value="1">
                 <?php } ?>       
                    
                    
				</div>
				<div class="ip-modal-footer">
                <div class="ip-actions">
                <button type="submit" name="submit" class="btn btn-primary"><?php _e('main.save_changes') ?></button>
                </div>
				</form>    
                <a  class="btn btn-dark ip-close text-white" data-dismiss="modal"><?php _e('main.close') ?></a>     
				
                
				</div>
			</div>
		</div>
	</div>



<script>

//$('[data-toggle="tabajax"]').on('click', function(){
//    var $this = $(this),
//        source = $this.attr('href'),
//        pane = $this.attr('data-target');
//  
//    if($(pane).is(':empty')) {  // check if pane is empty, if so get data
//      $.get(source, function(data) {
//          $(pane).html(data);
//      });
//
//      $(this).tab('show');
//      return false;
//    }
//});    
    
    
$(document).ready(function(){
    if(window.location.search !=""){
        var startDate = getUrlParams('startDate');
        var endDate = getUrlParams('endDate');
        $.get('LeadsList/Opens.php?startDate='+startDate+'&endDate='+endDate, function(data){
            $('#nav-open').html(data);
        });
    }
    else {
        $.get('LeadsList/Opens.php', function(data){
            $('#nav-open').html(data);
        });
    }

    $('#minor_checkbox').on('click', function() {
        if ($(this).is(":checked")) {
            $("#minor-lead-div").show();
            $('#minor-lead-div').height(200);
            $("#minor_firstName").prop('required', true);
            $("#minor_lastName").prop('required', true);
            
        } else {
            $("#minor_firstName").prop('required', false);
            $("#minor_lastName").prop('required', false);
            $('#minor-lead-div').height(0);
            setTimeout(() => {
                $("#minor-lead-div").hide();    
            }, 200);
        }
    });

});    
    
    
$('[data-toggle="tabajax"]').click(function(e) {
    var $this = $(this),
        loadurl = $this.attr('href'),
        targ = $this.attr('data-target');

    $.get(loadurl, function(data) {
        
//    $(targ).load(data,function(e){    
//    $('#DivViewDeskInfo .ajax-form').on('submit', BeePOS.ajaxForm);  
//    return false;     
//    });    
//        
//        
        $(targ).html(data);
    });

    $this.tab('show');
    window.location.hash = targ;
    $('html,body').scrollTop(0);    
    return false;
});

var hash = window.location.hash;
$('.nav-tabs a[data-target="' + hash + '"]').trigger('click'); 
$('html,body').scrollTop(0);
    
window.onhashchange = function() {
var hash = window.location.hash;
$('.nav-tabs a[data-target="' + hash + '"]').trigger('click');
$('html,body').scrollTop(0);    // do stuff
}    
    
    
$( ".select2multipleDesk" ).select2( {theme:"bootstrap", placeholder: "<?= lang('choose_class_type') ?>"  } ); $( ".ChangeLeadAgentp" ).select2( {theme:"bootstrap", placeholder: "<?= lang('choose_representative') ?>" } );
    
$('#ClassType').on('select2:select', function (e) {    
var selected = $(this).val();

  if(selected != null)
  {
    if(selected.indexOf('BA999')>=0){
      $(this).val('BA999').select2( {theme:"bootstrap", placeholder: "<?= lang('choose_class_type') ?>" } );
    }
  }
    
});	    
    
$('#PipeLineSelect').on('change', function() {	 
var Id = this.value;

 $('#StatusSelect option')
        .hide() // hide all
        .filter('[data-ajax="'+$(this).val()+'"]') // filter options with required value
        .show(); // and show them    
    
 $('#StatusSelect').val('');     
}); 
    
    
$(function() {
			var time = function(){return'?'+new Date().getTime()};
						
			$('#AddNewLead').imgPicker({
			});

	
	
});	    
    
</script>








<?php include('Reports/popupSendByClientId.php'); ?>
<?php include('LeadsList/popupAgentByClientId.php'); ?>



<?php else: ?>
<?php redirect_to('../index.php'); ?>
<?php endif ?>


<?php endif ?>

<?php if (Auth::guest()): ?>

<?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>