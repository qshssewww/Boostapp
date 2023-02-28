<?php
require_once '../app/init.php';

if (Auth::check()):
	if (Auth::userCan('152')):

		$CompanyNum = Auth::user()->CompanyNum;
		$PageInfo = DB::table('dynamicforms')->where('CompanyNum', $CompanyNum)->where('id', $_GET['u'])->first();
		if (empty($_GET['u']) || empty($PageInfo)) redirect_to(App::url());

		$pageTitle = lang('statistics_dynamic').' :: ' .$PageInfo->name;
		require_once '../app/views/headernew.php';

		CreateLogMovement(
            lang('dynamic_forms_log_statistics') .'<u>'.$PageInfo->name.'</u>'.lang('dynamice_version_ajax').$PageInfo->GroupNumber,
		'0');

?>

<link href="assets/css/fixstyle.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<style>
.my-custom-scrollbar {
position: relative;
height: 120px;
overflow: auto;
}
.table-wrapper-scroll-y {
display: block;
}

</style>

<div class="col-md-12 col-sm-12">
<!-- <div class="row">



<div class="col-md-5 col-sm-12 order-md-1">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<?php //echo $DateTitleHeader; ?>
</h3>
</div>

<div class="col-md-5 col-sm-12 order-md-4">
<h3 class="page-header headertitlemain" dir="rtl" style="height:54px;">
<div id="date" style="color:#666; font-size:22px; font-weight:bold; padding-top:7px; float:right;">
<i class="fab fa-wpforms fa-fw"></i> סטטיסטיקות טופס :: <?php //echo $PageInfo->name; ?>
</div>
</h3>
</div>

<div class="col-md-2 col-sm-12 order-md-3 pb-1">
    
<a href="DynamicForms.php" class="btn btn-primary text-white btn-block" name="Items"  dir="rtl"><i class="fab fa-wpforms fa-fw"></i> ניהול טפסים דינאמיים</a>
   
</div>
    

</div>

<nav aria-label="breadcrumb" dir="rtl">
  <ol class="breadcrumb">	
  <li class="breadcrumb-item"><a href="index.php" class="text-dark">ראשי</a></li>
  <li class="breadcrumb-item"><a href="SettingsDashboard.php" class="text-dark">הגדרות</a></li>
  <li class="breadcrumb-item"><a href="DynamicForms.php" class="text-dark">ניהול טפסים</a></li>	  
  <li class="breadcrumb-item active">סטטיסטיקות טופס :: <?php //echo $PageInfo->name; ?></li>
  </ol>  
</nav>     -->


<div class="row">
<?php include("SettingsInc/RightCards.php"); ?>

<div class="col-md-10 col-sm-12 order-md-1">	

    <div class="card spacebottom">
    <div class="card-header text-right" dir="rtl">
    <i class="fab fa-wpforms"></i> <b><?php echo lang('statistics_dynamic') ?>: <?php echo $PageInfo->name; ?> <?php echo lang('version_dynamic') ?> <span style="color:#0074A4;"><?php echo $PageInfo->GroupNumber; ?> </span></b>
 	</div>    
  	<div class="card-body text-right" dir="rtl">       

<div dir="ltr">
<?php 
$PageInfo->data = json_decode(@$PageInfo->data);
?>		
</div>		
		
		
		
<?php 
$DocHtml = '';
		


