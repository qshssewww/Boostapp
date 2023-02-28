<?php

use Hazzard\Database\Model;

require_once __DIR__ . '/DocsTable.php';
require_once __DIR__ . '/../../app/enums/Docs/DocPaymentTypeEnum.php';
require_once __DIR__ . '/../services/payment/PaymentSystem.php';

/**
 * @property $id
 * @property $CompanyNum
 * @property $TrueCompanyNum
 * @property $Brands
 * @property $TypeDoc
 * @property $TypeHeader
 * @property $TypeNumber
 * @property $DocsId
 * @property $ClientId
 * @property $TypePayment
 * @property $Amount
 * @property $L4digit
 * @property $YaadCode
 * @property $CCode
 * @property $ACode
 * @property $Bank
 * @property $Payments
 * @property $CheckDate
 * @property $Brand
 * @property $BrandName
 * @property $Issuer
 * @property $tashType
 * @property $CheckBank
 * @property $CheckBankSnif
 * @property $CheckBankCode
 * @property $CheckNumber
 * @property $BankNumber
 * @property $BankDate
 * @property $Dates
 * @property $UserId
 * @property $Excess
 * @property $UserDate
 * @property $Minus
 * @property $DocDate
 * @property $DocMonth
 * @property $DocYear
 * @property $DocTime
 * @property $CreditType
 * @property $InvoiceId
 * @property $StatusInvoice
 * @property $ActivityJson
 * @property $Refound
 * @property $BusinessCompanyId
 * @property $BusinessType
 * @property $RefAction
 * @property $PayToken
 * @property $TransactionId
 * @property $MeshulamPageCode
 */
class DocsPayment extends Model
{
    public const TYPE_PAYMENT_CASH = 1;
    public const TYPE_PAYMENT_CHECK = 2;
    public const TYPE_PAYMENT_CREDIT_CARD = 3;
    public const TYPE_PAYMENT_BANK_TRANSFER = 4;
    public const TYPE_PAYMENT_PAYMENT_COUPON = 5;
    public const TYPE_PAYMENT_RETURN_NOTE = 6;
    public const TYPE_PAYMENT_PAYMENT_BILL = 7;
    public const TYPE_PAYMENT_STANDING_ORDER = 8;
    public const TYPE_PAYMENT_OTHER = 9;

    public const AFTER_REF_ACTION = 1;
    public const BEFORE_REF_ACTION = 0;

    public const IS_REFUND = 1;
    public const IS_NOT_REFUND = 0;

    public const TASH_TYPE_REGULAR = 1;
    public const TASH_TYPE_PAYMENT = 2;

    protected $table = 'boostapp.docs_payment';

    private $_client;
    private $_docsTable;

    /**
     * @param $CompanyNum
     * @return mixed
     */
    public function getDailyDeals($CompanyNum)
    {
        $beginOfDay = date('Y-m-d H:i:s', strtotime("today"));
        $EndOfDay = date('Y-m-d H:i:s', strtotime("tomorrow") - 1);
        $SumDeals = DB::table($this->table)
            ->where('DocDate', '>=', $beginOfDay)
            ->where('DocDate', '<=', $EndOfDay)
            ->where('CompanyNum', '=', $CompanyNum)->sum('Amount');
        return $SumDeals;
    }

    /**
     * @param $CompanyNum
     * @return mixed
     */
    public function getDailyDealsCount($CompanyNum)
    {
        $beginOfDay = date('Y-m-d H:i:s', strtotime("today"));
        $EndOfDay = date('Y-m-d H:i:s', strtotime("tomorrow") - 1);
        $countDeals = DB::table($this->table)
            ->where('CheckDate', '>=', $beginOfDay)
            ->where('CheckDate', '<=', $EndOfDay)
            ->where('CompanyNum', '=', $CompanyNum)->count();
        return $countDeals;
    }

    /**
     * @param $CompanyNum
     * @return mixed
     */
    public function getLastMonthDeals($CompanyNum)
    {
        $StartMonth = date("Y-m-d", strtotime("first day of previous month"));
        $EndMonth = date("Y-m-d", strtotime("last day of previous month"));
        $SumDeals = DB::table($this->table)
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('UserDate', '>=', $StartMonth)
            ->where('UserDate', '<=', $EndMonth)
            ->sum('Amount');
        return $SumDeals;
    }

