@php
    /** @var \App\Models\Article $article */
    $coverUrl = $article->coverUrl();
@endphp

<article class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-lg">
    <a
        href="{{ $article->url() }}"
        class="block aspect-[16/10] overflow-hidden bg-slate-100"
        tabindex="-1"
        aria-hidden="true"
    >
        @if ($coverUrl)
            <img
                src="{{ $coverUrl }}"
                alt=""
                class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                loading="lazy"
                decoding="async"
            >
        @else
            <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700">
                <img
                    src="{{ asset('images/logo-smk-pancakarya.webp') }}"
                    alt=""
                    class="h-16 w-16 object-contain opacity-90"
                    loading="lazy"
                    decoding="async"
                >
            </div>
        @endif
    </a>

    <div class="flex flex-1 flex-col p-5">
        <p class="text-xs font-semibold uppercase tracking-wider text-blue-700">
            {{ $article->published_at?->translatedFormat('d F Y') }}
        </p>

        <h3 class="mt-2 text-lg font-bold leading-snug text-slate-900">
            <a
                href="{{ $article->url() }}"
                class="transition hover:text-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2"
            >
                {{ $article->title }}
            </a>
        </h3>

        <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-slate-600">
            {{ $article->excerptText() }}
        </p>

        <a
            href="{{ $article->url() }}"
            class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-blue-700 transition hover:text-blue-800"
        >
            {{ __('site.articles.read_more') }}
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
            </svg>
        </a>
    </div>
</article>
