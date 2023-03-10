<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Session Driver
	|--------------------------------------------------------------------------
	|
	| Supported: "native", "file", "database"
	|
	*/
	
	'driver' => 'database',

	/*
	|--------------------------------------------------------------------------
	| Session Lifetime
	|--------------------------------------------------------------------------
	|
	| Here you may specify the number of minutes that you wish the session
	| to be allowed to remain idle before it expires.
	|
	*/

	'lifetime' => 240,

	/*
	|--------------------------------------------------------------------------
	| Session File Location
	|--------------------------------------------------------------------------
	*/

	'files' => storage_path().'/sessions',
	
	/*
	|--------------------------------------------------------------------------
	| Session Database Table
	|--------------------------------------------------------------------------
	*/

	'table' => 'pipeline',

	/*
	|--------------------------------------------------------------------------
	| Session Cookie Name
	|--------------------------------------------------------------------------
	*/

	'cookie' => '247SOFT_pipeline',
	
	/*
	|--------------------------------------------------------------------------
	| Default Cookie Path
	|--------------------------------------------------------------------------
	*/

	'path' => '/',

	/*
	|--------------------------------------------------------------------------
	| Default Cookie Domain
	|--------------------------------------------------------------------------
	*/

	'domain' => null,
);
