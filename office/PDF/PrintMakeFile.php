<?php

require_once '../../app/init.php'; 

$StartDate = $_REQUEST["StartDate"];
$EndDate = $_REQUEST["EndDate"];
$Act = $_REQUEST["Act"];



$StartDateView = with(new DateTime($StartDate))->format('d/m/Y');
$EndDateView = with(new DateTime($EndDate))->format('d/m/Y');

	
$SettingsInfo = DB::table('settings')->where('id','=','1')->first();  

$html = '<!--mpdf
<htmlpageheader name="myheader">
</htmlpageheader>


<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->';

// Invoice List
//// C100 סך רשומות מסוג כותרת מסמך
$DocsInfoC100 = DB::table('docs')->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->count();
//// D110 סך רשומות מסוג פרטי מסמך
$DocsInfoD110 = DB::table('docslist')->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->count();
//// D120 סך רשומות מסוג פרטי קבלה
$DocsInfoD120 = DB::table('docs_payment')->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->count();

$TotalResomot = $DocsInfoC100+$DocsInfoD110+$DocsInfoD120+2;	  

/// פתיחת תקיות
$DirName = 'OPENFRMT'; // שם הספרייה בשרת

$IdBiz = mb_substr($SettingsInfo->CompanyId, 0, 8); // מספר עוסק מורשה או ח.פ. ללא ספרת ביקרות

/// נתונים משתנים
$Year = date('y'); /// שנה דו ספרתי YY לפי תאריך הנפקת הקובץ
$DateTime = date('mdHi'); /// MMDDhhmm
	  
$PathDir = $DirName.'/'.$IdBiz.'.'.$Year.'/'.$DateTime;	  	

if ($Act=='1'){
$html .= '
<div class="row" align="center" dir="rtl">
<div class="col-md-12 col-sm-12">
	<span>הפקת קבצים במבנה אחיד עבור:</span>	
	
<table class="table borderless" style="font-size:12px; font-weight:bold; width: 70%" dir="rtl">
<tbody>
	<tr>
	<td>מס עוסק מורשה / ח.פ : </td>
	<td style="text-align: right; width: 70%;">'.$SettingsInfo->CompanyId.'</td>	
	</tr>

	<tr>
	<td>שם בית העסק : </td>
	<td style="text-align: right; width: 70%;">'.$SettingsInfo->CompanyName.'</td>	
	</tr>	
	
</tbody>
</table>
	
<span>** ביצוע ממשק פתוח הסתיים בהצלחה **</span>	
	
<table class="table borderless" style="font-size:12px; font-weight:bold; width: 70%" dir="rtl">
<tbody>
	<tr>
	<td>הנתונים נשמרו בנתיב : </td>
	<td colspan="3" style="text-align: right; width: 40%; text-align: left; direction: ltr;">'.$PathDir.'</td>	
	</tr>

	<tr>
	<td>טווח תאריכים  מתאריך : </td>	
	<td style="text-align: right; width: 20%;">'.$StartDateView.'</td>	
	<td>ועד תאריך : </td>	
	<td style="text-align: right; width: 20%;">'.$EndDateView.'</td>		
	</tr>	
	
</tbody>
</table>	
	
	
</div>
</div>	

';

$html .= '<div class="row" align="center" dir="rtl">
<div class="col-md-12 col-sm-12">
	
<span>פירוט סך סוגי הרשומות בקובץ BKMVDATA.TXT :</span>	
	
<table class="table table-hover" style="font-size:12px; font-weight:bold; width: 70%" dir="rtl">
  <thead>
          <tr class="bg-dark text-white">
            <th style="text-align:right;" width="20%">קוד רשומה</th>
            <th style="text-align:right;" width="60%">תיאור רשומה</th>
            <th style="text-align:right;" width="20%">סך רשומות</th>
          </tr>
        </thead>
<tbody>


	<tr>
		<td>A100</td>
		<td>רשומת פתיחה</td>
		<td style="text-align: left;">1</td>
	</tr>';	
 
	if ($DocsInfoC100<='0'){} else { 

$html .= '<tr>
		<td>C100</td>
		<td>כותרת מסמך</td>
		<td style="text-align: left;">'.$DocsInfoC100.'</td>
	</tr>';	
	 } 
	if ($DocsInfoD110<='0'){} else { 
	
$html .= '	<tr>
		<td>D110</td>
		<td>פרטי מסמך</td>
		<td style="text-align: left;">'.$DocsInfoD110.'</td>
	</tr>';	
	} 
	if ($DocsInfoD120<='0'){} else { 

$html .= '	<tr>
		<td>D120</td>
		<td>פרטי קבלות</td>
		<td style="text-align: left;">'.$DocsInfoD120.'</td>
	</tr>';	
	 } 
$html .= '	<tr>
		<td>Z900</td>
		<td>רשומת סגירה</td>
		<td style="text-align: left;">1</td>
	</tr>	
	
	
	
</tbody>
	
<tfoot>

	<tr>
		<td></td>
		<td>סה"כ : </td>
		<td style="text-align: left;">'.$TotalResomot.'</td>
	</tr>	
	
</tfoot>	
	
</table>	  
	
	
	
	
	
	
<table class="table borderless" style="font-size:12px; font-weight:bold; width: 70%" dir="rtl">
<tbody>
	<tr>
	<td>הנתונים הופקו באמצעות תוכנה : </td>
	<td style="text-align: right; width: 20%;">247SOFT</td>	
	<td>מספר תעודת הרישום : </td>	
	<td style="text-align: right; width: 20%;">000000000</td>		
	</tr>
	
	<tr>
	<td colspan="4">בתאריך : '.date('d/m/Y').' בשעה : '.date('H:i').'</td>				
	</tr>	
	
</tbody>
</table>		
		
</div>
	</div>	';

}

