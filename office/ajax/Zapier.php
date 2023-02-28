<?php
require_once '../../app/init.php';
require_once '../Classes/Client.php';
require_once '../Classes/Settings.php';
require_once '../Classes/Brand.php';
require_once '../Classes/AppNotification.php';
require_once '../Classes/PipelineCategory.php';
require_once '../Classes/LeadStatus.php';
require_once '../Classes/Pipeline.php';
require_once '../Classes/Pipereasons.php';
require_once '../Classes/Functions.php';
require_once '../Classes/StudioBoostappLogin.php';
require_once '../Classes/UserBoostappLogin.php';
require_once '../Classes/Clientcrm.php';
require_once '../Classes/Notificationcontent.php';
require_once '../services/LoggerService.php';
require_once '../services/ClientService.php';

$dataJson = json_decode(file_get_contents('php://input'), true);

if(!isset($dataJson)){
    $dataJson = $_POST;
}

function getZapierReasonId($companyNum){
    $zapierPipeReason = Pipereasons::getZapierReasonId($companyNum);

    if(empty($zapierPipeReason)){
        $zapierPipeReason = new Pipereasons();
        $zapierPipeReason->Title = 'Zapier';
        $zapierPipeReason->CompanyNum = $companyNum;
        $zapierPipeReason->Status = 1;
        $zapierPipeReason->save();
    }

    return $zapierPipeReason->id;
}


