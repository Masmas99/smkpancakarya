<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Kelola {{ $sectionLabel }}
            </h2>

            <a
                href="{{ route($routePrefix.'.create') }}"
                class="inline-flex items-center gap-2 rounded-md bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2"
            >
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tulis {{ $sectionLabel }}
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
                    placeholder="Cari judul atau ringkasan..."
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:max-w-sm"
                >

                <select
                    name="status"
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:w-44"
                >
                    <option value="">Semua status</option>
                    <option value="draft" @selected($status === 'draft')>Draf</option>
                    <option value="published" @selected($status === 'published')>Terbit</option>
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

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Konten
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Penulis
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Diperbarui
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    <span class="sr-only">Aksi</span>
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($articles as $article)
                                <tr>
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-semibold text-gray-900">
                                            {{ $article->title }}
                                        </p>
                                        <p class="mt-0.5 max-w-md truncate text-xs text-gray-500">
                                            {{ $article->excerptText(90) }}
                                        </p>
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4">
                                        @if ($article->status === \App\Models\Article::STATUS_PUBLISHED)
                                            <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                                Terbit
                                            </span>
                                        @else
                                            <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                                Draf
                                            </span>
                                        @endif
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                        {{ $article->author?->name ?? '-' }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                        {{ ($article->published_at ?? $article->updated_at)?->translatedFormat('d M Y H:i') }}
                                    </td>

                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                        <div class="flex items-center justify-end gap-3">
                                            <a
                                                href="{{ route($routePrefix.'.edit', $article) }}"
                                                class="font-medium text-blue-700 transition hover:text-blue-800"
                                            >
                                                Ubah
                                            </a>

                                            <form
                                                method="POST"
                                                action="{{ route($routePrefix.'.destroy', $article) }}"
                                                onsubmit="return confirm('Hapus konten ini? Tindakan ini tidak dapat dibatalkan.');"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="font-medium text-red-600 transition hover:text-red-700">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-14 text-center text-sm text-gray-500">
                                        Belum ada {{ strtolower($sectionLabel) }}. Klik <span class="font-semibold text-gray-700">Tulis {{ $sectionLabel }}</span> untuk membuat yang pertama.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if ($articles->hasPages())
                <nav class="flex items-center justify-between gap-3" aria-label="Navigasi halaman konten">
                    @if ($articles->onFirstPage())
                        <span class="inline-flex items-center rounded-md border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-400" aria-disabled="true">
                            &larr; Sebelumnya
                        </span>
                    @else
                        <a
                            href="{{ $articles->previousPageUrl() }}"
                            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                        >
                            &larr; Sebelumnya
                        </a>
                    @endif

                    <span class="text-sm font-medium text-gray-500">
                        Halaman {{ $articles->currentPage() }}
                    </span>

                    @if ($articles->hasMorePages())
                        <a
                            href="{{ $articles->nextPageUrl() }}"
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
