<?php 
require_once '../app/init.php'; 
include_once('DocsInc/DocsParameters.php');
$pageTitle = $TypeList;
require_once '../app/views/headernew.php';
?>


<?php if (Auth::check()):?>
<?php if (Auth::userCan('43')): ?>
<?php

$Items = DB::table('paymentstep')->orderBy('Title', 'ASC')->get();
$resultcount = count($Items);

//Log	
$LogUserId = Auth::user()->id;
$LogUserName = Auth::user()->display_name;
$LogDateTime = date('Y-m-d G:i:s');
$LogContent = "<i class='fa fa-headphones' aria-hidden='true'></i> " .$LogUserName . lang('entered_to') . "<a href='DocsList.php' target='_blank'>" . lang('docs_archive') . "</a>";
//DB::table('log')->insert(array('UserId' => $LogUserId, 'Text' => $LogContent, 'Dates' => $LogDateTime, 'ClientId' => '0'));
//Log

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


<script type="text/javascript" charset="utf-8">     

function myFunction(value)
{

window.location.href = 'DocsList.php?Types=<?php echo $Types; ?>&Dates='+value;

}

</script>



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
	        //"scrollY":        "450px",
            //"scrollCollapse": true,
            "paging":         true,
	         fixedHeader: {
        headerOffset: 50
    },

	     //  bStateSave:true,
		   // serverSide: true,
	        pageLength: 100,
	      dom:  '<<"d-flex justify-content-between w-100 mb-10" <rf><B>> t <"mt-10 d-flex justify-content-between w-100" <p><i> >>',
		//info: true,
	    buttons: [
		//{extend: 'copy', text: 'העתק <i class="fa fa-clipboard" aria-hidden="true"></i>', className: 'btn btn-info'},
			{extend: 'excelHtml5',  text: 'Excel <i class="fas fa-file-excel" aria-hidden="true"></i>', filename: '<?= lang("archive") ?> <?php echo $TypeTitle; ?>', className: 'btn btn-dark'},
			{extend: 'csvHtml5', text: 'CSV <i class="fas fa-file-code" aria-hidden="true"></i>', filename: '<?= lang("archive") ?> <?php echo $TypeTitle; ?>' , className: 'btn btn-dark'},
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
<i class="fas fa-file-alt"></i> <?php //echo $TypeList; ?>
<?php
	// if ((@$Act == 'Search') && (@$SearchValue != '')) {
	// 	echo lang('search_double_colon') . '<span style="color:#48AD42;">'.@$SearchValue.'</span>';
	// }
	// else {
	// 	echo lang('for_month') .  ' <span style="color:#48AD42;">'.$monthNames[$cMonth-1].' '.$cYear.'</span>';
	// }
?>
</div>
</h3>
</div>

<div class="col-md-2 col-sm-12 order-md-2">
<a href="Docs.php?Types=<?php //echo $Types; ?>"  class="btn btn-primary btn-block" ><?php //echo $TypeTitleSingle; ?> <?//= lang('new') ?></a>
</div>


</div>

<div class="row"  style="margin-right: 0px;margin-left:0px;padding-right: 0px;padding-left:0px;">
<div class="col-12" style="margin-right: 0px;margin-left:0px;padding-right: 0px;padding-left:0px;">

<nav aria-label="breadcrumb" >
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-dark"><?//= lang('main') ?></a></li>
<?php
	// if ((@$Act == 'Search') && (@$SearchValue != '')) {
	// 	echo '';
	// 	echo '<li class="breadcrumb-item"><a href="DocsList.php?Types='.$Types.'" class="text-dark">'.$TypeList.'</a></li><li class="breadcrumb-item active">' . lang('search') . ' <span style="color:#48AD42;">'.@$SearchValue.'</span> <span class="badge badge-primary">'.$DocCount.'</span></li>';
	// }
	// else {
	// 	echo '<li class="breadcrumb-item active">'.$TypeList.' <span class="badge badge-primary">'.$DocCount.'</span></li>';
	// }
?>
  </ol>  
</nav>    



</div>
</div> -->


<div class="row">

<?php include("DocsInc/RightCards.php"); ?>

<div class="col-md-10 col-sm-12">	
    <div class="tab-content">
                        
                        
                        
                       
							
							
							
                        <div class="tab-pane fade show active text-start" role="tabpanel" id="user-overview">
                         <div class="card spacebottom">
      <div class="card-header text-start" >
    
    
<?php
	if ((@$Act == 'Search') && (@$SearchValue != '')) {
		echo '';
		echo '<i class="fas fa-file-alt fa-fw"></i> <b>'.$TypeList.' '. lang("search_double_colon") . ' <span class="text-success">'.@$SearchValue.'</span></b>';
	}
	else {
		echo '<i class="fas fa-file-alt fa-fw"></i> <b>'.$TypeList.' ' .lang("for_month") . ' <span class="text-success">'.$monthNames[$cMonth-1].' '.$cYear.'</b>';
	}
?>
    
    
    
    
  </div>    
  <div class="card-body">       
                    
    
                      
<?php
	if ((@$Act == 'Search') && (@$SearchValue != '')) {
	}
	else {
?>
<div class="row">
<div class="col-md-9 col-sm-12 d-flex justify-content-start">
<span class="mie-6 mb-6" > <a href="<?php echo $_SERVER["PHP_SELF"] . "?Types=".$Types."&month=". sprintf('%02d', $prev_month) . "&year=" . $prev_year; ?>"  class="btn btn-light"><?= lang('to_prev_month') ?></a></span>

<span class="mie-6 mb-6"  > <a href="<?php echo $_SERVER["PHP_SELF"] . "?Types=".$Types."&month=". sprintf('%02d', $next_month) . "&year=" . $next_year; ?>"  class="btn btn-light"><?= lang('to_next_month') ?></a></span>

                            
<span class="mie-6 mb-6" > <a href="DocsList.php?Types=<?php echo $Types; ?>"  class="btn btn-dark"><?= lang('this_month') ?></a></span>
</div>
<div class="col-md-3 col-sm-12">
<span><input type="month" class="form-control" id="CDate" value="<?php echo $Dates;?>" onChange="myFunction(this.value);"></span>  
</div>
	</div>
<hr>
<?php
	}
?>





<table class="table table-bordered table-hover dt-responsive text-start display wrap" id="categories"  cellspacing="0" width="100%">
		<thead class="thead-dark">
			<tr>
				<th class="text-start">#</th>
				<th class="text-start"><?= lang('client_name') ?></th>
                <th class="text-start"><?= lang('date') ?></th>
                <th class="text-start"><?= lang('t_ereh') ?></th>
                <?php if ($PaymentBalance=='0'){ ?>
                <th class="text-start"><?= lang('status') ?></th>
                <?php } ?>
                <th class="text-start"><?= lang('summary') ?></th>
                <?php if ($PaymentBalance=='0'){ ?>
                <th class="text-start lastborder"><?= lang('remainder_of_payment') ?></th>
                <?php } ?>
			</tr>
		</thead>
		<tbody>
<?php 

foreach ($DocGets as $DocGet) {

if ($DocGet->PayStatus=='1' || $DocGet->PayStatus=='0' || $DocGet->PayStatus=='2'){
$DocBalance = $DocGet->BalanceAmount;
}
else {
$DocBalance = '0';    
}    

?>
        <tr>
        <td><?php DocumentGroupButton($DocGet->TypeNumber,$DocGet->TypeDoc,$DocGet->TypeHeader,$DocGet->PayStatus, $DocGet); ?></td>
        <td><?php if ($DocGet->ClientId != '0'){echo '<a href="ClientProfile.php?u='.$DocGet->ClientId.'">';} ?><span class="text-dark"><?php echo $DocGet->Company; ?><?php if ($DocGet->ClientId != '0'){echo "</a>";} ?></span></td>
        <td><?php echo with(new DateTime($DocGet->UserDate))->format('d/m/Y'); ?></td>
        <td><?php echo with(new DateTime($DocGet->Dates))->format('d/m/Y'); ?></td>
        <?php if ($PaymentBalance=='0'){ ?>    
        <td><?php 
			
			 if ($DocGet->PayStatus=='0'){
				echo '<span style="color:#e14d43;">' . lang('opened_single') . '</span>';
			}
			
			else if ($DocGet->PayStatus=='1'){
				echo '<span style="color:#e14d43;">' . lang('opened_single') . '</span>';
			}
			
			else if ($DocGet->PayStatus=='2'){
				echo '<span style="color:orange;">' . lang('not_fully_paid') . '</span>';
			}
			
			else if ($DocGet->PayStatus=='3'){
				echo '<span style="color:green;">' . lang('paid') . '</span>';
			}
			
			else if ($DocGet->PayStatus=='4'){
				echo '<span style="color:orange;">' . lang('doc_surrounding') . '</span>';
			}
			
			else if ($DocGet->PayStatus=='5'){
				echo '<span style="color:orange;">' . lang('paused') . '</span>';
			}
			
			else if ($DocGet->PayStatus=='6'){
				echo '<span style="color:#996666;">' . lang('canceled') . '</span>';
			}
			else if ($DocGet->PayStatus=='7'){
				echo '<span style="color:#996666;">' . lang('credit_monetary') . '</span>';
			} 
			else if ($DocGet->PayStatus=='8'){
				echo '<span style="color:#996666;">' . lang('doc_converted') . '</span>';
			}     
    
            ?></td>
           <?php }  ?>    
        <td><?php echo number_format($DocGet->Amount, 2); ?> ₪</td>
        <?php if ($PaymentBalance=='0'){ ?>
        <td><span style="color:#e14d43;" ><?php echo number_format(@$DocBalance, 2); ?></span><span style="color:#e14d43;"> ₪</span></td>
        <?php }  ?>
        </tr>
<?php 
} 
?>       
        </tbody>
	        <tfoot>
 <tr>
                <?php if ($PaymentBalance=='0'){ ?>
				<th colspan="5"><?= lang('total') ?></th>
                <th width="20px" style="text-align:start; color:red;"><span ><?php echo number_format($DocSum, 2); ?></span> ₪</th>
                <th width="20px" style="text-align:start; color:red;">--- ₪</th>
                <?php } else { ?>
                <th colspan="4"><?= lang('total') ?></th>
                <th style="text-align:start; color:red;"><span ><?php echo number_format($DocSum, 2); ?></span> ₪</th>
				<?php } ?>
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


<?php
require_once 'InfoPopUpInc.php';
?>


<!-- מודל שיעור אישי חדש -->
<div class="ip-modal text-start" role="dialog" id="UpdateCancelDocumentModalRefoundPopup" data-backdrop="static" data-keyboard="false" aria-hidden="true">
		<div class="ip-modal-dialog BigDialog" <?php //_e('main.rtl') ?>>
			<div class="ip-modal-content">
				<div class="ip-modal-header"  <?php //_e('main.rtl') ?>>
				<h4 class="ip-modal-title"><?= lang('cancel_doc') ?></h4>
                <a class="ip-close ClassClosePopUp" title="Close"   data-dismiss="modal" aria-label="Close">&times;</a>

				</div>
				<div class="ip-modal-body">
				<form action="UpdateCancelDocumentModalRefound" id="UpdateCancelDocumentModalRefound" class="ajax-form needs-validation" novalidate autocomplete="off">
				<div id="resultCancelDocumentModalRefound"><center><i class="fas fa-spinner fa-pulse fa-5x p-3"></i></center></div>
                    
                    
                <div class="alertb alert-danger" id="RPOSCancelDocsError" style="display: none;"><span id="RPOSCancelDocsErrorText"></span></div>    
                    
				</form>
				</div>
			</div>
		</div>
	</div>
<!-- מודל שיעור אישי חדש -->


	
<?php else: ?>
<?php redirect_to('../index.php'); ?>
<?php endif ?>


<?php endif ?>

<?php if (Auth::guest()): ?>

<?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>