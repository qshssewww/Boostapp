<?php

use Hazzard\Database\Model;

/**
 * @property $id
 * @property $email
 * @property $Status
 * @property $calculation
 * @property $name
 * @property $phone
 * @property $business_number
 * @property $email_sent
 * @property $form_completed
 * @property $company_num
 * @property $by_user_id
 * @property $form_step
 * @property $created_at
 * @property $updated_at
 *
 * Class InsurancePopupEntries
 */

class InsurancePopupEntries extends Model
{
    protected $table = 'insurance_popup_entries';

    public const FORMULA_ARR = [
        "regular" => [
            "name" => "Regular",
            "1000000" => [
                0,
                995,
                1493,
                1991,
                2986,
                3981,
                4976
            ],
            "500000" => [
                0,
                665,
                997,
                1329,
                1994,
                2659
            ]
        ],
        "crossfit" => [
            "name" => "CrossFit Coach",
            "1000000" => [
                0,
                1194,
                1791,
                2388,
                3582,
                4776
            ],
            "500000" => [
                0,
                798,
                1197,
                1596,
                2394,
                3192
            ]
        ]
    ];

    /**
     * @param $phone
     * @return true | false
     */
    public static function isPhoneExist($phone) {
        if(empty($phone)) {
            return false;
        }
        return self::where('company_num', '=', Auth::user()->CompanyNum)
            ->where('form_completed', 1)
            ->where('email_sent', 1)
            ->where('phone', '=', $phone)
            ->exists();
    }

    /**
     * @param $id
     * @param $data
     * @return true | false
     */
    public static function update($id, $data) {
        return self::where('id', $id)->update($data);
    }

}