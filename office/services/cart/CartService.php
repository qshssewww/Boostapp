<?php


require_once __DIR__ . '/../../../app/controllers/responses/cart/getCartData/CartDataResponse.php';
require_once __DIR__ . '/../../../app/controllers/responses/cart/checkOut/getCheckOutData/CheckOutDataResponse.php';
require_once __DIR__ . '/../../../app/controllers/responses/cart/CartResponse.php';
require_once __DIR__ . '/../../../app/controllers/responses/cart/getCartData/itemDetails/ItemDetailsResponse.php';
require_once __DIR__ . '/../../../app/controllers/responses/BaseResponse.php';
require_once __DIR__ . '/../../../app/controllers/responses/IdsResponse.php';
require_once __DIR__ . '/../../../app/controllers/responses/cart/post/SaveInDebtResponse.php';
require_once __DIR__ . '/../../../app/controllers/responses/cart/checkOut/CheckoutCreditsResponse.php';

require_once __DIR__ . '/../../Classes/CheckoutOrder.php';
require_once __DIR__ . '/../../Classes/CheckoutOrderItem.php';
require_once __DIR__ . '/../../Classes/CheckoutOrderItemDetails.php';

require_once __DIR__ . '/../../Classes/Settings.php';
require_once __DIR__ . '/../../Classes/Client.php';
require_once __DIR__ . '/../../Classes/ClientActivities.php';
require_once __DIR__ . '/../../Classes/AppNotification.php';
require_once __DIR__ . '/../../Classes/ClassStudioAct.php';
require_once __DIR__ . '/../../Classes/Users.php';
require_once __DIR__ . '/../../Classes/ItemCategory.php';
require_once __DIR__ . '/../../Classes/Item.php';
require_once __DIR__ . '/../../Classes/MeetingCategories.php';
require_once __DIR__ . '/../../Classes/ClassesType.php';
require_once __DIR__ . '/../../Classes/Section.php';
require_once __DIR__ . '/../../Classes/DocsLinkToInvoice.php';
require_once __DIR__ . '/../../Classes/cartClasses/ItemAndItemCat.php';
require_once __DIR__ . '/../../Classes/cartClasses/ProductDetails.php';


require_once __DIR__ . '/../LoggerService.php';
require_once __DIR__ . '/../ClientService.php';
require_once __DIR__ . '/../receipt/DocsService.php';
require_once __DIR__ . '/../meetings/CreateMeetingService.php';
require_once __DIR__ . '/../ItemService.php';
require_once __DIR__ . '/../ClientActivityService.php';
require_once __DIR__ . '/../ClassStudioActService.php';
require_once __DIR__ . '/../../Classes/Brand.php';
require_once __DIR__ . '/../../Classes/ClassStudioAct.php';
require_once __DIR__ . '/../../Classes/ClassStudioDate.php';
require_once __DIR__ . '/../../Classes/ClientActivities.php';
require_once __DIR__ . '/../../../app/enums/ClassStudioDate/MeetingStatus.php';
require_once __DIR__ . '/../../../app/enums/Docs/DocPaymentTypeEnum.php';
require_once __DIR__ . '/../../../app/enums/Docs/CardPaymentSettingEnum.php';
require_once __DIR__ . '/../../../app/helpers/PhoneHelper.php';

class CartService
{
    public const DISCOUNT_TYPE_PERCENT = 1;
    public const DISCOUNT_TYPE_NUMBER = 2;

    /**
     * todo add system logger
     * @param string $message
     * @param string $loggerTyoe
     */
    protected static function addToLogger(string $message, string $loggerTyoe = 'error' ): void
    {
        switch ($loggerTyoe) {
            case LoggerService::TYPE_DEBUG:
                LoggerService::debug($message, LoggerService::CATEGORY_CART);
                break;
            case LoggerService::TYPE_ERROR:
                LoggerService::error($message, LoggerService::CATEGORY_CART);
                break;
            case LoggerService::TYPE_INFO:
                LoggerService::info($message, LoggerService::CATEGORY_CART);
                break;
        }
    }
    /**
     * @param string $permission
     * @return bool
     */
    protected static function isAuth(string $permission): bool
    {
        //todo beta
        return (Auth::check() && Auth::userCan($permission) && Settings::getSettings(Auth::user()->CompanyNum)->beta == 1);
    }
    /**
     * @param CartResponse $CartResponse
     * @param int $permission
     * @param string $message
     * @return int 0-false
     */
    public static function checkAuth(CartResponse $CartResponse , int $permission = 173, string $message = ''): int
    {
        //todo add Auth check!
        if($message === '') {
            $message = lang('page_role_admin');
        }
        if(!self::isAuth($permission)) {
            $CartResponse->returnError($message);
            return 0;
        }
        return Auth::user()->CompanyNum ?? 0;
    }
    /**
     * @param int $clientId
     * @return bool
     */
    public static function getClientData(int $clientId): bool
    {
        $CartDataResponse = new CartDataResponse();
        $companyNum = self::checkAuth($CartDataResponse);
        if ($companyNum === 0){
            return false;
        }
        try {
            $RandomClientCheckoutOpenOrder = CheckoutOrder::getOpenOrderRandomClient($companyNum);
            if ($RandomClientCheckoutOpenOrder !== null) {
                $CartDataResponse->setOpenOrderId($RandomClientCheckoutOpenOrder->id, ((bool)$RandomClientCheckoutOpenOrder->IsRefundOrder));
            } else {
                self::addClient($clientId, $companyNum, $CartDataResponse);
            }
        } catch (Exception $e) {
            //todo-cart-add-error
            return $CartDataResponse->returnError($e->getMessage());
        }
        return $CartDataResponse->getData(true);
    }
    /**
     * Checking whether the client exists returns client=null if it does not exist
     * @param string $phone
     * @return bool
     */
    public static function checkNewClientPhone(string $phone): bool
    {
        $CartDataResponse = new CartDataResponse();
        try {
            $companyNum = self::checkAuth($CartDataResponse);
            if ($companyNum === 0) {
                return false;
            }
            $clientId = ClientService::findByPhoneAndStudio($companyNum, $phone);
            self::addClient($clientId, $companyNum, $CartDataResponse);
        } catch (Exception $e) {
            //todo-cart-add-error
            return $CartDataResponse->returnError($e->getMessage());
        }
        return $CartDataResponse->getData(true);
    }


    /****************************** getCartData ******************************/

    /**
     * @param int|null $clientId
     * @param string $debtId - $debtId
     * @return bool
     */
    public static function getCartData(?int $clientId, string $debtId = ''): bool
    {
        $CartDataResponse = new CartDataResponse();
        $companyNum = self::checkAuth($CartDataResponse);
        if ($companyNum === 0){
            return false;
        }
        try {
            $debtIdsArray = $debtId !== '' ? explode(",", $debtId) : [];
            $RandomClientCheckoutOpenOrder = CheckoutOrder::getOpenOrderRandomClient($companyNum);
            if ($RandomClientCheckoutOpenOrder !== null) {
                $CartDataResponse->setOpenOrderId($RandomClientCheckoutOpenOrder->id, ((bool)$RandomClientCheckoutOpenOrder->IsRefundOrder));
            } else {
                self::addStudioSettings($companyNum, $CartDataResponse);
                if ($clientId) {
                    self::addClient($clientId, $companyNum, $CartDataResponse, $debtIdsArray);
                }
                self::addProducts($companyNum, $CartDataResponse);
                self::addPackages($companyNum, $CartDataResponse);
                //meetings
                self::addAllMeetingData($companyNum, $CartDataResponse);
            }
        } catch (Exception $e) {
            //todo-cart-add-erorr
            return $CartDataResponse->returnError($e->getMessage());
        }
        $CartDataResponse->getData();
        return true;

    }

    /**
     * @param int $companyNum
     * @param CartDataResponse $CartDataResponse
     */
    protected static function addProducts(int $companyNum, CartDataResponse $CartDataResponse):void
    {
        $ItemAndItemCatArray = ItemAndItemCat::getProductsToCart($companyNum);
        foreach ($ItemAndItemCatArray as $ItemAndItemCat) {
            $CartDataResponse->addProduct($ItemAndItemCat);
        }
    }

    /**
     * @param int $companyNum
     * @param CartDataResponse $CartDataResponse
     */
    protected static function addPackages(int $companyNum, CartDataResponse $CartDataResponse):void
    {
        $ItemAndItemCatArray = ItemAndItemCat::getPackagesToCart($companyNum);
        foreach ($ItemAndItemCatArray as $ItemAndItemCat) {
            $CartDataResponse->addPackage($ItemAndItemCat);
        }
    }

    /**
     * @param int $companyNum
     * @param CartDataResponse $CartDataResponse
     */
    protected static function addAllMeetingData(int $companyNum, CartDataResponse $CartDataResponse):void
    {
        self::addCoaches($companyNum, $CartDataResponse);
        self::addDiaries($companyNum, $CartDataResponse);
        self::addServices($companyNum, $CartDataResponse);
    }

