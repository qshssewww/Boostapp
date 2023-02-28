<?php

require_once __DIR__ . '/../LoggerService.php';
require_once __DIR__ . '/DocsService.php';
require_once __DIR__ . '/../../Classes/DocsClientActivities.php';
require_once __DIR__ . '/../../Classes/DocsList.php';
require_once __DIR__ . '/../../Classes/Docs.php';
require_once __DIR__ . '/../../Classes/Settings.php';
require_once __DIR__ . '/../../Classes/DocsTable.php';
require_once __DIR__ . '/../../Classes/Client.php';
require_once __DIR__ . '/../../Classes/ClientActivities.php';
require_once __DIR__ . '/../../Classes/Settings.php';
require_once __DIR__ . '/../../../app/helpers/GroupNumberHelper.php';
require_once __DIR__ . '/../../../app/helpers/PriceHelper.php';
require_once __DIR__ . '/../../../app/controllers/responses/BaseResponse.php';
require_once __DIR__ . '/../../../app/controllers/responses/IdResponse.php';


class InvoiceService
{
    /**
     * save doc in db and create Doc-list
     * @param Docs $Doc
     * @param array $clientActivitiesIds
     * @throws Exception
     * @return Docs
     */
    public static function createInvoiceByActivitiesIds(Docs $Doc, array $clientActivitiesIds = []): Docs
    {
        try {
            $Doc->Refound = Docs::REFUND_STATUS_OFF;
            $Doc->PayStatus = Docs::PAY_STATUS_OPEN;
            if(!$Doc->save()) {
                throw new LogicException('create doc error');
            }
            foreach ($clientActivitiesIds as $clientActivityId) {
                /** @var ClientActivities $ClientActivity */
                $ClientActivity = ClientActivities::find($clientActivityId);
                if ($ClientActivity === null) {
                    throw new LogicException('clientActivity id not valid');
                }
                $docsClientActivityId = DocsClientActivities::saveRelation($Doc->id, (int)$clientActivityId);
                if(!$docsClientActivityId) {
                    throw new LogicException('docsClientActivity id not valid');
                }
                $DocList = new DocsList();
                $DocList->setPropertiesByClientActivity($ClientActivity);
                $DocList->setPropertiesByDoc($Doc);
                if(!$DocList->save()) {
                    throw new LogicException('error in create doc list');
                } //add - checkAndSave
                $ClientActivity->InvoiceId = $Doc->id;
                if(!$ClientActivity->save()) {
                    throw new LogicException('error update client activity invoiceId');
                }
            }
        } catch (\Throwable $e) {
            LoggerService::error($e);
            if (!empty($Doc->id)) {
                $docId = $Doc->id;
                foreach ($clientActivitiesIds as $clientActivityId) {
                    DocsClientActivities::where('docs_id', $docId)->where('client_activity_id', $clientActivityId)->delete();
                }

                $Doc->removeDocsAndDocList();
            }
        }
        return $Doc;
    }

    /**
     * @param Docs $DocInvoice
     * @param float $refundAmount
     * @param string $remarksText
     * @return IdResponse
     */
    public static function cancelInvoice(Docs $DocInvoice, float $refundAmount, $remarksText = ''): IdResponse
    {
        $Response = new IdResponse();
        try {
            if($DocInvoice === null){
                throw new LogicException('DocInvoice not valid');
            }
            $companyNum = $DocInvoice->CompanyNum;
            /** @var Settings $SettingsInfo */
            $SettingsInfo = Settings::getSettings($companyNum);
            if ($SettingsInfo === null) {
                throw new LogicException('Settings id =' . $companyNum . ' not valid');
            }
            $Client = new Client($DocInvoice->ClientId);
            if($Client === null) {
                throw new LogicException('client not valid');
            }
            $typeHeader = DocsService::fromBusinessTypeAndDocumentTypeToDocTypeHeader(
                DocsService::DOCUMENT_TYPE_REFUND_INVOICE, (int)$SettingsInfo->BusinessType);
            $docDetailsArray = [
                'Amount' => -1 * abs($refundAmount),
                'BalanceAmount' => 0,
                'PayStatus' => Docs::PAY_STATUS_CLOSE,
                'Refound'=> Docs::REFUND_STATUS_ON
            ];
            !empty($remarksText) ? $docDetailsArray['Remarks'] = $remarksText : null;
            $Doc = DocsService::createBaseDoc($typeHeader, $Client, $SettingsInfo, $docDetailsArray);
            $Doc->ActivityJson = DocsService::getActivityJson($DocInvoice, 0, $Doc->Refound);

            $Doc->save();
            if ($Doc->id === 0) {
                throw new LogicException('create doc error');
            }
            $docsLinkToInvoiceId = DocsLinkToInvoice::createDocsLinkToInvoice($DocInvoice->id, $Doc->id);
            if ($docsLinkToInvoiceId === 0) {
                throw new LogicException('create DocsLinkToInvoice error');
            }
            $DocList = new DocsList();
            $DocList->setPropertiesRefund($DocInvoice, $refundAmount);
            $DocList->setPropertiesByDoc($Doc);
            if(!$DocList->save()) {
                throw new LogicException('error in create doc list');
            }
        } catch (\Throwable $e) {
            LoggerService::error($e->getMessage(), LoggerService::CATEGORY_PAYMENT_PROCESS_REFUND);
            if(!empty($Doc) && $Doc->id !== 0) {
                $Response->setError('הופקה חשבונית זיכוי/ חשבונית עסקה אך אחת הפעולות נכשלה!');
                $Response->setId($Doc->id ?? 0);
            } else {
                $Response->setError('התגלתה שגיאה ולכן לא הופקה חשבונית זיכוי. חשבונית עיסקה');
            }
            return $Response;
        }
        $Response->setId(($Doc->id ?? 0));
        return $Response;
    }

}