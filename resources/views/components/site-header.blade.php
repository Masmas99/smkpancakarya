@php
    $links = [
        ['label' => 'Beranda', 'route' => 'home'],
        ['label' => 'Kompetensi Keahlian', 'route' => 'kompetensi-keahlian'],
        ['label' => 'Prestasi', 'route' => 'prestasi'],
        [
            'label' => 'Profil',
            'route' => 'profil',
            'children' => [
                ['label' => 'About', 'route' => 'profil.about'],
                ['label' => 'Kesiswaan', 'route' => 'profil.kesiswaan'],
                ['label' => 'Akademik', 'route' => 'profil.akademik'],
            ],
        ],
        ['label' => 'Visi dan Misi', 'route' => 'visi-dan-misi'],
        ['label' => 'Kontak', 'route' => 'kontak'],
        ['label' => 'Sarana', 'route' => 'sarana'],
    ];
@endphp

<header class="sticky top-0 z-50 border-b border-slate-200 bg-white/95 backdrop-blur">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:h-20 lg:px-8">
        {{-- Logo --}}
        <a
            href="{{ route('home') }}"
            class="flex shrink-0 items-center gap-3 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2"
        >
            <img
                src="{{ asset('images/logo-smk-pancakarya.webp') }}"
                alt="Logo SMK Pancakarya"
                width="56"
                height="56"
                class="h-12 w-12 shrink-0 object-contain lg:h-14 lg:w-14"
                decoding="async"
            >
            <span class="flex flex-col leading-tight">
                <span class="text-base font-semibold text-slate-900">SMK Pancakarya</span>
                <span class="hidden text-xs font-medium text-slate-500 sm:block">Sekolah Menengah Kejuruan</span>
            </span>
        </a>

        {{-- Navigasi desktop --}}
        <nav class="hidden items-center gap-0.5 lg:flex" aria-label="Navigasi utama">
            @foreach ($links as $link)
                @php
                    $isActive = request()->routeIs($link['route'], $link['route'].'.*');
                @endphp

                @if (! empty($link['children']))
                    <div class="group relative">
                        <a
                            href="{{ route($link['route']) }}"
                            @if ($isActive) aria-current="page" @endif
                            @class([
                                'rounded-lg px-3 py-2 text-sm font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2',
                                'bg-blue-50 text-blue-700' => $isActive,
                                'text-slate-700 hover:bg-slate-100 hover:text-blue-700' => ! $isActive,
                            ])
                        >
                            {{ $link['label'] }}
                        </a>

                        {{-- Submenu --}}
                        <div class="invisible absolute left-0 top-full w-52 pt-2 opacity-0 transition duration-150 group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100">
                            <div class="rounded-xl border border-slate-200 bg-white p-1.5 shadow-lg">
                                @foreach ($link['children'] as $child)
                                    @php
                                        $childActive = request()->routeIs($child['route']);
                                    @endphp
                                    <a
                                        href="{{ route($child['route']) }}"
                                        @if ($childActive) aria-current="page" @endif
                                        @class([
                                            'block rounded-lg px-3 py-2 text-sm font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600',
                                            'bg-blue-50 text-blue-700' => $childActive,
                                            'text-slate-700 hover:bg-slate-100 hover:text-blue-700' => ! $childActive,
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
                            'rounded-lg px-3 py-2 text-sm font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2',
                            'bg-blue-50 text-blue-700' => $isActive,
                            'text-slate-700 hover:bg-slate-100 hover:text-blue-700' => ! $isActive,
                        ])
                    >
                        {{ $link['label'] }}
                    </a>
                @endif
            @endforeach

            <a
                href="{{ route('info-ppdb') }}"
                @if (request()->routeIs('info-ppdb')) aria-current="page" @endif
                class="ml-2 inline-flex items-center gap-1.5 rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2"
            >
                Info PPDB
                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0-6.75-6.75M19.5 12l-6.75 6.75" />
                </svg>
            </a>
        </nav>

        {{-- Tombol menu seluler --}}
        <button
            id="nav-toggle"
            type="button"
            class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-slate-700 transition hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 lg:hidden"
            aria-controls="mobile-nav"
            aria-expanded="false"
            aria-label="Buka menu navigasi"
        >
            <svg data-icon="open" class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
            <svg data-icon="close" class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Navigasi seluler --}}
    <div id="mobile-nav" class="hidden border-t border-slate-200 bg-white lg:hidden">
        <nav class="mx-auto max-w-7xl space-y-1 px-4 py-4 sm:px-6" aria-label="Navigasi seluler">
            @foreach ($links as $link)
                @php
                    $isActive = request()->routeIs($link['route'], $link['route'].'.*');
                @endphp

                <a
                    href="{{ route($link['route']) }}"
                    @if ($isActive) aria-current="page" @endif
                    @class([
                        'block rounded-lg px-3 py-2.5 text-base font-medium transition',
                        'bg-blue-50 text-blue-700' => $isActive,
                        'text-slate-700 hover:bg-slate-100 hover:text-blue-700' => ! $isActive,
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
                                    'text-blue-700' => $childActive,
                                    'text-slate-600 hover:bg-slate-100 hover:text-blue-700' => ! $childActive,
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
                Info PPDB
            </a>
        </nav>
    </div>
</header>

<script>
    (() => {
        const toggle = document.getElementById('nav-toggle');
        const menu = document.getElementById('mobile-nav');

        if (!toggle || !menu) {
            return;
        }

        const openIcon = toggle.querySelector('[data-icon="open"]');
        const closeIcon = toggle.querySelector('[data-icon="close"]');

        const setOpen = (open) => {
            menu.classList.toggle('hidden', !open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            toggle.setAttribute('aria-label', open ? 'Tutup menu navigasi' : 'Buka menu navigasi');
            openIcon?.classList.toggle('hidden', open);
            closeIcon?.classList.toggle('hidden', !open);
        };

        toggle.addEventListener('click', () => setOpen(menu.classList.contains('hidden')));

        menu.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => setOpen(false));
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                setOpen(false);
            }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                setOpen(false);
            }
        });
    })();
</script>
