<?php

namespace Tests\Feature;

use App\Models\Gallery;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GalleryTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_shows_gallery_section_below_the_articles(): void
    {
        $content = $this->get('/')
            ->assertOk()
            ->assertSee('Album Galeri')
            ->assertSee('Dokumentasi kegiatan')
            ->assertSee('Belum ada foto galeri yang dipublikasikan.')
            ->getContent();

        $articles = strpos($content, 'id="artikel"');
        $gallery = strpos($content, 'id="galeri"');

        $this->assertNotFalse($articles);
        $this->assertNotFalse($gallery);
        $this->assertTrue($articles < $gallery, 'Bagian galeri harus berada di bawah bagian artikel.');
    }

    public function test_landing_page_only_shows_published_gallery_photos(): void
    {
        Gallery::factory()->create(['title' => 'Foto Upacara Bendera']);
        Gallery::factory()->hidden()->create(['title' => 'Foto Belum Tayang']);

        $this->get('/')
            ->assertOk()
            ->assertSee('Foto Upacara Bendera')
            ->assertDontSee('Foto Belum Tayang');
    }

    public function test_landing_page_orders_gallery_photos_by_newest_first(): void
    {
        Gallery::factory()->create([
            'title' => 'Foto Lebih Lama',
            'created_at' => now()->subDay(),
        ]);
        Gallery::factory()->create([
            'title' => 'Foto Terbaru',
            'created_at' => now(),
        ]);

        $content = $this->get('/')->assertOk()->getContent();

        $newest = strpos($content, 'Foto Terbaru');
        $older = strpos($content, 'Foto Lebih Lama');

        $this->assertNotFalse($newest);
        $this->assertNotFalse($older);
        $this->assertTrue($newest < $older);
    }

    public function test_guests_are_redirected_to_login_from_gallery_management(): void
    {
        $this->get('/admin/galeri')->assertRedirect('/login');
        $this->get('/staff/galeri')->assertRedirect('/login');
    }

    public function test_staff_can_open_staff_gallery_management_but_not_admin(): void
    {
        $staff = User::factory()->create();

        $this->actingAs($staff)
            ->get('/staff/galeri')
            ->assertOk()
            ->assertSee('Kelola Galeri')
            ->assertSee('Tambah Foto');

        $this->actingAs($staff)
            ->get('/admin/galeri')
            ->assertForbidden();
    }

    public function test_admin_can_open_admin_gallery_management_but_not_staff(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin/galeri')
            ->assertOk()
            ->assertSee('Kelola Galeri');

        $this->actingAs($admin)
            ->get('/staff/galeri')
            ->assertForbidden();
    }

    public function test_staff_can_upload_a_gallery_photo(): void
    {
        Storage::fake('public');

        $staff = User::factory()->create();

        $this->actingAs($staff)->post('/staff/galeri', [
            'title' => 'Kegiatan Peringatan Hari Guru',
            'image' => UploadedFile::fake()->image('hari-guru.jpg', 1200, 800),
            'is_published' => '1',
        ])->assertRedirect(route('staff.galeri.index'));

        $photo = Gallery::firstOrFail();

        $this->assertSame('Kegiatan Peringatan Hari Guru', $photo->title);
        $this->assertTrue($photo->is_published);
        $this->assertSame($staff->id, $photo->user_id);
        Storage::disk('public')->assertExists($photo->image);
    }

    public function test_gallery_photo_requires_an_image(): void
    {
        $staff = User::factory()->create();

        $this->actingAs($staff)
            ->post('/staff/galeri', ['title' => 'Tanpa Foto'])
            ->assertSessionHasErrors('image');

        $this->assertDatabaseCount('galleries', 0);
    }

    public function test_gallery_photo_must_be_a_valid_image(): void
    {
        Storage::fake('public');

        $staff = User::factory()->create();

        $this->actingAs($staff)
            ->post('/staff/galeri', [
                'image' => UploadedFile::fake()->create('dokumen.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHasErrors('image');

        $this->assertDatabaseCount('galleries', 0);
    }

    public function test_gallery_photo_can_be_saved_without_a_title_and_hidden(): void
    {
        Storage::fake('public');

        $staff = User::factory()->create();

        $this->actingAs($staff)->post('/staff/galeri', [
            'image' => UploadedFile::fake()->image('tanpa-judul.png'),
        ])->assertRedirect(route('staff.galeri.index'));

        $photo = Gallery::firstOrFail();

        $this->assertNull($photo->title);
        $this->assertFalse($photo->is_published);
    }

    public function test_staff_can_update_a_gallery_photo_and_replace_its_image(): void
    {
        Storage::fake('public');

        $staff = User::factory()->create();
        $photo = Gallery::factory()->create([
            'title' => 'Judul Lama',
            'image' => 'galeri/lama.jpg',
        ]);

        Storage::disk('public')->put('galeri/lama.jpg', 'lama');

        $this->actingAs($staff)->put(route('staff.galeri.update', $photo), [
            'title' => 'Judul Baru',
            'image' => UploadedFile::fake()->image('baru.png'),
        ])->assertRedirect(route('staff.galeri.index'));

        $photo->refresh();

        $this->assertSame('Judul Baru', $photo->title);
        $this->assertFalse($photo->is_published);
        $this->assertNotSame('galeri/lama.jpg', $photo->image);
        Storage::disk('public')->assertMissing('galeri/lama.jpg');
        Storage::disk('public')->assertExists($photo->image);
    }

    public function test_updating_without_a_new_image_keeps_the_existing_photo(): void
    {
        Storage::fake('public');

        $staff = User::factory()->create();
        $photo = Gallery::factory()->create([
            'title' => 'Judul Lama',
            'image' => 'galeri/tetap.jpg',
        ]);

        Storage::disk('public')->put('galeri/tetap.jpg', 'tetap');

        $this->actingAs($staff)->put(route('staff.galeri.update', $photo), [
            'title' => 'Judul Diperbarui',
            'is_published' => '1',
        ])->assertRedirect(route('staff.galeri.index'));

        $photo->refresh();

        $this->assertSame('Judul Diperbarui', $photo->title);
        $this->assertSame('galeri/tetap.jpg', $photo->image);
        $this->assertTrue($photo->is_published);
        Storage::disk('public')->assertExists('galeri/tetap.jpg');
    }

    public function test_staff_can_delete_a_gallery_photo_with_its_file(): void
    {
        Storage::fake('public');

        $staff = User::factory()->create();
        $photo = Gallery::factory()->create([
            'image' => 'galeri/hapus.jpg',
        ]);

        Storage::disk('public')->put('galeri/hapus.jpg', 'gambar');

        $this->actingAs($staff)
            ->delete(route('staff.galeri.destroy', $photo))
            ->assertRedirect(route('staff.galeri.index'));

        $this->assertDatabaseMissing('galleries', ['id' => $photo->id]);
        Storage::disk('public')->assertMissing('galeri/hapus.jpg');
    }

    public function test_management_sections_show_their_gallery_photos(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create();

        Gallery::factory()->create(['title' => 'Foto Bersama Galeri']);
        Gallery::factory()->hidden()->create(['title' => 'Foto Tersembunyi Galeri']);

        $this->actingAs($admin)
            ->get('/admin/galeri')
            ->assertOk()
            ->assertSee('Foto Bersama Galeri')
            ->assertSee('Foto Tersembunyi Galeri');

        $this->actingAs($staff)
            ->get('/staff/galeri')
            ->assertOk()
            ->assertSee('Foto Bersama Galeri')
            ->assertSee('Foto Tersembunyi Galeri');
    }

    public function test_gallery_management_status_filter(): void
    {
        $staff = User::factory()->create();

        Gallery::factory()->create(['title' => 'Foto Tampil']);
        Gallery::factory()->hidden()->create(['title' => 'Foto Disembunyikan']);

        $this->actingAs($staff)
            ->get('/staff/galeri?status=hidden')
            ->assertOk()
            ->assertSee('Foto Disembunyikan')
            ->assertDontSee('Foto Tampil');

        $this->actingAs($staff)
            ->get('/staff/galeri?status=published')
            ->assertOk()
            ->assertSee('Foto Tampil')
            ->assertDontSee('Foto Disembunyikan');
    }

    public function test_dashboards_link_to_gallery_management(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create();

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('href="'.route('admin.galeri.index').'"', false);

        $this->actingAs($staff)
            ->get('/staff/dashboard')
            ->assertOk()
            ->assertSee('href="'.route('staff.galeri.index').'"', false);
    }
}
