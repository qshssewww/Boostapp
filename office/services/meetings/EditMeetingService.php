<?php

require_once __DIR__ . '/../../../app/enums/ClassStudioDate/MeetingStatus.php';
require_once __DIR__ . '/../../Classes/AppNotification.php';
require_once __DIR__ . '/../../Classes/ClassStudioAct.php';
require_once __DIR__ . '/../../Classes/ClassStudioDate.php';
require_once __DIR__ . '/../../Classes/Client.php';
require_once __DIR__ . '/../../Classes/ClientActivities.php';
require_once __DIR__ . '/../../Classes/Notificationcontent.php';
require_once __DIR__ . '/../../services/meetings/MeetingPayment.php';

class EditMeetingService
{
    /**
     * @param $id int ClassStudioDate id
     * @param $statusOld
     * @param $statusNew
     * @param $cancelShare
     * @return bool
     * @throws Throwable
     */
    public static function changeStatus($id, $statusOld, $statusNew, $cancelShare = null): bool
    {
        if ($statusNew === $statusOld) {
            return false;
        }

        $studioAct = ClassStudioAct::getMeetingActByClassId($id);
        if (!$studioAct) {
            throw new LogicException('Meeting not found');
        }
        //if act Already cancelled
        if(!in_array($studioAct->Status, [ClassStudioAct::STATUS_MEETING_ACTIVE, 11])) {
            return true;
        }

        $studioDate = $studioAct->classStudioDate();
        if (!$studioDate) {
            throw new LogicException('Meeting not found');
        }

        $clientActivity = $studioAct->clientActivity();
        $client = $studioAct->client();

        if (!$client->isRandomClient) {
            switch ($statusNew) {
                case MeetingStatus::ORDERED:
                    if ($statusOld === MeetingStatus::COMPLETED) {
                        // remove debt
                        $clientActivity->isDisplayed = 0;
                        $clientActivity->save();
                        $client->updateBalanceAmount();
                    }
                    break;
                case MeetingStatus::DIDNT_ATTEND:
                    if (!$clientActivity) {
                        break;
                    }


                    if (!empty($cancelShare)) {
                        if ($clientActivity->isPaymentForSingleClass) {
                            $cancelPrice = $clientActivity->ItemPrice * $cancelShare / 100;
                            $clientActivity->applyCancellationPolicy($cancelPrice);
                            $clientTokens = $client->tokens();
                            if (count($clientTokens) > 0) {
                                MeetingPayment::payByMeetingAct($studioAct);
                            } else {
                                $client->updateBalanceAmount();
                            }
                        }

                    } else {
                        $studioAct->setNotAttendAndCharged();
                        if ((int) $clientActivity->isPaymentForSingleClass === 1) {
                            $clientActivity->didntAttendCancelActivity();
                        }
                    }
                    break;
                case MeetingStatus::COMPLETED:
                    if($client->Status != Client::STATUS_ACTIVE && in_array($clientActivity->Department, [1, 2])) {
                        $isFirstActivityClientActivity = ClientActivities::isFirstActivityClientForClient($clientActivity->id, $client->id, $client->CompanyNum);
                        if ($isFirstActivityClientActivity) { // if this first clientActivity active, change status to active
                            $client->JoinDate = date('Y-m-d');
                            $client->Status = Client::STATUS_ACTIVE;
                            $client->save();
                        }
                    }
                    break;
            }
        }

        $studioDate->meetingStatus = $statusNew;
        $studioDate->save();
        return true;
    }

