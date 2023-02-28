<?php
$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';

require_once $_SERVER['DOCUMENT_ROOT'] .'/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/Classes/Utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/FirebaseToken.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/services/LoggerService.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/mail/class.phpmailer.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/services/EmailService.php';
require_once $_SERVER['DOCUMENT_ROOT'] .'/office/services/PushNotificationService.php';

require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/LoginPushNotifications.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/LoginPushNotificationsLog.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/Roles.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/Users.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/Settings.php';

$filename = basename(__FILE__, '.php');
$cron = new CronManager($filename);
$id = $cron->start();

const API_ACCESS_KEY = 7;

ini_set('max_execution_time', 0);
set_time_limit(0);
ini_set('memory_limit', -1);

$limit_messages = 300;

try {
    $pushNotifications = LoginPushNotifications::where('workerStatus', 0)
        ->where('status', LoginPushNotifications::STATUS['active'])
        ->where(function ($q) {
            $q->where('date', '<', date('Y-m-d'))->where('date', '>=', date('Y-m-d', strtotime("-2 days")))
                ->Orwhere('date', '=', date('Y-m-d'))->where('time', '<=', date('H:i:s'));
        })
        ->limit($limit_messages)->get();

    foreach ($pushNotifications as $pN) {

        $roleIds = [];

        $settings = Settings::getSettings($pN->companyNum);
        if(!$settings || $settings->Status != 0) {
            LoginPushNotifications::setError($pN->id, LoginPushNotifications::STATUS['error'], 'company not found or inactive');
            continue;
        }

        $studioRoles = Roles::where('CompanyNum', $pN->companyNum)
            ->get();

        if(empty($studioRoles)) {
            LoginPushNotifications::setError($pN->id, LoginPushNotifications::STATUS['error'], 'roles not found');
            continue;
        }


        foreach ($studioRoles as $role) {
            $studioPermissions = explode(',', $role->permissions);

            if (in_array($pN->permission, $studioPermissions) || $role->permissions == '*') {
                $roleIds[] = $role->id;
            }
        }

        $users = Users::where('CompanyNum', $pN->companyNum)
            ->whereIn('role_id', $roleIds)
            ->where('status', 1)
            ->where('tokenFirebase', '<>', '')
            ->get();

        if(empty($users)) {
            LoginPushNotifications::setError($pN->id, LoginPushNotifications::STATUS['error'], 'users not found');
            continue;
        }

        $content = preg_replace('#<[^>]+>#', ' ', $pN->content);
        $content = str_replace("&nbsp;", ' ', $content);

        $apiAccessKey = FirebaseToken::getTokenByProject(7);

        $pN->workerStatus = 1;
        $pN->save();

        $successFailureCount = [
            'success' => 0,
            'failure' => 0
        ];

        foreach ($users as $user) {

            $pushNotificationResult = PushNotificationService::sendPushNotification($user->tokenFirebase, $pN->subject, $pN->content, $apiAccessKey);

            $pushResults = $pushNotificationResult['results'];
            $pushResultsObj = json_decode($pushResults);

            if (isset($pushResultsObj->success) && $pushResultsObj->success == 1) {
                ++$successFailureCount['success'];
            } else {
                ++$successFailureCount['failure'];
            }

            $pushLogObj = new LoginPushNotificationsLog([
                'userId' => $user->id,
                'loginPushId' => $pN->id,
                'results' => $pushResults ?? null,
                'status' => $pushNotificationResult['status'] ?? LoginPushNotifications::STATUS['error']
            ]);
            $pushLogObj->save();
        }

        $pN->status = LoginPushNotifications::STATUS['done'];
        $pN->results = json_encode($successFailureCount, JSON_PRETTY_PRINT);
        $pN->workerStatus = 2;
        $pN->save();
    }

    $cron->end();
} catch (Exception $e) {
    $arr = [
        'line' => $e->getLine(),
        'message' => $e->getMessage(),
        'file_path' => $e->getFile(),
        'trace' => $e->getTraceAsString()
    ];

    if(isset($pN)){
        $util = new Utils();
        $arr['data'] = json_encode($util->createArrayFromObj($pN),JSON_UNESCAPED_UNICODE);
    }

    $cron->cronLog($arr);
}
