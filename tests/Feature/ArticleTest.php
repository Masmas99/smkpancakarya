<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Artikel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_article_index_only_lists_published_articles(): void
    {
        $published = Article::factory()->published()->create(['title' => 'Artikel Sudah Terbit', 'category' => 'artikel']);
        Article::factory()->create(['title' => 'Artikel Masih Draf', 'category' => 'artikel']);

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

        $this->get($published->url())
            ->assertOk()
            ->assertSee('Berita Sekolah');

        $this->get($draft->url())->assertNotFound();
    }

    public function test_articles_scheduled_in_the_future_are_not_public(): void
    {
        $article = Article::factory()->create([
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now()->addDay(),
        ]);

        $this->get($article->url())->assertNotFound();
    }

    public function test_article_detail_urls_are_separated_by_category(): void
    {
        $berita = Article::factory()->published()->create(['title' => 'Detail Berita', 'category' => 'berita']);
        $prestasi = Article::factory()->published()->create(['title' => 'Detail Prestasi', 'category' => 'prestasi']);
        $artikel = Article::factory()->published()->create(['title' => 'Detail Artikel', 'category' => 'artikel']);
        $blog = Article::factory()->published()->create(['title' => 'Detail Blog Guru', 'category' => 'blog-guru']);

        $this->get(route('berita.show', $berita))->assertOk()->assertSee('Detail Berita');
        $this->get(route('prestasi.show', $prestasi))->assertOk()->assertSee('Detail Prestasi');
        $this->get(route('artikel.show', $artikel))->assertOk()->assertSee('Detail Artikel');
        $this->get(route('blog-guru.show', $blog))->assertOk()->assertSee('Detail Blog Guru');
    }

    public function test_article_detail_redirects_to_its_canonical_category_url(): void
    {
        $prestasi = Article::factory()->published()->create(['title' => 'Juara Kota', 'category' => 'prestasi']);

        $this->get(route('artikel.show', $prestasi))
            ->assertStatus(301)
            ->assertRedirect(route('prestasi.show', $prestasi));

        $this->get(route('berita.show', $prestasi))
            ->assertStatus(301)
            ->assertRedirect(route('prestasi.show', $prestasi));
    }

    public function test_guests_are_redirected_to_login_from_article_management(): void
    {
        foreach (['artikel', 'berita', 'prestasi', 'blog-guru'] as $section) {
            $this->get('/admin/'.$section)->assertRedirect('/login');
            $this->get('/staff/'.$section)->assertRedirect('/login');
        }
    }

    public function test_staff_can_open_staff_article_management_but_not_admin(): void
    {
        $staff = User::factory()->create();

        $sections = [
            'artikel' => 'Kelola Artikel',
            'berita' => 'Kelola Berita',
            'prestasi' => 'Kelola Prestasi',
            'blog-guru' => 'Kelola Blog Guru',
        ];

        foreach ($sections as $section => $heading) {
            $this->actingAs($staff)
                ->get('/staff/'.$section)
                ->assertOk()
                ->assertSee($heading)
                ->assertSee('Tulis '.str_replace('Kelola ', '', $heading));
        }

        $this->actingAs($staff)
            ->get('/admin/artikel')
            ->assertForbidden();
    }

    public function test_admin_can_open_admin_article_management_but_not_staff(): void
    {
        $admin = User::factory()->admin()->create();

        $sections = [
            'artikel' => 'Kelola Artikel',
            'berita' => 'Kelola Berita',
            'prestasi' => 'Kelola Prestasi',
            'blog-guru' => 'Kelola Blog Guru',
        ];

        foreach ($sections as $section => $heading) {
            $this->actingAs($admin)
                ->get('/admin/'.$section)
                ->assertOk()
                ->assertSee($heading);
        }

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

        $article = Artikel::firstOrFail();

        $this->assertSame('Kegiatan Peringatan Hari Guru', $article->title);
        $this->assertSame('kegiatan-peringatan-hari-guru', $article->slug);
        $this->assertSame(Article::ARTICLE_CATEGORY, $article->category);
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

        $article = Artikel::firstOrFail();

        $this->assertSame(Article::STATUS_DRAFT, $article->status);
        $this->assertNull($article->published_at);
    }

    public function test_article_store_validates_required_fields(): void
    {
        $staff = User::factory()->create();

        $this->actingAs($staff)
            ->post('/staff/artikel', ['status' => 'invalid'])
            ->assertSessionHasErrors(['title', 'body', 'status']);

        $this->assertDatabaseCount('artikel', 0);
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

        $article = Artikel::firstOrFail();

        $this->assertNotNull($article->cover_image);
        Storage::disk('public')->assertExists($article->cover_image);
    }

    public function test_staff_can_update_and_unpublish_an_article(): void
    {
        $staff = User::factory()->create();
        $article = Article::factory()->published()->create([
            'title' => 'Judul Lama',
            'category' => Article::ARTICLE_CATEGORY,
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
        $article = Article::factory()->create([
            'cover_image' => 'artikel/lama.jpg',
            'category' => Article::ARTICLE_CATEGORY,
        ]);

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
        $article = Article::factory()->create([
            'cover_image' => 'artikel/hapus.jpg',
            'category' => Article::ARTICLE_CATEGORY,
        ]);

        Storage::disk('public')->put('artikel/hapus.jpg', 'gambar');

        $this->actingAs($staff)
            ->delete(route('staff.artikel.destroy', $article))
            ->assertRedirect(route('staff.artikel.index'));

        $this->assertDatabaseMissing('artikel', ['id' => $article->id]);
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
        $berita = Article::factory()->published()->create(['title' => 'Kabar Berita Sekolah', 'category' => 'berita']);
        Article::factory()->published()->create(['title' => 'Juara Prestasi Sekolah', 'category' => 'prestasi']);
        Article::factory()->published()->create(['title' => 'Artikel Umum Sekolah', 'category' => 'artikel']);
        Article::factory()->published()->create(['title' => 'Tulisan Guru Pertama', 'category' => 'blog-guru']);

        $response = $this->get('/berita')
            ->assertOk()
            ->assertSee('Kabar Berita Sekolah')
            ->assertDontSee('Juara Prestasi Sekolah')
            ->assertDontSee('Artikel Umum Sekolah')
            ->assertDontSee('Tulisan Guru Pertama');

        $response->assertSee(route('berita.show', $berita), false);
    }

    public function test_artikel_page_only_lists_artikel_category_articles(): void
    {
        $artikel = Article::factory()->published()->create(['title' => 'Artikel Umum Sekolah', 'category' => 'artikel']);
        Article::factory()->published()->create(['title' => 'Kabar Berita Sekolah', 'category' => 'berita']);
        Article::factory()->published()->create(['title' => 'Juara Prestasi Sekolah', 'category' => 'prestasi']);
        Article::factory()->published()->create(['title' => 'Tulisan Guru Pertama', 'category' => 'blog-guru']);

        $response = $this->get('/artikel')
            ->assertOk()
            ->assertSee('Artikel Umum Sekolah')
            ->assertDontSee('Kabar Berita Sekolah')
            ->assertDontSee('Juara Prestasi Sekolah')
            ->assertDontSee('Tulisan Guru Pertama');

        $response->assertSee(route('artikel.show', $artikel), false);
    }

    public function test_blog_guru_page_only_lists_blog_guru_articles(): void
    {
        $blog = Article::factory()->published()->create(['title' => 'Tulisan Guru Pertama', 'category' => 'blog-guru']);
        Article::factory()->published()->create(['title' => 'Kabar Berita Sekolah', 'category' => 'berita']);
        Article::factory()->published()->create(['title' => 'Artikel Umum Sekolah', 'category' => 'artikel']);

        $response = $this->get('/blog-guru')
            ->assertOk()
            ->assertSee('Blog Guru')
            ->assertSee('Tulisan Guru Pertama')
            ->assertDontSee('Kabar Berita Sekolah')
            ->assertDontSee('Artikel Umum Sekolah');

        $response->assertSee(route('blog-guru.show', $blog), false);
    }

    public function test_blog_guru_page_shows_empty_state(): void
    {
        $this->get('/blog-guru')
            ->assertOk()
            ->assertSee('Belum ada tulisan guru yang dipublikasikan.');
    }

    public function test_prestasi_page_only_lists_prestasi_articles(): void
    {
        $prestasi = Article::factory()->published()->create(['title' => 'Juara Prestasi Sekolah', 'category' => 'prestasi']);
        Article::factory()->published()->create(['title' => 'Kabar Berita Sekolah', 'category' => 'berita']);
        Article::factory()->published()->create(['title' => 'Artikel Umum Sekolah', 'category' => 'artikel']);

        $response = $this->get('/prestasi')
            ->assertOk()
            ->assertSee('Juara Prestasi Sekolah')
            ->assertDontSee('Kabar Berita Sekolah')
            ->assertDontSee('Artikel Umum Sekolah');

        $response->assertSee(route('prestasi.show', $prestasi), false);
    }

    public function test_public_sections_have_their_own_card_partials(): void
    {
        foreach (['artikel', 'berita', 'prestasi', 'blog-guru'] as $section) {
            $this->assertFileExists(resource_path("views/pages/landing/{$section}/_card.blade.php"));
            $this->assertFileExists(resource_path("views/pages/landing/{$section}/_card-featured.blade.php"));

            $index = file_get_contents(resource_path("views/pages/landing/{$section}/index.blade.php"));

            $this->assertStringContainsString("pages.landing.{$section}._card", $index);
        }

        $home = file_get_contents(resource_path('views/pages/landing/index.blade.php'));

        $this->assertStringContainsString('pages.landing.{$activeCategory}._card-featured', $home);
    }

    public function test_homepage_does_not_show_blog_guru_articles(): void
    {
        Article::factory()->published()->create(['title' => 'Kabar Prestasi di Beranda', 'category' => 'prestasi']);
        Article::factory()->published()->create(['title' => 'Tulisan Guru di Beranda', 'category' => 'blog-guru']);

        $this->get('/')
            ->assertOk()
            ->assertSee('Kabar Prestasi di Beranda')
            ->assertDontSee('Tulisan Guru di Beranda');
    }

    public function test_staff_can_create_a_blog_guru_article(): void
    {
        $staff = User::factory()->create();

        $this->actingAs($staff)->post('/staff/blog-guru', [
            'title' => 'Refleksi Mengajar di Kelas',
            'body' => 'Isi tulisan guru.',
            'status' => Article::STATUS_PUBLISHED,
        ])->assertRedirect(route('staff.blog-guru.index'));

        $this->assertDatabaseHas('blog_guru', [
            'title' => 'Refleksi Mengajar di Kelas',
            'category' => Article::BLOG_CATEGORY,
        ]);

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
            ->assertSee('Artikel')
            ->assertSee('Blog Guru')
            ->assertSee('Belum ada artikel pada kategori ini.');
    }

    public function test_landing_page_shows_latest_published_articles(): void
    {
        Article::factory()->published()->create(['title' => 'Prestasi Terbaru di Beranda', 'category' => 'prestasi']);

        $this->get('/')
            ->assertOk()
            ->assertSee('Artikel dan Berita Terbaru')
            ->assertSee('Prestasi Terbaru di Beranda');
    }

    public function test_landing_page_shows_category_tabs(): void
    {
        $response = $this->get('/');
        $content = $response->getContent();

        $response
            ->assertOk()
            ->assertSee('Artikel dan Berita Terbaru')
            ->assertSee('Berita')
            ->assertSee('Prestasi')
            ->assertSee('Artikel')
            ->assertSee('Blog Guru')
            // Tab diganti di tempat tanpa reload dan tanpa mengubah URL.
            ->assertSee('data-article-tab', false)
            ->assertSee('data-category="berita"', false)
            ->assertSee('event.preventDefault()', false)
            ->assertSee("searchParams.delete('category')", false)
            ->assertDontSee('?category=berita#artikel', false)
            ->assertDontSee('?category=prestasi#artikel', false)
            ->assertDontSee('?category=artikel#artikel', false)
            ->assertDontSee('?category=blog-guru#artikel', false);

        // Konten lama tidak boleh diredupkan hingga tampak seperti area putih.
        // Batasi pemeriksaan pada bagian artikel agar kelas opacity milik lightbox tidak ikut terperiksa.
        $articleSection = Str::between($content, '<section id="artikel"', '</section>');

        $this->assertStringNotContainsString('opacity-60', $articleSection);
        $this->assertStringNotContainsString('transition-opacity', $articleSection);
    }

    public function test_landing_page_places_prestasi_tab_first(): void
    {
        $content = $this->get('/')->assertOk()->getContent();

        $prestasi = strpos($content, 'data-category="prestasi"');
        $berita = strpos($content, 'data-category="berita"');
        $artikel = strpos($content, 'data-category="artikel"');
        $blogGuru = strpos($content, 'data-category="blog-guru"');

        $this->assertNotFalse($prestasi);
        $this->assertNotFalse($berita);
        $this->assertNotFalse($artikel);
        $this->assertNotFalse($blogGuru);
        $this->assertTrue($prestasi < $berita);
        $this->assertTrue($berita < $artikel);
        $this->assertTrue($artikel < $blogGuru);
    }

    public function test_landing_page_article_section_can_filter_by_category(): void
    {
        Article::factory()->published()->create(['title' => 'Berita Umum Sekolah', 'category' => 'berita']);
        Article::factory()->published()->create(['title' => 'Juara Lomba Kompetensi', 'category' => 'prestasi']);
        Article::factory()->published()->create(['title' => 'Artikel Umum Sekolah', 'category' => 'artikel']);

        $this->get('/?category=prestasi')
            ->assertOk()
            ->assertSee('Juara Lomba Kompetensi')
            ->assertDontSee('Berita Umum Sekolah')
            ->assertDontSee('Artikel Umum Sekolah');
    }

    public function test_landing_page_ignores_unknown_article_category(): void
    {
        Article::factory()->published()->create(['title' => 'Artikel Tetap Tampil', 'category' => 'prestasi']);

        $this->get('/?category=tidak-ada')
            ->assertOk()
            ->assertSee('Artikel Tetap Tampil');
    }

    public function test_landing_page_article_section_shows_three_articles_and_links_to_prestasi(): void
    {
        foreach (range(1, 7) as $index) {
            Article::factory()->published()->create([
                'title' => 'Prestasi Beranda '.$index,
                'category' => 'prestasi',
                'published_at' => now()->subMinutes($index),
            ]);
        }

        $response = $this->get('/')
            ->assertOk()
            ->assertSee('Muat Lebih Banyak')
            ->assertSee('Prestasi Beranda 1')
            ->assertSee('Prestasi Beranda 2')
            ->assertSee('Prestasi Beranda 3')
            ->assertDontSee('Prestasi Beranda 4');

        $this->assertMatchesRegularExpression(
            '/id="load-more"\s+href="'.preg_quote(route('prestasi.index'), '/').'"/',
            $response->getContent(),
        );

        $this->get('/prestasi')
            ->assertOk()
            ->assertSee('Prestasi Beranda 4')
            ->assertSee('Prestasi Beranda 7');
    }

    public function test_landing_page_load_more_links_to_the_matching_category_page(): void
    {
        foreach (range(1, 4) as $index) {
            Article::factory()->published()->create(['title' => 'Berita '.$index, 'category' => 'berita']);
            Article::factory()->published()->create(['title' => 'Prestasi '.$index, 'category' => 'prestasi']);
            Article::factory()->published()->create(['title' => 'Artikel '.$index, 'category' => 'artikel']);
            Article::factory()->published()->create(['title' => 'Blog Guru '.$index, 'category' => 'blog-guru']);
        }

        $targets = [
            'berita' => route('berita.index'),
            'prestasi' => route('prestasi.index'),
            'artikel' => route('artikel.index'),
            'blog-guru' => route('blog-guru.index'),
        ];

        foreach ($targets as $category => $url) {
            $response = $this->get('/?category='.$category)->assertOk();

            $this->assertMatchesRegularExpression(
                '/id="load-more"\s+href="'.preg_quote($url, '/').'"/',
                $response->getContent(),
                'Tombol muat lebih banyak untuk tab '.$category.' tidak mengarah ke '.$url,
            );
        }
    }

    public function test_landing_page_defaults_to_prestasi_tab(): void
    {
        Article::factory()->published()->create(['title' => 'Kabar Berita Utama', 'category' => 'berita']);
        Article::factory()->published()->create(['title' => 'Kabar Prestasi Utama', 'category' => 'prestasi']);

        $this->get('/')
            ->assertOk()
            ->assertSee('Kabar Prestasi Utama')
            ->assertDontSee('Kabar Berita Utama');
    }

    public function test_landing_page_artikel_tab_only_shows_artikel_articles(): void
    {
        foreach (range(1, 4) as $index) {
            Article::factory()->published()->create([
                'title' => 'Artikel Utama '.$index,
                'category' => 'artikel',
                'published_at' => now()->subMinutes($index),
            ]);
        }

        Article::factory()->published()->create(['title' => 'Kabar Berita Utama', 'category' => 'berita']);
        Article::factory()->published()->create(['title' => 'Kabar Prestasi Utama', 'category' => 'prestasi']);
        Article::factory()->published()->create(['title' => 'Tulisan Guru Utama', 'category' => 'blog-guru']);

        $this->get('/?category=artikel')
            ->assertOk()
            ->assertSee('Artikel Utama 1')
            ->assertSee('Artikel Utama 2')
            ->assertSee('Artikel Utama 3')
            ->assertDontSee('Artikel Utama 4')
            ->assertDontSee('Kabar Berita Utama')
            ->assertDontSee('Kabar Prestasi Utama')
            ->assertDontSee('Tulisan Guru Utama');

        $this->get('/?category=artikel&page=2')
            ->assertOk()
            ->assertSee('Artikel Utama 4');
    }

    public function test_landing_page_blog_guru_tab_only_shows_blog_guru_articles(): void
    {
        Article::factory()->published()->create(['title' => 'Kabar Berita Utama', 'category' => 'berita']);
        Article::factory()->published()->create(['title' => 'Tulisan Guru Utama', 'category' => 'blog-guru']);

        $this->get('/?category=blog-guru')
            ->assertOk()
            ->assertSee('Tulisan Guru Utama')
            ->assertDontSee('Kabar Berita Utama');
    }

    public function test_staff_can_create_articles_in_each_section(): void
    {
        $staff = User::factory()->create();

        $sections = [
            'berita' => Article::DEFAULT_CATEGORY,
            'prestasi' => Article::PRESTASI_CATEGORY,
            'artikel' => Article::ARTICLE_CATEGORY,
            'blog-guru' => Article::BLOG_CATEGORY,
        ];

        foreach ($sections as $section => $category) {
            $title = 'Konten Baru '.ucfirst($section);

            $this->actingAs($staff)->post('/staff/'.$section, [
                'title' => $title,
                'body' => 'Isi konten.',
                'status' => Article::STATUS_DRAFT,
            ])->assertRedirect(route('staff.'.$section.'.index'));

            $table = str_replace('-', '_', $section);

            $this->assertDatabaseHas($table, [
                'title' => $title,
                'category' => $category,
            ]);
        }
    }

    public function test_admin_can_create_an_article_from_the_admin_area(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/artikel', [
            'title' => 'Artikel Admin',
            'body' => 'Isi artikel.',
            'status' => Article::STATUS_DRAFT,
        ])->assertRedirect(route('admin.artikel.index'));

        $this->assertDatabaseHas('artikel', [
            'title' => 'Artikel Admin',
            'category' => Article::ARTICLE_CATEGORY,
        ]);
    }

    public function test_admin_and_staff_manage_the_same_articles(): void
    {
        $article = Article::factory()->published()->create([
            'title' => 'Artikel Bersama',
            'category' => Article::ARTICLE_CATEGORY,
        ]);
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create();

        $this->actingAs($admin)->get('/admin/artikel')->assertSee('Artikel Bersama');
        $this->actingAs($staff)->get('/staff/artikel')->assertSee('Artikel Bersama');

        $this->actingAs($staff)->delete(route('staff.artikel.destroy', $article))
            ->assertRedirect(route('staff.artikel.index'));

        $this->assertDatabaseMissing('artikel', ['id' => $article->id]);
    }

    public function test_management_sections_only_list_their_own_category(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create();

        $titles = [
            'berita' => 'Konten Khusus Berita',
            'prestasi' => 'Konten Khusus Prestasi',
            'artikel' => 'Konten Khusus Artikel',
            'blog-guru' => 'Konten Khusus Blog Guru',
        ];

        foreach ($titles as $section => $title) {
            Article::factory()->create([
                'title' => $title,
                'category' => $section,
            ]);
        }

        foreach (['admin' => $admin, 'staff' => $staff] as $area => $user) {
            foreach ($titles as $section => $visibleTitle) {
                $response = $this->actingAs($user)
                    ->get('/'.$area.'/'.$section)
                    ->assertOk()
                    ->assertViewIs('pages.'.$area.'.'.$section.'.index')
                    ->assertSee($visibleTitle);

                foreach ($titles as $otherSection => $hiddenTitle) {
                    if ($otherSection !== $section) {
                        $response->assertDontSee($hiddenTitle);
                    }
                }
            }
        }
    }

    public function test_management_cannot_edit_articles_from_another_section(): void
    {
        $admin = User::factory()->admin()->create();
        $prestasi = Article::factory()->create([
            'title' => 'Prestasi Lintas Section',
            'category' => Article::PRESTASI_CATEGORY,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.berita.edit', $prestasi))
            ->assertNotFound();

        $this->actingAs($admin)
            ->put(route('admin.berita.update', $prestasi), [
                'title' => 'Diubah dari Berita',
                'body' => 'Isi.',
                'status' => Article::STATUS_DRAFT,
            ])
            ->assertNotFound();

        $this->actingAs($admin)
            ->delete(route('admin.berita.destroy', $prestasi))
            ->assertNotFound();

        $this->assertDatabaseHas('prestasi', [
            'id' => $prestasi->id,
            'title' => 'Prestasi Lintas Section',
        ]);
    }

    public function test_management_section_forces_its_own_category(): void
    {
        $staff = User::factory()->create();

        $this->actingAs($staff)->post('/staff/prestasi', [
            'title' => 'Prestasi Baru Staff',
            'body' => 'Isi prestasi.',
            'category' => Article::DEFAULT_CATEGORY,
            'status' => Article::STATUS_DRAFT,
        ])->assertRedirect(route('staff.prestasi.index'));

        $this->assertDatabaseHas('prestasi', [
            'title' => 'Prestasi Baru Staff',
            'category' => Article::PRESTASI_CATEGORY,
        ]);
    }

    public function test_management_create_and_edit_forms_show_their_section(): void
    {
        $users = [
            'admin' => User::factory()->admin()->create(),
            'staff' => User::factory()->create(),
        ];
        $labels = [
            'berita' => 'Berita',
            'prestasi' => 'Prestasi',
            'artikel' => 'Artikel',
            'blog-guru' => 'Blog Guru',
        ];

        foreach ($users as $area => $user) {
            foreach ($labels as $section => $label) {
                $this->actingAs($user)
                    ->get('/'.$area.'/'.$section.'/create')
                    ->assertOk()
                    ->assertViewIs('pages.'.$area.'.'.$section.'.create')
                    ->assertSee('Tulis '.$label);

                $title = 'Konten Edit '.$label.' '.$area;
                $article = Article::factory()->create([
                    'title' => $title,
                    'category' => $section,
                ]);

                $this->actingAs($user)
                    ->get(route($area.'.'.$section.'.edit', $article))
                    ->assertOk()
                    ->assertViewIs('pages.'.$area.'.'.$section.'.edit')
                    ->assertSee('Ubah '.$label)
                    ->assertSee($title);
            }
        }
    }

    public function test_dashboards_link_to_each_management_section(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create();

        $adminResponse = $this->actingAs($admin)->get('/admin/dashboard')->assertOk();
        $staffResponse = $this->actingAs($staff)->get('/staff/dashboard')->assertOk();

        foreach (['berita', 'prestasi', 'artikel', 'blog-guru'] as $section) {
            $adminResponse->assertSee('href="'.route('admin.'.$section.'.index').'"', false);
            $staffResponse->assertSee('href="'.route('staff.'.$section.'.index').'"', false);
        }
    }
}
