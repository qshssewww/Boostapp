<?php

use Hazzard\Database\Model;

require_once __DIR__ . '/Docs.php';
require_once __DIR__ . '/ClientActivities.php';

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
 * @property $ItemId
 * @property $SKU
 * @property $ItemName
 * @property $ItemText
 * @property $ItemPrice
 * @property $ItemPriceVat
 * @property $ItemPriceVatDiscount
 * @property $ItemQuantity
 * @property $ItemDiscountType
 * @property $ItemDiscount
 * @property $ItemDiscountAmount
 * @property $Itemtotal
 * @property $ItemTable
 * @property $Dates
 * @property $Supplier
 * @property $ItemTotalFix
 * @property $MainDepartment
 * @property $CoastPrice
 * @property $UserDate
 * @property $TypeDocBasis
 * @property $TypeDocBasisNumber
 * @property $Vat
 * @property $VatAmount
 * @property $Minus
 * @property $DocDate
 * @property $DocMonth
 * @property $DocYear
 * @property $DocTime
 * @property $Refound
 * @property $BusinessCompanyId
 * @property $BusinessType
 */
class DocsList extends Model
{
    protected $table = 'boostapp.docslist';

    public static $createRules = [
        'CompanyNum' => 'required|integer',
        'TrueCompanyNum' => 'required|integer',
        'Brands' => 'integer',
        'TypeDoc' => 'required|exists:boostapp.docstable,id',
        'TypeHeader' => 'integer',
        'TypeNumber' => 'integer',
        'DocsId' => 'exists:boostapp.docs,id',
        'ClientId' => 'exists:boostapp.Client,id',
        'ItemId' => 'exists:boostapp.Items,id',
//        'SKU' => '',
        'ItemName' => 'required|max:256',
        'ItemText' => 'max:256',
        'ItemPrice' => 'required|numeric|between:0,999999999',
        'ItemPriceVat' => 'numeric|between:0,999999999',
        'ItemPriceVatDiscount' => 'numeric|between:0,999999999',
        'ItemQuantity' => 'numeric',// todo
        'ItemDiscountType' => 'integer|between:1,2',
        'ItemDiscount' => 'numeric',
        'ItemDiscountAmount' => 'numeric',
        'Itemtotal' => 'numeric',
//        'ItemTable' => '',
//        'Dates' => 'date_format:Y-m-d ',
//        'Supplier' => '',
//        'ItemTotalFix' => '',
//        'MainDepartment' => '',
        'CoastPrice' => 'numeric|between:0,999999999',
        'UserDate' => '',
        'TypeDocBasis' => 'integer',//todo
        'TypeDocBasisNumber' => 'integer',//todo
        'Vat' => 'integer|between:0,2',
        'VatAmount' => 'numeric|between:0,100',
//        'Minus' => '',
        'DocDate' => 'date_format:Y-m-d',
        'DocMonth' => 'integer|between:0,12',
        'DocYear' => 'integer',
//        'DocTime' => '',
        'Refound' => 'integer|between:0,1',
        'BusinessCompanyId' => 'integer',
        'BusinessType' => 'exists:247softnew.businesstype,id',
    ];

//    public function checkAndSave(): bool
//    {
//        $validator = Validator::make($this->getAttributes(), self::$createRules);
//        if ($validator->fails()) {
//            throw new LogicException();
//        }
//        return $this->save();
//    }