    /**
     * @param int $companyNum
     * @param CartDataResponse $CartDataResponse
     */
    protected static function addCoaches(int $companyNum, CartDataResponse $CartDataResponse):void
    {
        $CoachesArray = Users::getAllCoaches($companyNum);
        foreach ($CoachesArray as $Coach) {
            $CartDataResponse->addCoach($Coach);
        }
    }

    /**
     * @param int $companyNum
     * @param CartDataResponse $CartDataResponse
     */
    protected static function addDiaries(int $companyNum, CartDataResponse $CartDataResponse):void
    {
        $DiariesArray = Section::getAllBrandAndCalendars($companyNum);
        foreach ($DiariesArray as $Diary) {
            $CartDataResponse->addDiary($Diary);
        }
    }

    /**
     * @param int $companyNum
     * @param CartDataResponse $CartDataResponse
     */
    protected static function addServices(int $companyNum, CartDataResponse $CartDataResponse):void
    {
        $MeetingTemplateClassTypeArray = MeetingTemplateClassType::getMeetingToCart($companyNum);
        foreach ($MeetingTemplateClassTypeArray as $MeetingTemplateClassType) {
            $CartDataResponse->addService($MeetingTemplateClassType);
        }
    }

    /**
     * @param int $clientId
     * @param int $companyNum
     * @param CartDataResponse $CartDataResponse
     * @param array $debtId - array of client activity id of some debt
     */
    protected static function addClient(int $clientId, int $companyNum, CartDataResponse $CartDataResponse, array $debtId = []):void
    {
        /** @var Client $Client */
        $Client = Client::find($clientId);
        if (!empty($Client) && (int)$Client->CompanyNum === $companyNum) {
            $CheckoutOrder = CheckoutOrder::getOpenOrderByClient($Client->id);
            if ($CheckoutOrder !== null) {
                $CartDataResponse->setOpenOrderId($CheckoutOrder->id, ((bool)$CheckoutOrder->IsRefundOrder));
            }
            $CartDataResponse->setClient($Client);
            if(!$Client->isRandomClient || !empty($debtId)){
                self::addDebts($clientId, $companyNum, $CartDataResponse, $debtId);
            }
        }
    }

    /**
     * @param int $clientId
     * @param int $companyNum
     * @param CartDataResponse $CartDataResponse
     * @param array $debtIdsArray - client activity id of some debt
     */
    protected static function addDebts(int $clientId, int $companyNum, CartDataResponse $CartDataResponse, array $debtIdsArray = []): void
    {
        $ClientActivitiesArray = ClientActivities::getClientActivitiesInDocsDebt($clientId, $companyNum);
        foreach ($ClientActivitiesArray as $ClientActivity) {
            if(!empty($debtIdsArray) && !in_array($ClientActivity->id, $debtIdsArray)){
                continue;
            }
            if(!empty($ClientActivity->PayClientId) && $clientId !== (int)$ClientActivity->ClientId) {
                $ClientActivity->ItemText .= ' ( ' . Client::getNameById($ClientActivity->ClientId) . ')';
            }
            $CartDataResponse->addDebt($ClientActivity);
        }
    }

    /**
     * @param int $companyNum
     * @param CartDataResponse $CartDataResponse
     */
    protected static function addStudioSettings(int $companyNum, CartDataResponse $CartDataResponse): void
    {
        $Settings = Settings::getByCompanyNum($companyNum);
        if(!empty($Settings)){
            $CartDataResponse->setBusinessType($Settings->BusinessType ?? 1);
            $CartDataResponse->setVatAmount(Settings::getVatByBusinessType($Settings) ?? 0);
            $CartDataResponse->setHasLessons(!isset($Settings->displayClasses) ||(int)$Settings->displayClasses === 1);
        }
    }

    /****************************** getProductData ******************************/

    /**
     * @param int $itemId
     * @param string $type
     * @return bool
     */
    public static function getProductData(int $itemId, string $type): bool
    {
        $ItemDetailsResponse = new ItemDetailsResponse($type);
        $companyNum = self::checkAuth($ItemDetailsResponse);
        if ($companyNum === 0){
            return false;
        }
        try {
            self::addProductDetails($itemId, $companyNum, $ItemDetailsResponse);
            $ItemDetailsResponse->getData();
            return true;
        } catch (Exception $e) {
            //todo-cart-add-erorr
            return $ItemDetailsResponse->returnError($e->getMessage());
        }
    }

    /**
     * @param int $itemId
     * @param int $companyNum
     * @param ItemDetailsResponse $ItemDetailsResponse
     */
    protected static function addProductDetails(int $itemId, int $companyNum, ItemDetailsResponse $ItemDetailsResponse):void
    {
        $ItemDetailsArray = ProductDetails::getItemDetails($itemId, $companyNum);
        foreach ($ItemDetailsArray as $ItemDetail) {
            $ItemDetailsResponse->item->addProductDetail($ItemDetail);
        }
    }

    /****************************** getLessonsData ******************************/

    /**
     * @param int $classStudioDateId
     * @return bool
     */
    public static function getClientInLesson(int $classStudioDateId): bool
    {
        $IdsResponse = new IdsResponse();
        try {
            if (!self::isAuth(123)) {
                throw new ErrorException(lang('page_role_admin'));
            }
            $classStudioActIdsArray = ClassStudioAct::getAllClientIdInClass($classStudioDateId);
            $IdsResponse->setIds($classStudioActIdsArray);
        } catch (Exception $e) {
            //todo-cart-add-erorr
            $IdsResponse->setError($e->getMessage());
        }
        return $IdsResponse->getData();
    }

    /**
     * @param string $date
     * @return bool
     */
    public static function getLessonsData(string $date): bool
    {
        $ItemDetailsResponse = new ItemDetailsResponse(ItemDetailsResponse::LESSON);
        $companyNum = self::checkAuth($ItemDetailsResponse);
        if ($companyNum === 0){
            return false;
        }
        try {
            self::addLessons($date, $companyNum, $ItemDetailsResponse);
            $ItemDetailsResponse->getData(true);
            return true;
        } catch (Exception $e) {
            //todo-cart-add-erorr
            return $ItemDetailsResponse->returnError($e->getMessage());
        }
    }

    /**
     * @param string $date
     * @param int $companyNum
     * @param ItemDetailsResponse $ItemDetailsResponse
     */
    protected static function addLessons(string $date, int $companyNum, ItemDetailsResponse $ItemDetailsResponse):void
    {
        $ClassStudioDateArray = ClassStudioDate::geAllActiveLessonInDate($companyNum, $date);
        foreach ($ClassStudioDateArray as $ClassStudioDate) {
            $ItemDetailsResponse->addLesson($ClassStudioDate);
        }
    }

    /****************************** updateFavorite ******************************/

    /**
     * @param bool $newStatus
     * @param string $itemCategoryType
     * @param string $itemType
     * @param int $itemId
     * @return bool
     */
    public static function updateFavorite(bool $newStatus, string $itemCategoryType, string $itemType, int $itemId): bool
    {
        //todo-cart-logger
        $BaseResponse = new BaseResponse();
        try {
            if(!self::isAuth(123)) {
                throw new ErrorException(lang('page_role_admin'));
            }
            $successesFlag = false;
            switch ($itemCategoryType) {
                case 'product':
                    if($itemType === 'subcategory') {
                        $successesFlag = (new ItemCategory())->changeFavorite($itemId,(int)$newStatus);
                    } elseif ($itemType === 'subItem') {
                        $successesFlag = Item::changeFavorite($itemId,(int)$newStatus);
                    }
                    break;
                case 'package':
                    if($itemType === 'item') {
                        $successesFlag = Item::changeFavorite($itemId,(int)$newStatus);
                    }
                    break;
                case 'service':
                    if($itemType === 'subcategory') {
                        $successesFlag = MeetingCategories::changeFavorite($itemId,(int)$newStatus);
                    } elseif ($itemType === 'subItem') {
                        $successesFlag = ClassesType::changeFavorite($itemId,(int)$newStatus);
                    }
                    break;
            }
            if(!$successesFlag) {
                $errorMsg = str_replace("{{action}}",
                    $newStatus ? lang('action_added') : lang('action_removed'),
                    lang('error_changing_favorites'));
                throw new ErrorException($errorMsg);
            }
        } catch (Exception $e) {
            //todo-cart-add-erorr
            $BaseResponse->setError($e->getMessage());
        }
        return $BaseResponse->getData();

    }

    /****************************** post function ******************************/

