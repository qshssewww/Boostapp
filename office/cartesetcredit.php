<?php require_once '../app/init.php'; 

if (Auth::guest()) redirect_to(App::url());

$pageTitle = lang('reports_cc');
require_once '../app/views/headernew.php';
?>

<?php if (Auth::check()):?>
<?php if (Auth::userCan('20')): ?>

<?php

$BusinessSettings = DB::table('settings')->where('CompanyNum', '=', Auth::user()->CompanyNum)->first(); 


$CpaType = $BusinessSettings->CpaType;

if (!isset($_REQUEST["month"])) $_REQUEST["month"] = date("m");
if (!isset($_REQUEST["year"])) $_REQUEST["year"] = date("Y");
if (!isset($_REQUEST["Bank"])) $_REQUEST["Bank"] = '';

if (@$_REQUEST['Dates']==''){

$cMonth = $_REQUEST["month"];
$cYear = $_REQUEST["year"];
$Dates = $_REQUEST["year"].'-'.$_REQUEST["month"];
}

else {

$Dates = $_REQUEST['Dates'];
$cMonth = with(new DateTime($_REQUEST['Dates']))->format('m');
$cYear = with(new DateTime($_REQUEST['Dates']))->format('Y');	
	
}
 

if (@$_REQUEST["Bank"]==''){
$BankId = '';      
}
else {
$BankId = $_REQUEST["Bank"];    
}

$prev_year = $cYear;
$next_year = $cYear;
$prev_month = $cMonth-1;
$next_month = $cMonth+1;
 
if ($prev_month == 0 ) {
    $prev_month = 12;
    $prev_year = $cYear - 1;
}
if ($next_month == 13 ) {
    $next_month = 1;
    $next_year = $cYear + 1;
}

$StartDate = $cYear.sprintf("-%02d",$cMonth).sprintf("-%02d", !empty($_REQUEST['day'])?$_REQUEST['day']:'01');
if(empty($_REQUEST['day'])){
	$EndDate = date("Y-m-t", strtotime($StartDate));
}else{
	$EndDate = $cYear.sprintf("-%02d",$cMonth).sprintf("-%02d", !empty($_REQUEST['day'])?$_REQUEST['day']:'31');
}

if(!empty($_REQUEST['startDate'])) $StartDate = $_REQUEST['startDate'];
if(!empty($_REQUEST['endDate'])) $EndDate = $_REQUEST['endDate'];

//$EndDate = date('Y-m-d', strtotime('+1 day', strtotime($EndDate)));





		$TypePayment = array(
		lang('cash')=>"1",
		lang('credit_card_single')=>"3",
		lang('check')=>"2",
		lang('bank_transfer')=>"4",
		lang('payment_coupon')=>"5",
		lang('return_note')=>"6",
		lang('payment_bill')=>"7",
		lang('standing_order')=>"8",
		lang('other')=>"9"
		);
		$TashType = array(
		lang('regular_payment')=>"1",
		lang('payments')=>"2",
		lang('credit_payments_carteset')=>"3",
		lang('deferred_debit_carteset')=>"4",
		lang('other_way_carteset')=>"5"
		);
		$TypeBanks = array(
		lang('isracard')=>"1",
		lang('visa_cal')=>"2",
		lang('diners')=>"3",
		lang('american_express')=>"4",
		lang('leumi_card')=>"6"
		);

?>








<link href="assets/css/fixstyle.css" rel="stylesheet">


<!-- <div class="row pb-3">

<div class="col-md-6 col-sm-12 order-md-1">
<h3 class="page-header headertitlemain"  style="height:54px;">
<?php //echo $DateTitleHeader; ?>
</h3>
</div>

<div class="col-md-6 col-sm-12 order-md-4">
<h3 class="page-header headertitlemain"  style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">

<i class="fas fa-chart-pie fa-fw"></i> ריכוז כרטיסי אשראי</span>
</div>
</h3>
</div>


</div> -->
<div class="row px-0 mx-0"  >
<div class="col-12 px-0 mx-0" >


<!-- <nav aria-label="breadcrumb" >
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-info">ראשי</a></li>
  <li class="breadcrumb-item active" aria-current="page">ריכוז כרטיסי אשראי</li>
  </ol>  
</nav>     -->

<div class="row">

<?php include("ReportsInc/SideMenu.php"); ?>

<div class="col-md-10 col-sm-12">	
    <div class="tab-content">
                        
                        
                        
                       
							
							
							
