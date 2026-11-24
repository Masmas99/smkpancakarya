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
     * Tampilkan daftar seluruh artikel untuk dikelola.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');

        if (! in_array($status, [Article::STATUS_DRAFT, Article::STATUS_PUBLISHED], true)) {
            $status = '';
        }

        $articles = Article::query()
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

        return view($this->viewPrefix().'.index', compact('articles', 'search', 'status'));
    }

    /**
     * Tampilkan formulir artikel baru.
     */
    public function create(): View
    {
        return view($this->viewPrefix().'.create');
    }

    /**
     * Simpan artikel baru.
     */
    public function store(ArticleRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['slug'] = $request->filled('slug') ? (string) $request->input('slug') : null;
        $data['status'] = $data['status'] ?? Article::STATUS_DRAFT;
        $data['published_at'] = $data['status'] === Article::STATUS_PUBLISHED ? now() : null;
        $data['cover_image'] = $this->storeCover($request);

        Article::create($data);

        return $this->redirectToIndex('Artikel berhasil dibuat.');
    }

    /**
     * Tampilkan formulir ubah artikel.
     */
    public function edit(Article $artikel): View
    {
        return view($this->viewPrefix().'.edit', compact('artikel'));
    }

    /**
     * Perbarui artikel.
     */
    public function update(ArticleRequest $request, Article $artikel): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = $request->filled('slug') ? (string) $request->input('slug') : null;

        $data['published_at'] = $data['status'] === Article::STATUS_PUBLISHED
            ? ($artikel->published_at ?? now())
            : null;

        if ($request->hasFile('cover_image')) {
            $this->deleteCover($artikel);
            $data['cover_image'] = $this->storeCover($request);
        } else {
            unset($data['cover_image']);
        }

        $artikel->update($data);

        return $this->redirectToIndex('Artikel berhasil diperbarui.');
    }

    /**
     * Hapus artikel.
     */
    public function destroy(Article $artikel): RedirectResponse
    {
        $this->deleteCover($artikel);

        $artikel->delete();

        return $this->redirectToIndex('Artikel berhasil dihapus.');
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