    public static $validateCartItem = [
        'id' => 'required_if:type, !=, general|integer',
        'quantity' => 'integer', // only- product
        'variantId' => 'integer|exists:boostapp.item_details,id', // only- product
        'type' => 'required',
        'price' => 'required|numeric',
        'discount_amount' => 'numeric',
        'discount_type' => 'integer|between:1,2',//1-%
        'discount_value' => 'numeric',
        'durationMin' => 'required_if:type,==,service|integer',//meeting
        'date' => 'date_format:Y-m-d||required_if:type,==,service',//meeting
        'time' => 'required_if:type,==,service',//meeting
        'membershipStartCount' => 'integer|between:1,4', //4 ידיני,3 התחלה
        'packageManualStart' => 'date_format:Y-m-d|required_if:membershipStartCount,==,4',
        'packageManualEnd' => 'date_format:Y-m-d',
    ];

    /**
     * @param $Client
     * @param array $clientActivitiesArray
     * @param int $docsType
     * @param array $docDataArray
     * @param array $transactions
     * @param SaveInDebtResponse|null $SaveInDebtResponse
     * @return Docs|null
     */
    protected static function createDocs($Client, array $clientActivitiesArray, int $docsType = DocsService::DOCUMENT_TYPE_RECEPTION, array $docDataArray = [], array $transactions = [], SaveInDebtResponse $SaveInDebtResponse = null): ?Docs
    {
        switch ($docsType) {
            case DocsService::DOCUMENT_TYPE_INVOICE:
                $DocInvoice = DocsService::createDocByClientActivities($docsType, $Client, $clientActivitiesArray, $docDataArray);
                break;
            case DocsService::DOCUMENT_TYPE_RECEPTION:
                $DocInvoice = DocsService::createDocByClientActivities(DocsService::DOCUMENT_TYPE_INVOICE, $Client, $clientActivitiesArray, $docDataArray);
                if ($DocInvoice === null) {
                    return null; //todo
                }
                $transactionsArrayData = self::createPaymentDataForDocArray($transactions, $DocInvoice->id);
                $Docs = DocsService::createDocByClientActivities($docsType, $Client, $clientActivitiesArray, $docDataArray, $transactionsArrayData);
                if ($Docs === null || $Docs->id === 0 || $SaveInDebtResponse === null) {
                    return null;
                }
                $SaveInDebtResponse->addReceipt($Docs);
                break;
            case DocsService::DOCUMENT_TYPE_RECEIPT_TAX_INVOICE:
                $transactionsArrayData = self::createPaymentDataForDocArray($transactions);
                $DocInvoice = DocsService::createDocByClientActivities($docsType, $Client, $clientActivitiesArray, $docDataArray, $transactionsArrayData);
                break;
        }
        return $DocInvoice ?? null;
    }

    /**
     * @param array $transactionsArray
     * @param int $docId
     * @param bool $isRefund
     * @return array
     */
    protected static function createPaymentDataForDocArray(array $transactionsArray, int $docId = 0, bool $isRefund = false): array
    {
        $paymentDataForDocArray = [
            'paymentData' => [],
            'paymentTotalAmount' => 0,
        ];
        $docId !== 0 ? $paymentDataForDocArray['docInvoiceId'] = $docId : null;
        foreach ($transactionsArray as $transaction) {
            $transaction['type'] = DocPaymentTypeEnum::getStatusByCheckOutFrontText($transaction['type'] ?? '');
            $transaction['dateCreated'] = date("Y-m-d G:i:s", strtotime($transaction['dateCreated'] ?? 'now'));
            $paymentDataForDocArray['paymentTotalAmount'] += ($transaction['price'] ?? 0);

            if((int)$transaction['type'] === DocPaymentTypeEnum::CREDIT_CARD && isset($transaction['creditPaymentSettings']) ) {
                switch ((int)$transaction['creditPaymentSettings']) {
                    case CardPaymentSettingEnum::OTHER_TERMINAL:
                        $transaction['BrandName'] = $isRefund ? 'כרטיס זוכה במסוף אחר' : 'כרטיס חויב במסוף אחר';
                        $transaction['CreditType'] = CardPaymentSettingEnum::name(CardPaymentSettingEnum::OTHER_TERMINAL);
                        $transaction['Issuer'] = 2;
                        $transaction['tashType'] = (isset($transaction['paymentNumber']) && $transaction['paymentNumber'] > 1 ) ? 2 : 1;
                        break;
                    case CardPaymentSettingEnum::MANUAL_IFRAME:
                    case CardPaymentSettingEnum::TOKEN:
                        if((int)$transaction['creditPaymentSettings'] === CardPaymentSettingEnum::MANUAL_IFRAME) {
                            $transaction['CreditType'] = CardPaymentSettingEnum::name(CardPaymentSettingEnum::MANUAL_IFRAME);
                        } else {
                            $transaction['CreditType'] = CardPaymentSettingEnum::name(CardPaymentSettingEnum::TOKEN);
                        }
                        if(isset($transaction['loginOrderId'])) {
                            /** @var OrderLogin $Order */
                            $Order = OrderLogin::find($transaction['loginOrderId']);
                            if($Order !== null) {
                                $transactionInfo = $Order->getTransactionInfo();
                                $transaction = array_merge($transaction, $transactionInfo);
                                $transaction['CheckDate'] = date('Y-m-d', strtotime($Order->CreatedAt));
                            }
                        }

                }
            }
            foreach ($transaction as $transactionField => $value) {
                if (isset(self::DOCS_PAYMENT_FILED_FRONT_TO_DB[$transactionField])) {
                    unset($transaction[$transactionField]);
                    $transaction[self::DOCS_PAYMENT_FILED_FRONT_TO_DB[$transactionField]] = $value;
                } else {
                    unset($transaction[$transactionField]);
                }
            }
            $paymentDataForDocArray['paymentData'][] = $transaction;
        }
        return $paymentDataForDocArray;
    }
    /**
     * @param array $items
     * @param int $clientId
     * @param array $clientDetails
     * @param array $docDataArray
     * @param int $docsType
     * @param array $transactions
     * @param int $checkOrderId
     * @return bool
     */
    public static function postCartItems(array $items, int $clientId = 0, array $clientDetails=[], array $docDataArray = [], int $docsType = 1, array $transactions = [], int $checkOrderId = 0): bool
    {
        $SaveInDebtResponse = new SaveInDebtResponse();
        try {
            if (!self::isAuth(123)) {
                throw new ErrorException(lang('page_role_admin'));
            }
            $studioSettings = Settings::getSettings(self::checkAuth($SaveInDebtResponse));
            $SaveInDebtResponse->setBusiness($studioSettings);

            //todo change to function -add/get client
            $Client = self::getOrCreateClient($studioSettings->CompanyNum, $clientId, $clientDetails);
            if ($Client) {
                $SaveInDebtResponse->setClient($Client);
            }
            //add clientActivityToClient
            $clientActivitiesArray = self::postItems($items, $Client, !(bool)$clientId, $docDataArray);

            //add docs
            $DocInvoice = self::createDocs($Client, $clientActivitiesArray, $docsType, $docDataArray, $transactions, $SaveInDebtResponse);
            if ($DocInvoice === null || !isset($DocInvoice->TypeDoc, $DocInvoice->id)) {
                throw new ErrorException('שגיאה ביצירת מסמך - יש למחוק את כל הפריטים',3);
            }
            $SaveInDebtResponse->setInvoice($DocInvoice);

            if($checkOrderId > 0) {
                if(!CheckoutOrder::updateStatusById($checkOrderId, CheckoutOrder::STATUS_AFTER_PAYMENT_CLOSE)) {
                    throw new ErrorException('הוספת המוצרים והפקה תקינה , בעיה בסגרת ההזמנה CheckoutOrderID - ' .  $checkOrderId);
                }
            }
            return $SaveInDebtResponse->getData();

        } catch (\Throwable $e) {
            try {
                if(empty($Client)) {
                    self::addToLogger($e->getMessage());
                    return $SaveInDebtResponse->returnError($e->getMessage());
                }
                // make refund of all the paid transactions
//                $studioSettings = $Client->studioSettings();
//                $paymentMethod = PaymentService::getPaymentMethodByType($studioSettings->TypeShva);
//                unset($transaction);
//                foreach ($transactions as $transaction) {
//                    if (isset($transaction['isPaid']) && $transaction['isPaid']) {
//                        $amount = $transaction['price'];
//                        $order = OrderService::createOrder($Client, $amount, 1, OrderLogin::TYPE_REFUND);
//                        $order->TokenId = $token->id;
//                        $order->PaymentMethod = $paymentMethod;
//                        $order->save();
//
//                        $refundResult = PaymentService::makeRefund($order, $transaction);
//                        $transactionModel = Transaction::saveTransaction($Client, $refundResult, Auth::user()->id);
//
//                        $order->TransactionId = $transactionModel->id;
//                        $order->Status = OrderLogin::STATUS_PAID;
//                        $order->save();
//                    }
//                }
            } catch (\Throwable $e) {
                LoggerService::error($e);
                LoggerService::info($transactions);
            }
            //A new customer has been created?
//            if (isset($client) && $isNewClient) {
//                // Delete customer
//                $client->delete();
//            }

            // is clientActivity created
            if (!empty($clientActivitiesArray)) {
                //created or updated - clientActivity
                foreach ($clientActivitiesArray as $clientActivity) {
                    $c = 1;
                }
            }

            self::addToLogger($e->getMessage());
        }
        return $SaveInDebtResponse->returnError($e->getMessage());
    }
    /**
     * @param int $clientId
     * @param array $clientDetails
     * @return Client|null
     * @throws ErrorException
     * @throws Exception
     */
    public static function postClient(int $clientId = 0, array $clientDetails=[], int $companyNum = 0): ?Client
    {
        if (!empty($clientDetails)) {
            if (isset($clientDetails['name'], $clientDetails['phone'])) {

                $clientId = ClientService::findByPhoneAndStudio($companyNum, $clientDetails['phone']);
                if($clientId === 0) {
                    //create client
                    $newClientResponse = ClientService::addClientByPhoneAndName($clientDetails['phone'], $clientDetails['name']);
                    if (!isset($newClientResponse['Status'], $newClientResponse['Message']['client_id']) || $newClientResponse['Status'] === 'Error') {
                        throw new ErrorException('שגיאה ביצירת לקוח חדש'); //TODO-CART-TRN
                    }
                    $clientId = (int)$newClientResponse['Message']['client_id'];
                    //todo-cart-addto-logger //A new customer has been created
                }
            } else {
                throw new ErrorException('שגיאה ביצירת לקוח חדש'); //TODO-CART-TRN
            }
        }
        /** @var Client $client */
        $client = Client::find($clientId);
        if ($client === null) {
            throw new ErrorException('שגיאה לקוח שנבחר לא תקין'); //TODO-CART-TRN
        }
        return $client;
    }

