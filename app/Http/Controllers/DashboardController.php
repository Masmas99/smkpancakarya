<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard sesuai role pengguna.
     */
    public function __invoke(Request $request): View
    {
        return view(
            $request->user()->isAdmin() ? 'pages.admin.index' : 'pages.staff.index',
        );
    }
}
