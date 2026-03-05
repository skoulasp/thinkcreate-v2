<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateWebsiteSettingsRequest;
use App\Models\Page;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(Request $request): View
    {
        $homepageSource = Setting::getValue('website.homepage.source', 'default');
        $homepagePageId = Setting::getValue('website.homepage.page_id');
        $postsPerPage = Setting::getIntValue('website.blog.posts_per_page', 5, 1, 50);
        $showBlogNavLink = Setting::getBoolValue('website.navbar.show_blog_link', false);
        $defaultPostCommentsEnabled = Setting::getBoolValue('website.posts.defaults.comments_enabled', false);
        $defaultPostStatus = Setting::getValue('website.posts.defaults.status', 'draft');

        if (! in_array($defaultPostStatus, ['draft', 'published'], true)) {
            $defaultPostStatus = 'draft';
        }

        $publishedPages = Page::query()
            ->published()
            ->orderBy('title')
            ->get(['id', 'title', 'slug']);

        return view('admin.settings.edit', [
            'user' => $request->user(),
            'homepageSource' => $homepageSource,
            'homepagePageId' => $homepagePageId,
            'postsPerPage' => $postsPerPage,
            'showBlogNavLink' => $showBlogNavLink,
            'defaultPostCommentsEnabled' => $defaultPostCommentsEnabled,
            'defaultPostStatus' => $defaultPostStatus,
            'publishedPages' => $publishedPages,
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    public function updateWebsite(UpdateWebsiteSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Setting::putValue('website.homepage.source', $validated['homepage_source']);
        Setting::putValue('website.homepage.page_id', isset($validated['homepage_page_id']) ? (string) $validated['homepage_page_id'] : null);
        Setting::putValue('website.blog.posts_per_page', (string) $validated['posts_per_page']);
        Setting::putValue('website.navbar.show_blog_link', $validated['show_blog_nav_link'] ? '1' : '0');
        Setting::putValue('website.posts.defaults.comments_enabled', $validated['default_post_comments_enabled'] ? '1' : '0');
        Setting::putValue('website.posts.defaults.status', $validated['default_post_status']);

        return back()->with('success', 'Website settings updated successfully.');
    }
}
