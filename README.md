[![Latest Stable Version](https://poser.pugx.org/lecturize/laravel-followers/v/stable)](https://packagist.org/packages/lecturize/laravel-followers)
[![Total Downloads](https://poser.pugx.org/lecturize/laravel-followers/downloads)](https://packagist.org/packages/lecturize/laravel-followers)
[![License](https://poser.pugx.org/lecturize/laravel-followers/license)](https://packagist.org/packages/lecturize/laravel-followers)

# Laravel Followers

Build a poly-morph Follower system or simply associate Eloquent models in Laravel 5.

## Important Notice

**This package is a work in progress**, please use with care and feel free to report any issues or ideas you may have!

We've transferred this package to a new owner and therefor updated the namespaces to **Lecturize\Followers**. The config file is now `config/lecturize.php`.

## Installation

Require the package from your `composer.json` file

```php
"require": {
	"lecturize/laravel-followers": "dev-master"
}
```

and run `$ composer update` or both in one with `$ composer require lecturize/laravel-followers`.

Next register the service provider and (optional) facade to your `config/app.php` file

```php
'providers' => [
    // Illuminate Providers ...
    // App Providers ...
    Lecturize\Followers\FollowersServiceProvider::class
];
```

## Configuration & Migration

```bash
$ php artisan vendor:publish --provider="Lecturize\Followers\FollowersServiceProvider"
```

This will create a `config/lecturize.php` and a migration file. In the config file you can customize the table names, finally you'll have to run migration like so:

```bash
$ php artisan migrate
```

## License

Licensed under [MIT license](http://opensource.org/licenses/MIT).

## Author

**Handcrafted with love by [Alexander Manfred Poellmann](http://twitter.com/AMPoellmann) for [Lecturize](https://lecturize.com) in Vienna &amp; Rome.**