<div class="tab-pane fade show active text-start" role="tabpanel" id="user-overview">
<div class="card spacebottom">
      <div class="card-header text-start d-flex justify-content-between" >
	  	<span class=""><i class="fas fa-chart-pie fa-fw"></i><strong> <?php echo lang('reports_cc') ?></span>
		  <span>
			<input id="dateRange" type="text" placeholder="<?php echo lang('search_by_date_range') ?>" class="form-control">
		</span>  		  
		
	</div>   

  <div class="card-body">       
                    
    
                      
<div class="row">
<div class="col-md-12 col-sm-12">
<?php if ($CpaType=='0'){ ?> 
<span class="pr-0">
<select name="Bank" id="BankSelect" class="form-control">
<option value=""><?php echo lang('reports_company_pay_off') ?></option>    
<option value="2" <?php if ($BankId=='2'){ echo 'selected'; } ?> ><?php echo lang('a_cal') ?></option>    
<option value="1" <?php if ($BankId=='1'){ echo 'selected'; } ?> ><?php echo lang('isracard') ?></option>
<option value="6" <?php if ($BankId=='6'){ echo 'selected'; } ?> ><?php echo lang('leumi_card') ?></option>    
</select>        
</span>  
<?php } ?>
    
<div class="col-md-6 col-sm-12" style="padding-right:0px;">


<span style="display: none;"> <a href="javascript:void(0);" onclick="TINY.box.show({iframe:'pdf/cartesetcredit.php?month=<?php echo $cMonth ?>&year=<?php echo $cYear; ?>',boxid:'frameless',width:750,height:470,fixed:false,maskid:'bluemask',maskopacity:40,closejs:function(){closeJS()}})"  class="btn btn-info"><?php echo lang('action_print') ?></a></span> 
	</div>
</div>
</div>

<hr>
 
<div class="row px-15"  >
<table class="table table-hover dt-responsive text-start display wrap" id="categories"  cellspacing="0" width="100%" style="font-size:14px;">
<tbody>
    
 <thead>

          <tr class="bg-dark text-white" style="font-size: 12px;">
            <th style="text-align:start;"><?php echo lang('carteset_doc_num') ?></th>
            <th style="text-align:start;"><?php echo lang('reports_card_name') ?></th>
            <th style="text-align:start;"><?php echo lang('table_value_date') ?></th>
            <th style="text-align:start;"><?php echo lang('reports_document_date') ?></th>
            <th style="text-align:start;"><?php echo lang('reports_company_pay_off') ?></th>
            <th style="text-align:start;"><?php echo lang('cc_number_single') ?></th>
            <th style="text-align:start;"><?php echo lang('reports_cc_type') ?></th>
            <th style="text-align:start;"><?php echo lang('confirmation_number') ?></th>
            <th style="text-align:start;"><?php echo lang('payments') ?></th>  
            <th style="text-align:start;"><?php echo lang('summary') ?></th>
            <th style="text-align:start;"><?php echo lang('reports_voucher') ?></th>
          </tr>

</thead>
      
      
<?php

// if($StartDate != $EndDate){

    
    if ($BankId!=''){
	$DocsTables = DB::table('docs_payment')->where('CompanyNum', '=', Auth::user()->CompanyNum)->where('TypePayment','=','3')->where('Bank','=',$BankId)->whereBetween('CheckDate', array($StartDate, $EndDate))->orderBy('CheckDate', 'ASC')->get();
    
	$DocsTablesAmount = DB::table('docs_payment')->where('CompanyNum', '=', Auth::user()->CompanyNum)->where('TypePayment','=','3')->where('Bank','=',$BankId)->whereBetween('CheckDate', array($StartDate, $EndDate))->orderBy('CheckDate', 'ASC')->sum('Amount'); 
    }
    else {
   	$DocsTables = DB::table('docs_payment')->where('CompanyNum', '=', Auth::user()->CompanyNum)->where('TypePayment','=','3')->whereBetween('CheckDate', array($StartDate, $EndDate))->orderBy('CheckDate', 'ASC')->get();
    
	$DocsTablesAmount = DB::table('docs_payment')->where('CompanyNum', '=', Auth::user()->CompanyNum)->where('TypePayment','=','3')->whereBetween('CheckDate', array($StartDate, $EndDate))->orderBy('CheckDate', 'ASC')->sum('Amount');      
    }
    
    
// }else{
// 	$DocsTables = DB::table('docs_payment')->where('CompanyNum', '=', Auth::user()->CompanyNum)->where('TypePayment','=','3')->where('CheckDate', '=', $StartDate)->orderBy('CheckDate', 'ASC')->get();
    
