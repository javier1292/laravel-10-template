<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as Model;
use Spatie\Permission\Contracts\Permission as PermissionContract;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

class Permission extends Model
{
    public $guard_name = 'web';

    public static function findByName(string $name, $guardName = null): PermissionContract
    {
        $guardName = 'web';
        $permission = static::getPermission(['name' => $name, 'guard_name' => $guardName]);
        if (!$permission) {
            throw PermissionDoesNotExist::create($name, $guardName);
        }

        return $permission;
    }
}
