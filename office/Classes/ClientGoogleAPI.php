<?php

require_once "GoogleCalendarSyncIssues.php";

/**
 * @property $id
 * @property $client_id
 * @property $token
 * @property $user_info
 * @property $status
 */
class ClientGoogleAPI extends \Hazzard\Database\Model
{
    protected $table = "boostapp.client_google_api";

    const STATUS_RECONNECT = 0;
    const STATUS_SYNCED = 1;
    const STATUS_RESYNC = 2;        // depricated
    const STATUS_LIMIT_REACHED = 3; // temporary status

    /**
     * @param $ClientId
     * @return mixed
     */
    public static function getByClientId($ClientId)
    {
        return self::where('client_id', '=', $ClientId)
            ->first();
    }

    /**
     * @param $ClientId
     * @param $token
     * @return mixed
     */
    public static function updateTokenByClientId($ClientId, $token)
    {
        return self::where('client_id', '=', $ClientId)
            ->update(['token' => json_encode($token)]);
    }

    /**
     * @param $ClientId
     * @return mixed
     */
    public static function deleteByClientId($ClientId)
    {
        return self::where('client_id', '=', $ClientId)
            ->delete();
    }

    /**
     * @param $ClientId
     * @return mixed
     */
    public static function setStatusSyncedByClientId($ClientId)
    {
        return self::where('client_id', '=', $ClientId)
            ->update(['status' => self::STATUS_SYNCED]);
    }

    /**
     * @param $ClientId
     * @return mixed
     */
    public static function setStatusReconnectByClientId($ClientId)
    {
        return self::where('client_id', '=', $ClientId)
            ->update(['status' => self::STATUS_RECONNECT]);
    }

    /**
     * @param $ClientId
     * @return mixed
     */
    public static function setStatusLimitReachedByClientId($ClientId)
    {
        // temporary status - set task for cron to remove
        GoogleCalendarSyncIssues::insertTemporaryStatusRemove($ClientId);

        return self::where('client_id', '=', $ClientId)
            ->update(['status' => self::STATUS_LIMIT_REACHED]);
    }
}
