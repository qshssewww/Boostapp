<?php

require_once __DIR__ . '/../../../../../../office/Classes/Section.php';

class ServiceDiaryResponse
{
    public $id;
    public $name;
    public $brandName;
    public $brandId;

    /**
     * ServiceDiaryResponse constructor.
     * @param Section $Diary
     */
    public function __construct(Section $Diary)
    {
        $this->id = (int)$Diary->sectionsId; //using Brand but main table Section
        $this->name = $Diary->Title;
        $this->brandName = $Diary->BrandName; ////using Brand but main table Section
        $this->brandId = $Diary->id;
    }


    /**
     * @return array
     */
    public function getData(): array
    {
        return (array)$this;
    }


}
