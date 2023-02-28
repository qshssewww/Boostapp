<?php

$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/mail/class.phpmailer.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/ClientActivities.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/Client.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/ClassStudioDateRegular.php';

$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();

set_time_limit(0);
ini_set("memory_limit", "-1");

$ThisDate = date('Y-m-d');
$ThisDay = date('l');
$ThisTime = date('H:i:s');


//////////////////////////////////////////////////////////////// סגירת שיעורים ///////////////////////////////////////////////////////

$GetClasses = DB::table('classstudio_date')->where('Status', '=', '0')->where('StartDate', '<', $ThisDate)->get();

$total = count($GetClasses);
echo "{$total}\n";

foreach ($GetClasses as $GetClasse) {
    $CheckSettings = DB::table('settings')->where('CompanyNum', '!=', '569121')->where('CompanyNum', '=', $GetClasse->CompanyNum)->where('Status', '=', '0')->first();
    if ($CheckSettings) {


/// עדכן שיעור הושלם
        DB::table('classstudio_date')
            ->where('id', $GetClasse->id)
            ->where('CompanyNum', $GetClasse->CompanyNum)
            ->update(array('Status' => '1', 'color' => '#e2e2e2'));


        DB::table('classstudio_act')
            ->where('ClassDate', '<', $ThisDate)
            ->where('CompanyNum', $GetClasse->CompanyNum)
            ->where('Status', '9')
            ->update(array('WatingStatus' => '1'));


        $Clients = DB::table('classstudio_act')->where('ClassId', '=', $GetClasse->id)->where('CompanyNum', $GetClasse->CompanyNum)->where('StatusCount', '=', '0')->get();
        foreach ($Clients as $Client) {


            if ($Client->Status == 12) {

                /// ניקוב משיבוץ קבוע להגיע/מומש

                $ClientInfo = DB::table('client_activities')->where('id', '=', $Client->ClientActivitiesId)->where('CompanyNum', $Client->CompanyNum)->first();

                if($ClientInfo) {
                    if((in_array($ClientInfo->Department, [2, 3]) && $ClientInfo->TrueBalanceValue <= 0)
                        || (in_array($ClientInfo->Department, [1, 2, 3]) && !empty($ClientInfo->TrueDate) && $ClientInfo->TrueDate <= $GetClasse->StartDate)) {
                        $resArr = ClientActivities::findActiveMembership($ClientInfo->id, $Client->ClientId, $ClientInfo->CompanyNum, $GetClasse->ClassNameType);
                        $invalid = in_array($Client->Department, [2,3]) && $ClientInfo->TrueBalanceValue <= 0 ? lang('balance_over') : lang('valid_date_over');
                        $subject = '<strong class="text-danger">'.lang('invalid_regular_assignment').'</strong>';
                        $client_info = new Client($ClientInfo->ClientId);
                        if(!$client_info->__get('id')) {
                            continue;
                        }
                        $regular = new ClassStudioDateRegular($Client->RegularClassId);
                        if ($resArr && $regular->__get('id')) {
                            $cardNumber = $resArr['CardNumber'];
                            unset($resArr['CardNumber']);
                            $update = DB::table('classstudio_act')
                                ->where('ClassDate', '>=', $GetClasse->StartDate)
                                ->where('ClientActivitiesId', '=', $ClientInfo->id)
                                ->where('CompanyNum', '=', $ClientInfo->CompanyNum)
                                ->where('ClientId', '=', $Client->ClientId)
                                ->where('RegularClassId', '!=', 0)
                                ->where('RegularClassId', '=', $Client->RegularClassId)
                                ->update($resArr);
                            $regularArr = array(
                                'ClientActivitiesId' => $resArr['ClientActivitiesId'],
                                'MemberShipType' => $resArr['MemberShip']
                            );
                            ClassStudioDateRegular::updateById($regular->__get('id') ,$regularArr);
                            $found = lang('regular_assignment').' '.$regular->__get('ClassName').' '.lang('a_day').' '.$regular->__get('ClassDay').' '.lang('at_time_ajax').' '.date('H:i', strtotime($regular->__get('ClassTime')));
                            $transfered = lang('transfered_to_membership').': '.$resArr['ItemText']. ' ('.$cardNumber.')';
                            $text = $found.' '.$transfered;


                            $logMovement = DB::table('boostapp.log')->insert(array('UserId' => 0, 'Text' => $text, 'Dates' => date('Y-m-d H:i:s'), 'ClientId' => $client_info->__get('id'), 'CompanyNum' => $client_info->__get('CompanyNum')));


                        } else {    // activity not found
                            $TrueBalanceValue = $ClientInfo->TrueBalanceValue - 1;

                            /// עדכון כרטיסיה
                            $updateActivity = DB::table('client_activities')
                                ->where('id', $Client->ClientActivitiesId)
                                ->where('CompanyNum', $GetClasse->CompanyNum)
                                ->update(array('TrueBalanceValue' => $TrueBalanceValue));

                            $found = lang('not_found_membership_to_replace').' '.$Client->ClassName.' '.lang('a_day').' '.$Client->Day. ' '.lang('at_time_ajax').' '.date('H:i', strtotime($Client->ClassStartTime)).' <br>';
                            $notice = '<strong class="text-danger">'.lang('invalid_balance_notice').'</strong> <br> * '.lang('message_sent_to_customer');
                            $text = lang('to_customer_subscription').': <strong>'.$ClientInfo->ItemText.' ('.$ClientInfo->CardNumber.') </strong> '.$invalid.', '.$found. $notice;

                            $notification = DB::table('appnotification')->insertGetId(
                                array('CompanyNum' => $ClientInfo->CompanyNum, 'ClientId' => $client_info->__get('id'), 'Subject' => $subject, 'Text' => $text, 'Dates' => date('Y-m-d H:i:s'), 'UserId' => 0, 'Type' => 3, 'Date' => date('Y-m-d'), 'Time' => date('H:i:s')) );

                            //// send message to client
                            if($ClientInfo->Department == 1) {
                                $invalidType = lang('balance_customer_push_notice');
                                $notificationSubject = lang('invaliddate_subject');
                            } else {
                                $invalidType = lang('validdate_customer_push_notice');
                                $notificationSubject = lang('invalid_balance_subject');
                            }


                            $clientText = '<p>'.lang('hi_corona_cron').' ,'.$client_info->__get('FirstName').'</p><p>'.$invalidType.'</p><p>'.lang('thanks_client').', '.$CheckSettings->AppName.'</p>';

                            $sendNotification = DB::table('boostapp.appnotification')->insertGetId(
                                array('CompanyNum' => $client_info->__get('CompanyNum'), 'ClientId' => $client_info->__get('id'), 'TrueClientId' => 0, 'Subject' => $notificationSubject, 'Text' => $clientText, 'Dates' => date('Y-m-d H:i:s'), 'UserId' => 0, 'Type' => 0, 'Date' => date('Y-m-d'), 'Time' => date('H:i:s')));

                        }


                    } else if(in_array($ClientInfo->Department, [2, 3])) {

                        $TrueBalanceValue = $ClientInfo->TrueBalanceValue - 1;
                        /// עדכון כרטיסיה
                        DB::table('client_activities')
                            ->where('id', $Client->ClientActivitiesId)
                            ->where('CompanyNum', $GetClasse->CompanyNum)
                            ->update(array('TrueBalanceValue' => $TrueBalanceValue));
                    }
                }

                // תיעוד שינוי סטטוס

                $Dates = date('Y-m-d H:i:s');
                $NewStatus = 2;
                $CheckNewStatus = DB::table('class_status')->where('id', '=', '2')->first();

                $StatusJson = '';
                $StatusJson .= '{"data": [';

                if (!empty($Client->StatusJson)) {
                    $Loops = json_decode($Client->StatusJson, true);
                    foreach ($Loops['data'] as $key => $val) {

                        $DatesDB = $val['Dates'];
                        $UserIdDB = $val['UserId'];
                        $StatusDB = $val['Status'];
                        $StatusTitleDB = $val['StatusTitle'];
                        $UserNameDB = $val['UserName'];

                        $StatusJson .= '{"Dates": "' . $DatesDB . '", "UserId": "' . $UserIdDB . '", "Status": "' . $StatusDB . '", "StatusTitle": "' . $StatusTitleDB . '", "UserName": "' . $UserNameDB . '"},';

                    }
                }

                $StatusJson .= '{"Dates": "' . $Dates . '", "UserId": "", "Status": "' . $NewStatus . '", "StatusTitle": "' . $CheckNewStatus->Title . '", "UserName": ""}';

                $StatusJson .= ']}';

                /// עדכון סטטוס חדש
                DB::table('classstudio_act')
                    ->where('id', $Client->id)
                    ->where('CompanyNum', $GetClasse->CompanyNum)
                    ->update(array('Status' => 2, 'StatusJson' => $StatusJson));


                if ($Client->TrueClientId == '0') {
                    $FixClientId = $Client->ClientId;
                } else {
                    $FixClientId = $Client->TrueClientId;
                }

                ///// Class Log
                DB::table('boostapp.classlog')->insertGetId(
                    array('CompanyNum' => $GetClasse->CompanyNum, 'ClassId' => $Client->ClassId, 'ClientId' => $Client->FixClientId, 'Status' => $CheckNewStatus->Title, 'UserName' => '0'));
                /////////////////////////////////////////

            }


            if ($Client->Status != 8) {
//                $ClientInfo = DB::table('client')->where('id', '=', $Client->FixClientId)->where('CompanyNum', '=', $GetClasse->CompanyNum)->first();

                //// עדכון שיעור אחרון ללקוח
                DB::table('client')
                    ->where('id', $Client->FixClientId)
                    ->where('CompanyNum', $Client->CompanyNum)
                    ->update(array('LastClassDate' => @$Client->ClassDate, 'ChangeDate' => @$ClientInfo->ChangeDate));

            }

        }


    } else {
        DB::table('classstudio_date')
            ->where('id', $GetClasse->id)
            ->update(array('Status' => '1', 'color' => '#e2e2e2'));

        DB::table('classstudio_act')
            ->where('ClassDate', '<', $ThisDate)
            ->where('CompanyNum', $GetClasse->CompanyNum)
            ->where('Status', '9')
            ->update(array('WatingStatus' => '1'));
    }

//  echo "done {$GetClasse->id} \n";   
    echo $total-- . " left\n";

}


