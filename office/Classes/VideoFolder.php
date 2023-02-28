<?php

require_once "Video.php";

class VideoFolder
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
     * @var $display int
     */
    private $display;
    /**
     * @var $showForAll int
     */
    private $showForAll;

    /**
     * @var $order int
     */
    private $order;

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

    public function __construct($folderId = null)
    {
        $this->table = "boostapp.videoFolder";
        if($folderId != null){
            $this->setFolder($folderId);
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
    public function getVideoFolder($videoId){
        $video = DB::table($this->table)->where("id", "=", $videoId)->first();
        return $video;
    }
    public function getVideoFolderObect($videoId){
        $video = DB::table($this->table)->where("id", "=", $videoId)->first();
        $videoObj = new Video();
        foreach ($video as $key => $value){
            $videoObj->__set($key,$value);
        }
        return $videoObj;
    }

    public function setFolder($folderId){
        $folder = DB::table($this->table)->where("id", "=", $folderId)->first();
        if($folder != null) {
            foreach ($folder as $key => $value) {
                $this->__set($key, $value);
            }
        }
    }
    public function getFolders(){
        $folders = DB::table($this->table)
        ->where("CompanyNum","=" , $this->CompanyNum)->where("status","=" , 1)
        ->get();
        return $folders;
        }
    public function getCompanyFolders($companyNum = null){
        if($companyNum == null){
            if($this->CompanyNum == null){
                return null;
            }
            else{
                $companyNum = $this->CompanyNum;
            }
        }
        $folders = DB::table($this->table)
        ->where("companyNum", "=",$companyNum)
        ->where("status","=", 1)
        ->orderBy("order","asc")->get();

        // return $folders;

        if(!empty($folders)){
            $companyFolder = array();
            foreach ($folders as $folder){
                $folderObj = new VideoFolder();
                foreach ($folder as $key => $value){
                    $folderObj->__set($key,$value);
                }
                array_push($companyFolder,$folderObj);
            }
            return $companyFolder;
        }
        return null;
    }
    public function getFolderVideos($folderId = null){
        if($folderId == null){
            if($this->id == null){
                return null;
            }
            else{
                $folderId = $this->id;
            }
        }
        $videos = DB::table("boostapp.video")->where("folderId", "=",$folderId)->where("status","=", 1)->orderBy("order","asc")->get();
        if(!empty($videos)){
            $folderVideos = array();
            foreach ($videos as $video){
                $videoObj = new Video();
                foreach ($video as $key => $value){
                    $videoObj->__set($key,$value);
                }
                array_push($folderVideos,$videoObj);
            }
            return $folderVideos;
        }
        return null;
    }
    
    public function updateFolderNameAndLimit($folderObj){
        return DB::table($this->table)
              ->where('id', $folderObj->id)
              ->update([
                  'name' => $folderObj->name,
                  'showForAll' => $folderObj->showForAll
                ] 
            );
    }
    public function updateFolderOrder($folderIds=[]){
        $results=[];
        foreach($folderIds as $folderId){
            $folder = DB::table($this->table)->where("id", "=",$folderId->id)->update(['order'=>$folderId->position]);
            $results[]=$folder;
        } 
        return $results;
    }
    public function updateFolderDisplay($folderObj){
        $results=[];
        $folder = DB::table($this->table)->where("id", "=", $folderObj->id)->update(['display'=>$folderObj->display]);
        $results[]=$folder;
        return $results;
    }
    public function DeleteFolder($folderObj){
        $results=[];
        $folder = DB::table($this->table)->where("id", "=", $folderObj->id)->update(['status'=>0]);
        $results[]=$folder;
        return $results;
    }
    public static function addVideoFolder($folderObj){
        try {
            return DB::table('boostapp.videoFolder')->insertGetId(
                [
                    'name' =>  $folderObj->name, 
                    'CompanyNum' => $folderObj->CompanyNum,
                    'display' =>  $folderObj->display,
                    'order'=>self::getHighestOrderNum($folderObj->CompanyNum)+1,
                    'status' =>  1,
                    'showForAll' =>  $folderObj->showForAll
                ]); 
        }catch(Exception $e){
            var_dump($e);
        //    return 'failed';
        }
    }

static function getHighestOrderNum($companyNum){
    $videoFolder = DB::table("boostapp.videoFolder")
    ->where("status", "=", 1)
    ->where("CompanyNum", "=", $companyNum)
    ->max('order');
    return $videoFolder;
}
}