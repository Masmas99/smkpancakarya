<x-layouts.public :title="__('site.nav.articles')" :description="__('site.articles.meta_description')">
    <main class="bg-white">
        <section class="mx-auto max-w-7xl px-4 pb-16 pt-14 sm:px-6 sm:pt-16 lg:px-8">
            <h1 class="text-center text-3xl font-bold tracking-tight text-blue-950 sm:text-4xl lg:text-5xl">
                {{ __('site.articles.heading') }}
            </h1>

            <nav
                class="mx-auto mt-8 grid max-w-5xl grid-cols-3 gap-1 rounded-full bg-blue-950 p-1.5"
                aria-label="{{ __('site.articles.filter_label') }}"
            >
                @foreach ($categories as $key => $label)
                    @php $isActive = $activeCategory === $key; @endphp
                    <a
                        href="{{ $isActive ? route('artikel.index') : route('artikel.index', ['category' => $key]) }}"
                        @class([
                            'rounded-full px-2 py-2.5 text-center text-sm font-bold transition sm:px-4 sm:text-base',
                            'bg-amber-400 text-blue-950' => $isActive,
                            'text-amber-400 hover:text-amber-300' => ! $isActive,
                        ])
                        @if ($isActive) aria-current="page" @endif
                    >
                        {{ $label }}
                    </a>
                @endforeach
            </nav>

            @if ($articles->isEmpty())
                <div class="mx-auto mt-12 max-w-3xl rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-14 text-center">
                    <p class="text-base font-semibold text-slate-700">
                        {{ $activeCategory !== '' ? __('site.articles.empty_category') : __('site.articles.empty') }}
                    </p>
                </div>
            @else
                <div id="article-grid" class="mt-12 grid gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($articles as $article)
                        @include('pages.landing.artikel._card', ['article' => $article])
                    @endforeach
                </div>

                @if ($articles->hasMorePages())
                    <div id="load-more-wrapper" class="mt-12 flex justify-center">
                        <a
                            id="load-more"
                            href="{{ $articles->nextPageUrl() }}"
                            class="inline-flex items-center justify-center rounded-md bg-blue-950 px-8 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2"
                        >
                            {{ __('site.articles.load_more') }}
                        </a>
                    </div>
                @endif
            @endif
        </section>
    </main>

    <script>
    (() => {
        const button = document.getElementById('load-more');
        const grid = document.getElementById('article-grid');
        if (! button || ! grid) return;

        button.addEventListener('click', (event) => {
            event.preventDefault();

            const target = button.getAttribute('href');
            if (! target) return;

            button.setAttribute('aria-busy', 'true');
            button.classList.add('pointer-events-none', 'opacity-60');

            fetch(target, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then((response) => response.ok ? response.text() : Promise.reject())
                .then((html) => {
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const nextGrid = doc.getElementById('article-grid');
                    const nextButton = doc.getElementById('load-more');

                    if (nextGrid) {
                        while (nextGrid.firstElementChild) {
                            grid.appendChild(nextGrid.firstElementChild);
                        }
                    }

                    if (nextButton) {
                        button.setAttribute('href', nextButton.getAttribute('href'));
                        button.removeAttribute('aria-busy');
                        button.classList.remove('pointer-events-none', 'opacity-60');
                    } else {
                        const wrapper = document.getElementById('load-more-wrapper');
                        if (wrapper) wrapper.remove();
                    }
                })
                .catch(() => {
                    window.location.href = target;
                });
        });
    })();
    </script>
</x-layouts.public>
