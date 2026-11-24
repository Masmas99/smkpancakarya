<?php

namespace Tests\Feature;

use Tests\TestCase;

class MenuPageTest extends TestCase
{
    public function test_menu_pages_are_accessible_and_show_their_title(): void
    {
        $pages = [
            '/kompetensi-keahlian' => 'Study Programs',
            '/prestasi' => 'Achievements',
            '/profil' => 'Profile',
            '/profil/about' => 'About',
            '/profil/kesiswaan' => 'Student Affairs',
            '/profil/akademik' => 'Academics',
            '/visi-dan-misi' => 'Vision and Mission',
            '/kontak' => 'Contact',
            '/sarana' => 'Facilities',
            '/info-ppdb' => 'Admission Info',
        ];

        foreach ($pages as $uri => $title) {
            $response = $this->get($uri);

            $response->assertOk();
            $response->assertSee('<title>'.$title.' — SMK Pancakarya</title>', false);
            $response->assertSee('<h1', false);
            $response->assertSee('aria-label="Breadcrumb"', false);
            $response->assertSee('aria-current="page"', false);
            $response->assertSee('href="'.route('home').'"', false);
        }
    }
}