    /**
     * @param $id
     * @return array
     * @throws Throwable
     */
    public static function unsubscribeFromSubscription($id)
    {
        $classStudioAct = ClassStudioAct::getMeetingActByClassId($id);
        if (!$classStudioAct) {
            throw new LogicException('Meeting not found');
        }

        try {
            /** @var ClassStudioDate $classStudioDate */
            $classStudioDate = $classStudioAct->classStudioDate();

            ClientActivities::CancelClassReturnBalance($classStudioAct, $classStudioAct->CompanyNum, ClassStudioAct::STATUS_MEETING_CANCELED);
            $classStudioAct->changeStatus(ClassStudioAct::STATUS_MEETING_CANCELED);
            $classStudioAct->Status = ClassStudioAct::STATUS_MEETING_CANCELED;

            $newClientActivityInfo = ClientActivities::assignMembership([
                'clientId' => $classStudioAct->client()->id,
                'itemId' => Item::getSingleClassItem($classStudioDate->ClassNameType),
                'activityName' => $classStudioDate->getSingleItemName(),
                'itemPrice' => $classStudioDate->purchaseAmount,
                'isForMeeting' => 1,
                'isDisplayed' => 0
            ]);

            $newClientActivity = ClientActivities::find($newClientActivityInfo['ClientActivityId']);

            // set new client activity
            $statusJson = $classStudioAct->getTransferStatusJson(ClassStudioAct::STATUS_MEETING_ACTIVE, $newClientActivity->CardNumber);
            $classStudioAct->ClientActivitiesId = $newClientActivity->id;
            ClientActivities::CancelClassReturnBalance($classStudioAct, $classStudioAct->CompanyNum, ClassStudioAct::STATUS_MEETING_ACTIVE);

            $classStudioAct->Department = $newClientActivity->Department;
            $classStudioAct->MemberShip = $newClientActivity->MemberShip;
            $classStudioAct->StatusJson = $statusJson;
            $classStudioAct->StatusCount = 0;
            $classStudioAct->Status = ClassStudioAct::STATUS_MEETING_ACTIVE;
            $classStudioAct->save();

            EditMeetingService::changeStatus($id, $classStudioAct->classStudioDate()->meetingStatus, MeetingStatus::ORDERED);

        } catch (\Throwable $e) {
            LoggerService::error($e);
            throw $e;
        }

        return ['status' => 'success', 'success' => true];
    }

    /**
     * @param $id
     * @param $meetingCancelReason
     * @param string $cancellationPolicy
     * @param string $repeatType
     * @param null $repeatVal
     * @param bool $overrideStartDate
     * @return array
     * @throws Throwable
     */
    public static function cancelMeeting($id, $meetingCancelReason, $cancellationPolicy = 'no_charge', $repeatType = 'single', $repeatVal = null, $overrideStartDate = false)
    {
        /** @var ClassStudioDate $meeting */
        $meeting = ClassStudioDate::find($id);
        $meeting->Status = ClassStudioDate::STATUS_CANCELLED;
        $meeting->meetingStatus = MeetingStatus::CANCELED;
        $meeting->displayCancel = 0;
        $meeting->setCancelReason($meetingCancelReason);

        /** @var ClassStudioAct $meetingAct */
        $meetingAct = ClassStudioAct::getMeetingActByClassId($id);
        $meetingAct->setClassStudioDate($meeting);

        $client = $meetingAct->client();

        if ($client && !$client->isRandomClient) {
            $clientActivity = $meetingAct->clientActivity();
            if (is_numeric($cancellationPolicy)) {
                if ($clientActivity) {
                    if ($clientActivity->isPaymentForSingleClass) {
                        $chargeAmount = $clientActivity->ItemPrice * $cancellationPolicy / 100;
                        $clientActivity->applyCancellationPolicy($chargeAmount);
                    } else {
                        $clientActivityId = ClientActivities::applyCancellationOnMembership($cancellationPolicy, $meetingAct);
                        if ($clientActivityId != $clientActivity->id) {
                            $clientActivity = ClientActivities::find($clientActivityId);
                        }
                    }
                    if ($clientActivity->isPaymentForSingleClass && $clientActivity->isForMeeting) {
                        $clientTokens = $client->tokens();
                        if (count($clientTokens) > 0) {
                            MeetingPayment::payByMeetingAct($meetingAct);
                        }
                    }
                }
            } else {
                if ($clientActivity) {
                    if ($clientActivity->isPaymentForSingleClass) {
                        $clientActivity->Status = ClientActivities::STATUS_CANCEL;
//                        $clientActivity->isDisplayed = 1;
                        $clientActivity->BalanceMoney = 0;
                        $clientActivity->CancelStatus = 1;
                        $clientActivity->save();

                        $client->updateBalanceAmount();
                    }

                    ClientActivities::CancelClassReturnBalance(
                        $meetingAct,
                        $meetingAct->CompanyNum,
                        ClassStudioAct::STATUS_MEETING_CANCELED
                    );
                    $meetingAct->changeStatus(ClassStudioAct::STATUS_MEETING_CANCELED);
                }
            }
        }

        $query = ClassStudioDate::where('CompanyNum', $meeting->CompanyNum)
            ->where('GroupNumber', $meeting->GroupNumber)
            ->whereIn('Status', [ClassStudioDate::STATUS_ACTIVE, ClassStudioDate::STATUS_COMPLETED])
            ->where('StartDate', '>=', $meeting->StartDate);
        if (!$overrideStartDate) {
            $query = $query->where('StartDate', '>=', date('Y-m-d'));
        }

        switch ($repeatType) {
            case 'single':
                $query = $query->where('id', '=', $meeting->id);
                break;
            case 'dates':
                $repeatVal = json_decode($repeatVal);
                $query = $query->where('StartDate', '>=', $repeatVal->since)
                    ->where('EndDate', '<=', $repeatVal->until);
                break;
            case 'quantity':
                if (is_numeric($repeatVal)) {
                    $query = $query->limit((int)$repeatVal);
                } else {
                    return ['status' => 0, 'message' => 'invalid repeat quantity'];
                }
                break;
        }

        $query->update($meeting->getDirty());

        AppNotification::sendMeetingStatusUpdateToClient(
            $client->id,
            $meetingAct->ClassName,
            $meetingAct->ClassDate.' '.$meetingAct->ClassStartTime,
            31
        );

        return [
            'status' => 'success',
            'message' => 'Meeting canceled',
            'success' => true
        ];
    }

