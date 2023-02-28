<?php

require_once __DIR__ . '/LoggerService.php';
require_once __DIR__ . '/../Utils/HttpClient.php';
require_once __DIR__ . '/../Classes/WhatsAppNotifications.php';
require_once __DIR__ . '/../Classes/WhatsAppWebhooks.php';

/**
 *
 */
class WhatsAppService
{
    private const userAccessToken = 'EAAbclhZCL3BMBAH83DZBc3ZBNYoYNswJslrUVI99Dx6a3DRV2Xqi4h6DZCqm7R64t7lAMv5ZBt97iGDa4LMLYRHyHh7kCrBHqhPMKuPp2ZANXgIinwVfuF1kceMDFQDqJQoKnPqWWuONZAtWKyOZAbDEjWZAj07JzDAJDk1lioUewmYnpWgZCWM3Kd';
    private const businessAccountId = '103947879185251';  // Boostapp
    private const phoneNumberId = '107655148812507';      // 054 806 9948
    private const version = 'v15.0';
    private const url = "https://graph.facebook.com/";

    /**
     * @param WhatsAppNotifications $message
     * @return array|bool
     */
    public static function sendNotificationMessage(WhatsAppNotifications $message)
    {
        $res = self::sendTemplate($message->clientPhone, $message->template_name, $message->template_language, $message->template_components);

        // add webhook if needed
        if (isset($res['messages'][0]['id']) && $message->template_name == 'waiting_list_btn') {
            $valid = null;

            /** @var ClassStudioAct $classAct */
            $classAct = ClassStudioAct::find($message->classActId) ?? null;
            if ($classAct) {
                $valid = $classAct->TimeAutoWatinglistDate . " " . $classAct->TimeAutoWatinglist;
            }

            WhatsAppWebhooks::insertGetId([
                'phone' => $message->clientPhone,
                'wamid' => $res['messages'][0]['id'],
                'template_name' => $message->template_name,
                'valid' => $valid,
                'classActId' => $message->classActId,
            ]);
        }

        return $res;
    }

    /**
     * Sending WhatsApp message
     * @param string $phoneNumber message will be sent to (including country code without +, no leading zero)
     * @param string|null $name Template name
     * @param string|null $language Template language
     * @param string|null $components Template content
     * @return array|boolean
     */
    public static function sendTemplate(string $phoneNumber, ?string $name = "hello_world", ?string $language = "en_US", ?string $components = '')
    {
        if (strpos($phoneNumber, "972") === 0) {
            $json = self::composeTemplateMessage($phoneNumber, $name, $language, json_decode($components));

            try {
                return HttpClient::sendRequest('POST', self::url . self::version . "/" . self::phoneNumberId . "/messages", $json, false, [
                    'json' => true,
                    "Authorization" => "Bearer " . self::userAccessToken,
                ]);
            } catch (LogicException $e) {
                LoggerService::error([
                    'error' => 'Error when sending WhatsApp template',
                    'phoneNumber' => $phoneNumber,
                    'name' => $name,
                    'language' => $language,
                    'components' => $components,
                    'response' => $e->getMessage(),
                ], LoggerService::CATEGORY_WHATSAPP);
            } catch (Throwable $e) {
                LoggerService::error($e, LoggerService::CATEGORY_WHATSAPP);
            }
        } else {
            LoggerService::info('Wrong phone format: ' . $phoneNumber, LoggerService::CATEGORY_WHATSAPP);
        }
        return false;
    }

    /**
     * Getting WhatsApp template list
     * @param string $businessAccountId
     * @return array
     */
    public static function getTemplateList(string $businessAccountId = self::businessAccountId): array
    {
        try {
            $response = HttpClient::sendRequest('GET', self::url . self::version . "/" . $businessAccountId . "/message_templates", [
                'access_token' => self::userAccessToken,
            ]);

            $data = $response['data'];
            while (isset($response['paging']['next'])) {
                $response = HttpClient::sendRequest('GET', $response['paging']['next'], [
                    'access_token' => self::userAccessToken,
                ]);
                $data = array_merge($data, $response['data']);
            }

            return $data;

        } catch (LogicException $e) {
            LoggerService::error([
                'error' => 'Error when getting WhatsApp template list',
                'businessAccountId' => $businessAccountId,
                'response' => $e->getMessage(),
            ], LoggerService::CATEGORY_WHATSAPP);
        } catch (Throwable $e) {
            LoggerService::error($e, LoggerService::CATEGORY_WHATSAPP);
        }

        return [];
    }

    /**
     * @param $phoneNumber
     * @param $name
     * @param $language
     * @param $components
     * @return array
     */
    private static function composeTemplateMessage($phoneNumber, $name, $language, $components): array
    {
        return [
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $phoneNumber,
            "type" => 'template',
            "template" => (object)[
                "name" => $name,
                "language" => (object)[
                    "code" => $language
                ],
                "components" => $components
            ]
        ];
    }

    /**
     * @param array $header
     * @param array $body
     * @param array $buttons
     * @return string
     */
    public static function composeTemplateComponents(array $header = [], array $body = [], array $buttons = []): string
    {
        $res = [];

        if (sizeof($header) > 0) {
            // TODO format header structure when needed
        }

        // format body structure
        if (sizeof($body) > 0) {
            $resBody = [];
            foreach ($body as $part) {
                $resBody[] = (object)[
                    "type" => "text",
                    "text" => $part
                ];
            }

            $res[] = (object)[
                "type" => "body",
                "parameters" => $resBody
            ];
        }

        $ind = 0;
        foreach ($buttons as $button) {
            $params = [];
            if ($button['subType'] == "quick_reply") {
                $params[] = (object)[
                    "type" => "payload",
                    "payload" => $button['value']
                ];
            } else {
                // 'subType' == 'url'
                $params[] = (object)[
                    "type" => "text",
                    "text" => $button['value']
                ];
            }

            $res[] = (object)[
                "type" => "button",
                "sub_type" => $button['subType'],
                "index" => $ind++,
                "parameters" => $params
            ];
        }

        return json_encode($res);
    }

}
