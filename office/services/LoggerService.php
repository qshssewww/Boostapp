<?php

class LoggerService
{
    public static $table = 'login_log';

    const TYPE_DEBUG = 'debug';
    const TYPE_INFO = 'info';
    const TYPE_ERROR = 'error';

    // Add here your category for logs
    const CATEGORY_COMMON = 'common';
    const CATEGORY_CLIENT = 'client';
    const CATEGORY_EXCEPTION = 'exception';
    const CATEGORY_CRON_CARDS = 'cron_cards';
    const CATEGORY_PAYMENT_RESOLVER_YAAD = 'payment_resolver_yaad';
    const CATEGORY_CRON_HORAAT_KEVA = 'cron_horaat_keva';
    const CATEGORY_CRON_HORAAT_KEVA_RETURNS = 'cron_horaat_keva_returns';
    const CATEGORY_PAYMENT_PROCESS = 'payment_process';
    const CATEGORY_PAYMENT_PROCESS_REFUND = 'payment_process_refund';
    const CATEGORY_PAYMENT_PROCESS_CANCEL = 'payment_process_cancel';
    const CATEGORY_PAYMENT_WITH_NEW_CARD = 'payment_with_new_card';
    const CATEGORY_PAYMENT_WITH_SAVED_CARD = 'payment_with_saved_card';
    const CATEGORY_PAYMENT_CANCEL_DOCS = 'payment_cancel_docs';
    const CATEGORY_ADD_NEW_TOKEN = 'add_new_token';
    const CATEGORY_YAADSARIG = 'yaadsarig';
    const CATEGORY_MESHULAM = 'meshulam';
    const CATEGORY_TRANZILA = 'tranzila';
    const CATEGORY_TEMPLATE_MEEETING = 'template_meeting';
    const CATEGORY_CLUB_MEMBERSHIPS = 'club_memberships';
    const CATEGORY_SMS = 'sms';
    const CATEGORY_NOTIFICATION_SETTINGS = 'notification_settings';
    const CATEGORY_HORAAT_KEVA_CHANGE_STATUS = 'horaat_keva_change_status';
    const CATEGORY_CRON_ADD_CLASSES = 'cron_add_classes';
    const CATEGORY_GOOGLE_CALENDAR = 'google_calendar';
    const CATEGORY_ACT_MEETING = 'act_meeting';
    const CATEGORY_CANCEL_MEETING = 'cancel_meeting';
    const CATEGORY_MOVE_CLASSES = 'move_classes';
    const CATEGORY_CART = 'cart';
    const CATEGORY_DOCS = 'docs';
    const CATEGORY_WHATSAPP = 'whatsapp';
    const CATEGORY_CLIENT_GOOGLE_ADDRESS = 'client_google_address';
    const CATEGORY_CLASS_STATUS = 'class_status';
    const CATEGORY_DIGITAL_SIGNATURE = 'digital_signature';
    const CATEGORY_CHECKOUT_ORDER = 'checkout_order';

    /**
     * @param $message
     * @param string $type
     * @param string $category
     */
    public static function sendToLog($message, string $type = self::TYPE_INFO, string $category = self::CATEGORY_COMMON)
    {
        if (!is_array($message)) {
            if ($message instanceof \Throwable) {
                $message = [
                    'message' => $message->getMessage(),
                    'file' => $message->getFile(),
                    'line' => $message->getLine(),
                    'trace' => $message->getTraceAsString(),
                ];
            } else {
                $message = ['message' => $message];
            }
        }

        if (!isset($message['file'], $message['line'])) {
            $bt = debug_backtrace();
            $caller = array_shift($bt);
            if ($caller['class'] === LoggerService::class && !empty($bt)) {
                $caller = array_shift($bt);
            }
            $message['file'] = $caller['file'];
            $message['line'] = $caller['line'];
        }

        $message['page_url'] = self::getPageUrl();
        if (!empty($_SERVER['HTTP_REFERER'])) {
            $message['referer'] = $_SERVER['HTTP_REFERER'];
        }

        $clientId = 0;
        if (Auth::check()) {
            $clientId = Auth::user()->id;
        }

        $file = $message['file'];
        $line = $message['line'];
        $trace = $message['trace'] ?? '';

        unset($message['file'], $message['line'], $message['trace']);

        DB::table(self::$table)->insertGetId([
            'type' => $type,
            'category' => $category,
            'file_path' => $file . ':' . $line,
            'message' => json_encode($message, JSON_PRETTY_PRINT),
            'trace' => $trace,
            'client_id' => $clientId,
            'date' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * @param $message
     * @param string $category
     */
    public static function error($message, string $category = self::CATEGORY_COMMON)
    {
        self::sendToLog($message, self::TYPE_ERROR, $category);
    }

    /**
     * @param $message
     * @param string $category
     */
    public static function info($message, string $category = self::CATEGORY_COMMON)
    {
        self::sendToLog($message, self::TYPE_INFO, $category);
    }

    /**
     * @param $message
     * @param string $category
     */
    public static function debug($message, string $category = self::CATEGORY_COMMON)
    {
        self::sendToLog($message, self::TYPE_DEBUG, $category);
    }

    /**
     * @return string
     */
    private static function getPageUrl()
    {
        if(isset($_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI'])) {
            if ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1))
                || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')
            ) {
                $protocol = 'https://';
            } else {
                $protocol = 'http://';
            }

            return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        } else {
            return "";
        }
    }
}
