<?php

namespace Tests\Feature;

use App\Models\AccessItem;
use App\Models\Category;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AccessHubPhaseThreeTest extends TestCase
{
    use RefreshDatabase;

    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->category = Category::factory()->create();
    }

    public function test_admin_can_access_filament_access_item_manager(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get('/admin/access-items')
            ->assertOk();
    }

    public function test_staff_only_sees_access_items_granted_to_them(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $staffVisible = AccessItem::factory()->create([
            'platform_name' => 'Staff Visible Item',
            'category_id' => $this->category->id,
            'created_by' => $admin->id,
        ]);
        $staffVisible->visibleToRoles()->sync([Role::where('name', 'staff')->value('id')]);

        $adminOnly = AccessItem::factory()->create([
            'platform_name' => 'Admin Only Item',
            'category_id' => $this->category->id,
            'created_by' => $admin->id,
        ]);
        $adminOnly->visibleToRoles()->sync([Role::where('name', 'admin')->value('id')]);

        $privateOwn = AccessItem::factory()->create([
            'platform_name' => 'Staff Own Item',
            'category_id' => $this->category->id,
            'created_by' => $staff->id,
        ]);

        $response = $this->actingAs($staff)->get(route('app.access-items.index'));

        $response->assertOk();
        $response->assertSeeText($staffVisible->platform_name);
        $response->assertSeeText($privateOwn->platform_name);
        $response->assertDontSeeText($adminOnly->platform_name);
    }

    public function test_access_item_open_route_logs_activity(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $accessItem = AccessItem::factory()->create([
            'platform_name' => 'Staff Open Item',
            'login_url' => 'https://accounts.google.com/',
            'category_id' => $this->category->id,
            'created_by' => $staff->id,
        ]);
        $accessItem->visibleToRoles()->sync([Role::where('name', 'staff')->value('id')]);

        $response = $this->actingAs($staff)->get(route('app.access-items.open', $accessItem));

        $response->assertRedirect('https://accounts.google.com/');

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'access_items',
            'event' => 'opened',
            'description' => 'Access item opened',
            'subject_type' => AccessItem::class,
            'subject_id' => $accessItem->id,
        ]);
    }

    public function test_archiving_access_item_creates_archived_activity_event(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $accessItem = AccessItem::factory()->create([
            'category_id' => $this->category->id,
            'status' => 'active',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin);
        $accessItem->update(['status' => 'archived']);

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'access_items',
            'event' => 'archived',
            'description' => 'Access item archived',
            'subject_type' => AccessItem::class,
            'subject_id' => $accessItem->id,
        ]);
    }

    public function test_access_items_table_has_no_password_column(): void
    {
        $this->assertFalse(Schema::hasColumn('access_items', 'password'));
        $this->assertFalse(in_array('password', (new AccessItem)->getFillable(), true));
    }

    public function test_access_item_login_url_must_be_valid(): void
    {
        $this->expectException(ValidationException::class);

        AccessItem::create([
            'platform_name' => 'Broken Access Item',
            'login_url' => 'not-a-valid-url',
            'category_id' => $this->category->id,
            'sensitivity_level' => 'medium',
            'status' => 'active',
        ]);
    }

    public function test_access_item_created_by_is_filled_automatically_for_authenticated_user(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin);

        $accessItem = AccessItem::create([
            'platform_name' => 'Auto Creator Access',
            'login_url' => 'https://accounts.google.com',
            'category_id' => $this->category->id,
            'pic_name' => 'Ops PIC',
            'sensitivity_level' => 'medium',
            'password_location' => 'Bitwarden - Ops',
            'status' => 'active',
        ]);

        $this->assertSame($admin->id, $accessItem->created_by);
    }

    public function test_access_item_activity_log_does_not_store_sensitive_fields(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin);

        $accessItem = AccessItem::factory()->create([
            'category_id' => $this->category->id,
            'created_by' => $admin->id,
            'username' => 'secret@example.test',
            'password_location' => 'Bitwarden - Secret',
            'note' => 'Sensitive operational note',
        ]);

        $accessItem->update([
            'username' => 'changed@example.test',
            'password_location' => 'Changed Location',
            'note' => 'Changed sensitive note',
            'status' => 'needs_review',
        ]);

        $activity = \Spatie\Activitylog\Models\Activity::query()
            ->where('log_name', 'access_items')
            ->where('subject_type', AccessItem::class)
            ->where('subject_id', $accessItem->id)
            ->where('event', 'updated')
            ->latest('id')
            ->first();

        $this->assertNotNull($activity);
        $properties = $activity->properties->toArray();

        $this->assertArrayNotHasKey('attributes', $properties);
        $this->assertArrayNotHasKey('old', $properties);
        $this->assertSame(['status'], $properties['changed_fields']);
        $this->assertStringNotContainsString('changed@example.test', json_encode($properties));
        $this->assertStringNotContainsString('Changed Location', json_encode($properties));
        $this->assertStringNotContainsString('Changed sensitive note', json_encode($properties));
    }
}
