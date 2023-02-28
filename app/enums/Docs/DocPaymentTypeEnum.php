<?php

class DocPaymentTypeEnum
{
    const CASH = 1;
    const CHECK = 2;
    const CREDIT_CARD = 3;
    const BANKTRAS = 4;
    const TAVIM = 5;
    const TLOSH = 6;
    const SHTAR = 7;
    const KEVA = 8;
    const OTHER = 9;

    private static $_paymentType = [];

    /**
     * @return array
     */
    public static function toList(): array
    {
        if (empty(self::$_paymentType)) {
            foreach (array_keys(self::names()) as $status) {
                self::$_paymentType[] = [
                    'id' => (string)$status,
                    'name' => self::name($status),
                    'checkOutFrontText' => self::checkOutFrontTexts($status),
                ];
            }
        }

        return self::$_paymentType;
    }

    /**
     * @return array
     */
    public static function names(): array
    {
        return [
            self::CASH => lang('cash'),
            self::CHECK => lang('check'),
            self::CREDIT_CARD => lang('credit_card'),
            self::BANKTRAS => lang('bank_transfer'),
            self::TAVIM => 'תווים',//lang('characters_appsettings'),
            self::TLOSH => lang('payment_bill'),
            self::SHTAR => lang('standing_order'),
            self::KEVA => lang('other'),
        ];
    }

    /**
     * @return string[]
     */
    public static function checkOutFrontTexts(): array
    {
        return [
            self::CASH => 'cash',
            self::CHECK => 'check',
            self::CREDIT_CARD => 'credit',
            self::BANKTRAS => 'bankTransfer',
            self::TAVIM => 'tavim',
            self::TLOSH => 'paycheck',
            self::SHTAR => 'promissoryNote',
            self::KEVA => 'fixedPayment',
        ];
    }

    /**
     * @param $status
     * @return string|null
     */
    public static function name($status): ?string
    {
        return self::names()[$status] ?? null;
    }


    /**
     * @param string $checkOutFrontText
     * @return int - 0 not found else status
     */
    public static function getStatusByCheckOutFrontText(string $checkOutFrontText): ?int
    {
        return array_search($checkOutFrontText, self::checkOutFrontTexts());
    }

    /**
     * @param $status
     * @return string|null
     */
    public static function getCheckOutFrontText($status): ?string
    {
        return self::checkOutFrontTexts()[$status] ?? null;
    }

}

