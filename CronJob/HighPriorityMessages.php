<?php
$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] .'/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/mail/class.phpmailer.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/FirebaseToken.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/services/LoggerService.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/services/EmailService.php';

$filename = basename(__FILE__, '.php');

$Cron = new CronManager($filename);

$id = $Cron->start();

    define('API_ACCESS_KEY', 'AAAAfalH6QE:APA91bH2R0r1DpGic-YvizEQMbGCjIIJthZMpg7zs1U3a0PQFZx_ogmZHv5hyy89gFNEmcqkBJrSVWOkI_7ZfEeSkUDSzDXHEURYqFOhuIENY4wCsuE26ypBmolz9k_c8XJ42og-m0td');



    ini_set('max_execution_time', 0);

    set_time_limit(0);

    ini_set("memory_limit", "-1");



        $limit_messages = 300;

        $limit_iteration = 60;

try {

    $SendMessages = DB::table('appnotification')
        ->whereNull('workerStatus')
        ->where('Status', '=', '0')
        ->where('Type', '!=', '3')
        ->where('priority', '=', '1')
        ->where(function ($q) {
            $q->where('Date', '<', date('Y-m-d'))->where('Date', '>=', date('Y-m-d', strtotime("-2 days")))
                ->Orwhere('Date', '=', date('Y-m-d'))->where('Time', '<=', date('H:i:s'));
        })
        ->limit($limit_messages)->get();


    $count_messages = count($SendMessages);


    if ($limit_messages > $count_messages) {

        $iterations = ceil(count($SendMessages) / $limit_iteration);

    } else {

        $iterations = 5;

    }

    for ($i = 0; $i < $iterations; $i++) {

//        $cmd = 'php ' . $_SERVER['DOCUMENT_ROOT'] . '/CronJob/SendMessages.php ' . $now . ' 60 ' . ($i * 60);


        $now = strtotime("now");


        $argv[1] = $now;

        $argv[2] = $limit_iteration;

        $argv[3] = $i * $limit_iteration;


        //ini_set("register_argc_argv", "1");


        $ThisDate = date('Y-m-d');

        $ThisTime = date('H:i:s');


        $PhoneReplay = 'boost';

        $EmailReplay = 'no-reply@boostapp.co.il';

        $EmailReplayName = 'boost';

        $EmailsLogo = 'https://login.boostapp.co.il/assets/img/LogoMail.png';


        $SendMessages_splice = DB::table('appnotification')
            ->whereNull('workerStatus')
            ->where('Status', '=', '0')
            ->where('Type', '!=', '3')
            ->where('priority', '=', '1')
            ->where(function ($q) {
                $q->where('Date', '<', date('Y-m-d'))->where('Date', '>=', date('Y-m-d', strtotime("-2 days")))
                    ->Orwhere('Date', '=', date('Y-m-d'))->where('Time', '<=', date('H:i:s'));
            })
            ->offset($argv[3])->limit($argv[2])->get();


        $update = array();

        foreach ($SendMessages_splice as $SendMessage) {

            $update[] = $SendMessage->id;

        }


        DB::table('appnotification')
            ->whereIn('id', $update)
            ->update(array('workerStart' => $now, 'workerStatus' => 1));


        //echo "We are doing " . COUNT($update) . " from db \n";

        foreach ($SendMessages_splice as $SendMessage) {

            if ($SendMessage->workerStatus != null && ($SendMessage->workerStatus === '2' || $SendMessage->workerStatus === '1')) continue;

            $OpenTables = DB::table('client')->leftJoin('settings', function ($join) {
                $join->on('client.CompanyNum', '=', 'settings.CompanyNum');
            })->leftjoin('boostapplogin.studio', function ($join) {
                $join->on("client.id", "=", "boostapplogin.studio.ClientId")->on('client.CompanyNum', '=', 'boostapplogin.studio.CompanyNum');
            })->leftjoin('boostapplogin.users', 'boostapplogin.studio.UserId', '=', 'boostapplogin.users.id')
                ->join('appsettings', 'appsettings.CompanyNum', '=', 'client.CompanyNum')
                ->select('client.*', 'settings.id as settingsId', 'settings.API_ACCESS_KEY as API_ACCESS_KEY', 'settings.AppName as AppName'
                    , 'settings.Username019 as Username019', 'settings.Password019 as Password019', 'settings.UsernameSendGrid as UsernameSendGrid', 'settings.PasswordSendGrid as PasswordSendGrid'
                    , 'settings.EmailCRM as EmailCRM', 'settings.PhoneSMSTitle as PhoneSMSTitle', 'settings.PhoneSMS as PhoneSMS',
                    'settings.Memotag as Memotag', 'settings.DocsCompanyLogo as DocsCompanyLogo', 'settings.SMSPrice as SMSPrice', 'settings.Email as settingsEmail', 'appsettings.SendSMS as SendSMS', "boostapplogin.studio.tokenFirebase as studioToken", "boostapplogin.studio.OS as studioOS", "boostapplogin.users.tokenFirebase as usersToken", "boostapplogin.users.OS as usersOS")
                ->where('client.CompanyNum', '!=', '569121')->where('client.CompanyNum', '=', $SendMessage->CompanyNum)->where('client.id', '=', $SendMessage->ClientId)->where('settings.Status', '=', '0')->first();


            if (!empty($OpenTables->settingsId)) {

                $CountTotalLetters = '1'; // חישוב אורך הודעת סמס

                $PhoneReplay = 'boost'; // שם החברה בשליחת הודעת סמס

                $EmailReplay = 'no-reply@boostapp.co.il';

                $EmailReplayName = 'boost';

                $EmailsLogo = 'https://login.boostapp.co.il/assets/img/LogoMail.png';


                ///  בדיקת שעה שליחת ההתראה

                if (($SendMessage->Date == $ThisDate && $SendMessage->Time <= $ThisTime) || $SendMessage->Date < $ThisDate) {


                    DB::table('appnotification')
                        ->where('id', $SendMessage->id)
                        ->where('CompanyNum', $SendMessage->CompanyNum)
                        ->update(array('Status' => '3'));


                    $CompanyNum = $SendMessage->CompanyNum;

                    $APIACCESSKEY = $OpenTables->API_ACCESS_KEY;
                    $API_ACCESS_KEY = FirebaseToken::getTokenByProject($APIACCESSKEY);

                    $AppName = $OpenTables->AppName;

                    $SendSMS = $OpenTables->SendSMS;

                    $Username019 = $OpenTables->Username019;

                    $Password019 = $OpenTables->Password019;

                    $UsernameSendGrid = EmailService::USERNAME_SENDGRID;

                    $PasswordSendGrid = EmailService::PASSWORD_SENDGRID;


                    if ($OpenTables->EmailCRM != '') {

                        $EmailReplay = $OpenTables->EmailCRM;

                        $EmailReplayName = $OpenTables->EmailCRM;

                    }


                    if ($OpenTables->PhoneSMSTitle != '') {

                        $PhoneReplay = $OpenTables->PhoneSMSTitle;

                    }


                    if ($OpenTables->PhoneSMS != '') {

                        $PhoneReplay = $OpenTables->PhoneSMS;

                    }

                    $TrueClientId = $SendMessage->ClientId;

                    if (($OpenTables->Memotag == '1' && !empty($OpenTables->DocsCompanyLogo)) || $OpenTables->CompanyNum == '274835') {

                        $EmailsLogo = 'https://login.boostapp.co.il/office/files/' . $OpenTables->DocsCompanyLogo;

                    }

                    //// פרטי לקוח

                    //$OpenTables->B::table('client')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $TrueClientId)->first();

                    $MobileTrue = @$OpenTables->ContactMobile;

                    $EmailTrue = @$OpenTables->Email;

                    if ($OpenTables->parentClientId != 0) {
                        $getParent = DB::table('client')->where('id', $OpenTables->parentClientId)->where('CompanyNum', '=', $CompanyNum)->first();
                        if (!empty($getParent)) {
                            $MobileTrue = !empty($getParent->ContactMobile) ? $getParent->ContactMobile : $MobileTrue;
                            $EmailTrue = !empty($getParent->Email) ? $getParent->Email : $EmailTrue;
                        }
                    }

                    $display_name = @$OpenTables->CompanyName;

                    $GetSMS = @$OpenTables->GetSMS;

                    $GetEmail = @$OpenTables->GetEmail;



                    $unsubscribeText = '"<p>'.lang('remove_from_mailing').' <a href="https://1ba.co/r/' . $OpenTables->id . '/sms">'.lang('click_here').'</a></p>"';

                    $ContentTrue = $SendMessage->Text;

                    //$ContentTrue = $unsubscribeText;

                    $SubjectTrue = $SendMessage->Subject;


                    if ($SendMessage->Type == '0') { ///  שליחת התראה באפליקציה


                        //$FireBaseInfo = DB::table('boostapplogin.studio')->where('CompanyNum', '=', $CompanyNum)->where('ClientId', '=', $TrueClientId)->first();

                        if (!empty($OpenTables->studioToken)) {

                            $OS = $OpenTables->studioOS;
                            $tokenFirebase = $OpenTables->studioToken;

                        } else {

                            //$UsersFireBaseInfo = DB::table('boostapplogin.users')->where('id', '=', @$FireBaseInfo->UserId)->first();
                            $OS = @$OpenTables->usersOS;
                            $tokenFirebase = @$OpenTables->usersToken;

                        }


                        if (empty($tokenFirebase) || @$tokenFirebase == 'No Token') {

                            $tokenFirebase = '';

                        }


                        if (!empty($tokenFirebase)) {


                            $ContentTrue = preg_replace('#<[^>]+>#', ' ', $ContentTrue);

                            $ContentTrue = str_replace("&nbsp;", ' ', $ContentTrue);


                            $registrationIds = array($tokenFirebase);


                            $msg = array

                            (

                                'body' => $ContentTrue,

                                'title' => $AppName,

                                'icon' => 'ic_launcher72',/*Default Icon*/

                                'sound' => 'arpeggio'/*Default sound*/

                            );

                            $fields = array

                            (

                                'registration_ids' => $registrationIds,

                                'notification' => $msg

                            );


                            $headers = array

                            (

                                'Authorization: key=' . $API_ACCESS_KEY,

                                'Content-Type: application/json'

                            );


                            $ch = curl_init();

                            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_HEADER, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

                            $result = curl_exec($ch);
                            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
                            $header = substr($result, 0, $header_size);
                            $body = substr($result, $header_size);


                            try {
                                $character = json_decode($body);
                                $NotificationStatus = $character->success;
                                $NotificationResults = $body;

                                if ($NotificationStatus == '1') {
                                    $Status = '1';
                                } else {
                                    $Status = '2';
                                }

                            } catch(Exception $err) {
                                LoggerService::debug($err->getMessage(), 'send messages debug');
                                $NotificationResults = 'error on parsing';
                                $Status = 2;
                            }

                            if(curl_errno($ch)) {
                                LoggerService::debug('curl err: '.curl_errno($ch), 'debug status code send messages');
                            }

                            curl_close($ch);

                            /// Update


                            DB::table('appnotification')
                                ->where('id', $SendMessage->id)
                                ->where('CompanyNum', $SendMessage->CompanyNum)
                                ->update(array('Status' => $Status, 'Results' => $NotificationResults));


                        } else if (@$tokenFirebase == '' && $SendSMS == '1' && $GetSMS == '0') { /// שולח הודעת טקסט במקום


                            //echo '1';


                            $CompanyNameSend = $PhoneReplay;


                            $ContentTrue = preg_replace('#<[^>]+>#', ' ', $ContentTrue);

                            $ContentTrue = str_replace("&nbsp;", ' ', $ContentTrue);


                            $url = "https://019sms.co.il/api";

                            $xml = '

<?xml version="1.0" encoding="UTF-8"?>

<sms>

<user>

<username>' . $Username019 . '</username>

<password>' . $Password019 . '</password>

</user>

<source>' . $CompanyNameSend . '</source>

<destinations>

<phone id="' . $MobileTrue . '">' . $MobileTrue . '</phone>

</destinations>

<message>

' . $ContentTrue . '



להסרה: 1ba.co/r/' . $OpenTables->id . '/sms

</message>

</sms>';


                            $CR = curl_init();

                            curl_setopt($CR, CURLOPT_URL, $url);
                            curl_setopt($CR, CURLOPT_POST, 1);
                            curl_setopt($CR, CURLOPT_HEADER, 1);
                            curl_setopt($CR, CURLOPT_FAILONERROR, true);
                            curl_setopt($CR, CURLOPT_POSTFIELDS, $xml);
                            curl_setopt($CR, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($CR, CURLOPT_FOLLOWLOCATION, false);
                            curl_setopt($CR, CURLOPT_SSL_VERIFYPEER, 0);
                            curl_setopt($CR, CURLOPT_HTTPHEADER, array("charset=utf-8"));


                            $result = curl_exec($CR);

                            $header_size = curl_getinfo($CR, CURLINFO_HEADER_SIZE);
                            $header = substr($result, 0, $header_size);
                            $body = substr($result, $header_size);

                            try {
                                $responseArr = new SimpleXMLElement($body);
                            } catch (Exception $err) {
                                LoggerService::debug($err->getMessage(), 'send messages debug');
                            }

                            if(curl_errno($CR)) {
                                LoggerService::debug('curl err: '.curl_errno($CR), 'debug status code send messages');
                            }

                            if ((int)@$responseArr->status[0] != 0) {

                                $Status = @$responseArr->status[0];

                                $Results = @$responseArr->message[0];

                            } else {

                                $Status = '1';

                                $Results = @$responseArr->message[0];

                            }


                            curl_close($CR);

                            /// Update

                            $ContentTrue = preg_replace('#<[^>]+>#', ' ', $ContentTrue);

                            $ContentTrue = str_replace("&nbsp;", ' ', $ContentTrue);


                            if (mb_strlen($ContentTrue) <= '200') {

                                $CountTotalLetters = '1';

                            } else {

                                $CountTotalLetters = ceil(mb_strlen($ContentTrue) / 200);

                            }

                            $SMSPrice = $OpenTables->SMSPrice;

                            $SMSSumPrice = $CountTotalLetters * $OpenTables->SMSPrice;


                            DB::table('appnotification')
                                ->where('id', $SendMessage->id)
                                ->where('CompanyNum', $SendMessage->CompanyNum)
                                ->update(array('Type' => '1', 'Status' => $Status, 'Count' => $CountTotalLetters, 'SMSPrice' => $SMSPrice, 'SMSSumPrice' => $SMSSumPrice, 'Results' => $Results));


                        } else if (@$tokenFirebase == '' && $SendSMS == '2' && $EmailTrue != '' && $GetEmail == '0') { /// שולח דואר אלקטרוני


                            $message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<meta charset="utf-8">

</head>

<body>

<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" dir="rtl">

  <tr>

    <td><table width= "650" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="border:10px solid #e1e1e1;">

        <tr>

          <td align="left" valign="top"><table width="650" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #cccccc;">

            <tr>

              <td width="275" align="right" valign="middle" style="padding:30px;"><img src="' . $EmailsLogo . '" alt="Boostapp" title="Boostapp" width="180"  /></td>

              <td width="255" align="left" valign="middle" style="font-family:Arial; font-size:14px; color:#555555; padding:30px;"><strong>'.lang('system_notice').'</strong><br />

                ' . date('d/m/Y') . '</td>

            </tr>

          </table></td>

        </tr>

        <tr>

          <td align="left" valign="top"><table width="650" border="0" style="padding: 30px 30px 30px 30px;" cellpadding="0">

           		  

			<tr><td style="font-family:Arial; font-size:12px;padding-bottom:15px;">

            

			 ' . $ContentTrue . ' 

			 

			  </td>

			  </tr>

          

          </table></td>

        </tr>

      </table>

    <p align="center" style="font-family:Arial; font-size:11px;">&nbsp;</p></td>

  </tr>

</table>

<p align ="center" style="font-family:Arial; font-size:11px;">'.lang('remove_from_mailing').' <a href="https://1ba.co/r/'. $OpenTables->id .'/email"> לחץ כאן</a></p>

</body>

</html>';


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

                            $mail->SetFrom($EmailReplay, $EmailReplayName);

                            //Set an alternative reply-to address

                            $mail->AddReplyTo($EmailReplay, $EmailReplayName);

                            //Set who the message is to be sent to


                            //Set who the message is to be sent to

                            $mail->AddAddress($EmailTrue);

                            //Set the subject line

                            $mail->Subject = ($SubjectTrue);


                            //Read an HTML message body from an external file, convert referenced images to embedded, convert HTML into a basic plain-text alternative body

                            $mail->MsgHTML($message);

                            //Replace the plain text body with one created manually

                            //$mail->AltBody = ($message);


                            //Send the message, check for errors

                            if (!$mail->Send()) {

                                $Results = $mail->ErrorInfo;

                                $Status = '2';

                            } else {

                                $Status = '1';

                                $Results = '';

                            }


                            DB::table('appnotification')
                                ->where('id', $SendMessage->id)
                                ->where('CompanyNum', $SendMessage->CompanyNum)
                                ->update(array('Type' => '2', 'Status' => $Status, 'Results' => $Results));


                        }


                    } else if ($SendMessage->Type == '1' && $GetSMS == '0') { /// שליחת הודעת טקסט

                        if(!empty($SendMessage->PhoneNumber)) { // בדיקת אם קיים טלפון ב appnotification->phoneNumber
                            $phoneNumber = PhoneHelper::getFullPhoneNumber((string)$SendMessage->PhoneNumber);
                            if(!empty($phoneNumber)) {
                                $MobileTrue = $phoneNumber;
                            }
                        }

                        $CompanyNameSend = $PhoneReplay;


                        $ContentTrue = preg_replace('#<[^>]+>#', ' ', $ContentTrue);

                        $ContentTrue = str_replace("&nbsp;", ' ', $ContentTrue);


                        $url = "https://019sms.co.il/api";

                        $xml = '

<?xml version="1.0" encoding="UTF-8"?>

<sms>

<user>

<username>' . $Username019 . '</username>

<password>' . $Password019 . '</password>

</user>

<source>' . $CompanyNameSend . '</source>

<destinations>

<phone id="' . $MobileTrue . '">' . $MobileTrue . '</phone>

</destinations>

<message>

' . $ContentTrue . '



להסרה: 1ba.co/r/' . $OpenTables->id . '/sms



</message>

</sms>';


                        $CR = curl_init();

                        curl_setopt($CR, CURLOPT_URL, $url);
                        curl_setopt($CR, CURLOPT_POST, 1);
                        curl_setopt($CR, CURLOPT_HEADER, 1);
                        curl_setopt($CR, CURLOPT_FAILONERROR, true);
                        curl_setopt($CR, CURLOPT_POSTFIELDS, $xml);
                        curl_setopt($CR, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($CR, CURLOPT_FOLLOWLOCATION, false);
                        curl_setopt($CR, CURLOPT_SSL_VERIFYPEER, 0);
                        curl_setopt($CR, CURLOPT_HTTPHEADER, array("charset=utf-8"));

                        $result = curl_exec($CR);

                        $header_size = curl_getinfo($CR, CURLINFO_HEADER_SIZE);
                        $header = substr($result, 0, $header_size);
                        $body = substr($result, $header_size);

                        try {
                            $responseArr = new SimpleXMLElement($body);
                        } catch (Exception $err) {
                            LoggerService::debug($err->getMessage(), 'send messages debug');
                        }

                        if ((int)@$responseArr->status[0] != 0) {

                            $Status = @$responseArr->status[0];

                            $Results = @$responseArr->message[0];

                        } else {

                            $Status = '1';

                            $Results = @$responseArr->message[0];

                        }


                        curl_close($CR);


                        /// Update

                        $ContentTrue = preg_replace('#<[^>]+>#', ' ', $ContentTrue);

                        $ContentTrue = str_replace("&nbsp;", ' ', $ContentTrue);

                        if (mb_strlen($ContentTrue) <= '200') {

                            $CountTotalLetters = '1';

                        } else {

                            $CountTotalLetters = ceil(mb_strlen($ContentTrue) / 200);

                        }

                        $SMSPrice = $OpenTables->SMSPrice;

                        $SMSSumPrice = $CountTotalLetters * $OpenTables->SMSPrice;


                        DB::table('appnotification')
                            ->where('id', $SendMessage->id)
                            ->where('CompanyNum', $SendMessage->CompanyNum)
                            ->update(array('Status' => $Status, 'Count' => $CountTotalLetters, 'SMSPrice' => $SMSPrice, 'SMSSumPrice' => $SMSSumPrice, 'Results' => $Results));


                    } else if ($SendMessage->Type == '2' && $GetEmail == '0') { ///  שליחת דואר אלקטרוני


                        $message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<meta charset="utf-8">

</head>

<body>

<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" dir="rtl">

  <tr>

    <td><table width= "650" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="border:10px solid #e1e1e1;">

        <tr>

          <td align="left" valign="top"><table width="650" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #cccccc;">

            <tr>

              <td width="275" align="right" valign="middle" style="padding:30px;"><img src="' . $EmailsLogo . '" alt="Boostapp" title="Boostapp" width="180"  /></td>

              <td width="255" align="left" valign="middle" style="font-family:Arial; font-size:14px; color:#555555; padding:30px;"><strong>'.lang('system_notice').'</strong><br />

                ' . date('d/m/Y') . '</td>

            </tr>

          </table></td>

        </tr>

        <tr>

          <td align="left" valign="top"><table width="650" border="0" style="padding: 30px 30px 30px 30px;" cellpadding="0">

           		  

			<tr><td style="font-family:Arial; font-size:12px;padding-bottom:15px;">

			

			 <br />

			 ' . $SendMessage->Text . ' 



			  </td>

			  </tr>

          

          </table></td>

        </tr>

      </table>

    <p align="center" style="font-family:Arial; font-size:11px;">&nbsp;</p></td>

  </tr>

</table>

<p align ="center" style="font-family:Arial; font-size:11px;">'.lang('remove_from_mailing').' <a href="https://1ba.co/r/' . $OpenTables->id . '/email"> '.lang('click_here').'</a></p>

</body>

</html>';


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

                        $mail->SetFrom($EmailReplay, $EmailReplayName);

//Set an alternative reply-to address

                        $mail->AddReplyTo($EmailReplay, $EmailReplayName);

//Set who the message is to be sent to


//Set who the message is to be sent to

                        $mail->AddAddress($EmailTrue);

//Set the subject line

                        $mail->Subject = ($SubjectTrue);


//Read an HTML message body from an external file, convert referenced images to embedded, convert HTML into a basic plain-text alternative body

                        $mail->MsgHTML($message);

//Replace the plain text body with one created manually

//$mail->AltBody = ($message);

                        $Status = '1';

//Send the message, check for errors

                        if (!$mail->Send()) {

                            $Results = $mail->ErrorInfo;

                            $Status = '2';

                        } else {

                            $Status = '1';

                            $Results = '';

                        }


                        DB::table('appnotification')
                            ->where('id', $SendMessage->id)
                            ->where('CompanyNum', $SendMessage->CompanyNum)
                            ->update(array('Status' => $Status, 'Results' => $Results));


                    } ///// הגדרת התראה לסטודיו במייל


                    else if ($SendMessage->Type == '4' && $OpenTables->settingsEmail != '') { ///  שליחת דואר אלקטרוני


                        $EmailTrue = $OpenTables->settingsEmail;


                        $message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<meta charset="utf-8">

</head>

<body>

<table width="100%" height="100%" border="0" cellspacing="0" cellpadding="0" dir="rtl">

  <tr>

    <td><table width= "650" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="border:10px solid #e1e1e1;">

        <tr>

          <td align="left" valign="top"><table width="650" border="0" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #cccccc;">

            <tr>

              <td width="275" align="right" valign="middle" style="padding:30px;"><img src="' . $EmailsLogo . '" alt="Boostapp" title="Boostapp" width="180"  /></td>

              <td width="255" align="left" valign="middle" style="font-family:Arial; font-size:14px; color:#555555; padding:30px;"><strong>'.lang('system_notice').'</strong><br />

                ' . date('d/m/Y') . '</td>

            </tr>

          </table></td>

        </tr>

        <tr>

          <td align="left" valign="top"><table width="650" border="0" style="padding: 30px 30px 30px 30px;" cellpadding="0">

           		  

			<tr><td style="font-family:Arial; font-size:12px;padding-bottom:15px;">

			 <br />

			 ' . $SendMessage->Text . ' 



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

                        $mail->SetFrom($EmailReplay, $EmailReplayName);

//Set an alternative reply-to address

                        $mail->AddReplyTo($EmailReplay, $EmailReplayName);

//Set who the message is to be sent to


//Set who the message is to be sent to

                        $mail->AddAddress($EmailTrue);

//Set the subject line

                        $mail->Subject = ($SubjectTrue);


//Read an HTML message body from an external file, convert referenced images to embedded, convert HTML into a basic plain-text alternative body

                        $mail->MsgHTML($message);

//Replace the plain text body with one created manually

//$mail->AltBody = ($message);

                        $Status = '1';

//Send the message, check for errors

                        if (!$mail->Send()) {

                            $Results = $mail->ErrorInfo;

                            $Status = '2';

                        } else {

                            $Status = '1';

                            $Results = '';

                        }


                        DB::table('appnotification')
                            ->where('id', $SendMessage->id)
                            ->where('CompanyNum', $SendMessage->CompanyNum)
                            ->update(array('Status' => $Status, 'Results' => $Results));


                    }


                } /// סיום שליחת הודעה


                $PhoneReplay = 'boost';

                $EmailReplay = 'no-reply@boostapp.co.il';

                $EmailReplayName = 'boost';

                $EmailsLogo = 'https://login.boostapp.co.il/assets/img/LogoMail.png';

                $API_ACCESS_KEY = FirebaseToken::DEFAULT_TOKEN;

            } else {

                DB::table('appnotification')
                    ->where('id', $SendMessage->id)
                    ->where('CompanyNum', $SendMessage->CompanyNum)
                    ->update(array('Status' => '2', 'workStatusDone' => date('Y-m-d H:i:s')));


            }


            //echo "send email";

        }

        // end foreach message


        DB::table('appnotification')
            ->whereIn('id', $update)
            ->update(array('workerStatus' => 2));


        sleep(5);


    }


    $Cron->end();
}
catch (Exception $e){
    $arr = array(
        "line" => $e->getLine(),
        "message" => $e->getMessage(),
        "file_path" => $e->getFile(),
        "trace" => $e->getTraceAsString()
    );
    if(isset($SendMessage)){
        $util = new Utils();
        $arr["data"] = json_encode($util->createArrayFromObj($SendMessage),JSON_UNESCAPED_UNICODE);
    }
    $Cron->cronLog($arr);
}

