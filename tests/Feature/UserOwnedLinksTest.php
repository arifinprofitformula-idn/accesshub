<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Link;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserOwnedLinksTest extends TestCase
{
    use RefreshDatabase;

    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
        $this->category = Category::factory()->create();
    }

    /** Helper — create an approved, active user with the given role. */
    private function makeUser(string $role = 'user'): User
    {
        $user = User::factory()->create(['approved_at' => now()]);
        $user->assignRole($role);

        return $user;
    }

    public function test_user_can_open_create_link_page(): void
    {
        $user = $this->makeUser('user');

        $this->actingAs($user)
            ->get(route('app.links.create'))
            ->assertOk()
            ->assertSeeText('Judul Link')
            ->assertSeeText('Simpan Link');
    }

    public function test_user_can_create_link_and_owner_is_assigned_automatically(): void
    {
        $user = $this->makeUser('user');

        $this->actingAs($user)
            ->post(route('app.links.store'), [
                'title' => 'Docs Roadmap',
                'url' => 'https://docs.example.test/roadmap',
                'category_id' => $this->category->id,
                'description' => 'Roadmap tim produk',
                'visibility' => 'private',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('status', 'Link berhasil disimpan.');

        $this->assertDatabaseHas('links', [
            'title' => 'Docs Roadmap',
            'created_by' => $user->id,
            'visibility' => 'private',
            'status' => 'active',
        ]);
    }

    public function test_dashboard_only_shows_users_own_links(): void
    {
        $user = $this->makeUser('user');
        $other = $this->makeUser('user');

        $ownLink = Link::factory()->create([
            'title' => 'My Private Link',
            'category_id' => $this->category->id,
            'visibility' => 'private',
            'created_by' => $user->id,
            'status' => 'active',
        ]);

        $otherSharedLink = Link::factory()->create([
            'title' => 'Other Shared Link',
            'category_id' => $this->category->id,
            'visibility' => 'internal',
            'created_by' => $other->id,
            'status' => 'active',
        ]);

        $otherPrivateLink = Link::factory()->create([
            'title' => 'Other Private Link',
            'category_id' => $this->category->id,
            'visibility' => 'private',
            'created_by' => $other->id,
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText($ownLink->title)
            ->assertDontSeeText($otherSharedLink->title)
            ->assertDontSeeText($otherPrivateLink->title);
    }

    public function test_user_can_edit_own_link_but_not_other_users_link(): void
    {
        $user = $this->makeUser('user');
        $other = $this->makeUser('user');

        $ownLink = Link::factory()->create([
            'category_id' => $this->category->id,
            'visibility' => 'private',
            'created_by' => $user->id,
            'status' => 'active',
        ]);

        $otherLink = Link::factory()->create([
            'category_id' => $this->category->id,
            'visibility' => 'private',
            'created_by' => $other->id,
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->get(route('app.links.edit', $ownLink))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('app.links.edit', $otherLink))
            ->assertForbidden();
    }

    public function test_user_can_update_and_archive_own_link(): void
    {
        $user = $this->makeUser('user');

        $link = Link::factory()->create([
            'title' => 'Old Link',
            'category_id' => $this->category->id,
            'visibility' => 'private',
            'created_by' => $user->id,
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->put(route('app.links.update', $link), [
                'title' => 'Updated Link',
                'url' => 'https://updated.example.test',
                'category_id' => $this->category->id,
                'description' => 'Updated description',
                'visibility' => 'shared',
            ])
            ->assertRedirect(route('dashboard'));

        $this->actingAs($user)
            ->delete(route('app.links.destroy', $link))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('status', 'Link dipindahkan ke arsip.');

        $this->assertDatabaseHas('links', [
            'id' => $link->id,
            'title' => 'Updated Link',
            'visibility' => 'internal',
            'status' => 'archived',
        ]);
    }

    public function test_super_admin_can_see_all_links_including_ownerless_legacy_links(): void
    {
        $superAdmin = $this->makeUser('super_admin');
        $owner = $this->makeUser('user');

        $owned = Link::factory()->create([
            'title' => 'Owned Link',
            'category_id' => $this->category->id,
            'visibility' => 'private',
            'created_by' => $owner->id,
            'status' => 'active',
        ]);

        $ownerless = Link::factory()->create([
            'title' => 'Legacy Link',
            'category_id' => $this->category->id,
            'visibility' => 'internal',
            'created_by' => null,
            'status' => 'active',
        ]);

        $this->actingAs($superAdmin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText($owned->title)
            ->assertSeeText($ownerless->title);
    }

    public function test_search_only_returns_users_own_links(): void
    {
        $user = $this->makeUser('user');
        $other = $this->makeUser('user');

        $ownLink = Link::factory()->create([
            'title' => 'Campaign Plan Own',
            'category_id' => $this->category->id,
            'visibility' => 'private',
            'created_by' => $user->id,
            'status' => 'active',
        ]);

        $otherLink = Link::factory()->create([
            'title' => 'Campaign Plan Other',
            'category_id' => $this->category->id,
            'visibility' => 'internal',
            'created_by' => $other->id,
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard', ['search' => 'Campaign']))
            ->assertOk()
            ->assertSeeText($ownLink->title)
            ->assertDontSeeText($otherLink->title);
    }

    public function test_pending_user_cannot_access_dashboard(): void
    {
        $user = User::factory()->create(['approved_at' => null, 'is_active' => true]);
        $user->assignRole('user');

        $this->actingAs($user)
            ->get(route('app.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_inactive_user_cannot_access_dashboard(): void
    {
        $user = User::factory()->create(['approved_at' => now(), 'is_active' => false]);
        $user->assignRole('user');

        $this->actingAs($user)
            ->get(route('app.dashboard'))
            ->assertRedirect(route('login'));
    }
}
