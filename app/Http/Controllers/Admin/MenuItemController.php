<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use App\Rules\NoReservedTopLevelPath;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MenuItemController extends Controller
{
    public function create(Menu $menu): View
    {
        $pages = Page::query()->published()->orderBy('title')->get();

        return view('admin.menus.items.create', compact('menu', 'pages'));
    }

    public function store(Request $request, Menu $menu): RedirectResponse
    {
        $validated = $this->validateItem($request);
        $url = $this->normalizeUrl($validated['url'] ?? null);

        MenuItem::create([
            'menu_id' => $menu->id,
            'page_id' => $validated['page_id'] ?? null,
            'label' => $validated['label'] ?? null,
            'url' => $url,
            'sort_order' => (int) ($menu->items()->max('sort_order') ?? -1) + 1,
        ]);

        return redirect()
            ->route('admin.menus.edit', $menu)
            ->with('success', 'Menu item created successfully.');
    }

    public function edit(Menu $menu, MenuItem $item): View
    {
        $this->ensureBelongsToMenu($menu, $item);

        $pages = Page::query()->published()->orderBy('title')->get();

        return view('admin.menus.items.edit', compact('menu', 'item', 'pages'));
    }

    public function update(Request $request, Menu $menu, MenuItem $item): RedirectResponse
    {
        $this->ensureBelongsToMenu($menu, $item);

        $validated = $this->validateItem($request);
        $url = $this->normalizeUrl($validated['url'] ?? null);

        $item->update([
            'page_id' => $validated['page_id'] ?? null,
            'label' => $validated['label'] ?? null,
            'url' => $url,
        ]);

        return redirect()
            ->route('admin.menus.edit', $menu)
            ->with('success', 'Menu item updated successfully.');
    }

    public function destroy(Menu $menu, MenuItem $item): RedirectResponse
    {
        $this->ensureBelongsToMenu($menu, $item);

        $item->delete();

        return redirect()
            ->route('admin.menus.edit', $menu)
            ->with('success', 'Menu item deleted successfully.');
    }

    public function reorder(Request $request, Menu $menu): JsonResponse
    {
        $itemIds = $request->validate([
            'items' => ['required', 'array'],
            'items.*' => [
                'integer',
                Rule::exists('menu_items', 'id')->where(
                    fn ($query) => $query->where('menu_id', $menu->id)
                ),
            ],
        ])['items'];

        $menuItemCount = $menu->items()->count();

        if (count($itemIds) !== $menuItemCount) {
            throw ValidationException::withMessages([
                'items' => 'Invalid item order payload.',
            ]);
        }

        foreach ($itemIds as $index => $id) {
            MenuItem::query()
                ->where('menu_id', $menu->id)
                ->whereKey($id)
                ->update(['sort_order' => $index]);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * @return array<string, mixed>
     */
    private function validateItem(Request $request): array
    {
        $validated = $request->validate([
            'label' => ['nullable', 'string', 'max:255'],
            'page_id' => [
                'nullable',
                'integer',
                Rule::exists('pages', 'id')
                    ->where(fn ($query) => $query->where('status', 'published')->where('published_at', '<=', now())),
            ],
            'url' => ['nullable', 'string', 'max:2048', new NoReservedTopLevelPath()],
        ]);

        $hasPage = ! empty($validated['page_id']);
        $hasUrl = filled($validated['url'] ?? null);

        if (! $hasPage && ! $hasUrl) {
            throw ValidationException::withMessages([
                'url' => 'Select an internal page or provide a custom URL.',
            ]);
        }

        if ($hasPage && $hasUrl) {
            throw ValidationException::withMessages([
                'url' => 'Choose either an internal page or a custom URL, not both.',
            ]);
        }

        if ($hasPage) {
            $page = Page::query()->find($validated['page_id']);

            if ($page && $this->conflictsReservedTopLevelRoute($page->slug)) {
                throw ValidationException::withMessages([
                    'page_id' => 'This page slug conflicts with a reserved top-level route.',
                ]);
            }
        }

        return $validated;
    }

    private function ensureBelongsToMenu(Menu $menu, MenuItem $item): void
    {
        abort_unless($item->menu_id === $menu->id, 404);
    }

    private function normalizeUrl(?string $url): ?string
    {
        if (! filled($url)) {
            return null;
        }

        $url = trim($url);
        $parsed = parse_url($url);

        if (($parsed['scheme'] ?? null) || ($parsed['host'] ?? null)) {
            return $url;
        }

        return str_starts_with($url, '/') ? $url : '/'.$url;
    }

    private function conflictsReservedTopLevelRoute(string $path): bool
    {
        $trimmedPath = trim($path, '/');

        if ($trimmedPath === '') {
            return false;
        }

        $firstSegment = strtok($trimmedPath, '/');

        if ($firstSegment === false) {
            return false;
        }

        $segment = strtolower($firstSegment);

        return in_array($segment, $this->reservedTopLevelSegments(), true);
    }

    /**
     * @return array<int, string>
     */
    private function reservedTopLevelSegments(): array
    {
        $segments = [];

        foreach (Route::getRoutes() as $route) {
            $uri = $route->uri();

            if ($uri === '' || str_contains($uri, '{')) {
                continue;
            }

            $first = strtok($uri, '/');

            if ($first !== false && $first !== '') {
                $segments[] = strtolower($first);
            }
        }

        return array_values(array_unique($segments));
    }
}
