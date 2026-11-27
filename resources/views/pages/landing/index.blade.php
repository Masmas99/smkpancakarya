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
                                'bg-white text-blue-950 shadow-sm' => $isActive,
                                'text-white hover:bg-blue-900 hover:text-white' => ! $isActive,
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

        <section id="galeri" class="border-t border-slate-200 bg-slate-50 py-16 sm:py-20" aria-labelledby="gallery-title">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-3xl text-center">
                    <p class="text-xs font-bold uppercase tracking-[0.25em] text-blue-700">
                        {{ __('site.gallery.label') }}
                    </p>

                    <h2 id="gallery-title" class="mt-2 text-3xl font-bold uppercase tracking-tight text-blue-950 sm:text-4xl lg:text-5xl">
                        {{ __('site.gallery.heading') }}
                    </h2>

                    <p class="mt-3 text-sm leading-relaxed text-slate-600 sm:text-base">
                        {{ __('site.gallery.intro') }}
                    </p>
                </div>

                @if ($galleryPhotos->isEmpty())
                    <div class="mx-auto mt-10 max-w-3xl rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                        <p class="text-base font-semibold text-slate-700">
                            {{ __('site.gallery.empty') }}
                        </p>
                    </div>
                @else
                    <div class="mt-10 grid grid-cols-2 gap-2 sm:grid-cols-3 sm:gap-3 lg:grid-cols-4 xl:grid-cols-5">
                        @foreach ($galleryPhotos as $photo)
                            <figure class="group relative overflow-hidden rounded-lg bg-slate-200 shadow-sm">
                                <button
                                    type="button"
                                    class="block aspect-square w-full cursor-zoom-in overflow-hidden focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-blue-600"
                                    data-gallery-open
                                    data-gallery-src="{{ $photo->imageUrl() }}"
                                    data-gallery-caption="{{ $photo->title }}"
                                    data-gallery-alt="{{ $photo->title ?: __('site.gallery.photo_alt') }}"
                                    aria-label="{{ __('site.gallery.open_photo') }}"
                                >
                                    <img
                                        src="{{ $photo->imageUrl() }}"
                                        alt="{{ $photo->title ?: __('site.gallery.photo_alt') }}"
                                        class="block h-full w-full object-cover transition duration-500 ease-out group-hover:scale-105"
                                        loading="lazy"
                                        decoding="async"
                                    >
                                </button>

                                @if (filled($photo->title))
                                    <figcaption class="pointer-events-none absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/85 via-slate-950/35 to-transparent p-2.5 pt-8 text-xs font-semibold leading-snug text-white opacity-100 sm:opacity-0 sm:transition sm:duration-300 sm:group-hover:opacity-100">
                                        {{ $photo->title }}
                                    </figcaption>
                                @endif
                            </figure>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <div
            id="gallery-lightbox"
            class="fixed inset-0 z-[100] hidden bg-slate-950/95 backdrop-blur-sm"
            role="dialog"
            aria-modal="true"
            aria-label="{{ __('site.gallery.lightbox_label') }}"
            tabindex="-1"
        >
            <div class="flex h-full flex-col" data-lightbox-panel>
                <div class="flex shrink-0 items-center justify-between gap-3 px-3 py-3 sm:px-6">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-white sm:text-base" data-lightbox-caption></p>
                        <p class="mt-0.5 text-xs font-semibold tracking-[0.2em] text-white/50" data-lightbox-counter aria-live="polite"></p>
                    </div>

                    <div class="flex shrink-0 items-center gap-2">
                        <button
                            type="button"
                            class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/10 text-white/80 backdrop-blur transition hover:bg-white/20 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                            data-lightbox-play
                            data-label-play="{{ __('site.gallery.play') }}"
                            data-label-pause="{{ __('site.gallery.pause') }}"
                            aria-label="{{ __('site.gallery.play') }}"
                            aria-pressed="false"
                        >
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true" data-lightbox-play-icon>
                                <path d="M8 5.14v13.72L19 12z" />
                            </svg>
                            <svg class="hidden h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true" data-lightbox-pause-icon>
                                <path d="M7 5h3.5v14H7zM13.5 5H17v14h-3.5z" />
                            </svg>
                        </button>

                        <button
                            type="button"
                            class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/10 text-white backdrop-blur transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                            data-lightbox-close
                            aria-label="{{ __('site.gallery.close') }}"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="relative flex min-h-0 flex-1 items-center justify-center overflow-hidden px-2 sm:px-16" data-lightbox-stage>
                    <button
                        type="button"
                        class="absolute left-2 top-1/2 z-10 inline-flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-white backdrop-blur transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white sm:left-4"
                        data-lightbox-prev
                        aria-label="{{ __('site.gallery.prev') }}"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 5l-7 7 7 7" />
                        </svg>
                    </button>

                    <img
                        src=""
                        alt=""
                        class="block max-h-full max-w-full select-none rounded-lg object-contain shadow-2xl"
                        data-lightbox-image
                        draggable="false"
                    >

                    <button
                        type="button"
                        class="absolute right-2 top-1/2 z-10 inline-flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-white backdrop-blur transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white sm:right-4"
                        data-lightbox-next
                        aria-label="{{ __('site.gallery.next') }}"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <div class="flex shrink-0 items-center gap-1.5 px-2 py-3 sm:gap-2 sm:px-6">
                    <button
                        type="button"
                        class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-white/60 transition hover:bg-white/10 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                        data-lightbox-thumbs-prev
                        aria-label="{{ __('site.gallery.scroll_prev') }}"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 5l-7 7 7 7" />
                        </svg>
                    </button>

                    <div class="flex min-w-0 flex-1 gap-1.5 overflow-x-auto px-0.5 py-0.5 sm:gap-2" data-lightbox-thumbs></div>

                    <button
                        type="button"
                        class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-white/60 transition hover:bg-white/10 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                        data-lightbox-thumbs-next
                        aria-label="{{ __('site.gallery.scroll_next') }}"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
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

        (() => {
            const lightbox = document.getElementById('gallery-lightbox');
            const triggers = Array.from(document.querySelectorAll('[data-gallery-open]'));

            if (!lightbox || triggers.length === 0) {
                return;
            }

            const image = lightbox.querySelector('[data-lightbox-image]');
            const caption = lightbox.querySelector('[data-lightbox-caption]');
            const counter = lightbox.querySelector('[data-lightbox-counter]');
            const thumbsContainer = lightbox.querySelector('[data-lightbox-thumbs]');
            const thumbsPrevButton = lightbox.querySelector('[data-lightbox-thumbs-prev]');
            const thumbsNextButton = lightbox.querySelector('[data-lightbox-thumbs-next]');
            const closeButton = lightbox.querySelector('[data-lightbox-close]');
            const stage = lightbox.querySelector('[data-lightbox-stage]');
            const prevButtons = Array.from(lightbox.querySelectorAll('[data-lightbox-prev]'));
            const nextButtons = Array.from(lightbox.querySelectorAll('[data-lightbox-next]'));
            const playButton = lightbox.querySelector('[data-lightbox-play]');
            const playIcon = lightbox.querySelector('[data-lightbox-play-icon]');
            const pauseIcon = lightbox.querySelector('[data-lightbox-pause-icon]');

            const thumbClasses = {
                base: 'block h-14 w-14 shrink-0 overflow-hidden rounded-md border-2 opacity-50 transition hover:opacity-90 focus:outline-none focus-visible:ring-2 focus-visible:ring-white sm:h-16 sm:w-16',
                active: 'border-white opacity-100',
                idle: 'border-transparent',
            };

            const slideIntervalMs = 5000;
            const playLabel = playButton?.dataset.labelPlay || 'Putar otomatis';
            const pauseLabel = playButton?.dataset.labelPause || 'Hentikan putar otomatis';

            const thumbs = triggers.map((trigger, index) => {
                const thumb = document.createElement('button');
                thumb.type = 'button';
                thumb.className = `${thumbClasses.base} ${thumbClasses.idle}`;
                thumb.setAttribute('aria-label', `Lihat foto ${index + 1}`);

                const thumbImage = document.createElement('img');
                thumbImage.src = trigger.dataset.gallerySrc || '';
                thumbImage.alt = '';
                thumbImage.loading = 'lazy';
                thumbImage.decoding = 'async';
                thumbImage.className = 'block h-full w-full object-cover';

                thumb.appendChild(thumbImage);
                thumb.addEventListener('click', () => show(index));
                thumbsContainer?.appendChild(thumb);

                return thumb;
            });

            let currentIndex = 0;
            let lastFocused = null;
            let touchStartX = null;
            let slideTimer = null;

            const syncPlayButton = (playing) => {
                playIcon?.classList.toggle('hidden', playing);
                pauseIcon?.classList.toggle('hidden', !playing);
                playButton?.setAttribute('aria-pressed', playing ? 'true' : 'false');
                playButton?.setAttribute('aria-label', playing ? pauseLabel : playLabel);
            };

            const stopSlideshow = () => {
                if (slideTimer !== null) {
                    window.clearInterval(slideTimer);
                    slideTimer = null;
                }

                syncPlayButton(false);
            };

            const show = (index) => {
                currentIndex = (index + triggers.length) % triggers.length;

                const trigger = triggers[currentIndex];
                const source = trigger.dataset.gallerySrc || '';
                const title = (trigger.dataset.galleryCaption || '').trim();

                image.src = source;
                image.alt = (trigger.dataset.galleryAlt || '').trim();
                caption.textContent = title || `Foto ${currentIndex + 1}`;
                counter.textContent = `${currentIndex + 1} / ${triggers.length}`;

                thumbs.forEach((thumb, thumbIndex) => {
                    const active = thumbIndex === currentIndex;
                    thumb.className = `${thumbClasses.base} ${active ? thumbClasses.active : thumbClasses.idle}`;

                    if (active) {
                        thumb.setAttribute('aria-current', 'true');
                        thumb.scrollIntoView({ block: 'nearest', inline: 'nearest' });
                    } else {
                        thumb.removeAttribute('aria-current');
                    }
                });
            };

            const startSlideshow = () => {
                stopSlideshow();
                syncPlayButton(true);
                slideTimer = window.setInterval(() => show(currentIndex + 1), slideIntervalMs);
            };

            const open = (index, trigger) => {
                lastFocused = trigger || null;
                stopSlideshow();
                lightbox.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
                show(index);
                lightbox.focus({ preventScroll: true });
            };

            const close = () => {
                stopSlideshow();
                lightbox.classList.add('hidden');
                document.body.style.overflow = '';
                image.removeAttribute('src');

                if (lastFocused && typeof lastFocused.focus === 'function') {
                    lastFocused.focus({ preventScroll: true });
                }
            };

            const isOpen = () => !lightbox.classList.contains('hidden');

            triggers.forEach((trigger, index) => {
                trigger.addEventListener('click', () => open(index, trigger));
            });

            closeButton?.addEventListener('click', close);
            prevButtons.forEach((button) => button.addEventListener('click', () => show(currentIndex - 1)));
            nextButtons.forEach((button) => button.addEventListener('click', () => show(currentIndex + 1)));

            playButton?.addEventListener('click', () => {
                if (slideTimer === null) {
                    startSlideshow();
                } else {
                    stopSlideshow();
                }
            });

            thumbsPrevButton?.addEventListener('click', () => {
                thumbsContainer?.scrollBy({ left: -(thumbsContainer.clientWidth || 240), behavior: 'smooth' });
            });

            thumbsNextButton?.addEventListener('click', () => {
                thumbsContainer?.scrollBy({ left: thumbsContainer.clientWidth || 240, behavior: 'smooth' });
            });

            stage?.addEventListener('click', (event) => {
                if (event.target === stage) {
                    close();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (!isOpen()) {
                    return;
                }

                if (event.key === 'Escape') {
                    close();
                } else if (event.key === 'ArrowLeft') {
                    event.preventDefault();
                    show(currentIndex - 1);
                } else if (event.key === 'ArrowRight') {
                    event.preventDefault();
                    show(currentIndex + 1);
                }
            });

            lightbox.addEventListener('touchstart', (event) => {
                if (event.target.closest('[data-lightbox-thumbs]')) {
                    touchStartX = null;
                    return;
                }

                touchStartX = event.changedTouches[0]?.clientX ?? null;
            }, { passive: true });

            lightbox.addEventListener('touchend', (event) => {
                if (touchStartX === null) {
                    return;
                }

                const delta = (event.changedTouches[0]?.clientX ?? touchStartX) - touchStartX;
                touchStartX = null;

                if (Math.abs(delta) < 40) {
                    return;
                }

                show(currentIndex + (delta < 0 ? 1 : -1));
            }, { passive: true });
        })();
    </script>
</x-layouts.public>
