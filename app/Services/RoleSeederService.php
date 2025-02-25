<?php

namespace App\Services;

use Illuminate\Support\Collection;
use App\Models\Role;
use App\Models\Permission;
use App\Services\MainService;

class RoleSeederService extends MainService
{
    /**
     * @return bool
     */
    static function createSystemRoles(): bool
    {
        return (new RoleSeederService)->_createSystemRoles();
    }

    /**
     * Создание системных ролей
     * @return bool
     */
    private function _createSystemRoles(): bool
    {
        $rolesAndPermissions = config('roles_and_permissions');

        $roles       = $rolesAndPermissions['roles'];
        $permissions = $rolesAndPermissions['permissions'];

        foreach ($permissions as &$permission) {

            $permission = Permission::findOrCreate($permission);
        }

        $permissions = collect($permissions);

        foreach ($roles as $role => $isAvailable) {

            if ( ! $isAvailable) continue;

            $role = Role::findOrCreate($role);

            $this->syncPermissions($role, $permissions);
        }

        return true;
    }

    /**
     * Синхронизация ролей и разрешений
     * @param Role $role
     * @param Collection $permissions
     */
    private function syncPermissions(Role $role, Collection $permissions)
    {
        $name = $role->name;

        if ($name === 'Super Admin') {
            // Синхроним все существующие разрешения
            // Также даются все права в app/Provider/AuthServiceProvider.php:31
            $neededPermissions = config('roles_and_permissions.permissions');
        } else {
            $neededPermissions = config('roles_and_permissions.relations.' . $name);
        }

        if (empty($neededPermissions)) return;

        $neededPermissions = $permissions->whereIn('name', $neededPermissions);

        $role->syncPermissions($neededPermissions);
    }
}
