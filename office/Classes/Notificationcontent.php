<?php

require_once __DIR__ . '/../../app/enums/NotificationContent/SendOption.php';

use Hazzard\Database\Model;

/**
 * @property $id
 * @property $CompanyNum
 * @property $GroupType
 * @property $Type
 * @property $TypeName
 * @property $Subject
 * @property $Content
 * @property $Parameters
 * @property $Status
 * @property $SendOption
 * @property $SendStudioOption
 *
 * Class Notificationcontent
 */
class Notificationcontent extends Model
{
    protected $table = 'boostapp.notificationcontent';

    public const STATUS_ACTIVE = 0;
    public const STATUS_OFF = 1;

    public const SEND_OPTION_PRIORITY_DEFAULT = [
        SendOption::SEND_OPTION_NONE,
        SendOption::SEND_OPTION_ALL,
        SendOption::SEND_OPTION_PUSH,
        SendOption::SEND_OPTION_MAIL,
        SendOption::SEND_OPTION_SMS
    ];

    public const SEND_OPTION_PRIORITY_MAIL_SMS_PUSH = [
        SendOption::SEND_OPTION_NONE,
        SendOption::SEND_OPTION_ALL,
        SendOption::SEND_OPTION_MAIL,
        SendOption::SEND_OPTION_SMS,
        SendOption::SEND_OPTION_PUSH
    ];


    public const TYPE_SEND_INVOICE_OR_RECEIPT = 23;

    public const REPLACE_HEB_ARR = [
        "[[שם מלא]]",
        "[[שם פרטי]]",
        "[[שם העסק]]",
        "[[סוג מסמך]]",
        "[[מספר מסמך]]",
        "[[קישור למסמך]]",
        "[[סיבת ההחזרה]]",
        "[[לחץ כאן]]",
        "[[תוקף]]",
        "[[תוקף חדש]]",
        "[[מספר ימים]]",
        "[[שם המנוי]]",
        "[[שם משתמש]]",
        "[[שם האירוע]]",
        "[[תאריך האירוע]]",
        "[[שעת האירוע]]",
        "[[סיסמה]]",
        "[[קישור]]",
        "[[תוקף המנוי]]",
        "[[כותרת מנוי]]",
        "[[שם הנציג]]",
        "[[שם נציג מלא]]",
        "[[שם האירוע שהוזמן]]",
        "[[שעת האירוע שהוזמן]]",
    ];


    public const REPLACE_ARR = [
        "name_table" => "[[שם מלא]]",
        "first_name" => "[[שם פרטי]]",
        "studio_name" => "[[שם העסק]]",
        "doc_type" => "[[סוג מסמך]]",
        "doc_number" => "[[מספר מסמך]]",
        "doc_link" => "[[קישור למסמך]]",
        "declined_reason" => "[[סיבת ההחזרה]]",
        "click_here" => "[[לחץ כאן]]",
        "store_period" => "[[תוקף]]",
        "new_validity" => "[[תוקף חדש]]",
        "days_number" => "[[מספר ימים]]",
        "subscription_name" => "[[שם המנוי]]",
        "username_single" => "[[שם משתמש]]",
        "cal_new_class_type_name" => "[[שם האירוע]]",
        "class_date_single" => "[[תאריך האירוע]]",
        "time_of_a_class" => "[[שעת האירוע]]",
        "meeting_name" => "[[שם האירוע]]",
        "date_of_meeting" => "[[תאריך האירוע]]",
        "time_of_meeting" => "[[שעת האירוע]]",
        "password_single" => "[[סיסמה]]",
        "link" => "[[קישור]]",
        "membership_expire_date" => "[[תוקף המנוי]]",
        "customer_card_table_membership" => "[[כותרת מנוי]]",
        "representative_name" => "[[שם הנציג]]",
        "full_representative_name" => "[[שם נציג מלא]]",
        "booked_class_name" => "[[שם האירוע שהוזמן]]",
        "booked_class_time" => "[[שעת האירוע שהוזמן]]",
    ];

    /**
     * @param $CompanyNum
     * @param $type
     * @return Notificationcontent|null
     */
    public function getByTypeAndCompany($CompanyNum, $type): ?Notificationcontent
    {
        return self::where('CompanyNum', '=', $CompanyNum)
            ->where('Type', '=', $type)
            ->first();
    }

    /**
     * @param string $content
     * @return string | null
     */
    public static function generateBtnsFromContent(string $content)
    {
        $replaceContent = [];
        foreach (self::REPLACE_ARR as $key => $exp) {
            $replaceContent[] = '<input type="button" class="btn btn-sm btn-rounded btn-light" value="' . lang($key) . '">';
        }
        return str_replace(self::REPLACE_HEB_ARR, $replaceContent, $content);
    }

