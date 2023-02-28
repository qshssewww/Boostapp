<?php
ini_set("pcre.backtrack_limit", "5000000");
 require_once '../../app/initcron.php';
//require_once '../../app/initPDF.php';
require_once '../PDF18/vendor/autoload.php';
require_once __DIR__.'/../Classes/247SoftNew/UpdateBusinessNumber.php';
require_once __DIR__.'/../Classes/247SoftNew/ClientGoogleAddress.php';
//set_time_limit(0);

error_reporting(0);
ini_set("memory_limit", "256M");

$CompanyNum = Auth::user()->CompanyNum;
$UserId = Auth::user()->id;

$BusinessSettings = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();
$companyGoogleAddress = ClientGoogleAddress::getBusinessAddress($CompanyNum);
//עיצוב מסמך
$BackgroundColor = $BusinessSettings->DocsBackgroundColor; //צבע רקע, קווים וכותרות
$TextColor = $BusinessSettings->DocsTextColor; //צבע טקסט על הרקע שנבחר למעלה
$CompanyLogo = $BusinessSettings->DocsCompanyLogo; //לוגו של העסק (מוצג בצד שמאל)
$PdfMakerLogo = "247soft.png"; //לוגו של המערכת שיצרה את המסמך
$PdfDigitalSignImg = "digitalsignature.png"; //אייקון מסמך ממוחשב וחתום דיגיטלית
//עיצוב מסמך

//פרטי עסק
$BusinessSettingsCity = DB::table('cities')->where('CityId', '=', $BusinessSettings->City)->first(); 
$UsersInfo = DB::table('users')->where('id', '=', $UserId)->where('CompanyNum', '=', $CompanyNum)->first(); 
	
$CompanyName = @$BusinessSettings->CompanyName; //Business name
if (@$BusinessSettings->BusinessType == '2') {$CompanyKind = "ע.מ";}//The kind of the company
elseif (@$BusinessSettings->BusinessType == '3') {$CompanyKind = "ח.פ";}//The kind of the company
elseif (@$BusinessSettings->BusinessType == '4') {$CompanyKind = "ח.צ";}//The kind of the company
elseif (@$BusinessSettings->BusinessType == '5') {$CompanyKind = "ע.פ";}//The kind of the company
elseif (@$BusinessSettings->BusinessType == '6') {$CompanyKind = "מלכ״ר";}//The kind of the company
elseif (@$BusinessSettings->BusinessType == '7') {$CompanyKind = "איחוד עוסקים";}//The kind of the company
elseif (@$BusinessSettings->BusinessType == '8') {$CompanyKind = "מ.מ";}//The kind of the company
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
$CompanyFullAddress = $companyGoogleAddress ? $companyGoogleAddress->address : $CompanyFullAddress;

if (!isset($_REQUEST["month"])) $_REQUEST["month"] = date("m");
if (!isset($_REQUEST["year"])) $_REQUEST["year"] = date("Y");


$cMonth = $_REQUEST["month"];
$cYear = $_REQUEST["year"];

$StartDate = $cYear.'-'.$cMonth.'-01';
$EndDate = $cYear.'-'.$cMonth.'-'.date('t',strtotime($StartDate));

$brand_id = $_REQUEST['brandid'] ?? 0;


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
<img src="'.$PdfDigitalSignImg.'" height="80"><br>
<span style="text-align:center;">מ ס מ ך &nbsp;&nbsp;&nbsp; מ מ ו ח ש ב</span>
</td>';
//מסמך ממוחשב

if (@$CompanyLogo != "") {    
//לוגו העסק
$DocHtml .= '<td width="33.33%" style="text-align: left;vertical-align: top;margin:0px;padding:0px;">
<img src="../files/'.@$CompanyLogo.'" style="max-height:70px;max-width:150px;margin-left:0px;padding-left:0px;">
</td>';
//לוגו העסק
}

$DocHtml .= '</tr></table><br>';

//פרטי המסמך והלקוח
$DocHtml .= '<table width="100%" dir="rtl" style="padding-right:0px;margin-right:0px;border-collapse: collapse;vertical-align: bottom;">
<tr style="padding-right:0px;margin-right:0px;vertical-align: bottom;">
<td style="font-size: 15pt; color: '.$BackgroundColor.';padding-right:0px;margin-right:0px;vertical-align: bottom;"><strong>כרטסת הנהלת חשבונות</strong></td>
<td style="text-align:left;font-size: 10pt;padding-left:0px;margin-left:0px;vertical-align: bottom;" dir="ltr">'.date('d/m/Y H:i:s').'</td>
</tr>
</table>
<div style="border-top: 5px solid '.$BackgroundColor.'; font-size: 7pt; text-align: center; padding:0px;margin:0px; ">
<table width="100%" dir="rtl" style="padding-right:0px;margin-right:0px;margin-top:0px;border-collapse: collapse;vertical-align: bottom;">
<tr class="CompanyTable">
<td style="font-size: 9pt;padding-right:0px;margin-right:0px;vertical-align: top;">
<table style="padding:0px;margin-top:-2px;margin-right:0px;font-size:11px;" cellspacing="0">';

$HowMuchClientParameters = '0';



$DocHtml .= '
</table>
</td>
<td style="text-align:left;font-size:11px;padding-left:0px;margin-left:0px;vertical-align: top;"></td>
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
<td style="font-size: 9pt;">הופק על ידי: '.$UsersInfo->display_name.'</td>
<td style="text-align:left;font-size: 9pt;">';


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

	
	
$TotalPreAmount = '0';
$TotalVatAmount = '0';
$TotalAmount = '0';


