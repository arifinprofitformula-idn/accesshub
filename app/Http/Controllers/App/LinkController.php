<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Link;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Link::class);

        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
            'status' => ['nullable', 'in:active,needs_review,archived'],
            'priority' => ['nullable', 'in:normal,important,critical'],
            'platform' => ['nullable', 'string', 'max:255'],
            'favorites' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();

        $links = Link::query()
            ->with(['category', 'tags', 'creator', 'visibleToRoles'])
            ->withCount('favorites')
            ->visibleTo($user)
            ->search($validated['search'] ?? null)
            ->when($validated['category'] ?? null, fn (Builder $query, int $categoryId) => $query->where('category_id', $categoryId))
            ->when($validated['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($validated['priority'] ?? null, fn (Builder $query, string $priority) => $query->where('priority', $priority))
            ->when($validated['platform'] ?? null, fn (Builder $query, string $platform) => $query->where('platform', $platform))
            ->when(
                filter_var($validated['favorites'] ?? false, FILTER_VALIDATE_BOOL),
                fn (Builder $query) => $query->whereHas('favorites', fn (Builder $favoriteQuery) => $favoriteQuery->where('user_id', $user->id))
            )
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $platforms = Link::query()
            ->visibleTo($user)
            ->whereNotNull('platform')
            ->where('platform', '!=', '')
            ->distinct()
            ->orderBy('platform')
            ->pluck('platform');

        $favoriteIds = $user->favorites()->pluck('link_id')->all();

        return view('app.links.index', [
            'links' => $links,
            'categories' => $categories,
            'platforms' => $platforms,
            'favoriteIds' => $favoriteIds,
            'filters' => $validated,
        ]);
    }

    public function open(Request $request, Link $link): RedirectResponse
    {
        $this->authorize('open', $link);

        activity('links')
            ->causedBy($request->user())
            ->performedOn($link)
            ->event('opened')
            ->withProperties([
                'ip_address' => $request->ip(),
                'opened_via' => 'internal_app',
            ])
            ->log('Link opened');

        return redirect()->away($link->url);
    }

    public function toggleFavorite(Request $request, Link $link): RedirectResponse
    {
        $this->authorize('favorite', $link);

        $favorite = $request->user()
            ->favorites()
            ->where('link_id', $link->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $message = 'Link dihapus dari favorit.';
        } else {
            $request->user()->favorites()->create([
                'link_id' => $link->id,
            ]);
            $message = 'Link ditambahkan ke favorit.';
        }

        return back()->with('status', $message);
    }
}
