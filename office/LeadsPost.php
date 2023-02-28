<?php require_once '../app/init.php'; 

header('Content-Type: text/html; charset=utf-8');

if (Auth::guest()) exit;


if ((Auth::user()->role_id == "1") && (@$_GET['Manage'] == "Me")) { 
	
if (@$_GET['Status'] != "") { 
$OpenTables = DB::table('leads')->where('Status', '=', @$_GET['Status'])->orderBy('Dates', 'ASC')->groupBy('Phone')->get();
}
elseif (@$_GET['MStatus'] == "2") { 
$OpenTables = DB::table('leads')->where('Seller', '=', NULL)->orderBy('Dates', 'ASC')->groupBy('Phone')->get();
}
elseif (@$_GET['AffStatus'] != "") { 
$OpenTables = DB::table('leads')->where('Seller', '=', NULL)->where('FirstLead', '=', @$_GET['AffStatus'])->Orwhere('LastLead', '=', @$_GET['AffStatus'])->orderBy('Dates', 'ASC')->groupBy('Phone')->get();
}

else {
$OpenTables = DB::table('leads')->orderBy('Dates', 'ASC')->groupBy('Phone')->get();
}
	
}




else {
	
if (@$_GET['Status'] != "") { 
$OpenTables = DB::table('leads')->where('Seller', '=', Auth::user()->id)->groupBy('Phone')->where('Status', '=', @$_GET['Status'])->orderBy('Dates', 'ASC')->groupBy('Phone')->get();
}
else {
$OpenTables = DB::table('leads')->where('Seller', '=', Auth::user()->id)->groupBy('Phone')->orderBy('Dates', 'ASC')->groupBy('Phone')->get();
}

}




$OpenTableCount = count($OpenTables);

?>
{
  "data": [
<?php	

$number = $OpenTableCount;
$i=1;	
foreach($OpenTables as $Client){ 

$ClientInfo = DB::table('client')->where('id', '=', $Client->ClientId)->first();    
@$ManagerInfo = DB::table('users')->where('id', '=', $Client->Manager)->first();    
@$SellerInfo = DB::table('users')->where('id', '=', $Client->Seller)->first();    
    
if (@$ClientInfo->Status=='0'){
$MemberShipText = '<SPAN class=\"text-success\"><strong>פעיל</strong></SPAN>';    
}   
else {
$MemberShipText = '<SPAN class=\"text-danger\"><strong>מוקפא</strong></SPAN>';     
}  
 
$StatusInfo = DB::table('leadstatus')->where('id', '=', $Client->Status)->first();     
    
?> 
	[
      "<center><?php echo $ClientInfo->id; ?><br /><a href=\"javascript:ViewCallsLog('<?php echo $ClientInfo->id; ?>');\"><i class='fa fa-archive' aria-hidden='true'></i></a> <a href=\"javascript:ViewTaskLog('<?php echo $ClientInfo->id; ?>');\"><i class='fa fa-calendar' aria-hidden='true'></i></a> <a href=\"javascript:ViewInfoLog('<?php echo $ClientInfo->id; ?>');\"><i class='fa fa-info-circle' aria-hidden='true'></i></a> <a href=\"javascript:ViewLeadLog('<?php echo $ClientInfo->id; ?>');\"><i class='fa fa-hand-pointer-o' aria-hidden='true'></i></a></center>",
      "<a target=\"_blank\" href=\"ClientProfile.php?u=<?php echo $ClientInfo->id; ?>\"><strong><?php echo htmlentities($ClientInfo->CompanyName); ?></strong></a>",
    
      "<?php echo @$ClientInfo->ContactMobile; ?>",
      "<?php echo @$ClientInfo->Email; ?>",
      "<?php echo with(new DateTime($ClientInfo->Dates))->format('d/m/Y H:i'); ?>",
    
      "<?php if (Auth::user()->role_id == "1") { ?><select name=\"Seller\" id=\"Seller\" class=\"form-control\" onchange=\"funcs(this.value)\" ><option value=\"\">ללא נציג</option> <?php $Sellerss = DB::table('users')->where('ActiveStatus', '=' , '0')->get();foreach ($Sellerss as $Sellers){?><option value=\"<?php echo $Sellers->id; ?>:<?php echo $Client->id; ?>\" <?php if ($Sellers->id==$Client->Seller){ ?>selected <?php } else {} ?>><?php echo $Sellers->display_name; ?></option><?php } ?></select><span style='color:white;font-size:1px;'><?php echo @$SellerInfo->display_name;  ?></span><?php } else { ?><?php echo @$SellerInfo->display_name; } ?>",
      
      "<select name=\"TypeStatus\" id=\"TypeStatus\" class=\"form-control\" onchange=\"func(this.value)\" > <?php $Statuss = DB::table('leadstatus')->get();foreach ($Statuss as $Status){?><option value=\"<?php echo $Status->id; ?>:<?php echo $Client->id; ?>\" <?php if ($Status->id==$Client->Status){ ?>selected <?php } else {} ?>><?php echo $Status->Status; ?></option><?php } ?></select><span style='font-size:1px;'><?php $Statussss = DB::table('leadstatus')->where('id', '=', $Client->Status)->first(); echo $Statussss->Status; ?></span>"
    ]<?php if ($i < $number)	{echo ',';} ?>

	<?php $i++;} ?>
  ]
}