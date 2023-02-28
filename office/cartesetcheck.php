<?php require_once '../app/init.php'; 

if (Auth::guest()) redirect_to(App::url());

?>

<?php 
$pageTitle = lang('reports_checks');
require_once '../app/views/headernew.php'; 
?>

<?php if (Auth::check()):?>
<?php if (Auth::userCan('20')): ?>

<?php

$BusinessSettings = DB::table('settings')->where('CompanyNum', '=', Auth::user()->CompanyNum)->first(); 

if (!isset($_REQUEST["month"])) $_REQUEST["month"] = date("m");
if (!isset($_REQUEST["year"])) $_REQUEST["year"] = date("Y");

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

$StartDate = $cYear.'-'.$cMonth.'-01';
$EndDate = $cYear.'-'.$cMonth.'-'.date('t',strtotime($StartDate));



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
<script type="text/javascript" charset="utf-8">     

function myFunction(value)
{

window.location.href = 'cartesetcheck.php?Dates='+value;

}

</script>






<!-- <div class="row pb-3">

<div class="col-md-6 col-sm-12 order-md-1">
<h3 class="page-header headertitlemain"  style="height:54px;">
<?php //echo $DateTitleHeader; ?>
</h3>
</div>

<div class="col-md-6 col-sm-12 order-md-4">
<h3 class="page-header headertitlemain"  style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
<i class="fas fa-chart-pie fa-fw"></i> ריכוז המחאות לחודש <span style="color:#0074A4;"><?php //echo $monthNames[$cMonth-1].' '.$cYear; ?></span>
</div>
</h3>
</div>

</div> -->

<div class="row px-0 mx-0" >
<div class="col-12">


<!-- <nav aria-label="breadcrumb" >
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-info">ראשי</a></li>
  <li class="breadcrumb-item active" aria-current="page">ריכוז המחאות</li>
  </ol>  
</nav>     -->

<div class="row">

<?php include("ReportsInc/SideMenu.php"); ?>

<div class="col-md-10 col-sm-12 ">	
    <div class="tab-content">
                        
                        
                        
                       
							
							
							
<div class="tab-pane fade show active text-start" role="tabpanel" id="user-overview">
<div class="card spacebottom">
      <div class="card-header text-start" ><i class="fas fa-chart-pie fa-fw"></i><strong> <?php echo lang('reports_check_title') ?> <span style="color:#0074A4;"><?php echo $monthNames[$cMonth-1].' '.$cYear; ?></span></strong></div>    
  <div class="card-body">       
                    
    
                      
<div class="row">

<div class="col-md-6 col-sm-12 d-flex justify-content-start">

<span class="mie-6 mb-6"><input type="month" class="form-control" id="CDate" value="<?php echo $Dates;?>" onChange="myFunction(this.value);"></span>  

<span class="mie-6 mb-6" style="display: none;"> <a href="javascript:void(0);" onclick="TINY.box.show({iframe:'pdf/cartesetcheck.php?month=<?php echo $cMonth ?>&year=<?php echo $cYear; ?>',boxid:'frameless',width:750,height:470,fixed:false,maskid:'bluemask',maskopacity:40,closejs:function(){closeJS()}})"  class="btn btn-info"><?php echo lang('action_print') ?></a></span> 
	</div>

<div class="col-md-6 col-sm-12 d-flex justify-content-end spacebottom px-0 mx-0"  >
<span class="mis-6 mb-6"> 
	<a href="<?php echo $_SERVER["PHP_SELF"] . "?month=". sprintf('%02d', $prev_month) . "&year=" . $prev_year; ?>"  class="btn btn-light"><?php echo lang('to_prev_month') ?></a>
</span>

<span class="mis-6 mb-6" > 
	<a href="<?php echo $_SERVER["PHP_SELF"] . "?month=". sprintf('%02d', $next_month) . "&year=" . $next_year; ?>"  class="btn btn-light"><?php echo lang('to_next_month') ?></a>
</span>

                            
<span class="mis-6 mb-6" > 
	<a href="cartesetcheck.php"  class="btn btn-dark"><?php echo lang('this_month') ?></a>
</span> 
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
            <th style="text-align:start;"><?php echo lang('reports_value_date') ?></th>
            <th style="text-align:start;"><?php echo lang('reports_document_date') ?></th>
            <th style="text-align:start;"><?php echo lang('account_number') ?></th>
            <th style="text-align:start;"><?php echo lang('bank_code') ?></th>
            <th style="text-align:start;"><?php echo lang('branch_id') ?></th>
            <th style="text-align:start;"><?php echo lang('check_number') ?></th>
            <th style="text-align:start;"><?php echo lang('check_sum') ?></th>
            <th style="text-align:start;"><?php echo lang('reports_voucher') ?></th>
          </tr>

</thead>
      
      
<?php
$DocsTables = DB::table('docs_payment')->where('CompanyNum', '=', Auth::user()->CompanyNum)->where('TypePayment','=','2')->whereBetween('CheckDate', array($StartDate, $EndDate))->orderBy('CheckDate', 'ASC')->get();
    
$DocsTablesAmount = DB::table('docs_payment')->where('CompanyNum', '=', Auth::user()->CompanyNum)->where('TypePayment','=','2')->whereBetween('CheckDate', array($StartDate, $EndDate))->orderBy('CheckDate', 'ASC')->sum('Amount');    

//לופ לסוגי המסמכים
foreach($DocsTables as  $DocsTable) {

$ClientInfo = DB::table('client')->where('CompanyNum', '=', Auth::user()->CompanyNum)->where('id', '=', $DocsTable->ClientId)->first();    
    
?>

<tr>
<td><a href="javascript:void(0);" onclick="TINY.box.show({iframe:'PDF/Docs.php?DocType=<?php echo $DocsTable->TypeDoc; ?>&DocId=<?php echo $DocsTable->TypeNumber; ?>',boxid:'frameless',width:750,height:470,fixed:false,maskid:'bluemask',maskopacity:40,closejs:function(){}})"><?php echo $DocsTable->TypeNumber; ?></a></td>
<td><a href="ClientProfile.php?u=<?php echo $ClientInfo->id; ?>"><?php echo $ClientInfo->CompanyName; ?></a></td>
<td><?php echo with(new DateTime($DocsTable->CheckDate))->format('d/m/Y'); ?></td>
<td><?php echo with(new DateTime($DocsTable->UserDate))->format('d/m/Y'); ?></td>
<td><?php echo $DocsTable->CheckBank; ?></td>
<td><?php echo $DocsTable->CheckBankCode; ?></td>
<td><?php echo $DocsTable->CheckBankSnif; ?></td>
<td><?php echo $DocsTable->CheckNumber; ?></td>    
<td><span ><?php echo @number_format($DocsTable->Amount, 2); ?></span> ₪</td>

<td><?php if ($DocsTable->Refound=='0') { echo lang('a_deposit'); } else { echo lang('credit_monetary'); } ?></td>
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
<td ></td>
<td></td>
<td><?php echo @number_format(@$DocsTablesAmount, 2); ?> ₪</td>
<td></td>
</tr>
    
    
</tbody>

</table>

</div>

</div></div>
    
    
    
    
<?php else: ?>
<?php redirect_to('../index.php'); ?>
<?php endif ?>


<?php endif ?>

<?php if (Auth::guest()): ?>

<?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>