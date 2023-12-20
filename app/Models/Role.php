<?php

namespace App\Models;

use Spatie\Permission\Models\Role as Model;
use Spatie\Permission\Contracts\Role as RoleContract;
use Spatie\Permission\Exceptions\RoleDoesNotExist;

class Role extends Model
{
    public $guard_name = 'web';

    public static function findByName(string $name, $guardName = null): RoleContract
    {
        $role = static::findByParam(['name' => $name, 'guard_name' => 'web']);

        if (!$role) {
            throw RoleDoesNotExist::named($name);
        }

        return $role;
    }
}
