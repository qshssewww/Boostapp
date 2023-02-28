<?php
require_once __DIR__ . '/../../Classes/Company.php';
require_once __DIR__ . '/../../Classes/BusinessType.php';
require_once __DIR__ . '/../../Classes/ClientActivities.php';
require_once __DIR__ . '/../../Classes/DocsClientActivities.php';
require_once __DIR__ . '/../../Classes/Notificationcontent.php';
require_once __DIR__ . '/../../Classes/TempReceiptPayment.php';
require_once __DIR__ . '/../../Classes/TempReceiptPaymentClient.php';
require_once __DIR__ . '/../../../app/helpers/GroupNumberHelper.php';
require_once __DIR__ . '/../../../app/helpers/PhoneHelper.php';
require_once __DIR__ . '/../../Classes/UserBoostappLogin.php';
require_once __DIR__ . '/../../Classes/Models/TranslationValues.php';
require_once __DIR__ . '/../../Classes/Docs.php';
require_once __DIR__ . '/../../Classes/Settings.php';
require_once __DIR__ . '/../../Classes/Client.php';
require_once __DIR__ . '/../../Classes/DocsTable.php';
require_once __DIR__ . '/../LoggerService.php';
require_once __DIR__ . '/../meetings/EditMeetingService.php';
require_once __DIR__ . '/../ClassStudioActService.php';
require_once __DIR__ . '/InvoiceService.php';
require_once __DIR__ . '/ReceiptService.php';
require_once __DIR__ . '/../../../app/controllers/responses/BaseResponse.php';
require_once __DIR__ . '/../../../app/controllers/responses/IdResponse.php';
require_once __DIR__ . '/../../../app/enums/ClassStudioDate/MeetingStatus.php';

class DocsService
{
    public const DOCUMENT_TYPE_INVOICE = 0;
    public const DOCUMENT_TYPE_RECEPTION = 1;
    public const DOCUMENT_TYPE_RECEIPT_TAX_INVOICE = 2;
    public const DOCUMENT_TYPE_REFUND_INVOICE = 3;
    public const DOCUMENT_TYPE_REFUND_RECEPTION = 4;

    /**
     * @param int $trueCompanyNum
     * @param int $typeHeaderDoc
     * @return int|null null -notFound
     */
    public static function getNextDocsNumber(int $trueCompanyNum, int $typeHeaderDoc): ?int
    {
        //get last doc number
        $typeNumber = Docs::getDocsLastNumber($trueCompanyNum, $typeHeaderDoc);
        if($typeNumber === 0) {
            return null;
        }
        return ++$typeNumber;
    }

    /**
     * @param int $typeHeaderDoc
     * @param Client $Client
     * @param $Settings
     * @param array $docDetailsArray
     * @throws Exception
     * @return Docs
     */
    public static function createBaseDoc(int $typeHeaderDoc, Client $Client, $Settings, array $docDetailsArray = []): Docs {
        $currentDate = date('Y-m-d');
        $currentTime = date('Y-m-d H:i:s');
        $trueCompanyNum = Settings::getCompanyNumFromMainBrand($Settings);
        $DocsTable = DocsTable::getByTrueCompanyNumAndTypeHeader($trueCompanyNum, $typeHeaderDoc);
        if($DocsTable === null) {
            throw new LogicException('docstable status error');
        }
        $Doc = new Docs();
        $Doc->ManualInvoice = 0; /// 1 is ManualInvoice
        $Doc->setPropertiesBySettings($Settings);
        $Doc->setPropertiesByClient($Client);
        $Doc->setPropertiesByDocsTable($DocsTable);
        $Doc->setPropertiesOfDateDoc($currentDate);
        $Doc->TrueCompanyNum = $trueCompanyNum;
        $Doc->TextId = 1;//new doc-> //todo change
        $Doc->UserDate = $currentDate;
        $Doc->PaymentRole = 1;// default
        $Doc->PaymentTime = $currentDate;
        $Doc->Dates = $currentTime;
        $Doc->UserId = Auth::user()->id ?? 0;
        // todo: override cpaType for cart only!!
        $Doc->CpaType = Docs::CPA_TYPE_DUE_CREATE_RECEIPT;
        $Doc->TypeNumber = self::getNextDocsNumber($trueCompanyNum, $typeHeaderDoc) ?? $DocsTable->TypeNumber;
        $Doc->setPropertiesOfDocDetailsArray($docDetailsArray);
        return $Doc;
    }

