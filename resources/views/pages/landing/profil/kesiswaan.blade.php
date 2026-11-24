<x-layouts.public :title="__('site.nav.student_affairs')">
    <main>
        <x-page-heading
            :title="__('site.nav.student_affairs')"
            :parents="[['label' => __('site.nav.profile'), 'url' => route('profil')]]"
        />
    </main>
</x-layouts.public>