//////////////////////////////////////////////////////////////// סגירת שיעורים ///////////////////////////////////////////////////////
////////////////////////////////////////////////////// OneDayCards ///////////////////////////////////////////////////////


$Clients = DB::table('classstudio_act')->where('ClassDate', '<', $ThisDate)->where('ActStatus', '=', '0')->whereIn('Status', array(1, 2, 4, 6, 8, 11, 15, 21))->get();

foreach ($Clients as $Client) {

    $CheckSettings = DB::table('settings')->where('CompanyNum', '!=', '569121')->where('CompanyNum', '=', $Client->CompanyNum)->where('Status', '=', '0')->first();
    if (@$CheckSettings->id != '') {

        /// ניקוב משיבוץ קבוע להגיע/מומש

        $ClientInfo = DB::table('client_activities')->where('id', '=', $Client->ClientActivitiesId)->where('CompanyNum', $Client->CompanyNum)->first();

        if (@$ClientInfo->Department == '2' || @$ClientInfo->Department == '3') {

            $ActBalanceValue = $ClientInfo->ActBalanceValue - 1;

            if ($ActBalanceValue == '1') {
                $CardStatus = '0';
            } else if ($ActBalanceValue <= '0') {
                $CardStatus = '1';
            } else {
                $CardStatus = $ClientInfo->CardStatus;
            }

            /// עדכון כרטיסיה
            DB::table('client_activities')
                ->where('id', $Client->ClientActivitiesId)
                ->where('CompanyNum', $Client->CompanyNum)
                ->update(array('ActBalanceValue' => $ActBalanceValue, 'CardStatus' => $CardStatus));

        }

        DB::table('classstudio_act')
            ->where('id', $Client->id)
            ->where('CompanyNum', $Client->CompanyNum)
            ->update(array('ActStatus' => '1'));


    } else {

        DB::table('classstudio_act')
            ->where('id', $Client->id)
            ->where('CompanyNum', $Client->CompanyNum)
            ->update(array('ActStatus' => '1'));


    }


}


//////////////////////////////////////////////////////////////// סיום פקודת מערכת ///////////////////////////////////////////////////////

$Cron->end();
