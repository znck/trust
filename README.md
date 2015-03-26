# Trust for Laravel 5
User roles and permissions

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