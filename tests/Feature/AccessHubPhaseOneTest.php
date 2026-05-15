<?php

namespace Tests\Feature;

use App\Models\AccessItem;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\Link;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AccessHubPhaseOneTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_super_admin_can_access_user_management(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super_admin');

        $this->actingAs($user)
            ->get('/admin/users')
            ->assertOk();
    }

    public function test_staff_can_not_access_user_management(): void
    {
        $user = User::factory()->create();
        $user->assignRole('staff');

        $this->actingAs($user)
            ->get('/admin/users')
            ->assertForbidden();
    }

    public function test_staff_can_not_access_admin_panel_routes(): void
    {
        $user = User::factory()->create();
        $user->assignRole('staff');

        $this->actingAs($user)
            ->get('/admin/links')
            ->assertForbidden();
    }

    public function test_link_can_be_created(): void
    {
        $category = Category::factory()->create();
        $user = User::factory()->create();

        $link = Link::create([
            'title' => 'Main Dashboard',
            'url' => 'https://example.com/dashboard',
            'description' => 'Internal dashboard',
            'category_id' => $category->id,
            'platform' => 'Dashboard',
            'priority' => 'important',
            'status' => 'active',
            'visibility' => 'internal',
            'owner_name' => 'Ops Team',
            'created_by' => $user->id,
        ]);

        $this->assertDatabaseHas('links', [
            'id' => $link->id,
            'title' => 'Main Dashboard',
        ]);
    }

    public function test_link_url_must_be_valid(): void
    {
        $this->expectException(ValidationException::class);

        Link::create([
            'title' => 'Broken Link',
            'url' => 'not-a-valid-url',
            'category_id' => Category::factory()->create()->id,
            'platform' => 'Dashboard',
            'priority' => 'normal',
            'status' => 'active',
            'visibility' => 'internal',
        ]);
    }

    public function test_access_item_does_not_have_a_password_field(): void
    {
        $this->assertFalse(Schema::hasColumn('access_items', 'password'));
        $this->assertFalse(in_array('password', (new AccessItem)->getFillable(), true));
    }

    public function test_favorite_can_be_created_by_user(): void
    {
        $user = User::factory()->create();
        $link = Link::factory()->create();

        $favorite = Favorite::create([
            'user_id' => $user->id,
            'link_id' => $link->id,
        ]);

        $this->assertDatabaseHas('favorites', [
            'id' => $favorite->id,
            'user_id' => $user->id,
            'link_id' => $link->id,
        ]);
    }

    public function test_permissions_are_assigned_based_on_role(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $this->assertTrue($admin->can('links.create'));
        $this->assertFalse($staff->can('links.create'));
        $this->assertTrue($staff->can('links.view'));
    }

    public function test_admin_can_not_delete_super_admin(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super_admin');

        $this->assertFalse($admin->can('delete', $superAdmin));
    }

    public function test_inactive_user_fails_login(): void
    {
        $user = User::factory()->inactive()->create();

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
    }
}
