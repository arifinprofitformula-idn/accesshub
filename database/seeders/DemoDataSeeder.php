<?php

namespace Database\Seeders;

use App\Models\AccessItem;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\Link;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@accesshub.test'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
        );
        $superAdmin->syncRoles(['super_admin']);

        $admin = User::updateOrCreate(
            ['email' => 'admin@accesshub.test'],
            [
                'name' => 'Admin AccessHub',
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
        );
        $admin->syncRoles(['admin']);

        $staff = User::updateOrCreate(
            ['email' => 'staff@accesshub.test'],
            [
                'name' => 'Staff AccessHub',
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
        );
        $staff->syncRoles(['staff']);

        $marketingCategory = Category::where('slug', 'marketing')->first();
        $workspaceCategory = Category::where('slug', 'google-workspace')->first();
        $websiteCategory = Category::where('slug', 'website-hosting')->first();

        $campaignTag = Tag::firstOrCreate(['slug' => 'campaign'], ['name' => 'campaign']);
        $clientTag = Tag::firstOrCreate(['slug' => 'client'], ['name' => 'client']);
        $invoiceTag = Tag::firstOrCreate(['slug' => 'invoice'], ['name' => 'invoice']);
        $landingPageTag = Tag::firstOrCreate(['slug' => 'landingpage'], ['name' => 'landingpage']);

        $adminRole = Role::where('name', 'admin')->first();
        $staffRole = Role::where('name', 'staff')->first();

        $links = [
            [
                'title' => 'Google Sheet Campaign Tracker',
                'url' => 'https://docs.google.com/spreadsheets/d/example-campaign-tracker',
                'description' => 'Tracking campaign harian tim marketing.',
                'category_id' => $marketingCategory?->id,
                'platform' => 'Google Sheets',
                'priority' => 'important',
                'status' => 'active',
                'visibility' => 'internal',
                'owner_name' => 'Marketing Team',
                'created_by' => $admin->id,
                'tags' => [$campaignTag->id, $landingPageTag->id],
                'roles' => [],
            ],
            [
                'title' => 'Client Asset Drive',
                'url' => 'https://drive.google.com/drive/folders/example-client-assets',
                'description' => 'Folder aset klien untuk tim internal.',
                'category_id' => $workspaceCategory?->id,
                'platform' => 'Google Drive',
                'priority' => 'normal',
                'status' => 'active',
                'visibility' => 'role',
                'owner_name' => 'Operations Team',
                'created_by' => $superAdmin->id,
                'tags' => [$clientTag->id],
                'roles' => array_filter([$adminRole?->id]),
            ],
            [
                'title' => 'Hosting Dashboard Utama',
                'url' => 'https://hosting.example.com/dashboard',
                'description' => 'Dashboard untuk memantau hosting dan website utama.',
                'category_id' => $websiteCategory?->id,
                'platform' => 'Hosting Panel',
                'priority' => 'critical',
                'status' => 'needs_review',
                'visibility' => 'internal',
                'owner_name' => 'Website PIC',
                'created_by' => $superAdmin->id,
                'tags' => [$clientTag->id, $invoiceTag->id],
                'roles' => [],
            ],
            [
                'title' => 'Staff Personal SOP Shortcut',
                'url' => 'https://docs.google.com/document/d/example-staff-private-sop',
                'description' => 'Link pribadi untuk kebutuhan operasional staff tertentu.',
                'category_id' => $marketingCategory?->id,
                'platform' => 'Google Docs',
                'priority' => 'normal',
                'status' => 'active',
                'visibility' => 'private',
                'owner_name' => 'Staff AccessHub',
                'created_by' => $staff->id,
                'tags' => [$clientTag->id],
                'roles' => array_filter([$staffRole?->id]),
            ],
        ];

        foreach ($links as $payload) {
            $tagIds = $payload['tags'];
            $roleIds = $payload['roles'];
            unset($payload['tags']);
            unset($payload['roles']);

            $link = Link::updateOrCreate(
                ['title' => $payload['title']],
                $payload + ['last_checked_at' => now()->subDays(3)],
            );

            $link->tags()->sync($tagIds);
            $link->visibleToRoles()->sync($roleIds);
        }

        $accessItems = [
            [
                'platform_name' => 'Google Workspace - Marketing',
                'login_url' => 'https://accounts.google.com/',
                'username' => 'marketing@accesshub.test',
                'category_id' => $workspaceCategory?->id,
                'pic_name' => 'Marketing PIC',
                'sensitivity_level' => 'high',
                'password_location' => 'Bitwarden - Folder Marketing',
                'note' => 'Akun utama untuk akses dokumen tim marketing. Tidak ada password disimpan di AccessHub.',
                'status' => 'active',
                'created_by' => $superAdmin->id,
                'roles' => array_filter([$adminRole?->id]),
            ],
            [
                'platform_name' => 'Canva Brand Team',
                'login_url' => 'https://www.canva.com/login',
                'username' => 'design@accesshub.test',
                'category_id' => $marketingCategory?->id,
                'pic_name' => 'Design PIC',
                'sensitivity_level' => 'medium',
                'password_location' => 'Google Password Manager',
                'note' => 'Gunakan untuk aset desain internal. Password tetap dikelola eksternal.',
                'status' => 'active',
                'created_by' => $admin->id,
                'roles' => array_filter([$staffRole?->id]),
            ],
            [
                'platform_name' => 'Finance Workspace Shared',
                'login_url' => 'https://accounts.google.com/',
                'username' => 'finance@accesshub.test',
                'category_id' => $workspaceCategory?->id,
                'pic_name' => 'Finance PIC',
                'sensitivity_level' => 'high',
                'password_location' => 'Bitwarden - Folder Finance',
                'note' => 'Metadata akses bersama tim finance, tanpa password di database.',
                'status' => 'needs_review',
                'created_by' => $superAdmin->id,
                'roles' => array_filter([$adminRole?->id]),
            ],
        ];

        foreach ($accessItems as $payload) {
            $roleIds = $payload['roles'];
            unset($payload['roles']);

            $accessItem = AccessItem::updateOrCreate(
                ['platform_name' => $payload['platform_name'], 'username' => $payload['username']],
                $payload + ['last_checked_at' => now()->subDays(2)],
            );

            $accessItem->visibleToRoles()->sync($roleIds);
        }

        $favoriteLink = Link::where('title', 'Google Sheet Campaign Tracker')->first();

        if ($favoriteLink) {
            Favorite::firstOrCreate([
                'user_id' => $staff->id,
                'link_id' => $favoriteLink->id,
            ]);
        }
    }
}
