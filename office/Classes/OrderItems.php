<?php

require_once "Utils.php";

class OrderItems extends Utils
{
    private static $table = "boostapp.order_items";

    protected $id;
    protected $OrderId;
    protected $CartId;
    protected $ItemId;
    protected $ItemDetailsId;
    protected $Amount;
    protected $TotalAmount;
    protected $Discount;
    protected $Quantity;
    protected $ExtraFees;
    protected $CreatedAt;

    public function __construct($id = null) {
        if ($id) {
            $data = DB::table(self::$table)->where("id", $id)->first();
            $this->setData($data);
        }
    }

    public function setData($data) {
        if ($data) {
            foreach ($data as $key => $value) {
                $this->__set($key, $value);
            }
        }
    }

    public function __set($name, $value) {
        if(property_exists($this,$name)){
            $this->$name = $value;
        }
    }

    public function __get($name) {
        if(property_exists($this, $name)){
            return $this->$name;
        }
        return null;
    }

    public static function insert_into_table($data) {
        return DB::table(self::$table)->insertGetId($data);
    }

    public function setDataByItemAndOrder($itemId, $orderId, $itemDetailsId=0){

        $query = DB::table('order_items')->where('OrderId', $orderId)->where('ItemId', $itemId);
        if($itemDetailsId !=0 ) {
            $query = $query->where('ItemDetailsId', $itemDetailsId);
        }

        $data = $query->first();
        $this->setData($data);
    }

    public static function getOrderQuantity($docId, $itemId) {
        $quantity = DB::table(self::$table)->join('docs', 'order_items.orderId', '=', 'docs.OrderId')
            ->where('docs.id', "=", $docId)->where('ItemId', '=', $itemId)
            ->pluck('order_items.Quantity');
        return is_numeric($quantity) ? $quantity : 1;

    }

}