// 	$DocsTablesAmount = DB::table('docs_payment')->where('CompanyNum', '=', Auth::user()->CompanyNum)->where('TypePayment','=','3')->where('CheckDate', '=', $StartDate)->orderBy('CheckDate', 'ASC')->sum('Amount');    
	
// }

//לופ לסוגי המסמכים
foreach($DocsTables as  $DocsTable) {

$ClientInfo = DB::table('client')->where('CompanyNum', '=', Auth::user()->CompanyNum)->where('id', '=', $DocsTable->ClientId)->first(); 
if ($BankId!=''){    
$CountPayment = DB::table('docs_payment')->where('CompanyNum', '=', Auth::user()->CompanyNum)->where('TypePayment','=','3')->where('Bank','=',$BankId)->where('DocsId', '=', $DocsTable->DocsId)->where('L4digit',  $DocsTable->L4digit)->count();     
}
else {
$CountPayment = DB::table('docs_payment')->where('CompanyNum', '=', Auth::user()->CompanyNum)->where('TypePayment','=','3')->where('DocsId', '=', $DocsTable->DocsId)->where('L4digit',  $DocsTable->L4digit)->count();      
}    
?>

<tr>
<td><a href="javascript:void(0);" onclick="TINY.box.show({iframe:'PDF/Docs.php?DocType=<?php echo $DocsTable->TypeDoc; ?>&DocId=<?php echo $DocsTable->TypeNumber; ?>',boxid:'frameless',width:750,height:470,fixed:false,maskid:'bluemask',maskopacity:40,closejs:function(){}})"><?php echo $DocsTable->TypeNumber; ?></a></td>
<td><a href="ClientProfile.php?u=<?php echo @$ClientInfo->id; ?>"><?php echo @$ClientInfo->CompanyName; ?></a></td>
<td><?php echo with(new DateTime($DocsTable->CheckDate))->format('d/m/Y'); ?></td>
<td><?php echo with(new DateTime($DocsTable->UserDate))->format('d/m/Y'); ?></td>
<td><?php echo array_search($DocsTable->Bank, $TypeBanks); ?></td>
<td><?php echo $DocsTable->L4digit; ?></td>
<td><?php echo $DocsTable->BrandName; ?></td>
<td><?php echo $DocsTable->ACode; ?></td> 
<?php if ($CpaType=='0'){ ?>    
<td>תשלום <?php echo $DocsTable->Payments; ?> <?php echo lang('of_user_manage') ?> <?php echo $CountPayment; ?></td> 
<?php } else { ?>
<td><?php echo $DocsTable->Payments; ?> <?php echo lang('payments') ?></td>    
<?php } ?>    
<td><span dir="ltr"><?php echo @number_format($DocsTable->Amount, 2); ?></span> ₪</td>

<td><?php if ($DocsTable->Refound=='0') { echo lang('a_charge'); } else { echo lang('credit_monetary'); } ?></td>
</tr>
 

<?php
}
//לופ לסוגי המסמכים
?>

<tr class="active" style="color: red;font-weight: bold;">
<td ></td>
<td ></td>
<td ></td>
<td ></td>
<td></td>
<td></td>
<td></td>
<td></td>
<td></td>    
<td><?php echo @number_format(@$DocsTablesAmount, 2); ?> ₪</td>
<td></td>
</tr>
    
    
</tbody>

</table>

</div>

</div></div>

<!--	<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>-->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!--    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/he.js"></script>-->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
			<script>
				(function($){
					$(document).ready(function(){
						var dateRange = jQuery('#dateRange');
						dateRange.daterangepicker({
                            startDate: moment('<?php echo $StartDate; ?>', 'YYYY-MM-DD'),
                            endDate:  moment('<?php echo $EndDate; ?>', 'YYYY-MM-DD'),
                            isRTL: true,
                            langauge: 'he',
                            locale: {
                                format: 'DD/M/YY',
                                "applyLabel": "<?php echo lang('approval') ?>",
                                "cancelLabel": "<?php echo lang('cancel') ?>",
                            }
                        }).on('apply.daterangepicker', function(e, d){
							window.location.href = 'cartesetcredit.php?startDate='+moment(d.startDate).format('YYYY-MM-DD')+'&endDate='+moment(d.endDate).format('YYYY-MM-DD');
                        });
					})
				})(jQuery)
			</script>
    
<script type="text/javascript" charset="utf-8">     

$("#BankSelect").change(function() {
var Bank = this.value;
var startDate = '<?php echo $StartDate; ?>';    
var endDate =  '<?php echo $EndDate; ?>';   
    
window.location.href = 'cartesetcredit.php?Bank='+Bank+'&startDate='+startDate+'&endDate='+endDate;    
    
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