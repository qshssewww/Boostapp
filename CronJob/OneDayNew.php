<?php

$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/mail/class.phpmailer.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/ClientActivities.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/Client.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/ClassStudioDateRegular.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/LoginPushNotifications.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/ClassStudioAct.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/ClassSettings.php';

$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();


//require_once '../app/init.php';
//require_once '../office/Classes/ClientActivities.php';
//require_once '../office/Classes/Client.php';
//require_once '../office/Classes/ClassStudioDateRegular.php';
//require_once '../office/Classes/ClassSettings.php';
//require_once '../office/Classes/ClassStudioAct.php';

set_time_limit(0);
ini_set("memory_limit", "-1");

$ThisDate = date('Y-m-d');
$ThisDay = date('l');
$ThisTime = date('H:i:s');
$CronNow = date('Y-m-d H:i:s');
echo("Cron_start " . date('H:i:s') . "\n");

//////////////////////////////////////////////////////////////// סגירת שיעורים ///////////////////////////////////////////////////////
try {

    $GetClasses = DB::table('classstudio_date')
        ->where('Status', '=', '0')
        ->where('StartDate', '<', $ThisDate)
        ->whereNull('meetingTemplateId')
        ->get();

    $total = count($GetClasses);
    echo "{$total}\n";
    $iteration = 1;
    $statusArr = array(1, 2, 4, 6, 8, 11, 15, 16, 21, 22, 23);
    $loopStart = date('Y-m-d H:i:s');
    foreach ($GetClasses as $GetClasse) {
        echo("Iteration Number: " . $iteration . " Start Time: " . date('H:i:s') . "\n");
        $now = date('Y-m-d H:i:s');
        $CheckSettings = DB::table('settings')->where('CompanyNum', '=', $GetClasse->CompanyNum)->where('Status', '=', '0')->first();
        if ($CheckSettings) {
            $classSettings = (new ClassSettings())->GetClassSettingsByCompanyNum($GetClasse->CompanyNum);

            DB::table('classstudio_date')
                ->where('id', $GetClasse->id)
                ->where('CompanyNum', $GetClasse->CompanyNum)
                ->update(array('Status' => '1', 'color' => '#e2e2e2'));

            DB::table('classstudio_act')
                ->where('ClassId', '=', $GetClasse->id)
                ->where('CompanyNum', $GetClasse->CompanyNum)
                ->where('Status', '9')
                ->update(array('WatingStatus' => '1'));

            $Clients = DB::table('classstudio_act')->where('ClassId', '=', $GetClasse->id)->where('CompanyNum', $GetClasse->CompanyNum)->get();
            foreach ($Clients as $Client) {
                if ($Client->Status == 12) {
                    /// ניקוב משיבוץ קבוע להגיע/מומש
                    $ClientInfo = DB::table('client_activities')->where('id', '=', $Client->ClientActivitiesId)->where('CompanyNum', $Client->CompanyNum)->first();
                    if ($ClientInfo) {
                        if ((in_array($ClientInfo->Department, [2,3]) && $ClientInfo->TrueBalanceValue <= 0)
                            || (in_array($ClientInfo->Department, [1,2,3]) && !empty($ClientInfo->TrueDate) && $ClientInfo->TrueDate <= $GetClasse->StartDate)) {

                            $resArr = ClientActivities::findActiveMembership($ClientInfo->id, $Client->ClientId, $ClientInfo->CompanyNum, $GetClasse->ClassNameType);
                            $invalid = in_array($Client->Department, [2,3]) && $ClientInfo->TrueBalanceValue <= 0 ? lang('balance_over') : lang('valid_date_over');
                            $subject = '<strong class="text-danger">' . lang('invalid_regular_assignment') . '</strong>';
                            $client_info = new Client($ClientInfo->ClientId);
                            if (!$client_info->__get('id')) {
                                continue;
                            }
                            $regular = new ClassStudioDateRegular($Client->RegularClassId);
                            if ($resArr && $regular->__get('id')) {
                                $cardNumber = $resArr['CardNumber'];
                                $TrueBalanceValue = $resArr['TrueBalanceValue'] ?? 0;

                                unset($resArr['CardNumber'], $resArr['TrueBalanceValue']);

                                $actList = ClassStudioAct::where('ClassDate', '>=', $GetClasse->StartDate)
                                    ->where('ClientActivitiesId', '=', $ClientInfo->id)
                                    ->where('CompanyNum', '=', $ClientInfo->CompanyNum)
                                    ->where('ClientId', '=', $Client->ClientId)
                                    ->where('RegularClassId', '!=', 0)
                                    ->where('RegularClassId', '=', $Client->RegularClassId)
                                    ->get();

                                /** @var ClassStudioAct $act */
                                foreach ($actList as $act) {
                                    $act->update($resArr);
                                }

                                $regularArr = array(
                                    'ClientActivitiesId' => $resArr['ClientActivitiesId'],
                                    'MemberShipType' => $resArr['MemberShip']
                                );
                                ClassStudioDateRegular::updateById($regular->__get('id'), $regularArr);
                                if(in_array($resArr['Department'], [2,3])) {

                                    $updateActivity = DB::table('client_activities')
                                        ->where('id', $resArr['ClientActivitiesId'])
                                        ->update(['TrueBalanceValue' => $TrueBalanceValue]);
                                }

                                $found = lang('regular_assignment') . ' ' . $regular->__get('ClassName') . ' ' . lang('a_day') . ' ' . $regular->__get('ClassDay') . ' ' . lang('at_time_ajax') . ' ' . date('H:i', strtotime($regular->__get('ClassTime')));
                                $transfered = lang('transfered_to_membership') . ': ' . $resArr['ItemText'] . ' (' . $cardNumber . ')';
                                $text = $found . ' ' . $transfered;
                                $logMovement = DB::table('boostapp.log')->insert(array(
                                    'UserId' => 0,
                                    'Text' => $text,
                                    'Dates' => date('Y-m-d H:i:s'),
                                    'ClientId' => $client_info->__get('id'),
                                    'CompanyNum' => $client_info->__get('CompanyNum')
                                ));


                            } else { // activity not found
                                if(in_array($ClientInfo->Department, [2,3])) {
                                    $TrueBalanceValue = $ClientInfo->TrueBalanceValue - 1;
                                    /// עדכון כרטיסיה
                                    $updateActivity = DB::table('client_activities')
                                        ->where('id', $Client->ClientActivitiesId)
                                        ->where('CompanyNum', $GetClasse->CompanyNum)
                                        ->update(array('TrueBalanceValue' => $TrueBalanceValue));
                                }

                                $found = lang('not_found_membership_to_replace') . ' ' . $Client->ClassName . ' ' . lang('a_day') . ' ' . $Client->Day . ' ' . lang('at_time_ajax') . ' ' . date('H:i', strtotime($Client->ClassStartTime)) . ' <br>';
                                $notice = '<strong class="text-danger">' . lang('invalid_balance_notice') . '</strong> <br> * ' . lang('message_sent_to_customer');
                                $text = lang('to_customer_subscription') . ': <strong>' . $ClientInfo->ItemText . ' (' . $ClientInfo->CardNumber . ') </strong> ' . $invalid . ', ' . $found . $notice;

                                LoginPushNotifications::sendLoginPushNotification(
                                    $GetClasse->CompanyNum,
                                    LoginPushNotifications::PUSH_NOTIFICATIONS_ID['login_regular_class_limit_alert'],
                                    $subject,
                                    $text,
                                    date('Y-m-d'),
                                    date('H:i:s')
                                );

                                $notification = DB::table('appnotification')->insertGetId(
                                    array('CompanyNum' => $ClientInfo->CompanyNum, 'ClientId' => $client_info->__get('id'), 'Subject' => $subject, 'Text' => $text, 'Dates' => date('Y-m-d H:i:s'), 'UserId' => 0, 'Type' => 3, 'Date' => date('Y-m-d'), 'Time' => date('H:i:s')));

                                //// send message to client
                                if ($ClientInfo->Department == 1) {
                                    $invalidType = lang('balance_customer_push_notice');
                                    $notificationSubject = lang('invaliddate_subject');
                                } else {
                                    $invalidType = lang('validdate_customer_push_notice');
                                    $notificationSubject = lang('invalid_balance_subject');
                                }

                                $sendTime = date('07:00:00');
                                $clientText = '<p>' . lang('hi_corona_cron') . ' ,' . $client_info->__get('FirstName') . '</p><p>' . $invalidType . '</p><p>' . lang('thanks_client') . ', ' . $CheckSettings->AppName . '</p>';
                                $sendNotification = DB::table('boostapp.appnotification')->insertGetId(array(
                                    'CompanyNum' => $client_info->__get('CompanyNum'),
                                    'ClientId' => $client_info->__get('id'),
                                    'TrueClientId' => 0,
                                    'Subject' => $notificationSubject,
                                    'Text' => $clientText,
                                    'Dates' => date('Y-m-d H:i:s'),
                                    'UserId' => 0,
                                    'Type' => 0,
                                    'Date' => date('Y-m-d'),
                                    'Time' => $sendTime
                                ));

                            }


                        } elseif (in_array($ClientInfo->Department, [2, 3])) {
                            $TrueBalanceValue = $ClientInfo->TrueBalanceValue - 1;
                            /// עדכון כרטיסיה
                            DB::table('client_activities')
                                ->where('id', $Client->ClientActivitiesId)
                                ->where('CompanyNum', $GetClasse->CompanyNum)
                                ->update(array('TrueBalanceValue' => $TrueBalanceValue));
                        }
                    }

                    // תיעוד שינוי סטטוס

                    if($classSettings) {
                        $updateStatus = $classSettings->DefaultStatusClass == 0 ? 2 : 8;
                        $res = (new ClassStudioAct($Client->id))->changeStatus($updateStatus, true, true);
                    }

                } elseif (in_array($Client->Status, $statusArr) && $Client->ActStatus == "0") {
                    /// ניקוב משיבוץ קבוע להגיע/מומש
                    $ClientInfo = DB::table('client_activities')->where('id', '=', $Client->ClientActivitiesId)->where('CompanyNum', $Client->CompanyNum)->first();
                    if (isset($ClientInfo->Department) && in_array($ClientInfo->Department, [2, 3])) {

                        $ActBalanceValue = $ClientInfo->ActBalanceValue - 1;

//                        if ($ActBalanceValue == '1') {
//                            $CardStatus = '0';
//                        } elseif ($ActBalanceValue <= '0') {
//                            $CardStatus = '1';
//                        } else {
//                            $CardStatus = $ClientInfo->CardStatus;
//                        }
                        /// עדכון כרטיסיה
                        DB::table('client_activities')
                            ->where('id', $Client->ClientActivitiesId)
                            ->where('CompanyNum', $Client->CompanyNum)
                            ->update(array('ActBalanceValue' => $ActBalanceValue));
                    }
                    if($classSettings && !in_array($Client->Status, [2,4,8,21,23])) {
                        $updateStatus = $classSettings->DefaultStatusClass == 0 ? 2 : 8;
                        if($Client->Status == 16) {
                            $updateStatus = $classSettings->DefaultStatusClass == 0 ? 23 : 7;
                        }
                        $res = (new ClassStudioAct($Client->id))->changeStatus($updateStatus, true, true);

                    } elseif(in_array($Client->Status, [2,4,8,21,23])) {
                        DB::table('classstudio_act')
                            ->where('id', $Client->id)
                            ->where('CompanyNum', $Client->CompanyNum)
                            ->update(array('ActStatus' => 1));
                    }

                }
                if (in_array($Client->Status, [1,2,6,10,11,12,15,16,21,22,23])) {
                    $ClientInfo = DB::table('client')->where('id', '=', $Client->FixClientId)->where('CompanyNum', '=', $GetClasse->CompanyNum)->first();
                    //// עדכון שיעור אחרון ללקוח
                    DB::table('client')
                        ->where('id', $Client->FixClientId)
                        ->where('CompanyNum', $Client->CompanyNum)
                        ->update(array('LastClassDate' => $GetClasse->StartDate));
                }
            }
        } else {
            DB::table('classstudio_date')
                ->where('id', $GetClasse->id)
                ->update(array('Status' => 1, 'color' => '#e2e2e2'));

            DB::table('classstudio_act')
                ->where('ClassId', '=', $GetClasse->id)
                ->where('CompanyNum', $GetClasse->CompanyNum)
                ->where('Status', '9')
                ->update(array('WatingStatus' => 1));
        }

//  echo "done {$GetClasse->id} \n";
        echo $total-- . " left\n";
        $later = date('Y-m-d H:i:s');
        $diff = strtotime($later) - strtotime($now);
        echo("Iteration " . $iteration . " Ends After " . $diff . " Seconds" . "\n");
        $iteration++;
    }
    $loopEnds = date('Y-m-d H:i:s');
    $diff = strtotime($loopEnds) - strtotime($loopStart);
    echo("loop Ends After " . $diff . " Seconds" . "\n");


//////////////////////////////////////////////////////////////// סיום פקודת מערכת ///////////////////////////////////////////////////////
    $CronLater = date('Y-m-d H:i:s');
    $diffCron = strtotime($CronLater) - strtotime($CronNow);
    echo("Cron Ends After " . $diffCron . " Seconds" . "\n");
    $Cron->end();
}
catch (Exception $e){
    $arr = array(
        "line" => $e->getLine(),
        "message" => $e->getMessage(),
        "file_path" => $e->getFile(),
        "trace" => $e->getTraceAsString()
    );
    if(isset($GetClasse)){
        $util = new Utils();
        $arr["data"] = json_encode($util->createArrayFromObj($GetClasse),JSON_UNESCAPED_UNICODE);
    }
    $Cron->cronLog($arr);
}



