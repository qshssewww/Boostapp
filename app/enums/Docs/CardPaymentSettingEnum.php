<?php

class CardPaymentSettingEnum
{
    const CARD_READER = 1;
    const TOKEN = 2;
    const MANUAL_IFRAME = 3;
    const OTHER_TERMINAL = 4;


    private static $_cardPaymentSetting = [];

    /**
     * @return array
     */
    public static function toList(): array
    {
        if (empty(self::$_cardPaymentSetting)) {
            foreach (array_keys(self::names()) as $status) {
                self::$_cardPaymentSetting[] = [
                    'id' => (int)$status,
                    'name' => self::name((int)$status),
                ];
            }
        }
        return self::$_cardPaymentSetting;
    }

    /**
     * @return array
     */
    public static function names(): array
    {
        return [
            self::CARD_READER => lang('credit_card_scanner'),
            self::TOKEN => lang('credit_card_saved_in_system'),
            self::MANUAL_IFRAME => lang('manual_type'),
            self::OTHER_TERMINAL => lang('transfer_made_by_other_terminal'),
        ];
    }

    /**
     * @param int $status
     * @return string|null
     */
    public static function name(int $status): ?string
    {
        return self::names()[$status] ?? null;
    }


}

