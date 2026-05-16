<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DemoSeederUsersTest extends TestCase
{
    use RefreshDatabase;

    public function test_demo_users_are_approved_and_active_after_seeding(): void
    {
        $this->seed(DatabaseSeeder::class);

        foreach ([
            'superadmin@accesshub.test' => 'super_admin',
            'admin@accesshub.test' => 'admin',
            'staff@accesshub.test' => 'staff',
        ] as $email => $role) {
            $user = DB::table('users')->where('email', $email)->first();

            $this->assertNotNull($user, "User {$email} tidak ditemukan.");
            $this->assertTrue((bool) $user->is_active, "User {$email} harus aktif.");
            $this->assertNotNull($user->approved_at, "User {$email} harus approved.");

            $this->assertDatabaseHas('model_has_roles', [
                'model_type' => 'App\\Models\\User',
                'model_id' => $user->id,
                'role_id' => DB::table('roles')->where('name', $role)->value('id'),
            ]);
        }
    }
}
