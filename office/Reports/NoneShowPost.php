<?php require_once '../../app/initcron.php'; 

header('Content-Type: text/html; charset=utf-8');

if (Auth::guest()) exit;

$CompanyNum = Auth::user()->CompanyNum;

$StartDate = $_REQUEST['StartDate'];
$EndDate = $_REQUEST['EndDate'];
$StartDateWeek = $_REQUEST['StartDateWeek'];
$EndDateWeek = $_REQUEST['EndDateWeek'];

$Clients = DB::table('client')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->whereBetween('LastClassDate', array($StartDate, $EndDate))->select('id', 'CompanyName', 'ContactMobile', 'LastClassDate', 'MemberShipText')->get();
$OpenTableCount = '0';

?>
{
  "data": [
<?php	

$number = $OpenTableCount;
$i=1;	
foreach($Clients as $Client){

    
$ClientNoneShowMonthCount = DB::table('classstudio_act')->where('ClientId','=',$Client->id)->where('TrueClientId','=','0')->where('CompanyNum','=',$CompanyNum)->whereBetween('ClassDate', array($StartDateWeek, $EndDateWeek))->where('StatusCount','=','0')   
->select('id')->count();     
    
if ($ClientNoneShowMonthCount=='0'){
  
$MemberShip = '';
    
if ($Client->MemberShipText!=''){                  
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
      "<?php echo  htmlentities(with(new DateTime(@$Client->LastClassDate))->format('d/m/Y'));?>"
    ]<?php if ($i < $number) { echo ','; } ?>

	<?php 
    $i++;  } else {  }   } 
  ?>
  ]
}













