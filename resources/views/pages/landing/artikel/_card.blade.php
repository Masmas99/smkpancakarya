@php
    /** @var \App\Models\Article $article */
    $coverUrl = $article->coverUrl();
@endphp

<article class="group flex flex-col border-b border-slate-200 pb-6">
    <a
        href="{{ route('artikel.show', $article) }}"
        class="block overflow-hidden bg-slate-100"
        tabindex="-1"
        aria-hidden="true"
    >
        @if ($coverUrl)
            <img
                src="{{ $coverUrl }}"
                alt=""
                class="aspect-[4/3] w-full object-cover transition duration-300 group-hover:scale-[1.03]"
                loading="lazy"
                decoding="async"
            >
        @else
            <div class="flex aspect-[4/3] w-full items-center justify-center bg-gradient-to-br from-blue-950 via-blue-900 to-blue-800">
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

    <h3 class="mt-4 text-lg font-bold leading-snug text-blue-950">
        <a
            href="{{ route('artikel.show', $article) }}"
            class="transition hover:text-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2"
        >
            {{ $article->title }}
        </a>
    </h3>

    @if ($article->published_at)
        <p class="mt-3 flex items-center gap-2 text-sm text-slate-500">
            <svg class="h-4 w-4 shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
            </svg>
            <time datetime="{{ $article->published_at->toDateString() }}">
                {{ $article->published_at->translatedFormat('d F Y') }}
            </time>
        </p>
    @endif
</article>
