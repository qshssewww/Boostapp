<?php

class MeetingStatus
{
    const PENDING = 0;
    const WAITING = 1;
    const ORDERED = 2;
    CONST STARTED = 3;
    CONST COMPLETED = 4;
    CONST DIDNT_ATTEND = 5;
    CONST DONE = 6;
    CONST CANCELED = 7;

    private static $_statuses = [];

    /**
     * @return array
     */
    public static function toList(): array
    {
        if (empty(self::$_statuses)) {
            foreach (array_keys(self::names()) as $status) {
                self::$_statuses[] = [
                    'id' => (string)$status,
                    'type' => self::name($status),
                    'color' => self::color($status),
                    'bg' => self::backgroundColor($status),
                ];
            }
        }

        return self::$_statuses;
    }

    /**
     * @return array
     */
    public static function names(): array
    {
        return [
            self::PENDING => lang('meeting_pending'),
            self::WAITING => lang('meeting_waiting'),
            self::ORDERED => lang('meeting_ordered'),
            self::STARTED => lang('meeting_started'),
            self::COMPLETED => lang('completed_client_profile'),
            self::DIDNT_ATTEND => lang('did_not_arrive_cal_general'),
            self::DONE => lang('meeting_done'),
            self::CANCELED => lang('canceled')
        ];
    }

    /**
     * @return array
     */
    public static function filterNames(): array
    {
        return [
            self::WAITING => lang('meeting_waiting'),
            self::ORDERED => lang('meeting_ordered'),
            self::STARTED => lang('meeting_started'),
            self::COMPLETED => lang('completed_client_profile'),
            self::DIDNT_ATTEND => lang('did_not_arrive_cal_general'),
            self::CANCELED => lang('canceled'),
        ];
    }

    /**
     * @return string[]
     */
    public static function colors(): array
    {
        return [
            self::WAITING => '#4CACFB',
            self::ORDERED => '#0089FA',
            self::STARTED => '#00C736',
            self::COMPLETED => '#707070',
            self::DIDNT_ATTEND => '#FF0045',
            self::DONE => '#818181',
            self::CANCELED => '#FF0045',
        ];
    }

    /**
     * @return string[]
     */
    public static function backgroundColors(): array
    {
        return [
            self::PENDING => '#ededed',
            self::WAITING => '#C8E6FE',
            self::ORDERED => '#B1DBFD',
            self::STARTED => '#B1EEC1',
            self::COMPLETED => '#F7F7F7',
            self::DIDNT_ATTEND => '#FFB1C6',
            self::DONE => '#F7F7F7',
            self::CANCELED => '#FFB1C6',
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
     * @param $status
     * @return string|null
     */
    public static function color($status): ?string
    {
        return self::colors()[$status] ?? null;
    }

    /**
     * @param $status
     * @return string|null
     */
    public static function backgroundColor($status): ?string
    {
        return self::backgroundColors()[$status] ?? null;
    }
}
