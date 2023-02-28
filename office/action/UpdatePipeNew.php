<?php
header('Content-Type: application/json');
require_once '../../app/init.php';
require_once __DIR__.'/../Classes/Client.php';
require_once __DIR__.'/../Classes/Pipereasons.php';
require_once __DIR__ . '/../Classes/Notificationcontent.php';

$userid = Auth::user()->id;
$CompanyNum = Auth::user()->CompanyNum;
$ItemId = Auth::user()->ItemId;
$Dates = date('Y-m-d H:i:s');
$LeadId = $_REQUEST['LeadId'];
$PipeId = $_REQUEST['PipeId'];
$ReasonId = $_REQUEST['ReasonId'] ?? null;
$NewStatus = $_REQUEST['NewStatus'] ?? 2;
$Remarks = isset($_REQUEST['ReasonText']) ? htmlspecialchars($_REQUEST['ReasonText']) : null;
$mobile_err = false;
$LeadPipeLineBeforeChange = DB::table('pipeline')->where('id', $LeadId)->where('CompanyNum', $CompanyNum)->first();
//$ClientInfo = DB::table('client')->where('CompanyNum', $CompanyNum)->where('id', $LeadPipeLineBeforeChange->ClientId)->first();
$ClientInfo = new Client($LeadPipeLineBeforeChange->ClientId);
$isMinor = !empty($ClientInfo) && $ClientInfo->parentClientId != 0;
if ((empty($ClientInfo->__get('ContactMobile')) || $ClientInfo->__get('ContactMobile') == "Error") && !$isMinor) {
    $mobile_err = true;
}

///הצלחה
$GetSuccessInfo = DB::table('leadstatus')->where('CompanyNum', '=', $CompanyNum)->where('PipeId', '=', $LeadPipeLineBeforeChange->MainPipeId)->where('title', '=', 'הצלחה')->first();
/// כשלון
$GetFailsInfo = DB::table('leadstatus')->where('CompanyNum', '=', $CompanyNum)->where('PipeId', '=', $LeadPipeLineBeforeChange->MainPipeId)->where('title', '=', 'כישלון')->first();
/// לא רלוונטי
$GetNoneFailsInfo = DB::table('leadstatus')->where('CompanyNum', '=', $CompanyNum)->where('PipeId', '=', $LeadPipeLineBeforeChange->MainPipeId)->where('Act', '=', '3')->first();

$GetSuccess = $GetSuccessInfo->id;
$GetFails = $GetFailsInfo->id;
$GetNoneFails = $GetNoneFailsInfo->id;


if ($NewStatus == 1) {
    $PipeId = $GetFails;
} elseif ($NewStatus == 0) {
    $PipeId = $GetSuccess;
}


if ($LeadPipeLineBeforeChange->PipeId == $GetSuccess) {
    $NameStatusBefore = 'הצלחה';
    $IdStatusBefore = $GetSuccess;
} elseif ($LeadPipeLineBeforeChange->PipeId == $GetFails) {
    $NameStatusBefore = 'כישלון';
    $IdStatusBefore = $GetFails;
} elseif ($LeadPipeLineBeforeChange->PipeId == $GetNoneFails) {
    $NameStatusBefore = 'לא רלוונטי';
    $IdStatusBefore = $GetNoneFails;
} else {
    $LeadStatusBeforeChange = DB::table('leadstatus')->where('CompanyNum', $CompanyNum)->where('id', $LeadPipeLineBeforeChange->PipeId)->first();
    $NameStatusBefore = $LeadStatusBeforeChange->Title;
    $IdStatusBefore = $LeadStatusBeforeChange->id;
}


$CheckActStatus = DB::table('leadstatus')->where('CompanyNum', $CompanyNum)->where('id', $PipeId)->first();
$StstusFilter = !empty($CheckActStatus) ? $CheckActStatus->Act : 0;


