<?php
require_once __DIR__.'/../../Classes/247SoftNew/ClientGoogleAddress.php';
//פרטי עסק
$BusinessSettings = DB::table('settings')->where('CompanyNum', $CompanyNum)->first();
$DocHeaderHtml = '';

if (!empty($BusinessSettings)) :
    $BusinessSettingsCity = DB::table('cities')->where('CityId', '=', $BusinessSettings->City)->first();
    $BusinessAddressGoogle = ClientGoogleAddress::getBusinessAddress($CompanyNum);

//עיצוב מסמך
    $BackgroundColor = $BackgroundColor ?? $BusinessSettings->DocsBackgroundColor; //צבע רקע, קווים וכותרות
    $TextColor = $BusinessSettings->DocsTextColor; //צבע טקסט על הרקע שנבחר למעלה
    $CompanyLogo = $CompanyLogo ?? $BusinessSettings->DocsCompanyLogo; //לוגו של העסק (מוצג בצד שמאל)
    $CompanyBusinessType = $BusinessSettings->BusinessType;  //Business name
    $PdfDigitalSignImg = "/office/PDF/digitalsignature.png"; //אייקון מסמך ממוחשב וחתום דיגיטלית
//עיצוב מסמך


    $CompanyName = $CompanyName ?? $BusinessSettings->CompanyName; //Business name
    if ($CompanyBusinessType == '2') {
        $CompanyKind = "ע.מ";
    }//The kind of the company
    elseif ($CompanyBusinessType == '3') {
        $CompanyKind = "ח.פ";
    }//The kind of the company
    elseif ($CompanyBusinessType == '4') {
        $CompanyKind = "ח.צ";
    }//The kind of the company
    elseif ($CompanyBusinessType == '5') {
        $CompanyKind = "ע.פ";
    }//The kind of the company
    elseif ($CompanyBusinessType == '6') {
        $CompanyKind = "מלכ״ר";
    }//The kind of the company
    elseif ($CompanyBusinessType == '7') {
        $CompanyKind = "איחוד עוסקים";
    }//The kind of the company
    elseif ($CompanyBusinessType == '8') {
        $CompanyKind = "מ.מ";
    }//The kind of the company

    $CompanyNumber = $CompanyNumber ?? $BusinessSettings->CompanyId; //מספר עוסק

    $CompanyPhone = @$BusinessSettings->ContactPhone; //טלפון
    $CompanyFax = @$BusinessSettings->ContactFax; //פקס
    $CompanyMobile = @$BusinessSettings->ContactMobile; //סלולרי
    $CompanyEmail = @$BusinessSettings->Email; //דואר אלקטרוני
    $CompanySite = @$BusinessSettings->WebSite; //כתובת אתר
    $CompanyCity = @$BusinessSettingsCity->City; //City
    $CompanyAddress = @$BusinessSettings->Street; //Street
    $CompanyAddressNum = @$BusinessSettings->Number; //מספר בית
    $CompanyPOBox = @$BusinessSettings->POBox; //תא דואר
    if (@$BusinessSettings->Zip != '0' && @$BusinessSettings->Zip != '') {
        $CompanyZip = ' ' . @$BusinessSettings->Zip;
    } else {
        $CompanyZip = '';
    } //מיקוד
    if ((@$CompanyCity != '') && (@$CompanyAddress != '')) {
        $CompanyAddressPsik = ', ';
    } //אם יש עיר להוסיף פסיק בין הרחוב לעיר
    if ((@$CompanyPOBox != '0') && (@$CompanyPOBox != '') && (@$CompanyCity != '')) {
        $CompanyZipPsik = ',';
    } //אם יש עיר להוסיף פסיק בין התא דואר למיקוד
    if ((@$CompanyPOBox != '0') && (@$CompanyPOBox != '') && (@$CompanyCity != '')) {
        $CompanyPOBoxPsik = '. תא דואר: ';
    } //אם יש עיר להוסיף פסיק בין תא דואר לעיר
    if ((@$CompanyAddress != '') && (@$CompanyAddressNum != '')) {
        $CompanyAddressSpace = ' ';
    } //אם יש מספר בית אז לעשות רווח בין המספר בית לבין הרחוב
    $CompanyFullAddress = @$CompanyAddress . '' . @$CompanyAddressSpace . '' . @$CompanyAddressNum . '' . @$CompanyAddressPsik . '' . @$CompanyCity . '' . @$CompanyPOBoxPsik . '' . @$CompanyPOBox . '' . @$CompanyZipPsik . '' . @$CompanyZip; //כתובת מלאה של העסק
    $CompanyFullAddress = $BusinessAddressGoogle->address ?? $CompanyFullAddress;   // business address from google


// [START] DOC studio header

//חלק עליון ותחתון של המסמך
    $DocHeaderHtml .= '<table id="studioInfoTable" data-studio-color="'.$TextColor.'" data-studio-bg="'.$BackgroundColor.'" cellspacing="0" width="100%" dir="rtl" style="padding:0px;margin:0px;vertical-align: top;"><tr style="padding:0px;margin:0px;vertical-align: top;">