    /**
     * @param ClientActivities $ClientActivity
     * @param int $minus - or 1 or -1
     */
    public function setPropertiesByClientActivity(ClientActivities $ClientActivity, int $minus = 1): void
    {
        $this->ItemQuantity = round($ClientActivity->ItemQuantity ?? 1,2) ?? 1.00;
        $itemQuantity = $this->ItemQuantity > 0 ? $this->ItemQuantity : 1;
        $this->ItemId = $ClientActivity->ItemId;
        $this->ItemName = $ClientActivity->ItemText;
        $this->ItemText = $ClientActivity->ItemText;// ?
        $this->ItemPrice = ($ClientActivity->ItemPrice / $itemQuantity) * $minus;
        $this->ItemPriceVat = $this->ItemPrice;
        $this->ItemDiscountType = $ClientActivity->DiscountType ?? 1;
        $this->ItemDiscount = $ClientActivity->Discount ?? 0;
        $this->ItemDiscountAmount = $ClientActivity->DiscountAmount ?? 0;
        $this->Itemtotal = ($this->ItemQuantity * $this->ItemPrice) - $this->ItemDiscountAmount;
        $this->ItemPriceVatDiscount = (($this->ItemQuantity * $ClientActivity->ItemPriceVat) - $this->ItemDiscountAmount)  * $minus;
        //        $this->MainDepartment = $ClientActivity->Department;//not must
        $this->ItemTable = 'items';//todo
    }


    public function setPropertiesRefund(Docs $DocInvoice, float $refundAmount): void
    {
        $this->ItemQuantity = 1;
        $this->ItemId = '';
        $this->ItemName = $DocInvoice->getActivityJsonInvoiceText(true);
        $this->ItemText = $this->ItemName;// ?
        $this->ItemPrice = abs($refundAmount);
        $this->ItemPriceVat = $this->ItemPrice;
        $this->ItemDiscountType = 1;
        $this->ItemDiscount = 0;
        $this->ItemDiscountAmount =0;
        $this->Itemtotal = $this->ItemPrice;
        $this->ItemPriceVatDiscount =  $this->ItemPrice;
        //        $this->MainDepartment = $ClientActivity->Department;//not must
        $this->ItemTable = 'invoice';//todo
    }


    /**
     * @param Docs $Docs
     */
    public function setPropertiesByDoc(Docs $Docs): void
    {
        $this->CompanyNum = $Docs->CompanyNum;
        $this->Brands = $Docs->Brands;
        $this->TrueCompanyNum = $Docs->TrueCompanyNum;
        $this->TypeDoc = $Docs->TypeDoc;
        $this->TypeHeader = $Docs->TypeHeader;
        $this->TypeNumber = $Docs->TypeNumber;
        $this->DocsId = $Docs->id;
        $this->ClientId = $Docs->ClientId;
        $this->Dates = $Docs->Dates;
        $this->UserDate = $Docs->UserDate;
        $this->Vat = $Docs->Vat;
        $this->VatAmount = $Docs->VatAmount;
        $this->DocDate = $Docs->DocDate;
        $this->DocMonth = $Docs->DocMonth;
        $this->DocYear = $Docs->DocYear;
        $this->DocTime = $Docs->DocTime;
        $this->BusinessCompanyId = $Docs->BusinessCompanyId;
        $this->BusinessType = $Docs->BusinessType;
        $this->ItemPriceVat = PriceHelper::getPriceWithoutVat($this->ItemPrice , $this->Vat);
    }

    /**
     * @param int $docId
     * @return mixed
     */
    public static function removeByDocsId(int $docId){
        return self::where('DocsId', $docId)->delete();
    }

    /**
     * @param int $docId
     * @return DocsList[]
     */
    public static function getAllByDocId(int $docId): array
    {
        return self::where('DocsId', $docId)->get();
    }

    /**
     *
     */
    public function changeDocsListToCalc(): void
    {
        $discountType = $this->ItemDiscountType ?? 1; // 1-DISCOUNT_TYPE_PERCENT
        $discountValue = $this->ItemDiscount ?? 0;
        $totalPrice = $this->ItemPrice * $this->ItemQuantity;
        if ((int)$discountType === 1) {
            if($discountValue > 100) {
                $this->ItemDiscount = 100;
            }
            $this->ItemDiscountAmount = $totalPrice * ($this->ItemDiscount / 100);
        } elseif ((int)$discountType === 2 && $discountValue > $totalPrice) {
            $this->ItemDiscountAmount = $totalPrice;
        } else {
            $this->ItemDiscountAmount = $discountValue;
        }
        $this->Itemtotal = $totalPrice - $this->ItemDiscountAmount;
    }


}
