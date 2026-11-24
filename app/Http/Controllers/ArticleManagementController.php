<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

abstract class ArticleManagementController extends Controller
{
    /**
     * Direktori view untuk modul artikel (mis. "pages.admin.artikel").
     */
    abstract protected function viewPrefix(): string;

    /**
     * Prefix nama rute untuk modul artikel (mis. "admin.artikel").
     */
    abstract protected function routePrefix(): string;

    /**
     * Kategori artikel yang dikelola pada modul ini.
     */
    abstract protected function section(): string;

    /**
     * Model Eloquent yang menyimpan konten modul ini.
     *
     * @return class-string<Article>
     */
    abstract protected function modelClass(): string;

    /**
     * Tampilkan daftar artikel sesuai kategori modul.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');

        if (! in_array($status, [Article::STATUS_DRAFT, Article::STATUS_PUBLISHED], true)) {
            $status = '';
        }

        $model = $this->modelClass();

        $articles = $model::query()
            ->with('author')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', '%'.$search.'%')
                        ->orWhere('excerpt', 'like', '%'.$search.'%');
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest('updated_at')
            ->paginate(10)
            ->withQueryString();

        return view($this->viewPrefix().'.index', [
            'articles' => $articles,
            'search' => $search,
            'status' => $status,
            'sectionLabel' => $this->sectionLabel(),
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    /**
     * Tampilkan formulir artikel baru.
     */
    public function create(): View
    {
        return view($this->viewPrefix().'.create', [
            'sectionLabel' => $this->sectionLabel(),
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    /**
     * Simpan artikel baru pada kategori modul.
     */
    public function store(ArticleRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['slug'] = $request->filled('slug') ? (string) $request->input('slug') : null;
        $data['category'] = $this->section();
        $data['status'] = $data['status'] ?? Article::STATUS_DRAFT;
        $data['published_at'] = $data['status'] === Article::STATUS_PUBLISHED ? now() : null;
        $data['cover_image'] = $this->storeCover($request);

        $model = $this->modelClass();
        $model::create($data);

        return $this->redirectToIndex($this->sectionLabel().' berhasil dibuat.');
    }

    /**
     * Tampilkan formulir ubah artikel.
     */
    public function edit(string $artikel): View
    {
        $article = $this->findArticle($artikel);

        return view($this->viewPrefix().'.edit', [
            'artikel' => $article,
            'sectionLabel' => $this->sectionLabel(),
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    /**
     * Perbarui artikel pada kategori modul.
     */
    public function update(ArticleRequest $request, string $artikel): RedirectResponse
    {
        $article = $this->findArticle($artikel);

        $data = $request->validated();
        $data['slug'] = $request->filled('slug') ? (string) $request->input('slug') : null;
        $data['category'] = $this->section();

        $data['published_at'] = $data['status'] === Article::STATUS_PUBLISHED
            ? ($article->published_at ?? now())
            : null;

        if ($request->hasFile('cover_image')) {
            $this->deleteCover($article);
            $data['cover_image'] = $this->storeCover($request);
        } else {
            unset($data['cover_image']);
        }

        $article->update($data);

        return $this->redirectToIndex($this->sectionLabel().' berhasil diperbarui.');
    }

    /**
     * Hapus artikel pada kategori modul.
     */
    public function destroy(string $artikel): RedirectResponse
    {
        $article = $this->findArticle($artikel);

        $this->deleteCover($article);

        $article->delete();

        return $this->redirectToIndex($this->sectionLabel().' berhasil dihapus.');
    }

    protected function sectionLabel(): string
    {
        return Article::categoryOptions()[$this->section()] ?? 'Artikel';
    }

    private function findArticle(string $slug): Article
    {
        $model = $this->modelClass();

        return $model::where('slug', $slug)->firstOrFail();
    }

    private function redirectToIndex(string $message): RedirectResponse
    {
        return redirect()
            ->route($this->routePrefix().'.index')
            ->with('status', $message);
    }

    private function storeCover(ArticleRequest $request): ?string
    {
        if (! $request->hasFile('cover_image')) {
            return null;
        }

        return $request->file('cover_image')->store('artikel', 'public');
    }

    private function deleteCover(Article $artikel): void
    {
        if (blank($artikel->cover_image)) {
            return;
        }

        Storage::disk('public')->delete($artikel->cover_image);
    }
}
