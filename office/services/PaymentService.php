<?php

require_once __DIR__ . '/LoggerService.php';
require_once __DIR__ . '/OrderService.php';
require_once __DIR__ . '/payment/Meshulam.php';
require_once __DIR__ . '/payment/PaymentTypeEnum.php';
require_once __DIR__ . '/payment/Tranzila.php';
require_once __DIR__ . '/payment/Yaad.php';
require_once __DIR__ . '/receipt/ReceiptService.php';
require_once __DIR__ . '/../Classes/DocsPayments.php';
require_once __DIR__ . '/../Classes/OrderLogin.php';
require_once __DIR__ . '/../Classes/TempReceiptPaymentClient.php';
require_once __DIR__ . '/../Classes/TempReceiptPayment.php';
require_once __DIR__ . '/../Classes/Token.php';
require_once __DIR__ . '/../Classes/Transaction.php';

class PaymentService
{
    public const PAYMENT_YAAD = Yaad::METHOD;
    public const PAYMENT_MESHULAM = Meshulam::METHOD;
    public const PAYMENT_BIT = 'bit';
    public const PAYMENT_TRANZILA = Tranzila::METHOD;


    public const CARD_SYSTEM_PAYMENT_TYPE_REGULAR = 1;
    public const CARD_SYSTEM_PAYMENT_TYPE_PAYMENTS = 2;
    public const CARD_SYSTEM_PAYMENT_TYPE_BOOST_PAYMENTS = 3;


    /**
     * @param $method
     * @return Meshulam|Yaad|Tranzila
     * @throws Exception
     */
    public static function getPaymentSystemByMethod($method)
    {
        if (self::PAYMENT_YAAD === $method) {
            $paymentSystem = Yaad::getInstance();
        } elseif (self::PAYMENT_MESHULAM === $method) {
            $paymentSystem = Meshulam::getInstance();
        } elseif (self::PAYMENT_TRANZILA === $method) {
            $paymentSystem = Tranzila::getInstance();
        } else {
            throw new Exception('Wrong payment method');
        }

        return $paymentSystem;
    }

    /**
     * @param $type
     * @return Meshulam|Yaad|Tranzila
     * @throws Exception
     */
    public static function getPaymentSystemByType($type)
    {
        return self::getPaymentSystemByMethod(self::getPaymentMethodByType($type));
    }

    /**
     * @param $type
     * @return string
     */
    public static function getPaymentMethodByType($type)
    {
        switch ((int) $type) {
            case PaymentTypeEnum::TYPE_YAAD:
                return self::PAYMENT_YAAD;
            case PaymentTypeEnum::TYPE_MESHULAM:
                return self::PAYMENT_MESHULAM;
            case PaymentTypeEnum::TYPE_TRANZILA:
                return self::PAYMENT_TRANZILA;
        }

        throw new InvalidArgumentException('Wrong payment system');
    }

