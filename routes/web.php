<?php

use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\BeritaController as AdminBeritaController;
use App\Http\Controllers\Admin\BlogGuruController as AdminBlogGuruController;
use App\Http\Controllers\Admin\PrestasiController as AdminPrestasiController;
use App\Http\Controllers\ArtikelController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\BlogGuruController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PrestasiController;
use App\Http\Controllers\Staff\ArticleController as StaffArticleController;
use App\Http\Controllers\Staff\BeritaController as StaffBeritaController;
use App\Http\Controllers\Staff\BlogGuruController as StaffBlogGuruController;
use App\Http\Controllers\Staff\PrestasiController as StaffPrestasiController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('artikel', [ArtikelController::class, 'index'])->name('artikel.index');
Route::get('artikel/{article}', [ArtikelController::class, 'show'])->name('artikel.show');
Route::get('berita', [BeritaController::class, 'index'])->name('berita.index');
Route::get('berita/{article}', [BeritaController::class, 'show'])->name('berita.show');
Route::get('prestasi', [PrestasiController::class, 'index'])->name('prestasi.index');
Route::get('prestasi/{article}', [PrestasiController::class, 'show'])->name('prestasi.show');
Route::get('blog-guru', [BlogGuruController::class, 'index'])->name('blog-guru.index');
Route::get('blog-guru/{article}', [BlogGuruController::class, 'show'])->name('blog-guru.show');

Route::view('kompetensi-keahlian', 'pages.landing.kompetensi-keahlian')->name('kompetensi-keahlian');
Route::view('profil', 'pages.landing.profil')->name('profil');
Route::view('profil/about', 'pages.landing.profil.about')->name('profil.about');
Route::view('profil/kesiswaan', 'pages.landing.profil.kesiswaan')->name('profil.kesiswaan');
Route::view('profil/akademik', 'pages.landing.profil.akademik')->name('profil.akademik');
Route::view('visi-dan-misi', 'pages.landing.visi-dan-misi')->name('visi-dan-misi');
Route::view('kontak', 'pages.landing.kontak')->name('kontak');
Route::view('sarana', 'pages.landing.sarana')->name('sarana');
Route::view('info-ppdb', 'pages.landing.info-ppdb')->name('info-ppdb');

Route::get('dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('admin/dashboard', 'pages.admin.index')
    ->middleware(['auth', 'verified', 'role:admin'])
    ->name('admin.dashboard');

Route::view('staff/dashboard', 'pages.staff.index')
    ->middleware(['auth', 'verified', 'role:staff'])
    ->name('staff.dashboard');

Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('artikel', AdminArticleController::class)->except('show');

        Route::resource('berita', AdminBeritaController::class)
            ->except('show')
            ->parameters(['berita' => 'artikel']);

        Route::resource('prestasi', AdminPrestasiController::class)
            ->except('show')
            ->parameters(['prestasi' => 'artikel']);

        Route::resource('blog-guru', AdminBlogGuruController::class)
            ->except('show')
            ->parameters(['blog-guru' => 'artikel']);
    });

Route::middleware(['auth', 'verified', 'role:staff'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        Route::resource('artikel', StaffArticleController::class)->except('show');

        Route::resource('berita', StaffBeritaController::class)
            ->except('show')
            ->parameters(['berita' => 'artikel']);

        Route::resource('prestasi', StaffPrestasiController::class)
            ->except('show')
            ->parameters(['prestasi' => 'artikel']);

        Route::resource('blog-guru', StaffBlogGuruController::class)
            ->except('show')
            ->parameters(['blog-guru' => 'artikel']);
    });

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
