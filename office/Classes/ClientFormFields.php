<?php

require_once "Utils.php";

class ClientFormFields extends Utils
{

    /**
     * @var $field_id int
     */
    protected $field_id;

    /**
     * @var $name string
     */
    protected $name;

    /**
     * @var $customer_default_field boolean
     */
    protected $customer_default_field;

    /**
     * @var $lead_default_field boolean
     */
    protected $lead_default_field;

    protected $form_id;

    /**
     * @var $mandatory boolean
     */
    protected $mandatory;

    /**
     * @var $show boolean
     */
    protected $show;

    /**
     * @var $order int
     */
    protected $order;

    protected $type;

    protected $options;

    /**
     * @var FormFields
     */
    private $table;

    /**
     * @var $status boolean
     */
    protected $status;

    public function __construct()
    {
        $this->table = "client_form_fields";
    }

    public function updateClientFormFields(){
        $formArr = $this->createArrayFromObj($this);
        if(!isset($formArr["options"])){
            $formArr["options"] = null;
        }
        $res = DB::table($this->table)->where("field_id", "=",$this->field_id)->Where("form_id","=",$this->form_id)->update($formArr);
        return $res;
    }

    public function insertClientFormField(){
        $formArr = $this->createArrayFromObj($this);
        if(!isset($formArr["options"])){
            $formArr["options"] = null;
        }

        $fieldId = DB::table($this->table)->insertGetId($formArr);
        return $fieldId;
    }

    public function getCountOfRecordById($id){
        $countOfRecords = DB::table($this->table)->where("form_id", "=",$id)->count();
        return $countOfRecords;
    }

    public function deleteViaFormAndFieldId($form_id,$field_id){
        $deleted = DB::table($this->table)->where("form_id","=",$form_id)->where("field_id","=",$field_id)->update(["status" => 0]);
        return $deleted;
    }

    public function updateClientFields($data,$id,$form_id){
        return DB::table($this->table)->where("field_id","=",$id)->where("form_id","=",$form_id)->update($data);

    }


    public function getClientFormFields($type,$form_id = null){
        if($form_id != null){
            $fields = DB::table($this->table)->leftJoin('form_fields','form_fields.field_id','=','client_form_fields.field_id')->
            where("client_form_fields.form_id","=",$form_id)->
            orderBy('client_form_fields.order', 'asc')->get();
            $fieldsArr = array();
            foreach ($fields as $field){
                $fieldObj = new ClientFormFields();
                foreach ($field as $key => $value) {
                    $fieldObj->__set($key, $value);
                }
                $fieldObj->fields = $this->arrayIntoObject($field,"FormFields");
                array_push($fieldsArr,$fieldObj);
            }
        }
        else{ 
            $fieldsArr = array();
        }
        return $fieldsArr;
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
    public function returnThisAsArray(){
        $returnedArray=get_object_vars($this);
        $returnedArray["table"]=null;
        return $returnedArray;
    }
}