    /**
     * @param $data
     * @param bool $isRefund
     * @param bool $isDocs if we create new client from popup - $isDocs = false
     * @return array
     * @throws Exception|Throwable
     */
    public static function chargeClient($data, bool $isRefund = false, bool $isDocs = false)
    {
        // check amount is valid
//        if(isset($data['CashValue']) && $data['CashValue'] <= 0 ){
//            throw new Exception('CashValue no valid');
//        } elseif(isset($data['CreditValue']) && $data['CreditValue'] <= 0 ){
//            throw new Exception('CreditValue no valid');
//        } elseif(isset($data['CheckValue']) && $data['CheckValue'] <= 0 ){
//            throw new Exception('CheckValue no valid');
//        } elseif(isset($data['BankValue']) && $data['BankValue'] <= 0 ){
//            throw new Exception('BankValue no valid');
//        }
        $UserId = Auth::user()->id;
        $CompanyNum = Auth::user()->CompanyNum;
        $studioSettings = Settings::getSettings($CompanyNum);

        $paymentSystem = PaymentService::getPaymentSystemByType($studioSettings->TypeShva);

        $Dates = date('Y-m-d H:i:s');
        $UserDate = date('Y-m-d');
        $CheckRefresh = $data['CheckRefresh'] ?? 0;
        $TempId = $data['TempId'];
        $TypeDoc = $data['TypeDoc'];
        $TrueFinalinvoicenum = $data['TrueFinalinvoicenum'] ?? 0;
        $Finalinvoicenum = $data['Finalinvoicenum'] ?? 0;

        $result = [
            'status' => 'success',
            'TrueFinalinvoicenum' => $TrueFinalinvoicenum,
            'Finalinvoicenum' => $Finalinvoicenum,
            'TempId' => $TempId,
            'TypeDoc' => $TypeDoc,
            'CreditStatus' => 0,
        ];

        if ($CheckRefresh != 2) {
            $Act = $data['Act'];

            if (!$isDocs) {
                $TempReceipt = new TempReceiptPaymentClient();
            } else {
                $TempReceipt = new TempReceiptPayment();
            }

            $TempReceipt->CompanyNum = $CompanyNum;
            $TempReceipt->TypeDoc = $TypeDoc;
            $TempReceipt->TempId = $TempId;
            $TempReceipt->Dates = $Dates;
            $TempReceipt->UserDate = $UserDate;
            $TempReceipt->UserId = $UserId;
            $TempReceipt->CheckDate = $UserDate;

            if ($Act == '1') {
                // cash / cash refund

                $CashValue = $data['CashValue'];

                $TempReceipt->TypePayment = 1;
                $TempReceipt->Amount = $CashValue;
                $TempReceipt->Excess = 0;

                $TempReceipt->save();

            } elseif ($Act == '2') {
                // bank checks / bank checks refund

                $CheckValue = $data['CheckValue'];
                $CheckDate = $data['CheckDate'];
                $CheckSnif = $data['CheckSnif'];
                $CheckAccount = $data['CheckAccount'];
                $CheckBank = $data['CheckBank'];
                $CheckNumber = $data['CheckNumber'];

                if (!$isRefund) {
                    $documentExists = DocsPayment::select('*')
                        ->where('CheckNumber', $CheckNumber)
                        ->where('CompanyNum', $CompanyNum)
                        ->where('ClientId', $TempId)
                        ->first();

                    if ($documentExists) {
                        $result['status'] = 'error';
                        return $result;
                    }

                    $checkReceiptExists = $TempReceipt->where('CheckNumber', $CheckNumber)->where('TempId', $TempId)->first();
                    if ($checkReceiptExists) {
                        $TempReceipt = $TempReceipt::find($checkReceiptExists->id);
                        $TempReceipt->CompanyNum = $CompanyNum;
                        $TempReceipt->TypeDoc = $TypeDoc;
                        $TempReceipt->TempId = $TempId;
                        $TempReceipt->Dates = $Dates;
                        $TempReceipt->UserDate = $UserDate;
                        $TempReceipt->UserId = $UserId;
                        $TempReceipt->CheckDate = $UserDate;
                    }
                }

                $TempReceipt->TypePayment = 2;
                $TempReceipt->Amount = $CheckValue;
                $TempReceipt->CheckBank = $CheckAccount;
                $TempReceipt->CheckBankSnif = $CheckSnif;
                $TempReceipt->CheckDate = $CheckDate;
                $TempReceipt->CheckBankCode = $CheckBank;
                $TempReceipt->CheckNumber = $CheckNumber;

                $TempReceipt->save();

            } elseif ($Act == '3') {
                // credit card
                $data['Credit'] = (int)$data['Credit'];


                $CheckPayments = $TempReceipt::where('TempId', '=', $TempId)
                    ->where('CompanyNum', '=', $CompanyNum)
                    ->where('TypePayment', '=', '3')
                    ->where('Amount', '=', $data['CreditValue'])
                    ->first();

                if ($CheckPayments) {
                    $CheckPayments->TypeDoc = $TypeDoc;
                    $CheckPayments->save();

                    return $result;
                }

                $client = new Client((int)$data['ClientId']);
                if (!$client->id && $data['Credit'] !== 4) {
                    throw new InvalidArgumentException('Wrong Client ID or CompanyNum');
                }

                $CreditType = 'עסקה מגנטית';

                if ($data['tashType'] == '0') {
                    $tashTypeDB = '1';
                } elseif ($data['tashType'] == '1') {
                    $tashTypeDB = '2';
                } elseif ($data['tashType'] == '2') {
                    $tashTypeDB = '4';
                } elseif ($data['tashType'] == '6') {
                    $tashTypeDB = '3';
                } else {
                    $tashTypeDB = '5';
                }


                $paymentNumber = $data['Tash'];
                $paymentType = $tashTypeDB;

                $TempReceipt->CompanyNum = $CompanyNum;
                $TempReceipt->TypeDoc = $TypeDoc;
                $TempReceipt->TempId = $TempId;
                $TempReceipt->TypePayment = 3;
                $TempReceipt->Amount = $data['CreditValue'];
                $TempReceipt->tashType = $tashTypeDB;
                $TempReceipt->CheckDate = $UserDate;
                $TempReceipt->Dates = $Dates;
                $TempReceipt->UserId = $UserId;
                $TempReceipt->UserDate = $UserDate;
                $TempReceipt->CreditType = $CreditType;
                $TempReceipt->PayToken = null;

                if ($data['Credit'] === 1) {
                    // card scanner

                    try {
                        if (PaymentService::getPaymentMethodByType($studioSettings->TypeShva) != PaymentService::PAYMENT_YAAD) {
                            // if not yaad => throw exception
                            throw new LogicException('Wrong payment system');
                        }

                        $CreditType = 'עסקה מגנטית';

                        if ($isDocs) {
                            $orderType = OrderLogin::TYPE_PAYMENT_CARD_READER_DOCS;
                        } else {
                            $orderType = OrderLogin::TYPE_PAYMENT_CARD_READER;
                        }

                        $order = OrderService::createOrder($client, $data['CreditValue'], $paymentNumber, $orderType);

                        $order->PaymentMethod = PaymentService::getPaymentMethodByType($studioSettings->TypeShva);
                        $order->save();

                        // get card number from reader to send to payment system
                        $cardNumberFromReader = $data['CC2'];

                        $TempReceipt->CreditType = $CreditType;
                        $TempReceipt->OrderId = $order->id;

                        $paymentResult = $paymentSystem->makePaymentWithMagneticStripe($order, $cardNumberFromReader, $paymentType, $paymentNumber);
                        LoggerService::info($paymentResult, LoggerService::CATEGORY_PAYMENT_PROCESS);

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

                        if ($CCode == '0' || $CCode == '700' || $CCode == '600') {
                            $BrandName = $paymentResult['BrandName'];

                            $result['CreditStatus'] = 1;

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

                            $TempReceipt->save();

                            $transaction = new Transaction();
                            $transaction->CompanyNum = $CompanyNum;
                            $transaction->ClientId = $TempId;
                            $transaction->UpdateTransactionDetails = serialize($paymentResult);
                            $transaction->UserId = $UserId;
                            $transaction->Transaction = $TransactionId;
                            if (!$transaction->save()) {
                                LoggerService::error([
                                    'message' => 'Transaction is not set',
                                    'transaction' => $transaction->toArray(),
                                ]);
                            }

                            $order->TransactionId = $transaction->id;
                            $order->TempReceiptId = $TempReceipt->id;
                            $order->Status = OrderLogin::STATUS_PAID;
                            if (!$order->save()) {
                                LoggerService::error([
                                    'message' => 'Error while updating order',
                                    'order' => $order->toArray(),
                                    'TransactionId' => $transaction->id,
                                    'TempReceiptId' => $TempReceipt->id,
                                ]);
                            }
                        }
                    } catch (\Throwable $e) {
                        LoggerService::error($e);

                        $result['status'] = 'error';
                        $result['CreditStatus'] = 2;
                        if (is_numeric($e->getMessage())) {
                            // Yaad
                            $result['CCode'] = $e->getMessage();
                            $result['ErrorMessage'] = null;
                        } else {
                            // Meshulam
                            $result['CCode'] = null;
                            $result['ErrorMessage'] = $e->getMessage();
                        }

                        return $result;
                    }
                } elseif ($data['Credit'] === 2) {
                    // saved card (token)
                    $CreditType = 'עסקת טוקן';

                    if (!$isDocs) {
                        $orderType = OrderLogin::TYPE_PAYMENT_SAVED_CARD;
                    } else {
                        $orderType = OrderLogin::TYPE_PAYMENT_SAVED_CARD_DOCS;
                    }

                    $order = OrderService::createOrder($client, $data['CreditValue'], $paymentNumber, $orderType);

                    $order->PaymentMethod = PaymentService::getPaymentMethodByType($studioSettings->TypeShva);
                    $order->save();

                    $tokenId = $data['CC3'];

                    $tokenModel = Token::where('id', $tokenId)->first();
                    if (!$tokenModel) {
                        throw new InvalidArgumentException('Token is not valid');
                    }

                    $TempReceipt->CreditType = $CreditType;
                    $TempReceipt->TokenId = $tokenModel->id;
                    $TempReceipt->OrderId = $order->id;
                    $order->TokenId = $tokenModel->id;
                    $order->save();

                    try {
                        if (!$isRefund) {
                            $paymentResult = $paymentSystem->makePaymentWithToken($order, $tokenModel, $paymentType, $paymentNumber);
                            LoggerService::info($paymentResult, LoggerService::CATEGORY_PAYMENT_PROCESS);
                        } else {
                            $paymentResult = $paymentSystem->makeRefundWithToken($order, $tokenModel);
                            LoggerService::info($paymentResult, LoggerService::CATEGORY_PAYMENT_PROCESS_REFUND);
                        }

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

                        if ($CCode == '0' || $CCode == '700' || $CCode == '600') {
                            $BrandName = $paymentResult['BrandName'];

                            $result['CreditStatus'] = 1;

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
                            $TempReceipt->MeshulamPageCode = $MeshulamPageCode;

                            $TempReceipt->save();

                            $transaction = new Transaction();
                            $transaction->CompanyNum = $CompanyNum;
                            $transaction->ClientId = $TempId;
                            $transaction->UpdateTransactionDetails = serialize($paymentResult);
                            $transaction->UserId = $UserId;
                            $transaction->Transaction = $TransactionId;
                            if (!$transaction->save()) {
                                LoggerService::error([
                                    'message' => 'Transaction is not set',
                                    'transaction' => $transaction->toArray(),
                                ]);
                            }

                            $order->TransactionId = $transaction->id;
                            $order->TempReceiptId = $TempReceipt->id;
                            $order->Status = OrderLogin::STATUS_PAID;
                            if (!$order->save()) {
                                LoggerService::error([
                                    'message' => 'Error while updating order',
                                    'order' => $order->toArray(),
                                    'TransactionId' => $transaction->id,
                                    'TempReceiptId' => $TempReceipt->id,
                                ]);
                            }
                        }
                    } catch (\Throwable $e) {
                        LoggerService::error($e);

                        $result['status'] = 'error';
                        $result['CreditStatus'] = 2;
                        if (is_numeric($e->getMessage())) {
                            // Yaad
                            $result['CCode'] = $e->getMessage();
                            $result['ErrorMessage'] = null;
                        } else {
                            // Meshulam
                            $result['CCode'] = null;
                            $result['ErrorMessage'] = $e->getMessage();
                        }

                        return $result;
                    }
                } elseif ($data['Credit'] === 3) {
                    // pay with new card
                    $CreditType = 'עסקה טלפונית';

                    if ($isRefund) {
                        $tokenModel = Token::getById($data['TokenId']);

                        if (!$isDocs) {
                            $orderType = OrderLogin::TYPE_REFUND_NEW_CARD;
                        } else {
                            $orderType = OrderLogin::TYPE_PAYMENT_SAVED_CARD_DOCS;
                        }

                        $order = OrderService::createOrder($client, $data['CreditValue'], $paymentNumber, $orderType);

                        $order->PaymentMethod = self::getPaymentMethodByType($studioSettings->TypeShva);
                        $order->save();

                        $paymentResult = $paymentSystem->makeRefundWithToken($order, $tokenModel);
                        $L4digit = $paymentResult['L4digit'];
                        $CCode = $paymentResult['CCode'];
                        $Bank = $paymentResult['Bank'];
                        $Brand = $paymentResult['Brand'];
                        $Issuer = $paymentResult['Issuer'];

                        $YaadCode = $paymentResult['YaadCode'];
                        $ACode = $paymentResult['ACode'];
                        $Payments = $paymentResult['Payments'];
                        $PayToken = $paymentResult['PayToken'];

                        if ($CCode == '0' || $CCode == '700' || $CCode == '600') {
                            $BrandName = $paymentResult['BrandName'];

                            $result['CreditStatus'] = 1;

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
                            $TempReceipt->CreditType = $CreditType;

                            $TempReceipt->save();

                            $transaction = new Transaction();
                            $transaction->CompanyNum = $CompanyNum;
                            $transaction->ClientId = $client->id;
                            $transaction->UpdateTransactionDetails = serialize($paymentResult);
                            $transaction->UserId = 0;
                            $transaction->save();

                            $order->TempReceiptId = $TempReceipt->id;
                            $order->TransactionId = $transaction->id;
                            $order->Status = OrderLogin::STATUS_PAID;
                            $order->save();
                        }
                    }
                } elseif ($data['Credit'] === 4) {
                    // deposit with another terminal - just save info
                    $CreditType = 'עסקה ממסוף אחר';

                    $TempReceipt->UserDate = $data['CDate'];
                    $TempReceipt->L4digit = $data['CC'];
                    $TempReceipt->YaadCode = 0;
                    $TempReceipt->CCode = 0;
                    $TempReceipt->ACode = $data['CCode'];
                    $TempReceipt->Bank = $data['TypeBank'];
                    $TempReceipt->Payments = $data['Tash'];
                    $TempReceipt->Brand = $data['TypeBrand'];
                    $TempReceipt->BrandName = lang('another_terminal_process_meshulam');
                    $TempReceipt->Issuer = $data['TypeBank'];
                    $TempReceipt->PayToken = '';
                    $TempReceipt->CreditType = $CreditType;

                    $TempReceipt->save();
                }
            } elseif ($Act == '4') {
                // bank transfer

                $BankValue = $data['BankValue'];
                $BankDate = $data['BankDate'];
                $BankNumber = $data['BankNumber'];

                $TempReceipt->TypePayment = 4;
                $TempReceipt->Amount = $BankValue;
                $TempReceipt->CheckDate = $BankDate;
                $TempReceipt->BankNumber = $BankNumber;
                $TempReceipt->save();
            }
        }

        return $result;
    }

