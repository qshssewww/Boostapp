<?php

require_once __DIR__ . '/../../../../../../office/Classes/Item.php';
require_once __DIR__ . '/../../ItemBaseResponse.php';

class ProductsResponse
{
    public $id;
    public $name;
    public $isFavorite;
    public $items;

    /**
     * ProductsResponse constructor.
     * @param ItemAndItemCat $ItemAndItemCat
     */
    public function __construct(ItemAndItemCat $ItemAndItemCat)
    {
        $this->id = $ItemAndItemCat->itemCat_id ? (int)$ItemAndItemCat->itemCat_id : 0;
        $this->name =$ItemAndItemCat->itemCat_name ?? lang('without_cat_app');
        $this->isFavorite = (bool)$ItemAndItemCat->itemCat_favorite;
        $this->items = [];
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
     * @param bool $isFavorite
     */
    public function setIsFavorite(bool $isFavorite = false): void
    {
        $this->isFavorite = $isFavorite;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param ItemAndItemCat $ItemAndItemCat
     */
    public function addItem(ItemAndItemCat $ItemAndItemCat): void
    {
        $this->items[] = new ItemBaseResponse((int)$ItemAndItemCat->item_id,
            $ItemAndItemCat->item_price,
            $ItemAndItemCat->item_name,
            null,
            $ItemAndItemCat->item_favorite);
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return (array)$this;
    }


}
