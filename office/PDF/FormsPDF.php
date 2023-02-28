<?php
require_once '../../app/initPDF.php'; 
require_once '../PDF18/vendor/autoload.php';

$CompanyNum = Auth::user()->CompanyNum;


defined('TBL_DYNAMICFORMANSWERS') or define('TBL_DYNAMICFORMANSWERS', 'dynamicforms_answers');
defined('TBL_DYNAMICFORM') or define('TBL_DYNAMICFORM', 'dynamicforms');

// retrive data from db
$data = DB::table(TBL_DYNAMICFORMANSWERS)
            ->select(TBL_DYNAMICFORMANSWERS.'.*', TBL_DYNAMICFORM.'.name as formName', TBL_DYNAMICFORM.'.GroupNumber as GroupNumber')
            ->join(TBL_DYNAMICFORM, TBL_DYNAMICFORM.'.id', '=', TBL_DYNAMICFORMANSWERS.'.FormId')
            ->where(TBL_DYNAMICFORMANSWERS.'.id', '=', (int) $_GET['id'])
            ->where(TBL_DYNAMICFORMANSWERS.'.CompanyNum', '=', $CompanyNum)->first();


//פרטי עסק
$BusinessSettings = DB::table('settings')->where('CompanyNum',  $CompanyNum)->first();
$BusinessSettingsCity = DB::table('cities')->where('CityId', '=', $BusinessSettings->City)->first(); 
	
//עיצוב מסמך
$BackgroundColor = $BusinessSettings->DocsBackgroundColor; //צבע רקע, קווים וכותרות
$TextColor = $BusinessSettings->DocsTextColor; //צבע טקסט על הרקע שנבחר למעלה
$CompanyLogo = $BusinessSettings->DocsCompanyLogo; //לוגו של העסק (מוצג בצד שמאל)
$PdfMakerLogo = "247soft.png"; //לוגו של המערכת שיצרה את המסמך
$PdfDigitalSignImg = "digitalsignature.png"; //אייקון מסמך ממוחשב וחתום דיגיטלית
//עיצוב מסמך
	


