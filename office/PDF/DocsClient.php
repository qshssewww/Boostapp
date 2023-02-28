<?php

require_once '../../app/initcron.php'; 
require_once '../PDF18/vendor/autoload.php';
require_once '../Classes/247SoftNew/ClientGoogleAddress.php';


$Docs = DB::table('docs')->where('RandomUrl','=', $_GET['RandomUrl'])->where('ClientId','=', $_GET['ClientId'])->first();
if (!empty($Docs)) {
    
$CompanyNum = $Docs->CompanyNum;
    
//פרטי עסק
$BusinessSettings = DB::table('settings')->where('CompanyNum',  $CompanyNum)->first();
$BusinessSettingsCity = DB::table('cities')->where('CityId', '=', $BusinessSettings->City)->first();
    $BusinessAddressGoogle = ClientGoogleAddress::getBusinessAddress($CompanyNum);

//עיצוב מסמך
$BackgroundColor = $BusinessSettings->DocsBackgroundColor; //צבע רקע, קווים וכותרות
$TextColor = $BusinessSettings->DocsTextColor; //צבע טקסט על הרקע שנבחר למעלה
$CompanyLogo = $BusinessSettings->DocsCompanyLogo; //לוגו של העסק (מוצג בצד שמאל)
$PdfMakerLogo = "247soft.png"; //לוגו של המערכת שיצרה את המסמך
$PdfDigitalSignImg = "digitalsignature.png"; //אייקון מסמך ממוחשב וחתום דיגיטלית
//עיצוב מסמך
	

$CompanyName = @$BusinessSettings->CompanyName; //Business name
if (@$Docs->BusinessType == '2') {$CompanyKind = "ע.מ";}//The kind of the company
elseif (@$Docs->BusinessType == '3') {$CompanyKind = "ח.פ";}//The kind of the company
elseif (@$Docs->BusinessType == '4') {$CompanyKind = "ח.צ";}//The kind of the company
elseif (@$Docs->BusinessType == '5') {$CompanyKind = "ע.פ";}//The kind of the company
elseif (@$Docs->BusinessType == '6') {$CompanyKind = "מלכ״ר";}//The kind of the company
elseif (@$Docs->BusinessType == '7') {$CompanyKind = "איחוד עוסקים";}//The kind of the company
elseif (@$Docs->BusinessType == '8') {$CompanyKind = "מ.מ";}//The kind of the company
$CompanyNumber = @$Docs->BusinessCompanyId; //מספר עוסק
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
    $CompanyFullAddress = $BusinessAddressGoogle->address ?? $CompanyFullAddress;
//פרטי עסק

	
//סטטוסים למסמכים
		$DocStatus = array(
		"תצוגה מקדימה"=>"0",
		"מקור"=>"1",
		"העתק נאמן למקור"=>"2",
		"מסמך מבוטל"=>"3"
		);
		if (@$Docs->Status == '3') {$StatusDocColor = 'color:red;';} else {$StatusDocColor = '';}
		if (@$Docs->Status == '1') {DB::table('docs')->where('id', $Docs->id)->where('CompanyNum',  $CompanyNum)->update(array('Status' => '2'));}
//סטטוסים למסמכים
	
//פרטי מסמך
$DocTypeId = $Docs->TypeDoc; //מספר סוג מסמך (חשבונית, חשבונית מס קבלה וכו׳)
$DocsTable = DB::table('docstable')->where('id','=', $DocTypeId)->where('CompanyNum',  $CompanyNum)->first();
  
if ($Docs->TypeHeader == '400' && $Docs->Refound=='1'){
$DocTypeName = $DocsTable->TypeTitleSingle.' זיכוי';    
}   
else {    
$DocTypeName = $DocsTable->TypeTitleSingle; //Doc type name
}
    
    
$DocKindName = array_search(@$Docs->Status, $DocStatus); // Makor / Copy / etc...
$DocId = $Docs->TypeNumber; //מספר מסמך
$CpaType = $Docs->CpaType ?? $BusinessSettings->CpaType;
$DocDate = with(new DateTime($Docs->UserDate))->format('d/m/Y'); //תאריך הפקת המסמך
$DocPrintDate = date('d/m/Y H:i:s'); //תאריך הדפסה
$DocRemarksSpecific = $Docs->Remarks; //Spesific remarks to this doc only
$DocWithPayments = $DocsTable->DocsPayment; //האם להציג תקבולים 0=לא, 1=כן
$DocTermsOfPaymentShow = $DocsTable->PaymentRole; //להציג תנאי תשלום? 0=לא, 1=כן
$DocTermsOfPaymentId = $Docs->PaymentRole; //סוג תנאי תשלום לפי מספר סידורי מהמסד נתונים
$DocTermsOfPaymentName = DB::table('paymentrole')->where('id','=', $DocTermsOfPaymentId)->pluck('Role'); //Name of the terms of payment
if ($DocTermsOfPaymentShow == '1' && $DocTermsOfPaymentName != '') {
$DocTermsOfPaymentText = '<strong>תנאי תשלום: '.@$DocTermsOfPaymentName.'</strong><br />'; // תנאי תשלום טקסט מסודר
}
$DocRemarksAll = @$DocTermsOfPaymentText.''.@$DocRemarksSpecific; //גם הערות קבועות וגם ספציפיות

if ($Docs->UserId!='0'){
$Users = DB::table('users')->where('id','=', $Docs->UserId)->first();
$DocCreatorName = 'הופק על ידי: '.$Users->display_name.' // '; //Name of user create this doc
}
else {
$DocCreatorName = '';    
}   
    
$DocRequestClientSign = '1'; //האם להציג סעיף חתימת לקוח? 0=לא, 1=כן
$DocVatPercent = $Docs->Vat; //אחוז המע״מ
$DocVatAmount = $Docs->VatAmount; //סכום המע״מ
$DocDiscountType = $Docs->DiscountType; //סוג הנחת המסמך 1=אחוזים, 2=שקלים
if ($DocDiscountType == '1') {$DocDiscountTypeSign = '%';} else {$DocDiscountTypeSign = '₪';}
$DocDiscountPercent = $Docs->Discount; //אחוז הנחת המסמך
$DocDiscountAmount = $Docs->DiscountAmount; //סכום הנחת המסמך 0=לא להציג
$DocTaxDeductionPercent = $Docs->NikuyMsBamakor; //אחוז ניכוי מס במקור
$DocTaxDeductionAmount = $Docs->NikoyMasAmount; //סכום ניקוי מס במקור 0=לא להציג
$DocTotalToPay = $Docs->Amount; //סך הכל לתשלום כולל מעמ
$DocTotalPayment = DB::table('docs_payment')->where('DocsId' ,'=', $Docs->id)->where('CompanyNum',  $CompanyNum)->sum('Amount'); //סך הכל שולם בתקבולים
$DocMiddleAmount = $DocTotalToPay-$DocVatAmount+$DocDiscountAmount; //סכום סיכום ביניים
//פרטי מסמך

//פרטי לקוח
$ClientNameOnInvoice = @$Docs->Company; //Name to invoice
$ClientId = @$Docs->CompanyId; //תעודת זהות או עוסק מורשה 0=הסתר
$ClientContactFullName = @$Docs->ContactName; //שם מלא של הלקוח
$ClientMobile = @$Docs->Mobile; //סלולרי 0=הסתר
$ClientPhone = @$Docs->Phone; //טלפון 0=הסתר
$ClientFax = @$Docs->Fax; //פקס 0=הסתר
$ClientCity = @$Docs->City; //City
$ClientAddress = @$Docs->Street; //Street
$ClientAddressNum = @$Docs->Number; //מספר בית
if (@$Docs->PostCode != '0' && @$Docs->PostCode != '') {$ClientZip = ' '.@$Docs->PostCode;} else {$ClientZip='';} //מיקוד
if ((@$ClientCity != '') && (@$ClientAddress != '')) {$ClientAddressPsik = ', ';} //אם יש עיר להוסיף פסיק בין הרחוב לעיר
if ((@$ClientAddress != '') && (@$ClientAddressNum != '')) {$ClientAddressSpace = ' ';} //אם יש מספר בית אז לעשות רווח בין המספר בית לבין הרחוב
$ClientFullAddress = @$ClientAddress.''.@$ClientAddressSpace.''.@$ClientAddressNum.''.@$ClientAddressPsik.''.@$ClientCity.''.$ClientZip; // כתובת מלאה של הלקוח
$ClientEmail =  @$Docs->Email; //כתובת מייל של הלקוח
//פרטי לקוח

//ערכים לפירוט המסמך
$DocsDetailDBs = DB::table('docsdetails')->where('Status' ,'=', '0')->where('CompanyNum',  $CompanyNum)->orderBy('OrderBy', 'ASC')->get();
$DocsDetailDBSumPercentTd = DB::table('docsdetails')->where('Status' ,'=', '1')->where('CompanyNum',  $CompanyNum)->orderBy('OrderBy', 'ASC')->sum('PercentTd');
$NumberOfColspanItemTableF = count($DocsDetailDBs); //כמות טורים בטבלת פירוט מסמך
$NumberOfColspanItemTable = count($DocsDetailDBs)-3; //כמות טורים בטבלת פירוט מסמך, מורידים 3 בגלל טבלת הסיכום הקטנה
if (@$DocDiscountAmount != "0") {$NumberOfRowspanItemTable = "4";}
if (@$DocDiscountAmount == "0") {$NumberOfRowspanItemTable = "3";}
//ערכים לפירוט המסמך


//
		$TypePayment = array(
		"מזומן"=>"1",
		"כרטיס אשראי"=>"3",
		"המחאה"=>"2",
		"העברה בנקאית"=>"4",
		"תו"=>"5",
		"פתק החלפה"=>"6",
		"שטר"=>"7",
		"הוראת קבע"=>"8",
		"אחר"=>"9"
		);
	
		$TashType = array(
		"בתשלום רגיל"=>"1",
		"תשלומים"=>"2",
		"בתשלומי קרדיט"=>"3",
		"בחיוב נדחה"=>"4",
		"באחר"=>"5"
		);
//

$DocHtml = '<html><head>';

//עיצוב CSS
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
//עיצוב CSS

$DocHtml .= '</head><body>';

//חלק עליון ותחתון של המסמך
$DocHtml .= '<!--mpdf
<htmlpageheader name="myheader">
<table cellspacing="0" width="100%" dir="rtl" style="padding:0px;margin:0px;vertical-align: top;"><tr style="padding:0px;margin:0px;vertical-align: top;">
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
if (strlen(@$CompanyPhone) == '9') {$ChunkAfter = '2';} else if (strlen(@$CompanyPhone) == '10') {$ChunkAfter = '3';} else {$ChunkAfter = '4';} //לבדוק כמה ספרות המספר ולהוסיף מקף בהתאם לכמות הספרות
$DocHtml .= '
<tr class="ClientTable">
<td style="padding-right:0px;margin-right:0px;vertical-align: top;padding-left:5px;">טלפון:</td>
<td class="CompanyTable">'.substr(@$CompanyPhone, 0, $ChunkAfter) . '-' . substr(@$CompanyPhone, $ChunkAfter).'</td>
</tr>
';
}

