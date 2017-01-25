<?php

namespace Znck\Tests\Trust\Services;

use InvalidArgumentException;
use Znck\Tests\Trust\Models\User;
use Znck\Tests\Trust\TestCase;
use Znck\Trust\Services\PermissionFinder;

class PermissionFinderTest extends TestCase
{
    public function test_it_finds_permissions_for_string()
    {
        $finder = new PermissionFinder('foo');

        $this->assertEquals([
            'foo' => [
                'slug' => 'foo',
                'name' => 'Foo',
            ],
        ], $finder->toArray());
    }

    public function test_it_finds_permissions_for_string_with_extras()
    {
        $finder = new PermissionFinder('foo', [
            'name' => 'Foo Permission', 'Ignore Me', 'description' => 'Foo Description',
        ]);

        $this->assertEquals([
            'foo' => [
                'slug'        => 'foo',
                'name'        => 'Foo Permission',
                'description' => 'Foo Description',
            ],
        ], $finder->toArray());

        $this->assertEquals([
            'slug' => 'foo',
            'name' => 'Foo',
        ], $finder->preparePermission(0, ['slug' => 'foo']));
    }

    public function test_it_throws_exception_if_slug_is_not_found()
    {
        $finder = new PermissionFinder([]);

        $this->expectException(InvalidArgumentException::class);

        $finder->preparePermission(0);
    }

    public function test_it_throws_exception()
    {
        $this->expectException(InvalidArgumentException::class);

        new PermissionFinder(null);
    }

    public function test_it_finds_permissions_for_assoc_array()
    {
        $finder = new PermissionFinder([
            'foo' => 'Foo',
        ]);

        $this->assertEquals([
            'foo' => [
                'slug' => 'foo',
                'name' => 'Foo',
            ],
        ], $finder->toArray());
    }

    public function test_it_finds_permissions_for_array()
    {
        $finder = new PermissionFinder([
            'foo' => [
                'name' => 'Foo Permission', 'Ignore Me', 'description' => 'Foo Description',
            ],
            ['slug' => 'BAR'],
            ['name' => 'Baz Perm'],
            'YAK',
        ]);

        $this->assertEquals([
            'foo' => [
                'slug'        => 'foo',
                'name'        => 'Foo Permission',
                'description' => 'Foo Description',
            ],
            'BAR' => [
                'slug' => 'BAR',
                'name' => 'BAR',
            ],
            'baz_perm' => [
                'slug' => 'baz_perm',
                'name' => 'Baz Perm',
            ],
            'yak' => [
                'name' => 'YAK',
                'slug' => 'yak',
            ],
        ], $finder->toArray());
    }

    public function test_it_finds_for_class()
    {
        $finder = new PermissionFinder(User::class, ['members']);

        $this->assertEquals([
            'user.create'  => ['name' => 'Create User', 'slug' => 'user.create'],
            'user.read'    => ['name' => 'Read User', 'slug' => 'user.read'],
            'user.update'  => ['name' => 'Update User', 'slug' => 'user.update'],
            'user.delete'  => ['name' => 'Delete User', 'slug' => 'user.delete'],
            'user.members' => ['name' => 'Members User', 'slug' => 'user.members'],
        ], $finder->toArray());
    }
}
