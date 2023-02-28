<?php require_once '../app/init.php'; 

header('Content-Type: text/html; charset=utf-8');

if (Auth::guest()) exit;

if (Auth::userCan('45')):

$CompanyNum = Auth::user()->CompanyNum;
$OpenTables = DB::table('client')->where('CompanyNum','=', $CompanyNum)->orderBy('CompanyName', 'ASC')->groupBy('ContactMobile')->get();
$OpenTableCount = count($OpenTables);

?>
{
  "data": [
<?php	

$number = $OpenTableCount;
$i=1;	
foreach($OpenTables as $Client){ 
    
if ($Client->Status=='0'){
$MemberShipText = '<SPAN class=\"text-success\"><strong>פעיל</strong></SPAN>';    
}   
else {
$MemberShipText = '<SPAN class=\"text-danger\"><strong>מוקפא</strong></SPAN>';     
}  
    
?> 
	[
      " <?php echo $Client->id; ?>",
      "<a class=\"text-success\" href=\"ClientProfile.php?u=<?php echo $Client->id; ?>\"><strong class=\"text-success\"><?php echo htmlentities($Client->CompanyName); ?></strong></a>",
      "<?php echo @$Client->CompanyId; ?>",      
      "<?php echo @$Client->ContactMobile; ?>",
      "<?php echo @$Client->Email; ?>",
      "<?php echo with(new DateTime($Client->Dates))->format('d/m/Y H:i'); ?>",
      "<?php echo $MemberShipText; ?>",
      ""
    ]<?php if ($i < $number)	{echo ',';} ?>

	<?php $i++;} ?>
  ]
}


<?php endif ?>











