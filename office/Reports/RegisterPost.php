<?php require_once '../../app/initcron.php'; 

header('Content-Type: text/html; charset=utf-8');

if (Auth::guest()) exit;

$CompanyNum = Auth::user()->CompanyNum;

$StartDateWeek = $_REQUEST['StartDateWeek'];
$EndDateWeek = $_REQUEST['EndDateWeek'];

$Clients = DB::table('client')->where('CompanyNum','=', $CompanyNum)->where('Status','=', '0')->whereBetween('Dates', array($StartDateWeek, $EndDateWeek))->select('id', 'CompanyName', 'ContactMobile', 'LastClassDate', 'MemberShipText', 'Dates', 'Gender','Email','Dob')->get();
$OpenTableCount = count($Clients);


?>
{
  "data": [
<?php	

$number = $OpenTableCount;
$i=1;	
foreach($Clients as $Client){
    
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
    
if ($Client->Gender=='0'){
$Gender = 'לא הוגדר';    
}    
else if ($Client->Gender=='1'){
$Gender = 'זכר';   
}   
else if ($Client->Gender=='2'){
$Gender = 'נקבה';   
}       

?> 
	[
      "<a class=\"text-success\" href=\"/office/ClientProfile.php?u=<?php echo @$Client->id; ?>\"><strong class=\"text-success\"><?php echo  htmlentities(@$Client->CompanyName); ?></strong></a>",
      "<?php echo  htmlentities(@$Client->ContactMobile); ?>",
      "<?php echo  htmlentities(@$Client->Email); ?>",
      "<?php if (@$Client->Dob=='' || @$Client->Dob=='0000-00-00' || @$Client->Dob=='1970-01-01'){} else { echo  htmlentities(with(new DateTime(@$Client->Dob))->format('d/m/Y')); } ?>",
      "<?php echo  htmlentities(@$Gender); ?>",
      "<?php echo  htmlentities(@$MemberShip); ?>",
      "<?php if (@$Client->LastClassDate==''){} else { echo  htmlentities(with(new DateTime(@$Client->LastClassDate))->format('d/m/Y')); } ?>",
      "<?php if (@$Client->Dates==''){} else { echo  htmlentities(with(new DateTime(@$Client->Dates))->format('d/m/Y H:i')); } ?>"
    ]<?php if ($i < $number) { echo ','; } ?>

	<?php 
    $i++;   } 
  ?>
  ]
}