$DocsTables = DB::table('docstable')->where('CompanyNum','=',$CompanyNum)->where('Status','=','0')->where('Accounts', '=', '1')->get();

$updateBusiness = UpdateBusinessNumber::where('company_num', $CompanyNum)->first();
$updateBusinessColumn = !empty($updateBusiness) ? '<th style="text-align:right;">מספר עוסק</th>' : '';

//לופ לסוגי המסמכים
foreach($DocsTables as  $DocsTable) {
    
$DocGetsCountThisDates = DB::table('docs')->where('CompanyNum','=',$CompanyNum)->where('TypeDoc','=', $DocsTable->id)->whereBetween('UserDate', array($StartDate, $EndDate))->count();

if ($DocGetsCountThisDates != '0') {

$DocHtml .= '<table class="table table-hover text-right display wrap" dir="rtl" width="100%">
';


$DocHtml .= '<thead id="T'.$DocsTable->id.'">

<tr>
<td colspan="8" class="bg-info text-white"><strong>'.$DocsTable->TypeTitle.'</strong></td>
</tr>

          <tr class="bg-dark text-white">
            <th style="text-align:right;">מס׳ מסמך #</th>
            <th style="text-align:right;">שם הלקוח</th>
            '.$updateBusinessColumn.'
            <th style="text-align:right;">תאריך ערך</th>
            <th style="text-align:right;">תאריך המסמך</th>
            <th style="text-align:right;">סה״כ לפני מע״מ</th>
            <th style="text-align:right;">מע״מ</th>
            <th style="text-align:right;">סה״כ מע״מ</th>
            <th style="text-align:right;">סה״כ כולל מע״מ</th>
          </tr>

</thead><tbody>';


$DocGets = DB::table('docs')->where('CompanyNum','=',$CompanyNum)->where('TypeDoc','=', $DocsTable->id)->whereBetween('UserDate', array($StartDate, $EndDate));

    if (!empty($brand_id)) $DocGets = $DocGets->where('Brands', $brand_id);
    
    $DocGets = $DocGets->orderBy('id', 'ASC')->get();

    $sumVatQuery = DB::table('docs')
        ->where('CompanyNum','=',$CompanyNum)
        ->where('TypeDoc','=',$DocsTable->id)
        ->whereBetween('UserDate', array($StartDate, $EndDate));
    if((int)$brand_id !== 0) {
        $sumVatQuery->where('Brands', $brand_id);
    }
    $TotalVatAmount = $sumVatQuery->sum('VatAmount');

    $sumAmountQuery = DB::table('docs')
        ->where('CompanyNum','=',$CompanyNum)
        ->where('TypeDoc','=',$DocsTable->id)
        ->whereBetween('UserDate', array($StartDate, $EndDate));
    if((int)$brand_id !== 0) {
        $sumAmountQuery->where('Brands', $brand_id);
    }
    $TotalAmount = $sumAmountQuery->sum('Amount');

$TotalPreAmount = $TotalAmount - $TotalVatAmount;
									
foreach ($DocGets as $DocGet) {

    $getBusinessUpdate  = UpdateBusinessNumber::where('company_num', $CompanyNum)
        ->where('until_date', '>', $DocGet->UserDate)
        ->orderBy('until_date', 'ASC')
        ->first();

    $updateBusinessColumnVar = !empty($updateBusiness) ? '<td style="text-align:right;">'.($getBusinessUpdate->business_number ?? $CompanyNumber).'</td>' : '';

$PreVat = $DocGet->Amount-$DocGet->VatAmount;    
    
$DocHtml .= '<tr>
<td style="text-align:right;">'.$DocGet->TypeNumber.'</td>
<td style="text-align:right;">'.$DocGet->Company.'</td>
'.$updateBusinessColumnVar.'
<td style="text-align:right;">'.with(new DateTime($DocGet->Dates))->format('d/m/Y').'</td>
<td style="text-align:right;">'.with(new DateTime($DocGet->UserDate))->format('d/m/Y').'</td>
<td style="text-align:right;" dir="ltr">'.$PreVat.'</td>
<td style="text-align:right;">'.$DocGet->Vat.'%</td>
<td style="text-align:right;" dir="ltr">'.$DocGet->VatAmount.'</td>
<td style="text-align:right;" dir="ltr">'.$DocGet->Amount.'</td>';

$DocHtml .= '</tr>';

  
}
    
$DocHtml .= '<tr class="active" style="font-weight: bold;">
<td ></td>
<td ></td>
<td ></td>
<td></td>
<td style="text-align:right;" dir="ltr">'.$TotalPreAmount.'</td>
<td> </td>
<td style="text-align:right;" dir="ltr">'.$TotalVatAmount.'</td>
<td style="text-align:right;" dir="ltr">'.$TotalAmount.'</td>
</tr>

</tbody>

</table><hr>';   
    
    
}
    
     
    
}
//לופ לסוגי המסמכים







$DocHtml .= '</body></html>';



$mpdf = new \Mpdf\Mpdf([

        'mode' => 'utf-8',
        'default_font' => 'dejavusans',//original value - assistant
		'margin_left' => 15,
		'margin_right' => 15,
		'margin_top' => 55,
		'margin_bottom' => 25,
		'margin_header' => 10,
		'margin_footer' => 10,
       // 'format' => 'A4-L',    
      //  'orientation' => 'L'
]);
$mpdf->useLang = true;
$mpdf->SetProtection(array('print'));
$mpdf->SetTitle($CompanyName);
$mpdf->SetAuthor("247SOFT");
$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML($DocHtml);
$mpdf->Output($CompanyName.'.pdf', 'I');
//==============================================================
//==============================================================
//==============================================================
?>