    /**
     * @param array $item
     * @return void
     * @throws Exception
     */
    protected static function postItemValid(array &$item): void
    {
        if(!empty($item['discount'])) {
            $item['discount_type'] = (int)($item['discount']['type'] ?? 1);
            if($item['discount_type'] === self::DISCOUNT_TYPE_PERCENT){
                if(isset($item['discount']['value']) || $item['discount']['value'] < 0) {
                    $item['discount_value'] = $item['discount']['value'] > 100 ? 100 : $item['discount']['value'];
                } else {
                    $item['discount_value'] = 0;
                }
            } else {
                $item['discount_value'] = $item['discount']['value'];
            }
            $item['discount_amount'] = $item['discount']['amount'] ?? 0;
            unset($item['discount']);
        }
        $validator = Validator::make($item, self::$validateCartItem);
        if ($validator->passes()) {
            return;
        }
        throw new Exception(json_encode($validator->errors()->toArray()));

    }
    /**
     * @param array $items
     * @param Client $Client
     * @param bool $newClient
     * @param array $docDataArray
     * @return array - $client Activities Id Array
     * @throws ErrorException
     */
    protected static function postItems(array $items = [], Client $Client, bool $newClient, array &$docDataArray): array
    {
        $clientActivitiesIdArray = [];
        $totalAmount = 0;
        try {
            foreach ($items as $item) {
                self::postItemValid($item);
                $itemDiscount = $item['discount_amount'] ?? 0;
                $quantity = $item['quantity'] ?? 1;
                $totalAmount +=  (($item['price']  * $quantity) - $itemDiscount) ?? 0;
                switch ($item['type']) {
                    case 'product':
                        $clientActivityId = self::postCartItemProduct($Client->id, $item);
                        break;
                    case 'package':
                        $clientActivityId = self::postCartItemPackage($Client->id, $item);
                        break;
                    case 'lesson':
                        $clientActivityId = self::postCartItemLesson($Client, $item);
                        break;
                    case 'debt':
                        $clientActivityId = self::postCartItemDebt($Client->id, $item);
                        break;
                    case 'general':
                        $clientActivityId = self::postCartGeneralItem($Client->id, $item);
                        break;
                    case 'service':
                        $clientActivityId = self::postCartItemMeeting($Client->id, $item);
                        break;
                }
                if ($clientActivityId !== 0 ) {
                    $clientActivitiesIdArray[] = $clientActivityId;
                } else {
                    throw new ErrorException('item - '.$item['id'] . 'type -'. $item['type'] . ' not valid');
                }
            }
        } catch (Exception $e) {
            //todo-cart-logger
            self::addToLogger($e->getMessage());
            $messageArray = $newClient ? ['create new client - ' . $Client->CompanyName ?? ' ' . ' -ID:' . $Client->id ?? 0]: [];
            $p=1; //מחיקה כל מה שנוצר ושליחת הודעה
            foreach ($messageArray as $message) {
                self::addToLogger($message , LoggerService::TYPE_DEBUG);
            }
            throw new ErrorException('התגלתה שגיאה בהוספת אחד המוצרים, לפרטים נוספים כנס ללוג מערכת');
        }
        $docDataArray['Amount'] = $totalAmount;
        return $clientActivitiesIdArray;
    }
    /**
     * @param int $clientId
     * @param array $itemData
     * @return int
     * @throws ErrorException
     */
    protected static function postCartItemDebt(int $clientId, array $itemData = []): int
    {
        try {
            $clientActivityDataArray = self::getBaseItemDataArray($clientId, $itemData);
            $clientActivityDataArray['clientActivityId'] = $clientActivityDataArray['itemId'];
            unset($clientActivityDataArray['itemId']);
            $response = ClientActivityService::updateClientActivities($clientActivityDataArray);
            if(isset($response['Status']) && $response['Status'] === 1) {
                return $clientActivityDataArray['clientActivityId'];
            }
            if(isset($response['Error'])) {
                self::addToLogger($response['Error'] , LoggerService::TYPE_ERROR);
            }
        } catch (Exception $e) {
            throw new ErrorException('item - '.$itemData['id'] . 'type -'. 'debt' . ' not valid');
        }
        return 0;

    }
    /**
     * @param int $clientId
     * @param array $itemData
     * @return int
     */
    protected static function postCartItemProduct(int  $clientId, array $itemData = []): int
    {
        $assignMembershipDataArray = self::getBaseItemDataArray($clientId, $itemData);
        isset($itemData['quantity']) ? $assignMembershipDataArray['itemQuantity'] = $itemData['quantity'] : null ;
        isset($itemData['variantId']) ? $assignMembershipDataArray['itemDetailsId'] = $itemData['variantId'] : null ;
        return self::assignItemToClient($assignMembershipDataArray);
    }
    /**
     * @param int $clientId
     * @param array $itemData
     * @return int
     */
    protected static function postCartGeneralItem(int $clientId, array $itemData = []): int
    {
        $itemData['id'] = ItemService::getGeneralItem(Auth::user()->CompanyNum);
        $assignMembershipDataArray = self::getBaseItemDataArray($clientId, $itemData);
        isset($itemData['name']) ? $assignMembershipDataArray['activityName'] = $itemData['name'] : null ;
        isset($itemData['quantity']) ? $assignMembershipDataArray['itemQuantity'] = $itemData['quantity'] : null ;
        isset($itemData['variantId']) ? $assignMembershipDataArray['itemDetailsId'] = $itemData['variantId'] : null ;
        return self::assignItemToClient($assignMembershipDataArray);
    }
    /**
     * @param int $clientId
     * @param array $itemData
     * @return int 0 -error other ClientActivityId
     */
    protected static function postCartItemPackage(int $clientId, array $itemData = []): int
    {
        $assignMembershipDataArray = self::getBaseItemDataArray($clientId, $itemData);
        isset($itemData['packageManualStart']) ? $assignMembershipDataArray['startDate'] = $itemData['packageManualStart'] : null ;
        isset($itemData['packageManualEnd']) ? $assignMembershipDataArray['endDate'] = $itemData['packageManualEnd'] : null ;
        isset($itemData['membershipStartCount']) ? $assignMembershipDataArray['calcType'] = $itemData['membershipStartCount'] : null ;
        return self::assignItemToClient($assignMembershipDataArray);
    }
    /**
     * @param int $clientId
     * @param array $itemData
     * @return int 0 -error other ClientActivityId
     */
    protected static function postCartItemLesson(Client $Client, array $itemData = []): int
    {
        try {
            $classStudioDateId = $itemData['id'];
            /** @var ClassStudioDate $ClassStudioDate */
            $ClassStudioDate = ClassStudioDate::find($classStudioDateId);

            //Checking whether the customer is already registered for the class
            if($Client->isAssignToClass($classStudioDateId)) {
                $messageArray[] = 'customer is already registered for the class - ' . $ClassStudioDate->ClassName;
                return 0;
            }

            $itemId = $Client->Status === Client::STATUS_LEAD ?
                Item::getSingleClassItemLead($ClassStudioDate->ClassNameType, $Client->id) :
                Item::getSingleClassItem($ClassStudioDate->ClassNameType);
            if ($itemId === "overLimitLeadSubscription") {
                $itemId = Item::getSingleClassItem($ClassStudioDate->ClassNameType);
                $messageArray[] = 'over limit lead subscription - add normal';
            }
            $itemData['id'] = $itemId;
            $assignMembershipDataArray = self::getBaseItemDataArray($Client->id, $itemData);
            $assignMembershipDataArray['ItemText'] = $ClassStudioDate->ClassName . ' - ' .  date('d/m', strtotime($ClassStudioDate->StartDate));
            $activityId = self::assignItemToClient($assignMembershipDataArray);
            $addWaitingClient = isset($itemData['lessonClientAssign']) && $itemData['lessonClientAssign'] === 'waitingList' ? 0 : 1;
            $isaAssignToClass  = self::assignClientToClass($ClassStudioDate, $Client->id, $activityId, $addWaitingClient);
            if(!$isaAssignToClass) {
                return 0;
            }
            return $activityId;
        } catch (Exception $e) {
            if (isset($activityId) && $activityId > 0) {
                //remove $activityId and send message
                ClientActivities::where('id', $activityId)->delete();
                return  0;
            }
            return 0;
        }
    }

