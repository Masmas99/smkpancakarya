<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Artikel;
use App\Models\Berita;
use App\Models\BlogGuru;
use App\Models\Prestasi;
use App\Models\User;
use Carbon\Carbon;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleXMLElement;
use Throwable;

/**
 * Impor konten dari situs lama SMK Pancakarya.
 *
 * Sumber:
 * - Prestasi   : https://smkpancakarya.sch.id/prestasi/
 * - Berita     : https://smkpancakarya.sch.id/pengumuman/
 * - Artikel    : https://smkpancakarya.sch.id/category/berita/
 * - Blog Guru  : https://smkpancakarya.sch.id/blog/
 */
class ImportLegacyArticles extends Command
{
    /**
     * Nama dan tanda tangan perintah.
     *
     * @var string
     */
    protected $signature = 'articles:import-legacy
        {--dry-run : Tampilkan data yang akan diimpor tanpa menyimpan}
        {--no-images : Jangan unduh gambar sampul}
        {--category=* : Batasi ke kategori tertentu (prestasi, berita, artikel, blog-guru)}';

    /**
     * Deskripsi perintah.
     *
     * @var string
     */
    protected $description = 'Impor artikel dari situs lama SMK Pancakarya ke database lokal';

    private const SOURCE_BASE = 'https://smkpancakarya.sch.id';

    private const SOURCE_TIMEZONE = 'Asia/Jakarta';

    private const USER_AGENT = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0 Safari/537.36';

    /**
     * Statistik per kategori.
     *
     * @var array<string, array{label: string, total: int, created: int, updated: int, failed: int, images: int}>
     */
    private array $stats = [];