$i = '1';		
foreach(@$PageInfo->data->items as $d){

        if(!empty($d->i)){
        $DocHtml .= $d->i;
        continue;
    }
	
	if (@$d->type=='instruction'){
	$DocHtml .= @$d->instruction;	
	$DocHtml .= sprintf('<hr>');	
	}
	
	else {
	
    $QText = '';
	if (@$d->typeQ->label==lang('multiple_select_dynamic')){
	$QText = '<i class="fas fa-info-circle text-secondary" data-toggle="tooltip" data-placement="top" title='.lang('multiple_answer_note').'></i>';
	}
	
	
	$DocHtml .= sprintf('<h6><strong>%s</strong>'.sprintf(' <span><small> (%s) </small></span>', @$d->typeQ->label). ' '.$QText.'</h6>', @$d->question); 
	
	$QAnswersCounts = DB::table('dynamicforms_answers_reports')->where('CompanyNum', $CompanyNum)->where('FormId', $PageInfo->id)->where('QuestionId', @$d->q_id)->groupBy('QuestionId')->groupBy('ClientId')->get();
	$QAnswersCounts = count($QAnswersCounts);
	if ($QAnswersCounts=='0'){
	$DocHtml .= '<span class="badge badge-secondary">'.lang('no_comments_dynamic').'</span><hr>';
	}
	else if ($QAnswersCounts=='1'){
	$DocHtml .= '<span class="badge badge-secondary">'.lang('one_comment_dynamic').'</span><hr>';
	}
	else {
	$DocHtml .= '<span class="badge badge-secondary">'.$QAnswersCounts.' '.lang('commecnts_dynamic').'</span><hr>';
	}
//	$DocHtml .= $d->typeQ->type;
//	$DocHtml .= $d->typeQ->label;
	if (@$d->typeQ->type!='text'){
	$DocHtml .= sprintf('<div class="row"><div class="col-md-5">');	
	$DocHtml .= sprintf('<ul>');
	if (!empty(@$d->answers)){		
	foreach(@$d->answers as $field => $value){
		
	$AnswersCounts = DB::table('dynamicforms_answers_reports')->where('CompanyNum', $CompanyNum)->where('FormId', $PageInfo->id)->where('QuestionId', @$d->q_id)->where('AnswersId', @$d->answers[$field]->a_id)->count();
	
    $DocHtml .= sprintf('<li>');
    $DocHtml .= @$d->answers[$field]->item. ' ('. $AnswersCounts.')';
    $DocHtml .= sprintf('</li>');
		
	$GetTextAnswers = DB::table('dynamicforms_answers_reports')->where('CompanyNum', $CompanyNum)->where('FormId', $PageInfo->id)->where('QuestionId', @$d->q_id)->where('AnswersId', @$d->answers[$field]->a_id)->where('Explain','!=', '')->orderBy('id','ASC')->get();
	if (!empty($GetTextAnswers)){	
	$qai = '1';
	$DocHtml .= '<details><summary style="font-size:13px;">'.lang('comments_details_dynamic').'</summary>';
	$DocHtml .= '<div class="table-wrapper-scroll-y my-custom-scrollbar">';	
	$DocHtml .= '<table class="table table-striped"><tbody>';
	foreach ($GetTextAnswers as $GetTextAnswer){
	$DocHtml .= '<tr><td>';	
	$DocHtml .= $qai.'. '.@$GetTextAnswer->Explain;
	$DocHtml .= '</td></tr>';
	++$qai; }
	$DocHtml .= '</tbody></table></div></details>';		
	}
		
	}
	}
	$DocHtml .= sprintf('</ul>');

		
		
	$DocHtml .= sprintf('</div>');	
	$DocHtml .= sprintf('<div class="col-md-7">');
	$DocHtml .= '<div id="myChartTop'.$i.'"></div>';	
	$DocHtml .= sprintf('</div>');	

	$DocHtml .= sprintf('</div>');		
	$DocHtml .= sprintf('<hr>');	
		
	}
	else {
	$GetTextAnswers = DB::table('dynamicforms_answers_reports')->where('CompanyNum', $CompanyNum)->where('FormId', $PageInfo->id)->where('QuestionId', $d->q_id)->orderBy('id','ASC')->get();
	$qai = '1';	
	$DocHtml .= '<div class="table-wrapper-scroll-y my-custom-scrollbar">';	
	$DocHtml .= '<table class="table table-striped"><tbody>';
	foreach ($GetTextAnswers as $GetTextAnswer){
	$DocHtml .= '<tr><td>';	
	$DocHtml .= $qai.'. '.@$GetTextAnswer->Answers;
	$DocHtml .= '</td></tr>';
	++$qai; }
	$DocHtml .= '</tbody></table></div>';	
	$DocHtml .= sprintf('<hr>');	
		
	}
	
    }
	
	
 ++$i; }

		
