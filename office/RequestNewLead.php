<?php require_once '../app/init.php'; ?>

<?php echo View::make('headernew')->render() ?>


<?php if (Auth::check()):?>





<?php
$DatesToNew = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s'). '- 636 hour'));

$Leads000000000 = DB::table('leads')->where('Seller', '=', NULL)->where('Status', '=', '31')->orderBy('id', 'DESC')->groupBy('Phone')->first();
$resultcount000000000 = count($Leads000000000);

$Leads00000000 = DB::table('leads')->where('Seller', '=', NULL)->where('Status', '=', '19')->orderBy('id', 'DESC')->groupBy('Phone')->first();
$resultcount00000000 = count($Leads00000000);

$LeadsHOT = DB::table('leads')->where('Seller', '=', NULL)->where('Dates', '>=', $DatesToNew)->where('LP', '!=', '151144239764704030')->where('LP', '!=', '151130262398545833')->orderBy('id', 'DESC')->groupBy('Phone')->first();
$resultcountHOT = count($LeadsHOT);

$LeadsHOT1 = DB::table('leads')->where('Seller', '=', NULL)->where('Video1', '>=', $DatesToNew)->where('LP', '!=', '151144239764704030')->where('LP', '!=', '151130262398545833')->orderBy('id', 'DESC')->groupBy('Phone')->first();
$resultcountHOT1 = count($LeadsHOT1);

$LeadsHOT2 = DB::table('leads')->where('Seller', '=', NULL)->where('Video2', '>=', $DatesToNew)->where('LP', '!=', '151144239764704030')->where('LP', '!=', '151130262398545833')->orderBy('id', 'DESC')->groupBy('Phone')->first();
$resultcountHOT2 = count($LeadsHOT2);

$LeadsHOT3 = DB::table('leads')->where('Seller', '=', NULL)->where('Video3', '>=', $DatesToNew)->where('LP', '!=', '151144239764704030')->where('LP', '!=', '151130262398545833')->orderBy('id', 'DESC')->groupBy('Phone')->first();
$resultcountHOT3 = count($LeadsHOT3);

$Leads0000000 = DB::table('leads')->where('Seller', '=', NULL)->where('Status', '=', '23')->orderBy('id', 'DESC')->groupBy('Phone')->first();
$resultcount0000000 = count($Leads0000000);

$Leads000000 = DB::table('leads')->where('Seller', '=', NULL)->where('Status', '=', '22')->orderBy('id', 'DESC')->groupBy('Phone')->first();
$resultcount000000 = count($Leads000000);

$Leads00000 = DB::table('leads')->where('Seller', '=', NULL)->where('Status', '=', '29')->orderBy('id', 'DESC')->groupBy('Phone')->first();
$resultcount00000 = count($Leads00000);

$Leads0000 = DB::table('leads')->where('Seller', '=', NULL)->where('Status', '=', '21')->orderBy('id', 'DESC')->groupBy('Phone')->first();
$resultcount0000 = count($Leads0000);

$Leads000 = DB::table('leads')->where('Seller', '=', NULL)->where('Status', '=', '20')->orderBy('id', 'DESC')->groupBy('Phone')->first();
$resultcount000 = count($Leads000);

$Leads00 = DB::table('leads')->where('Seller', '=', NULL)->where('Status', '=', '17')->orderBy('id', 'DESC')->groupBy('Phone')->first();
$resultcount00 = count($Leads00);

$Leads0 = DB::table('leads')->where('Seller', '=', NULL)->where('Status', '=', '16')->orderBy('id', 'DESC')->groupBy('Phone')->first();
$resultcount0 = count($Leads0);

$Leads1 = DB::table('leads')->where('Seller', '=', NULL)->where('VisitSalePage', '!=', '')->orderBy('id', 'DESC')->groupBy('Phone')->first();
$resultcount1 = count($Leads1);

$Leads2 = DB::table('leads')->where('Seller', '=', NULL)->where('StudentFunnel', '!=', '')->where('Video3', '!=', '')->where('Video2', '!=', '')->where('Video1', '!=', '')->orderBy('id', 'DESC')->groupBy('Phone')->first();
$resultcount2 = count($Leads2);

$Leads3 = DB::table('leads')->where('Seller', '=', NULL)->where('StudentFunnel', '!=', '')->where('Video2', '!=', '')->where('Video1', '!=', '')->orderBy('id', 'DESC')->groupBy('Phone')->first();
$resultcount3 = count($Leads3);

$Leads4 = DB::table('leads')->where('Seller', '=', NULL)->where('StudentFunnel', '!=', '')->where('Video1', '!=', '')->orderBy('id', 'DESC')->groupBy('Phone')->first();
$resultcount4 = count($Leads4);

$Leads5 = DB::table('leads')->where('Seller', '=', NULL)->where('StudentFunnel', '!=', '')->orderBy('id', 'DESC')->groupBy('Phone')->first();
$resultcount5 = count($Leads5);

