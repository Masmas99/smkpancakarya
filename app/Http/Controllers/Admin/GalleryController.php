<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\GalleryManagementController;

class GalleryController extends GalleryManagementController
{
    protected function viewPrefix(): string
    {
        return 'pages.admin.galeri';
    }

    protected function routePrefix(): string
    {
        return 'admin.galeri';
    }
}