    /**
     * @param $CompanyNum
     * @return mixed
     */
    public function getCurrentMonthDeals($CompanyNum)
    {
        $StartMonth = date("Y-m-d", strtotime("first day of this month"));
        $EndMonth = date("Y-m-d", strtotime("last day of this month"));
        $SumDeals = DB::table($this->table)
            ->where('CompanyNum', '=', $CompanyNum)
            ->where('UserDate', '>=', $StartMonth)
            ->where('UserDate', '<=', $EndMonth)
            ->sum('Amount');
        return $SumDeals;
    }

    /**
     * @param $clientId
     * @param $typeNumber
     * @return mixed
     */
    public function getByDocsId($clientId, $typeNumber)
    {
        return self::where('ClientId', $clientId)
            ->where(function ($q) use ($typeNumber) {
                $q->where('DocsId', $typeNumber)
                    ->orWhere('InvoiceId', $typeNumber);
            })
            ->first();
    }

    /**
     * @param $id
     * @return array|string|string[]
     */
    public function getPaymentTypeText($id)
    {
        switch ($id){
            case 1:
                $paymentType = lang('cash');
                break;
            case 2:
                $paymentType = lang('check');
                break;
            case 3:
                $paymentType = lang('credit_card');
                break;
            case 4:
                $paymentType = lang('bank_transfer');
                break;
            case 5:
                $paymentType = 'תווים';//lang('characters_appsettings');
                break;
            case 7:
                $paymentType = lang('payment_bill');
                break;
            case 8:
                $paymentType = lang('standing_order');
                break;
            default:
                $paymentType = lang('other');
                break;
        }

        return $paymentType;
    }

    /**
     * @return DocsTable
     */
    public function docsTable()
    {
        if (empty($this->_docsTable)) {
            $this->_docsTable = DocsTable::where('id', '=', $this->TypeDoc)->where('CompanyNum', $this->CompanyNum)->first();
        }
        return $this->_docsTable;
    }

    /**
     * @return Client|null
     */
    public function client()
    {
        if (empty($this->_client)) {
            $this->_client = Client::where('id', '=', $this->ClientId)->where('CompanyNum', $this->CompanyNum)->first();
        }
        return $this->_client;
    }

    /**
     * @param Docs $Doc
     */
    public function setPropertiesByDocs(Docs $Doc): void
    {
        $this->CompanyNum = $Doc->CompanyNum;
        $this->Brands = $Doc->Brands;
        $this->TrueCompanyNum = $Doc->TrueCompanyNum;
        $this->TypeDoc = $Doc->TypeDoc;
        $this->TypeHeader = $Doc->TypeHeader;
        $this->TypeNumber = $Doc->TypeNumber;
        $this->DocsId = $Doc->id;
        $this->ClientId = $Doc->ClientId;
        $this->Dates = $Doc->Dates;
        $this->UserId = $Doc->UserId;
        $this->UserDate = $Doc->UserDate;
        $this->DocDate = $Doc->DocDate;
        $this->DocMonth = $Doc->DocMonth;
        $this->DocYear = $Doc->DocYear;
        $this->DocTime = $Doc->DocTime;
        $this->StatusInvoice = 1;///todo????
        $this->BusinessCompanyId = $Doc->BusinessCompanyId;
        $this->BusinessType = $Doc->BusinessType;
    }


    /**
     * @param array $docPaymentsDataArray
     */
    public function setPropertiesByData(array $docPaymentsDataArray): void
    {
        foreach ($docPaymentsDataArray as $propertyName => $docPaymentsData) {
            $this->$propertyName = $docPaymentsData;
        }
    }


