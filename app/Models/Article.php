<?php

namespace App\Models;

use Database\Factories\ArticleFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Article extends Model
{
    /** @use HasFactory<ArticleFactory> */
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const DEFAULT_CATEGORY = 'berita';

    public const BLOG_CATEGORY = 'blog-guru';

    /**
     * Kategori berita yang tampil pada tab beranda.
     *
     * @var array<string, string>
     */
    public const CATEGORIES = [
        'berita' => 'Berita',
        'prestasi' => 'Prestasi',
        'press-release' => 'Press Release',
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

    protected static function booted(): void
    {
        static::saving(function (Article $article) {
            $article->slug = static::uniqueSlug(
                $article->slug ?: $article->title,
                $article->exists ? $article->getKey() : null,
            );
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
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
