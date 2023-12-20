<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DefaultUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $isProd = app()->environment('production');

        $u = User::firstOrCreate([
            'email' => 'admin@pruebas.com',
            'first_name' => 'admin',
            'last_name' => 'admin',
            'password' => $isProd ? bcrypt('admin') : bcrypt('admin'),
            'email_verified_at' => now(),
        ]);

        if (!$u->hasRole(RoleEnum::ADMINISTRATOR->value)) {
            $u->assignRole(RoleEnum::ADMINISTRATOR->value);
        }
    }
}
