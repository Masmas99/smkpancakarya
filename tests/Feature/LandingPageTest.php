<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_is_accessible(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('SMK Pancakarya')
            ->assertSee('images/logo-smk-pancakarya.webp', false)
            ->assertSee('alt="Logo SMK Pancakarya"', false);
    }

    public function test_landing_page_uses_indonesian(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<html lang="id"', false)
            ->assertSee('Kota Tangerang')
            ->assertSee('<title>SMK Pancakarya — Kota Tangerang</title>', false);
    }

    public function test_landing_page_shows_the_main_navigation(): void
    {
        $response = $this->get('/');

        foreach ([
            'Beranda',
            'Kompetensi Keahlian',
            'Prestasi',
            'Profil',
            'Visi dan Misi',
            'Kontak',
            'Sarana',
            'Info PPDB',
        ] as $menu) {
            $response->assertSee($menu);
        }
    }

    public function test_header_shows_search_and_primary_action(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('aria-label="Cari"', false)
            ->assertSee('id="site-search"', false)
            ->assertSee('data-search-item', false)
            ->assertSee('bg-blue-700', false)
            ->assertDontSee('/language/', false);
    }

    public function test_top_bar_shows_contact_information(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Jl. Perintis Kemerdekaan 1/3 Cikokol, Kota Tangerang')
            ->assertSee('href="tel:0215581180"', false)
            ->assertSee('021-5581180')
            ->assertSee('NPSN')
            ->assertSee('20606894');
    }

    public function test_landing_page_shows_achievement_highlights(): void
    {
        $response = $this->get('/');

        foreach ([
            'Juara 1 LKS Nasional',
            'Bidang IT Network Systems Administration',
            'Seleknas ASEAN Skills',
            'Perwakilan Kompetensi Tingkat Nasional',
            'Kompetensi Keahlian',
            'Kurikulum Selaras Dunia Industri',
            'Bursa Kerja Khusus (BKK)',
            'Jaringan Penyaluran Kerja Tangerang & Jabodetabek',
        ] as $text) {
            $response->assertSee($text);
        }

        foreach ([
            'images/trophy-gold.webp',
            'images/globe-asia.webp',
            'images/graduationcap.webp',
            'images/briefcase.webp',
        ] as $icon) {
            $response->assertSee($icon, false);
        }
    }

    public function test_landing_page_shows_headmaster_welcome_section(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Selayang Pandang')
            ->assertSee('Selamat datang di SMKS Pancakarya Kota Tangerang.')
            ->assertSee('Baca Profil Lengkap')
            ->assertSee('Kepala SMKS Pancakarya Kota Tangerang')
            ->assertSee('images/kepala-sekolah.jpg', false)
            ->assertSee('images/wave-kanan.svg', false)
            ->assertSee('images/wave-kiri.svg', false)
            ->assertSee('href="'.route('profil.about').'"', false);
    }

    public function test_header_hides_on_scroll_down_and_reappears_on_scroll_up(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('id="site-header"', false)
            ->assertSee('-translate-y-full', false)
            ->assertSee('syncHeaderWithScroll', false);
    }

    public function test_navigation_links_point_to_menu_pages(): void
    {
        $response = $this->get('/');

        foreach ([
            'kompetensi-keahlian',
            'prestasi',
            'profil',
            'profil.about',
            'profil.kesiswaan',
            'profil.akademik',
            'visi-dan-misi',
            'kontak',
            'sarana',
            'info-ppdb',
        ] as $route) {
            $response->assertSee('href="'.route($route).'"', false);
        }
    }
}
