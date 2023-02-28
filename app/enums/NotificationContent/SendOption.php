<?php

class SendOption
{
    public const SEND_OPTION_NONE = 'BA000';
    public const SEND_OPTION_PUSH = 0;
    public const SEND_OPTION_SMS  = 1;
    public const SEND_OPTION_MAIL = 2;
    public const SEND_OPTION_WHATSAPP = 4;
    public const SEND_OPTION_ALL  = 'BA999';

    private static $_sendOption = [];

    /**
     * @return array
     */
    public static function toList(): array
    {
        if (empty(self::$_sendOption)) {
            foreach (array_keys(self::names()) as $event) {
                self::$_sendOption[] = [
                    'id' => (int)$event,
                    'name' => self::name($event),
                    'appNotificationType' => self::appNotificationType($event)
                ];
            }
        }
        return self::$_sendOption;
    }

    /**
     * @return array
     */
    public static function names(): array
    {
        return [
            self::SEND_OPTION_NONE => lang('without_notify'),
            self::SEND_OPTION_PUSH => lang('push_notification'),
            self::SEND_OPTION_SMS => lang('sms_message_pay'),
            self::SEND_OPTION_MAIL => lang('email_free'),
            self::SEND_OPTION_WHATSAPP => lang('whatsapp_notifications_select'),
            self::SEND_OPTION_ALL => lang('all'),
        ];
    }

    /**
     * @return ?int[]
     */
    public static function appNotificationTypes(): array
    {
        return [
            self::SEND_OPTION_NONE => null,
            self::SEND_OPTION_ALL => null,
            self::SEND_OPTION_WHATSAPP => AppNotification::TYPE_PUSH,
            self::SEND_OPTION_PUSH => AppNotification::TYPE_PUSH,
            self::SEND_OPTION_MAIL => AppNotification::TYPE_EMAIL,
            self::SEND_OPTION_SMS => AppNotification::TYPE_SMS,
        ];
    }


    /**
     * @param $event
     * @return string|null
     */
    public static function name($event): ?string
    {
        return self::names()[$event] ?? null;
    }

    /**
     * @param $event
     * @return string|null
     */
    public static function appNotificationType($event): ?string
    {
        return self::appNotificationTypes()[$event] ?? null;
    }

    /**
     * @param $TemplateSendOption
     * @param $Type
     * @return bool
     */
    public static function checkSendOption($TemplateSendOption, $Type): bool
    {
        if ($TemplateSendOption == self::SEND_OPTION_NONE) return false;

        if ($Type == self::SEND_OPTION_WHATSAPP) {
            return $TemplateSendOption == self::SEND_OPTION_WHATSAPP;
        }

        if ($TemplateSendOption == self::SEND_OPTION_ALL) return true;

        $myArray = explode(',', $TemplateSendOption);
        return in_array($Type, $myArray);
    }
}