if (is_object($data)) {

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


$Docs = DB::table('client')->where('CompanyNum', $CompanyNum)->where('id', $data->ClientId)->first();  



//פרטי לקוח
$ClientNameOnInvoice = @$Docs->CompanyName; //Name to invoice
$ClientId = @$Docs->CompanyId; //תעודת זהות או עוסק מורשה 0=הסתר
$ClientContactFullName = @$Docs->ContactName; //שם מלא של הלקוח
$ClientMobile = @$Docs->ConatctMobile; //סלולרי 0=הסתר
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



$DocHtml = <<<BOF
<!DOCTYPE html>
<html lang="he" class="no-js" ng-app="dynForm" lang="">

<head>
    <meta charset="UTF-8">
    <title></title>
    <style>
        html, body{
            margin: 0px;
            padding: 0px;
            direction: rtl;
        }
    </style>
</head>
<body>
BOF;



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
$date = new DateTime($data->created);
//פרטי המסמך והלקוח
$DocHtml .= '<table width="100%" dir="rtl" style="padding-right:0px;margin-right:0px;border-collapse: collapse;vertical-align: bottom;">
<tr style="padding-right:0px;margin-right:0px;vertical-align: bottom;">
<td style="font-size: 15pt; color: '.$BackgroundColor.';padding-right:0px;margin-right:0px;vertical-align: bottom;"><strong>'.$data->formName.' גרסה: '.$data->GroupNumber.'</strong></td>
<td style="text-align:left;font-size: 10pt;padding-left:0px;margin-left:0px;vertical-align: bottom;'.@$StatusDocColor.'">תאריך חתימה: : '. $date->format('d/m/Y H:i').'</td>
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
<td style="padding-right:0px;margin-right:0px;vertical-align: top;padding-left:5px;">שם לקוח:</td>
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
if (strlen(@$ClientMobile) == '9') {$ChunkAfter = '2';} else {$ChunkAfter = '3';} //לבדוק כמה ספרות המספר ולהוסיף מקף בהתאם לכמות הספרות
$DocHtml .= '
<tr class="ClientTable">
<td class="ClientInfo">טלפון נייד:</td>
<td class="ClientTable">'.substr(@$ClientMobile, 0, $ChunkAfter) . '-' . substr(@$ClientMobile, $ChunkAfter).'</td>
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
<td style="font-size: 9pt;">תאריך הדפסה: '.date('d-m-Y').'</td>
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


// mysql bug for json type
$data->data = json_decode($data->data);
// $signuture = $data->data->signuture;

foreach($data->data->data as $d){
    if(!empty($d->i)){
        $DocHtml .= $d->i;
        continue;
    }

	$DocHtml .= sprintf('<h4>%s</h4>', $d->q);
	if(!is_object(@$d->a)){
		$DocHtml .= sprintf('<div>%s</div>', @$d->a);
		continue;
    }
    $DocHtml .= sprintf('<ul>');
    foreach($d->a as $a=>$b){
        
        if($a === 'explain') continue; // ignore explain delt with item
        if($a === 'push') continue; // ignore explain delt with item

        // radio type
        if($a == 'item'){       


            // we would check if next item is explain, and reverse back
            // old form logic before 20/03/2019
            next($d->a);
            if(key($d->a) === 'explain'){
                $explain = $d->a;
            }
            prev($d->a);

            if(isset($d->a->explain)){
                $explain = new stdClass();
                $explain->explain = $d->a->explain;
            }


            $DocHtml .= sprintf('<li>%s %s</li>', $b, !empty($explain)?sprintf('<div style="">פירוט: %s</div>', $explain->explain):'');
            unset($explain);
            continue;
        }

        // checkbox type
        if(is_object($b)){
            $DocHtml .= sprintf('<li>%s %s</li>', $a, !empty($b->explain)?sprintf('<div style="">פירוט: %s</div>', $b->explain):'');
            continue;
        }
        // text type
//        if(is_string($a)) $DocHtml .= sprintf('<li>%s</li>', $a);
        
    }
    $DocHtml .= sprintf('</ul>');
}
// $html .= sprintf('<pre>%s</pre>', print_r($data->data->signuture, true));
$DocHtml .= sprintf('<h3>חתימת לקוח</h3>');
// $DocHtml .= sprintf('<img src="%s" style="height: 100px;">', $data->data->signuture);

try {
$dom = new DOMDocument('1.0', 'utf-8');
$dom->loadXML(base64_decode (str_replace('data:image/svg+xml;base64,', '', $data->data->signuture)));
$svg = $dom->documentElement;
$svg->setAttribute('width', 568/2);
$svg->setAttribute('height', 220/2);
$svg->setAttribute('viewBox', '-200 0 '.(568).' '.(220));
$DocHtml .= sprintf('%s', explode("\n", $dom->saveXML(), 2)[1] );
}catch (\Exception $e) {}


//$DocHtml .= sprintf('%s', $data->data->signuture);

$DocHtml .= <<<BOF
</body>
</html>
BOF;

// echo base64_decode (str_replace('data:image/svg+xml;base64,', '', $data->data->signuture));
// echo sprintf('%s', explode("\n", $dom->saveXML(), 2)[1] );
// printf('<pre dir="ltr" class="text-align: left">%s</pre>', print_r($data, true));
// echo $DocHtml;
// exit; 
$mpdf = new \Mpdf\Mpdf([

        'mode' => 'utf-8',
        'default_font' => 'assistant',
		'margin_left' => 15,
		'margin_right' => 15,
		'margin_top' => 65,
		'margin_bottom' => 25,
		'margin_header' => 10,
		'margin_footer' => 10,
       // 'format' => 'A4-L',    
      //  'orientation' => 'L'
]);
$mpdf->useLang = true;
$mpdf->SetProtection(array('print'));
$mpdf->SetTitle('');
$mpdf->SetAuthor("247SOFT");
$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML(@$DocHtml);
$mpdf->Output(time().'_forms.pdf', 'I');
    
}
else {
$DocHtml = '<div align="center"><img src='.$PdfMakerLogo.' height="50"><br /><br /><u>מסמך לא קיים</u></div>';

$mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'default_font' => 'assistant',
		'margin_left' => 15,
		'margin_right' => 15,
		'margin_top' => 15,
		'margin_bottom' => 25,
		'margin_header' => 10,
		'margin_footer' => 10
]);
$mpdf->useLang = true;
$mpdf->SetProtection(array('print'));
$mpdf->SetTitle('מסמך לא קיים');
$mpdf->SetAuthor("247SOFT");
$mpdf->SetDisplayMode('fullpage');
$mpdf->WriteHTML($DocHtml);
$mpdf->Output('מסמך לא קיים.pdf', 'I');    
    
}
//==============================================================
//==============================================================
//==============================================================
?>