<?php

/**
 * @property $id
 * @property $clientId
 * @property $type
 * @property $dateTS
 * @property $details
 */
class GoogleCalendarSyncIssues extends \Hazzard\Database\Model
{
    protected $table = "boostapp.google_calendar_sync_issues";

    const TYPE_INIT_SYNC = 0;
    const TYPE_SINGLE_CLASS = 1;
    const TYPE_CALENDAR_COLOR = 2;
    const TYPE_TEMPORARY_STATUS = 3;

    /**
     * @param $clientId
     * @param $date
     * @param $classActId
     * @return mixed
     */
    public static function insertSingle($clientId, $date, $classActId)
    {
        $details = [
            'classActId' => $classActId,
        ];

        return self::insert([
            'clientId' => $clientId,
            'type' => self::TYPE_SINGLE_CLASS,
            'dateTS' => strtotime($date),
            'details' => json_encode($details),
        ]);
    }

    /**
     * @param $clientId
     * @param $date
     * @return mixed
     */
    public static function insertSync($clientId, $date)
    {
        return self::insert([
            'clientId' => $clientId,
            'type' => self::TYPE_INIT_SYNC,
            'dateTS' => strtotime($date),
        ]);
    }

    /**
     * @param $clientId
     * @return mixed
     */
    public static function insertCalendarColorFix($clientId)
    {
        return self::insert([
            'clientId' => $clientId,
            'type' => self::TYPE_CALENDAR_COLOR,
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function deleteById($id)
    {
        return self::where('id', '=', $id)
            ->delete();
    }

    /**
     * @param $id
     * @param $date
     * @return mixed
     */
    public static function updateDateById($id, $date)
    {
        return self::where('id', '=', $id)
            ->update([
                'dateTS' => strtotime($date),
            ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function deleteByClientId($id)
    {
        return self::where('clientId', '=', $id)
            ->delete();
    }

    /**
     * @return array
     */
    public static function getAllGrouped()
    {
        $errors = self::get();
        $result = [];

        // group by client
        foreach ($errors as $error) {
            if (!isset($result[$error->clientId])) {
                $result[$error->clientId] = [];
            }
            $result[$error->clientId][] = $error;
        }

        foreach ($result as $key => $client) {
            // sort by date asc
            usort($client, function ($a, $b) {
                return $a['dateTS'] - $b['dateTS'];
            });

            for ($i = 0; $i < sizeof($client); $i++) {
                if ($client[$i]->type == self::TYPE_INIT_SYNC) {
                    // remove unnecessary lines before returning to cron
                    while (sizeof($client) > $i + 1) {
                        GoogleCalendarSyncIssues::deleteById($client[sizeof($client)-1]->id);
                        unset($client[sizeof($client)-1]);
                    }
                }
            }

            // remove single class duplicates
            for ($i = sizeof($client) - 1; $i > 0; $i--) {
                if ($client[$i]->type == self::TYPE_SINGLE_CLASS) {
                    // remove unnecessary lines before returning to cron
                    while ($i > 0
                        && $client[$i - 1]->type == self::TYPE_SINGLE_CLASS
                        && $client[$i]->details === $client[$i - 1]->details) {
                        GoogleCalendarSyncIssues::deleteById($client[$i]->id);
                        unset($client[$i--]);
                    }
                }
            }

            // remove temporary status reset duplicates
            $delete = false;
            foreach ($client as $errId => $value) {
                if ($value->type == self::TYPE_TEMPORARY_STATUS) {
                    if ($delete) {
                        GoogleCalendarSyncIssues::deleteById($value->id);
                        unset($client[$errId]);
                    } else {
                        $delete = true;
                    }
                }
            }

            // sorted and trimmed
            $result[$key] = $client;
        }

        return $result;
    }

    /**
     * @param $clientId
     * @return mixed
     */
    public static function insertTemporaryStatusRemove($clientId)
    {
        return self::insert([
            'clientId' => $clientId,
            'type' => self::TYPE_TEMPORARY_STATUS,
        ]);
    }
}
