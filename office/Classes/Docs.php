<?php

require_once __DIR__ . '/DocsPayments.php';
require_once __DIR__ . '/DocsTable.php';
require_once __DIR__ . '/DocsList.php';
require_once __DIR__ . '/DocsClientActivities.php';
require_once __DIR__ . '/Client.php';
require_once __DIR__ . '/Street.php';
require_once __DIR__ . '/City.php';
require_once __DIR__ . '/Settings.php';

/**
 * @property $id
 * @property $CompanyNum
 * @property $TrueCompanyNum
 * @property $Brands
 * @property $TypeDoc
 * @property $TypeHeader
 * @property $TypeNumber
 * @property $ClientId
 * @property $UserDate
 * @property $Dates
 * @property $OrderId
 * @property $Amount
 * @property $Vat
 * @property $VatAmount
 * @property $DiscountType
 * @property $Discount
 * @property $DiscountAmount
 * @property $PaymentRole
 * @property $Remarks
 * @property $Company
 * @property $CompanyId
 * @property $ContactName
 * @property $Mobile
 * @property $Phone
 * @property $Fax
 * @property $Email
 * @property $Status
 * @property $UserId
 * @property $ManualInvoice
 * @property $NikuyMsBamakorType
 * @property $NikuyMsBamakor
 * @property $NikoyMasAmount
 * @property $DocConvert
 * @property $PageId
 * @property $AutoPayment
 * @property $AutoPaymentId
 * @property $PaymentTime
 * @property $BalanceAmount
 * @property $Street
 * @property $Number
 * @property $PostCode
 * @property $Cancel
 * @property $City
 * @property $TextTitle
 * @property $TextId
 * @property $Minus
 * @property $Accounts
 * @property $DocDate
 * @property $DocMonth
 * @property $DocYear
 * @property $DocTime
 * @property $RandomUrl
 * @property $ActivityJson
 * @property $Refound
 * @property $PayStatus
 * @property $BusinessCompanyId
 * @property $BusinessType
 * @property $TypeShva
 * @property $RefAction
 * @property $refundAmount
 * @property $CpaType
 */
class Docs extends \Hazzard\Database\Model
{
    protected $table = 'boostapp.docs';
    private $_docsPayment;
    private $_clientActivities;

    public const PAY_STATUS_OPEN = 0;
    public const PAY_STATUS_CLOSE = 1;
    public const PAY_STATUS_HALF_CLOSE = 2;
    public const PAY_STATUS_INVOICE = 3;
    public const PAY_STATUS_CANCELED = 6;

    public const REFUND_STATUS_OFF = 0;
    public const REFUND_STATUS_ON = 1;
//    public const REFUND_STATUS_ON_HALF = 2;

    public const AFTER_REF_ACTION = 1;
    public const BEFORE_REF_ACTION = 0;

    public const CPA_TYPE_DUE_PAYMENT_DATE = 0; // בעת מועד פרעון
    public const CPA_TYPE_DUE_CREATE_RECEIPT = 1; // בעת הפקת קבלה

    public const STATUS_SOURCE = 1;
    public const STATUS_COPY = 2;

    /**
     * @return DocsPayment
     */
    public function docsPayment(): ?DocsPayment
    {
        if (empty($this->_docsPayment)) {
            $this->_docsPayment = DocsPayment::where('DocsId', $this->id)->first();
        }
        return $this->_docsPayment;
    }

    /**
     * @return ClientActivities[]|stdClass[]
     */
    public function clientActivities(): array
    {
        if (empty($this->_clientActivities)) {
            $this->_clientActivities = DocsClientActivities::getClientActivities($this->id);
        }
        return $this->_clientActivities;
    }

    public static function isStudioHasDocs($company_num) {
        $res = DB::table(self::getTable())
            ->where('CompanyNum', $company_num)
            ->first();

        return !empty($res);
    }

    /**
     * @param int $trueCompanyNum
     * @param int $typeHeaderDoc
     * @return int 0- first doc
     */
    public static function getDocsLastNumber(int $trueCompanyNum, int $typeHeaderDoc): int
    {
        return self::where('TrueCompanyNum', '=', $trueCompanyNum)
            ->where('TypeHeader', '=', $typeHeaderDoc)
            ->orderBy('id', 'DESC')
            ->pluck('TypeNumber') ?? 0;
    }

