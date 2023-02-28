<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../init.php';
require_once __DIR__ . '/../helpers/PhoneHelper.php';
require_once __DIR__ . '/../../office/Classes/Brand.php';
require_once __DIR__ . '/../../office/Classes/ClassStudioAct.php';
require_once __DIR__ . '/../../office/Classes/DocsLinkToInvoice.php';
require_once __DIR__ . '/../../office/Classes/ClassStudioDate.php';
require_once __DIR__ . '/../../office/Classes/Client.php';
require_once __DIR__ . '/../../office/Classes/Docs.php';
require_once __DIR__ . '/../../office/Classes/ClientActivities.php';
require_once __DIR__ . '/../../office/Classes/Settings.php';
require_once __DIR__ . '/../../office/Classes/Company.php';
require_once __DIR__ . '/../../office/Classes/DocsClientActivities.php';
require_once __DIR__ . '/../../office/Classes/MeetingCancellationPolicy.php';
require_once __DIR__ . '/../../office/Classes/MeetingClient.php';
require_once __DIR__ . '/../../office/Classes/Section.php';
require_once __DIR__ . '/../../office/Classes/Token.php';
require_once __DIR__ . '/../../office/Classes/Users.php';
require_once __DIR__ . '/../../office/Classes/Utils.php';
require_once __DIR__ . '/../../office/Classes/ClassStudioDateRegular.php';
require_once __DIR__ . '/../../office/services/DocumentService.php';
require_once __DIR__ . '/../../office/services/LoggerService.php';
require_once __DIR__ . '/../../office/services/meetings/EditMeetingService.php';
require_once __DIR__ . '/../../office/services/meetings/MeetingPayment.php';
require_once __DIR__ . '/../../office/services/meetings/MeetingService.php';
require_once __DIR__ . '/../../office/services/receipt/DocsService.php';
require_once __DIR__ . '/../enums/ClassStudioDate/MeetingStatus.php';

