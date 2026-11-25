<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Ubah Foto Galeri
            </h2>

            <a href="{{ route($routePrefix.'.index') }}" class="text-sm font-medium text-blue-700 transition hover:text-blue-800">
                &larr; Kembali ke galeri
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @include('pages.admin.galeri._form', ['galeri' => $galeri])
        </div>
    </div>
</x-app-layout>
