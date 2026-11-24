<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Gabungkan kategori Press Release ke kategori Artikel.
     */
    public function up(): void
    {
        DB::table('articles')
            ->where('category', 'press-release')
            ->update(['category' => 'artikel']);
    }

    /**
     * Kembalikan kategori Artikel menjadi Press Release.
     */
    public function down(): void
    {
        DB::table('articles')
            ->where('category', 'artikel')
            ->update(['category' => 'press-release']);
    }
};