    /**
     * @param int $documentType
     * @param int $businessType
     * @return int
     */
    public static function fromBusinessTypeAndDocumentTypeToDocTypeHeader(int $documentType, int $businessType): int
    {
        switch ($documentType) {
            case self::DOCUMENT_TYPE_INVOICE:
                if(in_array((int)$businessType, BusinessType::BUSINESS_TYPE_WITH_OUT_VAT)){
                    return DocsTable::TYPE_HESHBONIT_HESHKA;
                }
                return DocsTable::TYPE_HESHBONIT_MAS;
            case self::DOCUMENT_TYPE_RECEPTION:
                return DocsTable::TYPE_KABALA;
            case self::DOCUMENT_TYPE_RECEIPT_TAX_INVOICE:
                if(in_array((int)$businessType, BusinessType::BUSINESS_TYPE_WITH_OUT_VAT)){
                    return DocsTable::TYPE_HESHBONIT_HESHKA;
                }
                return DocsTable::TYPE_HESHBONIT_MAS_KABLA;
            case self::DOCUMENT_TYPE_REFUND_INVOICE:
                if(in_array((int)$businessType, BusinessType::BUSINESS_TYPE_WITH_OUT_VAT)){
                    return DocsTable::TYPE_HESHBONIT_HESHKA;
                }
                return DocsTable::TYPE_HESHBONIT_MAS_ZIKUI;
            default:
                return 3;//todo
            }
    }

    /**
     * return $activityJson and update $DocInvoice status pay
     * @param Docs $DocInvoice
     * @param $totalAmount
     * @param int $refundStatus
     * @return string
     */
    public static function getActivityJson(Docs $DocInvoice, $totalAmount , int $refundStatus = Docs::REFUND_STATUS_OFF): string
    {
        $isRefund = $refundStatus !== Docs::REFUND_STATUS_OFF;
        /// קליטת חשבוניות שנבחרו
        $activityJson = '{"data": [';
        //// בדיקת תקבול לחשבונית
        if ($totalAmount >= $DocInvoice->BalanceAmount) {
            $activityJson .= '{"ItemText": "' . $DocInvoice->getActivityJsonInvoiceText($isRefund) . '", "ItemId": "' . $DocInvoice->TypeNumber . '", "OldBalanceMoney": "' . $DocInvoice->BalanceAmount . '", "NewAmount": "0"}';
        } //// תשלום חלקי
        else {
            $TotalAmountInfo = $DocInvoice->BalanceAmount - $totalAmount;
            $activityJson .= '{"ItemText": "' . $DocInvoice->getActivityJsonInvoiceText($isRefund) . '", "ItemId": "' . $DocInvoice->TypeNumber . '", "OldBalanceMoney": "' . $DocInvoice->BalanceAmount . '", "NewAmount": "' . $TotalAmountInfo . '"}'; //todo
        }
        $activityJson .= ']}';
        return $activityJson;
    }

