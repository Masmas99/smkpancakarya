<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ArtikelController extends Controller
{
    /**
     * Tampilkan daftar artikel yang sudah terbit.
     */
    public function index(Request $request): View
    {
        $categories = Article::CATEGORIES;
        $activeCategory = (string) $request->query('category', '');

        if (! array_key_exists($activeCategory, $categories)) {
            $activeCategory = '';
        }

        $articles = Article::published()
            ->with('author')
            ->when($activeCategory !== '', fn ($query) => $query->where('category', $activeCategory))
            ->latest('published_at')
            ->simplePaginate(6)
            ->withQueryString();

        return view('pages.landing.artikel.index', compact('articles', 'categories', 'activeCategory'));
    }

    /**
     * Tampilkan detail satu artikel.
     */
    public function show(Article $article): View
    {
        abort_unless($article->isPublished(), 404);

        return view('pages.landing.artikel.show', compact('article'));
    }
}
