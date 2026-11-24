<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\ArticleManagementController;
use App\Models\Article;
use App\Models\Artikel;

class ArticleController extends ArticleManagementController
{
    protected function viewPrefix(): string
    {
        return 'pages.staff.artikel';
    }

    protected function routePrefix(): string
    {
        return 'staff.artikel';
    }

    protected function section(): string
    {
        return Article::ARTICLE_CATEGORY;
    }

    protected function modelClass(): string
    {
        return Artikel::class;
    }
}
