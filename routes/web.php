<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\TagController as AdminTagController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\PageController as PublicPageController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::post('/logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'can:access-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('/', 'admin.index')->name('dashboard');

    Route::resource('posts', AdminPostController::class)->except(['show']);
    Route::resource('pages', AdminPageController::class)->except(['show']);
    Route::resource('categories', AdminCategoryController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    Route::resource('tags', AdminTagController::class)->only(['index', 'create', 'store', 'edit', 'update']);

    Route::get('settings', [AdminSettingsController::class, 'edit'])->name('settings.edit');
    Route::patch('settings/profile', [AdminSettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::patch('settings/password', [AdminSettingsController::class, 'updatePassword'])->name('settings.password.update');
});

Route::get('/{page:slug}', [PublicPageController::class, 'show'])->name('pages.show');
