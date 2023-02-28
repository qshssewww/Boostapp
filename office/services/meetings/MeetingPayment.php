<?php

require_once __DIR__ . '/../../Classes/ClassStudioAct.php';
require_once __DIR__ . '/../../Classes/ClassStudioDate.php';
require_once __DIR__ . '/../../Classes/ClientActivities.php';
require_once __DIR__ . '/../../Classes/MeetingClient.php';
require_once __DIR__ . '/../../Classes/Settings.php';
require_once __DIR__ . '/../../Classes/Token.php';
require_once __DIR__ . '/../../Classes/TempReceiptPayment.php';
require_once __DIR__ . '/../../Classes/Transaction.php';
require_once __DIR__ . '/../../Classes/OrderLogin.php';
require_once __DIR__ . '/../LoggerService.php';
require_once __DIR__ . '/../PaymentService.php';
require_once __DIR__ . '/../OrderService.php';
require_once __DIR__ . '/../receipt/ReceiptService.php';
require_once __DIR__ . '/EditMeetingService.php';




class MeetingPayment
{
    /**
     * @param $id
     * @return array
     * @throws Throwable
     */
    public static function payByMeetingId($id): array
    {
        try {
            $studioAct = ClassStudioAct::getMeetingActByClassId($id);
            if (!$studioAct) {
                throw new LogicException('Meeting not found');
            }
            $result = self::payByMeetingAct($studioAct);
        } catch (\Throwable $e) {
            LoggerService::error($e);
            throw $e;
        }
        return $result;
    }

    /**
     * @param ClassStudioAct $studioAct
     * @return array
     * @throws Throwable
     */
    public static function payByMeetingAct(ClassStudioAct $studioAct): array
    {
        try {
            $CompanyNum = Auth::user()->CompanyNum;
            /** @var ClassStudioDate|null $meeting */
            $meeting = $studioAct->classStudioDate();
            if (!$meeting) {
                throw new LogicException('Meeting not found');
            }

            $client = $studioAct->client();
            if (!$client) {
                throw new LogicException('Client not found');
            }

            /** @var ClientActivities|null $clientActivity */
            $clientActivity = $studioAct->clientActivity();
            if (!$clientActivity) {
                throw new LogicException('Client Activity not found');
            }

            $token = null;

            // get linked card (token) with MeetingClient model
            $GroupNumber = $meeting->GroupNumber;
            /** @var MeetingClient $meetingInfo */
            $meetingInfo = MeetingClient::where('GroupNumber', $GroupNumber)->first();
            if ($meetingInfo) {
                /** @var Token $token */
                $token = $meetingInfo->token();
                if (!$token || $token->Status == Token::STATUS_INACTIVE) {
                    $token = null;
                }
            }

            // if there is no card from meeting info, get first of client's cards
            if ($token === null && !empty($client->tokens())) {
                $token = $client->tokens()[0];
            }

            if (!$token) {
                throw new LogicException('Credit card not found');
            }

            // charge from saved card
            /** @var Settings $studioSettings */
            $studioSettings = Settings::getSettings($CompanyNum);

            $amount = $clientActivity->BalanceMoney;

            $TempReceipt = new TempReceiptPayment();
            $TempReceipt->CompanyNum = $studioAct->client()->CompanyNum;
            $TempReceipt->TypeDoc = DocsTable::TYPE_KABALA;
            $TempReceipt->TempId = $studioAct->client()->id;
            $TempReceipt->TypePayment = 3;
            $TempReceipt->tashType = 1; // regular payment
            $TempReceipt->Amount = $amount;
            $TempReceipt->Dates = date('Y-m-d H:i:s');
            $TempReceipt->UserDate = date('Y-m-d');
            $TempReceipt->CheckDate = date('Y-m-d');
            $TempReceipt->UserId = Auth::user()->id;
            $TempReceipt->CreditType = 'עסקה מגנטית';
            $TempReceipt->ClientActivityId = $clientActivity->id;

            $TempReceipt->save();

            $paymentSystem = PaymentService::getPaymentSystemByType($studioSettings->TypeShva);

            $order = OrderService::createOrder($client, $amount, 1, OrderLogin::TYPE_PAYMENT_SAVED_CARD_MEETING, Auth::user()->id);
            $order->TempReceiptId = $TempReceipt->id;
            $order->save();

            $paymentResult = $paymentSystem->makePaymentWithToken($order, $token, 1, $order->NumPayment);

            $transaction = Transaction::saveTransaction($client, $paymentResult, Auth::user()->id);

            $BrandName = $paymentResult['BrandName'];
            $L4digit = $paymentResult['L4digit'];
            $CCode = $paymentResult['CCode'];
            $Bank = $paymentResult['Bank'];
            $Brand = $paymentResult['Brand'];
            $Issuer = $paymentResult['Issuer'];

            $YaadCode = $paymentResult['YaadCode'];
            $ACode = $paymentResult['ACode'];
            $Payments = $paymentResult['Payments'];
            $PayToken = $paymentResult['PayToken'];
            $TransactionId = $paymentResult['TransactionId'];
            $MeshulamPageCode = $paymentResult['MeshulamPageCode'] ?? null;

            $TempReceipt->L4digit = $L4digit;
            $TempReceipt->YaadCode = $YaadCode;
            $TempReceipt->CCode = $CCode;
            $TempReceipt->ACode = $ACode;
            $TempReceipt->Bank = $Bank;
            $TempReceipt->Payments = $Payments;
            $TempReceipt->Brand = $Brand;
            $TempReceipt->BrandName = $BrandName;
            $TempReceipt->Issuer = $Issuer;
            $TempReceipt->PayToken = $PayToken;
            $TempReceipt->TransactionId = $YaadCode;
            $TempReceipt->OrderId = $order->id;
            $TempReceipt->PaymentConfirmed = 1;
            $TempReceipt->MeshulamPageCode = $MeshulamPageCode;

            $TempReceipt->save();

            ReceiptService::saveReceiptAfterPayWithCard($order);

            $order->Status = OrderLogin::STATUS_PAID;
            $order->TransactionId = $transaction->id;
            $order->save();

            // change meeting status to "Completed"
            EditMeetingService::changeStatus($meeting->id, $meeting->meetingStatus, MeetingStatus::COMPLETED);

            $result = [
                'status' => 'success',
                'success' => true,
            ];
        } catch (\Throwable $e) {
            LoggerService::error($e);
            throw $e;
        }
        return $result;
    }
}