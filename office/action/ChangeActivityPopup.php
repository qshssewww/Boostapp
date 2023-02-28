<?php require_once '../../app/initcron.php'; 
if (Auth::userCan('124')): 

$ClassId = $_REQUEST['ClassId'];
$ActivityId = $_REQUEST['ActivityId'];

$ClassYear = $_REQUEST['ClassYear'];
$ClassMonth = $_REQUEST['ClassMonth']; 
$FixClientId = $_REQUEST['ClientId']; 

$CompanyNum = Auth::user()->CompanyNum;

$Items = DB::table('client_activities')->where('CompanyNum', '=' , $CompanyNum)->where('id', '=' , $ActivityId)->first();
$ClassInfo = DB::table('classstudio_act')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ClassId)->first();

$ClientInfo = DB::table('client')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $ClassInfo->FixClientId)->first();

if ($ClassInfo->TrueClientId=='0'){
$ClientId = $ClassInfo->ClientId;    
$TrueClientIcon = ''; 
$ActCompanyName =  '';    
}   
else {
$TrueClientIcon = '<i class="fas fa-user-friends" title="מנוי משפחתי"></i>';
$ClientActInfo = DB::table('client')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $Items->ClientId)->first(); 
$ActCompanyName = 'בעל המנוי: '.$ClientActInfo->CompanyName;
$ClientId = $ClassInfo->TrueClientId;     
}  

?>


 <div class="row">
 <div class="col-md-12">	 
<span class="text-center font-weight-bold"><?php echo @$ClientInfo->CompanyName; ?></span>
 </div> 
<div class="col-md-6">	 
<span class="text-center font-weight-bold"><?php echo $TrueClientIcon; ?> <?php echo $Items->ItemText; ?> // מנוי מספר <?php echo $Items->CardNumber; ?></span>
 </div> 
<div class="col-md-6">	 
<span class="text-center font-weight-bold"><?php echo $ActCompanyName; ?></span>
 </div>      
    
 </div>    




<hr>

    
<input type="hidden" name="OldActivityId" value="<?php echo $ActivityId; ?>">    
<input type="hidden" name="ClassId" value="<?php echo $ClassId; ?>">
<input type="hidden" name="FixClientId" id="FixClientId1" value="<?php echo $FixClientId; ?>">
<input type="hidden" name="ClassYear" id="ClassYear1" value="<?php echo $ClassYear; ?>">
<input type="hidden" name="ClassMonth" id="ClassMonth1" value="<?php echo $ClassMonth; ?>">
    
 <div class="form-group">  
 <label>בחר מנוי חדש להחלפה</label>     
 <select name="NewActivityId" data-placeholder="בחר מנוי" class="form-control" style="width:100%;" required >
 <option>בחר מנוי</option>  
 <?php 
$i = '1';     
$MemberShipClients = DB::select('select * from boostapp.client_activities where (CompanyNum = "'.$CompanyNum.'" AND id != "'.$ActivityId.'"  AND Department != "4" AND Status = "0" AND FIND_IN_SET("'.$ClientId.'",TrueClientId) > 0 ) OR (CompanyNum = "'.$CompanyNum.'" AND id != "'.$ActivityId.'" AND ClientId = "'.$ClientId.'" AND Department != "4" AND Status = "0") Order By `CardNumber` DESC '); 
if (!empty($MemberShipClients)){ 
foreach ($MemberShipClients as $MemberShipClient) { 
    
$Disabled = '';
    $membership_type = DB::table('membership_type')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $MemberShipClient->MemberShip)->first();  
    if ($MemberShipClient->MemberShip=='BA999'){
    $Type = 'ללא סוג מנוי';    
    } 
    else {
    $Type = htmlentities($membership_type->Type);     
    }                                                    

    if ($MemberShipClient->Department=='1') {
    $TokefText = 'בתוקף עד: '. with(new DateTime($MemberShipClient->TrueDate))->format('d/m/Y');
    $BalnaceText = '';
        
    if ($MemberShipClient->TrueDate>=date('Y-m-d')){
    $CheckBoxColor = 'success';    
    }
    else {
    $CheckBoxColor = 'danger';
    $Disabled = 'disabled';     
    }    
        
        
    }
    else if ($MemberShipClient->Department=='2') {
    $BalnaceText = '<span dir="rtl">יתרת שיעורים:</span> <span dir="ltr">'.$MemberShipClient->TrueBalanceValue.'</span> '; 
    $TokefText = ''; 
        
    if ($MemberShipClient->TrueBalanceValue>='1'){
    $CheckBoxColor = 'success';    
    }
    else {
    $CheckBoxColor = 'danger';
    $Disabled = 'disabled';     
    }      
        
        
    if ($MemberShipClient->TrueDate!=''){
    $TokefText = 'בתוקף עד: '. with(new DateTime($MemberShipClient->TrueDate))->format('d/m/Y');  
        
    if ($MemberShipClient->TrueDate>=date('Y-m-d')){
    $CheckBoxColor = 'success';    
    }
    else {
    $CheckBoxColor = 'danger';
    $Disabled = 'disabled';    
    }      
        
    }
        
    }
    
    else if ($MemberShipClient->Department=='3') {
    $BalnaceText = '<span dir="rtl">יתרת שיעורים:</span> <span dir="ltr">'.$MemberShipClient->TrueBalanceValue.'</span> '; 
    $TokefText = ''; 
        
    if ($MemberShipClient->TrueBalanceValue>='1'){
    $CheckBoxColor = 'success';    
    }
    else {
    $CheckBoxColor = 'danger';
    $Disabled = 'disabled';     
    }      
    
        
    }    
    
    if ($MemberShipClient->TrueClientId=='0'){
    $TrueClientText = '';    
    }
    else {
    $TrueClientText = '<i class="fas fa-user-friends" title="מנוי משפחתי"></i> מנוי משפחתי';    
    }    

 ?>     
 <option value="<?php echo $MemberShipClient->id; ?>"><?php echo $Type; ?>, <?php echo htmlentities($MemberShipClient->ItemText); ?>, #<?php echo $MemberShipClient->CardNumber; ?> // <?php echo $BalnaceText; ?> <?php echo $TokefText; ?> <?php echo $TrueClientText;?> </option>    
<?php  ++ $i; } } else { ?>
     
<option>לא נמצא מנוי פעיל להחלפה</option>   
<?php } ?>     
</select>     
</div>


<script>
  
    
</script>

<?php endif ?>