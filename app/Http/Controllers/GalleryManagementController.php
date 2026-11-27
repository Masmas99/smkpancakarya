<?php

namespace App\Http\Controllers;

use App\Http\Requests\GalleryRequest;
use App\Models\Gallery;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

abstract class GalleryManagementController extends Controller
{
    /**
     * Direktori view untuk modul galeri (mis. "pages.admin.galeri").
     */
    abstract protected function viewPrefix(): string;

    /**
     * Prefix nama rute untuk modul galeri (mis. "admin.galeri").
     */
    abstract protected function routePrefix(): string;

    /**
     * Tampilkan daftar foto galeri.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = (string) $request->query('status', '');

        if (! in_array($status, ['published', 'hidden'], true)) {
            $status = '';
        }

        $photos = Gallery::query()
            ->with('author')
            ->when($search !== '', fn ($query) => $query->where('title', 'like', '%'.$search.'%'))
            ->when($status === 'published', fn ($query) => $query->where('is_published', true))
            ->when($status === 'hidden', fn ($query) => $query->where('is_published', false))
            ->ordered()
            ->paginate(12)
            ->withQueryString();

        return view($this->viewPrefix().'.index', [
            'photos' => $photos,
            'search' => $search,
            'status' => $status,
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    /**
     * Tampilkan formulir foto baru.
     */
    public function create(): View
    {
        return view($this->viewPrefix().'.create', [
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    /**
     * Simpan foto baru pada galeri.
     */
    public function store(GalleryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $data['image'] = $request->file('image')->store('galeri', 'public');
        $data['is_published'] = $request->boolean('is_published');

        Gallery::create($data);

        return $this->redirectToIndex('Foto galeri berhasil ditambahkan.');
    }

    /**
     * Tampilkan formulir ubah foto.
     */
    public function edit(string $galeri): View
    {
        $photo = Gallery::findOrFail($galeri);

        return view($this->viewPrefix().'.edit', [
            'galeri' => $photo,
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    /**
     * Perbarui foto pada galeri.
     */
    public function update(GalleryRequest $request, string $galeri): RedirectResponse
    {
        $photo = Gallery::findOrFail($galeri);

        $data = $request->validated();
        unset($data['image']);
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('image')) {
            $this->deleteImage($photo);
            $data['image'] = $request->file('image')->store('galeri', 'public');
        }

        $photo->update($data);

        return $this->redirectToIndex('Foto galeri berhasil diperbarui.');
    }

    /**
     * Hapus foto dari galeri beserta berkasnya.
     */
    public function destroy(string $galeri): RedirectResponse
    {
        $photo = Gallery::findOrFail($galeri);

        $this->deleteImage($photo);

        $photo->delete();

        return $this->redirectToIndex('Foto galeri berhasil dihapus.');
    }

    private function deleteImage(Gallery $photo): void
    {
        if (blank($photo->image)) {
            return;
        }

        Storage::disk('public')->delete($photo->image);
    }

    private function redirectToIndex(string $message): RedirectResponse
    {
        return redirect()
            ->route($this->routePrefix().'.index')
            ->with('status', $message);
    }
}
