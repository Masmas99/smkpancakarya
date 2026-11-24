<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ArticleManagementController;
use App\Models\Article;
use App\Models\Berita;

class BeritaController extends ArticleManagementController
{
    protected function viewPrefix(): string
    {
        return 'pages.admin.berita';
    }

    protected function routePrefix(): string
    {
        return 'admin.berita';
    }

    protected function section(): string
    {
        return Article::DEFAULT_CATEGORY;
    }

    protected function modelClass(): string
    {
        return Berita::class;
    }
}
