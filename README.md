# Trust [![Build Status](https://travis-ci.org/znck/trust.svg?branch=master)](https://travis-ci.org/znck/trust)  [![Packagist Version](https://img.shields.io/packagist/v/znck/trust.svg)](https://packagist.org/packages/znck/trust) [![Packagist Downloads](https://img.shields.io/packagist/dt/znck/trust.svg)](https://packagist.org/packages/znck/trust)  [![License](https://img.shields.io/packagist/l/znck/trust.svg)](http://znck.mit-license.org)
User roles and permissions for Laravel 5

> Note: This package still needs tests!

This package provides an easy way to use role-permissions based User model.

First, require this package in composer.json and run composer update

    "znck/trust": "~1.0.0"

After updating, add the ServiceProvider to the array of providers in `config/app.php`

```php
'Znck\Trust\ServiceProvider',
```

Publish migration tables.
```bash
$ php artisan trust:tables 
```

Usage
-----
It assumes that `role` name `Author` exists in database.

```php
Route::get('/', [
    'uses' => 'WelcomeController@welcome', 
    'middleware' => 'role', 
    'role' => 'Author'
]);
```
