@php
    $viteEntries = $viteEntries ?? ['resources/css/app.css', 'resources/js/app.js'];
    $hasViteBuild = file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot'));
@endphp

@if ($hasViteBuild)
    @vite($viteEntries)
@else
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
@endif
