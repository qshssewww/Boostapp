<?php

require_once __DIR__ . '/../../../../../../office/Classes/meetingClasses/MeetingTemplateClassType.php';
require_once __DIR__ . '/../../ItemBaseResponse.php';
require_once __DIR__ . '/MeetingItemResponse.php';

class ServiceResponse
{
    public $id; //meeting_category
    public $name; //meeting_category
    public $isFavorite;
    public $items; //array of meetingTemplate and classType

    /**
     * ServiceResponse constructor.
     * @param MeetingTemplateClassType $MeetingTemplateClassType
     */
    public function __construct(MeetingTemplateClassType $MeetingTemplateClassType)
    {
        $this->id = (int)$MeetingTemplateClassType->meetingCategory_id;
        $this->name =$MeetingTemplateClassType->meetingCategory_name;
        $this->isFavorite = (bool)$MeetingTemplateClassType->meetingCategory_favorite;
        $this->items = [];
    }

    /**
     * @param MeetingTemplateClassType $MeetingTemplateClassType
     * @param array $diariesIdsArray
     * @param array $coachIdsArray
     */
    public function addItem(MeetingTemplateClassType $MeetingTemplateClassType,array $diariesIdsArray = [] ,array $coachIdsArray = []): void
    {
        $this->items[] = new MeetingItemResponse($MeetingTemplateClassType, $diariesIdsArray , $coachIdsArray);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return (array)$this;
    }

}
