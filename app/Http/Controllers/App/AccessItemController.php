<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\AccessItem;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AccessItemController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', AccessItem::class);

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
            'status' => ['nullable', 'in:active,needs_review,archived'],
            'sensitivity' => ['nullable', 'in:low,medium,high'],
            'pic' => ['nullable', 'string', 'max:255'],
        ]);

        $user = $request->user();

        $accessItems = AccessItem::query()
            ->with([
                'category:id,name',
                'creator:id,name',
                'visibleToRoles:id,name',
            ])
            ->visibleTo($user)
            ->search($validated['search'] ?? null)
            ->when($validated['category'] ?? null, fn (Builder $query, int $categoryId) => $query->where('category_id', $categoryId))
            ->when($validated['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($validated['sensitivity'] ?? null, fn (Builder $query, string $sensitivity) => $query->where('sensitivity_level', $sensitivity))
            ->when($validated['pic'] ?? null, fn (Builder $query, string $pic) => $query->where('pic_name', $pic))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = Category::activeOptions();

        $pics = AccessItem::query()
            ->visibleTo($user)
            ->whereNotNull('pic_name')
            ->where('pic_name', '!=', '')
            ->distinct()
            ->orderBy('pic_name')
            ->pluck('pic_name');

        return view('app.access-items.index', [
            'accessItems' => $accessItems,
            'categories' => $categories,
            'pics' => $pics,
            'filters' => $validated,
        ]);
    }

    public function open(Request $request, AccessItem $accessItem): RedirectResponse
    {
        $this->authorize('open', $accessItem);

        abort_if(blank($accessItem->login_url), 404);

        activity('access_items')
            ->causedBy($request->user())
            ->performedOn($accessItem)
            ->event('opened')
            ->withProperties([
                'ip_address' => $request->ip(),
                'opened_via' => 'internal_app',
            ])
            ->log('Access item opened');

        return redirect()->away($accessItem->login_url);
    }
}
