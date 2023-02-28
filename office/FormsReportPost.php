<?php require_once '../app/initcron.php'; 

if (Auth::userCan('144')): 

header('Content-Type: text/html; charset=utf-8');

if (Auth::guest()) exit;

$CompanyNum = Auth::user()->CompanyNum;
$UserId = Auth::user()->id;

$OpenTables = DB::table('workplace')
->join('client', 'client.id', '=', 'workplace.ClientId')
->where('workplace.CompanyNum', '=', $CompanyNum)
->select('client.id as clientid', 'client.CompanyName', 'client.ContactMobile', 'workplace.Dates as workplaceDates', 'workplace.Arrived', 'workplace.Workout', 'client.City', 'workplace.City as CityW', 'client.Street', 'workplace.Street as StreetW', 'client.StreetH', 'workplace.StreetH as StreetHW', 'client.Number', 'workplace.Number as NumberW')
->get();    


$OpenTableCount = count($OpenTables);

?>
{
  "data": [
<?php	

$number = $OpenTableCount;
$i=1;	
foreach($OpenTables as $Task){ 
    
    $ClientUserNameLog2 = str_replace('"','``',@$Task->CompanyName);
    $ClientUserNameLog2 = str_replace("'",'`',$ClientUserNameLog2); 
	$ClientUserNameLog = '<a href=\"ClientProfile.php?u='.@$Task->clientid.'\"><span class=\"text-dark\">'.@$ClientUserNameLog2.'</span></a>';
	
$CityInfo = DB::table('cities')->where('CityId', '=', $Task->City)->first();
$CityInfoW = DB::table('cities')->where('CityId', '=', $Task->CityW)->first();	

if ($Task->StreetH==''){	
$AddressInfo = DB::table('street')->where('id', '=', $Task->Street)->first();
$Street = @$AddressInfo->Street.' '.$Task->Number;	
}
else {
$Street = $Task->StreetH.' '.$Task->Number;	
}	
	
if ($Task->StreetHW==''){	
$AddressInfoW = DB::table('street')->where('id', '=', $Task->StreetW)->first();
$StreetW = @$AddressInfoW->Street.' '.$Task->NumberW;	
}
else {
$StreetW = $Task->StreetHW.' '.$Task->NumberW;		
}	
	
    $Street = str_replace('"','``',$Street);
    $Street = str_replace("'",'`',$Street);
	
	$StreetW = str_replace('"','``',$StreetW);
    $StreetW = str_replace("'",'`',$StreetW);
	
?> 
	[
      "<?php echo @$ClientUserNameLog; ?>",   
      "<?php echo @$Task->ContactMobile; ?>", 
      "<?php echo @$CityInfo->City; ?>",
      "<?php echo @$Street; ?>",
      "<?php echo @$CityInfoW->City; ?>",
      "<?php echo @$StreetW; ?>",
      "<?php echo @$Task->Arrived ?>",
      "<?php echo @$Task->Workout ?>",
      "<?php echo with(new DateTime(@$Task->workplaceDates))->format('d/m/Y H:i'); ?>"
    ]<?php if ($i < $number)	{echo ',';} ?>

	<?php $i++; } ?>
  ]
}

<?php endif ?>











