<x-layouts.public :title="__('site.nav.about')">
    <main>
        <x-page-heading
            :title="__('site.nav.about')"
            :parents="[['label' => __('site.nav.profile'), 'url' => route('profil')]]"
        />
    </main>
</x-layouts.public>
