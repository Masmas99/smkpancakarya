<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Data artikel contoh untuk pengembangan.
 *
 * Jalankan dengan: php artisan db:seed --class=ArticleSeeder
 */
class ArticleSeeder extends Seeder
{
    /**
     * Seed artikel contoh.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@smkpancakarya.test')->first();
        $staff = User::where('email', 'staff@smkpancakarya.test')->first();

        if (! $admin || ! $staff) {
            $this->command?->warn('Jalankan DatabaseSeeder terlebih dahulu untuk membuat akun admin dan staff.');

            return;
        }

        foreach ($this->articles() as $index => $data) {
            $author = $data['author'] === 'staff' ? $staff : $admin;
            $slug = Str::slug($data['title']);

            Article::updateOrCreate(
                ['slug' => $slug],
                [
                    'user_id' => $author->id,
                    'title' => $data['title'],
                    'category' => $data['category'],
                    'excerpt' => $data['excerpt'],
                    'body' => $data['body'],
                    'cover_image' => $this->storeCover($slug, $data['category'], $data['title']),
                    'status' => Article::STATUS_PUBLISHED,
                    'published_at' => now()->subDays(($index + 1) * 2),
                ],
            );
        }
    }

    /**
     * @return list<array{title: string, category: string, author: string, excerpt: string, body: string}>
     */
    private function articles(): array
    {
        return [
            [
                'title' => 'Penerimaan Peserta Didik Baru Tahun Ajaran 2026/2027 Resmi Dibuka',
                'category' => 'berita',
                'author' => 'admin',
                'excerpt' => 'SMK Pancakarya membuka pendaftaran peserta didik baru untuk tahun ajaran 2026/2027 melalui beberapa jalur pendaftaran.',
                'body' => "SMK Pancakarya Kota Tangerang resmi membuka pendaftaran peserta didik baru untuk tahun ajaran 2026/2027. Pendaftaran dapat dilakukan secara daring melalui laman resmi sekolah maupun langsung di sekretariat PPDB.\n\nCalon peserta didik dapat memilih kompetensi keahlian sesuai minat dan bakatnya. Panitia PPDB menyediakan layanan konsultasi bagi siswa dan orang tua yang membutuhkan informasi lebih lanjut mengenai alur pendaftaran.\n\nSeluruh proses seleksi dilaksanakan secara transparan dan mengedepankan prinsip objektif. Jadwal dan persyaratan lengkap dapat dilihat pada bagian Info PPDB di situs sekolah.",
            ],
            [
                'title' => 'Sosialisasi Praktik Kerja Lapangan untuk Siswa Kelas XII',
                'category' => 'berita',
                'author' => 'admin',
                'excerpt' => 'Sekolah menggelar sosialisasi PKL untuk membekali siswa kelas XII dengan pemahaman dunia kerja.',
                'body' => "SMK Pancakarya menggelar sosialisasi Praktik Kerja Lapangan (PKL) bagi siswa kelas XII. Kegiatan ini bertujuan menyiapkan siswa agar mampu beradaptasi dengan lingkungan kerja profesional.\n\nDalam sosialisasi, siswa memperoleh penjelasan mengenai tata tertib, etika kerja, serta kompetensi yang perlu dikuasai selama PKL. Sekolah juga menekankan pentingnya komunikasi yang baik dengan pembimbing di industri.\n\nSiswa diharapkan memanfaatkan kesempatan PKL sebagai sarana belajar langsung dari dunia industri. Pendampingan akan dilakukan secara berkala oleh guru pembimbing dan pihak sekolah.",
            ],
            [
                'title' => 'Peringatan Hari Guru Berlangsung Khidmat dan Penuh Makna',
                'category' => 'berita',
                'author' => 'admin',
                'excerpt' => 'Keluarga besar SMK Pancakarya memperingati Hari Guru sebagai bentuk penghargaan kepada para pendidik.',
                'body' => "Keluarga besar SMK Pancakarya memperingati Hari Guru dengan kegiatan yang berlangsung khidmat. Acara diisi dengan upacara, pesan-pesan inspiratif, serta penampilan siswa sebagai bentuk apresiasi kepada para guru.\n\nDalam sambutannya, perwakilan sekolah menyampaikan terima kasih atas dedikasi para guru dalam mendidik dan membimbing siswa. Guru dinilai memiliki peran penting dalam membentuk karakter serta kompetensi lulusan.\n\nKegiatan ditutup dengan pemberian ucapan dan kenang-kenangan dari siswa kepada para guru. Suasana hangat dan penuh kekeluargaan mewarnai seluruh rangkaian acara.",
            ],
            [
                'title' => 'Kunjungan Industri Siswa ke Perusahaan Mitra Sekolah',
                'category' => 'berita',
                'author' => 'admin',
                'excerpt' => 'Kunjungan industri memberi pengalaman langsung kepada siswa tentang budaya kerja dan teknologi di industri.',
                'body' => "Siswa SMK Pancakarya melaksanakan kunjungan industri ke perusahaan mitra sekolah. Kegiatan ini menjadi bagian dari pembelajaran agar siswa mengenal secara langsung proses kerja di dunia industri.\n\nSelama kunjungan, siswa mendapat penjelasan mengenai struktur organisasi, standar keselamatan kerja, dan teknologi yang digunakan perusahaan. Siswa juga berkesempatan berdiskusi dengan praktisi mengenai kompetensi yang dibutuhkan di tempat kerja.\n\nSekolah berharap kunjungan industri dapat memperkuat link and match antara pendidikan vokasi dan kebutuhan industri. Pengalaman ini diharapkan memotivasi siswa untuk terus meningkatkan keterampilan.",
            ],
            [
                'title' => 'Sekolah Gelar Kegiatan Literasi Digital bagi Siswa',
                'category' => 'berita',
                'author' => 'admin',
                'excerpt' => 'Kegiatan literasi digital membekali siswa dengan kemampuan memanfaatkan teknologi secara bijak dan produktif.',
                'body' => "SMK Pancakarya menyelenggarakan kegiatan literasi digital bagi siswa. Kegiatan ini bertujuan meningkatkan pemahaman siswa mengenai pemanfaatan teknologi yang aman, bijak, dan produktif.\n\nMateri yang disampaikan meliputi etika berinternet, keamanan data pribadi, serta cara mengenali informasi yang tidak dapat dipertanggungjawabkan kebenarannya. Siswa juga diajak berdiskusi mengenai jejak digital dan dampaknya di masa depan.\n\nSekolah berkomitmen membekali siswa dengan keterampilan digital yang seimbang antara kemampuan teknis dan tanggung jawab sosial. Kegiatan serupa akan dilaksanakan secara berkelanjutan.",
            ],
            [
                'title' => 'Siswa SMK Pancakarya Raih Juara dalam Lomba Kompetensi Keahlian',
                'category' => 'prestasi',
                'author' => 'admin',
                'excerpt' => 'Prestasi membanggakan kembali diraih siswa SMK Pancakarya pada ajang lomba kompetensi keahlian tingkat kota.',
                'body' => "Siswa SMK Pancakarya kembali menorehkan prestasi pada ajang Lomba Kompetensi Keahlian tingkat kota. Perjuangan siswa dalam menyelesaikan berbagai tantangan praktik berhasil membawa pulang gelar juara.\n\nKeberhasilan ini tidak terlepas dari latihan intensif bersama guru pembimbing. Siswa dilatih menguasai kompetensi teknis sekaligus kemampuan menyelesaikan masalah secara mandiri.\n\nSekolah menyampaikan apresiasi kepada siswa dan guru pembimbing atas kerja keras yang telah dilakukan. Prestasi ini diharapkan menjadi motivasi bagi siswa lain untuk terus berprestasi.",
            ],
            [
                'title' => 'Alumni SMK Pancakarya Diterima Bekerja di Perusahaan Nasional',
                'category' => 'prestasi',
                'author' => 'staff',
                'excerpt' => 'Alumni SMK Pancakarya membuktikan kesiapan kerja dengan diterima di perusahaan berskala nasional.',
                'body' => "Alumni SMK Pancakarya kembali menunjukkan kualitas lulusannya dengan diterima bekerja di perusahaan berskala nasional. Penerimaan ini menjadi bukti relevansi kompetensi yang dipelajari di sekolah dengan kebutuhan industri.\n\nSelama menempuh pendidikan, alumni tersebut aktif mengikuti praktik kerja lapangan dan berbagai pelatihan keterampilan. Pengalaman tersebut menjadi bekal penting saat mengikuti proses rekrutmen.\n\nSekolah terus memperkuat kerja sama dengan dunia industri untuk membuka peluang bagi lulusan. Bimbingan karier juga diberikan agar siswa siap menghadapi dunia kerja setelah lulus.",
            ],
            [
                'title' => 'Tim Sekolah Meraih Penghargaan pada Ajang Inovasi Pembelajaran',
                'category' => 'prestasi',
                'author' => 'admin',
                'excerpt' => 'Inovasi pembelajaran karya guru SMK Pancakarya mendapat apresiasi pada ajang tingkat daerah.',
                'body' => "Tim guru SMK Pancakarya meraih penghargaan pada ajang inovasi pembelajaran tingkat daerah. Karya yang diusung mengangkat pemanfaatan media pembelajaran interaktif untuk meningkatkan keterlibatan siswa.\n\nInovasi tersebut lahir dari kebutuhan menyesuaikan metode pembelajaran dengan gaya belajar siswa saat ini. Media yang dikembangkan memadukan materi praktik dengan studi kasus dari dunia industri.\n\nPenghargaan ini menjadi penyemangat bagi para guru untuk terus berkreasi. Sekolah akan mendorong lebih banyak inovasi pembelajaran yang berdampak langsung pada kualitas lulusan.",
            ],
            [
                'title' => 'Siswa Raih Juara pada Kompetisi Bahasa Inggris Tingkat Kota',
                'category' => 'prestasi',
                'author' => 'staff',
                'excerpt' => 'Siswa SMK Pancakarya menunjukkan kemampuan bahasa Inggris dan meraih juara pada kompetisi tingkat kota.',
                'body' => "Siswa SMK Pancakarya meraih juara pada kompetisi bahasa Inggris tingkat kota. Kompetisi ini menguji kemampuan berbicara, menulis, serta pemahaman siswa terhadap teks berbahasa Inggris.\n\nPersiapan dilakukan melalui latihan rutin bersama guru pembimbing. Siswa juga dilatih percaya diri menyampaikan gagasan di depan juri dan peserta lain.\n\nSekolah menyampaikan apresiasi atas prestasi yang diraih. Kemampuan berbahasa Inggris dinilai penting sebagai bekal siswa memasuki dunia kerja yang semakin terhubung secara global.",
            ],
            [
                'title' => 'Pernyataan Resmi Sekolah Terkait Jadwal Penerimaan Peserta Didik Baru',
                'category' => 'press-release',
                'author' => 'admin',
                'excerpt' => 'Informasi resmi mengenai jadwal dan ketentuan penerimaan peserta didik baru tahun ajaran 2026/2027.',
                'body' => "Sehubungan dengan berlangsungnya proses penerimaan peserta didik baru, SMK Pancakarya menyampaikan informasi resmi mengenai jadwal dan ketentuan pendaftaran untuk tahun ajaran 2026/2027.\n\nSeluruh informasi yang berkaitan dengan PPDB hanya disampaikan melalui kanal resmi sekolah. Calon peserta didik dan orang tua diimbau berhati-hati terhadap informasi yang sumbernya tidak dapat diverifikasi.\n\nPanitia PPDB membuka layanan informasi pada jam kerja. Pertanyaan dapat disampaikan langsung ke sekretariat sekolah atau melalui kontak resmi yang tercantum pada situs sekolah.",
            ],
            [
                'title' => 'SMK Pancakarya Perluas Kerja Sama dengan Dunia Industri',
                'category' => 'press-release',
                'author' => 'admin',
                'excerpt' => 'Kerja sama baru mencakup praktik kerja lapangan, pelatihan guru, dan penyaluran lulusan.',
                'body' => "SMK Pancakarya menandatangani kerja sama dengan sejumlah perusahaan mitra untuk memperluas program link and match. Kerja sama mencakup penyelenggaraan praktik kerja lapangan, pelatihan guru, dan penyaluran lulusan.\n\nMelalui kemitraan ini, kurikulum sekolah dapat diselaraskan dengan kebutuhan industri. Perusahaan mitra akan terlibat dalam penyusunan materi praktik serta penilaian kompetensi siswa.\n\nSekolah berharap kerja sama tersebut memberikan manfaat bagi siswa, guru, dan industri. Kolaborasi berkelanjutan menjadi kunci dalam menyiapkan lulusan yang kompeten dan siap kerja.",
            ],
            [
                'title' => 'Strategi Pembelajaran Berbasis Proyek di Kelas Vokasi',
                'category' => 'blog-guru',
                'author' => 'staff',
                'excerpt' => 'Pembelajaran berbasis proyek membantu siswa memahami materi sekaligus melatih kemampuan bekerja dalam tim.',
                'body' => "Pembelajaran berbasis proyek menjadi salah satu pendekatan yang efektif di kelas vokasi. Siswa tidak hanya menerima materi, tetapi juga ditantang menyelesaikan masalah nyata secara berkelompok.\n\nDalam penerapannya, guru perlu merancang proyek yang relevan dengan kompetensi keahlian. Tahapan perencanaan, pelaksanaan, dan presentasi hasil menjadi bagian penting untuk melatih tanggung jawab serta komunikasi siswa.\n\nPenilaian tidak hanya berfokus pada hasil akhir, tetapi juga pada proses kerja sama dan kemandirian. Dengan begitu, siswa memperoleh pengalaman belajar yang lebih bermakna.",
            ],
            [
                'title' => 'Membangun Karakter Siswa Melalui Pembiasaan Positif',
                'category' => 'blog-guru',
                'author' => 'staff',
                'excerpt' => 'Pembiasaan positif di sekolah menjadi fondasi dalam membentuk karakter siswa yang disiplin dan bertanggung jawab.',
                'body' => "Pembentukan karakter siswa tidak dapat dilakukan secara instan. Dibutuhkan pembiasaan positif yang berlangsung konsisten, baik di kelas maupun di lingkungan sekolah.\n\nGuru dapat memulai dari hal-hal sederhana seperti datang tepat waktu, menjaga kebersihan, dan menghormati orang lain. Kebiasaan tersebut lambat laun akan menjadi nilai yang tertanam dalam diri siswa.\n\nKeteladanan guru menjadi faktor penting dalam proses ini. Ketika guru memberi contoh yang baik, siswa akan lebih mudah meniru dan menerapkan nilai-nilai tersebut dalam kehidupan sehari-hari.",
            ],
            [
                'title' => 'Memanfaatkan Teknologi dalam Pembelajaran Praktik',
                'category' => 'blog-guru',
                'author' => 'staff',
                'excerpt' => 'Teknologi dapat menjadi alat bantu yang memperkuat pemahaman siswa pada pembelajaran praktik.',
                'body' => "Teknologi bukan pengganti praktik langsung, tetapi dapat menjadi alat bantu yang memperkuat pemahaman siswa. Video demonstrasi, simulasi, dan dokumentasi hasil kerja dapat membantu siswa mengulang materi secara mandiri.\n\nGuru perlu memilih media yang sesuai dengan tujuan pembelajaran. Penggunaan teknologi juga harus tetap mengedepankan keselamatan kerja, terutama pada praktik yang menggunakan peralatan khusus.\n\nPemanfaatan teknologi secara tepat dapat meningkatkan efisiensi pembelajaran. Namun, pendampingan guru tetap menjadi bagian utama agar siswa memahami alasan di balik setiap langkah kerja.",
            ],
            [
                'title' => 'Refleksi Guru: Menyiapkan Lulusan yang Siap Kerja',
                'category' => 'blog-guru',
                'author' => 'staff',
                'excerpt' => 'Refleksi bersama guru membantu memastikan pembelajaran selaras dengan kebutuhan dunia kerja.',
                'body' => "Refleksi merupakan bagian penting dalam upaya meningkatkan kualitas pembelajaran. Melalui refleksi, guru dapat mengevaluasi metode yang digunakan dan menyesuaikannya dengan kebutuhan siswa.\n\nDalam konteks pendidikan vokasi, refleksi perlu diarahkan pada kesiapan kerja lulusan. Guru dapat memetakan kompetensi yang sudah dikuasai siswa dan mengidentifikasi keterampilan yang masih perlu diperkuat.\n\nKolaborasi antarguru dan komunikasi dengan pihak industri menjadi kunci. Dengan refleksi yang berkelanjutan, pembelajaran akan semakin relevan dengan tantangan dunia kerja.",
            ],
        ];
    }

