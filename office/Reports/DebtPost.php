<?php require_once '../../app/initcron.php'; 

header('Content-Type: application/json; charset=utf-8');

if (Auth::guest()) exit;

$CompanyNum = Auth::user()->CompanyNum;

$Clients = DB::table('client')->where('CompanyNum','=', $CompanyNum)->where('BalanceAmount','!=', '0')->select('id', 'CompanyName', 'ContactMobile', 'BalanceAmount', 'MemberShipText', 'Dates', 'Gender','Email','Dob','Status')->get();
$OpenTableCount = count($Clients);

$data = array();

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
    
    
if ($Client->Status=='0'){
$Status = 'פעיל';    
}    
else if ($Client->Status=='1'){
$Status = 'ארכיון';   
}   
else if ($Client->Status=='2'){
$Status = 'מתעניין';   
}    

$data[] = array(
  "<a class=\"text-success\" href=\"/office/ClientProfile.php?u=<?php echo @$Client->id; ?>\"><strong class=\"text-success\"><?php echo  htmlentities(@$Client->CompanyName); ?></strong></a>",
  "<?php echo  htmlentities(@$Client->ContactMobile); ?>",
  "<?php echo  htmlentities(@$Client->Email); ?>",
  "<?php if (@$Client->Dob=='' || @$Client->Dob=='0000-00-00' || @$Client->Dob=='1970-01-01'){} else { echo  htmlentities(with(new DateTime(@$Client->Dob))->format('d/m/Y')); } ?>",
  "<?php echo  htmlentities(@$Gender); ?>",
  "<?php if (@$Client->Dates==''){} else { echo  htmlentities(with(new DateTime(@$Client->Dates))->format('d/m/Y H:i')); } ?>",
  "<strong class=\"text-danger\"><?php echo @$Client->BalanceAmount; ?> ₪</strong>",
  "<?php echo  htmlentities(@$Status); ?>"
);

}

echo json_encode(array("data"=>$data));



exit;
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
    
    
if ($Client->Status=='0'){
$Status = 'פעיל';    
}    
else if ($Client->Status=='1'){
$Status = 'ארכיון';   
}   
else if ($Client->Status=='2'){
$Status = 'מתעניין';   
}        

?> 
	[
      "<a class=\"text-success\" href=\"/office/ClientProfile.php?u=<?php echo @$Client->id; ?>\"><strong class=\"text-success\"><?php echo  htmlentities(@$Client->CompanyName); ?></strong></a>",
      "<?php echo  htmlentities(@$Client->ContactMobile); ?>",
      "<?php echo  htmlentities(@$Client->Email); ?>",
      "<?php if (@$Client->Dob=='' || @$Client->Dob=='0000-00-00' || @$Client->Dob=='1970-01-01'){} else { echo  htmlentities(with(new DateTime(@$Client->Dob))->format('d/m/Y')); } ?>",
      "<?php echo  htmlentities(@$Gender); ?>",
      "<?php if (@$Client->Dates==''){} else { echo  htmlentities(with(new DateTime(@$Client->Dates))->format('d/m/Y H:i')); } ?>",
      "<strong class=\"text-danger\"><?php echo @$Client->BalanceAmount; ?> ₪</strong>",
      "<?php echo  htmlentities(@$Status); ?>"
    ]<?php if ($i < $number) { echo ','; } ?>

	<?php 
    $i++;   } 
  ?>
  ]
}