    /**
     * @param int $documentType
     * @param ?Client $Client
     * @param array $clientActivitiesIds
     * @param array $docDetails
     * @param array $transactionsArrayData
     * @param int $companyNum
     * @param bool $sendDoc
     * @return Docs|null
     */
    public static function createDocByClientActivities(int $documentType = self::DOCUMENT_TYPE_INVOICE,
                                                       Client $Client = null,
                                                       array $clientActivitiesIds = [],
                                                       array $docDetails = [],
                                                       array $transactionsArrayData = [],
                                                       int $companyNum = 0,
                                                       bool $sendDoc=false): ?Docs
    {
        try {
            if ($Client === null) {
                throw new LogicException('Client not valid');
            }
            $companyNum = $companyNum === 0 ? (int)Auth::user()->CompanyNum : $companyNum;
            $Settings = Settings::getByCompanyNum($companyNum);
            if ($Settings === null) {
                throw new LogicException('Settings id =' . $companyNum . ' not valid');
            }
            $typeHeaderDoc = self::fromBusinessTypeAndDocumentTypeToDocTypeHeader($documentType, (int)$Settings->BusinessType);
            $Doc = self::createBaseDoc($typeHeaderDoc, $Client, $Settings, $docDetails);
            switch ($typeHeaderDoc) {
                case DocsTable::TYPE_HESHBONIT_HESHKA:
                case DocsTable::TYPE_HESHBONIT_MAS:
                    $Doc = InvoiceService::createInvoiceByActivitiesIds($Doc, $clientActivitiesIds);
                    break;
                case DocsTable::TYPE_KABALA:
                    //todo create function
                    /** @var Docs $DocInvoice */
                    $DocInvoice = Docs::find($transactionsArrayData['docInvoiceId'] ?? 0);
                    if ($DocInvoice === null) {
                        throw new LogicException('cant create KABALA with out invoiceId');
                    }
                    $Doc = ReceiptService::createReceiptByActivitiesIds($Doc, $clientActivitiesIds, $DocInvoice, $transactionsArrayData);
                    self::updateBalanceAmountAndClientActivity($DocInvoice, abs($Doc->Amount), $clientActivitiesIds);
                    break;
                case DocsTable::TYPE_HESHBONIT_MAS_KABLA:
                    $Doc = InvoiceService::createInvoiceByActivitiesIds($Doc, $clientActivitiesIds);
                    $Doc = ReceiptService::createReceiptByActivitiesIds($Doc, $clientActivitiesIds, null, $transactionsArrayData);
                    self::updateBalanceAmountAndClientActivity($Doc, abs($Doc->Amount), $clientActivitiesIds);
                    break;
                default:
                    return null;
            }
            $Client->updateBalanceAmountNew(); // todo-need-create-new-field
            if ($sendDoc) {
                $isSend = self::sendDoc($Doc, $Client, $Settings); //todo-לבדוק אם תמיד לשלוח
            }
        } catch (\Throwable $e) {
            if (isset($Doc)) {
                $Doc->delete();
            }

            LoggerService::error($e, LoggerService::CATEGORY_DOCS);
            return null;
        }
        return $Doc ?? null;
    }

