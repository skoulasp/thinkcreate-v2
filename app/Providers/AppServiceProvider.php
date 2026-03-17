<?php

namespace App\Providers;

use App\Listeners\UpdateLastLoginAt;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use App\Policies\PagePolicy;
use App\Policies\PostPolicy;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Post::class, PostPolicy::class);
        Gate::policy(Page::class, PagePolicy::class);

        Gate::define('access-admin', fn (User $user) => $user->is_admin === true);

        Event::listen(Login::class, UpdateLastLoginAt::class);
    }
}
