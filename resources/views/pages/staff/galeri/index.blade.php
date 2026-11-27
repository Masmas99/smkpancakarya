<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Kelola Galeri
            </h2>

            <a
                href="{{ route($routePrefix.'.create') }}"
                class="inline-flex items-center gap-2 rounded-md bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2"
            >
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Foto
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            <form method="GET" action="{{ route($routePrefix.'.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <input
                    name="q"
                    type="search"
                    value="{{ $search }}"
                    placeholder="Cari judul foto..."
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:max-w-sm"
                >

                <select
                    name="status"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:w-44"
                >
                    <option value="">Semua status</option>
                    <option value="published" @selected($status === 'published')>Tampil</option>
                    <option value="hidden" @selected($status === 'hidden')>Disembunyikan</option>
                </select>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                >
                    Terapkan
                </button>

                @if ($search !== '' || $status !== '')
                    <a href="{{ route($routePrefix.'.index') }}" class="text-sm font-medium text-gray-600 transition hover:text-gray-900">
                        Reset
                    </a>
                @endif
            </form>

            @if ($photos->isEmpty())
                <div class="rounded-lg border border-dashed border-gray-300 bg-white px-6 py-14 text-center text-sm text-gray-500">
                    Belum ada foto galeri. Klik <span class="font-semibold text-gray-700">Tambah Foto</span> untuk mengunggah foto pertama.
                </div>
            @else
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($photos as $photo)
                        <article class="overflow-hidden rounded-lg bg-white shadow-sm">
                            <div class="relative aspect-[4/3] bg-gray-100">
                                <img
                                    src="{{ $photo->imageUrl() }}"
                                    alt="{{ $photo->title ?: 'Foto galeri' }}"
                                    class="h-full w-full object-cover"
                                    loading="lazy"
                                    decoding="async"
                                >

                                @if ($photo->is_published)
                                    <span class="absolute left-3 top-3 inline-flex rounded-full bg-emerald-600 px-2.5 py-0.5 text-xs font-semibold text-white shadow-sm">
                                        Tampil
                                    </span>
                                @else
                                    <span class="absolute left-3 top-3 inline-flex rounded-full bg-amber-500 px-2.5 py-0.5 text-xs font-semibold text-white shadow-sm">
                                        Disembunyikan
                                    </span>
                                @endif
                            </div>

                            <div class="space-y-1 p-4">
                                <p class="line-clamp-2 text-sm font-semibold text-gray-900">
                                    {{ $photo->title ?: 'Tanpa judul' }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $photo->author?->name ?? '-' }}
                                </p>

                                <div class="flex items-center justify-end gap-3 pt-3 text-sm">
                                    <a
                                        href="{{ route($routePrefix.'.edit', $photo) }}"
                                        class="font-medium text-blue-700 transition hover:text-blue-800"
                                    >
                                        Ubah
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route($routePrefix.'.destroy', $photo) }}"
                                        onsubmit="return confirm('Hapus foto ini? Tindakan ini tidak dapat dibatalkan.');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="font-medium text-red-600 transition hover:text-red-700">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

            @if ($photos->hasPages())
                <nav class="flex items-center justify-between gap-3" aria-label="Navigasi halaman galeri">
                    @if ($photos->onFirstPage())
                        <span class="inline-flex items-center rounded-md border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-400" aria-disabled="true">
                            &larr; Sebelumnya
                        </span>
                    @else
                        <a
                            href="{{ $photos->previousPageUrl() }}"
                            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                        >
                            &larr; Sebelumnya
                        </a>
                    @endif

                    <span class="text-sm font-medium text-gray-500">
                        Halaman {{ $photos->currentPage() }}
                    </span>

                    @if ($photos->hasMorePages())
                        <a
                            href="{{ $photos->nextPageUrl() }}"
                            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                        >
                            Berikutnya &rarr;
                        </a>
                    @else
                        <span class="inline-flex items-center rounded-md border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-400" aria-disabled="true">
                            Berikutnya &rarr;
                        </span>
                    @endif
                </nav>
            @endif
        </div>
    </div>
</x-app-layout>
