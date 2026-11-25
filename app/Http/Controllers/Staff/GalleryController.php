<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\GalleryManagementController;

class GalleryController extends GalleryManagementController
{
    protected function viewPrefix(): string
    {
        return 'pages.staff.galeri';
    }

    protected function routePrefix(): string
    {
        return 'staff.galeri';
    }
}
