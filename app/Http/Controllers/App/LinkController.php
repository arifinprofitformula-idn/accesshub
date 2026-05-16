<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLinkRequest;
use App\Http\Requests\UpdateLinkRequest;
use App\Models\Category;
use App\Models\Link;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class LinkController extends Controller
{
    public function dashboard(Request $request): View
    {
        $this->authorize('viewAny', Link::class);

        $user = $request->user();
        $filters = $this->validateFilters($request);
        $links = $this->buildFilteredQuery($user, $filters)
            ->paginate(12)
            ->withQueryString();

        return view('app.dashboard', [
            'links' => $links,
            'categories' => $this->categories(),
            'favoriteIds' => $this->favoriteIds($user),
            'filters' => $filters,
        ]);
    }

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Link::class);

        $user = $request->user();
        $filters = $this->validateFilters($request);

        return view('app.links.index', [
            'links' => $this->buildFilteredQuery($user, $filters)
                ->paginate(12)
                ->withQueryString(),
            'categories' => $this->categories(),
            'favoriteIds' => $this->favoriteIds($user),
            'filters' => $filters,
        ]);
    }

    public function manage(Request $request): View
    {
        $this->authorize('viewAny', Link::class);

        $user = $request->user();
        $filters = $this->validateFilters($request);

        return view('app.links.manage', [
            'links' => $this->buildFilteredQuery($user, $filters)
                ->paginate(12)
                ->withQueryString(),
            'categories' => $this->categories(),
            'favoriteIds' => $this->favoriteIds($user),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Link::class);

        return view('app.links.create', [
            'categories' => $this->categories(),
        ]);
    }

    public function store(StoreLinkRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $link = Link::create([
            'title' => $validated['title'],
            'url' => $validated['url'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'] ?? null,
            'platform' => $this->extractDomain($validated['url']),
            'priority' => 'normal',
            'status' => 'active',
            'visibility' => $this->resolveStoredVisibility($validated['visibility'] ?? 'private'),
            'owner_name' => $request->user()->name,
            'created_by' => $request->user()->id,
        ]);

        $this->syncTags($link, $validated['tags'] ?? null);

        return redirect()
            ->route('dashboard')
            ->with('status', 'Link berhasil disimpan.');
    }

    public function edit(Link $link): View
    {
        $this->authorize('update', $link);

        $link->load('tags');

        return view('app.links.edit', [
            'link' => $link,
            'categories' => $this->categories(),
            'tagString' => $link->tags->pluck('name')->implode(', '),
            'selectedVisibility' => $this->presentVisibility($link),
        ]);
    }

    public function update(UpdateLinkRequest $request, Link $link): RedirectResponse
    {
        $validated = $request->validated();

        $storedVisibility = $this->resolveStoredVisibility($validated['visibility'] ?? 'private', $link);

        $link->update([
            'title' => $validated['title'],
            'url' => $validated['url'],
            'category_id' => $validated['category_id'],
            'description' => $validated['description'] ?? null,
            'platform' => $this->extractDomain($validated['url']),
            'visibility' => $storedVisibility,
            'owner_name' => $link->owner_name ?: $request->user()->name,
        ]);

        if ($storedVisibility !== 'role') {
            $link->visibleToRoles()->sync([]);
        }

        $this->syncTags($link, $validated['tags'] ?? null);

        return redirect()
            ->route('dashboard')
            ->with('status', 'Link berhasil diperbarui.');
    }

    public function destroy(Request $request, Link $link): RedirectResponse
    {
        $this->authorize('delete', $link);

        $link->update([
            'status' => 'archived',
        ]);

        $message = str_contains((string) url()->previous(), '/app/manage')
            ? 'Asset link berhasil dihapus dari daftar kelola.'
            : 'Link dipindahkan ke arsip.';

        return back()->with('status', $message);
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

    protected function validateFilters(Request $request): array
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'integer', 'exists:categories,id'],
            'favorites' => ['nullable', 'boolean'],
            'status' => ['nullable', 'in:active,needs_review,archived'],
            'visibility' => ['nullable', 'in:private,shared'],
        ]);

        if (! $request->user()?->hasRole('super_admin')) {
            unset($filters['status']);
        }

        return $filters;
    }

    protected function buildFilteredQuery($user, array $filters): Builder
    {
        return Link::query()
            ->with(['category', 'tags'])
            ->withCount('favorites')
            ->visibleTo($user)
            ->search($filters['search'] ?? null)
            ->when($filters['category'] ?? null, fn (Builder $query, int $categoryId) => $query->where('category_id', $categoryId))
            ->when(
                isset($filters['status']) && $user->hasRole('super_admin'),
                fn (Builder $query) => $query->where('status', $filters['status']),
                fn (Builder $query) => $query->where('status', 'active')
            )
            ->when(
                filter_var($filters['favorites'] ?? false, FILTER_VALIDATE_BOOL),
                fn (Builder $query) => $query->whereHas('favorites', fn (Builder $favoriteQuery) => $favoriteQuery->where('user_id', $user->id))
            )
            ->latest();
    }

    protected function categories(): Collection
    {
        return Category::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    protected function favoriteIds($user): array
    {
        return $user->favorites()->pluck('link_id')->all();
    }

    protected function syncTags(Link $link, ?string $tagString): void
    {
        $tagIds = collect(explode(',', (string) $tagString))
            ->map(fn (string $tag) => trim($tag))
            ->filter()
            ->unique(fn (string $tag) => Str::lower($tag))
            ->take(8)
            ->map(function (string $tag): int {
                $slug = Str::slug($tag);

                return Tag::firstOrCreate(
                    ['slug' => $slug],
                    ['name' => $tag],
                )->id;
            })
            ->values()
            ->all();

        $link->tags()->sync($tagIds);
    }

    protected function extractDomain(string $url): ?string
    {
        $host = parse_url($url, PHP_URL_HOST);

        if (! is_string($host) || $host === '') {
            return null;
        }

        return Str::of($host)
            ->replaceStart('www.', '')
            ->toString();
    }

    protected function resolveStoredVisibility(string $visibility, ?Link $link = null): string
    {
        if ($visibility === 'private') {
            return 'private';
        }

        if ($link?->visibility === 'role') {
            return 'role';
        }

        return 'internal';
    }

    protected function presentVisibility(Link $link): string
    {
        return $link->visibility === 'private' ? 'private' : 'shared';
    }
}
