<?php

class DocsTableEnum
{
    const TYPE_RESERVATION = 100;
    const TYPE_SHIPPING_DOCUMENTS = 200;
    const TYPE_RETURN_CERTIFICATES = 210;
    const TYPE_HESHBONIT_HESHKA = 300;
    const TYPE_HESHBONIT_MAS = 305;
    const TYPE_CONCENTRATION_INVOICES = 310;
    const TYPE_HESHBONIT_MAS_KABLA = 320;
    const TYPE_HESHBONIT_MAS_ZIKUI = 330;
    const TYPE_KABALA = 400;

    private static $_docsTable = [];

    /**
     * @return array
     */
    public static function toList(): array
    {
        if (empty(self::$_docsTable)) {
            foreach (array_keys(self::names()) as $status) {
                self::$_docsTable[] = [
                    'id' => (string)$status,
                    'name' => self::name($status),
                ];
            }
        }

        return self::$_docsTable;
    }

    /**
     * @return array
     */
    public static function names(): array
    {
        //todo change to translation
        return [
            self::TYPE_RESERVATION => 'הזמנה',
            self::TYPE_SHIPPING_DOCUMENTS => 'מסמך משלוח',
            self::TYPE_RETURN_CERTIFICATES => 'תעודת החזרה',
            self::TYPE_HESHBONIT_HESHKA => 'חשבונית עיסקה',
            self::TYPE_HESHBONIT_MAS => 'חשבונית מס',
//            self::TYPE_CONCENTRATION_INVOICES => 'מסמך נוסף',
            self::TYPE_HESHBONIT_MAS_KABLA => 'חשבונית מס קבלה',
            self::TYPE_HESHBONIT_MAS_ZIKUI => 'חשבונית מס זיכוי',
            self::TYPE_KABALA => 'קבלה',
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


}

