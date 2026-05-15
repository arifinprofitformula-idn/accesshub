<?php

namespace Database\Factories;

use App\Models\AccessItem;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AccessItem>
 */
class AccessItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'platform_name' => fake()->randomElement(['Google Workspace', 'Canva', 'Meta Ads']),
            'login_url' => fake()->url(),
            'username' => fake()->safeEmail(),
            'category_id' => Category::factory(),
            'pic_name' => fake()->name(),
            'sensitivity_level' => fake()->randomElement(['low', 'medium', 'high']),
            'password_location' => fake()->randomElement(['Bitwarden - Folder Marketing', 'Google Password Manager']),
            'note' => fake()->sentence(),
            'status' => fake()->randomElement(['active', 'needs_review', 'archived']),
            'created_by' => User::factory(),
            'last_checked_at' => now()->subDays(fake()->numberBetween(0, 30)),
        ];
    }
}
