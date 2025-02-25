<?php

use App\Models\System\Consts\Permission;
use App\Models\System\Consts\Role;

return [
    'enabled' => env('ROLES_ENABLED', 0),
    'roles' => [
        Role::SUPER_ADMIN => true,
        Role::CONTENT_MANAGER => true,
    ],
    'permissions' => [
        Permission::EDIT_CONTENT,
    ],
    'relations' => [
        Role::SUPER_ADMIN => [], // имеет все возможные разрешения
        Role::CONTENT_MANAGER => [
            Permission::EDIT_CONTENT,
        ],
    ]
];
