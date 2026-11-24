<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ArticleManagementController;

class ArticleController extends ArticleManagementController
{
    protected function viewPrefix(): string
    {
        return 'pages.admin.artikel';
    }

    protected function routePrefix(): string
    {
        return 'admin.artikel';
    }
}
