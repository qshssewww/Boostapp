<?php

class ClientAdditionalContacts{
    /**
     * @var $id int
     */
    private $id;
    /**
     * @var $client_id int
     */
    private $client_id;
    /**
     * @var $phone string
     */
    private $phone;
    /**
     * @var $email string
     */
    private $email;
    /**
     * @var $relation string
     */
    private $relation;
    /**
     * @var $date DateTime
     */
    private $date;


    private $table;

    public function __construct($id = null){
        $this->table = "boostapp.clientAdditionalContacts";
        if($id != null){
            $this->setData($id);
        }
    }

    public function setData($id){
        $data = DB::table($this->table)->where("id", "=", $id)->first();
        if($data != null) {
            foreach ($data as $key => $value) {
                $this->__set($key, $value);
            }
        }
    }

    public function __set($name, $value){
        if(property_exists($this,$name)){
            $this->$name = $value;
        }
    }

    public function __get($name){
        if(property_exists($this,$name)){
            return $this->$name;
        }
        return null;
    }
    public function insert_into_table($data){
        $id =  DB::table($this->table)->insertGetId(
            $data);
        return $id;
    }
    public function getRow($id){
        $returnedObj = DB::table($this->table)->where("id", "=", $id)->first();
        return $returnedObj;
    }
    public function getAllRows(){
        $dataArray = DB::table($this->table)->get();
        return $dataArray;
    }
    public function getByClientId($client_id){
        $dataArray = DB::table($this->table)->where("client_id", "=", $client_id)->get();
        return $dataArray;
    }
}
?>