    /**
     * Jalankan perintah.
     */
    public function handle(): int
    {
        $admin = User::where('email', 'admin@smkpancakarya.test')->first();
        $staff = User::where('email', 'staff@smkpancakarya.test')->first();

        if (! $admin) {
            $this->error('Akun admin@smkpancakarya.test tidak ditemukan. Jalankan DatabaseSeeder terlebih dahulu.');

            return self::FAILURE;
        }

        $authors = [
            'admin' => $admin,
            'staff' => $staff ?? $admin,
        ];

        $sources = $this->sources();
        $requested = $this->requestedCategories();

        if ($requested === null) {
            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $downloadImages = ! $dryRun && ! (bool) $this->option('no-images');

        foreach ($requested as $key) {
            $source = $sources[$key];
            $source['user_id'] = $authors[$source['author']]->id;

            $this->stats[$key] = [
                'label' => $source['label'],
                'total' => 0,
                'created' => 0,
                'updated' => 0,
                'failed' => 0,
                'images' => 0,
            ];

            $this->newLine();
            $this->info('== '.$source['label'].' ==');

            $items = isset($source['rest'])
                ? $this->fetchRest($source['rest'])
                : $this->fetchFeed($source['feed']);

            $this->stats[$key]['total'] = count($items);

            if ($items === []) {
                $this->warn('Tidak ada item yang berhasil dibaca dari '.($source['rest'] ?? $source['feed']));

                continue;
            }

            $this->line('Sumber: '.($source['rest'] ?? $source['feed']));
            $this->line('Jumlah item: '.count($items));

            $bar = $dryRun ? null : $this->output->createProgressBar(count($items));

            if ($bar) {
                $bar->start();
            }

            foreach ($items as $item) {
                try {
                    $data = $this->buildArticle($item, $source, $downloadImages);

                    if ($data === null) {
                        $this->stats[$key]['failed']++;
                        $this->warn('  Dilewati: '.$item['title'].' ('.$item['link'].')');

                        if ($bar) {
                            $bar->advance();
                        }

                        continue;
                    }

                    if ($dryRun) {
                        $this->line(sprintf(
                            '  - %s | %s | %d karakter | gambar: %s',
                            $data['title'],
                            $data['attributes']['published_at']->toDateString(),
                            mb_strlen($data['attributes']['body']),
                            $data['cover_url'] ?: '-',
                        ));

                        if ($bar) {
                            $bar->advance();
                        }

                        continue;
                    }

                    $model = $source['model'];
                    $existing = $model::query()->where('slug', $data['slug'])->first();
                    $model::updateOrCreate(['slug' => $data['slug']], $data['attributes']);

                    if ($existing) {
                        $this->stats[$key]['updated']++;
                    } else {
                        $this->stats[$key]['created']++;
                    }

                    if ($data['downloaded_cover']) {
                        $this->stats[$key]['images']++;
                    }
                } catch (Throwable $exception) {
                    $this->stats[$key]['failed']++;
                    $this->warn('  Gagal: '.$item['title'].' - '.$exception->getMessage());
                }

                if ($bar) {
                    $bar->advance();
                }
            }

            if ($bar) {
                $bar->finish();
                $this->newLine();
            }
        }

        $this->printSummary();

        $failed = collect($this->stats)->sum('failed');

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * Daftar sumber per kategori lokal.
     *
     * @return array<string, array{label: string, model: class-string<Article>, category: string, author: string, feed?: string, rest?: string}>
     */
    private function sources(): array
    {
        return [
            'prestasi' => [
                'label' => 'Prestasi',
                'model' => Prestasi::class,
                'category' => Article::PRESTASI_CATEGORY,
                'author' => 'admin',
                'feed' => self::SOURCE_BASE.'/prestasi/feed/',
            ],
            'berita' => [
                'label' => 'Berita',
                'model' => Berita::class,
                'category' => Article::DEFAULT_CATEGORY,
                'author' => 'admin',
                'feed' => self::SOURCE_BASE.'/pengumuman/feed/',
            ],
            'artikel' => [
                'label' => 'Artikel',
                'model' => Artikel::class,
                'category' => Article::ARTICLE_CATEGORY,
                'author' => 'admin',
                'rest' => self::SOURCE_BASE.'/wp-json/wp/v2/posts?categories=28&per_page=100&_embed=1',
            ],
            'blog-guru' => [
                'label' => 'Blog Guru',
                'model' => BlogGuru::class,
                'category' => Article::BLOG_CATEGORY,
                'author' => 'staff',
                'feed' => self::SOURCE_BASE.'/blog/feed/',
            ],
        ];
    }

    /**
     * Kategori yang diminta melalui opsi --category.
     *
     * @return list<string>|null
     */
    private function requestedCategories(): ?array
    {
        $sources = $this->sources();
        $requested = $this->option('category');

        if (! is_array($requested)) {
            $requested = [$requested];
        }

        $requested = array_values(array_filter(array_map(
            static fn (mixed $value): string => trim((string) $value),
            $requested,
        )));

        if ($requested === []) {
            return array_keys($sources);
        }

        $expanded = [];

        foreach ($requested as $value) {
            foreach (preg_split('/[,\s]+/', $value) ?: [] as $category) {
                if ($category !== '') {
                    $expanded[] = $category;
                }
            }
        }

        $invalid = array_diff($expanded, array_keys($sources));

        if ($invalid !== []) {
            $this->error('Kategori tidak dikenal: '.implode(', ', $invalid).'. Pilihan: '.implode(', ', array_keys($sources)));

            return null;
        }

        return array_values(array_unique($expanded));
    }

    /**
     * Baca seluruh item dari feed WordPress, termasuk halaman berikutnya.
     *
     * @return list<array{title: string, link: string, slug: string, published_at: string, creator: string, description: string}>
     */
    private function fetchFeed(string $feedUrl): array
    {
        $items = [];
        $seen = [];

        for ($page = 1; $page <= 20; $page++) {
            $url = $feedUrl.($page > 1 ? (str_contains($feedUrl, '?') ? '&' : '?').'paged='.$page : '');
            $body = $this->request($url);

            if ($body === null) {
                break;
            }

            $xml = @simplexml_load_string($body, SimpleXMLElement::class, LIBXML_NOCDATA);

            if (! $xml instanceof SimpleXMLElement || ! isset($xml->channel->item)) {
                break;
            }

            $pageItems = [];

            foreach ($xml->channel->item as $item) {
                $link = trim((string) $item->link);

                if ($link === '' || isset($seen[$link])) {
                    continue;
                }

                $seen[$link] = true;
                $path = parse_url($link, PHP_URL_PATH) ?: '';
                $slug = urldecode(basename(rtrim($path, '/')));
                $creator = $item->children('dc', true);

                $pageItems[] = [
                    'title' => $this->cleanText((string) $item->title),
                    'link' => $link,
                    'slug' => $slug,
                    'published_at' => (string) $item->pubDate,
                    'creator' => $this->cleanText((string) ($creator->creator ?? '')),
                    'description' => (string) $item->description,
                ];
            }

            if ($pageItems === []) {
                break;
            }

            $items = array_merge($items, $pageItems);

            if (count($pageItems) < 10) {
                break;
            }
        }

        return $items;
    }

    /**
     * Baca seluruh pos dari REST API WordPress.
     *
     * @return list<array{title: string, link: string, slug: string, published_at: string, creator: string, description: string, content_html: string, cover_url: ?string}>
     */
    private function fetchRest(string $restUrl): array
    {
        $items = [];
        $seen = [];

        for ($page = 1; $page <= 10; $page++) {
            $url = $restUrl.(str_contains($restUrl, '?') ? '&' : '?').'page='.$page;
            $body = $this->request($url);

            if ($body === null) {
                break;
            }

            $posts = json_decode($body, true);

            if (! is_array($posts) || $posts === []) {
                break;
            }

            foreach ($posts as $post) {
                if (! is_array($post)) {
                    continue;
                }

                $link = (string) ($post['link'] ?? '');
                $slug = (string) ($post['slug'] ?? '');

                if ($link === '' || isset($seen[$link])) {
                    continue;
                }

                $seen[$link] = true;
                $featured = $post['_embedded']['wp:featuredmedia'][0]['source_url'] ?? null;

                $items[] = [
                    'title' => $this->cleanText(strip_tags((string) ($post['title']['rendered'] ?? ''))),
                    'link' => $link,
                    'slug' => $slug,
                    'published_at' => (string) ($post['date'] ?? ''),
                    'creator' => '',
                    'description' => (string) ($post['excerpt']['rendered'] ?? ''),
                    'content_html' => (string) ($post['content']['rendered'] ?? ''),
                    'cover_url' => is_string($featured) && $featured !== '' ? $featured : null,
                ];
            }

            if (count($posts) < 100) {
                break;
            }
        }

        return $items;
    }

    /**
     * Bangun data artikel dari satu item sumber.
     *
     * @param  array<string, mixed>  $item
     * @param  array{label: string, model: class-string<Article>, category: string, author: string, user_id?: int, feed?: string, rest?: string}  $source
     * @return array{slug: string, title: string, cover_url: ?string, downloaded_cover: bool, attributes: array<string, mixed>}|null
     */
    private function buildArticle(array $item, array $source, bool $downloadImages): ?array
    {
        $title = $this->cleanText((string) ($item['title'] ?? ''));
        $body = '';
        $inlineImages = [];
        $embedUrls = [];
        $coverUrl = null;

        if (array_key_exists('content_html', $item)) {
            $extracted = $this->extractHtmlContent((string) $item['content_html']);
            $body = $extracted['body'];
            $inlineImages = $extracted['inline_images'];
            $embedUrls = $extracted['embed_urls'];
            $coverUrl = $this->firstNonEmpty([
                $item['cover_url'] ?? null,
                $extracted['featured_image'],
                $inlineImages[0] ?? null,
            ]);
        } else {
            $detail = $this->fetchDetail((string) $item['link']);

            if ($detail === null) {
                return null;
            }

            $title = $detail['title'] !== '' ? $detail['title'] : $title;
            $body = $detail['body'];
            $inlineImages = $detail['inline_images'];
            $embedUrls = $detail['embed_urls'];
            $coverUrl = $detail['cover_url'];
        }

        $title = $this->cleanText($this->normalizeLookalikes($title));

        if ($title === '') {
            return null;
        }

        if ($this->isEffectivelyEmpty($body) && $inlineImages !== []) {
            $coverUrl = $inlineImages[0];
        }

        $hasImage = $inlineImages !== [] || $coverUrl !== null;

        if ($this->isEffectivelyEmpty($body)) {
            $body = $hasImage
                ? 'Informasi lengkap mengenai '.$title.' dapat dilihat pada gambar yang disertakan.'
                : $title;
        }

        if ($embedUrls !== []) {
            $mediaLines = array_map(
                static fn (string $url): string => 'Media: '.$url,
                array_values(array_unique($embedUrls)),
            );

            $body = trim($body."\n\n".implode("\n", $mediaLines));
        }

        $body = $this->normalizeBody($body);

        if ($this->isEffectivelyEmpty($body)) {
            $body = $title;
        }

        $excerptSource = $this->htmlToText((string) ($item['description'] ?? ''));

        if ($excerptSource === '') {
            $excerptSource = $body;
        }

        $excerpt = Str::limit($this->cleanText($excerptSource), 280);
        $slug = $this->sourceSlug($item, $title);
        $downloadedCover = false;
        $coverPath = null;

        if ($downloadImages && is_string($coverUrl) && $coverUrl !== '') {
            $coverPath = $this->downloadCover($coverUrl, $source['category'], $slug);
            $downloadedCover = $coverPath !== null;
        }

        $attributes = [
            'user_id' => $source['user_id'],
            'title' => Str::limit($title, 180, ''),
            'category' => $source['category'],
            'excerpt' => $excerpt,
            'body' => $body,
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => $this->parseDate((string) ($item['published_at'] ?? '')),
        ];

        if ($coverPath !== null) {
            $attributes['cover_image'] = $coverPath;
        }

        return [
            'slug' => $slug,
            'title' => $title,
            'cover_url' => $coverUrl,
            'downloaded_cover' => $downloadedCover,
            'attributes' => $attributes,
        ];
    }

    /**
     * Ambil judul, isi, dan gambar dari halaman detail WordPress.
     *
     * @return array{title: string, body: string, cover_url: ?string, inline_images: list<string>, embed_urls: list<string>}|null
     */
    private function fetchDetail(string $url): ?array
    {
        $html = $this->request($url);

        if ($html === null) {
            return null;
        }

        $dom = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $loaded = $dom->loadHTML(
            '<?xml encoding="UTF-8">'.$html,
            LIBXML_NOWARNING | LIBXML_NOERROR,
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (! $loaded) {
            return null;
        }

        $xpath = new DOMXPath($dom);
        $title = $this->cleanText($xpath->query('//h1')->item(0)?->textContent);
        $ogImage = null;

        foreach ($xpath->query('//meta[@property="og:image"]') as $meta) {
            if ($meta instanceof DOMElement) {
                $ogImage = trim($meta->getAttribute('content'));

                break;
            }
        }

        $contentNodes = [];

        foreach ($xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " post-content ")]') as $node) {
            if ($node instanceof DOMElement && $this->hasAncestorClass($node, 'single')) {
                $contentNodes[] = $node;
            }
        }

        if ($contentNodes === []) {
            foreach ($xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " entry-content ")] | //*[contains(concat(" ", normalize-space(@class), " "), " post-entry ")]') as $node) {
                if ($node instanceof DOMElement) {
                    $contentNodes[] = $node;
                }
            }
        }

