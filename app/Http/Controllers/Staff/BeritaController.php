<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\ArticleManagementController;
use App\Models\Article;
use App\Models\Berita;

class BeritaController extends ArticleManagementController
{
    protected function viewPrefix(): string
    {
        return 'pages.staff.berita';
    }

    protected function routePrefix(): string
    {
        return 'staff.berita';
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