class MeetingDetailsController extends BaseController
{
    /**
     * @param int $id
     * @return bool
     */
    public function getMeetingData(int $id): bool
    {
        try {
            $CompanyNum = Auth::user()->CompanyNum;

            $data = DB::table('classstudio_date as sd')
                ->where('sd.CompanyNum', $CompanyNum)
                ->where('sd.id', $id)
                ->select(
                    'sd.id', 'sd.meetingTemplateId as Type', 'sd.ClassName as title',
                    'sd.ClassName as full_title', 'sd.color as backgroundColor',
                    'sd.start_date as start', 'sd.end_date as end', 'ClassNameType as ClassTypeId',
                    'sd.MeetingStatus as status', 'sd.GuideId as ownerId', 'purchaseAmount as price_total',
                    'sd.ClassType as repeat_type', 'sd.Floor', 'sd.GroupNumber',
                    'sd.ClassRepeat as period_value', 'sd.ClassRepeatType as period_unit'
                )->first();

            if (!empty($data)) {

                /** @var Users $user */
                $user = Users::where('id', $data->ownerId)->first();
                $data->owner = $user->display_name;

                /** @var Section $section */
                $section = Section::where('id', $data->Floor)->first();
                $data->locationId = $section->Brands;
                $data->calendar_name = $section->Title;

                /** @var Brand|null $brand */
                $brand = Brand::where('id', $data->locationId)->first();
                if ($brand) {
                    $data->location = $brand->BrandName;
                }

                if ((int)$data->repeat_type == ClassStudioDate::CLASS_TYPE_EXPIRATION) {
                    $data->regularEndDate = (ClassStudioDate::getLastClass($data->GroupNumber, $CompanyNum))->StartDate;
                }

                $studioAct = ClassStudioAct::getMeetingActByClassId($id);
                if ($studioAct) {
                    $data->customer['id'] = $studioAct->FixClientId;
                    $data->customer['classActInfo'] = $studioAct->id;
                    $data->clientActivityId = $studioAct->ClientActivitiesId;

                    /** @var ClientActivities $clientActivity */
                    $clientActivity = ClientActivities::find($data->clientActivityId);

                    /** @var Client $client */
                    $client = Client::find($data->customer['id']);
                    $data->customer['name'] = $client->CompanyName;
                    // try to convert phone number to +972531234567 instead of 0531234567. If phone number is not valid - return as is
                    $data->customer['phone'] = PhoneHelper::processPhone($client->ContactMobile) ?? $client->ContactMobile;
                    $data->customer['avatar'] = Client::getAvatar($data->customer['id'], $CompanyNum);

                    if (!$client->isRandomClient) {
                        $meetingInfo = MeetingClient::where('GroupNumber', $data->GroupNumber)->first();
                        if ($meetingInfo && $meetingInfo->token()) {
                            $data->customer['token'] = [
                                'id' => $meetingInfo->TokenId,
                                'payment_url' => 'https://app.boostapp.co.il/payment/some-url-here',
                            ];
                        } elseif (!empty($client->tokens())) {
                            $lastToken = array_values($client->tokens())[0];

                            $data->customer['token'] = [
                                'id' => $lastToken->id,
                                'payment_url' => 'https://app.boostapp.co.il/payment/some-url-here',
                            ];
                        }
                        // icons part
                        MeetingService::getCustomerIcons($id, $data->customer);

                    } else {
                        $data->customer['is_random'] = true;
                    }

                    $docsList = [];
                    $docs = [];
                    //todo-bp-909 (cart) remove-beta - זה יהיה תמיד נכון ויכנס לפה!
                    $betaCode = Settings::getBetaCode($CompanyNum);
                    if(in_array($betaCode, [1])) {
                        if($clientActivity->InvoiceId) {
                            $docs[] = Docs::find($clientActivity->InvoiceId);
                            $linkDocsId = DocsLinkToInvoice::getAllDocsLinkToInvoiceIds($clientActivity->InvoiceId);
                            foreach ($linkDocsId as $linkDocId) {
                                /** @var Docs $linkDoc */
                                $linkDoc = Docs::find($linkDocId);
                                $docs[] = $linkDoc;
                            }
                        }
                    } else {  //todo-bp-909 (cart) remove this
                        $docs = DocsClientActivities::getDocs($data->clientActivityId);
                    }
                    foreach ($docs as $doc) {
                        if ($doc->docsPayment()) {
                            $docsPayment = $doc->docsPayment()->toArray();
                            $docsPayment['TypeTitleSingle'] = $doc->docsPayment()->docsTable()->TypeTitleSingle;
                            $docsList[] = $docsPayment;
                        } else {
                            //if doc without docpayment (Invoice)
                            $docsList[] = [
                                'id' => $doc->id,
                                'Refound' => $doc->Refound,
                                'TypeDoc' => $doc->TypeDoc,
                                'TypeNumber' => $doc->TypeNumber,
                                'TypeHeader' => $doc->TypeHeader,
                                'Amount' => $clientActivity->BalanceMoney ?? 0,
                                'TypeTitleSingle' => $doc->getTypeDocName(),
                                'Dates' => $doc->Dates,
                            ];
                        }
                    }

                    $data->docs = !empty($docsList) ? $docsList : null;

                    if ($clientActivity) {
                        $data->debt = $clientActivity->BalanceMoney;
                        if (!$clientActivity->isPaymentForSingleClass) {
                            // no debt
                            $data->debt = 0;
                            // price from template

                            $data->client_activities = [
                                "id" => $clientActivity->id,
                                "entries" => $clientActivity->TrueBalanceValue,
                                "MemberShip" => $clientActivity->ItemId,
                                "Status" => $clientActivity->Status,
                                "StartDate" => $clientActivity->StartDate,
                                "TrueDate" => $clientActivity->TrueDate,
                                "ItemText" => $clientActivity->ItemText,
                                "ItemPrice" => $clientActivity->ItemPrice,
                                "BalanceMoney" => $clientActivity->BalanceMoney,
                            ];
                        }
                    }

                    if ($data->status != MeetingStatus::COMPLETED && $client->getMatchingActivity($data->ClassTypeId)) {
                        $relatedActivityIds = $client->getMatchingActivitiesList($data->ClassTypeId);
                        foreach ($relatedActivityIds as $relatedActivityId) {
                            $relatedClientActivity = ClientActivities::find($relatedActivityId);
                            $restriction = $relatedClientActivity->checkMembershipLimitations($id, $client->id, true);

                            $data->customer['MemberShipText']['data'][] = [
                                'Id' => $relatedClientActivity->id,
                                'ItemText' => $relatedClientActivity->ItemText,
                                'balance' => in_array($relatedClientActivity->Department, [2, 3]) ? $relatedClientActivity->TrueBalanceValue : '',
                                'dateEnd' => date("d/m/y", strtotime($relatedClientActivity->TrueDate)),
                                'restriction' => $restriction,
                            ];
                        }
                    }

                    /** @var MeetingCancellationPolicy $policy */
                    $policy = MeetingCancellationPolicy::find($studioAct->MeetingCancellationPolicy); // find cancellation policy from classStudioAct
                    if (!$policy) { // if not found cancellation policy from classStudioAct, find for this Client
                        $policy = MeetingCancellationPolicy::getPolicyForClient($client);
                    }
                    if ($client->isRandomClient) {
                        $policy = null;
                    }
                    if ($policy) {
                        if ((int)$policy->AfterPurchaseChargeStatus !== 0) {
                            $data->show_cancellation_policy = true;
                            $data->cancellation_share = ((int)$policy->AfterPurchaseChargeStatus === 2) ? 100.00
                                : $policy->AfterPurchaseChargeAmount;
                        }
                        if ((int)$policy->ManualChargeStatus !== 0) {
                            $lateCancelTime =
                                Utils::addInterval($data->start, $policy->getManualChargeInterval(), 'Y-m-d H:i:s');
                            if ($lateCancelTime <= date('Y-m-d H:i:s')) {
                                $data->show_cancellation_policy = true;
                                $data->cancellation_share = ((int)$policy->ManualChargeStatus === 2) ? 100.00
                                    : $policy->ManualChargeAmount;
                            }
                        }
                        if ((int)$policy->NotArriveChargeStatus !== 0) {
                            $data->show_not_arrived_policy = true;
                            $data->not_arrived_share = ((int)$policy->NotArriveChargeStatus === 2) ? 100.00
                                : $policy->NotArriveChargeAmount;
                        } elseif (isset($data->show_cancellation_policy) && $data->show_cancellation_policy) {
                            $data->show_not_arrived_policy = true;
                            $data->not_arrived_share = $data->cancellation_share;
                        }
                    }
                }

                $branchId = $brand->id ?? 0;
                if (Section::countActiveByBranch($CompanyNum, $branchId) <= 1) {
                    unset($data->calendar_name);
                }
                if (Users::countActiveCoaches($CompanyNum) <= 1) {
                    unset($data->owner);
                }
                if (isset($data->location) && Brand::countActive($CompanyNum) <= 1) {
                    unset($data->location);
                }
                //todo-bp-909 (cart) remove-beta
                $CompanySettingsDash = DB::table('settings')->where('CompanyNum', '=', Auth::user()->CompanyNum)->first();
                if (isset($section) && in_array($CompanySettingsDash->beta, [1])) {
                    $data->isBeta = true;
                } else {
                    $data->isBeta = false;
                }
            }
            return $this->json($data);
        } catch (\Throwable $e) {
            LoggerService::error($e);
            throw $e;
        }
    }