    /**
     * @param int $clientId
     * @param array $itemData
     * @return int 0 -error other ClientActivityId
     */
    protected static function postCartItemMeeting(int $clientId, array $itemData = []): int
    {
        try {
            $treatmentsArray[] = [
                'classTypeId' => $itemData['id'],
                'cost' => $itemData['price'],
                'time' => $itemData['time'],
                'duration' => $itemData['durationMin']
            ];
            $meetingDataArray = [
                'from_cart' => true,
                'ClientId' => $clientId,
                'treatments' => $treatmentsArray,
                'GuideId' => $itemData['coachId'] ?? Users::getFirstCoach(Auth::user()->CompanyNum),
                'Floor' => $itemData['diaryId'] ?? Section::getFirstFloor(Auth::user()->CompanyNum),
                'StartDate' => $itemData['date'],
                'overrideLimitation' => 1,
                'DiscountType' => $itemData['discount_type'] ?? 0,
                'Discount' => $itemData['discount_value'] ?? 0,
                'DiscountAmount' => $itemData['discount_amount'] ?? 0,
            ];
            $response = CreateMeetingService::create($meetingDataArray);
            if($response['status'] === 1 && $response['data']['clientActivityIds']) {
                return $response['data']['clientActivityIds'];
            }
        } catch (Exception $e) {
           return 0;
        }
        return 1;
    }

    /**
     * @param int $clientId
     * @param array $itemData
     * @return array
     */
    protected static function getBaseItemDataArray(int $clientId, $itemData = []): array
    {
        $assignMembershipDataArray = [
            'clientId' => $clientId,
            'itemId' => $itemData['id'] ?? 0,
            'itemPrice'=> $itemData['price'],
        ];
        if (isset($itemData['discount_amount'])) {
            $assignMembershipDataArray['discountAmount'] = $itemData['discount_amount'];
        }
        if (isset($itemData['discount_type'])) {
            $assignMembershipDataArray['discountType'] = $itemData['discount_type'];
        }
        if (isset($itemData['discount_value'])) {
            $assignMembershipDataArray['discountValue'] = $itemData['discount_value'];
        }
        return $assignMembershipDataArray;
    }

    /**
     * @param array $assignMembershipDataArray
     * @return int 0 -error other ClientActivityId
     */
    protected static function assignItemToClient(array $assignMembershipDataArray): int
    {
        $response = ClientActivityService::assignItemToClient($assignMembershipDataArray);
        if (isset($response['Status'], $response['ClientActivityId']) && $response['Status'] === 1 && is_numeric($response['ClientActivityId'])) {
            return $response['ClientActivityId'];
        }
        return 0;
    }
    /**
     * @param ClassStudioDate $ClassStudioDate
     * @param int $clientId
     * @param int $activityId
     * @param int $addWaitingClient
     * @return bool
     */
    protected static function assignClientToClass(ClassStudioDate $ClassStudioDate, int $clientId,
                                                int $activityId, int $addWaitingClient = 1 ): bool
    {
        $data = [
            'classTypeId' => $ClassStudioDate->ClassNameType,
            'classId' => $ClassStudioDate->id,
            'clientId' => $clientId,
            'activityId' => $activityId,
            'overrideStatus' => $addWaitingClient, //1 - assign else waiting
            'deviceId' => 0,
//            'status' => 0,
//            'regularClassId' => 0,
            'popup' => 1, //0 - show popups
        ];
        $response = ClassStudioActService::assignClientToClass($data);
        if (isset($response['Status'], $response['actId']) && $response['Status'] === 1 && is_numeric($response['actId'])) {
            return true;
        }
        return false;
    }
    /**
     * @param int $clientId
     * @param array $itemData
     * @return array
     */
    protected static function getBaseDocDataArray(int $clientId, $itemData = []): array
    {
        $assignMembershipDataArray = array(
            'clientId' => $clientId,
            'itemId' => $itemData['id'],
            'itemPrice'=> $itemData['price'],
        );
        isset($itemData['discount_amount']) ? $assignMembershipDataArray['discountAmount'] = $itemData['discount_amount'] : null ;
        isset($itemData['discount_type']) ? $assignMembershipDataArray['discountType'] = $itemData['discount_type'] : null ;
        isset($itemData['discount_value']) ? $assignMembershipDataArray['discountValue'] = $itemData['discount_value'] : null ;
        return $assignMembershipDataArray;
    }

    /****************************** Checkout function ******************************/

    /**
     * @param int $docId
     * @param int $checkOrderId
     * @return bool
     */
    public static function getCheckoutDataFromDoc(int $docId, int $checkOrderId): bool {
        $CheckOutDataResponse = new CheckOutDataResponse($docId);
        $companyNum = self::checkAuth($CheckOutDataResponse);
        if ($companyNum === 0){
            return false; //todo-logger
        }
        try {
            $invoiceId = DocsLinkToInvoice::getInvoiceIdFromDoc($docId);
            /** @var Docs $InvoiceDoc */
            $InvoiceDoc = Docs::find($invoiceId);
            if($InvoiceDoc === null) {
                throw new ErrorException('docs not valid');
            }
            if($InvoiceDoc->CompanyNum != $companyNum) {
                throw new ErrorException('docs companyNum not valid');
            }
            if(!$InvoiceDoc->isInvoiceDocs()) {
                throw new ErrorException('docs not valid to show - not Invoice');
            }
            if((int)$InvoiceDoc->PayStatus === Docs::PAY_STATUS_CLOSE) {
                throw new ErrorException('docs not valid to show - close');
            }

            if($checkOrderId === 0) {
                $checkOrderId = CheckoutOrder::getOpenOrderIdByClient($InvoiceDoc->ClientId);
                if($checkOrderId !== 0) {
                    //found open order
                    /** @var CheckoutOrder $CheckoutOrder */
                    $CheckoutOrder = CheckoutOrder::find($checkOrderId);
                    $CheckOutDataResponse->setOpenOrderId($CheckoutOrder->id, ((bool)$CheckoutOrder->IsRefundOrder));
                    return $CheckOutDataResponse->getData();
                }
            } else {
                $OrderLoginArray = OrderLogin::getByCheckOutId($checkOrderId);
                foreach ($OrderLoginArray as $OrderLogin) {
                    $CheckOutDataResponse->addTransaction($OrderLogin);
                }
            }
            self::addCartToCheckout($InvoiceDoc, $CheckOutDataResponse);
            self::addClientToCheckout($InvoiceDoc, $CheckOutDataResponse);
            self::addReceiptsCheckout($InvoiceDoc, $CheckOutDataResponse);
            self::addRefundCheckout($InvoiceDoc, $CheckOutDataResponse);

        } catch (Exception $e) {
            //todo-cart-add-erorr
            return $CheckOutDataResponse->returnError($e->getMessage());
        }
        return $CheckOutDataResponse->getData();
    }

