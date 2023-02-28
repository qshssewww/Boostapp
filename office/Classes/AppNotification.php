<?php

require_once __DIR__ . "/Company.php";
require_once __DIR__ . "/Utils.php";
require_once __DIR__ . '/../../app/helpers/LinkHelper.php';
require_once __DIR__ . "/ClassStudioAct.php";
require_once __DIR__ . "/ClientActivities.php";
require_once __DIR__ . "/Client.php";
require_once __DIR__ . "/Notificationcontent.php";
require_once __DIR__ . "/AppSettings.php";
require_once __DIR__ . "/WhatsAppNotifications.php";
require_once __DIR__ . "/../../app/enums/NotificationContent/SendOption.php";

/**
 * @property $id
 * @property $CompanyNum
 * @property $ClientId
 * @property $Subject
 * @property $Text
 * @property $Dates
 * @property $UserId
 * @property $Status
 * @property $Type
 * @property $Date
 * @property $Time
 * @property $Results
 * @property $System
 * @property $SMSPrice
 * @property $SMSSumPrice
 * @property $StatusPay
 * @property $Count
 * @property $ChooseClass
 * @property $ClassId
 * @property $ReadStatus
 * @property $RoleId
 * @property $ClassIdStatus
 * @property $RandomUrl
 * @property $TrueClientId
 * @property $CalId
 * @property $CalDate
 * @property $FreeWatingList
 * @property $StatusFreeWatingList
 * @property $TrueWatingLimit
 * @property $SendStudioOption
 * @property $SendType
 * @property $EmailAddress
 * @property $PhoneNumber
 * @property $workerStatus
 * @property $workerStart
 * @property $workStatusDone
 * @property $priority
 */
class AppNotification extends \Hazzard\Database\Model
{
    protected $table = "boostapp.appnotification";

    public const TYPE_PUSH = 0;
    public const TYPE_SMS = 1;
    public const TYPE_EMAIL = 2;
    public const TYPE_NOTIFICATION_BADGE = 3;
    public const TYPE_EMAIL_STUDIO_OWNER = 4;

    public function setData($id) {
        $data = DB::table(self::getTable())->where("id", "=", $id)->first();
        if($data != null) {
            foreach ($data as $key => $value) {
                $this->__set($key, $value);
            }
        }
    }

    /**
     * @param $price double the price of a single sms
     * @param $chars int chars count per single sms
     * @param $size int The number of chars in the text
     * @return float|int
     */
    public function calcSmsTotalPrice($price,$chars,$size){
        $totalPrice = ceil($size / $chars);
        return $price * $totalPrice;
    }

