<?php namespace Hazzard\Support\Facades;
/**
* @see \Hazzard\ConfigManager
*/
class PipeLine extends Facade {
	
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor() { return 'pipeline'; }
}