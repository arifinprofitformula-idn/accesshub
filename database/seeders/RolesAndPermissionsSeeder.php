<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('permissions') || ! Schema::hasTable('roles')) {
            throw new RuntimeException('Tabel permissions/roles belum tersedia. Jalankan php artisan migrate --force sampai sukses penuh sebelum menjalankan seeder.');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'dashboard.view',
            'users.view',
            'users.create',
            'users.update',
            'categories.view',
            'categories.create',
            'categories.update',
            'categories.delete',
            'links.view',
            'links.create',
            'links.update',
            'links.delete',
            'links.archive',
            'links.open',
            'access_items.view',
            'access_items.create',
            'access_items.update',
            'access_items.delete',
            'access_items.archive',
            'access_items.open',
            'favorites.manage',
            'activity_logs.view',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $roles = [
            'super_admin' => $permissions,
            'admin' => [
                'dashboard.view',
                'categories.view',
                'categories.create',
                'categories.update',
                'categories.delete',
                'links.view',
                'links.create',
                'links.update',
                'links.delete',
                'links.archive',
                'links.open',
                'access_items.view',
                'access_items.create',
                'access_items.update',
                'access_items.delete',
                'access_items.archive',
                'access_items.open',
                'favorites.manage',
            ],
            // user = primary role for regular users (link asset management)
            'user' => [
                'dashboard.view',
                'links.view',
                'links.create',
                'links.update',
                'links.delete',
                'links.archive',
                'links.open',
                'favorites.manage',
            ],
            // staff kept for backward compatibility with existing user accounts
            'staff' => [
                'dashboard.view',
                'links.view',
                'links.create',
                'links.update',
                'links.delete',
                'links.archive',
                'links.open',
                'favorites.manage',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::findOrCreate($roleName, 'web');
            $role->syncPermissions(
                Permission::query()
                    ->whereIn('name', $rolePermissions)
                    ->where('guard_name', 'web')
                    ->get(),
            );
        }
    }
}
