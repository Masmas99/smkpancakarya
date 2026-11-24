@php
    $links = [
        ['label' => __('site.nav.home'), 'route' => 'home'],
        ['label' => __('site.nav.study_programs'), 'route' => 'kompetensi-keahlian'],
        ['label' => __('site.nav.achievements'), 'route' => 'prestasi.index'],
        [
            'label' => __('site.nav.profile'),
            'route' => 'profil',
            'children' => [
                ['label' => __('site.nav.about'), 'route' => 'profil.about'],
                ['label' => __('site.nav.student_affairs'), 'route' => 'profil.kesiswaan'],
                ['label' => __('site.nav.academics'), 'route' => 'profil.akademik'],
            ],
        ],
        ['label' => __('site.nav.vision_mission'), 'route' => 'visi-dan-misi'],
        ['label' => __('site.nav.contact'), 'route' => 'kontak'],
        ['label' => __('site.nav.facilities'), 'route' => 'sarana'],
    ];

    $searchPages = [
        ['label' => __('site.nav.home'), 'route' => 'home'],
        ['label' => __('site.nav.study_programs'), 'route' => 'kompetensi-keahlian'],
        ['label' => __('site.nav.achievements'), 'route' => 'prestasi.index'],
        ['label' => __('site.nav.profile'), 'route' => 'profil'],
        ['label' => __('site.nav.about'), 'route' => 'profil.about'],
        ['label' => __('site.nav.student_affairs'), 'route' => 'profil.kesiswaan'],
        ['label' => __('site.nav.academics'), 'route' => 'profil.akademik'],
        ['label' => __('site.nav.vision_mission'), 'route' => 'visi-dan-misi'],
        ['label' => __('site.nav.contact'), 'route' => 'kontak'],
        ['label' => __('site.nav.facilities'), 'route' => 'sarana'],
        ['label' => __('site.nav.admission'), 'route' => 'info-ppdb'],
    ];
@endphp

{{-- Top bar --}}
<div class="bg-blue-900 text-blue-100">
    <div class="mx-auto flex h-9 max-w-7xl items-center justify-between gap-3 px-4 text-[11px] sm:px-6 sm:text-xs lg:px-8">
        <p class="flex min-w-0 flex-1 items-center gap-1.5">
            <svg class="h-3.5 w-3.5 shrink-0 text-blue-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-6.75-5.25-6.75-10.5a6.75 6.75 0 1 1 13.5 0C18.75 15.75 12 21 12 21Z" />
                <circle cx="12" cy="10.5" r="2.25" />
            </svg>
            <span class="truncate">{{ __('site.topbar.address') }}</span>
        </p>

        <div class="flex shrink-0 items-center gap-3 sm:gap-4">
            <a
                href="tel:0215581180"
                class="inline-flex items-center gap-1.5 rounded transition hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-white/70"
            >
                <svg class="h-3.5 w-3.5 text-blue-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                </svg>
                <span>{{ __('site.topbar.phone') }}</span>
            </a>

            <span class="h-3 w-px bg-blue-700" aria-hidden="true"></span>

            <span class="whitespace-nowrap">
                <span class="hidden sm:inline">{{ __('site.topbar.npsn_label') }} </span>{{ __('site.topbar.npsn') }}
            </span>
        </div>
    </div>
</div>

