<?php
header('Access-Control-Allow-Origin: *');  

require '../../XML/mail/class.phpmailer.php';



$subject = 'פעולה חדשה תועדה '.rand (456765,5764346575876);

$message = '
<strong>מקור הפעולה</strong> '.$_GET["url"].'<br>
<strong>מספר חברה</strong> '.$_GET["company_id"].'<br>
<strong>מספר קוקי</strong> '.$_GET["session_id"].'<br>
<strong>דואר אלקטרוני</strong> '.$_GET["email"].'<br>
<strong>טלפון</strong> '.$_GET["number"].'<br>
<strong>מקור הגעה</strong> '.$_GET["ref"].'<br>
<br><strong>סוף המייל</strong>';
$mail = new PHPMailer();
$mail->IsSMTP(); // enable SMTP
$mail->SMTPAuth = true; // authentication enabled
$mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for GMail
$mail->Host = "smtp.sendgrid.net";
$mail->Port = 587; // or 587
$mail->IsHTML(true);
$mail->Username = "Beesoft";
$mail->Password = "Oliver6480";
$mail->SetFrom('Actions@5656.co.il', 'ActionSoft');
$mail->AddAddress('mail@5656.co.il');
$mail->Subject = ($subject);
$mail->MsgHTML($message);
$mail->AltBody = ($message);
if(!$mail->Send()) {
   echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
}
?>