# Trust [![](https://img.shields.io/travis/trust/flash.svg)](https://travis-ci.org/znck/trust) [![](https://img.shields.io/github/release/znck/trust.svg)](https://github.com/znck/trust/releases) [![](https://img.shields.io/packagist/v/znck/trust.svg)](https://packagist.org/packages/znck/trust) [![](https://img.shields.io/packagist/dt/znck/trust.svg)](https://packagist.org/packages/znck/trust)  [![](https://img.shields.io/packagist/l/znck/trust.svg)](http://znck.mit-license.org) [![Codacy Badge](https://www.codacy.com/project/badge/9264639675f04aed934032372d433c7a)](https://www.codacy.com/app/hi_3/trust)
User roles and permissions for Laravel 5

> Note: This package is still needs tests!

This package provides an easy way to use role-permissions based User model.

First, require this package in composer.json and run composer update

    "znck/trust": "~1.0.0"

After updating, add the ServiceProvider to the array of providers in `config/app.php`

```php
'Znck\Trust\ServiceProvider',
```

Publish migration tables.
```bash
$ php artisan trust:migration 
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