@extends('layouts.admin')

@section('title', 'Settings - Admin - ' . config('app.name'))

@section('content')
    <section>
        <header>
            <h1>Settings</h1>
            <p>Update your profile, password, and website settings.</p>
        </header>

        <section aria-labelledby="profile-settings-heading">
            <header>
                <h2 id="profile-settings-heading">Profile</h2>
            </header>

            <form method="POST" action="{{ route('admin.settings.profile.update') }}" novalidate>
                @csrf
                @method('PATCH')

                <div>
                    <label for="name">Name</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name', $user->name) }}"
                        autocomplete="name"
                        required
                    >
                </div>

                <div>
                    <label for="email">Email</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email', $user->email) }}"
                        autocomplete="email"
                        required
                    >
                </div>

                <button type="submit">Save profile</button>
            </form>
        </section>

        <section aria-labelledby="password-settings-heading">
            <header>
                <h2 id="password-settings-heading">Password</h2>
            </header>

            <form method="POST" action="{{ route('admin.settings.password.update') }}" novalidate>
                @csrf
                @method('PATCH')

                <div>
                    <label for="current_password">Current password</label>
                    <input
                        id="current_password"
                        name="current_password"
                        type="password"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <div>
                    <label for="password">New password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <div>
                    <label for="password_confirmation">Confirm new password</label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <button type="submit">Update password</button>
            </form>
        </section>

        <section aria-labelledby="website-settings-heading">
            <header>
                <h2 id="website-settings-heading">Website Settings</h2>
            </header>

            <form method="POST" action="{{ route('admin.settings.website.update') }}" novalidate>
                @csrf
                @method('PATCH')

                <div>
                    <label for="posts_per_page">Posts per page</label>
                    <input
                        id="posts_per_page"
                        name="posts_per_page"
                        type="number"
                        min="1"
                        max="50"
                        step="1"
                        value="{{ old('posts_per_page', $postsPerPage) }}"
                        required
                    >
                    <p>Used for the public blog index pagination. Allowed range: 1 to 50.</p>
                </div>

                <div>
                    <label for="show_blog_nav_link">
                        <input
                            id="show_blog_nav_link"
                            name="show_blog_nav_link"
                            type="checkbox"
                            value="1"
                            @checked((bool) old('show_blog_nav_link', $showBlogNavLink))
                        >
                        Show "Blog" link in public navbar
                    </label>
                    <p>When enabled, the Blog link appears after dynamic navbar pages and before login/register or admin links.</p>
                </div>

                <div>
                    <label for="default_post_comments_enabled">
                        <input
                            id="default_post_comments_enabled"
                            name="default_post_comments_enabled"
                            type="checkbox"
                            value="1"
                            @checked((bool) old('default_post_comments_enabled', $defaultPostCommentsEnabled))
                        >
                        Enable comments by default on new posts
                    </label>
                    <p>If disabled, "Enable comments for this post" remains unchecked by default in post creation.</p>
                </div>

                <div>
                    <label for="default_post_status">Default status for new posts</label>
                    <select id="default_post_status" name="default_post_status" required>
                        <option value="draft" @selected(old('default_post_status', $defaultPostStatus) === 'draft')>Draft</option>
                        <option value="published" @selected(old('default_post_status', $defaultPostStatus) === 'published')>Published</option>
                    </select>
                    <p>Controls which status is preselected on the post creation form.</p>
                </div>

                <div>
                    <label for="homepage_source">Homepage content</label>
                    <select id="homepage_source" name="homepage_source" required>
                        <option value="default" @selected(old('homepage_source', $homepageSource) === 'default')>Default welcome page</option>
                        <option value="blog" @selected(old('homepage_source', $homepageSource) === 'blog')>Blog index</option>
                        <option value="page" @selected(old('homepage_source', $homepageSource) === 'page')>Static page</option>
                    </select>
                </div>

                <div>
                    <label for="homepage_page_id">Homepage page</label>
                    <select id="homepage_page_id" name="homepage_page_id">
                        <option value="">Select a published page</option>
                        @foreach ($publishedPages as $page)
                            <option
                                value="{{ $page->id }}"
                                @selected((string) old('homepage_page_id', $homepagePageId) === (string) $page->id)
                            >
                                {{ $page->title }} ({{ $page->slug }})
                            </option>
                        @endforeach
                    </select>
                    <p>Used only when Homepage content is set to Static page.</p>
                </div>

                <button type="submit">Save website settings</button>
            </form>
        </section>
    </section>
@endsection