    /**
     * getDocByTypeHeaderAndTypeNumber function
     * @param int $companyNum
     * @param int $typeHeader
     * @param int $typeNumber
     * @param int|null $clientId
     * @return Docs|null
     */
    public static function getDocByTypeHeaderAndTypeNumber(int $companyNum, int $typeHeader, int $typeNumber, ?int $clientId = null): ?Docs{
        $Doc = self::where('TrueCompanyNum', '=', $companyNum)->where('TypeHeader', '=', $typeHeader)->where('TypeNumber', '=', $typeNumber);
        if($clientId)
        {
            $Doc->where('ClientId', '=', $clientId);
        }
        return $Doc->first();
    }

    /**
     * getDocsByFilters function
     * @param int $companyNum
     * @param int|null $clientId
     * @param int|null $typeHeader
     * @param int|null $typeNumber
     * @return Docs[]|null
     */
    public static function getDocsByFilters(int $companyNum, ?int $clientId = null, ?int $typeHeader = null, ?int $typeNumber = null): ?array{
        $doc = self::where('TrueCompanyNum', '=', $companyNum);
        if($clientId) {
            $doc->where('ClientId', '=', $clientId);
        }
        if($typeHeader) {
            $doc->where('TypeHeader', '=', $typeHeader);
        }
        if($typeNumber) {
            $doc->where('TypeNumber', '=', $typeNumber);
        }
        return $doc->get();
    }

    /**
     * @param $Settings
     */
    public function setPropertiesBySettings($Settings): void
    {
        $this->CompanyNum = $Settings->CompanyNum;
        $this->Vat = Settings::getVatByBusinessType($Settings);
        $this->BusinessCompanyId = $Settings->CompanyId;
        $this->BusinessType = $Settings->BusinessType;
        $this->TypeShva = $Settings->TypeShva;
        $this->CpaType = $Settings->CpaType;
    }

    /**
     * @param Client $Client
     */
    public function setPropertiesByClient(Client $Client): void
    {
        $this->Brands = $Client->Brands;
        $this->ClientId = $Client->id;
        $this->Company = empty($Client->Company) ? $Client->CompanyName : $Client->Company;
        $this->CompanyId = $Client->CompanyId;
        $this->ContactName = $Client->CompanyName;
        $this->Mobile = $Client->ContactMobile;
        $this->Phone = $Client->ContactPhone;
        $this->Fax = $Client->ContactFax;
        $this->Email = $Client->Email;
        $this->Street = $Client->getStreetName();
        $this->Number = $Client->Number;
        $this->PostCode = $Client->PostCode;
        $this->City = $Client->getCityName();
    }

    /**
     * @param DocsTable $DocsTable
     */
    public function setPropertiesByDocsTable(DocsTable $DocsTable): void
    {
        $this->TypeDoc = $DocsTable->id;
        $this->TypeHeader = $DocsTable->TypeHeader;
        $this->Accounts = $DocsTable->Accounts;
        $this->Remarks = $DocsTable->DocsRemarks ?? '';
    }


    /**
     * @param string $currentDate
     */
    public function setPropertiesOfDateDoc(string $currentDate = ''): void
    {
        $currentDate = $currentDate === '' ? date('Y-m-d'): $currentDate;
        $this->DocDate = $currentDate;
        $this->DocMonth = date("m", strtotime($currentDate));
        $this->DocYear = date("Y", strtotime($currentDate));
        $this->DocTime = date('H:i:s');
    }

    /**
     * @param array $docDetailsArray
     */
    public function setPropertiesOfDocDetailsArray(array $docDetailsArray = []): void
    {
        foreach ($docDetailsArray as $propertyName => $docDetail) {
            $this->$propertyName = $docDetail;
        }
        $discountAmount = $this->DiscountAmount ?? 0;
        $this->Amount = ($this->Amount - $discountAmount ) ?? 0;
        $this->BalanceAmount = $this->BalanceAmount ?? $this->Amount;
        $this->Vat = $this->Vat ?? 0;
        $this->VatAmount = $this->VatAmount ?? PriceHelper::getVatAmount($this->Amount, $this->Vat);
        $this->Status = $this->Status ?? 1; //first origen
        $this->DocConvert = $this->DocConvert ?? 0;
        $this->PaymentRole = $this->PaymentRole ?? 0;//paymentrole table
        $this->RandomUrl = $this->RandomUrl ?? GroupNumberHelper::generate();
    }

