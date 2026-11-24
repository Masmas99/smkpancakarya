<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\ArticleManagementController;
use App\Models\Article;
use App\Models\Prestasi;

class PrestasiController extends ArticleManagementController
{
    protected function viewPrefix(): string
    {
        return 'pages.staff.prestasi';
    }

    protected function routePrefix(): string
    {
        return 'staff.prestasi';
    }

    protected function section(): string
    {
        return Article::PRESTASI_CATEGORY;
    }

    protected function modelClass(): string
    {
        return Prestasi::class;
    }
}
