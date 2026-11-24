<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Tampilkan halaman beranda.
     */
    public function __invoke(Request $request): View
    {
        $categories = Article::HOME_TABS;
        $activeCategory = (string) $request->query('category', Article::DEFAULT_CATEGORY);

        if (! array_key_exists($activeCategory, $categories)) {
            $activeCategory = Article::DEFAULT_CATEGORY;
        }

        $articles = Article::published()
            ->when($activeCategory !== 'artikel', fn ($query) => $query->where('category', $activeCategory))
            ->latest('published_at')
            ->simplePaginate(3)
            ->withQueryString();

        return view('pages.landing.index', compact('articles', 'categories', 'activeCategory'));
    }
}
