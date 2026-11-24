<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\ArticleManagementController;

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
}