    /**
     * @param int $id ClassStudioDate id
     * @param int $status MeetingStatus id
     * @param int $oldStatus old MeetingStatus id
     * @param int|null $cancelShare
     * @return bool
     */
    public function changeStatus(int $id, int $status, int $oldStatus, int $cancelShare = null): bool
    {
        if ($status === $oldStatus) {
            $result = ['Status' => 'Error', 'message' => 'Status is the same'];
            return $this->json($result);
        }

        if (EditMeetingService::changeStatus($id, $oldStatus, $status, $cancelShare)) {
            $result = ['Status' => 'OK', 'message' => 'Status changed', 'success' => true];
        } else {
            $result = ['Status' => 'Error', 'message' => 'Status not changed'];
        }
        return $this->json($result);
    }

    /**
     * @param string $ids
     * @return bool
     * @throws Throwable
     */
    public function approveMeeting(string $ids)
    {
        $result = EditMeetingService::approveMeeting($ids);

        return $this->json($result);
    }

    /**
     * @param $id
     * @param $blockCustomerFromOrdering
     * @return bool
     * @throws Throwable
     */
    public function rejectMeeting($id, $blockCustomerFromOrdering = false)
    {
        if ($blockCustomerFromOrdering) {
            $ClassAct = ClassStudioAct::getMeetingActByClassId($id);

            $CompanyNum = Auth::user()->CompanyNum;

            // make list of ids
            $ids = MeetingService::getWaitingMeetingIdsByTypeAndClient($CompanyNum, $ClassAct->ClientId);
            $res = [];

            // reject all client meetings and send back the list
            foreach ($ids as $meeting) {
                EditMeetingService::cancelMeeting($meeting->id, CancelReason::NO_REASON, 'no_charge', 'single', null, true);
                $res[] = $meeting->id;
            }

            // block user from app
            $client = StudioBoostappLogin::where('ClientId', $ClassAct->ClientId)
                ->where('CompanyNum', $ClassAct->CompanyNum)
                ->first();
            $client->StatusBadPoint = 1;
            $client->save();

            $result = [
                'ids' => $res,
                'status' => 'success',
                'message' => 'Meeting canceled',
                'success' => true
            ];
        } else {
            $result = EditMeetingService::cancelMeeting($id, CancelReason::NO_REASON, 'no_charge', 'single', null, true);
        }

        return $this->json($result);
    }

