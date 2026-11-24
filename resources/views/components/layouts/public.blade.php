<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ isset($title) ? $title.' — SMK Pancakarya' : __('site.meta.title') }}</title>
        <meta name="description" content="{{ $description ?? __('site.meta.description') }}">

        <!-- Fonts (self-hosted) -->
        <link rel="preload" as="font" type="font/woff2" href="{{ asset('fonts/figtree-latin-400.woff2') }}" crossorigin>

        <!-- Styles -->
        @vite('resources/css/app.css')
    </head>
    <body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
        <x-site-header />

        {{ $slot }}
    </body>
</html>
