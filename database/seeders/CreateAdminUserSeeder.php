<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            ['name' => 'Afrath', 'password' => bcrypt('123456')]
        );

        $role = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);

        $permissions = Permission::pluck('id', 'id')->all();
        $role->syncPermissions($permissions);

        $user->assignRole($role);
    }
}