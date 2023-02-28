<?php require_once '../app/init.php'; 

header('Content-Type: text/html; charset=utf-8');

if (Auth::guest()) exit;
if (Auth::userCan('82')): 
$GroupNumber = $_REQUEST['u'];

$CompanyNum = Auth::user()->CompanyNum;
$OpenTables = DB::table('classstudio_date')->where('CompanyNum','=', $CompanyNum)->where('GroupNumber','=', $GroupNumber)->where('Status','=', '0')->orderBy('StartDate', 'ASC')->get();
$OpenTableCount = count($OpenTables);

?>
{
  "data": [
<?php	

$number = $OpenTableCount;
$i=1;	
foreach($OpenTables as $Class){
    
$ClassType = DB::table('class_type')->where('CompanyNum','=', $CompanyNum)->where('id','=', $Class->ClassNameType)->first();
    
$RegisterClient = $Class->ClientRegister; 
$WatingClient = $Class->WatingList; 
    

@$PRegisterClient = round(($RegisterClient) / ($Class->MaxClient) * 100);
 
  $ClassTitle = str_replace('"','``',$Class->ClassName);
  $ClassTitle = str_replace("'",'`',$ClassTitle); 
    
  $Type = str_replace('"','``',$ClassType->Type);
  $Type = str_replace("'",'`',$Type);     
    
?> 
	[
      "<?php echo htmlentities(addslashes(@$Type)); ?>",
      "<a class=\"text-success\" href=\"javascript:NewViewClass('<?php echo $Class->id; ?>');\" ><strong class=\"text-success\"><?php echo htmlentities(addslashes(@$ClassTitle)); ?></strong></a>",
      "<?php echo with(new DateTime($Class->StartDate))->format('d/m/Y'); ?>",
      "<?php echo @$Class->Day; ?>",
      "<?php echo with(new DateTime($Class->StartTime))->format('H:i'); ?>",
      "<?php echo @$Class->GuideName?>",
      "<a class=\"text-success\" href=\"javascript:UpdateClass('<?php echo $Class->id; ?>','2');\" ><strong class=\"text-success\"><?php echo @$RegisterClient; ?></strong></a>",
      "<?php echo @$Class->MaxClient-@$RegisterClient; ?>",
      "<?php echo @$WatingClient; ?>",
      "<?php echo @$PRegisterClient; ?>%",
      <?php if (Auth::userCan('82')): ?>
      "<a class=\"text-success\" href=\"javascript:NewEditClass('<?php echo $Class->id; ?>');\" ><strong class=\"text-success\">ערוך שיעור</strong></a>"
      <?php else: ?>
      "אין הרשאה"
      <?php endif ?>

    ]<?php if ($i < $number)	{echo ',';} ?>

	<?php 
    
 $RegisterClient = '0';
 $WatingClient = '0';    
 $PRegisterClient = '0';   
    
    $i++;} ?>
  ]
}


<?php endif ?>









