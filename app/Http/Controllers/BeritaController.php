<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Berita;

class BeritaController extends ArticleSectionController
{
    protected function section(): string
    {
        return Article::DEFAULT_CATEGORY;
    }

    protected function modelClass(): string
    {
        return Berita::class;
    }

    protected function title(): string
    {
        return __('site.nav.news');
    }

    protected function description(): string
    {
        return __('site.articles.news_intro');
    }

    protected function emptyMessage(): string
    {
        return __('site.articles.news_empty');
    }
}
