<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    protected $roles;

    public function run(): void
    {
        if (!app()->environment('testing')) {
            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        }

        $this->seedRoles();
        $this->seedPermissions();
    }

    protected function seedPermissions()
    {
        $this->createPermission([
            'name' => 'permiso',
            'display_name' => 'permiso',
            'description' => 'permiso',
        ], ['administrator']);
    }

    protected function seedRoles()
    {
        $roles = [
            RoleEnum::ADMINISTRATOR->value,
        ];

        $guardName = config('auth.defaults.guard');

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => $guardName,
            ]);
        }

        $this->command->info('Roles created or updated successfully.');
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    protected function createPermission(array $data, array $roles)
    {
        $permission = Permission::updateOrCreate(
            ['name' => $data['name']],
            [
                'display_name' => $data['display_name'],
                'description' => $data['description'],
                'guard_name' => $data['guard_name'] ?? config('auth.defaults.guard')
            ]
        );

        foreach ($roles as $roleName) {
            $role = Role::findByName($roleName);
            if ($role && !$role->hasPermissionTo($data['name'])) {
                $role->givePermissionTo($permission);
                $this->command->info("Permission '{$data['name']}' assigned to role '{$role->name}'.");
            }
        }
    }
}
