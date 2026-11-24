<x-layouts.public>
    <main>
        <section
            class="relative isolate flex min-h-[70vh] items-center justify-center overflow-hidden bg-slate-900 lg:min-h-[80vh]"
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

            <div class="absolute inset-0 -z-10 bg-gradient-to-b from-slate-900/75 via-slate-900/45 to-slate-900/75" aria-hidden="true"></div>

            <div class="mx-auto max-w-3xl px-4 py-20 text-center text-white sm:px-6 lg:py-28">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-blue-200 sm:text-sm">
                    {{ __('site.hero.eyebrow') }}
                </p>
                <h1 id="hero-title" class="mt-4 text-3xl font-bold tracking-tight sm:text-4xl lg:text-5xl">
                    {{ __('site.hero.title') }}
                </h1>
                <p class="mx-auto mt-4 max-w-2xl text-base text-slate-100 sm:text-lg">
                    {{ __('site.hero.subtitle') }}
                </p>

                <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
                    <a
                        href="{{ route('info-ppdb') }}"
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-700 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-900"
                    >
                        {{ __('site.nav.admission') }}
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0-6.75-6.75M19.5 12l-6.75 6.75" />
                        </svg>
                    </a>

                    <a
                        href="{{ route('profil') }}"
                        class="inline-flex items-center rounded-lg border border-white/40 bg-white/10 px-6 py-3 text-sm font-semibold text-white backdrop-blur-sm transition hover:bg-white/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-white"
                    >
                        {{ __('site.hero.secondary') }}
                    </a>
                </div>
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
