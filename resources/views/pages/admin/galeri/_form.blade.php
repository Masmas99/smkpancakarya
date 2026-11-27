@php
    $editing = isset($galeri) && $galeri->exists;
    $imageUrl = $editing ? $galeri->imageUrl() : null;
@endphp

<form
    method="POST"
    action="{{ $editing ? route($routePrefix.'.update', $galeri) : route($routePrefix.'.store') }}"
    enctype="multipart/form-data"
    class="space-y-6"
>
    @csrf

    @if ($editing)
        @method('PUT')
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2">
            <div class="space-y-5 bg-white p-6 shadow-sm sm:rounded-lg">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Judul / Keterangan Foto</label>
                    <input
                        id="title"
                        name="title"
                        type="text"
                        value="{{ old('title', $galeri->title ?? '') }}"
                        maxlength="150"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                    <p class="mt-1 text-xs text-gray-500">Opsional. Tampil sebagai keterangan pada foto di beranda.</p>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="image" class="block text-sm font-medium text-gray-700">Foto</label>

                    @if ($imageUrl)
                        <img
                            src="{{ $imageUrl }}"
                            alt="Foto galeri saat ini"
                            class="mt-2 aspect-video w-full rounded-lg object-cover"
                        >
                    @else
                        <div class="mt-2 flex aspect-video w-full items-center justify-center rounded-lg border border-dashed border-gray-300 bg-gray-50 text-xs font-medium text-gray-400">
                            Belum ada foto
                        </div>
                    @endif

                    <input
                        id="image"
                        name="image"
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                        @required(! $editing)
                        class="mt-3 block w-full text-sm text-gray-600 file:mr-4 file:rounded-md file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-blue-700"
                    >
                    <p class="mt-1 text-xs text-gray-500">
                        JPG, PNG, atau WEBP. Maksimal 8 MB.
                        @if ($editing)
                            Biarkan kosong jika tidak ingin mengganti foto.
                        @endif
                    </p>
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="space-y-5 bg-white p-6 shadow-sm sm:rounded-lg">
                <div>
                    <p class="text-sm font-medium text-gray-700">Urutan tampil otomatis</p>
                    <p class="mt-1 text-xs text-gray-500">Foto yang paling baru diunggah tampil lebih dahulu.</p>
                </div>

                <div>
                    <label for="is_published" class="inline-flex items-center gap-2">
                        <input
                            id="is_published"
                            name="is_published"
                            type="checkbox"
                            value="1"
                            @checked(old('is_published', $galeri->is_published ?? true))
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                        >
                        <span class="text-sm font-medium text-gray-700">Tampilkan di beranda</span>
                    </label>
                    @error('is_published')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center rounded-md bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2"
                >
                    {{ $editing ? 'Simpan Perubahan' : 'Simpan Foto' }}
                </button>

                <a
                    href="{{ route($routePrefix.'.index') }}"
                    class="mt-3 block text-center text-sm font-medium text-gray-600 transition hover:text-gray-900"
                >
                    Batal
                </a>
            </div>
        </div>
    </div>
</form>