    /**
     * @param string $ids
     * @return array|string[]
     * @throws Throwable
     */
    public static function approveMeeting(string $ids)
    {
        $CompanyNum = Auth::user()->CompanyNum;

        $state = true;
        if ($ids === 'all') {
            // get all meetings awaiting approval
            // TODO: move logic from controller to service
            $meetingIds = MeetingService::getAllWaitingMeetingsIds($CompanyNum);
        } else {
            $meetingIds = [$ids];

            $classStudioDate = ClassStudioDate::find($ids);
            $classStudioAct = $classStudioDate->classStudioAct();

            $groupId = MeetingGroupOrdersToAct::where('ClassStudioActId', $classStudioAct->id)->pluck('MeetingGroupOrdersId');
            if ($groupId !== null) {
                /** @var MeetingGroupOrdersToAct[] $groupOfActs */
                $groupOfActs = MeetingGroupOrdersToAct::where('MeetingGroupOrdersId', $groupId)->get();
                if (!empty($groupOfActs) && count($groupOfActs) > 1) {
                    foreach ($groupOfActs as $groupOfAct) {
                        $act = $groupOfAct->classStudioAct();
                        if (!$act) {
                            continue;
                        }

                        $classStudioDate = $act->classStudioDate();
                        if (!$classStudioDate || $classStudioDate->meetingStatus != MeetingStatus::WAITING) {
                            continue;
                        }

                        $meetingIds[] = $classStudioDate->id;
                    }
                }
            }
        }

        $meetingIds = array_unique($meetingIds);


        foreach ($meetingIds as $id) {
            $studioAct = ClassStudioAct::getMeetingActByClassId($id);

            AppNotification::sendMeetingStatusUpdateToClient(
                $studioAct->ClientId,
                $studioAct->ClassName,
                $studioAct->ClassDate.' '.$studioAct->ClassStartTime,
                30
            );

            if (!self::changeStatus($id, MeetingStatus::WAITING,
                $studioAct->clientActivity()->isPaymentForSingleClass ? MeetingStatus::ORDERED : MeetingStatus::COMPLETED)) {
                $state = false;
                break;
            }
        }

        if ($state) {
            $result = ['Status' => 'OK', 'message' => 'Status changed', 'success' => true];
        } else {
            $result = ['Status' => 'Error', 'message' => 'Status not changed'];
        }

        return $result;
    }

    /**
     * @param int $id - ClassStudioDate id
     * @return bool
     */
    public static function changeToCompletedAndShow(int $id): bool
    {
        /** @var ClassStudioDate $meeting */
        $meeting = ClassStudioDate::find($id);
        $meeting->meetingStatus = MeetingStatus::COMPLETED;
        return $meeting->save();
    }


    /**
     * @param int $actId
     * @param int $status
     * @param bool $returnOldStatus
     * @return int ($returnOldStatus == ture) return old status else return true or false
     */
    public static function updateStatusMeetingByActId(int $actId, int $status, bool $returnOldStatus): int
    {
        /** @var ClassStudioAct $classStudioAct */
        $classStudioAct = ClassStudioAct::find($actId);
        if ($classStudioAct === null) {
            return 0;
        }
        $classStudioAct->Status = 1;
        $classStudioAct->save();
        $classStudioDate = $classStudioAct->classStudioDate();
        if ($classStudioDate === null || $classStudioDate->meetingStatus === null) {
            return 0;
        }
        $oldStatus = $classStudioDate->meetingStatus;
        $classStudioDate->meetingStatus = $status;
        if($returnOldStatus) {
            $classStudioDate->save();
            return $oldStatus;
        }
        return $classStudioDate->save();
    }

}
