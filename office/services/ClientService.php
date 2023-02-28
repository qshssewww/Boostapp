<?php

require_once __DIR__ . '/../Classes/Client.php';
require_once __DIR__ . '/../Classes/UserBoostappLogin.php';
require_once __DIR__ . '/../Classes/StudioBoostappLogin.php';
require_once __DIR__ . '/../Classes/City.php';
require_once __DIR__ . '/../Classes/Rank.php';
require_once __DIR__ . '/../Classes/Automation.php';
require_once __DIR__ . '/../Classes/LeadSource.php';
require_once __DIR__ . '/../Classes/Settings.php';
require_once __DIR__ . '/../Classes/Notificationcontent.php';
require_once __DIR__ . '/../Classes/AppNotification.php';
require_once __DIR__ . '/ClientActivityService.php';
require_once __DIR__ . '/GoogleCalendarService.php';
require_once __DIR__ . '/../../office/Classes/Rank.php';
require_once __DIR__ . '/LoggerService.php';


require_once __DIR__ . '/../Classes/PayToken.php';
require_once __DIR__ . '/../Classes/Clientcrm.php';
require_once __DIR__ . '/../Classes/Pipereasons.php';
require_once __DIR__ . '/../Classes/LeadStatus.php';
require_once __DIR__ . '/../Classes/ClientActivities.php';
require_once __DIR__ . '/../Classes/ClassStudioDate.php';
require_once __DIR__ . '/../Classes/ClassStudioDateRegular.php';
require_once __DIR__ . '/../Classes/ClassStudioAct.php';



class ClientService
{
    public const CLIENT_STATUS_ACTIVE = 0;
    public const CLIENT_STATUS_ARCHIVE = 1;
    public const CLIENT_STATUS_LEAD = 2;

    const MOBILE_REGEX_FRONT = "^(\+972|\+91|\+1|\+44)?0?5(0|1|2|3|4|5|8|9){1}(-){0,1}[0-9]{7}$";
    const MOBILE_REGEX = "/" . self::MOBILE_REGEX_FRONT . "/";