else {
	
$html .= '
<div class="row" align="center" dir="rtl">
<div class="col-md-12 col-sm-12">
	<span>הפקת קבצים במבנה אחיד עבור:</span>	
	
<table class="table borderless" style="font-size:12px; font-weight:bold; width: 70%" dir="rtl">
<tbody>
	<tr>
	<td>מס עוסק מורשה / ח.פ : </td>
	<td style="text-align: right; width: 70%;">'.$SettingsInfo->CompanyId.'</td>	
	</tr>

	<tr>
	<td>שם בית העסק : </td>
	<td style="text-align: right; width: 70%;">'.$SettingsInfo->CompanyName.'</td>	
	</tr>	
	
</tbody>
</table>

<table class="table borderless" style="font-size:12px; font-weight:bold; width: 70%" dir="rtl">
<tbody>
	<tr>
	<td>טווח תאריכים  מתאריך : </td>	
	<td style="text-align: right; width: 20%;">'.$StartDateView.'</td>	
	<td>ועד תאריך : </td>	
	<td style="text-align: right; width: 20%;">'.$EndDateView.'</td>		
	</tr>	
	
</tbody>
</table>	
	
	
</div>
</div>	

';	
	

$html .= '<div class="row" align="center" dir="rtl">
<div class="col-md-12 col-sm-12">
		
<table class="table table-hover table-bordered" style="font-size:12px; font-weight:bold; width: 70%" dir="rtl">
  <thead>
          <tr class="bg-dark text-white">
            <th style="text-align:right;" width="20%">מספר מסמך</th>
            <th style="text-align:right;" width="30%">סוג מסמך</th>
            <th style="text-align:right;" width="20%">סה"כ כמותי</th>
			<th style="text-align:right;" width="30%">סה"כ כספי (בש"ח)</th>  
          </tr>
        </thead>
<tbody>';



									  
	$DocsTables = DB::table('docstable')->where('Misim', '=' ,'1')->orderBy('TypeHeader', 'ASC')->get();
									  
	foreach ($DocsTables as $DocsTable) {
		
	$DocsCount = DB::table('docs')->where('TypeHeader', '=', $DocsTable->TypeHeader)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->count();
	$DocsSum = DB::table('docs')->where('TypeHeader', '=', $DocsTable->TypeHeader)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->sum('Amount');	

	if ($DocsSum==''){ $Sum = '0'; } else { $Sum = number_format($DocsSum, 2); }	
		
		
$html .= '	<tr>
		<td>'.$DocsTable->TypeHeader.'</td>
		<td>'.$DocsTable->TypeTitleSingle.'</td>
		<td>'.$DocsCount.'</td>
		<td>'.$Sum.'</td>
	</tr>';	
} 

$html .= '</tbody>
		
</table>	  
	
	
	
	
	
	
<table class="table borderless" style="font-size:12px; font-weight:bold; width: 70%" dir="rtl">
<tbody>
	<tr>
	<td>הנתונים הופקו באמצעות תוכנה : </td>
	<td style="text-align: right; width: 20%;">247SOFT</td>	
	<td>מספר תעודת הרישום : </td>	
	<td style="text-align: right; width: 20%;">000000000</td>		
	</tr>
	
	<tr>
	<td colspan="4">בתאריך : '.date('d/m/Y').' בשעה : '.date('H:i').'</td>				
	</tr>	
	
</tbody>
</table>		
		
	
</div>
	</div';
	
	
}




$FullYear = date('Ymd');

$filename = 'MakeFile'.$FullYear.'.pdf';

//==============================================================
//==============================================================
//==============================================================

include("mpdf/mpdf.php");

$mpdf=new mPDF('win-1252','A4','','',20,15,30,25,10,10); 
$mpdf->SetDirectionality('rtl');
 
$mpdf->SetDisplayMode('fullpage');
 
$mpdf->list_indent_first_level = 0;  // 1 or 0 - whether to indent the first level of a list

$stylesheet = file_get_contents('CSS/bootstrap.min.css');
$mpdf->WriteHTML($stylesheet,1);


$mpdf->WriteHTML($html);
$mpdf->Output($filename, 'I');

// You can now optionally also send it to the browser

exit;

//==============================================================
//==============================================================
//==============================================================


?>