    /**
     * @param int $orderId
     * @param $data
     * @param $paymentMethod
     * @return OrderLogin
     * @throws Throwable
     */
    public static function processPayment(int $orderId, $data, $paymentMethod)
    {
        try {
            /** @var OrderLogin $order */
            $order = OrderLogin::find($orderId);
            if (!$order) {
                throw new InvalidArgumentException('Wrong Order ID');
            }

            if ((int)$order->Status === OrderLogin::STATUS_PAID) {
                throw new InvalidArgumentException('Order is already paid');
            }

            $paymentSystem = self::getPaymentSystemByMethod($paymentMethod);

            $paymentData = $paymentSystem->prepareData($data);

            // refresh order from db
            /** @var OrderLogin $order */
            $order = OrderLogin::find($order->id);
            if(!$order) {
                throw new InvalidArgumentException('Wrong Order ID');
            }
            $Client = $order->client();
            if($Client === null) {
                throw new InvalidArgumentException('Wrong Client ID');
            }
            $transactionModel = Transaction::saveTransaction($Client, $paymentData);
            $order->TransactionId = $transactionModel->id;
            $order->updateCheckOutOrder();

            /** @var $tempPaymentInfo TempReceiptPaymentClient|TempReceiptPayment|null */
            $tempPaymentInfo = $order->tempReceipt();
            if ($tempPaymentInfo) {
                $tempPaymentInfo->L4digit = $paymentData['L4digit'];
                $tempPaymentInfo->YaadCode = $paymentData['YaadCode'];
                $tempPaymentInfo->CCode = $paymentData['CCode'];
                $tempPaymentInfo->ACode = $paymentData['ACode'];
                $tempPaymentInfo->Bank = $paymentData['Bank'];
                $tempPaymentInfo->Brand = $paymentData['Brand'];
                $tempPaymentInfo->PayToken = $paymentData['PayToken'];
                $tempPaymentInfo->TransactionId = $paymentData['TransactionId'];
                $tempPaymentInfo->BrandName = $paymentData['BrandName'];
                $tempPaymentInfo->TokenId = $paymentData['TokenId'];
                $tempPaymentInfo->PaymentConfirmed = 1;
                $tempPaymentInfo->MeshulamPageCode = $paymentData['MeshulamPageCode'] ?? null;

                $tempPaymentInfo->save();

                if ($order->Type === OrderLogin::TYPE_NEW_CLIENT) {
                    ReceiptService::saveReceiptAfterPayWithCard($order);
                }
            }

            $order->Status = OrderLogin::STATUS_PAID;
            $order->save();

            $token = $order->token();
            $token->UserId = $order->UserId;
            $token->save();

            return $order;
        } catch (\Throwable $e) {
            LoggerService::error($e, LoggerService::CATEGORY_PAYMENT_PROCESS);
            throw $e;
        }
    }

