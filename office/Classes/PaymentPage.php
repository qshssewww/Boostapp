<?php

require_once "Company.php";
class PaymentPage
{
    /**
     * @var $id int
     */
    private $id;

    /**
     * @var $CompanyNum int
     */
    private $CompanyNum;

    /**
     * @var $ItemId int
     */
    private $ItemId;

    /**
     * @var $Title string
     */
    private $Title;

    /**
     * @var $TitleUrl string
     */
    private $TitleUrl;

    /**
     * @var $Content string
     */
    private $Content;

    /**
     * @var $Amount double
     */
    private $Amount;

    /**
     * @var $TypePage int
     */
    private $TypePage;

    /**
     * @var $PaymentType int
     */
    private $PaymentType;

    /**
     * @var $MaxPaymentRegular int
     */
    private $MaxPaymentRegular;

    /**
     * @var $PaymentStep string
     */
    private $PaymentStep;

    /**
     * @var $Vat int
     */
    private $Vat;

    /**
     * @var $ApiSend string
     */
    private $ApiSend;

    /**
     * @var $ThankYouPage string
     */
    private $ThankYouPage;

    /**
     * @var $PixleVisit string
     */
    private $PixleVisit;

    /**
     * @var $Status int
     */
    private $Status;

    /**
     * @var $Dates DateTime
     */
    private $Dates;

    /**
     * @var $Responder string
     */
    private $Responder;

    /**
     * @var $IncludeVat int
     */
    private $IncludeVat;

    /**
     * @var $ItemApp int
     */
    private $ItemApp;

    /**
     * @var $UserId int
     */
    private $UserId;

    /**
     * @var $RandomNumber string
     */
    private $RandomNumber;

    /**
     * @var $ImageLink string
     */
    private $ImageLink;

    /**
     * @var $visit int
     */
    private $visit;

    /**
     * @var $NumDate int
     */
    private $NumDate;

    /**
     * @var $TypePayment int
     */
    private $TypePayment;

    /**
     * @var $tashType int
     */
    private $tashType;

    /**
     * @var $Tash int
     */
    private $Tash;

    /**
     * @var $CouponCode string
     */
    private $CouponCode;

    /**
     * @var $Discount double
     */
    private $Discount;

    /**
     * @var $DiscountType int
     */
    private $DiscountType;

    /**
     * @var $DiscountTypePayments int
     */
    private $DiscountTypePayments;

    /**
     * @var $DiscountStatus int
     */
    private $DiscountStatus;

    /**
     * @var $TypeKeva int
     */
    private $TypeKeva;

    /**
     * @var $ItemVaildType
     */
    private $ItemVaildType;

    /**
     * @var $Sort
     */
    private $Sort;

    /**
     * @var $MaxPaymentRegularPlusKeva int
     */
    private $MaxPaymentRegularPlusKeva;

    /**
     * @var $ItemDepartment
     */
    private $ItemDepartment;

    /**
     * @var $Brands string
     */
    private $Brands;

    /**
     * @var $tableName string
     */
    private $tableName;

    public function __set($name, $value)
    {
        if(property_exists($this,$name)){
            $this->$name = $value;
        }
    }

    public function __get($name)
    {
        if(property_exists($this,$name)){
            return $this->$name;
        }
        return null;
    }

    /**
     * PaymentPage constructor.
     * @param null $itemId
     * @param null $paymentId
     */
    public function __construct($itemId = null, $paymentId = null)
    {
        $this->tableName = 'payment_pages';
        if($itemId != null && $paymentId != null){
            $this->getPaymentPageOfItem($itemId,$paymentId);
        }
    }

    /**
     * @param $itemId int
     * @param $paymentId int
     */
    public function getPaymentPageOfItem($itemId, $paymentId){
        $payment = DB::table($this->tableName)->where('ItemId','=', $itemId)->where("id",'=', $paymentId)->get();
        foreach ($payment as $key => $value) {
            $this->__set($key, $value);
        }
    }


    /**
     * @param $itemId
     * @return array
     */
    public function getAllPaymentPagesOfItem($itemId){
        $payment = DB::table($this->tableName)->where('ItemId','=', $itemId)->get();
        $payments = array();
        if(Count($payment) > 0) {
            foreach ($payment as $pay) {
                $payObj = new PaymentPage();
                foreach ($pay as $key => $value) {
                    $payObj->__set($key, $value);
                }
                if ($payObj->__get("id") != null) {
                    array_push($payments, $payObj);
                }
            }
        }
        return $payments;
    }
    public function getFirstPaymentPagesOfItem($itemId){
        $payment = DB::table($this->tableName)->where('ItemId','=', $itemId)->first();
        $payObj = new PaymentPage();
        if($payment != null) {
            foreach ($payment as $key => $value) {
                $payObj->__set($key, $value);
            }
        }
        return $payObj;
    }
    public function insertPaymentPage($itemId,$data){
        $company = Company::getInstance();
        $user = Auth::user();
        $userId = $user->id;
        DB::table($this->tableName)->insert(
            array(
                "CompanyNum" => $company->__get("id"),
                "ItemId" => $itemId,
                "Title" => $data["appName"],
                "TitleUrl" => $data["appName"],
                "Content" => $data["appDesc"],
                "Amount" => $data["appPrice"],
                "UserId" => $userId,
                "Dates" => date('Y-m-d H:i:s'),
                "Brands" => (isset($data["brand"])) ?  $data["brand"]->__get('id') : "BA999",
                "Payment" => $data["dealType"]
            )
        );
    }
    public function getRow($id){
        return DB::table($this->tableName)->where("id","=",$id)->first();
    }
}
