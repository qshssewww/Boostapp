<?php
require_once '../../app/initcron.php';


$CompanyNum = Auth::user()->CompanyNum;
$ClientId = $_REQUEST['ClientId'];
$GroupNumber = $_REQUEST['GroupNumber'];
$ClassId = $_REQUEST['ClassId'];
$Act = @$_REQUEST['Act'];

$CheckClient = DB::table('tempclient_private')->where('CompanyNum', '=', $CompanyNum)->where('ClientId', '=', $ClientId)->where('GroupNumber', '=', $GroupNumber)->first();

if (@$CheckClient->id=='' && @$Act!='4'){ 
DB::table('tempclient_private')->insertGetId(
array('CompanyNum' => $CompanyNum, 'GroupNumber' => $GroupNumber, 'ClientId' => $ClientId) );      
}
?>


<table class="table table-bordered table-sm table-responsive-sm">

<thead>
<th style="text-align:right;">#</th>
<th style="text-align:right;  width:200px;">לקוח</th>
<th style="text-align:right; width: 200px;">בחר מנוי</th>
<th style="text-align:right; width: 200px;">הקמת מנוי חדש</th>
<th style="text-align:right;">הסר</th> 
</thead>

<tbody>
    </tbody>

<?php 
$q = '1';    
$TempMemberLists = DB::table('tempclient_private')->where('CompanyNum', '=', $CompanyNum)->where('GroupNumber', '=', $GroupNumber)->orderBy('id','ASC')->get();
foreach ($TempMemberLists as $TempMemberList){
    
$ClientInfo = DB::table('client')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $TempMemberList->ClientId)->first();    
    
?>    

<tr>
<td class="align-middle"><?php echo $q; ?></td> 
<td class="align-middle"><?php echo $ClientInfo->CompanyName; ?></td> 
<td class="align-middle">
    
<select name="ActivityId<?php echo $TempMemberList->ClientId; ?>" id="PopUpActivityId<?php echo $TempMemberList->ClientId; ?>" class="form-control select999" style="width:200px;"  data-placeholder="בחר מנוי" required  >
<option value=""></option>
<option value="X999" <?php if ('X999'==$TempMemberList->ActivityId){ echo 'selected'; } else {} ?>>הקם מנוי חדש</option>    

<?php
$MemberShipClients = DB::select('select * from boostapp.client_activities where (CompanyNum = "'.$CompanyNum.'" AND Department != "4" AND Status = "0" AND FIND_IN_SET("'.$TempMemberList->ClientId.'",TrueClientId) > 0 ) OR (CompanyNum = "'.$CompanyNum.'" AND ClientId = "'.$TempMemberList->ClientId.'" AND Department != "4" AND Status = "0") Order By `CardNumber` DESC '); 
foreach ($MemberShipClients as $MemberShipClient) {    
?>
<option value="<?php echo $MemberShipClient->id ?>" <?php if ($MemberShipClient->id==$TempMemberList->ActivityId){ echo 'selected'; } else {} ?> ><?php echo $MemberShipClient->CardNumber ?> - <?php echo $MemberShipClient->ItemText ?></option>
<?php } ?>
</select>  
    
    
</td> 
<td class="align-middle">
 
    
<select name="ItemsId<?php echo $TempMemberList->ClientId; ?>" id="PopUpItemsId<?php echo $TempMemberList->ClientId; ?>" class="form-control select999" style="width:200px;"   data-placeholder="בחר מנוי" <?php if ('X999'==$TempMemberList->ActivityId){ } else { echo 'disabled'; } ?>  >
<option value=""></option>

<?php
if (@$Supplier->Status!='2') {     
$Activities = DB::table('items')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->orderBy('Department', 'ASC')->get();
}
else {
$Activities = DB::table('items')->where('CompanyNum', '=', $CompanyNum)->whereIn('Department', array(3, 4))->where('Status', '=', '0')->orderBy('Department', 'ASC')->get();    
}    
foreach ($Activities as $Activitie) {
$membership_type = DB::table('membership_type')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $Activitie->MemberShip)->first();  
if ($Activitie->MemberShip=='BA999'){
$Type = 'ללא סוג מנוי';    
} 
else {
$Type = @$membership_type->Type;     
} 
    
?>
<option value="<?php echo $Activitie->id ?>" data-price="<?php echo $Activitie->ItemPrice; ?>" data-name="<?php echo $Activitie->ItemName; ?>" <?php if ($Activitie->id==$TempMemberList->NewActivityId){ echo 'selected'; } else {} ?> ><?php echo $Type; ?> :: <?php echo $Activitie->ItemName; ?></option>
<?php } ?>
</select>    
 
</td> 
<td class="align-middle text-center"><a href="javascript:void(0)" id="DelMe<?php echo $TempMemberList->ClientId; ?>"><i class="fas fa-trash-alt"></i></a></td>     
</tr>

    
<script>    


  $('#PopUpActivityId<?php echo $TempMemberList->ClientId; ?>').on('change',function(){
  
  var Id = $(this).children('option:selected').val(); 

  if (Id=='X999'){       
  $('#PopUpItemsId<?php echo $TempMemberList->ClientId; ?>').prop( "disabled", false );
  $('#PopUpItemsId<?php echo $TempMemberList->ClientId; ?>').prop( "required", true );       
  }    
  else {
  $('#PopUpItemsId<?php echo $TempMemberList->ClientId; ?>').prop( "disabled", true ); 
  $('#PopUpItemsId<?php echo $TempMemberList->ClientId; ?>').prop( "required", false );       
  } 
      
      
    $.ajax({
    url: 'action/UpdatePrivateActivity.php?Act=1&GroupNumber=<?php echo @$GroupNumber; ?>&ClientId=<?php echo @$TempMemberList->ClientId; ?>&ActivityId='+Id,
    type: 'POST',
    success: function(data) {}
    });      
      
      
});	 
    
    
 $('#PopUpItemsId<?php echo $TempMemberList->ClientId; ?>').on('change',function(){
  
  var Id = $(this).children('option:selected').val(); 

    $.ajax({
    url: 'action/UpdatePrivateActivity.php?Act=2&GroupNumber=<?php echo @$GroupNumber; ?>&ClientId=<?php echo @$TempMemberList->ClientId; ?>&ActivityId='+Id,
    type: 'POST',
    success: function(data) {}
    });      
      
      
});	   
    
    
$('#DelMe<?php echo $TempMemberList->ClientId; ?>').click(function(){
    $.ajax({
    url: 'action/UpdatePrivateActivity.php?Act=3&GroupNumber=<?php echo @$GroupNumber; ?>&ClientId=<?php echo @$TempMemberList->ClientId; ?>',
    type: 'POST',
    success: function(data) {
        
    Runme<?php echo $TempMemberList->ClientId; ?>();    
        
    }
    });
    

    
//    return false;
});    
    
   
function Runme<?php echo $TempMemberList->ClientId; ?> (){
    
    var urls= 'action/ClientActivityPrivate.php?Act=4&GroupNumber=<?php echo @$GroupNumber; ?>&ClientId=<?php echo @$TempMemberList->ClientId; ?>&ClassId=<?php echo @$ClassId; ?>';
    $('#ClientpopActivityInfo').load(urls);       
    
    
}    
    
    
    
</script>    
    
    
<?php ++$q; } ?>
    
</tbody>
</table>


<script>
$( ".select999" ).select2( { theme:"bootstrap",placeholder: "בחר", minimumInputLength: 0,language: "he", allowClear: false, width: 'resolve' } );     
$(document).ready(function(){
//$('#AddClientpopActivity').val(null).trigger('change');  
 });
</script>

    