    /**
     * @return Notificationcontent[]
     */
    public static function getContentsByCompanyNum(): array
    {
        return self::where('CompanyNum', Auth::user()->CompanyNum)
            ->orderBy('GroupType', 'ASC')->get();
    }

    /**
     * @param $content
     * @return array|string|string[]
     */
    public static function rollbackBtns($content)
    {
        $replaced = [];
        foreach (self::REPLACE_ARR as $key => $exp) {
            $replaced[] = '<input type="button" class="btn btn-sm btn-rounded btn-light" value="' . lang($key) . '">';
        }
        return str_replace($replaced, self::REPLACE_HEB_ARR, $content);
    }

    /**
     * @param $content
     * @return array
     */
    public function getBtnsNamesByType($content)
    {
        $buttons = [];
        $defaults = ["name_table", "first_name", "studio_name"];
        foreach (self::REPLACE_ARR as $key => $exp) {
            if (str_contains($content, $exp) && !in_array($key, $defaults)) {
                $buttons[] = $key;
            }
        }
        return $buttons;
    }

    /**
     * @param $content
     * @param $arr
     * @return array|string|string[]
     */
    public static function replaceDynamicContent($content, $arr)
    {
        $replace = [];
        foreach (self::REPLACE_HEB_ARR as $exp) {
            if (str_contains($content, $exp)) {
                $replace[] = $exp;
            }
        }
        return str_replace($replace, $arr, $content);
    }


    /**
     * @param int $companyNum
     * @param int $type
     * @return Notificationcontent|null
     */
    public static function getByTypeAndCompanyNum(int $companyNum, int $type): ?Notificationcontent
    {
        return self::where('CompanyNum', '=', $companyNum)
            ->where('Type', '=', $type)
            ->where('Status', '=', self::STATUS_ACTIVE)
            ->first();
    }

    /**
     * Sort the array according to the priorities, and if necessary change the code according to send_option_enum
     * @param array $sentOptionArray
     * @param bool $changeToAppNotificationType
     * @param array $costumeOrder
     * @return array
     */
    private static function sortSentOptionArray(array $sentOptionArray,
                                         $changeToAppNotificationType = false,
                                         array $costumeOrder = self::SEND_OPTION_PRIORITY_DEFAULT): array
    {
        $response = [];
        foreach ($costumeOrder as $sentOption) {
            if(in_array($sentOption, $sentOptionArray)) {
                $response[] = $changeToAppNotificationType ? SendOption::appNotificationType($sentOption) : $sentOption;
            }
        }
        return $response;
    }


    /**
     * returns the send code (appNotificationType) by send_option_enum According to priority and after inspection given according to the customer's definition
     * @param Client|null $Client
     * @param array $costumeOrder
     * @return ?int null-not need to send
     */
    public function getSendOptionCode(?Client $Client = null, array $costumeOrder = self::SEND_OPTION_PRIORITY_DEFAULT): ?int
    {
        $sentOptionArray = explode(',', $this->SendOption);
        if(empty($sentOptionArray)) {
            return null;
        }
        $sentOptionArray = self::sortSentOptionArray($sentOptionArray, false, $costumeOrder);
        foreach ($sentOptionArray as $sentOption) {
            switch ($sentOption) {
                case SendOption::SEND_OPTION_NONE:
                    return null;
                case SendOption::SEND_OPTION_ALL:
                    $costumeOrderAddToArray = array_diff($costumeOrder, [SendOption::SEND_OPTION_NONE, SendOption::SEND_OPTION_ALL]);
                    $sentOptionArray[] = $costumeOrderAddToArray;
                    break;
                case SendOption::SEND_OPTION_MAIL:
                    if($Client === null || ($Client->hasEmail() && (int)$Client->GetEmail === Client::GET_MAIL_STATUS_ACTIVE) ) {
                        return SendOption::appNotificationType($sentOption);
                    }
                    break;
                case SendOption::SEND_OPTION_SMS:
                    if($Client === null || (int)$Client->GetSMS === Client::GET_SMS_STATUS_ACTIVE ) {
                        return SendOption::appNotificationType($sentOption);
                    }
                    break;
                default:
                    return SendOption::appNotificationType($sentOption);;

            }
        }
        return null;
    }

    /**
     * @param $companyNum
     * @param $templateType
     * @return mixed
     */
    public static function meetingMessagesTemplates($companyNum, $templateType) {
        return self::select(
            'notificationcontent.Subject as Subject',
            'notificationcontent.Content as Content',
            'notificationcontent.Status as Status',
            'notificationcontent.SendOption as SendOption',
            's.CompanyName as CompanyName',
            's.StudioUrl as StudioUrl'
        )
            ->where('notificationcontent.CompanyNum', $companyNum)
            ->leftJoin('settings as s', 's.CompanyNum', '=', 'notificationcontent.CompanyNum')
            ->where('notificationcontent.Type', $templateType)
            ->where('notificationcontent.Status', 0)
            ->first();
    }

}