echo $DocHtml;		
		
?>
		
		
		
		
		
		
	</div>
    </div>
    
	</div> 
</div>

</div>

<?php

$i = '1';		
foreach(@$PageInfo->data->items as $d){
	
$QAnswersCounts = DB::table('dynamicforms_answers_reports')->where('CompanyNum', $CompanyNum)->where('FormId', $PageInfo->id)->where('QuestionId', @$d->q_id)->groupBy('QuestionId')->groupBy('ClientId')->get();
$QAnswersCounts = count($QAnswersCounts);	
	
if (isset($d->answers) && count($d->answers) >= '6'){
$BarMultiSelects = '1';	
}	
else {
$BarMultiSelects = '0';	
}	
	
if (@$d->typeQ->type!='text'){
	
$ItemArray = '';
$AnswersArray = '';	
if (!empty(@$d->answers)){		
foreach(@$d->answers as $field => $value){

$TotalAnswersCounts = DB::table('dynamicforms_answers_reports')->where('CompanyNum', $CompanyNum)->where('FormId', $PageInfo->id)->where('QuestionId', $d->q_id)->count();		
	
$AnswersCounts = DB::table('dynamicforms_answers_reports')->where('CompanyNum', $CompanyNum)->where('FormId', $PageInfo->id)->where('QuestionId', $d->q_id)->where('AnswersId', @$d->answers[$field]->a_id)->count();	
	
if (@$AnswersCounts==''){
$AnswersCounts = '0';	
}	
	
if (@$TotalAnswersCounts!='0'){
$AnswersCounts = (round(($AnswersCounts / $TotalAnswersCounts) * 100));	
}
else {
$AnswersCounts = '0';
}	
	
if ((@$AnswersCounts == '' && $BarMultiSelects == '0') || (@$AnswersCounts == '0' && $BarMultiSelects == '0')){
$AnswersCounts = '0';	
}	
	
$ItemArray .= '"'.@$d->answers[$field]->item.'",';
$AnswersArray .= @$AnswersCounts.',';	
	
}

	
$ItemArray = rtrim($ItemArray, ',');
$AnswersArray = rtrim($AnswersArray, ',');	
	
if ($BarMultiSelects=='0'){	
?>
<script>
	
var options<?php echo $i; ?> = {
  chart: {
    type: 'pie',
	width: 300,
  },
  dataLabels: {
    enabled: false,
  },	
tooltip: {
  x: {
    format: 'dd MMM',
    formatter: undefined,
  },
  y: {
    formatter: (value) => { return value + "%" },
  },              
},	
  series: [<?php echo $AnswersArray; ?>],
  labels: [<?php echo $ItemArray; ?>],
//responsive: [{
//    breakpoint: undefined,
//    options: {},
//}],	
}

var chart<?php echo $i; ?> = new ApexCharts(document.querySelector("#myChartTop<?php echo $i; ?>"), options<?php echo $i; ?>);

chart<?php echo $i; ?>.render();

</script>	



<?php 
}
else { ?>
<script>
	
var options<?php echo $i; ?> = {
  chart: {
    type: 'bar',
	width: 350,
  },
  dataLabels: {
    enabled: false,
  },	
colors:['#48AD42', '#48AD42', '#48AD42'],
series: [{
    name: '<?php echo lang('reports_dynamics') ?>',
    data: [<?php echo $AnswersArray; ?>]
  }],
  xaxis: {
    categories: [<?php echo $ItemArray; ?>]
  },
tooltip: {
  x: {
    format: 'dd MMM',
    formatter: undefined,
  },
  y: {
    formatter: (value) => { return value + "%" },
  },              
},	
}

var chart<?php echo $i; ?> = new ApexCharts(document.querySelector("#myChartTop<?php echo $i; ?>"), options<?php echo $i; ?>);

chart<?php echo $i; ?>.render();

</script>	

<?php
	
}						   
						   
						   
}
else {}
							 
++$i; } } ?>

<script>
$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})

</script>


<?php
	require_once '../app/views/footernew.php';
	else:
		redirect_to('../index.php');
	endif;
endif;