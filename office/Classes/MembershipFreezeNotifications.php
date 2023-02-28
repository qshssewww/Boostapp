<?php

use Hazzard\Database\Model;

require_once __DIR__ . "/AppNotification.php";

/**
 * @property $id
 * @property $clientActivityId
 * @property $appnotificationId
 *
 * Class MembershipFreezeNotifications
 */
class MembershipFreezeNotifications extends Model
{
    protected $table = 'boostapp.membership_freeze_notifications';

    /**
     * @param $ActivityId
     * @return mixed
     */
    public static function findByClientActivityId($ActivityId)
    {
        return self::where("clientActivityId", "=", $ActivityId)->get();
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function deleteById($id)
    {
        return self::where('id', $id)->delete();
    }

    public static function checkByActivityId($ActivityId)
    {
        // check/delete freeze notifications
        $notificationInfos = self::findByClientActivityId($ActivityId);
        foreach ($notificationInfos as $notificationInfo) {
            /** @var AppNotification $notification */
            $notification = AppNotification::find($notificationInfo->appnotificationId);
            // delete if status active
            if ($notification->Status == 0) {
                AppNotification::deleteBulk([$notification->id]);
            }
            // delete old info
            self::deleteById($notificationInfo->id);
        }
    }
}
