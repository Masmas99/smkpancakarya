<?php

namespace Tests\Feature;

use App\Models\Gallery;
use Database\Seeders\GallerySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GallerySeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_gallery_seeder_imports_legacy_album_photos(): void
    {
        Storage::fake('public');

        $this->seed(GallerySeeder::class);

        $this->assertSame(14, Gallery::count());
        $this->assertSame(14, Gallery::published()->count());

        $first = Gallery::ordered()->first();

        $this->assertSame('Praktik Kerja Lapangan Kelas XII Jurusan Teknologi Komputer dan Jaringan 2024', $first->title);
        $this->assertSame(1, $first->sort_order);
        $this->assertNotNull($first->imageUrl());
        Storage::disk('public')->assertExists($first->image);
    }

    public function test_gallery_seeder_is_idempotent(): void
    {
        Storage::fake('public');

        $this->seed(GallerySeeder::class);
        $this->seed(GallerySeeder::class);

        $this->assertSame(14, Gallery::count());
    }
}