    /**
     * @param DocsPayment $docsPayment
     * @param $amount
     * @return array
     * @throws LogicException if payment system rejected payment
     * @throws Throwable if request to payment system is failed
     */
    public static function refundByDocPayment(DocsPayment $docsPayment, $amount, int $typeShva): array
    {
        $client = $docsPayment->client();
        if (!$client) {
            throw new InvalidArgumentException('Client not found');
        }

        $token = Token::where('CompanyNum', '=', $docsPayment->CompanyNum)
            ->where('ClientId', '=', $docsPayment->ClientId)
            ->where('L4digit', '=', $docsPayment->L4digit)
            ->where('Type', '=', $typeShva)
//            ->where("Private", "=", 0)
            ->orderBy('id', 'desc')
            ->first();
        if (!$token) {
            throw new InvalidArgumentException('Token not found');
        }

        $UserId = Auth::check() ? Auth::user()->id : null;
        $studioSettings = $client->studioSettings();
        $paymentMethod = self::getPaymentMethodByType($studioSettings->TypeShva);

        $order = OrderService::createOrder($client, $docsPayment->getSum(true), 1, OrderLogin::TYPE_REFUND);
        $order->Amount = $amount; // set amount that could be less than TotalAmount
        $order->TokenId = $token->id;
        $order->PaymentMethod = $paymentMethod;
        $order->save();

        $transactionInfo = $docsPayment->toArray();
        if ($paymentMethod === self::PAYMENT_TRANZILA) {
            $previousTransaction = Transaction::where('Transaction', $docsPayment['TransactionId'])->where('ClientId', $docsPayment->ClientId)->first();
            if (!$previousTransaction) {
                throw new LogicException('Previous transaction not found');
            }
            $transactionDetails = unserialize($previousTransaction->UpdateTransactionDetails, ['allowed_class' => [stdClass::class]]);
            $order->TokenId = $transactionDetails['TokenId'] ?? 0;
//            $token = Token::where('id', $tokenId)->where('Status', Token::STATUS_ACTIVE)->first();
//            $order->TokenId = $token->id;
//            $transactionInfo = $transactionDetails;
        }

        $paymentResult = self::makeRefund($order, $transactionInfo);

        $transaction = Transaction::saveTransaction($client, $paymentResult, $UserId);
        $order->TransactionId = $transaction->id;
        $order->save();

        return $paymentResult;
    }

    /**
     * @param OrderLogin $order
     * @param array $transaction [ACode, YaadCode, PayToken]
     * @return array
     * @throws Throwable
     */
    public static function makeRefund(OrderLogin $order, array $transaction=[])
    {
        /** @var Settings $studioSettings */
        $studioSettings = $order->studioSettings();
        $paymentSystem = self::getPaymentSystemByMethod($order->PaymentMethod);
        $token = $order->token();

        switch (PaymentService::getPaymentMethodByType($studioSettings->TypeShva)) {
            case self::PAYMENT_YAAD:
                $paymentResult = $paymentSystem->makeRefundWithToken($order, $token);
                break;
            case self::PAYMENT_MESHULAM:
                $paymentResult = $paymentSystem->makeRefund($studioSettings->MeshulamAPI, $studioSettings->MeshulamUserId, $transaction['YaadCode'], $transaction['PayToken'], $order->Amount, $transaction['MeshulamPageCode']);
                break;
            case self::PAYMENT_TRANZILA:
                $paymentResult = $paymentSystem->makeRefundWithToken($order, $token, $transaction['YaadCode'], $transaction['ACode']);
                break;
            default:
                throw new LogicException('Unknown payment method');
        }

        return $paymentResult;
    }
}
