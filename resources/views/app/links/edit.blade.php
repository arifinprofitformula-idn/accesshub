<x-internal-app-layout
    title="Edit Link | AccessHub"
    eyebrow="Edit Link"
    heading="Perbarui Link"
    subheading="Ubah judul, URL, kategori, atau catatan tanpa membuka panel admin."
>
    @include('app.links.partials.form', [
        'action' => route('app.links.update', $link),
        'method' => 'PUT',
        'link' => $link,
        'tagString' => $tagString,
        'selectedVisibility' => $selectedVisibility,
    ])
</x-internal-app-layout>
