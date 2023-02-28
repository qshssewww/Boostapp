<?php require_once '../app/initcron.php'; 

if (Auth::userCan('145')): 

header('Content-Type: text/html; charset=utf-8');

if (Auth::guest()) exit;

$CompanyNum = Auth::user()->CompanyNum;
$UserId = Auth::user()->id;
$RoleId = Auth::user()->role_id;


$OpenTables = DB::table('client')->where('CompanyNum', '=', $CompanyNum)->whereIn('Status', array(0,2))->orderBy('CompanyName', 'ASC')->get();    

$OpenTableCount = '0';

foreach($OpenTables as $Client){

$ClientNoneShowMonthCount = DB::table('classstudio_dateregular')->where('ClientId','=',$Client->id)->where('CompanyNum','=',$CompanyNum)
->select('id')->count();     
    
if ($ClientNoneShowMonthCount=='0' || $ClientNoneShowMonthCount==''){
$OpenTableCount += '1';  
}

}

?>
{
  "data": [
<?php	

$number = $OpenTableCount;
$i=1;	
foreach($OpenTables as $Task){ 
    
$ClientNoneShowMonthCount = DB::table('classstudio_dateregular')->where('ClientId','=',$Task->id)->where('CompanyNum','=',$CompanyNum)
->select('id')->count();     
if ($ClientNoneShowMonthCount=='0' || $ClientNoneShowMonthCount==''){      
    
    $ClientUserNameLog2 = str_replace('"','``',@$Task->CompanyName);
    $ClientUserNameLog2 = str_replace("'",'`',$ClientUserNameLog2); 
	$ClientUserNameLog = '<a href=\"ClientProfile.php?u='.@$Task->id.'\"><span class=\"text-dark\">'.@$ClientUserNameLog2.'</span></a>';
 
    
if ($Task->Status=='0'){
$Status = 'לקוח פעיל';    
}   
else {
$Status = 'מתענין';    
}    
    
    
?> 
	[
      "<?php echo @$ClientUserNameLog; ?>",   
      "<?php echo @$Task->ContactMobile; ?>",
      "<?php echo @$Status ?>"
    ]<?php if ($i < $number)	{echo ',';} ?>

	<?php $i++; }  $OpenTableCount--; } ?>
  ]
}

<?php endif ?>











