<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Website & Hosting',
            'Google Workspace',
            'Marketing',
            'Finance',
            'Client',
            'Ads',
            'Affiliate',
            'Wakaf',
            'Desain',
            'Legal',
            'Operasional',
            'SOP',
            'Social Media',
        ];

        foreach ($categories as $index => $name) {
            Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => 'Kategori default AccessHub untuk pengelompokan link dan metadata akses.',
                    'icon' => 'heroicon-o-folder',
                    'color' => ['slate', 'sky', 'emerald', 'amber', 'rose'][$index % 5],
                    'is_active' => true,
                ],
            );
        }
    }
}
