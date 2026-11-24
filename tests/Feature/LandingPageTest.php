<?php

namespace Tests\Feature;

use Tests\TestCase;

class LandingPageTest extends TestCase
{
    public function test_landing_page_is_accessible(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('SMK Pancakarya')
            ->assertSee('images/logo-smk-pancakarya.webp', false)
            ->assertSee('alt="Logo SMK Pancakarya"', false);
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

    public function test_header_shows_search_language_switch_and_primary_action(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('aria-label="Cari"', false)
            ->assertSee('aria-label="Bahasa Indonesia"', false)
            ->assertSee('bg-blue-700', false);
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
