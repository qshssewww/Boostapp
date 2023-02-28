<?php

require_once '../../app/init.php'; 

require 'mail/class.phpmailer.php';

$monthNames = Array("ינואר", "פברואר", "מרץ", "אפריל", "מאי", "יוני", "יולי", 
"אוגוסט", "ספטמבר", "אוקטובר", "נובמבר", "דצמבר");

if (!isset($_REQUEST["month"])) $_REQUEST["month"] = date("m");
if (!isset($_REQUEST["year"])) $_REQUEST["year"] = date("Y");

if (@$_REQUEST['Dates']==''){

$cMonth = $_REQUEST["month"];
$cYear = $_REQUEST["year"];
$Dates = $_REQUEST["year"].'-'.$_REQUEST["month"];
}

else {

$Dates = $_REQUEST['Dates'];
$cMonth = with(new DateTime($_REQUEST['Dates']))->format('m');
$cYear = with(new DateTime($_REQUEST['Dates']))->format('Y');	
	
}
 
$prev_year = $cYear;
$next_year = $cYear;
$prev_month = $cMonth-1;
$next_month = $cMonth+1;
 
if ($prev_month == 0 ) {
    $prev_month = 12;
    $prev_year = $cYear - 1;
}
if ($next_month == 13 ) {
    $next_month = 1;
    $next_year = $cYear + 1;
}

$StartDate = $cYear.'-'.$cMonth.'-01';
$EndDate = $cYear.'-'.$cMonth.'-'.date('t',strtotime($StartDate));


//// פרטי העסק

$CompanyInfo = DB::table('settings')->where('id' ,'=', '1')->first();

$BusinessType = $CompanyInfo->BusinessType;

$CompanyBusinessType = DB::table('businesstype')->where('id' ,'=', $BusinessType)->first();

$html = '<!--mpdf
<htmlpageheader name="myheader">
<table width="100%" cellpadding="0" cellspacing="0" dir="rtl">
<tr>
<td align="center"><img src="images/logo.jpg" width="920" height="127" /></td>
</tr>
<tr>
  <td height="5" align="center"></td>
  </tr>
<tr>
  <td align="left">
  <table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="75%" align="right"><strong style="text-align:right; ">'.$CompanyBusinessType->Type.': '.$CompanyInfo->CompanyId.'</strong></td>
    <td width="25%" align="left"><strong style="text-align:left;">תאריך: '.date('d.m.Y').'</strong></td>
  </tr>
</table>
<br>
<span style="font-size:16px; font-weight:bold;"></span>
</td>
  </tr>
</table>

</htmlpageheader>


<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->';

$html .='<div style="padding-top:90px;">
<h1 style="font-size: 20px;
  font-family: sans-serif;
  text-align:center;
  font-weight:bold;
  color:#E31E24;">כרטסת הנה"ח לחודש - '.$monthNames[$cMonth-1].' '.$cYear.'</h1>
</div>
<div style="padding-bottom:10px;"></div>
';


$html .='
<h1 style="font-size: 14px;
  font-family: sans-serif;
  text-align:right;
  color:#004566;">חשבוניות מס</h1>
<table class="table table-bordered" align="center">
  <thead>
          <tr>
            <th align="right">חשבונית מס #</th>
            <th align="right">שם הלקוח</th>
            <th align="right">תאריך</th>
			<th align="right">לפני מע"מ</th>
			<th align="right" width="50px;">מע"מ</th>
            <th align="right">כולל מע"מ</th>
          </tr>
        </thead>
<tbody>';

// Invoice List

$DocGets = DB::table('invoice')->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->get();
$DocSum = DB::table('invoice')->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->sum('Amount');
$DocSumVat = DB::table('invoice')->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->sum('VatAmount');

foreach ($DocGets as $DocGet) {

$html .='<tr>
<td valign="top" style="text-align: right;"><span contenteditable>'.$DocGet->id.'</span></td>';

if (@$DocGet->Company=='') { 

$html .='<td valign="top" style="text-align: right;"><span contenteditable>לקוח מזדמן</span></td>';	

} else { 

$html .='<td valign="top" style="text-align: right;"><span contenteditable>'.$DocGet->Company.'</span></td>';	

}

$html .='<td valign="top" style="text-align:right;"><span contenteditable>'.with(new DateTime($DocGet->UserDate))->format('d/m/Y').'</span></td>';
$html .='<td valign="top" style="text-align: right;"><span contenteditable>'.@number_format(str_replace('-', '', @$DocGet->Amount-$DocGet->VatAmount), 2).'</span></td>
<td valign="top" style="text-align: right;"><span contenteditable>'.$DocGet->VatAmount.'</span></td>
<td valign="top" style="text-align: right;"><span contenteditable>'.@number_format(str_replace('-', '', @$DocGet->Amount), 2).'</span></td>
</tr>';

}


