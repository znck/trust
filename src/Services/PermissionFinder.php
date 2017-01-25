<?php

namespace Znck\Trust\Services;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Create permission from model class or array or string.
 */
class PermissionFinder implements Arrayable
{
    /**
     * List of permissions.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $permissions;

    /**
     * List of default actions.
     *
     * @var array
     */
    protected $actions;

    /**
     * Find permissions.
     */
    public function __construct($source, $extras = [])
    {
        $this->actions = (array) config('trust.actions');
        if (is_string($source) and class_exists($source)) {
            $this->permissions = $this->getPermissionsFromClass($source, $extras);
        } elseif (is_array($source)) {
            $this->permissions = $this->getPermissionsFromArray($source, $extras);
        } elseif (is_string($source)) {
            $this->permissions = collect([$this->preparePermission($source, $extras)]);
        } else {
            throw new InvalidArgumentException();
        }
    }

    /**
     * Create permissions from class.
     *
     * @param string $class  Fully qualified class Name
     * @param array  $extras Extra actions required.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPermissionsFromClass(string $class, array $extras = [])
    {
        $class = class_basename($class);
        $prefix = str_slug(Str::snake($class), '_');
        $name = ucwords(str_replace('_', ' ', $prefix));

        if (array_values($extras) === $extras) {
            $extras = array_combine($extras, $extras);
        }

        $actions = collect($this->actions)->merge($extras);

        return $actions->map(function ($extras, $action) use ($prefix, $name) {
            $extras = (array) $extras;

            return $this->preparePermission(
                $prefix.'.'.$action,
                ['name' => ucwords($extras['name'] ?? $extras[0] ?? $action).' '.$name] + $extras
            );
        });
    }

    /**
     * Form permissions from array.
     *
     * @param array $source List of permissions.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getPermissionsFromArray(array $source)
    {
        return collect($source)->map(function ($permission, $slug) {
            if (is_string($permission)) {
                $permission = ['name' => $permission];
            }

            return $this->preparePermission($permission['slug'] ?? $slug, $permission);
        });
    }

    /**
     * Fix permission format.
     *
     * @param string|int $slug   Permission slug
     * @param array      $extras Extra attributes.
     *
     * @return array
     */
    public function preparePermission($slug, array $extras = [])
    {
        $permission = [];

        if (is_string($slug)) {
            $permission['slug'] = $slug;
        } elseif (isset($extras['slug'])) {
            $permission['slug'] = $extras['slug'];
        } elseif (isset($extras['name'])) {
            $permission['slug'] = str_slug($extras['name'], '_');
        } else {
            throw new InvalidArgumentException('Cannot add permission without slug.');
        }

        if (isset($extras['name'])) {
            $permission['name'] = trim($extras['name']);
        } else {
            $permission['name'] = ucwords(preg_replace('/[-_.\s]+/', ' ', $permission['slug']));
        }

        foreach ($extras as $key => $value) {
            if (!is_string($key)) {
                unset($extras[$key]);
            }
        }

        return $permission + $extras;
    }

    /**
     * Get array of permissions.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->permissions->keyBy('slug')->toArray();
    }
}
