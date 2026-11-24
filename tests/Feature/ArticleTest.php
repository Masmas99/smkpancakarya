<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_article_index_only_lists_published_articles(): void
    {
        $published = Article::factory()->published()->create(['title' => 'Artikel Sudah Terbit']);
        Article::factory()->create(['title' => 'Artikel Masih Draf']);

        $this->get('/artikel')
            ->assertOk()
            ->assertSee('Artikel Sudah Terbit')
            ->assertDontSee('Artikel Masih Draf');

        $this->assertTrue($published->fresh()->isPublished());
    }

    public function test_public_article_detail_is_available_for_published_articles_only(): void
    {
        $published = Article::factory()->published()->create(['title' => 'Berita Sekolah']);
        $draft = Article::factory()->create(['title' => 'Belum Terbit']);

        $this->get(route('artikel.show', $published))
            ->assertOk()
            ->assertSee('Berita Sekolah');

        $this->get(route('artikel.show', $draft))->assertNotFound();
    }

    public function test_articles_scheduled_in_the_future_are_not_public(): void
    {
        $article = Article::factory()->create([
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now()->addDay(),
        ]);

        $this->get(route('artikel.show', $article))->assertNotFound();
    }

    public function test_guests_are_redirected_to_login_from_article_management(): void
    {
        $this->get('/admin/artikel')->assertRedirect('/login');
        $this->get('/staff/artikel')->assertRedirect('/login');
    }

    public function test_staff_can_open_staff_article_management_but_not_admin(): void
    {
        $staff = User::factory()->create();

        $this->actingAs($staff)
            ->get('/staff/artikel')
            ->assertOk()
            ->assertSee('Kelola Artikel')
            ->assertSee('Tulis Artikel');

        $this->actingAs($staff)
            ->get('/admin/artikel')
            ->assertForbidden();
    }

    public function test_admin_can_open_admin_article_management_but_not_staff(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin/artikel')
            ->assertOk()
            ->assertSee('Kelola Artikel');

        $this->actingAs($admin)
            ->get('/staff/artikel')
            ->assertForbidden();
    }

    public function test_staff_can_create_a_published_article(): void
    {
        $staff = User::factory()->create();

        $response = $this->actingAs($staff)->post('/staff/artikel', [
            'title' => 'Kegiatan Peringatan Hari Guru',
            'excerpt' => 'SMK Pancakarya menggelar upacara peringatan Hari Guru.',
            'body' => "Paragraf pertama kegiatan.\n\nParagraf kedua kegiatan.",
            'status' => Article::STATUS_PUBLISHED,
        ]);

        $response->assertRedirect(route('staff.artikel.index'));

        $article = Article::firstOrFail();

        $this->assertSame('Kegiatan Peringatan Hari Guru', $article->title);
        $this->assertSame('kegiatan-peringatan-hari-guru', $article->slug);
        $this->assertSame(Article::DEFAULT_CATEGORY, $article->category);
        $this->assertSame(Article::STATUS_PUBLISHED, $article->status);
        $this->assertSame($staff->id, $article->user_id);
        $this->assertNotNull($article->published_at);
    }

    public function test_staff_can_save_a_draft_article(): void
    {
        $staff = User::factory()->create();

        $this->actingAs($staff)->post('/staff/artikel', [
            'title' => 'Draf Informasi Sekolah',
            'body' => 'Isi artikel masih dalam penyusunan.',
            'status' => Article::STATUS_DRAFT,
        ])->assertRedirect(route('staff.artikel.index'));

        $article = Article::firstOrFail();

        $this->assertSame(Article::STATUS_DRAFT, $article->status);
        $this->assertNull($article->published_at);
    }

    public function test_article_store_validates_required_fields(): void
    {
        $staff = User::factory()->create();

        $this->actingAs($staff)
            ->post('/staff/artikel', ['status' => 'invalid'])
            ->assertSessionHasErrors(['title', 'body', 'status']);

        $this->assertDatabaseCount('articles', 0);
    }

    public function test_staff_can_upload_a_cover_image(): void
    {
        Storage::fake('public');

        $staff = User::factory()->create();

        $this->actingAs($staff)->post('/staff/artikel', [
            'title' => 'Artikel Dengan Sampul',
            'body' => 'Isi artikel.',
            'status' => Article::STATUS_PUBLISHED,
            'cover_image' => UploadedFile::fake()->image('sampul.jpg', 1200, 750),
        ])->assertRedirect(route('staff.artikel.index'));

        $article = Article::firstOrFail();

        $this->assertNotNull($article->cover_image);
        Storage::disk('public')->assertExists($article->cover_image);
    }

    public function test_staff_can_update_and_unpublish_an_article(): void
    {
        $staff = User::factory()->create();
        $article = Article::factory()->published()->create([
            'title' => 'Judul Lama',
        ]);

        $this->actingAs($staff)->put(route('staff.artikel.update', $article), [
            'title' => 'Judul Baru Artikel',
            'body' => 'Isi yang sudah diperbarui.',
            'status' => Article::STATUS_DRAFT,
        ])->assertRedirect(route('staff.artikel.index'));

        $article->refresh();

        $this->assertSame('Judul Baru Artikel', $article->title);
        $this->assertSame('judul-baru-artikel', $article->slug);
        $this->assertSame(Article::STATUS_DRAFT, $article->status);
        $this->assertNull($article->published_at);
    }

    public function test_updating_a_cover_image_replaces_the_old_file(): void
    {
        Storage::fake('public');

        $staff = User::factory()->create();
        $article = Article::factory()->create(['cover_image' => 'artikel/lama.jpg']);

        Storage::disk('public')->put('artikel/lama.jpg', 'lama');

        $this->actingAs($staff)->put(route('staff.artikel.update', $article), [
            'title' => $article->title,
            'body' => $article->body,
            'status' => Article::STATUS_DRAFT,
            'cover_image' => UploadedFile::fake()->image('baru.png'),
        ])->assertRedirect(route('staff.artikel.index'));

        $article->refresh();

        Storage::disk('public')->assertMissing('artikel/lama.jpg');
        Storage::disk('public')->assertExists($article->cover_image);
        $this->assertNotSame('artikel/lama.jpg', $article->cover_image);
    }

    public function test_staff_can_delete_an_article_with_its_cover(): void
    {
        Storage::fake('public');

        $staff = User::factory()->create();
        $article = Article::factory()->create(['cover_image' => 'artikel/hapus.jpg']);

        Storage::disk('public')->put('artikel/hapus.jpg', 'gambar');

        $this->actingAs($staff)
            ->delete(route('staff.artikel.destroy', $article))
            ->assertRedirect(route('staff.artikel.index'));

        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
        Storage::disk('public')->assertMissing('artikel/hapus.jpg');
    }

    public function test_duplicate_titles_receive_unique_slugs(): void
    {
        $first = Article::factory()->create(['title' => 'Berita Sama']);
        $second = Article::factory()->create(['title' => 'Berita Sama']);

        $this->assertSame('berita-sama', $first->slug);
        $this->assertSame('berita-sama-2', $second->slug);
    }

    public function test_berita_page_only_lists_news_category_articles(): void
    {
        Article::factory()->published()->create(['title' => 'Kabar Berita Sekolah', 'category' => 'berita']);
        Article::factory()->published()->create(['title' => 'Juara Prestasi Sekolah', 'category' => 'prestasi']);
        Article::factory()->published()->create(['title' => 'Tulisan Guru Pertama', 'category' => 'blog-guru']);

        $this->get('/berita')
            ->assertOk()
            ->assertSee('Kabar Berita Sekolah')
            ->assertDontSee('Juara Prestasi Sekolah')
            ->assertDontSee('Tulisan Guru Pertama');
    }

    public function test_blog_guru_page_only_lists_blog_guru_articles(): void
    {
        Article::factory()->published()->create(['title' => 'Kabar Berita Sekolah', 'category' => 'berita']);
        Article::factory()->published()->create(['title' => 'Tulisan Guru Pertama', 'category' => 'blog-guru']);

        $this->get('/blog-guru')
            ->assertOk()
            ->assertSee('Blog Guru')
            ->assertSee('Tulisan Guru Pertama')
            ->assertDontSee('Kabar Berita Sekolah');
    }

    public function test_blog_guru_page_shows_empty_state(): void
    {
        $this->get('/blog-guru')
            ->assertOk()
            ->assertSee('Belum ada tulisan guru yang dipublikasikan.');
    }

    public function test_homepage_does_not_show_blog_guru_articles(): void
    {
        Article::factory()->published()->create(['title' => 'Kabar Beranda Sekolah', 'category' => 'berita']);
        Article::factory()->published()->create(['title' => 'Tulisan Guru di Beranda', 'category' => 'blog-guru']);

        $this->get('/')
            ->assertOk()
            ->assertSee('Kabar Beranda Sekolah')
            ->assertDontSee('Tulisan Guru di Beranda');
    }

    public function test_staff_can_create_a_blog_guru_article(): void
    {
        $staff = User::factory()->create();

        $this->actingAs($staff)->post('/staff/artikel', [
            'title' => 'Refleksi Mengajar di Kelas',
            'body' => 'Isi tulisan guru.',
            'category' => 'blog-guru',
            'status' => Article::STATUS_PUBLISHED,
        ])->assertRedirect(route('staff.artikel.index'));

        $this->get('/blog-guru')
            ->assertOk()
            ->assertSee('Refleksi Mengajar di Kelas');
    }

    public function test_landing_page_shows_article_section_below_welcome(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Artikel dan Berita Terbaru')
            ->assertSee('Berita')
            ->assertSee('Prestasi')
            ->assertSee('Press Release')
            ->assertSee('Belum ada artikel yang dipublikasikan.');
    }

    public function test_landing_page_shows_latest_published_articles(): void
    {
        Article::factory()->published()->create(['title' => 'Berita Terbaru di Beranda']);

        $this->get('/')
            ->assertOk()
            ->assertSee('Artikel dan Berita Terbaru')
            ->assertSee('Berita Terbaru di Beranda');
    }

    public function test_landing_page_shows_category_tabs(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Artikel dan Berita Terbaru')
            ->assertSee('Berita')
            ->assertSee('Prestasi')
            ->assertSee('Press Release');
    }

    public function test_landing_page_article_section_can_filter_by_category(): void
    {
        Article::factory()->published()->create(['title' => 'Berita Umum Sekolah', 'category' => 'berita']);
        Article::factory()->published()->create(['title' => 'Juara Lomba Kompetensi', 'category' => 'prestasi']);
        Article::factory()->published()->create(['title' => 'Rilis Pers Sekolah', 'category' => 'press-release']);

        $this->get('/?category=prestasi')
            ->assertOk()
            ->assertSee('Juara Lomba Kompetensi')
            ->assertDontSee('Berita Umum Sekolah')
            ->assertDontSee('Rilis Pers Sekolah');
    }

    public function test_landing_page_ignores_unknown_article_category(): void
    {
        Article::factory()->published()->create(['title' => 'Artikel Tetap Tampil', 'category' => 'berita']);

        $this->get('/?category=tidak-ada')
            ->assertOk()
            ->assertSee('Artikel Tetap Tampil');
    }

    public function test_landing_page_article_section_can_load_more_articles(): void
    {
        foreach (range(1, 7) as $index) {
            Article::factory()->published()->create([
                'title' => 'Artikel Beranda '.$index,
                'published_at' => now()->subMinutes($index),
            ]);
        }

        $this->get('/')
            ->assertOk()
            ->assertSee('Muat Lebih Banyak')
            ->assertSee('Artikel Beranda 1')
            ->assertDontSee('Artikel Beranda 7');

        $this->get('/?page=2')
            ->assertOk()
            ->assertSee('Artikel Beranda 7');
    }

    public function test_staff_can_choose_an_article_category(): void
    {
        $staff = User::factory()->create();

        $this->actingAs($staff)->post('/staff/artikel', [
            'title' => 'Prestasi Siswa Bulan Ini',
            'body' => 'Isi artikel prestasi.',
            'category' => 'prestasi',
            'status' => Article::STATUS_PUBLISHED,
        ])->assertRedirect(route('staff.artikel.index'));

        $this->assertDatabaseHas('articles', [
            'title' => 'Prestasi Siswa Bulan Ini',
            'category' => 'prestasi',
        ]);
    }

    public function test_admin_can_create_an_article_from_the_admin_area(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/artikel', [
            'title' => 'Artikel Admin',
            'body' => 'Isi artikel.',
            'status' => Article::STATUS_DRAFT,
        ])->assertRedirect(route('admin.artikel.index'));

        $this->assertDatabaseHas('articles', ['title' => 'Artikel Admin']);
    }

    public function test_admin_and_staff_manage_the_same_articles(): void
    {
        $article = Article::factory()->published()->create(['title' => 'Artikel Bersama']);
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create();

        $this->actingAs($admin)->get('/admin/artikel')->assertSee('Artikel Bersama');
        $this->actingAs($staff)->get('/staff/artikel')->assertSee('Artikel Bersama');

        $this->actingAs($staff)->delete(route('staff.artikel.destroy', $article))
            ->assertRedirect(route('staff.artikel.index'));

        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }
}
