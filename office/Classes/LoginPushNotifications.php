<?php

/**
 * @property $id
 * @property $companyNym
 * @property $status
 * @property $date
 * @property $time
 * @property $permission
 * @property $subject
 * @property $content
 * @property $results
 * @property $workerStatus
 * @property $createdAt
 **
 * Class LoginPushNotifications
 **/

class LoginPushNotifications extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.login_push_notifications';

    const PUSH_NOTIFICATIONS_ID = [
        'login_min_participants_not_reached' => 185,
        'login_assignment_not_handled' => 186,
        'login_today_assignment_reminder' => 187,
        'login_horaat_keva_failed' => 188,
        'login_regular_class_limit_alert' => 189,
        'app_client_disconnect_from_app' => 191,
        'app_client_payment' => 195,
        'app_client_meeting_cancel' => 196
    ];

    const STATUS = [
        'active' => 0,
        'done' => 1,
        'error' => 2
    ];

    public static function sendLoginPushNotification($companyNum, $permission, $subject, $content, $date, $time) {
        self::insert([
            'companyNum' => $companyNum,
            'permission' => $permission,
            'subject' => strip_tags($subject),
            'content' => strip_tags($content),
            'date' => $date,
            'time' => $time
        ]);
    }

    public static function setError($id, $status, $results, $workerStatus = 2) {
        return self::where('id', $id)->update([
            'status' => $status,
            'results' => $results,
            'workerStatus' => $workerStatus
        ]);
    }
}