    /**
     * @return bool
     */
    public function removeDocsAndDocList(): bool
    {
        DocsList::removeByDocsId($this->id);
        return $this->delete() ?? false;
    }

    /**
     * @return string
     */
    public function getTypeDocName(): string
    {
        return DocsTable::getTypeTitleSingleById($this->TypeDoc);
    }

    /**
     * @param $docs
     * @param bool $includeHeshbonitMasKabla
     * @return bool
     */
    public static function checkIsInvoice($docs, bool $includeHeshbonitMasKabla = true): bool
    {
        if(!$includeHeshbonitMasKabla && (int)$docs->TypeHeader === DocsTable::TYPE_HESHBONIT_MAS_KABLA) {
            return false;
        }
        return in_array((int)$docs->TypeHeader, DocsTable::TYPE_HEADER_INVOICE) || ((int)$docs->TypeHeader === DocsTable::TYPE_HESHBONIT_HESHKA && $docs->Amount > 0) ;
    }

    /**
     * @param bool $includeHeshbonitMasKabla
     * @return bool
     */
    public function isInvoiceDocs(bool $includeHeshbonitMasKabla = true): bool
    {
        if(!$includeHeshbonitMasKabla && (int)$this->TypeHeader === DocsTable::TYPE_HESHBONIT_MAS_KABLA) {
            return false;
        }
        return in_array((int)$this->TypeHeader, DocsTable::TYPE_HEADER_INVOICE) || ((int)$this->TypeHeader === DocsTable::TYPE_HESHBONIT_HESHKA && $this->Amount > 0);
    }

    /**
     * @param $docs
     * @param bool $includeHeshbonitMasKabla
     * @return bool
     */
    public static function checkIsReceiptDocs($docs ,bool $includeHeshbonitMasKabla = true): bool
    {
        if(!$includeHeshbonitMasKabla && (int)$docs->TypeHeader === DocsTable::TYPE_HESHBONIT_MAS_KABLA) {
            return false;
        }
        return in_array((int)$docs->TypeHeader, DocsTable::TYPE_HEADER_RECEIPT);
    }

    /**
     * @param bool $includeHeshbonitMasKabla
     * @param bool $onlyNotRefund
     * @return bool
     */
    public function isReceiptDocs(bool $includeHeshbonitMasKabla = true, $onlyNotRefund = false): bool
    {
        if(!$includeHeshbonitMasKabla && (int)$this->TypeHeader === DocsTable::TYPE_HESHBONIT_MAS_KABLA) {
            return false;
        }
        if($onlyNotRefund && ((int)$this->Refound !== self::REFUND_STATUS_OFF)) {
            return false;
        }
        return in_array((int)$this->TypeHeader, DocsTable::TYPE_HEADER_RECEIPT);
    }

    /**
     * @return bool
     */
    public function isRefundInvoiceDocs(): bool
    {
        return in_array((int)$this->TypeHeader, DocsTable::TYPE_HEADER_REFUND_INVOICE) && ($this->Amount < 0);
    }

    /**
     * @return bool
     */
    public function isRefundReceiptDocs(): bool
    {
        return ((int)$this->Refound !== self::REFUND_STATUS_OFF) && $this->isReceiptDocs(false);
    }


    /**
     * @param $docsIdArray
     * @return bool
     */
    public static function hasRefundReceiptInDocIdsArray($docsIdArray): bool
    {
        return (bool)self::whereIn('id',$docsIdArray)
            ->where('Refound', '!=', self::REFUND_STATUS_OFF)
            ->where('TypeHeader', '=', DocsTable::TYPE_KABALA)
            ->first();
    }


    /**
     * @param $docs
     * @return bool
     */
    public static function checkIsOpen($docs): bool
    {
        return in_array((int)$docs->PayStatus, [self::PAY_STATUS_OPEN, self::PAY_STATUS_HALF_CLOSE]);
    }

