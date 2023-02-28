<?php


/**
 * @property $id
 * @property $Type
 */
class BusinessType extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.businesstype';

    public const BUSINESS_TYPE_PRIVATE_CLIENT = 1;
    public const BUSINESS_TYPE_CERTIFICATE_LICENSED_DEALER = 2;
    public const BUSINESS_TYPE_PRIVATE_COMPANY = 3;
    public const BUSINESS_TYPE_PUBLIC_COMPANY = 4;
    public const BUSINESS_TYPE_EXEMPT_DEALER = 5;
    public const BUSINESS_TYPE_MELCHER = 6;
    public const BUSINESS_TYPE_GOVERNMENT_OFFICE= 7;

    public const BUSINESS_TYPE_WITH_OUT_VAT = [
        self::BUSINESS_TYPE_PRIVATE_CLIENT,
        self::BUSINESS_TYPE_EXEMPT_DEALER,
        self::BUSINESS_TYPE_MELCHER,
    ];

}
