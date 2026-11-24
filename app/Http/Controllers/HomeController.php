<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    /**
     * Tampilkan halaman beranda.
     */
    public function __invoke(): View
    {
        return view('pages.landing.index', [
            'latestArticles' => Article::published()
                ->latest('published_at')
                ->take(3)
                ->get(),
        ]);
    }
}