if ($PipeId != $CompanyNum . '100') {

    DB::table('pipeline')
        ->where('id', $LeadId)
        ->where('CompanyNum', $CompanyNum)
        ->update(array('StatusFilter' => $StstusFilter, 'ConvertDate' => $Dates));

}
$Lead = DB::table('pipeline')->where('CompanyNum', '=', $CompanyNum)->where('id', $LeadId)->first();
if ($StstusFilter == 1 && !$mobile_err) {
    $leadStatus = DB::table('boostapp.leadstatus')->where('PipeId', '=', $Lead->MainPipeId)
        ->where('CompanyNum', $CompanyNum)
        ->where('title', '=', 'הצלחה')->first();
} elseif ($StstusFilter == 2) {
    $leadStatus = DB::table('boostapp.leadstatus')
        ->where('CompanyNum', $CompanyNum)
        ->where('PipeId', '=', $Lead->MainPipeId)
        ->where('title', '=', 'כישלון')->first();
} elseif ($StstusFilter == 0) {
    $leadStatus = DB::table('boostapp.leadstatus')
        ->where('CompanyNum', $CompanyNum)
        ->where('PipeId', '=', $Lead->MainPipeId)
        ->where('id', '=', $PipeId)->first();
} elseif ($StstusFilter == 3) {
    $leadStatus = DB::table('boostapp.leadstatus')
        ->where('CompanyNum', $CompanyNum)
        ->where('PipeId', '=', $Lead->MainPipeId)
        ->where('title', '=', 'לא רלוונטי')->first();
}

if (!empty($leadStatus)) {

    $affect = DB::table('boostapp.pipeline')
        ->where('id', $LeadId)
        ->where('CompanyNum', '=', $CompanyNum)
        ->update(array('PipeId' => $leadStatus->id));

    $ClientInfo->Status = 2;
    $ClientInfo->updateClient($ClientInfo->id, [
        'Status' => 2,
    ]);
}


