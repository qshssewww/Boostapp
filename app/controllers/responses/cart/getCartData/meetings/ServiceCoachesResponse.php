<?php

require_once __DIR__ . '/../../../../../../office/Classes/Users.php';
require_once __DIR__ . '/../../../../../helpers/ImageHelper.php';


class ServiceCoachesResponse
{
    public $id;
    public $name;
    public $image;

    /**
     * ServiceCoachesResponse constructor.
     * @param Users $Coach
     */
    public function __construct(Users $Coach)
    {
        $this->id = (int)$Coach->id;
        $this->name = $Coach->display_name;
        $this->image = $Coach->UploadImage ? ImageHelper::getImageWithPrefix($Coach->UploadImage) : null;
    }


    /**
     * @return array
     */
    public function getData(): array
    {
        return (array)$this;
    }


}