    /**
     * @return bool
     */
    public function isOpen(): bool
    {
        return in_array((int)$this->PayStatus, [self::PAY_STATUS_OPEN, self::PAY_STATUS_HALF_CLOSE]);
    }

    /**
     * @return bool
     */
    public function isClose(): bool
    {
        return in_array((int)$this->PayStatus, [self::PAY_STATUS_CLOSE, self::PAY_STATUS_CANCELED]);
    }


    /**
     * @param $Doc
     * @return bool
     */
    public static function checkConnectedInvoiceWithReceipts($Doc): bool
    {
        if(!self::checkIsInvoice($Doc)) {
            $invoiceId = DocsLinkToInvoice::getInvoiceIdFromDoc($Doc->id);
            $Doc = self::find($invoiceId);
        }
        //CANCELED  or not pay yet
        return !((int)$Doc->PayStatus === self::PAY_STATUS_CANCELED ||
            ((float)($Doc->BalanceAmount - ($Doc->refundAmount ?? 0))  === (float)$Doc->Amount));
    }


    /**
     * @return bool
     */
    public function connectedInvoiceIsCanceled(): bool
    {
        if(!$this->isInvoiceDocs()) {
            $invoiceId = DocsLinkToInvoice::getInvoiceIdFromDoc($this->id);
            $payStatus = self::where('id', '=', $invoiceId)->pluck('PayStatus') ?? self::PAY_STATUS_OPEN;
            return ((int)$payStatus === self::PAY_STATUS_CANCELED);
        }
        return $this->PayStatus === self::PAY_STATUS_CANCELED;
    }


    /**
     * @param bool $isRefund
     * @return string
     */
    public function getActivityJsonInvoiceText(bool $isRefund = false):string
    {
        if($isRefund || (int)$this->Refound !== self::REFUND_STATUS_OFF) {
            $ItemText = ((int)$this->TypeHeader === DocsTable::TYPE_HESHBONIT_MAS ||
                (int)$this->TypeHeader === DocsTable::TYPE_HESHBONIT_MAS_KABLA) ? lang('refund_invoice_number_ajax') :
                lang('refund_invoice_payment_number_ajax');
        } else {
            $ItemText = ((int)$this->TypeHeader === DocsTable::TYPE_HESHBONIT_MAS ||
                (int)$this->TypeHeader === DocsTable::TYPE_HESHBONIT_MAS_KABLA) ? lang('invoice_number_ajax') :
                lang('invoice_payment_number_ajax');
        }
        $ItemText .=  ' ' . $this->TypeNumber;
        return $ItemText;
    }

    /**
     * @param int $docId
     * @return string
     */
    public static function getDocNumber(int $docId): string
    {
        return self::where('id', '=', $docId)
                ->pluck('TypeNumber') ?? '';
    }

    /**
     * @param float $amount
     */
    public function updateRefundAmount(float $amount) {
        $this->refundAmount += $amount;
        if($this->refundAmount >= $this->Amount) {
            $this->PayStatus = self::PAY_STATUS_CANCELED;
        }
    }

    public function getOutSideDisplayLink(): string
    {
        return get_newboostapp_domain() . '/office/PDF/DocsClient.php?RandomUrl=' . $this->RandomUrl . '&ClientId=' . $this->ClientId;
    }

    public function getInSideDisplayLink(): string
    {
        return get_loginboostapp_domain() . '/office/PDF/Docs.php?DocType=' . $this->TypeDoc . '&DocId=' . $this->TypeNumber;
    }

    public function updateStatus(int $status): bool
    {
        // add to logger
        if((int)$this->Status !== $status) {
            try {
                $oldStatus = $this->Status;
                $this->Status = $status;
                if($this->save()) {
                    LoggerService::info('docs_id- '. $this->id .' status change from-' . $oldStatus . ' to-' . $this->Status , LoggerService::CATEGORY_DOCS);
                    return true;
                }
                return false;
            } catch (\Exception $e) {
                return false;
            }
        } else {
            return true;
        }

    }

    /**
     * @param int $docId
     * @return float
     */
    public static function getBalanceAmount(int $docId): float
    {
        try {
            return (float)self::where('id', '=', $docId)->pluck('BalanceAmount');
        } catch (\Exception $e) {
            return 0;
        }
    }


}
