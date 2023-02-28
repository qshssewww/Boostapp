<?php

require_once __DIR__ . '/../../ItemBaseResponse.php';
require_once __DIR__ . '/../../../../../../office/Classes/Item.php';

class MeetingItemResponse extends ItemBaseResponse
{
    public $date; //today
    public $time; //now - duration
    public $durationMin;
    public $diaryId;
    public $coachId;


    /**
     * MeetingItemResponse constructor.
     * @param MeetingTemplateClassType $MeetingTemplateClassType
     * @param array $diariesIdsArray
     * @param array $coachIdsArray
     */
    public function __construct(MeetingTemplateClassType $MeetingTemplateClassType, array $diariesIdsArray, array $coachIdsArray)
    {
        parent::__construct($MeetingTemplateClassType->classType_id,
            $MeetingTemplateClassType->classType_price,
            $MeetingTemplateClassType->getDisplayName(),
            null,
            $MeetingTemplateClassType->classType_favorite);
        $this->durationMin = TimeHelper::returnMinuteTime($MeetingTemplateClassType->classType_duration,
            $MeetingTemplateClassType->classType_durationType);
        $this->date = date("Y-m-d");
        $timeMin = TimeHelper::roundDownToMinuteInterval(strtotime('-'.$this->durationMin.' minutes'), 5);
        $this->time = date("H:i", $timeMin);
        $this->diaryId = $MeetingTemplateClassType->getDefaultCalendar($diariesIdsArray);
        $this->coachId =  $MeetingTemplateClassType->getDefaultCoachId($coachIdsArray);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return (array)$this;
    }


}
