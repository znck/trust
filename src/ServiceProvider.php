<?php
/**
 * This file belongs to Trust.
 *
 * Author: Rahul Kadyan, <hi@znck.me>
 */

namespace Znck\Trust;


use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->alias('role', 'Znck\Trust\Http\Middleware\NeedsRole');
        $this->app->alias('permission', 'Znck\Trust\Http\Middleware\NeedsPermission');
    }
}