<?php namespace Lecturize\Followers;

use Illuminate\Support\ServiceProvider;

class FollowersServiceProvider extends ServiceProvider
{
    protected $migrations = [
        'CreateFollowersTable' => 'create_followers_table'
    ];

	/**
	 * Boot the service provider.
	 *
	 * @return void
	 */
	public function boot()
	{
        $this->handleConfig();
        $this->handleMigrations();
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
	//  $this->app->singleton(Lecturize::class);
	}

	/**
     * @inheritdoc
	 */
	public function provides()
	{
		return [];
	}

    /**
     * Publish and merge the config file
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
     * Publish migrations
     *
     * @return void
     */
    private function handleMigrations()
    {
        foreach ( $this->migrations as $class => $file ) {
            if ( ! class_exists($class) ) {
                $timestamp = date('Y_m_d_His', time());

                $this->publishes([
                    __DIR__ .'/../database/migrations/'. $file .'.php.stub' =>
                        database_path('migrations/'. $timestamp .'_'. $file .'.php')
                ], 'migrations');
            }
        }
    }
}