$Leads6 = DB::table('leads')->where('Seller', '=', NULL)->where('Status', '=', '16')->orderBy('id', 'DESC')->groupBy('Phone')->first();
$resultcount6 = count($Leads6);

$Leads7 = DB::table('leads')->where('Seller', '=', NULL)->where('Status', '=', '2')->orderBy('id', 'DESC')->groupBy('Phone')->first();
$resultcount7 = count($Leads7);

$Leads8 = DB::table('leads')->where('Seller', '=', NULL)->orderBy('id', 'DESC')->groupBy('Phone')->first();
$resultcount8 = count($Leads8);

//דתיים
if (Auth::user()->id == '10031') {
$Leads_Habad = DB::table('leads')->where('Seller', '=', NULL)->where('Phone', 'like', '%770%')->orderBy('id', 'DESC')->groupBy('Phone')->first();
$resultcount_Habad = count($Leads_Habad);
}
else {
$Leads_Habad = '';
$resultcount_Habad = '0';
}
//דתיים


if ($resultcount_Habad != "0") {
	$LeadToSend = $Leads_Habad;
}
elseif ($resultcount000000000 != "0") {
	$LeadToSend = $Leads000000000;
}
elseif ($resultcount00000000 != "0") {
	$LeadToSend = $Leads00000000;
}
elseif ($resultcountHOT != "0") {
	$LeadToSend = $LeadsHOT;
}
elseif ($resultcountHOT1 != "0") {
	$LeadToSend = $LeadsHOT1;
}
elseif ($resultcountHOT2 != "0") {
	$LeadToSend = $LeadsHOT2;
}
elseif ($resultcountHOT3 != "0") {
	$LeadToSend = $LeadsHOT3;
}
elseif ($resultcount0000000 != "0") {
	$LeadToSend = $Leads0000000;
}
elseif ($resultcount000000 != "0") {
	$LeadToSend = $Leads000000;
}
elseif ($resultcount00000 != "0") {
	$LeadToSend = $Leads00000;
}
elseif ($resultcount0000 != "0") {
	$LeadToSend = $Leads0000;
}
elseif ($resultcount000 != "0") {
	$LeadToSend = $Leads000;
}
elseif ($resultcount00 != "0") {
	$LeadToSend = $Leads00;
}
elseif ($resultcount0 != "0") {
	$LeadToSend = $Leads0;
}
elseif ($resultcount1 != "0") {
	$LeadToSend = $Leads1;
}
elseif ($resultcount2 != "0") {
	$LeadToSend = $Leads2;
}
elseif ($resultcount3 != "0") {
	$LeadToSend = $Leads3;
}
elseif ($resultcount4 != "0") {
	$LeadToSend = $Leads4;
}
elseif ($resultcount5 != "0") {
	$LeadToSend = $Leads5;
}
elseif ($resultcount6 != "0") {
	$LeadToSend = $Leads6;
}
elseif ($resultcount7 != "0") {
	$LeadToSend = $Leads7;
}
else {
	$LeadToSend = $Leads8;
}




$LeadsCheck = DB::table('leads')->where('Seller', '=', NULL)->where('Status', '!=', '9')->where('Email', '=', @$LeadToSend->Email)->get();
$LeadsCheckCount = count($LeadsCheck);


if ($LeadsCheckCount != "1") {
		   DB::table('leads')
           ->where('id', $LeadToSend->id)
           ->update(array('Seller' => '10025', 'Status' => '9'));
?>
	
<div class="alert alert-danger" dir="rtl" style="font-weight: bold; font-size: 20px;">
<i class="fa fa-refresh fa-spin fa-fw"></i>
<span class="sr-only">נא להמתין...</span>
נא להמתין. המערכת מאתרת ליד...
</div>

<?php
echo '<meta http-equiv="refresh" content="0">';
}
else {
		   DB::table('leads')
           ->where('id', $LeadToSend->id)
           ->update(array('Seller' => Auth::user()->id));


if (@$LeadToSend->FirstLead != '') {
$Aff1 = file_get_contents('https://msp.ilaunch.co.il/Aff/GetAffName.php?AffId='.$LeadToSend->FirstLead, true);
$Aff1 = "$Aff1 ($LeadToSend->FirstLead)";
}
if (@$LeadToSend->LastLead != '') {
$Aff2 = file_get_contents('https://msp.ilaunch.co.il/Aff/GetAffName.php?AffId='.$LeadToSend->LastLead, true);
$Aff2 = "$Aff2 ($LeadToSend->LastLead)";
}
if (@$LeadToSend->Lp != '') {
$LpName = file_get_contents('https://msp.ilaunch.co.il/Aff/GetLpName.php?LpId='.$LeadToSend->Lp, true);
}



?>

<div dir="rtl">
<div class="alert alert-info" dir="rtl" style="font-weight: bold;">
<i class="fa fa-external-link" aria-hidden="true"></i> <?php echo Auth::user()->display_name; ?>, ליד חדש שוייך אלייך! טפל בו יפה :-)
</div>
<?php
	$LeadStatusName = DB::table('leadstatus')->where('id', '=', @$LeadToSend->Status)->first();
