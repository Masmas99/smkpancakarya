<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Contracts\View\View;

class ArtikelController extends Controller
{
    /**
     * Tampilkan daftar artikel yang sudah terbit.
     */
    public function index(): View
    {
        $articles = Article::published()
            ->with('author')
            ->latest('published_at')
            ->simplePaginate(9);

        return view('pages.landing.artikel.index', compact('articles'));
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
