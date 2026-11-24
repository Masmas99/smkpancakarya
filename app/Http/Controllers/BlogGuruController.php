<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\BlogGuru;

class BlogGuruController extends ArticleSectionController
{
    protected function section(): string
    {
        return Article::BLOG_CATEGORY;
    }

    protected function modelClass(): string
    {
        return BlogGuru::class;
    }

    protected function title(): string
    {
        return __('site.nav.blog_guru');
    }

    protected function description(): string
    {
        return __('site.articles.blog_guru_intro');
    }

    protected function emptyMessage(): string
    {
        return __('site.articles.blog_guru_empty');
    }
}
