<?php

require_once __DIR__ . '/DocsPayments.php';

/**
 * @property $id
 * @property $CompanyNum
 * @property $TrueCompanyNum
 * @property $TypeHeader
 * @property $TypeNumber
 * @property $TypeTitle
 * @property $TypeTitleSingle
 * @property $TypeList
 * @property $TypeNew
 * @property $Payment
 * @property $PaymentRole
 * @property $Remarks
 * @property $PaymentBalance
 * @property $Status
 * @property $DocsRemarks
 * @property $Misim
 * @property $DocsPayment
 * @property $ShowBalance
 * @property $TypeDocList
 * @property $Accounts
 * @property $ShowSelect
 */
class DocsTable extends \Hazzard\Database\Model
{

    public const STATUS_ACTIVE = 0;
    public const STATUS_OFF = 1;

    public const TYPE_RESERVATION = 100;
    public const TYPE_SHIPPING_DOCUMENTS = 200;
    public const TYPE_RETURN_CERTIFICATES = 210;
    public const TYPE_HESHBONIT_HESHKA = 300;
    public const TYPE_HESHBONIT_MAS = 305;
    public const TYPE_CONCENTRATION_INVOICES = 310; //not used
    public const TYPE_HESHBONIT_MAS_KABLA = 320;
    public const TYPE_HESHBONIT_MAS_ZIKUI = 330;
    public const TYPE_KABALA = 400;

    public const TYPE_HEADER_INVOICE = [
        self::TYPE_HESHBONIT_MAS,
        self::TYPE_HESHBONIT_MAS_KABLA,
    ];

    public const TYPE_HEADER_RECEIPT = [
        self::TYPE_KABALA,
        self::TYPE_HESHBONIT_MAS_KABLA,
    ];

    public const TYPE_HEADER_REFUND_INVOICE = [
        self::TYPE_HESHBONIT_MAS_ZIKUI,
        self::TYPE_HESHBONIT_HESHKA,
    ];

    protected $table = 'boostapp.docstable';

    private $_docsPayment;

    /**
     * @return DocsPayment|null
     */
    public function docsPayment()
    {
        if (empty($this->_docsPayment)) {
            $this->_docsPayment = DocsPayment::where('CompanyNum', $this->CompanyNum)->where('TypeDoc', $this->id)->first();
        }
        return $this->_docsPayment;
    }


    /**
     * @param int $trueCompanyNum
     * @param int $typeHeaderDoc
     * @return DocsTable|null
     */
    public static function getByTrueCompanyNumAndTypeHeader(int $trueCompanyNum, int $typeHeaderDoc): ?DocsTable
    {
        return self::where('TypeHeader', '=', $typeHeaderDoc)
            ->where('TrueCompanyNum', '=', $trueCompanyNum)
            ->where('Status', '=', self::STATUS_ACTIVE)
            ->first();
    }

    /**
     * @param $id
     * @return string
     */
    public static function getTypeTitleSingleById($id): string
    {
        return self::where('id', $id)
                ->pluck('TypeTitleSingle') ?? '';
    }

}
