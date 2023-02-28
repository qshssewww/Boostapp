<?php


class VideoLimit
{
    /**
     * @var $id int
     */
    private $id;
    /**
     * @var $folderId int
     */
    private $folderId;
       /**
     * @var $membership string
     */
    private $membership;
    /**
     * @var $table string
     */
    private $table;

    private $date;

    private $update_date;

    public function __construct($itemId = null)
    {
        $this->table = "videoLimit";
        if($itemId != null){
            $this->getItemById($itemId);
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
    public function createArrFromObj(){
        $returnedArray=get_object_vars($this);
        $returnedArray["table"]=null;
        return $returnedArray;
    }

    public function getItemById($itemId){
        $item = DB::table($this->table)->where("id", "=", $itemId)->first();
        if($item != null) {
            foreach ($item as $key => $value) {
                $this->__set($key, $value);
            }
            return $item;
        }else{
            return null;
        }
    }
    public function getItemByFolderId($folderId){
        $item = DB::table($this->table)->where("folderId", "=", $folderId)->first();
        if($item != null) {
            foreach ($item as $key => $value) {
                $this->__set($key, $value);
            }
            return $item;
        }else{
            return null;
        }
    }
    public  function addOrUpdateLimit($folderObj){
        try {
            $item=DB::table($this->table)->where("folderId", "=", $folderObj->id)->update(  
                [
                'folderId'=>$folderObj->id,
                'membership' =>  implode(",",$folderObj->membership)
                ]
            );
            if($item){
            return $item;
            }else{
                $item=DB::table($this->table)->insertGetId(  
                    [
                    'folderId'=>$folderObj->id,
                    'membership' =>  implode(",",$folderObj->membership)
                    ]
                    );
                    return $item;
            }
        }catch(Exception $e){
           return 'failed';
        }
    }

}