<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $source = Setting::getValue('website.homepage.source', 'default');

        if ($source === 'blog') {
            $perPage = Setting::getIntValue('website.blog.posts_per_page', 5, 1, 50);

            $posts = Post::query()
                ->with(['author', 'categories', 'tags'])
                ->published()
                ->latest('published_at')
                ->paginate($perPage);

            return view('blog.index', compact('posts'));
        }

        if ($source === 'page') {
            $pageId = (int) Setting::getValue('website.homepage.page_id', '0');

            if ($pageId > 0) {
                $page = Page::query()->published()->find($pageId);

                if ($page !== null) {
                    return view('pages.show', compact('page'));
                }
            }
        }

        return view('welcome');
    }
}