if(isset($dataJson)){
    try {
        if (!empty($dataJson["fun"])) {
            $clientObj = new Client();
            $utils = new Utils();
            $function = new Functions();
            if (!isset($dataJson["CompanyNum"])) {
                throw new Exception("CompanyNum is required",400);
                //echo json_encode(["Message" => "CompanyNum is required", "Status" => "Failed"]);
            }
            if(!is_numeric($dataJson["CompanyNum"])){
                $company = DB::table("boostapp.settings")->where("StudioUrl",$dataJson["CompanyNum"])->first();
                $dataJson["CompanyNum"] = $company->CompanyNum;
            }
            switch ($dataJson["fun"]) {
                case "addClient":
                    $res = ClientService::addClient([
                        "CompanyNum" => $dataJson["CompanyNum"],
                        "Status" => $dataJson["Status"],
                        "ContactMobile" => $dataJson["ContactMobile"],
                        "FirstName" => $dataJson["FirstName"],
                        "LastName" => $dataJson["LastName"],
                        "Email" => $dataJson["Email"],
                        "Brands" => $dataJson["Branch"] ?? 0,
                        "Gender" => $dataJson["Gender"] ?? 0,
                        "Dob" => $dataJson["Dob"] ?? null,
                    ]);

                    if ($res['Status'] != 'Success') {
                        throw new Exception($res['Message'], 400);
                    }

                    echo json_encode(["Message" => "Success", "StatusCode" => 201, "Status" => true]);
                    break;
                case "changeClientStatus" :
                    if (!isset($dataJson["CompanyNum"])) {
                        throw new Exception("CompanyNum is required",400);
                        //echo json_encode(["Message" => "CompanyNum is required", "Status" => "Failed"]);
                    } elseif (!isset($dataJson["id"]) && !isset($dataJson["phone"]) && !isset($dataJson["email"])) {
                        throw new Exception("requires id or phone or email",400);
                        //echo json_encode(["Message" => "id is required", "Status" => "Failed"]);
                    } elseif (!isset($dataJson["Status"]) && $dataJson["Status"] != 1 && $dataJson["Status"] != 0 && $dataJson["Status"] != 2) {
                        throw new Exception("Status is not valid",400);
                        //echo json_encode(["Message" => "Status is not valid", "Status" => "Failed"]);
                    } else {
                        if(isset($dataJson["id"])){
                            $client = $clientObj->getClientByCompanyAndId($dataJson["CompanyNum"], $dataJson["id"]);
                        }
                        else if(isset($dataJson["phone"])){
                            $client = $function->getClientBy($dataJson["phone"],$dataJson["CompanyNum"]);
                        }
                        else{
                            $client = $function->getClientBy($dataJson["email"],$dataJson["CompanyNum"]);
                        }
                        //$client = $clientObj->getClientByCompanyAndId($dataJson["CompanyNum"], $dataJson["id"]);
                        $appUserStudio = StudioBoostappLogin::findByClientIdAndCompanyNum($dataJson["id"], $dataJson["CompanyNum"]);
                        if (!$client || !$appUserStudio) {
                            throw new Exception("Client not Exists",400);
                            //echo json_encode(["Message" => "Client not Exists", "Status" => "Failed"]);
                            break;
                        }
                        if ($client->Status == $dataJson["Status"]) {
                            echo json_encode(["Message" => "Status", "Status" => true, "StatusCode" => 200]);
                            break;
                        }


                        $clientObj->updateClient($dataJson["id"], array("Status" => $dataJson["Status"], 'ArchiveReasonId' => getZapierReasonId($dataJson['CompanyNum'])));

                        if ($dataJson["Status"] == 0) {
                            UserBoostappLogin::updateClient($appUserStudio->UserId, array("Status", "=", 1));
                        } elseif ($dataJson["Status"] == 1) {
                            UserBoostappLogin::updateClient($appUserStudio->UserId, array("Status", "=", 0));

                            $clientCrm = new Clientcrm();
                            $clientCrmRow = $clientCrm->addClientCrm($dataJson["ClientId"], $appUserStudio->UserId, 'Zapier');
                        }
                        echo json_encode(["Message" => "Success", "Status" => true, "StatusCode" => 200]);
                    }
                    break;
                case "sendMessage":
                    if (!isset($dataJson["CompanyNum"])) {
                        throw new Exception("CompanyNum is required",400);
                        break;
                        //echo json_encode(["Message" => "CompanyNum is required", "Status" => "Failed"]);
                    } else if (!isset($dataJson["ClientId"]) && !isset($dataJson["phone"]) && !isset($dataJson["email"])) {
                        throw new Exception("requires clientId or phone or email",400);
                        break;
                        //echo json_encode(["Message" => "clientId is required", "Status" => "Failed"]);
                    } else if (!isset($dataJson["Text"])) {
                        throw new Exception("Text is required",400);
                        break;
                        //echo json_encode(["Message" => "Text is required", "Status" => "Failed"]);
                    } else if (!isset($dataJson["Type"]) && $dataJson["Type"] != 1 && $dataJson["Type"] != 0 && $dataJson["Type"] != 2) {
                        throw new Exception("Type is required",400);
                        break;
                        //echo json_encode(["Message" => "Type is required", "Status" => "Failed"]);
                    } else {
                        if(isset($dataJson["ClientId"])){
                            $client = $clientObj->getClientByCompanyAndId($dataJson["CompanyNum"], $dataJson["ClientId"]);
                        }
                        else if(isset($dataJson["phone"])){
                            $phone = $function->checkMobile($dataJson["phone"]);
                            if($phone) {
                                $client = $function->getClientBy($dataJson["phone"], $dataJson["CompanyNum"]);
                            }
                            else{
                                throw new Exception("Phone is invalid",400);
                                break;
                            }
                        }
                        else{
                            $client = $function->getClientBy($dataJson["email"],$dataJson["CompanyNum"]);
                        }
                        //$client = $clientObj->getClientByCompanyAndId($dataJson["CompanyNum"], $dataJson["clientId"]);
                        if (!$client) {
                            throw new Exception("Client not Exists",400);
                            //echo json_encode(["Message" => "Client not Exists", "Status" => "Failed"]);
                            break;
                        }
                        $settings = new Settings($dataJson["CompanyNum"]);
                        $notification = new AppNotification();
                        $data = array(
                            "CompanyNum" => $dataJson["CompanyNum"],
                            "ClientId" => $dataJson["ClientId"],
                            "Text" => $dataJson["Text"],
                            "Type" => $dataJson["Type"],
                            "Date" => date("Y-m-d"),
                            "Time" => date("H:i:s"),
                            "Dates" => date("Y-m-d H:i:s"),
                        );
                        if ($settings->__get("id") == null) {
                            throw new Exception("Company not exists",400);
                            //echo json_encode(["Message" => "Company not exists", "Status" => "Failed"]);
                            break;
                        }
                        if ($dataJson["Type"] == 1 && $settings->__get("SMSPrice") != 0) {
                            $textSize = strlen($dataJson["Text"]);
                            $smsPrice = $notification->calcSmsTotalPrice($settings->__get("SMSPrice"), $settings->__get("SMSLimit"), $textSize);
                            $data["SMSPrice"] = $settings->__get("SMSPrice");
                            $data["SMSSumPrice"] = $smsPrice;
                        }
                        if ($dataJson["Type"] == 2) {
                            if (!isset($dataJson["Subject"])) {
                                throw new Exception("Subject is required",400);
                                //echo json_encode(["Message" => "Subject is required", "Status" => "Failed"]);
                                break;
                            }
                        }
                        if (isset($dataJson["Subject"]) && $dataJson["Subject"] != "") {
                            $data["Subject"] = $dataJson["Subject"];
                        }
                        if (isset($dataJson["Time"])) {
                            if (preg_match("/^(?:2[0-3]|[01][0-9]):[0-5][0-9]$/", $dataJson["Time"])) {
                                $data["Time"] = $dataJson["Time"];
                            } else if (preg_match('#^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$#', $dataJson["Time"])) {
                                $data["Time"] = $dataJson["Time"];
                            }
                        }
                        if (isset($dataJson["Date"])) {
                            if (strtotime($dataJson["Date"])) {
                                $data["Date"] = date("Y-m-d", strtotime($dataJson["Date"]));
                            }
                        }
                        $rowId = AppNotification::insertGetId($data);
                        if ($rowId != 0 && $rowId != null && $rowId != "") {
                            echo json_encode(["Message" => "Success", "Status" => true, "StatusCode" => 200]);
                        }
                        else{
                            throw new Exception("Action Failed",400);
                            break;
                        }
                    }
                    break;

                case "getLeadsCategoriesAndStatus":
                    if (!isset($dataJson["CompanyNum"])) {
                        throw new Exception("CompanyNum is required" ,400);
                        //echo json_encode(["Message" => "CompanyNum is required", "Status" => "Failed"]);
                    } else {
                        $pipelineCategories = PipelineCategory::getPipelineCategories($dataJson["CompanyNum"]);
                        $pipelineCategories = $utils->createArrayFromObjArr($pipelineCategories);
                        $data = array();
                        foreach ($pipelineCategories as $ind => $category) {
                            $leadStatus = LeadStatus::getLeadStatuses($dataJson["CompanyNum"], $category["id"],0);
                            $pipelineCategories[$ind]["statuses"] = $utils->createArrayFromObjArr($leadStatus);
                        }
                        echo json_encode(["Message" => "Success", "Status" => true, "StatusCode" => 200, "data" =>$pipelineCategories], JSON_UNESCAPED_UNICODE);
                    }
                    break;
                case "changeLeadsStatus":
                    if (!isset($dataJson["CompanyNum"])) {
                        throw new Exception("CompanyNum is required",400);
                        //echo json_encode(["Message" => "CompanyNum is required", "Status" => "Failed"]);
                    } else if (!isset($dataJson["ClientId"])) {
                        throw new Exception("ClientId is required",400);
                        //echo json_encode(["Message" => "PipeId is required", "Status" => "Failed"]);
                    } else if (!isset($dataJson["PipeCategory"])) {
                        throw new Exception("PipeCategory is required",400);
                        //echo json_encode(["Message" => "PipeCategory is required", "Status" => "Failed"]);
                    } else if (!isset($dataJson["LeadStatus"])) {
                        throw new Exception("LeadStatus is required",400);
                        //echo json_encode(["Message" => "LeadStatus is required", "Status" => "Failed"]);
                    } else {
                        $pipelineCategory = PipelineCategory::check_pipeline_category_exists($dataJson["CompanyNum"], $dataJson["PipeCategory"]);
                        if (!$pipelineCategory) {
                            throw new Exception("PipeCategory Not exists", 400);
                            //echo json_encode(["Message" => "PipeCategory Not exists", "Status" => "Failed"]);
                            break;
                        }
                        $leadStatus = LeadStatus::check_lead_status($dataJson["CompanyNum"], $dataJson["PipeCategory"], $dataJson["LeadStatus"]);
                        if (!$leadStatus) {
                            throw new Exception("LeadStatus Not exists", 400);
                            //echo json_encode(["Message" => "LeadStatus Not exists", "Status" => "Failed"]);
                            break;
                        }

                        $pipeline = new Pipeline();
                        $checkPipe = $pipeline->checkPipeId($dataJson["ClientId"], $dataJson["CompanyNum"]);
                        if (!$checkPipe) {
                            throw new Exception("ClientId Not Invalid", 400);
                            //echo json_encode(["Message" => "LeadStatus Not exists", "Status" => "Failed"]);
                            break;
                        }
                        $data = array(
                            "MainPipeId" => $dataJson["PipeCategory"],
                            "PipeId" => $dataJson["LeadStatus"],
                            "FreeText" => $dataJson["FreeText"],
                        );
                        if ($leadStatus->Act == 1) {
                            $SettingsInfo = DB::table('settings')->where('CompanyNum', '=',  $dataJson["CompanyNum"])->first();
                            $data["ConvertDate"] = date("Y-m-d H:i:s");
                            $client = $clientObj->getRow($dataJson["ClientId"]);
                            $clientUp = $clientObj->updateClient($dataJson["ClientId"], ["Status" => 0, "ConvertDate" => date("Y-m-d H:i:s")]);
                            $phone = $function->checkMobile($client->ContactMobile);
                            $AppUsers = DB::table('boostapplogin.users')->where('newUsername', '=', $phone)->first();
                            $MakeRandomPass = mt_rand(100000, 999999);
                            $password = Hash::make($MakeRandomPass);
                            if (!empty($AppUsers)) {
                                $AppStudio = DB::table('boostapplogin.studio')->where('CompanyNum', $dataJson["CompanyNum"])->where('ClientId', $dataJson["ClientId"])->first();
                                if (!empty($AppStudio)) {
                                    DB::table('boostapplogin.studio')
                                        ->where('id', $AppStudio->id)
                                        ->where('CompanyNum', $dataJson["CompanyNum"])
                                        ->update(array('UserId' => $AppUsers->id, 'Status' => '0', 'Takanon' => $client->Takanon, 'Medical' => $client->Medical));
                                } else { /// הקם חדש
                                    if ($client) {
                                        $AppStudio = DB::table('boostapplogin.studio')->insertGetId(
                                            array('StudioUrl' => $SettingsInfo->StudioUrl, 'StudioName' => $SettingsInfo->AppName, 'CompanyNum' => $dataJson["CompanyNum"], 'UserId' => $AppUsers->id, 'ClientId' => $dataJson["ClientId"], 'Memotag' => $SettingsInfo->Memotag, 'Folder' => $SettingsInfo->Folder, 'Takanon' => $client->Takanon, 'Medical' => $client->Medical));
                                    }
                                }
                                DB::table('boostapplogin.users')
                                    ->where('id', $AppUsers->id)
                                    ->update(array('email' => ($client->Email == null) ? "" : $client->Email, 'PassAct' => '0'));
                            }
                            else { /// שינוי יוזר באפלקיציה

                                $AppUserId = DB::table('boostapplogin.users')->insertGetId(
                                    array('username' =>  $client->Email, 'email' =>  $client->Email, "newUsername" => $phone, 'password' => $password, 'display_name' => $client->CompanyName, 'FirstName' => $client->FirstName, 'LastName' => $client->LastName, 'ContactMobile' => $client->ContactMobile, 'AppLoginId' => $client->ContactMobile, 'status' => 1, 'PassAct' => 0) );

                                $AppStudio = DB::table('boostapplogin.studio')->where('CompanyNum', $dataJson["CompanyNum"])->where('ClientId', $client->id)->first();

                                if (!empty($AppStudio)) {

                                    DB::table('boostapplogin.studio')
                                        ->where('id', $AppStudio->id)
                                        ->where('CompanyNum', $dataJson["CompanyNum"])
                                        ->update(array('UserId' => $AppUserId, 'Status' => '0', 'Takanon' => $client->Takanon, 'Medical' => $client->Medical));

                                } else { ////  הקם חדש
                                    if ($AppUserId!= 0 &&  $client->id != '0') {
                                        $AppStudio = DB::table('boostapplogin.studio')->insertGetId(
                                            array('StudioUrl' => $SettingsInfo->StudioUrl, 'StudioName' => $SettingsInfo->AppName, 'CompanyNum' => $dataJson["CompanyNum"], 'UserId' => $AppUserId, 'ClientId' =>  $client->id, 'Memotag' => $SettingsInfo->Memotag, 'Folder' => $SettingsInfo->Folder, 'Takanon' => $client->Takanon, 'Medical' => $client->Medical) );
                                    }
                                }
                                $Date = date('Y-m-d');
                                $Time = date('H:i:s');

                                $Template = DB::table('notificationcontent')->where('CompanyNum', '=' ,  $dataJson["CompanyNum"])->where('Type', '=' , '21')->first();
                                /// עדכון תבנית הודעה

                                // determine if to send notification by client status filter from notification template
                                $PerformNotificationDueToClientStatus = false;
                                if($Template->SendClientsTypeOption == 0) $PerformNotificationDueToClientStatus = true;
                                elseif($Template->SendClientsTypeOption == 1 && $client->Status == 0) $PerformNotificationDueToClientStatus = true;
                                elseif($Template->SendClientsTypeOption == 2 && $client->Status == 2) $PerformNotificationDueToClientStatus = true;

                                $GooglePlayLink = 'https://play.google.com/store/apps/details?id=com.connect_computer.boostnew&gl=IL';
                                $AppStoreLink = 'https://apps.apple.com/us/app/boost-%D7%91%D7%95%D7%A1%D7%98/id1479519489';

                                if (!empty($SettingsInfo->GooglePlayLink)){
                                    $GooglePlayLink = $SettingsInfo->GooglePlayLink;
                                }
                                if (!empty($SettingsInfo->AppStoreLink)){
                                    $AppStoreLink = $SettingsInfo->AppStoreLink;
                                }

                                $AppStore = '<a href="'.$AppStoreLink.'">App Store</a>';
                                $GooglePlay = '<a href="'.$GooglePlayLink.'">Google Play</a>';

                                $Content1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], $SettingsInfo->AppName,$Template->Content);
                                $Content2 = str_replace(Notificationcontent::REPLACE_ARR["name_table"], $client->CompanyName,$Content1);
                                $Content3 = str_replace(Notificationcontent::REPLACE_ARR["first_name"], $client->FirstName,$Content2);
                                $Content4 = str_replace(Notificationcontent::REPLACE_ARR["username_single"],@$client->Email,$Content3);
                                $Content5 = str_replace(Notificationcontent::REPLACE_ARR["password_single"], $MakeRandomPass,$Content4);
                                $Content6 = str_replace("App Store", $AppStore,$Content5);
                                $Content7 = str_replace("Google Play", $GooglePlay,$Content6);
                                $sendType = 2;

                                $Text = $Content7; /// משיכת הודעת ביטול שיעור מבסיס הנתונים
                                $Subject = $Template->Subject;
                                if(empty($Email)) {
                                    $sendType = 1;

                                    $Text = 'היי '.$client->CompanyName. ',
                                    ניתן להתחבר לאפליקציה באמצעות אימות טלפוני קצר
                                    להורדת האפליקציה:  https://new.boostapp.co.il/AppLink.php?StudioUrl='.$SettingsInfo->StudioUrl.'
                                    '.$SettingsInfo->AppName;
                                }
                                if ($PerformNotificationDueToClientStatus && $AppUserId != '0') {
                                    $AddNotification = DB::table('appnotification')->insertGetId(
                                        array('CompanyNum' =>  $dataJson["CompanyNum"], 'ClientId' => $client->id, 'Type' => $sendType, 'Subject' => $Subject, 'Text' => $Text, 'Dates' => date('Y-m-d H:i:s'), 'Date' => $Date, 'Time' => $Time, 'priority' => 1));
                                }
                            }
                        }
                        elseif ($leadStatus->Act == 2) {
                            $data["ConvertDate"] = date("Y-m-d H:i:s");
                            $clientUp = $clientObj->updateClient($dataJson["ClientId"], ["Status" => 1, "ConvertDate" => date("Y-m-d H:i:s"), "ArchiveReasonId" => getZapierReasonId($dataJson['CompanyNum'])]);
                            $data["Status"] = 1;
                            $data["FreeText"] = "Zapier";
                        }
//                        else{
//                            $data["Status"] = 1;
//                            $data["ConvertDate"] = date("Y-m-d H:i:s");
//                            $clientUp = $clientObj->updateClient($dataJson["ClientId"], ["Status" => 1, "ConvertDate" => date("Y-m-d H:i:s")]);
//                        }
                        $res = $pipeline->updatePipelineByClientId($dataJson["ClientId"], $data);
                        echo json_encode(["Message" => "Success", "Status" => true, "StatusCode" => 200]);
                    }
                    break;
                default:
                    throw new Exception("No function selected (Fun is incorrect)", 400);
            }
        }
        else{
            throw new Exception("No function selected ('fun' parameter was not sent)", 400);
        }

    }
    catch (Exception $e){

        echo json_encode(['Message' => "Failure", 'StatusCode'  => $e->getCode(), "errors" => $e->getMessage()]);
    }
}
else{
    echo json_encode(["Message" => "Failure", "errors" => "Not data has been sent", "StatusCode" => 400]);
}
