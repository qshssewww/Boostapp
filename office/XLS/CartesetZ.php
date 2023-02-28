<?php
$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';

require_once $_SERVER['DOCUMENT_ROOT'] .'/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/mail/class.phpmailer.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Client.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/DocsPayments.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/services/EmailService.php';

$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();

set_time_limit(0);

/** Error reporting */
error_reporting(E_ALL);

/** Include path **/
ini_set('include_path', ini_get('include_path').';../Classes/');

/** PHPExcel */
//include 'PHPExcel.php';
require $_SERVER['DOCUMENT_ROOT'] .'/office/XLS/PHPExcel.php';

/** PHPExcel_Writer_Excel2007 */
//include 'PHPExcel/Writer/Excel2007.php';
require $_SERVER['DOCUMENT_ROOT'] .'/office/XLS/PHPExcel/Writer/Excel2007.php';

try {

    $BusinessSettings = DB::table('settings')->where('CpaEmail', '!=', '')->get();

    foreach ($BusinessSettings as $BusinessSetting) {

        $CompanyNum = $BusinessSetting->CompanyNum;

        $CpaEmail = $BusinessSetting->CpaEmail;
        $CpaEmailCopy = $BusinessSetting->CpaEmailCopy;
        $UsernameSendGrid = EmailService::USERNAME_SENDGRID;
        $PasswordSendGrid = EmailService::PASSWORD_SENDGRID;


// Create new PHPExcel object
        echo date('H:i:s') . " Create new PHPExcel object\n";
        $objPHPExcel = new PHPExcel();

// Set properties
        echo date('H:i:s') . " Set properties\n";
        $objPHPExcel->getProperties()->setCreator("247Soft");
        $objPHPExcel->getProperties()->setLastModifiedBy("247Soft");
        $objPHPExcel->getProperties()->setTitle("הכנסות חודשיות");
        $objPHPExcel->getProperties()->setSubject("הכנסות חודשיות");
        $objPHPExcel->getProperties()->setDescription("הכנסות חודשיות");


        $StartDate = date("Y-m-d", strtotime("first day of previous month"));
        $EndDate = date("Y-m-d", strtotime("last day of previous month"));


        $TotalPreAmount = '0';
        $TotalVatAmount = '0';
        $TotalAmount = '0';

        $Eshcol = '1';
        $RowNum = '0';

        $AddRowNum = '1';


// Add some data
        $FileDates = date('d/m/y');
        $objPHPExcel->setActiveSheetIndex(0);

        $DocsTables = DB::table('docstable')->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->get();

//לופ לסוגי המסמכים
        foreach ($DocsTables as $DocsTable) {

            $DocGetsCountThisDates = DB::table('docs')->where('CompanyNum', '=', $CompanyNum)->where('TypeDoc', '=', $DocsTable->id)->whereBetween('UserDate', array($StartDate, $EndDate))->count();

            $AddRowNumNew = '0';

            if ($DocGetsCountThisDates != '0') {

                $RowNum += 1;
                $AddRowNumNew += 1;
                $objPHPExcel->getActiveSheet()->getStyle("A1:K1")->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $RowNum, 'סוג מסמך');
                $objPHPExcel->getActiveSheet()->SetCellValue('B' . $RowNum, 'מספר מסמך');
                $objPHPExcel->getActiveSheet()->SetCellValue('C' . $RowNum, 'שם לקוח');
                $objPHPExcel->getActiveSheet()->SetCellValue('D' . $RowNum, 'ת.ז');
                $objPHPExcel->getActiveSheet()->SetCellValue('E' . $RowNum, 'תאריך ערך');
                $objPHPExcel->getActiveSheet()->SetCellValue('F' . $RowNum, 'תאריך מסמך');
                $objPHPExcel->getActiveSheet()->SetCellValue('G' . $RowNum, 'סה"כ לפני מע"מ');
                $objPHPExcel->getActiveSheet()->SetCellValue('H' . $RowNum, 'מע"מ');
                $objPHPExcel->getActiveSheet()->SetCellValue('I' . $RowNum, 'סה"כ מע"מ');
                $objPHPExcel->getActiveSheet()->SetCellValue('J' . $RowNum, 'סה"כ כולל מע"מ');
                $objPHPExcel->getActiveSheet()->SetCellValue('K' . $RowNum, 'אמצעי תשלום');


                $DocGets = DB::table('docs')->where('CompanyNum', '=', $CompanyNum)->where('TypeDoc', '=', $DocsTable->id)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->get();


                $TotalVatAmount = DB::table('docs')->where('CompanyNum', '=', $CompanyNum)->where('TypeDoc', '=', $DocsTable->id)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->sum('VatAmount');
                $TotalAmount = DB::table('docs')->where('CompanyNum', '=', $CompanyNum)->where('TypeDoc', '=', $DocsTable->id)->whereBetween('UserDate', array($StartDate, $EndDate))->orderBy('id', 'ASC')->sum('Amount');

                $TotalPreAmount = $TotalAmount - $TotalVatAmount;

                foreach ($DocGets as $DocGet) {


                    $PreVat = $DocGet->Amount - $DocGet->VatAmount;

                    $RowNum += 1;
                    $AddRowNumNew += 1;

                    if ($DocGet->Refound == '1' && $DocGet->RefAction == '0' && $DocGet->TypeHeader == '400') {
                        $Refound = 'החזר';
                    } else {
                        $Refound = '';
                    }

                    $clientInfo = new Client($DocGet->ClientId);
                    $docsPayment = new DocsPayment();

                    $paymentDoc = $docsPayment->getByDocsId($DocGet->ClientId, $DocGet->id);
                    $paymentTypeText = $paymentDoc ? $docsPayment->getPaymentTypeText($paymentDoc->TypePayment) : '';

                    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $RowNum, $DocsTable->TypeTitleSingle . ' ' . $Refound);
                    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $RowNum, $DocGet->TypeNumber);
                    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $RowNum, $DocGet->Company);
                    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $RowNum, $clientInfo->CompanyId);
                    $objPHPExcel->getActiveSheet()->SetCellValue('E' . $RowNum, with(new DateTime($DocGet->Dates))->format('d/m/Y'));
                    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $RowNum, with(new DateTime($DocGet->UserDate))->format('d/m/Y'));
                    $objPHPExcel->getActiveSheet()->SetCellValue('G' . $RowNum, $PreVat);
                    $objPHPExcel->getActiveSheet()->SetCellValue('H' . $RowNum, $DocGet->Vat . '%');
                    $objPHPExcel->getActiveSheet()->SetCellValue('I' . $RowNum, $DocGet->VatAmount);
                    $objPHPExcel->getActiveSheet()->SetCellValue('J' . $RowNum, $DocGet->Amount);
                    $objPHPExcel->getActiveSheet()->SetCellValue('K' . $RowNum, $paymentTypeText);

                }

                $RowNum += 1;
                $AddRowNumNew += 1;

                $objPHPExcel->getActiveSheet()->getStyle("A$RowNum:K$RowNum")->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $RowNum, '');
                $objPHPExcel->getActiveSheet()->SetCellValue('B' . $RowNum, '');
                $objPHPExcel->getActiveSheet()->SetCellValue('C' . $RowNum, '');
                $objPHPExcel->getActiveSheet()->SetCellValue('D' . $RowNum, '');
                $objPHPExcel->getActiveSheet()->SetCellValue('E' . $RowNum, '');
                $objPHPExcel->getActiveSheet()->SetCellValue('F' . $RowNum, '');
                $objPHPExcel->getActiveSheet()->SetCellValue('G' . $RowNum, $TotalPreAmount);
                $objPHPExcel->getActiveSheet()->SetCellValue('H' . $RowNum, '');
                $objPHPExcel->getActiveSheet()->SetCellValue('I' . $RowNum, $TotalVatAmount);
                $objPHPExcel->getActiveSheet()->SetCellValue('J' . $RowNum, $TotalAmount);
                $objPHPExcel->getActiveSheet()->SetCellValue('K' . $RowNum, '');


            }


            ++$Eshcol;

        }


