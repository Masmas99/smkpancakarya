@php
    $coverUrl = $article->coverUrl();
@endphp

<x-layouts.public :title="$article->title" :description="$article->excerptText(160)">
    <main>
        <x-page-heading
            :title="$article->title"
            :parents="[['label' => __('site.nav.articles'), 'url' => route('artikel.index')]]"
        />

        <article class="mx-auto max-w-3xl px-4 py-10 sm:px-6 sm:py-14">
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-slate-500">
                <span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700">
                    {{ $article->categoryLabel() }}
                </span>

                <span>
                    {{ __('site.articles.by') }}
                    <span class="font-medium text-slate-700">{{ $article->author?->name ?? 'Admin' }}</span>
                </span>

                @if ($article->published_at)
                    <span aria-hidden="true">&bull;</span>
                    <time datetime="{{ $article->published_at->toDateString() }}">
                        {{ $article->published_at->translatedFormat('d F Y') }}
                    </time>
                @endif
            </div>

            @if ($coverUrl)
                <img
                    src="{{ $coverUrl }}"
                    alt="{{ $article->title }}"
                    class="mt-6 w-full rounded-2xl object-cover"
                    loading="eager"
                    decoding="async"
                >
            @endif

            <div class="mt-8 space-y-5 text-base leading-relaxed text-slate-700">
                @foreach (preg_split('/\R{2,}/', $article->body) as $paragraph)
                    @if (trim($paragraph) !== '')
                        <p>{!! nl2br(e(trim($paragraph))) !!}</p>
                    @endif
                @endforeach
            </div>

            <div class="mt-10 border-t border-slate-200 pt-6">
                <a
                    href="{{ route('artikel.index') }}"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-blue-700 transition hover:text-blue-800"
                >
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    {{ __('site.articles.back') }}
                </a>
            </div>
        </article>
    </main>
</x-layouts.public>
