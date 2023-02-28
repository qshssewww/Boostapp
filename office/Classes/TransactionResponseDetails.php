<?php

/**
 * @property $id
 * @property $shva
 * @property $id_code
 * @property $a_code
 * @property $created_at
 *
 * * *
 * Class TransactionResponseDetails
 * * */

class TransactionResponseDetails extends \Hazzard\Database\Model
{
    const YAAD = 0;
    const MESHULAM = 1;
    const TRANZILLA = 2;

    protected $table = 'boostapp.transaction_response_details';
    public $timestamps = false;

    public static function isPaymentExist(int $shva, string $id_code, string $a_code): bool {
        $req = self::where('shva', $shva)
            ->where('id_code', $id_code)
            ->where('a_code', $a_code)
            ->first();

        return (bool) $req;
    }
}
