<?php

namespace Lecturize\Followers;

use Illuminate\Support\ServiceProvider;

/**
 * Class FollowersServiceProvider
 * @package Lecturize\Followers
 */
class FollowersServiceProvider extends ServiceProvider
{
    protected $migrations = [
        'CreateFollowersTable' => 'create_followers_table'
    ];

    public function boot()
    {
        $this->handleConfig();
        $this->handleMigrations();

        $this->loadTranslationsFrom(__DIR__ .'/../resources/lang/', 'follower');
    }

    /** @inheritdoc */
    public function register()
    {
        //
    }

    /** @inheritdoc */
    public function provides()
    {
        return [];
    }

    /**
     * Publish and merge the config file.
     *
     * @return void
     */
    private function handleConfig()
    {
        $configPath = __DIR__ . '/../config/config.php';

        $this->publishes([$configPath => config_path('lecturize.php')]);

        $this->mergeConfigFrom($configPath, 'lecturize');
    }

    /**
     * Publish migrations.
     *
     * @return void
     */
    private function handleMigrations()
    {
        $count = 0;
        foreach ($this->migrations as $class => $file) {
            if (! class_exists($class)) {
                $timestamp = date('Y_m_d_Hi'. sprintf('%02d', $count), time());

                $this->publishes([
                    __DIR__ .'/../database/migrations/'. $file .'.php.stub' =>
                        database_path('migrations/'. $timestamp .'_'. $file .'.php')
                ], 'migrations');

                $count++;
            }
        }
    }
}