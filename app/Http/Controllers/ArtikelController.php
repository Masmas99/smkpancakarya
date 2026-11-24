<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;

class ArtikelController extends Controller
{
    /**
     * Tampilkan seluruh artikel yang sudah terbit.
     */
    public function index(): View
    {
        return $this->listing(
            Article::published(),
            __('site.nav.articles'),
            __('site.articles.intro'),
            __('site.articles.empty'),
        );
    }

    /**
     * Tampilkan berita (kategori Berita).
     */
    public function berita(): View
    {
        return $this->listing(
            Article::published()->where('category', Article::DEFAULT_CATEGORY),
            __('site.nav.news'),
            __('site.articles.news_intro'),
            __('site.articles.news_empty'),
        );
    }

    /**
     * Tampilkan prestasi (kategori Prestasi).
     */
    public function prestasi(): View
    {
        return $this->listing(
            Article::published()->where('category', 'prestasi'),
            __('site.nav.achievements'),
            __('site.articles.achievement_intro'),
            __('site.articles.achievement_empty'),
        );
    }

    /**
     * Tampilkan tulisan guru (kategori Blog Guru).
     */
    public function blogGuru(): View
    {
        return $this->listing(
            Article::published()->where('category', Article::BLOG_CATEGORY),
            __('site.nav.blog_guru'),
            __('site.articles.blog_guru_intro'),
            __('site.articles.blog_guru_empty'),
        );
    }

    /**
     * Tampilkan detail satu artikel.
     */
    public function show(Article $article): View
    {
        abort_unless($article->isPublished(), 404);

        return view('pages.landing.artikel.show', compact('article'));
    }

    /**
     * @param  Builder<Article>  $query
     */
    private function listing(Builder $query, string $title, string $description, string $empty): View
    {
        $articles = $query
            ->with('author')
            ->latest('published_at')
            ->simplePaginate(9);

        return view('pages.landing.artikel.index', compact('articles', 'title', 'description', 'empty'));
    }
}
