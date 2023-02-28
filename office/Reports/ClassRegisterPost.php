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

$ClientNoneShowMonthCounts = DB::table('classstudio_act')->where('CompanyNum','=',$CompanyNum)->whereBetween('ClassDate', array($StartDateWeek, $EndDateWeek))->where('StatusCount','=','0')->select('id','ClientId','ClassName','ClassDate','ClassStartTime','GuideId', 'ClientActivitiesId', 'Remarks','TrueClientId')
->orderBy('ClassStartTime', 'ASC')->orderBy('ClassDate', 'ASC')->orderBy('GuideId', 'ASC')->get(); 
    
    
}
else if ($Class=='BA999' && $Guide!='BA999') {

$myArray = explode(',', $Guide);      
    
$ClientNoneShowMonthCounts = DB::table('classstudio_act')->where('CompanyNum','=',$CompanyNum)->whereBetween('ClassDate', array($StartDateWeek, $EndDateWeek))->where('StatusCount','=','0')->whereIn('GuideId', $myArray)->select('id','ClientId','ClassName','ClassDate','ClassStartTime','GuideId', 'ClientActivitiesId', 'Remarks','TrueClientId')
->orderBy('ClassStartTime', 'ASC')->orderBy('ClassDate', 'ASC')->orderBy('GuideId', 'ASC')->get();    
    
}
else if ($Class!='BA999' && $Guide=='BA999') {

$myArray = explode(',', $Class);      
 
$ClientNoneShowMonthCounts = DB::table('classstudio_act')->where('CompanyNum','=',$CompanyNum)->whereBetween('ClassDate', array($StartDateWeek, $EndDateWeek))->where('StatusCount','=','0')->whereIn('ClassNameType', $myArray)->select('id','ClientId','ClassName','ClassDate','ClassStartTime','GuideId', 'ClientActivitiesId', 'Remarks','TrueClientId')
->orderBy('ClassStartTime', 'ASC')->orderBy('ClassDate', 'ASC')->orderBy('GuideId', 'ASC')->get();    
    
}

else if ($Class!='BA999' && $Guide!='BA999') {

$myArray = explode(',', $Class);
$myArrayGuideId = explode(',', $Guide);     
 
$ClientNoneShowMonthCounts = DB::table('classstudio_act')->where('CompanyNum','=',$CompanyNum)->whereBetween('ClassDate', array($StartDateWeek, $EndDateWeek))->where('StatusCount','=','0')->whereIn('ClassNameType', $myArray)->whereIn('GuideId', $myArrayGuideId)->select('id','ClientId','ClassName','ClassDate','ClassStartTime','GuideId', 'ClientActivitiesId', 'Remarks','TrueClientId')
->orderBy('ClassStartTime', 'ASC')->orderBy('ClassDate', 'ASC')->orderBy('GuideId', 'ASC')->get();    
    
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
$MemberShipInfo = DB::table('client_activities')->where('id','=', $ClientNoneShowMonthCount->ClientActivitiesId)->select('id', 'ItemText', 'Department', 'TrueDate', 'TrueBalanceValue', 'BalanceValue')->first();     
    
if ($MemberShipInfo->TrueDate!='') {
$TrueDate =  htmlentities(with(new DateTime(@$MemberShipInfo->TrueDate))->format('d/m/Y'));    
}  
else {
$TrueDate = '';    
}  
    
if ($MemberShipInfo->Department=='2' || $MemberShipInfo->Department=='3') {
$TrueBalanceValue = $MemberShipInfo->TrueBalanceValue.'/'.$MemberShipInfo->BalanceValue; 
}  
else {
$TrueBalanceValue = '';    
}      

$InfoMedical = '';    
$MedicalInfos = DB::table('clientmedical')
->where('id','=', $ClientNoneShowMonthCount->ClientId)->whereNull('TillDate')->where('Status', '=','0')
->Orwhere('id','=', $ClientNoneShowMonthCount->ClientId)->where('TillDate','>=', date('Y-m-d'))->where('Status', '=','0')
->select('id', 'Content', 'TillDate')->get();     
    
foreach ($MedicalInfos as $MedicalInfo) {
$InfoMedical = $MedicalInfo->Content.', ';    
}    
    

$InfoMedical = rtrim($InfoMedical, ',');    
  
?> 
	[
      "<a class=\"text-success\" href=\"/office/ClientProfile.php?u=<?php echo @$Client->id; ?>\"><strong class=\"text-success\"><?php echo  htmlentities(@$Client->CompanyName); ?></strong></a>",
      "<?php echo  htmlentities(trim(@$Client->ContactMobile)); ?>",
      "<?php echo  htmlentities(trim(@$Client->BalanceAmount)); ?>",
      "<?php echo  htmlentities(trim(@$MemberShipInfo->ItemText)); ?>",
      "<?php echo $TrueDate; ?>",
      "<?php echo $TrueBalanceValue; ?>",
      "<?php echo  htmlentities(trim(@$ClientNoneShowMonthCount->Remarks)); ?>",
      "",
      "<?php echo  htmlentities(trim(@$ClientNoneShowMonthCount->ClassName));  ?>",
      "<?php if (@$ClientNoneShowMonthCount->ClassDate==''){} else { echo  htmlentities(with(new DateTime(@$ClientNoneShowMonthCount->ClassDate))->format('d/m/Y')); } ?>",
      "<?php if (@$ClientNoneShowMonthCount->ClassStartTime==''){} else { echo  htmlentities(with(new DateTime(@$ClientNoneShowMonthCount->ClassStartTime))->format('H:i')); } ?>",
      "<?php echo  htmlentities(trim(@$Guide->display_name)); ?>"
    ]<?php if ($i < $number) { echo ','; } ?>

	<?php 
    $i++;    } 
  ?>
  ]
}













