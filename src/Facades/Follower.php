<?php namespace vendocrat\Followers\Facades;

use Illuminate\Support\Facades\Facade;
use vendocrat\Followers\Followers;

class Follower extends Facade
{
	/**
	 * Get the registered name of the component.
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return Followers::class;
	}
}