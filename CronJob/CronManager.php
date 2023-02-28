<?php
require_once $_SERVER['DOCUMENT_ROOT'] .'/mail/class.phpmailer.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/services/EmailService.php';

class CronManager
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $devEmails = [
        'moshegolan86@gmail.com'
      ];

    /**
     * @var array
     */
    public $boostappEmails = [
        'moshegolan86@gmail.com'
    ];

    /**
     * @var int
     */
    private const MAX_TRIES = 3;

    /**
     * CronManager constructor.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->sendMailIfCronFails();
        $this->preventDoubleExecution();
    }

    /**
     * Prevent the cron from being executed multiple times.
     *
     * @return void
     */
    private function preventDoubleExecution()
    {
        if ($this->isRunning()) {
            die('The last run of cron has not finished');
        }
    }

    /**
     * Check if the cron is running
     *
     * @return bool
     */
    private function isRunning()
    {
        $CronQuery = DB::table('crons')
            ->select('id')
            ->where('file_name', $this->name)
            ->where('done', 0)
            ->where('try', '<', self::MAX_TRIES)
            ->whereNotIn('file_name', array('SendMessages', 'HighPriorityMessages', 'CreditCardKeva'))
            ->orderBy('id', 'DESC')
            ->limit(1);

        if (!$CronQuery->count()) {
            return false;
        }

        $Cron = $CronQuery->first();

        DB::table('crons')
            ->where('id', $Cron->id)
            ->update([
                'try' => DB::raw('try + 1'),
            ]);

        return true;
    }

    public function sendMailIfCronFails()
    {
        $CronQuery = DB::table('crons')
            ->select('id')
            ->where('file_name', $this->name)
            ->whereIn('done', [0,2])
            ->where('try', '<', static::MAX_TRIES)
            ->whereNotIn('file_name', array('SendMessages', 'HighPriorityMessages'))
            ->orderBy('id', 'DESC')
            ->limit(1);

        if (!$CronQuery->count()) {
            return false;
        }

        $Cron = $CronQuery->first();
        $currentTry = $Cron->try + 1;

        if($currentTry > 1 && !$Cron->sent_dev_mail && (($Cron->file_name == "CreditCardKeva" && $Cron->done == 2) || $Cron->file_name != "CreditCardKeva") ){
            $message = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                        <meta charset="utf-8">
                        </head>
                        <body>
                            <p>date: '.date('d/m/Y H:i:s').'</p>
                            <p>cron failed: '. $this->name .' ran '. $currentTry .' time</p>
                        </body>
                        </html>';

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

            foreach($this->devEmails as $devEmail)
            {
                $mail->AddAddress($devEmail);
                $mail->Subject = ('boostapp cron failed');
                $mail->MsgHTML($message);

                if(!$mail->Send()) {
                    $Results = $mail->ErrorInfo;
                    $Status = '2';
                } else {
                    $Status = '1';
                    $Results = '';
                }
            }

            if($Status == 1 && $currentTry >= 2){
                DB::table('crons')
                    ->where('id', $Cron->id)
                    ->update([
                        'sent_dev_mail' => '1',
                    ]);
            }
        }
    }

    /**
     * Start cron process.
     *
     * @return int
     */
    public function start()
    {
        $this->id = DB::table('crons')
            ->insertGetId([
                'file_name' => $this->name,
                'start_process_ts' => time(),
                'done' => 0,
                'done_ts' => 0,
                'try' => 1,
            ]);

        return $this->id;
    }

    /**
     * End cron process.
     *
     * @return bool
     */
    public function end()
    {
        if (!$this->id) {
            return false;
        }

        DB::table('crons')
            ->where('id', $this->id)
            ->update([
                'done' => 1,
                'done_ts' => time(),
            ]);

        return true;
    }

    public function cronLog($arr){
        $arr["cron_id"] = $this->id;
        DB::table("boostapp.cron_log")->insertGetId($arr);

        // mark as error
        if (!$this->id) {
            return false;
        }

        DB::table('crons')
            ->where('id', $this->id)
            ->update([
                'done' => 2
            ]);
    }
}
