<?php

require_once 'LoggerService.php';

/**
 *
 */
class SmsService
{
    public const username019 = "beesoft";
    public const password019 = "u7jZ#oXGarq*sZON";
    public const url = "https://019sms.co.il/api";
    public const sender = 'Boostapp';

    /**
     * Sending sms message
     * @param string $phoneNumber message will be sent to (including country code, no leading zero)
     * @param string $message the message that will be sent
     *
     * @return bool
     */
    public static function send(string $phoneNumber, string $message): bool
    {
        if (strpos($phoneNumber, "+972") === 0) {
            $xml = self::composeMessage($phoneNumber, $message);

            try {
                return self::sendRequest($xml);
            } catch (LogicException $e) {
                LoggerService::error([
                    'error' => 'Error when sending sms',
                    'phoneNumber' => $phoneNumber,
                    'message' => $message,
                    'response' => $e->getMessage(),
                ], LoggerService::CATEGORY_SMS);
            } catch (Throwable $e) {
                LoggerService::error($e, LoggerService::CATEGORY_SMS);
            }
        } else {
            LoggerService::info('Wrong phone format: ' . $phoneNumber, LoggerService::CATEGORY_SMS);
        }
        return false;
    }

    /**
     * @param $phoneNumber
     * @param $message
     * @return string
     */
    protected static function composeMessage($phoneNumber, $message): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>
        <sms>
            <user>
                <username>' . self::username019 . '</username>
                <password>' . self::password019 . '</password>
            </user>
            <source>' . self::sender . '</source>
        
            <destinations>
                <phone id="' . $phoneNumber . '">' . $phoneNumber . '</phone>
            </destinations>
            <message>' . $message . '</message>
        </sms>';
    }

    /**
     * @param $message
     * @return bool
     * @throws Exception
     */
    protected static function sendRequest($message): bool
    {
        $CR = curl_init();
        curl_setopt($CR, CURLOPT_URL, self::url);
        curl_setopt($CR, CURLOPT_POST, 1);
        curl_setopt($CR, CURLOPT_FAILONERROR, true);
        curl_setopt($CR, CURLOPT_POSTFIELDS, $message);
        curl_setopt($CR, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($CR, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($CR, CURLOPT_HTTPHEADER, array("charset=utf-8"));

        $result = curl_exec($CR);
        curl_close($CR);

        $responseArr = new SimpleXMLElement($result);
        if ((int)$responseArr->status == 0) {
            return true;
        } else {
            throw new LogicException($responseArr);
        }
    }
}