<?php

require_once '../../app/init.php'; 

require 'mail/class.phpmailer.php';

$ClientId = $_REQUEST['ClientId'];

$ClientInfo = DB::table('client')->where('id' ,'=', $ClientId)->first();

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
    <td width="25%" align="left"><strong style="text-align:left;">תאריך: '.$ClientInfo->JoinDate.'</strong></td>
  </tr>
</table>
<br>
<span style="font-size:16px; font-weight:bold;"></span>
</td>
  </tr>
</table>

</htmlpageheader>

<htmlpagefooter name="myfooter">
<div dir="rtl" style=" border-bottom: 1px solid #000000; font-size: 12px; text-align: center; padding-top: 1mm; ">
'.$CompanyInfo->Street.' '.$CompanyInfo->Number.' ת.ד. '.$CompanyInfo->POBox.',  99012 | טלפון: 03-7922472 <br>
דואר אלקטרוני: info@gananot.org.il
</div>
<div dir="rtl" style="border-top: 1px solid #000000; font-size: 9pt; text-align: center; padding-top: 3mm; ">

<table class="table">
  <tbody>
    <tr>
      <td style="text-align:right;">עמוד {PAGENO} מתוך {nb}</td>
      <td style="text-align:left; font-size:11px;">הופק באמצעות BeeOFFICE V2.0</td>
    </tr>
   
  </tbody>
</table>

 
</div>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->';

$html .='<div style="padding-top:100px;">
<h1 style="font-size: 20px;
  font-family: sans-serif;
  text-align:center;
  font-weight:bold;
  ">בקשת הצטרפות לעמותה הפדגוגית<br>
<span style="font-size: 14px;">
ייפוי כוח בלתי חוזר
</span></h1>
</div>
<div style="padding-bottom:10px;"></div>
';


$html .='
<h1 style="font-size: 16px;
  font-family: sans-serif;
  text-align:right;">לכבוד<br>
הנהלת העמותה הפדגוגית - ארגון המורים</h1>';


$html .='
<span style="font-size: 14px;
  font-family: sans-serif;
  text-align:right;">
  אני הח"מ מצהיר/ה בזה כי הנני רוצה להצטרף כחבר/ה בעמותה הפדגוגית מיסודו של ארגון המורים העל-יסודיים.<br>
<br>

אינני חבר/ה ולא רוצה להיות רשום/ה בעמותה לקידום החינוך מיסודה של הסתדרות המורים.
</span>';


$html .='
<div style="padding-right:450px;">
<br>
<br>
<br>
<br>
<br>

<span style="font-size: 14px;
  font-family: sans-serif;
  text-align:right;">
בברכה,
<br>
<br>
<br>

שם: <u>'.$ClientInfo->CompanyName.'</u>
<br>
ת"ז: <u>'.$ClientInfo->CompanyId.'</u>
<br>
תאריך: <u>'.$ClientInfo->JoinDate.'</u>

</span></div>';





$html .='';

$filename = 'Forms1.pdf';

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