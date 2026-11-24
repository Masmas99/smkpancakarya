<?php

use App\Models\Article;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel konten beserta koneksi database-nya.
     *
     * @var array<string, string>
     */
    private const CONNECTIONS = [
        'berita' => 'articles_berita',
        'prestasi' => 'articles_prestasi',
        'artikel' => 'articles_artikel',
        'blog_guru' => 'articles_blog_guru',
    ];

    /**
     * Nilai kolom kategori untuk setiap tabel.
     *
     * @var array<string, string>
     */
    private const CATEGORIES = [
        'berita' => 'berita',
        'prestasi' => 'prestasi',
        'artikel' => 'artikel',
        'blog_guru' => 'blog-guru',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $default = config('database.default');

        if (app()->environment('testing')) {
            foreach (array_keys(self::CONNECTIONS) as $tableName) {
                Schema::connection($default)->dropIfExists($tableName);
                $this->createArticleTable($default, $tableName);
            }

            $this->copyFromLegacyTable($default, $default);

            Schema::connection($default)->dropIfExists('articles');

            return;
        }

        foreach (self::CONNECTIONS as $connection) {
            $this->ensureDatabaseExists($connection);
        }

        foreach (self::CONNECTIONS as $tableName => $connection) {
            Schema::connection($connection)->dropIfExists($tableName);
            $this->createArticleTable($connection, $tableName);
        }

        $this->copyFromLegacyTable($default);

        Schema::connection($default)->dropIfExists('articles');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $default = config('database.default');

        Schema::connection($default)->dropIfExists('articles');

        Schema::connection($default)->create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 180);
            $table->string('slug', 200)->unique();
            $table->string('category', 30)->default(Article::DEFAULT_CATEGORY);
            $table->string('excerpt', 300)->nullable();
            $table->longText('body');
            $table->string('cover_image')->nullable();
            $table->string('status', 20)->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index('category');
        });

        if (app()->environment('testing')) {
            foreach (array_keys(self::CONNECTIONS) as $tableName) {
                $this->insertLegacyRows($default, DB::connection($default)->table($tableName)->get());
            }

            foreach (array_keys(self::CONNECTIONS) as $tableName) {
                Schema::connection($default)->dropIfExists($tableName);
            }

            return;
        }

        foreach (self::CONNECTIONS as $tableName => $connection) {
            $this->insertLegacyRows($default, DB::connection($connection)->table($tableName)->get());
            Schema::connection($connection)->dropIfExists($tableName);
        }
    }

    private function createArticleTable(string $connection, string $tableName): void
    {
        $category = self::CATEGORIES[$tableName];

        Schema::connection($connection)->create($tableName, function (Blueprint $table) use ($category) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('title', 180);
            $table->string('slug', 200)->unique();
            $table->string('category', 30)->default($category);
            $table->string('excerpt', 300)->nullable();
            $table->longText('body');
            $table->string('cover_image')->nullable();
            $table->string('status', 20)->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'published_at']);
        });
    }

    private function copyFromLegacyTable(string $from, ?string $to = null): void
    {
        if (! Schema::connection($from)->hasTable('articles')) {
            return;
        }

        foreach (DB::connection($from)->table('articles')->get() as $row) {
            $tableName = $this->tableNameForCategory($row->category ?? null);
            $connection = $to ?? self::CONNECTIONS[$tableName];

            DB::connection($connection)
                ->table($tableName)
                ->insert((array) $row);
        }
    }

    /**
     * @param  Collection<int, object>  $rows
     */
    private function insertLegacyRows(string $connection, $rows): void
    {
        if ($rows->isEmpty()) {
            return;
        }

        $data = $rows
            ->map(fn (object $row): array => Arr::except((array) $row, ['id']))
            ->all();

        foreach (array_chunk($data, 100) as $chunk) {
            DB::connection($connection)->table('articles')->insertOrIgnore($chunk);
        }
    }

    private function tableNameForCategory(?string $category): string
    {
        return match ($category) {
            self::CATEGORIES['prestasi'] => 'prestasi',
            self::CATEGORIES['artikel'] => 'artikel',
            self::CATEGORIES['blog_guru'] => 'blog_guru',
            default => 'berita',
        };
    }

    private function ensureDatabaseExists(string $connection): void
    {
        $driver = config('database.connections.'.$connection.'.driver');

        if ($driver === 'sqlite') {
            $database = config('database.connections.'.$connection.'.database');

            if (is_string($database) && $database !== '' && $database !== ':memory:' && ! file_exists($database)) {
                touch($database);
            }

            return;
        }

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        $database = config('database.connections.'.$connection.'.database');

        if (blank($database)) {
            return;
        }

        $default = config('database.default');

        $exists = DB::connection($default)->selectOne(
            'SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?',
            [$database],
        );

        if ($exists) {
            return;
        }

        try {
            DB::connection($default)->statement(
                'CREATE DATABASE `'.str_replace('`', '``', $database).'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
            );
        } catch (Throwable $exception) {
            throw new RuntimeException(
                "Database konten '{$database}' belum ada dan tidak dapat dibuat otomatis. "
                .'Buat database tersebut terlebih dahulu, lalu jalankan migrate kembali.',
                0,
                $exception,
            );
        }
    }
};
