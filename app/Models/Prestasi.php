<?php

namespace App\Models;

class Prestasi extends Article
{
    public const SECTION = 'prestasi';

    public const CONNECTION = 'articles_prestasi';

    protected $table = 'prestasi';
}
