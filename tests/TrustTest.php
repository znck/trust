<?php
use GrahamCampbell\TestBench\AbstractPackageTestCase;

require '../vendor/autoload.php';
require 'User.php';

/**
 * This file belongs to Trust.
 *
 * Author: Rahul Kadyan, <hi@znck.me>
 */
class TrustTest extends AbstractPackageTestCase
{
    public function testUserHasRole()
    {
        $user = $this->getUser();

        $this->assertNotNull($user->roles, 'User should have roles attribute.');
    }

    protected function getServiceProviderClass($app)
    {
        return 'Znck\Trust\ServiceProvider';
    }

    public function setUp()
    {
        parent::setUp();
    }

    protected function getUser($attributes = [])
    {
        return Mockery::mock('\User');
    }
}