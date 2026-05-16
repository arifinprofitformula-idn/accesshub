<?php

namespace Tests\Feature;

use App\Models\User;
use App\Filament\Resources\Users\Pages\ListUsers;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function makeAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        return $admin;
    }

    private function makeSuperAdmin(): User
    {
        $sa = User::factory()->create();
        $sa->assignRole('super_admin');

        return $sa;
    }

    private function makeUser(string $role = 'user', bool $approved = true): User
    {
        $user = $approved
            ? User::factory()->create()
            : User::factory()->pending()->create();
        $user->assignRole($role);

        return $user;
    }

    // --- Admin panel access ---

    public function test_admin_can_access_filament_user_list(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)
            ->get('/admin/users')
            ->assertOk();
    }

    public function test_regular_user_cannot_access_admin_panel(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_pending_user_cannot_access_admin_panel(): void
    {
        $pending = $this->makeUser('user', false);

        $this->actingAs($pending)
            ->get('/admin')
            ->assertForbidden();
    }

    // --- Admin cannot access link/category resources ---

    public function test_admin_cannot_access_filament_link_resource(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)
            ->get('/admin/links')
            ->assertForbidden();
    }

    public function test_admin_cannot_access_filament_category_resource(): void
    {
        $admin = $this->makeAdmin();

        $this->actingAs($admin)
            ->get('/admin/categories')
            ->assertForbidden();
    }

    public function test_super_admin_can_access_filament_link_resource(): void
    {
        $sa = $this->makeSuperAdmin();

        $this->actingAs($sa)
            ->get('/admin/links')
            ->assertOk();
    }

    // --- Admin cannot manage super_admin ---

    public function test_admin_cannot_see_super_admin_in_user_list(): void
    {
        $admin = $this->makeAdmin();
        $superAdmin = $this->makeSuperAdmin();

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertOk()
            ->assertDontSeeText($superAdmin->email);
    }

    public function test_super_admin_can_see_all_users_in_list(): void
    {
        $superAdmin = $this->makeSuperAdmin();
        $admin = $this->makeAdmin();
        $user = $this->makeUser();

        $this->actingAs($superAdmin)
            ->get('/admin/users')
            ->assertOk()
            ->assertSeeText($admin->email)
            ->assertSeeText($user->email);
    }

    // --- Redirect after login ---

    public function test_admin_is_redirected_to_admin_panel_after_login(): void
    {
        $admin = $this->makeAdmin();

        $this->post(route('login'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect('/admin/users');
    }

    public function test_user_is_redirected_to_app_dashboard_after_login(): void
    {
        $user = $this->makeUser();

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('app.dashboard'));
    }

    public function test_super_admin_is_redirected_to_admin_user_management_after_login(): void
    {
        $superAdmin = $this->makeSuperAdmin();

        $this->post(route('login'), [
            'email' => $superAdmin->email,
            'password' => 'password',
        ])->assertRedirect('/admin/users');
    }

    public function test_pending_user_cannot_login_from_login_form(): void
    {
        $pending = $this->makeUser('user', false);

        $this->from(route('login'))
            ->post(route('login'), [
                'email' => $pending->email,
                'password' => 'password',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_admin_can_approve_pending_user_from_user_management(): void
    {
        $admin = $this->makeAdmin();
        $pending = $this->makeUser('user', false);

        $this->actingAs($admin);

        Livewire::test(ListUsers::class)
            ->callTableAction('approve', $pending);

        $pending->refresh();

        $this->assertNotNull($pending->approved_at);
        $this->assertTrue($pending->is_active);
    }

    // --- Approval guard ---

    public function test_pending_user_is_logged_out_and_shown_approval_message(): void
    {
        $pending = $this->makeUser('user', false);

        $this->actingAs($pending)
            ->get(route('app.dashboard'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_inactive_user_is_logged_out_and_shown_inactive_message(): void
    {
        $inactive = User::factory()->inactive()->create();
        $inactive->assignRole('user');

        $this->actingAs($inactive)
            ->get(route('app.dashboard'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

}
