<?php

class TrustTestUser extends Illuminate\Database\Eloquent\Model
{
    use \Znck\Trust\RoleTrait;

    protected $table = 'users';
}

/**
 * This file belongs to Trust.
 *
 * Author: Rahul Kadyan, <hi@znck.me>
 */
class TrustTest extends \Orchestra\Testbench\TestCase
{
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->date = date('Y_m_d_His');
        exec('cp ' . __DIR__ . '/../src/migrations/create.php.stub ' . __DIR__ . '/../src/migrations/' . $this->date . '_create_roles_permissions_tables.php');
    }

    function __destruct()
    {
        exec('rm ' . __DIR__ . '/../src/migrations/' . $this->date . '_create_roles_permissions_tables.php 2> /dev/null');
    }

    protected function getServiceProviderClass()
    {
        return 'Znck\Trust\ServiceProvider';
    }

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();
        // uncomment to enable route filters if your package defines routes with filters
        // $this->app['router']->enableFilters();
        // call migrations for packages upon which our package depends, e.g. Cartalyst/Sentry
        // not necessary if your package doesn't depend on another package that requires
        // running migrations for proper installation
        /* uncomment as necessary
        $this->artisan('migrate', [
            '--database' => 'testbench',
            '--path'     => '../vendor/cartalyst/sentry/src/migrations',
        ]);
        */
        // call migrations specific to our tests, e.g. to seed the db
        // the path option should be relative to the 'path.database'
        // path unless `--path` option is available.

        $this->artisan('migrate', [
            '--database' => 'testbench',
            '--realpath' => realpath(__DIR__ . '/../src/migrations'),
        ]);
    }

    protected function getUser()
    {
        \Illuminate\Support\Facades\Schema::create('users', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });

        return new TrustTestUser;
    }

    public function test_it_stores_role()
    {
        $role = new \Znck\Trust\Role(['name' => 'User']);

        $this->assertTrue($role->save());
        $this->assertEquals(0, count($role->permissions));
    }

    public function test_it_stores_permission()
    {
        $permission = new \Znck\Trust\Permission(['name' => 'user.create']);

        $this->assertTrue($permission->save());
    }

    public function test_it_creates_user()
    {
        $user = $this->getUser();
        $this->assertNotNull($user);
        $this->assertNotNull($user->roles());
        $this->assertEquals(0, count($user->roles));
    }

    public function test_it_can_do_something()
    {
        $text = [
            'user.create',
            'user.update',
            'user.delete'
        ];

        \Znck\Trust\Permission::create(['name' => $text[0]]);
        \Znck\Trust\Permission::create(['name' => $text[1]]);
        \Znck\Trust\Permission::create(['name' => $text[2]]);

        $user = $this->getUser();

        $role1 = new \Znck\Trust\Role(['name' => 'Editor']);
        $role2 = new \Znck\Trust\Role(['name' => 'Owner']);

        $this->assertTrue($role1->save());
        $this->assertTrue($role2->save());
        $this->assertTrue($user->save());

        $role1->permissions()->sync([1, 2]);
        $role2->permissions()->sync([1, 2, 3]);

        $user->attachRole($role1);
        $this->assertTrue($user->hasRole('Editor'));
        $this->assertFalse($user->hasRole('Owner'));
        $this->assertTrue($user->can('user.create'));
        $this->assertFalse($user->can('user.delete'));

        $this->assertTrue($user->ability([], ['user.create', 'user.update']));
        $this->assertTrue($user->ability([], ['user.create', 'user.update'], ['validate_all' => true]));
        $this->assertTrue($user->ability([], ['user.create', 'user.update', 'user.delete']));
        $this->assertFalse($user->ability([], ['user.create', 'user.update', 'user.delete'], ['validate_all' => true]));
        $this->assertTrue($user->ability([], ['user.create', 'user.update', 'user.delete'], ['validate_all' => false]));
        $this->assertFalse($user->ability([], ['user.delete']));

        $this->assertTrue($user->ability([], 'user.create,user.update'));
        $this->assertTrue($user->ability([], 'user.create,user.update', ['validate_all' => true]));
        $this->assertTrue($user->ability([], 'user.create,user.update,user.delete'));
        $this->assertFalse($user->ability([], 'user.create,user.update,user.delete', ['validate_all' => true]));
        $this->assertTrue($user->ability([], 'user.create,user.update,user.delete', ['validate_all' => false]));
        $this->assertFalse($user->ability([], 'user.delete'));

        $this->assertTrue($user->ability(['Editor'], ['user.create', 'user.update']));
        $this->assertTrue($user->ability(['Owner'], ['user.create', 'user.update']));
        $this->assertTrue($user->ability(['Editor'], ['user.create', 'user.update'], ['validate_all' => true]));
        $this->assertFalse($user->ability(['Editor', 'Owner'], ['user.create', 'user.update'], ['validate_all' => true]));

        $this->assertTrue($user->ability('Editor,Owner', 'user.create,user.update'));
        $this->assertTrue($user->ability('Owner', 'user.create,user.update'));
        $this->assertTrue($user->ability('Editor', 'user.create,user.update', ['validate_all' => true]));
        $this->assertFalse($user->ability('Editor,Owner', 'user.create,user.update', ['validate_all' => true]));

        $this->assertNotEmpty($user->ability('Editor', 'user.create,user.update', ['validate_all' => true, 'return_type' => 'array']));
        $this->assertCount(2, $user->ability('Editor', 'user.create,user.update', ['validate_all' => true, 'return_type' => 'array']));
        $this->assertCount(2, $user->ability('Editor', 'user.create,user.update', ['validate_all' => true, 'return_type' => 'both']));

        $this->setExpectedException('\InvalidArgumentException');
        $user->ability('Editor', 'user.create,user.update', ['validate_all' => true, 'return_type' => 'other']);
    }

    public function test_it_can_add_multiple_roles()
    {
        $role1 = new \Znck\Trust\Role(['name' => 'Editor']);
        $role2 = new \Znck\Trust\Role(['name' => 'Owner']);

        $this->assertTrue($role1->save());
        $this->assertTrue($role2->save());

        $user = $this->getUser();
        $this->assertTrue($user->save());

        $user->attachRoles([$role1, $role2]);

        $this->assertNotEmpty($user->roles);
        $this->assertTrue($user->hasRole('Editor'));
        $this->assertTrue($user->hasRole('Owner'));

        $this->assertArrayHasKey('roles', $user->toArray());
        $this->assertCount(2, $user->roles);
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}