$html .='<tr class="active">
<td valign="top" colspan="3" style="text-align: left;">סה"כ</td>
<td valign="top" style="text-align: right;"><span contenteditable>'.@number_format(str_replace('-', '', @$DocSum-@$DocSumVat), 2).'</span></td>
<td valign="top" style="text-align: right;"><span contenteditable>'.@number_format(str_replace('-', '', @$DocSumVat), 2).'</span></td>
<td valign="top" style="text-align: right;"><span contenteditable>'.@number_format(str_replace('-', '', @$DocSum), 2).'</span></td>
</tr>';


$html .='</tbody></table>';


/// חשבונית זיכוי

$html .='<h1 style="font-size: 14px;
  font-family: sans-serif;
  text-align:right;
  color:#004566;">חשבוניות זיכוי</h1>
<table class="table table-bordered">
  <thead>
          <tr>
            <th align="right">חשבונית זיכוי #</th>
            <th align="right">שם הלקוח</th>
            <th align="right">תאריך</th>
			<th align="right">לפני מע"מ</th>
			<th align="right">מע"מ</th>
            <th align="right">כולל מע"מ</th>
          </tr>
        </thead>
<tbody>';

// Invoice List

$DocGets = DB::table('inv')->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->get();
$DocSum = DB::table('inv')->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->sum('Amount');
$DocSumVat = DB::table('inv')->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->sum('VatAmount');

foreach ($DocGets as $DocGet) {

$html .='<tr>
<td valign="top" style="text-align: right;"><span contenteditable>'.$DocGet->id.'</span></td>';

if (@$DocGet->Company=='') { 

$html .='<td valign="top" style="text-align: right;"><span contenteditable>לקוח מזדמן</span></td>';	

} else { 

$html .='<td valign="top" style="text-align: right;"><span contenteditable>'.$DocGet->Company.'</span></td>';	

}	


$html .='<td valign="top" style="text-align: right;"><span contenteditable>'.with(new DateTime($DocGet->UserDate))->format('d/m/Y').'</span></td>';
$html .='
<td valign="top" style="text-align: right;"><span contenteditable>'.@number_format(str_replace('-', '', @$DocGet->Amount-$DocGet->VatAmount), 2).'</span></td>
<td valign="top" style="text-align: right;"><span contenteditable>'.$DocGet->VatAmount.'</span></td>
<td valign="top" style="text-align: right;"><span contenteditable>'.@number_format(str_replace('-', '', @$DocGet->Amount), 2).'</span></td>
</tr>';

}


$html .='<tr class="active">
<td valign="top" colspan="3" style="text-align: left;">סה"כ</td>
<td valign="top" style="text-align: right;"><span contenteditable>'.@number_format(str_replace('-', '', @$DocSum+@$DocSumVat), 2).'</span></td>
<td valign="top" style="text-align: right;"><span contenteditable>'.@number_format(str_replace('-', '', @$DocSumVat), 2).'</span></td>
<td valign="top" style="text-align: right;"><span contenteditable>'.@number_format(str_replace('-', '', @$DocSum), 2).'</span></td>
</tr>';


$html .='</tbody></table>';

/// חשבונית מס קבלה

$html .='<h1 style="font-size: 14px;
  font-family: sans-serif;
  text-align:right;
  color:#004566;">חשבוניות מס קבלה</h1>
<table class="table table-bordered">
  <thead>
          <tr>
            <th align="right">חשבונית מס קבלה #</th>
            <th align="right">שם הלקוח</th>
            <th align="right">תאריך</th>
			<th align="right">לפני מע"מ</th>
			<th align="right">מע"מ</th>
			<th align="right">ניכוי מס</th>
            <th align="right">כולל מע"מ</th>
          </tr>
        </thead>
<tbody>';

// Invoice List

$DocGets = DB::table('invoicereceipt')->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->get();
$DocSum = DB::table('invoicereceipt')->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->sum('Amount');
$DocSumVat = DB::table('invoicereceipt')->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->sum('VatAmount');
$DocSumNikoy = DB::table('invoicereceipt')->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->sum('NikoyMasAmount');

