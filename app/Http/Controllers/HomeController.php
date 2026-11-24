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
        $activeCategory = (string) $request->query('category', Article::HOME_DEFAULT_CATEGORY);

        if (! array_key_exists($activeCategory, $categories)) {
            $activeCategory = Article::HOME_DEFAULT_CATEGORY;
        }

        $model = Article::models()[$activeCategory] ?? Article::modelForCategory(Article::HOME_DEFAULT_CATEGORY);

        $articles = $model::published()
            ->latest('published_at')
            ->simplePaginate(3)
            ->withQueryString();

        $categoryRoutes = [
            'berita' => route('berita.index'),
            'prestasi' => route('prestasi.index'),
            'artikel' => route('artikel.index'),
            'blog-guru' => route('blog-guru.index'),
        ];

        return view('pages.landing.index', compact('articles', 'categories', 'activeCategory', 'categoryRoutes'));
    }
}
