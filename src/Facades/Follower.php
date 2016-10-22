<?php namespace Lecturize\Followers\Facades;

use Illuminate\Support\Facades\Facade;

class Follower extends Facade
{
	/**
     * @inheritdoc
	 */
	protected static function getFacadeAccessor()
	{
		return 'followers';
	}
}