?>
	<table class="table table-striped table-bordered table-hover table-dt" id="clients" dir="rtl">
		<tr><th style="text-align:right;">סטטוס</th><td dir="ltr" style="text-align: right;"><?php echo @$LeadStatusName->Status; ?><?php if (($resultcountHOT != "0") || ($resultcountHOT1 != "0") || ($resultcountHOT2 != "0") || ($resultcountHOT3 != "0")) {echo " :: <span style='color:red;font-weight:bold;'>אינדיקציה היה לאחרונה</span>";} ?></td></tr>
		<tr><th style="text-align:right;">הליד מתאריך</th><td dir="ltr" style="text-align: right;"><?php echo with(new DateTime($LeadToSend->Dates))->format('d/m/Y H:i:s'); ?></td></tr>
		<tr><th style="text-align:right;">דף נחיתה</th><td dir="ltr" style="text-align: right;"><?php echo @$LpName; ?></td></tr>
		<tr><th style="text-align:right;">מקור הגעה</th><td dir="ltr" style="text-align: right;"><?php echo @$LeadToSend->Ref; ?></td></tr>
		<tr><th style="text-align:right;">שם מלא</th><td dir="ltr" style="text-align: right;"><?php echo @$LeadToSend->Name; ?></td></tr>
		<tr><th style="text-align:right;">דואר אלקטרוני</th><td dir="ltr" style="text-align: right;"><?php echo @$LeadToSend->Email; ?></td></tr>
		<tr><th style="text-align:right;">טלפון</th><td dir="ltr" style="text-align: right;"><?php echo @$LeadToSend->Phone; ?></td></tr>
        <tr><th style="text-align:right;">וידאו 1</th>
        <td dir="ltr" style="text-align: right;"><?php if (@$LeadToSend->Video1 != "") {echo with(new DateTime(@$LeadToSend->Video1))->format('d/m/Y H:i:s');} ?></td></tr>
        <tr><th style="text-align:right;">וידאו 2</th><td dir="ltr" style="text-align: right;"><?php if (@$LeadToSend->Video2 != "") {echo with(new DateTime(@$LeadToSend->Video2))->format('d/m/Y H:i:s');} ?></td></tr>
        <tr><th style="text-align:right;">וידאו 3</th><td dir="ltr" style="text-align: right;"><?php if (@$LeadToSend->Video3 != "") {echo with(new DateTime(@$LeadToSend->Video3))->format('d/m/Y H:i:s');} ?></td></tr>
        <tr><th style="text-align:right;">דף מכירה</th><td dir="ltr" style="text-align: right;"><?php if (@$LeadToSend->VisitSalePage != "") {echo with(new DateTime(@$LeadToSend->VisitSalePage))->format('d/m/Y H:i:s');} ?></td></tr>
        <tr><th style="text-align:right;">נכנס לדמו של מערכת תלמידים</th><td dir="ltr" style="text-align: right;"><?php if (@$LeadToSend->StudentFunnel != "") {echo with(new DateTime(@$LeadToSend->StudentFunnel))->format('d/m/Y H:i:s');} ?></td></tr>
        <tr><th style="text-align:right;">אפיליאייט ראשון</th><td  width="50%"><?php echo @$Aff1; ?></td></tr>
        <tr><th style="text-align:right;">אפיליאייט אחרון</th><td  width="50%"><?php echo @$Aff2; ?></td></tr>
	</table>
<div class="alert alert-danger" dir="rtl" style="font-weight: bold;">
	<h1 style="text-align: center;"><a href="ClientProfile.php?u=<?php echo @$LeadToSend->ClientId; ?>">מעבר לכרטיס ליד</a></h1>
</div>

</div>



<?php
if (@$LeadToSend->Name != '') {$NameToLog = @$LeadToSend->Name;} else {$NameToLog = @$LeadToSend->Email;}
//Log	
$LogUserId = Auth::user()->id;
$LogUserName = Auth::user()->display_name;
$LogDateTime = date('Y-m-d G:i:s');
$LogContent = "<i class='fa fa-external-link' aria-hidden='true'></i> ".$LogUserName." משך ליד רנדומלי <a href='ClientProfile.php?u=".$LeadToSend->ClientId."' target='_blank'>".$NameToLog." :: ".$LeadToSend->id."</a>";
//DB::table('log')->insert(array('UserId' => $LogUserId, 'Text' => $LogContent, 'Dates' => $LogDateTime, 'ClientId' => @$LeadToSend->ClientId));
//Log	
?>



<?php } ?>

<?php else: ?>
<?php redirect_to('../index.php'); ?>
<?php endif ?>


<?php if (Auth::guest()): ?>

<?php redirect_to('../index.php'); ?>

<?php endif ?>

<?php require_once '../app/views/footernew.php'; ?>