    /**
     * @return bool
     * @throws Throwable
     */
    public function createPayment(int $id)
    {
        try {
            $result = MeetingPayment::payByMeetingId($id);
        } catch (\Throwable $e) {
            $result = ['status' => 'error', 'message' => $e->getMessage()];
        }
        return $this->json($result);
    }

    /**
     * @param int $id
     * @param null $meetingCancelReason
     * @return bool
     * @throws Throwable
     */
    public function cancelDocuments(int $id, $meetingCancelReason = null)
    {
        $meeting = ClassStudioAct::getMeetingActByClassId($id);

        if (!$meeting) {
            throw new LogicException('Meeting not found');
        }

        if (!in_array($meeting->classStudioDate()->meetingStatus,[MeetingStatus::COMPLETED, MeetingStatus::DIDNT_ATTEND])) {
            throw new LogicException('Meeting is not completed');
        }

        $clientActivity = $meeting->clientActivity();
        if (!$clientActivity) {
            throw new LogicException('Client Activity not found');
        }

        try{
            //todo-bp-909 (cart) remove-beta - remove this
            $betaCode = Settings::getBetaCode($meeting->CompanyNum);
            if(in_array($betaCode, [1])) {
                /** @var Docs $Invoice */
                $Invoice = Docs::find($clientActivity->InvoiceId);
                if ($Invoice === null) {
                    throw new LogicException('Error while canceling document #' . $clientActivity->InvoiceId ?? 0);
                }
                $Response = DocsService::cancelAllDocumentByInvoice($Invoice, $meetingCancelReason ?? '');
                if (!$Response->isSuccess()) {
                    $result = [
                        'success' => false,
                        'status'=> 'error',
                        'showMessage' => $Response->getMessage(),
                    ];
                    return $this->json($result);
                }
            } else {
                $documents = DocsClientActivities::getDocs($clientActivity->id);
                foreach ($documents as $document) {
                    // skip already refunded docs and actual refunds
                    if ($document->Refound == 1) continue;
                    // try to cancel document
                    DocumentService::cancelDocument($document->id);

                    // check if document is refunded
                    $notRefunded = DocsPayment::where('RefAction', '=', '0')
                        ->where('DocsId', '=', $document->id)
                        ->where('CompanyNum', '=', $document->CompanyNum)
                        ->exists();

                    if ($notRefunded) {
                        throw new LogicException('Error while canceling document #' . $document->id);
                    }
                }
                if ($meetingCancelReason) {
                    $clientActivity->Reason = $meetingCancelReason;
                    $clientActivity->save();
                }
                EditMeetingService::changeStatus($id, (int)$meeting->classStudioDate()->meetingStatus, MeetingStatus::ORDERED, null);
            }

            $result = [
                'success' => true,
            ];
        } catch (\Throwable $e) {
            LoggerService::error($e);
            throw $e;
        }

        return $this->json($result);
    }

