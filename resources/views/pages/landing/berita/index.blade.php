<x-layouts.public :title="$title" :description="$description">
    <main>
        <x-page-heading
            :title="$title"
            :description="$description"
        />

        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
            @if ($articles->isEmpty())
                <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center">
                    <p class="text-base font-medium text-slate-500">
                        {{ $empty }}
                    </p>
                </div>
            @else
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($articles as $article)
                        @include('pages.landing.berita._card', ['article' => $article])
                    @endforeach
                </div>

                @if ($articles->hasPages())
                    <nav class="mt-10 flex items-center justify-between gap-3" aria-label="Navigasi halaman {{ $title }}">
                        @if ($articles->onFirstPage())
                            <span class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-400" aria-disabled="true">
                                &larr; {{ __('site.articles.previous') }}
                            </span>
                        @else
                            <a
                                href="{{ $articles->previousPageUrl() }}"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:text-blue-700"
                            >
                                &larr; {{ __('site.articles.previous') }}
                            </a>
                        @endif

                        <span class="text-sm font-medium text-slate-500">
                            Halaman {{ $articles->currentPage() }}
                        </span>

                        @if ($articles->hasMorePages())
                            <a
                                href="{{ $articles->nextPageUrl() }}"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:text-blue-700"
                            >
                                {{ __('site.articles.next') }} &rarr;
                            </a>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-400" aria-disabled="true">
                                {{ __('site.articles.next') }} &rarr;
                            </span>
                        @endif
                    </nav>
                @endif
            @endif
        </div>
    </main>
</x-layouts.public>
