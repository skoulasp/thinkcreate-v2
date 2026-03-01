<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(): View
    {
        $menus = Menu::query()
            ->withCount('items')
            ->orderBy('name')
            ->get();

        return view('admin.menus.index', compact('menus'));
    }

    public function create(): View
    {
        return view('admin.menus.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'slug_effective' => ['nullable', 'string', 'max:255'],
            'manual_slug' => ['nullable', 'boolean'],
        ]);

        $manualSlug = $request->boolean('manual_slug');
        $submittedSlug = $manualSlug
            ? ($validated['slug'] ?? null)
            : ($validated['slug_effective'] ?? null);

        $slug = blank($submittedSlug) || ! $manualSlug
            ? Str::slug($validated['name'])
            : $submittedSlug;

        $request->merge(['slug' => $slug]);

        $finalSlug = $request->validate([
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('menus', 'slug')],
        ])['slug'];

        $menu = Menu::create([
            'name' => $validated['name'],
            'slug' => $finalSlug,
        ]);

        return redirect()
            ->route('admin.menus.edit', $menu)
            ->with('success', 'Menu created successfully.');
    }

    public function edit(Menu $menu): View
    {
        $menu->load(['items.page']);

        return view('admin.menus.edit', compact('menu'));
    }

    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $menu->update([
            'name' => $validated['name'],
        ]);

        return back()->with('success', 'Menu updated successfully.');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        $menu->delete();

        return redirect()
            ->route('admin.menus.index')
            ->with('success', 'Menu deleted successfully.');
    }
}
