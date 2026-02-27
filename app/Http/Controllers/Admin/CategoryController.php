<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::latest()->paginate(15);

        return view('admin.categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('admin.categories.create');
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
            'slug' => ['required', 'string', 'max:255', Rule::unique('categories', 'slug')],
        ])['slug'];

        Category::create([
            'name' => $validated['name'],
            'slug' => $finalSlug,
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category): RedirectResponse
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
            'slug' => ['required', 'string', 'max:255', Rule::unique('categories', 'slug')->ignore($category->id)],
        ])['slug'];

        $category->update([
            'name' => $validated['name'],
            'slug' => $finalSlug,
        ]);

        return back()->with('success', 'Category updated successfully.');
    }
}
