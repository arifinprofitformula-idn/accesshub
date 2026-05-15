<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Link;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Link>
 */
class LinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'url' => fake()->url(),
            'description' => fake()->sentence(),
            'category_id' => Category::factory(),
            'platform' => fake()->randomElement(['Google Sheets', 'Canva', 'Dashboard', 'Google Drive']),
            'priority' => fake()->randomElement(['normal', 'important', 'critical']),
            'status' => fake()->randomElement(['active', 'needs_review', 'archived']),
            'visibility' => fake()->randomElement(['internal', 'role', 'private']),
            'owner_name' => fake()->name(),
            'created_by' => User::factory(),
            'last_checked_at' => now()->subDays(fake()->numberBetween(0, 30)),
        ];
    }
}