    private function storeCover(string $slug, string $category, string $title): string
    {
        $path = 'artikel/'.$slug.'.svg';

        Storage::disk('public')->put($path, $this->coverSvg($category, $title));

        return $path;
    }

    private function coverSvg(string $category, string $title): string
    {
        $palettes = [
            'berita' => ['#0b1f3a', '#1e40af', '#fbbf24'],
            'prestasi' => ['#231400', '#b45309', '#fcd34d'],
            'press-release' => ['#052e2b', '#0f766e', '#fbbf24'],
            'blog-guru' => ['#1e1b4b', '#6d28d9', '#fbbf24'],
        ];

        [$dark, $mid, $accent] = $palettes[$category] ?? $palettes['berita'];

        $label = strtoupper(Article::categoryOptions()[$category] ?? 'Artikel');
        $badgeWidth = max(200, 70 + (mb_strlen($label) * 15));
        $badgeCenter = 80 + ($badgeWidth / 2);

        $titleMarkup = '';

        foreach ($this->wrapTitle($title) as $line => $text) {
            $y = 310 + ($line * 70);
            $titleMarkup .= '<text x="80" y="'.$y.'" font-family="Segoe UI, Arial, sans-serif" font-size="54" font-weight="700" fill="#ffffff">'.$this->escape($text).'</text>';
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="900" viewBox="0 0 1200 900" role="img" aria-label="'.$this->escape($label).'">'
            .'<defs><linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">'
            .'<stop offset="0" stop-color="'.$dark.'"/><stop offset="1" stop-color="'.$mid.'"/>'
            .'</linearGradient></defs>'
            .'<rect width="1200" height="900" fill="url(#bg)"/>'
            .'<circle cx="1060" cy="110" r="260" fill="#ffffff" opacity="0.06"/>'
            .'<circle cx="110" cy="840" r="300" fill="#ffffff" opacity="0.05"/>'
            .'<path d="M0 690 C 260 610, 520 780, 780 690 S 1100 610, 1200 650 L1200 900 L0 900 Z" fill="#ffffff" opacity="0.06"/>'
            .'<rect x="80" y="94" width="'.$badgeWidth.'" height="48" rx="24" fill="'.$accent.'"/>'
            .'<text x="'.$badgeCenter.'" y="126" font-family="Segoe UI, Arial, sans-serif" font-size="20" font-weight="700" fill="#0f172a" text-anchor="middle" letter-spacing="2">'.$this->escape($label).'</text>'
            .$titleMarkup
            .'<text x="80" y="812" font-family="Segoe UI, Arial, sans-serif" font-size="22" font-weight="600" fill="#e2e8f0" letter-spacing="4">SMK PANCAKARYA</text>'
            .'<text x="80" y="846" font-family="Segoe UI, Arial, sans-serif" font-size="16" fill="#cbd5e1" letter-spacing="3">KOTA TANGERANG</text>'
            .'</svg>';
    }

    /**
     * @return list<string>
     */
    private function wrapTitle(string $title): array
    {
        $lines = [];
        $current = '';

        foreach (preg_split('/\s+/', trim($title)) ?: [] as $word) {
            $candidate = $current === '' ? $word : $current.' '.$word;

            if ($current !== '' && mb_strlen($candidate) > 28) {
                $lines[] = $current;
                $current = $word;

                continue;
            }

            $current = $candidate;
        }

        if ($current !== '') {
            $lines[] = $current;
        }

        return array_slice($lines, 0, 4);
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }
}
