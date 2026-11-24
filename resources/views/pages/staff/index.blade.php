<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Staff') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="space-y-4 p-6 text-gray-900">
                    <p>{{ __("Anda masuk sebagai Staff.") }}</p>
                    <a
                        href="{{ route('staff.artikel.index') }}"
                        class="inline-flex items-center gap-2 rounded-md bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2"
                    >
                        Kelola Artikel
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