// Rename sheet
        echo date('H:i:s') . " Rename sheet\n";
        $objPHPExcel->getActiveSheet()->setTitle('247Soft');

// Save Excel 2007 file
        $FileDate = htmlentities(str_replace('/', '', str_replace(' ', '-', $BusinessSetting->CompanyName))) . '-' . date('d-m-Y');
        $FileDate = md5($FileDate);
        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
        $objWriter->save($_SERVER['DOCUMENT_ROOT'] . '/office/XLS/Archive/' . $FileDate . '.xlsx', __FILE__);

// Echo done
        echo date('H:i:s') . " Done writing file.\r\n";


//////////// שליחת הקובץ במייל

        $SubjectTrue = htmlentities($BusinessSetting->CompanyName) . ' כרטסת הנהלת חשבונות עבור התאריכים ' . $StartDate . ' - ' . $EndDate;


        $message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" dir="rtl">
  <tr>
    <td><table width= "650" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="border:10px solid #e1e1e1;">
        <tr>
          <td align="left" valign="top"><table width="650" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #cccccc;">
            <tr>
              <td width="275" align="right" valign="middle" style="padding:30px;"><img src="' . App::url('assets/img/LogoMail.png') . '" alt="Boostapp" title="Boostapp" width="180" height="63" /></td>
              <td width="255" align="left" valign="middle" style="font-family:Arial; font-size:14px; color:#555555; padding:30px;"><strong>הודעת מערכת</strong><br />
                ' . date('d/m/Y') . '</td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td align="left" valign="top"><table width="650" border="0" style="padding: 30px 30px 30px 30px;" cellpadding="0">
           		  
			<tr><td style="font-family:Arial; font-size:12px;padding-bottom:15px;">
            
			 מצ"ב כרטסת הנהלת חשבונות עבור התאריכים:<br>
            ' . $StartDate . ' - ' . $EndDate . '<br>
