<?php

namespace Tests\Feature;

use Tests\TestCase;

class LocaleTest extends TestCase
{
    public function test_public_site_defaults_to_english(): void
    {
        $this->get('/prestasi')
            ->assertOk()
            ->assertSee('<html lang="en"', false)
            ->assertSee('<title>Achievements — SMK Pancakarya</title>', false);
    }

    public function test_visitors_can_switch_the_site_to_indonesian(): void
    {
        $this->from('/prestasi')
            ->get(route('language.switch', 'id'))
            ->assertRedirect('/prestasi');

        $this->assertSame('id', session('locale'));
    }

    public function test_indonesian_locale_renders_indonesian_menu_and_titles(): void
    {
        $this->withSession(['locale' => 'id'])
            ->get('/prestasi')
            ->assertOk()
            ->assertSee('<html lang="id"', false)
            ->assertSee('Beranda')
            ->assertSee('<title>Prestasi — SMK Pancakarya</title>', false)
            ->assertSee('Info PPDB');
    }

    public function test_visitors_can_switch_back_to_english(): void
    {
        $this->withSession(['locale' => 'id'])
            ->get('/profil/about')
            ->assertSee('<title>About — SMK Pancakarya</title>', false);

        $this->from('/profil/about')
            ->get(route('language.switch', 'en'))
            ->assertRedirect('/profil/about');

        $this->assertSame('en', session('locale'));
    }

    public function test_unsupported_locales_are_rejected(): void
    {
        $this->get(route('language.switch', 'fr'))->assertNotFound();
    }
}
