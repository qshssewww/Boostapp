<?php
require_once '../app/init.php'; 
$BusinessSettings = DB::table('settings')->where('id',  '1')->first();



$BackgroundColor = "#2b619d"; //צבע רקע, קווים וכותרות
$TextColor = "#FFFFFF"; //צבע טקסט על הרקע שנבחר למעלה
$CompanyLogo = "LogoInvoice.png"; //לוגו של העסק (מוצג בצד שמאל)

$CompanyName = @$BusinessSettings->CompanyName; //Business name
if (@$BusinessSettings->BusinessType == '2') {$CompanyKind = "ע.מ";}//The kind of the company
elseif (@$BusinessSettings->BusinessType == '3') {$CompanyKind = "ח.פ";}//The kind of the company
elseif (@$BusinessSettings->BusinessType == '4') {$CompanyKind = "ח.צ";}//The kind of the company
elseif (@$BusinessSettings->BusinessType == '5') {$CompanyKind = "ע.פ";}//The kind of the company
elseif (@$BusinessSettings->BusinessType == '6') {$CompanyKind = "מלכ״ר";}//The kind of the company
elseif (@$BusinessSettings->BusinessType == '7') {$CompanyKind = "מ.מ";}//The kind of the company
$CompanyNumber = @$BusinessSettings->CompanyId; //מספר עוסק
$CompanyPhone = @$BusinessSettings->ContactPhone; //טלפון
$CompanyFax = @$BusinessSettings->ContactFax; //פקס
$CompanyMobile = @$BusinessSettings->ContactMobile; //סלולרי
$CompanyEmail = @$BusinessSettings->Email; //דואר אלקטרוני
$CompanySite = @$BusinessSettings->WebSite; //כתובת אתר
$CompanyCity = @$BusinessSettingsCity->City; //City
$CompanyAddress = @$BusinessSettings->Street; //Street
$CompanyAddressNum = @$BusinessSettings->Number; //מספר בית
$CompanyPOBox = @$BusinessSettings->POBox; //תא דואר
if (@$BusinessSettings->Zip != '0' && @$BusinessSettings->Zip != '') {$CompanyZip = ' '.@$BusinessSettings->Zip;} else {$CompanyZip='';} //מיקוד
if ((@$CompanyCity != '') && (@$CompanyAddress != '')) {$CompanyAddressPsik = ', ';} //אם יש עיר להוסיף פסיק בין הרחוב לעיר
if ((@$CompanyPOBox != '0') && (@$CompanyPOBox != '') && (@$CompanyCity != '')) {$CompanyZipPsik = ',';} //אם יש עיר להוסיף פסיק בין התא דואר למיקוד
if ((@$CompanyPOBox != '0') && (@$CompanyPOBox != '') && (@$CompanyCity != '')) {$CompanyPOBoxPsik = '. תא דואר: ';} //אם יש עיר להוסיף פסיק בין תא דואר לעיר
if ((@$CompanyAddress != '') && (@$CompanyAddressNum != '')) {$CompanyAddressSpace = ' ';} //אם יש מספר בית אז לעשות רווח בין המספר בית לבין הרחוב
$CompanyFullAddress = @$CompanyAddress.''.@$CompanyAddressSpace.''.@$CompanyAddressNum.''.@$CompanyAddressPsik.''.@$CompanyCity.''.@$CompanyPOBoxPsik.''.@$CompanyPOBox.''.@$CompanyZipPsik.''.@$CompanyZip; //כתובת מלאה של העסק
//פרטי עסק


$DocHtml = '';

$DocHtml .= '<style>
body {
	font-size: 10pt;
}
p {	margin: 0pt; }
table.items {
}
td { vertical-align: middle; }
.items td {
	vertical-align: middle;
}
table thead td { background-color: #EEEEEE;
	text-align: center;
	font-variant: small-caps;
	vertical-align: middle;
}
.items td.blanktotal {
	background-color: #EEEEEE;
	background-color: #FFFFFF;
	vertical-align: middle;
}
.items td.totals {
	text-align: right;
	vertical-align: middle;
}
.items td.cost {
	text-align: "." center;
	vertical-align: middle;
}
.CompanyTable {
padding-right:0px;margin-right:0px;vertical-align: top;
}
.ClientTable {
padding-right:0px;margin-right:0px;vertical-align: top;padding-top:-1px;
}
.ClientInfo {
padding-right:0px;margin-right:0px;vertical-align: top;padding-top:-1px;padding-left:5px;
}
.TakbolimTD {
text-align:right;border-bottom:1px solid #B9B9B9;
}
</style>';


$DocHtml .= '<table cellspacing="0" width="100%" dir="rtl" style="padding:0px;margin:0px;vertical-align: top;"><tr style="padding:0px;margin:0px;vertical-align: top;">
<td width="33.33%" style="direction:rtl;vertical-align: top; ">
<table style="padding:0px;margin-top:-2px;margin-right:0px;vertical-align: top;font-size:11px;" cellspacing="0">';


if (@$CompanyName != "") {
$DocHtml .= '
<tr class="ClientTable">
<td class="CompanyTable" colspan="2"><strong>'.@$CompanyName.'</strong></td>
</tr>
';
}

if (@$CompanyNumber != "") {
$DocHtml .= '
<tr class="ClientTable">
<td style="padding-right:0px;margin-right:0px;vertical-align: top;padding-left:5px;">'.@$CompanyKind.':</td>
<td class="CompanyTable">'.@$CompanyNumber.'</td>
</tr>
';
}