if (@$CompanyMobile != "") {
if (strlen(@$CompanyMobile) == '9') {$ChunkAfter = '2';} else if (strlen(@$CompanyMobile) == '10') {$ChunkAfter = '3';} else {$ChunkAfter = '4';} //לבדוק כמה ספרות המספר ולהוסיף מקף בהתאם לכמות הספרות
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
<img src="'.$PdfDigitalSignImg.'" height="80"><br>
<span style="text-align:center;">מ ס מ ך &nbsp;&nbsp;&nbsp; מ מ ו ח ש ב</span>
</td>';
//מסמך ממוחשב

if (@$CompanyLogo != "") {    
//לוגו העסק
$DocHtml .= '<td width="33.33%" style="text-align: left;vertical-align: top;margin:0px;padding:0px;">
<img src="'.get_loginboostapp_domain().'/office/files/'.@$CompanyLogo.'" style="max-height:70px;max-width:150px;margin-left:0px;padding-left:0px;">
</td>';
//לוגו העסק
}
    
$DocHtml .= '</tr></table><br>';

//פרטי המסמך והלקוח
$DocHtml .= '<table width="100%" dir="rtl" style="padding-right:0px;margin-right:0px;border-collapse: collapse;vertical-align: bottom;">
<tr style="padding-right:0px;margin-right:0px;vertical-align: bottom;">
<td style="font-size: 15pt; color: '.$BackgroundColor.';padding-right:0px;margin-right:0px;vertical-align: bottom;"><strong>'.$DocTypeName.' '.$DocId.'</strong></td>
<td style="text-align:left;font-size: 10pt;padding-left:0px;margin-left:0px;vertical-align: bottom;'.$StatusDocColor.'">'.$DocKindName.'</td>
</tr>
</table>
<div style="border-top: 5px solid '.$BackgroundColor.'; font-size: 7pt; text-align: center; padding:0px;margin:0px; ">
<table width="100%" dir="rtl" style="padding-right:0px;margin-right:0px;margin-top:0px;border-collapse: collapse;vertical-align: bottom;">
<tr class="CompanyTable">
<td style="font-size: 9pt;padding-right:0px;margin-right:0px;vertical-align: top;">
<table style="padding:0px;margin-top:-2px;margin-right:0px;font-size:11px;" cellspacing="0">';

$HowMuchClientParameters = '0';

$DocHtml .= '
<tr class="CompanyTable">
<td style="padding-right:0px;margin-right:0px;vertical-align: top;padding-left:5px;">לכבוד:</td>
<td class="CompanyTable"><strong>'.@$ClientNameOnInvoice.'</strong></td>
</tr>
';
$HowMuchClientParameters++;

if (@$ClientId != "0") {
$DocHtml .= '
<tr class="ClientTable">
<td class="ClientInfo">ת.ז / ע.מ:</td>
<td class="ClientTable">'.@$ClientId.'</td>
</tr>
';
$HowMuchClientParameters++;
}

if ((@$ClientContactFullName != "") && (@$ClientContactFullName != @$ClientNameOnInvoice) && (@$ClientContactFullName != NULL)) {
$DocHtml .= '
<tr class="ClientTable">
<td class="ClientInfo">איש קשר:</td>
<td class="ClientTable">'.@$ClientContactFullName.'</td>
</tr>
';
$HowMuchClientParameters++;
}


if (@$ClientMobile != "" && @$ClientMobile != "0") {
if (strlen(@$ClientMobile) == '9') {$ChunkAfter = '2';} else if (strlen(@$ClientMobile) == '10') {$ChunkAfter = '3';} else {$ChunkAfter = '4';} //לבדוק כמה ספרות המספר ולהוסיף מקף בהתאם לכמות הספרות
$DocHtml .= '
<tr class="ClientTable">
<td class="ClientInfo">טלפון נייד:</td>
<td class="ClientTable">'.substr(@$ClientMobile, 0, $ChunkAfter) . '-' . substr(@$ClientMobile, $ChunkAfter).'</td>
</tr>
';
$HowMuchClientParameters++;
}

if ($ClientPhone != "" && $ClientPhone != "0") {
if (strlen(@$ClientPhone) == '9') {$ChunkAfter = '2';} else if (strlen(@$ClientPhone) == '10') {$ChunkAfter = '3';} else {$ChunkAfter = '4';} //לבדוק כמה ספרות המספר ולהוסיף מקף בהתאם לכמות הספרות
$DocHtml .= '
<tr class="ClientTable">
<td class="ClientInfo">טלפון:</td>
<td class="ClientTable">'.substr(@$ClientPhone, 0, $ChunkAfter) . '-' . substr(@$ClientPhone, $ChunkAfter).'</td>
</tr>
';
$HowMuchClientParameters++;
}

if ($ClientFax != "" && $ClientFax != "0") {
if (strlen(@$ClientFax) == '9') {$ChunkAfter = '2';} else {$ChunkAfter = '3';} //לבדוק כמה ספרות המספר ולהוסיף מקף בהתאם לכמות הספרות
$DocHtml .= '
<tr class="ClientTable">d
<td class="ClientInfo">פקס:</td>
<td class="ClientTable">'.substr(@$ClientFax, 0, $ChunkAfter) . '-' . substr(@$ClientFax, $ChunkAfter).'</td>
</tr>
';
$HowMuchClientParameters++;
}

if (@$ClientFullAddress != "" && @$ClientFullAddress != NULL) {
$DocHtml .= '
<tr class="ClientTable">
<td class="ClientInfo">כתובת:</td>
<td class="ClientTable">'.@$ClientFullAddress.'</td>
</tr>
';
$HowMuchClientParameters++;
}

if (@$ClientEmail != "") {
$DocHtml .= '
<tr class="ClientTable">
<td class="ClientInfo">דוא״ל:</td>
<td class="ClientTable">'.@$ClientEmail.'</td>
</tr>
';
$HowMuchClientParameters++;
}


$DocHtml .= '
</table>
</td>
<td style="text-align:left;font-size:11px;padding-left:0px;margin-left:0px;vertical-align: top;">'.$DocDate.'</td>
</tr>
</table>
';
//פרטי המסמך והלקוח

$DocHtml .= '
</div>
</htmlpageheader>
<htmlpagefooter name="myfooter">
<div style="font-size:9pt;text-align:right;padding-bottom: 1mm;">
<table width="100%" dir="rtl">
<tr>
<td style="font-size: 9pt;">'.$DocCreatorName.' תאריך הדפסה: '.$DocPrintDate.'</td>
<td style="text-align:left;font-size: 9pt;">';

if (@$DocRequestClientSign == "1") {
$DocHtml .= '
חתימת הלקוח: <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>
';
}

$DocHtml .= '
</td>
</tr>
</table>
</div>

<div style="border-top: 5px solid '.$BackgroundColor.'; font-size: 7pt; text-align: center; padding-top: 1mm; ">
<table width="100%" dir="rtl">
<tr>
<td style="font-size: 7pt;">המסמך הופק ונחתם דיגיטלית באמצעות <img src="'.$PdfMakerLogo.'" height="11" style="padding-bottom:-1.2px;"></td>
<td style="text-align:left;font-size: 7pt;">עמוד {PAGENO} מתוך {nb}</td>
</tr>
</table>
</div>
</htmlpagefooter>
<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->';
//חלק עליון ותחתון של המסמך

	
	
	
if ($Docs->TypeHeader != '400') {
//פירוט המסמך
		if (!empty($DocsDetailDBs)){ 			

$DocHtml .= '<table class="items" width="100%" style="font-size: 9pt; border:0px;max-width:100%;  border-collapse: collapse;overflow: hidden;text-align:right;margin-top:0px;padding-top:0px; " dir="rtl">

<thead>
<tr style="border-bottom:1px solid #B9B9B9;">
<td colspan="'.$NumberOfColspanItemTableF.'" style="padding:10px 5px 10px 5px;text-align:right;background-color:'.$BackgroundColor.';color:'.$TextColor.';"><strong>פירוט המסמך</strong></td>
</tr>

<tr style="height:3px;background-color:#FFFFFF;"><td colspan="6" style="height:3px;background-color:#FFFFFF;"></td></tr>

<tr style="border-bottom: 1px solid #B9B9B9;">';

	//כותרת פירוט המסמך 
	foreach($DocsDetailDBs as $DocsDetailDB){
		if ($DocsDetailDB->id == '2') {$PercentTdTotal = $DocsDetailDB->PercentTd+$DocsDetailDBSumPercentTd;}
		else {$PercentTdTotal = $DocsDetailDB->PercentTd;}
		$DocHtml .= '<td width="'.$PercentTdTotal.'%" style="padding:5px;border-bottom: 1px solid #B9B9B9;text-align:right;"><strong>'.@$DocsDetailDB->Title.'</strong></td>';
	}
	//כותרת פירוט המסמך

$DocHtml .= '</tr></thead><tbody>';

	
	
	
	
	
$DocLists = DB::table('docslist')->where('DocsId' ,'=', $Docs->id)->where('CompanyNum',  $CompanyNum)->get();
if (!empty($DocLists)){ 			
foreach($DocLists as $DocList){

//לופ פריטים
$DocHtml .= '<!-- ITEMS HERE --><tr style="border-bottom: 1px solid #B9B9B9;">';

	//פריט פירוט המסמך
	if (!empty($DocsDetailDBs)){ 			
	foreach($DocsDetailDBs as $DocsDetailDB){
		$DocsDetailsDbName = $DocsDetailDB->NameDb;
		$DocsDetailsSign = $DocsDetailDB->Sign;
		if ($DocsDetailDB->NumberFormat == '0') {$DocsDetailsDbNameTotal = number_format($DocList->$DocsDetailsDbName, 2);}
		else {$DocsDetailsDbNameTotal = $DocList->$DocsDetailsDbName;}
		$DocHtml .= '<td class="totals cost" style="padding:5px;text-align:right;border-bottom: 1px solid #B9B9B9;">'.$DocsDetailsDbNameTotal.' '.$DocsDetailsSign.'</td>';
	}
	}
	//פריט פירוט המסמך

$DocHtml .= '</tr><!-- END ITEMS HERE -->';
//לופ פריטים

}
}
	
	
	
	
	

$DocHtml .= '<tr style="border-bottom:1px solid #B9B9B9 !important;" ><td class="blanktotal" colspan="'.$NumberOfColspanItemTable.'" rowspan="'.$NumberOfRowspanItemTable.'" style="border:0px;border-right:0px;">'.$DocRemarksAll.'</td>
<td class="totals" colspan="2" style="text-align:right;direction:rtl;padding:5px;border:0px;border-bottom:1px solid #B9B9B9;"><strong>סכום ביניים:</strong></td>
<td class="totals cost" style="padding:5px;text-align:right;border:0px;border-bottom: 1px solid #B9B9B9;">'.number_format($DocMiddleAmount, 2).' ₪</td>
</tr>';

if (@$DocDiscountAmount != "0") {
$DocHtml .= '
<tr style="border-bottom:1px solid #B9B9B9 !important;" >
<td class="totals" colspan="2" style="text-align:right;direction:rtl;padding:5px;border:0px;border-bottom: 1px solid #B9B9B9;"><strong>הנחה ('.$DocDiscountPercent.''.$DocDiscountTypeSign.'):</strong></td>
<td class="totals cost" style="padding:5px;text-align:right;border:0px;border-bottom: 1px solid #B9B9B9;">'.number_format($DocDiscountAmount, 2).' ₪</td>
</tr>';
}

$DocHtml .= '
<tr style="border-bottom:1px solid #B9B9B9 !important;" >
<td class="totals" colspan="2" style="text-align:right;direction:rtl;padding:5px;border:0px;"><strong>מע״מ ('.$DocVatPercent.'%):</strong></td>
<td class="totals cost" style="padding:5px;text-align:right;border:0px;">'.number_format($DocVatAmount, 2).' ₪</td>
</tr>
<tr style="border-bottom:1px solid #B9B9B9 !important;" >
<td class="totals" colspan="2" style="text-align:right;direction:rtl;padding:5px;border:0px;font-size:13px;background-color:'.$BackgroundColor.';color:'.$TextColor.';"><strong>סה״כ לתשלום:</strong></td>
<td class="totals cost" style="padding:5px;text-align:right;border:0px;font-size:13px;background-color:'.$BackgroundColor.';color:'.$TextColor.';"><strong>'.number_format($DocTotalToPay, 2).' ₪</strong></td>
</tr>
</tbody>
</table>';
	$DocHtml .= '<br><br>';
	}

//פירוט המסמך
}

else {
$DocLists = DB::table('docslist')->where('DocsId' ,'=', $Docs->id)->where('CompanyNum',  $CompanyNum)->get();
if (!empty($DocLists)){ 	

//פירוט המסמך
$DocHtml .= '<table class="items" width="100%" style="font-size: 9pt; border:0px;max-width:100%;  border-collapse: collapse;overflow: hidden;text-align:right;margin-top:0px;padding-top:0px; " dir="rtl">

<thead>
<tr style="border-bottom:1px solid #B9B9B9;">
<td colspan="3" style="padding:10px 5px 10px 5px;text-align:right;background-color:'.$BackgroundColor.';color:'.$TextColor.';"><strong>פירוט החשבוניות</strong></td>
</tr>

<tr style="height:3px;background-color:#FFFFFF;"><td colspan="6" style="height:3px;background-color:#FFFFFF;"></td></tr>

<tr style="border-bottom: 1px solid #B9B9B9;">
<td width="5%" style="padding:5px;border-bottom: 1px solid #B9B9B9;text-align:right;"><strong>#</strong></td>
<td width="95%" style="padding:5px;border-bottom: 1px solid #B9B9B9;text-align:right;"><strong>מספר חשבונית</strong></td>
</tr></thead><tbody>';

	
	
$i = '1';
foreach($DocLists as $DocList){

//לופ פריטים
$DocHtml .= '<!-- ITEMS HERE --><tr style="border-bottom: 1px solid #B9B9B9;">';

	//פריט פירוט המסמך
		$DocHtml .= '
		<td class="totals cost" style="padding:5px;text-align:right;border-bottom: 1px solid #B9B9B9;">'.$i.'</td>
		<td class="totals cost" style="padding:5px;text-align:right;border-bottom: 1px solid #B9B9B9;">'.@$DocList->ItemName.'</td>
		';
	//פריט פירוט המסמך

$DocHtml .= '</tr><!-- END ITEMS HERE -->';
	$i ++;
//לופ פריטים

}
	
	
$DocHtml .= '
</tbody>
</table>';
	$DocHtml .= '<br><br>';

}
	}


if ($DocWithPayments == '1') {
    
if ($Docs->TypeHeader == '400' && $Docs->ActivityJson!='') {
    
$DocHtml .= '<table class="" width="100%" style="font-size: 9pt; text-align:center; border-collapse: collapse;" dir="rtl">
<thead>
<tr style="border-bottom:1px solid #B9B9B9;">
<td colspan="2" style="padding:10px 5px 10px 5px;text-align:right;background-color:'.$BackgroundColor.';color:'.$TextColor.';"><strong>פירוט</strong></td>
</tr>

<tr style="height:3px;background-color:#FFFFFF;"><td colspan="4" style="height:3px;background-color:#FFFFFF;"></td></tr>

<tr style="border-bottom:1px solid #B9B9B9;">
<td class="TakbolimTD" width="15%" style="padding:5px;text-align:right;"><strong>קוד פריט</strong></td>
<td class="TakbolimTD" width="85%" style="padding:5px;text-align:right;"><strong>שם פריט</strong></td>
</tr>
</thead>
<tbody>';   
   
    
$Loops =  json_decode($Docs->ActivityJson,true);	
foreach($Loops['data'] as $key=>$val){ 

$ItemId = $val['ItemId'];
$ItemText = $val['ItemText'];

$DocHtml .= '
<!-- ITEMS HERE -->
<tr style="border-bottom:1px solid #B9B9B9;">
<td class="totals cost TakbolimTD" style="padding-right:5px;">'.$ItemId.'</td>
<td class="totals cost TakbolimTD">'.$ItemText.'</td>
</tr>
';
    
}      
    
$DocHtml .= '<!-- END ITEMS HERE --></tbody></table>';    
$DocHtml .= '<br><br>';    
    
}
       
    
//תקבולים
$DocPayments = DB::table('docs_payment')->where('DocsId' ,'=', $Docs->id)->where('CompanyNum',  $CompanyNum)->get();
if (!empty($DocPayments)){ 			


$DocHtml .= '<table class="" width="100%" style="font-size: 9pt; text-align:center; border-collapse: collapse;" dir="rtl">
<thead>
<tr style="border-bottom:1px solid #B9B9B9;">
<td colspan="4" style="padding:10px 5px 10px 5px;text-align:right;background-color:'.$BackgroundColor.';color:'.$TextColor.';"><strong>תקבולים</strong></td>
</tr>

<tr style="height:3px;background-color:#FFFFFF;"><td colspan="4" style="height:3px;background-color:#FFFFFF;"></td></tr>

<tr style="border-bottom:1px solid #B9B9B9;">
<td class="TakbolimTD" width="15%" style="padding:5px;text-align:right;"><strong>סוג תקבול</strong></td>
<td class="TakbolimTD" style="padding:5px;text-align:right;"><strong>פירוט</strong></td>
<td class="TakbolimTD" width="15%" style="padding:5px;text-align:right;"><strong>תאריך פרעון</strong></td>
<td class="TakbolimTD" width="15%" style="padding:5px;text-align:right;"><strong>סה״כ</strong></td>
</tr>
</thead>
<tbody>';
    
//לופ תקבולים
	
	
foreach($DocPayments as $DocPayment){

$PaymentNum = DB::table('docs_payment')->where('DocsId' ,'=', $Docs->id)->where('CompanyNum',  $CompanyNum)->where('L4digit',  $DocPayment->L4digit)->where('TypePayment',  '3')->count();

    if ($DocPayment->TypePayment == 2) {
        $DocPaymentNotes = 'מספר המחאה: ' . @$DocPayment->CheckNumber . ', קוד בנק: ' . @$DocPayment->CheckBankCode . ', סניף: ' . @$DocPayment->CheckBankSnif . ', מספר חשבון: ' . @$DocPayment->CheckBank;
    } elseif ($DocPayment->TypePayment == 3) {
        if ($CpaType == 1) {
            $DocPaymentNotes = @$DocPayment->BrandName . ' המסתיים ב-' . @$DocPayment->L4digit . ($DocPayment->tashType == 2 ? ' ב-' . $DocPayment->Payments : '') . ' ' . array_search(@$DocPayment->tashType, $TashType) . ', מס׳ אישור: ' . @$DocPayment->ACode;
        }
        else {
            $DocPaymentNotes = @$DocPayment->BrandName . ' המסתיים ב-' . @$DocPayment->L4digit . ' תשלום ' . @$DocPayment->Payments . ' מתוך  ' . $PaymentNum . ' ' . array_search(@$DocPayment->tashType, $TashType) . ', מס׳ אישור: ' . @$DocPayment->ACode;
        }
    } elseif ($DocPayment->TypePayment == '4') {
        $DocPaymentNotes = 'מספר אסמכתא: ' . @$DocPayment->BankNumber;
    } elseif (in_array($DocPayment->TypePayment, [1,5,6,7,8,9])) {
        $DocPaymentNotes = '';
    } else {
        $DocPaymentNotes = 'ללא פירוט';
    }
	
$DocHtml .= '
<!-- ITEMS HERE -->
<tr style="border-bottom:1px solid #B9B9B9;">
<td class="totals cost TakbolimTD" style="padding-right:5px;">'.array_search(@$DocPayment->TypePayment, $TypePayment).'</td>
<td class="totals cost TakbolimTD">'.$DocPaymentNotes.'</td>
<td class="totals cost TakbolimTD">'.with(new DateTime($DocPayment->CheckDate))->format('d/m/Y').'</td>
<td class="totals cost TakbolimTD">₪'.number_format($DocPayment->Amount, 2).'</td>
</tr>
';
	
}
//לופ תקבולים

$DocHtml .= '<tr style="height:10px;" ><td class="blanktotal" colspan="4" style="border:0px;border-right:0px;"></td></tr>';

if (@$DocTaxDeductionAmount != "0") {
$DocHtml .= '
<tr style="border-bottom:1px solid #B9B9B9 !important;" >
<td class="blanktotal" colspan="2" style="border:0px;border-right:0px;"></td>
<td class="totals" style="text-align:right;direction:rtl;padding:5px;border:0px;margin-top:1px;padding-top:1px;"><strong>ניכוי מס ('.$DocTaxDeductionPercent.'%):</strong></td>
<td class="totals cost" style="padding-top:5px;padding-bottom:5px;text-align:right;border:0px;margin-top:1px;padding-top:1px;">'.number_format($DocTaxDeductionAmount, 2).' ₪</td>
</tr>
';
}

	
$DocHtml .= '
<tr style="border-bottom:1px solid #B9B9B9 !important;" >
<td class="blanktotal" colspan="2" style="border:0px;border-right:0px;"></td>
<td class="totals" style="text-align:right;direction:rtl;padding:5px;border:0px;font-size:13px;background-color:'.$BackgroundColor.';color:'.$TextColor.';"><strong>סה״כ שולם:</strong></td>
<td class="totals cost" style="padding-top:5px;padding-bottom:5px;text-align:right;border:0px;font-size:13px;background-color:'.$BackgroundColor.';color:'.$TextColor.';"><strong>'.number_format($DocTotalPayment+$DocTaxDeductionAmount, 2).' ₪</strong></td>
</tr>';

$DocHtml .= '<!-- END ITEMS HERE --></tbody></table>';
}
//תקבולים
}
	
if ($Docs->TypeHeader == '400'){
$DocHtml .= '<div dir="rtl" style="text-align:right; font-weight:bold;"><span dir="rtl">'.$DocRemarksAll.'</span><br></div>';       
}       
    
if ($Docs->UserDate != $Docs->PaymentTime) {
$DocHtml .= '<div dir="rtl" style="text-align:right; font-weight:bold;">לתשלום עד: <span dir="ltr">'.with(new DateTime(@$Docs->PaymentTime))->format('d/m/Y').'</span></div>';
}

$BalanceAmountt = DB::table('client')->where('id', $Docs->ClientId)->pluck('BalanceAmount');
if ($Docs->ClientId != '0' && $BusinessSettings->ShowBalance == '0' && $DocsTable->ShowBalance == '1' && $BalanceAmountt != '') {
if ($BalanceAmountt == '0') {$BalanceColor = 'black';} else {$BalanceColor = 'red';}
$DocHtml .= '<div dir="rtl" style="text-align:right; font-weight:bold;">יתרת כרטסת: <span dir="ltr" style="color:'.$BalanceColor.';">'.@$BalanceAmountt.'</span> <span style="color:'.$BalanceColor.';">₪</span></div>';
}
	


$DocHtml .= '</body></html>';


if ($HowMuchClientParameters == "1") {$PdfHeaderHeight = "62";}
elseif ($HowMuchClientParameters == "2") {$PdfHeaderHeight = "65";}
elseif ($HowMuchClientParameters == "3") {$PdfHeaderHeight = "68";}
elseif ($HowMuchClientParameters == "4") {$PdfHeaderHeight = "72";}
elseif ($HowMuchClientParameters == "5") {$PdfHeaderHeight = "75";}
elseif ($HowMuchClientParameters == "6") {$PdfHeaderHeight = "80";}
elseif ($HowMuchClientParameters == "7") {$PdfHeaderHeight = "84";}
else {$PdfHeaderHeight = "86";}


$mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'default_font' => 'assistant',
		'margin_left' => 15,
		'margin_right' => 15,
		'margin_top' => $PdfHeaderHeight,
		'margin_bottom' => 25,
		'margin_header' => 10,
		'margin_footer' => 10
]);
}