<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Artikel;

class ArtikelController extends ArticleSectionController
{
    protected function section(): string
    {
        return Article::ARTICLE_CATEGORY;
    }

    protected function modelClass(): string
    {
        return Artikel::class;
    }

    protected function title(): string
    {
        return __('site.nav.articles');
    }

    protected function description(): string
    {
        return __('site.articles.intro');
    }

    protected function emptyMessage(): string
    {
        return __('site.articles.empty');
    }
}