    /**
     * @param int $checkoutOrderId
     * @return bool
     */
    public static function getCheckoutDataOrderId(int $checkoutOrderId): bool {
        require_once __DIR__ . '/../../../app/controllers/responses/cart/checkOut/getCheckOutData/CheckOutDataResponseOrder.php';

        $CheckOutDataResponse = new CheckOutDataResponseOrder($checkoutOrderId);
        $companyNum = self::checkAuth($CheckOutDataResponse);
        if ($companyNum === 0){
            return false; //todo-logger
        }
        try {
            /** @var CheckoutOrder $CheckoutOrder */
            $CheckoutOrder = CheckoutOrder::find($checkoutOrderId);
            if($CheckoutOrder === null) {
                throw new ErrorException('$CheckoutOrder not valid  ID-  ' . $checkoutOrderId);
            }
            if((int)$CheckoutOrder->Status !== CheckoutOrder::STATUS_AFTER_PAYMENT_OPEN) {
                throw new ErrorException('CheckoutOrder status not valid');
            }
            if($CheckoutOrder->InvoiceId) {
                return self::getCheckoutDataFromDoc($CheckoutOrder->InvoiceId, $checkoutOrderId);
            }

            $OrderLoginArray = OrderLogin::getByCheckOutId($checkoutOrderId);
            foreach ($OrderLoginArray as $OrderLogin) {
                $CheckOutDataResponse->addTransaction($OrderLogin);
            }


            $Settings = Settings::getByCompanyNum($companyNum);
            if(empty($Settings) || (int)$Settings->CompanyNum !== (int)$CheckoutOrder->CompanyNum) {
                throw new ErrorException('companyNum not valid');
            }
            /** @var Client $Client */
            $Client = Client::find($CheckoutOrder->ClientId);
            if ($Client === null) {
                throw new ErrorException('client not valid id = ' . $CheckoutOrder->ClientId); //TODO-CART-TRN
            }

            $CheckOutDataResponse->setClient($Client);
            $CheckOutDataResponse->setSettings($Settings);
            $CheckOutDataResponse->setCheckoutOrder($CheckoutOrder);

            //get all items
            $CheckOutDataResponseOrderArray = CheckoutOrderItem::getByCheckoutOrderId($CheckoutOrder->id);
            foreach ($CheckOutDataResponseOrderArray as $CheckOutDataResponseOrder) {
                $CheckOutDataResponse->addItem($CheckOutDataResponseOrder);
            }
        } catch (Exception $e) {
            //todo-cart-add-erorr
            return $CheckOutDataResponse->returnError($e->getMessage());
        }
        return $CheckOutDataResponse->getData();
    }

    /**
     * @param array $items
     * @param array $discount
     * @param Client $Client
     * @return int
     * @throws ErrorException
     */
    protected static function saveCheckOutOrders(array $items, array $discount, Client $Client): int
    {
        try {
            $totalAmount = 0;
            $CheckoutOrder = CheckoutOrder::creatByClient($Client, $discount);
            if ($CheckoutOrder->id <= 0) {
                throw new ErrorException('error on saveCheckOutOrders create CheckoutOrder ');
            }
            foreach ($items as $item) {
                $CheckoutOrderItem = CheckoutOrderItem::creatByCartItem($CheckoutOrder->id, $item, $CheckoutOrder->CompanyNum);
                if ($CheckoutOrderItem->id <= 0) {
                    throw new ErrorException('error on saveCheckOutOrders create CheckoutOrderItem ');
                }
                $totalAmount += $CheckoutOrderItem->Price - ($CheckoutOrderItem->DiscountAmount ?? 0);
                if (!empty($item)) {
                    $CheckoutOrderItemDetails = CheckoutOrderItemDetails::creatByCartItem($CheckoutOrderItem->id, $item);
                    if ($CheckoutOrderItemDetails->id <= 0) {
                        throw new ErrorException('error on saveCheckOutOrders create CheckoutOrderItemDetails ');
                    }
                }
            }
            $CheckoutOrder->Amount = $totalAmount;
            $CheckoutOrder->save();
        } catch (Exception $e) {
            //todo-cart-logger
            self::addToLogger($e->getMessage());
            throw new ErrorException('התגלתה שגיאה בשמירת אחד המוצרים, לפרטים נוספים כנס ללוג מערכת');
        }
        return $CheckoutOrder->id ?? 0;
    }

    /**
     * @param Docs $Doc
     * @param CheckOutDataResponse $CheckOutDataResponse
     */
    protected static function addCartToCheckout(Docs $Doc, CheckOutDataResponse $CheckOutDataResponse): void
    {
        $CheckOutDataResponse->setCart($Doc);
        //add list info item in Invoice
        $docsListArray = DocsList::getAllByDocId($Doc->id);
        foreach ($docsListArray as $DocsList) {
            $CheckOutDataResponse->addItemToCartInfo($DocsList);
        }
    }

    //key -> frontKey , value properties field in db
    protected const DOCS_PAYMENT_FILED_FRONT_TO_DB = [
        //all
        'type' => 'TypePayment' ,
        'price' => 'Amount' ,
        'dateCreated' => 'Dates',
        //check
        'checkNumber' => 'CheckNumber',
        'checkPaymentDate' => 'BankDate',
        'checkAccountNumber' => 'CheckBank',
        'checkBranchNumber' => 'CheckBankSnif',
        'checkBankNumber' => 'CheckBankCode',
        //bank_transfer
        'bankTransferRefNumber' => 'BankNumber',
        'bankTransferDepositDate' => 'CheckDate',
//        'bankTransferType' => '', //todo סוג העברה לא קייים במערכת

        //credit_card
        'tashTypeDB' => 'tashType',
        'BrandName' => 'BrandName',
        'Issuer' => 'Issuer',
        'CreditType' => 'CreditType',
        'PayToken' => 'PayToken',
        'YaadCode' => 'YaadCode',
        'L4digit' => 'L4digit',
        'CCode' => 'CCode',
        'ACode' => 'ACode',
        'Payments' => 'Payments',
        'Brand' => 'Brand',
        'Bank' => 'Bank',
        'TransactionId' => 'TransactionId',
        'CheckDate' => 'CheckDate',
        'MeshulamPageCode' => 'MeshulamPageCode',

        //other trminal
        'paymentNumber' => 'Payments',
        'credit4Number' => 'L4digit',
        'creditConfirmationNumber' => 'ACode',
        'creditOriginalChargeDate' => 'CheckDate',
        'creditTypeCard' => 'Brand',
        'creditTypeBank' => 'Bank',
        'tashType' => 'tashType',
//        'Excess' => 'Excess',

    ];

    /**
     * @param Docs $InvoiceDoc
     * @param CheckOutDataResponse $CheckOutDataResponse
     * @throws ErrorException
     */
    protected static function addReceiptsCheckout(Docs $InvoiceDoc, CheckOutDataResponse $CheckOutDataResponse): void
    {
        $docsArrayId = DocsLinkToInvoice::getAllDocsLinkToInvoiceIds($InvoiceDoc->id);
        if($InvoiceDoc->isReceiptDocs(true)){
            array_unshift($docsArrayId , (int)$InvoiceDoc->id);
        }
        foreach ($docsArrayId as $docId) {
            /** @var Docs $Doc */
            $Doc = Docs::find($docId);
            if($Doc === null ||$Doc->CompanyNum !== $InvoiceDoc->CompanyNum ) {
                throw new ErrorException('docs not valid');
            }
            if($Doc->isReceiptDocs(true, true)){
                $CheckOutDataResponse->addReceipt($Doc);
            }
        }
    }
    /**
     * @param Docs $Doc
     * @param CheckOutDataResponse $CheckOutDataResponse
     */
    protected static function addClientToCheckout(Docs $Doc , CheckOutDataResponse $CheckOutDataResponse):void
    {
        /** @var Client $Client */
        $Client = Client::find($Doc->ClientId);
        if($Client && $Client->CompanyNum == $Doc->CompanyNum) {
            $checkoutOrderId = CheckoutOrder::getOpenOrderIdByClient($Client->id);
            $CheckOutDataResponse->setClient($Client, $Doc, $checkoutOrderId);
        }
    }

    /**
     * @param string $phone
     * @param int $invoiceId
     * @param int $receiptId
     * @return bool
     */
    public static function sendDoc(string $phone, int $invoiceId = 0, int $receiptId = 0): bool
    {
        //todo-cart-logger
        $BaseResponse = new BaseResponse();
        $companyNum = self::checkAuth($BaseResponse);
        if ($companyNum === 0){
            return false; //todo-logger
        }
        try {
            if($invoiceId <= 0 &&  $receiptId <= 0){
                throw new ErrorException('invoiceId or receiptId not valid');
            }
            $companyName = Settings::getCompanyNameByNum($companyNum);
            if($companyName === null){
                throw new ErrorException('companyName not valid');
            }

            /** @var Client|null $Client */
            $Client = null;
            if($invoiceId > 0) {
                /** @var Docs $Invoice */
                $Invoice = Docs::find($invoiceId);
                if($Invoice === null || (int)$Invoice->CompanyNum !== $companyNum){
                    throw new ErrorException('invoiceId not valid');
                }
                $Client = Client::find($Invoice->ClientId);
            } else{
                $Invoice = null;
            }
            if($receiptId > 0) {
                /** @var Docs $Receipt */
                $Receipt = Docs::find($receiptId);
                if($Receipt === null || (int)$Receipt->CompanyNum !== $companyNum){
                    throw new ErrorException('receiptId not valid');
                }
                if($Client === null) {
                    $Client = Client::find($Receipt->ClientId);
                } else if((int)$Client->id !== (int)$Receipt->ClientId){
                    throw new ErrorException('sendDoc client not valid');
                }
            } else {
                $Receipt = null;
            }
            if($Client === null) {
                throw new ErrorException('sendDoc client not valid');
            }

            $Client->ContactMobile = $phone ?? $Client->ContactMobile;

            $isSuccessful = DocsService::sendCheckoutDocsBySMS($companyName, $Client, $Invoice, $Receipt);
            if($isSuccessful) {
                return $BaseResponse->getData();
            }
            throw new ErrorException('sendDoc send error');
        } catch (Exception $e) {
            //todo-cart-add-erorr
            $BaseResponse->setError($e->getMessage());
            return $BaseResponse->getData();
        }
    }

