<?php require_once '../../app/initcron.php'; 

header('Content-Type: text/html; charset=utf-8');

if (Auth::guest()) exit;

$CompanyNum = Auth::user()->CompanyNum;

$StartDateWeek = $_REQUEST['StartDateWeek'];
$EndDateWeek = $_REQUEST['EndDateWeek'];


if ($_REQUEST['Class']=='BA999'){
$Class = 'BA999';    
}
else {
$Class = $_REQUEST['Class']; 
}
   

if ($_REQUEST['Guide']=='BA999'){
$Guide = 'BA999';    
}
else {
$Guide = $_REQUEST['Guide'];
} 								


$Class = str_replace('"', '', $Class);
$Guide = str_replace('"', '', $Guide);





if ($Class=='BA999' && $Guide=='BA999'){

$ClientNoneShowMonthCounts = DB::table('classstudio_act')->where('CompanyNum','=',$CompanyNum)->whereBetween('ClassDate', array($StartDateWeek, $EndDateWeek))->where('StatusCount','=','0')->select('id','ClientId','ClassName','ClassDate','ClassStartTime','GuideId','TrueClientId')->get(); 
    
    
}
else if ($Class=='BA999' && $Guide!='BA999') {

$myArray = explode(',', $Guide);      
    
$ClientNoneShowMonthCounts = DB::table('classstudio_act')->where('CompanyNum','=',$CompanyNum)->whereBetween('ClassDate', array($StartDateWeek, $EndDateWeek))->where('StatusCount','=','0')->whereIn('GuideId', $myArray)->select('id','ClientId','ClassName','ClassDate','ClassStartTime','GuideId','TrueClientId')->get();    
    
}
else if ($Class!='BA999' && $Guide=='BA999') {

$myArray = explode(',', $Class);      
 
$ClientNoneShowMonthCounts = DB::table('classstudio_act')->where('CompanyNum','=',$CompanyNum)->whereBetween('ClassDate', array($StartDateWeek, $EndDateWeek))->where('StatusCount','=','0')->whereIn('ClassNameType', $myArray)->select('id','ClientId','ClassName','ClassDate','ClassStartTime','GuideId','TrueClientId')->get();    
    
}

else if ($Class!='BA999' && $Guide!='BA999') {

$myArray = explode(',', $Class);
$myArrayGuideId = explode(',', $Guide);     
 
$ClientNoneShowMonthCounts = DB::table('classstudio_act')->where('CompanyNum','=',$CompanyNum)->whereBetween('ClassDate', array($StartDateWeek, $EndDateWeek))->where('StatusCount','=','0')->whereIn('ClassNameType', $myArray)->whereIn('GuideId', $myArrayGuideId)->select('id','ClientId','ClassName','ClassDate','ClassStartTime','GuideId','TrueClientId')->get();    
    
}


$OpenTableCount = count($ClientNoneShowMonthCounts);



?>
{
  "data": [
<?php	

$number = $OpenTableCount;
$i=1;	
foreach($ClientNoneShowMonthCounts as $ClientNoneShowMonthCount){

    
if ($ClientNoneShowMonthCount->TrueClientId=='0'){    
$Client = DB::table('client')->where('CompanyNum','=', $CompanyNum)->where('id','=', $ClientNoneShowMonthCount->ClientId)->select('id', 'CompanyName', 'ContactMobile', 'LastClassDate', 'MemberShipText', 'Dates', 'Gender','Email','Dob')->first();
}
else {
$Client = DB::table('client')->where('CompanyNum','=', $CompanyNum)->where('id','=', $ClientNoneShowMonthCount->TrueClientId)->select('id', 'CompanyName', 'ContactMobile', 'LastClassDate', 'MemberShipText', 'Dates', 'Gender','Email','Dob')->first();    
}     
    
$Guide = DB::table('users')->where('id','=', $ClientNoneShowMonthCount->GuideId)->select('id', 'display_name')->first();    
    
  
$MemberShip = '';
    
if (@$Client->MemberShipText!=''){                  
$Loops =  json_decode($Client->MemberShipText,true);	
foreach($Loops['data'] as $key=>$val){ 

$ItemText = $val['ItemText'];
$TrueDate = $val['TrueDate'];
if ($TrueDate!=''){
$TrueDateFinal = with(new DateTime($TrueDate))->format('d/m/Y');    
}
else {
$TrueDateFinal = '';    
}   
    
$TrueBalanceValue = $val['TrueBalanceValue'];
    
if ($TrueBalanceValue!='0'){
$TrueBalanceValueFinal = $TrueBalanceValue;    
}    
else {
$TrueBalanceValueFinal = '';    
}    
  
$MemberShip .= $ItemText.' '.$TrueDateFinal.' '.$TrueBalanceValueFinal.', ';   
    
    
}  
}      
    
$MemberShip = rtrim($MemberShip, ', ');

?> 
	[
      "<a class=\"text-success\" href=\"/office/ClientProfile.php?u=<?php echo @$Client->id; ?>\"><strong class=\"text-success\"><?php echo  htmlentities(@$Client->CompanyName); ?></strong></a>",
      "<?php echo  htmlentities(@$Client->ContactMobile); ?>",      
      "<?php echo  htmlentities(@$MemberShip); ?>",
      "<?php echo  htmlentities(@$ClientNoneShowMonthCount->ClassName);  ?>",
      "<?php if (@$ClientNoneShowMonthCount->ClassDate==''){} else { echo  htmlentities(with(new DateTime(@$ClientNoneShowMonthCount->ClassDate))->format('d/m/Y')); } ?>",
      "<?php if (@$ClientNoneShowMonthCount->ClassStartTime==''){} else { echo  htmlentities(with(new DateTime(@$ClientNoneShowMonthCount->ClassStartTime))->format('H:i')); } ?>",
      "<?php echo  htmlentities(@$Guide->display_name); ?>"
    ]<?php if ($i < $number) { echo ','; } ?>

	<?php 
    $i++;    } 
  ?>
  ]
}













