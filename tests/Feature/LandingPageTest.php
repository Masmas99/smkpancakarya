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

    public function test_landing_page_defaults_to_english(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<html lang="en"', false)
            ->assertSee('Vocational High School');
    }

    public function test_landing_page_shows_the_main_navigation(): void
    {
        $response = $this->get('/');

        foreach ([
            'Home',
            'Study Programs',
            'Achievements',
            'Profile',
            'Vision and Mission',
            'Contact',
            'Facilities',
            'Admission Info',
        ] as $menu) {
            $response->assertSee($menu);
        }
    }

    public function test_header_shows_search_language_switch_and_primary_action(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('aria-label="Search"', false)
            ->assertSee('href="'.route('language.switch', 'en').'"', false)
            ->assertSee('href="'.route('language.switch', 'id').'"', false)
            ->assertSee('data-search-item', false)
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
