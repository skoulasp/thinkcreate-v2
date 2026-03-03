<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Post::class);

        $posts = Post::with(['author', 'categories', 'tags'])
            ->latest()
            ->paginate(15);

        return view('admin.posts.index', compact('posts'));
    }

    public function create(): View
    {
        $this->authorize('create', Post::class);

        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.posts.create', compact('categories', 'tags'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Post::class);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'slug_effective' => ['nullable', 'string', 'max:255'],
            'manual_slug' => ['nullable', 'boolean'],
            'excerpt' => ['nullable', 'string'],
            'body' => ['required', 'string', 'not_regex:/<script\b/i'],
            'featured_image' => ['nullable', 'image', 'max:5120'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ]);

        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        if ($validated['status'] === 'draft') {
            $validated['published_at'] = null;
        }

        $manualSlug = $request->boolean('manual_slug');
        $submittedSlug = $manualSlug
            ? ($validated['slug'] ?? null)
            : ($validated['slug_effective'] ?? null);

        $slug = blank($submittedSlug) || ! $manualSlug
            ? Str::slug($validated['title'])
            : $submittedSlug;

        $request->merge(['slug' => $slug]);

        $finalSlug = $request->validate([
            'slug' => ['required', 'string', 'max:255', Rule::unique('posts', 'slug')],
        ])['slug'];

        $featuredImagePath = null;

        if ($request->hasFile('featured_image')) {
            $featuredImagePath = $request->file('featured_image')->store('posts/featured-images', 'public');
        }

        $post = Post::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'slug' => $finalSlug,
            'excerpt' => $validated['excerpt'] ?? null,
            'body' => $validated['body'],
            'featured_image_path' => $featuredImagePath,
            'status' => $validated['status'],
            'published_at' => $validated['published_at'] ?? null,
        ]);

        $post->categories()->sync($validated['categories'] ?? []);
        $post->tags()->sync($validated['tags'] ?? []);

        return redirect()
            ->route('admin.posts.edit', $post)
            ->with('success', 'Post created successfully.');
    }

    public function edit(Post $post): View
    {
        $this->authorize('update', $post);

        $post->load(['categories', 'tags']);

        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $this->authorize('update', $post);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'slug_effective' => ['nullable', 'string', 'max:255'],
            'manual_slug' => ['nullable', 'boolean'],
            'excerpt' => ['nullable', 'string'],
            'remove_featured_image' => ['nullable', 'boolean'],
            'body' => ['required', 'string', 'not_regex:/<script\b/i'],
            'featured_image' => ['nullable', 'image', 'max:5120'],
            'status' => ['required', 'in:draft,published'],
            'published_at' => ['nullable', 'date'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ]);

        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        if ($validated['status'] === 'draft') {
            $validated['published_at'] = null;
        }

        $manualSlug = $request->boolean('manual_slug');
        $submittedSlug = $manualSlug
            ? ($validated['slug'] ?? null)
            : ($validated['slug_effective'] ?? null);

        $slug = blank($submittedSlug) || ! $manualSlug
            ? Str::slug($validated['title'])
            : $submittedSlug;

        $request->merge(['slug' => $slug]);

        $finalSlug = $request->validate([
            'slug' => ['required', 'string', 'max:255', Rule::unique('posts', 'slug')->ignore($post->id)],
        ])['slug'];

        $existingFeaturedImagePath = $post->featured_image_path;
        $featuredImagePath = $existingFeaturedImagePath;

        if ($request->boolean('remove_featured_image')) {
            $featuredImagePath = null;
        }

        if ($request->hasFile('featured_image')) {
            $featuredImagePath = $request->file('featured_image')->store('posts/featured-images', 'public');
        }

        $post->update([
            'title' => $validated['title'],
            'slug' => $finalSlug,
            'excerpt' => $validated['excerpt'] ?? null,
            'body' => $validated['body'],
            'featured_image_path' => $featuredImagePath,
            'status' => $validated['status'],
            'published_at' => $validated['published_at'] ?? null,
        ]);

        if (
            filled($existingFeaturedImagePath)
            && $existingFeaturedImagePath !== $featuredImagePath
        ) {
            Storage::disk('public')->delete($existingFeaturedImagePath);
        }

        $post->categories()->sync($validated['categories'] ?? []);
        $post->tags()->sync($validated['tags'] ?? []);

        return back()->with('success', 'Post updated successfully.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->authorize('delete', $post);

        $featuredImagePath = $post->featured_image_path;

        $post->delete();

        if (filled($featuredImagePath)) {
            Storage::disk('public')->delete($featuredImagePath);
        }

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Post deleted successfully.');
    }
}
