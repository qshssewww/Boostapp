<?php namespace Hazzard\PipeLine;

use Hazzard\Support\ServiceProvider;

class PipeLineServiceProvider extends ServiceProvider {

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$me = $this;

		$this->app->bindShared('session', function($app) use($me) {
			
			$config = $app['config']['session'];

			$me->registerSessionDriver($config);

			return with(new Store($config['cookie']))->start();
		});
	}

}