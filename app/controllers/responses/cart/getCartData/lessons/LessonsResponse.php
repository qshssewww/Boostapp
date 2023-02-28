<?php

require_once __DIR__ . '/../../../../BaseController.php';
require_once __DIR__ . '/../../../../../../office/Classes/ClassStudioDate.php';

class LessonsResponse
{
    public $backgroundColor;
    public $calendar_name;
    public $end;
    public $id;
    public $location;
    public $owner;
    public $participants;
    public $participantsMax;
    public $price_total;
    public $repeat_type;
    public $start;
    public $title;
    public $live;


    /**
     * LessonsResponse constructor.
     * @param ClassStudioDate $ClassStudioDate
     */
    public function __construct(ClassStudioDate $ClassStudioDate)
    {
        $this->backgroundColor = $ClassStudioDate->color;
        $this->calendar_name = $ClassStudioDate->getFloorName();
        $this->end = (new DateTime($ClassStudioDate->end_date))->format('Y-m-d\TH:i:s') ?? $ClassStudioDate->end_date;
        $this->id = (int)$ClassStudioDate->id;
        $this->location = $ClassStudioDate->getBrandName();
        $this->owner = $ClassStudioDate->GuideName;
        $this->participants = $ClassStudioDate->ClientRegister;
        $this->participantsMax = $ClassStudioDate->MaxClient;
        $this->price_total = null;
        $this->repeat_type = $ClassStudioDate->ClassType;
        $this->start = (new DateTime($ClassStudioDate->start_date))->format('Y-m-d\TH:i:s') ?? $ClassStudioDate->start_date;
        $this->title = $ClassStudioDate->ClassName;
        $this->live = !$ClassStudioDate->isFrontalClass();
    }
}
