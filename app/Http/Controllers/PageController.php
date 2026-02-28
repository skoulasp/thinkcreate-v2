<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(Page $page): View
    {
        abort_unless(
            Page::query()->published()->whereKey($page->getKey())->exists(),
            404
        );

        return view('pages.show', compact('page'));
    }
}
