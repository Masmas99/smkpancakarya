@php
    $editing = isset($artikel) && $artikel->exists;
    $coverUrl = $editing ? $artikel->coverUrl() : null;
@endphp

<form
    method="POST"
    action="{{ $editing ? route('staff.artikel.update', $artikel) : route('staff.artikel.store') }}"
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
                    <label for="title" class="block text-sm font-medium text-gray-700">Judul Artikel</label>
                    <input
                        id="title"
                        name="title"
                        type="text"
                        value="{{ old('title', $artikel->title ?? '') }}"
                        required
                        maxlength="180"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700">Slug URL</label>
                    <input
                        id="slug"
                        name="slug"
                        type="text"
                        value="{{ old('slug', $artikel->slug ?? '') }}"
                        maxlength="180"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                    <p class="mt-1 text-xs text-gray-500">Kosongkan untuk dibuat otomatis dari judul.</p>
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="excerpt" class="block text-sm font-medium text-gray-700">Ringkasan</label>
                    <textarea
                        id="excerpt"
                        name="excerpt"
                        rows="3"
                        maxlength="300"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >{{ old('excerpt', $artikel->excerpt ?? '') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Tampil pada kartu artikel. Maksimal 300 karakter.</p>
                    @error('excerpt')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="body" class="block text-sm font-medium text-gray-700">Isi Artikel</label>
                    <textarea
                        id="body"
                        name="body"
                        rows="16"
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 font-normal leading-relaxed shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >{{ old('body', $artikel->body ?? '') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Pisahkan paragraf dengan satu baris kosong.</p>
                    @error('body')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="space-y-5 bg-white p-6 shadow-sm sm:rounded-lg">
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Kategori</label>
                    <select
                        id="category"
                        name="category"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        @foreach (\App\Models\Article::categoryOptions() as $value => $label)
                            <option value="{{ $value }}" @selected(old('category', $artikel->category ?? \App\Models\Article::DEFAULT_CATEGORY) === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <select
                        id="status"
                        name="status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        @foreach (\App\Models\Article::statusOptions() as $value => $label)
                            <option value="{{ $value }}" @selected(old('status', $artikel->status ?? \App\Models\Article::STATUS_DRAFT) === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <span class="block text-sm font-medium text-gray-700">Gambar Sampul</span>

                    @if ($coverUrl)
                        <img
                            src="{{ $coverUrl }}"
                            alt="Gambar sampul saat ini"
                            class="mt-2 aspect-[16/10] w-full rounded-lg object-cover"
                        >
                    @else
                        <div class="mt-2 flex aspect-[16/10] w-full items-center justify-center rounded-lg border border-dashed border-gray-300 bg-gray-50 text-xs font-medium text-gray-400">
                            Belum ada gambar
                        </div>
                    @endif

                    <input
                        id="cover_image"
                        name="cover_image"
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                        class="mt-3 block w-full text-sm text-gray-600 file:mr-4 file:rounded-md file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-blue-700"
                    >
                    <p class="mt-1 text-xs text-gray-500">JPG, PNG, atau WEBP. Maksimal 4 MB.</p>
                    @error('cover_image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <button
                    type="submit"
                    class="inline-flex w-full items-center justify-center rounded-md bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2"
                >
                    {{ $editing ? 'Simpan Perubahan' : 'Simpan Artikel' }}
                </button>

                <a
                    href="{{ route('staff.artikel.index') }}"
                    class="mt-3 block text-center text-sm font-medium text-gray-600 transition hover:text-gray-900"
                >
                    Batal
                </a>
            </div>
        </div>
    </div>
</form>
