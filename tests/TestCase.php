<?php

namespace Znck\Tests\Trust;

use GrahamCampbell\TestBench\AbstractPackageTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Znck\Tests\Trust\Models\User;
use Znck\Trust\TrustServiceProvider;

class TestCase extends AbstractPackageTestCase
{
    use DatabaseMigrations;

    public function runDatabaseMigrations()
    {
        $this->artisan('migrate', ['--realpath' => realpath(__DIR__.'/migrations/')]);
        $this->artisan('migrate', ['--realpath' => realpath(__DIR__.'/../migrations/')]);
    }

    public function setUp()
    {
        parent::setUp();
        $this->app['config']['auth.providers.users.model'] = User::class;
    }

    protected function getServiceProviderClass($app)
    {
        return TrustServiceProvider::class;
    }

    /**
     * @return User
     */
    protected function createUser()
    {
        return User::create(['name' => 'Foo Bar', 'email' => 'foo@example.com', 'password' => bcrypt('password')]);
    }
}
