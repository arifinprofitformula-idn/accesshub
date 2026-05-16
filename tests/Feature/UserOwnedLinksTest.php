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

    public function test_user_can_open_create_link_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole('staff');

        $this->actingAs($user)
            ->get(route('app.links.create'))
            ->assertOk()
            ->assertSeeText('Judul Link')
            ->assertSeeText('Simpan Link');
    }

    public function test_user_can_create_link_and_owner_is_assigned_automatically(): void
    {
        $user = User::factory()->create();
        $user->assignRole('staff');

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

    public function test_dashboard_only_shows_users_private_links_and_allowed_shared_links(): void
    {
        $user = User::factory()->create();
        $user->assignRole('staff');

        $other = User::factory()->create();
        $other->assignRole('staff');

        $ownLink = Link::factory()->create([
            'title' => 'My Private Link',
            'category_id' => $this->category->id,
            'visibility' => 'private',
            'created_by' => $user->id,
            'status' => 'active',
        ]);

        $sharedLink = Link::factory()->create([
            'title' => 'Shared Team Link',
            'category_id' => $this->category->id,
            'visibility' => 'internal',
            'created_by' => $other->id,
            'status' => 'active',
        ]);

        $privateOther = Link::factory()->create([
            'title' => 'Other Private Link',
            'category_id' => $this->category->id,
            'visibility' => 'private',
            'created_by' => $other->id,
            'status' => 'active',
        ]);

        $ownerless = Link::factory()->create([
            'title' => 'Ownerless Legacy Link',
            'category_id' => $this->category->id,
            'visibility' => 'internal',
            'created_by' => null,
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText($ownLink->title)
            ->assertSeeText($sharedLink->title)
            ->assertDontSeeText($privateOther->title)
            ->assertDontSeeText($ownerless->title);
    }

    public function test_user_can_edit_own_link_but_not_other_users_private_link(): void
    {
        $user = User::factory()->create();
        $user->assignRole('staff');

        $other = User::factory()->create();
        $other->assignRole('staff');

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
        $user = User::factory()->create();
        $user->assignRole('staff');

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

    public function test_admin_can_see_all_links_including_ownerless_legacy_links(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $owner = User::factory()->create();
        $owner->assignRole('staff');

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

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText($owned->title)
            ->assertSeeText($ownerless->title);
    }

    public function test_search_and_category_filter_only_return_links_the_user_can_see(): void
    {
        $user = User::factory()->create();
        $user->assignRole('staff');

        $other = User::factory()->create();
        $other->assignRole('staff');

        $marketing = Category::factory()->create(['name' => 'Marketing']);
        $finance = Category::factory()->create(['name' => 'Finance']);

        $visible = Link::factory()->create([
            'title' => 'Campaign Plan',
            'category_id' => $marketing->id,
            'visibility' => 'internal',
            'created_by' => $other->id,
            'status' => 'active',
        ]);

        $hidden = Link::factory()->create([
            'title' => 'Campaign Secret',
            'category_id' => $marketing->id,
            'visibility' => 'private',
            'created_by' => $other->id,
            'status' => 'active',
        ]);

        $otherCategory = Link::factory()->create([
            'title' => 'Budget Board',
            'category_id' => $finance->id,
            'visibility' => 'private',
            'created_by' => $user->id,
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard', ['search' => 'Campaign']))
            ->assertOk()
            ->assertSeeText($visible->title)
            ->assertDontSeeText($hidden->title)
            ->assertDontSeeText($otherCategory->title);

        $this->actingAs($user)
            ->get(route('dashboard', ['category' => $marketing->id]))
            ->assertOk()
            ->assertSeeText($visible->title)
            ->assertDontSeeText($hidden->title)
            ->assertDontSeeText($otherCategory->title);
    }
}