    /**
     * Adding client or lead
     * @param $data array Variable names like in DB
     * @param $clientStatus
     * @return array|string[]
     * @throws Exception
     */
    public static function addClient($data, $clientStatus = self::CLIENT_STATUS_ACTIVE)
    {
        // trim and normalize phone and email
        $isMinor = ($data["parentClientId"] ?? 0) != 0;
        $email = isset($data["Email"]) ? strtolower(trim($data["Email"])) : null;
        $data['areaCode'] = isset($data['areaCode']) ? trim($data['areaCode']) : "+972";
        $phone = (isset($data["ContactMobile"]) && !empty($data["ContactMobile"])) ? trim($data["areaCode"]) . ltrim($data["ContactMobile"], '0- ') : null;
        $CompanyNum = $data['CompanyNum'] ?? Auth::user()->CompanyNum;
        $ClientRanks = $data['ClientRanks'] ?? '';
        $addMembership = isset($data['Membership']) && $data['Membership'] != -1;
        $CompanyName = trim($data["FirstName"]) . ' ' . trim($data["LastName"]);

        $Brands = $data['Brands'] ?? 0;
        $SettingsInfo = Settings::getSettings($CompanyNum);
        $BrandsInfo = (new Brand())->getBrandByCompanyNumAndId($CompanyNum, $Brands);
        if ($Brands != 0 && $BrandsInfo->BrandName != '') {
            $BrandName = $BrandsInfo->BrandName;
        } else {
            $BrandName = lang('primary_branch');
        }

        // check input data
        $checkResult = self::checkClientDataInput($data);
        if (!$checkResult) {
            return ["Message" => "Input check failed", "Status" => "Error"];
        }
        if ($checkResult["Status"] != "Success") {
            return $checkResult;
        }

        if ($clientStatus == self::CLIENT_STATUS_LEAD) {
            $leadCheckResults = self::checkLeadDataInput($data);
            if (!$leadCheckResults) {
                return ["Message" => "Input check failed", "Status" => "Error"];
            }
            if ($leadCheckResults["Status"] != "Success") {
                return $leadCheckResults;
            }

            $mainPipeId = $post["pipeline_category_id"] ?? PipelineCategory::get_main_category($CompanyNum)->id;
            if (!PipelineCategory::check_pipeline_category_exists($CompanyNum, $mainPipeId)) {
                return ["Message" => "Invalid pipeline category", "Status" => "Error"];
            }
        }

        // database queries
        $AppPassword = $data["AppPassword"] ?? mt_rand(100000, 999999);         // for boostapp.client
        $userPassword = Hash::make(trim($AppPassword)); // for boostapplogin.users

        // fix format
        if (isset($data["AppLoginId"])) {
            // check if already with code
            if (strlen($data["AppLoginId"]) > 9) {
                $areaCode = trim(substr($data["AppLoginId"], 0, strlen($data["AppLoginId"]) - 9), "0- ");
                $areaCode = $areaCode ?: "+972";
                $data["AppLoginId"] = substr($data["AppLoginId"], -9);
            }

            $areaCode = trim($data["areaCode"] ?? $areaCode ?? '+972');
            $AppLoginId = $areaCode . ltrim($data["AppLoginId"], '0- ');
        }
        if (isset($data["ContactPhone"])) {
            // check if already with code
            if (strlen($data["ContactPhone"]) > 9) {
                $areaCode = trim(substr($data["ContactPhone"], 0, strlen($data["ContactPhone"]) - 9), "0- ");
                $areaCode = $areaCode ?: "+972";
                $data["ContactPhone"] = substr($data["ContactPhone"], -9);
            }

            $areaCode = trim($data["areaCode"] ?? $areaCode ?? '+972');
            $ContactPhone = $areaCode . ltrim($data["ContactPhone"], '0- ');
        }

        $clientData = [
            'CompanyNum' => $CompanyNum,
            'Brands' => $Brands,
            'CompanyName' => $CompanyName,
            'Company' => isset($data['Company']) ? trim($data['Company']) : null,
            'BusinessType' => $data['BusinessType'] ?? 1,
            'CompanyId' => isset($data["CompanyId"]) ? trim($data["CompanyId"]) : null,
            'Email' => $email,
            'Status' => $clientStatus == self::CLIENT_STATUS_LEAD ? 2 : $data["Status"] ?? $clientStatus,
            'ContactMobile' => $phone ?? null,
            'FirstName' => trim($data["FirstName"]),
            'LastName' => trim($data["LastName"]),
            'UserId' => $data["UserId"] ?? Auth::user()->id,
            'Vat' => $data['Vat'] ?? 0,
            'PaymentRole' => $data['PaymentRole'] ?? 1,
            'Street' => !empty($data['Street']) ? $data['Street'] : 0,
            'StreetH' => (isset($data['Street']) && $data['Street'] == '99999999') ? $data['StreetH'] ?? null : null,
            'Number' => isset($data["Number"]) ? trim($data["Number"]) : null,
            'PostCode' => isset($data["PostCode"]) ? trim($data["PostCode"]) : null,
            'POBox' => isset($data["POBox"]) ? trim($data["POBox"]) : null,
            'ContactPhone' => $ContactPhone ?? null,
            'ContactFax' => $data['ContactFax'] ?? null,
            'WebSite' => $data['WebSite'] ?? null,
            'Remarks' => $data['Remarks'] ?? null,
            'City' => !empty($data["City"]) ? $data["City"] : 0,
            'Flat' => isset($data["Flat"]) ? trim($data["Flat"]) : null,
            'Floor' => isset($data["Floor"]) ? trim($data["Floor"]) : null,
            'Entry' => isset($data["Entry"]) ? trim($data["Entry"]) : null,
            'GetSMS' => $data['GetSMS'] ?? 0,
            'GetEmail' => $data['GetEmail'] ?? 0,
            'PayClientId' => $data["parentClientId"] ?? 0,
            'AppLoginId' => $AppLoginId ?? $phone ?? 0,
            'AppPassword' => $AppPassword,
            'BrandName' => $BrandName,
            'additional_data' => isset($data["additional_data"]) ? json_encode($data["additional_data"]) : null,
            'parentClientId' => $data["parentClientId"] ?? 0,
            'relationship' => $data['relationship'] ?? 0,
        ];

        if (isset($data["Gender"])) {
            $clientData['Gender'] = $data["Gender"];
        }
        if (isset($data["Dob"]) && $data["Dob"] && $data["Dob"] != '0000-00-00') {
            $dob = DateTime::createFromFormat('Y-m-d', $data["Dob"]);
            $tz = new DateTimeZone('Asia/Jerusalem');

            $clientData['Dob'] = $dob->format('Y-m-d');
            $clientData['Age'] = $dob->diff(new DateTime('now', $tz))->y;
        }

        $clientId = (new Client())->insert_into_table($clientData);

        if (!empty($data['ClassLevel'])) {
            foreach ($data['ClassLevel'] as $level) {
                $class_ClientRank = new Rank();
                $class_ClientRank->ClientId = $clientId;
                $class_ClientRank->RankId = $level;
                $class_ClientRank->save();
            }
        }

        if (!empty($ClientRanks)) {
            (new Rank())->updateClientRank($clientId, explode(",", $ClientRanks), true);
        }

        if ($addMembership) {
            ClientActivityService::assignMembership([
                'clientId' => $clientId,
                'itemId' => $data['Membership'],
                'itemPrice' => $data['MembershipPrice'] ?? null,
            ]);
        }

        if ($clientStatus != self::CLIENT_STATUS_LEAD) {
            CreateLogMovement(lang('add_customer_manually_ajax'), $clientId);
        } else {
            $PipelineId = (new Pipeline())->insert_into_table([
                'MainPipeId' => $data['PipeLine'] ?? $data['MainPipeId'] ?? 0,
                'PipeId' => $data["PipeId"] ?? $data["Status"] ?? 0,
                'Brands' => $Brands,
                'ClientId' => $clientId,
                'CompanyName' => $CompanyName,
                'ContactInfo' => $phone ?? $ContactPhone ?? '',
                'UserId' => $data["UserId"] ?? Auth::user()->id,
                'ItemId' => $data["ItemId"] ?? '0',
                'CompanyNum' => $CompanyNum,
                'ClassInfo' => (isset($data['ClassInfo']) && $data['ClassInfo'] != '') ? $data['ClassInfo'] : 'BA999',
                'ClassInfoNames' => (isset($data['ClassInfoNames']) && $data['ClassInfoNames'] != '') ? $data['ClassInfoNames'] : lang('all_classes'),
                'BrandsNames' => $BrandName,
                'Source' => $data['Source'] ?? lang('without'),
                'SourceId' => $data['SourceId'] ?? 0,
                'AgentId' => $data['Agents'] ?? 0,
            ]);

            if (isset($data["parentClientId"]) && $data["parentClientId"] != 0) {
                CreateLogMovement(lang('minor_lead_ajax'), $clientId);
            } else {
                CreateLogMovement(lang('manually_lead_ajax'), $clientId);
            }
        }

        $newUsername = $isMinor ? "-1" : $phone ?? "-1";
        $AppLoginId = $isMinor ? $AppLoginId ?? null : $phone ?? $AppLoginId ?? null;

        // add to boostapplogin.users
        $UserId = self::checkBoostappLoginUser([
            "username" => $email ?? "",
            "email" => $email ?? "",
            "newUsername" => $newUsername,
            "password" => $userPassword,
            "display_name" => $CompanyName,
            "status" => '1', // always 1
            "FirstName" => trim($data["FirstName"]),
            "LastName" => trim($data["LastName"]),
            "ContactMobile" => $phone ?? $ContactPhone ?? "",
            "AppLoginId" => $AppLoginId,
            "parentId" => $data["parentClientId"] ?? 0,
        ]);

        // add to boostapplogin.studio
        $StudioId = self::checkBoostappLoginStudio($CompanyNum, $clientId, $UserId,
            $clientStatus == self::CLIENT_STATUS_LEAD ? 0 : $clientStatus);

        if (!$isMinor) {
            // send credentials notification
            $Date = date('Y-m-d');
            $Time = date('H:i:s');
            $Dates = $Date . " " . $Time;

            $Template = (new Notificationcontent())->getByTypeAndCompany($CompanyNum, 21);

            // determine if to send notification by client status filter from notification template
            $PerformNotificationDueToClientStatus = false;
            if($Template->SendClientsTypeOption == 0) $PerformNotificationDueToClientStatus = true;
            elseif($Template->SendClientsTypeOption == 1 && $clientStatus == 0) $PerformNotificationDueToClientStatus = true;
            elseif($Template->SendClientsTypeOption == 2 && $clientStatus == 2) $PerformNotificationDueToClientStatus = true;

            if($PerformNotificationDueToClientStatus && $Template->Status == 0){ /// עדכון תבנית הודעה
                $GooglePlayLink = $SettingsInfo->GooglePlayLink ?? 'https://play.google.com/store/apps/details?id=com.connect_computer.boostnew&gl=IL';
                $GooglePlay = '<a href="' . $GooglePlayLink . '">Google Play</a>';

                $AppStoreLink = $SettingsInfo->AppStoreLink ?? 'https://apps.apple.com/us/app/boost-%D7%91%D7%95%D7%A1%D7%98/id1479519489';
                $AppStore = '<a href="' . $AppStoreLink . '">App Store</a>';

                $Subject = $Template->Subject;
                $Content1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], $SettingsInfo->AppName, $Template->Content);
                $Content2 = str_replace(Notificationcontent::REPLACE_ARR["name_table"], $CompanyName, $Content1);
                $Content3 = str_replace(Notificationcontent::REPLACE_ARR["first_name"], trim($data["FirstName"]), $Content2);
                $Content4 = str_replace(Notificationcontent::REPLACE_ARR["username_single"], $email ?? '', $Content3);
                $Content5 = str_replace(Notificationcontent::REPLACE_ARR["password_single"], $AppPassword, $Content4);
                $Content6 = str_replace("App Store", $AppStore, $Content5);
                $Text = str_replace("Google Play", $GooglePlay, $Content6);

                AppNotification::insertGetId([
                    'CompanyNum' => $CompanyNum,
                    'ClientId' => $clientId,
                    'Type' => '2',
                    'Subject' => $Subject,
                    'Text' => $Text,
                    'Dates' => $Dates,
                    'UserId' => $UserId,
                    'Date' => $Date,
                    'Time' => $Time,
                    'priority' => 1
                ]);
            }
        }

        // run automation for non-archive client if studio has it and if membership isn't manually assigned
        if ($clientStatus != self::CLIENT_STATUS_ARCHIVE && !isset($data["noAutomation"]) && !$addMembership) {
            $AutomationInfo = Automation::getAutomation($CompanyNum, $clientStatus == self::CLIENT_STATUS_LEAD ? 2 : 1);

            if (!empty($AutomationInfo)) {
                ClientActivityService::assignMembership([
                    'clientId' => $clientId,
                    'itemId' => $AutomationInfo->Value,
                    'calcType' => $AutomationInfo->VaildType,
                ]);
            }
        }

        return ["Message" => [
            "client_id" => $clientId,
            "user_id" => $UserId,
            "studio_id" => $StudioId,
            "pipeline_id" => $PipelineId ?? null,
            "newUsername" => $isMinor ? "-1" : $phone ?? "-1"],
            "Status" => "Success",
        ];
    }

    /**
     * Create client and app user by mobile phone and full name
     * IMPORTANT: This function don't run automation
     * @param string $contactMobile Phone number including prefix (ONLY FOR ISRAEL PREFIX)
     * @param string $fullName Client Full name
     * @return array|string[]
     * @throws Exception
     */
    public static function addClientByPhoneAndName(string $contactMobile, string $fullName, $Brands = 0)
    {
        if (!isset($contactMobile)) {
            return ['Message' => lang('phone_number_required'), 'Status' => 'Error'];
        }
        if (!preg_match(self::MOBILE_REGEX, $contactMobile)) {
            return ['Message' => lang('phone_format_incorrect_ajax'), 'Status' => 'Error'];
        }

        $nameExploded = explode(" ", $fullName, 2);
        $phoneZone = trim(substr($contactMobile, 0, strlen($contactMobile) - 9), "0");
        // if empty (was '0' before trim), should replace with '+972'
        $phoneZone = $phoneZone ?: "+972";

        return self::addClient([
            "areaCode" => $phoneZone,
            "ContactMobile" => substr($contactMobile, -9),
            "FirstName" => $nameExploded[0],
            "LastName" => $nameExploded[1] ?? null,
            "Brands" => $Brands,
            "noAutomation" => true,                     // prevent running automation
        ]);
    }

    /**
     * @param $data
     * @return string[]
     */
    public static function checkClientDataInput($data)
    {
        // checks from Classes/Client.php - addClient
        if (empty($data["FirstName"])) {
            return ["Message" => lang('first_name_req_field'), "Status" => "Error"];
        }
        if (empty($data["LastName"])) {
            // new popup has FE check
            $data["LastName"] = ' ';
        }
        if (empty($data["ContactMobile"]) && !isset($data["parentClientId"])) {
            return ["Message" => lang('phone_req_field'), "Status" => "Error"];
        }
        if (isset($data["ContactMobile"]) && !empty($data["ContactMobile"]) && empty($data["areaCode"])) {
            return ["Message" => "areaCode is required", "Status" => "Error"];
        }
        if (isset($data["Email"]) && $data["Email"] && !filter_var($data["Email"], FILTER_VALIDATE_EMAIL)) {
            return ["Message" => lang('woring_email'), "Status" => "Error"];
        }
        if (isset($data["Dob"]) && $data["Dob"] && !DateTime::createFromFormat('Y-m-d', $data["Dob"])) {
            return ["Message" => "Dob is not valid", "Status" => "Error"];
        }
        if (isset($data["Gender"]) && ($data["Gender"] != 0 && $data["Gender"] != 1 && $data["Gender"] != 2)) {
            return ["Message" => "Gender must be 0, 1 or 2", "Status" => "Error"];
        }
        if (isset($data["parentClientId"]) && !is_numeric($data["parentClientId"])) {
            return ["Message" => "parent_id must be numeric", "Status" => "Error"];
        }

        if (isset($data["CompanyNum"])) {
            $companyNum = $data["CompanyNum"];
        } else {
            $companyNum = Auth::user()->CompanyNum;
        }

        $email = isset($data["Email"]) ? strtolower(trim($data["Email"])) : null;
        $phone = isset($data["ContactMobile"]) ? trim($data["areaCode"]) . ltrim($data["ContactMobile"], '0- ') : null;

        if (!isset($data["parentClientId"]) && (new Client())->isDuplicatePhoneEmail($companyNum, $phone, $email)) {
            return ["Message" => lang("mobile_exists_ajax"), "Status" => "Error"];
        }

        if (isset($data["parentClientId"]) && $data["parentClientId"] != 0) {
            $parent = Client::find($data["parentClientId"]);
            if (!$parent || $parent->CompanyNum != $companyNum) {
                return ["Message" => "Invalid parentClientId", "Status" => "Error"];
            }
        }


        // checks from ajax.Client.php - fun addClient
        if (isset($data["ContactMobile"]) && !empty($data["ContactMobile"]) && (!preg_match(self::MOBILE_REGEX, $data["ContactMobile"]))) {
            return ["Message" => lang('mobile_number_wrong_ajax'), "Status" => "Error"];
        }
        if (isset($data["Email"]) && !empty($data["Email"]) && !filter_var($data["Email"], FILTER_VALIDATE_EMAIL)) {
            return ["Message" => lang('woring_email'), "Status" => "Error"];
        }


        return ["Message" => "", "Status" => "Success"];
    }

    /**
     * @param $data
     * @return string[]
     */
    public static function checkLeadDataInput($data)
    {
        $CompanyNum = $data['CompanyNum'] ?? Auth::user()->CompanyNum;

        if (isset($data["pipeline_id"]) && !is_numeric($data["pipeline_id"])) {
            return ["Message" => "pipeline_id must be numeric", "Status" => "Error"];
        }

        // not sure what $data["lead_source"] can contain
        // if errors because of it, change to name which contains id
        $LSource = $data["lead_source"] ?? $data["SourceId"] ?? null;

        // SourceId = 0 is a special case (most likely not set, which is better)
        if (isset($LSource) && $LSource != 0) {
            if (is_numeric($LSource)) {
                $leadSource = LeadSource::find($LSource);
                if ($leadSource->__get("CompanyNum") != $CompanyNum) {
                    return json_encode(["Message" => "Invalid lead source", "Status" => "Error"]);
                } else {
                    $data["Source"] = $data["Source"] ?? $leadSource->__get("Title");
                }
            } else {
                return json_encode(["Message" => "Invalid lead source", "Status" => "Error"]);
            }
        }

        return ["Message" => "", "Status" => "Success"];
    }

    /**
     * @param $data
     * @return void|null
     */
    public static function checkBoostappLoginUser($data)
    {
        if ($data["newUsername"] != -1 || $data["AppLoginId"]) {
            // check if user exists by phone number
            if ($data["newUsername"] != -1) {
                $existingUser = UserBoostappLogin::find_by_phone($data["newUsername"]);
                if ($existingUser) {
                    $oldId = $existingUser->__get("id");

                    // update email and password
                    UserBoostappLogin::updateClient($oldId, [
                        'email' => $data["email"],
                        'password' => $data["password"],
                    ]);

                    return $oldId;
                }
            }

            // getting parent id in users table
            if ($data["parentId"] != 0) {
                $CompanyNum = $data['CompanyNum'] ?? Auth::user()->CompanyNum;
                $studio = StudioBoostappLogin::findByClientIdAndCompanyNum($data["parentId"], $CompanyNum);
                $data["parentId"] = $studio->UserId;
            }

            // creating new user using data if needed
            return UserBoostappLogin::insert_into_table($data);
        }
    }

    /**
     * @param $CompanyNum
     * @param $clientId
     * @param $UserId
     * @param int $Status
     * @return void
     */
    public static function checkBoostappLoginStudio($CompanyNum, $clientId, $UserId, $Status = 0)
    {
        // check if studio exists
        $existingStudio = StudioBoostappLogin::findByUserIdAndCompanyNum($UserId, $CompanyNum);

        if ($existingStudio) {
            // update ClientId
            StudioBoostappLogin::updateStudioById($existingStudio->id, [
                "ClientId" => $clientId,
                'LastDate' => date('Y-m-d'),
                'LastTime' => date('H:i:s'),
            ]);
            return $existingStudio->id;
        } else {
            // create new
            $settings = new Settings($CompanyNum);
            return StudioBoostappLogin::insert_into_table([
                "StudioUrl" => $settings->__get("StudioUrl"),
                "StudioName" => $settings->__get("AppName"),
                "CompanyNum" => $CompanyNum,
                "UserId" => $UserId,
                "BrandsMain" => $settings->__get("BrandsMain"),
                "Status" => $Status,  // 1 - for archive
                "ClientId" => $clientId,
                "Takanon" => 0,
                "Medical" => 0,
                "OS" => 0,
                'Memotag' => $settings->__get("Memotag") ?? 0,
                'Folder' => $settings->__get("Folder") ?? null,
                'LastDate' => date('Y-m-d'),
                'LastTime' => date('H:i:s')
            ]);
        }
    }

    /**
     * @param $companyNum
     * @param $phone
     * @return int 0- not found or not valid
     */
    public static function findByPhoneAndStudio($companyNum, $phone): int
    {
        $phoneShortFormat = PhoneHelper::shortPhoneNumber($phone);
        if($phoneShortFormat) {
            return Client::findByPhoneAndStudio($companyNum, $phoneShortFormat);
        }
        return 0;
    }

    /**
     *todo-moshe- ask!
     * copy this function from UpdateClientStatus.php
     * @param Client $Client
     * @param int $status
     * @param int $reasonId
     * @param string $reasonText
     * @return bool - true if status changed
     */
    public static function updateStatus(Client $Client, int $status, int $reasonId = 0, string $reasonText =''): bool
    {
        try {
            if ($Client === null) {
                Throw new Exception('Client not valid');
            }
            if((int)$Client->Status === $status) {
                Throw new Exception('Client status is already '.$status);
            }
            $userId = Auth::user()->id ?? 0;

            if ($status === Client::STATUS_ARCHIVE) {
                $reasonText = htmlspecialchars($reasonText);
            }
            $pipeLine = new Pipeline();
            $pipe = $pipeLine->checkPipeId($Client->id, $Client->CompanyNum);
            if ($pipe !== null) {
                if ($status === Client::STATUS_ARCHIVE) {
                    $leadFail = LeadStatus::getLeadStatuses($Client->CompanyNum, $pipe->MainPipeId, 2)[0];
                    $pipeLine->updatePipelineByClientId($Client->id, array('PipeId' => $leadFail->id));
                } else {
                    $leadSuccess = LeadStatus::getLeadStatuses($Client->CompanyNum, $pipe->MainPipeId, 1)[0];
                    $pipeLine->updatePipelineByClientId($Client->id, array('PipeId' => $leadSuccess->id));
                }
            }

            //update client status
            $Client->Status = $status;
            $Client->ArchiveDate = date('Y-m-d H:i:s');
            $Client->ArchiveReasonId = $reasonId > 0 ? $reasonId : null;
            if(!$Client->save()) {
                Throw new Exception('Client not saved');
            }

            //update client activity
            $ClientActivities = new ClientActivities();
            $ClientActivities->updateTableByClientId($Client->id, $Client->CompanyNum, array('ClientStatus' => $status));

            StudioBoostappLogin::updatebyClientAndCompany($Client->id, $Client->CompanyNum, array('Status' => $status));

            //only if change to archive
            if ($status === Client::STATUS_ARCHIVE) {
                GoogleCalendarService::disconnectClient($Client->id); // Google Calendar API

                //add to crm
                if ($reasonText !== '') {
                    $ClientCrm = new Clientcrm();
                    /** @var Pipereasons $PipReasons */
                    $PipReasons = Pipereasons::find($reasonId);
                    $reasonText = ($PipReasons !== null ? $PipReasons->Title : '') . ': <br>' . $reasonText;
                    $ClientCrm->addClientCrm($Client->id, $userId, $reasonText);
                }

                $classClassStudioAct = new ClassStudioAct();
                $classClassStudioDate = new ClassStudioDate();
                foreach ($classClassStudioAct->getClassesByFixClientIdAfterDate($Client->id, date('Y-m-d', strtotime('+ 1 day'))) as $DeletesActClass) {
                    $DeletesActClass->changeStatus('5');
                    ClientActivities::CancelClassReturnBalance($DeletesActClass, $Client->CompanyNum, 5);
                    ///// ספירת שיעורים
                    $ClassInfo = $classClassStudioDate->getClassesByGroupnumberStatusType($Client->CompanyNum, $DeletesActClass->GroupNumber, 0, 1);
                    if ($ClassInfo) {
                        $classClassStudioDate::updateClassRegistersCount($DeletesActClass->ClassId, $DeletesActClass->GroupNumber, $ClassInfo->Floor, $ClassInfo->StartDate);
                    }
                }
                CreateLogMovement(//FontAwesome Icon
                    lang('log_archived_ajax'), //LogContent
                    $Client->id //ClientId
                );

                $ClientActivities->updateClientActivityByStatus($Client->CompanyNum, $Client->id, 0, array('Status' => '2', 'Reason' => lang('moved_to_archive')));
                (new ClassStudioDateRegular())->deleteRegularClassesByClientId($Client->CompanyNum, $Client->id);
                $payToken = new PayToken();
                if ($payToken->isPaytokensByClient($Client->CompanyNum, $Client->id) > 0) {
                    $payToken->cancelAllPayTokens($Client->CompanyNum, $Client->id);
                }
            }
            CreateLogMovement(
                lang('log_client_status_ajax'),
                $Client->id
            );
        } catch (\Exception $e) {
            LoggerService::error($e, LoggerService::CATEGORY_CLIENT);
            return false;
        }
        return true;
    }



}
