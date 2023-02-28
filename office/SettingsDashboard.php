<?php require_once '../app/init.php';
require_once "Classes/Company.php";

$pageTitle = lang('path_settings');
require_once '../app/views/headernew.php';
?>


<?php if (Auth::check()):?>
<?php if (Auth::userCan('1')): ?>

<?php

$company = Company::getInstance(false);
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
<script src="/office/assets/js/coronaReport.js?<?php echo date('YmdHis'); ?>"></script>





<link href="assets/css/fixstyle.css" rel="stylesheet">

<div class="row">
<?php include_once "SettingsInc/RightCards.php"; ?>

<div class="col-md-10 col-sm-12">	


    <div class="card spacebottom">
    <div class="card-header text-start" >
    <i class="fas fa-cogs"></i> <b><?php echo lang('path_settings') ?></b>
 	</div>    
  	<div class="card-body text-start" >       
                    
                      
<i class="fas fa-angle-double-right"></i> <?php echo lang('settings_main_notice') ?>


 
    
        </div> <!-- This section is hidden by request on BP-1844 -->
        <!-- <div class="form-group text-start p-20">
            <label><?php // echo lang('show_green_pass_statement') ?></label>
            <select class="form-control w-250p js-green-pass" >
                <option value="1" <?php // echo ($company->__get("greenPass") == 1 ) ? "selected" : "" ?>><?php // echo lang('yes') ?></option>
                <option value="0" <?php // echo ($company->__get("greenPass") == 0 ) ? "selected" : "" ?>><?php // echo lang('no') ?></option>
            </select>
        </div>
        <div class="text-start d-flex flex-column p-20">
            <label><?php // lang('reset_green_pass_to_all') ?></label>
            <a class="btn btn-light w-100p " href="#js-reset-green-pass-modal" data-toggle="modal"><?php // lang('reset_single') ?></a>
        </div> -->
    </div>

	</div>

</div>

</div>


<!--<div class="modal fade px-0 px-sm-auto text-start js-modal-no-close" tabindex="-1" role="dialog" id="js-reset-green-pass-modal">-->
<!--    <div class="modal-dialog  modal-dialog-centered m-0 m-sm-auto">-->
<!--        <div class="modal-content border-0 shadow-lg overflow-hidden ">-->
<!--            <div class="modal-body bsapp-overflow-y-auto position-relative p-0 overflow-hidden bsapp-max-h-700p" style="height:calc( 100vh - 200px );" >-->
<!---->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<div class="modal px-0 px-sm-auto" tabindex="-1" role="dialog" id="js-reset-green-pass-modal">
    <div class="modal-dialog modal-sm modal-dialog-centered m-0 m-sm-auto">
        <div class="modal-content">
            <div class="modal-body h-100">
                <div class="d-flex flex-column justify-content-between h-100 bsapp-min-h-400p">
                    <div class="d-flex justify-content-between w-100">
                        <h5><?php echo lang('reset_green_pass_title') ?></h5>
                        <a href="javascript:;"  class="text-dark" data-dismiss="modal" ><i class="fal fa-times h4"></i></a>
                    </div>
                    <div class="d-flex  flex-column text-center  my-50">
                        <div class="w-100 mb-10">
                            <label class="badge badge-light badge-pill px-30">
                                <h1 class="text-danger"><i class="fal fa-info-circle"></i></h1>
                            </label>
                        </div>
                        <div class="w-100 "><?php echo lang('reset_green_pass_notice') ?></div>
                        <div class="w-100 "><?php echo lang('q_action_notice') ?></div>
                    </div>
                    <div class="d-flex justify-content-around w-100">
                        <a  class="btn btn-primary flex-fill mie-15 js-green-pass-date-reset" href="javascript:;"><?php echo lang('confirm') ?></a>
                        <a  class="btn btn-light flex-fill" href="javascript:;" data-dismiss="modal"><?php echo lang('action_cacnel') ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--<div class="modal fade px-0 px-sm-auto text-start" tabindex="-1" role="dialog" id="js-reset-green-pass-modal">-->
<!--    <div class="modal-dialog  modal-dialog-centered ">-->
<!--        <div class="modal-content border-0 shadow-lg">-->
<!--            <div class="modal-body h-100 bsapp-overflow-y-auto position-relative px-0 py-0 " >-->
<!--                <div class="bsapp-overflow-y-auto position-relative d-flex justify-content-between flex-column bsapp-min-h-500p p-15">-->
<!--                    <div class="d-flex justify-content-between w-100 ">-->
<!--                        <h5 class="">--><?php //echo lang('reset_green_pass_title') ?><!--</h5>-->
<!--                        <a href="javascript:;"  class="text-dark " data-dismiss="modal" ><i class="fal fa-times h4"></i></a>-->
<!--                    </div>-->
<!--                    <div class="d-flex  flex-column text-center  my-50 px-15">-->
<!--                        <div class="w-100 mb-10">-->
<!--                            <label class="badge badge-light badge-pill px-30">-->
<!--                                <h1 class="text-warning"><i class="fal fa-info-circle"></i></h1>-->
<!--                            </label>-->
<!--                        </div>-->
<!--                        <div class="w-100 text-center ">-->
<!--                            <p>--><?php //echo lang('reset_green_pass_notice') ?><!--</p>-->
<!--                            <p>--><?php //echo lang('q_action_notice') ?><!--</p>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                    <div class="d-flex justify-content-end mt-15">-->
<!--                        <a class="btn btn-outline-dark border-radius-8p bsapp-fs-14 font-weight-bold p-10 bsapp-min-w-110p mis-10" href="javascript:;" data-dismiss="modal">--><?php //echo lang('action_cacnel') ?><!--</a>-->
<!--                        <a class="js-green-pass-date-reset btn btn-primary border-radius-8p bsapp-fs-14 font-weight-bold p-10 bsapp-min-w-110p mis-10" href="javascript:;">--><?php //echo lang('confirm') ?><!--</a>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<script>



$(function() {
			var time = function(){return'?'+new Date().getTime()};
			
			// Header setup
			$('#SourcePopup').imgPicker({
			});
			// Header setup
			$('#SourceEditPopup').imgPicker({
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