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

		$this->publishes([
			__DIR__ .'/../database/migrations/' => database_path('migrations')
		], 'migrations');
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
