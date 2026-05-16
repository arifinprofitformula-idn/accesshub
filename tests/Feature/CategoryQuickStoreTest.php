<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryQuickStoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function makeUser(string $role = 'user'): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    public function test_regular_user_can_quick_store_category_from_link_form(): void
    {
        $user = $this->makeUser('user');

        $response = $this->actingAs($user)
            ->postJson(route('app.categories.quick-store'), [
                'name' => 'Campaign Assets',
            ]);

        $response
            ->assertCreated()
            ->assertJson([
                'name' => 'Campaign Assets',
            ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Campaign Assets',
            'is_active' => true,
        ]);
    }

    public function test_quick_store_returns_existing_category_instead_of_failing(): void
    {
        $user = $this->makeUser('user');

        $existing = Category::factory()->create([
            'name' => 'Shared Docs',
            'slug' => 'shared-docs',
            'is_active' => false,
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('app.categories.quick-store'), [
                'name' => '  shared docs  ',
            ]);

        $response
            ->assertOk()
            ->assertJson([
                'id' => $existing->id,
                'name' => 'Shared Docs',
                'existing' => true,
            ]);

        $this->assertDatabaseHas('categories', [
            'id' => $existing->id,
            'name' => 'Shared Docs',
            'is_active' => true,
        ]);
    }
}