    /**
     * todo - add Adjustment of invoicing according to studio settings
     * @param array $docsPaymentIdArray
     * @return bool change array (add all id that added)..  and return true on succeed
     * @throws Exception
     */
    public function createOnMultiplePayment(array &$docsPaymentIdArray = []): bool
    {

        $fullAmount = $this->Amount;
        $onePaymentAmount = $fullAmount / $this->Payments;
        $onePaymentAmount = number_format((float)$onePaymentAmount, 2, '.', '');
        [$whole, $decimal] = explode('.', $onePaymentAmount);
        $checkPaymentAmount = $whole * ($this->Payments - 1);
        $firstPayment = $fullAmount - $checkPaymentAmount;
        $firstPayment = number_format((float)$firstPayment, 2, '.', '');
        $otherPayment = number_format((float)$whole, 2, '.', '');
        $count = $this->Payments;
        $startDate = $this->CheckDate ?? date('Y-m-d');
        for ($i = 1; $i <= $count; $i++) {
            $this->Payments = $i;
            $addMonth = '+' . ($i - 1) . ' month';
            $this->CheckDate = date('Y-m-d', strtotime($addMonth, strtotime($startDate)));
            if ($i === 1) {
                $this->Amount = $firstPayment;
                $this->saveAfterValidation();
                empty($this->id) ? null : $docsPaymentIdArray[] = $this->id;
            } else {
                $this->Amount = $otherPayment;
                $docsPaymentCopyArray = $this->toArray();
                unset($docsPaymentCopyArray['id']);
                $DocsPaymentCopy = new DocsPayment($docsPaymentCopyArray);
                $DocsPaymentCopy->saveAfterValidation();
                empty($DocsPaymentCopy->id) ? null : $docsPaymentIdArray[] = $DocsPaymentCopy->id;
            }
        }
        return true;
    }

    /**
     * @param string $id
     * @param string $acode
     * @return bool
     */
    public static function isPaymentExist(string $id , string $acode): bool {
        $docs_payment = self::where('YaadCode', $id)
            ->where('ACode', $acode)
            ->first();

        return (bool)$docs_payment;
    }


    /**
     * @param array $docsIdArray
     * @param int $companyNum
     * @return DocsPayment[]
     */
    public static function getPaymentsByDocsIdArray(array $docsIdArray, int $companyNum): array
    {
        return self::where('RefAction', '=', self::BEFORE_REF_ACTION)
            ->where('CompanyNum', '=', $companyNum)
            ->whereIn('DocsId', $docsIdArray)
            ->get();
    }

    /**
     * with out payments if $onlyFirstPaymentEachMethod = true
     * @param int $docsId
     * @param int $companyNum
     * @param bool $onlyFirstPaymentEachMethod
     * @return DocsPayment[]
     */
    public static function getPaymentsByDocsId(int $docsId, int $companyNum, $onlyFirstPaymentEachMethod = true): array
    {
        $DocsPaymentArray = self::where('RefAction', '=', self::BEFORE_REF_ACTION)
            ->where('CompanyNum', '=', $companyNum)
            ->where('DocsId', $docsId)
            ->where('Refound', 0)
            ->get();
        if($onlyFirstPaymentEachMethod) {
            $yaadCodeArray = [];
            /** @var DocsPayment $DocsPayment */
            foreach ($DocsPaymentArray as $key => $DocsPayment) {
                if (!$DocsPayment->isCreditCardPayment()) {
                    continue;
                }
                if ($DocsPayment->Payments > 1 && in_array($DocsPayment->YaadCode, $yaadCodeArray)) {
                    unset($DocsPaymentArray[$key]);
                } else {
                    $yaadCodeArray[] = $DocsPayment->YaadCode;
                }
            }
        }
        return $DocsPaymentArray;
    }

    /**
     * @param bool $isYaadPayment
     * @return float
     */
    public function getSum(bool $byDocAndYaadCode = false) :float {
        if($byDocAndYaadCode && (int)$this->TypePayment === DocPaymentTypeEnum::CREDIT_CARD) {
            return self::where('RefAction', '=', self::BEFORE_REF_ACTION)
                    ->where('CompanyNum', '=', $this->CompanyNum)
                    ->where('YaadCode', '=', $this->YaadCode)
                    ->where('Refound', 0)
                    ->where('DocsId', $this->DocsId)
                    ->sum('Amount') ?? 0;
        }
        return $this->Amount;
    }

    /**
     * @param bool $byDocAndYaadCode
     * @return int
     */
    public function getPaymentsNumber(bool $byDocAndYaadCode = false) :int {
        if($byDocAndYaadCode && (int)$this->TypePayment === DocPaymentTypeEnum::CREDIT_CARD) {
            return self::where('RefAction', '=', self::BEFORE_REF_ACTION)
                    ->where('CompanyNum', '=', $this->CompanyNum)
                    ->where('YaadCode', '=', $this->YaadCode)
                    ->where('DocsId', $this->DocsId)
                    ->where('Refound', 0)
                    ->count() ?? 1;
        }
        return 1;
    }

