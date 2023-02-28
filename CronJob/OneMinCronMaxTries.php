<?php
$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';

require_once $_SERVER['DOCUMENT_ROOT'] .'/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/services/EmailService.php';

$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();

set_time_limit(0);
ini_set("memory_limit", "-1");

$ThisDate = date('Y-m-d');
$ThisDay = date('l');
$ThisTime = date('H:i:s');

$crons = DB::table('crons')->where('try' , '=', 3)->where('done', '=', '0')->where('sent_comp_mail', '=', null)->get();

$message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
            <meta charset="utf-8"></head><body>';

if(count($crons)){
    foreach($crons as $cron)
    {
        $message .= '<p>date: ' . date('Y-m-d' ,$cron->start_process_ts) . ' at ' . date('h:i:sa' ,$cron->start_process_ts) . '</p>
                     <p>cron failed: ' . $cron->file_name . ' ran ' . 3 . ' time</p>';
    }
    $message .='</body></html>';

    $EmailReplay = 'no-reply@boostapp.co.il';
    $EmailReplayName = 'BOOSTAPP';

    $SettingsInfo = DB::table('settings')->where('CompanyNum','=',100)->first();

    $mail = new PHPMailer();
    $mail->IsSMTP(); // enable SMTP
    $mail->SMTPAuth = true; // authentication enabled
    $mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for GMail
    $mail->Host = "smtp.sendgrid.net";
    $mail->Port = 587; // or 587
    $mail->IsHTML(true);
    $mail->Username = EmailService::USERNAME_SENDGRID;
    $mail->Password = EmailService::PASSWORD_SENDGRID;

    $mail->SetFrom($EmailReplay, $EmailReplayName);
    $mail->AddReplyTo($EmailReplay, $EmailReplayName);

    foreach($Cron->boostappEmails as $boostappEmail)
    {
        $mail->AddAddress($boostappEmail);
        $mail->Subject = ('boostapp cron failed 3 times');
        $mail->MsgHTML($message);

        if(!$mail->Send()) {
            $Results = $mail->ErrorInfo;
            $Status = '2';
        } else {
            $Status = '1';
            $Results = '';
        }
    }

    if($Status == '1'){
        DB::table('crons')->where('try' , '=', 3)->where('done', '=', '0')->update(array('sent_comp_mail' => 1));
    }
}

$Cron->end();
