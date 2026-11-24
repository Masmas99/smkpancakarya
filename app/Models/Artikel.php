<?php

namespace App\Models;

class Artikel extends Article
{
    public const SECTION = 'artikel';

    public const CONNECTION = 'articles_artikel';

    protected $table = 'artikel';
}
