<x-layouts.public>
    <main>
        <section
            class="relative isolate flex min-h-[70vh] items-end overflow-hidden bg-slate-900 lg:min-h-[80vh]"
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

            <div class="absolute inset-0 -z-10 bg-gradient-to-t from-slate-900/85 via-slate-900/30 to-transparent" aria-hidden="true"></div>

            <div class="relative z-10 mx-auto w-full max-w-7xl px-4 pb-10 text-left text-white sm:px-6 lg:px-8 lg:pb-16">
                <h1 id="hero-title" class="max-w-3xl text-3xl font-bold tracking-tight drop-shadow sm:text-4xl lg:text-5xl">
                    {{ __('site.hero.title') }}
                </h1>
                <p class="mt-3 max-w-2xl text-base text-slate-100 drop-shadow sm:text-lg">
                    {{ __('site.hero.subtitle') }}
                </p>
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
