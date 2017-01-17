<?php

namespace Znck\Trust\Traits;

trait PermissionsHelper {
    /**
     * Fetch permission ids from given permissions.
     *
     * @param  int|string|Permission|Collection $permissions List of permissions
     * @return array List of model keys
     */
    private function getPermissionIds($permissions): array {
        if ($permissions instanceof Model) {
            $permissions = $permissions->getKey();
        }

        if ($permissions instanceof Collection) {
            $model = app(PermissionContract::class);

            $permissions = $permissions->pluck($model->getKeyName())->toArray();
        }

        // TODO: Add support for UUID keys.

        if (is_string(array_first((array) $permissions))) {
            $model = app(PermissionContract::class);

            $permissions = $model->whereIn('slug', (array) $permissions)->get()->pluck($model->getKeyName())->toArray();
        }

        return (array) $permissions;
    }
}