<td width="33.33%" style="direction:rtl;vertical-align: top; ">
<table style="table-layout: auto;max-width: 200px;padding:0px;margin-top:-2px;margin-right:0px;vertical-align: top;font-size:11px;" cellspacing="0">';


    if (@$CompanyName != "") {
        $DocHeaderHtml .= '
<tr class="ClientTable">
<td class="CompanyTable" colspan="2"><strong>' . @$CompanyName . '</strong></td>
</tr>
';
    }

    if (@$CompanyNumber != "") {
        $DocHeaderHtml .= '
<tr class="ClientTable">
<td style="padding-right:0px;margin-right:0px;vertical-align: top;padding-left:5px;">' . @$CompanyKind . ':</td>
<td class="CompanyTable">' . @$CompanyNumber . '</td>
</tr>
';
    }

    if (@$CompanyFullAddress != "") {
        $DocHeaderHtml .= '
<tr class="ClientTable">
<td style="padding-right:0px;margin-right:0px;vertical-align: top;padding-left:5px;">כתובת:</td>
<td class="CompanyTable">' . @$CompanyFullAddress . '</td>
</tr>
';
    }

    if (@$CompanyPhone != "") {
        if (strlen(@$CompanyPhone) == '9') {
            $ChunkAfter = '2';
        } else {
            $ChunkAfter = '3';
        } //לבדוק כמה ספרות המספר ולהוסיף מקף בהתאם לכמות הספרות
        $DocHeaderHtml .= '
<tr class="ClientTable">
<td style="padding-right:0px;margin-right:0px;vertical-align: top;padding-left:5px;">טלפון:</td>
<td class="CompanyTable">' . substr(@$CompanyPhone, 0, $ChunkAfter) . '-' . substr(@$CompanyPhone, $ChunkAfter) . '</td>
</tr>
';
    }

    if (@$CompanyMobile != "") {
        if (strlen(@$CompanyMobile) == '9') {
            $ChunkAfter = '2';
        } else {
            $ChunkAfter = '3';
        } //לבדוק כמה ספרות המספר ולהוסיף מקף בהתאם לכמות הספרות
        $DocHeaderHtml .= '
<tr class="ClientTable">
<td style="padding-right:0px;margin-right:0px;vertical-align: top;padding-left:5px;">סלולרי:</td>
<td class="CompanyTable">' . substr(@$CompanyMobile, 0, $ChunkAfter) . '-' . substr(@$CompanyMobile, $ChunkAfter) . '</td>
</tr>
';
    }

    if (@$CompanyFax != "") {
        if (strlen(@$CompanyFax) == '9') {
            $ChunkAfter = '2';
        } else {
            $ChunkAfter = '3';
        } //לבדוק כמה ספרות המספר ולהוסיף מקף בהתאם לכמות הספרות
        $DocHeaderHtml .= '
<tr class="ClientTable">
<td style="padding-right:0px;margin-right:0px;vertical-align: top;padding-left:5px;">פקס:</td>
<td class="CompanyTable">' . substr(@$CompanyFax, 0, $ChunkAfter) . '-' . substr(@$CompanyFax, $ChunkAfter) . '</td>
</tr>
';
    }

    if (@$CompanyEmail != "") {
        $DocHeaderHtml .= '
<tr class="ClientTable">
<td style="padding-right:0px;margin-right:0px;vertical-align: top;padding-left:5px;">דוא״ל:</td>
<td class="CompanyTable">' . @$CompanyEmail . '</td>
</tr>
';
    }

    if (@$CompanySite != "") {
        $DocHeaderHtml .= '
<tr class="ClientTable">
<td style="padding-right:0px;margin-right:0px;vertical-align: top;padding-left:5px;">אתר:</td>
<td class="CompanyTable">' . @$CompanySite . '</td>
</tr>
';
    }

    $DocHeaderHtml .= '</table></td>';

//מסמך ממוחשב
    $DocHeaderHtml .= '<td width="33.33%" style="text-align: center;vertical-align: top;">
<img src="' . $PdfDigitalSignImg . '" height="80"><br>
<span style="text-align:center;">מ ס מ ך &nbsp;&nbsp;&nbsp; מ מ ו ח ש ב</span>
</td>';
//מסמך ממוחשב

    $DocHeaderHtml .= '<td width="33.33%" style="text-align: left;vertical-align: top;margin:0px;padding:0px;">';
    if (@$CompanyLogo != "") {
//לוגו העסק
        @$CompanyLogoSrc = '/office/files/' . @$CompanyLogo;
        $DocHeaderHtml .= '<img src="' . @$CompanyLogoSrc . '" style="max-height:70px;max-width:150px;margin-left:0px;padding-left:0px;" onerror="this.src=\'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+P+/HgAFhAJ/wlseKgAAAABJRU5ErkJggg==\';" >';
//לוגו העסק
    }
    $DocHeaderHtml .= '</td>';

    $DocHeaderHtml .= '</tr></table><br>';

endif;

// [END] DOC studio header
return $DocHeaderHtml;

?>