        $htmlParts = [];

        foreach ($contentNodes as $node) {
            $htmlParts[] = $dom->saveHTML($node);
        }

        $extracted = $this->extractHtmlContent(implode("\n", $htmlParts));

        return [
            'title' => $title,
            'body' => $extracted['body'],
            'cover_url' => $this->firstNonEmpty([
                $ogImage,
                $extracted['featured_image'],
                $extracted['inline_images'][0] ?? null,
            ]),
            'inline_images' => $extracted['inline_images'],
            'embed_urls' => $extracted['embed_urls'],
        ];
    }

    /**
     * Ubah HTML konten menjadi teks paragraf dan kumpulkan media.
     *
     * @return array{body: string, featured_image: ?string, inline_images: list<string>, embed_urls: list<string>}
     */
    private function extractHtmlContent(string $html): array
    {
        $result = [
            'body' => '',
            'featured_image' => null,
            'inline_images' => [],
            'embed_urls' => [],
        ];

        if (trim($html) === '') {
            return $result;
        }

        $dom = new DOMDocument;
        $previous = libxml_use_internal_errors(true);
        $dom->loadHTML(
            '<?xml encoding="UTF-8"><div id="legacy-content-wrapper">'.$html.'</div>',
            LIBXML_NOWARNING | LIBXML_NOERROR,
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $xpath = new DOMXPath($dom);
        $wrapper = $xpath->query('//*[@id="legacy-content-wrapper"]')->item(0);

        if (! $wrapper instanceof DOMElement) {
            return $result;
        }

        $featuredNodes = $this->nodesToArray(
            $xpath->query('.//*[contains(concat(" ", normalize-space(@class), " "), " post-img ")]', $wrapper),
        );

        foreach ($featuredNodes as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            if ($result['featured_image'] === null) {
                $image = $xpath->query('.//img', $node)->item(0);

                if ($image instanceof DOMElement) {
                    $result['featured_image'] = $this->imageSrc($image);
                }
            }

            if ($node->parentNode) {
                $node->parentNode->removeChild($node);
            }
        }

        foreach ($xpath->query('.//img', $wrapper) as $image) {
            if (! $image instanceof DOMElement) {
                continue;
            }

            $src = $this->imageSrc($image);

            if ($src !== '') {
                $result['inline_images'][] = $src;
            }
        }

        $result['inline_images'] = array_values(array_unique($result['inline_images']));

        $embedNodes = $this->nodesToArray($xpath->query(
            './/*[contains(concat(" ", normalize-space(@class), " "), " wp-block-embed ") or contains(concat(" ", normalize-space(@class), " "), " tiktok-embed ") or contains(concat(" ", normalize-space(@class), " "), " instagram-media ")]',
            $wrapper,
        ));

        $toRemove = [];

        foreach ($embedNodes as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            $target = $node;
            $ancestor = $node->parentNode;

            while ($ancestor instanceof DOMElement) {
                if ($this->hasClass($ancestor, 'wp-block-embed')) {
                    $target = $ancestor;

                    break;
                }

                $ancestor = $ancestor->parentNode;
            }

            $url = $this->embedUrl($target);

            if ($url !== null) {
                $result['embed_urls'][] = $url;
            }

            $toRemove[] = $target;
        }

        $result['embed_urls'] = array_values(array_unique($result['embed_urls']));

        foreach ($toRemove as $node) {
            if ($node->parentNode) {
                $node->parentNode->removeChild($node);
            }
        }

        foreach ($this->nodesToArray($xpath->query('.//iframe | .//script | .//style | .//noscript', $wrapper)) as $node) {
            if ($node->parentNode) {
                $node->parentNode->removeChild($node);
            }
        }

        $result['body'] = $this->htmlToText($dom->saveHTML($wrapper));

        return $result;
    }

    /**
     * Cari URL media pada elemen embed.
     */
    private function embedUrl(DOMElement $element): ?string
    {
        $candidates = [];

        foreach (['cite', 'data-instgrm-permalink', 'src'] as $attribute) {
            if ($element->hasAttribute($attribute)) {
                $candidates[] = trim($element->getAttribute($attribute));
            }
        }

        $xpath = new DOMXPath($element->ownerDocument);

        foreach ($xpath->query('.//*[@cite]', $element) as $node) {
            if ($node instanceof DOMElement) {
                $candidates[] = trim($node->getAttribute('cite'));
            }
        }

        foreach ($xpath->query('.//*[@data-instgrm-permalink]', $element) as $node) {
            if ($node instanceof DOMElement) {
                $candidates[] = trim($node->getAttribute('data-instgrm-permalink'));
            }
        }

        foreach ($xpath->query('.//iframe[@src]', $element) as $node) {
            if ($node instanceof DOMElement) {
                $candidates[] = trim($node->getAttribute('src'));
            }
        }

        foreach ($candidates as $candidate) {
            if (str_starts_with($candidate, 'http://') || str_starts_with($candidate, 'https://')) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Ubah HTML menjadi teks dengan pemisah paragraf.
     */
    private function htmlToText(string $html): string
    {
        if (trim($html) === '') {
            return '';
        }

        $html = preg_replace('/<!--.*?-->/s', ' ', $html) ?? $html;
        $html = preg_replace('/<(br|hr)\b[^>]*>/i', "\n", $html) ?? $html;
        $html = preg_replace('/<li\b[^>]*>/i', "\n• ", $html) ?? $html;
        $html = preg_replace('/<\/(p|div|h[1-6]|li|blockquote|figure|section|article|ul|ol|table|thead|tbody|tr|td|th|pre)>/i', "\n\n", $html) ?? $html;
        $html = preg_replace('/<(p|div|h[1-6]|blockquote|figure|section|article|ul|ol|table|thead|tbody|tr|td|th|pre)\b[^>]*>/i', "\n\n", $html) ?? $html;

        $text = strip_tags($html);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace(["\xC2\xA0", "\r\n", "\r"], [' ', "\n", "\n"], $text);
        $text = preg_replace('/[ \t]+/u', ' ', $text) ?? $text;
        $text = preg_replace('/ *\n */u', "\n", $text) ?? $text;
        $text = preg_replace('/\n{3,}/u', "\n\n", $text) ?? $text;

        return trim($text);
    }

    /**
     * Rapikan pemisah paragraf pada isi artikel.
     */
    private function normalizeBody(string $body): string
    {
        $body = str_replace(["\r\n", "\r"], "\n", $body);
        $body = preg_replace('/[ \t]+/u', ' ', $body) ?? $body;
        $body = preg_replace('/ *\n */u', "\n", $body) ?? $body;
        $body = preg_replace('/\n{3,}/u', "\n\n", $body) ?? $body;

        return trim($body);
    }

    /**
     * Periksa apakah teks hanya berisi spasi, penanda daftar, atau karakter tak terlihat.
     */
    private function isEffectivelyEmpty(?string $text): bool
    {
        $text = html_entity_decode((string) $text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/[\s\x{00A0}\x{200B}•·]+/u', '', $text) ?? $text;

        return $text === '';
    }

    /**
     * Bersihkan teks judul atau ringkasan.
     */
    private function cleanText(?string $value): string
    {
        $value = html_entity_decode((string) $value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = str_replace("\xC2\xA0", ' ', $value);

        return trim(preg_replace('/\s+/u', ' ', $value) ?? $value);
    }

    /**
     * Tentukan slug yang stabil berdasarkan URL sumber.
     *
     * @param  array<string, mixed>  $item
     */
    private function sourceSlug(array $item, string $fallbackTitle): string
    {
        $slug = (string) ($item['slug'] ?? '');

        if ($slug === '') {
            $path = parse_url((string) ($item['link'] ?? ''), PHP_URL_PATH) ?: '';
            $slug = urldecode(basename(rtrim($path, '/')));
        }

        $slug = $this->normalizeLookalikes($slug);
        $slug = Str::slug($slug);

        if ($slug === '') {
            $slug = Str::slug($this->normalizeLookalikes($fallbackTitle));
        }

        return $slug !== '' ? $slug : 'artikel';
    }

    /**
     * Ubah huruf Cyrillic pengganti menjadi huruf Latin.
     */
    private function normalizeLookalikes(string $value): string
    {
        return str_replace(
            ['і', 'І', 'ї', 'Ї', 'є', 'Є', 'ґ', 'Ґ', 'а', 'А', 'е', 'Е', 'о', 'О', 'р', 'Р', 'с', 'С', 'у', 'У', 'х', 'Х'],
            ['i', 'I', 'i', 'I', 'e', 'E', 'g', 'G', 'a', 'A', 'e', 'E', 'o', 'O', 'p', 'P', 'c', 'C', 'y', 'Y', 'x', 'X'],
            $value,
        );
    }

    /**
     * Tentukan tanggal terbit dari format RSS atau ISO.
     */
    private function parseDate(string $value): Carbon
    {
        if ($value === '') {
            return now();
        }

        try {
            if (preg_match('/^\d{4}-\d{2}-\d{2}/', $value) === 1) {
                $date = substr($value, 0, 10);
            } else {
                $date = Carbon::parse($value)->setTimezone(self::SOURCE_TIMEZONE)->toDateString();
            }

            return Carbon::parse($date.' 00:00:00', 'UTC');
        } catch (Throwable) {
            return now();
        }
    }

    /**
     * Ambil URL gambar dari elemen img.
     */
    private function imageSrc(DOMElement $image): string
    {
        $src = trim($image->getAttribute('src'));

        if ($src === '' || str_starts_with($src, 'data:')) {
            $src = trim($image->getAttribute('data-src'));
        }

        return str_starts_with($src, 'data:') ? '' : $src;
    }

    /**
     * Unduh gambar sampul dan simpan ke disk publik.
     */
    private function downloadCover(string $url, string $category, string $slug): ?string
    {
        foreach ($this->imageCandidates($url) as $candidate) {
            try {
                $response = Http::withHeaders([
                    'User-Agent' => self::USER_AGENT,
                    'Accept' => 'image/avif,image/webp,image/apng,image/*,*/*;q=0.8',
                ])->timeout(60)->retry(2, 700, throw: false)->get($candidate);

                if (! $response->successful()) {
                    continue;
                }

                $body = $response->body();

                if (strlen($body) < 64) {
                    continue;
                }

                $extension = $this->imageExtension($body, (string) $response->header('Content-Type'), $candidate);

                if ($extension === null) {
                    continue;
                }

                $filename = trim(Str::limit(Str::slug($this->normalizeLookalikes($slug)), 80, ''), '-');
                $filename = $filename !== '' ? $filename : 'artikel';
                $path = 'artikel/legacy/'.$category.'/'.$filename.'-'.substr(sha1($candidate), 0, 8).'.'.$extension;

                Storage::disk('public')->put($path, $body);

                return $path;
            } catch (Throwable) {
                // Coba kandidat URL berikutnya.
            }
        }

        $this->warn('  Gambar gagal diunduh: '.$url);

        return null;
    }

    /**
     * Kandidat URL gambar (langsung, tanpa query, atau melalui Jetpack CDN).
     *
     * @return list<string>
     */
    private function imageCandidates(string $url): array
    {
        $url = trim(html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        if ($url === '') {
            return [];
        }

        if (str_starts_with($url, '//')) {
            $url = 'https:'.$url;
        }

        if (str_starts_with($url, '/')) {
            $url = self::SOURCE_BASE.$url;
        }

        $candidates = [];
        $path = parse_url($url, PHP_URL_PATH) ?: '';

        if (preg_match('#^/i\d\.wp\.com/(.+)$#', $path, $matches) === 1) {
            $candidates[] = 'https://'.$matches[1];
        }

        if (str_contains($url, 'smkpancakarya.sch.id/wp-content/')) {
            $candidates[] = preg_replace('/\?.*$/', '', $url) ?? $url;
        }

        $candidates[] = $url;

        return array_values(array_unique(array_filter($candidates)));
    }

    /**
     * Tentukan ekstensi gambar dari isi respons.
     */
    private function imageExtension(string $body, string $mime, string $url): ?string
    {
        $info = @getimagesizefromstring($body);

        if (is_array($info)) {
            return match ($info[2]) {
                IMAGETYPE_JPEG => 'jpg',
                IMAGETYPE_PNG => 'png',
                IMAGETYPE_GIF => 'gif',
                IMAGETYPE_WEBP => 'webp',
                IMAGETYPE_BMP => 'bmp',
                default => null,
            };
        }

        $mime = strtolower(trim(explode(';', $mime)[0]));

        $byMime = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            'image/bmp' => 'bmp',
        ];

        if (isset($byMime[$mime])) {
            return $byMime[$mime];
        }

        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?: '', PATHINFO_EXTENSION));

        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'], true)) {
            return $extension === 'jpeg' ? 'jpg' : $extension;
        }

        return null;
    }

    /**
     * Ambil nilai pertama yang tidak kosong.
     *
     * @param  array<int, mixed>  $values
     */
    private function firstNonEmpty(array $values): ?string
    {
        foreach ($values as $value) {
            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        return null;
    }

    /**
     * Ubah DOMNodeList menjadi array.
     *
     * @return list<DOMNode>
     */
    private function nodesToArray(DOMNodeList $nodes): array
    {
        $result = [];

        foreach ($nodes as $node) {
            $result[] = $node;
        }

        return $result;
    }

    /**
     * Periksa apakah elemen memiliki kelas tertentu.
     */
    private function hasClass(DOMElement $element, string $class): bool
    {
        $classes = preg_split('/\s+/', trim($element->getAttribute('class'))) ?: [];

        return in_array($class, $classes, true);
    }

    /**
     * Periksa apakah salah satu leluhur memiliki kelas tertentu.
     */
    private function hasAncestorClass(DOMNode $node, string $class): bool
    {
        $current = $node->parentNode;

        while ($current instanceof DOMElement) {
            if ($this->hasClass($current, $class)) {
                return true;
            }

            $current = $current->parentNode;
        }

        return false;
    }

    /**
     * Ambil konten dari URL dengan retry.
     */
    private function request(string $url): ?string
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => self::USER_AGENT,
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            ])->timeout(45)->retry(2, 700, throw: false)->get($url);

            return $response->successful() ? $response->body() : null;
        } catch (Throwable) {
            return null;
        }
    }

    /**
     * Tampilkan ringkasan hasil impor.
     */
    private function printSummary(): void
    {
        $this->newLine();

        $rows = [];

        foreach ($this->stats as $stat) {
            $rows[] = [
                $stat['label'],
                $stat['total'],
                $stat['created'],
                $stat['updated'],
                $stat['failed'],
                $stat['images'],
            ];
        }

        $this->table(
            ['Kategori', 'Total', 'Baru', 'Diperbarui', 'Gagal', 'Gambar'],
            $rows,
        );
    }
}
