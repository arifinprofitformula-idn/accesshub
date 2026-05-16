<x-internal-app-layout
    title="Tambah Link | AccessHub"
    eyebrow="Tambah Link"
    heading="Simpan Link Baru"
    subheading="Masukkan link penting agar bisa dicari dan dibuka lebih cepat dari dashboard."
>
    @include('app.links.partials.form', [
        'action' => route('app.links.store'),
        'method' => 'POST',
        'link' => null,
        'tagString' => '',
        'selectedVisibility' => 'shared',
    ])
</x-internal-app-layout>
