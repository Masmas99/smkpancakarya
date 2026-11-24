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

        <section class="border-t border-slate-200 bg-white py-14 sm:py-16" aria-labelledby="articles-title">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-700">
                            {{ __('site.nav.articles') }}
                        </p>
                        <h2 id="articles-title" class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                            {{ __('site.articles.latest') }}
                        </h2>
                    </div>

                    <a
                        href="{{ route('artikel.index') }}"
                        class="inline-flex items-center gap-1.5 rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:text-blue-700"
                    >
                        {{ __('site.articles.view_all') }}
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>

                @if (($latestArticles ?? collect())->isEmpty())
                    <div class="mt-8 rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-12 text-center">
                        <p class="text-base font-semibold text-slate-700">
                            {{ __('site.articles.empty') }}
                        </p>
                        <p class="mt-1 text-sm text-slate-500">
                            {{ __('site.articles.empty_hint') }}
                        </p>
                    </div>
                @else
                    <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($latestArticles as $article)
                            @include('pages.landing.artikel._card', ['article' => $article])
                        @endforeach
                    </div>
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
    </script>
</x-layouts.public>
