<?php

namespace Database\Seeders;

use App\Models\Gallery;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Foto album galeri yang diimpor dari situs lama sekolah.
 *
 * Jalankan dengan: php artisan db:seed --class=GallerySeeder
 */
class GallerySeeder extends Seeder
{
    /**
     * Seed foto album galeri.
     */
    public function run(): void
    {
        $author = User::where('email', 'admin@smkpancakarya.test')->first();
        $source = database_path('seeders/assets/galeri');

        foreach ($this->photos() as $index => $photo) {
            $file = $source.DIRECTORY_SEPARATOR.$photo['file'];

            if (! File::exists($file)) {
                $this->command?->warn('Berkas foto tidak ditemukan: '.$photo['file']);

                continue;
            }

            $path = 'galeri/'.$photo['file'];

            Storage::disk('public')->put($path, File::get($file));

            Gallery::firstOrCreate(
                ['image' => $path],
                [
                    'user_id' => $author?->id,
                    'title' => $photo['title'],
                    'is_published' => true,
                    // Foto teratas pada daftar dianggap paling baru agar urutan
                    // otomatis (created_at terbaru lebih dahulu) tetap sesuai.
                    'created_at' => now()->subMinutes($index),
                ],
            );
        }
    }

    /**
     * Daftar foto album, diurutkan dari kegiatan terbaru.
     *
     * @return list<array{file: string, title: string}>
     */
    private function photos(): array
    {
        return [
            [
                'file' => 'pkl-kelas-xii-tkj-2024.jpg',
                'title' => 'Praktik Kerja Lapangan Kelas XII Jurusan Teknologi Komputer dan Jaringan 2024',
            ],
            [
                'file' => 'diskusi-p5-personal-branding-2024.jpg',
                'title' => 'Diskusi Kelompok P5 Personal Branding 2024',
            ],
            [
                'file' => 'ulangan-umum-praktek-kelas-12-2024.jpg',
                'title' => 'Ulangan Umum Praktek Kelas 12 Tahun 2024',
            ],
            [
                'file' => 'penilaian-tengah-semester-2024.jpg',
                'title' => 'Penilaian Tengah Semester Paperless Based 2024/2025',
            ],
            [
                'file' => 'ldks-agustus-2024.jpg',
                'title' => 'Penyelenggaraan LDKS 30-31 Agustus 2024',
            ],
            [
                'file' => 'lks-provinsi-banten-2021.jpeg',
                'title' => 'LKS Tingkat Provinsi Banten 2021 - IT Software Solutions for Business',
            ],
            [
                'file' => 'medali-juara-1-lks-itnsa-nasional-2021.jpg',
                'title' => 'Penyerahan Medali Juara 1 LKS ITNSA Tingkat Nasional ke-28',
            ],
            [
                'file' => 'lks-tki-kota-tangerang-2020.jpg',
                'title' => 'LKS Bidang TKI Tingkat Kota Tangerang 2020',
            ],
            [
                'file' => 'upacara-sumpah-pemuda-2019.jpg',
                'title' => 'Upacara Peringatan Hari Sumpah Pemuda ke-91',
            ],
            [
                'file' => 'olimpiade-mikrotik-apjii-2019.jpg',
                'title' => 'Penyisihan Jakarta - Olimpiade Jaringan Mikrotik APJII 2019',
            ],
            [
                'file' => 'piala-juara-3-lks-itnsa-nasional-2019.jpg',
                'title' => 'Penyerahan Piala dan Medali Juara 3 LKS ITNSA Tingkat Nasional',
            ],
            [
                'file' => 'piala-juara-1-lks-itnsa-banten-2019.jpeg',
                'title' => 'Penyerahan Piala Juara 1 LKS ITNSA Tingkat Provinsi Banten',
            ],
            [
                'file' => 'olimpiade-mikrotik-jabodetabek-2018.jpg',
                'title' => 'Olimpiade Mikrotik Regional Jabodetabek 2018',
            ],
            [
                'file' => 'mum-yogyakarta-2018.jpg',
                'title' => 'MUM Yogyakarta 2018',
            ],
        ];
    }
}