    public static function sendClassCanceledByStudio($actId){
        $companyObj = Company::getInstance(false);
        $studioActObj = new ClassStudioAct($actId);

        $Template = DB::table('notificationcontent')->where('CompanyNum', '=', $companyObj->CompanyNum)->where('Type', '=', '18')->first();
        $ClientInfo = DB::table('client')->where('id', '=', $studioActObj->__get('FixClientId'))->where('CompanyNum', '=', $companyObj->CompanyNum)->first();

        $Content1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], $companyObj->AppName, $Template->Content);
        $Content2 = str_replace(Notificationcontent::REPLACE_ARR["name_table"], $ClientInfo->CompanyName, $Content1);
        $Content3 = str_replace(Notificationcontent::REPLACE_ARR["first_name"], $ClientInfo->FirstName, $Content2);
        $Content4 = str_replace(Notificationcontent::REPLACE_ARR["cal_new_class_type_name"], $studioActObj->__get('ClassName'), $Content3);
        $Content5 = str_replace(Notificationcontent::REPLACE_ARR["class_date_single"], date('d/m/Y', strtotime($studioActObj->__get('ClassDate'))), $Content4);
        $Text = str_replace(Notificationcontent::REPLACE_ARR["time_of_a_class"], date('H:i', strtotime($studioActObj->__get('ClassStartTime'))), $Content5);

        if($Template->Status != 1) {
            self::insertGetId(array(
                'CompanyNum' => $companyObj->CompanyNum, 
                'ClientId' => $studioActObj->__get('FixClientId'), 
                'Type' => self::TYPE_PUSH,
                'Subject' => $Template->Subject, 
                'Text' => $Text, 
                'Dates' => date('Y-m-d H:i:s'), 
                'UserId' => Auth::user()->id, 
                'Date' => date('Y-m-d'), 
                'Time' => date('H:i:s')
            ));
        }
    }

    public static function sendPermanentRegisterCanceled($registered){
        $companyObj = Company::getInstance(false);

        $text = '<p>'.$registered->FirstName.' '.lang('hello_ajax').',</p>';
        $text .= '<p>'.lang('regular_assignment').' '. $registered->ClassName.' '.lang('a_day').' '
        .$registered->ClassDay.' '.lang('and_in_time_cron').' '.$registered->ClassTime.' '.lang('cancelled_by_studio').'</p>';
        $text .= '<p>'.lang('best_regards') .', '.$companyObj->AppName.'</p>';
        $Subject = lang('regular_assign_cancelled');

        self::insertGetId(array(
            'CompanyNum' => $companyObj->CompanyNum,
            'ClientId' => $registered->ClientId,
            'Type' => self::TYPE_PUSH,
            'Subject' => $Subject,
            'Text' => $text,
            'Dates' => date('Y-m-d H:i:s'),
            'UserId' => Auth::user()->id,
            'Date' => date('Y-m-d'),
            'Time' => date('H:i:s')
        ));
    }

    public function getNotificationsBeforeDate($date,$limit){
        return DB::table(self::getTable())->where("Date","<",$date)->limit($limit)->get();
    }

    /**
     * @param $ids
     * @return mixed
     */
    public static function deleteBulk($ids)
    {
        return self::whereIn('id', $ids)->delete();
    }

    public function sendStandartNotification($CompanyNum, $ClientId, $AppName, $TemplateSendOption, $SendStudioOption, $Subject, $Content, $ItemText, $Date, $Dates, $Time)
    {
        $ClientInfo = (new Client())->getClientByCompanyAndId($CompanyNum, $ClientId);
        if (!$ClientInfo) return;

        $Type = self::getTypeByTemplateSendOption($TemplateSendOption);
        if ($Type == -1) return;

        /// עדכון תבנית הודעה
        $Content1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], $AppName, $Content);
        $Content2 = str_replace(Notificationcontent::REPLACE_ARR["name_table"], $ClientInfo->CompanyName, $Content1);
        $Content3 = str_replace(Notificationcontent::REPLACE_ARR["first_name"], $ClientInfo->FirstName,$Content2);
        $TextNotification = str_replace(Notificationcontent::REPLACE_ARR["customer_card_table_membership"], $ItemText, $Content3);

        if ($TemplateSendOption != 'BA000') {
            DB::table(self::getTable())->insertGetId([
                    'CompanyNum' => $CompanyNum,
                    'ClientId' => $ClientId,
                    'TrueClientId' => 0,
                    'Subject' => $Subject,
                    'Text' => $TextNotification,
                    'Dates' => $Dates,
                    'UserId' => 0,
                    'Type' => $Type,
                    'Date' => $Date,
                    'Time' => $Time
                ]
            );
        }

        if ($SendStudioOption != 'BA000') {
            DB::table(self::getTable())->insertGetId([
                    'CompanyNum' => $CompanyNum,
                    'Type' => self::TYPE_NOTIFICATION_BADGE,
                    'ClientId' => $ClientId,
                    'Subject' => $Subject,
                    'Text' => $TextNotification,
                    'Dates' => $Dates,
                    'UserId' => 0,
                    'RoleId' => 3,
                    'Date' => $Date,
                    'Time' => $Time,
                    'SendStudioOption' => $SendStudioOption
                ]
            );
        }
    }

    public static function sendRegistrationDetails($clientObj){
        $SettingsInfo = Company::getInstance();
        $Template = DB::table('notificationcontent')->where('CompanyNum', '=', $clientObj->CompanyNum)->where('Type', '=', '21')->first();

        ///עדכון תבנית הודעה
        $GooglePlayLink = 'https://play.google.com/store/apps/details?id=com.connect_computer.boostnew&gl=IL';
        $AppStoreLink = 'https://apps.apple.com/us/app/boost-%D7%91%D7%95%D7%A1%D7%98/id1479519489';

        if (!empty($SettingsInfo->__get('GooglePlayLink'))) {
            $GooglePlayLink = $SettingsInfo->__get('GooglePlayLink');
        }
        if (!empty($SettingsInfo->__get('AppStoreLink'))) {
            $AppStoreLink = $SettingsInfo->__get('AppStoreLink');
        }
        $AppStore = '<a href="' . $AppStoreLink . '">App Store</a>';
        $GooglePlay = '<a href="' . $GooglePlayLink . '">Google Play</a>';

        $Content1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], $SettingsInfo->__get('AppName'), $Template->Content);
        $Content2 = str_replace(Notificationcontent::REPLACE_ARR["name_table"], $clientObj->__get('CompanyName'), $Content1);
        $Content3 = str_replace(Notificationcontent::REPLACE_ARR["first_name"], $clientObj->__get('FirstName'), $Content2);
        $Content4 = str_replace(Notificationcontent::REPLACE_ARR["username_single"], $clientObj->__get('Email'), $Content3);
        $Content5 = str_replace(Notificationcontent::REPLACE_ARR["password_single"], $clientObj->__get('AppPassword'), $Content4);
        $Content6 = str_replace("App Store", $AppStore, $Content5);
        $Text = str_replace("Google Play", $GooglePlay, $Content6);

        DB::table(self::getTable())->insertGetId([
            'CompanyNum' => $clientObj->__get('CompanyNum'),
            'ClientId' => $clientObj->__get('id'),
            'Type' => self::TYPE_EMAIL,
            'Subject' => $Template->Subject,
            'Text' => $Text,
            'Dates' => date('Y-m-d H:i:s'),
            'UserId' => $clientObj->__get('UserId'),
            'Date' => date('Y-m-d'),
            'Time' => date('H:i:s'),
            'priority' => 1
        ]);
    }

    /**
     * @param $clientId int
     * @param $meetingName string
     * @param $meetingDate string
     * @param $guideName string
     * @return mixed
     */
    public static function sendMeetingStatusUpdateToClient($clientId, $meetingName, $meetingDate, $templateType, $meetingGroupOrdersId = null)
    {
        $user = Auth::user();

        $messageTemplate = Notificationcontent::meetingMessagesTemplates($user->CompanyNum, $templateType);

        if (!empty($messageTemplate) && $messageTemplate->Status == 0) {

            $firstName = Client::where('id', $clientId)->pluck('FirstName');

            $textContent = str_replace(Notificationcontent::REPLACE_ARR['first_name'], $firstName, $messageTemplate->Content);
            $textContent = str_replace(Notificationcontent::REPLACE_ARR['studio_name'], $messageTemplate->CompanyName, $textContent);
            $textContent = str_replace(Notificationcontent::REPLACE_ARR['meeting_name'], $meetingName, $textContent);
            $textContent = str_replace(Notificationcontent::REPLACE_ARR['date_of_meeting'], date('d/m', strtotime($meetingDate)), $textContent);
            $textContent = str_replace(Notificationcontent::REPLACE_ARR['time_of_meeting'], date('H:i', strtotime($meetingDate)), $textContent);
            if ($templateType == 29) {
                $textContent = str_replace(NotificationContent::REPLACE_ARR['doc_link'], LinkHelper::getTinyUrl(get_appboostapp_domain() . '/meeting-order.php?GetUrl=' . $messageTemplate->StudioUrl . '&order=' . $meetingGroupOrdersId), $textContent);
            }

            $sendOptions = $messageTemplate->SendOption == 'BA999' ? '0,1,2' : $messageTemplate->SendOption;

            $sendOptions = explode(',', $sendOptions);

            foreach ($sendOptions as $option) {
                if ($option != 'BA000') {
                    self::insert([
                        'CompanyNum' => $user->CompanyNum,
                        'ClientId' => $clientId,
                        'Subject' => $messageTemplate->Subject,
                        'Text' => $textContent,
                        'Dates' => date('Y-m-d H:i:s'),
                        'UserId' => $user->id,
                        'Type' => $option,
                        'Date' => date('Y-m-d'),
                        'Time' => date('H:i:s'),
                        'StatusPay' => 1
                    ]);
                }
            }
        }

        return 0;
    }

    /**
     * @param $membershipId
     * @return void
     */
    public static function sendMembershipFreeze($membershipId)
    {
        /** @var ClientActivities $membership */
        $membership = ClientActivities::find($membershipId);
        $CompanyNum = $membership->CompanyNum;

        $Template = Notificationcontent::getByTypeAndCompanyNum($CompanyNum, '19');
        if (!$Template || $Template->Status == 1) return;

        /** @var Client $ClientInfo */
        $ClientInfo = Client::find($membership->ClientId);

        $Text = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], Company::getInstance()->__get('AppName'), $Template->Content);
        $Text = str_replace(Notificationcontent::REPLACE_ARR["first_name"], $ClientInfo->FirstName, $Text);

        $Type = self::getTypeByTemplateSendOption($Template->SendOption);
        if ($Type == -1) return;

        // 9am - approved by Alex
        return self::insertGetId([
            'CompanyNum' => $CompanyNum,
            'ClientId' => $membership->ClientId,
            'Type' => $Type,
            'Subject' => $Template->Subject,
            'Text' => $Text,
            'Dates' => $membership->StartFreez . ' 09:00:00',
            'UserId' => Auth::user()->id,
            'Date' => $membership->StartFreez,
            'Time' => '09:00:00',
        ]);
    }

    /**
     * ContentType = 4
     * @param ClassStudioAct $GetWatingList
     * @param $ChooseClass
     * @param $StatusFreeWatingList
     * @param $TrueWatingLimit
     * @param $SendTimeReady
     * @return false|float|int|mixed|null
     */
    public static function sendWaitingListFree(ClassStudioAct $GetWatingList, $ChooseClass, $StatusFreeWatingList, $TrueWatingLimit, $SendTimeReady = null)
    {
        $AppSettings = AppSettings::getByCompanyNum($GetWatingList->CompanyNum);
        $SendTime = $SendTimeReady ?? AppSettings::checkSendTimeByCompanyNum($GetWatingList->CompanyNum);

        // current notif
        $Time = date("H:i:s", $SendTime);
        $Date = date("Y-m-d", $SendTime);

        $Template = Notificationcontent::getByTypeAndCompanyNum($GetWatingList->CompanyNum, 4);
        if ($Template->Status == '1' || $Template->SendOption == 'BA000') return null;

        /** @var Client $ClientInfo */
        $ClientInfo = Client::find($GetWatingList->TrueClientId == '0' ? $GetWatingList->ClientId : $GetWatingList->TrueClientId);

        $Type = self::getTypeByTemplateSendOption($Template->SendOption);

        $Content = str_replace(Notificationcontent::REPLACE_ARR['studio_name'], Settings::getSettings($GetWatingList->CompanyNum)->AppName ?? '', $Template->Content);
        $Content = str_replace(Notificationcontent::REPLACE_ARR["name_table"], $ClientInfo->CompanyName ?? '', $Content);
        $Content = str_replace(Notificationcontent::REPLACE_ARR["first_name"], $ClientInfo->FirstName ?? '', $Content);
        $Content = str_replace(Notificationcontent::REPLACE_ARR["meeting_name"], $GetWatingList->ClassName ?? '', $Content);
        $Content = str_replace(Notificationcontent::REPLACE_ARR["date_of_meeting"], date('d/m/Y', strtotime($GetWatingList->ClassDate)) ?? '', $Content);
        $Content = str_replace(Notificationcontent::REPLACE_ARR["time_of_meeting"], date('H:i', strtotime($GetWatingList->ClassStartTime)) ?? '', $Content);

        if ($Template->SendOption == SendOption::SEND_OPTION_WHATSAPP) {
            WhatsAppNotifications::sendWaitingListFree($GetWatingList, $Date, $Time);
        }

        self::insertGetId([
            'CompanyNum' => $GetWatingList->CompanyNum,
            'ClientId' => $ClientInfo->id,
            'TrueClientId' => '0',
            'Subject' => $Template->Subject,
            'Text' => $Content,
            'UserId' => '0',
            'Type' => $Type,
            'Dates' => $Date . " " . $Time,
            'Date' => $Date,
            'Time' => $Time,
            'ChooseClass' => $ChooseClass,
            'ClassId' => $GetWatingList->id,
            'FreeWatingList' => $AppSettings->FreeWatingList,
            'StatusFreeWatingList' => $StatusFreeWatingList,
            'TrueWatingLimit' => $TrueWatingLimit,
            'priority' => 1,
        ]);

        return AppSettings::getNextSendTimeByCompanyNum($GetWatingList->CompanyNum, $SendTime);
    }

    /**
     * @param $TemplateSendOption
     * @return int
     */
    public static function getTypeByTemplateSendOption($TemplateSendOption): int
    {
        if ($TemplateSendOption != 'BA000' && $TemplateSendOption != 'BA999' && $TemplateSendOption != SendOption::SEND_OPTION_WHATSAPP) {
            $myArray = explode(',', $TemplateSendOption);
            if (in_array(self::TYPE_PUSH, $myArray)) {
                return self::TYPE_PUSH;
            } elseif (in_array(self::TYPE_SMS, $myArray)) {
                return self::TYPE_SMS;
            } elseif (in_array(self::TYPE_EMAIL, $myArray)) {
                return self::TYPE_EMAIL;
            } else {
                return -1;
            }
        }

        return self::TYPE_PUSH;
    }

    /**
     * @param Client $ClientInfo
     * @param $DocUrl
     * @param $TypeDoc
     * @param Docs $DocsInfo
     * @return void
     */
    public static function sendReceipt(Client $ClientInfo, $DocUrl, $TypeDoc, Docs $DocsInfo)
    {
        $CompanyInfo = Company::getInstance();

        $Template = (new Notificationcontent())->getByTypeAndCompany($ClientInfo->CompanyNum, 23);

        if ($Template->SendOption == SendOption::SEND_OPTION_NONE) return;

        $FullLinks = 'https://new.boostapp.co.il/office/PDF/DocsClient.php?RandomUrl=' . $DocUrl . '&ClientId=' . $ClientInfo->id;
        $TrueFullLinks = get_tiny_url($FullLinks);
        $DocUrlTrue = '<a href="' . $TrueFullLinks . '">' . lang('view_doc_ajax') . '</a>';

        /** @var DocsTable $DocsTypeInfo */
        $DocsTypeInfo = DocsTable::find($TypeDoc);
        $TypeDocName = $DocsTypeInfo->TypeTitleSingle;

        $Subject = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], $CompanyInfo->AppName ?? '', $Template->Subject);
        $Subject = str_replace(Notificationcontent::REPLACE_ARR["doc_type"], $TypeDocName ?? '', $Subject);

        $NotificationAttrs = [
            'CompanyNum' => $ClientInfo->CompanyNum,
            'ClientId' => $ClientInfo->id,
            'TrueClientId' => '0',
            'Subject' => $Subject,
            'Dates' => date('Y-m-d H:i:s'),
            'Date' => date('Y-m-d'),
            'Time' => date('H:i:s'),
            'ClassId' => 0,
            'UserId' => 0
        ];

        if (SendOption::checkSendOption($Template->SendOption, SendOption::SEND_OPTION_MAIL)) {
            $TextNotification = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], $CompanyInfo->AppName, $Template->Content);
            $TextNotification = str_replace(Notificationcontent::REPLACE_ARR["name_table"], $ClientInfo->CompanyName ?? '', $TextNotification);
            $TextNotification = str_replace(Notificationcontent::REPLACE_ARR["first_name"], $ClientInfo->FirstName ?? '', $TextNotification);
            $TextNotification = str_replace(Notificationcontent::REPLACE_ARR["doc_number"], $DocsInfo->TypeNumber ?? '', $TextNotification);
            $TextNotification = str_replace(Notificationcontent::REPLACE_ARR["doc_type"], $TypeDocName ?? '', $TextNotification);
            $TextNotification = str_replace(Notificationcontent::REPLACE_ARR["doc_link"], $DocUrlTrue ?? '', $TextNotification);

            $Notification = new AppNotification($NotificationAttrs);

            $Notification->__set('Text', $TextNotification);
            $Notification->__set('Type', SendOption::SEND_OPTION_MAIL);
            $Notification->save();
        }

        // change text for SMS/Push
        $TextNotification = DocsService::getSharingMessageTemplate($CompanyInfo->AppName, $ClientInfo, null, $DocsInfo);
        $NotificationAttrs['Text'] = preg_replace("/RECEIPTLINK/", $TrueFullLinks, $TextNotification);

        if (SendOption::checkSendOption($Template->SendOption, SendOption::SEND_OPTION_PUSH)) {
            $Notification = new AppNotification($NotificationAttrs);

            $Notification->__set('Type', SendOption::SEND_OPTION_PUSH);
            $Notification->save();
        }

        if (SendOption::checkSendOption($Template->SendOption, SendOption::SEND_OPTION_SMS)) {
            $Notification = new AppNotification($NotificationAttrs);

            $Notification->__set('Type', SendOption::SEND_OPTION_SMS);
            $Notification->save();
        }
    }

}