if ($PipeId == $GetSuccess) {

    DB::table('client')
        ->where('id', $LeadPipeLineBeforeChange->ClientId)
        ->where('CompanyNum', $CompanyNum)
        ->update(array('Status' => '0', 'ConvertDate' => $Dates));

    $SettingsInfo = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();
    $ClientId = $LeadPipeLineBeforeChange->ClientId;
    $UserId = Auth::user()->id;

    $MakeRandomPass = mt_rand(100000, 999999);
    $password = Hash::make($MakeRandomPass);
    $Email = !empty($ClientInfo->Email) ? $ClientInfo->Email : '';
    $GetUsersId = '0';

    $ContactMobile = !empty($ClientInfo->__get('ContactMobile')) ? $ClientInfo->__get('ContactMobile') : '';

    $mobileRegex = Client::mobileRegex;
    if ((empty($ContactMobile) || !preg_match($mobileRegex, $ContactMobile)) && !$isMinor) {
        echo 'פורמט מספר נייד לא תקין, הפעולה לא הושלמה';
        exit;
    }

    if (!empty($ContactMobile)) {
        $mobile = substr($ContactMobile, 0, 4) == '+972' ? substr($ContactMobile, 4, strlen($ContactMobile)) : $ContactMobile;
        $mobile = substr($mobile, 0, 1) == '0' ? substr($mobile, 1, strlen($mobile)) : $mobile;
        $mobile = '+972' . $mobile;
    }

    if (!empty($ClientInfo)) {
        $isUserNew = true;
        if ($isMinor) {

            $parent = new Client($ClientInfo->parentClientId);
            if (empty($parent)) {
                echo 'לא נמצא לקוח אב, הפעולה לא הושלמה';
                exit;
            }
            $isUserNew = $parent->setMinorAppUser($ClientInfo->id, $password);

        } else {

            ///// שינוי יוזר באפליקציה
            $AppUsers = DB::table('boostapplogin.users')->where('newUsername', '=', $mobile)->first();

            if (!empty($AppUsers)) { /// משתמש קיים באפליקציה
                $GetUsersId = $AppUsers->id;
                $AppStudio = DB::table('boostapplogin.studio')->where('CompanyNum', $CompanyNum)->where('ClientId', $ClientId)->first();

                if (!empty($AppStudio)) {

                    DB::table('boostapplogin.studio')
                        ->where('id', $AppStudio->id)
                        ->where('CompanyNum', $CompanyNum)
                        ->update(array('UserId' => $AppUsers->id, 'Status' => '0', 'Takanon' => $ClientInfo->Takanon, 'Medical' => $ClientInfo->Medical));

                } else { /// הקם חדש
                    if ($ClientId != 0) {
                        $AppStudio = DB::table('boostapplogin.studio')->insertGetId(
                            array('StudioUrl' => $SettingsInfo->StudioUrl, 'StudioName' => $SettingsInfo->AppName, 'CompanyNum' => $CompanyNum, 'UserId' => $AppUsers->id, 'ClientId' => $ClientId, 'Memotag' => $SettingsInfo->Memotag, 'Folder' => $SettingsInfo->Folder, 'Takanon' => $ClientInfo->Takanon, 'Medical' => $ClientInfo->Medical));
                    }

                }


                DB::table('boostapplogin.users')
                    ->where('id', $AppUsers->id)
                    ->update(array('email' => $Email, 'password' => $password, 'PassAct' => '0'));

            } else { /// שינוי יוזר באפלקיציה

                $AppUserId = DB::table('boostapplogin.users')->insertGetId(
                    array('username' => $Email, 'email' => $Email, "newUsername" => $mobile, 'password' => $password, 'display_name' => $ClientInfo->CompanyName, 'FirstName' => $ClientInfo->FirstName, 'LastName' => $ClientInfo->LastName, 'ContactMobile' => $ClientInfo->ContactMobile, 'AppLoginId' => $ClientInfo->ContactMobile, 'status' => 1, 'PassAct' => 0));

                $AppStudio = DB::table('boostapplogin.studio')->where('CompanyNum', $CompanyNum)->where('ClientId', $ClientId)->first();

                if (!empty($AppStudio)) {

                    DB::table('boostapplogin.studio')
                        ->where('id', $AppStudio->id)
                        ->where('CompanyNum', $CompanyNum)
                        ->update(array('UserId' => $AppUserId, 'Status' => '0', 'Takanon' => $ClientInfo->Takanon, 'Medical' => $ClientInfo->Medical));

                } else { ////  הקם חדש

                    $GetUsersId = $AppUserId;
                    if ($AppUserId != 0 && $ClientId != '0') {
                        $AppStudio = DB::table('boostapplogin.studio')->insertGetId(
                            array('StudioUrl' => $SettingsInfo->StudioUrl, 'StudioName' => $SettingsInfo->AppName, 'CompanyNum' => $CompanyNum, 'UserId' => $AppUserId, 'ClientId' => $ClientId, 'Memotag' => $SettingsInfo->Memotag, 'Folder' => $SettingsInfo->Folder, 'Takanon' => $ClientInfo->Takanon, 'Medical' => $ClientInfo->Medical));
                    }

                }

            }
        }

        if ($isUserNew) {

            ///////////  שליחת פרטי התחברות
            $Date = date('Y-m-d');
            $Time = date('H:i:s');

            $Template = DB::table('notificationcontent')->where('CompanyNum', '=', $CompanyNum)->where('Type', '=', '21')->first();
            /// עדכון תבנית הודעה

            $GooglePlayLink = 'https://play.google.com/store/apps/details?id=com.connect_computer.boostnew&gl=IL';
            $AppStoreLink = 'https://apps.apple.com/us/app/boost-%D7%91%D7%95%D7%A1%D7%98/id1479519489';

            if (!empty($SettingsInfo->GooglePlayLink)) {
                $GooglePlayLink = $SettingsInfo->GooglePlayLink;
            }
            if (!empty($SettingsInfo->AppStoreLink)) {
                $AppStoreLink = $SettingsInfo->AppStoreLink;
            }

            $AppStore = '<a href="' . $AppStoreLink . '">App Store</a>';
            $GooglePlay = '<a href="' . $GooglePlayLink . '">Google Play</a>';

            $Content1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], $SettingsInfo->AppName, $Template->Content);
            $Content2 = str_replace(Notificationcontent::REPLACE_ARR["name_table"], $ClientInfo->CompanyName, $Content1);
            $Content3 = str_replace(Notificationcontent::REPLACE_ARR["first_name"], $ClientInfo->FirstName, $Content2);
            $Content4 = str_replace(Notificationcontent::REPLACE_ARR["username_single"], @$Email, $Content3);
            $Content5 = str_replace(Notificationcontent::REPLACE_ARR["password_single"], $MakeRandomPass, $Content4);
            $Content6 = str_replace("App Store", $AppStore, $Content5);
            $Content7 = str_replace("Google Play", $GooglePlay, $Content6);

            $Text = $Content7; /// משיכת הודעת ביטול שיעור מבסיס הנתונים
            $Subject = $Template->Subject;

            $sendType = $Template->SendOption;
            if ($sendType == 'BA999') {
                $sendType = '1,2';
            }

            /*
                If sendType contains 1 Send by phone
                If contains 2 Send by email If there is, another pass
            */
            $arr = array('CompanyNum' => $CompanyNum, 'ClientId' => $ClientInfo->id, 'Subject' => $Subject,
                'Text' => $Text, 'Dates' => $Dates, 'UserId' => $UserId,
                'Date' => $Date, 'Time' => $Time, 'priority' => 1);
            if ($GetUsersId != '0') {
                if (str_contains($sendType, '1')) {
                    $arr['Type'] = '1';
                    $AddNotification = DB::table('appnotification')->insertGetId($arr);
                } else if (str_contains($sendType, '2')) {
                    if (!empty($Email)) {
                        $arr['Type'] = '2';
                        $AddNotification = DB::table('appnotification')->insertGetId($arr);
                    }
                }
            }


        }
    }


} elseif ($PipeId == $GetFails || $PipeId == $GetNoneFails) {

    DB::table('client')
        ->where('id', $LeadPipeLineBeforeChange->ClientId)
        ->where('CompanyNum', $CompanyNum)
        ->update(array('Status' => 1, 'ConvertDate' => $Dates, 'ArchiveReasonId' => $ReasonId));

    DB::table('pipeline')
        ->where('id', $LeadId)
        ->where('CompanyNum', $CompanyNum)
        ->update(array('ReasonsId' => $ReasonId));

    if ($Remarks != null && $Remarks != '') {
        $clientCrm = new Clientcrm();
        $reason = Pipereasons::find($ReasonId);
        $Remarks = $reason->Title . ': <br>' . $Remarks;
        $clientCrmRow = $clientCrm->addClientCrm($ClientInfo->id, $userid, $Remarks);
    }
}