    /**
     * @param $id
     * @return bool
     * @throws Throwable
     */
    public function completeMeetingLeaveInDebt($id) : bool
    {
        $meeting = ClassStudioAct::getMeetingActByClassId($id);

        if (!$meeting) {
            throw new LogicException('Meeting not found');
        }

        try {
            if ((int)$meeting->classStudioDate()->meetingStatus === MeetingStatus::COMPLETED) {
                return $this->json(['success' => true]);
            }
            $Client = $meeting->client() ?? null;

            if($Client) {
                $activity = $meeting->clientActivity();
                //todo-bp-909 (cart) remove-beta - remove this
                $betaCode = Settings::getBetaCode($meeting->CompanyNum);
                if(in_array($betaCode, [1]) && $activity) {
                    //add receipt to this meeting!
                    $Doc = DocsService::createDocByClientActivities(DocsService::DOCUMENT_TYPE_INVOICE, $Client, [$activity->id],
                        ['Amount' => $activity->ItemPrice],[],0,true);
                    $activity->isDisplayed = ClientActivities::DISPLAYED_ON;
                    $activity->save();
                } else {
                    if($activity) {
                        $activity->isDisplayed = ClientActivities::DISPLAYED_ON;
                        $activity->save();
                    }
                    $Client->updateBalanceAmount();
                }
            }


            $statusChanged = EditMeetingService::changeStatus($id, $meeting->classStudioDate()->meetingStatus, MeetingStatus::COMPLETED, null);

            if ($statusChanged) {
                return $this->json(['success' => true]);
            }
        } catch (\Throwable $e) {
            LoggerService::error($e);
            throw $e;
        }

        throw new LogicException('Status not changed');
    }

    /**
     * @param $id
     * @param $registerSubscriptionId
     * @return bool
     * @throws Throwable
     */
    public  function registerSubscription($id, $registerSubscriptionId)
    {
        $meeting = ClassStudioAct::getMeetingActByClassId($id);
        if (!$meeting) {
            throw new LogicException('Meeting not found');
        }

        /** @var ClientActivities|null $registeredClientActivity */
        $registeredClientActivity = ClientActivities::find($registerSubscriptionId);
        if (!$registeredClientActivity || $registeredClientActivity->CompanyNum != $meeting->CompanyNum) {
            throw new LogicException('Client Activity not found');
        }

        try {
            self::registerSubscriptionToMeeting($meeting, $registeredClientActivity);

            EditMeetingService::changeStatus($id, $meeting->classStudioDate()->meetingStatus, MeetingStatus::COMPLETED);

        } catch (\Throwable $e) {
            LoggerService::error($e);
            throw $e;
        }

        return $this->json(['status' => 'success', 'success' => true]);
    }

    /**
     * @param $id
     * @param $subscriptionId
     * @return bool
     * @throws Throwable
     */
    public function unsubscribeFromSubscription($id)
    {
        return $this->json(EditMeetingService::unsubscribeFromSubscription($id));
    }

