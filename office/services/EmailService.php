<?php
require_once __DIR__ . '/../../mail/class.phpmailer.php';
require_once __DIR__ . '/../Classes/Company.php';

class EmailService
{
    public const USERNAME_SENDGRID = "apikey";
    public const PASSWORD_SENDGRID = "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";
    public const HOST = "smtp.sendgrid.net";
    public const PORT = 587;
    public const SENDER_EMAIL = "no-reply@boostapp.co.il";
    public const SENDER_NAME = 'Boostapp';


    /**
     * @param string $email
     * @param string $text
     * @param string $subject
     * @param int $clientId
     * @return array
     */
    public static function send(string $email, string $subject, string $text, $clientId = 0)
    {
        $message = self::composeMessage($text, $clientId);

        return self::sendEmail($email, $subject, $message);
    }

    /**
     * @param $email
     * @param $subject
     * @param $template
     * @param array $data
     * @return array
     */
    public static function sendTemplate($email, $subject, $template, array $data = [])
    {
        $clientId = $data['clientId'] ?? null;

        $template = View::make('mail/' . $template, $data)->render();

        $message = self::composeMessage($template, $clientId);

        return self::sendEmail($email, $subject, $message);
    }

    /**
     * @param $text
     * @param $clientId
     * @return string
     */
    protected static function composeMessage($text, $clientId = null): string
    {
        $logo = App::url('assets/img/LogoMail.png');

        if ($clientId) {
            // TODO: replace getting company
            $settings = Company::getInstance();
            if ($settings->__get('Memotag') == 1) {
                $logo = App::url('office/files/' . $settings->DocsCompanyLogo);
            }
        }

        $unsubscribe = self::getUnsubscribeLink($clientId);

        return View::make('mail/layout', [
            'logo' => $logo,
            'unsubscribe' => $unsubscribe,
            'text' => $text,
        ])->render();
    }

    /**
     * @param $clientId
     * @return string
     */
    protected static function getUnsubscribeLink($clientId)
    {
        return $clientId ? '<p align="center" style="font-family:Arial; font-size:11px;">'.lang('remove_from_mailing').' <a href="https://1ba.co/r/' . $clientId . '/email"> '.lang('click_here').'</a></p>' : '';
    }

    /**
     * @param $email
     * @param $subject
     * @param $message
     * @return array
     */
    protected static function sendEmail($email, $subject, $message): array
    {
        try {
            $mail = new PHPMailer();
            $mail->IsSMTP(); // enable SMTP
            $mail->SMTPAuth = true; // authentication enabled
            $mail->SMTPSecure = 'tls'; // secure transfer enabled REQUIRED for GMail
            $mail->Host = self::HOST;
            $mail->Port = self::PORT;
            $mail->IsHTML();
            $mail->Username = self::USERNAME_SENDGRID;
            $mail->Password = self::PASSWORD_SENDGRID;
            //Set who the message is to be sent from
            $mail->SetFrom(self::SENDER_EMAIL, self::SENDER_NAME);
            //Set an alternative reply-to address
            $mail->AddReplyTo(self::SENDER_EMAIL, self::SENDER_NAME);
            //Set who the message is to be sent to
            $mail->AddAddress($email);
            //Set the subject line
            $mail->Subject = ($subject);
            //Read an HTML message body from an external file, convert referenced images to embedded, convert HTML into a basic plain-text alternative body
            $mail->MsgHTML($message);

            if (!$mail->Send()) {
                return ["status" => 0, "message" => $mail->ErrorInfo];
            } else {
                return ["status" => 1, "message" => "Email sent!"];
            }
        } catch (Exception $e) {
            return [
                "status" => 500,
                "message" => $e->getMessage(),
                "trace" => $e->getTrace()
            ];
        }
    }

    public static function sendGetRegistrationSuccess(string $email, string $password, string $name) {
        $subject = 'הצטרפות למערכת';
        $system_notice = lang('system_notice') ;
        $date_notice = date('d/m/Y') ;
        $show_top_part = false;
        ob_start();
        include (__DIR__ . '/../email-parsed.php');
        $text = ob_get_contents();
        ob_end_clean();
        return self::sendEmail($email, $subject, $text);
    }



    public static function sendTagRequest(string $newTagName) {
        $user = User::find(Auth::user()->id);
        $companyNum = Auth::user()->CompanyNum;
        $companyName = Settings::getCompanyNameByNum($companyNum);

        $userId = $user->id;
        $userName = $user->display_name;
        $email = 'dev@boostapp.co.il';
        $subject = 'Request to add a new Tag';
        $text =
        '<div>
                <p> '.$userName.'</p>
                <p> user id: '.$userId.' </p>
                <p> '.$companyName.' from </p>
                <p> asking to add a new tag: '.$newTagName.'</p>
        </div>';

        return self::send($email,$subject,  $text);
    }

}