    /**
     * @param Docs $Docs
     * @param Client $Client
     * @param $Settings
     * @param int|null $sendOptionCode
     * @return int
     */
    public static function sendDoc(Docs $Docs, Client $Client, $Settings, ?int $sendOptionCode = null): int
    {
        ///// שליחת מסמך ללקוח
        $NotificationContent = Notificationcontent::getByTypeAndCompanyNum($Docs->CompanyNum, Notificationcontent::TYPE_SEND_INVOICE_OR_RECEIPT);
        if($NotificationContent === null) {
            return 0;//todo
        }
        $type = $sendOptionCode ?? $NotificationContent->getSendOptionCode($Client, Notificationcontent::SEND_OPTION_PRIORITY_MAIL_SMS_PUSH);
        if($type === null) {
            return 0;//todo-not need to send
        }
        /// עדכון תבנית הודעה
        $DocUrl = $Docs->RandomUrl;
        $trueFullLinks = LinkHelper::getTinyUrl(get_newboostapp_domain() . '/office/PDF/DocsClient.php?RandomUrl=' . $DocUrl . '&ClientId=' . $Docs->ClientId);
        if($type !== AppNotification::TYPE_SMS) {
            $docUrlTrue = '<a href="' . $trueFullLinks . '">' . lang('view_doc_ajax') . '</a>';
        } else {
            $docUrlTrue = lang('view_button') . ' - ' . $trueFullLinks;
        }
        $Content1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], $Settings->AppName, $NotificationContent->Content);
        $Content2 = str_replace(Notificationcontent::REPLACE_ARR["name_table"], $Client->CompanyName, $Content1);
        $Content3 = str_replace(Notificationcontent::REPLACE_ARR["first_name"], $Client->FirstName, $Content2);
        $Content4 = str_replace(Notificationcontent::REPLACE_ARR["doc_number"], $Docs->TypeNumber, $Content3);
        $Content5 = str_replace(Notificationcontent::REPLACE_ARR["doc_type"], $Docs->getTypeDocName(), $Content4);
        $Content6 = str_replace(Notificationcontent::REPLACE_ARR["doc_link"], $docUrlTrue, $Content5);
        $ContentTrue = $Content6;
        $subject1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], $Settings->AppName, $NotificationContent->Subject);
        $subject2 = str_replace(Notificationcontent::REPLACE_ARR["doc_type"], $Docs->getTypeDocName(), $subject1);
        $subjectTrue = $subject2;
        $textNotification = $ContentTrue; /// משיכת הודעת ביטול שיעור מבסיס הנתונים
        $subject = $subjectTrue;

        $AppNotification = new AppNotification([
            'CompanyNum' => $Docs->CompanyNum,
            'ClientId' => $Docs->ClientId,
            'TrueClientId' => '0',
            'Subject' => $subject,
            'Text' => $textNotification,
            'Dates' => date('Y-m-d H:i:s'),
            'UserId' => '0',
            'Type' => $type,
            'Date' => date('Y-m-d'),
            'Time' => date('H:i:s'),
            'ClassId' => '0'
        ]);
        return $AppNotification->save();
    }

    /**
     * @param string $appName
     * @param Client $Client
     * @param Docs|null $Invoice
     * @param Docs|null $Receipt
     * @return bool
     */
    public static function sendCheckoutDocsBySMS(string $appName, Client $Client, ?Docs $Invoice, ?Docs $Receipt = null):bool
    {
        $invoiceUrl = $Invoice ? LinkHelper::getTinyUrl(get_newboostapp_domain() . '/office/PDF/DocsClient.php?RandomUrl=' . $Invoice->RandomUrl . '&ClientId=' . $Invoice->ClientId) : "";
        $receiptUrl = $Receipt ? LinkHelper::getTinyUrl(get_newboostapp_domain() . '/office/PDF/DocsClient.php?RandomUrl=' . $Receipt->RandomUrl . '&ClientId=' . $Receipt->ClientId) : "";

        $message = self::getSharingMessageTemplate($appName, $Client, $Invoice, $Receipt);

        $message = $Invoice ? preg_replace("/INVOICELINK/", $invoiceUrl, $message) : $message;
        $message = $Receipt ? preg_replace("/RECEIPTLINK/", $receiptUrl, $message) : $message;
        $subject = "$appName :: מסמך/י רכישה";

        $clientPhone = PhoneHelper::shortPhoneNumber($Client->ContactMobile);

        $AppNotification = new AppNotification([
            'CompanyNum' => $Client->CompanyNum,
            'ClientId' => $Client->id,
            'TrueClientId' => '0',
            'Subject' => $subject,
            'Text' => $message,
            'Dates' => date('Y-m-d H:i:s'),
            'UserId' => '0',
            'Type' => '1',
            'Date' => date('Y-m-d'),
            'Time' => date('H:i:s'),
            'ClassId' => '0',
            'PhoneNumber' => $clientPhone,
            'priority' => '1'
        ]);
        return $AppNotification->save();
    }

    /**
     * @param string $companyName
     * @param Client $Client
     * @param Docs|null $Invoice
     * @param Docs|null $Receipt
     * @return string
     */
    public static function getSharingMessageTemplate(string $companyName, Client $Client, ?Docs $Invoice, ?Docs $Receipt = null): string{

        $userLang = (new UserBoostappLogin())->findUserByClientIDCompanyNum($Client->id, $Client->CompanyNum);
        $userLang = $userLang->language ?? 'he';

        // check if client is random client
        if((bool)$Client->isRandomClient) {
            $dearClient = TranslationValues::getTranslationValueByLangKeyPair('dear_client', $userLang);
            $clientName = !empty($dearClient) ? $dearClient : $Client->FirstName;
        } else {
            $clientName = $Client->FirstName;
        }

        // composing the share message
        $message = lang('hi_corona_cron')." $clientName, ". lang('attached_hereto')." ";

        $refundOrPurchase = ($Receipt !== null  && $Receipt->Refound) ? lang('for_refund_in') : lang('for_purchase_in');
        $message_forPurchaseAtAndWatch =  " ".($Receipt->TypeNumber ?? "")." ".$refundOrPurchase."$companyName, ".lang('to_watch');

        if($Invoice !== null) {
            if($Invoice->TypeHeader == DocsTable::TYPE_HESHBONIT_MAS_KABLA){
                $message .= lang('doc_invoice_receipt').$message_forPurchaseAtAndWatch.": INVOICELINK ";
            } else if($Receipt === null) {
                $message .= lang('invoice_single').$message_forPurchaseAtAndWatch.": INVOICELINK ";
            } else {
                $message .= lang('docs').$message_forPurchaseAtAndWatch." ".lang('in_invoice').": INVOICELINK, " . lang('to_watch_receipt').": RECEIPTLINK";
            }
        } else {
            $message .= lang('receipt').$message_forPurchaseAtAndWatch.": RECEIPTLINK ";
        }
        return $message;
    }

    /**
     * @param Docs|null $Invoice
     * @param string $reason
     * @return BaseResponse
     */
    public static function cancelAllDocumentByInvoice(Docs $Invoice = null, string $reason = ''): BaseResponse
    {
        $Response = new BaseResponse();
        try {
            if ($Invoice === null) {
                throw new LogicException('Invoice not valid');
            }
            $Settings = Settings::getByCompanyNum($Invoice->CompanyNum);
            if ($Settings === null) {
                throw new LogicException('Settings id =' . $Invoice->CompanyNum . ' not valid');
            }
            if($Invoice->connectedInvoiceIsCanceled()) {
                throw new LogicException('לא ניתן לבטל חשבונית שכבר בוטלה!');
            }

            $amountToRefund = 0;
            $balanceAmount = $Invoice->BalanceAmount;
            //canceled all receipts...
            $receiptsIds = DocsLinkToInvoice::getAllDocsLinkToInvoiceIds($Invoice->id);
            if($Invoice->isReceiptDocs(true)){
                array_unshift($receiptsIds , (int)$Invoice->id);
            }
            if(Docs::hasRefundReceiptInDocIdsArray($receiptsIds)) {
                $Response->setError('לא ניתן לבטל עסקה שיש בה קבלה החזר');///todo: remove?
                throw new LogicException('לא ניתן לבטל עסקה שיש בה קבלה החזר');
            }
            foreach ($receiptsIds as $receiptId) {
                /** @var Docs $DocReceipt */
                $DocReceipt = Docs::find($receiptId);
                if($DocReceipt === null) {
                    throw new ErrorException('DocReceipt docs id not valid');
                }
                if($DocReceipt->isReceiptDocs(true) && (int)$DocReceipt->RefAction === DocsPayment::BEFORE_REF_ACTION ){
                    $ResponseReceipt = ReceiptService::cancelReceipts($DocReceipt, $Invoice, $reason);
                    $refundReceiptId = $ResponseReceipt->getId() ?? 0;
                    /** @var Docs $RefundReceipt */
                    $RefundReceipt = Docs::find($refundReceiptId);
                    if($RefundReceipt !== null) {
                        $amountToRefund += abs($RefundReceipt->Amount ?? 0);
                    }
                    if(!$ResponseReceipt->isSuccess()) {
                        $Response->setMessage($Response->getMessage() . ',התקבלה שגיאה באחד הזיכויים ');
                    } else if(!empty($ResponseReceipt->getMessage())) {
                        $Response->setMessage($Response->getMessage() . ', ' . $ResponseReceipt->getMessage());
                    }
                }
            }
            if($amountToRefund > 0) {
                self::updateBalanceAmountAndClientActivity($Invoice, -1 *abs($amountToRefund));
            }
            $ResponseRefundInvoice = InvoiceService::cancelInvoice($Invoice, $amountToRefund + $balanceAmount, $reason);
            self::updateBalanceAmountAndClientActivity($Invoice, abs($balanceAmount + $amountToRefund), [],true);
            //update meeting status and balance amount
            self::updateMeetingAfterCancelDoc($Invoice->id, $reason);

            $Invoice->PayStatus = Docs::PAY_STATUS_CANCELED;
            $Invoice->save();
            return $Response;
        } catch (\Throwable $e) {
            LoggerService::error($e->getMessage(), LoggerService::CATEGORY_PAYMENT_PROCESS_REFUND);
            return $Response;
        }
    }

    /**
     * @param Docs $Invoice
     * @param float $amount - The amount you want to reduce
     * @param array $clientActivitiesArray
     * @param bool $offsettingInvoiceDebt - כאשר מקזזים סכום מהחשבונית
     */
    public static function updateBalanceAmountAndClientActivity(Docs $Invoice, float $amount, array $clientActivitiesArray =[], bool $offsettingInvoiceDebt = false): void {
        $Invoice->BalanceAmount -= $amount;
        if($offsettingInvoiceDebt && $amount > 0) { //refund invoice
            $Invoice->updateRefundAmount($amount); //רק בקיזוז חוב
        }
        if($amount < 0) { //refund receipt
            $Invoice->RefAction = Docs::AFTER_REF_ACTION;
        }

        if ($Invoice->BalanceAmount <= 0) {
            ClientActivities::updateByInvoiceIdBalanceMoneyTo0($Invoice->ClientId ,$Invoice->id);
            if($offsettingInvoiceDebt && $amount >= $Invoice->Amount) {
                $Invoice->PayStatus = Docs::PAY_STATUS_CANCELED;
            } else {
                $Invoice->PayStatus = Docs::PAY_STATUS_CLOSE;
            }
        } else {
            if($Invoice->Amount <= $Invoice->BalanceAmount) {
                $Invoice->PayStatus = Docs::PAY_STATUS_OPEN;
            } else if($Invoice->BalanceAmount > 0 ){
                $Invoice->PayStatus = Docs::PAY_STATUS_HALF_CLOSE;
            }
            if(empty($clientActivitiesArray)) {
                $clientActivitiesArray = DocsClientActivities::getClientActivitiesIds($Invoice->id);
            }
            ClientActivityService::updateBalanceMoney($amount ,$clientActivitiesArray);
        }
        if($Invoice->refundAmount >= $Invoice->Amount) {
            $Invoice->PayStatus = Docs::PAY_STATUS_CANCELED;
        }
        $Invoice->save(); //update BalanceAmount
    }

    /**
     * @param Docs $DocInvoice
     * @param array $transactionsArrayData
     * @param string $remarksText
     * @param bool $sendDoc
     * @return Docs|null
     */
    public static function createRefundDoc(Docs $DocInvoice , array $transactionsArrayData = [], string $remarksText = '', bool $sendDoc = false): ?Docs
    {
        try {
            /** @var Client $Client */
            $Client = Client::find($DocInvoice->ClientId);
            if($Client === null) {
                throw new ErrorException('client not valid in doc -' . $DocInvoice->id);
            }
            $Settings = Settings::getByCompanyNum($DocInvoice->CompanyNum);
            if ($Settings === null) {
                throw new LogicException('Settings id not valid in doc -' . $DocInvoice->id);
            }

            $typeHeaderDoc = DocsTable::TYPE_KABALA;
            $docDetailsArray = [
                'Amount' => abs($transactionsArrayData['paymentTotalAmount']),
                'BalanceAmount' => 0, //todo
                'PayStatus' => Docs::PAY_STATUS_CLOSE,
                'Refound'=> Docs::REFUND_STATUS_ON,
                'Status'=> 1,
            ];
            !empty($remarksText) ? $docDetailsArray['Remarks'] = $remarksText : null;

            $RefundReceipt = self::createBaseDoc($typeHeaderDoc, $Client, $Settings, $docDetailsArray);
            if(!$RefundReceipt->save()) {
                throw new LogicException('create doc error');
            }
            $RefundReceipt = ReceiptService::createRefundReceipt($RefundReceipt, $DocInvoice,  $transactionsArrayData['paymentData']);
            if($RefundReceipt === null) {
                throw new LogicException('error on create RefundReceipt');
            }
            $amountToRefund = abs($RefundReceipt->Amount ?? 0);
            if($amountToRefund > 0) {
                self::updateBalanceAmountAndClientActivity($DocInvoice, -1 *abs($amountToRefund));
            }
            if ($sendDoc) {
                $isSend = self::sendDoc($RefundReceipt, $Client, $Settings); //todo-לבדוק אם תמיד לשלוח
            }
        } catch (\Throwable $e) {
            if (isset($RefundReceipt)) {
                $RefundReceipt->delete();
            }
            LoggerService::error($e, LoggerService::CATEGORY_DOCS);
            return null;
        }
        return $RefundReceipt ?? null;
    }

    /**
     * @param int $docId
     * @param bool $isSource
     * @return bool
     * @throws ErrorException
     */
    public static function downloadDocPdf(int $docId, bool $isSource = true) :bool
    {
        $companyNum = Auth::user()->CompanyNum;
        /** @var Docs $Doc */
        $Doc = Docs::find($docId);
        if ($Doc === null || (int)$Doc->CompanyNum !== (int)$companyNum) {
            throw new ErrorException('docs not valid');
        }
        if ($isSource && (int)$Doc->Status !== Docs::STATUS_SOURCE) {
            throw new ErrorException('docs is not source');
        }
        //call PDF/Docs for download it to local
        $_GET['DocId'] = $Doc->TypeNumber;
        $_GET['DocType'] = $Doc->TypeDoc;
        $_GET['testSource'] = true;
        include __DIR__ . "/../../PDF/" . 'Docs.php'; //if in the same folder

        $file_name = $Doc->CompanyNum . '_' . $Doc->TypeNumber . '_signed.pdf';
        $dir = __DIR__ . "/../../PDF/mpdf/tmp/";
        $file = $dir . $file_name;

        if (file_exists($file)) {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header("Content-Type: application/force-download");
            header('Content-Disposition: attachment; filename=' . urlencode(basename($file)));
            // header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            return $Doc->updateStatus(Docs::STATUS_COPY);
        }
        throw new ErrorException('pdf file not valid');
    }

    /**
     * update class studio date meeting status and separate meeting from docs
     * @param int $docId
     * @param string $reason
     */
    public static function updateMeetingAfterCancelDoc(int $docId, string $reason = ''): void
    {
        $clientActivitiesArray = DocsClientActivities::getClientActivitiesIds($docId);
        foreach ($clientActivitiesArray as $clientActivityId) {
            /** @var ClientActivities $ClientActivity */
            $ClientActivity = ClientActivities::find($clientActivityId);
            if ($ClientActivity !== null) {
                $separateMeetingFlag = true;
                $ClassStudioActArray = ClassStudioActService::getActByClientActivityId($ClientActivity);
                foreach ($ClassStudioActArray as $ClassStudioAct) {
                    $oldStatus = EditMeetingService::updateStatusMeetingByActId($ClassStudioAct->id, MeetingStatus::ORDERED, true);
                    if($separateMeetingFlag && in_array($oldStatus,[MeetingStatus::COMPLETED, MeetingStatus::DIDNT_ATTEND], true)) { //separate meeting from doc only if completed status
                        $ClientActivity->separateMeetingFromClientActivity($reason);
                        $separateMeetingFlag = false;
                    }
                }
            }
        }
    }

}