<?php

namespace App\Utils;

use App\Models\DebugKeys;
use App\Models\DebugQuery;
use Hazzard\Support\Facades\Config;
use Predis\Client;

class DebugBar
{
    public const NUMBER_OF_PAGES_TO_LOG = 30;

    private static $debugKey;

    private static $isEnabled = false;

    private static $isInitiated = false;

    private static $redisClient;

    public static function init()
    {
        if (!self::isEnabled()) {
            return false;
        }

        self::$isInitiated = true;

        // prevent recursive calls
        self::disable();

        if (!DebugKeys::where('key', self::getDebugKey())->exists()) {
            $debugKeyModel = new DebugKeys([
                'key' => self::getDebugKey(),
                'url' => $_SERVER['REQUEST_URI'],
                'date' => date('Y-m-d H:i:s'),
            ]);

            $debugKeyModel->save();
        }

        // TODO: delete old keys and queries
        $keysForDelete = DebugKeys::where('date', '<=', date('Y-m-d H:i:s', strtotime('-15 minutes')))
            ->column('key');

        DebugQuery::whereIn('key', $keysForDelete)
            ->delete();

        DebugKeys::whereIn('key', $keysForDelete)->delete();

        self::enable();

        /*$redis = self::getRedisClient();

        if ($redis->exists('debug_keys')) {
            $countOfKeys = $redis->llen('debug_keys');
            if ($countOfKeys >= self::NUMBER_OF_PAGES_TO_LOG) {
                $key = $redis->lpop('debug_keys');
                $redis->del($key);
            }
        }

        $redis->rpush('debug_keys', self::getDebugKey());*/
    }

    /**
     * @return string
     */
    public static function getDebugKey()
    {
        if (!isset(self::$debugKey)) {
            self::$debugKey = 'debug_queries_' . substr(md5(time()), 0, 8);
        }
        return self::$debugKey;
    }

    /**
     * @return Client
     */
    public static function getRedisClient()
    {
        if (!isset(self::$redisClient)) {
            self::$redisClient = new Client([
                'host' => Config::get('redis.host'),
                'port' => Config::get('redis.port'),
                'password' => Config::get('redis.password'),
            ]);
        }
        return self::$redisClient;
    }

    /**
     * @return bool
     */
    public static function isEnabled()
    {
        if (isset($_SESSION['debugEnabled'])) {
            self::$isEnabled = $_SESSION['debugEnabled'];
        }
        return self::$isEnabled;
    }

    /**
     * @return void
     */
    public static function enable()
    {
        self::$isEnabled = $_SESSION['debugEnabled'] = true;
    }

    /**
     * @return void
     */
    public static function disable()
    {
        self::$isEnabled = $_SESSION['debugEnabled'] = false;
    }

    /**
     * @param $data
     * @return false|void
     */
    public static function put($data)
    {
        if (!self::isEnabled() || !self::$isInitiated) {
            return false;
        }

        /*$redis = self::getRedisClient();

        $redis->rpush(self::getDebugKey(), json_encode($data));
        $redis->expire(self::getDebugKey(), 10 * 60);*/

        // prevent recursive calls
        self::disable();

        $debugQueryModel = new DebugQuery([
            'key' => self::getDebugKey(),
            'type' => $data['type'],
            'query' => $data['query'],
            'trace' => json_encode($data['trace']),
            'time' => $data['time'],
        ]);
        $debugQueryModel->save();

        self::enable();
    }

    /**
     * @return string[]
     */
    public static function getLast()
    {
        return self::getInfo(self::getDebugKey());
    }

    /**
     * @return array
     */
    public static function getKeys()
    {
//        $redis = self::getRedisClient();
//
//        $keys = $redis->keys('debug_queries_*');
//
//        $result = [];
//        foreach ($keys as $key) {
//            $data = $redis->lrange($key, 0, -1);
//            foreach ($data as &$item) {
//                $item = json_decode($item, true);
//            }
//            $result[$key] = $data;
//        }

        // prevent recursive calls
        self::disable();

        $queries = DebugKeys::select('*')->get();
        $result = [];
        foreach ($queries as $query) {
            $result[] = $query->toArray();
        }

        self::disable();

        return $result;
    }

    /**
     * @param $key
     * @return string[]
     */
    public static function getInfo($key)
    {
//        $redis = self::getRedisClient();
//
//        $data = $redis->lrange($key, 0, -1);
//        if (!empty($data)) {
//            foreach ($data as &$item) {
//                $item = json_decode($item, true);
//            }
//        }

        // prevent recursive calls
        self::disable();

        $queries = DebugQuery::where('key', $key)->get();
        foreach ($queries as &$query) {
            $query = $query->toArray();
            $query['trace'] = json_decode($query['trace'], true);
        }

        self::enable();

        return $queries;
    }
}