    /**
     * @param $id - ClassStudioDateId
     * @param int $meetingCancelReason
     * @param string $cancellationPolicy
     * @param string $repeatType
     * @param null $repeatVal
     * @param null $chargedSubscriptionId
     * @param int $actStatus 0->back to order , 1-> cancel, 2-> not arrived
     * @return bool
     * @throws Throwable
     */
    public function cancelMeeting($id, $meetingCancelReason = 0, $cancellationPolicy = 'no_charge', $repeatType = 'single', $repeatVal = null, $chargedSubscriptionId = null, int $actStatus = 0)
    {
        try {
            /** @var ClassStudioAct $meetingAct */
            $ClassStudioAct = ClassStudioAct::getMeetingActByClassId($id);
            if(!$ClassStudioAct){
                throw new Exception('not found ClassStudioAct of classStudioDateId -  ' . $id);
            }
            $Client = $ClassStudioAct->client();
            if(!$Client){
                throw new Exception('not found Client of classStudioActId -  ' . $ClassStudioAct->id);
            }
            $ClassStudioDates = ClassStudioDate::getClassStudioDatesToCancelMeeting($id, $repeatType, $repeatVal);
            if($repeatType === 'single' && count($ClassStudioDates) != 1){
                throw new Exception('count of ClassStudioDate not match to single cancel');
            }
            $existError = false;
            /** @var ClassStudioDate $ClassStudioDate */
            foreach ($ClassStudioDates as $ClassStudioDate) {
                try {
                    $ClassStudioAct = ClassStudioAct::getMeetingActByClassId($ClassStudioDate->id);
                    if (!$ClassStudioAct) {
                        throw new Exception('not found ClassStudioAct for classStudioDateId - ' . $ClassStudioDate->id);
                    }
                    $ClientActivity = $ClassStudioAct->clientActivity();
                    if (!$ClientActivity) {
                        throw new Exception('not found clientActivity of classStudioActId -  ' . $ClassStudioAct->id);
                    }

                        if ($repeatType != 'single') { // if $repeatType no single or isRandomClient, $ClientActivity need isPaymentForSingleClass and cancel without cancellation policy
                            if ((int)$ClassStudioDate->meetingStatus === MeetingStatus::COMPLETED) {
                                continue;
                            }
                            if (!$ClientActivity->isPaymentForSingleClass) {
                                throw new Exception('clientActivity not valid (!isPaymentForSingleClass) to cancel meeting in repeat. clientActivityId - ' . $ClientActivity->id);
                            }
                            self::updateClientActivityAndActWithoutCancellationPolicy($ClientActivity, $ClassStudioAct, $Client);
                        } else {
                            if (is_numeric($cancellationPolicy) && !$Client->isRandomClient) {
                                if($chargedSubscriptionId && is_numeric($chargedSubscriptionId)) {
                                    /** @var ClientActivities $registeredClientActivity */
                                    $registeredClientActivity = ClientActivities::find($chargedSubscriptionId);
                                    if (!$registeredClientActivity || $registeredClientActivity->CompanyNum != $ClassStudioAct->CompanyNum) {
                                        throw new LogicException('Client Activity not found');
                                    }
                                    self::registerSubscriptionToMeeting($ClassStudioAct, $registeredClientActivity);
                                    $ClientActivity = $registeredClientActivity;
                                } else {
                                    //todo-bp-909 (cart) remove-beta - זה יהיה תמיד נכון ויכנס לפה!
                                    $betaCode = Settings::getBetaCode($ClientActivity->CompanyNum);
                                    if($chargedSubscriptionId !== 'cancellation_and_charge_cart' &&  in_array($betaCode, [1])) {
                                        //add receipt to this clientActivity
                                        $Doc = DocsService::createDocByClientActivities(DocsService::DOCUMENT_TYPE_INVOICE, $Client, [$ClientActivity->id],
                                            ['Amount' => $ClientActivity->ItemPrice], [], 0, true);
                                    }
                                }
                                $errorInChargedClient = self::updateClientActivityAndActWithCancellationPolicy($ClientActivity, $ClassStudioAct, $Client, $cancellationPolicy, $chargedSubscriptionId == 'saved_card');
                            } else {
                                self::updateClientActivityAndActWithoutCancellationPolicy($ClientActivity, $ClassStudioAct, $Client);
                            }
                        }
                        switch ($actStatus) {
                            case 0:
                                if(isset($Doc) || ($chargedSubscriptionId && is_numeric($chargedSubscriptionId))) {
                                    $ClassStudioDate->meetingStatus = MeetingStatus::COMPLETED;
                                    $ClassStudioDate->save();
                                } else {
                                        $ClassStudioDate->setStatusToCanceledMeeting($meetingCancelReason);
                                }
                                break;
                            case 1:
                                $ClassStudioDate->setStatusToCanceledMeeting($meetingCancelReason, false);
                                break;
                            case 2:
                                if(isset($Doc) || ($chargedSubscriptionId && is_numeric($chargedSubscriptionId))) {
                                    $ClassStudioDate->meetingStatus = MeetingStatus::COMPLETED;
                                    $ClassStudioDate->save();
                                } else {
                                    $ClassStudioDate->setStatusToCanceledMeeting($meetingCancelReason, true);
                                }
                                $ClassStudioAct->Status = ClassStudioAct::STATUS_MEETING_NOT_ARRIVED;
                                $ClassStudioAct->save();
                                break;
                        }
                } catch (Exception $errorInCancelMeting) {
                    $existError = true;
                    LoggerService::error($errorInCancelMeting, LoggerService::CATEGORY_CANCEL_MEETING);
                }
            }
            if ($repeatType != 'single' && !ClassStudioDate::getIsExistMeetingAfterCanceled($id, $repeatType, $repeatVal)) {
                self::updateClassStudioDateRegular($ClassStudioDate, $ClassStudioAct->RegularClassId, $ClassStudioAct->FixClientId);
            }

        } catch (Exception $error){
            if(isset($existError) && $existError){
                return $this->json(['status' => 0, 'message' => 'exist error in cancel one or more meeting']);
            } else {
                return $this->json(['status' => 0, 'message' => $error->getMessage()]);
            }
        }
        return $this->json([
            'status' => 'success',
            'message' => 'Meeting canceled',
            'clientId' => $Client->id ?? 0,
            'clientActivity' => $ClientActivity->id ?? 0,
            'showMessage' => isset($errorInChargedClient) && $errorInChargedClient,
            'success' => true
        ]);
    }


