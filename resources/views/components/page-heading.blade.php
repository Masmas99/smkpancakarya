@props(['title', 'description' => null, 'parents' => []])

<div class="border-b border-slate-200 bg-white">
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-14 lg:px-8">
        {{-- Breadcrumb --}}
        <nav aria-label="{{ __('site.breadcrumb.label') }}">
            <ol class="flex flex-wrap items-center gap-2 text-sm">
                <li>
                    <a
                        href="{{ route('home') }}"
                        class="font-medium text-slate-500 transition hover:text-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2"
                    >
                        {{ __('site.breadcrumb.home') }}
                    </a>
                </li>

                @foreach ($parents as $parent)
                    <li aria-hidden="true" class="text-slate-400">&gt;</li>
                    <li>
                        <a
                            href="{{ $parent['url'] }}"
                            class="font-medium text-slate-500 transition hover:text-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2"
                        >
                            {{ $parent['label'] }}
                        </a>
                    </li>
                @endforeach

                <li aria-hidden="true" class="text-slate-400">&gt;</li>
                <li aria-current="page" class="font-medium text-slate-900">
                    {{ $title }}
                </li>
            </ol>
        </nav>

        <h1 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
            {{ $title }}
        </h1>

        @if ($description)
            <p class="mt-3 max-w-2xl text-base leading-relaxed text-slate-600">
                {{ $description }}
            </p>
        @endif
    </div>
</div>
