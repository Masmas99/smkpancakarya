<?php

namespace App\Models;

class Berita extends Article
{
    public const SECTION = 'berita';

    public const CONNECTION = 'articles_berita';

    protected $table = 'berita';
}
