<?php require_once '../../app/initcron.php'; 

header('Content-Type: text/html; charset=utf-8');

if (Auth::guest()) exit;

$CompanyNum = Auth::user()->CompanyNum;

$BDay = $_REQUEST['BDay'];

 $Today = date($BDay."-01", strtotime($BDay));
 $SevenDays = date($BDay."-t", strtotime($BDay));

$Clients = DB::select("SELECT id,CompanyName,ContactMobile,LastClassDate,MemberShipText,Dates,Gender,Email,Dob FROM `client` where CompanyNum='".$CompanyNum."' AND Status=0 AND DATE_FORMAT(Dob, '%m-%d') BETWEEN '".$Today."' AND '".$SevenDays."'  ORDER by  DATE_FORMAT(Dob,'%M %d') ASC "); 
    
$OpenTableCount = count($Clients);


?>
{
  "data": [
<?php	

$number = $OpenTableCount;
$i=1;	
foreach($Clients as $Client){
    
    
if ($Client->Gender=='0'){
$Gender = 'לא הוגדר';    
}    
else if ($Client->Gender=='1'){
$Gender = lang('male');
}   
else if ($Client->Gender=='2'){
$Gender = lang('female');
}       

?> 
	[
      "<a class=\"text-success\" href=\"/office/ClientProfile.php?u=<?php echo @$Client->id; ?>\"><strong class=\"text-success\"><?php echo  htmlentities(@$Client->CompanyName); ?></strong></a>",
      "<?php echo  htmlentities(@$Client->ContactMobile); ?>",
      "<?php echo  htmlentities(@$Client->Email); ?>",
      "<?php if (@$Client->Dob=='' || @$Client->Dob=='0000-00-00' || @$Client->Dob=='1970-01-01'){} else { echo  htmlentities(with(new DateTime(@$Client->Dob))->format('d/m/Y')); } ?>",
      "<?php echo  htmlentities(@$Gender); ?>"
    ]<?php if ($i < $number) { echo ','; } ?>

	<?php 
    $i++;   } 
  ?>
  ]
}













