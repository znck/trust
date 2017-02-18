# Installation

Use [Composer](https://getcomposer.com){target=\_blank} to install **trust**.

``` bash
composer require znck/trust
```

Next, your need to register the service provider,

``` php
// config/app.php

'providers' => [
  ...
  Znck\Trust\TrustServiceProvider::class,
],
```

Run the migrations,

``` bash
php artisan migrate
```

> TODO: Add docs for publishing migrations


-------------------------------
[Edit this page on Github]({{ $docs_edit_url }}/installation.md)
