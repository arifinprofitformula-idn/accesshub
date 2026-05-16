<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function quickStore(Request $request): JsonResponse
    {
        $this->authorize('create', Category::class);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories', 'name'),
            ],
        ], [
            'name.required' => 'Nama kategori tidak boleh kosong.',
            'name.max' => 'Nama kategori maksimal 100 karakter.',
            'name.unique' => 'Kategori dengan nama ini sudah ada.',
        ]);

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'is_active' => true,
        ]);

        return response()->json([
            'id' => $category->id,
            'name' => $category->name,
        ], 201);
    }
}
