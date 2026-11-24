<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use App\Models\Article;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $role = $this->user()?->role;

        return $role === UserRole::Admin || $role === UserRole::Staff;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['nullable', 'string', 'max:180'],
            'category' => ['nullable', Rule::in(array_keys(Article::categoryOptions()))],
            'excerpt' => ['nullable', 'string', 'max:300'],
            'body' => ['required', 'string'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'status' => ['required', Rule::in([Article::STATUS_DRAFT, Article::STATUS_PUBLISHED])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'judul',
            'slug' => 'slug',
            'category' => 'kategori',
            'excerpt' => 'ringkasan',
            'body' => 'isi artikel',
            'cover_image' => 'gambar sampul',
            'status' => 'status',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul artikel wajib diisi.',
            'title.max' => 'Judul artikel maksimal 180 karakter.',
            'category.in' => 'Kategori artikel tidak valid.',
            'excerpt.max' => 'Ringkasan maksimal 300 karakter.',
            'body.required' => 'Isi artikel wajib diisi.',
            'cover_image.image' => 'Gambar sampul harus berupa file gambar.',
            'cover_image.mimes' => 'Gambar sampul harus berformat JPG, PNG, atau WEBP.',
            'cover_image.max' => 'Ukuran gambar sampul maksimal 4 MB.',
            'status.required' => 'Status artikel wajib dipilih.',
            'status.in' => 'Status artikel tidak valid.',
        ];
    }
}
