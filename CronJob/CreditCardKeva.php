<?php

$_SERVER['DOCUMENT_ROOT'] = getenv('HOME') . '/public_html';

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/initcron.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/helpers/GroupNumberHelper.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/Client.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/CompanyProductSettings.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/DocsClientActivities.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/Item.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/ItemRoles.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/Notificationcontent.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/OrderLogin.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/Token.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/Utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/KevaActivity.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/ClientActivities.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/Classes/LoginPushNotifications.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/services/OrderService.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/services/PaymentService.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/services/LoggerService.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/office/services/payment/PaymentStatusList.php';

$filename = basename(__FILE__, '.php');
$Cron = new CronManager($filename);
$id = $Cron->start();


set_time_limit(0);
ini_set("memory_limit", "-1");

$StatusreditCard = PaymentStatusList::getList();


$Vaild_TypeOption = array(
    1 => "day",
    2 => "week",
    3 => "month",
    4 => "year"
);


$ThisDate = date('Y-m-d');
$ThisDay = date('l');
$ThisTime = date('H:i:s');


//////////////////////////////////////////////////////////////// שליחת חיוב הוראת קבע אשראי ///////////////////////////////////////////////////////
try {

    $GetClientCreditKevas = DB::table('payment')
        ->where('Status', '=', 0)
        ->where('ActStatus', '=', 0)
        ->where('Date', '=', $ThisDate)
        ->where('workerStatus', '=', 0)
        ->orderBy('id', 'desc')
        ->limit(100)
        ->get();

    //// worker status
    $workerArr = [];
    foreach ($GetClientCreditKevas as $creditPayment) {
        $workerArr[] = $creditPayment->id;
    }

    // worker start
    DB::table('payment')
        ->whereIn('id', $workerArr)
        ->where('Date', '=', date('Y-m-d'))
        ->update(['workerStatus' => 1]);

    foreach ($GetClientCreditKevas as $GetClientCreditKeva) {
        $L4digit = null;
        $CCode = null;
        $Bank = null;
        $Brand = null;
        $Issuer = null;
        $YaadCode = null;
        $ACode = null;
        $Payments = null;
        $PayToken = null;
        $BrandName = null;
        $tashType = null;
        $Err_Message = null;
        $MeshulamPageCode = null;

        $CheckSettings = DB::table('settings')->where('CompanyNum', '=', $GetClientCreditKeva->CompanyNum)->where('Status', '=', '0')->first();
        if ($CheckSettings) {

            $CpaType = 0;
            $CCode = '999';
            $PayToken = null;
            $InsertTransaction = '0';
            $CompanyNum = $GetClientCreditKeva->CompanyNum;
            $ClientId = $GetClientCreditKeva->ClientId;

            $GetKevaInfo = DB::table('paytoken')
                ->where('id', '=', $GetClientCreditKeva->KevaId)
                ->where('CompanyNum', '=', $CompanyNum)
                ->first();

            /////  בדיקת חיובים כפולים
            $CheckKevaInfo = DB::table('payment')->where('KevaId', '=', $GetClientCreditKeva->KevaId)
                ->where('ClientId', '=', $ClientId)
                ->where('Amount', '=', $GetClientCreditKeva->Amount)
                ->where('CompanyNum', '=', $CompanyNum)
                ->where('Date', '=', $ThisDate)
                ->whereIn('Status', [1,2,4])
                ->first();


            if (!empty($CheckKevaInfo) || empty($GetKevaInfo) || $GetKevaInfo->Status != 0) {

                DB::table('payment')
                    ->where('id', $GetClientCreditKeva->id)
                    ->where('ClientId', '=', $GetClientCreditKeva->ClientId)
                    ->where('CompanyNum', '=', $GetClientCreditKeva->CompanyNum)
                    ->update(array('Status' => 3, 'ActStatus' => 1));

            } else {

                $ClientId = $GetKevaInfo->ClientId;
                $CompanyNum = $GetKevaInfo->CompanyNum;
                $TokenId = $GetKevaInfo->TokenId;
                $ItemId = $GetKevaInfo->ItemId;
                $AutoPaymentId = $GetKevaInfo->id;


                $Date = date('Y-m-d');
                $Time = '06:00:00';
                $Dates = date('Y-m-d H:i:s');

                $TypeKeva = $GetClientCreditKeva->TypeKeva;
                $NumPayment = $GetClientCreditKeva->NumPayment;

                $GetNextDate = DB::table('payment')->where('ClientId', '=', $ClientId)->where('KevaId', '=', $GetClientCreditKeva->KevaId)->where('Status', '=', '0')->where('NumPayment', '>', $NumPayment)->where('ActStatus', '=', '0')->where('CompanyNum', '=', $CompanyNum)->orderBy('NumPayment', 'ASC')->first();

                if ($GetNextDate) {
                    $FinalNextPayment = $GetNextDate->Date;
                } else {
                    $FinalNextPayment = null;
                }


                $CpaType = $CheckSettings->CpaType;
                $TypeShva = $CheckSettings->TypeShva;
                $MeshulamAPI = $CheckSettings->MeshulamAPI;
                $MeshulamUserId = $CheckSettings->MeshulamUserId;
                $LiveMeshulam = $CheckSettings->LiveMeshulam;


                $AppSettings = DB::table('appsettings')->where('CompanyNum', '=', $CompanyNum)->first();
                $ClientInfo = new Client($ClientId);
                if (!$ClientInfo || $ClientInfo->Status == 1) {
                    DB::table('payment')
                        ->where('id', $GetClientCreditKeva->id)
                        ->where('ClientId', '=', $GetClientCreditKeva->ClientId)
                        ->where('CompanyNum', '=', $GetClientCreditKeva->CompanyNum)
                        ->update(['Status' => 3, 'ActStatus' => 1]);
                    continue;
                }
                $TokenInfo = DB::table('token')->where('id', '=', $TokenId)->where('CompanyNum', $CompanyNum)->first();

                $Brands = $ClientInfo->Brands;
                //// בדיקת מסוף לסניף שונה
                $YaadNumber = $CheckSettings->YaadNumber;
                ///// minor client section
                if ($ClientInfo->parentClientId != 0) {
                    $parentClient = DB::table('client')->where('id', '=', $ClientInfo->parentClientId)->where('CompanyNum', '=', $CompanyNum)->first();
                    if (!empty($parentClient)) {
                        $ClientInfo->ContactMobile = empty($ClientInfo->ContactMobile) ? $parentClient->ContactMobile : $ClientInfo->ContactMobile;
                        $ClientInfo->Email = empty($ClientInfo->Email) ? $parentClient->Email : $ClientInfo->Email;
                    }
                }
                ///// end minor client section

                if ($ClientInfo->Brands != 0) {
                    $BrandCheckYaadNumber = DB::table('brands')->where('id', '=', $ClientInfo->Brands)->where('CompanyNum', $CompanyNum)->first();
                    if ($BrandCheckYaadNumber && $BrandCheckYaadNumber->YaadNumber != 0) {
                        $YaadNumber = $BrandCheckYaadNumber->YaadNumber;
                    }

                }

                if ($YaadNumber == '' || $YaadNumber == '0') {
                    $YaadNumber = $CheckSettings->YaadNumber;
                }

                $paymentSystem = PaymentService::getPaymentSystemByType($CheckSettings->TypeShva);

                $order = OrderService::createOrder($ClientInfo, $GetClientCreditKeva->Amount, $GetKevaInfo->Tash, OrderLogin::TYPE_CRON_CREDIT_CARD_KEVA);

                $order->PaymentMethod = PaymentService::getPaymentMethodByType($CheckSettings->TypeShva);
                $order->save();

                $tokenModel = Token::getById($TokenId);
                if (!$tokenModel) {
                    $paymentSystem::reset();
                    throw new Exception('Wrong Token: ' . $TokenId . ' for paying by Horaat Keva #' . $GetKevaInfo->id);
                }

                $order->TokenId = $tokenModel->id;
                $order->save();

                if ($GetKevaInfo->tashType == '0' || $GetKevaInfo->tashType == '1') {
                    $tashType = '1';
                } else {
                    $tashType = $GetKevaInfo->tashType;
                }

                if ($GetKevaInfo->tashType == '0') {
                    $tashTypeDB = '1';
                } elseif ($GetKevaInfo->tashType == '1') {
                    $tashTypeDB = '2';
                } elseif ($GetKevaInfo->tashType == '2') {
                    $tashTypeDB = '4';
                } elseif ($GetKevaInfo->tashType == '6') {
                    $tashTypeDB = '3';
                } else {
                    $tashTypeDB = '5';
                }

                try {
                    $paymentResult = $paymentSystem->makePaymentWithToken($order, $tokenModel, $tashType, $GetKevaInfo->Tash);

                    // fix for resetting instance in loop
                    $paymentSystem::reset();

                    $L4digit = $paymentResult['L4digit'];
                    $CCode = $paymentResult['CCode'];
                    $Bank = $paymentResult['Bank'];
                    $Brand = $paymentResult['Brand'];
                    $Issuer = $paymentResult['Issuer'];

                    $YaadCode = $paymentResult['YaadCode'];
                    $ACode = $paymentResult['ACode'];
                    $Payments = $paymentResult['Payments'];
                    $PayToken = $paymentResult['PayToken'];
                    $BrandName = $paymentResult['BrandName'];
                    $tashType = $paymentResult['tashTypeDB'];
                    $MeshulamPageCode = $paymentResult['MeshulamPageCode'] ?? null;

                    $Err_Message = lang('transaction_approved_meshulam');

                    $transaction = new Transaction();
                    $transaction->CompanyNum = $CompanyNum;
                    $transaction->ClientId = $ClientInfo->id;
                    $transaction->UpdateTransactionDetails = serialize($paymentResult);
                    $transaction->UserId = 0;
                    $transaction->Transaction = $YaadCode;
                    $transaction->save();

                    $order->Status = OrderLogin::STATUS_PAID;
                    $order->TransactionId = $transaction->id;
                    $order->save();
                } catch (\Throwable $e) {
                    LoggerService::error('Error: ' . $e->getMessage() . ' while paying by Horaat Keva #' . $GetKevaInfo->id . ', Token: ' . $TokenId, LoggerService::CATEGORY_CRON_HORAAT_KEVA);

                    DB::table('transaction_error')->insertGetId(
                        array('CompanyNum' => $CompanyNum, 'ClientId' => $ClientInfo->id, 'UpdateTransactionDetails' => $e->getMessage(), 'UserId' => '0'));

                    // fix for resetting instance in loop
                    $paymentSystem::reset();

                    // $e->getMessage = CCode for YAAD and = message for MESHULAM
                    $CCode = $Err_Message = $e->getMessage();
                }

                /// מחולל מספר מסמך
                $RandomTokenNumber = $GetClientCreditKeva->RandomUrl;

                if ($CCode == '0') {

                    $LastPayment = date('Y-m-d');
                    $KevaStatus = '1';

                    DB::table('paytoken')
                        ->where('id', $AutoPaymentId)
                        ->where('ClientId', '=', $ClientId)
                        ->where('CompanyNum', '=', $CompanyNum)
                        ->update(array('LastPayment' => $LastPayment, 'NextPayment' => $FinalNextPayment));

                    DB::table('payment')
                        ->where('id', $GetClientCreditKeva->id)
                        ->where('ClientId', '=', $ClientId)
                        ->where('CompanyNum', '=', $CompanyNum)
                        ->update(array('Status' => $KevaStatus));

                    //// הנפקת קבלה על תשלום מוצלח

                    $TypeHeader = '400'; /// מסמך קבלה בלבד
                    $UserDate = date('Y-m-d');
                    $ManualInvoice = '0';
                    $DocConvert = '0';
                    $PaymentRole = '1';
                    $Dates = date('Y-m-d H:i:s');
                    $PaymentTime = $UserDate;
                    $DocDate = date('Y-m-d');
                    $DocMonth = date("m", strtotime($UserDate));
                    $DocYear = date("Y", strtotime($UserDate));
                    $DocTime = date('H:i:s');

                    //// בדיקת סניפים
                    if ($CheckSettings->BrandsMain != '0' && $CheckSettings->MainAccounting == '1') {
                        $TrueCompanyNum = $CheckSettings->BrandsMain;
                    } else {
                        $TrueCompanyNum = $CompanyNum;
                    }


                    /// סוג מסמך וקבלת ID
                    $GetDocsId = DB::table('docstable')->where('TypeHeader', '=', $TypeHeader)->where('TrueCompanyNum', '=', $TrueCompanyNum)->first();
                    $TypeDoc = $GetDocsId->id;

                    /// בדיקת מספור מסמך + תאריך אחרון
                    $DocsTableNew = DB::table('docstable')->where('id', '=', $TypeDoc)->where('TrueCompanyNum', '=', $TrueCompanyNum)->first();

                    $DocsCountGets = DB::table('docs')->where('TrueCompanyNum', '=', $TrueCompanyNum)->where('TypeHeader', '=', $TypeHeader)->orderBy('id', 'DESC')->first();
                    if (@$DocsCountGets->TypeNumber == '') {
                        $TypeNumber = $DocsTableNew->TypeNumber;
                    } else {
                        $TypeNumber = $DocsCountGets->TypeNumber + 1;
                    }

                    /// סוג מסמך וקבלת ID
                    $GetDocsId = DB::table('docstable')->where('TypeHeader', '=', $TypeHeader)->where('CompanyNum', '=', $CompanyNum)->first();
                    $TypeDoc = $GetDocsId->id;
                    $DocsTableNew = DB::table('docstable')->where('id', '=', $TypeDoc)->where('CompanyNum', '=', $CompanyNum)->first();


                    if ($ClientInfo->Company == '') {
                        $Company = $ClientInfo->CompanyName;
                    } else {
                        $Company = $ClientInfo->Company;
                    }

                    $GetAmount = $GetClientCreditKeva->Amount;

                    /// מחולל מספר מסמך
                    $RandomNumber = GroupNumberHelper::generate();

                    $DocsGetAmount = '-' . $GetAmount;
                    $City = '';

                    if ($ClientInfo->City != '0') {
                        $BusinessSettingsCity = DB::table('cities')->where('CityId', '=', $ClientInfo->City)->first();
                        $City = @$BusinessSettingsCity->City;
                    }
                    if ($ClientInfo->Street != '0') {
                        $BusinessSettingsStreet = DB::table('street')->where('id', '=', $ClientInfo->Street)->first();
                        $Street = @$BusinessSettingsStreet->Street;
                    } else {
                        $Street = htmlentities($ClientInfo->StreetH);
                    }


                    $ItemsInfo = DB::table('items')->where('id', '=', $ItemId)->where('CompanyNum', '=', $CompanyNum)->first();

                    $ActivityArr = ["data" => []];
                    if ($GetKevaInfo->MultiItems) {
                        $itemIds = explode(',', $GetKevaInfo->MultiItems);
                    }
                    else {
                        $itemIds = [$ItemId];
                    }

                    foreach ($itemIds as $id) {
                        $itemObj = Item::find($id);
                        $ActivityArr['data'][] = [
                            'ItemText' => $itemObj->__get('ItemName'),
                            'ItemId' => $id,
                            'OldBalanceMoney' => $GetAmount,
                            'NewAmount' => 0
                        ];
                    }
                    $ActivityJson = json_encode($ActivityArr);

                    $order->Description = $ActivityJson;
                    $order->save();

                    $docsRemarks = !empty($GetDocsId) && !empty($GetDocsId->DocsRemarks) ? $GetDocsId->DocsRemarks : '';

                    $DocId = DB::table('docs')->insertGetId(array(
                        'CompanyNum' => $CompanyNum,
                        'Brands' => $Brands,
                        'TrueCompanyNum' => $TrueCompanyNum,
                        'TypeDoc' => $TypeDoc,
                        'TypeHeader' => $DocsTableNew->TypeHeader,
                        'TypeNumber' => $TypeNumber,
                        'ClientId' => $ClientId,
                        'UserDate' => $UserDate,
                        'Dates' => $Dates,
                        'Amount' => $DocsGetAmount,
                        'Vat' => '0',
                        'VatAmount' => '0',
                        'DiscountType' => '1',
                        'Discount' => '0',
                        'DiscountAmount' => '0',
                        'PaymentRole' => '1',
                        'Company' => htmlentities($Company),
                        'CompanyId' => $ClientInfo->CompanyId,
                        'ContactName' => htmlentities($ClientInfo->CompanyName),
                        'Mobile' => $ClientInfo->ContactMobile,
                        'Phone' => $ClientInfo->ContactPhone,
                        'Fax' => $ClientInfo->ContactFax,
                        'Email' => $ClientInfo->Email,
                        'UserId' => '0',
                        'ManualInvoice' => $ManualInvoice,
                        'DocConvert' => $DocConvert,
                        'PaymentTime' => $PaymentTime,
                        'BalanceAmount' => '0',
                        'Street' => $Street,
                        'Number' => $ClientInfo->Number,
                        'PostCode' => $ClientInfo->PostCode,
                        'City' => $City,
                        'Accounts' => $DocsTableNew->Accounts,
                        'DocDate' => $DocDate,
                        'DocMonth' => $DocMonth,
                        'DocYear' => $DocYear,
                        'DocTime' => $DocTime,
                        'RandomUrl' => $RandomNumber,
                        'ActivityJson' => $ActivityJson,
                        'Status' => '1',
                        'AutoPayment' => '1',
                        'AutoPaymentId' => $AutoPaymentId,
                        'TypeShva' => $TypeShva,
                        'Remarks' => $docsRemarks,
                        'CpaType' => $CpaType,
                    ));

                    $CreditType = lang('recurring_transaction_credit');

                    /// תשלום אחד
                    if ($Payments == '1' || $CpaType == '1') {

                        $CreditDate = date('Y-m-d');

                        $DocsList = DB::table('docs_payment')->insertGetId(
                            array(
                                'CompanyNum' => $CompanyNum,
                                'Brands' => $Brands,
                                'TrueCompanyNum' => $TrueCompanyNum,
                                'TypeDoc' => $TypeDoc,
                                'TypeHeader' => $TypeHeader,
                                'TypeNumber' => $TypeNumber,
                                'DocsId' => $DocId,
                                'ClientId' => $ClientId,
                                'TypePayment' => '3',
                                'Amount' => $GetClientCreditKeva->Amount,
                                'L4digit' => $L4digit,
                                'YaadCode' => $YaadCode,
                                'CCode' => $CCode,
                                'ACode' => $ACode,
                                'Bank' => $Bank,
                                'Payments' => $Payments,
                                'Brand' => $Brand,
                                'BrandName' => $BrandName,
                                'Issuer' => $Issuer,
                                'tashType' => $tashTypeDB,
                                'CheckDate' => $CreditDate,
                                'Dates' => $Dates,
                                'UserId' => '0',
                                'UserDate' => $UserDate,
                                'DocDate' => $DocDate,
                                'DocMonth' => $DocMonth,
                                'DocYear' => $DocYear,
                                'DocTime' => $DocTime,
                                'CreditType' => $CreditType,
                                'ActivityJson' => $ActivityJson,
                                'PayToken' => $PayToken,
                                'TransactionId' => $transaction->id ?? 0,
                                'MeshulamPageCode' => $MeshulamPageCode,
                            ));


                    } else {

                        $Amount = $GetClientCreditKeva->Amount;
                        $Money = $Amount;
                        $Payment = $Payments;

                        $MyMoney = $Money / $Payment;
                        $MyMoney = number_format((float)$MyMoney, 2, '.', '');

                        list($whole, $decimal) = explode('.', $MyMoney);

                        $CehckPayment = $whole * ($Payment - 1);
                        $FirstPayment = $Money - $CehckPayment;
                        $FirstPayment = number_format((float)$FirstPayment, 2, '.', '');
                        $SecendPayment = $whole;
                        $SecendPayment = number_format((float)$SecendPayment, 2, '.', '');

                        $count = $Payments;
                        for ($i = 1; $i <= $count; $i++) {

                            if ($i == 1) {
                                $FixAmount = $FirstPayment;
                            } else {
                                $FixAmount = $SecendPayment;
                            }

                            $PaymentsNew = $i;

                            $add = $i - 1;
                            $AddDate = '+' . $add . ' month';
                            $CreditDate = date('Y-m-d', strtotime($AddDate, strtotime($UserDate)));

                            $DocsList = DB::table('docs_payment')->insertGetId(array(
                                'CompanyNum' => $CompanyNum,
                                'Brands' => $Brands,
                                'TrueCompanyNum' => $TrueCompanyNum,
                                'TypeDoc' => $TypeDoc,
                                'TypeHeader' => $TypeHeader,
                                'TypeNumber' => $TypeNumber,
                                'DocsId' => $DocId,
                                'ClientId' => $ClientId,
                                'TypePayment' => '3',
                                'Amount' => $FixAmount,
                                'L4digit' => $L4digit,
                                'YaadCode' => $YaadCode,
                                'CCode' => $CCode,
                                'ACode' => $ACode,
                                'Bank' => $Bank,
                                'Payments' => $PaymentsNew,
                                'Brand' => $Brand,
                                'BrandName' => $BrandName,
                                'Issuer' => $Issuer,
                                'tashType' => $tashTypeDB,
                                'CheckDate' => $CreditDate,
                                'Dates' => $Dates,
                                'UserId' => '0',
                                'UserDate' => $UserDate,
                                'DocDate' => $DocDate,
                                'DocMonth' => $DocMonth,
                                'DocYear' => $DocYear,
                                'DocTime' => $DocTime,
                                'CreditType' => $CreditType,
                                'ActivityJson' => $ActivityJson,
                                'PayToken' => $PayToken,
                                'TransactionId' => $transaction->id ?? 0,
                                'MeshulamPageCode' => $MeshulamPageCode,
                            ));

                        }

                    }

                    //////  עדכון טבלת דוח מכירות

                    DB::table('docs2item')->insertGetId(array(
                        'CompanyNum' => $CompanyNum,
                        'TrueCompanyNum' => $TrueCompanyNum,
                        'Brands' => $Brands,
                        'ClientId' => $ClientId,
                        'ItemId' => $ItemId,
                        'DocsId' => $DocId,
                        'Amount' => $GetAmount,
                        'Department' => $ItemsInfo->Department,
                        'MemberShip' => $ItemsInfo->MemberShip,
                        'ItemName' => htmlentities($ItemsInfo->ItemName),
                        'UserDate' => $UserDate
                    ));


                    //// הכנסת מנוי לכרטיס לקוח ועדכון פרטים

                    $CountPayments = DB::table('payment')
                        ->where('CompanyNum', '=', $CompanyNum)
                        ->where('ClientId', '=', $ClientId)
                        ->where('KevaId', '=', $GetClientCreditKeva->KevaId)
                        ->where('Status', '=', '1')
                        ->where('ActStatus', '=', '0')
                        ->count();

                    if ($TypeKeva == 0 && $CountPayments >= 2) {

                        $Vat = $CheckSettings->Vat;
                        $Items = $ItemId;
                        $Vaild_LastCalss = '1';

                        $Today = date('Y-m-d');
                        $StartDate = date('Y-m-d');

                        ///// בדיקת תאריך הצטרפות

                        $CountMembership = DB::table('client_activities')->where('ClientId', $ClientId)->where('CompanyNum', $CompanyNum)->whereIn('Department', array('1,2'))->where('Status', '!=', '2')->count();

                        if ($CountMembership == '0') {

                            DB::table('client')
                                ->where('id', $ClientId)
                                ->where('CompanyNum', $CompanyNum)
                                ->update(array('JoinDate' => $StartDate));


                        }


                        /// קליטת פרטי פעילות

                        $ItemsInfo = DB::table('items')->where('CompanyNum', '=', $CompanyNum)->where('id', '=', $Items)->first();

                        $ItemText = $ItemsInfo->ItemName;
                        $ItemPrice = $ItemsInfo->ItemPrice;
                        $ItemPriceVat = $ItemsInfo->ItemPriceVat;

                        $Department = $ItemsInfo->Department; // חוק מנוי
                        $MemberShip = $ItemsInfo->MemberShip; // סוג מנוי

                        $Vaild = $ItemsInfo->Vaild; // חישוב תוקף
                        $Vaild_Type = $ItemsInfo->Vaild_Type; // סוג חישוב
                        $LimitClass = $ItemsInfo->LimitClass; // הגבלת שיעורים

                        $CompanyProductSettings = (new CompanyProductSettings())->getSingleByCompanyNum($CompanyNum);
                        $NotificationDays = $CompanyProductSettings->NotificationDays ?? 0;

                        $BalanceClass = $ItemsInfo->BalanceClass; // כמות שיעורים
                        $MinusCards = $CompanyProductSettings->offsetMemberships ?? 1; // קיזוז מכרטיסיה קודמת
                        $StartTime = $ItemsInfo->StartTime; // הגבלת הזמנת שיעורים
                        $EndTime = $ItemsInfo->EndTime; // הגבלת הזמנת שיעורים
                        $CancelLImit = $ItemsInfo->CancelLImit; // ביטול הגבלה
                        $ClassSameDay = $ItemsInfo->ClassSameDay; // הזמנת שיעור באותו היום
                        $FreezMemberShip = $ItemsInfo->FreezMemberShip; // ניתן להקפאה?
                        $FreezMemberShipDays = $ItemsInfo->FreezMemberShipDays; // מספר ימים מקסימלי להקפאה
                        $FreezMemberShipCount = $ItemsInfo->FreezMemberShipCount; // מספר פעמים שניתן להקפיא מנוי
                        $TrueBalanceClass = $BalanceClass;
                        $BalanceValueLog = NULL;

                        $LimitClassMorning = $ItemsInfo->LimitClassMorning;
                        $LimitClassEvening = $ItemsInfo->LimitClassEvening;
                        $LimitClassMonth = $ItemsInfo->LimitClassMonth;


                        $MemberShipRule = '{"data": [';
                        $MemberShipRule .= '{"LimitClass": "' . $LimitClass . '", "NotificationDays": "' . $NotificationDays . '", "StartTime": "' . $StartTime . '", "EndTime": "' . $EndTime . '", "CancelLImit": "' . $CancelLImit . '", "ClassSameDay": "' . $ClassSameDay . '", "FreezMemberShip": "' . $FreezMemberShip . '", "FreezMemberShipDays": "' . $FreezMemberShipDays . '", "FreezMemberShipCount": "' . $FreezMemberShipCount . '", "LimitClassMorning": "' . $LimitClassMorning . '", "LimitClassEvening": "' . $LimitClassEvening . '", "LimitClassMonth": "' . $LimitClassMonth . '"}';
                        $MemberShipRule .= ']}';


                        // מנוי תקופתי
                        if ($Department == '1') {


                            $Vaild_TypeOptions = @$Vaild_TypeOption[$Vaild_Type];
                            $ItemsTime = '+' . $Vaild . ' ' . $Vaild_TypeOptions;

                            $time = strtotime($StartDate);
                            $ClassDate = date("Y-m-d", strtotime($ItemsTime, $time));


                        } // כרטיסיה
                        elseif ($Department == '2') {

                            $ClassDate = NULL;

                            /// חישוב תוקף
                            if ($Vaild != '0') {

                                $Vaild_TypeOptions = @$Vaild_TypeOption[$Vaild_Type];
                                $ItemsTime = '+' . $Vaild . ' ' . $Vaild_TypeOptions;

                                $time = strtotime($StartDate);
                                $ClassDate = date("Y-m-d", strtotime($ItemsTime, $time));

                            }

                        } // התנסות
                        elseif ($Department == '3') {
                            $ClassDate = NULL;
                            $MemberShipRule = NULL;
                            $LimitClass = '999';


                        } // פריט כללי
                        elseif ($Department == '4') {
                            $ClassDate = NULL;
                            $MemberShipRule = NULL;
                            $LimitClass = '0';
                            $BalanceClass = '0';
                        }

                        // מספור מספר המנויים שהלקוח רכש
                        $CardNum = DB::table('client_activities')->where('CompanyNum', '=', $CompanyNum)->where('ClientId', '=', $ClientId)->count();
                        $CardNumber = $CardNum + 1;


                        /// הכנסת נתונים ועדכון טבלאות

                        $UserId = '0';
                        $Dates = date('Y-m-d G:i:s');
                        $VatAmount = $ItemPrice - $ItemPriceVat;

                        $Vaild_TypeOptions = @$Vaild_TypeOption['1'];
                        $ItemsTime = '-' . $NotificationDays . ' ' . $Vaild_TypeOptions;

                        $time = strtotime($ClassDate);
                        $NotificationDate = date("Y-m-d", strtotime($ItemsTime, $time));

                        if ($NotificationDays == '0') {
                            $NotificationDate = NULL;
                        }

                        if ($TypeKeva == '0') {
                            $KevaAction = '1';
                        } else {
                            $KevaAction = '0';
                        }

                        $ReceiptIdJson = '{"data": [';
                        $ReceiptIdJson .= '{"DocId": "' . $DocId . '"}';
                        $ReceiptIdJson .= ']}';

                        $AddClientActivity = DB::table('client_activities')->insertGetId(array(
                            'CompanyNum' => $CompanyNum,
                            'Brands' => $Brands,
                            'CardNumber' => $CardNumber,
                            'ClientId' => $ClientId,
                            'Department' => $Department,
                            'MemberShip' => $MemberShip,
                            'ItemId' => $Items,
                            'ItemText' => $ItemText,
                            'ItemPrice' => $ItemPrice,
                            'ItemPriceVat' => $ItemPriceVat,
                            'ItemPriceVatDiscount' => $ItemPriceVat,
                            'Vat' => $Vat,
                            'VatAmount' => $VatAmount,
                            'StartDate' => $StartDate,
                            'VaildDate' => $ClassDate,
                            'TrueDate' => $ClassDate,
                            'BalanceValue' => $BalanceClass,
                            'TrueBalanceValue' => $BalanceClass,
                            'ActBalanceValue' => $BalanceClass,
                            'LimitClass' => $LimitClass,
                            'Dates' => $Dates,
                            'UserId' => $UserId,
                            'BalanceMoney' => '0.00',
                            'MemberShipRule' => $MemberShipRule,
                            'NotificationDays' => $NotificationDate,
                            'KevaAction' => $KevaAction,
                            'InvoiceId' => $DocId,
                            'ReceiptId' => $ReceiptIdJson
                        ));

                        DocsClientActivities::saveRelation($DocId, $AddClientActivity);

                        ///// מעבר ניקובים+שיעורים ממנוי ישן לחדש

                        $MembershipType = $AppSettings->MembershipType ?? 1;
                        $CheckItemsRoleTwo = DB::table('items_roles')->where('CompanyNum', '=', $CompanyNum)->where('ItemId', '=', $Items)->first();
                        $TrueClasessFinal = $CheckItemsRoleTwo->GroupId ?? '';

                        $data = [
                            "CompanyNum" => $CompanyNum,
                            "ClientId" => $ClientId,
                            "ActivityId" => $AddClientActivity,
                            "MemberShip" => $MemberShip,
                            "MembershipType" => $MembershipType,
                            "MinusCards" => $MinusCards,
                            "Department" => $Department,
                            "TrueClasessFinal" => $TrueClasessFinal,
                            "BalanceClass" => $BalanceClass,
                            "StartDate" => $StartDate
                        ];
                        (new ClientActivities())->moveClassesToNewActivity($data);


                        //// עדכון חוב ללקוח
                        $ClientInfo->updateBalanceAmount();

                        $MemberShipText = '{"data": [';
                        $Taski = '1';
                        $GetTasks = DB::table('client_activities')
                            ->where('TrueDate', '>=', date('Y-m-d'))->where('Department', '=', '1')->where('ClientId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')
                            ->Orwhere('TrueBalanceValue', '>=', '1')->whereNull('TrueDate')->where('Department', '=', '2')->where('ClientId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')
                            ->Orwhere('TrueBalanceValue', '>=', '1')->where('TrueDate', '>=', date('Y-m-d'))->where('Department', '=', '2')->where('ClientId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')
                            ->Orwhere('TrueBalanceValue', '>=', '1')->whereNull('TrueDate')->where('Department', '=', '3')->where('ClientId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')
                            ->Orwhere('TrueBalanceValue', '>=', '1')->where('TrueDate', '>=', date('Y-m-d'))->where('Department', '=', '3')->where('ClientId', '=', $ClientId)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')
                            ->orderBy('CardNumber', 'ASC')->get();
                        $TaskCount = count($GetTasks);

                        foreach ($GetTasks as $GetTask) {

                            if ($Taski < $TaskCount) {
                                $MemberShipText .= '{"ItemText": "' . $GetTask->ItemText . '", "TrueDate": "' . $GetTask->TrueDate . '", "TrueBalanceValue": "' . $GetTask->TrueBalanceValue . '", "Id": "' . $GetTask->id . '", "LimitClass": "' . $GetTask->LimitClass . '"},';
                            } else {
                                $MemberShipText .= '{"ItemText": "' . $GetTask->ItemText . '", "TrueDate": "' . $GetTask->TrueDate . '", "TrueBalanceValue": "' . $GetTask->TrueBalanceValue . '", "Id": "' . $GetTask->id . '", "LimitClass": "' . $GetTask->LimitClass . '"}';
                            }


                            ++$Taski;
                        }
                        $MemberShipText .= ']}';

                        DB::table('client')
                            ->where('id', $ClientId)
                            ->where('CompanyNum', $CompanyNum)
                            ->update(array('MemberShipText' => $MemberShipText));


                        //// סגירת מנוי קודם
                        DB::table('boostapp.client_activities')
                            ->where('ClientId', $ClientId)
                            ->where('CompanyNum', $CompanyNum)
                            ->whereIn('Department', [1,2,3])
                            ->where('Status', '=', 0)
                            ->where('TrueDate', '<=', date('Y-m-d'))
                            ->update(array('Status' => 3));

                        DB::table('boostapp.client_activities')
                            ->where('ClientId', $ClientId)
                            ->where('CompanyNum', $CompanyNum)
                            ->whereIn('Department', [2,3])
                            ->where('Status', '=', 0)
                            ->where('TrueBalanceValue', '<=', 0)
                            ->update(array('Status' => 3));


                        /// עדכון ספירה לסוג המנוי
                        if (in_array($Department, [1, 2, 3])) {


                            if ($Department == '1') {

                                $GetActivityCount = DB::table('client_activities')
                                    ->where('TrueDate', '>=', date('Y-m-d'))
                                    ->where('StartDate', '<=', date('Y-m-d'))
                                    ->where('MemberShip', '=', $MemberShip)
                                    ->where('Department', '=', 1)
                                    ->where('CompanyNum', '=', $CompanyNum)
                                    ->where('Status', '=', 0)
                                    ->where('ClientStatus', '=', 0)
                                    ->where('FirstDateStatus', '=', '0')->count();

                            } elseif ($Department == '2') {

                                $GetActivityCount = DB::table('client_activities')->where('ActBalanceValue', '>=', '1')->where('StartDate', '<=', date('Y-m-d'))->whereNull('TrueDate')->where('Department', '=', '2')
                                    ->where('MemberShip', '=', $MemberShip)->where('CompanyNum', '=', $CompanyNum)->where('Status', '=', '0')->where('ClientStatus', '=', '0')->where('FirstDateStatus', '=', '0')
                                    ->Orwhere('ActBalanceValue', '>=', '1')->where('StartDate', '<=', date('Y-m-d'))->where('ClientStatus', '=', '0')->where('FirstDateStatus', '=', '0')->where('TrueDate', '>=', date('Y-m-d'))->where('Department', '=', '2')->where('MemberShip', '=', $MemberShip)->where('CompanyNum', '=', $CompanyNum)
                                    ->where('Status', '=', '0')->count();

                            } elseif ($Department == '3') {

                                $GetActivityCount = DB::table('client_activities')
                                    ->where('CompanyNum', $CompanyNum)
                                    ->where('StartDate', '<=', date('Y-m-d'))
                                    ->where('Department', '3')
                                    ->where('MemberShip', $MemberShip)
                                    ->where('ActBalanceValue', '>=', '1')
                                    ->where('ClientStatus', '=', '0')
                                    ->where('FirstDateStatus', '=', '0')
                                    ->where('Status', '=', '0')->count();

                            }


                            DB::table('membership_type')
                                ->where('id', $MemberShip)
                                ->where('CompanyNum', $CompanyNum)
                                ->update(array('Count' => $GetActivityCount));

                        }


                        if ($Department == 1 || $Department == 2) {

                            $GetClasess = DB::table('classstudio_act')
                                ->where('CompanyNum', $CompanyNum)
                                ->where('FixClientId', $ClientId)
                                ->where('ClassDate', '>=', $StartDate)
                                ->whereIn('Status', array(12, 9))
                                ->get();

                            foreach ($GetClasess as $GetClases) {

                                $TrueClasess = '';
                                $TrueClasessFinal = '';
                                $ClassInfo = DB::table('classstudio_date')
                                    ->where('id', '=', $GetClases->ClassId)
                                    ->where('Status', '=', '0')
                                    ->where('CompanyNum', '=', $CompanyNum)
                                    ->first();
                                if($ClassInfo) {
                                    $CheckItemsRole = ItemRoles::getFirstGroupClassByItemIdAndClassType($CompanyNum, $Items, $ClassInfo->ClassNameType);
                                    if ($CheckItemsRole) {
                                        $GroupId = $CheckItemsRole->GroupId;
                                        $TrueClasessFinal = $CheckItemsRole->GroupId;
                                        $TrueClasess = $CheckItemsRole->Class;
                                    }
                                }
                                if ($TrueClasessFinal != '') {

                                    if ($GetClases->FixClientId == $ClientId) {
                                        DB::table('classstudio_act')
                                            ->where('id', $GetClases->id)
                                            ->where('CompanyNum', $CompanyNum)
                                            ->update(array(
                                                'ClientId' => $ClientId,
                                                'TrueClientId' => '0',
                                                'ClientActivitiesId' => $AddClientActivity,
                                                'TrueClasess' => $TrueClasessFinal,
                                                'Department' => $Department,
                                                'MemberShip' => $MemberShip));
                                    } else {
                                        DB::table('classstudio_act')
                                            ->where('id', $GetClases->id)
                                            ->where('CompanyNum', $CompanyNum)
                                            ->update(array(
                                                'ClientId' => $ClientId,
                                                'TrueClientId' => $GetClases->FixClientId,
                                                'ClientActivitiesId' => $AddClientActivity,
                                                'TrueClasess' => $TrueClasessFinal,
                                                'Department' => $Department,
                                                'MemberShip' => $MemberShip));
                                    }

                                    //// עדכון מנוי שיבוץ קבוע
                                    DB::table('classstudio_dateregular')
                                        ->where('id', $GetClases->RegularClassId)
                                        ->where('CompanyNum', $CompanyNum)
                                        ->update(array('ClientActivitiesId' => $AddClientActivity, 'MemberShipType' => $MemberShip));


                                }


                            }

                        }


                    } elseif($TypeKeva == 1 || ($TypeKeva == 0 && $CountPayments == 1)) {

                        $temp = KevaActivity::where('keva_id', $GetKevaInfo->id)->first();

                        if($temp) {
                            $activity = DB::table('client_activities')->where('id', $temp->client_activity_id)->first();
                            if($activity) {
                                $ReceiptIdJson = '{"data": [';
                                if (!empty($activity->ReceiptId)) {
                                    $Loops = json_decode($activity->ReceiptId, true);
                                    foreach ($Loops['data'] as $key => $val) {
                                        $DocIdDB = $val['DocId'];
                                        $ReceiptIdJson .= '{"DocId": "' . $DocIdDB . '"},';
                                    }
                                }
                                $ReceiptIdJson .= '{"DocId": "' . $DocId . '"}';
                                $ReceiptIdJson .= ']}';

                                $updateActivity = DB::table('client_activities')
                                    ->where('id', $activity->id)
                                    ->update(['ReceiptId' => $ReceiptIdJson]);

                                if($TypeKeva == 0 && $CountPayments == 1) {
                                    try {
                                        // move classes on paytoken first activity
                                        $CompanyProductSettings = (new CompanyProductSettings())->getSingleByCompanyNum($CompanyNum);
                                        $MinusCards = $CompanyProductSettings->offsetMemberships ?? 1;
                                        $MembershipType = $AppSettings->MembershipType ?? 1;
                                        $item = Item::find($ItemId);
                                        $BalanceClass = $item->BalanceClass ?? 1;
                                        $CheckItemsRoleTwo = DB::table('items_roles')->where('CompanyNum', '=', $CompanyNum)->where('ItemId', '=', $activity->ItemId)->first();
                                        $TrueClasessFinal = $CheckItemsRoleTwo->GroupId ?? '';

                                        $data = [
                                            "CompanyNum" => $CompanyNum,
                                            "ClientId" => $ClientId,
                                            "ActivityId" => $activity->id,
                                            "MemberShip" => $activity->MemberShip,
                                            "MembershipType" => $MembershipType,
                                            "MinusCards" => $MinusCards,
                                            "Department" => $activity->Department,
                                            "TrueClasessFinal" => $TrueClasessFinal,
                                            "BalanceClass" => $BalanceClass,
                                            "StartDate" => $activity->StartDate,
                                        ];
                                        (new ClientActivities())->moveClassesToNewActivity($data);

                                        DB::table('boostapp.client_activities')
                                            ->where('ClientId', $ClientId)
                                            ->where('CompanyNum', $CompanyNum)
                                            ->whereIn('Department', [1,2,3])
                                            ->where('Status', '=', 0)
                                            ->where('TrueDate', '<=', date('Y-m-d'))
                                            ->update(array('Status' => 3));

                                        DB::table('boostapp.client_activities')
                                            ->where('ClientId', $ClientId)
                                            ->where('CompanyNum', $CompanyNum)
                                            ->whereIn('Department', [2,3])
                                            ->where('Status', '=', 0)
                                            ->where('TrueBalanceValue', '<=', 0)
                                            ->update(array('Status' => 3));

                                    } catch(Exception $e) {
                                        LoggerService::error($e->getMessage(), LoggerService::CATEGORY_CRON_HORAAT_KEVA);
                                    }

                                }
                            }

                        }
                    }


                    $KevaStatus = '1';
                    $TryDate = NULL;
                    $LastDate = NULL;


                    /// הגדרת התראה
                    $CheckTime = date('H:i');
                    $Date = date('Y-m-d');
                    if ($CheckTime >= '00:00' && $CheckTime <= '08:00') {
                        $Time = '08:30:00';
                    } else {
                        $Time = date('H:i');
                    }
                    $Dates = date('Y-m-d H:i:s');

                    $Template = DB::table('notificationcontent')->where('CompanyNum', '=', $CompanyNum)->where('Type', '=', '14')->first();


                    $TemplateStatus = $Template->Status;
                    $TemplateSendOption = $Template->SendOption;
                    $SendStudioOption = $Template->SendStudioOption;
                    $Type = '0';

                    if ($TemplateSendOption == 'BA999') {
                        $Type = '0';
                    } elseif ($TemplateSendOption == 'BA000') {
                    } else {
                        $myArray = explode(',', $TemplateSendOption);
                        $Type2 = (in_array('2', $myArray)) ? '2' : '';
                        $Type1 = (in_array('1', $myArray)) ? '1' : '';
                        $Type0 = (in_array('0', $myArray)) ? '0' : '';

                        if ($Type2 != '') {
                            $Type = $Type2;
                        }
                        if ($Type1 != '') {
                            $Type = $Type1;
                        }
                        if ($Type0 != '') {
                            $Type = $Type0;
                        }

                    }


                    /// עדכון תבנית הודעה
                    $Content1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], $CheckSettings->AppName, $Template->Content);
                    $Content2 = str_replace(Notificationcontent::REPLACE_ARR["name_table"], $ClientInfo->CompanyName ?? '', $Content1);
                    $Text = str_replace(Notificationcontent::REPLACE_ARR["first_name"], $ClientInfo->FirstName ?? '', $Content2);
//                    $ContentTrue = $Content3;


//                    $Text = $ContentTrue;
                    $Subject = $Template->Subject;
                    $TextBoostapp = lang('recurring_payment_for_credit') . ' ' . $ClientInfo->CompanyName . ' ' . lang('subscription_updated_creditcard');

                    if ($TemplateStatus != '1') {
                        if ($TemplateSendOption != 'BA000' && $TypeKeva == 0) {
                            $AddNotification = DB::table('appnotification')->insertGetId(
                                array('CompanyNum' => $CompanyNum, 'ClientId' => $ClientId, 'Subject' => $Subject, 'Text' => $Text, 'Dates' => $Dates, 'UserId' => '0', 'Type' => $Type, 'Date' => $Date, 'Time' => $Time));
                        }
                        if ($SendStudioOption != 'BA000') {
                            $AddNotification = DB::table('appnotification')->insertGetId(
                                array('CompanyNum' => $CompanyNum, 'ClientId' => $ClientId, 'Subject' => $Subject, 'Text' => $TextBoostapp, 'Dates' => $Dates, 'UserId' => '0', 'Type' => '3', 'Date' => $Date, 'Time' => $Time, 'SendStudioOption' => $SendStudioOption));
                        }
                    }

                    if ($TypeShva == 0) {
                        $ErrorText = $StatusreditCard[$CCode] ?? '';
                    } else {
                        $ErrorText = $Err_Message ?? '';
                    }


                } //// סיום עסקה מוצלחת


                //////////////////////////////////////////////////////////////// עסקה נכשלה ///////////////////////////////////////////////////////


                else {

                    $KevaStatus = '2';
                    $LastDate = date('Y-m-d');
                    $TypePayment = @$Vaild_TypeOption['1'];
                    $ItemsTime = '+5 ' . $TypePayment;
                    $time = strtotime($LastDate);
                    $TryDate = date("Y-m-d", strtotime($ItemsTime, $time));
                    $LastDate = date('Y-m-d H:i:s');

                    /// שליחת התראה כשלון

                    /// הגדרת התראה
                    $CheckTime = date('H:i');
                    $Date = date('Y-m-d');
                    if ($CheckTime >= '00:00' && $CheckTime <= '08:00') {
                        $Time = '08:30:00';
                    } else {
                        $Time = date('H:i');
                    }
                    $Dates = date('Y-m-d H:i:s');

                    $Template = DB::table('notificationcontent')->where('CompanyNum', '=', $CompanyNum)->where('Type', '=', '15')->first();

                    // $ClientInfo = DB::table('client')->where('id', '=', $ClientId)->where('CompanyNum', '=' , $CompanyNum)->first();
//                    $CompanyInfo = DB::table('settings')->where('CompanyNum', '=', $CompanyNum)->first();
                    if ($TypeShva == 0) {
                        $ErrorText = $StatusreditCard[$CCode] ?? '';
                    } else {
                        $ErrorText = $Err_Message ?? '';
                    }

                    $KevaLink = '<a href="https://payment.boostapp.co.il/UpdatePayment.php?CUrl=' . $RandomTokenNumber . '">לחץ כאן</a>';

                    $TemplateStatus = $Template->Status;
                    $TemplateSendOption = $Template->SendOption;
                    $SendStudioOption = $Template->SendStudioOption;
                    $Type = '0';

                    if ($TemplateSendOption == 'BA999') {
                        $Type = '0';
                    } else if ($TemplateSendOption == 'BA000') {
                    } else {
                        $myArray = explode(',', $TemplateSendOption);
                        $Type2 = (in_array('2', $myArray)) ? '2' : '';
                        $Type1 = (in_array('1', $myArray)) ? '1' : '';
                        $Type0 = (in_array('0', $myArray)) ? '0' : '';

                        if ($Type2 != '') {
                            $Type = $Type2;
                        }
                        if ($Type1 != '') {
                            $Type = $Type1;
                        }
                        if ($Type0 != '') {
                            $Type = $Type0;
                        }

                    }

                    /// עדכון תבנית הודעה
                    $Content1 = str_replace(Notificationcontent::REPLACE_ARR["studio_name"], $CheckSettings->AppName, $Template->Content);
                    $Content2 = str_replace(Notificationcontent::REPLACE_ARR["name_table"], $ClientInfo->CompanyName ?? '', $Content1);
                    $Content3 = str_replace(Notificationcontent::REPLACE_ARR["first_name"], $ClientInfo->FirstName ?? '', $Content2);
                    $Content4 = str_replace(Notificationcontent::REPLACE_ARR["declined_reason"], $ErrorText ?? '', $Content3);
                    $Text = str_replace(Notificationcontent::REPLACE_ARR["click_here"], $KevaLink, $Content4);


                    $Subject = $Template->Subject;
                    $TextBoostapp = '<strong class="text-danger">' . lang('recurring_payment_for_credit') . ' ' . $ClientInfo->CompanyName . ' ' . lang('error_return_creditcorona') . ' ' . $ErrorText . ' ' . lang('update_link_sent_corona') . '</strong>';

                    if ($TemplateStatus != '1') {
                        if ($TemplateSendOption != 'BA000') {
                            $AddNotification = DB::table('appnotification')->insertGetId(
                                array('CompanyNum' => $CompanyNum, 'ClientId' => $ClientId, 'Subject' => $Subject, 'Text' => $Text, 'Dates' => $Dates, 'UserId' => '0', 'Type' => $Type, 'Date' => $Date, 'Time' => $Time, 'RandomUrl' => $RandomTokenNumber));
                        }
                        if ($SendStudioOption != 'BA000') {

                            LoginPushNotifications::sendLoginPushNotification(
                                $CompanyNum,
                                LoginPushNotifications::PUSH_NOTIFICATIONS_ID['login_horaat_keva_failed'],
                                $Subject,
                                $TextBoostapp,
                                $Date,
                                $Time
                            );

                            $AddNotification = DB::table('appnotification')->insertGetId(
                                array('CompanyNum' => $CompanyNum, 'ClientId' => $ClientId, 'Subject' => $Subject, 'Text' => $TextBoostapp, 'Dates' => $Dates, 'UserId' => '0', 'Type' => '3', 'Date' => $Date, 'Time' => $Time, 'SendStudioOption' => $SendStudioOption));
                        }
                    }

                }


                /// כניסה לטבלת הצלחה/כשלון בגביה
                DB::table('payment')
                    ->where('id', $GetClientCreditKeva->id)
                    ->where('CompanyNum', '=', $CompanyNum)
                    ->update(array(
                        'Amount' => $GetClientCreditKeva->Amount,
                        'Date' => $Date,
                        'Status' => $KevaStatus,
                        'Error' => $ErrorText,
                        'NumTry' => '1',
                        'L4digit' => @$L4digit,
                        'YaadCode' => @$YaadCode,
                        'CCode' => @$CCode,
                        'ACode' => @$ACode,
                        'Bank' => @$Bank,
                        'Payments' => @$Payments,
                        'Brand' => @$Brand,
                        'Issuer' => @$Issuer,
                        'BrandName' => @$BrandName,
                        'tashType' => $tashTypeDB,
                        'TryDate' => $TryDate,
                        'LastDate' => $LastDate,
                        'KevaId' => $AutoPaymentId));

                //// עדכון טבלת הוראת קבע מרכזית

                $LastPayment = date('Y-m-d');


                DB::table('paytoken')
                    ->where('id', $AutoPaymentId)
                    ->where('ClientId', '=', $ClientId)
                    ->where('CompanyNum', '=', $CompanyNum)
                    ->update(array('LastPayment' => $LastPayment, 'NextPayment' => $FinalNextPayment));


                $GetNextDate = DB::table('payment')->where('ClientId', '=', $ClientId)->where('KevaId', '=', $AutoPaymentId)->where('Status', '=', '0')->where('NumPayment', '>', $NumPayment)->where('ActStatus', '=', '0')->where('CompanyNum', '=', $CompanyNum)->orderBy('NumPayment', 'DESC')->first();


                $CountPayment = DB::table('payment')->where('CompanyNum', '=', $CompanyNum)->where('KevaId', '=', $AutoPaymentId)->where('ClientId', '=', $ClientId)->count();

                $CountPaymentAct = DB::table('payment')->where('CompanyNum', '=', $CompanyNum)->where('KevaId', '=', $AutoPaymentId)->where('Status', '=', '0')->where('ActStatus', '=', '0')->where('ClientId', '=', $ClientId)->count();



                $GetCount = $GetKevaInfo->NumPayment;
                $TotalCount = $CountPayment;

                if ($CountPaymentAct == '0') {

                    DB::table('paytoken')
                        ->where('id', $AutoPaymentId)
                        ->where('ClientId', '=', $ClientId)
                        ->where('CompanyNum', '=', $CompanyNum)
                        ->update(array('Status' => '2'));

                }


                if ($GetNextDate && $GetCount > $TotalCount) {

                    $TrueDayNum = date("d", strtotime($GetNextDate->Date));
                    $TrueDatePayment = $GetNextDate->Date;

                    $NextPayment = $GetNextDate->Date;
                    $i = $GetNextDate->NumPayment + 1;
                    $TypePayment = $GetKevaInfo->TypePayment;
                    $NumDate = $GetKevaInfo->NumDate;

                    if ($TypePayment == '3') {

                        $FixDateDay = date("d", strtotime($NextPayment));


                        $LastPayment = date("Y-m", strtotime($NextPayment));
                        $FixTypePayment = @$Vaild_TypeOption[$TypePayment];
                        $ItemsTime = '+' . $NumDate . ' ' . $FixTypePayment;
                        $time = strtotime($LastPayment);
                        $NextPayment = date("Y-m", strtotime($ItemsTime, $time));
                        $NextPaymentMonth = date("m", strtotime($NextPayment));
                        $NextPaymentLasatDay = date("t", strtotime($NextPayment));


                        if ($FixDateDay <= $NextPaymentLasatDay) {
                            $NextPayment = $NextPayment . '-' . $TrueDayNum;
                        } else {
                            $NextPayment = $NextPayment . '-' . $NextPaymentLasatDay;
                        }

                    } else {


                        $LastPayment = $NextPayment;
                        $FixTypePayment = @$Vaild_TypeOption[$TypePayment];
                        $ItemsTime = '+' . $NumDate . ' ' . $FixTypePayment;
                        $time = strtotime($LastPayment);
                        $NextPayment = date("Y-m-d", strtotime($ItemsTime, $time));

                    }


                    /// מחולל מספר מסמך
                    $RandomTokenNumber = GroupNumberHelper::generate();

                    $StopInsert = $GetKevaInfo->StopInsert;

                    // add payment
                    if (!$StopInsert && date($NextPayment) < date('2029-12-01')) {

                        $AddPayment = DB::table('payment')->insertGetId(
                            array('CompanyNum' => $CompanyNum, 'ClientId' => $ClientId, 'TypeKeva' => $GetNextDate->TypeKeva, 'Amount' => $GetNextDate->Amount, 'Date' => $NextPayment, 'Status' => '0', 'NumTry' => '0', 'TryDate' => null, 'LastDate' => null, 'KevaId' => $GetNextDate->KevaId, 'RandomUrl' => $RandomTokenNumber, 'NumPayment' => $i, 'TrueDayNum' => $GetNextDate->TrueDayNum, 'ActStatus' => '0'));

                    }

                }

            }

        } else {

            DB::table('payment')
                ->where('id', $GetClientCreditKeva->id)
                ->where('ClientId', '=', $GetClientCreditKeva->ClientId)
                ->where('CompanyNum', '=', $GetClientCreditKeva->CompanyNum)
                ->update(array('Status' => '3', 'ActStatus' => '1'));

        }


    }


    /// worker done
    DB::table('payment')
        ->whereIn('id', $workerArr)
        ->update(['workerStatus' => 2]);

//////////////////////////////////////////////////////////////// סיום פקודת מערכת ///////////////////////////////////////////////////////

    $Cron->end();
} catch (Exception $e) {
    $arr = array(
        "line" => $e->getLine(),
        "message" => $e->getMessage(),
        "file_path" => $e->getFile(),
        "trace" => $e->getTraceAsString()
    );
    if (isset($GetClientCreditKeva)) {
        $util = new Utils();
        $arr["data"] = json_encode($util->createArrayFromObj($GetClientCreditKeva), JSON_UNESCAPED_UNICODE);

        if (!empty($workerArr)) {
            /// reset worker status
            DB::table('payment')
                ->whereIn('id', $workerArr)
                ->where('id', '!=', $GetClientCreditKeva->id)
                ->where('Status', '=', 0)
                ->update(['workerStatus' => 0]);
        }
    }
    $Cron->cronLog($arr);
}
