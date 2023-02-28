<?php

/**
 * @property $id
 * @property $MeetingTemplateId
 * @property $Status
 * @property $CalendarId
 *
 * Class MeetingTemplatesCoaches
 */
class MeetingTemplatesCalendars extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.meeting_template_calendars';

    public const STATUS_ACTIVE = 1;
    public const STATUS_OFF = 0;

    /**
     * @param $meetingTemplateId
     * @return array
     */
    public function getAllByMeetingTemplateId($meetingTemplateId)
    {
        return self::where('MeetingTemplateId', '=', $meetingTemplateId)->get();
    }

    /**
     * @param int $meetingTemplateId
     * @return int|null
     */
    public static function getFirstCalendarToMeeting(int $meetingTemplateId): ?int
    {
        return self::where('MeetingTemplateId', '=', $meetingTemplateId)
            ->where('Status', self::STATUS_ACTIVE)
            ->pluck('CalendarId');
    }

    public static $createRules = [
        'MeetingTemplateId' => 'required|exists:boostapp.meeting_templates,id',
        'Status' => 'integer|between:0,2',
        'CalendarId' => 'required|integer',
    ];

}