<?php


class Banks
{
    /**
     * @var $id int
     */
    private $id;

    /**
     * @var $BankId int
     */
    private $BankId;

    /**
     * @var $BankName string
     */
    private $BankName;

    /**
     * @var $ShortName string
     */
    private $ShortName;


    private static $table = 'boostapp.banks';

    public function __construct($id = null)
    {
        if ($id != null) {
            $this->setData($id);
        }
    }

    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
        return null;
    }

    public function getBankById($id)
    {
        return DB::table(self::$table)->where('id', "=", $id)->get();
    }

    public function getAllBanks()
    {
        return DB::table(self::$table)->get();
    }

}