if ($PipeId != $CompanyNum . '100') {
    $ClientDetails = DB::table('client')->where('id', $LeadPipeLineBeforeChange->ClientId)->where('CompanyNum', $CompanyNum)->first();

    if ($PipeId == $GetSuccess) {
        $NameStatusAfter = 'הצלחה';
        $IdStatusAfter = $GetSuccess;
    } elseif ($PipeId == $GetFails) {
        $NameStatusAfter = 'כישלון';
        $IdStatusAfter = $GetFails;
    } elseif ($PipeId == $GetNoneFails) {
        $NameStatusAfter = 'לא רלוונטי';
        $IdStatusAfter = $GetNoneFails;
    } else {
        $LeadStatusAfterChange = DB::table('leadstatus')->where('CompanyNum', $CompanyNum)->where('id', $PipeId)->first();
        $NameStatusAfter = $LeadStatusAfterChange->Title;
        $IdStatusAfter = $LeadStatusAfterChange->id;
    }


    if ($IdStatusBefore != $IdStatusAfter) {
        CreateLogMovement( //FontAwesome Icon
            'העביר את הליד ' . $ClientDetails->CompanyName . ' מסטטוס ' . @$NameStatusBefore . ' לסטטוס ' . @$NameStatusAfter, //LogContent
            $LeadPipeLineBeforeChange->ClientId //ClientId
        );
    }
}

$agentView = $_REQUEST['PipeAgentView'] ?? 0;
$all = isset($_REQUEST['All']) && $_REQUEST['All'] == 'True';

if ($PipeId && $LeadId) {
    if (($agentView == 0 && $LeadPipeLineBeforeChange->AgentId == '') || $all) {
        $countNewPipe = DB::table('pipeline')
            ->leftJoin('client', 'pipeline.ClientId', '=', 'client.id')
            ->where('client.CompanyName', '!=', '')
            ->where('pipeline.CompanyNum', '=', $CompanyNum)
            ->where('pipeline.PipeId', '=', $PipeId)
            ->count();
        $countOldPipe = DB::table('pipeline')
            ->leftJoin('client', 'pipeline.ClientId', '=', 'client.id')
            ->where('client.CompanyName', '!=', '')
            ->where('pipeline.CompanyNum', '=', $CompanyNum)
            ->where('pipeline.PipeId', '=', $LeadPipeLineBeforeChange->PipeId)
            ->count();
    } else {
        $countNewPipe = DB::table('pipeline')
            ->leftJoin('client', 'pipeline.ClientId', '=', 'client.id')
            ->where('client.CompanyName', '!=', '')
            ->where('pipeline.CompanyNum', '=', $CompanyNum)
            ->where('pipeline.PipeId', '=', $PipeId)
            ->where('pipeline.AgentId', '=', $LeadPipeLineBeforeChange->AgentId)
            ->count();
        $countOldPipe = DB::table('pipeline')
            ->leftJoin('client', 'pipeline.ClientId', '=', 'client.id')
            ->where('client.CompanyName', '!=', '')
            ->where('pipeline.CompanyNum', '=', $CompanyNum)
            ->where('pipeline.PipeId', '=', $LeadPipeLineBeforeChange->PipeId)
            ->where('pipeline.AgentId', '=', $LeadPipeLineBeforeChange->AgentId)
            ->count();
    }
    echo json_encode(array('PipeOldId' => $LeadPipeLineBeforeChange->PipeId, 'PipeOldCount' => $countOldPipe, 'PipeNewId' => $PipeId, 'PipeNewCount' => $countNewPipe));
} else {
    echo json_encode(array());
}


