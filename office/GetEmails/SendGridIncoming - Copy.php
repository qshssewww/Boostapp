<?php
require '../../XML/mail/class.phpmailer.php';

@$GetEmail_To = $_REQUEST['to'];
if ($GetEmail_To != '') {
$GetEmail_Subject = $_REQUEST['subject'];
$GetEmail_FromEmail = $EmailFromJson->{'from'};
$GetEmail_HowMuchFiles = $_REQUEST['attachments'];
$GetEmail_AttachmentInfo = json_decode($_REQUEST['attachment-info']);
$GetEmail_Body = $_REQUEST['html'];
$EmailFromJson = json_decode($_REQUEST['envelope']);

foreach ($GetEmail_AttachmentInfo as $name => $value) {
$path = '' . basename( $_FILES[$name]['name']);
    if(move_uploaded_file($_FILES[$name]['tmp_name'], $path)) {
      $GetEmail_F = "The file ".  basename( $_FILES[$name]['name']). " has been uploaded {$realname2ee}";
    } else{
        $dfgfdf = "There was an error uploading the file, please try again!";
    }

}

$subject = 'מייל חדש לתיבה '.$GetEmail_To.' בנושא '.$GetEmail_Subject;

$message = '
<strong>עבור</strong> '.$GetEmail_To.'<br>
<strong>מאת</strong> '.$GetEmail_FromEmail.'<br>
<strong>כמה קבצים מצורפים</strong> '.$GetEmail_HowMuchFiles.'<br>
<strong>נושא</strong> '.$GetEmail_Subject.'<br>
<strong>תוכן</strong> '.$GetEmail_Body.'<br><strong>סוף המייל</strong>';
$mail = new PHPMailer();
$mail->IsSMTP(); // enable SMTP
$mail->SMTPAuth = true; // authentication enabled
$mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for GMail
$mail->Host = "smtp.sendgrid.net";
$mail->Port = 587; // or 587
$mail->IsHTML(true);
$mail->Username = "Beesoft";
$mail->Password = "Oliver6480";
$mail->SetFrom('test@beesoft.co.il', 'beesoft');
$mail->AddAddress('mail@5656.co.il');
$mail->Subject = ($subject);
$mail->MsgHTML($message);
$mail->AltBody = ($message);
foreach ($GetEmail_AttachmentInfo as $name => $value) {
$mail->AddAttachment('C:/inetpub/247CRM/webpower.beepos.co.il/Office/GetEmails/'.$_FILES[$name]['name']);
}
if(!$mail->Send()) {
   echo "Mailer Error: " . $mail->ErrorInfo;
} else {
    echo "Message sent!";
}
}
else {echo "No Email?";}
?>