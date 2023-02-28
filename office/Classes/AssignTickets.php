<?php


class AssignTickets
{
    private $id;

    private $CompanyNum;

    private $classId;

    private $classDate;

    private $paymentId;

    private $date;

    private $table;

    public function __construct()
    {
        $this->table = "assignTickets";
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