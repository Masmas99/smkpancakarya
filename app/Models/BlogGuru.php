<?php

namespace App\Models;

class BlogGuru extends Article
{
    public const SECTION = 'blog-guru';

    public const CONNECTION = 'articles_blog_guru';

    protected $table = 'blog_guru';
}
