<?php

require_once "Utils.php";

class FormFields extends Utils
{
    protected $field_id;

    protected $name;

    protected $lead_default_field;

    protected $customer_default_field;

    protected $default_type;

    protected $default_options;

    protected $default_id;

    protected $enName;

    protected $for_minor;

    protected $system_field;

    private $table;

    public function __construct($id = null)
    {
        $this->table = "form_fields";
        if($id != null){
            $this->getFormFieldById($id);
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
    public function getFormFieldById($id){
        $form = DB::table($this->table)->where("field_id", "=", $id)->first();
        if($form != null) {
            foreach ($form as $key => $value) {
                $this->__set($key, $value);
            }
        }
    }

    public function updateFormFieldFromObj(){
        $formArr = $this->createArrayFromObj($this);
        $res = DB::table($this->table)->where("field_id", "=",$this->field_id)->update($formArr);
        return $res;
    }
    public function updateFormField($data,$id){
        $res = DB::table($this->table)->where("field_id", "=",$id)->update($data);
        return $res;
    }
    public function insertFormField(){
        $formArr = $this->createArrayFromObj($this);
        $fieldId = DB::table($this->table)->insertGetId($formArr);
        return $fieldId;
    }
    
    public function getDefaultFormFieldsByType($type){
        $fieldsType = "customer_default_field";
        if($type == "lead"){
            $fieldsType = "lead_default_field";
        }
        $fields = DB::table($this->table)
            ->where($fieldsType,"=",1)
            ->orderBy("for_minor", "desc")
            ->orderBy("default_id", "asc")
            ->get();
        $fieldsArr = array();
        foreach ($fields as $field){
            $fieldObj = new FormFields();
            foreach ($field as $key => $value) {
                $fieldObj->__set($key, $value);
            }
            array_push($fieldsArr,get_object_vars($fieldObj));
        }
        return $fieldsArr;
    }

    public function isDefault() {
        return $this->lead_default_field || $this->customer_default_field;
    }

    public function deleteViaFieldId($rowid){
        $deleted = DB::table($this->table)->where("customer_default_field","=",0)->where("lead_default_field","=",0)->where("field_id","=",$rowid)->delete();
        return $deleted;
    }

}
