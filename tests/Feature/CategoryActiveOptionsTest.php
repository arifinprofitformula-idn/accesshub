<?php

namespace Tests\Feature;

use App\Models\Category;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CategoryActiveOptionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_active_options_recovers_from_corrupt_cached_value(): void
    {
        Category::factory()->create([
            'name' => 'Marketing',
            'is_active' => true,
        ]);

        Category::factory()->create([
            'name' => 'Finance',
            'is_active' => false,
        ]);

        Cache::forever(Category::ACTIVE_OPTIONS_CACHE_KEY, 'corrupt-payload');

        $options = Category::activeOptions();

        $this->assertCount(1, $options);
        $this->assertSame('Marketing', $options->first()->name);

        $cached = Cache::get(Category::ACTIVE_OPTIONS_CACHE_KEY);

        $this->assertIsArray($cached);
        $this->assertSame('Marketing', $cached[0]['name']);
    }

    public function test_active_options_can_read_array_payload_from_cache(): void
    {
        Cache::forever(Category::ACTIVE_OPTIONS_CACHE_KEY, [
            ['id' => 99, 'name' => 'Cached Category'],
        ]);

        $options = Category::activeOptions();

        $this->assertCount(1, $options);
        $this->assertSame(99, $options->first()->id);
        $this->assertSame('Cached Category', $options->first()->name);
    }
}