foreach ($DocGets as $DocGet) {

$html .='<tr>
<td valign="top" style="text-align: right;"><span contenteditable>'.$DocGet->id.'</span></td>';

if (@$DocGet->Company=='') { 

$html .='<td valign="top" style="text-align: right;"><span contenteditable>לקוח מזדמן</span></td>';	

} else { 

$html .='<td valign="top" style="text-align: right;"><span contenteditable>'.$DocGet->Company.'</span></td>';	

}	


$html .='<td valign="top" style="text-align: right;"><span contenteditable>'.with(new DateTime($DocGet->UserDate))->format('d/m/Y').'</span></td>';
$html .='<td valign="top" style="text-align: right;"><span contenteditable>'.@number_format(str_replace('-', '', @$DocGet->Amount-$DocGet->VatAmount), 2).'</span></td>
<td valign="top" style="text-align: right;"><span contenteditable>'.$DocGet->VatAmount.'</span></td>';
$html .='<td valign="top" style="text-align: right;"><span contenteditable>'.@number_format(str_replace('-', '', @$DocGet->NikoyMasAmount), 2).'</span></td>
<td valign="top" style="text-align: right;"><span contenteditable>'.@number_format(str_replace('-', '', @$DocGet->Amount), 2).'</span></td>
</tr>';

}


$html .='<tr class="active">
<td valign="top" colspan="3" style="text-align: left;">סה"כ</td>
<td  valign="top" style="text-align: right;"><span contenteditable>'.@number_format(str_replace('-', '', @$DocSum-@$DocSumVat), 2).'</span></td>
<td  valign="top" style="text-align: right;"><span contenteditable>'.@number_format(str_replace('-', '', @$DocSumVat), 2).'</span></td>
<td  valign="top" style="text-align: right;"><span contenteditable>'.@number_format(str_replace('-', '', @$DocSumNikoy), 2).'</span></td>
<td  valign="top" style="text-align: right;"><span contenteditable>'.@number_format(str_replace('-', '', @$DocSum), 2).'</span></td>
</tr>';


$html .='</tbody></table>';




/// קבלות

$html .='<h1 style="font-size: 14px;
  font-family: sans-serif;
  text-align:right;
  color:#004566;">קבלות</h1>
<table class="table table-bordered">
  <thead>
          <tr>
            <th align="right">קבלה #</th>
            <th align="right">שם הלקוח</th>
            <th align="right">תאריך</th>
			<th align="right">ניכוי מס</th>
            <th align="right">סכום</th>
          </tr>
        </thead>
<tbody>';

// Invoice List

$DocGets = DB::table('receipt')->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->get();
$DocSum = DB::table('receipt')->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->sum('Amount');
$DocSumNikoy = DB::table('receipt')->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->sum('NikoyMasAmount');

foreach ($DocGets as $DocGet) {

$html .='<tr>
<td valign="top" style="text-align: right;"><span contenteditable>'.$DocGet->id.'</span></td>';

if (@$DocGet->Company=='') { 

$html .='<td valign="top" style="text-align: right;"><span contenteditable>לקוח מזדמן</span></td>';	

} else { 

$html .='<td valign="top" style="text-align: right;"><span contenteditable>'.$DocGet->Company.'</span></td>';	

}


$html .='<td valign="top" style="text-align: right;"><span contenteditable>'.with(new DateTime($DocGet->UserDate))->format('d/m/Y').'</span></td>';
$html .='<td valign="top" style="text-align: right;"><span contenteditable>'.@number_format(str_replace('-', '', @$DocGet->NikoyMasAmount), 2).'</span></td>
<td valign="top" style="text-align: right;"><span contenteditable>'.@number_format(str_replace('-', '', @$DocGet->Amount), 2).'</span></td>
</tr>';

}


$html .='<tr class="active">
<td valign="top" colspan="3" style="text-align: left;">סה"כ</td>
<td valign="top" style="text-align: right;"><span contenteditable>'.@number_format(str_replace('-', '', @$DocSumNikoy), 2).'</span></td>
<td valign="top" style="text-align: right;"><span contenteditable>'.@number_format(str_replace('-', '', @$DocSum), 2).'</span></td>
</tr>';


$html .='</tbody></table>';


$FullYear = $cMonth.''.$cYear;

$filename = 'Carteset'.$FullYear.'.pdf';

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