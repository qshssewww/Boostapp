<?php

require_once __DIR__ . '/../../app/enums/ClassStudioDate/CancelReason.php';

/**
 * @property $classId
 * @property $reasonId
 * @property $createdAt
 */
class MeetingCancelReason extends \Hazzard\Database\Model
{
    protected $table = 'meeting_cancel_reason';

    /**
     * @param $classId
     * @return CancelReason|null
     */
    public static function getByClassId($classId): ?CancelReason
    {
        return self::where('classId', $classId)->first();
    }

    public function getReasonText($reasonId)
    {
        CancelReason::get($reasonId);
    }
}