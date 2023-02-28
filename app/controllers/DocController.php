<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/responses/BaseResponse.php';
require_once __DIR__ . '/responses/documents/OffsetDocResponse.php';
require_once __DIR__ . '/responses/documents/LinkDocsResponse.php';
require_once __DIR__ . '/../../office/services/receipt/InvoiceService.php';
require_once __DIR__ . '/../../office/services/receipt/DocsService.php';

/**
 * @class DocController
 */
class DocController extends BaseController
{

    /**
     * @param int $docId
     * @return bool
     */
    public function getInvoiceData(int $docId): bool
    {
        $Response = new BaseResponse();
        try {
            if(!Auth::check()) {
                throw new LogicException('Auth error');
            }
            /** @var Docs $DocInvoice */
            $DocInvoice = Docs::find($docId);
            if ($DocInvoice === null || !$DocInvoice->isInvoiceDocs()) {
                throw new LogicException('docId not valid - ' . $docId);
            }
            if(Auth::user()->CompanyNum !== $DocInvoice->CompanyNum && Auth::user()->CompanyNum !== $DocInvoice->TrueCompanyNum) {
                throw new LogicException('CompanyNum not match - ' . Auth::user()->CompanyNum .'&' . $DocInvoice->CompanyNum);
            }
            $Response = new OffsetDocResponse($DocInvoice);
        } catch (\Throwable $e) {
            LoggerService::error($e->getMessage(), LoggerService::CATEGORY_DOCS);
            $Response->setError(lang('action_not_done'));
            return $Response->getData();
        }
        return $Response->getData();
    }

    /**
     * @param int $docId
     * @param float $offsetAmount
     * @param string $reason
     * @return bool
     */
    public function offsettingInvoiceDebt(int $docId, float $offsetAmount, string $reason = ''): bool
    {
        $Response = new BaseResponse();
        //ביטול מסמכים
        if(!Auth::check() || !Auth::userCan(42)) {
            $Response->setError(lang('no_page_persmission'));
            return false;
        }
        try {
            /** @var Docs $DocInvoice */
            $DocInvoice = Docs::find($docId);
            if ($DocInvoice === null || !$DocInvoice->isInvoiceDocs(true)) {
                throw new LogicException('docId not valid - ' . $docId);
            }
            if(Auth::user()->CompanyNum !== $DocInvoice->CompanyNum && Auth::user()->CompanyNum !== $DocInvoice->TrueCompanyNum) {
                throw new LogicException('CompanyNum not match - ' . Auth::user()->CompanyNum .'&' . $DocInvoice->CompanyNum);
            }
            if ($offsetAmount > $DocInvoice->BalanceAmount) {
                throw new LogicException('offsetAmount not valid- ' . $docId);
            }
            $CancelInvoiceResponse = InvoiceService::cancelInvoice($DocInvoice, $offsetAmount, $reason);
            if(!$CancelInvoiceResponse->isSuccess()) {
                throw new LogicException($CancelInvoiceResponse->getMessage(),401);
            }
            DocsService::updateBalanceAmountAndClientActivity($DocInvoice, abs($offsetAmount), [],true);
            //if we cancel the invoice we need check update status meetings
            if((int)$DocInvoice->PayStatus === Docs::PAY_STATUS_CANCELED) {
                DocsService::updateMeetingAfterCancelDoc($DocInvoice->id, $reason);
            }
            return $Response->getData();
        } catch (\Throwable $e) {
            LoggerService::error($e->getMessage(), LoggerService::CATEGORY_CART);
            //The cancelInvoice action start...
            if($e->getCode() === 401) {
                $Response->setError($e->getMessage());
            } else{
                $Response->setError(lang('action_not_done'));
            }
            return $Response->getData();
        }
    }

    /**
     * @param int $docId
     * @param string $reason
     * @return bool
     */
    public function cancelDocumentsByInvoice(int $docId, string $reason = ''): bool
    {
        $Response = new BaseResponse();
        //ביטול מסמכים
        if(!Auth::check() || !Auth::userCan(42)) {
            $Response->setError(lang('no_page_persmission'));
            return $Response->getData();
        }
        try {
            /** @var Docs $DocInvoice */
            $DocInvoice = Docs::find($docId);
            if ($DocInvoice === null || !$DocInvoice->isInvoiceDocs()) {
                throw new LogicException('docId not valid - ' . $docId);
            }
            if(Auth::user()->CompanyNum !== $DocInvoice->CompanyNum && Auth::user()->CompanyNum !== $DocInvoice->TrueCompanyNum) {
                throw new LogicException('CompanyNum not match - ' . Auth::user()->CompanyNum .'&' . $DocInvoice->CompanyNum);
            }
            if(CheckoutOrder::isInvoiceOpen($DocInvoice->id)) {
                throw new LogicException('לחשבונית - ' . $DocInvoice->TypeNumber . ' יש תשלום פתוח יש לסגור אותו לפני ביטול המסמך' , 401);
            }

            $CancelResponse = DocsService::cancelAllDocumentByInvoice($DocInvoice, $reason);
            if(!$CancelResponse->isSuccess()) {
                throw new LogicException($CancelResponse->getMessage(),401);
            }
            return $CancelResponse->getData();
        } catch (\Throwable $e) {
            LoggerService::error($e->getMessage(), LoggerService::CATEGORY_CART);
            //The cancelAllDocumentByInvoice action start...
            if($e->getCode() === 401) {
                $Response->setError($e->getMessage());
            } else{
                $Response->setError(lang('action_not_done'));
            }
            return $Response->getData();
        }
    }

    /**
     * @param int $docId
     * @return bool
     */
    public function getLinkDocs(int $docId): bool
    {
        $Response = new BaseResponse();
        try {
            /** @var Docs $Invoice */
            $Invoice = Docs::find($docId);
            if ($Invoice === null) {
                throw new LogicException('docId not valid - ' . $docId);
            }
            if(!$Invoice->isInvoiceDocs()) {
                $docId = DocsLinkToInvoice::getInvoiceIdFromDoc($docId);
                $Invoice = Docs::find($docId);
                if ($Invoice === null) {
                    throw new LogicException('Invoice Id not valid - ' . $docId);
                }
            }
            $Response = new LinkDocsResponse($Invoice);
            $linkDocsId =DocsLinkToInvoice::getAllDocsLinkToInvoiceIds($docId);
            foreach ($linkDocsId as $linkDocId) {
                /** @var Docs $linkDoc */
                $linkDoc = Docs::find($linkDocId);
                if ($linkDoc === null) {
                    throw new LogicException('linkDocId not valid - ' . $linkDocId);
                }
                //fix link doc show refund receipt on negative amount
                if((int)$linkDoc->TypeHeader === 400) {
                    $linkDoc->Amount *= -1;
                }
                $Response->addLinkDoc($linkDoc);
            }
        } catch (\Throwable $e) {
            LoggerService::error($e->getMessage(), LoggerService::CATEGORY_DOCS);
            $Response->setError(lang('something_wrong_cal'));
            return $Response->getData();
        }
        return $Response->getData();
    }


}
