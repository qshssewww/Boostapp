<?php

class EventType
{
    const EVENT_TYPE_CLASSES = 0;
    const EVENT_TYPE_MEETINGS = 1;
    const EVENT_TYPE_SPACES = 2;
    const ALL_CLASSES = 'BA999';
    const ALL_MEETINGS = 'BA888';
    const ALL_SPACES = 'BA777';

    private static $_events = [];

    /**
     * @return array
     */
    public static function toList(): array
    {
        if (empty(self::$_events)) {
            foreach (array_keys(self::names()) as $event) {
                self::$_events[] = [
                    'id' => (int)$event,
                    'code' => self::code($event),
                    'name' => self::name($event)
                ];
            }
        }
        return self::$_events;
    }

    /**
     * @return array
     */
    public static function names(): array
    {
        return [
            self::EVENT_TYPE_CLASSES => lang('all_classes'),
            self::EVENT_TYPE_MEETINGS => lang('meeting_all'),
            self::EVENT_TYPE_SPACES => lang('all_calendar'),
        ];
    }

    /**
     * @return string[]
     */
    public static function codes(): array
    {
        return [
            self::EVENT_TYPE_CLASSES => self::ALL_CLASSES,
            self::EVENT_TYPE_MEETINGS => self::ALL_MEETINGS,
            self::EVENT_TYPE_SPACES => self::ALL_SPACES,
        ];
    }

    /**
     * @param $event
     * @return string|null
     */
    public static function name($event): ?string
    {
        return self::names()[$event] ?? null;
    }

    /**
     * @param $event
     * @return string|null
     */
    public static function code($event): ?string
    {
        return self::codes()[$event] ?? null;
    }
}