    /**
     * The function charged on meeting, with membership, now payment or leave in debt
     * The function change status to COMPLETED
     * @param $id
     * @param $chargedSubscriptionId
     * @return bool
     * @throws Throwable
     */
    public function chargedOnMeeting($id, $chargedSubscriptionId): bool
    {
        if($chargedSubscriptionId && is_numeric($chargedSubscriptionId)){
            return $this->registerSubscription($id, $chargedSubscriptionId);
        }
        if($chargedSubscriptionId === 'saved_card'){
            return $this->chargedNowOnMeeting($id);
        }
        return $this->completeMeetingLeaveInDebt($id);

    }


    /**
     * @param $id
     * @return bool
     * @throws Throwable
     */
    public function chargedNowOnMeeting($id): bool
    {
        $meeting = ClassStudioAct::getMeetingActByClassId($id);
        if (!$meeting) {
            throw new LogicException('Meeting not found');
        }
        try {
            if ((int)$meeting->classStudioDate()->meetingStatus === MeetingStatus::COMPLETED) {
                return $this->json(['success' => true]);
            }
            $Client = $meeting->client();
            if ($Client) {
                $activity = $meeting->clientActivity();
                if($activity) {
                    if ($activity->isPaymentForSingleClass && $activity->isForMeeting) {
                        $hasClientToken = self::hasClientTokenToPay($meeting, $Client);
                        if ($hasClientToken) {
                            MeetingPayment::payByMeetingAct($meeting);
                            $statusChanged = EditMeetingService::changeStatus($id, $meeting->classStudioDate()->meetingStatus, MeetingStatus::COMPLETED, null);
                        }
                    }
                }
            }
            if (isset($statusChanged) && $statusChanged) {
                return $this->json(['success' => true]);
            }
        } catch (\Throwable $e) {
            LoggerService::error($e);
            throw $e;
        }

        throw new LogicException('Status not changed');
    }

    /**
     * @param ClientActivities $ClientActivity
     * @param ClassStudioAct $ClassStudioAct
     * @param Client $Client
     */
    public function updateClientActivityAndActWithoutCancellationPolicy(ClientActivities $ClientActivity, ClassStudioAct $ClassStudioAct, Client $Client)
    {
        if ($ClientActivity->isPaymentForSingleClass) {
            $ClientActivity->Status = ClientActivities::STATUS_CANCEL;
//                        $ClientActivity->isDisplayed = 1;
            $ClientActivity->BalanceMoney = 0;
            $ClientActivity->CancelStatus = 1;
            $ClientActivity->save();
            $Client->updateBalanceAmount();
        }
        ClientActivities::CancelClassReturnBalance(
            $ClassStudioAct,
            $ClassStudioAct->CompanyNum,
            ClassStudioAct::STATUS_MEETING_CANCELED
        );
        $ClassStudioAct->changeStatus(ClassStudioAct::STATUS_MEETING_CANCELED);
    }


    /**
     * @param ClientActivities $ClientActivity
     * @param ClassStudioAct $ClassStudioAct
     * @param Client $Client
     * @param int $cancellationPolicy
     * @param bool $chargedNow
     * @return bool
     * @throws Throwable
     */
    public function updateClientActivityAndActWithCancellationPolicy(ClientActivities $ClientActivity, ClassStudioAct $ClassStudioAct, Client $Client, int $cancellationPolicy, bool $chargedNow): bool
    {
        $errorInChargedClient = false;
        if ($ClientActivity->isPaymentForSingleClass) {
            $chargeAmount = $ClientActivity->ItemPrice * $cancellationPolicy / 100;
            $ClientActivity->applyCancellationPolicy($chargeAmount);
        } else {
            $clientActivityId = ClientActivities::applyCancellationOnMembership($cancellationPolicy, $ClassStudioAct);
            if ($clientActivityId != $ClientActivity->id) {
                $ClientActivity = ClientActivities::find($clientActivityId);
            }
        }
        if ($chargedNow && $ClientActivity->isPaymentForSingleClass && $ClientActivity->isForMeeting) {
            $hasClientToken = self::hasClientTokenToPay($ClassStudioAct, $Client);
            if ($hasClientToken) {
                try {
                    MeetingPayment::payByMeetingAct($ClassStudioAct);
                } catch (Exception $errorPayment){
                    //אם לא מצליח לחייב מהאשראי השמור על מדיניות ביטול שלא יקריס,
                    //מחזיר הודעה למשתמש
                    LoggerService::error($errorPayment);
                    $errorInChargedClient = true;
                }
            }
        }
        $Client->updateBalanceAmount();
        $ClassStudioAct->changeStatus(ClassStudioAct::STATUS_MEETING_CANCELED);
        return $errorInChargedClient;
    }

