<?php

/**
 * @property $MeetingTemplateId
 * @property $Status
 * @property $CoachId
 *
 * Class MeetingTemplatesCoaches
 */
class MeetingTemplatesCoaches extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.meeting_template_coaches';

    public const STATUS_ACTIVE = 1;
    public const STATUS_OFF = 0;

    /**
     * @param $meetingTemplateId
     * @return MeetingTemplatesCoaches[]
     */
    public static function getAllByMeetingTemplateId($meetingTemplateId): array
    {
        return self::where('MeetingTemplateId', '=', $meetingTemplateId)->get();
    }


    /**
     * @param int $meetingTemplateId
     * @param int $userId
     * @return MeetingTemplatesCoaches|null
     */
    public static function getByTemplateIdAndUserId(int $meetingTemplateId, int $userId): ?MeetingTemplatesCoaches
    {
        return self::where('MeetingTemplateId', '=', $meetingTemplateId)
            ->where('CoachId', $userId)
            ->where('Status', self::STATUS_ACTIVE)
            ->first();
    }

    /**
     * @param int $meetingTemplateId
     * @return int|null
     */
    public static function getFirstCoachToMeeting(int $meetingTemplateId): ?int
    {
        return self::where('MeetingTemplateId', '=', $meetingTemplateId)
            ->where('Status', self::STATUS_ACTIVE)
            ->pluck('CoachId');
    }

    public static $createRules = [
        'MeetingTemplateId' => 'required|exists:boostapp.meeting_templates,id',
        'Status' => 'integer|between:0,2',
        'CoachId' => 'required|integer',
    ];


}