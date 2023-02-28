<?php
require_once __DIR__ . '/../Classes/FirebaseToken.php';

class PushNotificationService
{
    const API_ACCESS_KEY = 7;

    public static function sendPushNotification($token, $subject, $content, $apiAccessKey = null): array
    {
        $apiAccessKey = !empty($apiAccessKey) ? $apiAccessKey : FirebaseToken::getTokenByProject(7);

        $headers = [
            'Authorization: key=' . $apiAccessKey,
            'Content-Type: application/json'
        ];

        $msg = [
            'body' => $content,
            'title' => $subject,
            'icon' => 'ic_launcher72',/*Default Icon*/
            'sound' => 'arpeggio'/*Default sound*/
        ];

        $fields = [
            'registration_ids' => [$token],
            'notification' => $msg
        ];

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
            $notificationStatus = $character->success;
            $results = $body;

            $status = $notificationStatus == 1 ? 1 : 2;

        } catch (Exception $err) {
            $results = json_encode($err, JSON_PRETTY_PRINT);
            $status = 2;
        }

        if (curl_errno($ch)) {
            $results = null;
            $status = 2;
            LoggerService::debug(curl_error($ch), 'debug status code send messages');
        }

        curl_close($ch);

        return [
            'results' => $results,
            'status' => $status
        ];
    }
}
