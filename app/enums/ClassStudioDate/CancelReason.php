<?php

class CancelReason
{
    const NO_REASON = 0;
    const BY_MISTAKE = 1;
    const NOT_AVAILABLE = 2;
    const MEMBERSHIP_FREEZE = 3;

    private static $_statuses = [];

    /**
     * @return array
     */
    public static function toList(): array
    {
        if (empty(self::$_statuses)) {
            foreach (array_keys(self::all()) as $status) {
                self::$_statuses[$status] = self::get($status);
            }
        }
        return self::$_statuses;
    }

    /**
     * @return array
     */
    public static function all(): array
    {
        return [
            self::NO_REASON => lang('no_reason'),
            self::BY_MISTAKE => lang('created_by_mistake'),
            self::NOT_AVAILABLE => lang('customer_not_available'),
            self::MEMBERSHIP_FREEZE => lang('freezed_membership'),
        ];
    }

    /**
     * @param $status
     * @return string|null
     */
    public static function get($status): ?string
    {
        return self::all()[$status] ?? null;
    }
}
