<?php

require_once __DIR__ . '/BaseController.php';

class TestRedisController extends BaseController
{
    public function index()
    {
        $redis = new \Predis\Client([
            'host' => Config::get('env.redisHost'),
            'port' => Config::get('env.redisPort'),
            'password' => Config::get('env.redisPassword'),
        ]);

        $redis->set('foo', 'bar');

        var_dump($redis->keys('*'));
        var_dump($redis->get('foo'));

        $redis->del('foo');
    }

    /**
     * @return void
     */
    public function info()
    {
        return $this->render('test-redis/info');
    }
}
