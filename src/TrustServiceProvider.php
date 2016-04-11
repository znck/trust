<?php
/**
 * This file belongs to Trust.
 *
 * Author: Rahul Kadyan, <hi@znck.me>
 */
namespace Znck\Trust;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class TrustServiceProvider extends BaseServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__.'/../config/trust.php' => config_path('trust.php')], 'config');
        $this->publishes([__DIR__.'/../migrations' => database_path()], 'migrations');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/trust.php', 'znck.trust');
    }
}
