<?php namespace Hazzard\Database;

use Hazzard\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bindShared('db', function($app) {

            if(isset($_SERVER["HTTP_HOST"]) && $_SERVER["HTTP_HOST"] == "localhost:8000"){
                $config = (isDevEnviroment()) ? $app['config']['database']['dev'] : $app['config']['database']['live'];
                return new Connection($config);
            }
		    else {
                $config = (isDevEnviroment() || isDevEnviromentSsh()) ? $app['config']['database']['dev'] : $app['config']['database']['live'];
                return new Connection($config);
            }
		});

		Model::setConnection($this->app['db']);
	}
}
