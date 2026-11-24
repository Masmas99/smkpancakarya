<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    /**
     * Buat model konkret sesuai kategori pada atribut.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function newModel(array $attributes = [])
    {
        $model = Article::modelForCategory($attributes['category'] ?? Article::DEFAULT_CATEGORY);

        return new $model($attributes);
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = rtrim(fake()->sentence(6), '.');

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => null,
            'category' => Article::DEFAULT_CATEGORY,
            'excerpt' => fake()->sentence(12),
            'body' => fake()->paragraphs(3, true),
            'cover_image' => null,
            'status' => Article::STATUS_DRAFT,
            'published_at' => null,
        ];
    }

    /**
     * Indicate that the article is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Article::STATUS_PUBLISHED,
            'published_at' => now()->subDay(),
        ]);
    }
}