<br>


בברכה,
' . htmlentities($BusinessSetting->CompanyName) . '
			 
			  </td>
			  </tr>
          
          </table></td>
        </tr>
      </table>
    <p align="center" style="font-family:Arial; font-size:11px;">&nbsp;</p></td>
  </tr>
</table>

</body>
</html>';


        $DocTotalAmount = DB::table('docs')->where('CompanyNum', '=', $CompanyNum)->whereBetween('UserDate', array($StartDate, $EndDate))->sum('Amount');

        if ($DocTotalAmount != '') {

            $mail = new PHPMailer();

            $mail->IsSMTP(); // enable SMTP
            $mail->SMTPAuth = true; // authentication enabled
            $mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for GMail
            $mail->Host = "smtp.sendgrid.net";
            $mail->Port = 587; // or 587
            $mail->IsHTML(true);
            $mail->Username = $UsernameSendGrid;
            $mail->Password = $PasswordSendGrid;

//Set who the message is to be sent from
            $mail->SetFrom('no-reply@boostapp.co.il', 'BOOSTAPP');
//Set an alternative reply-to address
            $mail->AddReplyTo('no-reply@boostapp.co.il', 'BOOSTAPP');
//Set who the message is to be sent to

//Set who the message is to be sent to 
            $mail->AddAddress($CpaEmail);
            $mail->AddCC($CpaEmailCopy);

//Set the subject line
            $mail->Subject = ($SubjectTrue);

//Read an HTML message body from an external file, convert referenced images to embedded, convert HTML into a basic plain-text alternative body
            $mail->MsgHTML($message);
//Replace the plain text body with one created manually
//$mail->AltBody = ($message);
            $mail->AddAttachment($_SERVER['DOCUMENT_ROOT'] . '/office/XLS/Archive/' . $FileDate . '.xlsx', $FileDate . '.xlsx');

//Send the message, check for errors
            if (!$mail->Send()) {
                $Results = $mail->ErrorInfo;
                $Status = '2';
            } else {
                $Status = '1';
                $Results = '';
            }


        }


    }

    $Cron->end();
    echo " ---------------  end script at ".date('H:i:s')." ------------------------\n";
}
catch (Exception $e){
    $arr = array(
        "line" => $e->getLine(),
        "message" => $e->getMessage(),
        "file_path" => $e->getFile(),
        "trace" => $e->getTraceAsString()
    );
    if(isset($BusinessSetting)){
        $util = new Utils();
        $arr["data"] = json_encode($util->createArrayFromObj($BusinessSetting),JSON_UNESCAPED_UNICODE);
    }
    $Cron->cronLog($arr);
}
