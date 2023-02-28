<?php

require_once __DIR__ . '/LoggerService.php';
require_once __DIR__ . '/../Classes/Item.php';


/**
 * Class ItemService
 */
class ItemService
{
    public static function getGeneralItem($companyNum): int
    {
        $itemId = Item::getGeneralItem($companyNum);
        if ($itemId === 0) {
            return Item::createGeneralItem($companyNum);
        }
        return $itemId;
    }
}
