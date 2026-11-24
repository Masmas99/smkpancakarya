<?php

use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\ArtikelController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Staff\ArticleController as StaffArticleController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('artikel', [ArtikelController::class, 'index'])->name('artikel.index');
Route::get('artikel/{article}', [ArtikelController::class, 'show'])->name('artikel.show');

Route::view('kompetensi-keahlian', 'pages.landing.kompetensi-keahlian')->name('kompetensi-keahlian');
Route::view('prestasi', 'pages.landing.prestasi')->name('prestasi');
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
    });

Route::middleware(['auth', 'verified', 'role:staff'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        Route::resource('artikel', StaffArticleController::class)->except('show');
    });

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';
