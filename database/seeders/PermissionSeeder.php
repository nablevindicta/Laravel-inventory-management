<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cache
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // === Daftar Permission ===
        $permissions = [
            'index-dashboard',

            'index-product', 'create-product', 'update-product', 'delete-product',
            'index-category', 'create-category', 'update-category', 'delete-category',
            'index-supplier', 'create-supplier', 'update-supplier', 'delete-supplier',
            'index-stock', 'create-stock', 'update-stock', 'delete-stock',
            'index-order', 'create-order', 'update-order', 'delete-order',
            'index-transaction', 'delete-transaction', 'export-transaction-pdf',
            'index-stockopname', 'create-stockopname', 'export-stockopname-pdf',

            // Kelola sistem (hanya Super Admin)
            'index-user', 'create-user', 'update-user', 'delete-user',
            'index-role', 'create-role', 'update-role', 'delete-role',
            'index-permission', 'create-permission', 'update-permission', 'delete-permission',
            'update-setting',
        ];

        // Buat semua permission
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // === Buat Role ===
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $admin = Role::firstOrCreate(['name' => 'Admin']);

        // === Assign Permission ===

        // Super Admin: semua permission
        $superAdmin->syncPermissions($permissions);

        // Admin: semua permission kecuali manajemen user, role, permission
        $adminPermissions = collect($permissions)->reject(function ($permission) {
            return str_starts_with($permission, 'index-user') ||
                   str_starts_with($permission, 'create-user') ||
                   str_starts_with($permission, 'update-user') ||
                   str_starts_with($permission, 'delete-user') ||
                   str_starts_with($permission, 'index-role') ||
                   str_starts_with($permission, 'create-role') ||
                   str_starts_with($permission, 'update-role') ||
                   str_starts_with($permission, 'delete-role') ||
                   str_starts_with($permission, 'index-permission') ||
                   str_starts_with($permission, 'create-permission') ||
                   str_starts_with($permission, 'update-permission') ||
                   str_starts_with($permission, 'delete-permission');
        })->toArray();

        $admin->syncPermissions($adminPermissions);

        // === Opsional: Buat Super Admin User Otomatis ===
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'department' => 'Umum',
            ]
        );

        $user->assignRole('Super Admin');

        $adminUser = \App\Models\User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'department' => 'Umum',
            ]
        );

        $adminUser->assignRole('Admin');

        $this->command->info('Roles, Permissions, and Users created successfully.');
    }
}