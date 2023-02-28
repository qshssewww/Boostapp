<?php


class AssignMembership
{
    private $id;

    private $CompanyNum;

    private $classGroup;

    private $day;

    private $paymentId;

    private $date;

    private $table;

    public function __construct()
    {
        $this->table = "assignMembership";
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
}