<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TagController extends Controller
{
    public function index(): View
    {
        $tags = Tag::latest()->paginate(15);

        return view('admin.tags.index', compact('tags'));
    }

    public function create(): View
    {
        return view('admin.tags.create');
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
            'slug' => ['required', 'string', 'max:255', Rule::unique('tags', 'slug')],
        ])['slug'];

        Tag::create([
            'name' => $validated['name'],
            'slug' => $finalSlug,
        ]);

        return redirect()
            ->route('admin.tags.index')
            ->with('success', 'Tag created successfully.');
    }

    public function edit(Tag $tag): View
    {
        return view('admin.tags.edit', compact('tag'));
    }

    public function update(Request $request, Tag $tag): RedirectResponse
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
            'slug' => ['required', 'string', 'max:255', Rule::unique('tags', 'slug')->ignore($tag->id)],
        ])['slug'];

        $tag->update([
            'name' => $validated['name'],
            'slug' => $finalSlug,
        ]);

        return back()->with('success', 'Tag updated successfully.');
    }
}
