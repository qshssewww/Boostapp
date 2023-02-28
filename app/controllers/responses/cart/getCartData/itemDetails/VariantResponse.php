<?php

require_once __DIR__ . '/../../../../../../office/Classes/cartClasses/ProductDetails.php';

class VariantResponse
{

    public $id;
    public $inventory;

    /**
     * VariantResponse constructor.
     * @param int $id
     * @param int $inventory
     */
    public function __construct(int $id, int $inventory)
    {
        $this->id = $id;
        $this->inventory = $inventory;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getInventory()
    {
        return $this->inventory;
    }

    /**
     * @param mixed $inventory
     */
    public function setInventory($inventory): void
    {
        $this->inventory = $inventory;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return (array)$this;
    }




}
