@php
    $viteEntries = $viteEntries ?? ['resources/css/app.css', 'resources/js/app.js'];
    $hotFile = public_path('hot');
    $manifestPath = public_path('build/manifest.json');
@endphp

@if (file_exists($hotFile))
    @vite($viteEntries)
@elseif (file_exists($manifestPath))
    @php
        $manifest = json_decode(file_get_contents($manifestPath), true) ?? [];

        $resolveEntry = function (string $entry) use ($manifest): ?array {
            if (isset($manifest[$entry])) {
                return $manifest[$entry];
            }

            foreach ($manifest as $key => $value) {
                if ($key === $entry || str_ends_with(str_replace('\\', '/', $key), '/'.$entry)) {
                    return $value;
                }

                if (($value['src'] ?? null) === $entry || str_ends_with(str_replace('\\', '/', (string) ($value['src'] ?? '')), '/'.$entry)) {
                    return $value;
                }
            }

            return null;
        };
    @endphp

    @foreach ($viteEntries as $entry)
        @php
            $chunk = $resolveEntry($entry);
        @endphp

        @if ($chunk)
            @if (($chunk['file'] ?? false) && str_ends_with($chunk['file'], '.css'))
                <link rel="stylesheet" href="{{ asset('build/'.$chunk['file']) }}">
            @elseif (($chunk['file'] ?? false) && str_ends_with($chunk['file'], '.js'))
                @foreach ($chunk['css'] ?? [] as $cssFile)
                    <link rel="stylesheet" href="{{ asset('build/'.$cssFile) }}">
                @endforeach
                <script type="module" src="{{ asset('build/'.$chunk['file']) }}"></script>
            @endif
        @endif
    @endforeach
@else
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
@endif
