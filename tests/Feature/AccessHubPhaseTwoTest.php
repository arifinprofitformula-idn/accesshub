<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Link;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AccessHubPhaseTwoTest extends TestCase
{
    use RefreshDatabase;

    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->category = Category::factory()->create();
    }

    public function test_staff_only_sees_links_allowed_by_visibility_rules(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $internal = Link::factory()->create([
            'title' => 'Internal Link',
            'category_id' => $this->category->id,
            'visibility' => 'internal',
            'created_by' => $admin->id,
        ]);

        $roleOnly = Link::factory()->create([
            'title' => 'Admin Only Link',
            'category_id' => $this->category->id,
            'visibility' => 'role',
            'created_by' => $admin->id,
        ]);
        $roleOnly->visibleToRoles()->sync([Role::where('name', 'admin')->value('id')]);

        $privateOwn = Link::factory()->create([
            'title' => 'Private Own Link',
            'category_id' => $this->category->id,
            'visibility' => 'private',
            'created_by' => $staff->id,
        ]);

        $privateOther = Link::factory()->create([
            'title' => 'Private Other Link',
            'category_id' => $this->category->id,
            'visibility' => 'private',
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($staff)->get(route('app.links.index'));

        $response->assertOk();
        $response->assertSeeText($internal->title);
        $response->assertSeeText($privateOwn->title);
        $response->assertDontSeeText($roleOnly->title);
        $response->assertDontSeeText($privateOther->title);
    }

    public function test_user_can_create_link_from_simplified_app_form(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $response = $this->actingAs($staff)->post(route('app.links.store'), [
            'title' => 'Proposal Klien A',
            'url' => 'https://docs.google.com/document/d/123',
            'category_id' => $this->category->id,
            'description' => 'Dokumen proposal utama',
            'visibility' => 'shared',
            'tags' => 'proposal, client-a',
        ]);

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('links', [
            'title' => 'Proposal Klien A',
            'category_id' => $this->category->id,
            'created_by' => $staff->id,
            'visibility' => 'internal',
            'status' => 'active',
        ]);
    }

    public function test_admin_can_access_filament_link_manager(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get('/admin/links')
            ->assertOk();
    }

    public function test_staff_can_view_role_scoped_link_when_role_matches(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $roleLink = Link::factory()->create([
            'title' => 'Staff Role Link',
            'category_id' => $this->category->id,
            'visibility' => 'role',
            'created_by' => $staff->id,
        ]);
        $roleLink->visibleToRoles()->sync([Role::where('name', 'staff')->value('id')]);

        $this->actingAs($staff)
            ->get(route('app.links.index'))
            ->assertSeeText($roleLink->title);
    }

    public function test_user_can_toggle_favorite_from_internal_links_page(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $link = Link::factory()->create([
            'category_id' => $this->category->id,
            'visibility' => 'internal',
            'created_by' => $staff->id,
        ]);

        $this->actingAs($staff)
            ->post(route('app.links.favorite.toggle', $link))
            ->assertRedirect();

        $this->assertDatabaseHas('favorites', [
            'user_id' => $staff->id,
            'link_id' => $link->id,
        ]);
    }

    public function test_favorite_filter_only_uses_current_user_favorites(): void
    {
        $staffA = User::factory()->create();
        $staffA->assignRole('staff');

        $staffB = User::factory()->create();
        $staffB->assignRole('staff');

        $link = Link::factory()->create([
            'title' => 'Pinned By A',
            'category_id' => $this->category->id,
            'visibility' => 'internal',
            'created_by' => $staffA->id,
        ]);

        $staffA->favorites()->create(['link_id' => $link->id]);

        $this->actingAs($staffA)
            ->get(route('app.links.index', ['favorites' => 1]))
            ->assertOk()
            ->assertSeeText($link->title);

        $this->actingAs($staffB)
            ->get(route('app.links.index', ['favorites' => 1]))
            ->assertOk()
            ->assertDontSeeText($link->title);
    }

    public function test_dashboard_shows_shared_links_from_other_users(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $owner = User::factory()->create();
        $owner->assignRole('staff');

        $sharedLink = Link::factory()->create([
            'title' => 'Shared Workspace Link',
            'category_id' => $this->category->id,
            'visibility' => 'internal',
            'created_by' => $owner->id,
            'status' => 'active',
        ]);

        $this->actingAs($staff)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText($sharedLink->title);
    }

    public function test_search_by_title_works_on_dashboard(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $proposalLink = Link::factory()->create([
            'title' => 'Proposal Marketing 2026',
            'category_id' => $this->category->id,
            'visibility' => 'internal',
            'created_by' => $staff->id,
            'status' => 'active',
        ]);

        $otherLink = Link::factory()->create([
            'title' => 'Finance Sheet',
            'category_id' => $this->category->id,
            'visibility' => 'internal',
            'created_by' => $staff->id,
            'status' => 'active',
        ]);

        $this->actingAs($staff)
            ->get(route('dashboard', ['search' => 'Proposal']))
            ->assertOk()
            ->assertSeeText($proposalLink->title)
            ->assertDontSeeText($otherLink->title);
    }

    public function test_category_filter_works_on_dashboard(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $marketingCategory = Category::factory()->create(['name' => 'Marketing']);
        $financeCategory = Category::factory()->create(['name' => 'Keuangan']);

        $marketingLink = Link::factory()->create([
            'title' => 'Campaign Planner',
            'category_id' => $marketingCategory->id,
            'visibility' => 'internal',
            'created_by' => $staff->id,
            'status' => 'active',
        ]);

        $financeLink = Link::factory()->create([
            'title' => 'Budget Sheet',
            'category_id' => $financeCategory->id,
            'visibility' => 'internal',
            'created_by' => $staff->id,
            'status' => 'active',
        ]);

        $this->actingAs($staff)
            ->get(route('dashboard', ['category' => $marketingCategory->id]))
            ->assertOk()
            ->assertSeeText($marketingLink->title)
            ->assertDontSeeText($financeLink->title);
    }

    public function test_user_can_not_edit_link_owned_by_other_user(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $owner = User::factory()->create();
        $owner->assignRole('staff');

        $link = Link::factory()->create([
            'category_id' => $this->category->id,
            'visibility' => 'internal',
            'created_by' => $owner->id,
            'status' => 'active',
        ]);

        $this->actingAs($staff)
            ->get(route('app.links.edit', $link))
            ->assertForbidden();
    }

    public function test_open_link_route_logs_activity(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $link = Link::factory()->create([
            'url' => 'https://example.com/internal-link',
            'category_id' => $this->category->id,
            'visibility' => 'internal',
            'created_by' => $staff->id,
        ]);

        $response = $this->actingAs($staff)->get(route('app.links.open', $link));

        $response->assertRedirect('https://example.com/internal-link');

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'links',
            'event' => 'opened',
            'description' => 'Link opened',
            'subject_type' => Link::class,
            'subject_id' => $link->id,
        ]);
    }

    public function test_archiving_link_creates_archived_activity_event(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $link = Link::factory()->create([
            'category_id' => $this->category->id,
            'status' => 'active',
            'visibility' => 'internal',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin);
        $link->update(['status' => 'archived']);

        $this->assertDatabaseHas('links', [
            'id' => $link->id,
            'status' => 'archived',
        ]);

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'links',
            'event' => 'archived',
            'description' => 'Link archived',
            'subject_type' => Link::class,
            'subject_id' => $link->id,
        ]);
    }

    public function test_link_created_by_is_filled_automatically_for_authenticated_user(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin);

        $link = Link::create([
            'title' => 'Auto Creator Link',
            'url' => 'https://example.com/auto-creator',
            'category_id' => $this->category->id,
            'platform' => 'Docs',
            'priority' => 'normal',
            'status' => 'active',
            'visibility' => 'internal',
        ]);

        $this->assertSame($admin->id, $link->created_by);
    }

    public function test_link_activity_log_does_not_store_full_change_payloads(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin);

        $link = Link::factory()->create([
            'category_id' => $this->category->id,
            'status' => 'active',
            'visibility' => 'internal',
            'created_by' => $admin->id,
        ]);

        $link->update([
            'status' => 'needs_review',
            'description' => 'Updated note that should not be stored as a full diff payload.',
        ]);

        $activity = \Spatie\Activitylog\Models\Activity::query()
            ->where('log_name', 'links')
            ->where('subject_type', Link::class)
            ->where('subject_id', $link->id)
            ->where('event', 'updated')
            ->latest('id')
            ->first();

        $this->assertNotNull($activity);
        $this->assertArrayNotHasKey('attributes', $activity->properties->toArray());
        $this->assertArrayNotHasKey('old', $activity->properties->toArray());
        $this->assertSame(['status'], Arr::wrap($activity->properties['changed_fields']));
    }
}