    /**
     * @param int $docsInvoiceId
     * @param int $docsType
     * @param array $transactions
     * @param int $clientId
     * @param int $checkOrderId
     * @return bool
     */
    public static function createPartialReceipt(int $docsInvoiceId, int $docsType = 1, array $transactions = [], int $clientId = 0, int $checkOrderId = 0): bool
    {
        $SaveInDebtResponse = new SaveInDebtResponse();
        try {
            if(!self::isAuth(123)) {
                throw new ErrorException(lang('page_role_admin'));
            }
            /** @var Client $Client */
            $Client = Client::find($clientId);
            if($Client === null) {
                throw new ErrorException('שגיאה לקוח שנבחר לא תקין'); //TODO-CART-TRN
            }
            $docsInvoiceId = DocsLinkToInvoice::getInvoiceIdFromDoc($docsInvoiceId);
            /** @var Docs $DocInvoice */
            $DocInvoice = Docs::find($docsInvoiceId);
            if($DocInvoice === null) {
                throw new ErrorException('sendDoc docs id not valid');
            }
            $SaveInDebtResponse->setClient($Client);
            $clientActivitiesArray = DocsClientActivities::getClientActivitiesIds($docsInvoiceId);
            //add docs
            $transactionsArrayData = self::createPaymentDataForDocArray($transactions, $docsInvoiceId);
            $Docs = DocsService::createDocByClientActivities($docsType, $Client, $clientActivitiesArray, [], $transactionsArrayData);
            if($Docs === null || $Docs->id === 0) {
                throw new ErrorException('createPartialReceipt add doc fail'); //todo
            }
            $SaveInDebtResponse->setInvoice($DocInvoice);
            $SaveInDebtResponse->addReceipt($Docs);
            if($checkOrderId > 0) {
                if(!CheckoutOrder::updateStatusById($checkOrderId, CheckoutOrder::STATUS_AFTER_PAYMENT_CLOSE)) {
                    throw new ErrorException('הוספת המוצרים והפקה תקינה , בעיה בסגרת ההזמנה CheckoutOrderID - ' .  $checkOrderId);
                }
            }

        }  catch (Exception | \Throwable $e) {
            //TODO ADDD REMOVE
            self::addToLogger($e->getMessage());
            return $SaveInDebtResponse->returnError($e->getMessage());
        }
        return $SaveInDebtResponse->getData();
    }

    /**
     * @param array $items
     * @param int $clientId
     * @param array $clientDetails
     * @param array $docDataArray
     * @param array $transactions
     * @param int $checkOrderId
     * @return bool
     */
    public static function postCheckOutItems(array $items, int $clientId = 0, array $clientDetails=[], array $docDataArray = [], array $transactions = [], int $checkOrderId = 0): bool
    {
        $businessType = Settings::getBusinessType((int)Auth::user()->CompanyNum);
        if(in_array($businessType, BusinessType::BUSINESS_TYPE_WITH_OUT_VAT, true)) {
            $docType = DocsService::DOCUMENT_TYPE_RECEPTION;
        } else {
            $docType = DocsService::DOCUMENT_TYPE_RECEIPT_TAX_INVOICE;
        }
        return self::postCartItems($items, $clientId, $clientDetails, $docDataArray, $docType, $transactions, $checkOrderId);
    }

    /**
     * @param int $companyNum
     * @param int|null $clientId
     * @param array $clientDetails
     * @return Client
     * @throws ErrorException
     */
    protected static function getOrCreateClient(int $companyNum, ?int $clientId = null, array $clientDetails = []): Client
    {
        if(empty($clientId) && empty($clientDetails)) {
            $clientId = Client::getRandomClient($companyNum)->id;
        }
        $Client = self::postClient($clientId, $clientDetails, $companyNum);
        if ($Client === null || !$Client->id || (int)$Client->CompanyNum !== $companyNum) {
            throw new InvalidArgumentException('Wrong Client ID or Company');
        }
        return $Client;
    }

    /**
     * @param int|null $clientId
     * @param float $amount
     * @param int $paymentNumber
     * @param array $clientDetails
     * @param int $checkOutOrderId
     * @param int $invoiceId
     * @param array $items
     * @param array $discountArray
     * @return bool
     */
    public static function payWithNewCard(?int $clientId, float $amount, int $paymentNumber,  array $clientDetails, int $checkOutOrderId, int $invoiceId, array $items, array $discountArray): bool
    {
        $Response = new CheckoutCreditsResponse();
        $companyNum = self::checkAuth($Response);
        if ($companyNum === 0){
            return false; //todo-logger
        }
        try {
            if(empty($items) && $checkOutOrderId === 0) {
                throw new InvalidArgumentException('$checkOutOrderId not valid');
            }
            $studioSettings = (new Settings($companyNum));
            $Client = self::getOrCreateClient($companyNum, $clientId, $clientDetails);

            $paymentSystem = PaymentService::getPaymentSystemByType($studioSettings->TypeShva);
            $Order = OrderService::createOrder($Client, $amount, $paymentNumber, OrderLogin::TYPE_PAYMENT_NEW_CARD);
            $Order->PaymentMethod = PaymentService::getPaymentMethodByType($studioSettings->TypeShva);
            if($invoiceId > 0) {
                $CheckoutOrder = CheckoutOrder::creatByClient($Client);
                $CheckoutOrder->InvoiceId = $invoiceId;
                $CheckoutOrder->save();
                if ($CheckoutOrder->id <= 0) {
                    throw new InvalidArgumentException('Wrong CheckoutOrder on invoiceId -' . $invoiceId);
                }
                $checkOutOrderId = $CheckoutOrder->id;
                $Response->setCheckOutOrderId($checkOutOrderId);
            } else if(!empty($items)) {
                $checkOutOrderId = self::saveCheckOutOrders($items, $discountArray, $Client);
                $Response->setCheckOutOrderId($checkOutOrderId);
            }
            $Order->CheckoutOrderId = $checkOutOrderId;
            $Order->save();
            $paymentType = $Order->NumPayment > 1 ? PaymentService::CARD_SYSTEM_PAYMENT_TYPE_PAYMENTS
                : PaymentService::CARD_SYSTEM_PAYMENT_TYPE_REGULAR;

            $iframeUrl = $paymentSystem->makeFirstPayment($Order,$paymentType, $Order->NumPayment);
            $Response->setIframeUrl($iframeUrl);
            $Response->setOrderId($Order->id);

        } catch (Exception | \Throwable $e) {
            //TODO ADDD REMOVE
            self::addToLogger($e->getMessage());
            return $Response->returnError($e->getMessage());
        }


        return $Response->getData();
    }


    /**
     * @param int $clientId
     * @param int $tokenId
     * @param float $amount
     * @param int $paymentNumber
     * @param int $checkOutOrderId
     * @param int $invoiceId
     * @param array $items
     * @param array $discountArray
     * @return bool
     */
    public static function payWithToken(int $clientId, int $tokenId, float $amount, int $paymentNumber, int $checkOutOrderId, int $invoiceId ,array $items, array $discountArray): bool
    {

        $Response = new CheckoutCreditsResponse();
        $companyNum = self::checkAuth($Response);
        if ($companyNum === 0){
            return false; //todo-logger
        }
        try {
            if(empty($items) && $checkOutOrderId === 0 && $invoiceId === 0) {
                throw new InvalidArgumentException('$checkOutOrderId not valid');
            }
            $studioSettings = (new Settings($companyNum));
            $Client = self::getOrCreateClient($companyNum, $clientId);

            $Token = Token::getById($tokenId);
            if (!$Token || (int)$Token->CompanyNum !== $companyNum || (int)$Token->ClientId !== (int)$Client->id) {
                throw new InvalidArgumentException('Wrong Token ID or Company');
            }

            $paymentSystem = PaymentService::getPaymentSystemByType($studioSettings->TypeShva);
            $Order = OrderService::createOrder($Client, $amount, $paymentNumber, OrderLogin::TYPE_PAYMENT_SAVED_CARD_MEETING, Auth::user()->id);
            $Order->TokenId = $tokenId;
            $Order->PaymentMethod = PaymentService::getPaymentMethodByType($studioSettings->TypeShva);
            if($invoiceId > 0) {
                $CheckoutOrder = CheckoutOrder::creatByClient($Client);
                $CheckoutOrder->InvoiceId = $invoiceId;
                $CheckoutOrder->save();
                if($CheckoutOrder->id <= 0) {
                    throw new InvalidArgumentException('Wrong CheckoutOrder on invoiceId -'.$invoiceId);
                }
                $checkOutOrderId = $CheckoutOrder->id;
                $Response->setCheckOutOrderId($checkOutOrderId);
            } else if(!empty($items)) {
                $checkOutOrderId = self::saveCheckOutOrders($items, $discountArray, $Client);
                $Response->setCheckOutOrderId($checkOutOrderId);
            }
            $Order->CheckoutOrderId = $checkOutOrderId;
            $Order->save();
            $paymentType = $Order->NumPayment > 1 ? PaymentService::CARD_SYSTEM_PAYMENT_TYPE_PAYMENTS
                : PaymentService::CARD_SYSTEM_PAYMENT_TYPE_REGULAR;

            $paymentResult = $paymentSystem->makePaymentWithToken($Order, $Token, $paymentType, $Order->NumPayment);
            $transactionModel = Transaction::saveTransaction($Client, $paymentResult, Auth::user()->id);

            $Order->TransactionId = $transactionModel->id;
            $Order->Status = OrderLogin::STATUS_PAID;
            $Order->save();
            $Order->updateCheckOutOrder();

            $Response->setOrderId($Order->id);

        } catch (Exception | \Throwable $e) {
            //TODO ADDD REMOVE
            self::addToLogger($e->getMessage());
            return $Response->returnError($e->getMessage());
        }


        return $Response->getData();
    }

