<?php
/**
 * @property $id
 * @property $client_id
 * @property $company_num
 * @property $name
 * @property $business_number
 * @property $business_type
 * @property $until_date
 * @property $color
 * @property $logo
 * @property $updated_at
 * @property $created_at
 *
 * Class UpdateBusinessNumber
 */

class UpdateBusinessNumber extends \Hazzard\Database\Model
{
    protected $table = '247softnew.update_business_number';
    public const BUSINESS_ARR = [
        2 => 'מורשה',
        3 => 'חברה בע"מ',
        5 => 'פטור',
        6 => 'מלכ"ר'
    ];
}