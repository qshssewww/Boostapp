<?php

use Hazzard\Database\Model;



/**
 * @property $id
 * @property $CompanyNum
 * @property $Title
 * @property $PercentTd
 * @property $NameDb
 * @property $Status
 * @property $Sign
 * @property $OrderBy
 * @property $NumberFormat
 * @property $EditTable
 */
class DocsDetails extends Model
{
    protected $table = 'boostapp.docsdetails';

    public const FIELD_ARRAY_OLD = [
        'makat' => [
            'Title' => "מק\"ט",
            'PercentTd' => 10,
            'NameDb' => 'ItemId',
            'NumberFormat' => 1,
            'Sign' => '',
            'Status' => 0
        ],
        'barcode' => [
            'Title' => "ברקוד",
            'PercentTd' => 10,
            'NameDb' => 'ItemId',
            'NumberFormat' => 1,
            'Sign' => '',
            'Status' => 1
        ],
        'itemName' => [
            'Title' => "שם הפריט",
            'PercentTd' => 20,
            'NameDb' => 'ItemName',
            'NumberFormat' => 1,
            'Sign' => '',
            'Status' => 1
        ],
        'color' => [
            'Title' => "צבע",
            'PercentTd' => 10,
            'NameDb' => 'ItemId',
            'NumberFormat' => 1,
            'Sign' => '',
            'Status' => 1
        ],
        'size' => [
            'Title' => "מידה",
            'PercentTd' => 10,
            'NameDb' => 'ItemId',
            'NumberFormat' => 1,
            'Sign' => '',
            'Status' => 0
        ],
        'itemPrice' => [
            'Title' => "מחיר ליח'",
            'PercentTd' => 8,
            'NameDb' => 'ItemPriceVat',
            'NumberFormat' => 0,
            'Sign' => '₪',
            'Status' => 0
        ],
        'itemQuantity' => [
            'Title' => "כמות",
            'PercentTd' => 8,
            'NameDb' => 'ItemQuantity',
            'NumberFormat' => 0,
            'Sign' => '',
            'Status' => 0
        ],
        'discount' => [
            'Title' => "הנחה",
            'PercentTd' => 8,
            'NameDb' => 'ItemDiscount',
            'NumberFormat' => 0,
            'Sign' => '₪',
            'Status' => 0
        ],
        'totalPrice' => [
            'Title' => "סה\"כ",
            'PercentTd' => 14,
            'NameDb' => 'ItemPriceVatDiscount',
            'NumberFormat' => 0,
            'Sign' => '₪',
            'Status' => 0
        ]
    ];

    public const FIELD_ARRAY_NEW = [
        'makat' => [
            'Title' => "מק\"ט",
            'PercentTd' => 10,
            'NameDb' => 'ItemId',
            'NumberFormat' => 1,
            'Sign' => '',
            'Status' => 0
        ],
        'barcode' => [
            'Title' => "ברקוד",
            'PercentTd' => 10,
            'NameDb' => 'ItemId',
            'NumberFormat' => 1,
            'Sign' => '',
            'Status' => 1
        ],
        'itemName' => [
            'Title' => "שם הפריט",
            'PercentTd' => 20,
            'NameDb' => 'ItemName',
            'NumberFormat' => 1,
            'Sign' => '',
            'Status' => 1
        ],
        'color' => [
            'Title' => "צבע",
            'PercentTd' => 10,
            'NameDb' => 'ItemId',
            'NumberFormat' => 1,
            'Sign' => '',
            'Status' => 1
        ],
        'size' => [
            'Title' => "מידה",
            'PercentTd' => 10,
            'NameDb' => 'ItemId',
            'NumberFormat' => 1,
            'Sign' => '',
            'Status' => 0
        ],
        'itemPrice' => [
            'Title' => "מחיר ליח'",
            'PercentTd' => 8,
            'NameDb' => 'ItemPrice',
            'NumberFormat' => 0,
            'Sign' => '₪',
            'Status' => 0
        ],
        'itemQuantity' => [
            'Title' => "כמות",
            'PercentTd' => 8,
            'NameDb' => 'ItemQuantity',
            'NumberFormat' => 0,
            'Sign' => '',
            'Status' => 0
        ],
        'discountCalc' => [
            'Title' => "הנחה",
            'PercentTd' => 8,
            'NameDb' => 'ItemDiscountAmount',
            'NumberFormat' => 0,
            'Sign' => '₪',
            'Status' => 0
        ],
        'totalPrice' => [
            'Title' => "סה\"כ",
            'PercentTd' => 14,
            'NameDb' => 'Itemtotal',
            'NumberFormat' => 0,
            'Sign' => '₪',
            'Status' => 0
        ]
    ];

    /**
     * @param $companyNum
     * @return DocsDetails[]
     */
    public static function getAllNotActiveByCompany($companyNum): array
    {
        return self::where('CompanyNum', $companyNum)->where('Status', 1)->get();
    }

    /**
     * @param $companyNum
     * @param bool $isNew
     * @return array[]
     */
    public static function getDocsFieldArrayByCompany($companyNum, $isNew = true): array
    {
        $response = $isNew ? self::FIELD_ARRAY_NEW : self::FIELD_ARRAY_OLD;
        $docsDetailsArray = self::getAllNotActiveByCompany($companyNum);
        foreach ($docsDetailsArray as $docsDetails) {
            switch ($docsDetails->Title) {
                case 'ברקוד':
                    unset($response['barcode']);
                    break;
                case 'צבע':
                    unset($response['color']);
                    break;
                case 'מידה':
                    unset($response['size']);
                    break;
                case 'מק\"ט':
                    unset($response['makat']);
                    break;
            }
        }
        return $response;
    }



}
