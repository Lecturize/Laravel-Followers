<?php namespace vendocrat\Followers;

use Illuminate\Support\ServiceProvider;

class FollowersServiceProvider extends ServiceProvider
{
	/**
	 * Boot the service provider.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->publishes([
			__DIR__ .'/../config/config.php' => config_path('followers.php')
		], 'config');

		if ( ! class_exists('CreateMediaTable') ) {
			$timestamp = date('Y_m_d_His', time());

			$this->publishes([
				__DIR__ .'/../database/migrations/create_media_table.php.stub' =>
					database_path('migrations/'. $timestamp .'_create_followers_table.php')
			], 'migrations');
		}
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->mergeConfigFrom(
			__DIR__ .'/../config/config.php',
			'followers'
		);

		$this->app->singleton(Followers::class, function ($app) {
			return new Followers($app);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return string[]
	 */
	public function provides()
	{
		return [
			Followers::class
		];
	}
}
