<?php

class PriceHelper
{
    /**
     * @param float $price
     * @param float|int $vat
     * @return float
     */
    public static function getVatAmount(float $price, float $vat = 17): float
    {
        if($vat === 0) {
            return 0;
        }
        return round($price - ( $price / (1+($vat/100))), 2);
    }

    /**
     * @param float $price
     * @param float|int $vat
     * @return float
     */
    public static function getPriceWithoutVat(float $price, float $vat = 17): float
    {
        if($vat === 0) {
            return $price;
        }
        return round($price / (1+($vat/100)), 2);
    }



    /**
     * @param float $percentageToPay
     * @param float $price
     * @return float
     */
    public static function getFormattedAmountFromPercentageAndPrice(float $percentageToPay, float $price ): float
    {
        return round(($percentageToPay * $price) / 100, 2);
    }


}
