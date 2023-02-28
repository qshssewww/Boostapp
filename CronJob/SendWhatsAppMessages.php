<?php
$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/WhatsAppNotifications.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/services/WhatsAppService.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/services/LoggerService.php';

$filename = basename(__FILE__, '.php');

$Cron = new CronManager($filename);

$id = $Cron->start();
ini_set('max_execution_time', 0);
set_time_limit(0);
ini_set("memory_limit", "-1");

$limit_messages = 300;
$limit_iteration = 60;

try {
    $SendMessages = WhatsAppNotifications::getMessages4Send($limit_messages);
    $iterations = (int)ceil(count($SendMessages) / $limit_iteration);

    for ($i = 0; $i < $iterations; $i++) {
        $ThisDate = date('Y-m-d');
        $ThisTime = date('H:i:s');

        $SendMessages_slice = array_slice($SendMessages, $i * $limit_iteration, $limit_iteration);

        $update = [];
        foreach ($SendMessages_slice as $SendMessage) {
            $update[] = $SendMessage->id;
        }
        WhatsAppNotifications::updateWorkerStatusStart($update);

        /** @var WhatsAppNotifications $SendMessage */
        foreach ($SendMessages_slice as $SendMessage) {
            if ($SendMessage->workerStatus === '2' || $SendMessage->workerStatus === '1') continue;

            /** @var Client $client */
            $client = Client::find($SendMessage->ClientId) ?? null;
            /** @var Settings $company */
            $company = Settings::getSettings($client->CompanyNum) ?? null;

            if ($company) {
                ///  בדיקת שעה שליחת ההתראה
                if (($SendMessage->Date == $ThisDate && $SendMessage->Time <= $ThisTime) || $SendMessage->Date < $ThisDate) {
                    // set temp error, should be rewritten on success
                    $SendMessage->setError(3);

                    $result = WhatsAppService::sendNotificationMessage($SendMessage);

                    $SendMessage->setResponse($result);
                } /// סיום שליחת הודעה
            } else {
                $SendMessage->setError();
            }
        }

        WhatsAppNotifications::updateWorkerStatusEnd($update);

        // sleep for 1 second to restore quota before next iteration
        sleep(1);
    }

    $Cron->end();
} catch (Exception $e) {
    $arr = array(
        "line" => $e->getLine(),
        "message" => $e->getMessage(),
        "file_path" => $e->getFile(),
        "trace" => $e->getTraceAsString()
    );
    if (isset($SendMessage)) {
        $util = new Utils();
        $arr["data"] = json_encode($util->createArrayFromObj($SendMessage), JSON_UNESCAPED_UNICODE);
    }
    $Cron->cronLog($arr);
}
