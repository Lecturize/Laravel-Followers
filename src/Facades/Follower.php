<?php namespace Lecturize\Followers\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Follower
 * @package Lecturize\Followers\Facades
 */
class Follower extends Facade
{
    /**
     * @inheritdoc
     */
    protected static function getFacadeAccessor()
    {
        return 'follower';
    }
}