[![Latest Stable Version](https://poser.pugx.org/lecturize/laravel-followers/v/stable)](https://packagist.org/packages/lecturize/laravel-followers)
[![Total Downloads](https://poser.pugx.org/lecturize/laravel-followers/downloads)](https://packagist.org/packages/lecturize/laravel-followers)
[![License](https://poser.pugx.org/lecturize/laravel-followers/license)](https://packagist.org/packages/lecturize/laravel-followers)

# Laravel Followers

Build a poly-morph follower system or simply associate Eloquent models in Laravel.

## Installation

Require the package from your `composer.json` file

```php
"require": {
    "lecturize/laravel-followers": "dev-master"
}
```

and run `$ composer update` or both in one with `$ composer require lecturize/laravel-followers`.

Next register the service provider and (optional) facade to your `config/app.php` file

## Configuration & Migration

```bash
$ php artisan vendor:publish --provider="Lecturize\Followers\FollowersServiceProvider"
```

This will publish a `config/lecturize.php` and some migration files, that you'll have to run:

```bash
$ php artisan migrate
```

For migrations to be properly published ensure that you have added the directory `database/migrations` to the classmap in your projects `composer.json`.

## License

Licensed under [MIT license](http://opensource.org/licenses/MIT).

## Author

**Handcrafted with love by [Alexander Manfred Poellmann](https://twitter.com/AMPoellmann) in Vienna &amp; Rome.**