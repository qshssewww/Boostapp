<?php

require_once __DIR__ . '/../Classes/Client.php';
require_once __DIR__ . '/../Classes/OrderLogin.php';

class OrderService
{
    /**
     * @param $Client
     * @param float $amount
     * @param int $paymentNumber
     * @param null $type
     * @param null $userId
     * @return OrderLogin
     */
    public static function createOrder($Client , float $amount, int $paymentNumber = 1, $type = null, $userId = null): OrderLogin
    {
        if ($Client) {
            $CompanyNum = $Client->CompanyNum;
        } elseif (Auth::check()) {
            $CompanyNum = Auth::user()->CompanyNum;
        } else {
            $CompanyNum = 0;
            LoggerService::error('CompanyNum is empty while creating new Order');
        }

        $order = new OrderLogin([
            'ClientId' => $Client ? $Client->id : 0,
            'CompanyNum' => $CompanyNum,
            'Notified' => OrderLogin::STATUS_NOT_NOTIFIED,
            'Status' => OrderLogin::STATUS_UNPAID,
            'NumPayment' => $paymentNumber,
            'Type' => $type,
            'PaymentType' => 3, // credit card
        ]);
        $order->save();

        $totalAmount = $amount;
        $orderDescription = '{"data": []}';

        $order->Amount = $totalAmount;
        $order->Discount = 0;
        $order->Interest = 0;
        $order->TotalAmount = $order->Amount - $order->Discount + $order->Interest;
        $order->Description = $orderDescription;
        $order->UserId = Auth::check() ? Auth::user()->id : $userId;
        $order->save();

        return $order;
    }

    /**
     * @param $type
     * @return string|null
     */
    private static function getValidTypeOptions($type)
    {
        $options = [
            1 => "day",
            2 => "week",
            3 => "month",
            4 => "year"
        ];

        return $options[$type] ?? null;
    }

    /**
     * @param $totalAmount
     * @param $NumOfPayments
     * @return array
     */
    public static function divideToPayments($totalAmount, $NumOfPayments) {
        $Amount = $totalAmount / $NumOfPayments;
        $roundedAmount = ceil(round($Amount, 2));
        $restOfPayments = $roundedAmount * ($NumOfPayments - 1);
        $firstPayment = $totalAmount - $restOfPayments;
        $firstPayment = number_format((float)$firstPayment, 2, '.', '');
        $secondPayment = number_format($roundedAmount, 2, '.', '');

        return ["firstPayment" => $firstPayment, "secondPayment" => $secondPayment];
    }

}
