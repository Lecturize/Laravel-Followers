[![Latest Stable Version](https://poser.pugx.org/vendocrat/laravel-followers/v/stable)](https://packagist.org/packages/vendocrat/laravel-followers)
[![Total Downloads](https://poser.pugx.org/vendocrat/laravel-followers/downloads)](https://packagist.org/packages/vendocrat/laravel-followers)
[![License](https://poser.pugx.org/vendocrat/laravel-followers/license)](https://packagist.org/packages/vendocrat/laravel-followers)

# Laravel Followers

Build a poly-morph Follower system or simply associate Eloquent models in Laravel 5.

**Attention:** This package is a work in progress, please use with care and be sure to report any issues!

## Installation

Require the package from your `composer.json` file

```php
"require": {
	"vendocrat/laravel-followers": "dev-master"
}
```

and run `$ composer update` or both in one with `$ composer require vendocrat/laravel-followers`.

Next register the service provider and (optional) facade to your `config/app.php` file

```php
'providers' => [
    // Illuminate Providers ...
    // App Providers ...
    vendocrat\Followers\FollowersServiceProvider::class
];
```

```php
'providers' => [
	// Illuminate Facades ...
    'Followers' => vendocrat\Followers\Facades\Followers::class
];
```

## Configuration & Migration

```bash
$ php artisan vendor:publish --provider="vendocrat\Followers\FollowersServiceProvider"
```

This will create a `config/followers.php` and a migration file. In the config file you can customize the table names, finally you'll have to run migration like so:

```bash
$ php artisan migrate
```

## License

Licensed under [MIT license](http://opensource.org/licenses/MIT).

## Author

**Handcrafted with love by [Alexander Manfred Poellmann](http://twitter.com/AMPoellmann) for [vendocrat](https://vendocr.at) in Vienna &amp; Rome.**