<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>PHPMailer - mail() test</title>
</head>
<body>
<?php
require '../class.phpmailer.php';

$to = 'laundryeilat@gmail.com';

$message = <<<_TEXT_
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><table width= "650" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="border:10px solid #c8e5f6;">
        <tr>
          <td align="left" valign="top"><table width="650" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #cccccc;">
            <tr>
              <td width="275" align="left" valign="middle" style="padding:30px;"><img src="http://www.beplusdo.com/email-template/images/be-dologoemail.png" alt="BePlusDo" width="189" height="57" /></td>
              <td width="255" align="right" valign="middle" style="font-family:Arial; font-size:14px; color:#555555; padding:30px;"><strong>Notification</strong><br />
                $date</td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td align="left" valign="top"><table width="650" border="0" style="padding: 30px 30px 30px 30px;" cellpadding="0">
           		  
			<tr><td style="font-family:Arial; font-size:12px;padding-bottom:15px;">
             <span style="color:#000;font-family:Arial; font-size:14px; font-weight:bold;">Hi {$_POST['email']},</span><br /><br />
			  You recently signed up for <strong>BePlusDo</strong>.<br />
			  To complete signup, <strong>click this button:</strong>
			  <table border="0" cellpadding="6" cellspacing="1" align="center"><tr><td align="center" valign="middle" bgcolor="#FFE86C" background="http://www.beplusdo.com/email-template/images/yellow_button_back.png" style="background:url(http://www.beplusdo.com/email-template/images/yellow_button_back.png) repeat-x scroll 100% 0 #FFE86C;background-color:#FFE86C;border:1px solid #E8B463;-moz-border-radius:4px;-webkit-border-radius:4px;border-radius:4px;"><div style="padding-right:10px;padding-left:10px;"><a href="http://www.beplusdo.com/signup.php?email={$_POST['email']}" style="text-decoration:none;"><span style="font-size:12px;font-family:Arial;font-weight:bold;color:#333333;white-space:nowrap;display:block;">Complete Signup</span></a></div></td></tr></table><br />
			  
			  Thank you,<br />
			  The <strong>BePlusDo</strong> Team
			  </td>
			  </tr>
            <tr>
              <td><table width="590" border="0" cellspacing="0" cellpadding="0" bgcolor="#effaff" style="border: 1px solid #c4e4f2;">
                <tr>
                  <td width="191" align="left" valign="top" style="padding:25px 0px 25px 25px"><span style="font-family:Arial; font-size:12px;"><strong>Want to become a leader?</strong></span>
                    <p style="font-family:Arial; font-size:11px; margin-bottom:0; margin-top:0.5em;">Some text here. <a href="http://www.beplusdo.com" style="color:#0093d0; text-decoration:none;">Learn how&raquo;</a></p></td>
                  <td width="211" align="left" valign="top" style="padding:25px 0px 25px 25px"><span style="font-family:Arial; font-size:12px;"><strong>Need help in balancing your life?</strong></span>
                    <p style="font-family:Arial; font-size:11px; margin-bottom:0; margin-top:0.5em;">Some text here. <a href="http://www.beplusdo.com" style="color:#0093d0; text-decoration:none;">Learn how&raquo;</a></p></td>
                  <td width="84" align="left" valign="top" style="padding:25px 25px 25px 25px"><span style="font-family:Arial; font-size:11px; margin-bottom:0.5em;"><img src="http://www.beplusdo.com/email-template/images/icon-twitter.gif" alt="Twitter" width="16" height="16" border="0" align="absmiddle" />&nbsp;&nbsp;Follow us</span>
                    <p style="font-family:Arial; font-size:11px; margin-top:6px;"><img src="http://www.beplusdo.com/email-template/images/icon-facebook.gif" alt="Facebook" width="16" height="16" border="0" align="absmiddle" />&nbsp;&nbsp;Like us</p></td>
                </tr>
              </table></td>
            </tr>
          </table></td>
        </tr>
      </table>
    <p align="center" style="font-family:Arial; font-size:11px;">&nbsp;</p></td>
  </tr>
</table>
</body>
</html>
_TEXT_;
//Create a new PHPMailer instance
$mail = new PHPMailer();
//Set who the message is to be sent from
$mail->SetFrom('no-reply@beplusdo.com', 'BePlusDo.com');
//Set an alternative reply-to address
$mail->AddReplyTo('no-reply@beplusdo.com', 'BePlusDo.com');
//Set who the message is to be sent to
$mail->AddAddress($to);
//Set the subject line
$mail->Subject = 'Active your BePlusDo account';
//Read an HTML message body from an external file, convert referenced images to embedded, convert HTML into a basic plain-text alternative body
$mail->MsgHTML($message);
//Replace the plain text body with one created manually
$mail->AltBody = 'Active your BePlusDo account';
//Attach an image file
//$mail->AddAttachment('images/phpmailer-mini.gif');

//Send the message, check for errors
if(!$mail->Send()) {
  echo "Mailer Error: " . $mail->ErrorInfo;
} else {
  echo "Message sent!";
}
?>
</body>
</html>
