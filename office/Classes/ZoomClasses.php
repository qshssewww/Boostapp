<?php

class ZoomClasses
{
    /**
     * @var $id int
     */
    private $id;

    /**
     * @var $class_id int
     */
    private $class_id;

    /**
     * @var $membership_type int
     */
    private $membership_type;

    /**
     * @var $CompanyNum int
     */
    private $CompanyNum;

    /**
     * @var $meeting_id int
     */
    private $meeting_id;

    /**
     * @var $single_reg int
     */
    private $single_reg;

    /**
     * @var $single_price double
     */
    private $single_price;

    /**
     * @var $external_video int
     */
    private $external_video;

    /**
     * @var $video_link string
     */
    private $video_link;

    /**
     * @var $save_video int
     */
    private $save_video;

    /**
     * @var $video_folder string
     */
    private $video_folder;

    /**
     * @var $chat int
     */
    private $chat;

    /**
     * @var $share_video int
     */
    private $share_video;

    /**
     * @var $audio int
     */
    private $audio;

    /**
     * @var $password string
     */
    private $password;

    /**
     * @var $date DateTime
     */
    private $date;

    /**
     * @var $update_date DateTime
     */
    private $update_date;

    /**
     * @var $table string
     */
    private $table;

    public function __construct($class_id = null)
    {
        $this->table = "boostapp.class_zoom";
        if($class_id != null){
            $this->getZoomByClassId($class_id);
        }
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if(property_exists($this,$name)){
            $this->$name = $value;
        }
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if(property_exists($this,$name)){
            return $this->$name;
        }
        return null;
    }
    public function getZoomByClassId($classId){
        $classes = DB::table($this->table)->where("class_id", "=", $classId)->first();
        if($classes) {
            foreach ($classes as $key => $value){
                $this->__set($key,$value);
            }
        }
    }
    public function insertNewZoomClass($data){
        return DB::table('boostapp.class_zoom')->insertGetId($data);
    }
}