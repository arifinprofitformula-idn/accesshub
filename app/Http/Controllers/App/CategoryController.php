<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Link;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function quickStore(Request $request): JsonResponse
    {
        abort_unless(
            ($request->user()?->can('create', Link::class) ?? false) || ($request->user()?->can('create', Category::class) ?? false),
            403,
        );

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],
        ], [
            'name.required' => 'Nama kategori tidak boleh kosong.',
            'name.max' => 'Nama kategori maksimal 100 karakter.',
        ]);

        $name = Str::of($validated['name'])->trim()->squish()->toString();

        $existingCategory = Category::query()
            ->whereRaw('LOWER(name) = ?', [Str::lower($name)])
            ->first();

        if ($existingCategory) {
            if (! $existingCategory->is_active) {
                $existingCategory->update(['is_active' => true]);
            }

            return response()->json([
                'id' => $existingCategory->id,
                'name' => $existingCategory->name,
                'existing' => true,
            ]);
        }

        $category = Category::create([
            'name' => $name,
            'slug' => $this->generateUniqueSlug($name),
            'is_active' => true,
        ]);

        return response()->json([
            'id' => $category->id,
            'name' => $category->name,
        ], 201);
    }

    protected function generateUniqueSlug(string $name): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug !== '' ? $baseSlug : 'kategori';
        $suffix = 2;

        while (Category::query()->where('slug', $slug)->exists()) {
            $slug = ($baseSlug !== '' ? $baseSlug : 'kategori').'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }
}
