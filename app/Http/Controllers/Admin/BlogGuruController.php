<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ArticleManagementController;
use App\Models\Article;
use App\Models\BlogGuru;

class BlogGuruController extends ArticleManagementController
{
    protected function viewPrefix(): string
    {
        return 'pages.admin.blog-guru';
    }

    protected function routePrefix(): string
    {
        return 'admin.blog-guru';
    }

    protected function section(): string
    {
        return Article::BLOG_CATEGORY;
    }

    protected function modelClass(): string
    {
        return BlogGuru::class;
    }
}
