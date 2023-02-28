<?php

class Video
{
    /**
     * @var $id int
     */
    private $id;

    /**
     * @var $name string
     */
    private $name;

    /**
     * @var $CompanyNum int
     */
    private $CompanyNum;

    /**
     * @var $folderId int
     */
    private $folderId;

    /**
     * @var $guide int
     */
    private $guide;

    /**
     * @var $time DateTime
     */
    private $time;

    /**
     * @var $videoLink string
     */
    private $videoLink;

    /**
     * @var $externalLink string
     */
    private $externalLink;

    /**
     * @var $order int
     */
    private $order;

    /**
     * @var $display int
     */
    private $display;
    /**
     * @var $status int
     */
    private $status;

    /**
     * @var $date DateTime
     */
    private $date;

    /**
     * @var $table string
     */
    private $table;

    public function __construct($videoId = null)
    {
        $this->table = "boostapp.video";
        if($videoId != null){
            $this->setVideo($videoId);
        }
    }
    public function __set($name, $value)
    {
        if(property_exists($this,$name)){
            $this->$name = $value;
        }
    }

    public function __get($name)
    {
        if(property_exists($this,$name)){
            return $this->$name;
        }
        return null;
    }

    public function setVideo($videoId){
        $video = DB::table($this->table)->where("id", "=", $videoId)->first();
        if($video != null) {
            foreach ($video as $key => $value) {
                $this->__set($key, $value);
            }
        }
    }

    public static function addVideo($video){
        try {
            return DB::table('video')->insertGetId(
                [
                    'name' =>  $video->name, 
                    'folderId' =>  $video->folderId, 
                    'guide' =>  $video->guide,
                    'externalLink' =>  $video->externalLink,
                    'description' =>  $video->description,
                    'videoLink' => $video->externalLink,
                    'CompanyNum' => $video->companyNum,
                    'order'=>self::getHighestOrderNum($video->folderId)+1,
                    'time' => $video->duration
                ]); 
        }catch(Exception $e){
           return 'failed';
        }
    }

    public static function updateVideo($video){
        return DB::table('video')
              ->where('id', $video->id)
              ->update([
                  'name' => $video->name,
                  'folderId' => $video->folderId,
                  'guide' => $video->guide ,
                  'externalLink' => $video ->externalLink , 
                  'description' => $video->description,
                  'time' => $video->duration
                ] 
            );
    }

    public function getVideo($videoId){
        $video = DB::table($this->table)->where("id", "=", $videoId)->first();
        return $video;
    }
    static function getHighestOrderNum($folderId){
        $video = DB::table("boostapp.video")
        ->where("status", "=", 1)
        ->where("folderId","=", $folderId)
        ->max('order');
        return $video;
    }
    public function updateVideoOrder($videoIds=[]){
        $results=[];
        foreach($videoIds as $videoId){
            $video = DB::table($this->table)->where("id", "=", $videoId->id)->update(['order'=>$videoId->position]);
            $results[]=$video;
        } 
        return $results;
    }
    public function updateVideoDisplay($videoObj){
        $results=[];
        $video = DB::table($this->table)->where("id", "=", $videoObj->id)->update(['display'=>$videoObj->display]);
        $results[]=$video;
        return $results;
    }
    public function DeleteVideo($videoObj){
        $results=[];
        $video = DB::table($this->table)->where("id", "=", $videoObj->id)->update(['status'=>0]);
        $results[]=$video;
        return $results;
    }

    public function DeleteVideosByFolderId($folderObj){
        $results=[];
        $video = DB::table($this->table)->where("folderId", "=", $folderObj->id)->update(['status'=>0]);
        $results[]=$video;
        return $results;
    }

}