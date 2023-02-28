<?php require_once '../app/init.php';
require_once "Classes/Item.php";
require_once "Classes/CompanyProductSettings.php";
header('Content-Type: text/html; charset=utf-8');
if (Auth::guest()) exit;

if (Auth::userCan('31')):
    $CompanySettingsDash = $CompanySettingsDash ?? DB::table('settings')->where('CompanyNum', '=', Auth::user()->CompanyNum)->first();

$Act = $_REQUEST['Act'];
if ($Act=='1'){
$OpenTables = DB::table('items')->where('CompanyNum', '=', Auth::user()->CompanyNum)->where('Display', '!=', '0')->orderBy('Status', 'ASC')->orderBy('ItemName', 'ASC')->get();
}
else {
$OpenTables = DB::table('items')->where('Status','=','0')->where('Display', '!=', '0')->where('CompanyNum', '=', Auth::user()->CompanyNum)->orderBy('Status', 'ASC')->orderBy('ItemName', 'ASC')->get();    
}


$OpenTableCount = count($OpenTables);

?>
{
  "data": [
<?php	

$number = $OpenTableCount;
$i='1';	
foreach($OpenTables as $Client){
    if($Client->Display == 0){
        $i++;
        continue;
    }

$Department = DB::table('membership')->where('id', '=', $Client->Department)->first(); 
$membership_type = DB::table('membership_type')->where('CompanyNum', '=', Auth::user()->CompanyNum)->where('id', '=', $Client->MemberShip)->first();     
if ($Client->MemberShip=='BA999'){
$Type = 'ללא סוג מנוי';    
} 
else {
$Type = str_replace('"',"``",@$membership_type->Type); 
$Type = str_replace("'","`",@$Type);      
$Type = htmlentities(@$Type);     
}
  
    
if ($Client->Vaild_Type=='1'){
$Vaild_Type = 'ימים';  
}  
else if ($Client->Vaild_Type=='2'){
$Vaild_Type = 'שבועות';  
}  
else if ($Client->Vaild_Type=='3'){
$Vaild_Type = 'חודשים';  
}

    if ($Client->Department == '1') {
        $Vaild = @$Client->Vaild . ' ' . $Vaild_Type;
        $BalanceClass = '';
        $LimitClass = @$Client->LimitClass . ' בשבוע';
        $StartTime = @$Client->StartTime;
        $EndTime = @$Client->EndTime;

        $NotificationDays = (new CompanyProductSettings())->getSingleByCompanyNum(Auth::user()->CompanyNum)->NotificationDays ?? 0;
        $NotificationDays = $NotificationDays . ' ימים';
    } elseif ($Client->Department == '2') {
        $Vaild = @$Client->Vaild . ' ' . $Vaild_Type;
        $BalanceClass = @$Client->BalanceClass;
        $LimitClass = @$Client->LimitClass . ' בשבוע';
        $StartTime = @$Client->StartTime;
        $EndTime = @$Client->EndTime;

        $NotificationDays = (new CompanyProductSettings())->getSingleByCompanyNum(Auth::user()->CompanyNum)->NotificationDays ?? 0;
        $NotificationDays = $NotificationDays . ' ימים';
    } elseif ($Client->Department == '3') {
        $Vaild = @$Client->Vaild . ' ' . $Vaild_Type;
        $BalanceClass = @$Client->BalanceClass;
        $LimitClass = '';
        $StartTime = @$Client->StartTime;
        $EndTime = @$Client->EndTime;
        $NotificationDays = '';
    } elseif ($Client->Department == '4') {
        $Vaild = '';
        $BalanceClass = '';
        $LimitClass = '';
        $StartTime = '';
        $EndTime = '';
        $NotificationDays = '';
    }

if ($Client->LimitClass=='999'){
$LimitClass = '';  
} 
    
if ($Client->Vaild=='0'){
$Vaild = '';  
} 
    
if ($Client->Status=='0'){
$Status = '<SPAN class=\"text-success\"><strong>פעיל</strong></SPAN>';    
}   
else {
$Status = '<SPAN class=\"text-danger\"><strong>לא פעיל</strong></SPAN>';     
}   

    
$ItemTile = str_replace('"',"``",@$Client->ItemName); 
$ItemTile = str_replace("'","`",@$ItemTile);     
    
    
?> 
	[
      "<?php echo $i; ?>",
      "<?php echo htmlentities(addslashes($Department->MemberShip)); ?>",
      <?php if (Auth::userCan('30')): ?>
      <?php if ($Client->ItemName=='פריט כללי'){ ?>
      "<?php echo htmlentities(addslashes(@$ItemTile)); ?>",
      <?php } else { ?>
      "<a class=\"text-success\" href=\"javascript:NewItemsEdit('<?php echo $Client->id; ?>');\"><strong class=\"text-success\"><?php echo htmlentities(addslashes(@$ItemTile)); ?></strong></a>",
      <?php  } ?>
      <?php else: ?>
      "<?php echo htmlentities(addslashes(@$ItemTile)); ?>",
      <?php endif ?>
      "<?php echo htmlentities(addslashes($Type)); ?>",
      "<?php echo htmlentities(addslashes($Vaild)); ?>",
      "<?php echo htmlentities(addslashes($BalanceClass)); ?>",
      "<?php echo htmlentities(addslashes($NotificationDays)); ?>",
      "<?php echo $Client->ItemPrice; ?> ₪",
      "<?php echo $Status; ?>"
    ]<?php if ($i < $number)	{echo ',';} ?>
	<?php $i++;} ?>
  ]
}

<?php endif ?>
