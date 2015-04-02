<?php
use GrahamCampbell\TestBench\AbstractPackageTestCase;

require '../vendor/autoload.php';

/**
 * This file belongs to Trust.
 *
 * Author: Rahul Kadyan, <hi@znck.me>
 */
class TrustTest extends AbstractPackageTestCase
{
    protected function getServiceProviderClass($app)
    {
        return 'Znck\Trust\ServiceProvider';
    }

    public function setUp()
    {
        parent::setUp();
    }

    protected function getUser()
    {
        return Mockery::mock('Znck\Trust\RoleTrait');
    }

    protected function  getMiddleware()
    {
        return Mockery::mock('Znck\Trust\Http\Middleware\AbstractRoleOrPermission');
    }
}