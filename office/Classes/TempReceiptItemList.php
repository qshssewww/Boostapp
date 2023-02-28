<?php

/**
 * @property $id
 * @property $doc_id
 * @property $item_id
 * @property $created_at
 */
class TempReceiptItemList extends \Hazzard\Database\Model
{
    protected $table = 'temp_receipt_itemlist';

    public static function getItemsByDocID($doc_id){
        return self::where('doc_id', $doc_id)
            ->get();
    }
}
