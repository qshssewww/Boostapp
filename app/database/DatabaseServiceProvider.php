<?php

namespace App\Database;

use Hazzard\Database\Model;

class DatabaseServiceProvider extends \Hazzard\Database\DatabaseServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('db', function ($app) {
            if (isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"] == "localhost:8000") {
                $config = (isDevEnviroment()) ? $app['config']['database']['dev'] : $app['config']['database']['live'];
                return new BaseConnection($config);
            } else {
                $config = (isDevEnviroment() || isDevEnviromentSsh()) ? $app['config']['database']['dev'] : $app['config']['database']['live'];
                return new BaseConnection($config);
            }
        });

        Model::setConnection($this->app['db']);
    }
}