if (@$CompanyFullAddress != "") {
$DocHtml .= '
<tr class="ClientTable">
<td style="padding-right:0px;margin-right:0px;vertical-align: top;padding-left:5px;">כתובת:</td>
<td class="CompanyTable">'.@$CompanyFullAddress.'</td>
</tr>
';
}

if (@$CompanyPhone != "") {
if (strlen(@$CompanyPhone) == '9') {$ChunkAfter = '2';} else {$ChunkAfter = '3';} //לבדוק כמה ספרות המספר ולהוסיף מקף בהתאם לכמות הספרות
$DocHtml .= '
<tr class="ClientTable">
<td style="padding-right:0px;margin-right:0px;vertical-align: top;padding-left:5px;">טלפון:</td>
<td class="CompanyTable">'.substr(@$CompanyPhone, 0, $ChunkAfter) . '-' . substr(@$CompanyPhone, $ChunkAfter).'</td>
</tr>
';
}

if (@$CompanyMobile != "") {
if (strlen(@$CompanyMobile) == '9') {$ChunkAfter = '2';} else {$ChunkAfter = '3';} //לבדוק כמה ספרות המספר ולהוסיף מקף בהתאם לכמות הספרות
$DocHtml .= '
<tr class="ClientTable">
<td style="padding-right:0px;margin-right:0px;vertical-align: top;padding-left:5px;">סלולרי:</td>
<td class="CompanyTable">'.substr(@$CompanyMobile, 0, $ChunkAfter) . '-' . substr(@$CompanyMobile, $ChunkAfter).'</td>
</tr>
';
}

if (@$CompanyFax != "") {
if (strlen(@$CompanyFax) == '9') {$ChunkAfter = '2';} else {$ChunkAfter = '3';} //לבדוק כמה ספרות המספר ולהוסיף מקף בהתאם לכמות הספרות
$DocHtml .= '
<tr class="ClientTable">
<td style="padding-right:0px;margin-right:0px;vertical-align: top;padding-left:5px;">פקס:</td>
<td class="CompanyTable">'.substr(@$CompanyFax, 0, $ChunkAfter) . '-' . substr(@$CompanyFax, $ChunkAfter).'</td>
</tr>
';
}

if (@$CompanyEmail != "") {
$DocHtml .= '
<tr class="ClientTable">
<td style="padding-right:0px;margin-right:0px;vertical-align: top;padding-left:5px;">דוא״ל:</td>
<td class="CompanyTable">'.@$CompanyEmail.'</td>
</tr>
';
}

if (@$CompanySite != "") {
$DocHtml .= '
<tr class="ClientTable">
<td style="padding-right:0px;margin-right:0px;vertical-align: top;padding-left:5px;">אתר:</td>
<td class="CompanyTable">'.@$CompanySite.'</td>
</tr>
';
}

$DocHtml .= '</table></td>';

//מסמך ממוחשב
$DocHtml .= '<td width="33.33%" style="text-align: center;vertical-align: top;">
<img src="'.@$PdfDigitalSignImg.'" height="80"><br>
<span style="text-align:center;">מ ס מ ך &nbsp;&nbsp;&nbsp; מ מ ו ח ש ב</span>
</td>';
//מסמך ממוחשב

//לוגו העסק
$DocHtml .= '<td width="33.33%" style="text-align: left;vertical-align: top;margin:0px;padding:0px;">
<img src="'.@$CompanyLogo.'" style="max-height:70px;max-width:150px;margin-left:0px;padding-left:0px;">
</td>';
//לוגו העסק

$DocHtml .= '</tr></table><br>';

//פרטי המסמך והלקוח
$DocHtml .= '<table width="100%" dir="rtl" style="padding-right:0px;margin-right:0px;border-collapse: collapse;vertical-align: bottom;">
<tr style="padding-right:0px;margin-right:0px;vertical-align: bottom;">
<td style="font-size: 15pt; color: '.$BackgroundColor.';padding-right:0px;margin-right:0px;vertical-align: bottom;"><strong>'.@$DocTypeName.' '.@$DocId.'</strong></td>
<td style="text-align:left;font-size: 10pt;padding-left:0px;margin-left:0px;vertical-align: bottom;'.@$StatusDocColor.'">'.@$DocKindName.'</td>
</tr>
</table>
<div style="border-top: 5px solid '.$BackgroundColor.'; font-size: 7pt; text-align: center; padding:0px;margin:0px; ">
<table width="100%" dir="rtl" style="padding-right:0px;margin-right:0px;margin-top:0px;border-collapse: collapse;vertical-align: bottom;">
<tr class="CompanyTable">
<td style="font-size: 9pt;padding-right:0px;margin-right:0px;vertical-align: top;">
<table style="padding:0px;margin-top:-2px;margin-right:0px;font-size:11px;" cellspacing="0">';


$DocHtml .= '
<tr class="CompanyTable">
<td style="padding-right:0px;margin-right:0px;vertical-align: top;padding-left:5px;">לכבוד:</td>
<td class="CompanyTable"><strong>לקוח מזדמן</strong></td>
</tr>
';



$DocHtml .= '
</table>
</td>
<td style="text-align:left;font-size:11px;padding-left:0px;margin-left:0px;vertical-align: top;">'.@$DocDate.'</td>
</tr>
</table>
';
//פרטי המסמך והלקוח



echo $DocHtml; 
?>