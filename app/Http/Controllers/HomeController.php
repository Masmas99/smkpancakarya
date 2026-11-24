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
        $categories = Article::CATEGORIES;
        $activeCategory = (string) $request->query('category', '');

        if (! array_key_exists($activeCategory, $categories)) {
            $activeCategory = '';
        }

        $articles = Article::published()
            ->when($activeCategory !== '', fn ($query) => $query->where('category', $activeCategory))
            ->latest('published_at')
            ->simplePaginate(6)
            ->withQueryString();

        return view('pages.landing.index', compact('articles', 'categories', 'activeCategory'));
    }
}
