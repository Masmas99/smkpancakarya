<?php

namespace App\Models;

use Database\Factories\ArticleFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Basis bersama untuk seluruh konten artikel.
 *
 * Setiap kategori memiliki model, tabel, dan koneksi database sendiri:
 * Berita, Prestasi, Artikel, dan BlogGuru.
 */
abstract class Article extends Model
{
    /** @use HasFactory<ArticleFactory> */
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const DEFAULT_CATEGORY = 'berita';

    public const PRESTASI_CATEGORY = 'prestasi';

    public const ARTICLE_CATEGORY = 'artikel';

    public const BLOG_CATEGORY = 'blog-guru';

    /**
     * Slug bagian publik sekaligus kategori konten.
     */
    public const SECTION = '';

    /**
     * Nama koneksi database khusus kategori.
     */
    public const CONNECTION = '';

    /**
     * Kategori artikel yang tampil pada tab beranda.
     *
     * @var array<string, string>
     */
    public const CATEGORIES = [
        'berita' => 'Berita',
        'prestasi' => 'Prestasi',
        'artikel' => 'Artikel',
    ];

    /**
     * Tab kategori pada beranda. Setiap tab mewakili satu kategori.
     *
     * @var array<string, string>
     */
    public const HOME_TABS = [
        'prestasi' => 'Prestasi',
        'berita' => 'Berita',
        'artikel' => 'Artikel',
        'blog-guru' => 'Blog Guru',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'category',
        'excerpt',
        'body',
        'cover_image',
        'status',
        'published_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    /**
     * Gunakan koneksi database khusus kategori di luar lingkungan pengujian.
     *
     * Pengujian memakai satu koneksi SQLite in-memory agar seluruh tabel
     * kategori dapat diuji dalam satu transaksi.
     */
    public function getConnectionName()
    {
        if (app()->environment('testing')) {
            return config('database.default');
        }

        return static::CONNECTION;
    }

    protected static function booted(): void
    {
        static::saving(function (Article $article) {
            $article->slug = static::uniqueSlug(
                $article->slug ?: $article->title,
                $article->exists ? $article->getKey() : null,
            );
        });
    }

    protected static function newFactory(): ArticleFactory
    {
        return ArticleFactory::new();
    }

    public function author(): BelongsTo
    {
        $user = (new User)->setConnection(config('database.default'));

        return new BelongsTo($user->newQuery(), $this, 'user_id', $user->getKeyName(), 'author');
    }

    /**
     * @param  Builder<Article>  $query
     * @return Builder<Article>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', self::STATUS_PUBLISHED)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED
            && $this->published_at !== null
            && $this->published_at->isPast();
    }

    public function statusLabel(): string
    {
        return $this->status === self::STATUS_PUBLISHED ? 'Terbit' : 'Draf';
    }

    public function categoryLabel(): string
    {
        $categories = self::categoryOptions();

        return $categories[$this->category] ?? $categories[self::DEFAULT_CATEGORY];
    }

    /**
     * Seluruh pilihan kategori artikel, termasuk Blog Guru.
     *
     * @return array<string, string>
     */
    public static function categoryOptions(): array
    {
        return self::CATEGORIES + [self::BLOG_CATEGORY => 'Blog Guru'];
    }

    /**
     * Daftar model per kategori konten.
     *
     * @return array<string, class-string<Article>>
     */
    public static function models(): array
    {
        return [
            self::DEFAULT_CATEGORY => Berita::class,
            self::PRESTASI_CATEGORY => Prestasi::class,
            self::ARTICLE_CATEGORY => Artikel::class,
            self::BLOG_CATEGORY => BlogGuru::class,
        ];
    }

    /**
     * Model yang menangani kategori tertentu.
     *
     * @return class-string<Article>
     */
    public static function modelForCategory(string $category): string
    {
        return self::models()[$category] ?? Berita::class;
    }

    /**
     * Bagian URL publik sesuai kategori artikel.
     */
    public function section(): string
    {
        return static::SECTION;
    }

    public function sectionLabel(): string
    {
        return match ($this->section()) {
            'prestasi' => __('site.nav.achievements'),
            'artikel' => __('site.nav.articles'),
            'blog-guru' => __('site.nav.blog_guru'),
            default => __('site.nav.news'),
        };
    }

    public function url(): string
    {
        return route($this->section().'.show', $this);
    }

    public function sectionUrl(): string
    {
        return route($this->section().'.index');
    }

    public function excerptText(int $limit = 160): string
    {
        if (filled($this->excerpt)) {
            return $this->excerpt;
        }

        return Str::limit(trim(strip_tags($this->body)), $limit);
    }

    public function coverUrl(): ?string
    {
        if (blank($this->cover_image)) {
            return null;
        }

        return Storage::disk('public')->url($this->cover_image);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public static function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: Str::random(8);
        $slug = $base;
        $suffix = 2;

        while (
            static::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn (Builder $query) => $query->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $slug = $base.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    /**
     * @return array<string, string>
     */
    public static function statusOptions(): array
    {
        return [
            self::STATUS_DRAFT => 'Draf',
            self::STATUS_PUBLISHED => 'Terbit',
        ];
    }
}
