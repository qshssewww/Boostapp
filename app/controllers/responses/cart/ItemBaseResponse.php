<?php

require_once __DIR__ . '/../../../../office/Classes/Item.php';

class ItemBaseResponse
{
    public $id;
    public $price;
    public $name;
    public $shortName;
    public $isFavorite;

    /**
     * ItemBaseResponse constructor.
     * @param int $id itemId
     * @param floot|int $price
     * @param string $itemName
     * @param ?string $clubMemberShipName if club_memberships need create name if not from item
     * @param bool false $isFavorite
     */
    public function __construct(int $id, $price = 0, string $itemName = '', $clubMemberShipName = null, bool $isFavorite = false)
    {
        $this->id = $id;
        $this->price = $price;
        $this->name = $itemName;
        $this->shortName = $clubMemberShipName ?? null;
        $this->isFavorite = $isFavorite;
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
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price): void
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getIsFavorite()
    {
        return $this->isFavorite;
    }

    /**
     * @param mixed $isFavorite
     */
    public function setIsFavorite($isFavorite): void
    {
        $this->isFavorite = $isFavorite;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return (array)$this;
    }

}
