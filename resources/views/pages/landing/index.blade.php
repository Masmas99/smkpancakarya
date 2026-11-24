<x-layouts.public>
    <main>
        <section
            class="hero-full relative isolate flex items-end justify-center overflow-hidden bg-slate-900"
            aria-labelledby="hero-title"
        >
            <video
                id="hero-video"
                class="absolute inset-0 -z-20 h-full w-full object-cover"
                autoplay
                muted
                loop
                playsinline
                preload="metadata"
                aria-hidden="true"
                tabindex="-1"
            >
                <source src="{{ asset('vidio/vidio-sekolah.mp4') }}" type="video/mp4">
            </video>

            <div class="absolute inset-0 -z-10 bg-slate-950/35" aria-hidden="true"></div>

            <div class="relative z-10 mx-auto w-full max-w-5xl px-5 pb-28 text-center text-white sm:px-6 sm:pb-32 lg:px-8 lg:pb-36">
                <h1 id="hero-title" class="text-balance text-[2rem] font-extrabold uppercase leading-[1.05] tracking-tight drop-shadow-lg sm:text-5xl lg:text-6xl xl:text-7xl">
                    {{ __('site.hero.title') }}
                </h1>
                <p class="mt-4 text-balance text-xs font-bold uppercase leading-snug tracking-[0.2em] text-white/90 drop-shadow-lg sm:text-sm lg:text-base">
                    {{ __('site.hero.tagline') }}
                </p>
                <p class="mx-auto mt-2 max-w-3xl text-balance text-base font-semibold leading-relaxed text-white drop-shadow sm:text-lg lg:text-xl">
                    {{ __('site.hero.subtitle') }}
                </p>
            </div>
        </section>

        <section
            class="relative z-20 -mt-16 pb-14 sm:-mt-20 lg:-mt-24"
            aria-labelledby="achievements-title"
        >
            <h2 id="achievements-title" class="sr-only">{{ __('site.achievements.label') }}</h2>

            <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
                @php
                    $achievementIcons = [
                        'trophy-gold.webp',
                        'globe-asia.webp',
                        'graduationcap.webp',
                        'briefcase.webp',
                    ];
                @endphp

                <ul class="grid grid-cols-2 gap-px bg-blue-800 shadow-[0_18px_40px_-26px_rgba(15,23,42,0.45)] lg:grid-cols-4">
                    @foreach (__('site.achievements.items') as $index => $item)
                        <li class="group flex cursor-pointer flex-col items-center justify-center gap-3 bg-white px-3 py-6 text-center transition-colors duration-200 hover:bg-blue-900 sm:gap-4 sm:px-6 sm:py-8 lg:px-8 lg:py-10">
                            <img
                                src="{{ asset('images/' . ($achievementIcons[$index] ?? 'trophy-gold.webp')) }}"
                                alt=""
                                class="h-12 w-12 object-contain sm:h-16 sm:w-16"
                                loading="lazy"
                                decoding="async"
                                aria-hidden="true"
                            >

                            <div>
                                <p class="text-balance text-sm font-bold uppercase leading-snug tracking-wide text-slate-900 transition-colors duration-200 group-hover:text-white sm:text-base">
                                    {{ $item['title'] }}
                                </p>
                                <p class="mt-1 text-balance text-xs font-semibold uppercase leading-snug tracking-wide text-slate-600 transition-colors duration-200 group-hover:text-slate-100 sm:text-sm">
                                    {{ $item['description'] }}
                                </p>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>

        <section class="relative isolate overflow-hidden bg-white" aria-labelledby="headmaster-title">
            @php
                $headmasterPhoto = null;

                foreach (['webp', 'jpg', 'jpeg', 'png'] as $extension) {
                    $candidate = 'images/kepala-sekolah.'.$extension;

                    if (file_exists(public_path($candidate))) {
                        $headmasterPhoto = $candidate;
                        break;
                    }
                }
            @endphp

            <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden" aria-hidden="true">
                <img
                    src="{{ asset('images/wave-kiri.svg') }}"
                    alt=""
                    class="absolute -left-10 bottom-0 hidden h-full w-auto max-w-none opacity-25 sm:block"
                    loading="lazy"
                    decoding="async"
                >
                <img
                    src="{{ asset('images/wave-kanan.svg') }}"
                    alt=""
                    class="absolute -right-10 top-0 h-full w-auto max-w-none opacity-30"
                    loading="lazy"
                    decoding="async"
                >
            </div>

            <div class="relative mx-auto grid w-full max-w-7xl items-center gap-10 px-5 py-14 sm:px-6 sm:py-16 lg:grid-cols-[minmax(0,1fr)_22rem] lg:gap-16 lg:px-8 lg:py-20">
                <div class="order-2 lg:order-1">
                    <h2 id="headmaster-title" class="sr-only">{{ __('site.headmaster.label') }}</h2>

                    <p class="text-balance text-xl font-medium leading-relaxed text-slate-700 sm:text-2xl sm:leading-relaxed">
                        &ldquo;{{ __('site.headmaster.quote') }}&rdquo;
                    </p>

                    <a
                        href="{{ route('profil.about') }}"
                        class="mt-7 inline-flex items-center rounded-full bg-blue-700 px-5 py-2.5 text-sm font-bold text-white transition hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2 focus-visible:ring-offset-white"
                    >
                        {{ __('site.headmaster.cta') }}
                    </a>
                </div>

                <div class="relative order-1 mx-auto w-full max-w-[14rem] sm:max-w-[18rem] lg:order-2 lg:ml-auto lg:mr-0 lg:max-w-[22rem]">
                    @if ($headmasterPhoto)
                        <img
                            src="{{ asset($headmasterPhoto) }}"
                            alt="{{ __('site.headmaster.photo_alt') }}"
                            class="aspect-[2/3] w-full object-cover"
                            loading="lazy"
                            decoding="async"
                        >
                    @else
                        <div class="flex aspect-[2/3] w-full items-center justify-center bg-slate-100" aria-hidden="true">
                            <svg class="h-24 w-24 text-slate-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                            </svg>
                        </div>
                    @endif

                    <div class="relative z-10 mx-3 -mt-12 rounded-2xl border border-slate-100 bg-white/95 px-5 py-4 text-center shadow-xl shadow-slate-900/10 backdrop-blur sm:mx-5 sm:-mt-14 sm:rounded-3xl">
                        <p class="text-balance text-sm font-bold leading-snug text-slate-900 sm:text-base">
                            {{ __('site.headmaster.name') }}
                        </p>
                        <p class="mt-1 text-xs font-medium leading-snug text-slate-500 sm:text-sm">
                            {{ __('site.headmaster.position') }}
                        </p>
                        <div class="mx-auto mt-3 h-1 w-12 rounded-full bg-blue-700" aria-hidden="true"></div>
                    </div>
                </div>
            </div>
        </section>

        <section id="artikel" class="border-t border-slate-200 bg-white py-16 sm:py-20" aria-labelledby="articles-title">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <h2 id="articles-title" class="text-center text-3xl font-bold tracking-tight text-blue-950 sm:text-4xl lg:text-5xl">
                    {{ __('site.articles.heading') }}
                </h2>

                <nav
                    class="mx-auto mt-8 grid max-w-5xl grid-cols-2 gap-1 rounded-3xl bg-blue-950 p-1.5 sm:grid-cols-4 sm:rounded-full"
                    aria-label="{{ __('site.articles.filter_label') }}"
                >
                    @foreach ($categories as $key => $label)
                        @php $isActive = $activeCategory === $key; @endphp
                        <a
                            href="{{ $categoryRoutes[$key] }}"
                            data-article-tab
                            data-category="{{ $key }}"
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
                            {{ __('site.articles.empty_category') }}
                        </p>
                    </div>
                @else
                    <div id="article-grid" class="mt-12 grid gap-x-6 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($articles as $article)
                            @include("pages.landing.{$activeCategory}._card-featured", ['article' => $article])
                        @endforeach
                    </div>

                    @if ($articles->hasMorePages())
                        <div class="mt-12 flex justify-center">
                            <a
                                id="load-more"
                                href="{{ $categoryRoutes[$activeCategory] }}"
                                class="inline-flex items-center justify-center rounded-md bg-blue-950 px-8 py-3 text-sm font-bold text-white shadow-sm transition hover:bg-blue-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2"
                            >
                                {{ __('site.articles.load_more') }}
                            </a>
                        </div>
                    @endif
                @endif
            </div>
        </section>
    </main>

    <script>
        (() => {
            const video = document.getElementById('hero-video');

            if (!video) {
                return;
            }

            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

            const syncPlayback = () => {
                if (reduceMotion.matches) {
                    video.pause();
                    return;
                }

                // Some browsers block autoplay until play() is called explicitly.
                const playPromise = video.play();
                if (playPromise && typeof playPromise.catch === 'function') {
                    playPromise.catch(() => {});
                }
            };

            syncPlayback();

            if (typeof reduceMotion.addEventListener === 'function') {
                reduceMotion.addEventListener('change', syncPlayback);
            } else if (typeof reduceMotion.addListener === 'function') {
                reduceMotion.addListener(syncPlayback);
            }
        })();

        (() => {
            let articleSection = document.getElementById('artikel');

            if (!articleSection
                || typeof window.fetch !== 'function'
                || typeof window.AbortController !== 'function'
                || typeof window.DOMParser !== 'function') {
                return;
            }

            // Bersihkan URL lama yang masih membawa parameter kategori dan anchor artikel.
            const pageUrl = new URL(window.location.href);
            let urlChanged = false;

            if (pageUrl.searchParams.has('category')) {
                pageUrl.searchParams.delete('category');
                urlChanged = true;
            }

            if (pageUrl.hash === '#artikel') {
                pageUrl.hash = '';
                urlChanged = true;
            }

            if (urlChanged) {
                window.history.replaceState(null, '', pageUrl.href);
            }

            let activeRequest = null;

            const setLoading = (loading) => {
                // Jangan meredupkan konten lama; cukup tandai sedang memuat
                // agar tidak muncul area putih saat menunggu respons.
                articleSection.setAttribute('aria-busy', loading ? 'true' : 'false');
            };

            const loadCategory = (link) => {
                const fetchUrl = new URL(window.location.href);
                fetchUrl.hash = '';
                fetchUrl.search = '';
                fetchUrl.searchParams.set('category', link.dataset.category);

                if (activeRequest) {
                    activeRequest.abort();
                }

                const controller = new AbortController();
                activeRequest = controller;

                setLoading(true);

                fetch(fetchUrl.href, {
                    headers: {
                        Accept: 'text/html',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    signal: controller.signal,
                })
                    .then((response) => {
                        if (!response.ok) {
                            throw new Error(`Gagal memuat kategori (${response.status}).`);
                        }

                        return response.text();
                    })
                    .then((html) => {
                        const doc = new DOMParser().parseFromString(html, 'text/html');
                        const nextSection = doc.getElementById('artikel');

                        if (!nextSection) {
                            throw new Error('Bagian artikel tidak ditemukan.');
                        }

                        articleSection.replaceWith(nextSection);
                        articleSection = nextSection;

                        // URL alamat tetap bersih; kategori hanya dipakai untuk request internal.

                        // Kembalikan fokus ke tab aktif agar navigasi keyboard tetap nyaman.
                        articleSection
                            .querySelector('[data-article-tab][aria-current="page"]')
                            ?.focus({ preventScroll: true });
                    })
                    .catch((error) => {
                        if (error.name === 'AbortError') {
                            return;
                        }

                        // Jika AJAX gagal, buka halaman kategori lengkap.
                        window.location.href = link.href;
                    })
                    .finally(() => {
                        if (activeRequest === controller) {
                            activeRequest = null;
                            setLoading(false);
                        }
                    });
            };

            document.addEventListener('click', (event) => {
                const link = event.target.closest?.('a[data-article-tab]');

                if (!link
                    || event.defaultPrevented
                    || event.button !== 0
                    || event.metaKey
                    || event.ctrlKey
                    || event.shiftKey
                    || event.altKey) {
                    return;
                }

                if (!link.dataset.category) {
                    return;
                }

                event.preventDefault();

                loadCategory(link);
            });
        })();
    </script>
</x-layouts.public>