    /**
     * @param null $clientId
     * @param int $invoiceId
     * @return bool
     */
    public static function getSavedCardTokens($clientId = null, int $invoiceId = 0): bool
    {
        require_once __DIR__ . '/../../Classes/Token.php';

        $Response = new CheckoutCreditsResponse();
        $companyNum = self::checkAuth($Response);
        if ($companyNum === 0){
            return false; //todo-logger
        }
        if($invoiceId > 0) {
            /** @var Docs $Doc */
            $Invoice = Docs::find($invoiceId);
            $docsArrayId = DocsLinkToInvoice::getAllDocsLinkToInvoiceIds($invoiceId);
            if($Invoice->isReceiptDocs(true)){
                array_unshift($docsArrayId , (int)$Invoice->id);
            }
            $DocsPayments = DocsPayment::getPaymentsByDocsIdArray($docsArrayId, $companyNum);
            foreach ($DocsPayments as $DocsPayment) {
                if($DocsPayment->isCardPayment()) {
                    $Response->addCreditCard($DocsPayment);
                }
            }
        } else if ($clientId !== null) {
            $creditCards = Token::getTokens($companyNum, $clientId, false);
            $Response->setCreditCards($creditCards);
        }
        return $Response->getData();
    }

    /**
     * @param int $checkOrderId
     * @return IdsResponse
     */
    public static function cancelAllPaymentsWithOutReceipt(int $checkOrderId): IdsResponse
    {
        $Response = new IdsResponse();
        $errorToUser = '';
        try {
            if($checkOrderId !== 0) {
                $OrderLoginArray = OrderLogin::getByCheckOutId($checkOrderId);
                foreach ($OrderLoginArray as $OrderLogin) {
                    $CancelResponse = self::cancelPaymentWithOutReceipt($OrderLogin->id);
                    if(!$CancelResponse->isSuccess()) {
                        $errorToUser .=  ($errorToUser === '' ? '' : ' , ' ) .   'orderLoginId = ביטול עסקה לא הצליחה ' . $OrderLogin->id  . ' והפעולה המשיכה יש לזכות את התשלום במסוף';
                        self::addToLogger($CancelResponse->getMessage(), LoggerService::TYPE_ERROR);
                        $Response->addId($OrderLogin->id);
                    }
                }
            }
            if($errorToUser !== '') {
                $Response->setError($errorToUser);
            }
        } catch (Exception $e) {
            //todo-cart-add-error
            $Response->setError($e->getMessage());
            return $Response;
        }
        return $Response;
    }

    /**
     * @param int $docId
     * @param int $checkOrderId
     * @param string $reason
     * @return bool
     */
    public static function cancellationEntireOrder(int $docId, int $checkOrderId, string $reason): bool
    {
        $Response = new IdsResponse();
        try {
            $companyNum = self::checkAuth($Response);
            if ($companyNum === 0 || $docId === 0){
                return false; //todo-logger
            }
            if($reason === '') {
                throw new LogicException('לא ניתן לבטל עסקה שיש ללא הגדרת סיבה');
            }
            $docsInvoiceId = DocsLinkToInvoice::getInvoiceIdFromDoc($docId);
            /** @var Docs $DocInvoice */
            $DocInvoice = Docs::find($docsInvoiceId);
            if($DocInvoice === null) {
                throw new ErrorException('sendDoc docs id not valid');
            }
            //canceled all receipts...
            $receiptsIds = DocsLinkToInvoice::getAllDocsLinkToInvoiceIds($docsInvoiceId);
            if(Docs::hasRefundReceiptInDocIdsArray($receiptsIds)) {
                throw new LogicException('לא ניתן לבטל עסקה שיש בה קבלה החזר');
            }
            if($checkOrderId !== 0 ){
                $CancelResponse = self::cancelAllPaymentsWithOutReceipt($checkOrderId);
                if(!$CancelResponse->isSuccess()) {
                    $Response->setError($CancelResponse->getMessage());
                    $Response->setIds($CancelResponse->getIds());
                }
            }
            $Response = DocsService::cancelAllDocumentByInvoice($DocInvoice, $reason);
        } catch (Exception $e) {
            //todo-cart-add-error
            return $Response->returnError($e->getMessage());
        }
        return $Response->getData();
    }

    /**
     * @param int $loginOrderId
     * @return BaseResponse
     */
    public static function cancelPaymentWithOutReceipt(int $loginOrderId): BaseResponse
    {
        $Response = new BaseResponse();
        try {
            $companyNum = self::checkAuth($Response);
            if ($companyNum === 0 || $loginOrderId === 0){
                throw new ErrorException('שגיאת הרשאות');// todo logger
            }

            /** @var OrderLogin $Order */
            $Order = OrderLogin::find($loginOrderId);
            if($Order === null || (int)$Order->CompanyNum !== $companyNum || $Order->CheckoutOrderId === null) {
                throw new ErrorException('מספר עסקה לא מאופשרת לזיכוי');
            }
            $CanceledOrder = $Order->cloneForCanceled();
            if($CanceledOrder === null || $CanceledOrder->id === null || $CanceledOrder->id <= 0) {
                throw new ErrorException('שגיאה בעת יצירת זיכוי');
            }
            $transactionInfo = $CanceledOrder->getTransactionInfo();
            if(empty($transactionInfo)) {
                throw new ErrorException('שגיאה בעת יצירת זיכוי');
            }
            try {
                $paymentResult = PaymentService::makeRefund($CanceledOrder, $transactionInfo);
                $Order->Status = OrderLogin::STATUS_CANCELLED;
                $Order->save();
                $transaction = Transaction::saveTransactionByOrderLogin($CanceledOrder, $paymentResult, Auth::user()->id);
                $CanceledOrder->TransactionId = $transaction->id;
                $CanceledOrder->save();
            } catch (Exception | \Throwable $e) {
                throw new ErrorException($e->getMessage());
            }
            if(OrderLogin::countPaidByCheckOutId($Order->CheckoutOrderId) === 0) {
                if(!CheckoutOrder::updateStatusById($Order->CheckoutOrderId, CheckoutOrder::STATUS_BEFOR_PAYMENT)) {
                    throw new ErrorException('CheckoutOrderId עדכון סטטוס לא תקין - '. $Order->CheckoutOrderId);
                }
            }

        } catch (Exception $e) {
            //todo-cart-add-error
            $Response->setError($e->getMessage());
            return $Response;
        }
        return $Response;
    }

    /**
     * @param Docs $InvoiceDoc
     * @param CheckOutDataResponse $CheckOutDataResponse
     * @throws ErrorException
     */
    protected static function addRefundCheckout(Docs $InvoiceDoc, CheckOutDataResponse $CheckOutDataResponse): void
    {
        $docsArrayId = DocsLinkToInvoice::getAllDocsLinkToInvoiceIds($InvoiceDoc->id);
        foreach ($docsArrayId as $docId) {
            /** @var Docs $Doc */
            $Doc = Docs::find($docId);
            if($Doc === null ||$Doc->CompanyNum !== $InvoiceDoc->CompanyNum ) {
                throw new ErrorException('docs not valid');
            }
            if($Doc->isRefundReceiptDocs()){
                $CheckOutDataResponse->addRefund($Doc);
            } else if($Doc->isRefundInvoiceDocs()){
                $CheckOutDataResponse->addRefund($Doc);
            }
        }
    }

}
