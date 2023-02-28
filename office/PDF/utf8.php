<?php
/**
 * HTML2PDF Librairy - example
 *
 * HTML => PDF convertor
 * distributed under the LGPL License
 *
 * @author      Laurent MINGUET <webmaster@html2pdf.fr>
 *
 * isset($_GET['vuehtml']) is not mandatory
 * it allow to display the result in the HTML format
 */

    require_once(dirname(__FILE__).'/new/html2pdf.class.php');

    // get the HTML
   $content = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<style type="text/css">
body {
	background-image: url(pdf/images/bg.jpg);
	background-repeat: no-repeat;
}
</style>
<link href="bootstrap.min.css" rel="stylesheet">
</head>
<body>
<!--mpdf
<htmlpageheader name="myheader">
<table width="100%" cellpadding="0" cellspacing="0" dir="rtl">
<tr>
<td align="center"><img src="images/mediapic-logo.jpg" width="920" height="127" /></td>
</tr>
<tr>
  <td height="5" align="center"></td>
  </tr>
<tr>
  <td align="left"><strong style="font-size:12px;">
  עוסק מורשה: 068600576<br>
תאריך: '.date('d/m/Y').'</strong><br>
<span style="font-size:16px; font-weight:bold;">מקור</span>
</td>
  </tr>
</table>

</htmlpageheader>

<htmlpagefooter name="myfooter">
<div dir="rtl" style=" border-bottom: 1px solid #000000; font-size: 12px; text-align: center; padding-top: 1mm; ">
קניון העיר 5, ת.ד. 4094 אילת | 88000 טלפון: , 08-9109092 פקס: | 08-6331210 אפי: , 057-5885668 לורי: 054-8000071
</div>
<div dir="rtl" style="border-top: 1px solid #000000; font-size: 9pt; text-align: center; padding-top: 3mm; ">
עמוד {PAGENO} מתוך {nb}
</div>
</htmlpagefooter>

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->

<div style="padding-top:120px;">
<h1 style="font-size: 20px;
  text-align:center;
  color:#E31E24;">חשבונית מס מספר - '.$id.'</h1>
</div>
<div style="padding-bottom:10px;"></div>

<table width="720" cellpadding="0" cellspacing="0" dir="rtl">
<tr>
  <td width="250" height="120" align="right" valign="top">
  <table width="100%" border="0" cellspacing="0" cellpadding="6">
  <tr>
    <td><strong>לכבוד</strong></td>
  </tr>
</table>

  <table width="98%" border="0" align="left" cellpadding="2" cellspacing="2" class="table">
    <tr class="success">
      <td align="right" class="success"><span style="color:#09F; font-weight:bold; font-size:13px;">בי.פלוס.דו - טכנולוגיות אימון בע"מ</span>
        </td>
    </tr>
    <tr class="success">
      <td align="right"><span style="font-size:12px;">מס. עוסק:</span> <span style="color:#333; font-weight:bold; font-size:12px;">514823798</span></td>
    </tr>
    <tr>
      <td align="right"><span style="font-size:12px;">טלפון:</span> <span style="color:#333; font-weight:bold; font-size:12px;">057-7322268</span></td>
    </tr>
    <tr>
      <td align="right"><span style="font-size:12px;">כתובת:</span> <span style="color:#333; font-weight:bold; font-size:12px;">נחל סירה 4, מצפה רמון</span></td>
    </tr>
</table>

  </td>
  <td width="468" align="center"><img src="images/ads.jpg" width="498" height="154"></td>
</tr>
</table>

<table width="100%" border="0" cellspacing="3" cellpadding="3" dir="rtl">
  <tr>
    <th width="3%" bgcolor="#b6b6b6" scope="col">#</th>
    <th width="73%" align="right" bgcolor="#b6b6b6" scope="col">תיאור פריט</th>
    <th width="12%" bgcolor="#b6b6b6" scope="col">הזמנה מספר</th>
    <th width="12%" bgcolor="#b6b6b6" scope="col">סה&quot;כ</th>
  </tr>
  <tr>
    <th scope="col">&nbsp;</th>
    <th scope="col">&nbsp;</th>
    <th scope="col">&nbsp;</th>
    <th scope="col">&nbsp;</th>
  </tr>
  <tr>
    <th scope="col">&nbsp;</th>
    <th scope="col">&nbsp;</th>
    <th scope="col">&nbsp;</th>
    <th scope="col">&nbsp;</th>
  </tr>
  <tr>
    <th scope="col">&nbsp;</th>
    <th scope="col">&nbsp;</th>
    <th scope="col">&nbsp;</th>
    <th scope="col">&nbsp;</th>
  </tr>
  <tr>
    <th scope="col">&nbsp;</th>
    <th scope="col">&nbsp;</th>
    <th scope="col">&nbsp;</th>
    <th scope="col">&nbsp;</th>
  </tr>
</table>

	  </body>
</html>

';


    // convert to PDF
    try
    {
        $html2pdf = new HTML2PDF('P', 'A4', 'he');
        $html2pdf->pdf->SetDisplayMode('real');
        $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
        $html2pdf->Output('myname.pdf');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
