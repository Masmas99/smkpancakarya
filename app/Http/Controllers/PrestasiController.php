<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Prestasi;

class PrestasiController extends ArticleSectionController
{
    protected function section(): string
    {
        return Article::PRESTASI_CATEGORY;
    }

    protected function modelClass(): string
    {
        return Prestasi::class;
    }

    protected function title(): string
    {
        return __('site.nav.achievements');
    }

    protected function description(): string
    {
        return __('site.articles.achievement_intro');
    }

    protected function emptyMessage(): string
    {
        return __('site.articles.achievement_empty');
    }
}