<header id="site-header" class="sticky top-0 z-50 border-b border-slate-200 bg-white transition-transform duration-300 ease-in-out will-change-transform motion-reduce:transition-none">
    <div class="mx-auto flex h-16 max-w-7xl items-center gap-3 px-4 sm:px-6 lg:h-20 lg:px-8">
        {{-- Logo --}}
        <a
            href="{{ route('home') }}"
            class="flex shrink-0 items-center gap-2.5 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2"
        >
            <img
                src="{{ asset('images/logo-smk-pancakarya.webp') }}"
                alt="Logo SMK Pancakarya"
                width="48"
                height="48"
                class="h-10 w-10 shrink-0 object-contain lg:h-12 lg:w-12"
                decoding="async"
            >
            <span class="flex flex-col leading-tight">
                <span class="text-[15px] font-bold tracking-tight text-slate-900 sm:text-base lg:text-lg">SMK Pancakarya</span>
                <span class="hidden text-[10px] font-medium uppercase tracking-widest text-slate-500 sm:block">{{ __('site.tagline') }}</span>
            </span>
        </a>

        {{-- Menu dan aksi di kanan --}}
        <div class="ml-auto flex items-center gap-1 sm:gap-2">
            <nav class="hidden items-center gap-0.5 xl:flex" aria-label="{{ __('site.nav.label') }}">
                @foreach ($links as $link)
                    @php
                        $baseRoute = str_contains($link['route'], '.') ? str($link['route'])->before('.')->toString() : $link['route'];
                        $isActive = request()->routeIs($link['route'], $baseRoute.'.*');
                    @endphp

                    @if (! empty($link['children']))
                        <div class="group relative">
                            <a
                                href="{{ route($link['route']) }}"
                                @if ($isActive) aria-current="page" @endif
                                @class([
                                    'rounded-lg px-2.5 py-2 text-sm font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2',
                                    'font-semibold text-slate-950' => $isActive,
                                    'text-slate-800 hover:text-slate-950' => ! $isActive,
                                ])
                            >
                                {{ $link['label'] }}
                            </a>

                            {{-- Submenu --}}
                            <div class="invisible absolute left-1/2 top-full w-52 -translate-x-1/2 pt-2 opacity-0 transition duration-150 group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100">
                                <div class="rounded-xl border border-slate-200 bg-white p-1.5 shadow-lg">
                                    @foreach ($link['children'] as $child)
                                        @php
                                            $childActive = request()->routeIs($child['route']);
                                        @endphp
                                        <a
                                            href="{{ route($child['route']) }}"
                                            @if ($childActive) aria-current="page" @endif
                                            @class([
                                                'block rounded-lg px-3 py-2 text-sm font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400',
                                                'bg-slate-100 text-slate-950' => $childActive,
                                                'text-slate-700 hover:bg-slate-100 hover:text-slate-950' => ! $childActive,
                                            ])
                                        >
                                            {{ $child['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <a
                            href="{{ route($link['route']) }}"
                            @if ($isActive) aria-current="page" @endif
                            @class([
                                'rounded-lg px-2.5 py-2 text-sm font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2',
                                'font-semibold text-slate-950' => $isActive,
                                'text-slate-800 hover:text-slate-950' => ! $isActive,
                            ])
                        >
                            {{ $link['label'] }}
                        </a>
                    @endif
                @endforeach
            </nav>

            <span class="mx-1 hidden h-6 w-px bg-slate-200 xl:block" aria-hidden="true"></span>

            {{-- Tombol utama --}}
            <a
                href="{{ route('info-ppdb') }}"
                @if (request()->routeIs('info-ppdb')) aria-current="page" @endif
                class="hidden items-center rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 focus-visible:ring-offset-2 sm:inline-flex"
            >
                {{ __('site.nav.admission') }}
            </a>

            {{-- Tombol menu seluler --}}
            <button
                id="nav-toggle"
                type="button"
                class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-slate-700 transition hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400 xl:hidden"
                aria-controls="mobile-nav"
                aria-expanded="false"
                aria-label="{{ __('site.menu.open') }}"
                data-label-open="{{ __('site.menu.open') }}"
                data-label-close="{{ __('site.menu.close') }}"
            >
                <svg data-icon="open" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
                <svg data-icon="close" class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>

            {{-- Pencarian (paling kanan) --}}
            <button
                id="search-toggle"
                type="button"
                aria-label="{{ __('site.search.label') }}"
                aria-controls="site-search"
                aria-expanded="false"
                class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-slate-700 transition hover:bg-slate-100 hover:text-slate-950 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400"
            >
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M19 10.5a8.5 8.5 0 1 1-17 0 8.5 8.5 0 0 1 17 0Z" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Navigasi seluler --}}
    <div id="mobile-nav" class="hidden border-t border-slate-200 bg-white xl:hidden">
        <nav class="mx-auto max-w-7xl space-y-1 px-4 py-4 sm:px-6" aria-label="{{ __('site.nav.mobile_label') }}">
            @foreach ($links as $link)
                @php
                    $baseRoute = str_contains($link['route'], '.') ? str($link['route'])->before('.')->toString() : $link['route'];
                    $isActive = request()->routeIs($link['route'], $baseRoute.'.*');
                @endphp

                <a
                    href="{{ route($link['route']) }}"
                    @if ($isActive) aria-current="page" @endif
                    @class([
                        'block rounded-lg px-3 py-2.5 text-base font-medium transition',
                        'bg-slate-100 text-slate-950' => $isActive,
                        'text-slate-700 hover:bg-slate-100 hover:text-slate-950' => ! $isActive,
                    ])
                >
                    {{ $link['label'] }}
                </a>

                @if (! empty($link['children']))
                    <div class="ml-3 space-y-1 border-l-2 border-slate-100 pl-3">
                        @foreach ($link['children'] as $child)
                            @php
                                $childActive = request()->routeIs($child['route']);
                            @endphp
                            <a
                                href="{{ route($child['route']) }}"
                                @if ($childActive) aria-current="page" @endif
                                @class([
                                    'block rounded-lg px-3 py-2 text-sm font-medium transition',
                                    'text-slate-950' => $childActive,
                                    'text-slate-600 hover:bg-slate-100 hover:text-slate-950' => ! $childActive,
                                ])
                            >
                                {{ $child['label'] }}
                            </a>
                        @endforeach
                    </div>
                @endif
            @endforeach

            <a
                href="{{ route('info-ppdb') }}"
                @if (request()->routeIs('info-ppdb')) aria-current="page" @endif
                class="mt-2 block rounded-lg bg-blue-700 px-3 py-2.5 text-center text-base font-semibold text-white transition hover:bg-blue-800"
            >
                {{ __('site.nav.admission') }}
            </a>
        </nav>
    </div>
</header>

{{-- Overlay pencarian --}}
<div
    id="site-search"
    class="fixed inset-0 z-40 hidden bg-black/60"
    role="dialog"
    aria-modal="true"
    aria-label="{{ __('site.search.label') }}"
>
    <div class="mx-auto w-full max-w-3xl px-4 pt-28 sm:px-6 sm:pt-40" data-search-dialog>
        <div class="relative">
            <label for="site-search-input" class="sr-only">{{ __('site.search.label') }}</label>
            <svg class="pointer-events-none absolute left-5 top-1/2 h-6 w-6 -translate-y-1/2 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M19 10.5a8.5 8.5 0 1 1-17 0 8.5 8.5 0 0 1 17 0Z" />
            </svg>
            <input
                id="site-search-input"
                type="search"
                autocomplete="off"
                placeholder="{{ __('site.search.placeholder') }}"
                class="h-16 w-full rounded-2xl border-0 bg-white pl-14 pr-14 text-base text-slate-900 shadow-2xl placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400 sm:h-[68px] sm:text-lg [&::-webkit-search-cancel-button]:appearance-none"
            >
            <button
                id="search-close"
                type="button"
                aria-label="{{ __('site.search.close') }}"
                class="absolute right-4 top-1/2 inline-flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400"
            >
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <ul id="site-search-results" class="mt-3 hidden max-h-72 overflow-y-auto rounded-2xl bg-white p-2 shadow-2xl">
            @foreach ($searchPages as $page)
                <li
                    data-search-item
                    data-search="{{ $page['label'].' '.str_replace(['-', '.', '_'], ' ', $page['route']) }}"
                >
                    <a
                        href="{{ route($page['route']) }}"
                        class="block rounded-xl px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-100 hover:text-slate-950 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400"
                    >
                        {{ $page['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>

        <p id="site-search-empty" class="mt-3 hidden w-fit rounded-xl bg-white px-4 py-2 text-sm font-medium text-slate-500 shadow-xl">
            {{ __('site.search.empty') }}
        </p>
    </div>
</div>

<script>
    (() => {
        const searchToggle = document.getElementById('search-toggle');
        const searchPanel = document.getElementById('site-search');
        const searchInput = document.getElementById('site-search-input');
        const searchEmpty = document.getElementById('site-search-empty');
        const searchResults = document.getElementById('site-search-results');
        const searchClose = document.getElementById('search-close');
        const searchItems = Array.from(document.querySelectorAll('[data-search-item]'));

        const menuToggle = document.getElementById('nav-toggle');
        const menu = document.getElementById('mobile-nav');

        const header = document.getElementById('site-header');

        const openIcon = menuToggle?.querySelector('[data-icon="open"]');
        const closeIcon = menuToggle?.querySelector('[data-icon="close"]');

        let lastScrollY = window.scrollY;

        const navIsOpen = () => menu !== null && !menu.classList.contains('hidden');
        const searchIsOpen = () => searchPanel !== null && !searchPanel.classList.contains('hidden');

        const setHeaderHidden = (hidden) => {
            if (!header) {
                return;
            }

            if (hidden && (navIsOpen() || searchIsOpen())) {
                return;
            }

            header.classList.toggle('-translate-y-full', hidden);
            header.inert = hidden;
        };

        const syncHeaderWithScroll = () => {
            if (!header) {
                return;
            }

            const currentY = window.scrollY;
            const delta = currentY - lastScrollY;
            lastScrollY = currentY;

            if (currentY <= 4) {
                setHeaderHidden(false);
                return;
            }

            if (Math.abs(delta) < 6) {
                return;
            }

            if (delta > 0 && currentY > header.offsetHeight) {
                setHeaderHidden(true);
            } else if (delta < 0) {
                setHeaderHidden(false);
            }
        };

        const normalize = (value) => value
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim();

        const filterSearch = () => {
            const query = normalize(searchInput.value);
            const hasQuery = query !== '';
            let visible = 0;

            searchResults?.classList.toggle('hidden', !hasQuery);

            searchItems.forEach((item) => {
                const matches = hasQuery && normalize(item.dataset.search ?? '').includes(query);
                item.classList.toggle('hidden', !matches);
                visible += matches ? 1 : 0;
            });

            searchEmpty?.classList.toggle('hidden', !hasQuery || visible > 0);
        };

        const setMenuOpen = (open) => {
            if (!menu || !menuToggle) {
                return;
            }

            if (open) {
                setHeaderHidden(false);
            }

            menu.classList.toggle('hidden', !open);
            menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            menuToggle.setAttribute('aria-label', open ? menuToggle.dataset.labelClose : menuToggle.dataset.labelOpen);
            openIcon?.classList.toggle('hidden', open);
            closeIcon?.classList.toggle('hidden', !open);
        };

        const setSearchOpen = (open) => {
            if (!searchPanel || !searchToggle) {
                return;
            }

            if (open) {
                setHeaderHidden(false);
            }

            searchPanel.classList.toggle('hidden', !open);
            searchToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            document.body.classList.toggle('overflow-hidden', open);

            if (open) {
                setMenuOpen(false);
                searchInput.value = '';
                filterSearch();
                window.requestAnimationFrame(() => searchInput.focus());
            }
        };

        const closeSearch = () => {
            if (!searchPanel || searchPanel.classList.contains('hidden')) {
                return;
            }

            setSearchOpen(false);
            searchToggle?.focus();
        };

        searchToggle?.addEventListener('click', () => {
            setSearchOpen(searchPanel.classList.contains('hidden'));
        });

        searchClose?.addEventListener('click', closeSearch);

        searchPanel?.addEventListener('click', (event) => {
            if (!event.target.closest('[data-search-dialog]')) {
                closeSearch();
            }
        });

        searchInput?.addEventListener('input', filterSearch);
        searchInput?.addEventListener('search', filterSearch);

        searchInput?.addEventListener('keydown', (event) => {
            if (event.key !== 'Enter') {
                return;
            }

            event.preventDefault();

            const firstVisible = searchItems.find((item) => !item.classList.contains('hidden'));
            firstVisible?.querySelector('a')?.click();
        });

        searchPanel?.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => setSearchOpen(false));
        });

        menuToggle?.addEventListener('click', () => {
            const willOpen = menu.classList.contains('hidden');

            if (willOpen) {
                setSearchOpen(false);
            }

            setMenuOpen(willOpen);
        });

        menu?.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => setMenuOpen(false));
        });

        document.addEventListener('click', (event) => {
            if (
                searchPanel
                && !searchPanel.classList.contains('hidden')
                && !searchPanel.contains(event.target)
                && !searchToggle?.contains(event.target)
            ) {
                closeSearch();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key !== 'Escape') {
                return;
            }

            setMenuOpen(false);
            closeSearch();
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1280) {
                setMenuOpen(false);
            }
        });

        window.addEventListener('scroll', syncHeaderWithScroll, { passive: true });

        header?.addEventListener('focusin', () => setHeaderHidden(false));

        window.addEventListener('pageshow', () => setHeaderHidden(false));
    })();
</script>