    /**
     * @param ClassStudioDate $ClassStudioDate
     * @param $regularId
     * @param $clientId
     * @return void
     * @throws Exception
     */
    public function updateClassStudioDateRegular(ClassStudioDate $ClassStudioDate, $regularId, $clientId)
    {
        $regularClass = (new ClassStudioDateRegular())->GetRegularById($regularId, $ClassStudioDate->CompanyNum, $clientId);
        if (!$regularClass) {
            return;
        }
        $deletedRegular = (new ClassStudioDateRegular())->deleteRegularAssignmentById($regularId, $ClassStudioDate->CompanyNum, $clientId);
        if (!$deletedRegular) {
            throw new Exception(lang('action_not_done'));
        }

        CreateLogMovement(
            lang('log_removed_booking_ajax') . ' ' . $ClassStudioDate->ClassName . ' ' . lang('a_day_ajax') . ' ' . $ClassStudioDate->Day . ' ' . lang('a_hour_ajax') . ' ' . $ClassStudioDate->StartTime,
            $clientId
        );
    }

    /**
     * @param ClassStudioAct $meeting
     * @param ClientActivities $newClientActivity
     * @return void
     */
    private static function registerSubscriptionToMeeting(ClassStudioAct $meeting, ClientActivities $newClientActivity){
        // cancel old subscription
        $oldClientActivity = $meeting->clientActivity();
        if ($oldClientActivity) {
            $oldClientActivity->Status = ClientActivities::STATUS_CANCEL;
            $oldClientActivity->isDisplayed = ClientActivities::DISPLAYED_OFF;
            $oldClientActivity->BalanceMoney = 0;
            $oldClientActivity->CancelStatus = 1;
            $oldClientActivity->save();

            ClientActivities::CancelClassReturnBalance($meeting, $meeting->CompanyNum, ClassStudioAct::STATUS_MEETING_CANCELED);
            $meeting->changeStatus(ClassStudioAct::STATUS_MEETING_CANCELED);
            $meeting->Status = ClassStudioAct::STATUS_MEETING_CANCELED;
        }

        // register new subscription

        $statusJson = $meeting->getTransferStatusJson(ClassStudioAct::STATUS_MEETING_ACTIVE, $newClientActivity->CardNumber);
        $meeting->ClientActivitiesId = $newClientActivity->id;
        ClientActivities::CancelClassReturnBalance($meeting, $meeting->CompanyNum, 1);

        $meeting->Department = $newClientActivity->Department;
        $meeting->MemberShip = $newClientActivity->MemberShip;
        $meeting->StatusJson = $statusJson;
        $meeting->StatusCount = 0;
        $meeting->Status = ClassStudioAct::STATUS_MEETING_ACTIVE;
        $meeting->save();
    }

    /**
     * The function check if exist token specific ti this meeting or if exist token to client with private - 0
     * @param ClassStudioAct $ClassStudioAct
     * @param Client $Client
     * @return bool
     */
    private static function hasClientTokenToPay(ClassStudioAct $ClassStudioAct, Client $Client): bool
    {
        // get linked card (token) with MeetingClient model
        $GroupNumber = $ClassStudioAct->GroupNumber;
        /** @var MeetingClient $meetingInfo */
        $meetingInfo = MeetingClient::where('GroupNumber', $GroupNumber)->first();
        if ($meetingInfo) {
            /** @var Token $token */
            $token = $meetingInfo->token();
            if ($token && $token->Status == Token::STATUS_ACTIVE) {
                return true;
            }
        }
        $clientTokens = $Client->tokens();
        return count($clientTokens) > 0;
    }
}
