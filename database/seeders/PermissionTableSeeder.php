<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
           'role-list',
           'role-create',
           'role-edit',
           'role-delete',
           'product-list',
           'product-create',
           'product-edit',
           'product-delete',
           'student-list',
           'student-create',
           'student-edit',
           'student-delete',
        ];

        foreach ($permissions as $permission) {
             // firstOrCreate = safe to run again, no duplicate error
             Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }
}