    /**
     * @param bool $byDocAndYaadCode - true -if want to update all payments in the transaction
     */
    public function updateRefAction(bool $byDocAndYaadCode = false): void
    {
        //card payment and full payment
        if((int)$this->TypePayment === DocPaymentTypeEnum::CREDIT_CARD && $byDocAndYaadCode) {
             self::where('RefAction', '=', self::BEFORE_REF_ACTION)
                    ->where('CompanyNum', '=', $this->CompanyNum)
                    ->where('YaadCode', '=', $this->YaadCode)
                    ->where('DocsId', $this->DocsId)
                    ->where('Refound', 0)
                    ->update(['RefAction' => self::AFTER_REF_ACTION]);
        } else {
            $this->RefAction = self::AFTER_REF_ACTION;
            $this->save();
        }
    }


    /**
     *
     * @return bool
     */
    public function isCreditCardPayment(): bool
    {
        return ((int)$this->TypePayment === self::TYPE_PAYMENT_CREDIT_CARD);
    }

    /**
     *
     * @param int|null $typeShva
     * @return bool
     */
    public function isValidCreditCardPayment(?int $typeShva = null): bool
    {
        if((int)$this->TypePayment !== self::TYPE_PAYMENT_CREDIT_CARD){
            return false;
        }
        if(empty($this->YaadCode)){
            return false;
        }
        if($typeShva !== null && $typeShva === PaymentSystem::TYPE_MESHULAM && empty($this->TransactionId)) {
            return false;
        }
        return true;
    }



    public const VALIDATION_PAYMENT_DATA = [
        'TypePayment' => 'required|between:1,9',
        'Amount' => 'required|numeric',
        'Dates' => 'date_format:Y-m-d G:i:s',

        'CheckNumber' => 'required_if:TypePayment,' . DocPaymentTypeEnum::CHECK,
        'BankDate' => 'date_format:Y-m-d|required_if:TypePayment,' . DocPaymentTypeEnum::CHECK,
        'CheckBank' => 'required_if:TypePayment,' . DocPaymentTypeEnum::CHECK,
        'CheckBankSnif' => 'required_if:TypePayment,' . DocPaymentTypeEnum::CHECK,
        'CheckBankCode' => 'required_if:TypePayment,' . DocPaymentTypeEnum::CHECK,

        'BankNumber' => 'required_if:TypePayment,' . DocPaymentTypeEnum::BANKTRAS,
    ];

    /**
     * @throws Exception
     */
    public function saveAfterValidation(): void
    {
        $validator = Validator::make($this->getAttributes(), self::VALIDATION_PAYMENT_DATA);
        if ($validator->passes()) {
            if(!empty($this->BankDate) && $this->TypePayment === DocPaymentTypeEnum::CHECK) {
                $this->CheckDate = $this->BankDate;
            }
            $this->save();
        } else {
            throw new Exception(json_encode($validator->errors()->toArray()));
        }
    }

    /**
     * @return bool
     */
    public function isCardPayment(): bool
    {
        return ((int)$this->TypeHeader === DocsTable::TYPE_KABALA || (int)$this->TypeHeader === DocsTable::TYPE_HESHBONIT_MAS_KABLA)
            && (int)$this->TypePayment === self::TYPE_PAYMENT_CREDIT_CARD
            &&  (int)$this->Refound === self::IS_NOT_REFUND;
    }

    /**
     * @return array
     */
    public function getArrayRefundTransactionDetails(): array
    {
        return  [
            'YaadCode' => $this->YaadCode ?? 0,
            'ACode' => $this->ACode ?? 0,
            'Bank' => $this->Bank ?? 0,
            'Brand' => $this->Brand ?? 0,
            'Issuer' => $this->Issuer ?? 0,
            'tashTypeDB' => $this->tashTypeDB ?? 0,
//            'Payments' => $this->Payments ?? 0,
            'PayToken' => $this->PayToken ?? 0,
            'TransactionId' => $this->TransactionId ?? 0,
            'L4digit' => $this->L4digit ?? 0,
            'BrandName' => $this->BrandName ?? 0,
//            'PaymentType' => $this->PaymentType ?? 0,
        ];
    }


}
